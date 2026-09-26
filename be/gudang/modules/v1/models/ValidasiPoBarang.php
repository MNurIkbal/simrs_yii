<?php

namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "validasipobarang_t".
 *
 * @property int $validasipobarang_id
 * @property string $tgl_validasi
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $supplier_id supplier_m
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
class ValidasiPoBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'validasipobarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_validasi', 'ruangan_id', 'pegawai_id', 'supplier_id'], 'required'],
            [['tgl_validasi', 'created_date', 'last_modified_date', 'deleted_date','is_verifikasi', 'no_pobarang'], 'safe'],
            [['ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active','is_verifikasi'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'validasipobarang_id' => 'Validasipobarang ID',
            'tgl_validasi' => 'Tgl Validasi',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'supplier_id' => 'Supplier ID',
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

    public function getDetails()
    {
        return $this->hasMany(ValidasiPoBarangDetail::className(), ['validasipobarang_id' => 'validasipobarang_id']);
    }

    public static function checkAndSetStatus($validasiPoBarangId)
    {
        $parent = self::find()
            ->where(['validasipobarang_id' => $validasiPoBarangId])
            ->with('details')
            ->one();
        if(!$parent) return null;
        $details = ArrayHelper::getValue($parent, 'details');
        $totalQtyInput = 0;
        $totalQtyPenerimaanWithRetur = 0;
        foreach ($details as $key => $value) {
            $qtyInput = ArrayHelper::getValue($value, 'qty_input');
            $qtyPenerimaan = ArrayHelper::getValue($value, 'qty_penerimaan');
            $qtyRetur = ArrayHelper::getValue($value, 'qty_retur');
            $qtyPenerimaanWithRetur = $qtyPenerimaan - $qtyRetur;
            $totalQtyInput += $qtyInput;
            $totalQtyPenerimaanWithRetur += $qtyPenerimaanWithRetur;
        }
        $status = ArrayHelper::getValue($parent, 'status_penerimaan');
        $isEmpty = $totalQtyPenerimaanWithRetur <= 0;
        $isHalf = ($totalQtyPenerimaanWithRetur > 0) && ($totalQtyPenerimaanWithRetur < $totalQtyInput);
        $isFull = ($totalQtyPenerimaanWithRetur >= $totalQtyInput);
        if($isEmpty) {
            $status = DocoConstants::PO_BELUM_DITERIMA;
        } else if($isHalf) {
            $status = DocoConstants::BELUM_SELESAI_PO;
        } else if ($isFull) {
            $status = DocoConstants::SUDAH_DITERIMA_PO;
        }
        $parent->status_penerimaan = $status;
        $parent->save(false);
        return $parent;
    }
}
