<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "satuanunit_m".
 *
 * @property int $satuanunit_id
 * @property string $satuanunit_nama
 * @property string $satuanunit_singkatan
 * @property string $satuan_jenis 0=kecil , 1=sedang, 2=besar
 *
 */
class SatuanUnit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'satuanunit_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['satuanunit_id'], 'required'],
            [['satuanunit_id'], 'default', 'value' => null],
            [['satuanunit_id'], 'integer'],
            [['satuanunit_nama', 'satuanunit_singkatan'], 'string'],
            [['satuan_jenis'], 'string', 'max' => 10],
            [['satuanunit_singkatan'], 'unique'],
            [['satuanunit_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuanunit_id' => 'Satuanunit ID',
            'satuanunit_nama' => 'Satuanunit Nama',
            'satuanunit_singkatan' => 'Satuanunit Singkatan',
            'satuan_jenis' => 'Satuan Jenis',
        ];
    }
}
