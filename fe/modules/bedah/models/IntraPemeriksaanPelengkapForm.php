<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-14 10:30:22
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-13 14:29:35
 */

namespace Doco\bedah\models;

class IntraPemeriksaanPelengkapForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $nama_jaringan;
    public $qty;
    public $tarif_satuan;
    public $tarif_tindakan;
    public $tarif_cyto;
    public $is_cyto;
    public $instalasi_id;
    public $ruangan_id;
    public $tariftindakan_id;
    public $additional_data;

    public function rules()
    {
        return [
            [[
                'tariftindakan_id',
                'ruangan_id'
            ], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['qty'], 'number', 'message'=>'{attribute} Harus berupa angka'],
            [[
                'pasienmasukpenunjang_id', 
                'inpostoperasi_id',
                'daftartindakan_nama',
                'nama_jaringan',
                'qty',
                'is_cyto', 
                'tarif_cyto',
                'tarif_tindakan',
                'tarif_satuan',
                'instalasi_id',
                'ruangan_id',
                'tariftindakan_id',
                'additional_data',
                'daftartindakan_id',
            ], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'daftartindakan_id'=>\Yii::t('fe', 'Nama tindakan'),
            'tariftindakan_id'=>\Yii::t('fe', 'Nama tindakan'),
            'nama_jaringan'=>\Yii::t('fe', 'Nama jaringan'),
            'qty'=>\Yii::t('fe', 'Ukuran'),
            'instalasi_id'=>\Yii::t('fe', 'Instalasi'),
            'ruangan_id'=>\Yii::t('fe', 'Ruangan'),
        ];
    }
}