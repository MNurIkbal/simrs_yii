<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 23 Maret 2018 aweutist
 */

namespace Doco\gudang\models;

use Yii;

class TransaksiPemesananForm extends \yii\base\Model
{
    public $instalasi_tujuan;
    public $ruangan_tujuan;
    public $tanggal_kirim;
    public $obat_alkes;
    public $barang;
    public $qty;
    public $satuan;
    public $stok;
    public $instalasi_id;
    public $ruangan_id;

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['instalasi_tujuan', 'ruangan_tujuan', 'tanggal_kirim', 'barang', 'satuan', 'qty'], 'required'],
            [['qty'], 'integer', 'min' => 1],
            [['stok', 'instalasi_tujuan', 'ruangan_tujuan'], 'safe']
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'instalasi_tujuan' => \Yii::t('fe', 'instalasi tujuan'),
            'ruangan_tujuan' => \Yii::t('fe', 'ruangan tujuan'),
            'tanggal_kirim' => \Yii::t('fe', 'Tanggal Pesan'),
            'obat_alkes' => \Yii::t('fe', 'Obat alkes'),
            'barang' => \Yii::t('fe', 'Barang'),
            'satuan' => \Yii::t('fe', 'satuan'),
            'qty' => 'Qty',
        ];
    }

    public function validQty($attribute, $params)
    {
        $request = Yii::$app->request;
        $stok_available = $request->post('stok');
        $current_id_stok = $request->post('satuankecil_id');
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $cacheBarang = Yii::$app->cache->get("pemesanan-barang-" . $id_pegawai);
        $konvSatuan = Yii::$app->cache->get('konvert-satuan');
        $isStart = true;

        $qty = $this->qty;
        // Konver Qty ke statuan paling kecil
        if (isset($konvSatuan[$this->satuan][$current_id_stok])) {
            $qty = $qty * $konvSatuan[$this->satuan][$current_id_stok];
            $qty = round($qty);
        }

        if ($cacheBarang == false) {
            if (isset($cacheBarang[$this->barang])) {
                $cacheItem = $cacheBarang[$this->barang];
                // ini permintaan qty paling terkcil
                $permintaan = isset($cacheItem['permintaan']) ? $cacheItem['permintaan'] : 0;
                if ($stok_available < ($qty + $permintaan)) {
                    $this->addError('qty', Yii::t('fe', 'Stok barang tidak mencukupi'));
                }
                $isStart = false;
            }
        }

        if ($isStart) {
            if ($stok_available < $qty) {
                $this->addError('qty', Yii::t('fe', 'Stok barang tidak mencukupi'));
            }
        }
    }
}


