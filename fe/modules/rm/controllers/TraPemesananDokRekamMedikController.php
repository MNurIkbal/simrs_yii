<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-16 11:15:00
 * @Last Modified by: Randy Vianda Putra
 * @Last Modified time: 2018-05-28
 * 
 */

// Namespace
namespace Doco\rm\controllers;

// Using
use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\PemesananDokRekamMedikForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Response;

class TraPemesananDokRekamMedikController extends DocoController
{
    // Define some variables
    protected $_restRm;
    protected $_restMaster;
    protected $_module = 'rm/tra-pemesanan-dok-rekam-medik/';

    // Init
    public function init()
    {
        // Parent init
        parent::init();

        // Rest rm
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    // Behaviors
    public function behaviors()
    {
        // Parent behaviors
        $behaviors = parent::behaviors();

        // Unset some actions
        unset($behaviors['index']);
        unset($behaviors['view']);

        // Return behaviors
        return $behaviors;
    }

    // Action index
    public function actionIndex()
    {
        // Define model
        $model = new PemesananDokRekamMedikForm();

        // Assign date
        $model->tgl_pesandokrm = date('d F, Y', strtotime("NOW"));

        // Try catch
        try {
            $response = $this->_restRm->get('allow/get-api');
            $body = json_decode($response->getBody(), TRUE);
            $resMaster = $body['response']['master'];

            // Render index
            return $this->render('form', get_defined_vars());
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Action create
    public function actionCreate()
    {
        // Define model
        $model = new PemesananDokRekamMedikForm();

        // Get post
        $post = Yii::$app->request->post();

        // Check post
        if (isset($post['PemesananDokRekamMedikForm']) && isset($post['PemesananDokRekamMedikDetailForm'])) {
            // Load model
            $model->load($post['PemesananDokRekamMedikForm'], '');

            // Assign status pesan
            $model->no_pesandokrm = rand(0, 1000000000);
            $model->status_pesan = 398;
            $model->ruanganpemesan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $model->ruangantujuan_id = $post['ruangantujuan_id'];

            // Try catch
            try {
                // Post
                $response = $this->_restRm->request('POST', 'tra-pemesanan-dok-rekam-medik/create', ['form_params' => [
                    'PemesananDokRekamMedik' => $model,
                    'PemesananDokRekamMedikDetail' => $post['PemesananDokRekamMedikDetailForm']
                ]]);
                $body = json_decode($response->getBody(), true);

                // Return
                return DocoHelpers::response($body, false, true);
                // // Return view
                // return $this->redirect(['index']);
            } catch (RequestException $e) {
                // Return
                return DocoHelpers::dataTabelsException($e->getMessage());
            } catch (\Exception $e) {
                // Return
                return DocoHelpers::dataTabelsException($e->getMessage());
            }
        }
        else {
            // Return
            return $this->redirect('index');
        }
    }

    // // Action ruangan
    // public function actionGetRuangan()
    // {
    //     // Get post
    //     $post = Yii::$app->request->post();

    //     // Check post
    //     if (isset($post['depdrop_parents'][0])) {
    //         // Assign instalasi id
    //         $instalasiId = $post['depdrop_parents'][0];

    //         // Try catch
    //         try {
    //             // Get ruangan
    //             $response = $this->_restRm->get('tra-pemesanan-dok-rekam-medik/get-ruangan?instalasi_id='.$instalasiId, ['form_params' => []]);
    //             $body = json_decode($response->getBody(), true);

    //             // Check ruangan
    //             if (!empty($body['response'])) {
    //                 // Define counter
    //                 $ruangan = [];
    //                 $counter = 0;

    //                 // Loop
    //                 foreach ($body['response'] as $index => $value) {
    //                     // Assign ruangan
    //                     $ruangan[$counter]['id'] = $value['ruangan_id'];
    //                     $ruangan[$counter]['name'] = $value['ruangan_nama'];

    //                     // Plus counter
    //                     $counter++;
    //                 }
    //             }

    //             // Return
    //             return Json::encode(['output' => $ruangan, 'selected' => '']);
    //         } catch (RequestException $e) {
    //             // Return
    //             return DocoHelpers::dataTabelsException($e->getMessage());
    //         } catch (\Exception $e) {
    //             // Return
    //             return DocoHelpers::dataTabelsException($e->getMessage());
    //         }
    //     }
    // }

    // Action get posisi dok rekam medik
    public function actionGetPosisiDokRekamMedik($params, $ruangan_id = null, $type) {
        // Check typed
        if ($type == 'dd') {
            // Try catch
            try {
                $results = [];
                // Get posisi dok rekam medik
                $posisiDokRekamMedik = $this->_restRm->get('tra-pemesanan-dok-rekam-medik/get-posisi-dok-rekam-medik-by-norekmed?no_rekam_medik='.$params.'&ruangan_id='.$ruangan_id, ['form_params' => []]);

                // Extract from response
                $posisiDokRekamMedik = json_decode($posisiDokRekamMedik->getBody(), true);
                $posisiDokRekamMedik = isset($posisiDokRekamMedik['response']['data']) ? $posisiDokRekamMedik['response']['data'] : [];

                // Check data
                if (!empty($posisiDokRekamMedik)) {
                    // Define result
                    $results = [];

                    // Loop
                    foreach ($posisiDokRekamMedik as $index => $value) {
                        // Assign text for result
                        $text = $value['no_rekam_medik'] . " - " . $value['nama_pasien'];
                        $results[] = ['id' => $value['posisidokrm_id'], 'text' => $text];
                    }
                }

                // Return
                return Json::encode(['results' => $results]);
            } catch (RequestException $e) {
                // Return
                return DocoHelpers::dataTabelsException($e->getMessage());
            } catch (\Exception $e) {
                // Return
                return DocoHelpers::dataTabelsException($e->getMessage());
            }
        }
        else {
            // Try catch
            try {
                // Get posisi dok rekam medik
                $posisiDokRekamMedik = $this->_restRm->get('tra-pemesanan-dok-rekam-medik/get-posisi-dok-rekam-medik-by-id?id='.$params, ['form_params' => []]);

                // Extract from response
                $posisiDokRekamMedik = json_decode($posisiDokRekamMedik->getBody(), true);
                $posisiDokRekamMedik = isset($posisiDokRekamMedik['response']) ? $posisiDokRekamMedik['response'] : [];

                // Return
                return Json::encode($posisiDokRekamMedik);
            } catch (RequestException $e) {
                // Return
                return DocoHelpers::dataTabelsException($e->getMessage());
            } catch (\Exception $e) {
                // Return
                return DocoHelpers::dataTabelsException($e->getMessage());
            }
        }
    }

    // Export pdf
    public function actionExportPdf($pesandokrm_id)
    {
        // Get request
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        // Download path
        $path = Yii::getAlias("@download")."/dokumen_rekam_medik.pdf";

        // Try catch
        try {
            // Response
            $response = $this->_restRm->get('tra-pemesanan-dok-rekam-medik/export-pdf?pesandokrm_id='.$pesandokrm_id, ['save_to' => $path
            ]);

            // Download pdf
            return DocoHelpers::downloadPdf($response, $path, 'pemesanan-dok-rekam-medik');
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetInstalasi($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-instalasi-by?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['instalasi_id'],
                    'name' => $value['instalasi_nama']
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

    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-ruangan-by'. $params);
            $body = json_decode($response->getBody(), True);
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
}
?>