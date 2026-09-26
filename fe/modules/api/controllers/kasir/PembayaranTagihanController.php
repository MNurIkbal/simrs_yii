<?php

namespace Doco\api\controllers\kasir;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use yii\web\UploadedFile;
use app\modules\api\models\DokumenPasienForm;
use app\components\Services\Kasir\CetakDetailInvoiceService;
use app\components\DocoConstants;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class PembayaranTagihanController extends DocoController
{
    protected $allowAction = ['*'];

	public function actionCetakDetailInvoice()
    {
        return (new CetakDetailInvoiceService)->execute();
    }
}