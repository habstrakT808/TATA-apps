<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
class PublicController extends Controller
{
    public function showHome(Request $request){
        return view('page.home');
    }
}