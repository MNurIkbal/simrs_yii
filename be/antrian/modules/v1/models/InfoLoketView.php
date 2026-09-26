<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoloket_v".
 *
 * @property int $loket_id
 * @property int $jenisantrian_id
 * @property int $fungsiantrian_id
 * @property int $konfigantrian_id
 * @property string $loket_nama
 * @property string $loket_fungsi
 * @property string $jenis_antrian
 * @property string $fungsi_antrian
 * @property string $kode_antrian
 * @property int $groupcarabayar_id
 * @property int $instalasi_id
 * @property int $penomoran_id
 */
class InfoLoketView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoloket_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loket_id', 'jenisantrian_id', 'fungsiantrian_id', 'konfigantrian_id', 'groupcarabayar_id', 'instalasi_id', 'penomoran_id'], 'default', 'value' => null],
            [['loket_id', 'jenisantrian_id', 'fungsiantrian_id', 'konfigantrian_id', 'groupcarabayar_id', 'instalasi_id', 'penomoran_id'], 'integer'],
            [['loket_fungsi'], 'string'],
            [['loket_nama'], 'string', 'max' => 50],
            [['jenis_antrian', 'fungsi_antrian'], 'string', 'max' => 200],
            [['kode_antrian'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'loket_id' => 'Loket ID',
            'jenisantrian_id' => 'Jenisantrian ID',
            'fungsiantrian_id' => 'Fungsiantrian ID',
            'konfigantrian_id' => 'Konfigantrian ID',
            'loket_nama' => 'Loket Nama',
            'loket_fungsi' => 'Loket Fungsi',
            'jenis_antrian' => 'Jenis Antrian',
            'fungsi_antrian' => 'Fungsi Antrian',
            'kode_antrian' => 'Kode Antrian',
            'groupcarabayar_id' => 'Groupcarabayar ID',
            'instalasi_id' => 'Instalasi ID',
            'penomoran_id' => 'Penomoran ID',
        ];
    }
}
