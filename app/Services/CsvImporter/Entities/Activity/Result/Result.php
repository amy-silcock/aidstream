<?php namespace App\Services\CsvImporter\Entities\Activity\Result;

use App\Services\CsvImporter\Entities\ResultCsv;

/**
 * Class Result
 * @package App\Services\CsvImporter\Entities\Activity
 */
class Result extends ResultCsv
{
    /**
     * Result constructor.
     * @param $rows
     * @param $organizationId
     * @param $userId
     */
    public function __construct($rows, $organizationId, $userId)
    {
        $this->csvRows        = $rows;
        $this->organizationId = $organizationId;
        $this->userId         = $userId;
        $this->rows           = $rows;
    }

    protected $resultRowNumber = 0;
    /**
     * Process the Result Csv.
     *
     * @return $this
     */
    public function process()
    {
        foreach ($this->rows() as $row) {
            $this->resultRowNumber = $this->initialize($row, $this->resultRowNumber)
                 ->mapResultRow()
                 ->validate()
                 ->keep();
        }

        return $this;
    }
}
