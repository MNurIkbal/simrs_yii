<?php
/**
 * @author: arief saputra
 * @description: master layar antrian
**/

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rajal\models\InforinciantagihapasienForm;
use GuzzleHttp\Exception\RequestException;

class InfRincianTagihanPasienController extends DocoController
{
	protected $_title = 'Info Rincian Tagihan Pasien';
    protected $_module = 'inf-rincian-tagihan-pasien/';
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restRajal = Yii::$app->docoRest->rajal;
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
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);

        $CaraBayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
        $body = json_decode($CaraBayarRequest->getBody(),TRUE);
        $listCaraBayar = $body['response'];

        $DokterRajalRequest = $this->_restMaster->get('allow/list-dokter-rajal');
        $body = json_decode($DokterRajalRequest->getBody(),TRUE);
        $listDokterRajal = $body['response'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            $response = $this->_restRajal->request('POST', 'inf-rincian-tagihan-pasien/',[ 'form_params' => [] ]);
            $row = [];
            $body = json_decode($response->getBody(), true);

            // echo "<pre>";
            // print_r($body['response']);
            // echo "</pre>"; exit;

            $no = $request->post('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;

                // $primary = json_encode([$value['daftartindakan_id'],$value['ruangan_id']]);
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;

                unset($value['pendaftaran_id']);
                
                $value['aksi'] = Html::button('<i class="fa fa-pencil" aria-hidden="true"></i>',[
                    'class' => 'btn btn-success btn-xs',
                    'style' => 'margin-right:5px',
                    'data-toggle' => 'modal',
                    'action' => Url::to([$this->_module .'update-pengambilan-antrian','id' => $primaryKey]),
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip",
                    'title' => "Update"
                ]);
                $value['aksi'] .= Html::button('<i class="fa fa-trash" aria-hidden="true"></i>',[
                    'class' => 'btn btn-danger btn-xs delete',
                    'style' => 'margin-right:5px',
                    'data-popup' => "tooltip",
                    'action' => Url::to([$this->_module .'delete-pengambilan-antrian','id' => $primaryKey]),
                    'title' => "Hapus"
                ]);

                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDetail($id)
    {
        $title = Yii::t('fe', "Detail ".$this->_title);
        $request = Yii::$app->request;        
        $id = DocoHelpers::decrypt($id);

        // $model = new TindakanRuanganForm;

        $responseOne = $this->_restRajal->request('GET', 'inf-rincian-tagihan-pasien/view',
            [
                'query' => ['pendaftaran_id' => $id]
            ]
        );

        $data = json_decode($responseOne->getBody(), true);
        $data = $data['response'];

        // $dataPerRuangan = array_unique(array_map(function ($i) { return $i['ruangan_nama']; }, $data));

        $ruangan_id = array();
        foreach ($data as $h) {
            $ruangan_id[$h['instalasi_id']] = $h['ruangan_nama'];
        }
        $dataPerRuangan = array_unique($ruangan_id);

        // $dataPerRuangan = array_map("unserialize", array_unique(array_map("serialize", $data)));
        // $dataPerRuangan = [];

        // foreach ($data as $k=>$subArray) {
        //     foreach ($subArray as $id=>$value) {
        //         $dataPerRuangan[$id]+=$value;
        //     }
        // }
    
        // $data['is_active'] = $data['is_active'];
        // $model->attributes = $data;

        // if($request->post()){
        //     $post = $request->post();
        //     $post['user_id'] = Yii::$app->docoVars->user("id");
        //     $response = $this->_restMaster->request('POST', 'inf-rincian-tagihan-pasien/update', ['form_params'=>$post]);
        //     $body = json_decode($response->getBody(), true);
        //     $return = ['response'=>$body['response']];
        //     return DocoHelpers::response($return);
        // }
		return $this->render('detail', get_defined_vars());
    }
}