<?php
namespace Doco\ranap\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\ranap\models\RencanaPulangForm;
use app\modules\ranap\models\RencanaPulangDetailForm;

class DashboardController extends DocoController
{
    protected $_title = "Dashboard";
    protected $_module = '/ranap/dashboard';
    protected $_restMaster;
    protected $_restRanap;
    protected $_restPendaftaran;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $title = 'Discharge Planning';
        $model = new RencanaPulangForm;
        $modelDetail = new RencanaPulangDetailForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        // try {
            if($model->load(Yii::$app->request->post())) {
                echo 'sini'; die;
                $model->pendaftaran_id = 1;
                $model->pasienadmisi_id = 1;
                
                $modelDetail->load(Yii::$app->request->post());
                $postDetail = Yii::$app->request->post('RencanaPulangDetailForm');
                $postLainnya = Yii::$app->request->post('lainnya');
                // dump($postDetail);die;
                if(isset($postDetail['edukasi_kesehatan'])) {
                    $parent = $postDetail['edukasi_kesehatan'];
                    foreach ($parent as $key => $value) {
                        if($postDetail['pemberi_edukasi'][$key] == "") {
                            DocoHelpers::multipleParseError($modelDetail,'Pemberi Edukasi Harus di isi','pemberi_edukasi', $key);
                        }
                        if($postDetail['ppa'][$key] == "") {
                            DocoHelpers::multipleParseError($modelDetail,'PPA Harus di isi','ppa', $key);
                        }
                        if($postDetail['tgl_edukasi'][$key] == "") {
                            DocoHelpers::multipleParseError($modelDetail,'Tanggal Edukasi Harus di isi','tgl_edukasi', $key);
                        }
                    }
                }

                $responseError = DocoHelpers::response($modelDetail->errors, 422, 'RencanaPulangDetailForm');
                if($model->validate() && empty($responseError['response']['data'])) {
                    $response = $this->_restRanap->post('discharge-planning/create', [
                        'form_params' => [
                            'rencana_pulang' => $model->attributes,
                            'rencana_pulang_detail' => $postDetail,
                            'lainnya' => $postLainnya,
                        ]
                    ]); 

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                }
                else {
                    return $responseError;
                }
            }
            else {
                $pasienadmisi_id = 1;
                $ruangan_id = (Yii::$app->docoVars->workspace("ruangan_id") == "-") ? 1 : Yii::$app->docoVars->workspace("ruangan_id");
                $response = $this->_restRanap->get('discharge-planning/get-request', ['query' => [
                    'type' => 'edukasi_kesehatan',
                    'ruangan_id' => $ruangan_id,
                    'pasienadmisi_id' => $pasienadmisi_id
                ]]);

                $body = json_decode($response->getBody(), TRUE);
                $response = $body['response'];
                
                if($response['header']) {
                    $model->attributes = $response['header'];
                    $model->rencanapulang_id = $response['header']['rencanapulang_id'];
                }

                if($response['detail'] != '') {
                    $modelDetail->attributes = $response['detail'];
                    foreach ($response['detail'] as $key => $value) {
                        if($value['edukasi_kesehatan'] == 31) {
                            $modelDetail->edukasi_kesehatan[$value['edukasi_kesehatan']][] = (int) $value['edukasi_kesehatan'];
                            $modelDetail->pemberi_edukasi[$value['edukasi_kesehatan']][] = $value['pemberi_edukasi'];
                            $modelDetail->tgl_edukasi[$value['edukasi_kesehatan']][] = $value['tgl_edukasi'];
                            $modelDetail->ppa[$value['edukasi_kesehatan']][] = $value['ppa'];
                        }
                        else {
                            $modelDetail->edukasi_kesehatan[$value['edukasi_kesehatan']] = (int) $value['edukasi_kesehatan'];
                            $modelDetail->pemberi_edukasi[$value['edukasi_kesehatan']] = $value['pemberi_edukasi'];
                            $modelDetail->tgl_edukasi[$value['edukasi_kesehatan']] = $value['tgl_edukasi'];
                            $modelDetail->ppa[$value['edukasi_kesehatan']] = $value['ppa'];                            
                        }
                    }
                }
                
                
                // dump($modelDetail);die;
                return $this->render('index', get_defined_vars());
            }
        // } catch (RequestException $e) {
        //     return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        // } catch (\Exception $e) {
        //     return DocoHelpers::responseTemplate(500, $e->getMessage());
        // }
    }

    private function isDokterDpjp()
    {

    }

}