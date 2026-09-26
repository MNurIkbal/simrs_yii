<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pengadaan\models;

use Yii;

class POMultipleValidasiForm extends \yii\base\Model
{

    public $no_pr;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_pr','status'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_pr' => 'No. PR',
            'status' => 'Status',
            
        ];
    }
}
