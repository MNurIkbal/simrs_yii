<?php

namespace app\components\Services\Rm;

use Yii;
use app\components\Services\Rm\BaseServiceRm;
use app\components\Services\Contracts\DiagnosaInterface;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use app\components\DocoConstants;

class DiagnosaService extends BaseServiceRm implements DiagnosaInterface
{
    public function getDiagnosa($q = '',$page = null,$type = 'diagnosa_masuk', $formatResponse = 0)
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $limit = 10;
            $offset = ($page-1)*10;
            $result = [];
            $result['results'] = [];

            if ($type == 'diagnosa_masuk') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK;
            } else if ($type == 'diagnosa_utama') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
            } else if ($type == 'diagnosa_penyerta') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
            } else if ($type == 'diagnosa_operasi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_OPERASI;
            } else if ($type == 'diagnosa_keluarga') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA;
            } else if ($type == 'diagnosa_terapi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
            } else {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_AWALAN;
            }

            $list = $this->guzzleExec($this->restRm, [
                'url' => 'allow/get-diagnosa',
                'payload' => [
                    'query' => [
                        'q' => $q,
                        'type' => $type,
                        'page' => $page,
                        'offset' => $offset,
                        'limit' => $limit
                    ]
                ]
            ]);

            switch ($formatResponse) {
                case '1':
                    $result['results'] = $this->formatResponseCodeName($list);
                    break;
                case '2':
                    $result['results'] = $this->formatResponseFull($list);
                    break;
                case '3':
                    $result['results'] = $this->formatResponseCode($list);
                    break;
                default:
                    $result['results'] = $this->formatResponseId($list);
                    break;
            }


            $result['pagination'] = [ 'more' => !empty($list)?true:false ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function formatResponseId($list)
    {
        $temp = [];
        foreach($list as $value) {
            $temp[] = [
                'id' => $value['diagnosa_id'],
                'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
            ];
        }

        return $temp;
    }

    public function formatResponseCodeName($list)
    {
        $temp = [];
        foreach($list as $value) {
            $temp[] = [
                'id' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
            ];
        }

        return $temp;
    }

    public function formatResponseFull($list)
    {
        $temp = [];
        foreach($list as $value) {
            $temp[] = [
                'id' => $value['diagnosa_id'].'_'.$value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
            ];
        }

        return $temp;
    }

    public function formatResponseCode($list)
    {
        $temp = [];
        foreach($list as $value) {
            $temp[] = [
                'id' => $value['diagnosa_kode'],
                'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
            ];
        }

        return $temp;
    }
}
