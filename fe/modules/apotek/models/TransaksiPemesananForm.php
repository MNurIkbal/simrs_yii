<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\apotek\models;

use Yii;

class TransaksiPemesananForm extends \yii\db\ActiveRecord
{
    public $instalasi_tujuan;
    public $ruangan_tujuan;
    public $tanggal_kirim;
    public $obat_alkes;
    public $qty;
    public $satuan;
    public $stok;
    public $instalasi_id;
    public $ruangan_id;
    public $kode_obat;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitobat_mp';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['instalasi_tujuan', 'ruangan_tujuan', 'tanggal_kirim', 'obat_alkes', 'satuan', 'qty'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            ['qty','validQty'],
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
            'satuan' => \Yii::t('fe', 'satuan'),
            'qty' => 'Qty',
            'stok' => \Yii::t('fe', 'Stok Obat Ruangan Pemesan'),
        ];
    }

    public function validQty($attribute, $params)
    {
        $request = Yii::$app->request;
        $stok_available = $request->post('stok');
        $current_id_stok = $request->post('satuankecil_id');
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai);
        $konvSatuan = Yii::$app->cache->get('konvert-satuan');
        $isStart = true;

        $qty = $this->qty;
        if ($qty <= 0) {
            $this->addError('qty', Yii::t('fe', 'Qty tidak boleh 0'));
        }
        if ($qty < 0) {
            $this->addError('qty', Yii::t('fe', 'Qty tidak boleh lebih kecil dari 0'));
        }
        // Konver Qty ke statuan paling kecil
        if (isset($konvSatuan[$this->satuan][$current_id_stok])) {
            $qty = $qty * $konvSatuan[$this->satuan][$current_id_stok];
            $qty = round($qty);
        }
    }
}
