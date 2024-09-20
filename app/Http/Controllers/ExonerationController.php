<?php

namespace App\Http\Controllers;

use App\Models\Exoneration;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExonerationController extends Controller
{
    public function storeExonerations(Request $request){
        foreach ($request->ids as $id) {
            Exoneration::create(
                [
                    'article_id' => $id,
                    'date' => Carbon::now(),

                ]
            );
        }
    }
}
