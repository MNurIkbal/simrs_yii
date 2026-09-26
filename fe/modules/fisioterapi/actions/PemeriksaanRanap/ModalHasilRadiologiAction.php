<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\fisioterapi\models\SoapForm;
use Doco\fisioterapi\actions\Pemeriksaan\BaseCurrentAction;

class ModalHasilRadiologiAction extends BaseCurrentAction
{
    private function callApi()
    {
        // $restTerapi = Yii::$app->docoRest->fisioterapi->get('informasi-program-fisioterapi/get-program-terapi-detail?id=' . $decrypted_id);
        // $response = json_decode($restTerapi->getBody(), true);
        // $data = $response['response'];
        // return $data;
    }

    public function run()
    {
        try {
            $id = Yii::$app->request->get('id');
            $decrypted_id = DocoHelpers::decrypt($id);
            // $data = $this->callApi();
            return $this->controller->renderAjax('modal-hasil-radiologi', [
                'data1' => 'asdasd'
            ]);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}
