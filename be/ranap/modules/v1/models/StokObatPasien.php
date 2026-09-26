<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokobatpasien_r".
 *
 * @property int $stokobatpasien_id
 * @property int $rekonsiliasiobat_id
 * @property int $obatalkespasien_id
 * @property int $obatalkes_id
 * @property string $nama_obat
 * @property string $satuan_kecil
 * @property int $stok_layak
 * @property int $stok_sisa
 * @property int $stok_dipakai
 * @property int $stok_pending
 * @property int $stok_retur stok_sisa+stok_pending
 * @property int $stok_retur_sisa
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
class StokObatPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokobatpasien_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekonsiliasiobat_id', 'obatalkespasien_id', 'obatalkes_id', 'stok_layak', 'stok_sisa', 'stok_dipakai', 'stok_pending', 'stok_retur', 'stok_retur_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['rekonsiliasiobat_id', 'obatalkespasien_id', 'obatalkes_id', 'stok_layak', 'stok_sisa', 'stok_dipakai', 'stok_pending', 'stok_retur', 'stok_retur_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_obat'], 'string', 'max' => 255],
            [['satuan_kecil'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekonsiliasiobat_id' => Yii::t('app', 'Rekonsiliasiobat ID'),
            'obatalkespasien_id' => Yii::t('app', 'Obatalkespasien ID'),
            'obatalkes_id' => Yii::t('app', 'Obatalkes ID'),
            'nama_obat' => Yii::t('app', 'Nama Obat'),
            'satuan_kecil' => Yii::t('app', 'Satuan Kecil'),
            'stok_layak' => Yii::t('app', 'Stok Layak'),
            'stok_sisa' => Yii::t('app', 'Stok Sisa'),
            'stok_dipakai' => Yii::t('app', 'Stok Dipakai'),
            'stok_pending' => Yii::t('app', 'Stok Pending'),
            'stok_retur' => Yii::t('app', 'Stok Retur'),
            'stok_retur_sisa' => Yii::t('app', 'Stok Retur Sisa'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}
