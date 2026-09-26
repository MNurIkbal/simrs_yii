<?php

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\UploadForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UploadProcessAction extends Action
{
	public function run()
	{
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $getData = Yii::$app->cache->get("upload-kontrak-supplier-".$user_login);
        if ($getData) Yii::$app->cache->delete("upload-kontrak-supplier-".$user_login);
        
        $model = new UploadForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        if ($model->upload_file == NULL || !$model->validate()) {
            Yii::$app->cache->set("upload-kontrak-supplier-".$user_login, []);
            return $this->errorResponse();
        }

        $fileName = $model->upload_file->name;
        $file = $model->upload_file->tempName;
        if (isset($model->upload_file)) {
            $datas = $this->convertExcelToData($file);
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                'url' => 'kontrak-supplier/mapping-validasi-data-excel',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'payload' => json_encode($datas, true)
                    ],
                ]
            ]);
            $response = ArrayHelper::getValue($response, 'data', []);
            
            $nomer = 0;
            $result = [];
            foreach ($response as $key => $value) {
                $nomer ++;
                $value['nomer'] = $nomer;
                $value['harga'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'harga'));
                $value['diskon'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'diskon'));
                $value['qty_min'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'qty_min'));
                $value['total_harga'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_harga'));
                $value['keterangan'] = ArrayHelper::getValue($value, 'keterangan');

                $result[] = $value;
            }

            Yii::$app->cache->set("upload-kontrak-supplier-".$user_login, $result);
            $getData = Yii::$app->cache->get("upload-kontrak-supplier-".$user_login);
            return $this->successResponse($fileName, $result);
        } else {
            return $this->errorResponse();   
        }
        return $this->errorResponse();
        
	}

    private function successResponse($filename, $datas)
    {
        $response['response']['file'] = $filename;
        $response['response']['data'] = $datas;
        $response['response']['data_valid'] = $this->countByStatus($datas);
        $response['response']['data_tidak_valid'] = $this->countByStatus($datas, false);
        $response['response']['status'] = 200;
        $response['response']['title'] = 'Proses Berhasil';
        $response['response']['text'] = 'Data Berhasil di upload!';
        return DocoHelpers::response($response, 200);
    }

    private function errorResponse($msg = 'Format yang di Upload tidak sesuai, harus berupa xls, xlsx')
    {
        $response['response']['file'] = '';
        $response['response']['data'] = [];
        $response['response']['data_valid'] = 0;
        $response['response']['data_tidak_valid'] = 0;
        $response['response']['status'] = 422;
        $response['response']['title'] = 'Proses Gagal';
        $response['response']['text'] = $msg;

        return DocoHelpers::response($response, 422);
    }

    private function convertExcelToData($file)  
    {
        $datas = [];
        $getDataImport =  DocoHelpers::getUploadFileExcel($file);
        for ($row = 1; $row <= ArrayHelper::getValue($getDataImport, 'highestRow', 0); $row++) {
            $rowData = $getDataImport['sheet']->rangeToArray('B'.$row.':'.$getDataImport['highestColumn'].$row,Null,true, false);
            if ($row < 10) continue;
            foreach ($rowData as $key => $value) {
                $item = [
                    'kontraksupplier_no' => trim(ArrayHelper::getValue($value, '0')),
                    'tgl_berlaku' => $this->convertExcelToDate(ArrayHelper::getValue($value, '1')),
                    'kode_supplier' => trim(ArrayHelper::getValue($value, '2')),
                    'supplier' => trim(ArrayHelper::getValue($value, '3')),
                    'payterm' => trim(ArrayHelper::getValue($value, '4')),
                    'persen_ppn' => ArrayHelper::getValue($value, '5'),
                    'contact_person' => trim(ArrayHelper::getValue($value, '6')),
                    'kode_obat' => trim(ArrayHelper::getValue($value, '7')),
                    'nama_obat' =>trim( ArrayHelper::getValue($value, '8')),
                    'harga' => ArrayHelper::getValue($value, '9'),
                    'diskon' => ArrayHelper::getValue($value, '10'),
                    'qty_min' => ArrayHelper::getValue($value, '11'),
                    'total_harga' => ArrayHelper::getValue($value, '12'),
                ];
                $datas[] = $item;
            }
        }
        return $datas;
    }

    private function convertExcelToDate($value)
    {
        $tanggal = null;
        if ($value) {
            $tanggal = $value;
            if (!strpos($value, '/')) {
                $tanggal = gmdate("d/m/Y", ($value - 25569) * 86400);
            } else {
                $tanggal = str_replace('/', '/', $value);
            }
        }
        return $tanggal;
    }

    private function countByStatus(array $result, $status = true)
    {
        return count(array_filter($result, function ($var) use ($status) {
            return (ArrayHelper::getValue($var, 'status') == $status);
        }));
    }
}
