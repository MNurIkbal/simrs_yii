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
use yii\web\View;

class HasilLabController extends DocoController
{
    protected $_title = "Hasil Pemeriksaan Laboratorium";
    protected $_module = '/laboratorium/hasil-lab';
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
        $count_expertise = $data['count_data_expertise'];
        $catatan = !empty($data['data_hasil_lab']['catatan_instruksi'])
            ? $data['data_hasil_lab']['catatan_instruksi']
            : '';

        $pendaftaran_id = DocoHelpers::encrypt($data_pasien['pendaftaran_id']);
        $is_bayar = isset($data_pasien['is_bayar']) ? $data_pasien['is_bayar'] : false;
        return $this->render('index', get_defined_vars());
    }

    private function getDataApi($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restLab->request('GET', 'hasil-lab/generate-api?id=' . $id);
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
            $response = $this->_restLab->request('get', 'hasil-lab/index?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
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

    public function actionCetakHasil($id = null, $penunjang_id)
    {
        $id = DocoHelpers::decrypt($id);
        $penunjang_id = DocoHelpers::decrypt($penunjang_id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $modulAlias = $activeWorkspace['modul_alias'];
        try {
            $response = $this->_restLab->get('input-hasil/cetak-hasil-pdf', [
                'save_to' => $path,
                'query' => [
                    'pelayanan_id' => $id,
                    'id' => $penunjang_id,
                    'modul' => $modulAlias,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path, $response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetak($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
        $activeWorkspace = Yii::$app->session->get('active_workspace');
        $modulAlias = $activeWorkspace['modul_alias'];
        try {
            $response = $this->_restLab->get('input-hasil/cetak-hasil-pdf', [
                'save_to' => $path,
                'query' => [
                    'id' => $id,
                    'modul' => $modulAlias,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path, $response);
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
            $response = $this->_restLab->request('GET', 'hasil-lab/verifikasi?id=' . $id);
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

    public function actionUnverifikasi($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restLab->request('GET', 'hasil-lab/unverifikasi?id=' . $id);
            $body = json_decode($response->getBody(), true);
            $result['response']['text'] = Yii::t('fe', 'Data berhasil di Unverifikasi');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function registerJsVar($key, $val){
      $this->registerJs("var $key = ".json_encode($val).";", View::POS_HEAD);
    }
}