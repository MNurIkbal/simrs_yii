<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\Services\Contracts\BpjsInterface;
use GuzzleHttp\Exception\RequestException;

class ToolsBpjsController extends DocoController
{
    protected $_title = 'Tools BPJS';
    protected $_restPendaftaran;
    protected $_module = 'tools-bpjs/';
    protected $bpjsService;
    protected $allowAction = ['*'];

    public function __construct($id, $module, $config = [], BpjsInterface $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        parent::__construct($id, $module, $config);

    }

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        return $this->render('index',get_defined_vars());
    }

    private function getCompactData($cid)
    {
        $model = new DynamicModel();
        $model->defineAttribute('noKartu');
        $model->defineAttribute('tglSep');
        $model->defineAttribute('tglPelayanan');
        $model->defineAttribute('jnsPelayanan');
        $model->defineAttribute('keterangan');
        $model->addRule(['noKartu','tglSep','tglPelayanan','jnsPelayanan','keterangan'],'safe');
        return compact('model');
    }

    private function processPost($cid)
    {
        return call_user_func(array($this, $cid));
    }

    public function actionRenderView($_cid)
    {
        if(Yii::$app->request->post()){
            return $this->processPost($_cid);
        }
        return $this->renderAjax('partial/'.$_cid,$this->getCompactData($_cid));
    }

    public function actionGetListfinger()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $draw = $request->get('draw', 1);
        $tglPelayanan = $request->get('tglPelayanan',date('Y-m-d'));
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try{
            $response = $this->_restPendaftaran->get('allow-bpjs/list-peserta-fingerprint', [
                'query' => [
                    'tglPelayanan' => isset($tglPelayanan) ? $tglPelayanan : date('Y-m-d'),
                ],
                'timeout' => 45,
                'connect_timeout' => 45,
            ]);
            $body = json_decode($response->getBody(),TRUE);

            $list = ArrayHelper::getValue($body,'response.response.list');
            if(isset($list) && is_array($list)){
                $result['data'] = $list;
                $result['recordsTotal'] = count($list);
                $result['recordsFiltered'] = count($list);
            }

            return $result;
        } catch (RequestException $e) {
            $result['erroMessage'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['erroMessage'] = $e->getMessage();
            return $result;
        }
    }

    private function _septanpafinger()
    {
        $request = Yii::$app->request;
        $model = new DynamicModel();
        $model->defineAttribute('noKartu');
        $model->defineAttribute('tglSep');
        $model->defineAttribute('keterangan');
        $model->defineAttribute('jnsPelayanan');
        $model->addRule(['noKartu','tglSep','jnsPelayanan','keterangan'],'safe');
        $model->load($request->post());
    
        $response = $this->_restPendaftaran->post('allow-bpjs/pengajuan-sep-manual', [
            'json' => [
                'noKartu' => $model->noKartu,
                'tglSep' => $model->tglSep,
                'jnsPelayanan' => $model->jnsPelayanan,
                'jnsPengajuan' => $request->post('jnsPengajuan','2'),
                'keterangan' => $model->keterangan,
                'user' => Yii::$app->docoVars->user('nama_pegawai')
            ]
        ]);
        $response = json_decode($response->getBody(), TRUE);

        $status = ArrayHelper::getValue($response,'metadata.status');
        $data = ArrayHelper::getValue($response,'response.data');
        if(isset($status) && $status == 422 && is_array($data)){
            return DocoHelpers::response($response, 422, 'DynamicModel');
        }

        $codeBpjs = ArrayHelper::getValue($response,'response.metaData.code');
        $messageBpjs = ArrayHelper::getValue($response,'response.metaData.message');
        if(isset($codeBpjs) && $codeBpjs != 200){
            return DocoHelpers::responseTemplate(
                422,
                'Gagal',
                [],
                [
                    'title' => 'Gagal',
                    'text' => ($messageBpjs) ? : 'Gagal',
                    'message' => ($messageBpjs) ? : 'Gagal',
                ]
            );
        }

        $responseApprove = $this->_restPendaftaran->post('allow-bpjs/approval-fingerprint', [
            'json' => [
                'noKartu' => $model->noKartu,
                'tglSep' => $model->tglSep,
                'jnsPelayanan' => $model->jnsPelayanan,
                'jnsPengajuan' => $request->post('jnsPengajuan','2'),
                'keterangan' => $model->keterangan,
                'user' => Yii::$app->docoVars->user('nama_pegawai')
            ]
        ]);
        $responseApprove = json_decode($responseApprove->getBody(), TRUE);

        $status = ArrayHelper::getValue($responseApprove,'metadata.status');
        $data = ArrayHelper::getValue($responseApprove,'response.data');
        if(isset($status) && $status == 422 && is_array($data)){
            return DocoHelpers::response($responseApprove, 422, 'DynamicModel');
        }

        $codeBpjs = ArrayHelper::getValue($responseApprove,'response.metaData.code');
        $messageBpjs = ArrayHelper::getValue($responseApprove,'response.metaData.message');
        if(isset($codeBpjs) && $codeBpjs != 200){
            return DocoHelpers::responseTemplate(
                422,
                'Gagal',
                [],
                [
                    'title' => 'Gagal',
                    'text' => ($messageBpjs) ? : 'Gagal',
                    'message' => ($messageBpjs) ? : 'Gagal',
                ]
            );
        }

        if(isset($codeBpjs) && $codeBpjs == 200){
            return DocoHelpers::response($responseApprove['response']);
        }

        return DocoHelpers::response($responseApprove);
    }

    private function _cekfingerpeserta()
    {
        $request = Yii::$app->request;
        $model = new DynamicModel();
        $model->defineAttribute('noKartu');
        $model->defineAttribute('tglPelayanan');
        $model->addRule(['noKartu','tglPelayanan'],'safe');
        $model->load($request->post());
    
        $response = $this->_restPendaftaran->get('allow-bpjs/pencarian-fingerprint', [
            'query' => [
                'noKartu' => $model->noKartu,
                'tglPelayanan' => $model->tglPelayanan
            ]
        ]);
        $response = json_decode($response->getBody(), TRUE);

        $status = ArrayHelper::getValue($response,'metadata.status');
        $data = ArrayHelper::getValue($response,'response.data');
        if(isset($status) && $status == 422 && is_array($data)){
            return DocoHelpers::response($response, 422, 'DynamicModel');
        }

        $codeBpjs = ArrayHelper::getValue($response,'response.metaData.code');
        $messageBpjs = ArrayHelper::getValue($response,'response.metaData.message');
        if(isset($codeBpjs) && $codeBpjs != 200){
            return DocoHelpers::responseTemplate(
                422,
                'Gagal',
                [],
                [
                    'title' => 'Gagal',
                    'text' => ($messageBpjs) ? : 'Gagal',
                    'message' => ($messageBpjs) ? : 'Gagal',
                ]
            );
        }

        if(isset($codeBpjs) && $codeBpjs == 200){
            return DocoHelpers::response($response['response']);
        }

        return DocoHelpers::response($response);
    }

}