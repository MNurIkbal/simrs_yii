<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataChartAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $helper = new DocoHelpers;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        if (!$startDate) $startDate = date('m-Y');
        if (!$endDate) $endDate = date('m-Y', strtotime('+1 year'));
        $startDate = "01-$startDate"; // For init date only
        $endDate = "01-$endDate"; // For init date only
        $filter['startDate'] = $startDate;
        $filter['endDate'] = $endDate;
        $no = $request->get('start', 1);
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'laporan-drop-out-pasien/get-data-chart',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            $dataResponse = ArrayHelper::getValue($response, 'data');
            return $dataResponse;
        } catch (RequestException $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        }
    }
}
