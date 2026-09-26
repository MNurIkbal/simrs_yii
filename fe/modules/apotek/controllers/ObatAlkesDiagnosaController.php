<?php 

/**
 * @author Randy Vianda Putra
 * @todo Master Obat Alkes Jenis Kasus Penyakit
 * @copyright 3 January 2018 aweutist
 * @edited yaya
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\ObatAlkesDiagnosaForm;

class ObatAlkesDiagnosaController extends DocoController
{
    
    public $_title = "Obat Alkes Diagnosa Pasien";
    public $_module = '/apotek/obat-alkes-diagnosa';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    
    public function actionIndex()
    {
        $model = new ObatAlkesDiagnosaForm;
        $title = $this->_title;
        $request = Yii::$app->request;
        $response = $this->_restMaster->request('GET', 'obat-kasus-diagnosa/get-filtered');
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
            $response = $this->_restMaster->get('obat-kasus-diagnosa/', 
                [
                    'form_params' => [],
                    'query' => $yiiRestfulParams
                ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                
                $no++;
                $primaryKey = [
                    'diagnosa_id' => $value['diagnosa_id'],
                    'obatalkes_id' => $value['obatalkes_id'],
                ];
                $value['primary'] = DocoHelpers::encrypt(json_encode($primaryKey));
                unset($value['diagnosa_id']);
                unset($value['obatalkes_id']);
                $value['diagnosa_nama'] = $value['diagnosa']['diagnosa_nama'];
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
        $state = '';
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah Obat Alkes Diagnosa Pasien');
        $model = new ObatAlkesDiagnosaForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $obatalkes_id = '';
        $obatalkes_nama = '';
        $diagnosa_id = '';
        $diagnosa_nama = '';
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('obat-kasus-diagnosa/create', [
                        'form_params' => $model->attributes
                    ]);

                    $response = json_decode($response->getBody(),true);
                    // return DocoHelpers::response($response);
                    return DocoHelpers::response($response,false,'ObatAlkesDiagnosaForm');
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
            $response = $this->_restMaster->request('GET', 'obat-kasus-diagnosa/get-filtered');
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $obatAlkes = isset($body['response']['obat_alkes']) ? $body['response']['obat_alkes'] : [];
            $kasusPenyakit = isset($body['response']['kasus_penyakit']) ? $body['response']['kasus_penyakit'] : [];
            return $this->renderAjax('form', get_defined_vars());
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
            $response = $this->_restMaster->request('DELETE', 'obat-kasus-diagnosa/delete',[
                            'query' => [
                                'id_obat' => isset($id['obatalkes_id']) ? $id['obatalkes_id'] : 0, 
                                'id_diagnosa' => isset($id['diagnosa_id']) ? $id['diagnosa_id'] : 0
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
            $response = $this->_restMaster->request('GET', 'obat-kasus-diagnosa/view',[
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
        $state = 'update';
        $id_obat = isset($id['obatalkes_id']) ? $id['obatalkes_id'] : 0;
        $id_diagnosa = isset($id['diagnosa_id']) ? $id['diagnosa_id'] : 0;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah Obat Alkes Diagnosa Pasien');
        $model = new ObatAlkesDiagnosaForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $obatalkes_id = '';
        $obatalkes_nama = '';
        $diagnosa_id = '';
        $diagnosa_nama = '';
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('obat-kasus-diagnosa/update', [
                        'form_params' => $model->attributes,
                        'query' => [
                            'id_obat' => $id_obat,
                            'id_diagnosa' => $id_diagnosa
                        ]
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'ObatAlkesDiagnosaForm');
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
            try {
                $response = $this->_restMaster->request('GET', 'obat-kasus-diagnosa/get-filtered',
                [
                    'query' => [
                        'id_obat' => $id_obat,
                        'id_diagnosa' => $id_diagnosa
                    ]
                ]);
                $row = [];
                $body = json_decode($response->getBody(),TRUE);

                $model->obatalkes_nama = ($body['response']['data']['obatalkes']) ? 
                $body['response']['data']['obatalkes']['obatalkes_namalain'] : '';

                $model->diagnosa_namalainnya = ($body['response']['data']['diagnosa']) ? 
                $body['response']['data']['diagnosa']['diagnosa_nama'] : '';
                
                $obatAlkes = isset($body['response']['obat_alkes']) ? $body['response']['obat_alkes'] : [];
                $kasusPenyakit = isset($body['response']['kasus_penyakit']) ? $body['response']['kasus_penyakit'] : [];
                if (isset($body['response']['data'])) {
                    $model->attributes = $body['response']['data'];
                }
                $obatalkes_id = $model->obatalkes_id;
                $obatalkes_nama = $model->obatalkes_nama;
                $diagnosa_id = $model->diagnosa_id;
                $diagnosa_nama = $model->diagnosa_namalainnya;
                return $this->renderAjax('form', get_defined_vars());
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/ObatAlkesDiagnosa.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('obat-kasus-diagnosa/cetak-obat-diagnosa', [
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

    public function actionListDiagnosa() 
    {
        $q = '';
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $q = $_GET['q']['term'];
        }

        $response = $this->_restMaster->request('GET', 'obat-kasus-diagnosa/list-diagnosa?q='.$q);
        $body = json_decode($response->getBody(),TRUE);
        $total = count($body['response']);
        $return = ['result'=>$body['response'],'total_count'=>$total,'incomplete_results'=>false];

        return DocoHelpers::response($return);
    }

    public function actionListObatAlkes()
    {   
        $q = '';
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $q = $_GET['q']['term'];
        }
        $response = $this->_restMaster->request('GET', 'obat-kasus-diagnosa/list-obat-alkes?q='.$q);
        $body = json_decode($response->getBody(),TRUE);
        $total = count($body['response']);
        $return = ['result'=>$body['response'],'total_count'=>$total,'incomplete_results'=>false];
        
        return DocoHelpers::response($return);
    }

    public function actionGetObatAlkes($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('obat-alkes-kasus/obat?advanced-filter[obatalkes_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $value['obatalkes_id'], 
                    'text' => $value['obatalkes_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDiagnosa($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('diagnosa/index?advanced-filter[diagnosa_nama]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $value['diagnosa_id'], 
                    'text' => $value['diagnosa_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

}