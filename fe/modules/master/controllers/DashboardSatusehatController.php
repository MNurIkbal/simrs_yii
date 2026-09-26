<?php

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DHtml;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use app\modules\master\models\SatusehatSinkronisasiForm;

class DashboardSatusehatController extends DocoController
{
    protected $_title = "Dashboard Satu Sehat";
    protected $_module = '/master/dashboard-satusehat';
    // protected $allowAction = ['*'];

    protected $_restKasir;
    protected $_restGudang;
    protected $_restPengadaan;
    protected $_restPendaftaran;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = DHtml::titleMenu($this->_title);
        $response = $this->guzzleExec($this->_restMaster,[
            'url' => 'dashboard-satusehat/get-data-to-filter',
            'method' => 'GET',
            'payload' => [
                'query' => []
            ],
        ]);
        $dataFilterType = ArrayHelper::getValue($response, 'dataFilterType', []);
        $dataFilterState = ArrayHelper::getValue($response, 'dataFilterState', []);
        
        $list_satu_sehat_type = ArrayHelper::map($dataFilterType, 'type', 'type');
        $list_satu_sehat_state = ArrayHelper::map($dataFilterState, 'state', 'state');
        $list_satu_sehat_status = DocoConstants::LIST_SATU_SEHAT_STATUS_INTEGRASI;
         
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
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
        
        $response = $this->guzzleExec($this->_restMaster,[
            'url' => 'dashboard-satusehat/index',
            'method' => 'GET',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
        ]);
        $body = ArrayHelper::getValue($response, 'data', []);
        $meta = ArrayHelper::getValue($response, '_meta', []);
        
        $no = $request->get('start', 1);
        foreach ($body as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'id'));
            $value['primary'] = $primaryKey;
            $value['tgl_sync'] = isset($value['tgl_sync']) ? date("d-M-Y H:i:s", strtotime($value['tgl_sync'])) : '';
            $value['select_item'] = Html::checkbox('select_item', false, [
                'id' => 'select_item-'.$primaryKey,
                'class' => 'select_item',
                'value' => ArrayHelper::getValue($value, 'id'),
            ]);
            $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                'class' => 'btn btn-sm btn-success btnfoo', 
                'data-source'=>"/master/dashboard-satusehat/detail-response?id=".$primaryKey,
                'onclick'=> 'docoHelper.detail(this)'
            ]);
            $value['tgl_resend'] = isset($value['tgl_resend']) ? date("d-M-Y H:i:s", strtotime($value['tgl_resend'])) : '-';
            $value['select_item'] = Html::checkbox('select_item', false, [
                'id' => 'select_item-'.$primaryKey,
                'class' => 'select_item',
                'value' => ArrayHelper::getValue($value, 'id'),
            ]);
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($meta, 'totalCount');
        $result['recordsFiltered'] = ArrayHelper::getValue($meta, 'totalCount');
        return $result;
    }

    public function actionDetailResponse()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $response = $this->guzzleExec($this->_restMaster,[
            'url' => 'dashboard-satusehat/get-data-transaksi',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ],
        ]);
        $detail = ArrayHelper::getValue($response, 'detail', []);
        $payload = ArrayHelper::getValue($detail, 'payload', []);
        $sync_response = ArrayHelper::getValue($detail, 'sync_response', []);

        $sync_respon = [
            'payload' =>  !empty($payload) ? json_decode($payload) : $payload,
            'response' => !empty($sync_response) ? (json_decode($sync_response, true) == NULL ? $sync_response : json_decode($sync_response)) : $sync_response,
        ];
        $response = json_encode($sync_respon, JSON_PRETTY_PRINT);
        
        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionResend()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        return $this->guzzleExec($this->_restMaster,[
            'url' => 'dashboard-satusehat/resend-satusehat',
            'method' => 'POST',
            'payload' => [
                'form_params' =>[
                    'list_id' => ArrayHelper::getValue($post, 'id', []),
                ],
                'query' => [],
            ],
            'returnResponse' => true
        ]);
    }

    public function actionSinkronisasi()
    {
        $title = 'Sinkronisasi Data Master';
        $request = Yii::$app->request;
        $model = new SatusehatSinkronisasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $jenis_sinkronisasi = [
            1 => 'Master Pegawai', 
            2 => 'Master Instalasi', 
            3 => 'Master Ruangan',
            4 => 'Pasien'
        ];
        $list_jumlah_data = $this->guzzleExec($this->_restMaster,[
            'url' => 'dashboard-satusehat/count-data-master',
            'method' => 'GET',
            'payload' => [
                'query' => [],
            ],
        ]);

        if ($request->post()) {
            $model->load($request->post());
            if(!$model->validate()) {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
            $params_url = [
                1 => 'sync-pegawai-satusehat', 
                2 => 'sync-instalasi-satusehat',
                3 => 'sync-ruangan-satusehat', 
                4 => 'sync-pasien-satusehat'
            ];
            $_url = "inf-sinkronisasi/".$params_url[$model->jenis_sinkronisasi];
            return $this->guzzleExec($this->_restMaster, [
                'url' => $_url,
                'payload' => [
                    'query' => [
                        'countData' => isset($list_jumlah_data[$model->jenis_sinkronisasi]) ? $list_jumlah_data[$model->jenis_sinkronisasi] : 0,
                    ]
                ],
                'returnResponse' => true
            ]);
        }
        return $this->renderAjax('_modal_sinkronisasi', get_defined_vars());
    }
}
