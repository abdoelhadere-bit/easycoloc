<?php

namespace App\Services;

use App\Models\User;
use App\Models\Colocation;

class ReputationService
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function handleLeave(Colocation $colocation, User $user): void
    {
        $balance = $this->getUserBalance($colocation, $user);

        $this->applyReputation($user, $balance);
    }

 
    public function handleCancel(Colocation $colocation): void
    {
        $balances = $this->balanceService->calculateBalances($colocation);

        foreach ($balances as $b) {
            $member = User::find($b['user_id']);
            if ($member) {
                $this->applyReputation($member, $b['balance']);
            }
        }
    }

 
    public function handleOwnerRemove(Colocation $colocation, User $owner, User $member): void {

        $balance = $this->getUserBalance($colocation, $member);

        if ($balance < 0) {

            // Imputer dette au owner (ajustement interne)
            $colocation->expenses()->create([
                'user_id' => $owner->id,
                'title' => 'Dette absorbée - membre retiré',
                'amount' => abs($balance),
                'date' => now(),
                'category_id' => null
            ]);
        }

    }

    /**
     * Appliquer règle -1 / +1
     */
    protected function applyReputation(User $user, float $balance): void
    {
        if ($balance < 0) {
            $user->decrement('reputation', 1);
        } else {
            $user->increment('reputation', 1);
        }
    }

    protected function getUserBalance(Colocation $colocation, User $user): float
    {
        $balances = $this->balanceService->calculateBalances($colocation);

        $userData = collect($balances)
            ->firstWhere('user_id', $user->id);

        return $userData['balance'] ?? 0;
    }
}