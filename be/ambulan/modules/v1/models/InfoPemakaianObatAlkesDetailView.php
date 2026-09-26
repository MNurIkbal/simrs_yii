<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemakaianobatalkesdetail_v".
 *
 * @property int $pemakaianobatdetail_id
 * @property int $pemakaianobat_id
 * @property date $tglpemakaianobat
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property int $qty_satuanpakai
 * @property int $satuankecil_id
 * @property string $satuankecil_nama
 * @property string $nopemakaian_obat
 * @property double $jumlah_input
 * @property int $satuanbesar_id
 * @property string $satuanbesar_nama
 * @property text $keterangan_pemakaianobat
 * @property string $ket_obatpakai
 */
class InfoPemakaianObatAlkesDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemakaianobatalkesdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemakaianobatdetail_id', 'pemakaianobat_id', 'jumlah_input'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'obatalkes_id', 'qty_satuanpakai', 'satuankecil_id', 'satuanbesar_id'], 'integer'],
            [['tglpemakaianobat', 'tgl_pemakaiandari', 'tgl_pemakaiansampai'], 'safe'],
            [['nama_pegawai','obatalkes_namalain', 'satuankecil_nama', 'nopemakaian_obat'], 'string'],
            [['satuanbesar_nama'], 'string', 'max' => 100],
            [['no_rekam_medik', 'ket_obatpakai'], 'string', 'max' => 10],
            [['keterangan_pemakaianobat'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemakaianobatdetail_id' => 'Pemakaian Obat Detail ID',
            'tglpemakaianobat' => 'Tanggal Pemakaian Obat',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'obatalkes_id' => 'Obat Alkes ID',
            'obatalkes_namalain' => 'Nama Lain Obat Alkes',
            'qty_satuanpakai' => 'Qty Satuan Pakai',
            'satuankecil_id' => 'Satuan Kecil ID',
            'satuankecil_nama' => 'Nama Satuan Kecil',
            'nopemakaian_obat' => 'No Pemakaian Obat',
            'jumlah_input' => 'Jumlah Input',
            'satuanbesar_id' => 'Satuan Besar ID',
            'satuanbesar_nama' => 'Nama Satuan Besar',
            'keterangan_pemakaianobat' => 'Keterangan Pemakaian Obat',
            'ket_obatpakai' => 'Keterangan Obat Pakai',
            
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
            'nomorindukpegawai' => 'Nomor Induk Pegawai',
            'jabatan_nama' => 'Nama Jabatan',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'qty' => 'Qty',
        ];
    }
}
