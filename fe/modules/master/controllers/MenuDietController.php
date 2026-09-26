<?php
    /*
    * @Author : Iqbal@docotel.com
    * @Date : 2018-11-27 10:56:40
    * @Last Modified by : 
    * @Last Modified time: 2019-02-21 14:31:37
    * @Desc :
    */

namespace Doco\master\controllers;

use Yii;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
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

use app\modules\master\models\MenuDietForm;

class MenuDietController extends DocoController
{
    protected $_title = 'Master menu diet';
    protected $_module = 'master/menu-diet/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    private function getJenisDiet($notIn){
        $response = $this->_restMaster->get('allow/get-list-jenis-diet',[
                'query' => ['not_in' => $notIn]
        ]);
        $resJenisDiet = json_decode($response->getBody(), True)['response']['jenis_diet'];
        $result = isset($resJenisDiet) ? $resJenisDiet : [];

        return $result;
    }

    public function actionIndex()
    {
        $model = new MenuDietForm;
        $notIn = "false";
        $listJenisDiet = $this->getJenisDiet($notIn);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $nomor = $request->get('start',1);
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restMaster->get('menu-diet/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $dataMenuDiet = $body['response']['data'];
            
            if (!empty($dataMenuDiet)) {
                foreach ($dataMenuDiet as $key => $value) {
                    $nomor++;
                    $value['rowNum'] = $nomor;
                    $value['primary'] = DocoHelpers::encrypt($value['jenisdiet_id']);
                    $value['dataCheckBox'] = '';
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['totalCount'];
                $result['recordsFiltered'] = $body['response']['totalCount'];
                // return json_encode($result);
                return DocoHelpers::response($result);
            }else{
                return DocoHelpers::response($result);
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        try {
            $this->layout = false;
            $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', 'Menu diet');
            $model = new MenuDietForm;
            $request = Yii::$app->request;
            $notIn = "true";
            $listJenisDiet = $this->getJenisDiet($notIn);
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $temp = [];
            $listValue = [];
            $disabled = false;

            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post['MenuDietForm'];
                $model->scenario = 'create';
                if ($model->validate()) {
                    $response = $this->_restMaster->post('menu-diet/create-menu-diet',[
                                        'form_params' => $model->attributes
                                ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } else {
                    return DocoHelpers::response($model->errors, 422, $formName);
                }
            } else {
                return $this->renderAjax('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate()
    {
        try {
            $this->layout = false;
            $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', 'Menu diet');
            $request = Yii::$app->request;
            $model = new MenuDietForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $jenisdiet_id = DocoHelpers::decrypt($request->get('jenis_id'));
            $notIn = "false";
            $listJenisDiet = $this->getJenisDiet($notIn);
            $temp = [];

            if ($request->post()) {
                $post = $request->post();
                $model->jenisdiet_id = $post['MenuDietForm']['jenisdiet_id_tmp'];
                $model->attributes = $post['MenuDietForm'];
                $model->scenario = 'edit';
                if ($model->validate()) {
                    $response = $this->_restMaster->post('menu-diet/ubah-menu-diet', [
                                'form_params' => $model->attributes
                            ]);

                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } else {
                    return DocoHelpers::response($model->errors, 422, $formName);
                }
            } else {
                $resMenuDiet = $this->_restMaster->get('menu-diet/get-data-by-jenisdiet?jenisdiet_id='.$jenisdiet_id);
                $data = json_decode($resMenuDiet->getBody(), true);
                $getDataMenuDiet = $data['response']['data'];
                $listValue = [];
                if($getDataMenuDiet){
                    $jenisdiet_id = '';
                    foreach ($getDataMenuDiet as $key => $value) {
                        $temp[$value['makanandiet_id']] = $value['nama_makanan'];
                        $listValue[] = $value['makanandiet_id'];
                        $jenisdiet_id = $value['jenisdiet_id'];
                    }
                    $model->jenisdiet_id = (int)$jenisdiet_id;
                    $model->makanandiet_id = $listValue;
                }
                $disabled = true;
                
                return $this->renderAjax('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/menu-diet.pdf";
        try {
            $response = $this->_restMaster->get('menu-diet/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'menu-diet/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/menu-diet.xlsx";
        try {
            $response = $this->_restMaster->get($url, ['save_to' => $path]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
