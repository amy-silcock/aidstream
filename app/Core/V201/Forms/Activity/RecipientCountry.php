<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class RecipientCountry
 * @package App\Core\V201\Forms\Activity
 */
class RecipientCountry extends BaseForm
{
    protected $showFieldErrors = true;

    /**
     * builds recipient country form
     */
    public function buildForm()
    {
        $this
            ->addSelect('country_code', $this->getCodeList('Country', 'Organization'), trans('elementForm.country_code'), $this->addHelpText('Activity_RecipientCountry-code'), null, true)
            ->addPercentage($this->addHelpText('Activity_RecipientCountry-percentage'))
            ->addNarrative('narrative')
            ->addAddMoreButton('add_narrative', 'narrative')
            ->addRemoveThisButton('remove_recipient_country');
    }
}
