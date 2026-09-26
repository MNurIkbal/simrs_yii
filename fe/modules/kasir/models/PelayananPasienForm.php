<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-19 16:48:08
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-22 17:37:54
 */

namespace app\modules\kasir\models;

use Yii;

class PelayananPasienForm extends \yii\base\Model
{
	public $tgl_pendaftaran;
	public $no_pendaftaran;
	public $no_rekam_medis;
	public $nama_pasien;
	public $cara_bayar;
	public $penjamin;
	public $kelas_pelayanan;
	public $jenis_pelayanan;

	public $tgl_pembayaran;
	public $total_tagihan;
	public $uang_muka;
	public $penggunaan_uang_muka;
	public $biaya_administrasi;
	public $pembulatan;
	public $subsidi_asuransi;
	public $uang_diterima;
	public $uang_kembalian;

	public $ecollection;
	public $nama_pemilik_rekening;
	public $nomor_rekening;

	public function rules()
	{
		return [
			[['tgl_pendaftaran','no_pendaftaran','no_rekam_medis','nama_pasien','cara_bayar','penjamin','kelas_pelayanan','jenis_pelayanan'],'required'],
		];
	}

	public function attributeLabels()
	{
		return [
			'tgl_pendaftaran'=>Yii::t('fe','Tanggal pendaftaran'),
			'no_pendaftaran'=>Yii::t('fe','No pendaftaran'),
			'no_rekam_medis'=>Yii::t('fe','No rekam medik'),
			'nama_pasien'=>Yii::t('fe', 'Nama pasien'),
			'cara_bayar'=>Yii::t('fe','Cara bayar'),
			'penjamin'=>Yii::t('fe','Penjamin'),
			'kelas_pelayanan'=>Yii::t('fe','Kelas pelayanan'),
			'jenis_pelayanan'=>Yii::t('fe','Jenis pelayanan'),
			'tgl_pembayaran'=>'Tgl pembayaran',
			'total_tagihan'=>'Total tagihan',
			'uang_muka'=>'Uang muka',
			'penggunaan_uang_muka'=>'Penggunaan uang muka',
			'biaya_administrasi'=>'Biaya administrasi',
			'pembulatan'=>'Pembulatan',
			'subsidi_asuransi'=>'Subsidi asuransi',
			'uang_diterima'=>'Uang diterima',
			'uang_kembalian'=>'Uang kembalian',
			'ecollection'=>'E-Collection',
			'nama_pemilik_rekening'=>'Nama pemilik rekening',
			'nomor_rekening'=>'Nomor rekening',
			// 'tgl_pembayaran'=>Yii::t('fe','Tgl pembayaran'),
			// 'total_tagihan'=>Yii::t('fe','Total tagihan'),
			// 'uang_muka'=>Yii::t('fe','Uang muka'),
			// 'penggunaan_uang_muka'=>Yii::t('fe', 'Penggunaan uang muka'),
			// 'biaya_administrasi'=>Yii::t('fe','Biaya administrasi'),
			// 'pembulatan'=>Yii::t('fe','Pembulatan'),
			// 'subsidi_asuransi'=>Yii::t('fe','Subsidi asuransi'),
			// 'uang_diterima'=>Yii::t('fe','Uang diterima'),
			// 'uang_kembalian'=>Yii::t('fe','Uang kembalian'),
			// 'ecollection'=>'E-Collection',
			// 'nama_pemilik_rekening'=>Yii::t('fe','Nama pemilik rekening'),
			// 'nomor_rekening'=>Yii::t('fe','Nomor rekening'),
		];
	}
}