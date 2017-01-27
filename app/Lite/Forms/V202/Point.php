<?php namespace App\Lite\Forms\V202;


use App\Lite\Forms\LiteBaseForm;

class Point extends LiteBaseForm
{
    public function buildForm()
    {
        $this
            ->add('latitude', 'text', ['attr' => ['class' => 'latitude'], 'label' => trans('elementForm.latitude'), 'help_block' => $this->addHelpText('Activity_Location_Point_Pos-latitude')])
            ->add('longitude', 'text', ['attr' => ['class' => 'longitude'], 'label' => trans('elementForm.longitude'), 'help_block' => $this->addHelpText('Activity_Location_Point_Pos-longitude')])
            ->add(
                'map',
                'static',
                [
                    'label'   => false,
                    'attr'    => [
                        'class' => 'map_container',
                        'style' => 'height: 400px;'
                    ],
                    'wrapper' => ['class' => 'form-group full-width-wrap']
                ]
            );
    }
}
