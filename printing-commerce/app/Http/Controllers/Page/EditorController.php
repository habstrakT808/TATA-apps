<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;
use App\Models\Admin;
use App\Models\Editor;
class EditorController extends Controller
{
    public function showAll(Request $request){
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'editorData' => Editor::all(),
            'headerData' => UtilityController::getHeaderData(),
        ];
        return view('page.editor.data',$dataShow);
    }
    public function showTambah(Request $request){
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'headerData' => UtilityController::getHeaderData(),
        ];
        return view('page.editor.tambah',$dataShow);
    }
    public function showEdit(Request $request, $id){
        $editorData = Editor::where('uuid', $id)->first();
        if(is_null($editorData)){
            return redirect('/editor')->with('error', 'Data editor tidak ditemukan');
        }
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'editorData' => $editorData,
            'headerData' => UtilityController::getHeaderData(),
        ];
        return view('page.editor.edit',$dataShow);
    }
}
?>