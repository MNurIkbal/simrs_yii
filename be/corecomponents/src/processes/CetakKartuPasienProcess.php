<?php

/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Doco\models\pendaftaran\PasienV;
use Doco\models\KonfigSystem;

class CetakKartuPasienProcess extends \Doco\components\DocoBaseProcessExtension
{
    /**
     * @property string kodeDoc
     */
    protected $kodeDoc;

    /**
     * @property int panjang Max Char Nama
     */
    protected $limitNama;

    /**
     * @property string lebar Barcode
     */
    protected $widthBarcode;

    /**
     * @property bool $hideAlias
     */
    protected $hideAlias;

    protected function init()
    {
        $this->kodeDoc = 'TPP-KARTU';
        $this->limitNama = 20;
        $this->widthBarcode = '100px';
        $this->hideAlias = $this->setAlias();
    }

    /**
    * @controller actionPrintKartuPasien
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien                          
    * @attribute #nama_pasien# => nama pasien
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #barcode# => barcode
    * @attribute #alamat# => Alamat
    **/
	protected function cetak()
	{
        $model = new PasienV;

        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id',null);
        if(!$pasien_id){
            throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
        }
        $model = $model::find()->where(['pasien_id'=>$pasien_id])->one();

        $kode_doc = $this->kodeDoc;
        $tanggal_lahir = isset($model->tanggal_lahir)?date('d M Y', strtotime($model->tanggal_lahir)):'';
        $namaLimit = $this->limitNama;

        $print = new DocoPrint($kode_doc);
        $barcodeGen = new BarcodeGeneratorPNG;
        $barcode = '<img src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($model->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) . '" width="'.$this->widthBarcode.'">';
        
        /** Potong Nama Pasien  */
        $namaPasien = $model->nama_pasien;
        $displayNama = DocoHelpers::cutSentence($namaPasien);
        $nama_depan = isset($model->nama_depan) ? $model->nama_depan : '';
        
        if($this->hideAlias != true) {
            $displayNama = $nama_depan .' '. $displayNama;
        }
        
        $print->attributes = [
            '#no_rekam_medik#' => isset($model->no_rekam_medik)?$model->no_rekam_medik:'',
            '#nama_pasien#' => isset($displayNama)? $displayNama : '',
            '#tanggal_lahir#' => $tanggal_lahir,
            '#barcode#' => $barcode,
            '#alamat#' => isset($model->alamat_pasien) ? $model->alamat_pasien : '',
        ];
        $print->Output();
	}

    public function getKonfigSystem()
    {
        return KonfigSystem::find();
    }

    public function setAlias()
    {
        $konfig = $this->getKonfigSystem()->asArray()->one();
        if(isset($konfig['is_hide_alias']) && $konfig['is_hide_alias'] != true) {
            $this->hideAlias = false;
        } else {
            $this->hideAlias = true;
        }
    }

	protected function processFlow()
    {
        $this->init();
        $this->cetak();
    }
}