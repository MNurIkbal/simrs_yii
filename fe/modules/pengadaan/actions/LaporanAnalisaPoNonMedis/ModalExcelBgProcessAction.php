<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class ModalExcelBgProcessAction extends Action {

        public function run() {
                $request = Yii::$app->request;
                $title = 'Excel Laporan Analisa PO Non Medis';
                $randString = DocoHelpers::generateRandomString();
                $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam($request->get());
                if (isset($request->get()['is_prcyto'])) {
                        $yiiRestfulParams['advanced-filter']['po_cito'] = $request->get('is_prcyto');
                }
                if (isset($request->get()['is_admin'])) {
                        $yiiRestfulParams['advanced-filter']['po_admin'] = $request->get('is_admin');
                }
                $yiiRestfulParams['randString'] = $randString;
                Yii::$app->session->setFlash($randString, $yiiRestfulParams);

                return $this->controller->renderAjax('_modal_excel_bg', get_defined_vars());
        }	
}
