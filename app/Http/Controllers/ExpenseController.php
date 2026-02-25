<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Colocation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request, Colocation $colocation)
    {
        // Vérifier que l'utilisateur est membre actif
        $isMember = $colocation->members()
            ->whereKey(auth()->id())
            ->wherePivotNull('left_at')
            ->exists();

        abort_unless($isMember, 403);

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'colocation_id' => $colocation->id,
            'user_id' => auth()->id(),
            'category_id' => $request->category_id ?? null,
        ]);

        return back()->with('success', 'Dépense ajoutée.');
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        // Seul le payeur peut modifier
        abort_unless($expense->user_id === auth()->id(), 403);

        $expense->update($request->validated());

        return back()->with('success', 'Dépense mise à jour.');
    }

    public function destroy(Expense $expense)
    {
        // Seul le payeur peut supprimer
        abort_unless($expense->user_id === auth()->id(), 403);

        $expense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }
}