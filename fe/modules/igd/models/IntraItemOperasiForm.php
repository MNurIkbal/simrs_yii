<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 10:26:24
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-21 10:30:53
 */

namespace Doco\igd\models;

class IntraItemOperasiForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $inpostoperasi_id;
    public $operasi_id;
    public $daftartindakan_id;
    public $daftartindakan_nama;
    public $is_cyto;
    public $jenis_luka;
    public $jenis_luka_nama;
    public $golonganoperasi_id;
    public $golonganoperasi_nama;
    public $jenisanastesi_id;
    public $jenisanastesi_nama;
    public $tarif_satuan;
    public $tarif_tindakan;
    public $tarif_cyto;
    public $cyto;
    public $default;

    public function rules()
    {
        return [
            [['daftartindakan_id','jenis_luka','jenisanastesi_id','golonganoperasi_id'], 'required', 'message'=>'{attribute} Tidak Boleh Kosong'],
            [['pasienmasukpenunjang_id','operasi_id', 'inpostoperasi_id','daftartindakan_nama','jenis_luka_nama','golonganoperasi_nama','jenisanastesi_nama','is_cyto','tarif_satuan','tarif_tindakan','tarif_cyto','cyto', 'default'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'daftartindakan_id'=>\Yii::t('fe', 'Nama operasi'),
            'jenis_luka'=>\Yii::t('fe', 'Jenis luka'),
            'jenisanastesi_id'=>\Yii::t('fe', 'Jenis anastesi'),
            'golonganoperasi_id'=>\Yii::t('fe', 'Jenis operasi'),
            'is_cyto'=>\Yii::t('fe', 'Cyto'),
        ];
    }
}