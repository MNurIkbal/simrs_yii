<?php

/**
*  @author yaya
*/
namespace app\modules\v1\models;

use Yii;

class PemakaianObatDetail extends \Doco\components\DocoActiveRecord
{ 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pemakaianobatdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['satuankecil_id', 'pemakaianobat_id', 'obatalkes_id', 'qty_satuanpakai', 'harga_satuanpakai', 'harganetto_satuanpakai'], 'required'],
            [['qty_satuanpakai', 'harga_satuanpakai', 'harganetto_satuanpakai'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','satuanbesar_id','jumlah_input'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ket_obatpakai'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemakaianobatdetail_id' => 'Pemakaianobatdetail ID',
            'satuankecil_id' => 'Satuankecil ID',
            'pemakaianobat_id' => 'Pemakaianobat ID',
            'obatalkes_id' => 'Obatalkes ID',
            'qty_satuanpakai' => 'Qty Satuanpakai',
            'harga_satuanpakai' => 'Harga Satuanpakai',
            'harganetto_satuanpakai' => 'Harganetto Satuanpakai',
            'ket_obatpakai' => 'Ket Obatpakai',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}