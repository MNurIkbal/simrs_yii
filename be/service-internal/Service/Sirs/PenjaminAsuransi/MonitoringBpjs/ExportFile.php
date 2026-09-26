<?php
namespace Integrasi\Service\Sirs\PenjaminAsuransi\MonitoringBpjs;

use Yii;
use yii\base\View;
use Integrasi\Components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoPrint;

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
               'label' => 'Tanggal Masuk',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tanggal Keluar',
               'rowspan' => 2,
            ],
            [
               'label' => 'Nama Pasien',
               'rowspan' => 2,
            ],
            [
               'label' => 'No Rekam Medik',
               'rowspan' => 2,
            ],
            [
               'label' => 'No Pendaftaran',
               'rowspan' => 2,
            ],
            [
               'label' => 'No SEP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Penjamin',
               'rowspan' => 2,
            ],
            [
               'label' => 'Ruangan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Kamar',
               'rowspan' => 2,
            ],
            [
               'label' => 'No Bed',
               'rowspan' => 2,
            ],
            [
               'label' => 'Hak Kelas',
               'rowspan' => 2,
            ],

            [
               'label' => 'Dokter Penanggung Jawab',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnosa Utama',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnosa Penyerta',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tindakan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tagihan RS (Rp)',
               'rowspan' => 2,
            ],
            [
               'label' => 'Tarif Inacbg (Rp)',
               'rowspan' => 2,
            ],
            [
               'label' => 'Persentase (%)',
               'rowspan' => 2,
            ],
            [
               'label' => 'Status',
               'rowspan' => 2,
            ],
            [
               'label' => 'Status Periksa',
               'rowspan' => 2,
            ]
         ]
      ];
   }

   private function generateExcel($dataRow,$path)
   {
      $tmpCache = $tmp = [];
      $no = 1;
      foreach ($dataRow as $value) {
         $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
         $tglPasienPulang = ArrayHelper::getValue($value, 'tglpasienpulang');
         $setDiagnosaTindakan = ArrayHelper::getValue($value, 'set_diagnosatindakan');
         $setDiagnosaPenyerta = ArrayHelper::getValue($value, 'set_diagnosapenyerta');
         $tarifInacbg = ArrayHelper::getValue($value, 'tarif_inacbg', 0);
         $tagihanRs = ArrayHelper::getValue($value, 'tagihan_rs', 0);
         $persentase = ($tarifInacbg == 0) ? 0 : ceil(($tagihanRs/$tarifInacbg) * 100);

         if(!empty($tglPendaftaran)) {
            $tglPendaftaran = date('d-M-Y', strtotime($tglPendaftaran));
         }
         if(!empty($tglPasienPulang)) {
            $tglPasienPulang = date('d-M-Y', strtotime($tglPasienPulang));
         }

         $listDiagnosaTindakan = $listDiagnosaPenyerta = [];
         $diagnosaTindakanNama = '';
         $diagnosaPenyertaNama = '';
         
         if(!empty($setDiagnosaTindakan)) {
               $listDiagnosaTindakan = json_decode($setDiagnosaTindakan, true);
               if(!empty($listDiagnosaTindakan)) {
                  foreach ($listDiagnosaTindakan as $k => $v) {
                     $kode = ArrayHelper::getValue($v, 'kode');
                     $text = ArrayHelper::getValue($v, 'text');
                     if(!empty($kode) && !empty($text)) {
                        $diagnosaTindakanNama .= $kode.' - '.$text.' ';
                     }
                  }
               }
         }
         
         if(!empty($setDiagnosaPenyerta)) {
               $listDiagnosaPenyerta = json_decode($setDiagnosaPenyerta, true);
               if(!empty($listDiagnosaPenyerta)) {
                  foreach ($listDiagnosaPenyerta as $k => $v) {
                     $kode = ArrayHelper::getValue($v, 'kode');
                     $text = ArrayHelper::getValue($v, 'text');
                     if(!empty($kode) && !empty($text)) {
                        $diagnosaPenyertaNama .= $kode.' - '.$text.' ';
                     }
                  }
               }
         }

         $tmp[1]  = $no;
         $tmp[2]  = $tglPendaftaran;
         $tmp[3]  = $tglPasienPulang;
         $tmp[4]  = ArrayHelper::getValue($value, 'nama_pasien');
         $tmp[5]  = ArrayHelper::getValue($value, 'no_rekam_medik');
         $tmp[6]  = ArrayHelper::getValue($value, 'no_pendaftaran');
         $tmp[7]  = ArrayHelper::getValue($value, 'nosep');
         $tmp[8]  = ArrayHelper::getValue($value, 'penjamin_nama');
         $tmp[9]  = ArrayHelper::getValue($value, 'ruangan_nama');
         $tmp[10]  = ArrayHelper::getValue($value, 'kamarruangan_nokamar');
         $tmp[11]  = ArrayHelper::getValue($value, 'no_tempattidur');
         $tmp[12]  = ArrayHelper::getValue($value, 'hak_kelas');
         $tmp[13]  = ArrayHelper::getValue($value, 'dokter_dpjp');
         $tmp[14]  = ArrayHelper::getValue($value, 'set_diagnosautama');
         $tmp[15]  = $diagnosaPenyertaNama;
         $tmp[16]  = $diagnosaTindakanNama;
         $tmp[17]  = number_format(ArrayHelper::getValue($value, 'tagihan_rs', 0), 0,",",".");
         $tmp[18]  = number_format(ArrayHelper::getValue($value, 'tarif_inacbg', 0), 0,",",".");
         $tmp[19]  = number_format($persentase, 2);
         $tmp[20]  = ArrayHelper::getValue($value, 'status_monitor');
         $tmp[21]  = ArrayHelper::getValue($value, 'status_periksa');

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
      if(isset($this->filter['tgl_pendaftaran'])) {
         $explode = explode("-", $this->filter['tgl_pendaftaran']);
         if(count($explode) == 2) {
            $start = date('d-M-Y', strtotime($explode[0]));
            $end = date('d-M-Y', strtotime($explode[1]));
         }
      }

      $footer = [];
      $header = [
         'Periode' => $start .' s/d '. $end,
         'Nama Pasien' => strtoupper(ArrayHelper::getValue($this->filter, 'nama_pasien', '-')),
         'No Rekam Medik' => ArrayHelper::getValue($this->filter, 'no_rekam_medik', '-'),
         'No Pendaftaran' => strtoupper(ArrayHelper::getValue($this->filter, 'no_pendaftaran', '-')),
         'No SEP' => strtoupper(ArrayHelper::getValue($this->filter, 'nosep', '-')),
      ];

      $filePath = DocoHelpers::exportExcel('Monitoring BPJS', $tmpCache, $header, [
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
      
      $print = new DocoPrint('monitoring-bpjs');
      $start = date('d-M-Y');
      $end = date('d-M-Y');
      if(isset($this->filter['tgl_pendaftaran'])) {
         $explode = explode("-", $this->filter['tgl_pendaftaran']);
         if(count($explode) == 2) {
            $start = date('d-M-Y', strtotime($explode[0]));
            $end = date('d-M-Y', strtotime($explode[1]));
         }
      }
      $pathView = "@app/views/index";
      $attributes = [
         '#datatable#' => (new View)->render($pathView, [
            'data' => $dataRow,
         ]),
         '#periode#' => $start .' s/d '. $end,
         '#tanggal#' => date('d M Y'),
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