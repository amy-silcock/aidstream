<?php namespace App\Services\CsvImporter\Entities\Activity\Components\Elements;

use App\Services\CsvImporter\Entities\Activity\Components\Elements\Foundation\Iati\Element;

/**
 * Class Description
 * @package App\Services\CsvImporter\Entities\Activity\Components\Elements
 */
class Description extends Element
{
    /**
     * CSV Header of Description with their code.
     */
    private $_csvHeaders = ['activity_description_general' => 1, 'activity_description_objectives' => 2, 'activity_description_target_groups' => 3, 'activity_description_others' => 4];

    /**
     * @var array
     */
    protected $narratives = [];

    /**
     * @var
     */
    protected $languages;

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @var array
     */
    protected $template = [['type' => '', 'narrative' => ['narrative' => '', 'language' => '']]];

    /**
     * Description constructor.
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->prepare($fields);
    }

    /**
     * Prepare the Description element.
     * @param $fields
     */
    public function prepare($fields)
    {
        foreach ($fields as $key => $values) {
            if (!is_null($values) && array_key_exists($key, $this->_csvHeaders)) {
                foreach ($values as $value) {
                    $this->map($key, $value);
                }
            }
        }
    }

    /**
     * Map data from CSV file into Description data format.
     * @param $key
     * @param $value
     */
    public function map($key, $value)
    {
        if (!(is_null($value) || $value == "")) {
            $type                              = $this->setType($key);
            $this->data[$type]['type']         = $type;
            $this->data[$type]['narratives'][] = $this->setNarrative($value);
        }
    }

    /**
     * Set the type for the Description element.
     * @param $key
     * @return mixed
     */
    public function setType($key)
    {
        $this->types[] = $key;
        $this->types   = array_unique($this->types);

        return $this->_csvHeaders[$key];
    }

    /**
     * Set the Narrative for the Description element.
     * @param $value
     * @return array
     */
    public function setNarrative($value)
    {
        $narrative          = ['narrative' => $value, 'language' => ''];
        $this->narratives[] = $narrative;

        return $narrative;
    }

    /**
     * Provides the rules for the IATI Element validation.
     * @return array
     */
    public function rules()
    {
        // TODO: Implement rules() method.
    }

    /**
     * Provides custom messages used for IATI Element Validation.
     * @return array
     */
    public function messages()
    {
        // TODO: Implement messages() method.
    }

    /**
     * Validate data for IATI Element.
     */
    public function validate()
    {
        // TODO: Implement validate() method.
    }

    /**
     * Set the validity for the IATI Element data.
     */
    protected function setValidity()
    {
        // TODO: Implement setValidity() method.
    }
}
