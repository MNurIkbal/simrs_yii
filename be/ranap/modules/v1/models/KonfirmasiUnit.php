<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Instalasi;

class KonfirmasiUnit extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'konfirmasiunit_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'instalasi_id', 'is_konfirmasi'], 'required'],
            [['pendaftaran_id', 'instalasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_konfirmasi', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'instalasi_id' => Yii::t('app', 'Instalasi ID'),
            'is_konfirmasi' => Yii::t('app', 'Is Konfirmasi'),
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


    public function getInstalasi()
    {
        return $this->hasMany(Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }
}
