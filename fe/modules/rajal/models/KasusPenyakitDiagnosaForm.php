<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 13:53:15
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:24:55
 * @Description: 
 */
namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitdiagnosa_mp".
 *
 * @property int $jeniskasuspenyakit_id
 * @property int $diagnosa_id
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
 * @property DiagnosaM $diagnosa
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 */
class KasusPenyakitDiagnosaForm extends \yii\base\Model
{
    public $jeniskasuspenyakit_id;
    public $diagnosa_id;
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
        return 'kasuspenyakitdiagnosa_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'diagnosa_id'], 'required'],
            [['jeniskasuspenyakit_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jeniskasuspenyakit_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Nama jenis kasus penyakit'),
            'diagnosa_id' => Yii::t('fe', 'Nama diagnosa'),
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