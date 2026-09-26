<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\MergeRekammedikForm;
use yii\base\Exception;
use yii\web\Response;

class MergeRekammedikController extends DocoController
{
    protected $_title = "Penggabungan Nomor Rekam Medik";
    protected $_restPendaftaran;
    protected $_module = 'merge-rekammedik/';

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
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
        try {
            $title = $this->_title;
            $model = new MergeRekammedikForm;
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetPasien($kunjungan = false)
    {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $param = $request->get('q', null);
            $id = $request->get('id', null);
            $results = [];
            if($kunjungan) {
                $rmAsal = $request->get('no_rekam_medik_asal');
                $rmTujuan = $request->get('no_rekam_medik_tujuan');
                if($rmAsal && $rmTujuan) {
                    $restPendaftaran = $this->_restPendaftaran->get($this->_module.'get-info-pasien', [
                        'query' => [
                            'no_rekam_medik_asal' => $rmAsal,
                            'no_rekam_medik_tujuan' => $rmTujuan,
                        ]
                    ]);
                    $response = json_decode($restPendaftaran->getBody(), true);
                    $status = $response['metadata']['status'];
                    if($status == 200) {
                        $response = $response['response'];
                        return [
                            'info_pasien_asal' => $response['info_asal']['info_pasien'],
                            'info_pasien_tujuan' => $response['info_tujuan']['info_pasien'],
                            'kunjungan_pasien_asal' => $response['info_asal']['kunjungan_pasien'],
                            'kunjungan_pasien_tujuan' => $response['info_tujuan']['kunjungan_pasien']
                        ];
                    }
                }
            } else {
                if ($param) {
                    $restPendaftaran = $this->_restPendaftaran->get($this->_module.'data-pasien', [
                        'query' => [
                            'id' => $id,
                            'q' => $param
                        ]
                    ]);
                    $response = json_decode($restPendaftaran->getBody(), true);
    
                    $status = $response['metadata']['status'];
    
                    if ($status == 200) {
                        $listPasien = $response['response']['data'];
                        foreach ($listPasien as $key => $each) {
                            $tglLahir = date('d-m-Y', strtotime($each['tanggal_lahir']));
                            $no_and_nama = $each['no_rekam_medik'] . " / " . $each['nama_pasien'] . " / " . $tglLahir;
                            $eachData = [
                                'id' => $each['no_rekam_medik'],
                                'text' => $no_and_nama,
                                'no_rekam_medik' => $each['no_rekam_medik'],
                                'nama_pasien' => $each['nama_pasien'],
                                'pasien_id' => $each['pasien_id'],
                            ];
                            $results[] = $eachData;
                        }
                    }
                }
            }
            return ['results' => $results];
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionConfirmMerge() 
    {
        try {
            $request = Yii::$app->request->get();
            $rmAwal = (isset($request['rm_awal']) ? $request['rm_awal'] : null);
            $rmTujuan = (isset($request['rm_tujuan']) ? $request['rm_tujuan'] : null);
            $title = 'Konfirmasi '.$this->_title;
            $username = Yii::$app->docoVars->user('nama');

            $mergeForm = new MergeRekammedikForm;
            $mergeForm->no_rekammedik_asal = $rmAwal;
            $mergeForm->no_rekammedik_tujuan = $rmTujuan;
            return $this->renderAjax('_modal_konfirm', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }   
    }

    public function actionSave() 
    {
        try {
            $post = Yii::$app->request->post();
            $mergeForm = new MergeRekammedikForm;
            $mergeForm->scenario = 'save';
            $mergeForm->attributes = $post['MergeRekammedikForm'];
            if($mergeForm->validate()) {
                $response = $this->_restPendaftaran->get($this->_module.'save', [
                    'form_params' => $mergeForm->attributes
                ]);
                $result = json_decode($response->getBody(), true);
                return DocoHelpers::response($result);
            } else {
                return DocoHelpers::response($mergeForm->errors,422,'MergeRekammedikForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }   
    }
}
