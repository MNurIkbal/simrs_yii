<?php

namespace app\modules\rajal\models;

use Yii;

class PasienDirujukKeluarForm extends \yii\base\Model
{
    public $pasiendirujukkeluar_id;
    public $pasien_id;
    public $rujukankeluar_id;
    public $pasienadmisi_id;
    public $pegawai_id;
    public $pendaftaran_id;
    public $nosuratrujukan;
    public $tgldirujuk;
    public $kepadayth;
    public $dirujukkebagian;
    public $alasandirujuk;
    public $hasilpemeriksaan_ruj;
    public $diagnosasementara_ruj;
    public $pengobatan_ruj;
    public $lainlain_ruj;
    public $catatandokterperujuk;
    public $ruanganasal_id;
    public $tglberlakusurat;
    public $sampaidengan;
    public $lampiransurat;
    public $dokterpemeriksa;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

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
            [['tgldirujuk', 'tglberlakusurat', 'sampaidengan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'rujukankeluar_id' => 'Rujukan Keluar',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pegawai_id' => 'Dokter Perujuk',
            'pendaftaran_id' => 'Pendaftaran ID',
            'nosuratrujukan' => Yii::t('fe', 'nosuratrujukan'),
            'tgldirujuk' => Yii::t('fe', 'tgldirujuk'),
            'kepadayth' => Yii::t('fe', 'kepadayth'),
            'dirujukkebagian' => Yii::t('fe', 'dirujukkebagian'),
            'alasandirujuk' => Yii::t('fe', 'alasandirujuk'),
            'hasilpemeriksaan_ruj' => Yii::t('fe', 'hasilpemeriksaan_ruj'),
            'diagnosasementara_ruj' => Yii::t('fe', 'diagnosasementara_ruj'),
            'pengobatan_ruj' => 'Pengobatan Ruj',
            'lainlain_ruj' => Yii::t('fe', 'lainlain_ruj'),
            'catatandokterperujuk' => Yii::t('fe', 'catatandokterperujuk'),
            'ruanganasal_id' => 'Ruanganasal ID',
            'tglberlakusurat' => Yii::t('fe', 'tglberlakusurat'),
            'sampaidengan' => Yii::t('fe', 'tglberlakusurat'),
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
