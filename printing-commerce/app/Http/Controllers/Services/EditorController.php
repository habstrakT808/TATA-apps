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
        $v = Validator::make($rt->only('nama_editor', 'jenis_kelamin', 'no_telpon', 'foto'), [
            'nama_editor' => 'required|min:3|max:50',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'no_telpon' => 'nullable|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'nama_editor.required' => 'Nama Editor wajib di isi',
            'nama_editor.min' => 'Nama Editor minimal 3 karakter',
            'nama_editor.max' => 'Nama Editor maksimal 50 karakter',
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
        $v = Validator::make($rt->only('id_editor', 'nama_editor', 'jenis_kelamin', 'no_telpon', 'foto'), [
            'id_editor' => 'required',
            'nama_editor' => 'required|min:3|max:50',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'no_telpon' => 'nullable|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'id_editor.required' => 'ID Editor wajib di isi',
            'nama_editor.required' => 'Nama Editor wajib di isi',
            'nama_editor.min' => 'Nama Editor minimal 3 karakter',
            'nama_editor.max' => 'Nama Editor maksimal 50 karakter',
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
        $editor = Editor::where('uuid', $rt->input('id_editor'))->first();
        if(!$editor){
            return response()->json(['status'=>'error','message'=>'Data Editor tidak ditemukan'], 404);
        }
        $editor->update([
            'nama_editor' => $rt->input('nama_editor'),
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
        $v = Validator::make($rt->only('id_editor'), [
            'id_editor' => 'required',
        ], [
            'id_editor.required' => 'ID Editor wajib di isi',
        ]);
        
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        $editor = Editor::where('uuid', $rt->input('id_editor'))->first();
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