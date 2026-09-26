<?php

namespace app\modules\rm\models;

use Yii;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-10 15:10:43
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-24 11:36:09
 * desc: model untuk form penyimpanan dokumen rekam medis
 */


class TransaksiPenyimpananDokumenForm extends \yii\base\Model
{
    public $no_rekam_medik;
    public $no_pengiriman;
    public $status_indexing;
    public $status_assembling;
    public $no_rak;
    public $no_sub_rak;
    public $tgl_akhir_masuk;
    public $dokrm_id;
    public $warnadokrm_id;
    public $tgl_rekam_medik;

    public function rules(){
        return [
            [['status_indexing','status_assembling'], 'required'],	
            [['no_rak','no_sub_rak','tgl_akhir_masuk','dokrm_id','warnadokrm_id','tgl_rekam_medik'],'safe'],					
        ];
    }

    public function attributeLabels(){
        return [
            'no_rekam_medik'=>\Yii::t('fe','No rekam medik'),
            'no_pengiriman'=>\Yii::t('fe','No pengiriman'),
            'status_indexing'=>\Yii::t('fe','Status indexing'),
            'status_assembling'=>\Yii::t('fe','Status assembling'),
            'no_rak'=>\Yii::t('fe','No rak'),
            'no_sub_rak'=>\Yii::t('fe','No sub rak'),
            'tgl_akhir_masuk'=>\Yii::t('fe','Tgl akhir masuk'),
            'dok_rm_id'=>"Dokumen Rekam Medik",
        ];
    }

}