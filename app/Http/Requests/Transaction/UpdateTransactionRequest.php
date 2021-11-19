<?php

namespace App\Http\Requests\Transaction;

use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->user()->can('all_transactions.validate.*');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $transaction = $this->transaction;
        return [
            'status'=>['required','in:1,2', function ($attribute, $value, $fail) use ($transaction) {
                if($transaction->status != TransactionStatus::Pending)
                    $fail($attribute.' has already been defined.');
                }
            ]
        ];
    }
}
