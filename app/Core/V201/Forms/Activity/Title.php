<?php namespace App\Core\V201\Forms\Activity;

use App\Core\Form\BaseForm;

/**
 * Class Title
 * Contains the function to create the title form
 * @package App\Core\V201\Forms\Activity
 */
class Title extends BaseForm
{
    /**
     * builds the activity title form
     */
    public function buildForm()
    {
        $this
            ->addNarrative('title')
            ->addAddMoreButton('add_title', 'title');
    }
}
