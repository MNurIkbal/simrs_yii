<?php

namespace SirsCore\features;

use Yii;

use Doco\components\DocoAkunting;
use Doco\components\DocoConstants;

use SirsCore\models\SyncPengeluaranobat;
use SirsCore\models\KonfigSystem;

class IntegrasiAkunting
{
    public static function integrateByNoResep($noresep)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($noresep) {
            $request->sendTo([
                'IntegrateByNoResep' => [
                    'noresep' => $noresep,
                ]
            ]);
        })->execute();
        return $response;
    }

    public static function integrateBmhp($oaId, $instalasiId = null)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($oaId, $instalasiId) {
            $request->sendTo([
                'IntegrateBmhp' => [
                    'oa_id' => $oaId,
                    'instalasi_id' => $instalasiId
                ]
            ]);
        })->execute();
        return $response;
    }

    public static function integrateTindakanBmhp($no_pendaftaran,$instalasi_id)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($no_pendaftaran, $instalasi_id) {
            $request->sendTo([
                'IntegrateTindakan' => [
                    'no_pendaftaran' => $no_pendaftaran,
                    'instalasi_id' => $instalasi_id
                ],
                'IntegrateObatPasien' => [
                    'no_pendaftaran' => $no_pendaftaran,
                    'instalasi_id' => $instalasi_id   
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integrateRevertTindakan($tindakanpelayanan_id, $instalasi_id, $method)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($tindakanpelayanan_id, $instalasi_id, $method) {
            $request->sendTo([
                'IntegrateRevertTindakan' => [
                    'tindakanpelayanan_id' => $tindakanpelayanan_id,
                    'instalasi_id' => $instalasi_id,
                    'crud_method' => $method 
                ]
            ]);
        })->execute();
        return $response;
    }

    public static function integrateRevertBmhp($oaId, $instalasi_id, $method)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($oaId, $instalasi_id, $method) {
            $request->sendTo([
                'IntegrateRevertObatPasien' => [
                    'oaId' => $oaId,
                    'instalasi_id' => $instalasi_id,
                    'crud_method' => $method 
                ]
            ]);
        })->execute();
        return $response;
    }

    public static function integrateRevertByNoResep($noresep, $method)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($noresep, $method) {
            $request->sendTo([
                'IntegrateRevertByNoResep' => [
                    'noresep' => $noresep,
                    'crud_method' => $method 
                ]
            ]);
        })->execute();
        return $response;
    }

    public static function integrateTindakanBmhpPenunjang($pasienmasukpenunjang_id,$instalasi_id)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($pasienmasukpenunjang_id, $instalasi_id) {
            $request->sendTo([
                'IntegrateTindakanPenunjang' => [
                    'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                    'instalasi_id' => $instalasi_id
                ],
                'IntegrateObatPasienPenunjang' => [
                    'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                    'instalasi_id' => $instalasi_id   
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integrateKasir($tindakanObat)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($tindakanObat) {
            $request->sendTo([
                'IntegrateKasir' => [
                    'tindakan_obat' => $tindakanObat 
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integrateKarcisPasien($pendaftaranId)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($pendaftaranId) {
            $request->sendTo([
                'IntegrateKarcisPasien' => [
                    'pendaftaranId' => $pendaftaranId 
                ] 
            ]);
        })->execute();
        return $response;
    }
    
    public static function integratePembayaranUangMuka($uangMukaId)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($uangMukaId) {
            $request->sendTo([
                'IntegratePembayaranUangMuka' => [
                    'uangMukaId' => $uangMukaId 
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function pengembalianUangMuka($pengembalianuangmukaId)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($pengembalianuangmukaId) {
            $request->sendTo([
                'IntegratePengembalianUangMuka' => [
                    'pengembalianuangmukaId' => $pengembalianuangmukaId 
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integratePenerimaanSupplier($noPenerimaan, $jenis)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($noPenerimaan, $jenis) {
            $request->sendTo([
                'IntegratePenerimaanSupplier' => [
                    'noPenerimaan' => $noPenerimaan,
                    'jenis' => $jenis 
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integrateReturSupplier($noRetur, $jenis)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($noRetur, $jenis) {
            $request->sendTo([
                'IntegrateReturSupplier' => [
                    'noRetur' => $noRetur,
                    'jenis' => $jenis
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integrateStokOpname($noStok)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($noStok) {
            $request->sendTo([
                'IntegrateStokOpname' => [
                    'noStok' => $noStok 
                ] 
            ]);
        })->execute();
        return $response;
    }

    public static function integratePengajuanKlaim($klaimId)
    {
        $response = Yii::$app->docoIntegrasi->akuntansi->publish('integerasi', function ($request) use ($klaimId) {
            $request->sendTo([
                'IntegratePenjaminAsuransi' => [
                    'klaimId' => $klaimId 
                ] 
            ]);
        })->execute();
        return $response;
    }
}