<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-17 10:39:45
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\FasilitasRsForm;
use app\modules\master\models\FasilitasRsDetailForm;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class FasilitasRsController extends DocoController
{
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    /**
     * @todo Method untuk menampilkan halaman awal master fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $listJenisFasilitas = [];

            $restMaster = $this->_restMaster->get('fasilitas-rs/get-jenis-fasilitas');
            $body = json_decode($restMaster->getBody(), true);
            $jenis_fasilitas = $body['response']['jenis_fasilitas'];
            $jenis_fasilitas_lainnya = $body['response']['jenis_fasilitas_lainnya'];
            $listJenisFasilitas = ArrayHelper::map($jenis_fasilitas, 'instalasi_nama', 'instalasi_nama');

            if (!empty($jenis_fasilitas_lainnya)) {
                foreach ($jenis_fasilitas_lainnya as $key => $value) {
                    $listJenisFasilitas[$value['nama_jenis']] = $value['nama_jenis'];
                }
            }

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk menyimpan data fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new FasilitasRsForm;
            $modelDetail = new FasilitasRsDetailForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $formDetailName = substr(strrchr(get_class($modelDetail), "\\"), 1);
            $listJenisFasilitas = [];
            $listNamaFasilitas = [];

            if ($request->post()) {
                $postFasilitas = $request->post('FasilitasRsForm');
                $postFasilitasDetail = $request->post('FasilitasRsDetailForm');

                $explodedJenisFasilitas = explode('_@_', $postFasilitas['nama_fasilitas']);

                if (isset($explodedJenisFasilitas[1])) {
                    $postFasilitas['jenis_fasilitas'] = $explodedJenisFasilitas[0];
                    $postFasilitas['nama_fasilitas'] = $explodedJenisFasilitas[1];
                } else {
                    $postFasilitas['jenis_fasilitas'] = null;
                    $postFasilitas['nama_fasilitas'] = $explodedJenisFasilitas[0];
                }

                if ($model->load($postFasilitas, '')) {
                    if ($model->validate()) {
                        if (!empty($postFasilitasDetail['nama_fasilitas'])) {
                            $restMaster = $this->_restMaster->post('fasilitas-rs/simpan-fasilitas-rs', [
                                'form_params' => [
                                    'FasilitasRs' => $model->attributes,
                                    'FasilitasRsDetail' => $postFasilitasDetail
                                ]
                            ]);
                            
                            $response = json_decode($restMaster->getBody(), true);

                            return DocoHelpers::responseTemplate($response['metadata']['status'], 'Error', $response['response']['data']);
                        } else {
                            $modelDetail->load($postFasilitasDetail, '');
                            $modelDetail->validate();
                            $errors = DocoHelpers::parseError($modelDetail->errors, $formDetailName);

                            return DocoHelpers::responseTemplate(422, 'Error', $errors);
                        }
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, $formName);

                        if (empty($postFasilitasDetail['nama_fasilitas'])) {
                            $errors['FasilitasRsDetailForm[nama_fasilitas]'][0] = Yii::t('fe', 'Nama Fasilitas tidak boleh kosong.');
                        }

                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                }
            } else {
                $restMaster = $this->_restMaster->get('fasilitas-rs/get-jenis-fasilitas');
                $body = json_decode($restMaster->getBody(), true);
                $response = $body['response']['jenis_fasilitas'];

                if (!empty($response)) {
                    foreach ($response as $key => $value) {
                        $listJenisFasilitas[$value['instalasi_id'].'_@_'.$value['instalasi_nama']] = $value['instalasi_nama'];
                    }
                }

                return $this->renderPartial('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mengubah data fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUpdate($id = null)
    {
        try {
            $request = Yii::$app->request;
            $model = new FasilitasRsForm;
            $modelDetail = new FasilitasRsDetailForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $formDetailName = substr(strrchr(get_class($modelDetail), "\\"), 1);
            $id = DocoHelpers::decrypt($id);
            $listJenisFasilitas = [];
            $listNamaFasilitas = [];
            $temp = [];

            if ($request->post()) {
                $postFasilitas = $request->post('FasilitasRsForm');
                $postFasilitasDetail = $request->post('FasilitasRsDetailForm');

                if (!empty($postFasilitasDetail['nama_fasilitas'])) {
                    $explodedJenisFasilitas = explode('_@_', $postFasilitas['nama_fasilitas']);

                    if (isset($explodedJenisFasilitas[1])) {
                        $postFasilitas['jenis_fasilitas'] = $explodedJenisFasilitas[0];
                        $postFasilitas['nama_fasilitas'] = $explodedJenisFasilitas[1];
                    } else {
                        $postFasilitas['jenis_fasilitas'] = null;
                        $postFasilitas['nama_fasilitas'] = $explodedJenisFasilitas[0];
                    }
                }

                if ($model->load($postFasilitas, '')) {
                    $model->fasilitasrs_id = $id;

                    if ($model->validate()) {
                        if (!empty($postFasilitasDetail['nama_fasilitas'])) {
                            $restMaster = $this->_restMaster->post('fasilitas-rs/ubah-fasilitas-rs', [
                                'form_params' => [
                                    'FasilitasRs' => $model->attributes,
                                    'FasilitasRsDetail' => $postFasilitasDetail
                                ]
                            ]);

                            $response = json_decode($restMaster->getBody(), true);

                            return DocoHelpers::responseTemplate($response['metadata']['status'], 'Error', $response['response']['data']);
                        } else {
                            $modelDetail->load($postFasilitasDetail, '');
                            $modelDetail->validate();
                            $errors = DocoHelpers::parseError($modelDetail->errors, $formDetailName);

                            return DocoHelpers::responseTemplate(422, 'Error', $errors);
                        }
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, $formName);

                        if (empty($postFasilitasDetail['nama_fasilitas'])) {
                            $errors['FasilitasRsDetailForm[nama_fasilitas]'][0] = Yii::t('fe', 'Nama Fasilitas tidak boleh kosong.');
                        }

                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                }
            } else {
                $restMaster = $this->_restMaster->get('fasilitas-rs/get-fasilitas-rs?id='.$id);
                $data = json_decode($restMaster->getBody(), true);
                $attributes = $data['response']['model'];
                $attributesDetail = $data['response']['modelDetail'];
                $model->load($attributes, '');
                $tempNamaFasilitas = $model->nama_fasilitas;
                $model->fasilitasrs_id = $id;
                $model->nama_fasilitas = $model->jenis_fasilitas.'_@_'.$model->nama_fasilitas;

                if (!empty($data['response']['jenis_fasilitas'])) {
                    foreach ($data['response']['jenis_fasilitas'] as $key => $value) {
                        $listJenisFasilitas[$value['instalasi_id'].'_@_'.$value['instalasi_nama']] = $value['instalasi_nama'];
                    }
                }

                if (!empty($data['response']['modelDetail'])) {
                    foreach ($data['response']['modelDetail'] as $key => $value) {
                        $listNamaFasilitas[$value['nama_fasilitas']] = $value['nama_fasilitas'];
                    }
                }

                if (!in_array($model->jenis_fasilitas, $listJenisFasilitas)) {
                    $listJenisFasilitas[$model->nama_fasilitas] = $tempNamaFasilitas;
                }

                if (!empty($attributesDetail)) {
                    foreach ($attributesDetail as $key => $value) {
                        $temp[$value['fasilitasrsdetail_id']] = $value['nama_fasilitas'];
                    }

                    $modelDetail->nama_fasilitas = $temp;
                }

                return $this->renderPartial('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk menghapus data fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionDelete($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);

            $restMaster = $this->_restMaster->delete('fasilitas-rs/delete?id='.$id);
            $response = json_decode($restMaster->getBody(), true);

            if($response['metadata']['status'] == 200){
                $response['response']['title'] = 'Proses Berhasil';
                $response['response']['text'] = 'Data Berhasil Dihapus!';
                return DocoHelpers::response($response);
            }else{
                $codeHttp = $response['metadata']['status'];
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mendapatkan data fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataFasilitasRs()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            $restMaster = $this->_restMaster->get('fasilitas-rs/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restMaster->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                $haystackNo = [];

                foreach ($data as $key => $value) {
                    if (!in_array($value['fasilitasrs_id'], $haystackNo)) {
                        $no++;
                        $haystackNo[] = $value['fasilitasrs_id'];
                    }
                    $primaryKey = DocoHelpers::encrypt($value['fasilitasrs_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = '';
                    $value['nama_jenis'] = $value['nama_jenis'];
                    $value['nama_fasilitas'] = $value['nama_fasilitas'];
                    $value['checkbox'] = '';
                    unset($value['fasilitasrs_id']);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mendapatkan nama fasilitas
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetNamaFasilitas($jenis_fasilitas)
    {
        try {
            $restMaster = $this->_restMaster->get('fasilitas-rs/get-nama-fasilitas?jenis_fasilitas='.$jenis_fasilitas);
            $body = json_decode($restMaster->getBody(), true);
            $nama_fasilitas = $body['response'];

            echo json_encode($nama_fasilitas);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mengexport pdf data fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/fasilitas_rs.pdf";

            $restMaster = $this->_restMaster->get('fasilitas-rs/export-pdf?'.http_build_query($yiiRestfulParams), ['save_to' => $path]);

            return DocoHelpers::downloadPdf($restMaster, $path, 'fasilitas-rs');
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mengexport excel data fasilitas rm
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Master Fasilitas.xlsx";

            $restMaster = $this->_restMaster->get('fasilitas-rs/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}