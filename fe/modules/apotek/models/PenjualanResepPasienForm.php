<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\models;

use Yii;

class PenjualanResepPasienForm extends \yii\db\ActiveRecord
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasien;
    public $dokter_id;
    public $iter;
    public $pasien_id;
    public $carabayar_id;
    public $ruangan_id;
    public $tglpenjualan;
    public $dokter_resep;
    public $tglresep;
    public $noresep;
    public $totharganetto;
    public $totalhargajual;
    public $carabayar;
    public $penjamin;
    public $instalasi_nama;
    public $ruangan_nama;
    public $catatan;
    public $obatalkes;


    public function rules()
    {
        return [
            /*[['no_resep'], 'required'],
            [['no_resep', 'iter', 'nama_pembeli', 'nama_pasien', 'dokter_resep', 'catatan'], 'safe'],*/

            [['pendaftaran_id', 'no_pendaftaran', 'pasien', 'dokter_id', 'iter', 'pasien_id', 'carabayar_id', 'ruangan_id', 'tglpenjualan', 'dokter_resep', 'tglresep', 'noresep', 'totharganetto', 'totalhargajual', 'carabayar', 'penjamin', 'instalasi_nama', 'ruangan_nama', 'obatalkes'], 'safe']
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran Id'),
            'no_pendaftaran' => Yii::t('fe', 'No. Pendaftaran'),
            'pasien' => Yii::t('fe', 'Pasien'),
            'dokter_id' => Yii::t('fe', 'Dokter Id'),
            'iter' => Yii::t('fe', 'Iter'),
            'pasien_id' => Yii::t('fe', 'Pasien Id'),
            'carabayar_id' => Yii::t('fe', 'Carabayar Id'),
            'ruangan_id' => Yii::t('fe', 'Ruangan Id'),
            'tglpenjualan' => Yii::t('fe', 'Tanggal Penjualan'),
            'dokter_resep' => Yii::t('fe', 'Dokter Resep'),
            'tglresep' => Yii::t('fe', 'Tanggal Resep'),
            'noresep' => Yii::t('fe', 'No. Resep'),
            'totharganetto' => Yii::t('fe', 'Total Harga Netto'),
            'totalhargajual' => Yii::t('fe', 'Total Harga Jual'),
            'carabayar' => Yii::t('fe', 'Cara Bayar'),
            'penjamin' => Yii::t('fe', 'Penjamin'),
            'instalasi_nama' => Yii::t('fe', 'Instalasi Nama'),
            'ruangan_nama' => Yii::t('fe', 'Ruangan Nama'),
            'obatalkes' => Yii::t('fe', 'Nama Obat Alkes'),
        ];
    }
}


