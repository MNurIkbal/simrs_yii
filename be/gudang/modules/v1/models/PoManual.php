<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pomanual_t".
 *
 * @property int $pomanual_id
 * @property string $no_pomanual
 * @property string $tlg_pomanual
 * @property int $instalasi_id instalasi_id berdasarkan ruangan yg di pilih
 * @property int $ruangan_id ruangan : gudang umum, gudang farmasi
 * @property int $supplier_id
 * @property int $diorder_oleh pegawai_id (berdasarkan login)
 * @property string $tgl_rencanaterima
 * @property int $payterm_id payterm_m.payterm_id
 * @property int $pajak_id pajam_m.pajak_id
 * @property int $peg_mengetahui_id
 * @property int $peg_menyetujui_id
 * @property string $catatan1
 * @property string $catatan2
 * @property double $sub_total
 * @property double $total_discount
 * @property int $ppn_persen
 * @property double $ppn_nilai
 * @property double $total
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
class PoManual extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'pomanual_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pomanual', 'instalasi_id', 'ruangan_id', 'supplier_id', 'diorder_oleh', 'payterm_id', 'pajak_id'], 'required'],
            [['tgl_pomanual', 'tgl_rencanaterima', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['instalasi_id', 'ruangan_id', 'supplier_id', 'diorder_oleh', 'payterm_id', 'pajak_id', 'peg_mengetahui_id', 'peg_menyetujui_id', 'ppn_persen', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_id', 'supplier_id', 'diorder_oleh', 'payterm_id', 'pajak_id', 'peg_mengetahui_id', 'peg_menyetujui_id', 'ppn_persen', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatan1', 'catatan2', 'additional_data'], 'string'],
            // [['sub_total', 'total_discount', 'ppn_nilai', 'total'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pomanual'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pomanual_id' => 'Pomanual ID',
            'no_pomanual' => 'No Pomanual',
            'tgl_pomanual' => 'Tlg Pomanual',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'supplier_id' => 'Supplier ID',
            'diorder_oleh' => 'Diorder Oleh',
            'tgl_rencanaterima' => 'Tgl Rencanaterima',
            'payterm_id' => 'Payterm ID',
            'pajak_id' => 'Pajak ID',
            'peg_mengetahui_id' => 'Peg Mengetahui ID',
            'peg_menyetujui_id' => 'Peg Menyetujui ID',
            'catatan1' => 'Catatan1',
            'catatan2' => 'Catatan2',
            'sub_total' => 'Sub Total',
            'total_discount' => 'Total Discount',
            'ppn_persen' => 'Ppn Persen',
            'ppn_nilai' => 'Ppn Nilai',
            'total' => 'Total',
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
