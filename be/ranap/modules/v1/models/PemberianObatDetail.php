<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:18:14
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-31 10:06:09
 */

// Namespace
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pemberianobatdetail_t".
 *
 * @property int $pemberianobatdetail_id
 * @property int $pemberianobat_id
 * @property int $stokobatpasien_id
 * @property int $jenisobat_id
 * @property string $no_res_rekon
 * @property int $obatalkes_id
 * @property string $nama_obat
 * @property string $signa_obat
 * @property int $jumlah
 * @property int $dokter_id
 * @property string $wkt_pemberian
 * @property int $pemberi1_id
 * @property int $pemberi2_id
 * @property string $efek lookup_type='efek_obat'
 * @property string $keterangan lookup_type='ket_pemberian'
 * @property bool $is_resep
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
class PemberianObatDetail extends \Doco\components\DocoActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'pemberianobatdetail_t';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['pemberianobat_id', 'stokobatpasien_id', 'dokter_id'], 'required'],
			[['pemberianobat_id', 'stokobatpasien_id', 'jenisobat_id', 'obatalkes_id', 'jumlah', 'dokter_id', 'pemberi1_id', 'pemberi2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
			[['pemberianobatdetail_id', 'pemberianobat_id', 'stokobatpasien_id', 'jenisobat_id', 'obatalkes_id', 'jumlah', 'dokter_id', 'pemberi1_id', 'pemberi2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
			[['wkt_pemberian', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
			[['is_resep', 'is_deleted', 'is_active'], 'boolean'],
			[['additional_data'], 'string'],
			[['no_res_rekon', 'nama_obat', 'efek', 'keterangan'], 'string', 'max' => 255],
			[['signa_obat'], 'string', 'max' => 100],
			[['pemberianobatdetail_id'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'pemberianobatdetail_id' => 'Pemberianobatdetail ID',
			'pemberianobat_id' => 'Pemberianobat ID',
			'stokobatpasien_id' => 'Stokobatpasien ID',
			'jenisobat_id' => 'Jenisobat ID',
			'no_res_rekon' => 'No Res Rekon',
			'obatalkes_id' => 'Obatalkes ID',
			'nama_obat' => 'Nama Obat',
			'signa_obat' => 'Signa Obat',
			'jumlah' => 'Jumlah',
			'dokter_id' => 'Dokter ID',
			'wkt_pemberian' => 'Wkt Pemberian',
			'pemberi1_id' => 'Pemberi1 ID',
			'pemberi2_id' => 'Pemberi2 ID',
			'efek' => 'Efek',
			'keterangan' => 'Keterangan',
			'is_resep' => 'Is Resep',
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
?>