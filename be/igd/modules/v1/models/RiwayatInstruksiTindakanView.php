<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "riwayat_instruksitindakan_v".
 *
 * @property string $tipe
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 * @property string $jenis_kelamin
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property int $ruangan_pendaftaran_id
 * @property string $ruangan_pendaftaran
 * @property int $ruangan_rawat_id
 * @property string $ruangan_rawat
 * @property int $instruksitindakan_id
 * @property string $tgl_tindakan
 * @property int $tindakan_paket_obat_id
 * @property string $tindakan_paket_obat
 * @property string $peket_detail
 * @property string $tindakan
 * @property int $qty
 * @property double $tarif_satuan
 * @property double $tarif_cyto
 * @property double $jumlah_tarif
 * @property string $ditagihkan
 * @property string $dokter_periksa
 * @property string $dokter_delegasi
 * @property string $perawat_1
 * @property string $perawat_2
 */
class RiwayatInstruksiTindakanView extends \Doco\components\DocoActiveRecord
{
    public $paketDetail;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'riwayat_instruksitindakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipe', 'tindakan_paket_obat', 'peket_detail', 'tindakan', 'ditagihkan'], 'string'],
            [['pendaftaran_id', 'ruangan_pendaftaran_id', 'ruangan_rawat_id', 'instruksitindakan_id', 'tindakan_paket_obat_id', 'qty'], 'default', 'value' => null],
            [['pendaftaran_id', 'ruangan_pendaftaran_id', 'ruangan_rawat_id', 'instruksitindakan_id', 'tindakan_paket_obat_id', 'qty'], 'integer'],
            [['tanggal_lahir', 'tgl_tindakan'], 'safe'],
            [['tarif_satuan', 'tarif_cyto', 'jumlah_tarif'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'carabayar_nama', 'penjamin_nama', 'ruangan_pendaftaran', 'ruangan_rawat', 'dokter_periksa', 'dokter_delegasi', 'perawat_1', 'perawat_2'], 'string', 'max' => 50],
            [['jenis_kelamin'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe' => 'Tipe',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'ruangan_pendaftaran_id' => 'Ruangan Pendaftaran ID',
            'ruangan_pendaftaran' => 'Ruangan Pendaftaran',
            'ruangan_rawat_id' => 'Ruangan Rawat ID',
            'ruangan_rawat' => 'Ruangan Rawat',
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'tgl_tindakan' => 'Tgl Tindakan',
            'tindakan_paket_obat_id' => 'Tindakan Paket Obat ID',
            'tindakan_paket_obat' => 'Tindakan Paket Obat',
            'peket_detail' => 'Peket Detail',
            'tindakan' => 'Tindakan',
            'qty' => 'Qty',
            'tarif_satuan' => 'Tarif Satuan',
            'tarif_cyto' => 'Tarif Cyto',
            'jumlah_tarif' => 'Jumlah Tarif',
            'ditagihkan' => 'Ditagihkan',
            'dokter_periksa' => 'Dokter Periksa',
            'dokter_delegasi' => 'Dokter Delegasi',
            'perawat_1' => 'Perawat 1',
            'perawat_2' => 'Perawat 2',
        ];
    }
}
