<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 11:54:09
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-12 09:53:34
 */

namespace app\modules\ranap\models;

use Yii;
use app\components\DocoHelpers;

class ResepturDetailForm extends \yii\base\Model
{

	public $resepturdetail_id;
	public $obatalkes_id;
	public $racikan_id;
	public $satuankecil_id;
	public $sumberdana_id;
	public $reseptur_id;
	public $jml_konversi;
	public $r;
	public $rke;
	public $permintaan_reseptur;
	public $jmlkemasan_reseptur;
	public $kekuatan_reseptur;
	public $satuankekuatan;
	public $qty_reseptur;
	public $hargasatuan_reseptur;
	public $signa_reseptur;
	public $harganetto_reseptur;
	public $hargajual_reseptur;
	public $etiket;
	public $iter;
	public $satuansediaan;
	public $additional_data;
	public $created_date;
	public $created_by;
	public $modified_count;
	public $last_modified_date;
	public $last_modified_by;
	public $is_deleted;
	public $is_active;
	public $deleted_date;
	public $deleted_by;
	public $tmp_hargasatuan;
	public $stok_sisa;
	public $tmp_stok_sisa;
	public $nilai_konversi;
	public $satuankecil;
	public $satuanbesar_id;
	public $satuan_penyimpanan;
	public $stok_konversi;
	public $qty_konversi;
	public $tmp_stok_sisa_r;

	// other attributes
	public $satuankecil_nama;
	public $cppt_id;

	// constants
	const SCENARIO_SESSION = 'session'; //scenario add session
	const VC_RC = 'OR'; //constant reseptur type racikan
	const VC_NRC = 'NR'; //constant reseptur type non racikan

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['obatalkes_id', 'qty_reseptur', 'signa_reseptur', 'satuankecil_id', 'satuankecil_nama', 'jml_konversi', 'qty_konversi', 'rke'], 'required'],
			[['obatalkes_id', 'racikan_id', 'satuankecil_id', 'sumberdana_id', 'reseptur_id', 'rke', 'permintaan_reseptur', 'jmlkemasan_reseptur', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'cppt_id'], 'default', 'value' => null],
			[['racikan_id', 'satuankecil_id', 'sumberdana_id', 'reseptur_id', 'rke', 'permintaan_reseptur', 'jmlkemasan_reseptur', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'cppt_id'], 'integer'],
			[['hargasatuan_reseptur', 'harganetto_reseptur', 'hargajual_reseptur'], 'number'],
			[['additional_data'], 'string'],
			// [['qty_konversi', 'jml_konversi'], 'double'],
			[['created_date', 'satuankecil_nama', 'last_modified_date', 'tmp_hargasatuan', 'satuankecil', 'nilai_konversi', 'tmp_stok_sisa', 'stok_sisa', 'jml_konversi', 'deleted_date', 'racikan_id', 'satuanbesar_id', 'qty_konversi', 'satuan_penyimpanan', 'stok_konversi', 'sumberdana_id', 'reseptur_id', 'qty_reseptur', 'tmp_stok_sisa_r'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['r'], 'string', 'max' => 2],
			[['satuankekuatan'], 'string', 'max' => 20],
			// [['signa_reseptur'], 'string', 'max' => 30],
			[['etiket'], 'string', 'max' => 100],
			[['satuansediaan'], 'string', 'max' => 50],
			[['qty_reseptur'], 'validasiQty'],
		];
	}

	// override scenarios
	public function scenarios()
	{
		return [
			self::SCENARIO_SESSION => ['obatalkes_id', 'racikan_id', 'qty_reseptur', 'jml_konversi', 'satuankecil_nama', 'signa_reseptur', 'qty_konversi', 'rke', 'required'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'resepturdetail_id' => 'Resepturdetail ID',
			'obatalkes_id' => 'Nama Obat',
			'racikan_id' => 'Racikan ID',
			'satuankecil_id' => 'Satuankecil ID',
			'sumberdana_id' => 'Sumberdana ID',
			'reseptur_id' => 'Reseptur ID',
			'r' => 'R',
			'rke' => 'R -',
			'permintaan_reseptur' => Yii::t('fe', 'Permintaan'),
			'jmlkemasan_reseptur' => Yii::t('fe', 'Jumlah kemasan'),
			'kekuatan_reseptur' => Yii::t('fe', 'Kekuatan'),
			'satuankekuatan' => Yii::t('fe', 'Satuan'),
			'qty_reseptur' => Yii::t('fe', 'QTY'),
			'hargasatuan_reseptur' => Yii::t('fe', 'Harga satuan'),
			'signa_reseptur' => Yii::t('fe', 'Signa'),
			'harganetto_reseptur' => Yii::t('fe', 'Harga neto'),
			'hargajual_reseptur' => Yii::t('fe', 'Harga jual'),
			'etiket' => Yii::t('fe', 'Catatan'),
			'iter' => 'Iter',
			'jml_konversi' => 'QTY',
			'satuansediaan' => Yii::t('fe', 'Satuan sediaan'),
			'additional_data' => Yii::t('fe', 'additional_data'),
			'created_date' => Yii::t('fe', 'created_date'),
			'created_by' => Yii::t('fe', 'created_by'),
			'modified_count' => Yii::t('fe', 'modified_count'),
			'last_modified_date' => Yii::t('fe', 'last_modified_date'),
			'last_modified_by' => Yii::t('fe', 'last_modified_by'),
			'is_deleted' => Yii::t('fe', 'is_deleted'),
			'is_active' => Yii::t('fe', 'is_active'),
			'deleted_date' => Yii::t('fe', 'deleted_date'),
			'deleted_by' => Yii::t('fe', 'deleted_by'),
		];
	}

	public function validasiQty()
	{
		// $post = Yii::$app->request->post();
		if(is_array($this->qty_reseptur)){
			foreach ($this->qty_reseptur as $key => $value) {
                if (is_double($this->qty_reseptur[$key])) {
                    DocoHelpers::multipleParseError($this, 'Harus Berupa Angka', 'qty_reseptur', $key);
                }
				if(empty($this->obatalkes_id[$key])){
					DocoHelpers::multipleParseError($this, 'harap diisi', 'obatalkes_id', $key);
				}
				if(empty($value)){
					DocoHelpers::multipleParseError($this, 'harap diisi', 'qty_reseptur', $key);
				}
			}
		}
	}
}
?>