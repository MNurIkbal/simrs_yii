<?php

/**
 * @Author: Aris
 * @Date:   2020-09-15 14:00:00
 * @Description: 
 */

namespace app\modules\igd\models;

use Yii;

class TriaseForm extends \yii\base\Model
{
	public $triase_id;
	public $pendaftaran_id;
	public $dokter_id;
	public $perawat_id;
	public $tgl_triase;
	public $keluhan_utama;
	public $tekanan_darah;
	public $nadi;
	public $nafas;
	public $suhu;
	public $saturasi_oksigen;
	public $alergi;
	public $alergi_obat;
	public $alergi_lainnya;
	public $trauma;
	public $jalan_nafas;
	public $pernafasan;
	public $sirkulasi;
	public $gcseye_id;
	public $gcsverbal_id;
	public $gcsmotorik_id;
	public $is_kapitis;
	public $jumlah_gcs;
	public $hasil_gcs;
	public $waktu_respon;
	public $hasil_triase;
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
	public $pegawai_id;
	public $kamartempattidur_id;
	public $old_kamartempattidur_id;
	public $kelompokpegawai_id;
	public $observation_site;
	public $tekanan_darah_sistolik;
	public $tekanan_darah_diastolik;
	public $disability;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[
				[
					'triase_id',
					'pendaftaran_id',
					'dokter_id',
					'perawat_id',
					'tgl_triase',
					'keluhan_utama',
					'tekanan_darah',
					'nadi',
					'nafas',
					'suhu',
					'saturasi_oksigen',
					'alergi',
					'alergi_obat',
					'alergi_lainnya',
					'trauma',
					'jalan_nafas',
					'pernafasan',
					'sirkulasi',
					'gcseye_id',
					'gcsverbal_id',
					'gcsmotorik_id',
					'is_kapitis',
					'jumlah_gcs',
					'hasil_gcs',
					'waktu_respon',
					'hasil_triase',
					'pegawai_id',
					'kamartempattidur_id',
					'old_kamartempattidur_id',
					'kelompokpegawai_id',
					'observation_site'
				],
				'safe'
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'triase_id'        => Yii::t('fe', 'Triase Id'),
			'pendaftaran_id'   => Yii::t('fe', 'Pendaftaran Id'),
			'dokter_id'        => Yii::t('fe', 'Dokter Triase'),
			'perawat_id'       => Yii::t('fe', 'Perawat Triase'),
			'tgl_triase'       => Yii::t('fe', 'Tanggal/Jam Pasien Datang'),
			'keluhan_utama'    => Yii::t('fe', 'Keluhan Utama'),
			'tekanan_darah'    => Yii::t('fe', 'Tekanan Darah'),
			'nadi'             => Yii::t('fe', 'Frekuensi Nadi'),
			'nafas'            => Yii::t('fe', 'Frekuensi Nafas'),
			'suhu'             => Yii::t('fe', 'Suhu/Temp'),
			'saturasi_oksigen' => Yii::t('fe', 'Saturasi Oksigen (SpO2)'),
			'alergi'           => Yii::t('fe', ''),
			'alergi_obat'      => Yii::t('fe', ''),
			'alergi_lainnya'   => Yii::t('fe', ''),
			'trauma'           => Yii::t('fe', ''),
			'jalan_nafas'      => Yii::t('fe', ''),
			'pernafasan'       => Yii::t('fe', ''),
			'sirkulasi'        => Yii::t('fe', ''),
			'gcseye_id'        => Yii::t('fe', 'GCS Eye'),
			'gcsverbal_id'     => Yii::t('fe', 'GCS Verbal'),
			'gcsmotorik_id'    => Yii::t('fe', 'GCS Motorik'),
			'is_kapitis'       => Yii::t('fe', ''),
			'jumlah_gcs'       => Yii::t('fe', ''),
			'hasil_gcs'        => Yii::t('fe', 'Hasil GCS'),
			'waktu_respon'     => Yii::t('fe', ''),
			'hasil_triase'     => Yii::t('fe', ''),
			'observation_site' => Yii::t('fe', 'Observation Site'),
			'tekanan_darah_sistolik' => Yii::t('fe', 'Tekanan Darah Sistolik'),
			'tekanan_darah_diastolik' => Yii::t('fe', 'Tekanan Darah Diastolik'),
		];
	}
}
