<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "golonganpegawai_m".
 *
 * @property integer $golonganpegawai_id
 * @property string $golonganpegawai_nama
 * @property string $golonganpegawai_namalainnya
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
 */
class GolonganOperasiForm extends \yii\base\Model
{
    
    public $golonganoperasi_id;
    public $golonganoperasi_kode;
    public $golonganoperasi_nama;
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
        return 'golonganoperasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['golonganoperasi_kode', 'golonganoperasi_nama'], 'required'],
            [['golonganoperasi_kode'], 'checkUnique'],
            [['golonganoperasi_nama'], 'checkName'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['golonganoperasi_kode', 'golonganoperasi_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'golonganoperasi_id' => \Yii::t('fe','Golongan Operasi ID'),
            'golonganoperasi_kode' => \Yii::t('fe','Kode Golongan Operasi'),
            'golonganoperasi_nama' => \Yii::t('fe','Nama Golongan Operasi'),
            'additional_data' => \Yii::t('fe','Additional data'),
            'created_date' => \Yii::t('fe','Created date'),
            'created_by' => \Yii::t('fe','Created by'),
            'modified_count' => \Yii::t('fe','Modified count'),
            'last_modified_date' => \Yii::t('fe','Last modified date'),
            'last_modified_by' => \Yii::t('fe','Last modified by'),
            'is_deleted' => \Yii::t('fe','Is deleted'),
            'is_active' => \Yii::t('fe','Status'),
            'deleted_date' => \Yii::t('fe','Deleted date'),
            'deleted_by' => \Yii::t('fe','Deleted by'),
        ];
    }

    public function checkUnique() 
    {
        $golonganoperasi_kode = $this->golonganoperasi_kode;
        if (strpos(substr($golonganoperasi_kode, 0, 1), ' ') !== FALSE) {
            $this->addError('golonganoperasi_kode', 'Kode golongan operasi mengandung spasi di awal kata');
            return false;
        }
        return true;
    }

    public function checkName() 
    {
        $golonganoperasi_nama = $this->golonganoperasi_nama;
        if (strpos(substr($golonganoperasi_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('golonganoperasi_nama', 'Nama golongan operasi mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}
