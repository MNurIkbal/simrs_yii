<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "returtagihandetail_r".
 *
 * @property int $returtagihandetail_id
 * @property int $returtagihan_id
 * @property int $nama_tagihan
 * @property int $obatalkespasien_id
 * @property int $tindakanpelayanan_id
 * @property int $qty_tagihan
 * @property int $tarif_tagihan
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
class ReturTagihanDetailR extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'returtagihandetail_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_tagihan', 'obatalkespasien_id', 'tindakanpelayanan_id', 'qty_tagihan', 'tarif_tagihan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returtagihan_id', 'tindakanpelayanan_id', 'returtagihandetail_id', 'obatalkespasien_id', 'qty_tagihan', 'tarif_tagihan', 'returtagihandetail_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'keterangan', 'returtagihandetail_id', 'nama_tagihan'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'returtagihan_id' => Yii::t('app', 'Retur Tagihan ID'),
            'nama_tagihan' => Yii::t('app', 'Pembayaran ID'),
            'obatalkespasien_id' => Yii::t('app', 'No Tagihan'),
            'tindakanpelayanan_id' => Yii::t('app', 'Pegawai Pembayaran'),
            'qty_tagihan' => Yii::t('app', 'Pegawai Retur'),
            'tarif_tagihan' => Yii::t('app', 'Tanggal Retur'),
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
