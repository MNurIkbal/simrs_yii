<?php

namespace app\components\Traits;
use Yii;
use app\components\DocoConstants;

trait HistoryFisioTrait
{
    public function actionListHistoryFisio($pendaftaran_id, $norm)
    {
    	if(!empty($norm) && !empty($pendaftaran_id)) {
				$pendaftaran_id = $this->helper->decrypt($pendaftaran_id);
				$pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
    		$rest = Yii::$app->docoRest->rajal;
    		$url = 'riwayat-pasien/list-history-fisio';
    		switch (Yii::$app->docoVars->workspace('instalasi_id')) {
    			case DocoConstants::INSTALASI_ID_RJ:
    				$rest = Yii::$app->docoRest->rajal;
    				break;
    			case DocoConstants::INSTALASI_ID_RD:
    				$rest = Yii::$app->docoRest->igd;
    				break;
    			case DocoConstants::INSTALASI_ID_RI:
    				$rest = Yii::$app->docoRest->ranap;
    				break;
    		}

    		$response = $rest->get($url, [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
										'pasienadmisi_id' => $pasienadmisi_id,
                    'no_rekam_medik' => $norm,
                ],
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $listData = $body['response']['data_fisioterapi'];
            $infoPasien = $body['response']['data_pasien'];

            return $this->renderAjax('//riwayat-pasien/tindakan/fisio', ['infoPasien' => $infoPasien, 'listData' => $listData]);
    	} else {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
    	}
    }

}