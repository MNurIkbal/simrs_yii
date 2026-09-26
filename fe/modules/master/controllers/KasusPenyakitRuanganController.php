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
use app\modules\master\models\KasusPenyakitRuanganForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KasusPenyakitRuanganController extends DocoController
{
    protected $_title = "Master :: Kasus Penyakit Ruangan";
    protected $_module = 'master/kasus-penyakit-ruangan/';
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

        $responseRuangan = json_decode($response->getBody(), True)['response']['ruangan'];
        $listRuangan = isset($responseRuangan) ? $responseRuangan : [];

        return $this->renderPartial('index', get_defined_vars());
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
            $response = $this->_restMaster->get('kasus-penyakit-ruangan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode([$value['ruangan_id'],$value['jeniskasuspenyakit_id']]);
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
            $title = 'Tambah Kasus Penyakit Ruangan';
            $model = new KasusPenyakitRuanganForm;
            $this->_status = [
                1 => Yii::t('fe', 'Aktif'),
                0 => Yii::t('fe', 'Tidak Aktif')
            ];
            $status = $this->_status;
            $response = $this->_restMaster->get('allow/get-filter-kasus-penyakit-diagonsa',[
                'query' => []
            ]);
            $responseJenisPenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
            $listKasusPenyakit = isset($responseJenisPenyakit) ? $responseJenisPenyakit : [];

            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                
                $model->attributes = $post['KasusPenyakitRuanganForm'];
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kasus-penyakit-ruangan/create-penyakit-ruangan',[
                                        'form_params' => $model->attributes
                                ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
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

    public function actionUpdate($id)
    {
        // try {
            $title = 'Ubah Kasus Penyakit Ruangan';
            $model = new KasusPenyakitRuanganForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = json_decode(DocoHelpers::decrypt($id));            
            $ruangan_id = $id[0];
            $jeniskasuspenyakit_id = $id[1];

            $response = $this->_restMaster->get('allow/get-filter-kasus-penyakit-diagonsa',[
                'query' => []
            ]);
            $responseJenisPenyakit = json_decode($response->getBody(), True)['response']['jenis_kasus_penyakit'];
            $listKasusPenyakit = isset($responseJenisPenyakit) ? $responseJenisPenyakit : [];

            if ($request->post()) {
                $model->load($request->post());
                $post = $request->post();
                $model->attributes = $post['KasusPenyakitRuanganForm'];
                if ($model->validate()) {
                    $response = $this->_restMaster->post( 'kasus-penyakit-ruangan/update-penyakit-ruangan', [
                                        'query' => [
                                            'ruangan_id' => $ruangan_id,
                                            'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
                                        ],
                                        'form_params' => $model->attributes
                                ]);
                    $body = json_decode($response->getBody(), True);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'KasusPenyakitRuanganForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $responsePRuangan = $this->_restMaster->get('kasus-penyakit-ruangan/view',
                    [
                        'query' => ['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,'ruangan_id' => $ruangan_id]
                    ]
                );
                $bodyPRuangan = json_decode($responsePRuangan->getBody(),true);
                $resultPRuangan = $bodyPRuangan['response'];

                $jeniskasuspenyakit_idjeniskasuspenyakit_id = $resultPRuangan['jeniskasuspenyakit_id'];
                $ruangan_id = $resultPRuangan['ruangan_id'];

                /*get data ruangan by id*/
                $responseRuangan = $this->_restMaster->get('ruangan/view',[
                    'query' => ['id'=>$ruangan_id]
                ]);
                $resResponse = json_decode($responseRuangan->getBody(), TRUE)['response'];
                $options[$resResponse['ruangan_id']] = $resResponse['ruangan_nama'];
                
                $model->ruangan_id = $ruangan_id;
                $model->jeniskasuspenyakit_id = $jeniskasuspenyakit_id;
                $model->is_active = ($resultPRuangan['is_active'] == false) ? 0  : 1;

                return $this->renderAjax('form', get_defined_vars());
            }
        /*} catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }*/
    }

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $id = json_decode(DocoHelpers::decrypt($id));
        $ruangan_id = $id[0];
        $jeniskasuspenyakit_id = $id[1];
        try {
            $response = $this->_restMaster->request('DELETE', 'kasus-penyakit-ruangan/delete-penyakit-ruangan',[
                            'query' => [
                                'ruangan_id' => $ruangan_id,
                                'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
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
            $response = $this->_restMaster->get('kasus-penyakit-ruangan/export-excel?'.http_build_query($yiiRestfulParams));
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
        
        $path = Yii::getAlias("@download") . "/kasus-penyakit-ruangan.pdf";
        try {
            $response = $this->_restMaster->get('kasus-penyakit-ruangan/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function find($jeniskasuspenyakit_id, $ruangan_id)
    {
        try {
            $response = $this->_restMaster->request->get('kasus-penyakit-ruangan/view',
                [
                    'query' => ['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,'ruangan_id' => $ruangan_id]
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
