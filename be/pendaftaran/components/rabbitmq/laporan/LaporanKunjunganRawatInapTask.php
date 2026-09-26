<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use app\modules\v1\models\LapKunjunganRawatInap;
use app\modules\v1\models\LapKunjunganRawatInapFn;
use Integrasi\Components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanKunjunganRawatInapTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $customData;
    protected $footer;
    protected $title;
    protected $totalPerPage;
    protected $countData;

    protected $list_data = [];

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttibutes();
        
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
    }

    /** proses export */
    protected function prosesExport()
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang menyiapkan file excel.',
                'progress' => 80
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel.',
                'progress' => 85
            ]),
        ]);

        $options = [
            "skipIncrement" => true,
            "customHeader" => $this->custHeader()
        ];

        $filePath = DocoHelpers::exportExcel($this->title, $this->list_data, $this->headerExcel, $options, [], [], true);
        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $filePath->save($path);
        
        // $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        if (file_exists($path)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:'.$this->unique_str,
                'message' => json_encode([
                     'status' => 'finish', 
                     'messageProcess' => 'Proses berhasil.',
                     'progress' => 100,
                     'filename' => $this->unique_str
                 ]),
            ]);
            
        }
    }

    private function getDataAttibutes()
    {
        try {
            $data =  $this->getDataLaporanExcel();
            Yii::error($data);
            
            $result = [];
            $no = 1;
            foreach($data as $key => $value) {
                $newData['No'] = $no++;
                $tgl_pendaftaran = !empty(ArrayHelper::getValue($value,'tgl_pendaftaran')) ? date('d M Y H:i:s', strtotime(ArrayHelper::getValue($value,'tgl_pendaftaran'))) : '-';
                $alamat_pasien = !empty(ArrayHelper::getValue($value,'alamat_pasien')) ? ArrayHelper::getValue($value,'alamat_pasien') : '-';
                $nosep = !empty(ArrayHelper::getValue($value,'nosep')) ? ArrayHelper::getValue($value,'nosep') : '-';
                $no_pendaftaran = !empty(ArrayHelper::getValue($value,'no_pendaftaran')) ? ArrayHelper::getValue($value,'no_pendaftaran') : '-';
                $no_rekam_medik = !empty(ArrayHelper::getValue($value,'no_rekam_medik')) ? ArrayHelper::getValue($value,'no_rekam_medik') : '-';
                $nama_pasien = !empty(ArrayHelper::getValue($value,'nama_pasien')) ? ArrayHelper::getValue($value,'nama_pasien') : '-';
                $info_kunjungan = $no_pendaftaran .' - '.$no_rekam_medik.' - '.$nama_pasien;
                $status_pasien = !empty(ArrayHelper::getValue($value,'status_pasien')) ? ArrayHelper::getValue($value,'status_pasien') : '-';
                $jenis_kelamin = !empty(ArrayHelper::getValue($value,'jenis_kelamin')) ? ArrayHelper::getValue($value,'jenis_kelamin') : '-';
                $umur = !empty(ArrayHelper::getValue($value,'umur')) ? ArrayHelper::getValue($value,'umur') : '-';
                $golonganumur_nama = !empty(ArrayHelper::getValue($value,'golonganumur_nama')) ? ArrayHelper::getValue($value,'golonganumur_nama') : '-';
                $agama = !empty(ArrayHelper::getValue($value,'agama')) ? ArrayHelper::getValue($value,'agama') : null;
                $statusperkawinan = !empty(ArrayHelper::getValue($value,'statusperkawinan')) ? ArrayHelper::getValue($value,'statusperkawinan') : '';
                $pekerjaan = !empty(ArrayHelper::getValue($value,'pekerjaan')) ? ArrayHelper::getValue($value,'pekerjaan') : '-';
                $kabupaten_nama = !empty(ArrayHelper::getValue($value,'kabupaten_nama')) ? ArrayHelper::getValue($value,'kabupaten_nama') : '';
                $kunjungan = !empty(ArrayHelper::getValue($value,'kunjungan')) ? ArrayHelper::getValue($value,'kunjungan') : '';
                $jeniskasuspenyakit_nama = !empty(ArrayHelper::getValue($value,'jeniskasuspenyakit_nama')) ? ArrayHelper::getValue($value,'jeniskasuspenyakit_nama') : '';
                $penjamin_nama = !empty(ArrayHelper::getValue($value,'penjamin_nama')) ? ArrayHelper::getValue($value,'penjamin_nama') : '';
                $carabayar_nama = !empty(ArrayHelper::getValue($value,'carabayar_nama')) ? ArrayHelper::getValue($value,'carabayar_nama') : '';
                $carabayar_penjamin = $carabayar_nama . ' / ' . $penjamin_nama;
                $nama_perujuk = !empty(ArrayHelper::getValue($value,'nama_perujuk')) ? ArrayHelper::getValue($value,'nama_perujuk' ): '-';
                $ruangan_nama = !empty(ArrayHelper::getValue($value,'ruangan_nama')) ? ArrayHelper::getValue($value,'ruangan_nama') : '';
                $kamar_bed = ArrayHelper::getValue($value,'kamarruangan_nokamar').' / '.ArrayHelper::getValue($value,'no_tempattidur');
                $nama_pegawai = !empty(ArrayHelper::getValue($value,'nama_pegawai')) ? ArrayHelper::getValue($value,'nama_pegawai') : '';
    
                $status_titipan = ' - ';
                if($value['carabayar_id'] == 6){
                    $status_titipan = ' - ';
                } else if (!empty(ArrayHelper::getValue($value,'is_pasientitipan_pk'))) {
                    if(ArrayHelper::getValue($value,'is_pasientitipan_pk') == true && ArrayHelper::getValue($value,'is_stoppasientitipan') == false){
                        $status_titipan = ArrayHelper::getValue($value,'kelas_ditagihkan_nama');
                    }
                } else if (empty(ArrayHelper::getValue($value,'is_pasientitipan_pk'))) {
                    if($value['is_pasientitipan'] == true && ArrayHelper::getValue($value,'is_stoppasientitipan') == false){
                        $status_titipan = ArrayHelper::getValue($value,'kelas_ditagihkan_nama');
                    }
                }
                $kelaspelayanan_nama =  ArrayHelper::getValue($value,'kelaspelayanan_nama').' / '.$status_titipan;
    
                $status_ranap_nama = !empty(ArrayHelper::getValue($value,'status_ranap_nama')) ? ArrayHelper::getValue($value,'status_ranap_nama') : '';
                $carakeluar_nama = !empty(ArrayHelper::getValue($value,'carakeluar_nama')) ? ArrayHelper::getValue($value,'carakeluar_nama') : '';
                $diagnosa = !empty(ArrayHelper::getValue($value,'diagnosa')) ? ArrayHelper::getValue($value,'diagnosa') : '';
                $tgl_keluar = !empty(ArrayHelper::getValue($value,'tgl_keluar')) ? date('d M Y', strtotime(ArrayHelper::getValue($value,'tgl_keluar'))) : '';

                $newData['Tanggal Pendaftaran'] = $tgl_pendaftaran;
                $newData['Info Kunjungan'] = $info_kunjungan;
                $newData['Status Pasien'] = $status_pasien;
                $newData['Jenis Kelamin'] = $jenis_kelamin;
                $newData['Umur'] = $umur;
                $newData['Golongan Umur'] = $golonganumur_nama;
                $newData['Agama'] = $agama;
                $newData['Status Perkawinan'] = $statusperkawinan;
                $newData['Pekerjaan'] = $pekerjaan;
                $newData['Alamat'] = $alamat_pasien;
                $newData['Kota/Kab'] = $kabupaten_nama;
                $newData['Kunjungan'] = $kunjungan;
                $newData['Jenis Kasus Penyakit'] = $jeniskasuspenyakit_nama;
                $newData['Cara Bayar / Penjamin'] = $carabayar_nama;
                $newData['Rujukan'] = $nama_perujuk;
                $newData['Ruangan'] = $ruangan_nama;
                $newData['Kamar - Bed'] = $kamar_bed;
                $newData['Dokter'] = $nama_pegawai;
                $newData['Kelas Pelayanan / Kelas Tagihan'] = $kelaspelayanan_nama;
                $newData['Status Periksa'] = $status_ranap_nama;
                $newData['Status Pulang'] = $carakeluar_nama;
                $newData['No SEP'] = $nosep;
                $newData['Diagnosa'] = $diagnosa;
                $newData['Tanggal Keluar'] = $tgl_keluar;
                $result[] = $newData;
            }
            
            return $result;
            
		} catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    public function getDataLaporanExcel()
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $getData = $this->filter;
        
        $params = isset($getData['params']) ? $getData['params'] : [];
        $advancedFilters = [];

        if(isset($getData['advanced-filter'])) {
            $advancedFilters = $getData['advanced-filter'];
        }

        if(isset($params['advanced-filter'])) {
            $advancedFilters = $params['advanced-filter'];
        }

        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($advancedFilters['tgl_pendaftaran']);
        }

        $model = new LapKunjunganRawatInapFn;
        $query = $model::getData($start, $end)->select([
            'tgl_pendaftaran',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pasien',
            'jenis_kelamin',
            'umur',
            'golonganumur_id',
            'golonganumur_nama',
            'agama',
            'statusperkawinan',
            'pekerjaan_nama',
            'alamat_pasien',
            'kabupaten_nama',
            'kunjungan',
            'jeniskasuspenyakit_nama',
            'carabayar_id',
            'carabayar_nama',
            'penjamin_nama',
            'nama_perujuk',
            'ruangan_nama',
            'kamarruangan_nokamar',
            'no_tempattidur',
            'nama_pegawai',
            'kelaspelayanan_nama',
            'kelas_ditagihkan_nama',
            'status_ranap_nama',
            'carakeluar_nama',
            'diagnosa',
            'tgl_keluar',
            'is_pasientitipan',
            'is_pasientitipan_pk',
            'nosep',
            'status_pasien'
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query, $getData);
        return $query->asArray()->all();
    }

    private function custHeader() 
    {	
        return [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Pendaftaran',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Info Kunjungan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Status Pasien',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Kelamin',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Umur',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Golongan Umur',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Agama',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=> 'Status Perkawinan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Pekerjaan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Alamat',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=> 'Kota/Kab',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Kunjungan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Kasus Penakit',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Cara Bayar / Penjamin',
                        'rowspan'=>2,
                    ],
                    [
                        'label' => 'Rujukan',
                        'rowspan' => 2
                     ],
                    [
                        'label'=>'Ruangan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Kamar - Bed',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Dokter',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Kelas Pelayanan / Kelas Tagihan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Status Periksa',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Status Pulang',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No SEP',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Diagnosa',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Keluar',
                        'rowspan'=>2,
                    ],
                ]
        ];
    }

    private function generateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:W1');
        $sheet->setCellValue("A1", $this->title);
        $row = 2;
        foreach ($this->headerExcel as $key => $val) {
            $sheet->mergeCells("A{$row}:W{$row}");
            $sheet->setCellValue("A{$row}", $key . " : {$val}");
            $row++;
        }

        $styleTitle = array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        $sheet->getStyle('A1:W1')->applyFromArray($styleTitle);

        if (isset($this->list_data[0])) {
            $headers = array_keys($this->list_data[0]); // get headers from source array
            foreach ($headers as $key => $str) {
                $headers[$key] = str_replace('_', ' ', $str);
            }
            array_unshift($this->list_data, array_map('ucwords', $headers)); 
        }

        $timer = microtime(true);
        $sheet->fromArray(
            $this->list_data,
            null,
            'A7'
        );
        
        $writer = new Xlsx($spreadsheet);
        $filePath = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $writer->save($filePath);
        $foo = 'elapsed time: '. round(microtime(true) - $timer, 3). ' sec';
        
    }
}
