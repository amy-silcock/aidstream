<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class LocationReach
 * @package App\Core\V201\Forms\Activity
 */
class LocationReach extends BaseForm
{
    protected $showFieldErrors = true;

    /**
     * builds location reach form
     */
    public function buildForm()
    {
        $this
            ->add(
                'code',
                'select',
                [
                    'choices'     => $this->getCodeList('GeographicLocationReach', 'Activity'),
                    'empty_value' => 'Select one of the following option :'
                ]
            );
    }
}
