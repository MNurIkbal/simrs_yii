<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penerimaanobat_t".
 *
 * @property int $penerimaanobat_id
 * @property int $validasipoobat_id jika asal dari validasi PO
 * @property string $no_penerimaan generate otomatis
 * @property string $tgl_penerimaan
 * @property int $supplier_id
 * @property string $no_suratjalan
 * @property string $tgl_suratjalan
 * @property string $no_faktur
 * @property int $diterima_oleh otomatis login pemakai -->pegawai_id
 * @property int $ruanganpenerima_id
 * @property int $peg_mengetahui pegawai_m.pegawai_id
 * @property int $peg_menyetujui pegawai_m.pegawai_id
 * @property string $upload_berkas
 * @property string $catatan_berkas
 * @property string $catatan
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
class PenerimaanObat extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penerimaanobat_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['validasipoobat_id', 'supplier_id', 'diterima_oleh', 'ruanganpenerima_id', 'peg_mengetahui', 'peg_menyetujui', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['validasipoobat_id', 'supplier_id', 'diterima_oleh', 'ruanganpenerima_id', 'peg_mengetahui', 'peg_menyetujui', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_penerimaan', 'tgl_suratjalan', 'created_date', 'last_modified_date', 'deleted_date','is_verifikasi', 'is_consigment'], 'safe'],
            [['supplier_id', 'ruanganpenerima_id'], 'required'],
            [['no_faktur', 'no_faktur_sementara', 'upload_berkas', 'catatan_berkas', 'catatan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_consigment'], 'boolean'],
            [['no_penerimaan', 'no_suratjalan', 'no_faktur'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penerimaanobat_id' => 'Penerimaanobat ID',
            'validasipoobat_id' => 'Validasipoobat ID',
            'no_penerimaan' => 'No Penerimaan',
            'tgl_penerimaan' => 'Tgl Penerimaan',
            'supplier_id' => 'Supplier ID',
            'no_suratjalan' => 'No Suratjalan',
            'tgl_suratjalan' => 'Tgl Suratjalan',
            'no_faktur' => 'No Faktur',
            'no_faktur_sementara' => 'No Faktur Sementara',
            'diterima_oleh' => 'Diterima Oleh',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'peg_mengetahui' => 'Peg Mengetahui',
            'peg_menyetujui' => 'Peg Menyetujui',
            'upload_berkas' => 'Upload Berkas',
            'catatan_berkas' => 'Catatan Berkas',
            'catatan' => 'Catatan',
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
