<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    //
    public function __construct() {
        $this->middleware('permission:menu-list|menu-create|menu-edit|menu-delete',['only' => ['index','show']]);
        $this->middleware('permission:menu-create', ['only' => ['create','store']]);
         $this->middleware('permission:menu-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:menu-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        # code...
    }
}
