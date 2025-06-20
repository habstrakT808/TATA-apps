<?php
namespace App\Http\Controllers\Services;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Editor;
class EditorController extends Controller
{
    public function createEditor(Request $rt){
        $v = Validator::make($rt->only('nama_editor', 'email', 'jenis_kelamin', 'no_telpon', 'foto'), [
            'nama_editor' => 'required|min:3|max:50',
            'email' => 'required|email|max:45|unique:editor,email',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'no_telpon' => 'nullable|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'nama_editor.required' => 'Nama Editor wajib di isi',
            'nama_editor.min' => 'Nama Editor minimal 3 karakter',
            'nama_editor.max' => 'Nama Editor maksimal 50 karakter',
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 45 karakter',
            'email.unique' => 'Email sudah digunakan',
            'jenis_kelamin.in' => 'Jenis Kelamin harus laki-laki atau perempuan',
            'no_telpon.max' => 'No Telepon maksimal 15 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'File harus berupa gambar',
            'foto.max' => 'File maksimal 2MB',
        ]);
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $ins = Editor::create([
            'uuid' => Str::uuid(),
            'nama_editor' => $rt->input('nama_editor'),
            'email' => $rt->input('email'),
            'jenis_kelamin' => $rt->input('jenis_kelamin'),
            'no_telpon' => $rt->input('no_telpon'),
            'foto' => $rt->input('foto'),
        ]);
        if(!$ins){
            return response()->json(['status'=>'error','message'=>'Gagal menambahkan data Editor'], 500);
        }
        return response()->json(['status'=>'success','message'=>'Data Editor berhasil ditambahkan']);
    }
    
    public function updateEditor(Request $rt){
        $v = Validator::make($rt->only('uuid', 'nama_editor', 'email', 'jenis_kelamin', 'no_telpon', 'foto'), [
            'uuid' => 'required',
            'nama_editor' => 'required|min:3|max:50',
            'email' => 'required|email|max:45',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'no_telpon' => 'nullable|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'uuid.required' => 'ID Editor wajib di isi',
            'nama_editor.required' => 'Nama Editor wajib di isi',
            'nama_editor.min' => 'Nama Editor minimal 3 karakter',
            'nama_editor.max' => 'Nama Editor maksimal 50 karakter',
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 45 karakter',
            'jenis_kelamin.in' => 'Jenis Kelamin harus laki-laki atau perempuan',
            'no_telpon.max' => 'No Telepon maksimal 15 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'File harus berupa gambar',
            'foto.max' => 'File maksimal 2MB',
        ]);
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $editor = Editor::where('uuid', $rt->input('uuid'))->first();
        if(!$editor){
            return response()->json(['status'=>'error','message'=>'Data Editor tidak ditemukan'], 404);
        }
        
        // Check if email is unique (except for current editor)
        if ($rt->input('email') !== $editor->email) {
            $existingEditor = Editor::where('email', $rt->input('email'))->first();
            if ($existingEditor) {
                return response()->json(['status'=>'error','message'=>'Email sudah digunakan'], 400);
            }
        }
        
        $editor->update([
            'nama_editor' => $rt->input('nama_editor'),
            'email' => $rt->input('email'),
            'jenis_kelamin' => $rt->input('jenis_kelamin'),
            'no_telpon' => $rt->input('no_telpon'),
            'foto' => $rt->input('foto'),
        ]);
        if(!$editor){
            return response()->json(['status'=>'error','message'=>'Gagal mengupdate data Editor'], 500);
        }
        return response()->json(['status'=>'success','message'=>'Data Editor berhasil diupdate']);
    }
    public function deleteEditor(Request $rt){
        $v = Validator::make($rt->only('uuid'), [
            'uuid' => 'required',
        ], [
            'uuid.required' => 'ID Editor wajib di isi',
        ]);
        
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        $editor = Editor::where('uuid', $rt->input('uuid'))->first();
        if(!$editor){
            return response()->json(['status'=>'error','message'=>'Data Editor tidak ditemukan'], 404);
        }
        $editor->delete();
        if(!$editor){
            return response()->json(['status'=>'error','message'=>'Gagal menghapus data Editor'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Data Editor berhasil dihapus']);
    }
}