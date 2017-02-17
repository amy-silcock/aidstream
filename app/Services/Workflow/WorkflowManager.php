<?php namespace App\Services\Workflow;

use App\Exceptions\Aidstream\Workflow\PublisherNotFoundException;
use App\Services\PerfectViewer\PerfectViewerManager;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use App\Models\Activity\Activity;
use App\Services\Twitter\TwitterAPI;
use App\Services\Publisher\Publisher;
use App\Services\Activity\ActivityManager;
use App\Services\Organization\OrganizationManager;
use App\Services\Xml\Providers\XmlServiceProvider;
use App\Services\Workflow\DataProvider\OrganizationDataProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WorkflowManager
 * @package App\Services\Workflow
 */
class WorkflowManager
{
    /**
     * Status code for Not Authorized Exception.
     */
    const NOT_AUTHORIZED_ERROR_CODE = 403;

    /**
     * @var OrganizationManager
     */
    protected $organizationManager;

    /**
     * @var ActivityManager
     */
    protected $activityManager;

    /**
     * @var XmlServiceProvider
     */
    protected $xmlServiceProvider;

    /**
     * @var
     */
    protected $activity;

    /**
     * @var Publisher
     */
    protected $publisher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TwitterAPI
     */
    protected $twitter;

    /**
     * @var PerfectViewerManager
     */
    protected $perfectActivity;

    /**
     * WorkflowManager constructor.
     * @param OrganizationManager      $organizationManager
     * @param ActivityManager          $activityManager
     * @param XmlServiceProvider       $xmlServiceProvider
     * @param OrganizationDataProvider $organizationDataProvider
     * @param Publisher                $publisher
     * @param LoggerInterface          $logger
     * @param TwitterAPI               $twitter
     * @param PerfectViewerManager     $perfectActivityViewerManager
     */
    public function __construct(
        OrganizationManager $organizationManager,
        ActivityManager $activityManager,
        XmlServiceProvider $xmlServiceProvider,
        OrganizationDataProvider $organizationDataProvider,
        Publisher $publisher,
        LoggerInterface $logger,
        TwitterAPI $twitter,
        PerfectViewerManager $perfectActivityViewerManager
    ) {
        $this->organizationManager      = $organizationManager;
        $this->activityManager          = $activityManager;
        $this->xmlServiceProvider       = $xmlServiceProvider;
        $this->organizationDataProvider = $organizationDataProvider;
        $this->publisher                = $publisher;
        $this->logger                   = $logger;
        $this->twitter                  = $twitter;
        $this->perfectActivity          = $perfectActivityViewerManager;
    }

    /**
     * Find an Activity with a specific id.
     * @param $id
     * @return Activity
     */
    public function findActivity($id)
    {
        return $this->organizationDataProvider->findActivity($id);
    }

    /**
     * Validate Activity against an Activity Xml Schema.
     * @param $activity
     * @return mixed
     */
    public function validate($activity)
    {
        $version             = $activity->organization->settings->version;
        $organizationElement = $this->organizationManager->getOrganizationElement();
        $activityElement     = $this->activityManager->getActivityElement();

        return $this->xmlServiceProvider->initializeValidator($version)->validate($activity, $organizationElement, $activityElement);
    }

    /**
     * @param $data
     * @param $activity
     * @return mixed
     */
    public function update(array $data, Activity $activity)
    {
        return $this->activityManager->updateStatus($data, $activity);
    }

    /**
     * Publish an Activity.
     *
     * If the auto-publish option is set, the Activity data is published into the IATI Registry.
     * @param $activity
     * @param $details
     * @return bool
     */
    public function publish($activity, array $details)
    {
        try {
            $organization = $activity->organization;
            $settings     = $organization->settings;
            $version      = $settings->version;

            $this->xmlServiceProvider->initializeGenerator($version)->generate(
                $activity,
                $this->organizationManager->getOrganizationElement(),
                $this->activityManager->getActivityElement()
            );

            if (getVal($settings['registry_info'], [0, 'publish_files']) == 'yes') {
                $this->publisher->publishFile(
                    $organization->settings['registry_info'],
                    $this->organizationDataProvider->fileBeingPublished($activity->id),
                    $organization,
                    $organization->settings->publishing_type
                );

                $activity->published_to_registry = 1;
                $activity->save();

                $this->activityManager->activityInRegistry($activity);
                $this->twitter->post($organization->settings, $organization);
            }

            $this->perfectActivity->createSnapshot($activity);

            $this->update($details, $activity);

            return true;
        } catch (Exception $exception) {
            $this->logger->error($exception, ['trace' => $exception->getTraceAsString()]);

            if ($exception instanceof PublisherNotFoundException || $exception instanceof ClientException) {
                return false;
            }

            if ($message = $this->isForbidden($exception)) {
                return 'Not Authorized';
            }

            return null;
        }
    }

    /**
     * @param Exception $exception
     * @return bool
     */
    protected function isForbidden(Exception $exception)
    {
        $message = explode(':', explode("\n", $exception->getMessage())[0]);

        return ($message[0] == self::NOT_AUTHORIZED_ERROR_CODE);
    }
}
