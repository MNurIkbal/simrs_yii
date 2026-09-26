<?php
// Author : Ardi Pratama

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\DokRekamMedisForm;
use GuzzleHttp\Exception\RequestException;
use kartik\widgets\ActiveForm;

class DokRekamMedisController extends DocoController
{
    protected $_title = "Rm :: Dokumen Rekam Medis";
    protected $_module = 'rm/dok-rekam-medis/';
    protected $_restRm;
    protected $_backUrl;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_backUrl = 'dok-rekam-medis/';
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
        $response = $this->_restRm->get('allow/pack-dok-rm');
        $body = json_decode($response->getBody(), TRUE);
        $warnadok = $body['response']['data-warnadok'];

        $response = $this->_restRm->get('lokasi-rak-rekam-medik');
        $body = json_decode($response->getBody(), true);
        $listLokasiRak = $body['response']['data'];

        $response = $this->_restRm->get('sub-rak-rekam-medik');
        $body = json_decode($response->getBody(), true);
        $listLokasiSubrak = $body['response']['data'];
        
        $penjamin = [];

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
            $response = $this->_restRm->get('dok-rekam-medis/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['dokrekammedis_id']);
                unset($value['dokrekammedis_id']);

                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['subrak_m']['subrak_nama'] = isset($value['subrak_m']) ? $value['subrak_m']['subrak_nama'] : '';
                $value['pasien_m']['no_rekam_medik'] = isset($value['pasien_m']) ? $value['pasien_m']['no_rekam_medik'] : '';
                $value['lokasirak_m']['lokasirak_nama'] = isset($value['lokasirak_m']) ? $value['lokasirak_m']['lokasirak_nama'] : '';
                $value['warnadokrekammedik_m']['warnadokrm_namawarna'] = isset($value['warnadokrekammedik_m']) ? $value['warnadokrekammedik_m']['warnadokrm_namawarna'] : '';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new DokRekamMedisForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restRm->get('dok-rekam-medis/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Tambah Data';
            $model = new DokRekamMedisForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $selectedListLokasiSubrak = [];
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    try {
                        $forms = $request->post();
                        $forms['DokRekamMedisForm']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
                        // echo json_encode($forms);
                        // die();
                        $response = $this->_restRm->post('dok-rekam-medis/create', [
                            'form_params' => $forms
                        ]);
                        $response = json_decode($response->getBody(),true);

                    return DocoHelpers::response($response,false,true);
                    } catch (RequestException $e) {
                        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'DokRekamMedisForm');
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restRm->get('lokasi-rak-rekam-medik');
                $body = json_decode($response->getBody(), TRUE);
                $listLokasiRak = $body['response']['data'];

                $response = $this->_restRm->get('sub-rak-rekam-medik');
                $body = json_decode($response->getBody(), TRUE);
                $listLokasiSubrak = $body['response']['data'];

                $response = $this->_restRm->get('pasien');
                $body = json_decode($response->getBody(), TRUE);
                $pasien = $body['response']['data'];

                $LokrakRequest = $this->_restRm->get('lokasi-rak-rekam-medik/list-rak');
                $body = json_decode($LokrakRequest->getBody(),TRUE);
                $lokrak = $body['response'];

                return $this->renderAjax('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new DokRekamMedisForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $forms = $request->post();
                    $response = $this->_restRm->post('dok-rekam-medis/update',[
                        'query' => ['id' => $id],
                        'form_params' => $forms
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
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
            $response = $this->_restRm->get('dok-rekam-medis/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'][0];
            $model->attributes = $attributes;
            $no_rekam_medik = "";

            //for form update
            $listPasien = $body['response'][1]['Pasien'];
            $listLokasiRak = $body['response'][1]['LokasiRak'];
            $listLokasiSubrak = $body['response'][1]['LokasiSubrak'];
            $selectedListLokasiSubrak = $body['response'][1]['SelectedLokasiSubrak'];

            // Loop pasien
            foreach ($listPasien as $value) {
              if ($value['pasien_id'] == $model->pasien_id) {
                $no_rekam_medik = $value['no_rekam_medik'];
              }
            }

            // Loop subrak
            if (!empty($listLokasiSubrak)) {
                // Loop
                foreach ($listLokasiSubrak as $key => $value) {
                    // Assign
                    $listLokasiSubrak[$key]['ruangan_nama'] = $value['subrak_nama'];
                }
            }

            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restRm->delete('dok-rekam-medis/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK", [
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restRm->get($this->_backUrl.'export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            $path = Yii::getAlias("@download") . "/laporan-dokumen-rekam-medik.xlsx";

            return DocoHelpers::downloadFile($path,true);
       } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
       } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
       }
    }
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/dokumen-rekam-medis.pdf";
        try {
            $response = $this->_restRm->get($this->_backUrl.'export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    public function actionGetSubrak()
    {
        // Change response
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get reqeust
        $request = Yii::$app->request;
        $params = '';

        // Declare result
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        // Check post
        if ($request->post()) {
            // Get parents
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        // Try catch
        try {
            // Get request
            $request = $this->_restRm->get('dok-rekam-medis/get-subrak'.$params);
            $body = json_decode($request->getBody(), true);

            // Check
            if (isset($body['response']['data']) && !empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $value) {
                    // Assign result
                    $result['output'][] = [
                        'id' => $value['subrak_id'],
                        'name' => $value['subrak_nama']
                    ];
                }
            }

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }
    public function actionGetLokasirak()
    {
        // Change response
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get reqeust
        $request = Yii::$app->request;
        $params = '';

        // Declare result
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        // Check post
        if ($request->post()) {
            // Get parents
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        // Try catch
        try {
            // Request
            $request = $this->_restRm->get('dok-rekam-medis/get-lokasirak'.$params);
            $body = json_decode($request->getBody(), true);

            // Check
            if (isset($body['response']['data']) && !empty($body['response']['data'])) {
                // Loop
                foreach ($body['response']['data'] as $value) {
                    // Assign result
                    $result['output'][] = [
                        'id' => $value['lokasirak_id'],
                        'name' => $value['lokasirak_nama']
                    ];
                }
            }

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return result
            return $result;
        }
    }
    public function actionGetNoRm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = "";
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $q = $_GET['q']['term'];
        }

        $result = [];
        $result['results'] = [];

        // $_GET['q']['term']
        // return $q;

        try {

            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {

                $response = $this->_restRm->get('allow/get-norm?advanced-filter[pasien_m.no_rekam_medik]=' . $q);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['pasien']['no_rekam_medik'], 'text' => $value['pasien']['no_rekam_medik']];
                }
                $total = count($body['response']);
                $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
                return DocoHelpers::response($return);
            }

            
            // foreach ($body['response'] as $value)
            //     $result['results'][] = [
            //         'id' => $value['pasien']['no_rekam_medik'],
            //         'text' => $value['pasien']['no_rekam_medik']
            //     ];
            // return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }

    }
    public function actionSubRakChild()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $propinsi = $post['depdrop_parents'][0];

        $PropinsiRequest = $this->_restMaster->get('kabupaten/list-kabupaten?propinsi='.$propinsi);
        $body = json_decode($PropinsiRequest->getBody(),TRUE);
        $ddlKabupaten = $body['response'];

        $out = [];
        foreach($ddlKabupaten as $kab => $value) {
            $out[] = [
                        'id' => $kab,
                        'name' => $value
                    ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionGetNoRak()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){

                $response = $this->_restRm->request('POST', 'allow/get-data-no-rak',[
                    'form_params'=>['term'=>$_GET['q']['term'] ],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['lokasirak_nama'], 'text' => $value['lokasirak_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNoSubRak()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                
                $response = $this->_restRm->request('POST', 'sub-rak-rekam-medik/get-data-no-sub-rak',[
                    'form_params'=>['term'=>$_GET['q']['term'] ],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['subrak_nama'], 'text' => $value['subrak_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNoSubRakParrent($lokasi = "")
    {
        try {
            $term = "";
            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
                $term = $_GET['q']['term'];
            }

            if (isset($_POST['depdrop_parents']) && !empty($_POST['depdrop_parents'][0])) {
                $lokasi = $_POST['depdrop_parents'][0];
            }
            if (empty($lokasi)) {
                $response = $this->_restRm->request('POST', 'sub-rak-rekam-medik/get-data-no-sub-rak', [
                    'form_params' => ['term' => $term],
                ]);
            }
            if (!empty($lokasi)) {
                $response = $this->_restRm->request('POST', 'sub-rak-rekam-medik/get-data-no-sub-rak?lokasi=' . $lokasi, [
                    'form_params' => ['term' => $term],
                ]);
            }

            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['subrak_nama'], 'name' => $value['subrak_nama']];
            }
            $total = count($body['response']);
            $return = ['output' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetNoRekamMedik()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){

                $response = $this->_restRm->request('POST', 'allow/get-data-no-rekam-medik',[
                    'form_params'=>['term'=>$_GET['q']['term'] ],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_rekam_medik'], 'text' => $value['no_rekam_medik']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetNoRekamMedikPasien()
    {
        try {
            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {

                $response = $this->_restRm->request('POST', 'allow/get-data-no-rekam-medik-pasien', [
                    'form_params' => ['term' => $_GET['q']['term']],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_rekam_medik'], 'text' => $value['no_rekam_medik']];
                }
                $total = count($body['response']);
                $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}
