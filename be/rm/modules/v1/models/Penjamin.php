<?php

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
            'additional_data' => \Yii::t('app','Additional data'),
            'created_date' => \Yii::t('app','Created date'),
            'created_by' => \Yii::t('app','Created by'),
            'modified_count' => \Yii::t('app','Modified count'),
            'last_modified_date' => \Yii::t('app','Last modified date'),
            'last_modified_by' => \Yii::t('app','Last modified by'),
            'is_deleted' => \Yii::t('app','Is deleted'),
            'is_active' => \Yii::t('app','Status'),
            'deleted_date' => \Yii::t('app','Deleted date'),
            'deleted_by' => \Yii::t('app','Deleted by'),
        ];
    }
}
