<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        // vendor portal currently reuses admin dashboard layout
        $layout = 'layouts.admin';
        return view('dashboard', compact('layout'));
    }
}
