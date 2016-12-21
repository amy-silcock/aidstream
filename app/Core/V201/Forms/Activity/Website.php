<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class Website
 * @package App\Core\V201\Forms\Activity
 */
class Website extends BaseForm
{
    /**
     * builds the contact info Website form
     */
    public function buildForm()
    {
        $this
            ->add('website', 'text', ['label' => trans('elementForm.website'), 'help_block' => $this->addHelpText('Activity_ContactInfo_Website-text')])
            ->addRemoveThisButton('remove_website');
    }
}
