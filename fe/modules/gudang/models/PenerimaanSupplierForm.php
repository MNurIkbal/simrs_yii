<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "penerimaansupp_t".
 *
 * @property int $penerimaansupp_id
 * @property string $no_penerimaan
 * @property string $tgl_penerimaan
 * @property int $supplier_id
 * @property string $no_faktur
 * @property string $no_suratjalan
 * @property int $peg_mengetahui
 * @property int $peg_menyetujui
 * @property int $ruanganpenerima_id
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
 * @property bool $is_donasi
 * @property int $sumber_penerimaan
 */
class PenerimaanSupplierForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    const SCENARIO_GUDANG_FARMASI = 'gudang-farmasi';
    const SCENARIO_GUDANG_UMUM = 'gudang-umum';

    public $satuankonversi_id;

    public $penerimaansupp_id;
    public $no_penerimaan;
    public $tgl_penerimaan;
    public $supplier_id;
    public $no_faktur;
    public $no_suratjalan;
    public $is_tipe;
    public $peg_mengetahui;
    public $peg_menyetujui;
    public $ruanganpenerima_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $pajak_id;
    public $payterm_id;
    public $is_consigment;
    public $is_donasi;
    public $sumber_penerimaan;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'supplier_id',
                'tgl_penerimaan',
                'sumber_penerimaan',
            ],  'required', 'message' => "{attribute} tidak boleh kosong"],
            [[
                'pajak_id',
                'no_faktur',
                'payterm_id',
            ],  'required', 'message' => "{attribute} tidak boleh kosong", 'on' => self::SCENARIO_GUDANG_UMUM],
            [[
                'pajak_id',
                'no_faktur',
                'payterm_id'],'required','when' => function($model){
                    return $model->is_consigment == false;
                },'whenClient' => "isConsigmentChecked", 'message' => "{attribute} tidak boleh kosong",
                'on' => self::SCENARIO_GUDANG_FARMASI
            ],
            [['penerimaansupp_id', 'supplier_id', 'peg_mengetahui', 'peg_menyetujui', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaansupp_id', 'supplier_id', 'peg_mengetahui', 'peg_menyetujui', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'sumber_penerimaan'], 'integer'],
            [['tgl_penerimaan', 'created_date', 'last_modified_date', 'deleted_date','is_donasi'], 'safe'],
            [['additional_data'], 'string'],
            [['pajak_id','payterm_id','no_faktur'],'default', 'value' => NULL],
            [['is_deleted', 'is_active', 'is_consigment','is_donasi'], 'boolean'],
            [['no_penerimaan', 'no_faktur', 'no_suratjalan'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penerimaansupp_id' => 'Penerimaansupp ID',
            'no_penerimaan' => 'No Penerimaan',
            'tgl_penerimaan' => 'Tgl Penerimaan',
            'supplier_id' => 'Nama Supplier',
            'no_faktur' => 'No. Faktur',
            'no_suratjalan' => 'No. Surat Jalan',
            'peg_mengetahui' => 'Peg Mengetahui',
            'peg_menyetujui' => 'Peg Menyetujui',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
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
            'pajak_id' => 'Tarif Pajak',
            'payterm_id' => 'Payment Term',
            'is_consigment' => 'Consignment',
            'is_donasi' => 'Donasi',
            'sumber_penerimaan' => 'Sumber Penerimaan',
        ];
    }
}
