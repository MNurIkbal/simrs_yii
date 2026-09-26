<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mutasiobatdetail_t".
 *
 * @property int $mutasiobatdetail_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $sumberdana_id
 * @property int $mutasiobatruangan_id
 * @property int $pesanobatdetail_id
 * @property double $jumlah_mutasi
 * @property double $jumlah_pesan
 * @property double $harga_netto
 * @property double $harga_jualsatuan
 * @property double $persen_discount
 * @property double $total_harga
 * @property string $tgl_kadaluarsa
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
class MutasiObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mutasiobatdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'mutasiobatruangan_id', 'jumlah_mutasi','satuankecil_id','satuanbesar_id','jumlah_input'], 'required'],
            [['obatalkes_id', 'satuankecil_id', 'sumberdana_id', 'mutasiobatruangan_id', 'pesanobatdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkes_id', 'satuankecil_id', 'sumberdana_id', 'mutasiobatruangan_id', 'pesanobatdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah_mutasi', 'jumlah_pesan', 'harga_netto', 'harga_jualsatuan', 'persen_discount', 'total_harga'], 'number'],
            [['tgl_kadaluarsa', 'jumlah_mutasi','total_harga','created_date', 'last_modified_date', 'deleted_date','satuanbesar_id','jumlah_input', 'cost'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'satuankecil_id' => 'Satuankecil ID',
            'sumberdana_id' => 'Sumberdana ID',
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',
            'pesanobatdetail_id' => 'Pesanobatdetail ID',
            'jumlah_mutasi' => 'Jumlah Mutasi',
            'jumlah_pesan' => 'Jumlah Pesan',
            'harga_netto' => 'Harga Netto',
            'harga_jualsatuan' => 'Harga Jualsatuan',
            'persen_discount' => 'Persen Discount',
            'total_harga' => 'Total Harga',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
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
