<?php

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\TerimaBayarKlaimDetailR;

class ImportKlaim extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $cacheFiles = Yii::$app->cacheFiles;
      $nameFile = $this->nameFile;
      $dataImport = DocoHelpers::getUploadFileExcel($nameFile);
      $sheet = ArrayHelper::getValue($dataImport, 'sheet', []);
      if(empty($sheet)) {
         Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'invoice:'.$this->randString,
            'message' => json_encode([
               'status' => 'failed', 
               'messageProcess' => 'Data Import tidak ditemukan',
            ]),
         ]);
      }
      else {
         Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'invoice:'.$this->randString,
            'message' => json_encode([
               'status' => 'finish', 
                  'messageProcess' => 'Sedang mengconvert data.',
                  'progress' => 70
            ]),
         ]);
         
         $result = $this->convertExcelImportToDatas($dataImport);
         if(empty($result)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
               'channel' => 'invoice:'.$this->randString,
               'message' => json_encode([
                  'status' => 'failed', 
                  'messageProcess' => 'Format Kolom di Excel Tidak Sesuai Format.',
               ]),
            ]);
         }

         $cacheFiles->set('detail-klaim-bpjs-'.$this->randString, $result);
         unlink($nameFile);
      }

      return json_encode([
         'service' => 'Sirs-ImportKlaim',
         'payload' => $this->randString,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   private function convertExcelImportToDatas($dirtyExcelData)
   {
      $datas = [];
      $counterNumber = 1;
      $totalDisetujui = $totalPengajuan = 0;
      $valid = false;
      for ($row = 1; $row <= $dirtyExcelData['highestRow']; $row++) {
         $rowData = $dirtyExcelData['sheet']->rangeToArray('B' . $row . ':' . $dirtyExcelData['highestColumn'] . $row, Null, true, false);
         $data = ArrayHelper::getValue($rowData, '0');
         $columnNoSep = ArrayHelper::getValue($data, '0');
         $columnNoSep = strtolower($columnNoSep);
         $columnTglVerifikasi = ArrayHelper::getValue($data, '1');
         $columnTglVerifikasi = strtolower($columnTglVerifikasi);
         $columnRiilRs = ArrayHelper::getValue($data, '2');
         $columnRiilRs = strtolower($columnRiilRs);
         $columnDiajukan = ArrayHelper::getValue($data, '3');
         $columnDiajukan = strtolower($columnDiajukan);
         $columnDisetujui = ArrayHelper::getValue($data, '4');
         $columnDisetujui = strtolower($columnDisetujui);
         if($columnNoSep == 'no.sep' && $columnTglVerifikasi == 'tgl. verifikasi' && $columnRiilRs == 'riil rs' && $columnDiajukan == 'diajukan' && $columnDisetujui == 'disetujui') {
            $valid = true;
         }
      }

      if(!$valid) {
         return [];
      }

      for ($row = 3; $row <= $dirtyExcelData['highestRow']; $row++) {
         $rowData = $dirtyExcelData['sheet']->rangeToArray('B' . $row . ':' . $dirtyExcelData['highestColumn'] . $row, Null, true, false);
         foreach ($rowData as $key => $value) {
            $noSep = ArrayHelper::getValue($value, '0');
            if(!empty($noSep)) {
               $diajukan = ArrayHelper::getValue($value, '3', 0);
               $disetujui = ArrayHelper::getValue($value, '4', 0);
               $tglVerifikasi = ArrayHelper::getValue($value, '1');
               if(!empty($tglVerifikasi)) {
                  $tglVerifikasi = ($value['1'] - 25569) * 86400;
                  $tglVerifikasi = gmdate("Y-m-d", $tglVerifikasi);
               }
               $item = [
                  'no' => $counterNumber,
                  'no_pengajuan' => '-',
                  'no_sep' => $noSep,
                  'tgl_verifikasi' => $tglVerifikasi,
                  'riil_rs' => ArrayHelper::getValue($value, '2'),
                  'diajukan' => $diajukan,
                  'disetujui' => $disetujui,
               ];
               $datas[] = $item;
               $totalPengajuan += $diajukan;
               $totalDisetujui += $disetujui;
               $counterNumber++;
            }
         }
      }

      return [
         'data' => $datas,
         'total_pengajuan' => $totalPengajuan,
         'total_disetujui' => $totalDisetujui,
      ];
   }
}