<?php
// Author : Johndoe

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KasusPenyakitDiagnosaForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KasusPenyakitDiagnosaController extends DocoController
{
    protected $_title = "Master :: KasusPenyakitDiagnosa";
    protected $_module = 'master/kasus-penyakit-diagnosa/';
    protected $_restMaster;
    protected $allowAction = [
        'index',
        'get-data',
        'create',
        'update',
        'delete',
        'export-excel',
        'export-pdf',
        'download-file',
        'find'
    ];

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
        $status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        $response = $this->_restMaster->get('allow/get-filter-kasus-penyakit-diagonsa',[
            'query' => []
        ]);
        $responseJenisPenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
        $listKasusPenyakit = isset($responseJenisPenyakit) ? $responseJenisPenyakit : [];
        return $this->renderAjax('index', get_defined_vars());
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
            $response = $this->_restMaster->get('kasus-penyakit-diagnosa/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode([$value['jeniskasuspenyakit_id'],$value['diagnosa_id']]);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                // $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

             return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCreate()
    {
        try {
            $title = 'Tambah Kasus Penyakit Diagnosa';
            $model = new KasusPenyakitDiagnosaForm;
            $status = $this->_status;
            $rest = $this->_restMaster;

            $response = $this->_restMaster->get('allow/get-filter-kasus-penyakit-diagonsa',[
                'query' => []
            ]);
            $responseJenisPenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
            $listKasusPenyakit = isset($responseJenisPenyakit) ? $responseJenisPenyakit : [];

            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post['KasusPenyakitDiagnosaForm'];
                if ($model->validate()) {
                    $response = $this->_restMaster->post('kasus-penyakit-diagnosa/create-penyakit-diagnosa',[
                                        'form_params' => $model->attributes
                                ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
      
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $options = [];
                $model->is_active = 1;
                return $this->renderAjax('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

     /**
    * @author iqbal@docotel.com
    * @since 2018-10-25 10:51:15
    * @param
    * @return
    * @desc
    */
    public function actionFindPenyakitDiagnosa($jeniskasuspenyakit_id)
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restMaster->get('allow/get-list-penyakit-diagonsa',[
                'query' => []
            ]);

            echo "<pre>";var_dump($response);die();
            return $response->getBody();
            $results = $response->getBody();

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
    }

    public function actionGetAllDiagnosa($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restMaster->get('allow/get-all-diagnosa',[
                'query' => ['keyword'=>$q]
            ]);;
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value) 
                $result['results'][] = [
                    'id' => $value['diagnosa_id'], 
                    'text' => $value['nama_diagnosa']
                ];
            return $result;

            $total = count($body['response']);
            $return = ['result'=>$result['results'],'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPenyakitDiagnosa()
    {
        if(isset($_GET['q']) && !empty($_GET['q'])){                    
            $response = $this->_restMaster->get('allow/data-kelompokpegawai',[
                            'form_params'=>['term'=>$_GET['q']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['kelompokpegawai_id'],'text'=>$value['kelompokpegawai_nama']];                
            }          
            // Return
            return Json::encode(['results' => $data]);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $title = 'Ubah Kasus Penyakit Diagnosa';
            $model = new KasusPenyakitDiagnosaForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = json_decode(DocoHelpers::decrypt($id));
            $jeniskasuspenyakit_id = $id[0];
            $diagnosa_id = $id[1];

            if ($request->post()) {
                $model->load($request->post());
                $post = $request->post();
                $model->attributes = $post['KasusPenyakitDiagnosaForm'];

                if ($model->validate()) {
                    $response = $this->_restMaster->post( 'kasus-penyakit-diagnosa/update-penyakit-diagnosa', [
                                        'query' => [
                                            'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
                                            'diagnosa_id' => $diagnosa_id,
                                        ],
                                        'form_params' => $model->attributes
                                ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);

                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'KasusPenyakitDiagnosaForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                /*get list jenis_kasus_penyakit*/
                $response = $this->_restMaster->get('allow/get-filter-kasus-penyakit-diagonsa',[
                    'query' => []
                ]);
                $responseJenisPenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
                $listKasusPenyakit = isset($responseJenisPenyakit) ? $responseJenisPenyakit : [];

                /*get kasus Penyakit diagnosa by jeniskasuspenyakit_id and diagnosa_id*/
                $responsekpd = $this->_restMaster->get('kasus-penyakit-diagnosa/view',[
                        'query' => ['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,'diagnosa_id' => $diagnosa_id]
                    ]);
                $bodykpd = json_decode($responsekpd->getBody(),true);
                $resultbodykpd = $bodykpd['response'];

                /*get data diagnosa by id*/
                $responseDiagnosa = $this->_restMaster->get('diagnosa/view',[
                    'query' => ['id'=>$diagnosa_id]
                ]);
                $resResponse = json_decode($responseDiagnosa->getBody(), TRUE)['response'];
                $options[$resResponse['diagnosa_id']] = $resResponse['diagnosa_kode'].' - '.$resResponse['diagnosa_nama'];
      
                $model->jeniskasuspenyakit_id = $jeniskasuspenyakit_id;
                $model->is_active = ($resultbodykpd['is_active'] == false) ? 0  : 1;

                return $this->renderAjax('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $id = json_decode(DocoHelpers::decrypt($id));
        $jeniskasuspenyakit_id = $id[0];
        $diagnosa_id = $id[1];
        try {
            $response = $this->_restMaster->request('DELETE', 'kasus-penyakit-diagnosa/delete',[
                            'query' => [
                                'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
                                'diagnosa_id' => $diagnosa_id,
                            ]
                        ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } 
    }
    
    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('kasus-penyakit-diagnosa/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
           
            return DocoHelpers::downloadFile($url);
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
        
        $path = Yii::getAlias("@download") . "/kasus-penyakit-diagnosa.pdf";
        try {
            $response = $this->_restMaster->get('kasus-penyakit-diagnosa/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
           

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $body = json_decode($e->getMessage(), true);
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
  
    public function find($jeniskasuspenyakit_id, $diagnosa_id)
    {
        try {
            $response = $this->_restMaster->request->get('kasus-penyakit-diagnosa/view',
                [
                    'query' => ['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,'diagnosa_id' => $diagnosa_id]
                ]
            );
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
