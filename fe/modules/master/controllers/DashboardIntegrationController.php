<?php

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

use app\modules\master\models\PencarianPasienForm;

class DashboardIntegrationController extends DocoController 
{
    protected $_restMaster;
    protected $_restPendaftaran;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
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
        $title = Yii::t('fe', 'Monitoring Integrasi');
        $model = new PencarianPasienForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        if($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post($formName);
            $model->attributes = $post;
            $path = $this->getPath($model->jenis_transaksi);
            return $this->renderAjax($path);
        }
        return $this->render('index', get_defined_vars());
    }

    private function getPath($jenis_transaksi)
    {
        switch ($jenis_transaksi) {
            case 1:
                $path = '_registrasi';
                break;
            case 2:
                $path = '_edit_registrasi';
                break;
            case 3:
                $path = '_batal_registrasi';
                break;

            default:
                $path = false;
                break;
        }

        return $path;
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $type = $request->get('type', null);
        $state = $request->get('state', null);
        try {
            $yiiRestfulParams['model'] = $type;
            $yiiRestfulParams['state'] = $state;
            $response = $this->_restMaster->get('dashboard-integration/get-data-transaksi?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['id']);
                    $value['primary'] = $primaryKey;
                    $value['tgl_pendaftaran'] = date('d-M-y H:i:s', strtotime($value['tgl_pendaftaran']));

                    $value['select_item'] = Html::checkbox('select_item', false, [
                        'id' => 'select_item-'.$primaryKey,
                        'class' => 'select_item',
                        'value' => $value['id'],
                    ]);
                    $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/dashboard-integration/detail-response?id=".$primaryKey.'&model='.$type.'&state='.$state,'onclick'=> 'docoHelper.detail(this)']);
                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetailResponse($id, $model, $state = null)
    {
        $request = $this->_restMaster->request('GET', 'dashboard-integration/detail-response?id='.DocoHelpers::decrypt($id).'&model='.$model.'&state='.$state);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];
        $sync_respon = [
            'payload' => [],
            'response' => []
        ];
        
        if(!empty($attributes)) {
            $sync_respon = [
                'payload' =>  [],
                'response' => json_decode($attributes['additional_data']),
            ];

            $response = json_encode($sync_respon, JSON_PRETTY_PRINT);
        }
        else {
            $response = '<center><h3><strong>Tidak Ada Response</strong><h3></center>';
        }
        
        return $this->renderAjax('_detail', get_defined_vars());
    }

    public function actionResync()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $cache = Yii::$app->cache;
        $status = '';
        $type = $request->get('state', null);
        switch ($request->get('model')) {
            case 'registrasi':
                $status = $cache->get('sync-pendaftaran');
                break;
            case 'edit_regis':
                $status = $cache->get('sync-pendaftaran-'.$type);
                break;
            default:
                $status = $cache->get('sync-pendaftaran');
                break;
        }
        $response = [];
        try {
            $model = ($request->get('model')) ? $request->get('model') : null;
            $syncIdApi = $post['id'];
            $syncIdApi = !is_array($syncIdApi) ? array($syncIdApi) : $syncIdApi;
            $result = $this->_restPendaftaran->post('inf-pasien/resync-transaction?model='.$model, [
                'form_params' => [
                    'id' => $syncIdApi,
                    'type' => $type
                ]
            ]);
            if(!empty($status)) {
                return DocoHelpers::response(false, 422);
            } else {
                return DocoHelpers::response(true);
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }
}