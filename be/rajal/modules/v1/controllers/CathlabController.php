<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\Traits\CathlabTrait;

class CathlabController extends DocoActiveController
{
    use CathlabTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['create'] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    /**
     * @controller actionCetakCathlabPdf
     * @attribute #tipe# => tipe
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #tgl_cetak# => tgl_cetak
     * @attribute #nama_user# => nama_user
     * @attribute #table_cathlab# => table
     * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
     * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
     * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
     * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
     * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
     * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
     * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
     * @attribute #inf_umur# => Informasi Pasien: Umur
     * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
     * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
     * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
     * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
     * @attribute #inf_ruangan# => Informasi Pasien: Ruangan
     **/
    public function actionCetakCathlabPdf()
    {
        $instalasi = 'rajal';
        return $this->printCathlabPdf($instalasi);
    }
}
