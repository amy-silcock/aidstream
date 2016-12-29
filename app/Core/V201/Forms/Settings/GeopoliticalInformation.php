<?php namespace App\Core\V201\Forms\Settings;

use App\Core\Form\BaseForm;

/**
 * Class GeopoliticalInformation
 * @package App\Core\V201\Forms\Settings
 */
class GeopoliticalInformation extends BaseForm
{
    /**
     * build geo political information form
     */
    public function buildForm()
    {
        $this
            ->addCheckBox('recipient_country', trans('element.recipient_country'), true, 'readonly')
            ->addCheckBox('recipient_region', trans('element.recipient_region'))
            ->addCheckBox('location', trans('element.location'));
    }
}
