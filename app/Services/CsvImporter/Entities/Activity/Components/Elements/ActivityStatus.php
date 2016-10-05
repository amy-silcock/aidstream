<?php namespace App\Services\CsvImporter\Entities\Activity\Components\Elements;

use App\Services\CsvImporter\Entities\Activity\Components\Elements\Foundation\Iati\Element;
use App\Services\CsvImporter\Entities\Activity\Components\Factory\Validation;

/**
 * Class ActivityStatus
 * @package App\Services\CsvImporter\Entities\Activity\Components\Elements
 */
class ActivityStatus extends Element
{
    /**
     * CSV Header of Description with their code.
     */
    private $_csvHeader = ['activity_status'];

    /**
     * Index under which the data is stored within the object.
     * @var string
     */
    protected $index = 'activity_status';

    protected $data;

    /**
     * Description constructor.
     * @param            $fields
     * @param Validation $factory
     */
    public function __construct($fields, Validation $factory)
    {
        $this->prepare($fields);
        $this->factory = $factory;
    }

    /**
     * Prepare the ActivityStatus element.
     * @param $fields
     */
    public function prepare($fields)
    {
        foreach ($fields as $key => $values) {
            if (!is_null($values) && array_key_exists($key, array_flip($this->_csvHeader))) {
                foreach ($values as $value) {
                    $this->map($value, $values);
                }
            }
        }
    }

    /**
     * Map data from CSV into ActivityStatus data format.
     * @param $value
     * @param $values
     */
    public function map($value, $values)
    {
        if (!(is_null($value) || $value == "")) {
            $validActivityStatus = $this->loadCodeList('ActivityStatus', 'V201');

            foreach ($validActivityStatus['ActivityStatus'] as $status) {
                if (ucwords($value) == $status['name']) {
                    $value = $status['code'];
                    break;
                }
            }
            (count(array_filter($values)) == 1) ? $this->data[$this->csvHeader()] = $value : $this->data[$this->csvHeader()][] = $value;
        }
    }

    /**
     * Provides the rules for the IATI Element validation.
     * @return array
     */
    public function rules()
    {
        return [
            $this->csvHeader()                  => 'required|size:1',
            sprintf('%s.*', $this->csvHeader()) => sprintf('in:%s', $this->validActivityStatus())
        ];
    }

    /**
     * Provides custom messages used for IATI Element Validation.
     * @return array
     */
    public function messages()
    {
        $key = $this->csvHeader();

        return [
            sprintf('%s.required', $key) => 'Activity Status is required.',
            sprintf('%s.size', $key)     => 'Multiple Activity Statuses are not allowed.',
            sprintf('%s.*.in', $key)       => 'Only valid Activity Status codes are allowed.'
        ];
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
     * Get the Csv header for ActivityStatus.
     * @return mixed
     */
    protected function csvHeader()
    {
        return end($this->_csvHeader);
    }

    /**
     * Get the valid ActivityStatus from the ActivityStatus codelist as a string.
     * @return string
     */
    protected function validActivityStatus()
    {
        list($activityStatusCodeList, $codes) = [$this->loadCodeList('ActivityStatus', 'V201'), []];

        array_walk(
            $activityStatusCodeList['ActivityStatus'],
            function ($activityStatus) use (&$codes) {
                $codes[] = $activityStatus['name'];
                $codes[] = $activityStatus['code'];
            }
        );

        return implode(',', array_keys(array_flip($codes)));
    }
}
