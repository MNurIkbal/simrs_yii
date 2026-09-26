<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokbarang_v".
 */
class KelompokBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'kelompokbarang_v';
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
        return [
            'kelompokbarang_id' => 'Kelompokbarang ID',
            'kelompokbarang_kode' => 'Kelompokbarang Kode',
            'kelompokbarang_nama' => 'Kelompokbarang Nama',
            'kelompokbarang_namalain' => 'Kelompokbarang Namalain',
            'servicecategory_nama' => 'Service Category',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active'
        ];
    }
}
