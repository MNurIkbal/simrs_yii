<?php

namespace app\modules\v1\models;

use Yii;

class InvoiceGabung extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoicegabung_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_invoicegabung', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'penjamin_id_cetak', 'pendaftaran_id_cetak'], 'integer'],
            [['tgl_invoicegabung', 'penjamin_id_cetak', 'pendaftaran_id_cetak'], 'required'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tgl_invoicegabung_cetak'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoicegabung_id' => Yii::t('app', 'Invoice Gabung ID'),
            'tgl_invoicegabung' => Yii::t('app', 'Tanggal Invoice'),
            'no_invoicegabung' => Yii::t('app', 'Nomor Invoice'),
            'total_invoicegabung' => Yii::t('app', 'Total Invoice'),
            'status_invoicegabung' => Yii::t('app', 'Status'),
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
