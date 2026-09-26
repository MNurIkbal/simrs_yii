<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 16:09:32
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-10 13:48:59
 */

namespace Doco\igd\models;

class IntraOperasiForm extends \yii\base\Model
{
    public $inpostoperasi_id;
    public $pasienmasukpenunjang_id;
    public $is_surgicalsavety;
    public $dokterbedah_id;
    public $dokteranastesi_id;
    public $masuk_kamar;
    public $mulai_anastesi;
    public $selesai_anastesi;
    public $mulai_operasi;
    public $selesai_operasi;
    public $set_instrumen;
    public $penunjang_khusus_id;
    public $perlengkapan_pribadi;
    public $is_diathermy;
    public $kondisi_kulit_sebelum;
    public $kondisi_kulit_setelah;
    public $posisi_operasi;
    public $kateter_urin;
    public $pencucian_operasi;
    public $posisi_elektroda;
    public $fiksasi_balon;
    public $pemakaian_implan;
    public $lokasi_drainvacum;
    public $lokasi_drainpenrose;
    public $lokasi_drainselang;
    public $is_jaringantubuh;
    public $jenis_jaringan;
    public $is_diserahkan;
    public $penerima;
    public $pegawai_pemberi_id;
    public $is_recovery;

    public function rules()
    {
        return [
            [['inpostoperasi_id','pasienmasukpenunjang_id','is_surgicalsavety', 'dokterbedah_id','dokteranastesi_id','masuk_kamar','mulai_anastesi','selesai_anastesi','mulai_operasi','selesai_operasi','set_instrumen', 'penunjang_khusus_id','perlengkapan_pribadi', 'is_diathermy','kondisi_kulit_sebelum','kondisi_kulit_setelah','posisi_operasi','kateter_urin','pencucian_operasi', 'posisi_elektroda', 'fiksasi_balon', 'pemakaian_implan', 'lokasi_drainvacum', 'lokasi_drainpenrose', 'lokasi_drainselang', 'is_jaringantubuh', 'jenis_jaringan', 'is_diserahkan', 'penerima', 'pegawai_pemberi_id', 'is_recovery'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'is_surgicalsavety'=>\Yii::t('fe', 'Surgical safety checklist'),
            'dokterbedah_id'=>\Yii::t('fe', 'Dokter bedah'),
            'dokteranastesi_id'=>\Yii::t('fe', 'Dokter anestesi'),
            'masuk_kamar'=>\Yii::t('fe', 'Masuk kamar operasi'),
            'mulai_anastesi'=>\Yii::t('fe', 'Mulai anestesi'),
            'selesai_anastesi'=>\Yii::t('fe', 'Selesai anestesi'),
            'mulai_operasi'=>\Yii::t('fe', 'Mulai operasi'),
            'selesai_operasi'=>\Yii::t('fe', 'Selesai operasi'),
            'set_instrumen'=>\Yii::t('fe', 'Set instrumen yang digunakan'),
            'penunjang_khusus_id'=>\Yii::t('fe', 'Penunjang khusus'),
            'perlengkapan_pribadi'=>\Yii::t('fe', 'Perlengkapan pribadi'),
            'is_diathermy'=>\Yii::t('fe', 'Pemasangan diathermy'),
            'kondisi_kulit_sebelum'=>\Yii::t('fe', 'Kondisi kulit sebelum operasi'),
            'kondisi_kulit_setelah'=>\Yii::t('fe', 'Kondisi kulit setelah operasi'),
            'posisi_operasi'=>\Yii::t('fe', 'Posisi operasi'),
            'kateter_urin'=>\Yii::t('fe', 'Kateter urin'),
            'pencucian_operasi'=>\Yii::t('fe', 'Pencucian area operasi'),
            'posisi_elektroda'=>\Yii::t('fe', 'Posisi elektroda'),
            'fiksasi_balon'=>\Yii::t('fe', 'Fiksasi balon'),
            'pemakaian_implan'=>\Yii::t('fe', 'Pemakaian implan'),
            'lokasi_drainvacum'=>\Yii::t('fe', 'Lokasi pemasangan drain vacum'),
            'lokasi_drainpenrose'=>\Yii::t('fe', 'Lokasi pemasangan drain penrose'),
            'lokasi_drainselang'=>\Yii::t('fe', 'Lokasi pemasangan drain selang'),
            'is_jaringantubuh'=>\Yii::t('fe', 'PA jaringan tubuh'),
            'jenis_jaringan'=>\Yii::t('fe', 'Jenis jaringan tubuh'),
            'is_diserahkan'=>\Yii::t('fe', 'Jaringan diserahkan pada keluarga'),
            'penerima'=>\Yii::t('fe', 'Penerima'),
            'pegawai_pemberi_id'=>\Yii::t('fe', 'Diserahkan oleh'),
            'is_recovery'=>\Yii::t('fe', 'Recovery'),
        ];
    }
}