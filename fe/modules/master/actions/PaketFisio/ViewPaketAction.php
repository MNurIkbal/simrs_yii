<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\modules\master\models\TipePaketForm;

class ViewPaketAction extends BaseCurrentAction
{
    public function run()
    {
        $title = 'Ubah Paket Tindakan';
        $model = new TipePaketForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $id_encrypt = $request->get('id');
        $id = DocoHelpers::decrypt($id_encrypt);
        // $response = Yii::$app->docoRest->master->get("tipe-paket/view?id=$id");
        // $body = json_decode($response->getBody(), true);
        $body = null;
        $attributes = ArrayHelper::getValue($body, 'response.data');
        $model->attributes = $attributes;
        $model->tipepaket_id = $id_encrypt;
        $dataMaping = ArrayHelper::getValue($body, 'response.data_maping', []);
        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_tindakan_" . $session_id);
        $tipepaket_id = $id;
        $disable = ($dataMaping) ? true : false;
        return $this->controller->renderAjax('components/paket/form', get_defined_vars());
    }
}
