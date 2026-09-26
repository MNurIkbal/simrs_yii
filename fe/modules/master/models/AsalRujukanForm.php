<?php
// Author : Naufal Ziyad L
namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "asalrujukan_m".
 *
 * @property integer $asalrujukan_id
 * @property string $asalrujukan_nama
 * @property string $asalrujukan_institusi
 * @property string $asalrujukan_namalainnya
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
 * @property PerujukM[] $perujukMs
 * @property RujukanT[] $rujukanTs
 * @property RujukandariM[] $rujukandariMs
 * @property RujukankeluarM[] $rujukankeluarMs
 */
class AsalRujukanForm extends \yii\base\Model
{

    public $asalrujukan;
    public $asalrujukan_id;
    public $asalrujukan_nama;
    public $asalrujukan_kode;
    public $asalrujukan_namalainnya;
    public $asalrujukan_institusi;
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
            [['asalrujukan_nama'], 'required'],
            [['asalrujukan_kode'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['asalrujukan_nama', 'asalrujukan_institusi', 'asalrujukan_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asalrujukan_id' => Yii::t('fe', 'asalrujukan_id'),
            'asalrujukan_nama' => Yii::t('fe', 'Asal Rujukan'),
            'asalrujukan_institusi' => Yii::t('fe', 'Asal Rujukan Institusi'),
            'asalrujukan_namalainnya' => Yii::t('fe', 'Asal Rujukan Nama Lainnya'),
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
