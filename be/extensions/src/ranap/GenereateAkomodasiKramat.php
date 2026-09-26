<?php
namespace Extensions\ranap;

class GenereateAkomodasiKramat extends \Doco\processes\GenerateAkomodasiProcess
{
	protected function populateData()
    {
        $service = $this->getInstance();
        $this->tglJamMasuk = $service->tanggalMasuk;
        $this->tglMasuk = date('Y-m-d', strtotime($this->tglJamMasuk));
        $this->tglKeluar = date('Y-m-d', strtotime($service->tanggalKeluar));
        $this->jamMasuk = date('H:i', strtotime($this->tglJamMasuk));
        $this->jamKeluar = date('H:i', strtotime($service->tanggalKeluar));
        $this->minJam = 6;
        $this->gracePeriode = false;
        $this->cutOff = '00:00';
        $this->cutOffEnd = '23:59';
    }

    protected function generateListAkomodasi()
    {
        $daterange = $this->getDatesFromRange($this->tglMasuk,$this->tglKeluar);
        $akomodasi = [];
        $listAkomodasi = [];
        $debug = [];

        if(!empty($daterange)) {
            $no = 1;
            $count = count($daterange);
            $lastElement = end($daterange);
            foreach ($daterange as $key => $value) {
                $tanggal = date('Y-m-d '.$this->cutOffEnd.'', strtotime($value));
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

            foreach ($akomodasi as $value) {
                $tanggalStart = date('Y-m-d '.$this->cutOff.'', strtotime($value['tanggal']));
                $tanggalEnd =date('Y-m-d H:i', strtotime($value['tanggal']));
                if ($no ==1) {
                    $tanggalStart = date('Y-m-d H:i', strtotime($value['tanggal']));   
                    $tanggalEnd = date('Y-m-d '.$this->cutOffEnd.'', strtotime($value['tanggal']));
                }

                $unixTime = strtotime($tanggalStart);
                $unixTimeEnd = strtotime($tanggalEnd);

                $jamAkhir = $unixTime;
                $jamMasuk = $unixTimeEnd;
                $hour = abs($jamAkhir - $jamMasuk)/(60*60);
                if($hour > $this->minJam) {
                    $listAkomodasi[] = [
                        'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                        'total_tarif' => '100'
                    ];
                } else {
                    $listAkomodasi[] = [
                        'tanggal' => date('Y-m-d', strtotime($value['tanggal'])),
                        'total_tarif' => '50'
                    ];
                }
                $no++;
            }
            return $listAkomodasi;
        }
        
        return [];
    }
}