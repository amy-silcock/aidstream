<?php namespace App\Tz\Aidstream\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;

/**
 * Class ProjectRequests
 * @package App\Tz\Aidstream\Requests
 */
class ProjectRequests extends FormRequest
{

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        $rules                      = [];
        $rules['identifier']        = 'required';
        $rules['title']             = 'required';
        $rules['description']       = 'required';
        $rules['activity_status']   = 'required';
        $rules['sector']            = 'required';
        $rules['start_date']        = 'required|date';
        $rules['end_date']          = 'date';
        $rules['recipient_country'] = 'required';
        $rules                      = array_merge(
            $rules,
            $this->getRulesForImplementingOrganization($this->get('implementing_organization'))
//            $this->getRulesForDocumentLink($this->get('document_link'))
//            $this->getRulesForFundingOrganization($this->get('funding_organization')),
        );

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        $messages                               = [];
        $messages['identifier.required']        = 'Project Identifier is required';
        $messages['title.required']             = 'Project Title is required';
        $messages['description.required']       = 'General description is required';
        $messages['activity_status.required']   = 'Project Status is required';
        $messages['sector.required']            = 'Sector is required';
        $messages['start_date.required']        = 'Start Date is required.';
        $messages['start_date.date']            = 'Start Date must be date';
        $messages['end_date.date']              = 'End date must be date';
        $messages['recipient_country.required'] = 'Recipient Country is required';
        $messages = array_merge(
            $messages,
            $this->getMessagesForImplementingOrganization($this->get('implementing_organization'))
//            $this->getMessagesForDocumentLink($this->get('document_link'))
//            $this->getMessagesForFundingOrganization($this->get('funding_organization')),
        );

        return $messages;
    }

    /**
     * @param $formFields
     * @return array
     */
    protected function getRulesForFundingOrganization($formFields)
    {
        $rules = [];

        foreach ($formFields as $fundingIndex => $funding) {
            $fundingForm                                                  = 'funding_organization.' . $fundingIndex;
            $rules[sprintf('%s.funding_organization_name', $fundingForm)] = 'required';
        }

        return $rules;
    }

    /**
     * @param $formFields
     * @return array
     */
    protected function getMessagesForFundingOrganization($formFields)
    {
        $messages = [];

        foreach ($formFields as $fundingIndex => $funding) {
            $fundingForm                                                              = 'funding_organization.' . $fundingIndex;
            $messages[sprintf('%s.funding_organization_name.required', $fundingForm)] = 'Funding Organization Name is required';
        }

        return $messages;
    }

    /**
     * @param $formFields
     * @return array
     */
    protected function getRulesForImplementingOrganization($formFields)
    {
        $rules = [];

        foreach ($formFields as $implementingIndex => $implementing) {
            $implementingForm                                                       = 'implementing_organization.' . $implementingIndex;
            $rules[sprintf('%s.implementing_organization_name', $implementingForm)] = 'required';
        }

        return $rules;
    }

    /**
     * @param $formFields
     * @return array
     */
    protected function getMessagesForImplementingOrganization($formFields)
    {
        $messages = [];

        foreach ($formFields as $implementingIndex => $implementing) {
            $implementingForm                                                                   = 'implementing_organization.' . $implementingIndex;
            $messages[sprintf('%s.implementing_organization_name.required', $implementingForm)] = 'Implementing Organization Name is required';
        }

        return $messages;
    }

    /**
     * @param $formFields
     * @return array
     */
    protected function getRulesForDocumentLink($formFields)
    {
        $rules = [];
        foreach ($formFields as $documentLinkIndex => $documentLink) {
            $documentForm                                                      = 'document_link.' . $documentLinkIndex;
            $rules[sprintf('%s.url', $documentForm)]                           = 'required|url';
            $rules[sprintf('%s.title.0.narrative.0.narrative', $documentForm)] = 'required';
        }

        return $rules;
    }

    /**
     * @param $formFields
     * @return array
     */
    protected function getMessagesForDocumentLink($formFields)
    {
        $messages = [];

        foreach ($formFields as $documentLinkIndex => $documentLink) {
            $documentForm                                                                  = 'document_link.' . $documentLinkIndex;
            $messages[sprintf('%s.url.required', $documentForm)]                           = 'Document Url is required';
            $messages[sprintf('%s.url.url', $documentForm)]                                = 'Enter valid URL. eg. http://example.com';
            $messages[sprintf('%s.title.0.narrative.0.narrative.required', $documentForm)] = 'Title is required';
        }

        return $messages;
    }
}
