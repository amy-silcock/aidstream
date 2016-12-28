<?php namespace App\Core\V201\Requests\Organization;

/**
 * Class CreateOrgRecipientOrgBudgetRequest
 * @package App\Core\V201\Requests\Organization
 */
class CreateOrgRecipientOrgBudgetRequest extends OrganizationBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        foreach ($this->get('recipient_organization_budget') as $recipientOrganizationBudgetIndex => $recipientOrganizationBudget) {
            $recipientOrganizationBudgetForm = sprintf('recipient_organization_budget.%s', $recipientOrganizationBudgetIndex);
            $narrativeField                  = sprintf('%s.recipient_organization.0.narrative.0.narrative', $recipientOrganizationBudgetForm);
            $narrativeRuleWithoutRef         = sprintf('required_without:%s.recipient_organization.0.ref', $recipientOrganizationBudgetForm);
            $rules[$narrativeField][]        = $narrativeRuleWithoutRef;
            $rules                           = array_merge_recursive(
                $rules,
                $this->getRulesForPeriodStart($recipientOrganizationBudget['period_start'], $recipientOrganizationBudgetForm),
                $this->getRulesForPeriodEnd($recipientOrganizationBudget['period_end'], $recipientOrganizationBudgetForm),
                $this->getRulesForValue($recipientOrganizationBudget['value'], $recipientOrganizationBudgetForm),
                $this->getRulesForBudgetLine($recipientOrganizationBudget['budget_line'], $recipientOrganizationBudgetForm),
                $this->getRulesForNarrative($recipientOrganizationBudget['recipient_organization'][0]['narrative'], $recipientOrganizationBudgetForm . '.recipient_organization.0')
            );
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        foreach ($this->get('recipient_organization_budget') as $recipientOrganizationBudgetIndex => $recipientOrganizationBudget) {
            $recipientOrganizationBudgetForm = sprintf('recipient_organization_budget.%s', $recipientOrganizationBudgetIndex);
            $narrativeField                  = sprintf('%s.recipient_organization.0.narrative.0.narrative.required_without', $recipientOrganizationBudgetForm);
            $messages[$narrativeField]       = trans('validation.required_without', ['attribute' => trans('elementForm.narrative'), 'values' => trans('elementForm.ref')]);
            $messages                        = array_merge(
                $messages,
                $this->getMessagesForPeriodStart($recipientOrganizationBudget['period_start'], $recipientOrganizationBudgetForm),
                $this->getMessagesForPeriodEnd($recipientOrganizationBudget['period_end'], $recipientOrganizationBudgetForm),
                $this->getMessagesForValue($recipientOrganizationBudget['value'], $recipientOrganizationBudgetForm),
                $this->getMessagesBudgetLine($recipientOrganizationBudget['budget_line'], $recipientOrganizationBudgetForm),
                $this->getMessagesForNarrative($recipientOrganizationBudget['recipient_organization'][0]['narrative'], $recipientOrganizationBudgetForm . '.recipient_organization.0')
            );
        }

        return $messages;
    }
}
