<?php

namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "validasipoobat_t".
 *
 * @property int $validasipoobat_id
 * @property string $tgl_validasai
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
class ValidasiPoObat extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'validasipoobat_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_validasi', 'ruangan_id', 'pegawai_id'], 'required'],
            [['ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['validasipoobat_id', 'ruangan_id', 'pegawai_id', 'peg_penerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_validasi', 'created_date', 'last_modified_date', 'deleted_date','is_verifikasi','no_poobat'], 'safe'],
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
            'validasipoobat_id' => 'Validasipoobat ID',
            'tgl_validasai' => 'Tgl Validasai',
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
            'peg_penerima_id' => 'Pegawai Penerima ID',
        ];
    }

    public function getDetails()
    {
        return $this->hasMany(ValidasiPoObatDetail::className(), ['validasipoobat_id' => 'validasipoobat_id']);
    }

    public static function checkAndSetStatus($validasiPoObatId)
    {
        $parent = self::find()
            ->where(['validasipoobat_id' => $validasiPoObatId])
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
