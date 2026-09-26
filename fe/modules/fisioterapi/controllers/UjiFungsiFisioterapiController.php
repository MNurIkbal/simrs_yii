<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\fisioterapi\models\UjiFungsiFisioterapiForm;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class UjiFungsiFisioterapiController extends DocoController
{
    private static $diagnosaUrl = 'allow/get-diagnosa-by-id';
    
    protected $allowAction = ['*'];

    public function actionIndex()
    {
        $title = 'Uji Fungsi';
        $params = $this->getParamDecrypted();
        $pendaftaranId    = ArrayHelper::getValue($params, 'pendaftaran_id');
        $programterapiIds = ArrayHelper::getValue($params, 'programterapi_ids_string');
        $pasienId         = ArrayHelper::getValue($params, 'pasien_id');
        
        $model = $this->initUjiFungsiFisioterapiModel($pendaftaranId, $pasienId, $programterapiIds);
        $response = $this->fetchUjiFungsiFisioterapiData($pendaftaranId);
        $data = ArrayHelper::getValue($response, 'data', []);
        $cppt = ArrayHelper::getValue($response, 'cppt', []);

        if (!empty($data)) {
            $model->attributes = $data;
        }

        $this->collectDiagnosa($model, $data, $cppt);
        
        $hasData = !empty($data);

        return $this->renderAjax('uji-fungsi-fisioterapi', compact(
            'title', 'data', 'model', 'pendaftaranId', 'hasData'
        ));
    }

    private function initUjiFungsiFisioterapiModel($pendaftaranId, $pasienId, $programterapiIds)
    {
        $model = new UjiFungsiFisioterapiForm();
        $model->pendaftaran_id = $pendaftaranId;
        $model->pasien_id = $pasienId;
        $model->program_terapi_ids = $programterapiIds;
        $model->programterapi_id = $programterapiIds;
        return $model;
    }

    private function fetchUjiFungsiFisioterapiData($pendaftaranId)
    {
        return $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'uji-fungsi-fisioterapi/get-data-uji-fungsi-fisioterapi',
            'method' => 'get',
            'payload' => [
                'query' => ['pendaftaran_id' => $pendaftaranId]
            ]
        ]);
    }

    public function actionGetNewDiagnosa($q = '', $type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0, $is_perawat = 0, $page = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['results' => []];

        try {
            $limit = 20;
            $offset = ($page - 1) * $limit;
            $typeMap = [
                'diagnosa_utama'    => DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA,
                'diagnosa_medis'    => DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA,
                'diagnosa_fungsional' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_FUNGSI,
                'tindakan_dan_prosedur' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_KERJA,
            ];
            $type = $typeMap[$type] ? $typeMap[$type] : DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK;

            $response = $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
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
            return ['id' => $text, 'text' => $text];
        }

        return ['id' => $diagnosaId, 'text' => $text];
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new UjiFungsiFisioterapiForm;
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        if($request->post()) {
            $data = Yii::$app->request->post();
            $formData = ArrayHelper::getValue($data, $modelName, []);
            $model->attributes = $formData;
            $model->ruangan_id = $ruanganId;
            if(!$model->validate()) {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $modelName);
            }

            $response = $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'uji-fungsi-fisioterapi/save',
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
        $diagnosaMedis = $this->getDiagnosaValue($source, 'medis', $isDataEmpty);
        $diagnosaFungsi = $this->getDiagnosaValue($source, 'fungsi', $isDataEmpty);
        $diagnosaTindakanProsedur = $this->getDiagnosaValue($source, 'tindakan_prosedur', $isDataEmpty);
        $hasilYangDidapat = $this->getFieldValue($source, $isDataEmpty, 'object', 'hasil_yang_didapat');
        $anjuran = $this->getFieldValue($source, $isDataEmpty, 'instruksi', 'anjuran_dan_goal', 'anjuran');
        $goal = $this->getFieldValue($source, $isDataEmpty, 'goal', 'anjuran_dan_goal', 'goal');

        $diagnosaUtamaText = $this->getDiagnosaText($diagnosaUtama, $isDataEmpty, 'diagnosa_utama');
        $diagnosaMedisText = $this->getDiagnosaText($diagnosaMedis, $isDataEmpty, 'diagnosa_medis');
        $diagnosaFungsiArray = $this->getDiagnosaArray($diagnosaFungsi, $isDataEmpty, 'diagnosa_fungsional');
        $diagnosaTindakanProsedurArray = $this->getDiagnosaArray($diagnosaTindakanProsedur, $isDataEmpty, 'tindakan_dan_prosedur');

        $model->diag_utama = $diagnosaUtamaText;
        $model->diag_medis = $diagnosaMedisText;
        $model->diag_fungsi = $diagnosaFungsiArray;
        $model->tindakan_prosedur = $diagnosaTindakanProsedurArray;
        
        $model->hasil_yang_didapat = $hasilYangDidapat;
        
        if ($isDataEmpty) {
            $model->anjuran_dan_goal = $this->combineAnjuranAndGoal($anjuran, $goal);
        } else {
            $model->anjuran_dan_goal = ArrayHelper::getValue($source, 'anjuran_dan_goal', '');
        }
    }

    private function getDiagnosaValue($source, $type, $isDataEmpty)
    {
        $keyMap = [
            'utama' => ['a_diag_utama', 'diag_utama'],
            'medis' => ['a_diag_utama', 'diag_medis'],
            'fungsi' => ['diagnosa_fungsi', 'diag_fungsi'],
            'tindakan_prosedur' => ['prosedur_kerja', 'tindakan_prosedur']
        ];
        $key = $isDataEmpty ? $keyMap[$type][0] : $keyMap[$type][1];
        $value = ArrayHelper::getValue($source, $key);
        return $isDataEmpty ? json_decode($value, true) : $value;
    }

    private function getDiagnosaText($diagnosa, $isDataEmpty, $type)
    {
        if ($isDataEmpty) {
            return ArrayHelper::getValue($diagnosa, 'text');
        } else {
            return $this->convertToKodeNamaFormat($diagnosa, $type);
        }
    }

    private function getDiagnosaArray($diagnosa, $isDataEmpty, $type)
    {
        $result = [];
        
        if ($isDataEmpty) {
            if (!empty($diagnosa)) {
                if (is_array($diagnosa)) {
                    foreach ($diagnosa as $diagnosaItem) {
                        if (is_array($diagnosaItem) || is_object($diagnosaItem)) {
                            $kode = ArrayHelper::getValue($diagnosaItem, 'kode');
                            $nama = ArrayHelper::getValue($diagnosaItem, 'nama');
                            $text = ArrayHelper::getValue($diagnosaItem, 'text');
                            
                            if (empty($kode)) {
                                $kode = ArrayHelper::getValue($diagnosaItem, 'diagnosa_kode');
                            }
                            if (empty($nama)) {
                                $nama = ArrayHelper::getValue($diagnosaItem, 'diagnosa_nama');
                            }
                            
                            if (!empty($text)) {
                                $result[] = $text;
                            } elseif (!empty($kode) && !empty($nama)) {
                                $result[] = $kode . ' - ' . $nama;
                            } elseif (!empty($nama)) {
                                $result[] = $nama;
                            }
                        } else {
                            $result[] = $diagnosaItem;
                        }
                    }
                } else {
                    try {
                        $response = $this->guzzleExec(Yii::$app->docoRest->rajal, [
                            'url' => self::$diagnosaUrl,
                            'method' => 'get',
                            'payload' => [
                                'query' => ['id' => $diagnosa]
                            ]
                        ]);
                        
                        $data = ArrayHelper::getValue($response, 'data');
                        if ($data) {
                            $kode = ArrayHelper::getValue($data, 'diagnosa_kode');
                            $nama = ArrayHelper::getValue($data, 'diagnosa_nama');
                            $text = !empty($kode) ? $kode . ' - ' . $nama : $nama;
                            $result = [$text];
                        }
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage());
                    }
                }
            }
        } else {
            if (is_array($diagnosa)) {
                foreach ($diagnosa as $item) {
                    $result[] = $this->convertSingleValueToKodeNama($item, $type);
                }
            } else {
                if (!empty($diagnosa)) {
                    if (strpos($diagnosa, "\n") === false) {
                        $result = [$this->convertSingleValueToKodeNama($diagnosa, $type)];
                    } else {
                        $items = explode("\n", $diagnosa);
                        foreach ($items as $item) {
                            $item = trim($item);
                            if (!empty($item)) {
                                $result[] = $this->convertSingleValueToKodeNama($item, $type);
                            }
                        }
                    }
                }
            }
        }
        
        return $result;
    }

    private function getFieldValue($source, $isDataEmpty, $emptyField, $nonEmptyField, $part = null)
    {
        $result = '';
        
        if ($isDataEmpty) {
            $result = ArrayHelper::getValue($source, $emptyField);
        } else {
            $value = ArrayHelper::getValue($source, $nonEmptyField);
            if (!empty($value) && $part) {
                $parts = explode("\n\n", $value, 2);
                if ($part === 'anjuran') {
                    $result = trim($parts[0]);
                } elseif ($part === 'goal' && count($parts) > 1) {
                    $result = trim($parts[1]);
                } else {
                    $result = $value;
                }
            } else {
                $result = $value;
            }
        }
        
        return $result;
    }

    private function combineAnjuranAndGoal($anjuran, $goal)
    {
        $result = '';
        
        if (!empty($anjuran)) {
            $result .= $anjuran;
        }
        
        if (!empty($goal)) {
            if (!empty($result)) {
                $result .= "\n";
            }
            $result .= $goal;
        }
        
        return $result;
    }


    private function convertToKodeNamaFormat($value, $type)
    {
        $result = '';
        
        if (empty($value)) {
            $result = '';
        } elseif (is_array($value)) {
            $formattedValues = [];
            foreach ($value as $item) {
                $formattedValues[] = $this->convertSingleValueToKodeNama($item, $type);
            }
            $result = ($type === 'tindakan_dan_prosedur' || $type === 'diagnosa_fungsional') ? $formattedValues : implode("\n", $formattedValues);
        } else {
            $result = $this->convertSingleValueToKodeNama($value, $type);
        }
        
        return $result;
    }

    private function convertSingleValueToKodeNama($value, $type)
    {
        $result = $value;
        
        if (strpos($value, ' - ') !== false) {
            if (preg_match('/^\d+_([A-Z0-9.]+) - (.+)$/', $value, $matches)) {
                $kode = $matches[1];
                $nama = $matches[2];
                $result = $kode . ' - ' . $nama;
            }
        } elseif (is_numeric($value)) {
            try {
                $response = $this->guzzleExec(Yii::$app->docoRest->rajal, [
                    'url' => self::$diagnosaUrl,
                    'method' => 'get',
                    'payload' => [
                        'query' => ['id' => $value]
                    ]
                ]);
                
                $data = ArrayHelper::getValue($response, 'data');
                if ($data) {
                    $kode = ArrayHelper::getValue($data, 'diagnosa_kode');
                    $nama = ArrayHelper::getValue($data, 'diagnosa_nama');
                    $result = !empty($kode) ? $kode . ' - ' . $nama : $nama;
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
            }
        }
        
        return $result;
    }

    private function extractKode($text)
    {
        $result = null;
        
        if (is_array($text)) {
            $kodes = [];
            foreach ($text as $item) {
                if (strpos($item, ' - ') !== false) {
                    $parts = explode(' - ', $item, 2);
                    $kode = trim($parts[0]);
                    if (preg_match('/^\d+_([A-Z0-9.]+)$/', $kode, $matches)) {
                        $kode = $matches[1];
                    }
                    $kodes[] = $kode;
                }
            }
            $result = $kodes;
        } elseif (is_string($text)) {
            if (strpos($text, ',') !== false) {
                $items = explode(',', $text);
                $kodes = [];
                foreach ($items as $item) {
                    $item = trim($item);
                    if (strpos($item, ' - ') !== false) {
                        $parts = explode(' - ', $item, 2);
                        $kode = trim($parts[0]);
                        if (preg_match('/^\d+_([A-Z0-9.]+)$/', $kode, $matches)) {
                            $kode = $matches[1];
                        }
                        $kodes[] = $kode;
                    }
                }
                $result = $kodes;
            } elseif (strpos($text, ' - ') !== false) {
                $parts = explode(' - ', $text, 2);
                $kode = trim($parts[0]);
                if (preg_match('/^\d+_([A-Z0-9.]+)$/', $kode, $matches)) {
                    $kode = $matches[1];
                }
                $result = $kode;
            }
        }
        
        return $result;
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

    public function actionCetakUjiFungsi()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-uji-fungsi-fisioterapi.pdf";
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        try {
            if(Yii::$app->report->enabled){
                return Yii::$app->report->exec('uji-fungsi-fisioterapi?pendaftaran_id='.DocoHelpers::decrypt($pendaftaran_id));
            }
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}