<?php

namespace Doco\antrian\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\antrian\models\DisplayAntrianForm;
use yii\web\UploadedFile;
use app\components\DocoDatatableHelper;

use app\components\DocoConstants;
use yii\helpers\Json;



class DisplayAntrianController extends DocoController
{
    protected $_title = "Display Antrian";
    protected $_module = '/antrian/display-antrian/';
    protected $_restAntrian;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restAntrian = Yii::$app->docoRest->antrian;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $options = [1 => 'Aktif', 0 => 'Tidak aktif'];
        $layarantrian_jenis = \Yii::$app->cache->get('display-antrian');        
        if(!$layarantrian_jenis){            
            $response = $this->_restAntrian->get('display-antrian/view');
            $body = json_decode($response->getBody(), True);     
            $layarantrian_jenisdata = ArrayHelper::map($body['response']['data'],'jenisantrian_id','layarantrian_jenis');
            \Yii::$app->cache->set('display-antrian', $layarantrian_jenisdata, 60);
            $layarantrian_jenis = $layarantrian_jenisdata;
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionCreate()
    {
        $title = Yii::t('fe', 'Tambah Master Display Antrian');
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $model = new DisplayAntrianForm;
        $model->scenario = 'loket';
        $request = Yii::$app->request;
        $post = $request->post();
        if ($post) {
            $jenis_antrian = $post['DisplayAntrianForm']['jenisantrian_id'];
            if ($jenis_antrian == 312) {
                $model->scenario = 'poli';
            } else {
                $model->scenario = 'loket';
            }
            $model->load($post);
            // $image = UploadedFile::getInstance($model, "layarantrian_latarbelakang");
            if ($model->validate()) {
                $model->is_active = $post['DisplayAntrianForm']["is_active"];
                // if (isset($image->name)) {
                //     $ext = end(explode(".", $image->name));
                //     $model->layarantrian_latarbelakang = Yii::$app->security->generateRandomString().".{$ext}";
                //     $model->loket_id = $post['DisplayAntrianForm']['loket_id'];
                //     $path = \Yii::getAlias('@webroot');
                //     if ($image->saveAs($path.'/media/img/display-antrian/' . $model->layarantrian_latarbelakang)) {
                //         $response = $this->_restAntrian->request('POST', 'display-antrian/save',[
                //             'form_params' => [
                //                 'data' => $model->attributes,
                //                 'data_post' => $post
                //             ],
                //         ]);
                //         $response = json_decode($response->getBody(), true);
                //         $data_display = Yii::$app->cache->delete("data-display-{$instalasi}");
                //     } else {
                //         $response = $this->_restAntrian->request('POST', 'display-antrian/save',[
                //             'form_params' => [
                //                 'data' => $model->attributes,
                //                 'data_post' => $post
                //             ],
                //         ]);
                //         $response = json_decode($response->getBody(), true);
                //         $data_display = Yii::$app->cache->delete("data-display-{$instalasi}");
                //     }
                // } else {
                    $response = $this->_restAntrian->request('POST', 'display-antrian/save',[
                        'form_params' => [
                            'data' => $model->attributes,
                            'data_post' => $post
                        ],
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $data_display = Yii::$app->cache->delete("data-display-{$instalasi}");

                    if (isset($response['response']['message'])) {
                        if (strpos($response['response']['message'], '4') !== false) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [], 
                                [
                                    'title' => Yii::t('fe', 'Peringatan').'!', 
                                    'text' => Yii::t('fe', 'Maksimal jumlah loket adalah 4.'),
                                    'message' => Yii::t('fe', 'Maksimal jumlah loket adalah 4.'),
                                ]
                            );
                        }

                        if (strpos($response['response']['message'], '8') !== false) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [], 
                                [
                                    'title' => Yii::t('fe', 'Peringatan').'!', 
                                    'text' => Yii::t('fe', 'Maksimal jumlah loket adalah 8.'),
                                    'message' => Yii::t('fe', 'Maksimal jumlah loket adalah 8.'),
                                ]
                            );
                        }
                    }
                    
                // }
            } else {
                $response = $model->errors;
            }

            return DocoHelpers::response($response, 422, 'DisplayAntrianForm');
        } else {
            $request = $this->_restAntrian->get('display-antrian/ajax');
            $body = json_decode($request->getBody(), TRUE);
            $data_ruangan = $data_antrian = [];
            if (isset($body['response']['data-ruangan'])) {
                $data_ruangan = $body['response']['data-ruangan'];
            }
    
            if (isset($body['response']['data-antrian'])) {
                $data_antrian = ArrayHelper::map($body['response']['data-antrian'], 'lookup_id', 'lookup_value');
            }
    
            $ruangan = Yii::$app->cache->get('ruangan-display');
            if ($ruangan === false) {
                $response = $this->_restMaster->get('ruangan/index');
                $body = json_decode($response->getBody(), true);
                $ruangan_data = ArrayHelper::map($body['response'], 'ruangan_id', 'ruangan_nama');
                Yii::$app->cache->set('ruangan-display', $ruangan_data);
                $ruangan = $ruangan_data;
            }
            $isBanyakLoket = $body['response']['data-konfig']['is_banyakloket'];

            if ($isBanyakLoket) {
                $isBanyakLoket = 1;
            } else {
                $isBanyakLoket = -1;
            }
        }
        return $this->render('form', get_defined_vars());
    }

    public function actionGetRuangan()
    {  
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restAntrian->request('POST','display-antrian/get-ruangan',['form_params'=>$post]);
        $body = json_decode($response->getBody(), true);
        // var_dump($body['response']);exit;
        // $result['data'] = $body['response'];
        return DocoHelpers::response($body,false,true);
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restAntrian->request('get', 'display-antrian/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(),TRUE);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['layarantrian_id']);
                $value['primary'] = $primaryKey;
                $cache[] = [
                    'layarantrian_id' => $value['layarantrian_id'],
                    'layarantrian_nama' => $value['layarantrian_nama'],
                ];
                unset($value['layarantrian_id']);

                $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak aktif';
                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
            $data_display = Yii::$app->cache->get("data-display-{$instalasi}");
            if ($data_display === false) {
                Yii::$app->cache->set("data-display-{$instalasi}", $cache);
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new DisplayAntrianForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('display-antrian/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());  
        try {
            $path = Yii::getAlias("@download") . "/display-antrian.xlsx";
            $response = $this->_restAntrian->get('display-antrian/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);    
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
        $path = Yii::getAlias("@download") . "/display-antrian.pdf";
        try {
            $response = $this->_restAntrian->get('display-antrian/cetak-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function actionUpdate($id)
    {
        $title = Yii::t('fe', 'Ubah Master Display Antrian');
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $id = DocoHelpers::decrypt($id);
        $model = new DisplayAntrianForm;
        $request = Yii::$app->request;
        $post = $request->post();
        if ($post) {
            $jenis_antrian = $post['DisplayAntrianForm']['jenisantrian_id'];
            if ($jenis_antrian == 312) {
                $model->scenario = 'poli';
            } else {
                $model->scenario = 'loket';
            }
            $model->load($post);
            $image = UploadedFile::getInstance($model, "layarantrian_latarbelakang");
            if ($model->validate()) {
                $model->is_active = $post['DisplayAntrianForm']["is_active"];
                if (isset($image->name)) {
                    $ext = end(explode(".", $image->name));
                    $model->layarantrian_latarbelakang = Yii::$app->security->generateRandomString().".{$ext}";
                    $model->loket_id = $post['DisplayAntrianForm']['loket_id'];
                    $path = \Yii::getAlias('@webroot');
                    if ($image->saveAs($path.'/media/img/display-antrian/' . $model->layarantrian_latarbelakang)) {
                        $response = $this->_restAntrian->request('POST', 'display-antrian/update?id=' . $id,[
                            'form_params' => [
                                'data' => $model->attributes,
                                'data_post' => $post
                            ],
                        ]);
                        $response = json_decode($response->getBody(), true);
                        $data_display = Yii::$app->cache->delete("data-display-{$instalasi}");
                    } else {
                        $response = $this->_restAntrian->request('POST', 'display-antrian/update?id=' . $id,[
                            'form_params' => [
                                'data' => $model->attributes,
                                'data_post' => $post
                            ],
                        ]);
                        $response = json_decode($response->getBody(), true);
                        $data_display = Yii::$app->cache->delete("data-display-{$instalasi}");
                    }
                } else {
                    $response = $this->_restAntrian->request('POST', 'display-antrian/update?id=' . $id,[
                        'form_params' => [
                            'data' => $model->attributes,
                            'data_post' => $post
                        ],
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $data_display = Yii::$app->cache->delete("data-display-{$instalasi}");

                    if (isset($response['response']['message'])) {
                        if (strpos($response['response']['message'], '4') !== false) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [], 
                                [
                                    'title' => Yii::t('fe', 'Peringatan').'!', 
                                    'text' => Yii::t('fe', 'Maksimal jumlah loket adalah 4.'),
                                    'message' => Yii::t('fe', 'Maksimal jumlah loket adalah 4.'),
                                ]
                            );
                        }

                        if (strpos($response['response']['message'], '8') !== false) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [], 
                                [
                                    'title' => Yii::t('fe', 'Peringatan').'!', 
                                    'text' => Yii::t('fe', 'Maksimal jumlah loket adalah 8.'),
                                    'message' => Yii::t('fe', 'Maksimal jumlah loket adalah 8.'),
                                ]
                            );
                        }
                    }
                }
            } else {
                $response = $model->errors;
            }

            return DocoHelpers::response($response, 422, 'DisplayAntrianForm');
        } else {
            $request = $this->_restAntrian->get('display-antrian/ajax?id=' . $id);
            $body = json_decode($request->getBody(), TRUE);
            $data_ruangan = $data_antrian = [];
            $isBanyakLoket = $body['response']['data-konfig']['is_banyakloket'];

            if (isset($body['response']['data-ruangan'])) {
                $data_ruangan = $body['response']['data-ruangan'];
            }
    
            if (isset($body['response']['data-antrian'])) {
                $data_antrian = ArrayHelper::map($body['response']['data-antrian'], 'lookup_id', 'lookup_value');
            }
    
            $ruangan = Yii::$app->cache->get('ruangan-display');
            if ($ruangan === false) {
                $response = $this->_restMaster->get('ruangan/get-display-antrian');
                $body = json_decode($response->getBody(), true);
                $ruangan_data = ArrayHelper::map($body['response'], 'ruangan_id', 'ruangan_nama');
                Yii::$app->cache->set('ruangan-display', $ruangan_data);
                $ruangan = $ruangan_data;
            }

            $response = $this->_restAntrian->get('display-antrian/view-data?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response']['data-edit'];
            if ($attributes['jenisantrian_id'] == 312) {
                $model->scenario = 'poli';
            } else {
                $model->scenario = 'loket';
            }
            $model->layarantrian_id = $attributes['layarantrian_id'];
            $model->jenisantrian_id = $attributes['jenisantrian_id'];
            $model->layarantrian_nama = $attributes['layarantrian_nama'];
            $model->layarantrian_latarbelakang = $attributes['layarantrian_latarbelakang'];
            $model->is_active = $attributes['is_active'];
            // $model->setAttributes($attributes);
            $data_ruangan = $data_pegawai = $list_id = $list_pegawai = [];
            foreach ($body['response']['data-ruangan'] as $key => $value) {
                $data_ruangan[$value['ruangan_id']] = [
                    'ruangan_id' => $value['ruangan_id'],
                    'ruangan_nama' => $value['ruangan_nama'],
                ];
                $data_pegawai[] = [
                    'pegawai_id' => $value['pegawai_id']
                ];
                $list_id[$value['ruangan_id']] = $value['ruangan_id'];
                $list_pegawai[$value['pegawai_id'] . '-' . $value['ruangan_id']] = $value['pegawai_id'] . '-' . $value['ruangan_id'];
            }
            // loket
            $getSelectedLoket = $this->_restAntrian->get('display-antrian/get-selected-loket?id='.$id);
            $bodyLoket = json_decode($getSelectedLoket->getBody(), TRUE);

            if (!empty($bodyLoket['response'])) {
                foreach ($bodyLoket['response'] as $key => $value) {
                    $id_loket[$value['loket_id']] = $value['loket_id'];
                }
                $selected_id_loket = $id_loket;
            } else {
                $selected_id_loket = [];
            }
            // ruangan
            $data_ruangan = json_encode($data_ruangan);
            $data_pegawai = json_encode($data_pegawai);
            $model->ruangan_id = $list_id;
            $model->pegawai_id = $list_pegawai;

            if ($model->jenisantrian_id == 312) {
                $model->scenario = 'poli';
            } else {
                $model->scenario = 'loket';
            }

            if ($isBanyakLoket) {
                $isBanyakLoket = 1;
            } else {
                $isBanyakLoket = -1;
            }
        }
        return $this->render('form', get_defined_vars());
    }
    
    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restAntrian->request('DELETE', 'display-antrian/delete',[
                            'query' => ['id' => $id ]
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

    public function actionGetLoket()
    {  
        $request = Yii::$app->request;
        $post = $request->post();
        $response = $this->_restAntrian->request('POST', 'display-antrian/get-loket',['form_params' => $post]);
        $body = json_decode($response->getBody(), true);

        return DocoHelpers::response($body, false, true);
    }

    public function actionGenerateLink($id)
    {
        $id_decrypt = DocoHelpers::decrypt($id);
        $request = $this->_restAntrian->get('display-antrian/view-data?id=' . $id_decrypt);
        $body = json_decode($request->getBody(), TRUE);
        

        $name = empty($body['response']['data-edit']['layarantrian_nama']) ? '' : $body['response']['data-edit']['layarantrian_nama'];
        $name = "display_antrian_". strtolower($name);

        $url_login = "http://".$_SERVER['HTTP_HOST']."/allow/antrian";
        $url = "http://".$_SERVER['HTTP_HOST']."/antrian/dashboard/layar-antrian?layar=".$id;

        
        $val = [
            'taskkill /F /IM Chrome.exe /T',
            'start chrome --kiosk --profile-directory=Default --app="'.$url_login.'"',
            'timeout /T 8',
            'start chrome --kiosk --profile-directory=Default --app="'.$url.'"'
        ];

        return DocoHelpers::generateBatFile($val,$name);
    }

}


