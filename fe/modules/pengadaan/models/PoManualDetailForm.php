<?php

namespace app\modules\pengadaan\models;

use Yii;

/**
 * This is the model class for table "pomanualdetail_t".
 *
 * @property int $pomanualdetail_id
 * @property int $pomanual_id
 * @property int $obatalkes_id
 * @property int $barang_id
 * @property int $qty
 * @property int $s_konversibrg_id
 * @property int $s_konversiobt_id
 * @property double $harga
 * @property double $discount
 * @property double $jumlah
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
class PoManualDetailForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $item_id;
    public $satuan_id;
    public $pomanualdetail_id;
    public $pomanual_id;
    public $obatalkes_id;
    public $barang_id;
    public $qty;
    public $s_konversibrg_id;
    public $s_konversiobt_id;
    public $harga;
    public $discount;
    public $jumlah;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;
    public $is_active;

    public static function tableName()
    {
        return 'pomanualdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[/*'pomanual_id',*/ 'qty'], 'required'],
            [['pomanual_id', 'obatalkes_id', 'barang_id', 'qty', 's_konversibrg_id', 's_konversiobt_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pomanual_id', 'obatalkes_id', 'barang_id', 'qty', 's_konversibrg_id', 's_konversiobt_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            // [['harga', 'discount', 'jumlah'], 'number'],
            [['additional_data'], 'string'],
            [['item_id', 'satuan_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pomanualdetail_id' => 'Pomanualdetail ID',
            'pomanual_id' => 'Pomanual ID',
            'obatalkes_id' => 'Obatalkes ID',
            'barang_id' => 'Barang ID',
            'qty' => 'Qty',
            's_konversibrg_id' => 'S Konversibrg ID',
            's_konversiobt_id' => 'S Konversiobt ID',
            'harga' => 'Harga',
            'discount' => 'Discount',
            'jumlah' => 'Jumlah',
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
