<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mutasibarangdetail_t".
 *
 * @property int $mutasibarangdetail_id
 * @property int $pesanbarangdetail_id
 * @property int $barang_id
 * @property int $batalmutasibrg_id
 * @property int $mutasibarang_id
 * @property double $qty_mutasi
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
 * @property double $qty_dipesan
 * @property int $satuanbesar_id
 * @property double $jumlah_input
 * @property int $satuankecil_id
 *
 * @property InventarisasiruanganT[] $inventarisasiruanganTs
 * @property BarangM $barang
 * @property BatalmutasibarangT $batalmutasibrg
 * @property MutasibarangT $mutasibarang
 * @property PesanbarangdetailT $pesanbarangdetail
 */
class MutasiBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mutasibarangdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanbarangdetail_id', 'barang_id', 'batalmutasibrg_id', 'mutasibarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuanbesar_id', 'satuankecil_id'], 'default', 'value' => null],
            [['pesanbarangdetail_id', 'barang_id', 'batalmutasibrg_id', 'mutasibarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuanbesar_id', 'satuankecil_id'], 'integer'],
            [['barang_id', 'mutasibarang_id', 'qty_mutasi'], 'required'],
            [['qty_mutasi', 'qty_dipesan', 'jumlah_input'], 'number'],
            [['additional_data'], 'string'],
            [['harga_netto','created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['barang_id'], 'exist', 'skipOnError' => true, 'targetClass' => BarangM::className(), 'targetAttribute' => ['barang_id' => 'barang_id']],
            // [['batalmutasibrg_id'], 'exist', 'skipOnError' => true, 'targetClass' => BatalmutasibarangT::className(), 'targetAttribute' => ['batalmutasibrg_id' => 'batalmutasibarang_id']],
            // [['mutasibarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasibarangT::className(), 'targetAttribute' => ['mutasibarang_id' => 'mutasibarang_id']],
            // [['pesanbarangdetail_id'], 'exist', 'skipOnError' => true, 'targetClass' => PesanbarangdetailT::className(), 'targetAttribute' => ['pesanbarangdetail_id' => 'pesanbarangdetail_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarangdetail_id' => 'Mutasibarangdetail ID',
            'pesanbarangdetail_id' => 'Pesanbarangdetail ID',
            'barang_id' => 'Barang ID',
            'batalmutasibrg_id' => 'Batalmutasibrg ID',
            'mutasibarang_id' => 'Mutasibarang ID',
            'qty_mutasi' => 'Qty Mutasi',
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
            'qty_dipesan' => 'Qty Dipesan',
            'satuanbesar_id' => 'Satuanbesar ID',
            'jumlah_input' => 'Jumlah Input',
            'satuankecil_id' => 'Satuankecil ID',
            'harga_netto' => 'Harga Netto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getInventarisasiruanganTs()
    // {
    //     return $this->hasMany(InventarisasiruanganT::className(), ['mutasibarangdetail_id' => 'mutasibarangdetail_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBarang()
    // {
    //     return $this->hasOne(BarangM::className(), ['barang_id' => 'barang_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getBatalmutasibrg()
    // {
    //     return $this->hasOne(BatalmutasibarangT::className(), ['batalmutasibarang_id' => 'batalmutasibrg_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getMutasibarang()
    // {
    //     return $this->hasOne(MutasibarangT::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    // }

    // /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getPesanbarangdetail()
    // {
    //     return $this->hasOne(PesanbarangdetailT::className(), ['pesanbarangdetail_id' => 'pesanbarangdetail_id']);
    // }
}
