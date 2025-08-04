<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only admin can access inventory management
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Filter by item type
        $type = $request->input('type');
        $query = Inventory::with('addedBy')->orderBy('item_name');

        if ($type === 'raw_materials') {
            $query->where('is_raw_material', true);
        } elseif ($type === 'supplies') {
            $query->where('is_raw_material', false);
        } elseif ($type === 'low_stock') {
            $query->whereRaw('quantity <= reorder_point');
        }

        $inventory = $query->paginate(10);

        return view('admin.inventory.index', compact('inventory', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only admin can create inventory items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admin can store inventory items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'is_raw_material' => 'required|boolean',
            'last_restock_date' => 'required|date',
        ]);

        Inventory::create([
            'item_name' => $request->item_name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price,
            'reorder_point' => $request->reorder_point,
            'is_raw_material' => $request->is_raw_material,
            'last_restock_date' => $request->last_restock_date,
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item inventaris berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Only admin can view inventory items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $item = Inventory::with('addedBy')->findOrFail($id);

        return view('admin.inventory.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Only admin can edit inventory items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $item = Inventory::findOrFail($id);

        return view('admin.inventory.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Only admin can update inventory items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'is_raw_material' => 'required|boolean',
            'last_restock_date' => 'required|date',
        ]);

        $item = Inventory::findOrFail($id);

        $item->update([
            'item_name' => $request->item_name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price,
            'reorder_point' => $request->reorder_point,
            'is_raw_material' => $request->is_raw_material,
            'last_restock_date' => $request->last_restock_date,
        ]);

        return redirect()->route('admin.inventory.show', $item->id)
            ->with('success', 'Item inventaris berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Only admin can delete inventory items
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Item inventaris berhasil dihapus.');
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, string $id)
    {
        // Only admin can update stock
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'quantity' => 'required|numeric|min:0',
            'last_restock_date' => 'required|date',
        ]);

        $item = Inventory::findOrFail($id);

        $item->update([
            'quantity' => $request->quantity,
            'last_restock_date' => $request->last_restock_date,
        ]);

        return redirect()->route('admin.inventory.show', $item->id)
            ->with('success', 'Stok inventaris berhasil diperbarui.');
    }
}
