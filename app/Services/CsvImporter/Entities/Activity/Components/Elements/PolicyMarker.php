<?php namespace App\Services\CsvImporter\Entities\Activity\Components\Elements;


use App\Services\CsvImporter\Entities\Activity\Components\Elements\Foundation\Iati\Element;
use App\Services\CsvImporter\Entities\Activity\Components\Factory\Validation;

/**
 * Class PolicyMarker
 * @package App\Services\CsvImporter\Entities\Activity\Components\Elements
 */
class PolicyMarker extends Element
{
    /**
     * Csv Header for PolicyMarker element.
     * @var array
     */
    protected $_csvHeaders = ['policy_marker_vocabulary', 'policy_marker_code', 'policy_marker_significance'];

    /**
     * Index under which the data is stored within the object.
     * @var string
     */
    protected $index = 'policy_marker';

    /**
     * PolicyMarker constructor.
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
     * Map data from CSV file into Policy Marker data format.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function map($key, $value, $index)
    {
        if (!(is_null($value) || $value == "")) {
            $this->setVocabulary($key, $value, $index);
            $this->setVocabularyUri($key, $value, $index);
            $this->setSignificance($key, $value, $index);
            $this->setPolicyMarker($key, $value, $index);
            $this->setNarrative($key, $value, $index);
        }
    }

    /**
     * Set Vocabulary for PolicyMarker element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setVocabulary($key, $value, $index)
    {
        if (!isset($this->data['policy_marker'][$index]['vocabulary'])) {
            $this->data['policy_marker'][$index]['vocabulary'] = '';
        }
        if ($key == $this->_csvHeaders[0]) {
            $this->data['policy_marker'][$index]['vocabulary'] = $value;
        }
    }

    /**
     * Set VocabularyUri for PolicyMarker element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setVocabularyUri($key, $value, $index)
    {
        if (!isset($this->data['policy_marker'][$index]['vocabulary_uri'])) {
            $this->data['policy_marker'][$index]['vocabulary_uri'] = '';
        }
        if (array_key_exists('vocabulary', $this->data['policy_marker'][$index])) {
            $this->data['policy_marker'][$index]['vocabulary_uri'] = '';
        }
    }

    /**
     * Set Significance for PolicyMarker element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setSignificance($key, $value, $index)
    {
        if (!isset($this->data['policy_marker'][$index]['significance'])) {
            $this->data['policy_marker'][$index]['significance'] = '';
        }
        if ($key == $this->_csvHeaders[2]) {
            $this->data['policy_marker'][$index]['significance'] = $value;
        }
    }

    /**
     * Set policy marker code for PolicyMarker element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setPolicyMarker($key, $value, $index)
    {
        if (!isset($this->data['policy_marker'][$index]['policy_marker'])) {
            $this->data['policy_marker'][$index]['policy_marker'] = '';
        }
        if ($key == $this->_csvHeaders[1]) {
            $this->data['policy_marker'][$index]['policy_marker'] = $value;
        }
    }

    /**
     * Set Narrative for PolicyMarker element.
     * @param $key
     * @param $value
     * @param $index
     */
    protected function setNarrative($key, $value, $index)
    {
        if (!isset($this->data['policy_marker'][$index]['narrative'])) {
            $this->data['policy_marker'][$index]['narrative'] = '';
        }
        if (array_key_exists('significance', $this->data['policy_marker'][$index])) {
            $this->data['policy_marker'][$index]['narrative'][0] = ['narrative' => '', 'language' => ''];
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
        dd($this);

        return $this;
    }

    /**
     * Provides the rules for the IATI Element validation.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'policy_marker.*.vocabulary'   => sprintf('in:%s', $this->policyMarkerCodeList('PolicyMarkerVocabulary')),
            'policy_marker.*.significance' => sprintf('in:%s', $this->policyMarkerCodeList('PolicySignificance')),
        ];

        foreach (getVal($this->data, ['policy_marker'], []) as $key => $value) {
            $rules['policy_marker.' . $key . '.policy_marker'] = sprintf(
                'required_unless:%s,%s,%s,%s|in:%s',
                'policy_marker.' . $key . '.vocabulary',
                '',
                'policy_marker.' . $key . '.significance',
                '',
                $this->policyMarkerCodeList('PolicyMarker')
            );
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
            'policy_marker.*.vocabulary.in'   => 'Entered Policy Marker Vocabulary is not valid.',
            'policy_marker.*.significance.in' => 'Entered Policy Marker Significance is not valid.',
        ];

        foreach (getVal($this->data, ['policy_marker'], []) as $key => $value) {
            $messages['policy_marker.' . $key . '.policy_marker.required_unless'] = 'Policy Marker Code is required.';
            $messages['policy_marker.' . $key . '.policy_marker.in']              = 'Entered Policy Marker Code is invalid.';
        }

        return $messages;
    }

    /**
     * Get the valid PolicyMaker Codes from the PolicyMarker codelist as a string.
     * @param        $codeList
     * @param string $version
     * @return string
     */
    protected function policyMarkerCodeList($codeList, $version = 'V201')
    {
        list($policyMarkerCodeList, $codes) = [$this->loadCodeList($codeList, $version), []];

        array_walk(
            $policyMarkerCodeList[$codeList],
            function ($policyMarker) use (&$codes) {
                $codes[] = $policyMarker['code'];
            }
        );

        return implode(',', array_keys(array_flip($codes)));
    }
}