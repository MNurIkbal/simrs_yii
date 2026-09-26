<?php
namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use yii\base\View;
use Integrasi\Components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoPrint;
use Integrasi\Components\DocoConstants;

class ExportFile extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
   {
      ini_set('memory_limit', '-1');
      $cacheFiles = Yii::$app->cacheFiles;
      $fileType = ($this->type == 1) ? 'PDF' : 'Excel';
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Sedang menyiapkan file '.$fileType,
            'progress' => 80
         ]),
      ]);

      $dataRow = $cacheFiles->get($this->unique_str);
      if($this->type == 1) {
         $path = 'uploads/' . $this->unique_str;
         return $this->generatePdf($dataRow,$path);
      }
      else {
         $path = 'uploads/' . $this->unique_str.'.xlsx';
         return $this->generateExcel($dataRow,$path);
      }
   }

   private function custHeader()
   {
      return [
         [
            [
               'label' => 'No',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Penerimaan',
               'rowspan' => 2,
            ],
            [
               'label' => 'No Pembayaran',
               'rowspan' => 2,
            ],
            [
               'label' => 'Cara Bayar',
               'rowspan' => 2,
            ],
            [
               'label' => 'Penjamin',
               'rowspan' => 2,
            ],
            [
               'label' => 'Total Pembayaran',
               'rowspan' => 2,
            ],
            [
               'label' => 'Status Alokasi',
               'rowspan' => 2,
            ],
         ]
      ];
   }

   private function generateExcel($dataRow,$path)
   {
      $tmpCache = $tmp = [];
      $no = 1;
      foreach ($dataRow as $value) {
         $tglTerimaBayarKlaim = ArrayHelper::getValue($value, 'tgl_terimabayarklaim');
         $finalAlokasi = ArrayHelper::getValue($value, 'final_alokasi');
         if(!empty($tglTerimaBayarKlaim)) {
            $tglTerimaBayarKlaim = date('d-M-Y', strtotime($tglTerimaBayarKlaim));
         }

         $tmp[1]  = $no;
         $tmp[2]  = $tglTerimaBayarKlaim;
         $tmp[3]  = ArrayHelper::getValue($value, 'no_terimabayarklaim');
         $tmp[4]  = ArrayHelper::getValue($value, 'carabayar_nama');
         $tmp[5]  = ArrayHelper::getValue($value, 'penjamin_nama');
         $tmp[6]  = number_format(ArrayHelper::getValue($value, 'total_terimabayar', 0), 0,",",".");
         $tmp[7]  = isset(DocoConstants::$statusAlokasi[$finalAlokasi]) ? DocoConstants::$statusAlokasi[$finalAlokasi] : null ;
         $tmpCache[] = $tmp;
         $no++;
      }
      
      $custHeader = $this->custHeader();
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:' . $this->unique_str,
         'message' => json_encode([
            'status' => 'finish',
            'messageProcess' => 'Sedang mengimport data ke dalam Excel',
            'progress' => 85
         ]),
      ]);
      
      $start = date('d-M-Y');
      $end = date('d-M-Y');
      if(isset($this->filter['tgl_terimabayarklaim'])) {
         $explode = explode("-", $this->filter['tgl_terimabayarklaim']);
         if(count($explode) == 2) {
            $start = date('d-M-Y', strtotime($explode[0]));
            $end = date('d-M-Y', strtotime($explode[1]));
         }
      }

      $caraBayarNama = $penjaminNama = $statusAlokasi = '';
      if(isset($this->filter['carabayar_id'])) {
         $caraBayarId = $this->filter['carabayar_id'];
         $caraBayar = Yii::$app->db->createCommand("
            SELECT carabayar_nama FROM carabayar_m
            WHERE carabayar_id = {$caraBayarId}
         ")->queryOne();
         $caraBayarNama = ArrayHelper::getValue($caraBayar, 'carabayar_nama');
      }
      
      if(isset($this->filter['penjamin_id'])) {
         $penjaminId = $this->filter['penjamin_id'];
         $penjamin = Yii::$app->db->createCommand("
            SELECT penjamin_nama FROM penjamin_m
            WHERE penjamin_id = {$penjaminId}
         ")->queryOne();
         $penjaminNama = ArrayHelper::getValue($penjamin, 'penjamin_nama');
      }

      if(isset($this->filter['final_alokasi'])) {
         $finalAlokasi = $this->filter['final_alokasi'];
         $statusAlokasi = isset(DocoConstants::$statusAlokasi[$finalAlokasi]) ? DocoConstants::$statusAlokasi[$finalAlokasi] : null;
      }

      $footer = [];
      $header = [
         'Periode' => $start .' s/d '. $end,
         'No. Pembayaran' => strtoupper(ArrayHelper::getValue($this->filter, 'no_terimabayarklaim', '-')),
         'Cara Bayar' => strtoupper($caraBayarNama),
         'Penjamin' => strtoupper($penjaminNama),
         'Status Alokasi' => strtoupper($statusAlokasi),
      ];

      $filePath = DocoHelpers::exportExcel('Penerimaan Pembayaran', $tmpCache, $header, [
         "skipIncrement" => true,
         'customHeader' => $custHeader,
      ], $footer, [], true);

      $filePath->save($path);
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:' . $this->unique_str,
         'message' => json_encode([
            'status' => 'finish',
            'messageProcess' => 'Proses import Excel berhasil',
            'progress' => 90
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-ExportFile',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }

   private function generatePdf($dataRow,$path)
   {
      Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				'status' => 'finish', 
            'messageProcess' => 'Sedang mengekstrak data PDF',
            'progress' => 80
			]),
		]);
      
      $print = new DocoPrint('inf-penerimaan-pembayaran');
      $start = date('d-M-Y');
      $end = date('d-M-Y');
      if(isset($this->filter['tgl_terimabayarklaim'])) {
         $explode = explode("-", $this->filter['tgl_terimabayarklaim']);
         if(count($explode) == 2) {
            $start = date('d-M-Y', strtotime($explode[0]));
            $end = date('d-M-Y', strtotime($explode[1]));
         }
      }

      $pathView = "@app/views/penjamin-asuransi/inf-penerimaan-pembayaran/index";
      $kabagKeuangan = DocoConstants::KABAG_KEUANGAN;
      $lookupTransaksi = Yii::$app->db->createCommand("
         SELECT kode_id FROM lookuptransaksi_m
         WHERE kode_transaksi = '{$kabagKeuangan}'
      ")->queryOne();
      
      $jabatanId = ArrayHelper::getValue($lookupTransaksi, 'kode_id');
      $pegawai = Yii::$app->db->createCommand("
         SELECT nama_pegawai,nomorindukpegawai,jabatan_nama FROM pegawai_v
         WHERE jabatan_id = '{$jabatanId}' AND pegawai_last_modified_date IS NOT NULL 
         ORDER BY pegawai_last_modified_date DESC
      ")->queryOne();

      $jabatan = ArrayHelper::getValue($pegawai, 'jabatan_nama');
      $nip = ArrayHelper::getValue($pegawai, 'nomorindukpegawai');
      $mengetahui = ArrayHelper::getValue($pegawai, 'nama_pegawai');
      $attributes = [
         '#datatable#' => (new View)->render($pathView, [
            'data' => $dataRow,
         ]),
         '#periode#' => $start .' s/d '. $end,
         '#tanggal#' => date('d M Y'),
         '#pegawai#' => $mengetahui,
         '#jabatan#' => $jabatan,
         '#nip#' => $nip,
      ];
      $print->attributes = $attributes;
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Sedang mengimport data ke dalam PDF',
            'progress' => 85
         ]),
      ]);

      $print->Output(false, $path);
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Proses import PDF berhasil',
            'progress' => 90
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-ExportFile',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }
}