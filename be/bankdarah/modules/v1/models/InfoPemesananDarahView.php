<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemesanandarah_v".
 *
 * @property int $pesandarah_id
 * @property string $no_pesandarah
 * @property string $tgl_pesandarah
 * @property int $ruanganpemesan_id
 * @property string $ruangan_nama
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $umur
 * @property int $jenis_kelaminid
 * @property string $jenis_kelamin
 * @property int $golongandarah_id
 * @property string $golongandarah_nama
 * @property string $kadar_hb
 * @property string $diagnosa
 * @property int $pasienadmisi_id
 * @property int $pesandarahdetail_id
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $lama_penyimpanan
 * @property int $suhu_penyimpanan
 * @property double $harga
 * @property string $tgl_mintakirim
 * @property string $wkt_mintakirim
 * @property int $jumlah
 * @property double $harga_satuan
 * @property string $indikasi_transfusi
 * @property int $metode_pengambilan
 * @property string $metode_pengambilan_nama
 * @property double $total_harga
 * @property string $riwayat_transfusi
 * @property string $riwayat_kehamilan
 * @property string $keterangan
 * @property string $additional_data
 * @property bool $is_deleted
 * @property bool $is_active
 */
class InfoPemesananDarahView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopemesanandarah_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarah_id', 'ruanganpemesan_id', 'pasien_id', 'jenis_kelaminid', 'golongandarah_id', 'pasienadmisi_id', 'pesandarahdetail_id', 'jenisdarah_id', 'lama_penyimpanan', 'suhu_penyimpanan', 'jumlah', 'metode_pengambilan'], 'default', 'value' => null],
            [['pesandarah_id', 'ruanganpemesan_id', 'pasien_id', 'jenis_kelaminid', 'golongandarah_id', 'pasienadmisi_id', 'pesandarahdetail_id', 'jenisdarah_id', 'lama_penyimpanan', 'suhu_penyimpanan', 'jumlah', 'metode_pengambilan'], 'integer'],
            [['tgl_pesandarah', 'tanggal_lahir', 'tgl_mintakirim', 'wkt_mintakirim'], 'safe'],
            [['jenis_kelamin', 'golongandarah_nama', 'kadar_hb', 'diagnosa', 'metode_pengambilan_nama', 'riwayat_transfusi', 'riwayat_kehamilan', 'keterangan', 'additional_data'], 'string'],
            [['harga', 'harga_satuan', 'total_harga'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pesandarah', 'jenisdarah_nama', 'indikasi_transfusi'], 'string', 'max' => 255],
            [['ruangan_nama', 'nama_pasien'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['umur'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesandarah_id' => 'Pesandarah ID',
            'no_pesandarah' => 'No Pesandarah',
            'tgl_pesandarah' => 'Tgl Pesandarah',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'umur' => 'Umur',
            'jenis_kelaminid' => 'Jenis Kelaminid',
            'jenis_kelamin' => 'Jenis Kelamin',
            'golongandarah_id' => 'Golongandarah ID',
            'golongandarah_nama' => 'Golongandarah Nama',
            'kadar_hb' => 'Kadar Hb',
            'diagnosa' => 'Diagnosa',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pesandarahdetail_id' => 'Pesandarahdetail ID',
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Jenisdarah Nama',
            'lama_penyimpanan' => 'Lama Penyimpanan',
            'suhu_penyimpanan' => 'Suhu Penyimpanan',
            'harga' => 'Harga',
            'tgl_mintakirim' => 'Tgl Mintakirim',
            'wkt_mintakirim' => 'Wkt Mintakirim',
            'jumlah' => 'Jumlah',
            'harga_satuan' => 'Harga Satuan',
            'indikasi_transfusi' => 'Indikasi Transfusi',
            'metode_pengambilan' => 'Metode Pengambilan',
            'metode_pengambilan_nama' => 'Metode Pengambilan Nama',
            'total_harga' => 'Total Harga',
            'riwayat_transfusi' => 'Riwayat Transfusi',
            'riwayat_kehamilan' => 'Riwayat Kehamilan',
            'keterangan' => 'Keterangan',
            'additional_data' => 'Additional Data',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }
}
