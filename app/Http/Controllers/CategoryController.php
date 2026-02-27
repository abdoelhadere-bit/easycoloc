<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $this->authorize('manageCategories', $colocation);

        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        Category::create([
            'name' => $request->name,
            'colocation_id' => $colocation->id,
        ]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function update(Request $request, Colocation $colocation, Category $category)
    {
        abort_unless($colocation->isOwner(auth()->id()), 403);

        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'Catégorie modifiée.');
    }

    public function destroy(Colocation $colocation, Category $category)
    {
        abort_unless($colocation->isOwner(auth()->id()), 403);
    
        // Vérifier si catégorie contient des dépenses
        if ($category->expenses()->exists()) {
            return back()->withErrors([
                'category_delete' => 'Impossible de supprimer une catégorie contenant des dépenses.'
            ]);
        }
    
        $category->delete();
    
        return back()->with('success', 'Catégorie supprimée.');
    }
}