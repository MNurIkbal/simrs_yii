<?php
namespace Doco\processes;

use Yii;
use app\components\object\AkomodasiAttributes;

class GenerateAkomodasiProcess extends \Doco\components\DocoBaseProcessExtension 
{
    public $minJam;
    public $cutOff;
    public $tglJamMasuk;
    public $tglMasuk;
    public $tglKeluar;
    public $jamMasuk;
    public $jamKeluar;
    public $gracePeriode;

    protected function getInstance()
    {
        return AkomodasiAttributes::getInstance();
    }

    protected function populateData()
    {
        $service = $this->getInstance();
        $this->tglJamMasuk = $service->tanggalMasuk;
        $this->tglMasuk = date('Y-m-d', strtotime($this->tglJamMasuk));
        $this->tglKeluar = date('Y-m-d', strtotime($service->tanggalKeluar));
        $this->jamMasuk = date('H:i', strtotime($this->tglJamMasuk));
        $this->jamKeluar = date('H:i', strtotime($service->tanggalKeluar));
        $this->minJam = 6;
        $this->gracePeriode = $service->gracePeriode;
        $this->cutOff = '12:00';
        $this->cutOffEnd = '14:00';
    }

    protected function processFlow()
    {
        $this->populateData();
        return $this->generateListAkomodasi();
    }

    protected function generateListAkomodasi()
    {
        $daterange = $this->getDatesFromRange($this->tglMasuk,$this->tglKeluar);
        $akomodasi = [];

        if(!empty($daterange)) {
            $no = 1;
            $count = count($daterange);
            $lastElement = end($daterange);
            foreach ($daterange as $key => $value) {
                $tanggal = date('Y-m-d H:i', strtotime($value));
                if($key == 0) {
                    $tanggal = date('Y-m-d '.$this->jamMasuk.'', strtotime($value));
                }
                elseif($value == $lastElement) {
                    $tanggal = date('Y-m-d '.$this->jamKeluar.'', strtotime($value));
                }
                
                $akomodasi[] = ['tanggal' => $tanggal];
            }

            $no = 1;
            $count = count($akomodasi);
            $lastDate = $this->tglJamMasuk;
            $listAkomodasi = [];

            foreach ($akomodasi as $value) {
                $tanggalStart = date('Y-m-d '.$this->cutOff.'', strtotime($value['tanggal']));
                $tanggalEnd = date('Y-m-d '.$this->cutOffEnd.'', strtotime($value['tanggal']));
                $unixTmasuk = strtotime($value['tanggal']);

                $unixTime = strtotime($tanggalStart);
                $unixTimeEnd = strtotime($tanggalEnd);
                if ($no == $count) {
                    $date1 = $unixTime;
                    $date2 = strtotime($lastDate);
                    $hour = abs($date1 - $date2)/(60*60);
                    $graceHour = abs($unixTmasuk - $date1)/(60*60);

                    if ($date1 > $date2) {
                        if ($hour < $this->minJam) {
                            if($this->gracePeriode) {
                                if (strtotime('NOW') > $unixTime && strtotime('NOW') < $unixTimeEnd) {
                                    if($graceHour < 2) continue;
                                }
                            }  
                            $listAkomodasi[] = [
                                'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                                'total_tarif' => '50'
                            ];
                        } else {
                          $listAkomodasi[] = [
                            'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                            'total_tarif' => '100'
                          ];      
                        }        
                    }

                    if ($unixTmasuk > $unixTime) {
                        if($this->gracePeriode) {
                            if (strtotime('NOW') > $unixTime && strtotime('NOW') < $unixTimeEnd) {
                                if($graceHour < 2) continue;
                            }
                        } 
                        $listAkomodasi[] = [
                            'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                            'total_tarif' => '50'
                        ];          
                    }
                }
                else if ($no == 1) {
                    $date1 = $unixTime;
                    $date2 = $unixTmasuk;
                    $hour = abs($date1 - $date2)/(60*60);
                    $hour = ceil($hour);

                    if ($date1 > $date2) {
                        if ($hour < $this->minJam) {
                            if($this->gracePeriode) {
                                if (strtotime('NOW') > $unixTime && strtotime('NOW') < $unixTimeEnd) {
                                    if($hour < 2) continue;
                                }
                            }
                            $listAkomodasi[] = [
                                'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                                'total_tarif' => '50'
                            ];      
                        } else {
                          $listAkomodasi[] = [
                            'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                            'total_tarif' => '100'
                          ];      
                        }        
                        
                    }
                }
                else {
                    $listAkomodasi[] = [
                      'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                      'total_tarif' => '100%'
                    ];
                }
                $lastDate = date('Y-m-d 12:00', strtotime($value['tanggal']));
                $no++;
            }

            return $listAkomodasi;
        }
        
        return [];
    }

    protected function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
        $array = array(); 
        $interval = new \DateInterval('P1D'); 
        $realEnd = new \DateTime($end); 
        $realEnd->add($interval); 
        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd); 
        foreach($period as $date) {                  
            $array[] = $date->format($format);  
        } 
      
        return $array; 
    } 
}