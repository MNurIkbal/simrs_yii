<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienmeninggaldetail_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property int $pasienpulang_id
 * @property string $tgl_meninggal
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property string $alamat_pasien
 * @property string $tanggal_lahir
 * @property string $umur
 * @property string $jeniskelamin
 * @property string $jenis_kelamin
 * @property string $tgl_pengambilan
 * @property int $alatterpasang_id
 * @property string $nama_alat
 * @property int $qty
 * @property string $daftartindakan_nama
 * @property int $qty_tindakan
 * @property double $tarif_satuan
 */
class InfoPasienMeninggalDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'infopasienmeninggaldetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasienpulang_id', 'pasien_id', 'alatterpasang_id', 'qty', 'qty_tindakan'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasienpulang_id', 'pasien_id', 'alatterpasang_id', 'qty', 'qty_tindakan'], 'integer'],
            [['tgl_meninggal', 'tanggal_lahir', 'tgl_pengambilan'], 'safe'],
            [['alamat_pasien', 'nama_alat'], 'string'],
            [['tarif_satuan'], 'number'],
            [['no_pendaftaran', 'jeniskelamin'], 'string', 'max' => 20],
            [['nama_pasien'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['umur'], 'string', 'max' => 30],
            [['jenis_kelamin', 'daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'pasienpulang_id' => 'Pasienpulang ID',
            'tgl_meninggal' => 'Tgl Meninggal',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'no_rekam_medik' => 'No Rekam Medik',
            'alamat_pasien' => 'Alamat Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'umur' => 'Umur',
            'jeniskelamin' => 'Jeniskelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tgl_pengambilan' => 'Tgl Pengambilan',
            'alatterpasang_id' => 'Alatterpasang ID',
            'nama_alat' => 'Nama Alat',
            'qty' => 'Qty',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'qty_tindakan' => 'Qty Tindakan',
            'tarif_satuan' => 'Tarif Satuan',
        ];
    }
}
