<?php namespace App\Services\CsvImporter\Entities\Activity\Components\Elements;


use App\Services\CsvImporter\Entities\Activity\Components\Elements\Foundation\Iati\Element;
use App\Services\CsvImporter\Entities\Activity\Components\Factory\Validation;

/**
 * Class Budget
 * @package App\Services\CsvImporter\Entities\Activity\Components\Elements
 */
class Budget extends Element
{
    /**
     * Csv Header for Budget element.
     * @var array
     */
    private $_csvHeaders = ['budget_type', 'budget_status', 'budget_period_start', 'budget_period_end', 'budget_value', 'budget_value_date', 'budget_currency'];

    /**
     * Index under which the data is stored within the object.
     * @var string
     */
    protected $index = 'budget';

    /**
     * Budget constructor.
     * @param            $fields
     * @param Validation $factory
     */
    public function __construct($fields, Validation $factory)
    {
        $this->prepare($fields);
        $this->factory = $factory;
    }

    /**
     * Prepare the IATI Element.
     * @param $fields
     */
    protected function prepare($fields)
    {
        foreach ($fields as $key => $values) {
            if (!is_null($values) && array_key_exists($key, array_flip($this->_csvHeaders))) {
                foreach ($values as $index => $value) {
                    $this->map($key, $value, $index);
                }
            }
        }
    }

    /**
     * Map data from CSV file into Budget data format.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function map($key, $value, $index)
    {
        if (!(is_null($value) || $value === "")) {
            $this->setBudgetType($key, $value, $index);
            $this->setBudgetStatus($key, $value, $index);
            $this->setBudgetPeriodStart($key, $value, $index);
            $this->setBudgetPeriodEnd($key, $value, $index);
            $this->setBudgetValue($key, $value, $index);
            $this->setBudgetCurrency($key, $value, $index);
        }
    }

    /**
     * Set Budget type for Budget Element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setBudgetType($key, $value, $index)
    {
        if (!isset($this->data['budget'][$index]['budget_type'])) {
            $this->data['budget'][$index]['budget_type'] = '';
        }
        if ($key == $this->_csvHeaders[0]) {
            $validBudgetTypes = $this->loadCodeList('BudgetType', 'V201');

            foreach ($validBudgetTypes['BudgetType'] as $budgetType) {
                if (ucwords($value) == $budgetType['name']) {
                    $value = $budgetType['code'];
                    break;
                }
            }

            $this->data['budget'][$index]['budget_type'] = $value;
        }
    }

    /**
     * Set Budget status for Budget Element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setBudgetStatus($key, $value, $index)
    {
        if (!isset($this->data['budget'][$index]['status'])) {
            $this->data['budget'][$index]['status'] = '';
        }
        if ($key == $this->_csvHeaders[1]) {
            $validBudgetStatus = $this->loadCodeList('BudgetStatus', 'V202');

            foreach ($validBudgetStatus['BudgetStatus'] as $budgetStatus) {
                if (ucwords($value) == $budgetStatus['name']) {
                    $value = $budgetStatus['code'];
                    break;
                }
            }
            $this->data['budget'][$index]['status'] = $value;
        }
    }

    /**
     * Set Budget period start for Budget Element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setBudgetPeriodStart($key, $value, $index)
    {
        if (!isset($this->data['budget'][$index]['period_start'][0]['date'])) {
            $this->data['budget'][$index]['period_start'][0]['date'] = '';
        }
        if ($key == $this->_csvHeaders[2]) {
            $this->data['budget'][$index]['period_start'][0]['date'] = $value;
        }
    }

    /**
     * Set Budget period end for Budget Element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setBudgetPeriodEnd($key, $value, $index)
    {
        if (!isset($this->data['budget'][$index]['period_end'][0]['date'])) {
            $this->data['budget'][$index]['period_end'][0]['date'] = '';
        }
        if ($key == $this->_csvHeaders[3]) {
            $this->data['budget'][$index]['period_end'][0]['date'] = $value;
        }
    }

    /**
     * Set Budget value and value date for Budget Element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setBudgetValue($key, $value, $index)
    {
        if (!isset($this->data['budget'][$index]['value'])) {
            $this->data['budget'][$index]['value'] = '';
        }
        if ($key == $this->_csvHeaders[4]) {
            $this->data['budget'][$index]['value'][0]['amount'] = $value;
        }
        if ($key == $this->_csvHeaders[5]) {
            $this->data['budget'][$index]['value'][0]['value_date'] = $value;
        }
    }

    /**
     * Set Budget currency for Budget Element
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setBudgetCurrency($key, $value, $index)
    {
        if (!isset($this->data['budget'][$index]['value'][0]['currency'])) {
            $this->data['budget'][$index]['value'][0]['currency'] = '';
        }
        if ($key == $this->_csvHeaders[6]) {
            $this->data['budget'][$index]['value'][0]['currency'] = strtoupper($value);
        }

    }

    /**
     * Validate data for IATI Element.
     */
    public function validate()
    {
        $this->validator = $this->factory->sign($this->data())
                                         ->with($this->rules(), $this->messages())
                                         ->getValidatorInstance();

        $this->setValidity();

        return $this;
    }

    /**
     * Provides the IATI Currency code list.
     * @return string
     */
    protected function currencyCodeList()
    {
        $currencyList = $this->loadCodeList('Currency', 'V201');

        $codes = [];
        foreach ($currencyList['Currency'] as $code) {
            $codes[] = $code['code'];
        }

        return implode(',', $codes);

    }

    /**
     * Provides the rules for the IATI Element validation.
     * @return array
     */
    public function rules()
    {
        $rules = ['budget' => 'start_before_end_date|diff_one_year'];

        foreach (getVal($this->data(), ['budget'], []) as $key => $value) {
            $rules['budget.' . $key . '.budget_type']         = sprintf('in:%s', $this->budgetCodeListWithValue('BudgetType'));
            $rules['budget.' . $key . '.status']              = sprintf(
                'required_unless:%s,%s,%s,%s,%s,%s,%s,%s,%s,%s|in:%s',
                'budget.' . $key . '.budget_type',
                '',
                'budget.' . $key . '.period_start.0.date',
                '',
                'budget.' . $key . '.period_end.0.date',
                '',
                'budget.' . $key . '.value.0.amount',
                '',
                'budget.' . $key . '.value.0.value_date',
                '',
                $this->budgetCodeListWithValue('BudgetStatus', 'V202')
            );
            $rules['budget.' . $key . '.period_start.0.date'] = sprintf(
                'required_unless:%s,%s,%s,%s,%s,%s,%s,%s,%s,%s|date_format:Y-m-d',
                'budget.' . $key . '.budget_type',
                '',
                'budget.' . $key . '.status',
                '',
                'budget.' . $key . '.period_end.0.date',
                '',
                'budget.' . $key . '.value.0.amount',
                '',
                'budget.' . $key . '.value.0.value_date',
                ''
            );
            $rules['budget.' . $key . '.period_end.0.date']   = sprintf(
                'required_unless:%s,%s,%s,%s,%s,%s,%s,%s,%s,%s|date_format:Y-m-d',
                'budget.' . $key . '.budget_type',
                '',
                'budget.' . $key . '.status',
                '',
                'budget.' . $key . '.period_start.0.date',
                '',
                'budget.' . $key . '.value.0.amount',
                '',
                'budget.' . $key . '.value.0.value_date',
                ''
            );
            $rules['budget.' . $key . '.value.0.amount']      = sprintf(
                'required_unless:%s,%s,%s,%s,%s,%s,%s,%s,%s,%s|numeric|min:0',
                'budget.' . $key . '.budget_type',
                '',
                'budget.' . $key . '.status',
                '',
                'budget.' . $key . '.period_start.0.date',
                '',
                'budget.' . $key . '.period_end.0.date',
                '',
                'budget.' . $key . '.value.0.value_date',
                ''
            );
            $rules['budget.' . $key . '.value.0.value_date']  = sprintf(
                'required_unless:%s,%s,%s,%s,%s,%s,%s,%s,%s,%s|date_format:Y-m-d',
                'budget.' . $key . '.budget_type',
                '',
                'budget.' . $key . '.status',
                '',
                'budget.' . $key . '.period_start.0.date',
                '',
                'budget.' . $key . '.period_end.0.date',
                '',
                'budget.' . $key . '.value.0.amount',
                ''
            );
            $rules['budget.' . $key . '.value.0.currency']    = sprintf('in:%s', $this->currencyCodeList());
        }

        return $rules;
    }

    /**
     * Provides custom messages used for IATI Element Validation.
     * @return array
     */
    public function messages()
    {
        $messages = [
            'budget.start_before_end_date' => 'Budget Period Start Date should be before Budget Period End Date.',
            'budget.diff_one_year'         => 'The difference of Budget Period Start Date and Budget Period End Date should not exceed 1 year.'
        ];

        foreach (getVal($this->data(), ['budget'], []) as $key => $value) {
            $messages['budget.' . $key . '.budget_type.in']                      = trans('validation.code_list', ['attribute' => trans('elementForm.budget_type')]);
            $messages['budget.' . $key . '.status.required_unless']              = trans('validation.required', ['attribute' => trans('elementForm.budget_status')]);
            $messages['budget.' . $key . '.status.in']                           = trans('validation.code_list', ['attribute' => trans('element.budget_status')]);
            $messages['budget.' . $key . '.period_start.0.date.date_format']     = trans('validation.csv_date', ['attribute' => trans('elementForm.budget_period_start_date')]);
            $messages['budget.' . $key . '.period_start.0.date.required_unless'] = trans('validation.required', ['attribute' => trans('elementForm.budget_period_start_date')]);
            $messages['budget.' . $key . '.period_end.0.date.date_format']       = trans('validation.budget_period_end_date', ['attribute' => trans('elementForm.budget_period_end_date')]);
            $messages['budget.' . $key . '.period_end.0.date.required_unless']   = trans('validation.required', ['attribute' => trans('elementForm.budget_period_end_date')]);
            $messages['budget.' . $key . '.value.0.amount.required_unless']      = trans('validation.required', ['attribute' => trans('elementForm.budget_value')]);
            $messages['budget.' . $key . '.value.0.amount.numeric']              = trans('validation.numeric', ['attribute' => trans('elementForm.budget_value')]);
            $messages['budget.' . $key . '.value.0.amount.min']                  = trans('validation.negative', ['attribute' => trans('elementForm.budget_value')]);
            $messages['budget.' . $key . '.value.0.value_date.required_unless']  = trans('validation.required', ['attribute' => trans('elementForm.budget_value_date')]);
            $messages['budget.' . $key . '.value.0.value_date.date_format']      = trans('validation.csv_date', ['attribute' => trans('elementForm.budget_value_date')]);
            $messages['budget.' . $key . '.value.0.currency.in']                 = trans('validation.code_list', ['attribute' => trans('elementForm.budget_currency_code')]);
        }

        return $messages;
    }

    /**
     * Get the valid BudgetCodes from the Budget codelist as a string.
     * @param        $codeList
     * @param string $version
     * @return string
     */
    protected function budgetCodeListWithValue($codeList, $version = 'V201')
    {
        list($budgetCodeList, $codes) = [$this->loadCodeList($codeList, $version), []];

        array_walk(
            $budgetCodeList[$codeList],
            function ($activityStatus) use (&$codes) {
                $codes[] = $activityStatus['code'];
                $codes[] = $activityStatus['name'];
            }
        );

        return implode(',', array_keys(array_flip($codes)));
    }
}