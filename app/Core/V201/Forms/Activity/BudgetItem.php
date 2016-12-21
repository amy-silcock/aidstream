<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class BudgetItem
 * @package App\Core\V201\Forms\Activity
 */
class BudgetItem extends BaseForm
{
    /**
     * builds the activity budget item form
     */
    public function buildForm()
    {
        $this
            ->add(
                'code_text',
                'text',
                [
                    'label'      => trans('elementForm.code'),
                    'wrapper'    => ['class' => 'form-group code_text codes'],
                    'help_block' => $this->addHelpText('Activity_CountryBudgetItems_BudgetItem-non_iati'),
                    'required'   => true
                ]
            )
            ->add(
                'code',
                'select',
                [
                    'label'       => trans('elementForm.code'),
                    'choices'     => $this->getCodeList('BudgetIdentifier', 'Activity'),
                    'empty_value' => trans('elementForm.select_text'),
                    'wrapper'     => ['class' => 'form-group code codes hidden'],
                    'help_block'  => $this->addHelpText('Activity_CountryBudgetItems_BudgetItem-non_iati'),
                    'required'    => true
                ]
            )
            ->addPercentage($this->addHelpText('Activity_CountryBudgetItems_BudgetItem-percentage'))
            ->addCollection('description', 'Activity\BudgetItemDescription', 'description', [], trans('elementForm.description'))
            ->addRemoveThisButton('remove_budget_item');
    }
}
