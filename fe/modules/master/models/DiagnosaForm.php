<?php

namespace app\modules\master\models;

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
 * @property RujukanT[] $rujukanTs
 */
class DiagnosaForm extends \yii\base\Model
{

  public $diagnosa_id;
  public $klasifikasidiagnosa_id;
  public $diagnosa_kode;
  public $diagnosa_nama;
  public $diagnosa_namalainnya;
  public $diagnosa_katakunci;
  public $diagnosa_nourut;
  public $diagnosa_imunisasi;
  public $diagnosa_cat_weight;
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'diagnosa_m';
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
    public function getDiagnosakeperawatanMs()
    {
        return $this->hasMany(DiagnosakeperawatanM::className(), ['diagnosa_id' => 'diagnosa_id']);
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
    public function getRujukanTs()
    {
        return $this->hasMany(RujukanT::className(), ['diagnosa_id' => 'diagnosa_id']);
    }
}
