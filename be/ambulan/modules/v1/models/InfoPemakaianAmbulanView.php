<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemakaianambulan_v".
 *
 * @property int $pemakaianambulan_id
 * @property date $tgl_pesanambulan
 * @property date $tgl_pemakaiandari
 * @property date $tgl_pemakaiansampai
 * @property int $durasi_pemakaian
 * @property int $pendaftaran_id
 * @property string $no_rekam_medik
 * @property string $nama_pemesan
 * @property string $jns_kelamin
 * @property int $ambulan_id
 * @property string $no_polisi
 * @property text $jenis_ambulan
 * @property smallint $status_ambulan
 * @property text $asal_pasien
 * @property text $keluhan
 * @property string $pelayanan
 * @property int $km_awal
 *
 */
class InfoPemakaianAmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemakaianambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemakaianambulan_id', 'pendaftaran_id'], 'default', 'value' => null],
            [['pemakaianambulan_id', 'pendaftaran_id', 'ambulan_id', 'km_awal'], 'integer'],
            [['tgl_pesanambulan', 'tgl_pemakaiandari', 'tgl_pemakaiansampai', 'status_ambulan', 'asal_pasien', 'keluhan','created_date'], 'safe'],
            [['no_rekam_medik', 'nama_pemesan', 'jns_kelamin', 'no_polisi', 'pelayanan'], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['jenis_ambulan', 'status_ambulan'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemakaianambulan_id' => 'Pemakaian Ambulan ID',
            'tgl_pesanambulan' => 'Tanggal Pesan Ambulan',
            'tgl_pemakaiandari' => 'Tanggal Pemakaian Dari',
            'tgl_pemakaiansampai' => 'Tanggal Pemakaian Sampai',
            'durasi_pemakaian' => 'Durasi Pemakaian',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pemesan' => 'Nama Pemesan',
            'jns_kelamin' => 'Jns Kelamin',
            'ambulan_id' => 'Ambulan ID',
            'no_polisi' => 'No Polisi',
            'jenis_ambulan' => 'Jenis Ambulan',
            'status_ambulan' => 'Status Ambulan',
            'asal_pasien' => 'Asal Pasien',
            'keluhan' => 'Keluhan',
            'pelayanan' => 'Pelayanan',
            'km_awal' => 'Km Awal',

        ];
    }
}
