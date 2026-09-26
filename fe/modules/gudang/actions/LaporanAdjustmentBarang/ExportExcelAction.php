<?php

namespace Doco\gudang\actions\LaporanAdjustmentBarang;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $doc_name = $this->setDocName($payload);
        $path = Yii::getAlias("@download") . $doc_name;
        $url = $this->controller->_endpoint . 'export-excel';
        try {
            Yii::$app->docoRest->gudang->get($url, [
                'save_to' => $path,
                'query' => $payload,
            ]);
            return DocoHelpers::response($path);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }

    private function setDocName($params)
    {
        if (isset($params['tgl_adjusmen'])) {
            $exp = explode(' - ', $params['tgl_adjusmen']);
            $tgl_awal = $exp[0];
            $tgl_akhir = $exp[1];
            $tgl_adjustment = "-" . date('d-M-Y', strtotime($tgl_awal)) . " - " . date('dMY', strtotime($tgl_akhir));
        } else {
            $tgl_adjustment = "-" . date('d-M-Y');
        }
        return "/laporan-adjustment-barang" . $tgl_adjustment . ".xlsx";
    }
}
