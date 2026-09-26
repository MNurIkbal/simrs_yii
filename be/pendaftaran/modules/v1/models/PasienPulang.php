<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienpulang_t".
 *
 * @property int $pasienpulang_id
 * @property int $pasien_id
 * @property int $pasienbatalpulang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $carakeluar_id
 * @property int $kondisikeluar_id
 * @property string $tglpasienpulang
 * @property int $ruanganakhir_id
 * @property string $penerima_pasien
 * @property int $lama_rawat
 * @property string $satuan_lamarawat
 * @property bool $is_meninggal
 * @property string $keterangan_keluar
 * @property int $hari_perawatan
 * @property string $satuan_hariperawatan
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $tgl_meninggal
 * @property bool $is_rencanakontrol
 * @property string $tgl_rencanakontrol
 * @property int $pasiendirujukkeluar_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $deleted_date
 * @property int $deleted_by
 *
 */
class PasienPulang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienpulang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carakeluar_id','kondisikeluar_id'], 'required'],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'pasiendirujukkeluar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'pasienbatalpulang_id', 'pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'kondisikeluar_id', 'ruanganakhir_id', 'lama_rawat', 'hari_perawatan', 'pasiendirujukkeluar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglpasienpulang', 'tgl_meninggal', 'tgl_rencanakontrol', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_meninggal', 'is_deleted', 'is_active', 'is_rencanakontrol'], 'boolean'],
            [['keterangan_keluar', 'additional_data'], 'string'],
            [['penerima_pasien'], 'string', 'max' => 100],
            [['satuan_lamarawat', 'satuan_hariperawatan'], 'string', 'max' => 50],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienpulang_id' => 'Pasienpulang ID',
            'pasien_id' => 'Pasien ID',
            'pasienbatalpulang_id' => 'Pasienbatalpulang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'carakeluar_id' => 'Carakeluar ID',
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'tglpasienpulang' => 'Tglpasienpulang',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'penerima_pasien' => 'Penerima Pasien',
            'lama_rawat' => 'Lama Rawat',
            'satuan_lamarawat' => 'Satuan Lamarawat',
            'is_meninggal' => 'Is Meninggal',
            'keterangan_keluar' => 'Keterangan Keluar',
            'hari_perawatan' => 'Hari Perawatan',
            'satuan_hariperawatan' => 'Satuan Hariperawatan',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'tgl_meninggal' => 'Tgl Meninggal',
            'is_rencanakontrol' => 'Is Rencanakontrol',
            'tgl_rencanakontrol' => 'Tgl Rencanakontrol',
            'pasiendirujukkeluar_id' => 'Pasiendirujukkeluar ID',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

}
