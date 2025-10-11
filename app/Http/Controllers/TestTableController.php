<?php

namespace App\Http\Controllers;
use App\Models\TestTable;

use Illuminate\Http\Request;


class TestTableController extends Controller
{
    public function index()
    {
        $datos = TestTable::all();
        return view('test_table.index', compact('datos'));
    }
}
