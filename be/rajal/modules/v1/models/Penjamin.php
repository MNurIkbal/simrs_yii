<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 11:38:54
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-15 11:39:11
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penjamin_m".
 *
 * @property integer $penjamin_id
 * @property integer $carabayar_id
 * @property string $penjamin_nama
 * @property string $penjamin_namalainnya
 * @property string $alamat_penjamin
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
class Penjamin extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjamin_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['penjamin_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['penjamin_nama'], 'string', 'max' => 50],
            [['penjamin_namalainnya'], 'string', 'max' => 70],
            [['alamat_penjamin'], 'string', 'max' => 200],
            [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaraBayar::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'penjamin_nama' => Yii::t('app', 'Nama penjamin'),
            'penjamin_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'alamat_penjamin' => Yii::t('app', 'Alamat penjamin'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
    
    public function getCaraBayar()
    {
        return $this->hasOne(CaraBayar::className(), ['carabayar_id' => 'carabayar_id']);
    }
    
    public function extraFields()
    {
        return ['carabayar_m' => function($item){
            return $item->caraBayar;
        }];
    }
}
