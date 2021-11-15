<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionTypes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        if($value){
            $this->attributes['password'] = Hash::make($value);
        }
    }

        /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['isAdmin'=>$this->hasRole("Admin",'api')];
    }

    public function canExpendAmount(float $amount) {
        return $this->balance + $amount >= 0.00;
    }

    public function adjustBalanceByAmount($amount) {
        if(!$this->canExpendAmount($amount))
            throw ValidationException::withMessages(['Unsuficient funds']);

        $this->balance = $this->balance + $amount;
        $this->save();
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
    
    public function approvedTransactions() {
        return $this->transactions()->where('status', TransactionStatus::Approved);
    }

    public function expenses() {
        return $this->approvedTransactions()->where('type', TransactionTypes::ManualExpense);
    }

    public function incomes() {
        return $this->transactions()->where('type', TransactionTypes::Check);
    }

    public function approvedIncomes() {
        return $this->approvedTransactions()->where('type', TransactionTypes::Check);
    }
}
