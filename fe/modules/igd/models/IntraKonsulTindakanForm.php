<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-14 10:59:52
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 10:30:53
 */

namespace Doco\igd\models;

class IntraKonsulTindakanForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $bagian_tubuh;
    public $dokter_id;
    public $dokter_nama;
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $tarif_satuan;
    public $tarif_tindakan;
    public $tarif_cyto;
    public $is_cyto;
    public $alasan;

    public function rules()
    {
        return [
            [['daftartindakan_id', 'dokter_id', 'bagian_tubuh'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id', 'inpostoperasi_id','daftartindakan_nama','dokter_nama', 'alasan','is_cyto', 'tarif_cyto', 'tarif_tindakan', 'tarif_satuan'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'bagian_tubuh'=>\Yii::t('fe', 'Bagian tubuh'),
            'daftartindakan_id'=>\Yii::t('fe', 'Nama tindakan'),
            'dokter_id'=>\Yii::t('fe', 'Nama dokter'),
            'alasan'=>\Yii::t('fe', 'Alasan'),
        ];
    }
}