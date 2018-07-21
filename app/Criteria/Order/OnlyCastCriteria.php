<?php

namespace App\Criteria\Order;

use App\Enums\UserType;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class OnlyCastCriteria.
 *
 * @package namespace App\Criteria\Order;
 */
class OnlyCastCriteria implements CriteriaInterface
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
        return $model->whereHas('user', function ($query) {
            $query->where('type', UserType::CAST);
        });
    }
}
