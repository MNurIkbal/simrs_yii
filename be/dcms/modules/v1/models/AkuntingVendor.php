<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sync_supplier".
 *
 * @property string $supplier_kode
 * @property string $supplier_nama
 * @property string $supplier_alamat
 * @property string $no_tlp
 * @property string $email
 * @property int $tax_number
 * @property string $website
 * @property string $account_bank_no
 * @property string $account_bank_holder
 * @property string $account_bank_name
 * @property string $date
 * @property int $deleted
 */
class AkuntingVendor extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['supplier_kode', 'supplier_nama', 'supplier_alamat', 'no_tlp', 'email', 'website', 'account_bank_no', 'account_bank_holder', 'account_bank_name'], 'string', 'max' => 255],
            [['date'], 'safe'],
            [['tax_number', 'deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'supplier_kode' => Yii::t('app', 'Kode Supplier'),
            'supplier_nama' => Yii::t('app', 'Nama Supplier'),
            'supplier_alamat' => Yii::t('app', 'Alamat Supplier'),
            'no_tlp' => Yii::t('app', 'No Telepon'),
            'email' => Yii::t('app', 'Alamat Email'),
            'tax_number' => Yii::t('app', 'NPWP'),
            'website' => Yii::t('app', 'Alamat Situs'),
            'account_bank_no' => Yii::t('app', 'Nomor Rekening'),
            'account_bank_holder' => Yii::t('app', 'Rekening Atas Nama'),
            'account_bank_name' => Yii::t('app', 'Nama Bank'),
            'date' => Yii::t('app', 'Tanggal Sync'),
        ];
    }
    
}
