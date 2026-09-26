<?php

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DHtml;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class PasienMeninggalController extends DocoController
{
    protected $allowAction = ['*'];
    protected $title = "Manajemen Pasien Meningggal";
    protected $_module = '/master/pasien-meninggal/';
    protected $restMaster;

    public function init()
    {
        parent::init();
        $this->restMaster = Yii::$app->docoRest->master;
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
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->title;
        $response = $this->guzzleExec($this->restMaster, [
            'url' => 'pasien-meninggal/index',
            'method' => 'get'
        ]);

        $konfigCron = ArrayHelper::getValue($response, 'data.konfig_cron.kode_id', []);
        $konfigValidate = false;
        if (isset($konfigCron) && !empty($konfigCron)) {
            $konfigValidate = filter_var($konfigCron, FILTER_VALIDATE_BOOLEAN);
        }

        $statusActivasi = [
            '1' => 'Aktif',
            '0' => 'Tidak Aktif'
        ];

        $statusActivasi = json_encode($statusActivasi);
        return $this->render('index', compact('title', 'konfigValidate', 'statusActivasi'));
    }

    public function actionGetDataPasienMeninggal()
    {
        $request = Yii::$app->request;
        try {
            $filter =  DocoDatatableHelper::advancedFilterParam();
            $response = $this->restMaster->get('pasien-meninggal/get-data-pasien-meninggal?' . http_build_query($filter), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $row = [];
            foreach ($body['response']['data'] as $value) {
                $no++;
                $pendaftaranId = $value['pendaftaran_id'];
                if (! $value['is_deleted']) {
                    $statusAktif = true;
                } else {
                    $statusAktif = false;
                }

                $value['rowNum'] = $no;
                $value['nama_norm'] = $value['no_rekam_medik'] . ' - ' . $value['nama_pasien'];
                $value['status_aktif'] = DocoHelpers::switchStatus($statusAktif, $pendaftaranId, 'change-status-pasien-meninggal');
                $row[] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount'],
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionChangeStatusPasien()
    {
        try {
            $request = Yii::$app->request;
            $statusPasien = $request->post('status');
            $pendaftaranId = $request->post('pendaftaran_id');
            
            return $this->guzzleExec($this->restMaster, [
                'url' => 'pasien-meninggal/active-pasien',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $pendaftaranId,
                        'status' => $statusPasien
                    ],
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $th) {
            $result['error'] = $th->getMessage();
            return $result;
        }
    }

    public function actionChangeStatusCron()
    {
        try {
            $request = Yii::$app->request;
            $statusCron = $request->post('status');

            return $this->guzzleExec($this->restMaster, [
                'url' => 'pasien-meninggal/active-cron',
                'payload' => [
                    'query' => [
                        'status' => $statusCron
                    ],
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $th) {
            $result['error'] = $th->getMessage();
            return $result;
        }
    }
}
