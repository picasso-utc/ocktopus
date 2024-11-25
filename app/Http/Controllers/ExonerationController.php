<?php

namespace App\Http\Controllers;

use App\Models\Exoneration;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExonerationController extends Controller
{
    public function storeExonerations(Request $request){
        foreach ($request->items as $item) {
            if (is_array($item) && count($item) === 2) {
                Exoneration::create([
                    'article_id' => $item[0],
                    'quantity' => $item[1],
                    'date' => Carbon::now(),
                ]);
            }
        }
    }
}
