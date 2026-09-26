<?php

namespace app\modules\v1\models;

use Yii;

class KontrakSupplierView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'kontraksupplier_v';
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