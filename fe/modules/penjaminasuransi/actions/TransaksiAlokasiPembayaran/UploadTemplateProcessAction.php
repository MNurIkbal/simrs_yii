<?php

namespace Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;

use app\components\DocoController;

use Doco\penjaminasuransi\models\PenerimaanPembayaranForm;
use Doco\penjaminasuransi\models\AlokasiPengajuanForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiImportForm;

class UploadTemplateProcessAction extends BaseCurrentAction
{
    private function sendSuccessResponse($filename, $datas)
    {
        $response['response']['file'] = $filename;
        $response['response']['data'] = $datas;
        $response['response']['status'] = 200;
        $response['response']['title'] = 'Proses Berhasil';
        $response['response']['text'] = 'Data Berhasil di upload!';
        return DocoHelpers::response($response, 200);
    }

    private function sendErrorResponse($msg = 'Format yang di Upload tidak sesuai, harus berupa xls, xlsx')
    {
        $response['response']['file'] = '';
        $response['response']['data'] = [];
        $response['response']['status'] = 422;
        $response['response']['title'] = 'Proses Gagal';
        $response['response']['text'] = $msg;
        return DocoHelpers::response($response, 422);
    }

    private function convertExcelImportToDatas($dirtyExcelData)
    {
        $datas = [];
        $counterNumber = 1;
        for ($row = 7; $row <= $dirtyExcelData['highestRow']; $row++) {
            $rowData = $dirtyExcelData['sheet']->rangeToArray('B' . $row . ':' . $dirtyExcelData['highestColumn'] . $row, Null, true, false);
            foreach ($rowData as $key => $value) {
                $status = 'complete';
                $dataPasien = ArrayHelper::getValue($value, '0');
                $noInvoice = ArrayHelper::getValue($value, '1');
                $noSep = ArrayHelper::getValue($value, '2');
                $tanggalMasuk = ArrayHelper::getValue($value, '3');
                $tanggalKeluar = ArrayHelper::getValue($value, '4');
                $instalasiRuangan = ArrayHelper::getValue($value, '5');
                $tagihan = ArrayHelper::getValue($value, '6');
                $pasienBayar = ArrayHelper::getValue($value, '7');
                $piutang = ArrayHelper::getValue($value, '8');
                $piutangBayar = ArrayHelper::getValue($value, '9');
                $jumlahBayar = ArrayHelper::getValue($value, '10');
                $sisaTagihan = ArrayHelper::getValue($value, '11');
                $isEmptyRow = !$value[0] && !$value[1] && !$value[6] && !$value[7] && !$value[8] && !$value[9] && !$value[10] && !$value[11];
                $isLargeThan = $jumlahBayar > $sisaTagihan;
                $isNegative = $jumlahBayar < 0;
                $isNotComplete = $value[6] === null || $value[7]  === null || $value[8]  === null || $value[9]  === null || $value[10]  === null || $value[11] === null;
                if ($isEmptyRow) {
                    continue;
                } else if ($isNotComplete) {
                    $status = 'incomplete';
                } else if ($isLargeThan) {
                    $status = 'is_large_than';
                } else if ($isNegative) {
                    $status = 'is_negative';
                }
                $item = [
                    'no' => $counterNumber,
                    'data_pasien' => $dataPasien,
                    'no_sep' => $noSep,
                    'no_invoice' => $noInvoice,
                    'instalasi_ruangan' => $instalasiRuangan,
                    'tagihan' => $tagihan,
                    'pasien_bayar' => $pasienBayar,
                    'piutang' => $piutang,
                    'piutang_bayar' => $piutangBayar,
                    'jumlah_bayar' => $jumlahBayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'status' => $status
                ];
                $datas[] = $item;
                $counterNumber++;
            }
        }
        return $datas;
    }

    public function run()
    {
        $request = Yii::$app->request;
        $model = new TransaksiAlokasiImportForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        $result = [];
        if ($model->upload_file == NULL) {
            return $this->sendErrorResponse();
        }
        $fileName = $model->upload_file->name;
        $file = $model->upload_file->tempName;
        if (isset($model->upload_file)) {
            $result = [];
            $getDataImport =  DocoHelpers::getUploadFileExcel($file);
            $result = $this->convertExcelImportToDatas($getDataImport);
            return $this->sendSuccessResponse($fileName, $result);
        } else {
            return $this->sendErrorResponse();
        }
        return $this->sendErrorResponse();
    }
}
