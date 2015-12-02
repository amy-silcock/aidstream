<?php namespace App\Core\V201\Element\Activity;

use App\Core\Elements\BaseElement;

/**
 * Class Condition
 * @package App\Core\V201\Element\Activity
 */
class Condition extends BaseElement
{
    /**
     * @return condition form
     */
    public function getForm()
    {
        return 'App\Core\V201\Forms\Activity\Conditions';
    }

    /**
     * @return condition repository
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\Condition');
    }

    /**
     * @param $activity
     * @return array
     */
    public function getXmlData($activity)
    {
        $activityData   = [];
        $conditions     = (array) $activity->conditions;
        $activityData[] = [
            '@attributes' => [
                'attached' => $conditions['condition_attached']
            ],
            'condition'   => $this->buildCondition($conditions['condition'])
        ];

        return $activityData;
    }

    /**
     * @param $conditions
     * @return array
     */
    public function buildCondition($conditions)
    {
        foreach ($conditions as $condition) {
            $conditionData[] = [
                '@attributes' => [
                    'type' => $condition['condition_type']
                ],
                'narrative'   => $this->buildNarrative($condition['narrative'])
            ];
        }

        return $conditionData;
    }
}
