<?php

namespace Doco\processes;

use Yii;
use Doco\components\DocoPrint;
use Doco\models\InfKunjunganRsView;
use Da\QrCode\QrCode;
use Doco\components\DocoHelpers;
class PrintGelangAnakProcess extends \Doco\components\DocoBaseProcessExtension
{
    const BIN = 'bin';
    public $pasien_id;
    public $pendaftaran_id;
    public $no_pendaftaran;

    /**
     * Populate Data
     * @return void
     */
    protected function populateData()
    {
        $request = $this->_requestData;
        $this->pasien_id = $request->post('pasien_id', null);
        $this->pendaftaran_id = $request->post('pendaftaran_id', null);
        $this->no_pendaftaran = $request->post('no_pendaftaran', null);
    }

    /**
     * @return array
     */
    protected function getDataPasien()
    {
        $model = new InfKunjunganRsView;
        if(!$this->pasien_id){
            throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
        }
        if(!$this->pendaftaran_id){
            throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
        }
        $kunjungan = $model::find()
            ->where(['pasien_id'=>$this->pasien_id,'pendaftaran_id'=>$this->pendaftaran_id])
            ->asArray()
            ->orderBy('tgl_pendaftaran DESC')->one();
        if(!$kunjungan){
            throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
        }
        return $kunjungan;
    }

    
    /**
     * @return string
     */
    protected function generateQr($no_rekam_medik)
    {
        $qrCode = (new QrCode($no_rekam_medik))
        ->setSize(35)
        ->setMargin(1)
        ->useForegroundColor(0, 0, 0);

        return '<img src="data:image/png;base64,' . base64_encode($qrCode->writeString()) . '">';
    }

    protected function processFlow()
    {
        $this->populateData();
        $kunjungan = $this->getDataPasien();
        $no_rekam_medik = isset($kunjungan['no_rekam_medik']) ? $kunjungan['no_rekam_medik'] : '-';

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekam_medik#' => $no_rekam_medik,
            '#nama_pasien#' => isset($kunjungan['nama_pasien']) ? DocoHelpers::cutSentence($kunjungan['nama_pasien'], 40) : '',
            '#tanggal_lahir#' => isset($kunjungan['tanggal_lahir'])
                ?date('d M Y', strtotime($kunjungan['tanggal_lahir']))
                :'',
            '#jenis_kelamin#' => isset($kunjungan['jenis_kelamin'])
                ? $kunjungan['jenis_kelamin']
                :'',
            '#nama_depan#' => isset($kunjungan['namadepan']) ? $kunjungan['namadepan'] : '',
            '#penjamin_nama#' => isset($kunjungan['penjamin_nama']) ? $kunjungan['penjamin_nama'] : '',
            '#umur#' => isset($kunjungan['umur']) ? $kunjungan['umur'] : '',
            '#qrcode#' => $this->generateQr($no_rekam_medik),
        ];
        $print->SetJs('this.print();');
        $print->Output();
    }

}