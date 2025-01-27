<?php

namespace App\Services;

use App\Repositories\Interfaces\BookKeepingInterface;

class BookKeepingService
{
    protected $bookKeepingRepository;

    public function __construct(BookKeepingInterface $bookKeepingRepository)
    {
        $this->bookKeepingRepository = $bookKeepingRepository;
    }

    /**
     * Get all bookkeeping transactions.
     *
     * @param array $params
     *      'month' => The month to retrieve transactions for.
     *      'year'  => The year to retrieve transactions for.
     *      'ascending' => A boolean indicating whether to sort the transactions in ascending order of date.
     *      'note' => A string to filter transactions by note.
     *      'type' => A string to filter transactions by type.
     *      'method_payment' => A string to filter transactions by method payment.
     *
     * @return array
     *      An array containing a 'data' key, which is a collection of transactions, and a 'meta' key containing the total saldo.
     */
    public function all(array $params)
    {
        return $this->bookKeepingRepository->all($params);
    }

    /**
     * Store a new bookkeeping transaction.
     *
     * @param array $attributes An array containing transaction details such as 'debit', 'credit', 'method_payment', 'note', 'date', and 'type'.
     * @return mixed The created transaction record.
     */

    public function store(array $attributes)
    {
        return $this->bookKeepingRepository->store($attributes);
    }

    /**
     * Update a bookkeeping transaction.
     *
     * @param string $id The transaction record ID.
     * @param array $attributes An array containing transaction details such as 'debit', 'credit', 'method_payment', 'note', 'date', and 'type'.
     * @return mixed The updated transaction record.
     */
    public function update(string $id, array $attributes)
    {
        return $this->bookKeepingRepository->update($id, $attributes);
    }

    /**
     * Delete a bookkeeping transaction.
     *
     * @param string $id The transaction record ID to be deleted.
     * @return mixed The deleted transaction record.
     */

    public function delete(string $id)
    {
        return $this->bookKeepingRepository->destroy($id);
    }
}
