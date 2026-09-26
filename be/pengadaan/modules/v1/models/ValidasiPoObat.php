<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "validasipoobat_t".
 *
 * @property int $validasipoobat_id
 * @property string $tgl_validasai
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
 * @property bool $is_consigment
 */
class ValidasiPoObat extends \Doco\components\DocoActiveRecord
{
    const SCENARIO_VALIDASI_PO = 'validasi_po';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'validasipoobat_t';
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
            [['ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['validasipoobat_id', 'ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_validasi', 'created_date', 'last_modified_date', 'deleted_date','catatan1','catatan2','is_manual','tgl_rencanaterima','payterm_id','pajak_id','peg_validasi_id','peg_mengetahui_id','peg_menyetujui_id','no_poobat','is_validasi','status_penerimaan','diorder_oleh','supplier_id', 'is_consigment', 'tgl_tercetak', 'is_cito', 'is_admin'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_consigment', 'is_cito', 'is_admin'], 'boolean'],
            // [['validasipoobat_id'], 'unique'],
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
            'is_consigment' => 'Consigment',
        ];
    }
}
