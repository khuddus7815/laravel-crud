<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $items = Item::with('category')->latest()->get();

    $formattedItems = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => $item->price ?? 0.00,
            'image_url' => $item->image_path ? Storage::url($item->image_path) : 'https://via.placeholder.com/300',
            'category' => $item->category ? $item->category->name : null,
        ];
    });

    return response()->json($formattedItems);
}
}