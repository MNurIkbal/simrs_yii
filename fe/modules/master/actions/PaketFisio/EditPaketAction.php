<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers; 
use app\modules\master\models\PaketFisioForm;

class EditPaketAction extends BaseCurrentAction
{
    public function run()
    {
        $title = 'Ubah Paket Tindakan Terapi';
        $model = new PaketFisioForm;
        $request = Yii::$app->request;
        $id_encrypt = $request->get('id');
        $id = DocoHelpers::decrypt($id_encrypt);
        $response = Yii::$app->docoRest->master->get("paket-fisio/edit?id=$id");
        $body = json_decode($response->getBody(), true);
        $attributes = ArrayHelper::getValue($body, 'response.listDataPaket');
        $model->parent_id = ArrayHelper::getValue($attributes, 'parent_id');
        $model->kode_paket = ArrayHelper::getValue($attributes, 'daftartindakan_kode');
        $model->nama_paket = ArrayHelper::getValue($attributes, 'daftartindakan_nama');
        $model->namalainya_paket = ArrayHelper::getValue($attributes, 'daftartindakan_namalainnya');
        $model->frekuensi = ArrayHelper::getValue($attributes, 'frekuensi');
        $model->jumlah = ArrayHelper::getValue($attributes, 'jumlah');
        $model->is_active = ArrayHelper::getValue($attributes, 'is_active');
        $model->catatan = ArrayHelper::getValue($attributes, 'catatan');
        $listDetailTindakanPaket = ArrayHelper::getValue($body, 'response.listDetailTindakanPaket');
        return $this->controller->renderAjax('components/paket-fisio/form-edit', get_defined_vars());
    }
}
