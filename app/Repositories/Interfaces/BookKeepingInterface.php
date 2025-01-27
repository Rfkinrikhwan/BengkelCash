<?php

namespace App\Repositories\Interfaces;

interface BookKeepingInterface
{
    public function all(array $params = []);
    public function store(array $attributes);
    public function update(string $id, array $attributes);
    public function destroy(string $id);
    public function recordPreviousMonthSaldo();
}
