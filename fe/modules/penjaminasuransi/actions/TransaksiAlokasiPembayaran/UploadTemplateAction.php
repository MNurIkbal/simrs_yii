<?php

namespace Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;

class UploadTemplateAction extends BaseCurrentAction
{
    public function run()
    {
        try {
            return $this->controller->renderAjax('upload-template', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}
