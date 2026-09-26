<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopesanambulan_v".
 *
 * @property int $pesanambulan_id
 * @property string $tgl_pesanambulan
 * @property string $no_pesanambulan
 * @property int $pendaftaran_id
 * @property string $no_rekam_medik
 * @property string $nama_pemesan
 * @property string $jns_kelamin
 * @property string $jenis_ambulan
 * @property string $asal_pasien
 * @property string $keluhan
 * @property string $status_ambulan
 */
class InfoPesanAmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopesanambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesanambulan_id', 'pendaftaran_id'], 'default', 'value' => null],
            [['pesanambulan_id', 'pendaftaran_id'], 'integer'],
            [['tgl_pesanambulan', 'created_date'], 'safe'],
            [['nama_pemesan', 'jenis_ambulan', 'asal_pasien', 'keluhan'], 'string'],
            [['no_pesanambulan'], 'string', 'max' => 100],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['jns_kelamin', 'status_ambulan'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesanambulan_id' => 'Pesanambulan ID',
            'tgl_pesanambulan' => 'Tgl Pesanambulan',
            'no_pesanambulan' => 'No Pesanambulan',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pemesan' => 'Nama Pemesan',
            'jns_kelamin' => 'Jns Kelamin',
            'jenis_ambulan' => 'Jenis Ambulan',
            'asal_pasien' => 'Asal Pasien',
            'keluhan' => 'Keluhan',
            'status_ambulan' => 'Status Ambulan',
        ];
    }
}
