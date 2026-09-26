<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompokremunerasi_m".
 *
 * @property integer $kelompokremunerasi_id
 * @property integer $kelompokremunerasi_urutan
 * @property string $kelompokremunerasi_kode
 * @property string $kelompokremunerasi_nama
 * @property string $kelompokremunerasi_desc
 * @property string $kelompokremunerasi_singkatan
 * @property integer $kelompokremunerasi_rate
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
class KelompokRemunerasiForm extends \yii\base\Model
{
    
    public $kelompokremunerasi_id;
    public $kelompokremunerasi_urutan;
    public $kelompokremunerasi_kode;
    public $kelompokremunerasi_nama;
    public $kelompokremunerasi_desc;
    public $kelompokremunerasi_singkatan;
    public $kelompokremunerasi_rate;
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
        return 'kelompokremunerasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokremunerasi_urutan', 'kelompokremunerasi_kode', 'kelompokremunerasi_nama', 'kelompokremunerasi_desc', 'kelompokremunerasi_singkatan', 'kelompokremunerasi_rate'], 'required'],
            [['kelompokremunerasi_urutan', 'kelompokremunerasi_rate', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokremunerasi_kode'], 'string', 'max' => 50],
            [['kelompokremunerasi_nama'], 'string', 'max' => 100],
            [['kelompokremunerasi_desc'], 'string', 'max' => 200],
            [['kelompokremunerasi_singkatan'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokremunerasi_id' => \Yii::t('fe','Kelompok remunerasi'),
            'kelompokremunerasi_urutan' => \Yii::t('fe','Urutan'),
            'kelompokremunerasi_kode' => \Yii::t('fe','Kode'),
            'kelompokremunerasi_nama' => \Yii::t('fe','Nama'),
            'kelompokremunerasi_desc' => \Yii::t('fe','Deskripsi'),
            'kelompokremunerasi_singkatan' => \Yii::t('fe','Singkatan'),
            'kelompokremunerasi_rate' => \Yii::t('fe','Rate'),
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
}
