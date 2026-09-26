<?php

namespace app\modules\v1\models;

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
 * * @property boolean $is_consigment
 * @property boolean $is_donasi
 * @property int $sumber_penerimaan
 */
class PenerimaanSupplier extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penerimaansupp_t';
    }

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
            ], 'required'],
            [['supplier_id','no_faktur','peg_mengetahui', 'peg_menyetujui', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaansupp_id', 'supplier_id', 'peg_mengetahui', 'peg_menyetujui', 'ruanganpenerima_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'sumber_penerimaan'], 'integer'],
            [['tgl_penerimaan', 'created_date', 'last_modified_date', 'deleted_date', 'is_verifikasi', 'tgl_verifikasi', 'is_consigment','is_donasi'], 'safe'],
            [['additional_data'], 'string'],
            [['pajak_id','payterm_id'], 'default', 'value' => 1],
            [['is_deleted', 'is_active', 'is_consigment','is_donasi'], 'boolean'],
            [['no_penerimaan', 'no_faktur'], 'string', 'max' => 100],
            [['no_faktur'], 'trim'],
            [['no_suratjalan'], 'trim'],
        ];
    }

    public function chkUnique()
    {
        $model = self::find()
        ->where([
            'LOWER (no_faktur)' => strtolower($this->no_faktur), 
            'supplier_id' => $this->supplier_id,
            'is_deleted' => false
        ])->one();

        if (!empty($model) && $model->penerimaansupp_id != $this->penerimaansupp_id) {
            $this->addError("no_faktur", "Nomor Faktur Sudah Dipakai");
            return false;
        }
       
        return true;
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
            'tgl_verifikasi' => 'Tanggal Verifikasi',
            'supplier_id' => 'Supplier ID',
            'no_faktur' => 'No Faktur',
            'no_suratjalan' => 'No Surat Jalan',
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
            'is_verifikasi' => 'Is Verifikasi',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'is_donasi' => 'Donasi',
            'sumber_penerimaan' => 'Sumber Penerimaan',
        ];
    }
}
