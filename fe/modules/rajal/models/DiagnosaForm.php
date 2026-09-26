<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 14:02:35
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:13:50
 * @Description: 
 */
namespace app\modules\rajal\models;

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
            'diagnosa_id' => Yii::t('fe', 'diagnosa_id'),
            'klasifikasidiagnosa_id' => Yii::t('fe', 'klasifikasidiagnosa_id'),
            'diagnosa_kode' => Yii::t('fe', 'diagnosa_kode'),
            'diagnosa_nama' => Yii::t('fe', 'diagnosa_nama'),
            'diagnosa_namalainnya' => Yii::t('fe', 'Nama diagnosa_namalainnya'),
            'diagnosa_katakunci' => Yii::t('fe', 'diagnosa_katakunci'),
            'diagnosa_nourut' => Yii::t('fe', 'diagnosa_nourut'),
            'diagnosa_imunisasi' => Yii::t('fe', 'diagnosa_imunisasi'),
            'diagnosa_cat_weight' => Yii::t('fe', 'diagnosa_cat_weight'),
            'additional_data' => \Yii::t('fe','additional_data'),
            'created_date' => \Yii::t('fe','created_date'),
            'created_by' => \Yii::t('fe','created_by'),
            'modified_count' => \Yii::t('fe','modified_count'),
            'last_modified_date' => \Yii::t('fe','last_modified_date'),
            'last_modified_by' => \Yii::t('fe','last_modified_by'),
            'is_deleted' => \Yii::t('fe','is_deleted'),
            'is_active' => \Yii::t('fe','is_active'),
            'deleted_date' => \Yii::t('fe','deleted_date'),
            'deleted_by' => \Yii::t('fe','deleted_by'),
        ];
    }
}