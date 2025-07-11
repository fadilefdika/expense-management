<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index()
    {
        return view('pages.settlement.index');
    }

    public function store()
    {
        return redirect()->route('admin.settlement.index');
    }
}
