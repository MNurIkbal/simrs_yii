<?php

/**
 * @Author: afil
 * @Date:   2018-01-22 14:01:25
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 13:11:11
 * @Description: 
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rajal\models\PaketBmhpForm;

class PaketBmhpController extends DocoController
{
    protected $_title = "Paket BMHP";
    protected $_module = 'rajal/paket-bmhp/';
    protected $_page;
    protected $_restRajal;
    protected $_id_ruangan;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_page = Yii::t('fe', 'Paket bmhp');
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
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRajal->get('paket-bmhp/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $data_body = $body['response']['data'];

            $no = 1;
            $tindakan_pivot = $data_body[0]['daftartindakan_id'];

            foreach ($data_body as $key => $value) {
                $primaryKey_tindakan = DocoHelpers::encrypt($value['daftartindakan_id']);
                $primaryKey_paketbmhp = DocoHelpers::encrypt($value['paketbmhp_id']);

                if ($tindakan_pivot != $value['daftartindakan_id']){
                    $no++;
                    $tindakan_pivot = $value['daftartindakan_id'];
                }

                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey_tindakan;
                unset($value['daftartindakan_id']);
                unset($value['paketbmhp_id']);

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $status = $this->_status; 
        $options = $this->_options;
        $default_url = Url::home().(Yii::$app->controller->module->id."/".Yii::$app->controller->id);

        return $this->render('index', get_defined_vars());
    }

    public function actionUpdate($id = null)
    {
        // Init
        $status = $this->_status; $options = $this->_options;

        // data select
        $list_alkes = $this->getObatAlkes();
        $list_tindakan = $this->getTindakan();

        $request = Yii::$app->request;
        $title = \Yii::t('fe', $this->_title);
        $action = \Yii::t('fe', 'Ubah');
        $modelPaketBmhp = new PaketBmhpForm;
        $formName = substr(strrchr(get_class($modelPaketBmhp), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        try{
            if ($request->post()) {
                if ($data = $request->post('PaketBmhpForm')){
                    try {
                        $response = $this->_restRajal->post('paket-bmhp/create', [
                                'form_params' => $data
                            ]);
                        $response = json_decode($response->getBody(), true);

                        return DocoHelpers::response($response, false, true);
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                }
            } else {
                $response = $this->_restRajal->get('paket-bmhp/view?id='.$id);
                $body = json_decode($response->getBody(), TRUE);
                $attributes = $body['response'];
                $modelPaketBmhp->attributes = $attributes;
                return $this->renderAjax('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionCreate()
    {
        // Init
        $status = $this->_status; $options = $this->_options;

        // data select
        $list_alkes = $this->getObatAlkes();
        $list_tindakan = $this->getTindakan();

        $request = Yii::$app->request;
        $title = \Yii::t('fe', $this->_title);
        $action = \Yii::t('fe', 'Tambah');
        $modelPaketBmhp = new PaketBmhpForm;
        $formName = substr(strrchr(get_class($modelPaketBmhp), "\\"), 1);

        try {
            if ($request->post()) {
                if ($data = $request->post('PaketBmhpForm')){
                    try {
                        $response = $this->_restRajal->post('paket-bmhp/create', [
                                'form_params' => $data
                            ]);
                        $response = json_decode($response->getBody(), true);

                        return DocoHelpers::response($response, false, true);
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                }
            } else {
                return $this->renderAjax('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRajal->delete('paket-bmhp/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    private function getObatAlkes()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('paket-bmhp/list-obat-alkes');
            $row = [];
            $body = json_decode($response->getBody(),TRUE);

            $return = $body['response']['data'];
            return $return;
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTindakan()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('paket-bmhp/list-daftar-tindakan');
            $row = [];
            $body = json_decode($response->getBody(),TRUE);

            $return = $body['response']['data'];
            return $return;
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/paket-bmhp.xlsx";
            $response = $this->_restRajal->get('paket-bmhp/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            return $e;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/paket-bmhp.pdf";
        try {
            $response = $this->_restRajal->get('paket-bmhp/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadPdf($response, $path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}