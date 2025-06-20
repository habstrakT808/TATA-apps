<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MetodePembayaran;
use App\Http\Controllers\UtilityController;

class MetodePembayaranController extends Controller
{
    public function showAll(Request $request){
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'metodePembayaranData' => MetodePembayaran::select('uuid','nama_metode_pembayaran')->get(),
        ];
        return view('page.metode-pembayaran.data',$dataShow);
    }
    public function showTambah(Request $request){
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.metode-pembayaran.tambah',$dataShow);
    }
    public function showDetail(Request $request, $uuid){
        $metodePembayaranData = MetodePembayaran::select(
            'uuid',
            'nama_metode_pembayaran', 
            'no_metode_pembayaran', 
            'deskripsi_1', 
            'deskripsi_2', 
            'thumbnail', 
            'icon'
        )->whereRaw("BINARY uuid = ?",[$uuid])->first();
        
        if(is_null($metodePembayaranData)){
            return redirect('/payment-methods')->with('error', 'Data Metode Pembayaran tidak ditemukan');
        }
        
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'metodePembayaranData' => $metodePembayaranData,
        ];
        return view('page.metode-pembayaran.detail',$dataShow);
    }
    public function showEdit(Request $request, $uuid){
        $metodePembayaranData = MetodePembayaran::select(
            'uuid',
            'nama_metode_pembayaran', 
            'no_metode_pembayaran', 
            'deskripsi_1', 
            'deskripsi_2', 
            'thumbnail', 
            'icon'
        )->whereRaw("BINARY uuid = ?",[$uuid])->first();
        
        if(is_null($metodePembayaranData)){
            return redirect('/payment-methods')->with('error', 'Data Metode Pembayaran tidak ditemukan');
        }
        
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'metodePembayaranData' => $metodePembayaranData,
        ];
        return view('page.metode-pembayaran.edit',$dataShow);
    }
}
