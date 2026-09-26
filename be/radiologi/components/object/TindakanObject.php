<?php
namespace app\components\object;

/**
 * 
 */
class TindakanObject extends DocoBaseObject
{
    /**
     * @var int
     */
    private $kelaspelayanan_id;

    /**
     * @var int
     */
    private $carabayar_id;

    /**
     * @var int
     */
    private $penjamin_id;

    /**
     * @var int
     */
    private $tanggal;

    /**
     * @var int
     */
    private $daftartindakan_id;

    /**
     * @var int
     */
    private $tipepaket_id;

    public function setTipepaket_id($value)
    {
        $this->tipepaket_id = $value;
    }

    public function setDaftarTindakan_id($value)
    {
        $this->daftartindakan_id = $value;
    }

    public function setTanggal($value)
    {
        $this->tanggal = $value;
    }

    public function setPenjamin_id($value)
    {
        $this->penjamin_id = $value;
    }

    public function setCarabayar_id($value)
    {
        $this->carabayar_id = $value;
    }

    public function setKelaspelayanan_id($value)
    {
        $this->kelaspelayanan_id = $value;
    }

    public function getTipepaket_id()
    {
        return $this->tipepaket_id;
    }

    public function getDaftartindakan_id()
    {
        return $this->daftartindakan_id;
    }

    public function getTanggal()
    {
        return $this->tanggal;
    }

    public function getPenjamin_id()
    {
        return $this->penjamin_id;
    }

    public function getCarabayar_id()
    {
        return $this->carabayar_id;
    }

    public function getKelaspelayanan_id()
    {
        return $this->kelaspelayanan_id;
    }

    public function buildArray()
    {
        return [
            'kelaspelayanan_id' => $this->getKelaspelayanan_id(),
            'carabayar_id' => $this->getCarabayar_id(),
            'penjamin_id' => $this->getPenjamin_id(),
            'tanggal' => $this->getTanggal(),
            'daftartindakan_id' => $this->getDaftartindakan_id(),
            'tipepaket_id' => $this->getTipepaket_id(),
        ];
    }
}