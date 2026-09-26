<?php

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class ExportDownloadExcelAction extends Action
{
	public function run()
	{
        $request = Yii::$app->request;
        $restParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/template-kontrak-supplier.xlsx";
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'kontrak-supplier/export-download-excel',
            'method' => 'GET',
            'payload' => [
                'query' => $restParams,
                'save_to' => $path,
            ],
        ]);
        return DocoHelpers::downloadFile($path,true);
	}
}