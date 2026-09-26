<?php

namespace Doco\models;

use Yii;
use Doco\models\DocMapping;

class Report extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    protected $xssProtected = [];
    
    public static function tableName()
    {
        return 'report_t';
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    public function rules()
    {
        return [
            [['docmapping_id','title','code','config'],'safe']
        ];
    }

    public function getDocmapping()
    {
        return $this->hasOne(DocMapping::className(),['docmapping_id'=>'docmapping_id']);
    }
}