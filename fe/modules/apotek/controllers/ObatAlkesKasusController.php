<?php 

/**
 * @author Randy Vianda Putra
 * @todo Master Obat Alkes Jenis Kasus Penyakit
 * @copyright 3 January 2018 aweutist
 * @edited yaya
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\ObatAlkesKasusForm;

class ObatAlkesKasusController extends DocoController
{
    
    public $_title = "Obat Alkes Jenis Kasus Penyakit";
    public $_module = '/apotek/obat-alkes-kasus';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    
    public function actionIndex()
    {
        $model = new ObatAlkesKasusForm;
        $title = $this->_title;
        $request = Yii::$app->request;
        $response = $this->_restMaster->request('GET', 'obat-alkes-kasus/get-filtered');
        $row = [];
        $body = json_decode($response->getBody(),TRUE);
        $obatAlkes = isset($body['response']['obat_alkes']) ? $body['response']['obat_alkes'] : [];
        $kasusPenyakit = isset($body['response']['kasus_penyakit']) ? $body['response']['kasus_penyakit'] : [];
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
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('obat-alkes-kasus/', 
                [
                    'form_params' => [],
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            // return DocoHelpers::response($body);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = [
                    'jeniskasuspenyakit_id' => $value['jeniskasuspenyakit_id'],
                    'obatalkes_id' => $value['obatalkes_id'],
                ];
                $value['primary'] = DocoHelpers::encrypt(json_encode($primaryKey));
                unset($value['jeniskasuspenyakit_id']);
                unset($value['obatalkes_id']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new ObatAlkesKasusForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('obat-alkes-kasus/create', [
                        'form_params' => $model->attributes
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'ObatAlkesKasusForm');
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->request('GET', 'obat-alkes-kasus/get-filtered');
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $obatAlkes = isset($body['response']['obat_alkes']) ? $body['response']['obat_alkes'] : [];
            $kasusPenyakit = isset($body['response']['kasus_penyakit']) ? $body['response']['kasus_penyakit'] : [];
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    /**
     * @todo delete table mapping
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionDelete($id)
    {
        try {
            $id = json_decode(DocoHelpers::decrypt($id),true);
            $response = $this->_restMaster->request('DELETE', 'obat-alkes-kasus/delete',[
                            'query' => [
                                'id_obat' => isset($id['obatalkes_id']) ? $id['obatalkes_id'] : 0, 
                                'id_penyakit' => isset($id['jeniskasuspenyakit_id']) ? $id['jeniskasuspenyakit_id'] : 0
                            ]
                        ]);
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

    /**
     * @todo find data by pk1 & pk2
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function find($id, $id2)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'obat-alkes-kasus/view',[
                            'query' => ['id' => $id, 'id2' => $id2]
                        ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionUpdate($id)
    {
        $id = json_decode(DocoHelpers::decrypt($id),true);

        $id_obat = isset($id['obatalkes_id']) ? $id['obatalkes_id'] : 0;
        $id_penyakit = isset($id['jeniskasuspenyakit_id']) ? $id['jeniskasuspenyakit_id'] : 0;

        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Update').' '.\Yii::t('fe', $this->_title);
        $model = new ObatAlkesKasusForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('obat-alkes-kasus/update', [
                        'form_params' => $model->attributes,
                        'query' => [
                            'id_obat' => $id_obat,
                            'id_penyakit' => $id_penyakit
                        ]
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'ObatAlkesKasusForm');
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->request('GET', 'obat-alkes-kasus/get-filtered',
                [
                    'query' => [
                        'id_obat' => $id_obat,
                        'id_penyakit' => $id_penyakit
                    ]
                ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $obatAlkes = isset($body['response']['obat_alkes']) ? $body['response']['obat_alkes'] : [];
            $kasusPenyakit = isset($body['response']['kasus_penyakit']) ? $body['response']['kasus_penyakit'] : [];
            if (isset($body['response']['data'])) {
                $model->attributes = $body['response']['data'];
            }
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/ObatAlkesKasus.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('obat-alkes-kasus/cetak-obat-alkes', [
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}