<?php

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class HasilPemeriksaanPhrForm extends DocoBaseModel
{
	protected $xssProtected = [
		'nama',
		'status_kesehatan',
		'usia',
		'catatan'
	];
	
    public $pendaftaran_id;
    public $pasien_id;

    public $nama_perusahaan;
	public $lokasi_kerja;
	public $jabatan;
	public $tgl_periksa;
	public $matriks_pemeriksaan;
	public $prosedur_pemeriksaan;
	public $prosedur_pemeriksaan_text;
    public $tipe_pekerja;
    public $status_derajat_kesehatan;
    public $laik_satutahun;
    public $laik_catatan;
    public $laik_catatan_keterangan;
    public $laik_catatan_masaberlaku;
    public $laik_penyesuaian_keterangan;
    public $laik_penyesuaian_masaberlaku;
    public $tidak_laik_keterangan;
    public $tidak_laik_pilihan;
    public $catatan_wajib_kacamata;
    public $catatan_wajib_alatdengar;
    public $catatan_rekomendasi;

    public $ruanganterakhir_id;
    public $pegawai_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['tgl_periksa'], 'required'],
			[
				[	
                    'resumemedis_id', 
					'tgl_periksa', 'nama','no_rm','usia', 'bagian', 'catatan', 'status_kesehatan', 'evaluasi',
                    'pendaftaran_id', 'pasien_id', 'ruanganterakhir_id', 'pegawai_id',
                    'nama_perusahaan',
                    'lokasi_kerja',
                    'jabatan',
                    'matriks_pemeriksaan',
                    'prosedur_pemeriksaan',
                    'prosedur_pemeriksaan_text',
                    'tipe_pekerja',
                    'status_derajat_kesehatan',
                    'laik_satutahun',
                    'laik_catatan',
                    'laik_catatan_keterangan',
                    'laik_catatan_masaberlaku',
                    'laik_penyesuaian',
                    'laik_penyesuaian_keterangan',
                    'laik_penyesuaian_masaberlaku',
                    'tidak_laik',
                    'tidak_laik_keterangan',
                    'tidak_laik_pilihan',
                    'catatan_wajib_kacamata',
                    'catatan_wajib_alatdengar',
                    'catatan_rekomendasi',
				],
				'safe'
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
            'tgl_periksa' => Yii::t('fe', 'Tanggal Pemeriksaan'),
            'nama' => Yii::t('fe', 'Nama'),
            'no_rm' => Yii::t('fe', 'No Rekam Medik'),
            'usia' => Yii::t('fe', 'Usia'),
            'bagian' => Yii::t('fe', 'Bagian'),
            'catatan' => Yii::t('fe', 'Catatan'),
            'status_kesehatan' => Yii::t('fe', 'Status Kelaikan'),
            'evaluasi' => Yii::t('fe', 'Evaluasi Ulang'),
		];
	}
}
?>