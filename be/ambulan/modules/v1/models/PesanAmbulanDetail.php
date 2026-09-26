<?php

namespace app\modules\v1\models;

use Yii;

class PesanAmbulanDetail extends \Doco\components\DocoActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pesanambulandetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesanambulan_id'], 'required'],
            [['pesanambulan_id', 'daftartindakan_id', 'qty_tindakan', 'obatalkes_id', 'qty_obat', 'satuankonversi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesanambulan_id', 'daftartindakan_id', 'qty_tindakan', 'obatalkes_id', 'qty_obat', 'satuankonversi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_default', 'is_deleted', 'is_active'], 'boolean'],
            [['tarif_satuan', 'jumlah_tarif'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesanambulandetail_id' => 'Pesanambulandetail ID',
            'pesanambulan_id' => 'Pesanambulan ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'is_default' => 'Is Default',
            'qty_tindakan' => 'Qty Tindakan',
            'tarif_satuan' => 'Tarif Satuan',
            'jumlah_tarif' => 'Jumlah Tarif',
            'obatalkes_id' => 'Obatalkes ID',
            'qty_obat' => 'Qty Obat',
            'satuankonversi_id' => 'Satuankonversi ID',
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
