<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pesanbarangdetail_t".
 *
 * @property int $pesanbarangdetail_id
 * @property int $barang_id
 * @property int $pesanbarang_id
 * @property double $qty_pesan
 * @property string $satuankecil_id
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
 *
 * @property MutasibarangdetailT[] $mutasibarangdetailTs
 */
class PesanBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanbarangdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['barang_id', 'pesanbarang_id'], 'required'],
            [['barang_id', 'pesanbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuanbesar_id'], 'default', 'value' => null],
            [['barang_id', 'pesanbarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuanbesar_id'], 'integer'],
            [['qty_pesan', 'jumlah_input'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','is_active','is_deleted'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['satuankecil_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarangdetail_id' => 'Pesanbarangdetail ID',
            'barang_id' => 'Barang ID',
            'pesanbarang_id' => 'Pesanbarang ID',
            'qty_pesan' => 'Qty Pesan',
            'satuankecil_id' => 'Satuankecil ID',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibarangdetailTs()
    {
        return $this->hasMany(MutasiBarangDetail::className(), ['pesanbarangdetail_id' => 'pesanbarangdetail_id']);
    }
}
