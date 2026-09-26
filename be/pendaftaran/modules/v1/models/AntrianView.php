<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "antrian_v".
 *
 * @property int $antrian_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $loket_nama
 * @property string $no_antrian
 * @property string $carabayar_nama
 * @property string $tgl_antrian
 * @property string $layarantrian_nama
 * @property string $loket_namalain
 */
class AntrianView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antrian_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['antrian_id', 'ruangan_id'], 'default', 'value' => null],
            [['antrian_id', 'ruangan_id'], 'integer'],
            [['tgl_antrian'], 'safe'],
            [['ruangan_nama', 'loket_nama', 'carabayar_nama', 'loket_namalain'], 'string', 'max' => 50],
            [['no_antrian'], 'string', 'max' => 6],
            [['layarantrian_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => 'Antrian ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'loket_nama' => 'Loket Nama',
            'no_antrian' => 'No Antrian',
            'carabayar_nama' => 'Carabayar Nama',
            'tgl_antrian' => 'Tgl Antrian',
            'layarantrian_nama' => 'Layarantrian Nama',
            'loket_namalain' => 'Loket Namalain',
        ];
    }
}
