<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DHtml;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\fisioterapi\models\ResumeMedisFisioForm;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

class ResumeMedisController extends DocoController
{
    protected $allowAction = ['*'];
    protected $restFisio;
    public function init()
    {
        parent::init();
        $this->restFisio = Yii::$app->docoRest->fisoterapi;
    }

    public function actionIndex()
    {
        $title = 'Resume Medis';
        $params = $this->getParamDecrypted();
        $pendaftaranId    = ArrayHelper::getValue($params, 'pendaftaran_id');
        $programterapiIds = ArrayHelper::getValue($params, 'programterapi_ids_string');
        $pasienId         = ArrayHelper::getValue($params, 'pasien_id');

        $model = $this->initResumeMedisModel($pendaftaranId, $pasienId, $programterapiIds);
        $response = $this->fetchResumeMedisData($pendaftaranId);
        $data = ArrayHelper::getValue($response, 'data', []);
        $cppt = ArrayHelper::getValue($response, 'cppt', []);
        $pendaftaran = ArrayHelper::getValue($response, 'pendaftaran', []);
        
        $dokter = $this->mapDokter(ArrayHelper::getValue($response, 'dokter', []));
        $pasienAdmisiId = ArrayHelper::getValue($pendaftaran, 'pasienadmisi_id');
        $tglPendaftaran = $this->formatDate(ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran'));
        $tglPasienPulang = ArrayHelper::getValue($pendaftaran, 'tglpasienpulang');

        if (!empty($data)) {
            $model->attributes = $data;
        }

        $tglMasuk = $model->tgl_masuk ? $model->tgl_masuk : $tglPendaftaran;
        $model->tgl_masuk  = date('d-M-Y', strtotime($tglMasuk));
        if($pasienAdmisiId) {
            $tglKeluar = $model->tgl_keluar ? $model->tgl_keluar : $tglPasienPulang;
            $tglKeluar = $tglKeluar ? $this->formatDate($tglKeluar) : '';
            $model->tgl_masuk  = date('d-M-Y', strtotime($tglPendaftaran));
            $model->tgl_keluar = $tglKeluar ? date('d-M-Y', strtotime($tglKeluar)) : '';
        }
        else {
            $model->tgl_keluar = $model->tgl_masuk;
        }

        $this->collectDiagnosa($model, $data, $cppt);
        $disabledCetakan = $data ? '' : 'disabled';
        return $this->renderAjax('resume-medis', compact(
            'title', 'data', 'model', 'pendaftaranId', 'tglPendaftaran', 'dokter',
            'disabledCetakan'
        ));
    }

    private function initResumeMedisModel($pendaftaranId, $pasienId, $programterapiIds)
    {
        $model = new ResumeMedisFisioForm();
        $model->pendaftaran_id = $pendaftaranId;
        $model->pasien_id = $pasienId;
        $model->program_terapi_ids = $programterapiIds;
        return $model;
    }

    private function fetchResumeMedisData($pendaftaranId)
    {
        return $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'resume-medis/get-data-resume-medis',
            'method' => 'get',
            'payload' => [
                'query' => ['pendaftaran_id' => $pendaftaranId]
            ]
        ]);
    }

    private function mapDokter(array $dokter)
    {
        return ArrayHelper::map($dokter, 'id', 'text');
    }

    private function formatDate($date)
    {
        return $date ? date('Y-m-d', strtotime($date)) : null;
    }

    public function actionGetNewDiagnosa($q = '', $type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0, $is_perawat = 0, $page = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['results' => []];

        try {
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $typeMap = [
                'diagnosa_masuk'    => DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK,
                'diagnosa_utama'    => DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA,
                'diagnosa_penyerta' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA,
                'diagnosa_operasi'  => DocoConstants::VAR_KELOMPOK_DIAGNOSA_OPERASI,
                'diagnosa_keluarga' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA,
                'diagnosa_terapi'   => DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI,
                'prosedur_kerja'    => DocoConstants::VAR_KELOMPOK_DIAGNOSA_KERJA,
            ];
            $type = $typeMap[$type] ? $typeMap[$type] : DocoConstants::VAR_KELOMPOK_DIAGNOSA_AWALAN;
            
            $response = $this->guzzleExec(Yii::$app->docoRest->rajal, [
                'url' => 'allow/get-new-diagnosa',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'q' => $q,
                        'type' => $type,
                        'page' => $page,
                        'offset' => $offset,
                        'limit' => $limit,
                        'is_perawat' => $is_perawat
                    ]
                ]
            ]);
            foreach ($response as $value) {
                $result['results'][] = $this->formatDiagnosaItem($value, $all_text, $id_with_text);
            }

            $result['pagination'] = ['more' => !empty($response)];
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private function formatDiagnosaItem($value, $all_text, $id_with_text)
    {
        $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
        $diagnosaKode = ArrayHelper::getValue($value, 'diagnosa_kode');
        $diagnosaNama = ArrayHelper::getValue($value, 'diagnosa_nama');

        $text = $diagnosaKode . ' - ' . $diagnosaNama;

        if ($all_text) {
            return ['id' => $text, 'text' => $text];
        }

        if ($id_with_text) {
            return ['id' => $diagnosaId . '_' . $text, 'text' => $text];
        }

        return ['id' => $diagnosaId, 'text' => $text];
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new ResumeMedisFisioForm;
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        if($request->post()) {
            $data = Yii::$app->request->post();
            $formData = ArrayHelper::getValue($data, $modelName, []);
            $model->attributes = $formData;
            $model->ruangan_id = $ruanganId;
            $model->tgl_masuk = date('Y-m-d', strtotime($model->tgl_masuk));
            $model->tgl_keluar = $model->tgl_keluar ? date('Y-m-d', strtotime($model->tgl_keluar)) : '';
            if(!$model->validate()) {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $modelName);
            }

            $response = $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'resume-medis/save',
                'method' => 'post',
                'payload' => [
                    'form_params' => $model->attributes,
                ]
            ]);
            return DocoHelpers::response($response);
        }
    }

    private function collectDiagnosa($model, $data, $cppt)
    {
        $isDataEmpty = empty($data);
        $source = $isDataEmpty ? $cppt : $data;

        $diagnosaUtama = $this->getDiagnosaValue($source, 'utama', $isDataEmpty);
        $diagnosaPenyerta = $this->getDiagnosaValue($source, 'penyerta', $isDataEmpty);
        $subjective = ArrayHelper::getValue($source, $isDataEmpty ? 'subject' : 'subjective');
        $objective = ArrayHelper::getValue($source, $isDataEmpty ? 'object' : 'objective');
        $planning = ArrayHelper::getValue($source, 'planning');

        $diagnosaUtamaText = $this->getDiagnosaUtamaText($diagnosaUtama, $isDataEmpty);
        $listDiagPenyerta = $this->getListDiagnosaPenyerta($diagnosaPenyerta, $isDataEmpty);

        $output = implode(', ', $listDiagPenyerta);
        $asesment = $diagnosaUtamaText;
        if ($output) {
            $asesment .= $diagnosaUtamaText ? ', ' . $output : $output;
        }

        $model->diag_utama = $diagnosaUtamaText;
        $model->diag_penyerta = $listDiagPenyerta;
        $model->assesment = !empty($data['assesment']) ? $data['assesment'] : $asesment;
        $model->subjective = $subjective;
        $model->objective = $objective;
        $model->planning = $planning;
    }

    private function getDiagnosaValue($source, $type, $isDataEmpty)
    {
        $keyMap = [
            'utama' => ['a_diag_utama', 'diag_utama'],
            'penyerta' => ['a_diag_penyerta', 'diag_penyerta']
        ];
        $utama = isset($keyMap[$type][0]) ? $keyMap[$type][0] : '';
        $penyerta = isset($keyMap[$type][1]) ? $keyMap[$type][1] : '';
        $key = $isDataEmpty ? $utama : $penyerta;
        $value = ArrayHelper::getValue($source, $key);
        return $isDataEmpty ? json_decode($value, true) : $value;
    }

    private function getDiagnosaUtamaText($diagnosaUtama, $isDataEmpty)
    {
        return $isDataEmpty
            ? ArrayHelper::getValue($diagnosaUtama, 'text')
            : $diagnosaUtama;
    }

    private function getListDiagnosaPenyerta($diagnosaPenyerta, $isDataEmpty)
    {
        $list = [];
        if (!is_array($diagnosaPenyerta)) {
            return $list;
        }

        foreach ($diagnosaPenyerta as $value) {
            $list[] = $isDataEmpty
                ? ArrayHelper::getValue($value, 'text')
                : $value;
        }

        return $list;
    }

    private function getParamDecrypted()
    {
        $paramGet = Yii::$app->request->get();
        $pendaftaranId = ArrayHelper::getValue($paramGet, 'id');
        $pendaftaranId = DocoHelpers::decrypt($pendaftaranId);
        $programTerapiIdsString = ArrayHelper::getValue($paramGet, 'program_terapi_ids');
        $programTerapiIds = explode(',', $programTerapiIdsString);
        $pasienId = ArrayHelper::getValue($paramGet, 'pasien_id');
        $pasienId = DocoHelpers::decrypt($pasienId);
        $programTerapiIdsDecrypted = [];
        $tempProgramTerapiIdsString = "";
        foreach ($programTerapiIds as $value) {
            $decryptedValue = DocoHelpers::decrypt($value);
            $programTerapiIdsDecrypted[] = $decryptedValue;
            $tempProgramTerapiIdsString = $tempProgramTerapiIdsString . $decryptedValue . ",";
        }
        $programTerapiIdsString = rtrim($tempProgramTerapiIdsString, ',');
        return [
            'pendaftaran_id' => $pendaftaranId,
            'programterapi_ids' => $programTerapiIdsDecrypted,
            'programterapi_ids_string' => $programTerapiIdsString,
            'pasien_id' => $pasienId
        ];
    }

    public function actionCetakResume()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-resume-medis.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        try {
            if(Yii::$app->report->enabled){
                return Yii::$app->report->exec('resume-medis-fisio?pendaftaran_id='.DocoHelpers::decrypt($pendaftaran_id));
            }
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}

