<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "pasiendirujukkeluar_t".
 *
 * @property int $pasiendirujukkeluar_id
 * @property int $pasien_id
 * @property int $rujukankeluar_id
 * @property int $pasienadmisi_id
 * @property int $pegawai_id
 * @property int $pendaftaran_id
 * @property string $nosuratrujukan
 * @property string $tgldirujuk
 * @property string $kepadayth
 * @property string $dirujukkebagian
 * @property string $alasandirujuk
 * @property string $hasilpemeriksaan_ruj
 * @property string $diagnosasementara_ruj
 * @property string $pengobatan_ruj
 * @property string $lainlain_ruj
 * @property string $catatandokterperujuk
 * @property int $ruanganasal_id
 * @property string $tglberlakusurat
 * @property string $sampaidengan
 * @property string $lampiransurat
 * @property string $dokterpemeriksa
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class PasienDirujukKeluar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasiendirujukkeluar_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rujukankeluar_id', 'pegawai_id', 'nosuratrujukan', 'tgldirujuk', 'ruanganasal_id', 'tglberlakusurat', 'sampaidengan'], 'required'],
            [['pasien_id', 'rujukankeluar_id', 'pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasien_id', 'rujukankeluar_id', 'pasienadmisi_id', 'pegawai_id', 'pendaftaran_id', 'ruanganasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgldirujuk', 'tglberlakusurat', 'sampaidengan', 'created_date', 'last_modified_date', 'deleted_date', 'permintaankepenunjang_id', 'diagnosa'], 'safe'],
            [['alasandirujuk', 'hasilpemeriksaan_ruj', 'diagnosasementara_ruj', 'pengobatan_ruj', 'lainlain_ruj', 'catatandokterperujuk', 'dokterpemeriksa', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nosuratrujukan'], 'string', 'max' => 50],
            [['kepadayth'], 'string', 'max' => 100],
            [['dirujukkebagian'], 'string', 'max' => 30],
            [['lampiransurat'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasiendirujukkeluar_id' => 'Pasiendirujukkeluar ID',
            'pasien_id' => 'Pasien ID',
            'rujukankeluar_id' => 'Rujukankeluar ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'nosuratrujukan' => 'Nosuratrujukan',
            'tgldirujuk' => 'Tgldirujuk',
            'kepadayth' => 'Kepadayth',
            'dirujukkebagian' => 'Dirujukkebagian',
            'alasandirujuk' => 'Alasandirujuk',
            'hasilpemeriksaan_ruj' => 'Hasilpemeriksaan Ruj',
            'diagnosasementara_ruj' => 'Diagnosasementara Ruj',
            'pengobatan_ruj' => 'Pengobatan Ruj',
            'lainlain_ruj' => 'Lainlain Ruj',
            'catatandokterperujuk' => 'Catatandokterperujuk',
            'ruanganasal_id' => 'Ruanganasal ID',
            'tglberlakusurat' => 'Tglberlakusurat',
            'sampaidengan' => 'Sampaidengan',
            'lampiransurat' => 'Lampiransurat',
            'dokterpemeriksa' => 'Dokterpemeriksa',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
