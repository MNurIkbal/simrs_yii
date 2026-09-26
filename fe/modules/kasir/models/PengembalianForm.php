<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-11-05 15:45:36
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-11-06 13:24:40
 */
namespace Doco\kasir\models;

use Yii;

class PengembalianForm extends \yii\base\Model
{
	public $pendaftaran_id;
    public $ruangan_id;
    public $tgl_pengembalian;
    public $total_pengembalian;
    public $biaya_administrasi;
    public $pembulatan;
    public $carapembayaran;
    public $pegawai1_id;
    public $namapemilik_rek;
    public $no_rek;
    public $uang_diterima;
    public $keterangan;

    public function rules()
    {
        return [
            [['total_pengembalian'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['ruangan_id','pegawai1_id','biaya_administrasi','pembulatan','carapembayaran','namapemilik_rek','no_rek','pendaftaran_id','uang_diterima','tgl_pengembalian', 'keterangan'], 'safe'],
            [['total_pengembalian'], 'number'],
            [['carapembayaran'], 'validateCheck']
        ];
    }
    public function attributeLabels()
    {
        return [
            'no_rek'=> Yii::t('fe','Nomor Rekening'),
            'namapemilik_rek'=> Yii::t('fe','Nama Pemilik Rekening'),
            'pasien_id'=> Yii::t('fe','Pasien ID'),
            'pendaftaran_id'=> Yii::t('fe','Pendaftaran ID'),
            'tgl_uangmuka'=> Yii::t('fe','Tanggal Uang Muka'),
            'jumlah_uangmuka'=> Yii::t('fe','Jumlah Uang Muka'),
            'total_tagihan'=> Yii::t('fe','Total Tagihan'),
            'biayaadministrasi'=> Yii::t('fe', 'Biaya administrasi'),
            'jmlpembulatan'=> Yii::t('fe', 'Pembulatan'),
            'uangditerima'=> Yii::t('fe', 'Uang diterima'),

        ];
    }
    public function validateCheck()
    {
        if($this->carapembayaran){
            if(empty($this->no_rek)){
                $this->addError('no_rek', Yii::t('fe','Nomor Rekening').' '.Yii::t('fe','Tidak boleh kosong'));
            }
            if(empty($this->namapemilik_rek)){
                $this->addError('namapemilik_rek', Yii::t('fe','Nama Pemilik Rekening').' '.Yii::t('fe','Tidak boleh kosong'));
            }
        }
    }
}