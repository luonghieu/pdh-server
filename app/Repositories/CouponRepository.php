<?php

namespace App\Repositories;

use App\Coupon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class CastClassRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CouponRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Coupon::class;
    }


}
