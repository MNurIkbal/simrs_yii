<?php

/**
* @author yaya
* @dependencis [apotek/informasi-pemakaian-obatalkes, apotek/pemakaian-obat-alkes]
**/

namespace Doco\apotek\models;

use Yii;

class PemakaianObatAlkesForm extends \yii\base\Model
{
    public $tanggal_pemakaian;
    public $instalasi_id;
    public $ruangan_id;
    public $obatalkes_id;
    public $nama_obat_alkes;
    public $qty;
    public $satuan;
    public $satuan_text;
    public $stok;
    public $current_stok;
    public $keterangan_pemakaianobat;

    public $tglpemakaianobat;
    public $nopemakaian_obat;
    public $pemakaianobat_id;
    public $nama_pegawai;

    public $ket_obatpakai;
    
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
                'instalasi_id',
                'ruangan_id',
                'obatalkes_id',
                'satuan',
                'qty'
            ], 'required'],
            ['qty','validQty'],
            [['obatDetail','tglpemakaianobat','nopemakaian_obat','tanggal_pemakaian','pemakaianobat_id', 'satuan_text', 'ket_obatpakai','keterangan_pemakaianobat', 'nama_pegawai'],'safe']
        ];
    }

    public function validQty($attribute, $params)
    {
        if($this->qty <= 0){
            $this->addError('qty',Yii::t('fe','Qty tidak boleh kurang dari 0'));
        }
        $request = Yii::$app->request;
        $stok_available = $request->post('stok');
        $current_id_stok = $request->post('satuankecil_id');
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}");
        $isStart = true;
        $qty = $this->qty;

        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$this->obatalkes_id])) {
                $cacheItem = $cacheObatAlkes[$this->obatalkes_id];
                // ini permintaan qty paling terkcil
                $permintaan = isset($cacheItem['permintaan']) ? $cacheItem['permintaan'] : 0;
                if ($stok_available < ($qty + $permintaan)) {
                    $this->addError('qty',Yii::t('fe','Stok obat tidak mencukupi'));
                }
                $isStart = false;
            }
        }

        if ($isStart) {
            if ($stok_available < $qty) {
                $this->addError('qty',Yii::t('fe','Stok obat tidak mencukupi'));
            }
        }
    }
}