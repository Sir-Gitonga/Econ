<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Minimal super-admin dashboard. You can expand this with metrics later.
        return view('admin.super');
    }
}
