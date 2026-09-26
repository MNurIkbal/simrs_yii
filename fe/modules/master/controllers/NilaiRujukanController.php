<?php 

/**
 * @author Sunarko
 * @todo Master Nilai Rujukan
 * @copyright 03-07-2018
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\NilaiRujukanForm;
use yii\helpers\ArrayHelper;

class NilaiRujukanController extends DocoController
{

    protected $_title = "Nilai Rujukan Pemeriksaan Laboratorium";
    protected $_module = '/master/nilai-rujukan';
    protected $_restMaster;
    
    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        //get data header dinamis
        $response = $this->_restMaster->request('get', 'nilai-rujukan/data-golongan');
        $body = json_decode($response->getBody(),TRUE);
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
            $response = $this->_restMaster->get('nilai-rujukan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pemeriksaanlab_id']);
                $value['primary'] = $primaryKey;
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

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new NilaiRujukanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        if ($request->post()) {
            $model->load($request->post());
            // $post = $request->post();
            $post = $request->post('NilaiRujukanForm');
            // $exp = explode("-", $post['id']);
            // $model->attributes = $post;
            // if(isset($post['field'])) {
            //     if($post['field'] == 'nilai_min') {
            //         $model->nilai_min = $post['value'];
            //     }
            //     elseif($post['field'] == 'nilai_max') {
            //         $model->nilai_max = $post['value'];
            //     }
            //     elseif($post['field'] == 'satuan_hasillab') {
            //         $model->satuan_hasillab = $post['value'];
            //     }
            //     elseif($post['field'] == 'nilaikritis_min') {
            //         $model->nilaikritis_min = $post['value'];
            //     }
            //     elseif($post['field'] == 'nilaikritis_max') {
            //         $model->nilaikritis_max = $post['value'];
            //     }
            //     elseif($post['field'] == 'keterangan') {
            //         $model->keterangan = $post['value'];
            //     }
            // }   

            // $model->pemeriksaanlab_id = $request->get('id');
            // $model->jenis_kelamin = $exp[0];
            // $model->golonganumur_id = $exp[1];
            // if(is_null($request->post('NilaiRujukanForm'))) {
            //     try {
            //         $response = $this->_restMaster->put('nilai-rujukan/update?id='.$id, [
            //             'form_params' => $model->attributes
            //         ]);

            //         $response = json_decode($response->getBody(),true);
            //         return DocoHelpers::response($response, false, $formName);
            //     } catch (RequestException $e) {
            //         return DocoHelpers::response($e->getMessage(), 422);
            //     } catch (\Exception $e) {
            //         return DocoHelpers::response($e->getMessage(), 422);
            //     }
            // }

            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('nilai-rujukan/update?id='.$id, [
                        'form_params' => $post
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false, $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::response($e->getMessage(), 422);
                } catch (\Exception $e) {
                    return DocoHelpers::response($e->getMessage(), 422);
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('nilai-rujukan/get-request?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];

            $parent = $attributes['valueParent'];
            $jk = [];
            $golongan_umur = [];

            foreach ($attributes['golongan_umur'] as $key => $value) {
                $golongan_umur[$value['golonganumurlab_id']] = $value['gol_umurlab_nama'];
            }
            
            foreach ($attributes['jenis_kelamin'] as $key => $value) {
                $jk[$value['lookup_id']] = [
                    'id' => $value['lookup_id'],
                    'label' => $value['lookup_name'],
                    'data' => $golongan_umur
                ];
            }

            // dump($attributes['nama_rujukan']);die;
            return $this->render('form', get_defined_vars());
        }
    }

    public function actionUpdateParent($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restMaster->post('nilai-rujukan/update-parent?id='.$id,[
                'form_params' => $post
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage(), 422);
        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage(), 422);
        }
    }

    public function actionUpdateHasil($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new NilaiRujukanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post('NilaiRujukanForm');
            $model->attributes = $post;
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('nilai-rujukan/update-hasil?id='.$id.'&nama='.$_GET['nama'], [
                        'form_params' => $post
                    ]);

                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response, false, $formName);
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
            $response = $this->_restMaster->get('nilai-rujukan/get-request?id='.$id.'&nama='.$_GET['nama']);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $nama_rujukan = $attributes['nama'];
            $is_active = $attributes['is_active'];
            $model->is_active = $is_active;
            return $this->renderPartial('form_hasil', get_defined_vars());
        }
    }

    private function getData()
    {
        try {
            $response = $this->_restMaster->request('GET', 'kamar/generate-api');
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_ruangan' => $body['response']['data-ruangan'],
                'data_jenis_kamar' => $body['response']['data-jenis-kamar'],
            ];
            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");

        try {
            $response = $this->_restMaster->request('DELETE', 'kamar/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            Yii::$app->cache->delete("data-kamar-{$instalasi}");
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $path = Yii::getAlias("@download") . "/kamar.pdf";
        try {
            $response = $this->_restMaster->get('kamar/cetak-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        $url = 'kamar/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/kamar.xlsx";
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

    public function actionGetPemeriksaanLab()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'nilai-rujukan/data-new-pemeriksaan-lab',[
                    'form_params'=>['term'=>$_GET['q']['term'] ],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['nama_pemeriksaan'], 'text' => $value['nama_pemeriksaan']];
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

    public function actionGetKelompokPeriksa()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restMaster->request('POST', 'nilai-rujukan/data-kelompok-periksa',[
                    'form_params'=>['term'=>$_GET['q']['term'] ],
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['nama_kelompok'], 'text' => $value['nama_kelompok']];
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

    public function actionCreate($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new NilaiRujukanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post('NilaiRujukanForm');

            $model->attributes = $post;
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('nilai-rujukan/create', [
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false, $formName);
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
            $response = $this->_restMaster->get('nilai-rujukan/get-request?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $nama_rujukan = '';
            $is_active = true;
            $model->is_active = $is_active;
            return $this->renderPartial('form_hasil', get_defined_vars());
        }
    }

    public function actionDeleteHasil($id, $nama)
    {
        try {
            $response = $this->_restMaster->request('DELETE', 'nilai-rujukan/delete-hasil',[
                            'query' => ['id' => $id, 'nama' => $nama]
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

    public function actionUpdateForm()
    {
        $request = Yii::$app->request;
        $model = new NilaiRujukanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            $post = $request->post();
            $exp = explode("-", $post['id']);
            $model->attributes = $post;
            if(isset($post['field'])) {
                if($post['field'] == 'nilai_min') {
                    $model->nilai_min = $post['value'];
                }
                elseif($post['field'] == 'nilai_max') {
                    $model->nilai_max = $post['value'];
                }
                elseif($post['field'] == 'satuan_hasillab') {
                    $model->satuan_hasillab = $post['value'];
                }
                elseif($post['field'] == 'nilaikritis_min') {
                    $model->nilaikritis_min = $post['value'];
                }
                elseif($post['field'] == 'nilaikritis_max') {
                    $model->nilaikritis_max = $post['value'];
                }
                elseif($post['field'] == 'keterangan') {
                    $model->keterangan = $post['value'];
                }
            }   

            $model->nilairujukan_id = $exp[0];
            $model->jenis_kelamin = $exp[1];
            $model->golonganumur_id = $exp[2];

            try {
                $response = $this->_restMaster->put('nilai-rujukan/update-form', [
                    'form_params' => $model->attributes
                ]);

                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response, false, $formName);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        } 
    }
}
