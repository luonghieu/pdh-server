<?php

namespace App\Criteria\Order;

use App\Enums\OrderScope;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByScopeCriteriaCriteria.
 *
 * @package namespace App\Criteria\Order;
 */
class FilterByScopeCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $scope = request()->scope;
        $now = Carbon::parse()->format('Y-m-d');
        $model = $model->where('status', OrderStatus::OPEN);

        if ($scope == OrderScope::OPEN_TODAY) {
            $model = $model->where('date', $now);
        } elseif ($scope == OrderScope::OPEN_TOMORROW_ONWARDS) {
            $model = $model->where('date', '>', $now);
        }

        return $model;
    }
}
