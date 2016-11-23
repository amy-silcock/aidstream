<?php namespace App\Services\CsvImporter\Queue\Jobs;

use App\Jobs\Job;
use App\Services\CsvImporter\CsvResultProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ImportActivity
 * @package App\Services\CsvImporter\Queue\Jobs
 */
class ImportResult extends Job implements ShouldQueue
{
    /**
     * @var CsvResultProcessor
     */
    protected $csvResultProcessor;

    /**
     * Current Organization's Id.
     * @var
     */
    protected $organizationId;

    /**
     * Current User's Id.
     * @var
     */
    protected $userId;

    /**
     * Directory where the uploaded Csv file is stored temporarily before import.
     */
    const UPLOADED_CSV_STORAGE_PATH = 'csvImporter/tmp/result/file';

    /**
     * @var
     */
    protected $filename;

    /**
     * Create a new job instance.
     *
     * @param CsvResultProcessor $csvResultProcessor
     * @param                    $filename
     */
    public function __construct(CsvResultProcessor $csvResultProcessor, $filename)
    {
        $this->csvResultProcessor = $csvResultProcessor;
        $this->organizationId     = session('org_id');
        $this->userId             = $this->getUserId();
        $this->filename           = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->csvResultProcessor->handle($this->organizationId, $this->userId);

        $path = storage_path(sprintf('%s/%s/%s/%s', 'csvImporter/tmp/result/', $this->organizationId, $this->userId, 'status.json'));
        file_put_contents($path, json_encode(['status' => 'Complete']));

        $this->fixStagingPermission($path);
        $uploadedFilepath = $this->getStoredCsvFilePath($this->filename);

        if (file_exists($uploadedFilepath)) {
            unlink($uploadedFilepath);
        }

        $this->delete();
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
     * Get the current User's id.
     * @return mixed
     */
    protected function getUserId()
    {
        if (auth()->check()) {
            return auth()->user()->id;
        }
    }

    /**
     * Get the temporary Csv filepath for the uploaded Csv file.
     * @param $filename
     * @return string
     */
    protected function getStoredCsvFilePath($filename)
    {
        return sprintf('%s/%s', storage_path(sprintf('%s/%s/%s', self::UPLOADED_CSV_STORAGE_PATH, $this->organizationId, $this->userId)), $filename);
    }
}
