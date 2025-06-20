<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Models\User;
use App\Models\Admin;
use App\Models\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function showAll(Request $request){
        $userData = User::select('uuid', 'nama_user', 'jenis_kelamin', 'no_telpon')
            ->join('auth', 'users.id_auth', '=', 'auth.id_auth')
            ->whereNotIn('auth.role', ['super_admin', 'admin_chat', 'admin_pemesanan'])
            ->get()
            ->map(function($item) {
                $item->role = ucwords(str_replace('_', ' ', $item->role));
                return $item;
            });
        $dataShow = [
            'userData' => $userData ?? [],
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.user.data', $dataShow);
    }
    public function showTambah(Request $request){
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.user.tambah',$dataShow);
    }
    public function showDetail(Request $request, $uuid){
        $userData = User::select('uuid','nama_user', 'jenis_kelamin', 'no_telpon', 'foto')->whereRaw("BINARY uuid = ?",[$uuid])->join('auth', 'users.id_auth', '=', 'auth.id_auth')->first();
        if(is_null($userData)){
            return redirect('/user')->with('error', 'Data User tidak ditemukan');
        }
        $dataShow = [
            'userData' => $userData,
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.user.detail',$dataShow);
    }

    public function deleteUser(Request $request) {
        Log::info('UserController::deleteUser request payload', $request->all());
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->only('uuid'), [
            'uuid' => 'required',
        ], [
            'uuid.required' => 'User ID wajib di isi',
        ]);
        
        if ($validator->fails()){
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        // First get the user record
        $user = User::where('uuid', $request->input('uuid'))->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }
        
        // Store the id_auth
        $id_auth = $user->id_auth;
        
        // Delete the user record
        if(!User::where('uuid', $request->input('uuid'))->delete()){
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data User'], 500);
        }
        
        // Delete the corresponding auth record
        Auth::where('id_auth', $id_auth)->delete();
        
        return response()->json(['status' => 'success', 'message' => 'Data User berhasil dihapus']);
    }
}