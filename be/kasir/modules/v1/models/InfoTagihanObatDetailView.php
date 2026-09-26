<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotagihanobatdetail_v".
 *
 * @property int $tindakansudahbayar_id

 */
class InfoTagihanObatDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanobatdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
