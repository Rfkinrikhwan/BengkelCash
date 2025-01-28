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

        $data = $this->model->with(['user' => fn($query) => $query->select('id', 'nama')]);

        $month = $params['month'] ?? now()->month;
        $year = $params['year'] ?? now()->year;

        $data = $data
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

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
        $inputDate = Carbon::parse($attributes['date']);

        $lastTransaction = $this->model
            ->whereMonth('date', $inputDate->month)
            ->whereYear('date', $inputDate->year)
            ->orderBy('date', 'desc')
            ->first();

        $lastSaldo = $lastTransaction ? $lastTransaction->saldo : 0;
        $newSaldo = $this->calculateNewSaldo($lastSaldo, $attributes);

        return $this->model->create([
            'debit' => (int) ($attributes['debit'] ?? 0),
            'credit' => (int) ($attributes['credit'] ?? 0),
            'method_payment' => $attributes['method_payment'],
            'note' => $attributes['note'],
            'date' => $attributes['date'],
            'saldo' => $newSaldo,
            'type' => $attributes['type']
        ]);
    }

    public function update(string $id, array $attributes)
    {
        $transaction = $this->model->findOrFail($id);
        $newSaldo = $this->calculateNewSaldo(0, $attributes);

        $transaction->update([
            'debit' => (int) ($attributes['debit'] ?? 0),
            'credit' => (int) ($attributes['credit'] ?? 0),
            'method_payment' => $attributes['method_payment'],
            'note' => $attributes['note'],
            'date' => $attributes['date'],
            'saldo' => $newSaldo,
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

    private function calculateNewSaldo($lastSaldo, array $attributes)
    {
        if ($attributes['type'] === 'debit') {
            return $lastSaldo + (int) ($attributes['debit'] ?? 0);
        } elseif ($attributes['type'] === 'credit') {
            return $lastSaldo - (int) ($attributes['credit'] ?? 0);
        }
        return $lastSaldo;
    }

    private function updateTransactionBalances(array $params = [])
    {
        $month = $params['month'] ?? Carbon::now()->month;
        $year = $params['year'] ?? Carbon::now()->year;

        $transactions = $this->model
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        $currentBalance = $transactions->first()->saldo;

        foreach ($transactions->slice(1) as $transaction) {
            $currentBalance = $this->calculateNewSaldo(
                $currentBalance,
                $transaction->toArray()
            );
            $transaction->update(['saldo' => $currentBalance]);
        }
    }
}
