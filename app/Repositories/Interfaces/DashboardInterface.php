<?php

namespace App\Repositories\Interfaces;

interface DashboardInterface
{
    public function retrieveTotalOmset(array $params = []);
    public function retrieveChartGrowthOmset(array $params = []);
}
