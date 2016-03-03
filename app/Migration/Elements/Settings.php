<?php namespace App\Migration\Elements;

use App\Migration\MigrateHelper;

/**
 * Class Settings
 * @package App\Migration\Elements
 */
class Settings
{
    /**
     * @var array
     */
    protected $unNestedValues = ['Participating Organizations'];

    /**
     * @var null
     */
    protected $key = null;

    /**
     * @var array
     */
    protected $unwantedKeys = ['participating_org'];

    /**
     * @var array
     */
    protected $newDefaultFieldGroupsFormat = [];

    /**
     * @var array
     */
    protected $mappings = [
        'Identification'              => ['other_identifier' => 'Other Identifier'],
        'Basic Activity Information'  => [
            'activity_date'   => 'Activity Date',
            'title'           => 'Title',
            'description'     => 'Description',
            'activity_status' => 'Activity Status',
            'activity_scope'  => 'Activity Scope',
            'contact_info'    => 'Contact Info'
        ],
        'Participating Organizations' => ['participating_organization' => 'Participating Organization'],
        'Geopolitical Information'    => [
            'location'          => 'Location',
            'recipient_region'  => 'Recipient Region',
            'recipient_country' => 'Recipient Country'
        ],
        'Classifications'             => [
            'default_finance_type' => 'Default Finance Type',
            'default_flow_type'    => 'Default Flow Type',
            'collaboration_type'   => 'Collaboration Type',
            'policy_marker'        => 'Policy Marker',
            'sector'               => 'Sector',
            'country_budget_items' => 'Country Budget Items',
            'default_tied_status'  => 'Default Tied Status',
            'default_aid_type'     => 'Default Aid Type'
        ],
        'Financial'                   => [
            'capital_spend'        => 'Capital Spend',
            'transaction'          => 'Transaction',
            'planned_disbursement' => 'Planned Disbursement',
            'budget'               => 'Budget'
        ],
        'Related Documents'           => [
            'document_link' => 'Document Link'
        ],
        "Relations"                   => [
            "related_activity" => 'Related Activity'
        ],
        'Performance'                 => [
            'legacy_data' => 'Legacy Data',
            'result'      => 'Result',
            'condition'   => 'Condition'
        ]
    ];

    /**
     * Get the correct mapping for each key.
     * @return array
     */
    protected function getMapping()
    {
        foreach (array_except($this->mappings, $this->unNestedValues) as $index => $map) {
            if (array_key_exists($this->key, $map)) {
                return ['key' => $index, 'value' => $map];
            }
        }

        return ['key' => '', 'value' => []];
    }

    /**
     * Extract the key from object.
     * @param $index
     * @return $this
     */
    protected function extract($index)
    {
        $key       = substr($index, 3);
        $this->key = in_array($key, $this->unwantedKeys) ?: $key;

        return $this;
    }

    /**
     * Format Default field Groups.
     * @param array $MetaDataDefaultFieldGroups
     * @return array
     */
    public function formatDefaultFieldGroups(array $MetaDataDefaultFieldGroups)
    {
        $this->newDefaultFieldGroupsFormat = [0 => []];

        foreach (array_except($MetaDataDefaultFieldGroups, ['__PHP_Incomplete_Class_Name', "\x00*\x00activity_website", "\x00*\x00participating_org"]) as $index => $fieldGroup) {
            if ($fieldGroup == "1") {
                $map      = $this->extract($index)->getMapping();
                $category = $map['key'];

                //Temproray fix !! keys dont match !!!
                if ($index == "\x00*\x00conditions" && $MetaDataDefaultFieldGroups[$index] == '1') {
                    $this->newDefaultFieldGroupsFormat[0]['Performance']['condition'] = 'Condition';
                }
                //

                if (!in_array($category, $this->unNestedValues) && array_key_exists($this->key, $map['value'])) {
                    $this->newDefaultFieldGroupsFormat[0][$category][array_search($map['value'][$this->key], $map['value'])] = $map['value'][$this->key];
                }
            }
        }

        $this->newDefaultFieldGroupsFormat[0]['Participating Organizations'] = ['participating_organization' => 'Participating Organization'];

        return $this->newDefaultFieldGroupsFormat;
    }

    /**
     * Format Default Field Values.
     * @param array $MetaDataDefaultFieldGroups
     * @return array
     */
    public function formatDefaultFieldValues(array $MetaDataDefaultFieldGroups)
    {
        $defaultFieldValues = ['linked_data_uri' => ''];
        $migrateHelper      = new MigrateHelper();
        $unrequiredKeys     = ['default_reporting_org', 'default_reporting_org_ref', 'default_reporting_org_type', 'default_reporting_org_lang'];

        foreach (array_except($MetaDataDefaultFieldGroups, ['__PHP_Incomplete_Class_Name', "\x00*\x00linked_data_default"]) as $index => $data) {
            $key = sprintf('default_%s', substr($index, 3));

            if (!in_array($key, $unrequiredKeys)) {
                if ($index === "\x00*\x00currency") {
                    $defaultFieldValues[$key] = $migrateHelper->FetchCurrencyCode($data);
                } elseif ($index === "\x00*\x00language") {
                    $defaultFieldValues[$key] = $migrateHelper->FetchLangCode($data);
                } elseif ($index === "\x00*\x00aid_type") {
                    $defaultFieldValues[$key] = $migrateHelper->FetchAidTypeCode($data);
                } elseif ($index === "\x00*\x00flow_type") {
                    $defaultFieldValues[$key] = fetchCode($data, 'FlowType');
                } elseif ($index === "\x00*\x00finance_type") {
                    $defaultFieldValues[$key] = fetchCode($data, 'FinanceType');
                } elseif ($index === "\x00*\x00tied_status") {
                    $defaultFieldValues[$key] = fetchCode($data, 'TiedStatus');
                } else {
                    $defaultFieldValues[$key] = array_key_exists($index, $MetaDataDefaultFieldGroups) ? $data : '';
                }
            }
        }

        return [$defaultFieldValues];
    }
}
