<?php namespace App\Services\Publisher;

use Exception;
use App\Http\API\CKAN\CkanClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Workflow\Registry\RegistryApiHandler;
use App\Exceptions\Aidstream\Workflow\PublisherNotFoundException;


/**
 * Class Publisher
 * @package App\Services\Publisher
 */
class Publisher extends RegistryApiHandler
{
    /**
     * @var null
     */
    protected $file = null;

    /**
     * @param       $registryInfo
     * @param       $organization
     * @param       $publishingType
     * @param array $changes
     * @throws PublisherNotFoundException
     */
    public function publish($registryInfo, $organization, $publishingType, array $changes = [])
    {
        try {
            $this->init(env('REGISTRY_URL'), getVal($registryInfo, [0, 'api_id'], ''))->setPublisher(getVal($registryInfo, [0, 'publisher_id'], ''));

            /* Depcricated */
//        $this->client->package_search($this->publisherId)

            if (!$this->checkPublisherValidity($this->searchForPublisher())) {
                throw new PublisherNotFoundException('Publisher not found.');
            }

            if ($changes) {
                $this->publishSegmentationChanges($changes, $organization, $publishingType);
            } else {
                if ($this->file) {
                    $this->publishIntoRegistry($organization, $publishingType);
                }
            }
        } catch (ClientException $exception) {
            throw $exception;
        }
    }

    /**
     * Unlink a file from the IATI Registry.
     * @param $registryInfo
     * @param $changeDetails
     * @return bool
     * @throws Exception
     */
    public function unlink($registryInfo, $changeDetails)
    {
        try {
            $apiKey = $registryInfo[0]['api_id'];
            $api    = new CkanClient(env('REGISTRY_URL'), $apiKey);

            foreach ($changeDetails['previous'] as $filename => $previous) {
                $pieces = explode(".", $filename);
                $fileId = array_first(
                    $pieces,
                    function () {
                        return true;
                    }
                );

                if (getVal($previous, ['published_status'])) {
                    $api->package_delete($fileId);
                }
            }

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    /**
     * Returns the request header payload while publishing any files to the IATI Registry.
     * @param      $organization
     * @param      $filename
     * @param      $publishingType
     * @param null $publishedFile
     * @return array
     * @internal param $data
     */
    protected function generatePayload($organization, $filename, $publishingType, $publishedFile = null)
    {
        $code  = $this->getCode($filename);
        $key   = $this->getKey($code);
        $title = $this->extractTitle($organization, $publishingType, $code);

        if (!$publishedFile) {
            $publishedFile = $organization->publishedFiles()->where('filename', '=', $filename)->first();

            return $this->formatHeaders($this->extractPackage($filename), $organization, $publishedFile, $key, end($code), $title);
        }

        return $this->formatHeaders($this->extractPackage($filename), $organization, $publishedFile, $key, end($code), $title);
    }

    /**
     * Get the required key for the code provided.
     * @param $code
     * @return string
     */
    protected function getKey($code)
    {
        if ($code == "998") {
            return "Others";
        } elseif (is_numeric($code)) {
            return "region";
        }

        return "country";
    }

    /**
     * Format headers required to publish into the IATI Registry.
     * @param $filename
     * @param $organization
     * @param $publishedFile
     * @param $key
     * @param $code
     * @param $title
     * @return string
     */
    protected function formatHeaders($filename, $organization, $publishedFile, $key, $code, $title)
    {
        return json_encode(
            [
                'title'          => $title,
                'name'           => $filename,
                'author_email'   => $organization->getAdminUser()->email,
                'owner_org'      => $this->publisherId,
                'license_id'     => 'other-open',
                'resources'      => [
                    [
                        'format'   => config('xmlFiles.format'),
                        'mimetype' => config('xmlFiles.mimeType'),
                        'url'      => url(sprintf('files/xml/%s.xml', $filename))
                    ]
                ],
                "filetype"       => "activity",
                $key             => ($code == 'activities') ? '' : $code,
                "data_updated"   => $publishedFile->updated_at->toDateTimeString(),
                "activity_count" => count($publishedFile->published_activities),
                "language"       => config('app.locale'),
                "verified"       => "no"
            ]
        );
    }

    /**
     * @param $registryInfo
     * @param $file
     * @param $organization
     * @param $publishingType
     * @throws Exception
     */
    public function publishFile($registryInfo, $file, $organization, $publishingType)
    {
        try {
            $this->setFile($file);
            $this->publish($registryInfo, $organization, $publishingType);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get the country code/region code from the filename.
     * @param $filename
     * @return array
     */
    protected function getCode($filename)
    {
        return explode('-', explode('.', $filename)[0]);
    }

    /**
     * Extract title for the file being published.
     * @param $organization
     * @param $publishingType
     * @param $code
     * @return string
     */
    protected function extractTitle($organization, $publishingType, $code)
    {
        return ($publishingType == "segmented")
            ? $organization->name . ' Activity File-' . strtoupper(end($code))
            : $organization->name . ' Activity File';
    }

    /**
     * Set the file attribute.
     * @param $file
     */
    protected function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Publish File to the IATI Registry.
     * @param $organization
     * @param $filename
     * @param $publishingType
     * @param $publishedStatus
     */
    protected function publishToRegistry($organization, $filename, $publishingType, $publishedStatus)
    {
        $data = $this->generatePayload($organization, $filename, $publishingType);

        if (!$publishedStatus) {
            $this->client->package_create($data);
        } else {
            $this->client->package_update($data);
        }
    }

    /**
     * Publish Segmentation changes into the IATI Registry.
     * @param $changeDetails
     * @param $organization
     * @param $publishingType
     */
    protected function publishSegmentationChanges($changeDetails, $organization, $publishingType)
    {
        $changes = $changeDetails['changes'];

        foreach ($changes as $filename => $changeDetail) {
            $this->publishToRegistry($organization, $filename, $publishingType, $changeDetail['published_status']);
        }
    }

    /**
     * Check if there data returned by the IATI Registry Api is valid.
     * @param $publisherData
     * @return bool
     */
    protected function checkPublisherValidity($publisherData)
    {
        $publisherData = json_decode($publisherData);

        return $publisherData ? ($publisherData->result->name == $this->publisherId) : false;
    }

    /**
     * Publish file(s) into the IATI Registry.
     * @param $organization
     * @param $publishingType
     */
    protected function publishIntoRegistry($organization, $publishingType)
    {
        if ($this->file instanceof Collection) {
            foreach ($this->file as $file) {
                $this->publishToRegistry($organization, $file->filename, $publishingType, $file->published_to_register);
            }
        } else {
            $this->publishToRegistry($organization, $this->file->filename, $publishingType, $this->file->published_to_register);
        }
    }

    /**
     * Extract the package name from the published filename.
     * @param $filename
     * @return string
     */
    protected function extractPackage($filename)
    {
        return array_first(
            explode('.', $filename),
            function () {
                return true;
            }
        );
    }

    /**
     * Search for a publisher with a specific publisherId.
     * @return string
     */
    protected function searchForPublisher()
    {
        $apiHost = env('REGISTRY_URL');
        $uri     = 'action/organization_show';
        $url     = sprintf('%s%s?id=%s', $apiHost, $uri, $this->publisherId);
        $client  = $this->initGuzzleClient();

        return $client->get($url)
                      ->getBody()
                      ->getContents();
    }

    /**
     * Initialize the GuzzleHttp\Client instance
     * @return mixed
     */
    protected function initGuzzleClient()
    {
        return app()->make(Client::class);
    }
}
