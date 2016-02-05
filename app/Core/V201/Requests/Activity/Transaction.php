<?php namespace App\Core\V201\Requests\Activity;

use App\Core\V201\Repositories\Activity\UploadTransaction;

/**
 * Class Transaction
 * @package App\Core\V201\Requests\Activity
 */
class Transaction extends ActivityBaseRequest
{
    /**
     * @var UploadTransaction
     */
    protected $transactionRepo;

    /**
     * Transaction constructor.
     * @param UploadTransaction $transactionRepo
     */
    public function __construct(UploadTransaction $transactionRepo)
    {
        parent::__construct();
        $this->transactionRepo = $transactionRepo;
    }

    public function rules()
    {
        return $this->getTransactionRules($this->get('transaction'));
    }

    /**
     * prepare the error message
     * @return array
     */
    public function messages()
    {
        return $this->getTransactionMessages($this->get('transaction'));
    }

    /**
     * returns rules for transaction
     * @param $formFields
     * @return array|mixed
     */
    protected function getTransactionRules($formFields)
    {
        $rules = [];

        foreach ($formFields as $transactionIndex => $transaction) {
            $transactionForm = sprintf('transaction.%s', $transactionIndex);
            $transactionId   = $this->segment(4);
            $activityId      = $this->segment(2);
            $references      = ($transactionId) ? $this->transactionRepo->getTransactionReferencesExcept(
                $activityId,
                $transactionId
            ) : $this->transactionRepo->getTransactionReferences($activityId);

            $transactionReferences = [];
            foreach ($references as $referenceKey => $reference) {
                $transactionReferences[] = $referenceKey;
            }

            $transactionReference                             = implode(',', $transactionReferences);
            $rules                                            = [];
            $rules[sprintf('%s.reference', $transactionForm)] = 'required|not_in:' . $transactionReference;

            $rules = array_merge(
                $rules,
                $this->getTransactionTypeRules($transaction['transaction_type'], $transactionForm),
                $this->getTransactionDateRules($transaction['transaction_date'], $transactionForm),
                $this->getValueRules($transaction['value'], $transactionForm),
                $this->getDescriptionRules($transaction['description'], $transactionForm),
                $this->getSectorsRules($transaction['sector'], $transactionForm),
                $this->getRecipientRegionRules($transaction['recipient_region'], $transactionForm)
            );
        }

        return $rules;
    }

    /**
     * returns messages for transaction
     * @param $formFields
     * @return array|mixed
     */
    protected function getTransactionMessages($formFields)
    {
        $messages = [];

        foreach ($formFields as $transactionIndex => $transaction) {
            $transactionForm                                              = sprintf('transaction.%s', $transactionIndex);
            $messages[sprintf('%s.reference.required', $transactionForm)] = 'Reference is required';
            $messages[sprintf('%s.reference.not_in', $transactionForm)]   = 'Reference should be unique';

            $messages = array_merge(
                $messages,
                $this->getTransactionTypeMessages($transaction['transaction_type'], $transactionForm),
                $this->getTransactionDateMessages($transaction['transaction_date'], $transactionForm),
                $this->getValueMessages($transaction['value'], $transactionForm),
                $this->getDescriptionMessages($transaction['description'], $transactionForm),
                $this->getSectorsMessages($transaction['sector'], $transactionForm),
                $this->getRecipientRegionMessages($transaction['recipient_region'], $transactionForm)
            );
        }

        return $messages;
    }

    /**
     * get transaction type rules
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getTransactionTypeRules($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $typeIndex => $type) {
            $typeForm                                              = sprintf('%s.transaction_type.%s', $formBase, $typeIndex);
            $rules[sprintf('%s.transaction_type_code', $typeForm)] = 'required';
        }

        return $rules;
    }

    /**
     * get transaction type error message
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getTransactionTypeMessages($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $typeIndex => $type) {
            $typeForm                                                          = sprintf('%s.transaction_type.%s', $formBase, $typeIndex);
            $messages[sprintf('%s.transaction_type_code.required', $typeForm)] = 'Transaction type is required';
        }

        return $messages;
    }

    /**
     * get transaction date rules
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getTransactionDateRules($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $dateIndex => $date) {
            $dateForm                             = sprintf('%s.transaction_date.%s', $formBase, $dateIndex);
            $rules[sprintf('%s.date', $dateForm)] = 'required';
        }

        return $rules;
    }

    /**
     * get transaction date error message
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getTransactionDateMessages($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $dateIndex => $date) {
            $dateForm                                         = sprintf('%s.transaction_date.%s', $formBase, $dateIndex);
            $messages[sprintf('%s.date.required', $dateForm)] = 'Date is required';
        }

        return $messages;
    }

    /**
     * get values rules
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getValueRules($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $valueIndex => $value) {
            $valueForm                               = sprintf('%s.value.%s', $formBase, $valueIndex);
            $rules[sprintf('%s.amount', $valueForm)] = 'required|numeric';
            $rules[sprintf('%s.date', $valueForm)]   = 'required';
        }

        return $rules;
    }

    /**
     * get value error message
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getValueMessages($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $valueIndex => $value) {
            $valueForm                                           = sprintf('%s.value.%s', $formBase, $valueIndex);
            $messages[sprintf('%s.amount.required', $valueForm)] = 'Amount is required';
            $messages[sprintf('%s.amount.numeric', $valueForm)]  = 'Amount must be numeric';
            $messages[sprintf('%s.date.required', $valueForm)]   = 'Date is required';
        }

        return $messages;
    }

    /**
     * get description rules
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getDescriptionRules($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $descriptionIndex => $description) {
            $narrativeForm = sprintf('%s.description.%s', $formBase, $descriptionIndex);
            $rules         = array_merge(
                $rules,
                $this->getRulesForNarrative($description['narrative'], $narrativeForm)
            );
        }

        return $rules;
    }

    /**
     * get description error message
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getDescriptionMessages($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $descriptionIndex => $description) {
            $narrativeForm = sprintf('%s.description.%s', $formBase, $descriptionIndex);
            $messages      = array_merge(
                $messages,
                $this->getMessagesForNarrative($description['narrative'], $narrativeForm)
            );
        }

        return $messages;
    }

    /**
     * returns rules for sector
     * @param $formFields
     * @param $formBase
     * @return array|mixed
     */
    public function getSectorsRules($formFields, $formBase)
    {
        $rules = [];

        foreach ($formFields as $sectorIndex => $sector) {
            $sectorForm                                          = sprintf('%s.sector.%s', $formBase, $sectorIndex);
            $rules[sprintf('%s.sector_vocabulary', $sectorForm)] = 'required';
            if ($sector['sector_vocabulary'] == 1) {
                $rules[sprintf('%s.sector_code', $sectorForm)] = 'required';
            } elseif ($sector['sector_vocabulary'] == 2) {
                $rules[sprintf('%s.sector_category_code', $sectorForm)] = 'required';
            } else {
                $rules[sprintf('%s.sector_text', $sectorForm)] = 'required';
            }
            $rules = array_merge($rules, $this->getRulesForNarrative($sector['narrative'], $sectorForm));
        }

        return $rules;
    }

    /**
     * returns messages for sector
     * @param $formFields
     * @param $formBase
     * @return array|mixed
     */
    public function getSectorsMessages($formFields, $formBase)
    {
        $messages = [];

        foreach ($formFields as $sectorIndex => $sector) {
            $sectorForm                                                            = sprintf('%s.sector.%s', $formBase, $sectorIndex);
            $messages[sprintf('%s.sector_vocabulary.%s', $sectorForm, 'required')] = 'Sector Vocabulary is required.';
            if ($sector['sector_vocabulary'] == 1) {
                $messages[sprintf('%s.sector_code.%s', $sectorForm, 'required')] = 'Sector is required.';
            } elseif ($sector['sector_vocabulary'] == 2) {
                $messages[sprintf('%s.sector_category_code.%s', $sectorForm, 'required')] = 'Sector is required.';
            } else {
                $messages[sprintf('%s.sector_text.%s', $sectorForm, 'required')] = 'Sector is required.';
            }
            $messages = array_merge($messages, $this->getMessagesForNarrative($sector['narrative'], $sectorForm));
        }

        return $messages;
    }

    /**
     * returns rules for recipient region
     * @param $formFields
     * @param $formBase
     * @return array|mixed
     */
    public function getRecipientRegionRules($formFields, $formBase)
    {
        $rules = [];

        foreach ($formFields as $recipientRegionIndex => $recipientRegion) {
            $recipientRegionForm                          = sprintf('%s.recipient_region.%s', $formBase, $recipientRegionIndex);
            $rules[$recipientRegionForm . '.region_code'] = 'required';
            $rules                                        = array_merge($rules, $this->getRulesForNarrative($recipientRegion['narrative'], $recipientRegionForm));
        }

        return $rules;
    }

    /**
     * returns messages for recipient region
     * @param $formFields
     * @param $formBase
     * @return array|mixed
     */
    public function getRecipientRegionMessages($formFields, $formBase)
    {
        $messages = [];

        foreach ($formFields as $recipientRegionIndex => $recipientRegion) {
            $recipientRegionForm                                      = sprintf('%s.recipient_region.%s', $formBase, $recipientRegionIndex);
            $messages[$recipientRegionForm . '.region_code.required'] = 'Recipient region code is required';
            $messages                                                 = array_merge($messages, $this->getMessagesForNarrative($recipientRegion['narrative'], $recipientRegionForm));
        }

        return $messages;
    }
}
