<?php

/**
 * @author : Ardi Pratama Septiadi
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "prescribe_v".
 *
 */
class PrescribeView extends \Doco\components\DocoActiveRecord
{
    public $tgl_resep;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prescribe_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }
}
