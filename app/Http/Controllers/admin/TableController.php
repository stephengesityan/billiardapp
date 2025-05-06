<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function kelolaMeja()
    {
        $tables = Table::where('venue_id', auth()->user()->venue_id)->paginate(10);
        return view('admin.tables.index', compact('tables'));
    }

    public function editTable($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }

    public function updateTable(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        
        $table->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Data meja berhasil diperbarui.');
    }

}
