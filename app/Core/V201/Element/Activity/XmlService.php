<?php namespace App\Core\V201\Element\Activity;

/**
 * Class XmlService
 * @package App\Core\V201\Element\Activity
 */
class XmlService extends XmlGenerator
{
    /**
     * @param $activity
     * @param $settings
     * @param $activityElement
     * @return mixed
     */
    public function validateActivitySchema($activity, $settings, $activityElement)
    {
        try {
            $xml = $this->getXml($activity, $settings, $activityElement);
            $xml->schemaValidate(app_path('/Core/V201/XmlSchema/iati-activities-schema.xsd'));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = str_replace('DOMDocument::schemaValidate(): ', '', $message);

            return redirect()->back()->withMessage($message);
        }
    }

    /**
     * @param $activity
     * @param $settings
     * @param $activityElement
     */
    public function generateActivityXml($activity, $settings, $activityElement)
    {
        $this->generateXml($activity, $settings, $activityElement);
    }
}
