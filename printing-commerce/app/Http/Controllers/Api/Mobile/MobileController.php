<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function show($id)
    {
        $jasa = Jasa::findOrFail($id);
        return response()->json($jasa);
    }
} 