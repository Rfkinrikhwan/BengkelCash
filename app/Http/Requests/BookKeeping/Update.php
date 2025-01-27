<?php

namespace App\Http\Requests\BookKeeping;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:255'],
            'debit' => ['required', 'integer', 'min:1'],
            'credit' => ['required', 'integer', 'min:1'],
            'method_payment' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:debit,credit'],
            'date' => ['required'],
        ];
    }
}
