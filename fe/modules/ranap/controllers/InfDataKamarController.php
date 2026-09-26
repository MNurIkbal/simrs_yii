<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

namespace Doco\ranap\controllers;

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

class InfDataKamarController extends DocoController
{
    protected $_restRanap;
    protected $_module = 'ranap/inf-data-kamar/';

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;

    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function actionIndex()
    {
        try {
            
            $response = $this->_restRanap->get('inf-data-kamar/index');
            $body = json_decode($response->getBody(), TRUE);
            
            $list_status = $body['response']['list_status'];
            $list_keterangan = $body['response']['list_keterangan'];
            $list_data_ruangan = $body['response']['list_data_ruangan'];
            $list_data_kamar = $body['response']['list_data_kamar'];
            $list_kelas_pelayanan = $body['response']['list_kelas_pelayanan'];
            $list_keterangan_nonisi = !empty($list_keterangan) ? array_filter($list_keterangan, function($item) {
                    return in_array($item['kettempattidur_id'], DocoConstants::STATUS_KAMAR_NON_ISI);
            }) : [];
            
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataTempatTidur()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        
        
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try{
            $type = $request->get('type', null);
            $yiiRestfulParams['advanced-filter']['type'] = $type;
            $response = $this->_restRanap->request('GET', 'inf-data-kamar/get-data-tempat-tidur?'.http_build_query($yiiRestfulParams));

            $body = json_decode($response->getBody(), true);
            $data_kamar = $body['response']['data'];
            $no = $request->get('start',1);

            foreach ($data_kamar as $key => $val_kamar) {
                $no++;
                $val_kamar['rowNum'] = $no;
                // $val_kamar['keterangan'] = "Isi : ".$val_kamar['total_isi']."<br> Kosong : ".$val_kamar['total_kosong'];
                $val_kamar['status_tempat_tidur'] = '';
                $data[] = $val_kamar;
            }
            
            $result['data'] = $data;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataTempatTidurV2()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try{
            $type = $request->get('type', null);
            $yiiRestfulParams['advanced-filter']['type'] = $type;
            $status = $request->get('status', null);
            $yiiRestfulParams['advanced-filter']['status'] = $status;
            
            $response = $this->_restRanap->request('GET', 'inf-data-kamar/get-data-tempat-tidur-v2?'.http_build_query($yiiRestfulParams));

            $body = json_decode($response->getBody(), true);
            $data_kamar = $body['response']['data'];
            $no = $request->get('start',1);
            
            foreach ($data_kamar as $key => $val_kamar) {
                $no++;
                $val_kamar['rowNum'] = $no;
                $val_kamar['keterangan'] = "Isi : ".$val_kamar['total_isi']."<br> Kosong : ".$val_kamar['total_kosong'];
                // $val_kamar['keterangan'] = "";
                $val_kamar['status_tempat_tidur'] = '';
                $data[] = $val_kamar;
            }
            
            $result['data'] = $data;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function parseKamarRuangan($data_kamar)
    {
        $html = '';
        $count= 1;
        foreach ($data_kamar as $val_tempat_tidur) {
            $img_kasur = "";
            if($val_tempat_tidur['f3'] === true){
                $img_kasur = "<i class='fa fa-bed position-left'></i>";
            }
            $html .= "<button type='button' class='btn btn-xs' data-kamartempattidur_id='".@$val_tempat_tidur['f1']."' style='margin-bottom:8px;background-color:".@$val_tempat_tidur['f4']."'>".$img_kasur.@$val_tempat_tidur['f2']."</button> ";
            if($count % 4 ==0){
                $html .= "<br>";
            }
            $count++;
        }
        return $html;
    }

    public function actionDetailKamarRuangan()
    {
        $request = Yii::$app->request;
        $kamarruangan_id = $request->get('kamarruangan_id',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id',0);
        $dataTable = [];
        $info_kamar = null;

        try{
            $response = $this->_restRanap->request('GET', 'inf-data-kamar/detail-kamar-ruangan',['query'=>[
                    'kamarruangan_id' => $kamarruangan_id,
                    'ruangan_id' => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id
                ]
            ]);

            $result = json_decode($response->getBody(),TRUE);
            $dataTable = $result['response']['data_detail'];
            $info_kamar = $result['response']['info_kamar'];
        } catch (RequestException $e) {
            $dataTable = [];
        } catch (\Exception $e) {
            $dataTable = [];
        }

        echo $this->renderPartial('_detail_kamar_ruangan',['dataTable'=>$dataTable,'info_kamar'=>$info_kamar]);
    }

    public function actionGetKamarRuangan()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $kamarruangan_id = $parents[0];

                $response = $this->_restRanap->request('GET', 'inf-data-kamar/list-kamar-ruangan',['query'=>[
                        'kamarruangan_id' => $kamarruangan_id,
                    ]
                ]);
                $response = json_decode($response->getBody(),TRUE);

                foreach ($response['response']['list_kamar'] as $each) {
                    $result[] = ['id'=>$each['kamarruangan_id'],'name'=>$each['kamarruangan_nokamar']];
                }

                $out = $result;
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionSaveUpdateStatus()
    {
        return $this->helper->guzzleExec($this->_restRanap, [
            'url' => 'inf-data-kamar/save-update-status',
            'method' => 'POST',
            'payload' => [
                'form_params' => Yii::$app->request->post()
            ],
            'returnResponse' => true
        ]);
    }
}