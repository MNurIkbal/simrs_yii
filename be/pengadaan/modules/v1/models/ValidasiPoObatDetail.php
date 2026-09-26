<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "validasipoobatdetail_t".
 *
 * @property int $validasipoobatdetail_id
 * @property int $validasipoobat_id
 * @property int $rekomendasiobatdetail_id
 * @property int $obatalkes_id
 * @property int $nilai_ro
 * @property int $qty_tersedia
 * @property int $ro_stok
 * @property int $rekomendasi
 * @property int $qty_po
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class ValidasiPoObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'validasipoobatdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'qty_po'], 'required'],
            [['validasipoobatdetail_id', 'validasipoobat_id', 'rekomendasiobatdetail_id', 'obatalkes_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'qty_po', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['validasipoobatdetail_id', 'validasipoobat_id', 'rekomendasiobatdetail_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'rekomendasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'qty_input',
                's_konversiobt_id',
                'harga',
                'discount',
                'discount_rp',
                'jumlah',
                'qty_penerimaan',
                'is_completed',
                'additional_data',
                'purchasereqdetail_id',
                'status',
                'is_disc_nominal'
            ], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['validasipoobatdetail_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'validasipoobatdetail_id' => 'Validasipoobatdetail ID',
            'validasipoobat_id' => 'Validasipoobat ID',
            'rekomendasiobatdetail_id' => 'Rekomendasiobatdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'nilai_ro' => 'Nilai Ro',
            'qty_tersedia' => 'Qty Tersedia',
            'ro_stok' => 'Ro Stok',
            'rekomendasi' => 'Rekomendasi',
            'qty_po' => 'Qty Po',
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

    public function getPurchaseRequisitionDetail()
    {
        return $this->hasOne(PurchaseRequisitionDetail::className(),['purchasereqdetail_id'=>'purchasereqdetail_id']);
    }
}
