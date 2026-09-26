<?php

/**
 * @Author: Sigit
 * @Date:   2019-10-16 16:43:55
 */

namespace Doco\penjaminasuransi\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

class LaporanKlaimIndividualController extends DocoController
{

    protected $_title = "Laporan Klaim Individual";
    protected $_restPenjaminAsuransi;

    public function init()
    {
        parent::init();
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
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
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        $response = $this->_restPenjaminAsuransi->get('laporan-klaim-individual/get-data-filter?ruangan_id='.$ruangan_id);
        $response = json_decode($response->getBody(), true);
        $response = $response['response'];

        $filterJenisRawat = ArrayHelper::map($response['jenis_rawat'], 'lookup_id', 'lookup_name');
        $filterPeriode = ArrayHelper::map($response['periode'], 'lookup_id', 'lookup_name');
        $filterPenjamin = ArrayHelper::map($response['penjamin'], 'penjamin_id', 'penjamin_nama');
        $filterKelasRawat = ArrayHelper::map($response['kelas_rawat'], 'kelaspelayanan_nama', 'kelaspelayanan_nama');
        $filterCaraPulang = ArrayHelper::map($response['cara_pulang'], 'lookup_name', 'lookup_name');
        $filterJenisTarif = ArrayHelper::map($response['jenis_tarif'], 'lookup_name', 'lookup_name');
        $filterPetugas = ArrayHelper::map($response['petugas'], 'nama_pegawai', 'nama_pegawai');

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];
            $result = [];
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if (!empty($yiiRestfulParams['advanced-filter'])) {
                $filter = $yiiRestfulParams['advanced-filter'];

                if (isset($filter['periode'])) {
                    if (isset($filter['filter'])) {
                        if ($filter['filter'] == 647) {
                            $yiiRestfulParams['advanced-filter']['tgl_keluar'] = $filter['periode'];
                        } elseif ($filter['filter'] == 648) {
                            $yiiRestfulParams['advanced-filter']['tgl_masuk'] = $filter['periode'];
                        }

                        unset($yiiRestfulParams['advanced-filter']['filter']);
                    }
                }

                unset($yiiRestfulParams['advanced-filter']['periode']);
            }

            $response = $this->_restPenjaminAsuransi->get('laporan-klaim-individual/get-data?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);

            if (!empty($body['response']['data'] )) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $value['rowNum'] = $no;
                    $value['tgl_masuk'] = date('Y-m-d', strtotime($value['tgl_masuk']));
                    $value['tgl_keluar'] = date('Y-m-d', strtotime($value['tgl_keluar']));
                    $value['nama_pasien'] = $value['no_rekam_medik'].' - '.$value['nama_pasien'];
                    $value['tgl_group'] = $value['tgl_group'] != '' ? date('Y-m-d', strtotime($value['tgl_group'])) : '';
                    $value['filter'] = '';
                    $value['periode'] = '';
                    $data[$key] = $value;
                }

                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            }

            $result['data'] = $data;
            $result['draw'] = $draw;
            
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