<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 11:35:24
 */

namespace Doco\rm\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class HistoryTempatTidurController extends DocoController
{
	/**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restRm;
    protected $allowAction = ['*'];

    /**
     * @todo Init function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    /**
     * @todo Behaviors function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    /**
     * @todo Fungsi untuk menampilkan halaman awal history tempat tidur
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $restRm = $this->_restRm->get('history-tempat-tidur/get-data-bundle');
            $response = json_decode($restRm->getBody(), true)['response'];

            $listRuangan = ArrayHelper::map($response['master']['ruangan'], 'ruangan_id', 'ruangan_nama');
            $listKamar = ArrayHelper::map($response['master']['kamar'], 'kamarruangan_id', 'kamarruangan_nokamar');
            $listKeterangan = ArrayHelper::map($response['lookup']['keterangan_history_tt'], 'lookup_id', 'lookup_name');
            $listStatus = [
                1 => Yii::t('fe', 'Aktif'),
                0 => Yii::t('fe', 'Tidak Aktif'),
            ];

            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk melakukan proses export pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            $docoVars = Yii::$app->docoVars;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/History Master Tempat Tidur.pdf";
            $ruangan_id = $docoVars->workspace('ruangan_id');
            $user_identity = Yii::$app->session->get('user_identity');
            $nama_pegawai = $user_identity['nama_pegawai'];

            $restRm = $this->_restRm->get('history-tempat-tidur/export-pdf?ruangan_id='.$ruangan_id.'&nama_pegawai='.$nama_pegawai.'&'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/History Master Tempat Tidur.xlsx";

            $restRm = $this->_restRm->get('history-tempat-tidur/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data history tempat tidur
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataHistoryTempatTidur()
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

            $restRm = $this->_restRm->get('history-tempat-tidur/get-data-history-tempat-tidur?'.http_build_query($yiiRestfulParams));
            $body = json_decode($restRm->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $value['no'] = $no;
                    $value['tgl_tthistory'] = DocoHelpers::convDateTime($value['tgl_tthistory'], false, true);
                    $value['status'] = $value['status'] ? Yii::t('fe', 'Aktif') : Yii::t('fe', 'Tidak Aktif');
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
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data filter ruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionFilterRuangan()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $params = '';

            if ($request->post()) {
                $depdrop_parents = $request->post('depdrop_parents');
                $parent_label = $depdrop_parents[0];
                $params = '?kamarruangan_id='.$parent_label;
            }

            $result = [];
            $result['output'] = [];
            $result['selected'] = '';

            $response = $this->_restRm->get('history-tempat-tidur/get-ruangan'.$params);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data filter kamar
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionFilterKamar()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            
            $result = [];
            $result['output'] = [];
            $result['selected'] = '';

            $response = $this->_restRm->get('history-tempat-tidur/get-kamar-ruangan?ruangan_id='.$parent_label);
            $body = json_decode($response->getBody(), true);

            foreach ($body['response'] as $value) {
                $result['output'][] = [
                    'id' => $value['kamarruangan_id'],
                    'name' => $value['kamarruangan_nokamar']
                ];
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}