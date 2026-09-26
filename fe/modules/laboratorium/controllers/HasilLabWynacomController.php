<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Laboratorium
 * @copyright 13 Juli 2018 aweutist
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\laboratorium\models\SpecimentForm;
use yii\helpers\ArrayHelper;

class HasilLabWynacomController extends DocoController
{
    protected $_title = "Hasil Pemeriksaan Laboratorium Wynacom";
    protected $_module = '/laboratorium/hasil-lab-wynacom';
    protected $_restLab;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
    }

    public function actionIndex($id)
    {
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $cache = Yii::$app->cache;
        $title = $this->_title;
        $data = $this->getDataApi($id);
        $data_pasien = !empty($data['data_pasien']) ? $data['data_pasien'] : [];
        $pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
        $data_wynacom = $this->_restLab->request('GET', 'hasil-lab-wynacom/data-wynacom?id=' . $pasienmasukpenunjang_id);
        $wynacom = json_decode($data_wynacom->getBody(),TRUE);
        $row = [];
        $no = 0;
        foreach ($wynacom['response'] as $key => $value) {
            $no++;
            $value['test_nama_lis']       = empty($value['test_nama_lis']) ? '-' : $value['test_nama_lis']; 
            $value['hasil']               = empty($value['hasil']) ? '-' : $value['hasil']; 
            $value['satuan']              = empty($value['satuan']) ? '-' : $value['satuan']; 
            $value['nilai_rujukan']       = empty($value['nilai_rujukan']) ? '-' : $value['nilai_rujukan']; 
            $value['test_method']         = empty($value['test_method']) ? '-' : $value['test_method']; 
            $value['rowNum']              = $no;
            $row[$key]                    = $value;
            }
        $count_expertise = $data['count_data_expertise'];
        $catatan = !empty($data['data_hasil_lab']['catatan_instruksi'])
            ? $data['data_hasil_lab']['catatan_instruksi']
            : '';
        return $this->render('index', get_defined_vars());
    }

    private function getDataApi($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restLab->request('GET', 'hasil-lab-wynacom/generate-api?id=' . $id);
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_pasien' => $body['response']['data-pasien'],
                'data_hasil_lab' => $body['response']['data-hasil-lab'],
                'count_data_expertise' => $body['response']['count-data-expertise'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetData($id)
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restLab->request('get', 'hasil-lab-wynacom/index?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['samplelab_id']);
                $pasienmasukpenunjang_id = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $value['primary'] = $primaryKey;
                $value['penunjang_id'] = $pasienmasukpenunjang_id;
                unset($value['samplelab_id']);
                $value['rowNum'] = $no;
                $value['is_expertise'] = empty($value['is_expertise']) ? '' : '&#10003;';
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => count($body['response']['data']),
                'recordsFiltered' => count($body['response']['data'])
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionCetakHasil($id, $penunjang_id)
    {
        $id = DocoHelpers::decrypt($id);
        $penunjang_id = DocoHelpers::decrypt($penunjang_id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
        try {
            $response = $this->_restLab->get('input-hasil/cetak-pdf-wynacom?id=' . $id . '&penunjang_id=' . $penunjang_id, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function getDataPasienMasukPenunjang($id){
        $responseWynacom = $this->_restLab->get('input-hasil/get-pasien-masuk-penunjang?id=' . $id);
        return json_decode($responseWynacom->getBody(),TRUE)['response'];
    }

    public function getCetakHasilWynacom($path, $id){
        $response = $this->_restLab->get('input-hasil/cetak-hasil-pdf-wynacom?id=' . $id, [
            'save_to' => $path
        ]);
        return json_decode($response->getBody(), true);
    }


    public function actionCetak($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
        try {
            $responseWynacom = $this->getDataPasienMasukPenunjang($id);
            $test = Yii::$app->docoFTP->path();
            if ( !empty($responseWynacom['image_link'] )) {
                DocoHelpers::previewPdfFromFTP(Yii::$app->docoFTP->path(), $responseWynacom['image_link']);
            }else{
                $responseWynacom = $this->getCetakHasilWynacom($path, $id);
                return DocoHelpers::previewPdf($path);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakWynacom($id)
    {   
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
        try {
            $response = $this->_restLab->get('input-hasil/cetak-hasil-pdf-wynacom?id=' . $id, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionVerifikasi($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restLab->request('GET', 'hasil-lab-wynacom/verifikasi?id=' . $id);
            $body = json_decode($response->getBody(), true);

            if (!$body['response']) {
                $result['response']['title'] = Yii::t('fe', 'Tidak Berhasil !');
                $result['response']['text'] = Yii::t('fe', 'Masih ada pemeriksaan yang belum memiliki expertise');
                return DocoHelpers::response($result, 422);
            } else {
                $result['response']['text'] = Yii::t('fe', 'Data berhasil di verifikasi');
            }
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}