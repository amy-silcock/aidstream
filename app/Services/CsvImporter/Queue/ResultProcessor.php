<?php namespace App\Services\CsvImporter\Queue;

use App\Services\CsvImporter\CsvResultProcessor;
use Maatwebsite\Excel\Excel;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Services\CsvImporter\Queue\Jobs\ImportResult;

/**
 * Class Processor
 * @package App\Services\CsvImporter\Queue
 */
class ResultProcessor
{
    use DispatchesJobs;

    /**
     * @var ImportResult
     */
    protected $importResult;

    /**
     * @var Excel
     */
    protected $csvReader;

    /**
     * Total no. of header present in basic csv.
     */
    const CSV_HEADERS_COUNT = 33;

    /**
     * Processor constructor.
     * @param Excel $csvReader
     */
    public function __construct(Excel $csvReader)
    {
        $this->csvReader = $csvReader;
    }

    /**
     * Push a CSV file's data for processing into Queue.
     * @param $file
     * @param $filename
     */
    public function pushIntoQueue($file, $filename)
    {
        $csv = $this->csvReader->load($file)->toArray();
// TODO: remove this

        $a = new CsvResultProcessor($csv);

        $a->handle(session('org_id'), auth()->user()->id);

// TODO: remove that

//        $this->dispatch(
//            new ImportActivity(new CsvProcessor($csv), $filename)
//        );
    }
}
