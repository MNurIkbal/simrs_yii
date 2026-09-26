<?php

namespace Doco\dcms\models;

use Yii;

class ReportConfigForm extends \yii\base\Model
{
    public $docmapping_enabled;
    public $render_mode;
    public $show_in_viewer;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['docmapping_enabled','show_in_viewer'],'boolean'],
            [['docmapping_enabled','render_mode','show_in_viewer'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}