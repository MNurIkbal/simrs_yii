<?php

namespace app\modules\mcu\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\mcu\models\RiwayatPenyakitForm;

trait RiwayatPenyakitTrait 
{
    public function actionRiwayatPenyakit()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id', null);
        $title = Yii::t('fe', 'Riwayat Pemeriksaan Kesehatan');
        $model = new RiwayatPenyakitForm;
        $listPenyakit = $this->getListRiwayat($pendaftaran_id);
        $defaultLainLain = $listPenyakit['default_lain_lain'];
        $nama_lain_lain = $listPenyakit['nama_lain_lain'];
        $master_riwayat = $listPenyakit['master_riwayat'];
        $data_riwayat = $listPenyakit['data_riwayat'];
        $riwayat = [];
    
        if($model->load($request->post())) {
            $data = $request->post('RiwayatPenyakitForm');
            $model->attributes = $data;
            $model->pendaftaran_id = $pendaftaran_id;
            $request = $this->_restMcu->post('pemeriksaan/save-riwayat', [
                'form_params' => $model->attributes
            ]);
            
            $response = json_decode($request->getBody(), true);
            if(isset($response['response']['data']['RiwayatPenyakitForm[riwayat_lainnya]'])) {
                $response['response']['data']['RiwayatPenyakitForm[riwayat]['.$nama_lain_lain.']'] = $response['response']['data']['RiwayatPenyakitForm[riwayat_lainnya]'];
            }
            
            return DocoHelpers::response($response);
        }
        else {
            $masterPenyakit = $master_riwayat;
            $riwayat_lainnya = '';
            if(!empty($data_riwayat)) {
                $data = [];
                $riwayat = json_decode($data_riwayat['riwayat'], true);
                $riwayat_lainnya = $data_riwayat['riwayat_lainnya'];
                foreach ($master_riwayat as $key => $value) {
                    foreach ($riwayat as $k => $v) {
                        if($value['riwayat_nama'] == $v['nama_riwayat']) {
                            if($v['nama_riwayat'] == $nama_lain_lain) {
                                $flag = $riwayat_lainnya;
                            }
                            else {
                                $flag = ($v['flag'] == true) ? 1 : 0;
                            }
                            $data[] = [
                                'riwayat_id' => $value['riwayat_id'],
                                'riwayat_nama' => $value['riwayat_nama'],
                                'flag' => $flag
                            ];
                        }   
                    }
                }

                $masterPenyakit = $data;
            }
            return $this->renderAjax('__riwayat_penyakit', [
                'pendaftaran_id' => $pendaftaran_id,
                'pasien_id' => $pasien_id,
                'title' => $title,
                'masterPenyakit' => $masterPenyakit,
                'model' => $model,
                'data_riwayat' => $data_riwayat,
                'riwayat' => $riwayat,
                'riwayat_lainnya' => $riwayat_lainnya,
                'default_lain_lain' => $defaultLainLain
            ]);
        }
    }

    private function getListRiwayat($pendaftaran_id)
    {
        $response = $this->_restMcu->get('pemeriksaan/get-list-data?pendaftaran_id='.$pendaftaran_id);
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];

        return $response;
    }
    
}