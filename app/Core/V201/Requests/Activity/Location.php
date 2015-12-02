<?php namespace App\Core\V201\Requests\Activity;

/**
 * Class Location
 * @package App\Core\V201\Requests\Activity
 */
class Location extends ActivityBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->getRulesForLocation($this->request->get('location'));
    }

    /**
     * prepare the error message
     * @return array
     */
    public function messages()
    {
        return $this->getMessagesForLocation($this->request->get('location'));
    }

    /**
     * returns rules for location form
     * @param $formFields
     * @return array
     */
    protected function getRulesForLocation($formFields)
    {
        $rules = [];
        foreach ($formFields as $locationIndex => $location) {
            $locationForm                                                   = 'location.' . $locationIndex;
            $rules[sprintf('%s.location_reach.0.code', $locationForm)]      = 'required';
            $rules[sprintf('%s.exactness.0.code', $locationForm)]           = 'required';
            $rules[sprintf('%s.location_class.0.code', $locationForm)]      = 'required';
            $rules[sprintf('%s.feature_designation.0.code', $locationForm)] = 'required';
            $rules                                                          = array_merge(
                $rules,
                $this->getRulesForLocationId($location['location_id'], $locationForm),
                $this->getRulesForName($location['name'], $locationForm),
                $this->getRulesForLocationDescription($location['location_description'], $locationForm),
                $this->getRulesForActivityDescription($location['activity_description'], $locationForm),
                $this->getRulesForAdministrative($location['activity_description'], $locationForm),
                $this->getRulesForPoint($location['point'], $locationForm)
            );
        }

        return $rules;
    }

    /**
     * returns messages for location form
     * @param $formFields
     * @return array
     */
    protected function getMessagesForLocation($formFields)
    {
        $messages = [];
        foreach ($formFields as $locationIndex => $location) {
            $locationForm                                                               = 'location.' . $locationIndex;
            $messages[sprintf('%s.location_reach.0.code.required', $locationForm)]      = 'Code is required.';
            $messages[sprintf('%s.exactness.0.code.required', $locationForm)]           = 'Code is required.';
            $messages[sprintf('%s.location_class.0.code.required', $locationForm)]      = 'Code is required.';
            $messages[sprintf('%s.feature_designation.0.code.required', $locationForm)] = 'Code is required.';
            $messages                                                                   = array_merge(
                $messages,
                $this->getMessagesForLocationId($location['location_id'], $locationForm),
                $this->getMessagesForName($location['name'], $locationForm),
                $this->getMessagesForLocationDescription($location['location_description'], $locationForm),
                $this->getMessagesForActivityDescription($location['activity_description'], $locationForm),
                $this->getMessagesForAdministrative($location['activity_description'], $locationForm),
                $this->getMessagesForPoint($location['point'], $locationForm)
            );
        }

        return $messages;
    }

    /**
     * returns rules for location id
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getRulesForLocationId($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $locationIdIndex => $locationId) {
            $locationIdForm                                   = sprintf('%s.location_id.%s', $formBase, $locationIdIndex);
            $rules[sprintf('%s.vocabulary', $locationIdForm)] = 'required';
            $rules[sprintf('%s.code', $locationIdForm)]       = 'required';
        }

        return $rules;
    }

    /**
     * returns messages for location id
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getMessagesForLocationId($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $locationIdIndex => $locationId) {
            $locationIdForm                                               = sprintf('%s.location_id.%s', $formBase, $locationIdIndex);
            $messages[sprintf('%s.vocabulary.required', $locationIdForm)] = 'Vocabulary is required.';
            $messages[sprintf('%s.code.required', $locationIdForm)]       = 'Code is required.';
        }

        return $messages;
    }

    /**
     * returns rules for name
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getRulesForName($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $nameIndex => $name) {
            $narrativeForm = sprintf('%s.name.%s', $formBase, $nameIndex);
            $rules         = array_merge($rules, $this->getRulesForNarrative($name['narrative'], $narrativeForm));
        }

        return $rules;
    }

    /**
     * returns messages for name
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getMessagesForName($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $nameIndex => $name) {
            $narrativeForm = sprintf('%s.name.%s', $formBase, $nameIndex);
            $messages      = array_merge($messages, $this->getMessagesForNarrative($name['narrative'], $narrativeForm));
        }

        return $messages;
    }

    /**
     * returns rules for location description
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getRulesForLocationDescription($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $descriptionIndex => $description) {
            $narrativeForm = sprintf('%s.location_description.%s', $formBase, $descriptionIndex);
            $rules         = array_merge($rules, $this->getRulesForNarrative($description['narrative'], $narrativeForm));
        }

        return $rules;
    }

    /**
     * returns messages for location description
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getMessagesForLocationDescription($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $descriptionIndex => $description) {
            $narrativeForm = sprintf('%s.location_description.%s', $formBase, $descriptionIndex);
            $messages      = array_merge($messages, $this->getMessagesForNarrative($description['narrative'], $narrativeForm));
        }

        return $messages;
    }

    /**
     * returns rules for activity description
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getRulesForActivityDescription($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $descriptionIndex => $description) {
            $narrativeForm = sprintf('%s.activity_description.%s', $formBase, $descriptionIndex);
            $rules         = array_merge($rules, $this->getRulesForNarrative($description['narrative'], $narrativeForm));
        }

        return $rules;
    }

    /**
     * returns messages for activity description
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getMessagesForActivityDescription($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $descriptionIndex => $description) {
            $narrativeForm = sprintf('%s.activity_description.%s', $formBase, $descriptionIndex);
            $messages      = array_merge($messages, $this->getMessagesForNarrative($description['narrative'], $narrativeForm));
        }

        return $messages;
    }

    /**
     * returns rules for administrative
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getRulesForAdministrative($formFields, $formBase)
    {
        $rules = [];
        foreach ($formFields as $administrativeIndex => $administrative) {
            $administrativeForm                                   = sprintf('%s.administrative.%s', $formBase, $administrativeIndex);
            $rules[sprintf('%s.vocabulary', $administrativeForm)] = 'required';
            $rules[sprintf('%s.code', $administrativeForm)]       = 'required';
        }

        return $rules;
    }

    /**
     * returns messages for administrative
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getMessagesForAdministrative($formFields, $formBase)
    {
        $messages = [];
        foreach ($formFields as $administrativeIndex => $administrative) {
            $administrativeForm                                               = sprintf('%s.administrative.%s', $formBase, $administrativeIndex);
            $messages[sprintf('%s.vocabulary.required', $administrativeForm)] = 'Vocabulary is Required';
            $messages[sprintf('%s.code.required', $administrativeForm)]       = 'Code is Required';
        }

        return $messages;
    }

    /**
     * returns rules for point
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getRulesForPoint($formFields, $formBase)
    {
        $rules                                         = [];
        $pointForm                                     = sprintf('%s.point.0', $formBase);
        $rules[sprintf('%s.srs_name', $pointForm)]     = 'required';
        $positionForm                                  = sprintf('%s.position.0', $pointForm);
        $rules[sprintf('%s.latitude', $positionForm)]  = 'required|numeric';
        $rules[sprintf('%s.longitude', $positionForm)] = 'required|numeric';

        return $rules;
    }

    /**
     * returns messages for point
     * @param $formFields
     * @param $formBase
     * @return array
     */
    protected function getMessagesForPoint($formFields, $formBase)
    {
        $messages                                                  = [];
        $pointForm                                                 = sprintf('%s.point.0', $formBase);
        $messages[sprintf('%s.srs_name.required', $pointForm)]     = 'SRS name is required.';
        $positionForm                                              = sprintf('%s.position.0', $pointForm);
        $messages[sprintf('%s.latitude.required', $positionForm)]  = 'Latitude is required.';
        $messages[sprintf('%s.latitude.numeric', $positionForm)]   = 'Latitude should be numeric.';
        $messages[sprintf('%s.longitude.required', $positionForm)] = 'Longitude is required.';
        $messages[sprintf('%s.longitude.numeric', $positionForm)]  = 'Longitude should be numeric.';

        return $messages;
    }
}
