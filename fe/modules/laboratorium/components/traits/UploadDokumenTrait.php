<?php

/**
 * @Author: Budi
 */

namespace app\modules\laboratorium\components\traits;

use Yii;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use Doco\laboratorium\models\InputHasilForm;
use Doco\laboratorium\models\UploadForm;
use yii\web\UploadedFile;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;

trait UploadDokumenTrait
{
   public function actionUploadDokumen($id, $pasienadmisi_id=null, $is_modal=null)
   {
        return Yii::$app->runAction('/api/upload-dokumen/tab-upload-dokumen',[
            'id' => $id,
            'pasienadmisi_id' => $pasienadmisi_id,
            'is_modal' => $is_modal
        ]);
   }
}