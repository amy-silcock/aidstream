<?php namespace App\Core\V201\Traits\Forms\Result;

/**
 * Class Result
 * @package App\Core\V201\Forms\Activity
 */
trait Result
{

    /**
     * Return result form
     * @return mixed
     */
    public function addResults()
    {
        return $this->addCollection('result', 'Activity\Result', 'result');
    }

    /**
     * Return type field
     * @return mixed
     */
    public function addTypeList()
    {
        return $this->addSelect('type', $this->getCodeList('ResultType', 'Activity'));
    }

    /**
     * Return aggregation status field
     * @return mixed
     */
    public function addAggregationStatusList()
    {
        return $this->addSelect('aggregation_status', [0 => 'False', 1 => 'True']);
    }
}
