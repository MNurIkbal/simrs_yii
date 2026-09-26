<?php 

namespace app\components\object;

class ImplementBiayaAdm implements BiayaAdmInterface {
    
    public $biayaResep;
    public $totalBiayaAdm;
    public $admPersen;
    public $maxAdm;
    public $listJpk;
    public $total_tagihan;
    
    public function __construct($data)
    {
        $this->mappAttributes($data);
    }

    public function mappAttributes($data)
    {
        $this->biayaResep = isset($data['biayaResep']) ? $data['biayaResep'] : 0;
        $this->totalBiayaAdm = isset($data['totalBiayaAdm']) ? $data['totalBiayaAdm'] : 0;
        $this->admPersen = isset($data['admPersen']) ? $data['admPersen'] : 0;
        $this->maxAdm = isset($data['maxAdm']) ? $data['maxAdm'] : 0;
        $this->listJpk = isset($data['listJpk']) ? $data['listJpk'] : [];
        $this->total_tagihan = isset($data['total_tagihan']) ? $data['total_tagihan'] : [];
    }

    public function getBiayaResep() 
    {
        return $this;
    }
}