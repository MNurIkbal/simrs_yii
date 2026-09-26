<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ShowPopupExcelAction extends Action {
    public function run() {
        $title = 'Export Excel Laporan Summary Stock Opname';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->controller->renderAjax('_modalExcel', get_defined_vars());
    }
}
