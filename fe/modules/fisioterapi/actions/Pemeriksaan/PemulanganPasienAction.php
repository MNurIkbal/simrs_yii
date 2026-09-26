<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\modules\rajal\models\PasienPulangForm;

class PemulanganPasienAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $pendaftaranId = ArrayHelper::getValue($request->post(), 'PasienPulangForm.pendaftaran_id');
            $tglpasienpulang = ArrayHelper::getValue($request->post(), 'PasienPulangForm.tglpasienpulang');
            $carakeluar_id = ArrayHelper::getValue($request->post(), 'PasienPulangForm.carakeluar_id');
            $loginPemakaiId = Yii::$app->docoVars->user('loginpemakai_id');
      
            $jsonForm = [
                'pendaftaran_id' => $pendaftaranId,
                'tglpasienpulang' => $tglpasienpulang,
                'carakeluar_id' => $carakeluar_id,
                'loginpemakai_id' => $loginPemakaiId,
            ];
    
            $response = Yii::$app->docoRest->fisioterapi->post('soap/pemulangan-pasien', ['json' => $jsonForm]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        }else{
            $id = $request->get('id', null);
            $pendaftaranId = DocoHelpers::decrypt($id);
            $response = Yii::$app->docoRest->fisioterapi->get('soap/bundle-pasien-pulang', [
                'query' => [
                    'id' => $pendaftaranId,
                ]
            ]);
            $modelpulangpasien = new PasienPulangForm;
            $body = json_decode($response->getBody(), true);
            $data_pendaftaran = $body['response']['data_pendaftaran'];
            $caraKeluar = $body['response']['carakeluar'][0];
            $tglpendaftaran = $data_pendaftaran['tgl_pendaftaran'];
            return $this->controller->renderAjax('pemulangan', get_defined_vars());
        }
    }
}
