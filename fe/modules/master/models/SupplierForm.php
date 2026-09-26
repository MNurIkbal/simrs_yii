<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 09:19:10
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-02-13 11:48:22
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jenispemeriksaanlab_m".
 *
 * @property int $jenispemeriksaanlab_id
 * @property string $jenispemeriksaanlab_kode
 * @property string $jenispemeriksaanlab_nama
 * @property string $jenispemeriksaanlab_namalainnya
 * @property int $kelompokpemeriksaanlab_id
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
 * @property PemeriksaanLab[] $pemeriksaanLab
 */
class SupplierForm extends \yii\base\Model
{
	const SCENARIO_PMI = 'pmi';
	const SCENARIO_MASTER = 'master';

	// Public property
	public $supplier_id;
	public $pbf_id;
	public $supplier_kode;
	public $supplier_nama;
	public $supplier_namalain;
	public $supplier_alamat;
	public $propinsi_id;
	public $kabupaten_id;
	public $no_tlp;
	public $email;
	public $no_fax;
	public $no_npwp;
	public $no_rekening;
	public $nama_pemilikrek;
	public $bank_id;
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
	public $nama_pmi;
	public $pajak_id;

	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_PMI] = ['nama_pmi', 'supplier_alamat', 'no_tlp'];
        return $scenarios;
    }

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pbf_id', 'propinsi_id', 'kabupaten_id', 'no_npwp', 'bank_id', 'pajak_id','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['pbf_id', 'propinsi_id', 'kabupaten_id', 'no_npwp', 'bank_id', 'pajak_id','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],

			[['supplier_kode', 'supplier_nama', 'supplier_alamat', 'propinsi_id', 'kabupaten_id', 'no_tlp', 'no_npwp', 'no_rekening', 'nama_pemilikrek', 'bank_id', 'pajak_id'], 'required', 'on' => self::SCENARIO_MASTER, 'message'=>'{attribute} Tidak boleh kosong'],
			[['supplier_alamat', 'additional_data'], 'string'],
			[['nama_pmi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['is_deleted', 'is_active'], 'boolean'],
			[['supplier_kode'], 'string', 'max' => 25],
			[['supplier_nama', 'supplier_namalain', 'email'], 'string', 'max' => 100],
			[['no_tlp'], 'string', 'max' => 30],
			[['no_fax'], 'string', 'max' => 50],
			[['no_rekening', 'nama_pemilikrek'], 'string', 'max' => 255],
			[['nama_pmi', 'supplier_alamat', 'no_tlp'], 'required', 'on' => self::SCENARIO_PMI, 'message'=>'{attribute} Tidak boleh kosong'],
			[['nama_pmi'], 'string', 'max' => 100],
			[['supplier_alamat'], 'string', 'max' => 500],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'supplier_id' => Yii::t('fe', 'Supplier ID'),
			'pbf_id' => Yii::t('fe', 'Pbf ID'),
			'supplier_kode' => Yii::t('fe', 'Kode Supplier'),
			'supplier_nama' => Yii::t('fe', 'Nama Supplier'),
			'supplier_namalain' => Yii::t('fe', 'Nama Lain Supplier'),
			'supplier_alamat' => Yii::t('fe', 'Alamat'),
			'propinsi_id' => Yii::t('fe', 'Propinsi'),
			'kabupaten_id' => Yii::t('fe', 'Kabupaten'),
			'no_tlp' => Yii::t('fe', 'No Telepon'),
			'email' => Yii::t('fe', 'Alamat Email'),
			'no_fax' => Yii::t('fe', 'Nomor Fax'),
			'no_npwp' => Yii::t('fe', 'Nomor NPWP'),
			'no_rekening' => Yii::t('fe', 'Nomor Rekening'),
			'nama_pemilikrek' => Yii::t('fe', 'Rekening Atas Nama'),
			'bank_id' => Yii::t('fe', 'Nama Bank'),
			'pajak_id' => Yii::t('fe', 'Tarif Pajak'),
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
			'nama_pmi' => Yii::t('fe', 'Nama PMI'),
		];
	}
}
?>
