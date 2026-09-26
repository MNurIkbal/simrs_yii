<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-26 10:25:38
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-26 16:51:27
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketpelayanan_mp".
 *
 * @property int $daftartindakan_id
 * @property int $tipepaket_id
 * @property int $paketdetail_id
 * @property int $ruangan_id
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
 *
 * @property DaftartindakanM $daftartindakan
 * @property TipepaketM $tipepaket
 */
class PaketPelayanan extends \Doco\components\DocoActiveRecord
{
	public static function primaryKey()
	{
		return ['daftartindakan_id','tipepaket_id', 'paketdetail_id', 'ruangan_id'];
	}
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'paketpelayanan_mp';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['tipepaket_id'], 'required'],
			[['tipepaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['daftartindakan_id', 'tipepaket_id', 'paketdetail_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['additional_data'], 'string'],
			[['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_id', 'paketdetail_id', 'ruangan_id'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
			// [['daftartindakan_id', 'tipepaket_id'], 'unique', 'targetAttribute' => ['daftartindakan_id', 'tipepaket_id']],
			// [['daftartindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DaftartindakanM::className(), 'targetAttribute' => ['daftartindakan_id' => 'daftartindakan_id']],
			// [['tipepaket_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipepaketM::className(), 'targetAttribute' => ['tipepaket_id' => 'tipepaket_id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'daftartindakan_id' => Yii::t('app', 'Daftar Tindakan ID'),
			'tipepaket_id' => Yii::t('app', 'Tipe Paket ID'),
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

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDaftarTindakan()
	{
		return $this->hasOne(DaftarTindakan::className(), ['daftartindakan_id' => 'daftartindakan_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTipePaket()
	{
		return $this->hasOne(TipePaket::className(), ['tipepaket_id' => 'tipepaket_id']);
	}
}
?>