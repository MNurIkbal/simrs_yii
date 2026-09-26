<?php
/**
* @author Narko
* @since
* @param
* @return
* @desc
*/
namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\WarnaDokumenForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class WarnaDokumenController extends DocoController
{
    protected $_title = "Master :: WarnaDokumen";
    protected $_module = 'master/warna-dokumen/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_status = [
            Yii::t('fe', 'Tidak aktif'),
            Yii::t('fe', 'Aktif')
        ];
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
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('warna-dokumen/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['warnadokrm_id']);
                $value['primary'] = $primaryKey;
                unset($value['warnadokrm_id']);
                $value['rowNum'] = $no;
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }


    public function actionCreate()
    {
        try {
            $title = 'Tambah Warna Dokumen';
            $model = new WarnaDokumenForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'warna-dokumen/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false, $formName);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'WarnaDokumenForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $title = 'Ubah Warna Dokumen';
            $model = new WarnaDokumenForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'warna-dokumen/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'WarnaDokumenForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $result = $this->find($id);
                if (isset($result['response'])) {
                    $model->attributes = $result['response'];
                    return $this->renderPartial('form',get_defined_vars());
                } else {
                    return $this->actionCreate();
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function find($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'warna-dokumen/view',
                [
                    'query' => ['id' => $id ]
                ]
            );
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionDelete($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restMaster->request('POST', 'warna-dokumen/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus',
                'return'=>'tabel.reload()'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }


    public function actionPageWarnaDokumen() {
        $status = $this->_status;

        $warnaRequest = $this->_restMaster->get('warna-dokumen/list-warna');
        $warnaRequest = json_decode($warnaRequest->getBody(),TRUE);
        $warnaRequest = $warnaRequest['response']['data'];
        $warnaDokrm = ArrayHelper::map($warnaRequest,'warnadokrm_namawarna','warnadokrm_namawarna');
        return $this->renderPartial('_warnaDokumen', ['status' => $status,'warnaDokrm'=>$warnaDokrm]);
    }

}
