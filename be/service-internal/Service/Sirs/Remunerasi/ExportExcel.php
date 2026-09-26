<?php
namespace Integrasi\Service\Sirs\Remunerasi;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Remunerasi\RemunPegawaiView;
use yii\helpers\ArrayHelper;


class ExportExcel extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      ini_set('memory_limit', '-1');
      $cacheFiles = Yii::$app->cacheFiles;
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Sedang menyiapkan file excel.',
            'progress' => 80
         ]),
      ]);

      $get = $this->get;
      $filter = $this->filter;
      $dataRow = $cacheFiles->get($this->unique_str);
      
      $listKelompok = [];
      $tmpCache = $tmp = [];
      $no = 1;
      if(!empty($dataRow)) {
         foreach ($dataRow as $key => $value) {
            $detailTindakan = ArrayHelper::getValue($value, 'detail_tindakan', []);
            if(!empty($detailTindakan)) {
               $detailTindakan = json_decode($detailTindakan, true);
               foreach ($detailTindakan as $key => $rowKelompok) {
                  $kelompokNama = ArrayHelper::getValue($rowKelompok, 'tindakan_nama');
                  $listKelompok[$kelompokNama] = $rowKelompok;
               }
            }
            $tmp[1]  = $no;
            $tmp[2]  = ArrayHelper::getValue($value, 'nama_pegawai');
            $tmp[3]  = ArrayHelper::getValue($value, 'jabatan_nama');
            $tmp[4]  = ArrayHelper::getValue($value, 'jabatan_kode');
            $tmp[5]  = ArrayHelper::getValue($value, 'grading');
            $tmp[6]  = ArrayHelper::getValue($value, 'posisi');
            $tmp[7]  = ArrayHelper::getValue($value, 'target_point', 0);
            $tmp[8]  = number_format(ArrayHelper::getValue($value, 'target_nominal', 0), 0,",",".");
            $counter = 9;
            foreach ($listKelompok as $key => $rowsKelompok) {
               $point = ArrayHelper::getValue($listKelompok[$key], 'point', 0);
               $tmp[$counter++] = $point;
            }
            $tmp[84] = ArrayHelper::getValue($value, 'ronde_besar');
            $tmp[85] = ArrayHelper::getValue($value, 'koord_lap_jaga_bangsal');
            $tmp[86] = ArrayHelper::getValue($value, 'rapat_koord_pelayanan');
            $tmp[87] = ArrayHelper::getValue($value, 'audit_medik');
            $tmp[88] = ArrayHelper::getValue($value, 'penelitian');
            $tmp[89] = ArrayHelper::getValue($value, 'presentasi');
            $tmp[90] = ArrayHelper::getValue($value, 'rapat_direksi');
            $tmp[91] = ArrayHelper::getValue($value, 'kwalitas');
            $tmp[92] = ArrayHelper::getValue($value, 'kepatuhan');
            $tmp[93] = ArrayHelper::getValue($value, 'jumlah_point');
            $tmp[94] = ArrayHelper::getValue($value, 'manajerial');
            $tmp[95] = ArrayHelper::getValue($value, 'total_point');
            $tmp[96] = ArrayHelper::getValue($value, 'iki');
            $tmp[97] = number_format(ArrayHelper::getValue($value, 'nominal', 0), 0,",",".");
            $tmp[98] = number_format(ArrayHelper::getValue($value, 'max_kmk', 0), 0,",",".");
            $tmp[99] = number_format(ArrayHelper::getValue($value, 'imbal_jasa', 0), 0,",",".");
            $tmpCache[] = $tmp;
            $no++;
            $key++;
         }
      }
      $custHeader = $this->custHeader();
      $path = 'uploads/' . $this->unique_str . '.xlsx';
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:' . $this->unique_str,
         'message' => json_encode([
            'status' => 'finish',
            'messageProcess' => 'Sedang mengimport data ke dalam excel',
            'progress' => 85
         ]),
      ]);

      $bulan = date('m');
      $tahun = date('Y');
      $ids = ArrayHelper::getValue($get, 'ids', []);
      if(isset($filter['periode_remun']) && !empty($filter['periode_remun'])) {
         $explode = explode("-", $filter['periode_remun']);
         if(count($explode) == 2) {
            $bulan = ArrayHelper::getValue($explode, 1);
            $tahun = ArrayHelper::getValue($explode, 0);
         }
         
      }
      $footer = [];
      $header = [];
      $countIds = count(array($ids));
      $bulan = (int) $bulan;
      if(!empty($ids)) {
         if($countIds == 1) {
            $remunPegawai = RemunPegawaiView::find()->where(['remunpegawai_id' => $ids])->one();
            $header = [
               'Periode Remunerasi' => !empty($bulan) ? DocoHelpers::$_bulan[$bulan].' '.$tahun : ' ',
               'Nama Pegawai' => strtoupper(ArrayHelper::getValue($remunPegawai, 'nama_pegawai', '-')),
               'NIK' => ArrayHelper::getValue($remunPegawai, 'nik', '-'),
               'Jabatan' => strtoupper(ArrayHelper::getValue($remunPegawai, 'jabatan_nama', '-')),
            ];
         }
         else {
            $header = [
               'Periode Remunerasi' => !empty($bulan) ? DocoHelpers::$_bulan[$bulan].' '.$tahun : ' ',
               'Nama Pegawai' => '-',
               'NIK' => '-',
               'Jabatan' => '-',
            ];
         }
      }
      else {
         $bulan = (int) $bulan;
         $header = [
            'Periode Remunerasi' => !empty($bulan) ? DocoHelpers::$_bulan[$bulan].' '.$tahun : ' ',
            'Nama Pegawai' => strtoupper(ArrayHelper::getValue($filter, 'nama_pegawai', '-')),
            'NIK' => ArrayHelper::getValue($filter, 'nik', '-'),
            'Jabatan' => strtoupper(ArrayHelper::getValue($filter, 'jabatan_nama', '-')),
         ];
      }

      $filePath = DocoHelpers::exportExcel('Daftar Pegawai Penerima Remunerasi', $tmpCache, $header, [
         "skipIncrement" => true,
         'customHeader' => $custHeader,
      ], $footer, [], true);

      $filePath->save($path);
      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:' . $this->unique_str,
         'message' => json_encode([
            'status' => 'finish',
            'messageProcess' => 'Proses import excel berhasil',
            'progress' => 90
         ]),
      ]);

      return json_encode([
         'service' => 'Sirs-Remunerasi-ExportExcel',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
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
               'label' => 'Nama Pegawai',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jabatan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jabatan Kode',
               'rowspan' => 2,
            ],
            [
               'label' => 'Grading',
               'rowspan' => 2,
            ],
            [
               'label' => 'Posisi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Target IKI 1',
               'rowspan' => 2,
            ],
            [
               'label' => 'Nominal IKI 1',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite DPJP Utama 1',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite DPJP Utama 2',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite DPJP Utama 3',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite DPJP Tambahan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Join Conference',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite HCU & Isolasi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite DPJP Utama Insentif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite DPJP Tambahan Insentif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Konsul',
               'rowspan' => 2,
            ],
            [
               'label' => 'Visite VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Poli RJ DPJP Langsung',
               'rowspan' => 2,
            ],
            [
               'label' => 'Poli RJ DPJP Supervisi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Poli Eksekutif',
               'rowspan' => 2,
            ],
            [
               'label' => 'MCU',
               'rowspan' => 2,
            ],
            [
               'label' => 'TTD Hasil MCU',
               'rowspan' => 2,
            ],
            [
               'label' => 'Konsul On Call',
               'rowspan' => 2,
            ],
            [
               'label' => 'Onsul On Call Rawat Intensif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik I Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik II Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik III Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik IV Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik V Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VI Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VII Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VIII Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik I Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik II Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik III Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik IV Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik V Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VI Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VII Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VIII Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik I VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik II VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik III VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik IV VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik V VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VI VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VII VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Diagnostik VIII VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Sedang Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Besar Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Canggih Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv I Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv II Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv III Elektif',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Sedang Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Besar Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Canggih Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv I Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv II Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv III Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Sedang VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Besar VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Canggih VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv I VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv II VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'OP Khusus Lv III VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel I',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel II',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel III',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel IV',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel V',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel VI',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel I Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel II Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel III Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel IV Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel V Emergensi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel I VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel II VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel III VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel IV VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jantung Kel V VIP',
               'rowspan' => 2,
            ],
            [
               'label' => 'Ronde Besar',
               'rowspan' => 2,
            ],
            [
               'label' => 'Koord Lap Jaga Bangsal',
               'rowspan' => 2,
            ],
            [
               'label' => 'Rapat Koord Pelayanan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Audit Medik',
               'rowspan' => 2,
            ],
            [
               'label' => 'Penelitian',
               'rowspan' => 2,
            ],
            [
               'label' => 'Presentasi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Rapat Direksi',
               'rowspan' => 2,
            ],
            [
               'label' => 'Kwalitas',
               'rowspan' => 2,
            ],
            [
               'label' => 'Kepatuhan',
               'rowspan' => 2,
            ],
            [
               'label' => 'Jumlah Point',
               'rowspan' => 2,
            ],
            [
               'label' => 'Manajerial',
               'rowspan' => 2,
            ],
            [
               'label' => 'Total Point',
               'rowspan' => 2,
            ],
            [
               'label' => 'IKI',
               'rowspan' => 2,
            ],
            [
               'label' => 'Nominal',
               'rowspan' => 2,
            ],
            [
               'label' => 'Max KMK',
               'rowspan' => 2,
            ],
            [
               'label' => 'Imbal Jasa',
               'rowspan' => 2,
            ],
         ]
      ];
   }
}