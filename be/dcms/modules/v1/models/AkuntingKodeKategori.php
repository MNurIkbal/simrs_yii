<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sync_category".
 *
 * @property string $code
 * @property string $type
 * @property string $name
 * @property string $date
 * @property string $deleted
 */
class AkuntingKodeKategori extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
  
            [['type', 'code', 'name'], 'string', 'max' => 255],
            [['date'], 'safe'],
            [['deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('app', 'Tipe'),
            'code' => Yii::t('app', 'Kode Kategori'),
            'name' => Yii::t('app', 'Nama Kategori'),
            'date' => Yii::t('app', 'Tanggal Sync'),
            'deleted' => Yii::t('app', 'Status'),
        ];
    }
    
}
