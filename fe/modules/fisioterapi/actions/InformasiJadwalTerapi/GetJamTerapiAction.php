<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\InformasiJadwalTerapi;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetJamTerapiAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers;
        $request = Yii::$app->request;
        $tglAwal = $request->get('tgl_penjadwalan_awal');
        $pegawaiId = $request->get('pegawai_id');
        $filter['tglAwal'] = $tglAwal;
        $filter['pegawaiId'] = $pegawaiId;
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'allow/get-jadwal-terapi',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        }
    }
}
