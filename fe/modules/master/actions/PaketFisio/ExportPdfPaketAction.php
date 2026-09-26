<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ExportPdfPaketAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Master Paket Fisioterapi.pdf";
            $this->restMaster->get('paket-fisio/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}
