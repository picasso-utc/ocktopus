<?php

namespace App\Http\Controllers;

use App\Models\PicStatus;
use Illuminate\Http\Request;

class PicStatusController extends Controller
{
    public function show()
    {
        return response()->json(['success' => true, 'open' => PicStatus::current()->open]);
    }

    public function update(Request $request)
    {
        $status = PicStatus::current();
        $status->open = $request->boolean('open');
        $status->save();

        return response()->json(['success' => true, 'open' => $status->open]);
    }
}
