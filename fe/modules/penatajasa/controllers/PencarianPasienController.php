<?php

/**
 * @author: rizqi@docotel.com
 * @description: default dashboard penata jasa
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
**/

namespace Doco\penatajasa\controllers;

use app\components\DocoConstants;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\penatajasa\models\PencarianPasienForm;
use yii\helpers\ArrayHelper;

class PencarianPasienController extends DocoController
{
    protected $_title = "Pencarian Pasien";
    protected $_module = '/penata-jasa/pencarian-pasien';

    protected $_restPenatajasa;

    public function init()
    {
        parent::init();
        $this->_restPenatajasa = Yii::$app->docoRest->penatajasa;
    }

    public function actionIndex()
    {
        $title = Yii::t('fe', 'Pencarian Pasien');
        $model = new PencarianPasienForm;
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        if($request->post()){
            // Get data pencarian terakhir
            $pencarian = $request->post('PencarianPasienForm');
            $session->set('form-pencarian', $pencarian);
            $model->load($request->post());
            $model->no_rekam_medik = is_null($model->no_rekam_medik) ? $model->no_rekam_medik : trim($model->no_rekam_medik);
            $model->no_pendaftaran = is_null($model->no_pendaftaran) ? $model->no_pendaftaran : trim($model->no_pendaftaran);
            $data_pasien = $list_kunjungan = [];
            try {
                $request = $this->_restPenatajasa->get('informasi-kunjungan-pasien/index', [
                    'query' => $model->attributes
                ]);
                $body = json_decode($request->getBody(), true);
                $data_pasien = isset($body['response']['pasien']) ? $body['response']['pasien'] : [];
                $list_kunjungan = isset($body['response']['list_kunjungan']) ? $body['response']['list_kunjungan'] : [];
            } catch (\RequestException $e) {
                $data_pasien = $list_kunjungan = [];
            } catch(\Exception $e){
                $data_pasien = $list_kunjungan = [];
            }
            if(count($data_pasien) < 1){
                return DocoHelpers::response(['message' => 'Data Tidak Ditemukan!'], 500);
            }

            return $this->renderAjax('_detailpasien',[
                'data_pasien' => $data_pasien,
                'list_kunjungan' => $list_kunjungan,
            ]);
        }
        $formPencarian = $session->get('form-pencarian');
        return $this->render('index', get_defined_vars());
    }
}