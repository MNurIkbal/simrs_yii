<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokopnamebarangdetail_t".
 *
 * @property int $stokopnamebarangdetail_id
 * @property int $formsobarangdetail_id
 * @property int $satuankecil_id
 * @property int $stokopnamebarang_id
 * @property int $barang_id
 * @property double $volume_fisik
 * @property double $volume_sistem
 * @property double $hargasatuan
 * @property double $jumlahharga
 * @property double $harganetto
 * @property double $jumlahnetto
 * @property string $tglkadaluarsa
 * @property string $kondisibarang lookup_type='kondisi_barang'
 * @property string $tglperiksafisik
 * @property double $jmlselisihstok
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
class StokOpnameBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokopnamebarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formsobarangdetail_id', 'satuankecil_id', 'stokopnamebarang_id', 'barang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['formsobarangdetail_id', 'satuankecil_id', 'stokopnamebarang_id', 'barang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['barang_id', 'volume_fisik', 'volume_sistem', 'tglkadaluarsa', 'kondisibarang', 'tglperiksafisik'], 'required'],
            [['volume_fisik', 'volume_sistem', 'hargasatuan', 'jumlahharga', 'harganetto', 'jumlahnetto', 'jmlselisihstok', 'revisi_stok'], 'number'],
            [['tglkadaluarsa', 'tglperiksafisik', 'created_date', 'last_modified_date', 'deleted_date', 'is_newso'], 'safe'],
            [['kondisibarang', 'additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_newso'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokopnamebarangdetail_id' => 'Stokopnamebarangdetail ID',
            'formsobarangdetail_id' => 'Formsobarangdetail ID',
            'satuankecil_id' => 'Satuankecil ID',
            'stokopnamebarang_id' => 'Stokopnamebarang ID',
            'barang_id' => 'Barang ID',
            'volume_fisik' => 'Volume Fisik',
            'volume_sistem' => 'Volume Sistem',
            'hargasatuan' => 'Hargasatuan',
            'jumlahharga' => 'Jumlahharga',
            'harganetto' => 'Harganetto',
            'jumlahnetto' => 'Jumlahnetto',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'kondisibarang' => 'Kondisibarang',
            'tglperiksafisik' => 'Tglperiksafisik',
            'jmlselisihstok' => 'Jmlselisihstok',
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
