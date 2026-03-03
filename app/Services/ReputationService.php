<?php

namespace App\Services;

use App\Models\User;
use App\Models\Colocation;
use App\Services\BalanceService;
use App\Models\Payment;

class ReputationService
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }
    
    
    public function handleCancel(Colocation $colocation): void
    {
        $data = $this->balanceService->calculateBalances($colocation);
        $balances = $data['balances'];
        
        foreach ($balances as $b) {
            $member = User::find($b['user_id']);
            if ($member) {
                $this->applyReputation($member, $b['balance']);
            }
        }
    }
                
                
    public function handleLeave(Colocation $colocation, User $user): void
    {
        $balance = $this->getUserBalance($colocation, $user->id);

        $this->applyReputation($user, $balance);
    }
 
    public function handleOwnerRemove(Colocation $colocation, User $owner, User $member): void
    {
        $balance = $this->getUserBalance($colocation, $member);

        if ($balance < 0) {
            $data = $this->balanceService->calculateBalances($colocation);

            foreach ($data['transactions'] as $t) {
                if ($t['from_user_id'] === $member->id) {

                    if ($t['to_user_id'] === $owner->id) {

                        Payment::create([
                            'colocation_id' => $colocation->id,
                            'from_user_id'  => $member->id,
                            'to_user_id'    => $owner->id,
                            'amount'        => $t['amount'],
                        ]);

                    } else {
                        Payment::create([
                            'colocation_id' => $colocation->id,
                            'from_user_id'  => $owner->id,  
                            'to_user_id'    => $t['to_user_id'],
                            'amount'        => $t['amount'],
                        ]); 
                    }
                    $colocation->expenses()
                                ->where('user_id', $member->id)
                                ->update(['user_id' => $owner->id]);
                }
            }
        }
    }
   
    
    protected function applyReputation(User $user, float $balance): void
    {
        if ($balance < 0) {
            $user->decrement('reputation', 1);
        } else {
            $user->increment('reputation', 1);
        }
    }

    protected function getUserBalance(Colocation $colocation, int $userId): float
    {
        $data = $this->balanceService->calculateBalances($colocation);
        $balances = $data['balances'];

        $userData = collect($balances)
            ->firstWhere('user_id', $userId);

        return $userData['balance'] ?? 0;
    }
}