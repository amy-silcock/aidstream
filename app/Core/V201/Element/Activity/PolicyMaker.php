<?php namespace App\Core\V201\Element\Activity;

use App\Core\Elements\BaseElement;

/**
 * Class PolicyMaker
 * @package App\Core\V201\Element\Activity
 */
class PolicyMaker extends BaseElement
{

    /**
     * @return string
     */
    public function getForm()
    {
        return 'App\Core\V201\Forms\Activity\PolicyMakers';
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\PolicyMaker');
    }

    /**
     * @param $activity
     * @return array
     */
    public function getXmlData($activity)
    {
        $activityData = [];
        $policyMakers = (array) $activity->policy_maker;
        foreach ($policyMakers as $policyMaker) {
            $activityData[] = [
                '@attribute' => [
                    'code'         => $policyMaker['policy_marker'],
                    'significance' => $policyMaker['significance']
                ],
                'narrative'  => $this->buildNarrative($policyMaker['narrative'])
            ];
        }

        return $activityData;
    }
}
