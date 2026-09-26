<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;

class ProgramRanapAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $pasien_id = $request->get('pasien_id');
        $filter['advanced-filter']['pasien_id'] = $pasien_id; 
        $pendaftaran_id = $request->get('pendaftaran_id');
        $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/program-ranap', ['query' =>  $filter]);
        $body = json_decode($response->getBody(), true);
        $resData = ArrayHelper::getValue($body, 'response.data');
        $data = [];
        $no = 0;
        foreach ($resData as $key => $value) {
            $no++;
            $programTerapiIdEnc = DocoHelpers::encrypt($value['programterapi_id']);
            $value['rowNum'] = $no;
            $value['tgl_rujukan'] = date('Y-m-d H:i:s', strtotime($value['tgl_rujukan']));
            $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
            $value['programterapi_id'] = DocoHelpers::encrypt($value['programterapi_id']);
            $value['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
            $statusProgram = $value['status_program_fisio_nama'];
            $statusPeriksa = ArrayHelper::getValue($value, 'status_periksa_id');
            $frekuensi = ArrayHelper::getValue($value, 'frekuensi');
            $realisasi = ArrayHelper::getValue($value, 'realisasi');
            // Set realisasi sebagai frekuensi jiga realisasi lebih besar
            if ($realisasi > $frekuensi) {
                $frekuensi = $realisasi;
                $value['frekuensi'] = $realisasi;
            }
            $paramBtnPeriksa = [
                'statusProgram' => $statusProgram,
                'status' => $statusPeriksa,
                'pendaftaran_id' => $pendaftaran_id,
                'programTerapiIdEnc' => $programTerapiIdEnc,
                'frekuensi' => $frekuensi,
                'realisasi' => $realisasi
            ];
            $btnRanap = self::btnPeriksaPermission($paramBtnPeriksa);
            $value['aksi'] = $btnRanap;
            $data[] = $value;
        }
        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
        $result['recordsFiltered'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
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
        $pendaftaran_id = $paramBtnPeriksa['pendaftaran_id'];
        $programTerapiIdEnc = $paramBtnPeriksa['programTerapiIdEnc'];
        $status = $paramBtnPeriksa['programTerapiIdEnc'];
        $status = $paramBtnPeriksa['status'];
        $status = $paramBtnPeriksa['status'];
        $frekuensi = $paramBtnPeriksa['frekuensi'];
        $realisasi = $paramBtnPeriksa['realisasi'];
        if ($statusProgram == 'OPEN') {
            $btnRanap = Html::button('<b><i class="fa fa-stethoscope"></i></b>Periksa', [
                'data-target' => '/fisioterapi/informasi-pasien-fisioterapi-ranap/pilih-ranap?pendaftaran_id=' . $pendaftaran_id . '&program_id=' . $programTerapiIdEnc,
                'data-program-terapi-id' => $programTerapiIdEnc,
                'data-pendaftaran-id' => $pendaftaran_id,
                'class' => 'btn btn-info btn-labeled btn-xs btn-pilih-program',
            ]);
        } else if ($statusProgram == 'BATAL') {
            $btnRanap = Html::button('<b><i class="fa fa-stethoscope"></i></b>Periksa', [
                'data-target' => '/fisioterapi/informasi-pasien-fisioterapi-ranap/pilih-ranap?pendaftaran_id=' . $pendaftaran_id . '&program_id=' . $programTerapiIdEnc,
                'data-program-terapi-id' => $programTerapiIdEnc,
                'data-pendaftaran-id' => $pendaftaran_id,
                'title' => 'Program Sudah Dibatalkan',
                'class' => 'btn btn-info btn-labeled disabled btn-xs btn-pilih-program disabled',
            ]);
        } else if ($statusProgram == 'CLOSE') {
            $btnRanap = Html::button('<b><i class="fa fa-stethoscope"></i></b>Periksa', [
                'data-target' => '/fisioterapi/informasi-pasien-fisioterapi-ranap/pilih-ranap?pendaftaran_id=' . $pendaftaran_id . '&program_id=' . $programTerapiIdEnc,
                'data-program-terapi-id' => $programTerapiIdEnc,
                'data-pendaftaran-id' => $pendaftaran_id,
                'title' => 'Program Sudah Selesai',
                'class' => 'btn btn-info btn-labeled disabled btn-xs btn-pilih-program disabled',
            ]);
        } else if ($statusProgram == 'EXPIRED') {
            $btnRanap = Html::button('<b><i class="fa fa-stethoscope"></i></b>Periksa', [
                'data-target' => '/fisioterapi/informasi-pasien-fisioterapi-ranap/pilih-ranap?pendaftaran_id=' . $pendaftaran_id . '&program_id=' . $programTerapiIdEnc,
                'data-program-terapi-id' => $programTerapiIdEnc,
                'data-pendaftaran-id' => $pendaftaran_id,
                'title' => 'Program Sudah Expired',
                'class' => 'btn btn-info btn-labeled disabled btn-xs btn-pilih-program disabled',
            ]);
        } else if ($statusProgram == "DROP OUT") {
            $btnRanap = Html::button('<b><i class="fa fa-stethoscope"></i></b>Periksa', [
                'data-target' => '/fisioterapi/informasi-pasien-fisioterapi-ranap/pilih-ranap?pendaftaran_id=' . $pendaftaran_id . '&program_id=' . $programTerapiIdEnc,
                'data-program-terapi-id' => $programTerapiIdEnc,
                'data-pendaftaran-id' => $pendaftaran_id,
                "title" => "Program Sudah Drop Out",
                'class' => 'btn btn-info btn-labeled disabled btn-xs btn-pilih-program disabled',
            ]);
        }
        if ($status == DocoConstants::STATUS_RANAP_PULANG) {
            $btnRanap = Html::button('<b><i class="fa fa-stethoscope"></i></b>Periksa', [
                'data-target' => '/fisioterapi/informasi-pasien-fisioterapi-ranap/pilih-ranap?pendaftaran_id=' . $pendaftaran_id . '&program_id=' . $programTerapiIdEnc,
                'data-program-terapi-id' => $programTerapiIdEnc,
                'data-pendaftaran-id' => $pendaftaran_id,
                'title' => 'Pasien Sudah Pulang',
                'class' => 'btn btn-info btn-labeled disabled btn-xs btn-pilih-program disabled',
            ]);
        }
        if ($frekuensi == $realisasi) {
            $btnRanap = '-';
        }
        return $btnRanap;
    }
}
