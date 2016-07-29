<?php namespace App\Services\Import\Validators\Transaction;

use App\Core\V201\Traits\GetCodes;
use App\Exceptions\Aidstream\Import\HeaderMisMatchException;
use App\Services\Queue\Validators\Transaction\Traits\RegistersValidators;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Collections\RowCollection;
use Maatwebsite\Excel\Facades\Excel;

class TransactionCsvValidator
{
    use GetCodes, RegistersValidators;

    const REQUIRED_NONEMPTY_FIELD = 1;
    const IDENTICAL_INTERNAL_REFERENCE = 1;

    public function __construct()
    {
        $this->registerValidators();
    }

    /**
     * check if date is valid or not
     * @param $date
     * @return bool
     */
    public function validateDate($date)
    {
        $dateFormat = DateTime::createFromFormat('Y-m-d', $date);
        $date       = explode('-', $dateFormat);
        $year       = $date[0];
        $month      = $date[1];
        $day        = $date[2];
        $checkDate  = checkdate($month, $day, $year);

        return ($dateFormat && $checkDate);
    }

    /**
     * Check if the csv file data is valid or not
     * @param $filename
     * @param $transactions
     * @return Validator
     * @throws HeaderMisMatchException
     */
    public function getDetailedCsvValidator($filePath, RowCollection $transactions)
    {
        $data              = $transactions->toArray();
        $transactionHeader = $transactions->first()->keys()->toArray();
        $templateHeader    = Excel::load(app_path('Core/V201/Template/Csv/iati_transaction_template_detailed.csv'))->get()->first()->keys()->toArray();

        if (count(array_intersect($transactionHeader, $templateHeader)) !== count($templateHeader)) {
            throw new HeaderMisMatchException('The headers in the uploaded file do not match with our template. Please check.');
        }

        $transactionTypeCodes           = implode(',', $this->getCodes('TransactionType', 'Activity'));
        $disbursementChannelCodes       = implode(',', $this->getCodes('DisbursementChannel', 'Activity'));
        $sectorVocabularyCodes          = implode(',', $this->getCodes('SectorVocabulary', 'Activity'));
        $recipientCountryCodes          = implode(',', $this->getCodes('Country', 'Organization'));
        $recipientRegionCodes           = implode(',', $this->getCodes('Region', 'Activity'));
        $recipientRegionVocabularyCodes = implode(',', $this->getCodes('RegionVocabulary', 'Activity'));
        $flowTypeCodes                  = implode(',', $this->getCodes('FlowType', 'Activity'));
        $financeTypeCodes               = implode(',', $this->getCodes('FinanceType', 'Activity'));
        $aidTypeCodes                   = implode(',', $this->getCodes('AidType', 'Activity'));
        $tiedStatusCodes                = implode(',', $this->getCodes('TiedStatus', 'Activity'));

        $rules    = [];
        $messages = [];

        foreach ($data as $transactionIndex => $transactionRow) {
            $rules = array_merge(
                $rules,
                [
                    "$transactionIndex.transaction_ref"             => sprintf(
                        'required|unique_validation:%s.transaction_ref,%s,%s,transaction_ref',
                        $transactionIndex,
                        trimInput($transactionRow['transaction_ref']),
                        $filePath
                    ),
                    "$transactionIndex.transactiontype_code"        => 'required|in:' . $transactionTypeCodes,
                    "$transactionIndex.transactiondate_iso_date"    => 'required|date',
                    "$transactionIndex.transactionvalue_value_date" => 'required|date',
                    "$transactionIndex.transactionvalue_text"       => 'required|numeric',
                    "$transactionIndex.description_text"            => 'required',
                    "$transactionIndex.disbursementchannel_code"    => 'in:' . $disbursementChannelCodes,
                    "$transactionIndex.sector_vocabulary"           => 'in:' . $sectorVocabularyCodes,
                    "$transactionIndex.recipientcountry_code"       => 'in:' . $recipientCountryCodes,
                    "$transactionIndex.recipientregion_code"        => 'in:' . $recipientRegionCodes,
                    "$transactionIndex.recipientregion_vocabulary"  => 'in:' . $recipientRegionVocabularyCodes,
                    "$transactionIndex.flowtype_code"               => 'in:' . $flowTypeCodes,
                    "$transactionIndex.financetype_code"            => 'in:' . $financeTypeCodes,
                    "$transactionIndex.aidtype_code"                => 'in:' . $aidTypeCodes,
                    "$transactionIndex.tiedstatus_code"             => 'in:' . $tiedStatusCodes,
                ]
            );

            $messages = array_merge(
                $messages,
                [
                    "$transactionIndex.transaction_ref.required"             => sprintf('At row %s Transaction-ref is required', $transactionIndex + 1),
                    "$transactionIndex.transaction_ref.unique_validation"    => sprintf('At row %s Transaction-ref should be unique', $transactionIndex + 1),
                    "$transactionIndex.transactiontype_code.required"        => sprintf('At row %s TransactionType-code is required', $transactionIndex + 1),
                    "$transactionIndex.transactiontype_code.in"              => sprintf('At row %s TransactionType-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.transactiondate_iso_date.required"    => sprintf('At row %s TransactionDate-iso_date is required', $transactionIndex + 1),
                    "$transactionIndex.transactiondate_iso_date.date"        => sprintf('At row %s TransactionDate-iso_date is invalid', $transactionIndex + 1),
                    "$transactionIndex.transactionvalue_value_date.required" => sprintf('At row %s TransactionValue-value_date is required', $transactionIndex + 1),
                    "$transactionIndex.transactionvalue_value_date.date"     => sprintf('At row %s TransactionValue-value_date is invalid', $transactionIndex + 1),
                    "$transactionIndex.transactionvalue_text.required"       => sprintf('At row %s TransactionValue-text is required', $transactionIndex + 1),
                    "$transactionIndex.transactionvalue_text.numeric"        => sprintf('At row %s TransactionValue-text should ne numeric', $transactionIndex + 1),
                    "$transactionIndex.description_text.required"            => sprintf('At row %s Description-text is required', $transactionIndex + 1),
                    "$transactionIndex.disbursementchannel_code.in"          => sprintf('At row %s DisbursementChannel-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.sector_vocabulary.in"                 => sprintf('At row %s Sector-vocabularye is invalid', $transactionIndex + 1),
                    "$transactionIndex.recipientcountry_code.in"             => sprintf('At row %s RecipientCountry-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.recipientregion_code.in"              => sprintf('At row %s RecipientRegion-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.recipientregion_vocabulary.in"        => sprintf('At row %s RecipientRegion-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.flowtype_code.in"                     => sprintf('At row %s FlowType-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.financetype_code.in"                  => sprintf('At row %s FinanceType-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.aidtype_code.in"                      => sprintf('At row %s AidType-code is invalid', $transactionIndex + 1),
                    "$transactionIndex.tiedstatus_code.in"                   => sprintf('At row %s TiedStatus-code is invalid', $transactionIndex + 1),
                ]
            );

            $sectorVocabulary = $transactionRow['sector_vocabulary'];
            if ($sectorVocabulary == 1) {
                $sectorCodes                                  = implode(',', $this->getCodes('Sector', 'Activity'));
                $rules["$transactionIndex.sector_code"]       = 'in:' . $sectorCodes;
                $messages["$transactionIndex.sector_code.in"] = sprintf('At row %s 5 digit Sector-code is invalid', $transactionIndex + 1);
            } elseif ($sectorVocabulary == 2) {
                $sectorCodes                                  = implode(',', $this->getCodes('SectorCategory', 'Activity'));
                $rules["$transactionIndex.sector_code"]       = 'in:' . $sectorCodes;
                $messages["$transactionIndex.sector_code.in"] = sprintf('At row %s 3 digit Sector-code is invalid', $transactionIndex + 1);
            }
        }

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Check if the activities in csv are valid or not
     * @param $file
     * @param $identifiers
     * @return mixed
     */
    public function isValidActivityCsv($file, $identifiers)
    {
        try {
            $activities            = Excel::load($file)->get()->toArray();
            $activityStatus        = implode(',', $this->getCodes('ActivityStatus', 'Activity'));
            $sectorCategory        = implode(',', $this->getCodes('Sector', 'Activity'));
            $recipientCountryCodes = implode(',', $this->getCodes('Country', 'Organization'));
            $recipientRegionCodes  = implode(',', $this->getCodes('Region', 'Activity'));
            $rules                 = [];
            $messages              = [];
            $identifiers           = implode(',', $identifiers);
            foreach ($activities as $activityIndex => $activityRow) {
                $rules = array_merge(
                    $rules,
                    [
                        "$activityIndex.activity_identifier"                => sprintf(
                            'required|unique_validation:%s.activity_identifier,%s,%s,activity_identifier|not_in:%s',
                            $activityIndex,
                            trimInput($activityRow['activity_identifier']),
                            $file,
                            $identifiers
                        ),
                        "$activityIndex.activity_title"                     => 'required',
                        "$activityIndex.actual_start_date"                  => sprintf(
                            'date|required_any:%s.actual_start_date,%s,%s.actual_end_date,%s,%s.planned_start_date,%s,%s.planned_end_date,%s',
                            $activityIndex,
                            trimInput($activityRow['actual_start_date']),
                            $activityIndex,
                            trimInput($activityRow['actual_end_date']),
                            $activityIndex,
                            trimInput($activityRow['planned_start_date']),
                            $activityIndex,
                            trimInput($activityRow['planned_end_date'])
                        ),
                        "$activityIndex.actual_end_date"                    => 'date',
                        "$activityIndex.planned_start_date"                 => 'date',
                        "$activityIndex.planned_end_date"                   => 'date',
                        "$activityIndex.description_general"                => sprintf(
                            'required_any:%s.description_general,%s,%s.description_objectives,%s,%s.description_target_group,%s',
                            $activityIndex,
                            trimInput($activityRow['description_general']),
                            $activityIndex,
                            trimInput($activityRow['description_objectives']),
                            $activityIndex,
                            trimInput($activityRow['description_target_group'])
                        ),
                        "$activityIndex.funding_participating_organization" => sprintf(
                            'required_any:%s.funding_participating_organization,%s,%s.implementing_participating_organization,%s',
                            $activityIndex,
                            trimInput($activityRow['funding_participating_organization']),
                            $activityIndex,
                            trimInput($activityRow['implementing_participating_organization'])
                        ),
                        "$activityIndex.activity_status"                    => 'required|in:' . $activityStatus,
                        "$activityIndex.sector_dac_5digit"                  => 'required|multiple_value_in:' . $sectorCategory,
                        "$activityIndex.recipient_country"                  => 'required_without:' . $activityIndex . '.recipient_region|multiple_value_in:' . $recipientCountryCodes,
                        "$activityIndex.recipient_region"                   => 'required_without:' . $activityIndex . '.recipient_country|multiple_value_in:' . $recipientRegionCodes
                    ]
                );

                $messages = array_merge(
                    $messages,
                    [
                        "$activityIndex.sector_dac_5digit.multiple_value_in"             => sprintf('At row %s Sector_DAC_5Digit category code is invalid.', $activityIndex + 1),
                        "$activityIndex.sector_dac_5digit.required"                      => sprintf('At row %s Sector_DAC_5Digit category code is required.', $activityIndex + 1),
                        "$activityIndex.recipient_country.multiple_value_in"             => sprintf('At row %s Recipient_Country is invalid.', $activityIndex + 1),
                        "$activityIndex.recipient_country.required_without"              => sprintf('At row %s either Recipient_Country or Recipient_Region is required.', $activityIndex + 1),
                        "$activityIndex.recipient_region.multiple_value_in"              => sprintf('At row %s Recipient_Region is invalid.', $activityIndex + 1),
                        "$activityIndex.recipient_region.required_without"               => sprintf('At row %s either Recipient_Country or Recipient_Region is required.', $activityIndex + 1),
                        "$activityIndex.activity_status.in"                              => sprintf('At row %s Activity_Status is invalid.', $activityIndex + 1),
                        "$activityIndex.activity_status.required"                        => sprintf('At row %s Activity_Status is required.', $activityIndex + 1),
                        "$activityIndex.activity_identifier.required"                    => sprintf('At row %s Activity_Identifier is required.', $activityIndex + 1),
                        "$activityIndex.activity_identifier.not_in"                      => sprintf('At row %s Activity_Identifier already exists.', $activityIndex + 1),
                        "$activityIndex.activity_identifier.unique_validation"           => sprintf('At row %s Activity_Identifier is invalid and must be unique.', $activityIndex + 1),
                        "$activityIndex.activity_title.required"                         => sprintf('At row %s Activity_Title is required.', $activityIndex + 1),
                        "$activityIndex.actual_start_date.date"                          => sprintf('At row %s Actual_start_date is invalid.', $activityIndex + 1),
                        "$activityIndex.actual_end_date.date"                            => sprintf('At row %s Actual_end_date is invalid.', $activityIndex + 1),
                        "$activityIndex.planned_start_date.date"                         => sprintf('At row %s Planned_start_date is invalid.', $activityIndex + 1),
                        "$activityIndex.planned_end_date.date"                           => sprintf('At row %s Planned_end_date is invalid.', $activityIndex + 1),
                        "$activityIndex.actual_start_date.required_any"                  => sprintf(
                            'At row %s date among Actual_start_date/Actual_end_date/Planned_start_date/Planned_end_date is required.',
                            $activityIndex + 1
                        ),
                        "$activityIndex.description_general.required_any"                => sprintf(
                            'At row %s at least one description among description_general/description_objectives/description_target_group is required.',
                            $activityIndex + 1
                        ),
                        "$activityIndex.funding_participating_organization.required_any" => sprintf(
                            'At row %s at least one participating_organization among funding/implementing is required.',
                            $activityIndex + 1
                        ),
                    ]
                );

            }

            return Validator::make($activities, $rules, $messages);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Undefined index:') == 0) { // ???
                return false;
            }
        }
    }

    public function getSimpleCsvValidator($filePath, $data, $filename)
    {
        $transactions      = Excel::load($filePath)->get()->toArray();
        $transactionHeader = Excel::load($filePath)->get()->first()->keys()->toArray();
        $templateHeader    = Excel::load(app_path('Core/V201/Template/Csv/iati_transaction_template_simple.csv'))->get()->first()->keys()->toArray();

        if (count(array_intersect($transactionHeader, $templateHeader)) !== count($templateHeader)) {
            return null;
        }

        $transactionCurrency = implode(',', $this->getCodes('Currency', 'Organization'));

        $rules    = [];
        $messages = [];

        foreach ($transactions as $transactionIndex => $transactionRow) {
            $requiredOnlyOneRule = sprintf(
                'required_only_one:%s.incoming_fund,%s,%s.expenditure,%s,%s.commitment,%s,%s.disbursement,%s',
                $transactionIndex,
                trimInput($transactionRow['incoming_fund']),
                $transactionIndex,
                trimInput($transactionRow['expenditure']),
                $transactionIndex,
                trimInput($transactionRow['commitment']),
                $transactionIndex,
                trimInput($transactionRow['disbursement'])
            );

            $rules = array_merge(
                $rules,
                [
                    "$transactionIndex.internal_reference"   => sprintf(
                        'unique_validation:%s.internal_reference,%s,%s,internal_reference',
                        $transactionIndex,
                        trimInput($transactionRow['internal_reference']),
                        $filename
                    ),
                    "$transactionIndex.incoming_fund"        => (trimInput($transactionRow['incoming_fund'])) ? ($requiredOnlyOneRule . '|numeric') : $requiredOnlyOneRule,
                    "$transactionIndex.expenditure"          => (trimInput($transactionRow['expenditure'])) ? 'numeric' : '',
                    "$transactionIndex.disbursement"         => (trimInput($transactionRow['disbursement'])) ? 'numeric' : '',
                    "$transactionIndex.commitment"           => (trimInput($transactionRow['commitment'])) ? 'numeric' : '',
                    "$transactionIndex.transaction_date"     => 'required|date',
                    "$transactionIndex.transaction_currency" => 'in:' . $transactionCurrency,
                    "$transactionIndex.description"          => 'required'
                ]
            );

            $messages = array_merge(
                $messages,
                [
                    "$transactionIndex.internal_reference.required"          => sprintf('At row %s Internal Reference is required', $transactionIndex + 1),
                    "$transactionIndex.internal_reference.unique_validation" => sprintf('At row %s Internal Reference should be unique.', $transactionIndex + 1),
                    "$transactionIndex.incoming_fund.numeric"                => sprintf('At row %s Incoming Fund should be numeric', $transactionIndex + 1),
                    "$transactionIndex.incoming_fund.required_only_one"      => sprintf(
                        'At row %s only one among Incoming Fund ,expenditure, disbursement and commitment is required.',
                        $transactionIndex + 1
                    ),
                    "$transactionIndex.expenditure.numeric"                  => sprintf('At row %s Expenditure should be numeric', $transactionIndex + 1),
                    "$transactionIndex.disbursement.numeric"                 => sprintf('At row %s Disbursement should be numeric', $transactionIndex + 1),
                    "$transactionIndex.commitment.numeric"                   => sprintf('At row %s Commitment should be numeric', $transactionIndex + 1),
                    "$transactionIndex.transaction_date.required"            => sprintf('At row %s Transaction Date is required', $transactionIndex + 1),
                    "$transactionIndex.transaction_date.date"                => sprintf('At row %s Transaction Date is invalid', $transactionIndex + 1),
                    "$transactionIndex.transaction_currency.in"              => sprintf('At row %s Transaction Currency is invalid', $transactionIndex + 1),
                    "$transactionIndex.description.required"                 => sprintf('At row %s Description is required', $transactionIndex + 1),
                ]
            );

        }

        return Validator::make($transactions, $rules, $messages);
    }
}
