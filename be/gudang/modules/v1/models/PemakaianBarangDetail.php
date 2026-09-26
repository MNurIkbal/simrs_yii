<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemakaianbarangdetail_t".
 *
 * @property int $pemakaianbarangdetail_id
 * @property int $pemakaianbarang_id
 * @property int $barang_id
 * @property int $jumlah_pakai
 * @property double $harga_netto
 * @property double $ppn
 * @property double $disc
 * @property double $hpp
 * @property double $harga_jual
 * @property string $catatan_barang
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
 * @property int $satuanbesar_id
 * @property double $jumlah_input
 * @property int $satuankecil_id
 */
class PemakaianBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pemakaianbarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemakaianbarang_id', 'barang_id', 'jumlah_pakai', 'harga_netto', 'ppn', 'harga_jual'], 'required'],
            [['pemakaianbarang_id', 'barang_id', 'jumlah_pakai', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuanbesar_id', 'satuankecil_id'], 'default', 'value' => null],
            [['pemakaianbarang_id', 'barang_id', 'jumlah_pakai', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuanbesar_id', 'satuankecil_id'], 'integer'],
            [['harga_netto', 'ppn', 'disc', 'hpp', 'harga_jual', 'jumlah_input'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','is_active','is_deleted'], 'safe'],
            [['catatan_barang'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pemakaianbarangdetail_id' => 'Pemakaianbarangdetail ID',
            'pemakaianbarang_id' => 'Pemakaianbarang ID',
            'barang_id' => 'Barang ID',
            'jumlah_pakai' => 'Jumlah Pakai',
            'harga_netto' => 'Harga Netto',
            'ppn' => 'Ppn',
            'disc' => 'Disc',
            'hpp' => 'Hpp',
            'harga_jual' => 'Harga Jual',
            'catatan_barang' => 'Catatan Barang',
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
            'satuanbesar_id' => 'Satuanbesar ID',
            'jumlah_input' => 'Jumlah Input',
            'satuankecil_id' => 'Satuankecil ID',
        ];
    }
}
