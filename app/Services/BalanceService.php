<?php

namespace App\Services;

use App\Models\Colocation;

class BalanceService
{
    public function calculate(Colocation $colocation)
    {
        $members = $colocation->members()
            ->wherePivotNull('left_at')
            ->get();

        $expenses = $colocation->expenses;
        $payments = $colocation->payments;

        $total = $expenses->sum('amount');
        $count = $members->count();

        if ($count == 0) {
            return ['balances' => [], 'transactions' => []];
        }

        $share = $total / $count;

        $balances = [];

        // Calcul balances depuis dépenses
        foreach ($members as $member) {

            $paid = $expenses
                ->where('user_id', $member->id)
                ->sum('amount');

            $balances[] = [
                'id' => $member->id,
                'name' => $member->name,
                'balance' => round($paid - $share, 2),
            ];
        }
        // Appliquer paiements
        foreach ($payments as $payment) {
            foreach ($balances as $key => $b) {
                if ($b['id'] == $payment->from_user_id) {
                    $balances[$key]['balance'] += $payment->amount;
                }
                if ($b['id'] == $payment->to_user_id) {
                    $balances[$key]['balance'] -= $payment->amount;
                }
            }
        }
        
        // Séparer
        $creditors = [];
        $debtors = [];

        foreach ($balances as $b) {
            // dd($balances);
            if ($b['balance'] > 0) $creditors[] = $b;
            if ($b['balance'] < 0) $debtors[] = $b;
            }
            
            //  Générer transactions
            $transactions = [];

        foreach ($debtors as &$debtor) {
            foreach ($creditors as &$creditor) {

                if ($debtor['balance'] == 0) break;
                if ($creditor['balance'] == 0) continue;

                $amount = min(abs($debtor['balance']), $creditor['balance']);

                $transactions[] = [
                    'from_user_id' => $debtor['id'],
                    'to_user_id' => $creditor['id'],
                    'from' => $debtor['name'],
                    'to' => $creditor['name'],
                    'amount' => round($amount, 2),
                    ];
                    
                    $debtor['balance'] += $amount;
                    $creditor['balance'] -= $amount;
                    }
                    }
                    
        return [
            'balances' => $balances,
            'transactions' => $transactions
        ];
    }
}