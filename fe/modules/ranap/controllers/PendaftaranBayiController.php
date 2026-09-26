<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-04 16:30:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-18 10:47:25
 */

namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use yii\helpers\Json;

use app\modules\ranap\models\PasienPendaftaran;

class PendaftaranBayiController extends DocoController
{
    protected $_title = 'Pendaftaran Bayi Baru Lahir';
    protected $_module = '/ranap/';
    protected $_moduleRedirect = '/ranap/';
    // protected $_restMaster;
    protected $_restRanap;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
    }

    public function actionIndex(){
        $title = Yii::t('fe', $this->_title);
        $modelPasien = new PasienPendaftaran;
        return $this->render('index', get_defined_vars());
    }
    /*
    url: cari-rm-ibu
    author: rizqi-ftn
    deskripsi: fungsi untuk cari no rm ibu
    result: 
    */
    public function actionCariRmIbu(){
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page-1)*5;
        try {
            $result = $this->_restRanap->get('pendaftaran-bayi/cari-rm-ibu',[
                'query' => [
                    'term' => $request->get('term',null),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pendaftaran_id'],
                    'text' => $value['no_rekam_medik'].' - '.$value['nama_pasien'],
                ];
            }
        } catch (RequestException $e) {
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ]
        ]);
    }
    public function actionDataPasien($id = null)
    {
        // try {
        //     $response = $this->_restRanap->get('pendaftaran-bayi/pack-pendaftaran-bayi', [
        //         'query' => [
        //             'id' => $id,
        //         ]
        //     ]);
        //     $result = json_decode($response->getBody(), true);
        // } catch (Exception $e) {
        //     $result = [];
        // }
        

        return $this->renderAjax('_datapasien', get_defined_vars());
    }
}
