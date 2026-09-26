<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 10:03:25
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-26 17:43:14
 */


namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\DaftarTindakanForm;

trait TindakanTrait
{

    public function actionTindakan()
    {
        $title = 'Master Tindakan';
        $status = ['1'=>Yii::t('fe', 'Aktif'), '0'=>Yii::t('fe','Tidak aktif')];
        return $this->renderAjax('components/tindakan/index', get_defined_vars());
    }

    public function actionGetDataTindakan()
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
            $response = $this->_restMaster->get('tindakan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // return DocoHelpers::response($body);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                $value['primary'] = $primaryKey;
                $value['jeniskegiatantindakan_nama'] = isset($value['jeniskegiatantindakan']['jeniskegiatantindakan_nama']) ? $value['jeniskegiatantindakan']['jeniskegiatantindakan_nama'] : null;
                $value['kategoritindakan_nama'] = isset($value['kategoritindakan']['kategoritindakan_nama']) ? $value['kategoritindakan']['kategoritindakan_nama'] : null;
                $value['kelompoktindakan_nama'] = isset($value['kelompoktindakan']['kelompoktindakan_nama']) ? $value['kelompoktindakan']['kelompoktindakan_nama'] : null;
                $value['groupinacbg_nama'] = isset($value['groupinacbg']['groupinacbg_nama']) ? $value['groupinacbg']['groupinacbg_nama'] : null;
                $value['status'] = ($value['is_active']) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
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

    public function actionModalCreateJenisPemeriksaanFisio()
    {
        $fisioAttributes = $this->getFisioAttr();
        return $this->renderAjax('components/tindakan/modal/create-jenis-pemeriksaan-fisio', get_defined_vars());
    }

    public function actionModalCreateKelompokPemeriksaanFisio()
    {
        $fisioAttributes = $this->getFisioAttr();
        return $this->renderAjax('components/tindakan/modal/create-kelompok-pemeriksaan-fisio', get_defined_vars());
    }

    public function actionCreateTindakan()
    {
        $title = 'Tambah Tindakan';
        $disabled = 0;
        try {
            $model = new DaftarTindakanForm;
            $request = Yii::$app->request;
            $post = $request->post();
            if ($post) {
                $model->load($post['DaftarTindakanForm'], '');
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'tindakan/create',[
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $scenario = 'create';
                $attributes = $this->getRequest();

                //cek apakah modul fisio ada
                // $isFisio = Yii::$app->hasModule('fisioterapi');
                $isFisio  = Yii::$app->docoRest->fisioterapi->getConfig('base_uri')->getPath();
                if(!empty($isFisio)){
                    $fisioAttributes = $this->getFisioAttr();
                    $tindakanFisio = ArrayHelper::getValue($fisioAttributes, 'tindakanFisio');
                    $model->is_fisio = 0;
                }

                $isGroupInaCbg = 1;
                $model->is_active = 1;
                $model->is_akomodasi = 0;
                $model->isGroupInaCbg = $isGroupInaCbg;
                $action = '/master/tindakan/create-tindakan';
                return $this->renderAjax(Yii::$app->docoPlugin->execute($this,'form_tindakan'), get_defined_vars());
            }

        } catch (Exception $e) {
            $response = [
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } catch (RequestException $e) {
            $response = [
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        }

    }

    public function actionUpdateTindakan($id)
    {
        $title = 'Ubah Tindakan';
        try {
            $id = DocoHelpers::decrypt($id);
            $model = new DaftarTindakanForm;
            $request = Yii::$app->request;
            $post = $request->post();
            $disabled = 0;
            if ($post) {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $model->load($post);
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'tindakan/update?id='.$_GET['id'], [
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $scenario = 'update';
                $isGroupInaCbg = 0;
                $request = $this->_restMaster->request('GET', 'tindakan/view?id='.$id);
                $response = json_decode($request->getBody(), true);
                $attributes = $response['response']['data'];
                $disabled = $response['response']['count'];
                $isSetTindakan = !empty($response['response']['config']['is_set_tindakan']) ? $response['response']['config']['is_set_tindakan'] : false;
                if ($isSetTindakan) {
                    $disabled = 0;
                }

                $attributes = array_merge($attributes, $this->getRequest());

                if(isset($attributes['additional_data'])) {
                    $additional_data = json_decode($attributes['additional_data'], true);
                    $isGroupInaCbg = isset($additional_data['isGroupInaCbg']) ? $additional_data['isGroupInaCbg'] : null;
                }

                $serviceGroup = isset($attributes['serviceGroup']) ? $attributes['serviceGroup'] : [];
                $serviceCategory = isset($attributes['serviceCategory']) ? $attributes['serviceCategory'] : [];

                $optGroup = [];
                if (!empty($serviceGroup)) {
                    $optGroup = [
                        $serviceGroup['servicegroup_id'] => $serviceGroup['servicegroup_nama']
                    ];
                }

                $optCategory = [];
                if (!empty($serviceCategory)) {
                    $optCategory = [
                        $serviceCategory['servicecategory_id'] => $serviceCategory['servicecategory_nama']
                    ];
                }

                //cek apakah modul fisio ada
                // $isFisio = Yii::$app->hasModule('fisioterapi');
                $tindakanFisio = false;
                $isFisio  = Yii::$app->docoRest->fisioterapi->getConfig('base_uri')->getPath();
                if(!empty($isFisio)){
                    $fisioAttributes = $this->getFisioAttr($id, true);
                    $tindakanFisio = ArrayHelper::getValue($fisioAttributes, 'tindakanFisio');
                    if ($tindakanFisio == true) {
                        $primaryKey = DocoHelpers::encrypt($id);
                        $dataTindakan = ArrayHelper::getValue($fisioAttributes, 'dataTindakan');
                        $mappingRuangan = ArrayHelper::getValue($fisioAttributes, 'mappingRuangan');
                        $kelompokPemeriksaanFisioId = ArrayHelper::getValue($dataTindakan, 'kelompokpemeriksaanfisio_id');
                        $jenisPemeriksaanFisioId = ArrayHelper::getValue($dataTindakan, 'jenispemeriksaanfisio_id');
                        $jenisPemeriksaanFisioId = ArrayHelper::getValue($dataTindakan, 'jenispemeriksaanfisio_id');
                        $model->jenis_pemeriksaan_fisio_id = $jenisPemeriksaanFisioId;
                        $model->kelompok_pemeriksaan_fisio_id = $kelompokPemeriksaanFisioId;
                        $model->list_ruangan = array_column($mappingRuangan, 'ruangan_id');
                    }
                    $model->is_fisio = 0;
                }

                $model->attributes = $attributes;
                $model->jeniskegiatantindakan_id = $attributes['jeniskegiatantindakan_id'];
                $model->kelompoktindakan_id = $attributes['kelompoktindakan_id'];
                $model->catatan = $attributes['catatan'];
                $model->isGroupInaCbg = $isGroupInaCbg;
                $model->is_akomodasi = is_null($model->is_akomodasi) ? null : ($model->is_akomodasi ? 1 : 0);

                $action = '/master/tindakan/update-tindakan?id='.$id;
                return $this->renderAjax(Yii::$app->docoPlugin->execute($this,'form_tindakan'), get_defined_vars());
            }
        } catch (Exception $e) {
            $response = [
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } catch (RequestException $e) {
            $response = [
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        }
    }

    private function getFisioAttr($id = null, $update = false)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'tindakan-fisio/get-create-tindakan-attributes',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'id' => $id,
                    'update' => $update
                ],
            ],
        ]);

        $dataResponse = ArrayHelper::getValue($response, 'data');
        return $dataResponse;
    }

    private function getRequest()
    {
        $request = $this->_restMaster->request('GET', 'tindakan/generate-api');
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    public function actionDeleteTindakan($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->POST('tindakan/delete', ['query'=>['id'=>$id]]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportExcelTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'tindakan/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master Tindakan.xlsx";
        try {
            $response = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/master-tindakan.pdf";
            $response = $this->_restMaster->get('tindakan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionGetKategori()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tindakan/data-kategori', [
                'form_params' => [
                    'term' => $_GET['q']['term'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['kategoritindakan_nama'],
                    'text' => $value['kategoritindakan_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total,'incomplete_results' =>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetKegiatan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tindakan/data-kegiatan', [
                'form_params' => [
                    'term' => $_GET['q']['term'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['jeniskegiatantindakan_nama'],
                    'text' => $value['jeniskegiatantindakan_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total,'incomplete_results' =>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetKelompok()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tindakan/data-kelompok', [
                'form_params' => [
                    'term' => $_GET['q']['term'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['kelompoktindakan_nama'],
                    'text' => $value['kelompoktindakan_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total,'incomplete_results' =>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionGetCbg()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restMaster->request('POST', 'tindakan/data-cbg', [
                'form_params' => [
                    'term' => $_GET['q']['term'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['groupinacbg_nama'],
                    'text' => $value['groupinacbg_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total,'incomplete_results' =>false];
            return DocoHelpers::response($return);
        }
    }

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi tindakan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekTransaksiTindakan()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($request->get('id'));
            $response = false;

            $restMaster = $this->_restMaster->get('tindakan/cek-transaksi-tindakan', [
                'query' => ['id' => $id]
            ]);
            $body = json_decode($restMaster->getBody(), true);
            $count = $body['response'];

            if ($count > 0) {
                $response = true;
            }

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}