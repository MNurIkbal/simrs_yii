<?php
// Author : Naufal Ziyad L
namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "golonganumur_m".
 *
 * @property integer $golonganumur_id
 * @property string $golonganumur_nama
 * @property string $golonganumur_namalainnya
 * @property string $golonganumur_minimal
 * @property string $golonganumur_maksimal
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
 * @property PendaftaranT[] $pendaftaranTs
 */
class GolonganUmurForm extends \yii\base\Model
{

 public $golonganumur_id;
 public $golonganumur_nama;
 public $golonganumur_namalainnya;
 public $golonganumur_minimal;
 public $golonganumur_maksimal;
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
    public function rules()
    {
       return [
            [['golonganumur_nama'], 'required'],
            [['golonganumur_minimal', 'golonganumur_maksimal'], 'number'],
            [['additional_data'], 'string'],
            [['is_active', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['golonganumur_nama', 'golonganumur_namalainnya'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'golonganumur_id' => Yii::t('fe','Golonganumur ID'),
            'golonganumur_nama' => Yii::t('fe','Usia'),
            'golonganumur_namalainnya' => Yii::t('fe','Nama Lainnya'),
            'golonganumur_minimal' => Yii::t('fe','Golonganumur Minimal'),
            'golonganumur_maksimal' => Yii::t('fe','Golonganumur Maksimal'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }

}
