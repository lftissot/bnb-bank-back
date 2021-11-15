<?php

namespace App\Models;

use App\Enums\FileTypes;
use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use App\Enums\TransactionTypes;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['amount','type','status','executed_on','description', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function file() {
        return $this->morphOne(File::class,'fileable');
    }

    public function check() {
        return $this->morphOne(File::class, 'fileable')->where('type', FileTypes::Check);
    }

    public function checkConsolidation() {
        if($this->shouldConsolidate()) {
            $this->user->adjustBalanceByAmount($this->amount);
        }
    }

    public function shouldConsolidate() {
        $wasPending = $this->getOriginal('status') == TransactionStatus::Pending;
        $isntPending = $this->status == TransactionStatus::Approved;
        return $wasPending && $isntPending;
    }
}
