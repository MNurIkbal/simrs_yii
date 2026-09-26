<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;

class ProgramAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['pasien_id'] = $pasien_id;
        $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/program', ['query' =>  $filter]);
        $body = json_decode($response->getBody(), true);
        $resData = ArrayHelper::getValue($body, 'response.data');
        $data = [];
        $no = 0;
        foreach ($resData as $value) {
            $no++;
            $programTerapiIdEnc = DocoHelpers::encrypt($value['programterapi_id']);
            $programTerapiDetailIdEnc = DocoHelpers::encrypt($value['programterapidetail_id']);
            $value['rowNum'] = $no;
            $value['tgl_rujukan'] = date('Y-m-d H:i:s', strtotime($value['tgl_rujukan']));
            $statusProgram = $value['status_program_fisio_nama'];
            $status = ArrayHelper::getValue($value, 'status_periksa_id');
            $caraBayar = ArrayHelper::getValue($value, 'carabayar_id');
            $bayar = ArrayHelper::getValue($value, 'bayar');
            $isPaketFisio = ArrayHelper::getValue($value, 'is_paketfisio');
            $frekuensi = ArrayHelper::getValue($value, 'frekuensi');
            $realisasi = ArrayHelper::getValue($value, 'realisasi');
            $paramBtnPeriksa = [
                'statusProgram' => $statusProgram,
                'status' => $status,
                'caraBayar' => $caraBayar,
                'bayar' => $bayar,
                'isPaketFisio' => $isPaketFisio,
                'frekuensi' => $frekuensi,
                'realisasi' => $realisasi
            ];
            $btnRajal = self::btnPeriksaPermission($paramBtnPeriksa);
            $value['aksi'] = $btnRajal;
            $value['programterapi_id'] = $programTerapiIdEnc;
            $value['programterapidetail_id'] = $programTerapiDetailIdEnc;
            $data[] = $value;
        }
        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return DocoHelpers::response($result);
    }

    /**
     * @method btnPeriksaPermission
     * @param Array $paramBtnPeriksa
     * @return String
     */
    private static function btnPeriksaPermission($paramBtnPeriksa)
    {
        $statusProgram = $paramBtnPeriksa['statusProgram'];
        $caraBayar = $paramBtnPeriksa['caraBayar'];
        $bayar = $paramBtnPeriksa['bayar'];
        $isPaketFisio = $paramBtnPeriksa['isPaketFisio'];
        $status = $paramBtnPeriksa['status'];
        $frekuensi = $paramBtnPeriksa['frekuensi'];
        $realisasi = $paramBtnPeriksa['realisasi'];

        if ($statusProgram == 'OPEN') {
            $btnRajal = false;
            if (!is_null($isPaketFisio)) {
                if ($isPaketFisio == true) {
                    if (!is_null($caraBayar)) {
                        if ($caraBayar == DocoConstants::CARA_BAYAR_PRIVATE) {
                            if (!is_null($bayar)) {
                                if ($bayar == 0) {
                                    $btnRajal = true;
                                }
                            }
                        }
                    }
                }
            }
            if ($status == DocoConstants::STATUS_PERIKSA_RUJUK_RANAP) {
                $btnRajal = true;
            }
        } else if ($statusProgram == 'BATAL') {
            $btnRajal = true;
        } else if ($statusProgram == 'CLOSE') {
            $btnRajal = true;
        } else if ($statusProgram == 'EXPIRED') {
            $btnRajal = true;
        } else if($statusProgram == "DROP OUT") {
            $btnRajal = true;
        }
        if ($status == Dococonstants::STATUS_PERIKSA_RUJUK_RANAP){
            $btnRajal = true;
        }
        if ($frekuensi == $realisasi) {
            // $btnRajal = '-';
            $btnRajal = false;
        }
        return $btnRajal;
    }
}
