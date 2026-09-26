<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use Exception;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;
use app\modules\fisioterapi\models\BatalOrderPenunjangForm;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
use app\widgets\filters\DropdownStatusKunjunganFisioterapi\DHSelectStatusKunjunganFisioterapi;

class InformasiProgramFisioterapiRajalController extends DocoController
{
    protected $_module = '/fisioterapi/informasi-program-fisioterapi-rajal/';
    protected $_restFisioterapi;

    private $isFirstSelect = true;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['batal-program-form'] = ['GET'];
        $verbs['batal-program'] = ['POST'];
        $verbs['get-data'] = ['GET'];
        $verbs['edit'] = ['GET'];
        $verbs['edit-tambah-tindakan'] = ['GET'];
        $verbs['update-data'] = ['POST'];
        return $verbs;
    }

    public function init()
    {
        parent::init();
        $this->_restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function actions()
    {
        $actions = parent::actions();
        $newAction = [
            'index' => 'Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal\IndexAction',
            'get-data' => 'Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal\GetDataAction',
            'edit' => 'Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal\EditAction',
            'edit-tambah-tindakan' => 'Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal\EditTambahTindakanAction',
            'update-data' => 'Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal\UpdateDataAction'
        ];
        return array_merge($actions, $newAction);
    }

    public function generateActionBtnJadwal($dataPenjadwalan = null, $key, $readOnly = false) {
        if (isset($dataPenjadwalan)) {
            $btnRealisasi = null;
            $bgColor = null;
            $jadwalRealisasi = ArrayHelper::getValue($dataPenjadwalan, 'realisasi');
            $statusKunjungan = ArrayHelper::getValue($dataPenjadwalan, 'status_kunjungan_id');
            $statusKunjunganNama = ArrayHelper::getValue($dataPenjadwalan, 'status_kunjungan');
            $realisasi = ArrayHelper::getValue($dataPenjadwalan, 'realisasi');

            if ($statusKunjungan == DocoConstants::STATUS_KUNJUNGAN_FISIO_DROP_OUT) {
                $btnRealisasi = $statusKunjunganNama;
                $bgColor = 'background-color: red; color: white;';
            } else if ($statusKunjungan == DocoConstants::STATUS_KUNJUNGAN_FISIO_KETIDAKHADIRAN) {
                $btnRealisasi = $statusKunjunganNama;
                $bgColor = 'background-color: yellow;';
            } else if ($statusKunjungan == DocoConstants::STATUS_KUNJUNGAN_FISIO_REALISASI) {
                $btnRealisasi = $realisasi;
            } else if($jadwalRealisasi != '-' && !is_null($jadwalRealisasi)) {
                $btnRealisasi = $jadwalRealisasi;
            } else {
                $className = "status_kunjungan_$key";
                if ($this->isFirstSelect) {
                    $className = "$className isFirst";
                }
                $btnRealisasi = DHSelectStatusKunjunganFisioterapi::widget([
                    'id' => 'status_kunjungan_fisio_options_' . $key,
                    'prompt' => 'Pilih Status',
                    'className' => $className
                ]);
                if($readOnly == true || $readOnly == 'true'){
                    $btnRealisasi = '';
                }
                $this->isFirstSelect = false;
            }
            return [
                'btnRealisasi' => $btnRealisasi,
                'style' => $bgColor
            ];
        }
    }

    /**
     * @method actionDetail (Show Modal Detail Jadwal)
     * @param int $id (programterapi_id)
     * @return view
     */
    public function actionDetail($id = null)
    {
        try {
            if ($id == null) {
                $id = Yii::$app->request->get('id', null);
            }
            $readOnly = Yii::$app->request->get('readonly', null);
            $restTerapi = $this->_restFisioterapi->get('informasi-program-fisioterapi-rajal/get-program-terapi-detail?id='.$id);
            $response = json_decode($restTerapi->getBody(), true);
            $data = $response['response'];
            $dataPenjadwalan = ArrayHelper::getValue($data, 'data_penjadwalan');
            $maxKeteranganDropOut = 100;

            $this->isFirstSelect = true;
            foreach ($dataPenjadwalan as $key => $value) {
                $generateUtility = $this->generateActionBtnJadwal($value, $key, $readOnly);
                $dataPenjadwalan[$key]['btnJadwal'] = ArrayHelper::getValue($generateUtility, 'btnRealisasi');
                $dataPenjadwalan[$key]['style'] = ArrayHelper::getValue($generateUtility, 'style');
            }

            $dataView = [
                'id' => DocoHelpers::decrypt($id),
                'data' => $data['schedule_terapi'],
                'detail' => $data['schedule_program'],
                'terapi_schedule_fisio' => $data['terapi_schedule_fisio'],
                'scheduledetailDoctor' => $data['scheduledetailDoctor'],
                'data_penjadwalan' => $dataPenjadwalan,
                'maxKeteranganDropOut' => $maxKeteranganDropOut,
                'readOnly' => $readOnly 
            ];

            if($readOnly == 'true' || $readOnly == true){
                return $this->renderAjax('modal/detail-jadwal-readonly', $dataView);
            }else{
                return $this->renderAjax('modal/detail-jadwal', $dataView);
            }

            
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('data');
        $programTerapiId = Yii::$app->request->post('id');
        try {
            $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'POST',
                'url' => 'informasi-program-fisioterapi-rajal/save',
                'payload' => [
                    'form_params' => [
                        'programterapi_id' => $programTerapiId,
                        'status_program_id' => DocoConstants::STATUS_PROGRAM_FISIO_CLOSE,
                        'data' => $data
                    ]
                ],
                'with_metadata' => true
            ]);
            $response = DocoHelpers::response($response);
            return $response;
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            (new DocoHelpers)->logError($e);
            return ['error' => $e->getMessage()];
        }
    }

    public function actionUpdateJadwal($id)
    {
        $date = Yii::$app->request->post('date');
        $kunjunganke = Yii::$app->request->post('kunjunganke');
        $restFisioterapi = $this->_restFisioterapi->post('informasi-program-fisioterapi-rajal/update-jadwal?id='.$id,[
            'json' => [
                'tgl_penjadwalan' => $date,
                'kunjunganke' => $kunjunganke
            ]
        ]);
        $response = json_decode($restFisioterapi->getBody(), true);
        if ($response['metadata']['status'] == 200) {
            return DocoHelpers::response($response);
        } else {
            $message = Yii::t('fe', 'Tidak ada data yang disimpan');
            return DocoHelpers::responseTemplate(500, 'Error', [], 
                [
                    'title' => Yii::t('fe', 'Proses Gagal'), 
                    'text' => $message,
                    'message' => $message,
                ]
            );
        }
    }

    public function actionDetailCppt()
    {
        try {
            $id = Yii::$app->request->get('id');
            $decrypted_id = DocoHelpers::decrypt($id);
            $restTerapi = $this->_restFisioterapi->get('informasi-program-fisioterapi-rajal/detail-cppt?id='.$decrypted_id);
            $response = json_decode($restTerapi->getBody(), true);
            $data = ArrayHelper::getValue($response, 'response', []);
            $cpptPatient = ArrayHelper::getValue($data, 'cpptPatient', []);
            $aDiagUtama = ArrayHelper::getValue($cpptPatient, 'a_diag_utama', []);
            return $this->renderAjax('modal/detail-cppt', [
                'id' => $id,
                'data' => ArrayHelper::getValue($data, 'data', []),
                'cpptPatient' => ArrayHelper::getValue($data, 'cpptPatient', []),
                'scheduledetailDoctor' => ArrayHelper::getValue($data, 'scheduledetailDoctor', [])
            ]);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @method actionBatalProgramForm (Batal Program Form)
     * @param String $id atau programterapi_id (encrypted)
     */
    public function actionBatalProgramForm()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->responseJson(400, 'Program Terapi ID Harus Diisi');
        }
        try {
            $response = $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'informasi-program-fisioterapi-rajal/get-attributes-batal-program-form',
                'method' => 'GET'
            ], [
                'query' => [
                    'id' => $id
                ]
            ]);
            $model = new BatalOrderPenunjangForm;
            $informasiPasien = $response['informasiPasien'];
            $tglPermintaan = !empty($informasiPasien['tgl_permintaan']) ? date('d-M-Y' , strtotime($informasiPasien['tgl_permintaan'])) : date('d-M-Y');
            $strTglPermintaan = (string) $tglPermintaan;
            $scheduleDetailDokter = $response['scheduleDetailDokter'];
            $listDokter = $response['listDokter'];
            $scheduleDetailProgram = $response['scheduleDetailProgram'];
            $model->peg_menyetujui_id = Yii::$app->docoVars->user("id_pegawai");
            $model->tgl_batalorder = !empty($dataBatal['tgl_batalorder']) ? date('d-M-Y' , strtotime($dataBatal['tgl_batalorder'])) : date('d-M-Y');
            return $this->renderAjax('modal/form-batal', get_defined_vars());
        } catch (RequestException $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionBatalProgram()
    {
        $request = Yii::$app->request;
        $model = new BatalOrderPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $formData = $request->post();
                $form_params['programterapi_id'] = $formData['programterapi_id'];                
                $form_params['pasienkirimkeunitlain_id'] = $formData['pasienkirimkeunitlain_id'];                
                $form_params['peg_menyetujui_id'] = $formData['BatalOrderPenunjangForm']['peg_menyetujui_id'];
                $form_params['tgl_batalorder'] = DocoHelpers::coalesce($formData['BatalOrderPenunjangForm']['tgl_batalorder'], date('Y-m-d H:i:s'));
                $form_params['alasan'] = $formData['BatalOrderPenunjangForm']['alasan'];
                $response = $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                    'url' => 'informasi-program-fisioterapi-rajal/batal-program',
                    'returnResponse' => true,
                    'method' => 'POST'
                ], [
                    'form_params' => $form_params
                ]);
                return $response;
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } 
    }
}
