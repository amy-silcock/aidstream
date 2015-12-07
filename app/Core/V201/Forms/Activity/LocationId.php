<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class LocationId
 * @package App\Core\V201\Forms\Activity
 */
class LocationId extends BaseForm
{
    protected $showFieldErrors = true;

    /**
     * builds location id form
     */
    public function buildForm()
    {
        $this
            ->add(
                'vocabulary',
                'select',
                [
                    'choices'     => $this->getCodeList('GeographicVocabulary', 'Activity'),
                    'empty_value' => 'Select one of the following option :'
                ]
            )
            ->add('code', 'text')
            ->addRemoveThisButton('remove');
    }
}
