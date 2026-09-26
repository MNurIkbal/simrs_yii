<?php

namespace Doco\api\controllers\penjamin;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use yii\web\UploadedFile;
use app\modules\api\models\DokumenPasienForm;
use app\components\Services\Penjamin\ListPengajuanService;
use app\components\DocoConstants;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class ListPengajuanController extends DocoController
{
    protected $allowAction = ['*'];

    public function actionIndex()
    {
        try {
            $result = (new ListPengajuanService)->execute();
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}
