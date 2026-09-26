<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\pendaftaran;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\InfPencarianPasienForm;

class InformasiPencarianPasienTera extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Informasi Pasien Tera";

    protected function processFlow($controller)
    {
        $title = Yii::t('fe','List Data Pasien');
        $model = new InfPencarianPasienForm;

        $packData = Yii::$app->docoRest->pendaftaran->get('allow/pack-informasi-pencarian-pasien');
        $body = json_decode($packData->getBody(),TRUE);

        $ddlJenisKelamin = (count($body['response']['lookup']['jenis_kelamin']) > 0) ? ArrayHelper::map($body['response']['lookup']['jenis_kelamin'],'lookup_id','lookup_name') : [];
        $ddlPropinsi = (count($body['response']['data_propinsi']) > 0) ?  ArrayHelper::map($body['response']['data_propinsi'],'propinsi_nama','propinsi_nama') : [];
        return $controller->render('@app/extensions/pendaftaran/views/index', get_defined_vars());
    }

}