<?php namespace App\Core\V201\Forms\Organization;

use App\Core\Form\BaseForm;
use Illuminate\Database\DatabaseManager;

class NarrativeForm extends BaseForm
{
    protected $showFieldErrors = true;

    public function buildForm()
    {
        $this->add('narrative', 'textarea', ['label' => trans('elementForm.text'), 'help_block' => $this->addHelpText('Organisation_Name_Narrative-text'), 'attr' => ['rows' => 4], 'required' => true])
             ->addSelect('language', $this->getCodeList('Language', 'Organization'), trans('elementForm.language'), $this->addHelpText('Organisation_Name_Narrative-xml_lang'))
             ->addRemoveThisButton('remove');
    }
}
