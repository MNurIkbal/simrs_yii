<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\InfPencarianPasienForm;

class InformasiPencarianPasienIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $title = Yii::t('fe','List Data Pasien');
        $model = new InfPencarianPasienForm;
        $userIdentity = Yii::$app->session->get('user_identity');
        $packData = Yii::$app->docoRest->pendaftaran->get('allow/pack-informasi-pencarian-pasien', [
            'query' => [
                'param'=>'index',
                'userIdentity'=>$userIdentity,
            ]
        ]);

        $body = json_decode($packData->getBody(),TRUE);
        $cetakDataPasienButton = $this->cetakDataPasienButton();
        $cetakPdfButton = $this->cetakPdf();
        $cetakExcelButton = $this->cetakExcel();
        $modalPrmjButton = $this->profilRingkasMedisRJButton();
        $isAksesUpdate = $body['response']['isAksesUpdate'];
        $ddlJenisKelamin = (count($body['response']['lookup']['jenis_kelamin']) > 0) ? ArrayHelper::map($body['response']['lookup']['jenis_kelamin'],'lookup_id','lookup_name') : [];
        $ddlPropinsi = (count($body['response']['data_propinsi']) > 0) ?  ArrayHelper::map($body['response']['data_propinsi'],'propinsi_id','propinsi_nama') : [];
        return $controller->render('index', get_defined_vars());
    }

    protected function cetakDataPasienButton()
    {
        return [
            'type' => 'button',
            'title' => \Yii::t('fe', 'Cetak Data Pasien'),
            'icon' => 'fa fa-file-pdf-o',
            'method' => '',
            'attributes' => [
                'id'=>'cetak-pdf',
                'data-options'=>'click',
                'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/cetak-data-pasien?id=',
            ]
        ];
    }

    protected function cetakPdf()
    {
        return [
            'attributes' => [
                'data-target' => Url::home() . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/export-pdf?',
                'class' => 'btn btn-info btn-labeled btn-xs data-pdf btn-toolbar',
            ]
        ];
    }

    protected function cetakExcel()
    {
        return [
            'title' => \Yii::t('fe', 'Unduh Excel'),
            'icon' => 'fa fa-file-excel-o',
            'attributes' => [
                'data-options'=>'click',
                'id' => 'export-excel',
            ]
        ];
    }

    protected function profilRingkasMedisRJButton()
    {
        return [
            'type' => 'button',
            'title' => \Yii::t('fe', 'Profil Ringkas Medis Rawat Jalan'),
            'icon' => 'fa fa-user',
            'attributes' => [
                'id' => 'btn-profil-ringkas-medis-rj',
                'data-options'=>'modal',
                'data-target' => '#modal_backdrop',
                'data-width' => '90%',
                'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/modal-profil-ringkas-medis-rj?pasien_id=',
            ]
        ];
    }
}