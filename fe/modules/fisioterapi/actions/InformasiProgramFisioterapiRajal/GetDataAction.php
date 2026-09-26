<?php

namespace Doco\fisioterapi\actions\InformasiProgramFisioterapiRajal;

use Yii;
use Exception;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
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
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-program-fisioterapi-rajal/get-data', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $data = [];
            $responseDatas = ArrayHelper::getValue($body, 'response.data', []);
            foreach ($responseDatas as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'programterapi_id'));
                $primaryCppt = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'pendaftaran_id'));
                $value['primary'] = $primaryKey;
                $value['primaryCppt'] = $primaryCppt;
                $value['tanggal_lahir'] = date("d-M-Y", strtotime(ArrayHelper::getValue($value, 'tanggal_lahir')));
                $value['tgl_rujukan'] = date("d-M-Y H:i:s", strtotime(ArrayHelper::getValue($value, 'tgl_rujukan')));
                $realisasi = ArrayHelper::getValue($value, 'realisasi');
                $statusProgramFisio = ArrayHelper::getValue($value, 'status_program_fisio_nama');
                $value['btnBatalProgram'] = self::buttonBatalProgram($primaryKey, $value);
                $value['btnEditSchedule'] = self::buttonEditSchedulePermission($statusProgramFisio, $primaryKey);
                $value['btnDetailCppt'] = self::buttonDetailCppt($primaryKey);
                $value['btnEditProgram'] = self::buttonEditProgram($primaryKey, $value);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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
            $result = '<button data-target="#modalProgramTerapi" title="Batalkan Program" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-rajal/batal-program-form?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/batal-program.svg"></button>';
        } else {
            $result = '<button disabled data-target="#modalProgramTerapi" title="Program Tidak Bisa Dibatalkan" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-rajal/batal-program-form?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/batal-program.svg"></button>';
        }
        return $result;
    }

    /**
     * @method buttonEditSchedulePermission
     * @param String $statusProgramFisio
     * @param Integer $primaryKey (programterapi_id)
     * @return String
     */
    private static function buttonEditSchedulePermission($statusProgramFisio, $primaryKey)
    {
        if ($statusProgramFisio == 'BATAL') {
            $btnEditSchedule = '<button data-target="#modalProgramTerapi" title="Program Sudah Dibatalkan" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-rajal/detail?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/detail-schedule.svg"></button>';
        } else {
            $btnEditSchedule = '<button data-target="#modalProgramTerapi" title="Detail Jadwal" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-rajal/detail?id=' . $primaryKey . '" data-toggle="modal" data-options="modal" class="btn-transparent"><img class="img-icon" src="/media/img/icon-app/detail-schedule.svg"></button>';
        }
        return $btnEditSchedule;
    }

    private static function buttonDetailCppt($primaryKey)
    {
        $btnDetailCppt = "<button data-target='#modalProgramTerapi' title='Detail CPPT' type='button' data-btntrigger='modal' action='/fisioterapi/informasi-program-fisioterapi-rajal/detail-cppt?id=$primaryKey' data-toggle='modal' data-options='modal' class='btn-transparent'><img class='img-icon' src='/media/img/icon-app/detail-cppt.svg'></button>";
        return $btnDetailCppt;
    }

    private static function buttonEditProgram($primaryKey, $data)
    {
        $isPaket = ArrayHelper::getValue($data, 'is_paket');
        $btnEditProgramFisio = "";
        $bayar = ArrayHelper::getValue($data, 'bayar');
        $realisasi = ArrayHelper::getValue($data, 'realisasi');
        $statusProgramFisio = ArrayHelper::getValue($data, 'status_program_fisio_nama');
        $statusIsEditable = $statusProgramFisio == DocoConstants::OPEN;
        $isEditableNonPaket = !$isPaket & ($realisasi <= 0) & $statusIsEditable;
        $isEditablePaket = $isPaket & ($realisasi <= 0) & ($bayar != 1) & $statusIsEditable;
        $isEditable = $isEditablePaket || $isEditableNonPaket;
        $isPaket = DocoHelpers::decrypt(1);
        if ($isEditablePaket) $primaryKey = "$primaryKey&p=true";
        if ($isEditable) {
            $btnEditProgramFisio = "<button data-width='75%' data-wrapper='#modal-lab .modal-content' data-target='#modal-lab' title='Edit Program' type='button' data-btntrigger='modal' action='/fisioterapi/informasi-program-fisioterapi-rajal/edit?id=$primaryKey' data-toggle='modal' data-options='modal' class='btn-transparent'><img class='img-icon' src='/media/img/icon-app/edit-program.svg'></button>";
        } else {
            $btnEditProgramFisio = "<button disabled data-width='75%' data-wrapper='#modal-lab .modal-content' data-target='#modal-lab' title='Tidak Bisa Edit Program' type='button' data-btntrigger='modal' action='/fisioterapi/informasi-program-fisioterapi-rajal/edit?id=$primaryKey' data-toggle='modal' data-options='modal' class='btn-transparent'><img class='img-icon' src='/media/img/icon-app/edit-program.svg'></button>";
        }
        return $btnEditProgramFisio;
    }
}
