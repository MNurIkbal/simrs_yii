<?php

namespace app\modules\pendaftaran\components\traits;

use Yii;

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
