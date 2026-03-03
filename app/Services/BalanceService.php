<?php

namespace App\Services;

use App\Models\Colocation;
use Carbon\Carbon;

class BalanceService
{
    public function calculateBalances(Colocation $colocation): array
    {
        $membersAll = $colocation->members()
            ->withPivot(['joined_at', 'left_at'])
            ->withTimestamps()
            ->get();

        if ($membersAll->isEmpty()) {
            return ['balances' => [], 'transactions' => []];
        }

        $membersActive = $membersAll->filter(fn ($m) => is_null($m->pivot->left_at));

        $expenses = $colocation->expenses()->orderBy('date')->orderBy('id')->get();
        $payments = $colocation->payments()->orderBy('paid_at')->orderBy('id')->get();

        $balancesCents = array_fill_keys($membersAll->pluck('id')->all(), 0);

        foreach ($expenses as $e) {
            $day = Carbon::parse($e->date)->toDateString();

            $participants = $membersAll
                ->filter(fn ($m) => $this->activeOnDay($m, $day))
                ->values();

            $count = $participants->count();
            if ($count === 0) continue;

            $amount = $this->toCents($e->amount);

            $share = intdiv($amount, $count);
            $remainder = $amount - ($share * $count);

            // payeur : + montant total
            if (isset($balancesCents[$e->user_id])) {
                $balancesCents[$e->user_id] += $amount;
            }

            // participants : - part
            foreach ($participants as $i => $p) {
                $owed = $share + ($i < $remainder ? 1 : 0);
                $balancesCents[$p->id] -= $owed;
            }
        }

        // Paiements : from + amount ; to - amount
        foreach ($payments as $p) {
            $amount = $this->toCents($p->amount);

            if (isset($balancesCents[$p->from_user_id])) $balancesCents[$p->from_user_id] += $amount;
            if (isset($balancesCents[$p->to_user_id]))   $balancesCents[$p->to_user_id]   -= $amount;
        }

        $displayBalances = $membersActive->map(function ($m) use ($balancesCents) {
            return [
                'user_id' => $m->id,
                'name' => $m->name,
                'balance' => $this->fromCents($balancesCents[$m->id] ?? 0),
            ];
        })->values()->all();

        $transactions = $this->buildTransactions($displayBalances);

        return [
            'balances' => $displayBalances,
            'transactions' => $transactions,
        ];
    }

    private function buildTransactions(array $displayBalances): array
    {
        $creditors = [];
        $debtors = [];

        foreach ($displayBalances as $b) {
            $c = $this->toCents($b['balance']);
            if ($c > 0) $creditors[] = ['cents' => $c] + $b;
            if ($c < 0) $debtors[] = ['cents' => $c] + $b;
        }

        $transactions = [];

        foreach ($debtors as &$debtor) {
            foreach ($creditors as &$creditor) {
                if ($debtor['cents'] === 0) break;
                if ($creditor['cents'] === 0) continue;

                $amount = min(abs($debtor['cents']), $creditor['cents']);

                $transactions[] = [
                    'from_user_id' => $debtor['user_id'],
                    'to_user_id' => $creditor['user_id'],
                    'from' => $debtor['name'],
                    'to' => $creditor['name'],
                    'amount' => $this->fromCents($amount),
                ];

                $debtor['cents'] += $amount;
                $creditor['cents'] -= $amount;
            }
        }

        return $transactions;
    }

    // Inclut le jour du départ
    private function activeOnDay($member, string $day): bool
    {
        $joined = Carbon::parse($member->pivot->joined_at);

        $left = $member->pivot->left_at ? Carbon::parse($member->pivot->left_at) : null;

        $joinedDay = $joined->toDateString();
        $leftDay = $left ? $left->toDateString() : null;

        return $joinedDay <= $day && (!$leftDay || $leftDay >= $day);
    }

    private function toCents($amount): int
    {
        $s = str_replace(',', '.', (string) $amount);
        $neg = false;

        if (str_starts_with($s, '-')) {
            $neg = true;
            $s = substr($s, 1);
        }

        if (!str_contains($s, '.')) {
            $c = ((int) $s) * 100;
            return $neg ? -$c : $c;
        }

        [$i, $d] = explode('.', $s, 2);
        $d = substr($d . '00', 0, 2);

        $c = ((int) $i) * 100 + (int) $d;
        return $neg ? -$c : $c;
    }

    private function fromCents(int $cents): float
    {
        return round($cents / 100, 2);
    }

    public function getUserBalance(Colocation $colocation, int $userId): float
    {
        $data = $this->calculateBalances($colocation);
        $row = collect($data['balances'])->firstWhere('user_id', $userId);

        return $row ? (float) $row['balance'] : 0.0;
    }
}