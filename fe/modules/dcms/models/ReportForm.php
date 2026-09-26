<?php

namespace Doco\dcms\models;

use Yii;

class ReportForm extends \yii\base\Model
{
    public $docmapping_id;
    public $title;
    public $code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [[['docmapping_id','title','code'],'safe']];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}