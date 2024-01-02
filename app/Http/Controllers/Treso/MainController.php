<?php

namespace App\Http\Controllers\Treso;

use App\Http\Controllers\Controller;
use App\Models\Treso\FactureRecue;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index() {

        return view('home', [
            'FactureRecue' => FactureRecue::all()
        ]);
    }
}
