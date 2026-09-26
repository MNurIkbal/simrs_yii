<?php

namespace Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;
use app\components\DocoController;
use Doco\penjaminasuransi\models\PenerimaanPembayaranForm;
use Doco\penjaminasuransi\models\AlokasiPengajuanForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiImportForm;

class ClearDetailsSessionAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $this->clearDetailsSession();
        $oldDataSession = $this->getDetailsSession();
        return DocoHelpers::response('success clear session');
    }
}
