<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemakaianambulandetail_v".
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
 * @property text $jenis_ambulan
 * @property text $asal_pasien
 * @property text $keluhan
 * @property string $pelayanan
 * @property int $km_awal
 * @property string $nama_pegawai
 * @property string $nomorindukpegawai
 * @property string $jabatan_nama
 * @property string $obatalkes_nama
 * @property int $qty
 */
class InfoPemakaianAmbulanDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemakaianambulandetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemakaianambulan_id', 'pendaftaran_id'], 'default', 'value' => null],
            [['pemakaianambulan_id', 'pendaftaran_id', 'km_awal', 'qty', 'durasi_pemakaian'], 'integer'],
            [['tgl_pesanambulan', 'tgl_pemakaiandari', 'tgl_pemakaiansampai'], 'safe'],
            [['nama_pemesan', 'no_rekam_medik', 'asal_pasien', 'keluhan', 'jenis_ambulan', 'pelayanan', 'nama_pegawai', 'nomorindukpegawai', 'jabatan_nama', 'obatalkes_nama'], 'string'],
            [['no_pesanambulan'], 'string', 'max' => 100],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['jns_kelamin', 'nama_pemesan'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemakaianambulan_id' => 'Pemakaian Ambulan ID',
            'tgl_pesanambulan' => 'Tgl Pesan Ambulan',
            'tgl_pemakaiandari' => 'Tgl Pemakaian Dari',
            'tgl_pemakaiansampai' => 'Tgl Pemakaian Sampai',
            'durasi_pemakaian' => 'Durasi Pemakaian',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pemesan' => 'Nama Pemesan',
            'jns_kelamin' => 'Jns Kelamin',
            'jenis_ambulan' => 'Jenis Ambulan',
            'asal_pasien' => 'Asal Pasien',
            'keluhan' => 'Keluhan',
            'pelayanan' => 'Pelayanan',
            'km_awal' => 'Km Awal',
            'nama_pegawai' => 'Nama Pegawai',
            'nomorindukpegawai' => 'Nomor Induk Pegawai',
            'jabatan_nama' => 'Nama Jabatan',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'qty' => 'Qty',
        ];
    }
}
