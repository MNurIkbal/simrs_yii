<?php

namespace Doco\api\controllers;

use app\components\DocoController;
use app\components\Services\Master\PenjaminService;
use app\components\Services\Master\PenjaminDepDropService;
use app\components\Services\Master\CaraBayarService;
use app\components\Services\Master\CaraBayarDepDropService;
use app\components\Services\Master\PegawaiService;
use app\components\Services\Master\KamarRanapService;
use app\components\Services\Master\KamarRanapDepService;
use app\components\Services\Master\PemeriksaanDepService;
use app\components\Services\Master\TenagaMedisService;
use app\components\Services\Master\RuanganRanapService;
use app\components\Services\Master\RuanganByInstalasiDepService;
use app\components\Services\Master\RuanganRanapDepService;
use app\components\Services\Master\TempatTidurRanapService;
use app\components\Services\Master\TempatTidurRanapDepService;
use app\components\Services\Master\SatuanKonversiService;
use app\components\Services\Master\BarangService;
use app\components\Services\Master\ObatSoService;
use app\components\Services\Master\MasterObatService;
use app\components\Services\Gudang\KonfigFarmasiService;

class MasterController extends DocoController
{
    protected $allowAction = ['*'];

    public function actionGetCaraBayar()
    {
        return (new CaraBayarService)->execute();
    }

    public function actionGetCaraBayarDepDrop()
    {
        return (new CaraBayarDepDropService)->execute();
    }

    public function actionGetPenjamin()
    {
        return (new PenjaminService)->execute();
    }

    public function actionGetPenjaminDepDrop()
    {
        return (new PenjaminDepDropService)->execute();
    }

    public function actionGetListDataPegawai()
    {
        return (new PegawaiService)->execute();
    }

    public function actionGetKamarRanap()
    {
        return (new KamarRanapService)->execute();
    }

    public function actionGetKamarRanapDep()
    {
        return (new KamarRanapDepService)->execute();
    }

    public function actionGetRuanganRanap()
    {
        return (new RuanganRanapService)->execute();
    }

    public function actionGetRuanganByInstalasiDep()
    {
        return (new RuanganByInstalasiDepService)->execute();
    }

    public function actionGetRuanganRanapDep()
    {
        return (new RuanganRanapDepService)->execute();
    }

    public function actionGetTempatTidurRanap()
    {
        return (new TempatTidurRanapService)->execute();
    }

    public function actionGetTempatTidurRanapDep()
    {
        return (new TempatTidurRanapDepService)->execute();
    }

    public function actionGetPemeriksaanFisioterapiDep()
    {
        return (new PemeriksaanDepService)->execute();
    }

    public function actionGetTenagaMedis()
    {
        return (new TenagaMedisService)->execute();
    }

    public function actionGetSatuanKonversi($satuankonversi_id = null)
    {
        return (new SatuanKonversiService)->execute($satuankonversi_id);
    }
    
    public function actionGetBarang()
    {
        return (new BarangService)->execute();
    }

    public function actionGetSoObat()
    {
        return (new ObatSoService)->execute();
    }

    public function actionGetMasterObat()
    {
        return (new MasterObatService)->execute();
    }

    public function actionClearCache()
    {
        return (new KonfigFarmasiService)->execute();
    }
}
