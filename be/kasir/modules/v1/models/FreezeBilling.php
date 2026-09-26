<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Penjamin;

/**
 * This is the model class for table "int_freezebill_r".
 *
 * @property string $invoice_id
 * @property string $invoice_date
 * @property string $invoice_due_date
 * @property string $invoice_number
 * @property integer $invoice_total
 * @property integer $payer_id
 * @property string $payer_code
 * @property string $payer_name
 * @property string $payer_sync_id_api
 * @property integer $create_uid
 * @property string $create_by
 * @property string $create_date
 * @property integer $write_uid
 * @property string $write_by
 * @property string $write_date
 * @property string $status
 * @property string $additional_detail
 * @property string $pendaftaran_id
 * @property integer $status
 */
class FreezeBilling extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'int_freezebill_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id'], 'required'],
            [['payer_id', 'create_uid', 'write_uid', 'status'], 'integer'],
            [['invoice_date', 'invoice_due_date', 'invoice_number', 'payer_code', 'payer_name', 'payer_sync_id_api', 'create_by','create_date', 'write_by', 'write_date', 'additional_detail','invoice_total'], 'safe'],
            [['pendaftaran_id'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoice_id' => Yii::t('app', 'Invoce id'),   
            'invoice_date' => Yii::t('app', 'invoice_date'), 
            'invoie_due_date' => Yii::t('app', 'invoie_due_date'), 
            'invoice_number' => Yii::t('app', 'invoice_number'), 
            'invoice_total' => Yii::t('app', 'invoice_total'), 
            'payer_id' => Yii::t('app', 'payer_id'), 
            'payer_code' => Yii::t('app', 'payer_code'), 
            'payer_name' => Yii::t('app', 'payer_name'), 
            'payer_sync_id_api' => Yii::t('app', 'payer_sync_id_api'),
            'create_uid' => Yii::t('app', 'create_uid'), 
            'create_by' => Yii::t('app', 'create_by'), 
            'create_date'=> Yii::t('app', 'create_date'), 
            'write_uid' => Yii::t('app', 'write_uid'), 
            'write_by' => Yii::t('app', 'write_by'), 
            'write_date' => Yii::t('app', 'write_date'), 
            'additional_detail' => Yii::t('app', 'Details'), 
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'), 
            'status' => Yii::t('app', 'Status'), 
        ];
    }
}
