<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Display a listing of the tables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Table::query()->where('venue_id', auth()->user()->venue_id);

        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $tables = $query->latest()->paginate(10);
        
        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new table.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tables.create');
    }

    /**
     * Store a newly created table in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'status' => 'required|in:Available,Booked,Unavailable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Table::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'status' => $request->status,
            'venue_id' => auth()->user()->venue_id,
        ]);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified table.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $table = Table::where('venue_id', auth()->user()->venue_id)->findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }

    /**
     * Update the specified table in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'status' => 'required|in:Available,Booked,Unavailable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $table = Table::where('venue_id', auth()->user()->venue_id)->findOrFail($id);
        
        $table->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Data meja berhasil diperbarui.');
    }

    /**
     * Remove the specified table from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $table = Table::where('venue_id', auth()->user()->venue_id)->findOrFail($id);
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }
}