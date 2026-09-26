<?php

namespace app\modules\v1\models;

use Yii;

class SatuanUnitR extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'satuanunit_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuanunit_id' => 'Satuanunit ID',
            'satuanunit_nama' => 'Nama Lainnya',
            'satuanunit_namalain' => 'Nama Lainnya',
            'satuanunit_singkatan' => 'Satuanunit Singkatan',
            'satuan_jenis' => 'Satuan Jenis',
        ];
    }

}
