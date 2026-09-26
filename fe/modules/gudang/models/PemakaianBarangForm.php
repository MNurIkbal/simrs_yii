<?php

/**
* @author yaya
*
**/

namespace Doco\gudang\models;

use Yii;

class PemakaianBarangForm extends \yii\base\Model
{
    public $tanggal_pemakaian;
    public $instalasi_id;
    public $ruangan_id;
    public $barang_id;
    public $nama_obat_alkes;
    public $qty;
    public $satuan;
    public $stok;
    public $current_stok;
    public $keterangan;
    /**
    * @var array
    **/
    public $obatDetail;

    /**
    * @var array [untuk penampung cache]
    **/
    public $cacheKonversi;

    public function rules()
    {
        return [
            [[
                'tanggal_pemakaian',
                'instalasi_id',
                'ruangan_id',
                'barang_id',
                // 'satuan',
                'qty'
            ], 'required'],
            ['qty','validQty'],
            [['obatDetail','keterangan','satuan'],'safe']
        ];
    }

    public function validQty($attribute, $params)
    {
        $request = Yii::$app->request;
        $stok_available = $request->post('stok');
        $current_id_stok = $request->post('satuankecil_id');
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $uid = Yii::$app->docoVars->user('loginpemakai_id');
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}");
        $konvSatuan = Yii::$app->cache->get('konvert-satuan-barang');
        $isStart = true;
        
        $qty = $this->qty;
        if($qty <= 0){
            $this->addError('qty',Yii::t('fe','Qty tidak boleh kurang dari 0'));
        }
        // Konver Qty ke statuan paling kecil
        if (isset($konvSatuan[$this->barang_id][$current_id_stok])) {
            $qty = $qty * $konvSatuan[$this->barang_id][$current_id_stok];
            $qty = round($qty);
        }

        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$this->barang_id])) {
                $cacheItem = $cacheObatAlkes[$this->barang_id];
                // ini permintaan qty paling terkcil
                $permintaan = isset($cacheItem['permintaan']) ? $cacheItem['permintaan'] : 0;
                if ($stok_available < ($qty + $permintaan)) {
                    $this->addError('qty',Yii::t('fe','Stok barang tidak mencukupi'));
                }
                $isStart = false;
            }
        }

        if ($isStart) {
            if ($stok_available < $qty) {
                $this->addError('qty',Yii::t('fe','Stok barang tidak mencukupi'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tanggal_pemakaian' => Yii::t('fe','Tanggal Pemakaian'),
            'instalasi_id' => Yii::t('fe','Instalasi'),
            'ruangan_id' => Yii::t('fe','Ruangan'),
            'barang_id' => Yii::t('fe','Barang'),
            'satuan' => Yii::t('fe','Satuan'),
            'qty' => Yii::t('fe','Qty'),
        ];
    }
}