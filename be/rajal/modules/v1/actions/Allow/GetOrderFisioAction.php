<?php

namespace app\modules\v1\actions\Allow;

use Yii;
use Doco\components\DocoConstansId;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\TarifTotalRsFisioV;

class GetOrderFisioAction extends BaseCurrentAction
{
    private static function getDataPaketFisioOnly($payload = null)
    {
        $ruanganId = ArrayHelper::getValue($payload, 'ruangan_id');
        $penjaminId = ArrayHelper::getValue($payload, 'penjamin_id');
        $kelasPelayananId = ArrayHelper::getValue($payload, 'kelaspelayanan_id');
        $searchByNamaTindakan = ArrayHelper::getValue($payload, 'daftartindakan_nama');
        $queryTarifTotalRsFisio = TarifTotalRsFisioV::find();
        if ($ruanganId) $queryTarifTotalRsFisio = $queryTarifTotalRsFisio->andWhere(['ruangan_id' => $ruanganId]);
        if ($penjaminId) $queryTarifTotalRsFisio = $queryTarifTotalRsFisio->andWhere(['penjamin_id' => $penjaminId]);
        if ($kelasPelayananId) $queryTarifTotalRsFisio = $queryTarifTotalRsFisio->andWhere(['kelaspelayanan_id' => $kelasPelayananId]);
        if ($searchByNamaTindakan) {
            $queryTarifTotalRsFisio->andFilterWhere(
                [
                    'or',
                    ['ilike', 'LOWER(daftartindakan_detail_nama)', strtolower($searchByNamaTindakan)],
                    ['ilike', 'LOWER(daftartindakan_detail_kode)', strtolower($searchByNamaTindakan)]
                ]
            );
        }
        $tarifTotalRsFisio = $queryTarifTotalRsFisio->asArray()->all();
        return $tarifTotalRsFisio;
    }

    private static function removeSameData($tarifTotalRsDatas, $paketFisioOnlyDatas)
    {
        $resultData = $tarifTotalRsDatas;
        foreach ($paketFisioOnlyDatas as $key => $value) {
            $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
            $key = array_search($daftarTindakanId, array_column($tarifTotalRsDatas, 'daftartindakan_id'));
            if (!$key === false) {
                unset($resultData[$key]);
            }
        }
        return $resultData;
    }

    private static function convertPaketFisio($paketFisioDatas)
    {
        $resultDatas = [];
        foreach ($paketFisioDatas as $key => $value) {
            $tindakanId = ArrayHelper::getValue($value, 'daftartindakan_detail_id');
            $tindakanNama = ArrayHelper::getValue($value, 'daftartindakan_detail_nama');
            $tindakanKode = ArrayHelper::getValue($value, 'daftartindakan_detail_kode', 'Kode Dummy gan');
            $parentTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
            $parentTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $value['daftartindakan_id'] = $tindakanId;
            $value['daftartindakan_nama'] = "$tindakanNama";
            $value['kode'] = $tindakanKode;
            $value['jenispemeriksaanlab_id'] = $parentTindakanId;
            $value['jenispemeriksaanlab_nama'] = $parentTindakanNama;
            $value['pemeriksaanlab_id'] = $tindakanId;
            $value['pemeriksaanlab_nama'] = $tindakanNama;
            // New Property After Fisio
            $value['is_paketfisio'] = true;
            $value['parentdaftartindakan_id'] = $parentTindakanId;
            $value['parentdaftartindakan_nama'] = $parentTindakanNama;
            $value['kelompokpemeriksaanlab_id'] = ArrayHelper::getValue($value, 'kelompokpemeriksaanfisio_id');
            $value['nama_kelompok'] = ArrayHelper::getValue($value, 'kelompokpemeriksaanfisio_nama');
            $value['jenispemeriksaanlab_id'] = ArrayHelper::getValue($value, 'jenispemeriksaanfisio_id');
            $value['pemeriksaanlab_id'] = ArrayHelper::getValue($value, 'pemeriksaanfisio_id');
            $value['pemeriksaanlab_nama'] = ArrayHelper::getValue($value, 'pemeriksaanfisio_nama');
            $value['paketfisio_jumlah'] = ArrayHelper::getValue($value, 'jumlah');
            $value['paketfisio_frekuensi'] = ArrayHelper::getValue($value, 'frekuensi');
            $value['is_aktif'] = ArrayHelper::getValue($value, 'is_active');
            $value['is_deleted'] = ArrayHelper::getValue($value, 'is_deleted');
            unset($value['jumlah']);
            unset($value['frekuensi']);
            unset($value['kelompokpemeriksaanfisio_id']);
            unset($value['jenispemeriksaanfisio_id']);
            unset($value['jenispemeriksaanfisio_nama']);
            unset($value['pemeriksaanfisio_id']);
            unset($value['pemeriksaanfisio_nama']);
            unset($value['daftartindakan_detail_id']);
            unset($value['daftartindakan_detail_nama']);
            $resultDatas[] = $value;
        }
        return $resultDatas;
    }

    private static function convertTarifTotalRs($tarifTotalRsDatas)
    {
        $resultDatas = [];
        foreach ($tarifTotalRsDatas as $key => $value) {
            // Normalize Data Tarif Rs with Fisio response
            $value['is_paketfisio'] = false;
            $value['frekuensi'] = null;
            $value['jumlah'] = null;
            $value['parentdaftartindakan_id'] = null;
            $value['parentdaftartindakan_nama'] = null;
            $value['paketfisio_jumlah'] = null;
            $value['paketfisio_frekuensi'] = null;
            $resultDatas[] = $value;
        }
        return $resultDatas;
    }

    private static function filteredBySpecialistAndException($listDaftarTindakan, $payload)
    {
        $fisioSpesialisIds = (new DocoConstansId)->actionGetAdditional('spesialis_fisioterapi_ids', true);
        $payloadSpesialisId = ArrayHelper::getValue($payload, 'spesialis_id');
        $isAllTindakan = in_array($payloadSpesialisId, $fisioSpesialisIds);
        if ($isAllTindakan) return $listDaftarTindakan;
        $listAllowedDaftarTindakanIds = (new DocoConstansId)->actionGetAdditional('show_order_fisioterapi_tindakan_ids', true);
        $newListDaftarTindakan = $listDaftarTindakan;
        foreach ($listDaftarTindakan as $key => $value) {
            $currentDaftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
            $isMustShowTindakan = in_array($currentDaftarTindakanId, $listAllowedDaftarTindakanIds);
            if (!$isMustShowTindakan) {
                unset($newListDaftarTindakan[$key]);
            }
        }
        return $newListDaftarTindakan;
    }

    public static function getFromTarifTotalRsFunc($tarifTotalRsDatas, $payload)
    {
        $paketFisioAllDatas = self::getDataPaketFisioOnly();
        $tarifTotalRsPure = self::removeSameData($tarifTotalRsDatas, $paketFisioAllDatas);
        $tarifTotalRsConverted = self::convertTarifTotalRs($tarifTotalRsPure);
        $paketFisioDatas = self::getDataPaketFisioOnly($payload);
        $paketFisioDatasConverted = self::convertPaketFisio($paketFisioDatas);
        $normalizedData = array_merge($tarifTotalRsConverted, $paketFisioDatasConverted);
        $finalData = self::filteredBySpecialistAndException($normalizedData, $payload);
        return $finalData;
    }
}
