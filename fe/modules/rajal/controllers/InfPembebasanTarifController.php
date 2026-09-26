<?php 
// Author : Ardi Pratama

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\modules\rajal\models\PembebasanTarifForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InfPembebasanTarifController extends DocoController
{
    protected $_title;
    protected $_restRajal;
    protected $_restMaster;
    protected $_module = '/rm/inf-pembebasan-tarif/';
    protected $_ruangan_id;

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi Pembebasan Tarif');
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
        
        $this->_ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
    }

    public function actionIndex()
    {
        $title = $this->_title;

        $list_data = $this->getListData();
        $ddlKelasPelayanan = $list_data['data_kelaspelayanan'];
        $ddlJenisKasusPenyakit = $list_data['data_jeniskasuspenyakit'];
        $ddlDokter = $list_data['data_dokter'];
        $ddlStatusbayar = $list_data['data_statusbayar'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        if(isset($yiiRestfulParams['advanced-filter']['nama_dokter'])){
            $yiiRestfulParams['advanced-filter']['pegawai_id'] = $yiiRestfulParams['advanced-filter']['nama_dokter'];
            unset($yiiRestfulParams['advanced-filter']['nama_dokter']);
        }
        if(isset($yiiRestfulParams['advanced-filter']['kelaspelayanan_nama'])){
            $yiiRestfulParams['advanced-filter']['kelaspelayanan_id'] = $yiiRestfulParams['advanced-filter']['kelaspelayanan_nama'];
            unset($yiiRestfulParams['advanced-filter']['kelaspelayanan_nama']);
        }
        if(isset($yiiRestfulParams['advanced-filter']['jeniskasuspenyakit_nama'])){
            $yiiRestfulParams['advanced-filter']['jeniskasuspenyakit_id'] = $yiiRestfulParams['advanced-filter']['jeniskasuspenyakit_nama'];
            unset($yiiRestfulParams['advanced-filter']['jeniskasuspenyakit_nama']);
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRajal->get('inf-pembebasan-tarif/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pembebasantarif_id']);
                unset($value['pembebasantarif_id']);
                $value['primary'] = $primaryKey;

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

    public function actionUpdate($id)
    {
        try {
            $title = 'Ubah Pembebasan Tarif';
            $model = new PembebasanTarifForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $list_data = $this->getListData();
            
            $ddlJabatan = $list_data['data_jabatan'];
            $id = DocoHelpers::decrypt($id);

            if ($request->post()) {
                $model->load($request->post());
                $model->total_tarifpelayanan = $model->total_tagihan;
                if ($model->validate()) {
                    $model->tgl_pembebasantarif = DocoHelpers::convDateTime($model->tgl_pembebasantarif);
                    $response = $this->_restRajal->request('PUT', 'inf-pembebasan-tarif/update',[
                                        'query' => ['id' => $id],
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,true);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PembebasanTarifForm');
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
                    if(isset($model->tgl_pembebasantarif)){
                        $model->tgl_pembebasantarif = DocoHelpers::convDateTime($model->tgl_pembebasantarif);
                    }
                    return $this->renderAjax('form',get_defined_vars());
                } else {
                    throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    /**
    * @param integer $id
    * 
    * @return array|mix
    * @throws GuzzleHttp\Exception\RequestException
    * @throws Exception
    */

    public function find($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restRajal->request('GET', 'inf-pembebasan-tarif/view',[
                            'query' => ['id' => $id ]
                        ]);
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
            $response = $this->_restRajal->request('DELETE', 'inf-pembebasan-tarif/delete',[
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

    private function getListData()
    {
        try {
            $request = $this->_restRajal->get('inf-pembebasan-tarif/get-list-data?ruangan_id='. $this->_ruangan_id);
            $body = json_decode($request->getBody(),TRUE);

            $list_kelaspelayanan = isset($body['response']['data-kelaspelayanan']) ? $body['response']['data-kelaspelayanan'] : [] ;
            $data_kelaspelayanan = ArrayHelper::map($list_kelaspelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');
            
            $list_jeniskasuspenyakit = isset($body['response']['data-jeniskasuspenyakit']) ? $body['response']['data-jeniskasuspenyakit'] : [];
            $data_jeniskasuspenyakit = ArrayHelper::map($list_jeniskasuspenyakit, 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

            $list_dokter = isset($body['response']['data-dokter']) ? $body['response']['data-dokter'] : [];
            $data_dokter = ArrayHelper::map($list_dokter, 'pegawai_id', 'nama_pegawai');

            $list_statusbayar = isset($body['response']['data-statusbayar']) ? $body['response']['data-statusbayar'] : [];
            $data_statusbayar = ArrayHelper::map($list_statusbayar, 'lookup_id', 'lookup_name');

            $list_jabatan = isset($body['response']['data-jabatan']) ? $body['response']['data-jabatan'] : [];
            $data_jabatan = ArrayHelper::map($list_jabatan, 'jabatan_id', 'jabatan_nama');

            return [
                'data_kelaspelayanan' => $data_kelaspelayanan,
                'data_jeniskasuspenyakit' => $data_jeniskasuspenyakit,
                'data_dokter' => $data_dokter,
                'data_statusbayar' => $data_statusbayar,
                'data_jabatan' => $data_jabatan,
            ];
        } catch (Exception $e) {
            return [
                'data_kelaspelayanan'=> [],
                'data_jeniskasuspenyakit'=> [],
                'data_dokter'=> [],
                'data_statusbayar'=> [],
                'data_jabatan'=> [],
                'message'=> $e->getMessage(),
            ];
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $id = DocoHelpers::decrypt($request->get('id'));

        $path = Yii::getAlias("@download") . "/pembebasantarif.pdf";
        try {
            $response = $this->_restRajal->get('inf-pembebasan-tarif/export-pdf?id='. $id);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadPdf($response, $path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}