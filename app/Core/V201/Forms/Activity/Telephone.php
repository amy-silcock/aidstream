<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class Telephone
 * @package App\Core\V201\Forms\Activity
 */
class Telephone extends BaseForm
{
    /**
     * builds the contact info telephone form
     */
    public function buildForm()
    {
        $this
            ->add('telephone', 'text', ['label' => trans('elementForm.telephone'), 'help_block' => $this->addHelpText('Activity_ContactInfo_Telephone-text')])
            ->addRemoveThisButton('remove_telephone');
    }
}
