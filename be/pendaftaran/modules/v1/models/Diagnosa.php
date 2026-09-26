<?php

namespace app\modules\v1\models;

use Yii;


/**
 * This is the model class for table "diagnosa_m".
 *
 * @property integer $diagnosa_id
 * @property integer $klasifikasidiagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $diagnosa_namalainnya
 * @property string $diagnosa_katakunci
 * @property integer $diagnosa_nourut
 * @property boolean $diagnosa_imunisasi
 * @property string $diagnosa_cat_weight
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 *
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property KlasifikasidiagnosaM $klasifikasidiagnosa
 * @property DiagnosakeperawatanM[] $diagnosakeperawatanMs
 * @property DiagnosaobatMp[] $diagnosaobatMps
 * @property ObatalkesM[] $obatalkes
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
            [['klasifikasidiagnosa_id', 'diagnosa_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['diagnosa_kode', 'diagnosa_nama'], 'required'],
            [['diagnosa_imunisasi', 'is_deleted', 'is_active'], 'boolean'],
            [['diagnosa_cat_weight'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['diagnosa_kode'], 'string', 'max' => 10],
            [['diagnosa_nama', 'diagnosa_namalainnya'], 'string', 'max' => 200],
            [['diagnosa_katakunci'], 'string', 'max' => 100],
            [['klasifikasidiagnosa_id'], 'exist', 'skipOnError' => true, 'targetClass' => KlasifikasidiagnosaM::className(), 'targetAttribute' => ['klasifikasidiagnosa_id' => 'klasifikasidiagnosa_id']],
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
        return $this->hasMany(AsuhankeperawatanT::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKlasifikasidiagnosa()
    {
        return $this->hasOne(KlasifikasidiagnosaM::className(), ['klasifikasidiagnosa_id' => 'klasifikasidiagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosakeperawatanMs()
    {
        return $this->hasMany(DiagnosakeperawatanM::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosaobatMps()
    {
        return $this->hasMany(DiagnosaobatMp::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkes()
    {
        return $this->hasMany(ObatalkesM::className(), ['obatalkes_id' => 'obatalkes_id'])->viaTable('diagnosaobat_mp', ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJadwalimunisasiMs()
    {
        return $this->hasMany(JadwalimunisasiM::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKasuspenyakitdiagnosaMps()
    {
        return $this->hasMany(KasuspenyakitdiagnosaMp::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJeniskasuspenyakits()
    {
        return $this->hasMany(JeniskasuspenyakitM::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id'])->viaTable('kasuspenyakitdiagnosa_mp', ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukanTs()
    {
        return $this->hasMany(RujukanT::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    public function getDiagnosa() {
        $sql = "
        SELECT 
            diagnosa_id, 
            CONCAT(diagnosa_kode, ' - ', diagnosa_nama) as diagnosa_nama 
        FROM diagnosa_m
        WHERE is_deleted = false AND is_active = true
        ORDER BY diagnosa_nourut
        ";
        $list = Yii::$app->db->createCommand($sql)->queryAll();
        return $list;
    }
}
