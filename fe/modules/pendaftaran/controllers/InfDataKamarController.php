<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi keterserdiaan kamar
 * @copyright 14 November 2018 aweutist
 */


namespace Doco\pendaftaran\controllers;

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
    protected $_restPendaftaran;
    protected $_module = 'pendaftaran/inf-data-kamar/';

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;

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
            // $userIdentity = Yii::$app->session->get('user_identity');
            $response = $this->_restPendaftaran->get('inf-data-kamar/index');
            $body = json_decode($response->getBody(), TRUE);
            $list_keterangan = $body['response']['list_keterangan'];
            $list_data_ruangan = $body['response']['list_data_ruangan'];
            $list_kelas_pelayanan = $body['response']['list_kelas_pelayanan'];

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
            $response = $this->_restPendaftaran->request('GET', 'inf-data-kamar/get-data-tempat-tidur?'.http_build_query($yiiRestfulParams));

            $body = json_decode($response->getBody(),TRUE);
            $data_kamar = $body['response']['data_kamar'];
            $no = $request->get('start',1);

            foreach ($data_kamar as $key => $val_kamar) {
                $no++;
                $val_kamar['rowNum'] = $no;
                $val_kamar['data_kamar_ruangan'] = 
                [
                    // 'kamarruangan_id'=>$val_kamar['kamarruangan_id'],
                    'kamarruangan_nokamar'=>$val_kamar['kamarruangan_nokamar'],
                    // 'ruangan_id'=>$val_kamar['ruangan_id'],
                    'kelaspelayanan_id' =>$val_kamar['kelaspelayanan_id']
                ];
                $val_kamar['detail_tempat_tidur'] = $this->parseKamarRuangan(json_decode($val_kamar['detail_kamar_ruangan'],TRUE));
                // dump($val_kamar);exit;
                $data[$key] = $val_kamar;
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
            $response = $this->_restPendaftaran->request('GET', 'inf-data-kamar/detail-kamar-ruangan',['query'=>[
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

                $response = $this->_restPendaftaran->request('GET', 'inf-data-kamar/list-kamar-ruangan',['query'=>[
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
}
