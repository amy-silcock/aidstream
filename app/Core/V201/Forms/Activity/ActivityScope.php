<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class ActivityScope
 * @package App\Core\V201\Forms\Activity
 */
class ActivityScope extends BaseForm
{
    protected $showFieldErrors = true;

    /**
     * builds activity scope form
     */
    public function buildForm()
    {
        $this->addSelect('activity_scope', $this->getCodeList('ActivityScope', 'Activity'), trans('elementForm.activity_scope'), $this->addHelpText('Activity_ActivityScope-code'), null, true);
    }
}
