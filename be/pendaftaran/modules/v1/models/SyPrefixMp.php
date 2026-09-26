<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sy_prefix_mp".
 *
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property string $carabayar_namalainnya
 * @property string $metode_pembayaran
 * @property string $carabayar_loket
 * @property string $carabayar_singkatan
 * @property integer $carabayar_urutan
 * @property boolean $is_subsidiasuransi
 * @property boolean $is_subsidipemerintah
 * @property boolean $is_subsidirs
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
class SyPrefixMp extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sy_prefix_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'penomoran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data', 'nama_prefix'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'penomoran_id' => Yii::t('app', 'Penomoran'),
            'nama_prefix' => Yii::t('app', 'Prefix'),
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
}
