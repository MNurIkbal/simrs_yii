<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sync_paymentmethod".
 *
 * @property string $type
 * @property string $kode
 * @property string $name
 * @property string $number
 * @property string $bank_name
 * @property int $bank_phone
 * @property string $bank_address
 * @property string $notes
 * @property string $date
 * @property string $account_bank_name
 * @property string $date
 * @property int $deleted
 */
class AkuntingPayMethod extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_paymentmethod';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
  
            [['type', 'kode', 'name', 'number', 'bank_name'], 'string', 'max' => 255],
            [['date'], 'safe'],
            [['bank_phone', 'deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('app', 'Kode Supplier'),
            'kode' => Yii::t('app', 'Nama Supplier'),
            'name' => Yii::t('app', 'Alamat Supplier'),
            'number' => Yii::t('app', 'No Telepon'),
            'bank_name' => Yii::t('app', 'Alamat Email'),
            'bank_phone' => Yii::t('app', 'NPWP'),
            'bank_address' => Yii::t('app', 'Alamat Situs'),
            'notes' => Yii::t('app', 'Nomor Rekening'),
            'date' => Yii::t('app', 'Rekening Atas Nama'),
            'account_bank_name' => Yii::t('app', 'Nama Bank'),
            'date' => Yii::t('app', 'Tanggal Sync'),
        ];
    }
    
}
