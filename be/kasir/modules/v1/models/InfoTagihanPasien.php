<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotagihanpasien_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property string $tgl_pendaftaran
 * @property string $no_pendaftaran
 * @property int $pasienadmisi_id
 * @property int $instalasi_id
 * @property int $tindakan_obat_id
 * @property string $tindakan_obat_nama
 * @property bool $is_obat
 * @property bool $is_deleted
 * @property bool $is_valid
 * @property double $tarif_satuan
 * @property double $qty
 * @property double $tarif_cyto
 * @property double $sub_total
 * @property int $ruangan_id
 * @property int $pasienmasukpenunjang_id
 * @property int $dokterpenanggungjawab_id
 * @property string $tgl_pelayanan
 * @property int $kelaspelayanan_id
 * @property int $pelayanan_id
 * @property int $carabayar_pelayanan_id
 * @property string $carabayar_pelayanan
 * @property int $penjamin_pelayanan_id
 * @property string $penjamin_pelayanan
 * @property int $carabayar_pendaftaran_id
 * @property int $penjamin_pendaftaran_id
 * @property int $pasienpulang_id
 * @property int $penjualanresep_id
 * @property int $groupcarabayar_id
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property string $dokterpenanggungjawab_nama
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
 */
class InfoTagihanPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanpasien_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'tindakan_obat_id', 'ruangan_id', 'kelaspelayanan_id', 'carabayar_pelayanan_id', 'penjamin_pelayanan_id', 'carabayar_pendaftaran_id', 'penjamin_pendaftaran_id', 'pasienpulang_id', 'kelompoktindakan_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'penjualanresep_id', 'groupcarabayar_id', 'pasienmasukpenunjang_id', 'dokterpenanggungjawab_id', 'instalasi_id', 'pasienadmisi_id', 'tindakan_obat_id', 'ruangan_id', 'kelaspelayanan_id', 'pelayanan_id', 'carabayar_pelayanan_id', 'penjamin_pelayanan_id', 'carabayar_pendaftaran_id', 'penjamin_pendaftaran_id', 'pasienpulang_id', 'kelompoktindakan_id'], 'integer'],
            [['tgl_pendaftaran', 'tgl_pelayanan', 'dokterpenanggungjawab_nama', 'tglpasienpulang'], 'safe'],
            [['tindakan_obat_nama'], 'string'],
            [['is_obat', 'is_deleted', 'is_valid'], 'boolean'],
            [['tarif_satuan', 'qty', 'tarif_cyto', 'sub_total', 'jumlah_uangmuka'], 'number'],
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
            'penjualanresep_id' => 'Penjualan Resep ID',
            'groupcarabayar_id' => 'Group CaraBayar ID',
            'dokterpenanggungjawab_id' => 'Dokter Penanggungjawab ID',
            'dokterpenanggungjawab_nama' => 'Dokter Penanggungjawab Nama',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tindakan_obat_id' => 'Tindakan Obat ID',
            'instalasi_id' => 'Instalasi ID',
            'pasienmasukpenunjang_id' => 'Pasien Masuk Penunjang ID',
            'tindakan_obat_nama' => 'Tindakan Obat Nama',
            'is_obat' => 'Is Obat',
            'is_valid' => 'Is Valid',
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
            'tarif_cyto' => 'Tarif Cyto',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'tglpasienpulang' => 'Tglpasienpulang',
            'jumlah_uangmuka' => 'Jumlah Uangmuka',
        ];
    }
}
