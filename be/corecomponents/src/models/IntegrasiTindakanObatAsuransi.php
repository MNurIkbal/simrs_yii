<?php

namespace Doco\models;

use Yii;

class IntegrasiTindakanObatAsuransi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'integrasi_tindakanobat_asuransi_t';
    }

    public function rules()
    {
        return [
            [['pendaftaran_id', 'asuransi_id', 'pelayanan_id'], 'integer'],
            [['is_sending'], 'boolean'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['is_deleted','is_active','last_modified_date', 'deleted_date', 'type', 'item_code', 'qty', 'subtotal'], 'safe'],
        ];
    }
}
