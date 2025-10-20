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

    public function create()
    {
        return view('test_table.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255',
        ]);

        TestTable::create($request->all());
        return redirect()->route('test.index')->with('success', 'Registro creado exitosamente');
    }

    public function edit($id)
    {
        $dato = TestTable::findOrFail($id);
        return view('test_table.edit', compact('dato'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:255',
        ]);

        $dato = TestTable::findOrFail($id);
        $dato->update($request->all());
        return redirect()->route('test.index')->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy($id)
    {
        $dato = TestTable::findOrFail($id);
        $dato->delete();
        return redirect()->route('test.index')->with('success', 'Registro eliminado exitosamente');
    }
}
