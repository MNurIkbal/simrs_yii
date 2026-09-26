<?php
// Author : Rizal Faidin

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\JenisKasusPenyakitForm;
use app\modules\master\models\KasusPenyakitDiagnosaForm;
use app\modules\master\models\KasusPenyakitRuanganForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class JenisKasusPenyakitController extends DocoController
{
    protected $_title = "Master :: JenisKasusPenyakit";
    protected $_module = 'master/jenis-kasus-penyakit/';
    protected $_moduleDiagnosa = 'master/jenis-kasus-penyakit/';
    protected $_moduleRuangan = 'master/jenis-kasus-penyakit/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;

        $this->_status = [
          0 => Yii::t('fe', 'Tidak Aktif'),
          1 => Yii::t('fe', 'Aktif')
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
            $response = $this->_restMaster->get('jenis-kasus-penyakit/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $dataBody = $body['response']["data"];
            $no = $request->get('start',1);

            foreach ($dataBody as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['jeniskasuspenyakit_id']);
                $value['primary'] = $primaryKey;
                unset($value['jeniskasuspenyakit_id']);

                // $value['status'] = DocoHelpers::isActive($value['is_active']);
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $value['rowNum'] = $no;
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

    /**
    * @author Rizal
    * @since
    * @param
    * @return
    * @desc
    */
    public function actionCreate()
    {
        try {
            $title = 'Tambah Jenis Kasus Penyakit';
            $model = new JenisKasusPenyakitForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $this->_status = [
                1 => Yii::t('fe', 'Aktif'),
                0 => Yii::t('fe', 'Tidak Aktif')
            ];

            $status = $this->_status;
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'jenis-kasus-penyakit/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'JenisKasusPenyakitForm');
                } else {
                    return DocoHelpers::response($model->errors, 422, $formName);                   
                }
            } else {                
                $model->is_active = 1;
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
            $title = 'Ubah Jenis Kasus Penyakit';
            $model = new JenisKasusPenyakitForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'jenis-kasus-penyakit/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'JenisKasusPenyakitForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JenisKasusPenyakitForm');
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
                    $model->is_active = ($model->is_active == false) ? 0  : 1;
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
            $response = $this->_restMaster->request('GET', 'jenis-kasus-penyakit/view',
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
            $response = $this->_restMaster->request('DELETE', 'jenis-kasus-penyakit/delete-jenis-penyakit',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }


    // kasus penyakit diagnosa
    /**
    * @author Rizal
    * @since 2018-01-09 11:39:11
    * @param
    * @return
    * @desc
    */
    public function actionGetDataDiagnosa()
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
            $response = $this->_restMaster->get('kasus-penyakit-diagnosa/list-data?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode([$value['jeniskasuspenyakit_id'],$value['diagnosa_id']]);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['totalCount'];
            $result['recordsFiltered'] = $body['response']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
    * @author Rizal
    * @since
    * @param
    * @return
    * @desc
    */
    public function actionCreateDiagnosa()
    {
        try {
            $title = 'Tambah kasus penyakit diagnosa';
            $model = new KasusPenyakitDiagnosaForm;
            $status = $this->_status;
            $rest = $this->_restMaster;
            $responseDiagnosa = $rest->request('POST', 'kasus-penyakit-diagnosa/get-list-diagnosa');
            $bodyListDiagnosa = json_decode($responseDiagnosa->getBody(), True);
            $listDiagnosa = ArrayHelper::map($bodyListDiagnosa['response'], 'diagnosa_id', 'diagnosa_nama');

            $responseKasusPenyakit = $rest->request('POST', 'kasus-penyakit-diagnosa/get-list-kasus-penyakit');
            $bodyListKasusPenyakit = json_decode($responseKasusPenyakit->getBody(), True);
            $listKasusPenyakit = ArrayHelper::map(
                $bodyListKasusPenyakit['response'],
                'jeniskasuspenyakit_id',
                'jeniskasuspenyakit_nama'
            );

            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post['KasusPenyakitDiagnosaForm'];
                $model->diagnosa_id = 1;
                $model->list_diagnosa_id = $post['KasusPenyakitDiagnosaForm']['list_diagnosa_id'];
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kasus-penyakit-diagnosa/batch-create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form_diagnosa',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-11 10:51:15
    * @param
    * @return
    * @desc
    */
    public function actionFindPenyakitDiagnosa($jeniskasuspenyakit_id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'kasus-penyakit-diagnosa/view',
                [
                    'query' => ['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id]
                ]
            );
            return $response->getBody();
            $results = $response->getBody();

        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-12 10:06:09
    * @param
    * @return
    * @desc
    */
    public function actionCreateRuangan()
    {
        try {
            $title = 'Tambah kasus penyakit ruangan';
            $model = new KasusPenyakitRuanganForm;
            $status = $this->_status;
            $rest = $this->_restMaster;
            $responseRuangan = $rest->request('POST', 'kasus-penyakit-ruangan/get-list-ruangan');
            $bodyListRuangan = json_decode($responseRuangan->getBody(), True);
            $listRuangan = ArrayHelper::map($bodyListRuangan['response'], 'ruangan_id', 'ruangan_nama');

            $responseKasusPenyakit = $rest->request('POST', 'kasus-penyakit-ruangan/get-list-kasus-penyakit');
            $bodyListKasusPenyakit = json_decode($responseKasusPenyakit->getBody(), True);
            $listKasusPenyakit = ArrayHelper::map(
                $bodyListKasusPenyakit['response'],
                'jeniskasuspenyakit_id',
                'jeniskasuspenyakit_nama'
            );

            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post['KasusPenyakitRuanganForm'];
                $model->list_jeniskasuspenyakit_id = $post['KasusPenyakitRuanganForm']['list_jeniskasuspenyakit_id'];
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kasus-penyakit-ruangan/batch-create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form_ruangan',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-11 10:51:15
    * @param
    * @return
    * @desc
    */
    public function findPenyakitDiagnosa($jeniskasuspenyakit_id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'kasus-penyakit-diagnosa/view',
                [
                    'query' => ['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id]
                ]
            );
            return json_decode($response->getBody(),true);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-11 10:44:42
    * @param integer jeniskasuspenyakit_id diagnosa_id
    * @return
    * @desc
    */
    public function actionUpdateDiagnosa($jeniskasuspenyakit_id, $diagnosa_id)
    {
        try {
            $title = 'Ubah kasus penyakit diagnosa';
            $model = new JenisKasusPenyakitForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kasus-penyakit-diagnosa/update',[
                                        'query' => ['jeniskasuspenyakit_id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JenisKasusPenyakitForm');
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

    /**
    * @author Rizal
    * @since 2018-01-11 16:10:49
    * @param
    * @return
    * @desc
    */
    public function actionDeleteDiagnosa($jeniskasuspenyakit_id, $diagnosa_id)
    {
        try {
            $jeniskasuspenyakit_id = DocoHelpers::decrypt($jeniskasuspenyakit_id);
            $diagnosa_id = DocoHelpers::decrypt($diagnosa_id);
            $response = $this->_restMaster->request('DELETE', 'kasus-penyakit-diagnosa/delete',[
                            'query' => [
                                'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
                                'diagnosa_id' => $diagnosa_id,
                            ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus',
                'return'=>'tabelDiagnosa.reload()'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    // kasus penyakit ruangan
    public function actionGetDataRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        // $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            // $response = $this->_restMaster->get('jenisKasusPenyakit/listdata?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = $this->_restMaster->request('POST', 'kasus-penyakit-ruangan/index',[
                'form_params' => $post
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey1 = DocoHelpers::encrypt($value['jeniskasuspenyakit_id']);
                $primaryKey2 = DocoHelpers::encrypt($value['ruangan_id']);
                $value['primary'] = $primaryKey;
                unset($value['jeniskasuspenyakit_id']);
                unset($value['ruangan_id']);

                //$value['aksi'] = '';
                // $value['aksi']  = Html::button(
                //     'Lihat', [
                //         'class' => 'btn btn-info btn-xs btn-block data-view',
                //         'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                //         'data-toggle' => 'modal',
                //         'data-target' => '#modal_backdrop'
                //     ]
                // );
                // $value['aksi'] .= Html::a(
                    // 'Export', '#', [
                        // 'class' => 'btn btn-success btn-xs btn-block data-export',
                        // 'action' => Url::home().$this->_module.'export?id='.$primaryKey
                    // ]
                // );
                // $value['aksi'] .= Html::a(
                //     'Cetak', '#', [
                //         'class' => 'btn btn-warning btn-xs btn-block data-print',
                //         'action' => Url::home().$this->_module.'print?id='.$primaryKey
                //     ]
                // );
                // $value['aksi'] .= Html::button(
                //     "<i class='fa fa-pencil'></i>",
                //     [
                //         'class' => 'btn btn-primary btn-xs data-update',
                //         'style' => 'margin-right:5px',
                //         'action' =>
                //             Url::home()
                //             . $this->_module
                //             . 'update?'
                //             . 'jeniskasuspenyakit_id=' . $primaryKey1
                //             . '&ruangan_id=' . $primaryKey2,
                //         'data-popup' => "tooltip",
                //         'data-placement' => 'bottom',
                //         'data-original-title' => Yii::t('fe', 'Ubah'),
                //         'data-toggle'  => 'modal',
                //         'data-target' => '#modal_backdrop'
                //     ]
                // );
                /*$value['aksi'] .= Html::button(
                    "<i class='fa fa-trash'></i>",
                    [
                        'class' => 'btn btn-danger btn-xs delete',
                        'id' => 'deleteKasusPenyakitRuangan',
                        'style' => 'margin-right:5px',
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                        'action' =>
                            Url::home()
                            . $this->_module
                            . 'delete?'
                            . 'jeniskasuspenyakit_id=' . $primaryKey1
                            . '&ruangan_id=' . $primaryKey2,
                    ]
                );*/
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-11 10:51:15
    * @param
    * @return
    * @desc
    */
    public function actionFindPenyakitRuangan($ruangan_id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restMaster->request('GET', 'kasus-penyakit-ruangan/view',
                [
                    'query' => ['ruangan_id' => $ruangan_id]
                ]
            );
            return $response->getBody();
            $results = $response->getBody();

        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
    * @author Ayip
    * @since 2018-03-07 09:32:15
    * @param
    * @return
    * @desc
    */
    public function actionGetJenisKasusPenyakit()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){

            $response = $this->_restMaster->request('POST', 'jenis-kasus-penyakit/list-jenis-kasus-penyakit',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['jeniskasuspenyakit_id'],'text'=>$value['jeniskasuspenyakit_nama']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }


    public function actionPageJenisKasusPenyakit() {
        $status = $this->_status;
        return $this->renderAjax('_jenisKasusPenyakit', ['status' => $status]);
    }

    public function actionPageKasusPenyakitDiagnosa() {
        $status = $this->_status;
        return $this->renderPartial('_kasusPenyakitDiagnosa', ['status' => $status]);
    }

    public function actionPageKasusPenyakitRuangan() {
        $status = $this->_status;
        return $this->renderPartial('_kasusPenyakitRuangan', ['status' => $status]);
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
            $response = $this->_restMaster->get('jenis-kasus-penyakit/export-excel?'.http_build_query($yiiRestfulParams));
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
        
        $path = Yii::getAlias("@download") . "/jenis-kasus-penyakit.pdf";
        try {
            $response = $this->_restMaster->get('jenis-kasus-penyakit/export-pdf?'.http_build_query($yiiRestfulParams),[
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
}
