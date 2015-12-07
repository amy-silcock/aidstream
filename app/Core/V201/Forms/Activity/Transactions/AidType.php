<?php namespace App\Core\V201\Forms\Activity\Transactions;

use App\Core\Form\BaseForm;
use App\Core\V201\Traits\Forms\Transaction\AidType as AidTypeCodeList;

/**
 * Class AidType
 * @package App\Core\V201\Forms\Activity\Transactions
 */
class AidType extends BaseForm
{
    use AidTypeCodeList;
    protected $showFieldErrors = true;

    /**
     * builds aid type form
     */
    public function buildForm()
    {
        $this
            ->add(
                'aid_type',
                'select',
                [
                    'choices'     => $this->getAidTypeCodeList(),
                    'empty_value' => 'Select one of the following option :',
                    'attr'        => ['class' => 'form-control aid_type']
                ]
            );
    }
}
