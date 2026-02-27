<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\expenses;

class Colocation extends Model
{
    protected $fillable = [
        'name',
        'status',
        'owner_id',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'colocation_user')
                    ->withPivot('role', 'joined_at', 'left_at')
                    ->withTimestamps();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function isOwner($userId)
    {
        return $this->members()
            ->where('users.id', $userId)
            ->wherePivot('role', 'owner')
            ->wherePivotNull('left_at')
            ->exists();
    }
}