<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\JasaImage;
use App\Http\Controllers\UtilityController;
class JasaController extends Controller
{
    public function showAll(Request $request){
        $dataShow = [
            'jasaData' => Jasa::select('uuid','kategori')->get(),
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.jasa.data',$dataShow);
    }
    public function showEdit(Request $request, $uuid){
        $jasa = Jasa::where('uuid', $uuid)->first();
        
        if(is_null($jasa)){
            return redirect('/jasa')->with('error', 'Data Jasa tidak ditemukan');
        }
        $paketJasa = PaketJasa::where('id_jasa', $jasa->id_jasa)->get();
        $jasaImages = JasaImage::where('id_jasa', $jasa->id_jasa)->get();
        if(is_null($paketJasa)){
            return redirect('/jasa')->with('error', 'Data Paket Jasa tidak ditemukan');
        }
        $jasaData = [
            'uuid' => $jasa->uuid,
            'deskripsi_jasa' => $jasa->deskripsi_jasa,
            'kategori' => $jasa->kategori,
            "paket_jasa" => PaketJasa::where('id_jasa', $jasa->id_jasa)->get(),
            'images' => JasaImage::where('id_jasa', $jasa->id_jasa)->get()
        ];
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'jasa' => $jasaData,
        ];
        return view('page.jasa.edit',$dataShow);
    }
}
?>