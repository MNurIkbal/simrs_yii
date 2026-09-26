<?php

namespace app\modules\v1\models;

use Yii;

class DetailPemesananBarangView extends \Doco\components\DocoActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detailpemesananbarang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
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
