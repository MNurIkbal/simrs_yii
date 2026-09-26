<?php

/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\models;

use Yii;

class TransaksiPemesananProduksiForm extends \yii\base\Model
{
    public $pemesananproduksiobat_id;
    public $nopemesanan;
    public $tglpemesanan;
    public $instalasi_id;
    public $ruangan_id;
    public $obat_alkes;
    public $satuan;
    public $qty;
    public $catatan_bahanbaku;
    public $pegawai_pemesanan;

    public function rules()
    {
        return [
            [['pemesananproduksiobat_id'], 'integer'],
            [['nopemesanan', 'catatan_bahanbaku', 'pegawai_pemesanan'], 'string'],
            [['tglpemesanan', 'instalasi_id', 'ruangan_id', 'obat_alkes', 'satuan', 'qty'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            ['qty', 'integer', 'min' => 1, 'tooSmall' => 'Qty minimal 1'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pemesananproduksiobat_id' => \Yii::t('fe', 'Pemesanan Produksi Obat ID'),
            'nopemesanan' => \Yii::t('fe', 'Nomor Pemesanan'),
            'tglpemesanan' => \Yii::t('fe', 'Tanggal Pesan'),
            'instalasi_id' => \Yii::t('fe', 'Instalasi Tujuan'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan Tujuan'),
            'obat_alkes' => \Yii::t('fe', 'Obat alkes'),
            'satuan' => \Yii::t('fe', 'Satuan'),
            'qty' => \Yii::t('fe', 'Qty'),
            'catatan_bahanbaku'=> \Yii::t('fe', 'Catatan'),
            'pegawai_pemesanan'=> \Yii::t('fe', 'Nama Pemesan'),
        ];
    }
}
