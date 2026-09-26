<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "riwayattindakan_v".
 *
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property int $ruangan_pendaftaran_id
 * @property string $ruangan_pendaftaran
 * @property int $id
 * @property string $tipe_pelayanan
 * @property string $tgl_tindakan
 * @property string $tindakan_obat
 * @property string $tindakan
 * @property double $qty
 * @property double $tarif_satuan
 * @property double $tarifcyto_tindakan
 * @property double $jumlah_tarif
 * @property double $ditagihkan
 * @property int $ruangan_pelayanan_id
 * @property string $ruangan_pelayanan
 * @property string $dokter_pemeriksa
 * @property string $dokter_delegasi
 * @property string $perawat_1
 * @property string $perawat_2
 */
class RiwayatTindakanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'riwayattindakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'ruangan_pendaftaran_id', 'id', 'ruangan_pelayanan_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'ruangan_pendaftaran_id', 'id', 'ruangan_pelayanan_id'], 'integer'],
            [['tanggal_lahir', 'tgl_tindakan'], 'safe'],
            [['jenis_kelamin', 'tipe_pelayanan', 'tindakan_obat', 'tindakan', 'dokter_pemeriksa', 'dokter_delegasi'], 'string'],
            [['qty', 'tarif_satuan', 'tarifcyto_tindakan', 'jumlah_tarif', 'ditagihkan'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'jenis_kelamin', 'carabayar_nama', 'penjamin_nama', 'ruangan_pendaftaran', 'ruangan_pelayanan', 'perawat_1', 'perawat_2'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'ruangan_pendaftaran_id' => 'Ruangan Pendaftaran ID',
            'ruangan_pendaftaran' => 'Ruangan Pendaftaran',
            'id' => 'ID',
            'tipe_pelayanan' => 'Tipe Pelayanan',
            'tgl_tindakan' => 'Tgl Tindakan',
            'tindakan_obat' => 'Tindakan Obat',
            'tindakan' => 'Tindakan',
            'qty' => 'Qty',
            'tarif_satuan' => 'Tarif Satuan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'jumlah_tarif' => 'Jumlah Tarif',
            'ditagihkan' => 'Ditagihkan',
            'ruangan_pelayanan_id' => 'Ruangan Pelayanan ID',
            'ruangan_pelayanan' => 'Ruangan Pelayanan',
            'dokter_pemeriksa' => 'Dokter Pemeriksa',
            'dokter_delegasi' => 'Dokter Delegasi',
            'perawat_1' => 'Perawat 1',
            'perawat_2' => 'Perawat 2',
        ];
    }
}
