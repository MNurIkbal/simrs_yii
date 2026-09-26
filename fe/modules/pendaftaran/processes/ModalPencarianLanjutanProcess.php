<?php
/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class ModalPencarianLanjutanProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        try {
            $request = Yii::$app->request;
            $is_bpjs = $request->get('is_bpjs', '');
            if ($is_bpjs) {
                $is_bpjs = DocoHelpers::decrypt($is_bpjs);
            }
            return $controller->renderAjax('pencarian_lanjutan', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}