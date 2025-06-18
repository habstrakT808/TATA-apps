<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\Services\ReviewController AS ServiceReviewController;
use Illuminate\Http\Request;
class ReviewController extends Controller
{
    public function showData(Request $request){
        $dataShow = [
            'dataReview' => app()->make(ServiceReviewController::class)->dataCacheFile(null, 'get_limit',null, ['uuid', 'judul','rentang_usia']),
            'userAuth' => $request->input('user_auth'),
            'headerData' => UtilityController::getHeaderData(),
        ];
        return view('page.review.data',$dataShow);
    }
}