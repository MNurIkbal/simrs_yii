<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemakaianambulan_t".
 *
 * @property int $pemakaianambulan_id
 * @property datetime $tgl_pemakaiandari
 * @property datetime $tgl_pemakaiansampai
 * @property int $durasi_pemakaian
 * @property int $pelayanan_ambulan
 * @property int $km_awal
 * @property int $estimasi_jarak
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property datetime $tgl_realisasikembali
 * @property string $lama_pemakaian
 * @property int $km_akhir
 * @property double $biaya_pemakaian
 * @property double $biaya_tambahan
 * @property double $total_biaya

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
class PemakaianAmbulan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemakaianambulan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pemakaiandari', 'tgl_pemakaiansampai', 'km_awal', 'pelayanan_ambulan'], 'required'],
            [[
                'durasi_pemakaian',
                'pelayanan_ambulan',
                'km_awal',
                'pendaftaran_id',
                'pasien_id',
                'km_akhir',
                'biaya_pemakaian',
                'biaya_tambahan',
                'total_biaya',
                'estimasi_jarak',
                'created_by', 'modified_count', 'last_modified_by', 'deleted_by'
            ], 'default', 'value' => null],
            [[
                'durasi_pemakaian',
                'pelayanan_ambulan',
                'km_awal',
                'pendaftaran_id',
                'pasien_id',
                'km_akhir',
                'estimasi_jarak',
                'created_by', 'modified_count', 'last_modified_by', 'deleted_by'
            ], 'integer'],
            [['lama_pemakaian', 'additional_data'], 'string'],
            [['tgl_realisasikembali', 'created_date', 'last_modified_date', 'deleted_date', 'estimasi_jarak'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemakaianambulan_id' => 'Pemakaian Ambulan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'tgl_pemakaiandari' => 'Tanggal Pemakaian Dari',
            'tgl_pemakaiansampai' => 'Tanggal Pemakaian Sampai',
            'durasi_pemakaian' => 'Durasi Pemakaian',
            'pelayanan_ambulan' => 'Pelayanan Ambulan',
            'km_awal' => 'Km Awal',
            'estimasi_jarak' => 'Estimasi Jarak',
            'tgl_realisasikembali' => 'Tanggal Realisasi Kembali',
            'lama_pemakaian' => 'Lama Pemakaian',
            'km_akhir' => 'Km Akhir',
            'biaya_pemakaian' => 'Biaya Pemakaian',
            'biaya_tambahan' => 'Biaya Tambahan',
            'total_biaya' => 'Total Biaya',

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
