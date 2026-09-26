<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 17:23:36
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:25:01
 * @Description: 
 */
namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitruangan_mp".
 *
 * @property integer $ruangan_id
 * @property integer $jeniskasuspenyakit_id
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
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property RuanganM $ruangan
 */
class KasusPenyakitRuanganForm extends \yii\base\Model
{
    public $ruangan_id;
    public $jeniskasuspenyakit_id;
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
        return 'kasuspenyakitruangan_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'required'],
            [['ruangan_id', 'jeniskasuspenyakit_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
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
            'ruangan_id' => Yii::t('fe', 'Nama ruangan'),
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Nama jenis kasus penyakit'),
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
