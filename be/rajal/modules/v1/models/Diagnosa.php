<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 13:59:39
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-09 14:06:22
 * @Description: 
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "diagnosa_m".
 *
 * @property int $diagnosa_id
 * @property int $klasifikasidiagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property string $diagnosa_katakunci
 * @property int $diagnosa_nourut
 * @property bool $diagnosa_imunisasi
 * @property string $diagnosa_cat_weight
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
 *
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property DiagnosakeperawatanM[] $diagnosakeperawatanMs
 * @property JadwalimunisasiM[] $jadwalimunisasiMs
 * @property KasuspenyakitdiagnosaMp[] $kasuspenyakitdiagnosaMps
 * @property JeniskasuspenyakitM[] $jeniskasuspenyakits
 * @property RujukanT[] $rujukanTs
 */
class Diagnosa extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'diagnosa_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['klasifikasidiagnosa_id', 'diagnosa_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['klasifikasidiagnosa_id', 'diagnosa_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['diagnosa_kode', 'diagnosa_nama'], 'required'],
            [['diagnosa_imunisasi', 'is_deleted', 'is_active'], 'boolean'],
            [['diagnosa_cat_weight'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['diagnosa_kode'], 'string', 'max' => 10],
            [['diagnosa_nama', 'diagnosa_namalainnya'], 'string', 'max' => 200],
            [['diagnosa_katakunci'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'diagnosa_id' => 'Diagnosa ID',
            'klasifikasidiagnosa_id' => 'Klasifikasidiagnosa ID',
            'diagnosa_kode' => 'Diagnosa Kode',
            'diagnosa_nama' => 'Diagnosa Nama',
            'diagnosa_namalainnya' => 'Diagnosa Namalainnya',
            'diagnosa_katakunci' => 'Diagnosa Katakunci',
            'diagnosa_nourut' => 'Diagnosa Nourut',
            'diagnosa_imunisasi' => 'Diagnosa Imunisasi',
            'diagnosa_cat_weight' => 'Diagnosa Cat Weight',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuhankeperawatanTs()
    {
        return $this->hasMany(AsuhanKeperawatan::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosakeperawatanMs()
    {
        return $this->hasMany(DiagnosaKeperawatan::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalimunisasiMs()
    {
        return $this->hasMany(JadwalImunisasi::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKasuspenyakitdiagnosaMps()
    {
        return $this->hasMany(KasusPenyakitDiagnosa::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakits()
    {
        return $this->hasMany(JenisKasusPenyakit::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id'])->viaTable('kasuspenyakitdiagnosa_mp', ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukanTs()
    {
        return $this->hasMany(Rujukan::className(), ['diagnosa_id' => 'diagnosa_id']);
    }
}
