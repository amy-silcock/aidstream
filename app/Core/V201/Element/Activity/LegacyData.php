<?php namespace App\Core\V201\Element\Activity;

/**
 * Class LegacyData
 * @package App\Core\V201\Element\Activity
 */
class LegacyData
{
    /**
     * @return description form
     */
    public function getForm()
    {
        return 'App\Core\V201\Forms\Activity\LegacyDatas';
    }

    /**
     * @return description repository
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\LegacyData');
    }
}
