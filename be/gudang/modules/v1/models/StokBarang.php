<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokbarang_t".
 *
 * @property int $stokbarang_id
 * @property int $ruangan_id
 * @property int $penerimaandetail_id
 * @property int $terimamutasibarangdetail_id
 * @property int $returbarangdetail_id
 * @property int $mutasibarangdetail_id
 * @property int $pemusnahanbarangdetail_id
 * @property int $stokopnamebarangdetail_id
 * @property int $barang_id
 * @property string $tglkadaluarsa
 * @property string $nobatch
 * @property string $tglstok_in
 * @property string $tglstok_out
 * @property double $qtystok_in
 * @property double $qtystok_out
 * @property double $harganetto
 * @property double $persendiscount
 * @property double $jmldiscount
 * @property double $persenppn
 * @property double $persenpph
 * @property double $persenmargin
 * @property double $jmlmargin
 * @property bool $stokbarang_aktif
 * @property int $stokbarangasal_id
 * @property int $satuankecil_id
 * @property string $tglterima
 * @property int $lokasibarang_id
 * @property int $rakbarang_id
 * @property int $pemakaianbarangdetail_id
 * @property int $produksibarangdetail_id
 * @property int $storexpiredbarangdetail_id
 * @property double $stok
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
class StokBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokbarang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id','barang_id'], 'required'],
            [['stokbarang_id', 'ruangan_id', 'penerimaandetail_id', 'terimamutasibarangdetail_id', 'returbarangdetail_id', 'mutasibarangdetail_id', 'pemusnahanbarangdetail_id', 'barang_id', 'stokbarangasal_id', 'satuankecil_id', 'lokasibarang_id', 'rakbarang_id', 'pemakaianbarangdetail_id', 'produksibarangdetail_id', 'penerimaansuppdetail_id'], 'default', 'value' => null],
            [['stokbarang_id', 'ruangan_id', 'penerimaandetail_id', 'terimamutasibarangdetail_id', 'returbarangdetail_id', 'mutasibarangdetail_id', 'pemusnahanbarangdetail_id', 'barang_id', 'stokbarangasal_id', 'satuankecil_id', 'lokasibarang_id', 'rakbarang_id', 'pemakaianbarangdetail_id', 'produksibarangdetail_id'], 'integer'],
            [['tglkadaluarsa', 'tglstok_in', 'tglstok_out', 'tglterima','created_date','is_deleted','stokopnamebarangdetail_id', 'adjusmenbarangmasuk_id', 'adjusmenbarangkeluar_id','stokbarang_aktif','stokbarangasal_id'], 'safe'],
            [['qtystok_in', 'qtystok_out', 'harganetto', 'persendiscount', 'jmldiscount', 'persenppn', 'persenpph', 'persenmargin', 'jmlmargin'], 'number'],
            [['stokbarang_aktif'], 'boolean'],
            [['nobatch'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokbarang_id' => 'Stokbarang ID',
            'ruangan_id' => 'Ruangan ID',
            'penerimaandetail_id' => 'Penerimaandetail ID',
            'terimamutasibarangdetail_id' => 'Terimamutasibarangdetail ID',
            'returbarangdetail_id' => 'Returbarangdetail ID',
            'mutasibarangdetail_id' => 'Mutasibarangdetail ID',
            'pemusnahanbarangdetail_id' => 'Pemusnahanbarangdetail ID',
            'barang_id' => 'Barang ID',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'nobatch' => 'Nobatch',
            'tglstok_in' => 'Tglstok In',
            'tglstok_out' => 'Tglstok Out',
            'qtystok_in' => 'Qtystok In',
            'qtystok_out' => 'Qtystok Out',
            'harganetto' => 'Harganetto',
            'persendiscount' => 'Persendiscount',
            'jmldiscount' => 'Jmldiscount',
            'persenppn' => 'Persenppn',
            'persenpph' => 'Persenpph',
            'persenmargin' => 'Persenmargin',
            'jmlmargin' => 'Jmlmargin',
            'stokbarang_aktif' => 'Stokbarang Aktif',
            'stokbarangasal_id' => 'Stokbarangasal ID',
            'satuankecil_id' => 'Satuankecil ID',
            'tglterima' => 'Tglterima',
            'lokasibarang_id' => 'Lokasibarang ID',
            'rakbarang_id' => 'Rakbarang ID',
            'pemakaianbarangdetail_id' => 'Pemakaianbarangdetail ID',
            'produksibarangdetail_id' => 'Produksibarangdetail ID',
            'stokinhand' => 'Stokinhand',
            'storexpiredbarangdetail_id' => 'Storexpiredbarangdetail ID',
            'stok' => 'Stok',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by'=>'Deleted By',
        ];
    }
}
