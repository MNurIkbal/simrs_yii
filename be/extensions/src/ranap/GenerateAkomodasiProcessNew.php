<?php
namespace Extensions\ranap;

use Yii;
use app\components\object\AkomodasiAttributes;
use Doco\components\DocoConstants;

class GenerateAkomodasiProcessNew extends \Doco\components\DocoBaseProcessExtension 
{
    public $minJam;
    public $cutOff;
    public $gracePeriode;

    public $tglJamMasuk;
    public $tglJamKeluar;
    public $paramsType;

    protected function getInstance()
    {
        return AkomodasiAttributes::getInstance();
    }

    protected function populateData()
    {
        $service = $this->getInstance();
        $this->tglJamMasuk = $service->tanggalMasuk;
        $this->tglJamKeluar = $service->tanggalKeluar;
        $this->minJam = $service->halfCharge;
        $this->gracePeriode = $service->gracePeriode;
        $this->cutOff = $service->cutOff;
        $this->paramsType =  $service->paramsType;
    }

    protected function processFlow()
    {
        $this->populateData();
        return $this->generateListAkomodasi();
    }

    protected function generateListAkomodasi()
    {  
        $startDate = strtotime($this->tglJamMasuk);
        $endDate = strtotime($this->tglJamKeluar);
        $paramsType = $this->paramsType;
        $is_cron = isset($paramsType['is_cron']) ? $paramsType['is_cron'] : false;
        $type = isset($paramsType['type']) ? $paramsType['type'] : DocoConstants::AKOMODASI_MUTASI;
        $is_pindahkamar = isset($paramsType['is_pindahkamar']) ? $paramsType['is_pindahkamar'] : false;
        $jamCutOff = strtotime(date('Y-m-d '. $this->cutOff, $startDate));
        $realJamCutOff = $jamCutOff = $jamCutOff > $startDate ? $jamCutOff : strtotime('+1 day', $jamCutOff);
        $jamCutOff = $jamCutOff > $endDate ? $endDate : $jamCutOff;
        if($is_cron || $is_pindahkamar || $type == DocoConstants::STOP_TITIPAN){
            $this->gracePeriode = 0;
        }
        $akomodasi = [];
        $no = 0;
        while ($startDate < $endDate) {
            $jamTime = ($jamCutOff - $startDate) / (60 * 60);
            $gracePeriode = $no <= 0 && $type == DocoConstants::STOP_AKOMODASI ? 0 : $this->gracePeriode;
            $no++;
            if($jamTime > $gracePeriode) {
                if($jamTime > $this->minJam || empty($this->minJam)) {
                    $akomodasi[] = [
                        'tanggal' => date('Y-m-d', strtotime('-1 day', $realJamCutOff)),
                        'total_tarif' => '100'
                    ];
                } else {
                    $akomodasi[] = [
                        'tanggal' => date('Y-m-d', strtotime('-1 day', $realJamCutOff)),
                        'total_tarif' => '50'
                    ];
                }
            }

            $startDate = $jamCutOff;
            $realJamCutOff = $jamCutOff = strtotime('+1 day', $jamCutOff);
            $jamCutOff = $jamCutOff > $endDate ? $endDate : $jamCutOff;
        }
        return $akomodasi;
    } 
}