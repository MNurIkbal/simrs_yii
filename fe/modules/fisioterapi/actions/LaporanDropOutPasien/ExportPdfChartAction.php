<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoPrintPdf;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportPdfChartAction extends BaseCurrentAction
{
    private function callApi($formData = null)
    {
        $helper = new DocoHelpers;
        $resultApi = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'laporan-drop-out-pasien/export-pdf-chart',
            'method' => 'POST',
            'payload' => [
                'form_params' => $formData
            ]
        ]);
        return $resultApi;
    }

    private function renderPrintFromApi($responseApi)
    {
        $construct = ArrayHelper::getValue($responseApi, '_constract');
        $html = ArrayHelper::getValue($responseApi, '_html');
        $printPdf = new DocoPrintPdf($construct);
        return $printPdf->getBase64Pdf($html);
    }

    private function getFormDataClient()
    {
        $helper = new DocoHelpers;
        $request = Yii::$app->request;
        $start_date = $request->post('startDate');
        $end_date = $request->post('endDate');
        $data = $request->post('data');
        if (!$start_date) $start_date = date('m-Y');
        if (!$end_date) $end_date = date('m-Y', strtotime('+1 year'));
        $start_date = "01-$start_date"; // For init date only
        $end_date = "01-$end_date"; // For init date only
        $img_chart = $request->post('imgChart');
        $formData = compact(
            'start_date',
            'end_date',
            'img_chart',
            'data'
        );
        return $formData;
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $helper = new DocoHelpers;
        try {
            $formData = $this->getFormDataClient();
            $responseApi = $this->callApi($formData);
            return $this->renderPrintFromApi($responseApi);
        } catch (RequestException $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        }
    }
}
