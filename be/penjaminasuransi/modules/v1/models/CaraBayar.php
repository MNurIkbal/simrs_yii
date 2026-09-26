<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carabayar_m".
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
class CaraBayar extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carabayar_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_nama'], 'required'],
            [['carabayar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_subsidiasuransi', 'is_subsidipemerintah', 'is_subsidirs', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['carabayar_nama', 'carabayar_namalainnya', 'metode_pembayaran', 'carabayar_loket'], 'string', 'max' => 50],
            [['carabayar_singkatan'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carabayar_id' => Yii::t('app', 'Cara bayar'),
            'carabayar_nama' => Yii::t('app', 'Nama cara bayar'),
            'carabayar_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'metode_pembayaran' => Yii::t('app', 'Metode pembayaran'),
            'carabayar_loket' => Yii::t('app', 'Cara bayar loket'),
            'carabayar_singkatan' => Yii::t('app', 'Singkatan'),
            'carabayar_urutan' => Yii::t('app', 'Urutan'),
            'is_subsidiasuransi' => Yii::t('app', 'Is subsidi asuransi'),
            'is_subsidipemerintah' => Yii::t('app', 'Is subsidi pemerintah'),
            'is_subsidirs' => Yii::t('app', 'Is subsidi rs'),
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
