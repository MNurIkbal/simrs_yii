<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporankunjunganpenunjang_instalasi_v".
 *
 * @property string $instalasi_id
 * @property string $instalasi_nama
 * @property int $jenispemeriksaan_id
 * @property string $jenispemeriksaan_nama

 */
class JenisPemeriksaanPenunjangInstalasiV extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporankunjunganpenunjang_instalasi_v';
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
            'instalasi_id' => 'Instalasi id',
            'instalasi_nama' => 'Instalasi Nama',
            'jenispemeriksaan_id' => 'Jenispemeriksaan Id',
            'jenispemeriksaan_nama' => 'Jenispemeriksaan Nama',
        ];
    }
}
