<?php namespace App\Core\V201\Element\Activity;

use App\Helpers\ArrayToXml;
use App\Models\Activity\Activity;
use App\Models\ActivityPublished;
use App\Models\Settings;
use DOMDocument;
use Illuminate\Support\Collection;

/**
 * Class XmlGenerator
 * @package App\Core\V201\Element\Activity
 */
class XmlGenerator
{

    protected $titleElem;
    protected $arrayToXml;
    protected $descriptionElem;
    protected $activityStatusElem;
    protected $activityDateElem;
    protected $contactElem;
    protected $activityScopeElem;
    protected $participatingOrgElem;
    protected $recipientCountryElem;
    protected $recipientRegionElem;
    protected $locationElem;
    protected $sectorElem;
    protected $countryBudgetItemElem;
    protected $policyMakerElem;
    protected $collaborationTypeElem;
    protected $defaultFlowTypeElem;
    protected $defaultFinanceTypeElem;
    protected $defaultAidTypeElem;
    protected $defaultTiedStatusElem;
    protected $budgetElem;
    protected $plannedDisbursementElem;
    protected $capitalSpendElem;
    protected $documentLinkElem;
    protected $relatedActivityElem;
    protected $legacyDataElem;
    protected $conditionElem;
    protected $transactionElem;
    protected $resultElem;

    /**
     * @param ArrayToXml        $arrayToXml
     * @param ActivityPublished $activityPublished
     */
    public function __construct(ArrayToXml $arrayToXml, ActivityPublished $activityPublished)
    {
        $this->arrayToXml        = $arrayToXml;
        $this->activityPublished = $activityPublished;
    }

    /**
     * @param $activityElement
     */
    public function setElements($activityElement)
    {
        $this->titleElem               = $activityElement->getTitle();
        $this->descriptionElem         = $activityElement->getDescription();
        $this->activityStatusElem      = $activityElement->getActivityStatus();
        $this->activityDateElem        = $activityElement->getActivityDate();
        $this->contactElem             = $activityElement->getContactInfo();
        $this->activityScopeElem       = $activityElement->getActivityScope();
        $this->participatingOrgElem    = $activityElement->getParticipatingOrganization();
        $this->recipientCountryElem    = $activityElement->getRecipientCountry();
        $this->recipientRegionElem     = $activityElement->getRecipientRegion();
        $this->locationElem            = $activityElement->getLocation();
        $this->sectorElem              = $activityElement->getSector();
        $this->countryBudgetItemElem   = $activityElement->getCountryBudgetItem();
        $this->policyMakerElem         = $activityElement->getPolicyMaker();
        $this->collaborationTypeElem   = $activityElement->getCollaborationType();
        $this->defaultFlowTypeElem     = $activityElement->getDefaultFlowType();
        $this->defaultFinanceTypeElem  = $activityElement->getDefaultFinanceType();
        $this->defaultAidTypeElem      = $activityElement->getDefaultAidType();
        $this->defaultTiedStatusElem   = $activityElement->getDefaultTiedStatus();
        $this->budgetElem              = $activityElement->getBudget();
        $this->plannedDisbursementElem = $activityElement->getPlannedDisbursement();
        $this->capitalSpendElem        = $activityElement->getCapitalSpend();
        $this->documentLinkElem        = $activityElement->getDocumentLink();
        $this->relatedActivityElem     = $activityElement->getRelatedActivity();
        $this->legacyDataElem          = $activityElement->getLegacyData();
        $this->conditionElem           = $activityElement->getCondition();
        $this->transactionElem         = $activityElement->getTransaction();
        $this->resultElem              = $activityElement->getResult();
    }

    /**
     * @param Activity   $activity
     * @param Collection $transaction
     * @param Collection $result
     * @param Settings   $settings
     * @param            $activityElement
     */
    public function generateXml(Activity $activity, Collection $transaction, Collection $result, Settings $settings, $activityElement)
    {
        $xml               = $this->getXml($activity, $transaction, $result, $settings, $activityElement);
        $publishedActivity = $activity->identifier['iati_identifier_text'] . '.xml';
        $file              = substr($publishedActivity, 0, strpos($publishedActivity, "-"));
        $result            = $xml->save(public_path('uploads/files/activity/' . $publishedActivity));
        if ($result) {
            $published = $this->activityPublished->firstOrNew(['filename' => $file . '.xml', 'organization_id' => $activity->organization_id]);
            $published->touch();
            $publishedActivities = (array) $published->published_activities;
            (in_array($publishedActivity, $publishedActivities)) ?: array_push($publishedActivities, $publishedActivity);
            $published->published_activities = $publishedActivities;
            $published->save();
            $xmlMerge = $this->getMergeXml($published->published_activities, $file);
        }
    }

    /**
     * @param Activity   $activity
     * @param Collection $transaction
     * @param Collection $result
     * @param Settings   $settings
     * @param            $activityElement
     * @return DomDocument
     */
    public function getXml(Activity $activity, Collection $transaction, Collection $result, Settings $settings, $activityElement)
    {
        $this->setElements($activityElement);
        $xmlData                                 = [];
        $xmlData['@attributes']                  = [
            'version'            => $settings->version,
            'generated-datetime' => gmdate('c')
        ];
        $xmlData['iati-activity']                = $this->getXmlData($activity, $transaction, $result);
        $xmlData['iati-activity']['@attributes'] = [
            'last-updated-datetime' => gmdate('c', time($settings->updated_at)),
            'xml:lang'              => $settings->default_field_values[0]['default_language'],
            'default-currency'      => $settings->default_field_values[0]['default_currency']
        ];

        return $this->arrayToXml->createXML('iati-activities', $xmlData);
    }

    /**
     * @param Activity   $activity
     * @param Collection $transaction
     * @param Collection $result
     * @return array
     */
    public function getXmlData(Activity $activity, Collection $transaction, Collection $result)
    {
        $xmlActivity                               = [];
        $xmlActivity['activity-identifier']        = $activity->identifier['iati_identifier_text'];
        $xmlActivity['title']                      = $this->titleElem->getXmlData($activity);
        $xmlActivity['description']                = $this->descriptionElem->getXmlData($activity);
        $xmlActivity['activity-status']            = $this->activityStatusElem->getXmlData($activity);
        $xmlActivity['activity-date']              = $this->activityDateElem->getXmlData($activity);
        $xmlActivity['contact-info']               = $this->contactElem->getXmlData($activity);
        $xmlActivity['activity-scope']             = $this->activityScopeElem->getXmlData($activity);
        $xmlActivity['participating-organization'] = $this->participatingOrgElem->getXmlData($activity);
        $xmlActivity['recipient-country']          = $this->recipientCountryElem->getXmlData($activity);
        $xmlActivity['recipient-region']           = $this->recipientRegionElem->getXmlData($activity);
        $xmlActivity['location']                   = $this->locationElem->getXmlData($activity);
        $xmlActivity['sector']                     = $this->sectorElem->getXmlData($activity);
        $xmlActivity['country-budget-item']        = $this->countryBudgetItemElem->getXmlData($activity);
        $xmlActivity['policy-maker']               = $this->policyMakerElem->getXmlData($activity);
        $xmlActivity['collaboration-type']         = $this->collaborationTypeElem->getXmlData($activity);
        $xmlActivity['default-flow-type']          = $this->defaultFlowTypeElem->getXmlData($activity);
        $xmlActivity['default-finance-type']       = $this->defaultFinanceTypeElem->getXmlData($activity);
        $xmlActivity['default-aid-type']           = $this->defaultAidTypeElem->getXmlData($activity);
        $xmlActivity['default-tied-status']        = $this->defaultTiedStatusElem->getXmlData($activity);
        $xmlActivity['budget']                     = $this->budgetElem->getXmlData($activity);
        $xmlActivity['planned-disbursement']       = $this->plannedDisbursementElem->getXmlData($activity);
        $xmlActivity['capital-spend']              = $this->capitalSpendElem->getXmlData($activity);
        $xmlActivity['document-link']              = $this->documentLinkElem->getXmlData($activity);
        $xmlActivity['related-activity']           = $this->relatedActivityElem->getXmlData($activity);
        $xmlActivity['legacy-data']                = $this->legacyDataElem->getXmlData($activity);
        $xmlActivity['condition']                  = $this->conditionElem->getXmlData($activity);
        $xmlActivity['transaction']                = $this->transactionElem->getXmlData($transaction);
        $xmlActivity['result']                     = $this->resultElem->getXmlData($result);

        return array_filter(
            $xmlActivity,
            function ($value) {
                return $value;
            }
        );
    }

    /**
     * @param $published
     * @param $file
     */
    public function getMergeXml($published, $file)
    {
        $dom = new DOMDocument();
        $dom->appendChild($dom->createElement('iati-activities'));
        foreach ($published as $xml) {
            $addDom = new DOMDocument();
            $addDom->load(public_path('uploads/files/activity/' . $xml));
            if ($addDom->documentElement) {
                foreach ($addDom->documentElement->childNodes as $node) {
                    $dom->documentElement->appendChild(
                        $dom->importNode($node, true)
                    );
                }
            }
        }

        $dom->saveXml();
        $dom->save(public_path('uploads/files/activity/' . $file . '.xml'));
    }
}
