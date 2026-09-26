<?php

/**
 * @Author: afil
 * @Date:   2018-01-19 09:21:25
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-15 10:37:04
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class PasienMorbiditasForm extends \yii\base\Model
{

    public $pasienmorbiditas_id;
    public $morfologineoplasma_id;
    public $instalasi_id;
    public $kamarruangan_id;
    public $jenisketunaan_id;
    public $jeniskasuspenyakit_id;
    public $ruangan_id;
    public $diagnosaicdix_id;
    public $pegawai_id;
    public $sebabdiagnosa_id;
    public $kelompokumur_id;
    public $diagnosa_id;
    public $sebabin_id;
    public $pasien_id;
    public $jenisin_id;
    public $kelompokdiagnosa_id;
    public $golonganumur_id;
    public $pendaftaran_id;
    public $penyebabluarcedera_id;
    public $pasienadmisi_id;
    public $tglmorbiditas;
    public $kasusdiagnosa;
    public $umur_0_28hr;
    public $umur_28hr_1thn;
    public $umur_1_4thn;
    public $umur_5_14thn;
    public $umur_15_24thn;
    public $umur_25_44thn;
    public $umur_45_64thn;
    public $umur_65;
    public $infeksinosokomial;
    public $laki_laki;
    public $perempuan;
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
    public $umur_0_6hr;

    public $diagnosa_nama;
    public $diagnosa_kode;
    public $kelompokdiagnosa_nama;
    // constants
    const SCENARIO_SESSION = 'session'; //scenario add session
    const KD_UTAMA = 0; //constant var jenis kelompok diagnosa utama
    const KD_MASUK = 1; //constant var jenis kelompok diagnosa masuk

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasienmorbiditas_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['morfologineoplasma_id', 'instalasi_id', 'kamarruangan_id', 'jenisketunaan_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'diagnosaicdix_id', 'pegawai_id', 'sebabdiagnosa_id', 'kelompokumur_id', 'diagnosa_id', 'sebabin_id', 'pasien_id', 'jenisin_id', 'kelompokdiagnosa_id', 'golonganumur_id', 'pendaftaran_id', 'penyebabluarcedera_id', 'pasienadmisi_id', 'umur_0_6hr', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'laki_laki', 'perempuan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['morfologineoplasma_id', 'instalasi_id', 'kamarruangan_id', 'jenisketunaan_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'diagnosaicdix_id', 'pegawai_id', 'sebabdiagnosa_id', 'kelompokumur_id', 'sebabin_id', 'pasien_id', 'jenisin_id', 'kelompokdiagnosa_id', 'golonganumur_id', 'pendaftaran_id', 'penyebabluarcedera_id', 'pasienadmisi_id', 'umur_0_6hr', 'umur_1_4thn', 'umur_15_24thn', 'umur_25_44thn', 'umur_45_64thn', 'laki_laki', 'perempuan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jeniskasuspenyakit_id', 'ruangan_id', 'pegawai_id', 'diagnosa_id', 'pasien_id', 'kelompokdiagnosa_id', 'pendaftaran_id', 'tglmorbiditas', 'kasusdiagnosa', 'diagnosa_nama'], 'required'],
            [['tglmorbiditas', 'created_date', 'last_modified_date', 'deleted_date', 'diagnosa_nama', 'diagnosa_kode', 'kelompokdiagnosa_nama'], 'safe'],
            [['infeksinosokomial', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['kasusdiagnosa'], 'string', 'max' => 20],
            [['diagnosa_nama'], 'cekDiagnosa']
        ];
    }

    // override scenarios
    public function scenarios()
    {
        return [
            self::SCENARIO_SESSION => ['kelompokdiagnosa_id','diagnosa_nama', 'required'],
        ];
    }
    public function cekDiagnosa()
    {
        if($this->diagnosa_nama == ""){
            $this->addError('diagnosa_id', 'Diagnosa Tidak Boleh Kosong');
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienmorbiditas_id' => 'Pasienmorbiditas ID',
            'morfologineoplasma_id' => 'Morfologineoplasma ID',
            'instalasi_id' => 'Instalasi ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'jenisketunaan_id' => 'Jenisketunaan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'ruangan_id' => 'Ruangan ID',
            'diagnosaicdix_id' => 'Diagnosaicdix ID',
            'pegawai_id' => 'Pegawai ID',
            'sebabdiagnosa_id' => 'Sebabdiagnosa ID',
            'diagnosa_id' => Yii::t('fe', 'diagnosa_id'),
            'sebabin_id' => 'Sebabin ID',
            'pasien_id' => 'Pasien ID',
            'jenisin_id' => 'Jenisin ID',
            'kelompokdiagnosa_id' => Yii::t('fe', 'kelompokdiagnosa_id'),
            'golonganumur_id' => 'Golonganumur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'penyebabluarcedera_id' => 'Penyebabluarcedera ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tglmorbiditas' => Yii::t('fe', 'Tanggal morbiditas'),
            'kasusdiagnosa' => Yii::t('fe', 'Kasus diagnosa'),
            'umur_0_6hr' => 'Umur 0 28hr',
            'umur_28hr_' => 'Umur 28hr 1thn',
            'umur_1_4thn' => 'Umur 1 4thn',
            'umur_5_14thn' => 'Umur 5 14thn',
            'umur_25_44thn' => 'Umur 25 44thn',
            'umur_45_64thn' => 'Umur 45 64thn',
            'umur_>65thn' => 'Umur 65',
            'infeksinosokomial' => Yii::t('fe', 'Infeksi nosokomial'),
            'laki_laki' => Yii::t('fe', 'Laki laki'),
            'perempuan' => Yii::t('fe', 'Perempuan'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
        ];
    }
}
