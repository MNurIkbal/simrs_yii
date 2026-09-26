<?php

namespace app\components\object;

class AkomodasiAttributes 
{
    public $tanggalMasuk;

    public $tanggalKeluar;

    public $gracePeriode;
    
    public $cutOff;

    public $halfCharge;

    private static $instance = null;

    public function __construct($tanggalMasuk = null, $tanggalKeluar = null, $cutOff = null, $gracePeriode = false, $halfCharge = null, $paramsType = [])
    {
        $this->tanggalMasuk = $tanggalMasuk;
        $this->tanggalKeluar = $tanggalKeluar;
        $this->cutOff = $cutOff;
        $this->gracePeriode = $gracePeriode;
        $this->halfCharge = $halfCharge;
        $this->paramsType = $paramsType;
    }

    public static function getInstance($tanggalMasuk = null, $tanggalKeluar = null, $cutOff = null, $gracePeriode = false, $halfCharge = null, $resetInstance = false, $paramsType = [])
    {
        if (self::$instance == null || $resetInstance) {
            self::$instance = new AkomodasiAttributes($tanggalMasuk, $tanggalKeluar, $cutOff, $gracePeriode, $halfCharge, $paramsType);
        }

        return self::$instance;
    }
}