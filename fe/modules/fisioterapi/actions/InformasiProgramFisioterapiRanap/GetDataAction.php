<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\actions\InformasiProgramFisioterapiRanap;

use Yii;
use Exception;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoConstants;

class GetDataAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $data = [];
        try {
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-program-fisioterapi-ranap/get-data', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $resData = ArrayHelper::getValue($body, 'response.data');
            $no = $request->get('start', 1);
            $data = [];
            foreach ($resData as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'programterapi_id'));
                $primaryCppt = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'pendaftaran_id'));
                $value['primary'] = $primaryKey;
                $value['primaryCppt'] = $primaryCppt;
                $value['tanggal_lahir'] = date("d-M-Y", strtotime(ArrayHelper::getValue($value, 'tanggal_lahir')));
                $value['tgl_rujukan'] = date("d-M-Y H:i:s", strtotime(ArrayHelper::getValue($value, 'tgl_rujukan')));
                $value['noRuangannya'] = "<b>" . ArrayHelper::getValue($value, 'ruangan_nama') . "</b>" . " <br> " . ArrayHelper::getValue($value, 'kamar') . " - " . ArrayHelper::getValue($value, 'no_tempattidur');
                $realisasi = ArrayHelper::getValue($value, 'realisasi');
                $statusProgramFisio = ArrayHelper::getValue($value, 'status_program_fisio_nama');
                $value['btnEditProgram'] = self::buttonEditProgram($primaryKey, $value);
                $value['btnBatalProgram'] = self::buttonBatalProgram($primaryKey, $value);
                $value['btnEditSchedule'] = self::buttonEditSchedule($primaryKey, $value);
                $value['btnDetailCppt'] = self::buttonDetailCppt($primaryKey, $value);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function buttonBatalProgram($primaryKey, $data)
    {
        $isPaket = ArrayHelper::getValue($data, 'is_paket');
        $realisasi = ArrayHelper::getValue($data, 'realisasi');
        $bayar = ArrayHelper::getValue($data, 'bayar');
        $statusProgramFisio = ArrayHelper::getValue($data, 'status_program_fisio_nama');
        $result = "";
        $statusIsCancelable = $statusProgramFisio == DocoConstants::OPEN;
        $isCancelableNonPaket = !$isPaket & ($realisasi <= 0) & $statusIsCancelable;
        $isCancelablePaket = $isPaket & ($realisasi <= 0) & ($bayar != 1) & $statusIsCancelable;
        $isCancelable = $isCancelablePaket || $isCancelableNonPaket;
        if ($isCancelable) {
            $result = '<button data-target="#modalProgramTerapi" title="Batalkan Program" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-ranap/batal-program-form?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/batal-program.svg"></button>';
        } else {
            $result = '<button disabled data-target="#modalProgramTerapi" title="Program Tidak Bisa Dibatalkan" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-ranap/batal-program-form?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/batal-program.svg"></button>';
        }
        return $result;
    }

    public static function buttonEditSchedule($primaryKey, $data)
    {
        $realisasi = ArrayHelper::getValue($data, 'realisasi');
        $statusProgramFisio = ArrayHelper::getValue($data, 'status_program_fisio_nama');
        $result = "";
        if ($statusProgramFisio == 'BATAL') {
            $result = '<button data-target="#modalProgramTerapi" title="Program Sudah Dibatalkan" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-ranap/detail?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/detail-schedule.svg"></button>';
        } else {
            $result = '<button data-target="#modalProgramTerapi" title="Detail Jadwal" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-ranap/detail?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/detail-schedule.svg"></button>';
        }
        return $result;
    }

    public static function buttonDetailCppt($primaryKey, $data)
    {
        $primaryCppt = DocoHelpers::encrypt(ArrayHelper::getValue($data, 'pendaftaran_id'));
        $result = "<button data-target='#modalProgramTerapi' title='Detail CPPT' type='button' data-btntrigger='modal' action='/fisioterapi/informasi-program-fisioterapi-ranap/detail-cppt?p_id=$primaryCppt&id=$primaryKey' data-toggle='modal' data-options='modal' class='btn-transparent'><img class='img-icon' src='/media/img/icon-app/detail-cppt.svg'></button>";
        return $result;
    }

    private static function buttonEditProgram($primaryKey, $data)
    {
        $isPaket = ArrayHelper::getValue($data, 'is_paket');
        $btnEditProgramFisio = "";
        $realisasi = ArrayHelper::getValue($data, 'realisasi');
        $bayar = ArrayHelper::getValue($data, 'bayar');
        $statusProgramFisio = ArrayHelper::getValue($data, 'status_program_fisio_nama');
        $statusIsEditable = $statusProgramFisio == DocoConstants::OPEN;
        $isEditableNonPaket = !$isPaket & ($realisasi <= 0) & $statusIsEditable;
        $isEditablePaket = $isPaket & ($realisasi <= 0) & ($bayar != 1) & $statusIsEditable;
        $isEditable = $isEditablePaket || $isEditableNonPaket;
        if ($isEditablePaket) $primaryKey = "$primaryKey&p=true";
        if ($isEditable) {
            $btnEditProgramFisio = "<button data-width='75%' data-wrapper='#modal-lab .modal-content' data-target='#modal-lab' title='Edit Program' type='button' data-btntrigger='modal' action='/fisioterapi/informasi-program-fisioterapi-ranap/edit?id=$primaryKey' data-toggle='modal' data-options='modal' class='btn-transparent'><img class='img-icon' src='/media/img/icon-app/edit-program.svg'></button>";
        } else {
            $btnEditProgramFisio = "<button data-width='75%' disabled data-wrapper='#modal-lab .modal-content' data-target='#modal-lab' title='Tidak Bisa Edit Program' type='button' data-btntrigger='modal' action='/fisioterapi/informasi-program-fisioterapi-ranap/edit?id=$primaryKey' data-toggle='modal' data-options='modal' class='btn-transparent'><img class='img-icon' src='/media/img/icon-app/edit-program.svg'></button>";
        }
        return $btnEditProgramFisio;
    }
}
