<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportExcelUangMuka extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
   {
      ini_set('memory_limit', '-1');
      $cacheFiles = Yii::$app->cacheFiles;
      $headerExcel = $this->headerExcel();
      $header = $this->setHeaderExcel();
      $footer = $options = [];
      $row = $tmp = [];
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
               'status' => 'finish', 
               'messageProcess' => 'Sedang menyiapkan file excel.',
               'progress' => 80
         ]),
      ]);

      $dataRow = $cacheFiles->get($this->unique_str);
      if (!empty($dataRow)) {
         foreach ($dataRow as $index => $value) {
            foreach($header as $row){
               $title = isset($row['title']) ? $row['title'] : '';
               $data = isset($row['data']) ? $row['data'] : '';
               $tglPulang = isset($value['tgl_pulang']) && !empty($value['tgl_pulang']) ? date('d-M-Y', strtotime($value['tgl_pulang'])) : '';
               $str = !empty($tglPulang) ? ' - ' : '';
               $value['tgl_pendaftaran'] = date('d-M-Y', strtotime($value['tgl_pendaftaran'])).$str.$tglPulang;
               $value['tgl_pembayaran'] = isset($value['tgl_pembayaran']) && !empty($value['tgl_pembayaran']) ? date('d-M-Y', strtotime($value['tgl_pembayaran'])) : '';
               $tmp[$index][$title] = isset($value[$data]) ? $value[$data] : [];
            }
         }
         $row = $tmp;
      }
      
      $path = 'uploads/'. $this->unique_str .'.xlsx';
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
               'status' => 'finish', 
               'messageProcess' => 'Sedang mengimport data ke dalam excel.',
               'progress' => 85
         ]),
      ]);

      $title = 'Informasi Uang Muka';
      $filePath = DocoHelpers::exportExcel($title, $row, $headerExcel, $options, $footer, [], true);
      $filePath->save($path);
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
               'status' => 'finish', 
               'messageProcess' => 'Proses import excel berhasil.',
               'progress' => 90
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-ExportExcelUangMuka',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   private function headerExcel()
   {
      $start = date('Y-m-d 00:00:00');
      $end = date('Y-m-d 23:59:00');
      $starBayar = $endBayar = '';
      $filter = $this->filter;
      if(isset($filter['advanced-filter'])) {
         $advancedFilter = $filter['advanced-filter'];
         if(isset($advancedFilter['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
            if(count($explode) == 2) {
               $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
               $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($filter['advanced-filter']['tgl_pendaftaran']);
         }
         if(isset($advancedFilter['tgl_pembayaran']) && !empty($advancedFilter['tgl_pembayaran'])) {
            $explode = explode(" - ", $advancedFilter['tgl_pembayaran']);
            if(count($explode) == 2) {
               $starBayar = date('Y-m-d 00:00:00', strtotime($explode[0]));
               $endBayar = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($filter['advanced-filter']['tgl_pembayaran']);
         }
      }

      $periodePendaftaran = $periodePembayaran = '';
      if(!empty($start) && !empty($end)) {
         $periodePendaftaran = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
      }
      if(!empty($starBayar) && !empty($endBayar)) {
         $periodePembayaran = date('d-M-Y', strtotime($starBayar)).' - '.date('d-M-Y', strtotime($endBayar));
      }

      return [
         'Tanggal Pendaftaran' => $periodePendaftaran,
         'Tanggal Pembayaran' => $periodePembayaran,
         'No Pendaftaran' => isset($filter['advanced-filter']['no_pendaftaran']) ? $filter['advanced-filter']['no_pendaftaran'] : '-',
         'No Rekam Medik' => isset($filter['advanced-filter']['no_rekam_medik']) ? $filter['advanced-filter']['no_rekam_medik'] : '-',
         'Nama Pasien' => isset($filter['advanced-filter']['nama_pasien']) ? $filter['advanced-filter']['nama_pasien'] : '-',
      ];
   }

   private function setHeaderExcel()
   {
      $column = [
         [
            'title' => 'Tanggal Masuk - Keluar',
            'data' => 'tgl_pendaftaran'
         ],
         [
            'title' => 'Tanggal Pembayaran',
            'data' => 'tgl_pembayaran'
         ],
         [
            'title' => 'No Pendaftaran',
            'data' => 'no_pendaftaran'
         ],
         [
            'title' => 'No Rekam Medik',
            'data' => 'no_rekam_medik'
         ],
         [
            'title' => 'Nama Pasien',
            'data' => 'nama_pasien'
         ],
         [
            'title' => 'Jumlah Uang Muka',
            'data' => 'jumlah_uangmuka'
         ],
         [
            'title' => 'Jumlah Pemakaian',
            'data' => 'pemakaian_uangmuka'
         ],
         [
            'title' => 'Jumlah Pengembalian',
            'data' => 'pengembalian'
         ],
         [
            'title' => 'Sisa',
            'data' => 'sisa_uangmuka'
         ],
      ];

      return $column;
   }
}