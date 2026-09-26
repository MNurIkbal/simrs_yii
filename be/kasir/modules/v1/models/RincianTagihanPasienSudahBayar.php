<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rinciantagihanpasiensudahbayar_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property int $pasienadmisi_id
 * @property int $tindakan_obat_id
 * @property string $tindakan_obat_nama
 * @property bool $is_obat
 * @property double $tarif_satuan
 * @property double $qty
 * @property double $sub_total
 * @property int $ruangan_id
 * @property string $tgl_pelayanan
 * @property int $kelaspelayanan_id
 * @property int $carabayar_pelayanan_id
 * @property string $carabayar_pelayanan
 * @property int $penjamin_pelayanan_id
 * @property string $penjamin_pelayanan
 * @property int $carabayar_pendaftaran_id
 * @property int $penjamin_pendaftaran_id
 * @property int $pasienpulang_id
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property string $carabayar_pendaftaran
 * @property string $penjamin_pendaftaran
 * @property string $kelaspelayanan_nama
 * @property string $instalasi_pelayanan
 * @property string $ruangan_pelayanan
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $no_mobile_pasien
 * @property string $tglpasienpulang
 * @property double $jumlah_uangmuka
 * @property int $pelayanan_id
 * @property double $tarifcyto_tindakan
 * @property double $total_tagihan
 * @property double $total_uang_muka
 * @property double $total_sudah_dibayarkan
 * @property double $total_sisatagihan
 */
class RincianTagihanPasienSudahBayar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rinciantagihanpasiensudahbayar_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'tindakan_obat_id', 'ruangan_id', 'kelaspelayanan_id', 'carabayar_pelayanan_id', 'penjamin_pelayanan_id', 'carabayar_pendaftaran_id', 'penjamin_pendaftaran_id', 'pasienpulang_id', 'kelompoktindakan_id', 'pelayanan_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'tindakan_obat_id', 'ruangan_id', 'kelaspelayanan_id', 'carabayar_pelayanan_id', 'penjamin_pelayanan_id', 'carabayar_pendaftaran_id', 'penjamin_pendaftaran_id', 'pasienpulang_id', 'kelompoktindakan_id', 'pelayanan_id'], 'integer'],
            [['tgl_pendaftaran', 'tgl_pelayanan', 'tglpasienpulang','tarif_cyto'], 'safe'],
            [['tindakan_obat_nama'], 'string'],
            [['is_obat'], 'boolean'],
            [['tarif_satuan', 'qty', 'sub_total', 'jumlah_uangmuka', 'tarifcyto_tindakan', 'total_tagihan', 'total_uang_muka', 'total_sudah_dibayarkan', 'total_sisatagihan'], 'number'],
            [['no_pendaftaran', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['carabayar_pelayanan', 'penjamin_pelayanan', 'kelompoktindakan_nama', 'carabayar_pendaftaran', 'penjamin_pendaftaran', 'kelaspelayanan_nama', 'instalasi_pelayanan', 'ruangan_pelayanan', 'nama_pasien'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tindakan_obat_id' => 'Tindakan Obat ID',
            'tindakan_obat_nama' => 'Tindakan Obat Nama',
            'is_obat' => 'Is Obat',
            'tarif_satuan' => 'Tarif Satuan',
            'qty' => 'Qty',
            'sub_total' => 'Sub Total',
            'ruangan_id' => 'Ruangan ID',
            'tgl_pelayanan' => 'Tgl Pelayanan',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_pelayanan_id' => 'Carabayar Pelayanan ID',
            'carabayar_pelayanan' => 'Carabayar Pelayanan',
            'penjamin_pelayanan_id' => 'Penjamin Pelayanan ID',
            'penjamin_pelayanan' => 'Penjamin Pelayanan',
            'carabayar_pendaftaran_id' => 'Carabayar Pendaftaran ID',
            'penjamin_pendaftaran_id' => 'Penjamin Pendaftaran ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'carabayar_pendaftaran' => 'Carabayar Pendaftaran',
            'penjamin_pendaftaran' => 'Penjamin Pendaftaran',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'instalasi_pelayanan' => 'Instalasi Pelayanan',
            'ruangan_pelayanan' => 'Ruangan Pelayanan',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'tglpasienpulang' => 'Tglpasienpulang',
            'jumlah_uangmuka' => 'Jumlah Uangmuka',
            'pelayanan_id' => 'Pelayanan ID',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'total_tagihan' => 'Total Tagihan',
            'total_uang_muka' => 'Total Uang Muka',
            'total_sudah_dibayarkan' => 'Total Sudah Dibayarkan',
            'total_sisatagihan' => 'Total Sisatagihan',
        ];
    }
}
