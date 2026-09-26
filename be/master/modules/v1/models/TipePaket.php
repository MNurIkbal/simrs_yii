<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-26 10:11:05
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-30 16:46:18
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tipepaket_m".
 *
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property string $tipepaket_kode
 * @property string $tipepaket_namalainnya
 * @property string $keterangan_tipepaket
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property bool $is_mcu
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property PaketbmhpM[] $paketbmhpMs
 * @property PaketpelayananMp[] $paketpelayananMps
 * @property DaftartindakanM[] $daftartindakans
 * @property PermintaanmcuT[] $permintaanmcuTs
 */
class TipePaket extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'tipepaket_m';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['tipepaket_nama', 'tipepaket_kode', 'tipepaket_namalainnya'], 'required'],
			[['keterangan_tipepaket', 'additional_data'], 'string'],
			[['tipepaket_id', 'is_mcu', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['is_deleted', 'is_active', 'is_mcu'], 'boolean'],
			[['tipepaket_nama', 'tipepaket_namalainnya'], 'string', 'max' => 50],
			[['tipepaket_kode'], 'string', 'max' => 20],
			['tipepaket_kode', 'chkKodePaket'],
			['tipepaket_nama', 'chkNamaPaket'],
		];
	}

	public function getKodeLowercase()
	{
		return strtolower($this->tipepaket_kode);
	}

	public function chkKodePaket()
	{

		$model = self::find()->where(['LOWER (tipepaket_kode)' => strtolower($this->tipepaket_kode), 'is_deleted' => false])->one();
		if (!empty($model) && $model->tipepaket_id != $this->tipepaket_id) {
			$this->addError("tipepaket_kode", "Kode Paket Sudah Dipakai");
			return false;
		}
		

		return true;
	}


	public function chkNamaPaket()
	{
		$rest = substr($this->tipepaket_nama, 0, 1);    // returns "f"
		if ($rest == " ") {
			$this->addError("tipepaket_nama", "Nama Paket mengandung spasi di awal kata");
			return false;
		} else {
			$model = self::find()->where(['LOWER (tipepaket_nama)' => strtolower($this->tipepaket_nama), 'is_deleted' => false])->one();
			if (!empty($model) && $model->tipepaket_id != $this->tipepaket_id) {
				$this->addError("tipepaket_nama", "Nama Paket Sudah Dipakai");
				return false;
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'tipepaket_id' => Yii::t('app', 'Tipe Paket ID'),
			'tipepaket_nama' => Yii::t('app', 'Nama Paket'),
			'tipepaket_kode' => Yii::t('app', 'Kode Paket'),
			'tipepaket_namalainnya' => Yii::t('app', 'Nama Lainnya'),
			'keterangan_tipepaket' => Yii::t('app', 'Keterangan'),
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
?>