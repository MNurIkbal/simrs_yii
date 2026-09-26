<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Components\Repositories;

use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;

class LookUpTransaksiRepositories 
{
    /**
     * @method getTenagaMedisId
     * @return Integer
     */
    public function getTenagaMedisId()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::KELOMPOK_PEGAWAI_TENAGAMEDIS);
    }

    /**
     * @method getInstalasiIdRi
     * @return Integer
     */
    public function getInstalasiIdRi()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::INSTALASI_RI);
    }

    /**
     * @method getInstalasiIdRj
     * @return Integer
     */
    public function getInstalasiIdRj()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::INSTALASI_RJ);
    }

    /**
     * @method getInstalasiIdFisio
     * @return Integer
     */
    public function getInstalasiIdFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::INSTALASI_FISIO);
    }

    /**
     * @method getTindakanKelompokFisio
     * @return Integer
     */
    public static function getTindakanKelompokFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::TINDAKAN_KELOMPOK_FISIO);
    }

    /**
     * @method getTindakanKategoriFisio
     * @return Integer
     */
    public static function getTindakanKategoriFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::TINDAKAN_KATEGORI_FISIO);
    }

    /**
     * @method getStatusBatalProgramFisio
     * @return Integer
     */
    public function getStatusBatalProgramFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::STATUS_BATAL_PROGRAM_FISIO);
    }

    /**
     * @method getStatusBatalProgramFisio
     * @return Integer
     */
    public function getStatusOpenProgramFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::STATUS_OPEN_PROGRAM_FISIO);
    }

    /**
     * @method getKelompokPemeriksaanFisio
     * @return Integer
     */
    public static function getDefaultKelompokPemeriksaanFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::KELOMPOK_PEMERIKSAAN_FISIO_DEFAULT);
    }

    /**
     * @method getStatusCloseFisio
     * @return Integer
     */
    public function getStatusCloseFisio()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::STATUS_CLOSE_PROGRAM_FISIO);
    }

    /**
     * @method getStatusPeriksaBatal
     * @return Integer
     */
    public function getStatusPeriksaBatal()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::STATUS_PERIKSA_BATAL);
    }

    /**
     * @method getStatusPeriksaPulang
     * @return Integer
     */
    public function getStatusPeriksaPulang()
    {
        $docoConstantsId = new DocoConstansId;
        return $docoConstantsId->actionGetId(DocoConstants::LT_STATUS_PERIKSA_PULANG);
    }
}

?>