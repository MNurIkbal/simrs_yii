<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\pendaftaran\Daftar;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class ModalPencarianLanjutanSty extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        try {
            $request = Yii::$app->request;
            $is_bpjs = $request->get('is_bpjs', '');
            if ($is_bpjs) {
                $is_bpjs = DocoHelpers::decrypt($is_bpjs);
            }
            return $controller->renderAjax('@app/extensions/pendaftaran/views/pencarian_lanjutan_sty', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}