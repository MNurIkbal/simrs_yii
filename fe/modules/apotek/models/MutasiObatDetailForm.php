<?php

namespace app\modules\apotek\models;

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
class MutasiObatDetail extends \yii\db\ActiveRecord
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
            [['obatalkes_id', 'mutasiobatruangan_id', 'jumlah_mutasi'], 'required'],
            [['obatalkes_id', 'satuankecil_id', 'sumberdana_id', 'mutasiobatruangan_id', 'pesanobatdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['obatalkes_id', 'satuankecil_id', 'sumberdana_id', 'mutasiobatruangan_id', 'pesanobatdetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jumlah_mutasi', 'jumlah_pesan', 'harga_netto', 'harga_jualsatuan', 'persen_discount', 'total_harga'], 'number'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'mutasiobatdetail_id' => Yii::t('fe','Mutasiobatdetail ID'),
            'obatalkes_id' => Yii::t('fe','Obatalkes ID'),
            'satuankecil_id' => Yii::t('fe','Satuankecil ID'),
            'sumberdana_id' => Yii::t('fe','Sumberdana ID'),
            'mutasiobatruangan_id' => Yii::t('fe','Mutasiobatruangan ID'),
            'pesanobatdetail_id' => Yii::t('fe','Pesanobatdetail ID'),
            'jumlah_mutasi' => Yii::t('fe','Jumlah Mutasi'),
            'jumlah_pesan' => Yii::t('fe','Jumlah Pesan'),
            'harga_netto' => Yii::t('fe','Harga Netto'),
            'harga_jualsatuan' => Yii::t('fe','Harga Jualsatuan'),
            'persen_discount' => Yii::t('fe','Persen Discount'),
            'total_harga' => Yii::t('fe','Total Harga'),
            'tgl_kadaluarsa' => Yii::t('fe','Tgl Kadaluarsa'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }
}
