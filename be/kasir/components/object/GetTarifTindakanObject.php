<?php

namespace app\components\object;

/**
 * 
 */
use Yii;

class GetTarifTindakanObject
{
    
    protected static $instance = null;
    protected $_listTarif;
    const PERCENT = 100;
    const RACIKAN = 1;

    public function __construct($listCompare)
    {
        $this->_listTarif = $listCompare;
    }

    public static function getInstance($listCompare = [])
    {
        if (self::$instance == null) {
            self::$instance = new GetTarifTindakanObject($listCompare);
        } 
        return self::$instance;
    }

    public function getTarif($type, $id, $qty = 0, $cyto = false, $penyulit = false, $dokterId = null, $racikanId = null, $countRacikan = 0)
    {
        $tarif = 0;
        if (isset($this->_listTarif[$type][$id])) {
            $rowData = isset($this->_listTarif[$type][$id][$dokterId]) ? $this->_listTarif[$type][$id][$dokterId] : $this->_listTarif[$type][$id];
            $hargaSatuan = isset($rowData['harga_tariftindakan']) ? $rowData['harga_tariftindakan'] : 0;
            $percentCyto = isset($rowData['persencyto_tindakan']) ? $rowData['persencyto_tindakan'] / self::PERCENT : 0;
            $percentPenyulit = isset($rowData['persen_penyulit']) ? $rowData['persen_penyulit'] / self::PERCENT : 0;
            $hargaPenyulit = ($penyulit) ? $hargaSatuan * $percentPenyulit : 0;
            // $hargaSatuan+= $penyulit ? $hargaPenyulit : 0;
            $hargaCyto = ($cyto) ? $hargaSatuan * $percentCyto : 0; 
            // $hargaSatuan += $cyto ? $hargaCyto : 0;
            $tarif = ($hargaSatuan * $qty) + $hargaCyto + $hargaPenyulit;

            if (!empty($rowData['obatalkes_id'])) {
                $embalaseRacikan = isset($rowData['embalase_racikan']) ? $rowData['embalase_racikan'] : 0;
                $embalaseNonRacikan = isset($rowData['embalase_nonracikan']) ? $rowData['embalase_nonracikan'] : 0;
                $hargaEmbalase = ($racikanId == self::RACIKAN) ? $embalaseRacikan/$countRacikan : $embalaseNonRacikan/$qty;
                $tarif = ceil($hargaSatuan);
                $tarif = ceil($hargaSatuan + $hargaEmbalase)*$qty;
            }
        }
        return $tarif;
    }
}