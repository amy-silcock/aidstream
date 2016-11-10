<?php namespace App\Services\CsvImporter;

use App\Services\CsvImporter\Entities\Activity\Result\Result;
use Maatwebsite\Excel\Excel;

/**
 * Class CsvProcessor
 * @package App\Services\CsvImporter
 */
class CsvResultProcessor
{
    /**
     * @var
     */
    protected $csv;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var
     */
    public $activity;

    /**
     * @var string
     */
    protected $csvIdentifier = 'type';

    /**
     * Total no. of header present in basic csv.
     */
    const CSV_HEADERS_COUNT = 33;

    /**
     * CsvProcessor constructor.
     * @param $csv
     */
    public function __construct($csv)
    {
        $this->csv = $csv;
    }

    /**
     * Handle the import functionality.
     * @param $organizationId
     * @param $userId
     */
    public function handle($organizationId, $userId)
    {
        if ($this->isCorrectCsv()) {
            $this->groupValues();

            $this->initActivity(['organization_id' => $organizationId, 'user_id' => $userId]);
//            dd($this->data);
            $this->activity->process();
        } else {
            $filepath = storage_path('csvImporter/tmp/result/' . $organizationId . '/' . $userId);
            $filename = 'header_mismatch.json';

            if (!file_exists($filepath)) {
                mkdir($filepath, 0777, true);
            }

            file_put_contents($filepath . '/' . $filename, json_encode(['mismatch' => true]));
        }
    }

    /**
     * Fix file permission while on staging environment
     * @param $path
     */
    protected function fixStagingPermission($path)
    {
        // TODO: Remove this.
        shell_exec(sprintf('chmod 777 -R %s', $path));
    }

    /**
     * Initialize an object for the Activity class with the provided options.
     *
     * @param array $options
     */
    protected function initActivity(array $options = [])
    {
        if (class_exists(Result::class)) {
            $this->activity = app()->make(Result::class, [$this->data, getVal($options, ['organization_id']), getVal($options, ['user_id'])]);
        }
    }

    /**
     * Group rows into single Activities.
     */
    protected function groupValues()
    {
        $index = - 1;

        foreach ($this->csv as $row) {
            if (!$this->isSameEntity($row)) {
                $index ++;
            }

            $this->group($row, $index);
        }
    }

    /**
     * Group the values of a row to a specific index.
     * @param $row
     * @param $index
     */
    protected function group($row, $index)
    {
        foreach ($row as $key => $value) {
            $this->setValue($index, $key, $value);
        }
    }

    /**
     * Set the provided value to the provided key/index.
     * @param $index
     * @param $key
     * @param $value
     */
    protected function setValue($index, $key, $value)
    {
        $this->data[$index][$key][] = $value;
    }

    /**
     * Check if the next row is new row or not.
     * @param $row
     * @return bool
     */
    protected function isSameEntity($row)
    {
        if (is_null($row[$this->csvIdentifier]) || $row[$this->csvIdentifier] == '') {
            return true;
        }

        return false;
    }

    /**
     * Check if the headers are correct according to the provided template.
     * @return bool
     */
    protected function isCorrectCsv()
    {
        if (!$this->csv) {
            return false;
        }

        return $this->hasCorrectHeaders();
    }

    //ChecksCsvHeaders
    /**
     * Load Csv template
     * @param $version
     * @param $filename
     * @return array
     */
    protected function loadTemplate($version, $filename)
    {
        $excel = app()->make(Excel::class);

        $file = $excel->load(app_path(sprintf('Services/CsvImporter/Templates/Activity/%s/%s.csv', $version, $filename)));

        return $file->toArray();
    }

    /**
     * Check if the difference of the csv headers is empty.
     * @param array $diffHeaders
     * @return bool
     */
    protected function isSameCsvHeader(array $diffHeaders)
    {
        if (empty($diffHeaders)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the headers are correct for the uploaded Csv File.
     * @param        $csvHeaders
     * @param        $templateFileName
     * @param string $version
     * @return bool
     */
    protected function checkHeadersFor($csvHeaders, $templateFileName, $version = 'V201')
    {
        $templateHeaders = $this->loadTemplate($version, $templateFileName);
//        dd($templateHeaders);
        $templateHeaders = array_keys($templateHeaders[0]);
        $diffHeaders     = array_diff($csvHeaders, $templateHeaders);

        return $this->isSameCsvHeader($diffHeaders);
    }

    /**
     * Check if the headers for the uploaded Csv file matches with the provided header count.
     *
     * @param $actualHeaders
     * @param $providedHeaderCount
     * @return bool
     */
    protected function headerCountMatches(array $actualHeaders, $providedHeaderCount)
    {
        return (count($actualHeaders) == $providedHeaderCount);
    }

    /**
     * Check if the uploaded Csv file has correct headers.
     *
     * @return bool
     */
    protected function hasCorrectHeaders()
    {
        $csvHeaders = array_keys($this->csv[0]);

        if ($this->headerCountMatches($csvHeaders, self::CSV_HEADERS_COUNT)) {
            return $this->checkHeadersFor($csvHeaders, 'result', 'V201');
        }

        return false;
    }
}
