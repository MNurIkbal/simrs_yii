<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "validasipobarang_t".
 *
 * @property int $validasipobarang_id
 * @property string $tgl_validasi
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $peg_validasi_id
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
    const SCENARIO_VALIDASI_PO = 'validasi_po';
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'validasipobarang_t';
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'pegawai_id'], 'required'],
            [['tgl_validasi'], 'required', 'on' => self::SCENARIO_VALIDASI_PO],
            [['tgl_validasi', 'created_date', 'last_modified_date', 'deleted_date','catatan1','catatan2','is_manual','no_pomanual','tgl_rencanaterima','is_validasi','status_penerimaan','diorder_oleh','tgl_rencanaterima','payterm_id','pajak_id','peg_validasi_id','peg_mengetahui_id','peg_menyetujui_id','sub_total','total_discount','ppn_persen','ppn_nilai','total','no_pobarang','supplier_id', 'tgl_tercetak', 'is_cito', 'is_admin'], 'safe'],
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pegawai_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_cito', 'is_admin'], 'boolean'],
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
            'peg_validasi_id' => 'Pegawai Validasi',
            'supplier_id' => 'Supplier',
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
