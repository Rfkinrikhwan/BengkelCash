<?php

namespace App\Repositories;

use App\Models\BookKeeping;
use App\Repositories\Interfaces\BookKeepingInterface;
use Carbon\Carbon;

class BookKeepingRepository implements BookKeepingInterface
{
    protected $model;

    public function __construct(BookKeeping $model)
    {
        $this->model = $model;
    }

    public function all(array $params = [])
    {
        $this->updateTransactionBalances($params);

        $data = $this->model;

        $month = $params['month'] ?? now()->month;
        $year = $params['year'] ?? now()->year;

        $data = $data
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc');

        // Filter
        foreach (['note', 'type', 'method_payment'] as $field) {
            if (!empty($params[$field])) {
                $data->where(
                    $field,
                    $field === 'note' ? 'LIKE' : '=',
                    $field === 'note' ? '%' . $params[$field] . '%' : $params[$field]
                );
            }
        }

        // Sorting
        $orderField = $params['order'] ?? 'date';
        $orderDirection = isset($params['ascending']) && $params['ascending'] == 0 ? 'ASC' : 'DESC';
        $data->orderBy($orderField, $orderDirection);

        $result = $data->paginate($params['limit'] ?? 25);

        return $result;
    }

    public function store(array $attributes)
    {
        return $this->model->create([
            'debit' => (int) ($attributes['debit'] ?? 0),
            'credit' => (int) ($attributes['credit'] ?? 0),
            'method_payment' => $attributes['method_payment'],
            'note' => $attributes['note'],
            'date' => $attributes['date'],
            'saldo' => $attributes['type'] === 'debit' ? $attributes['debit'] : $attributes['credit'],
            'type' => $attributes['type']
        ]);
    }

    public function update(string $id, array $attributes)
    {
        $transaction = $this->model->findOrFail($id);

        $transaction->update([
            'debit' => (int) ($attributes['debit'] ?? 0),
            'credit' => (int) ($attributes['credit'] ?? 0),
            'method_payment' => $attributes['method_payment'],
            'note' => $attributes['note'],
            'date' => $attributes['date'],
            'saldo' => $attributes['type'] === 'debit' ? $attributes['debit'] : $attributes['credit'],
            'type' => $attributes['type']
        ]);

        $this->updateTransactionBalances([
            'month' => Carbon::parse($attributes['date'])->month,
            'year' => Carbon::parse($attributes['date'])->year
        ]);

        return $transaction;
    }

    public function destroy(string $id)
    {
        $transaction = $this->model->findOrFail($id);

        $transaction->delete();
        return $transaction;
    }

    public function recordPreviousMonthSaldo()
    {
        $currentDate = Carbon::now();
        $newMonthStart = $currentDate->startOfMonth();

        $lastMonthEnd = $this->model
            ->whereDate('date', '<', $newMonthStart)
            ->orderBy('date', 'desc')
            ->first();

        if (!$lastMonthEnd) {
            return 'Tidak ada catatan saldo bulan sebelumnya.';
        }

        $lastMonthEndDate = Carbon::parse($lastMonthEnd->date);
        $lastSaldo = $lastMonthEnd->saldo;

        $existingEntry = $this->model
            ->whereDate('date', $newMonthStart->toDateString())
            ->where('note', 'LIKE', 'Sisa Saldo Bulan%')
            ->first();

        if ($existingEntry) {
            return 'Saldo untuk bulan baru sudah dicatat.';
        }

        $this->store([
            'debit' => $lastSaldo,
            'credit' => 0,
            'method_payment' => 'transfer',
            'note' => 'Sisa Saldo Bulan ' . $lastMonthEndDate->format('F Y'),
            'date' => $newMonthStart->toDateString(),
            'type' => 'debit'
        ]);

        return 'Saldo bulan sebelumnya berhasil dicatat.';
    }

    private function updateTransactionBalances(array $params = [])
    {
        $month = $params['month'] ?? \Carbon\Carbon::now()->month;
        $year = $params['year'] ?? \Carbon\Carbon::now()->year;

        // Retrieve all transactions for the selected month and year, sorted by date
        $transactions = $this->model
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->orderBy('updated_at', 'asc')
            ->get();

        if ($transactions->isNotEmpty()) {
            // Get the initial balance from the first transaction
            $currentBalance = $transactions->first()->saldo;

            // Process all transactions
            foreach ($transactions as $transaction) {
                // Skip the first transaction (already used as initial balance)
                if ($transaction->id === $transactions->first()->id) {
                    continue;
                }

                // Adjust balance based on transaction type
                if ($transaction->type === 'debit') {
                    $currentBalance += $transaction->debit;
                } else {
                    $currentBalance -= $transaction->credit;
                }

                // Update saldo for the current transaction
                $transaction->update(['saldo' => $currentBalance]);
            }
        }
    }
}
