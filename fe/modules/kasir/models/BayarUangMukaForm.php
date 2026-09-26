<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-26 13:45:52
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-06-08 08:50:02
 */

namespace Doco\kasir\models;

use Yii;

class BayarUangMukaForm extends \yii\base\Model
{

    public $no_pendaftaran;
    public $ruangan_id;
    public $pasien_id;
    public $darinama_bkm;
    public $pasienadmisi_id;
    public $pendaftaran_id;
    public $tanggal_pembayaran;
    public $jumlah_uangmuka;
    public $total_tagihan;
    public $carabayar_id;
    public $penjamin_id;
    public $biayaadministrasi;
    public $jmlpembulatan;
    public $uangditerima;
    public $carapembayaran;
    public $pegawai1_id;
    public $namapemilik_rek;
    public $no_rek;
    public $jenisnontunai_id;

    public function rules()
    {
        return [
            [[
                'no_pendaftaran', 
                'jumlah_uangmuka', 
                'tanggal_pembayaran', 
                'biayaadministrasi'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['no_pendaftaran'], 'checkJenis'],
            [[
                'ruangan_id',
                'pasienadmisi_id',
                'pegawai1_id',
                'darinama_bkm',
                'carapembayaran',
                'pegawai1_id',
                'namapemilik_rek',
                'no_rek',
                'pendaftaran_id',
                'no_pendaftaran',
                'jenisnontunai_id',
                'pasien_id'], 'safe'],
            [['total_tagihan'], 'integer'],
            [['jumlah_uangmuka'], 'integer','min'=>1,'tooSmall'=>'{attribute} '.Yii::t('fe','Harus lebih dari 0'),
            'message'=>'{attribute}'.Yii::t('fe',' tidak boleh ada nilai desimal')],

        ];
    }

    public function checkJenis($attributes, $params)
    {
        if (!empty($this->carapembayaran) && empty($this->jenisnontunai_id)) {
            $this->addError('jenisnontunai_id', 'Jenis Non Tunai tidak boleh kosong');
        }
    }

    public function attributeLabels()
    {
        return [
            'tandabuktibayar_id'=> Yii::t('fe','Tanda Bukti Bayar ID'),
            'ruangan_id'=> Yii::t('fe','Ruangan ID'),
            'pasien_id'=> Yii::t('fe','Pasien ID'),
            'pendaftaran_id'=> Yii::t('fe','Pendaftaran ID'),
            'tgl_uangmuka'=> Yii::t('fe','Tanggal Uang Muka'),
            'jumlah_uangmuka'=> Yii::t('fe','Jumlah Uang Muka'),
            'total_tagihan'=> Yii::t('fe','Total Tagihan'),
            'biayaadministrasi'=> Yii::t('fe', 'Biaya administrasi'),
            'jmlpembulatan'=> Yii::t('fe', 'Pembulatan'),
            'uangditerima'=> Yii::t('fe', 'Uang diterima'),
            'jenisnontunai_id'=> Yii::t('fe', 'Jenis Non Tunai'),
        ];
    }
}