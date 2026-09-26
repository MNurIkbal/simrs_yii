<?php

namespace app\modules\v1\models;

use Yii;

class InvoiceGabungDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoicegabungdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoicegabungdetail_id', 'invoicegabung_id', 'pendaftaran_id', 'pembayaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['invoicegabung_id', 'pendaftaran_id', 'pembayaran_id', 'tgl_invoice', 'no_pembayaran', 'total_invoice'], 'required'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoicegabungdetail_id' => Yii::t('app', 'Invoice Gabung Detail ID'),
            'invoicegabung_id' => Yii::t('app', 'Invoice Gabung ID'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'pembayaran_id' => Yii::t('app', 'Pembayaran ID'),
            'no_pembayaran' => Yii::t('app', 'Nomor Pembayaran'),
            'tgl_invoice' => Yii::t('app', 'Tanggal Invoice'),
            'total_invoice' => Yii::t('app', 'Total Invoice'),
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
