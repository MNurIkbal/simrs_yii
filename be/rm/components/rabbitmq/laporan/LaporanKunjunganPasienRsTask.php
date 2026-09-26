<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use GuzzleHttp\Client;
use app\modules\v1\models\LaporanKunjunganPasienRsDiagnosa;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanKunjunganPasienRsTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $filter;
    protected $list_data = [];

    public static $look_exlude = [402,628];
    const DIPERIKSA_PDFTRN = 2;
    const PULANG_PDFTRN = 4;
    const BATAL_PDFTRN = 402;
    const BELUM_PDFTRN = 486;
    const DIPERIKSA_ADMISI = 441;
    const PULANG_ADMISI = 487;
    const BATAL_ADMISI = 453;
    const BELUM_ADMISI = 440;
    public static $look_diperiksa = [self::DIPERIKSA_PDFTRN,self::DIPERIKSA_ADMISI];
    public static $look_pulang = [self::PULANG_PDFTRN,self::PULANG_ADMISI];
    public static $look_batal = [self::BATAL_PDFTRN,self::BATAL_ADMISI];
    public static $look_belum = [self::BELUM_PDFTRN,self::BELUM_ADMISI];


    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttributes();
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
        $this->generateExcel();
        $this->uploadFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                 'status' => 'finish', 
                 'messageProcess' => 'Proses berhasil',
                 'progress' => 100,
                 'filename' => $this->unique_str
             ]),
         ]);
    }

    private function getDataAttributes()
    {
        $jenisIdentitas = $this->getListJenisIdentitas();
        $dataObject = $this->getObjectData()->asArray()->all();
        $row = [];
        $no = 1;
        $prefix = 0;
        foreach ($dataObject as $value) {
            if (!empty($value['diagnosa_penyerta'])) {
                $value['diagnosa_penyerta'] = str_replace('$', ', ', $value['diagnosa_penyerta']);
            }

            $noTlp = isset($value['no_telepon_pasien']) ? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
            $noIdentitas = null;
            if (!empty($value['additional_pasien'])) {
                $pasienAdds = json_decode($value['additional_pasien'], JSON_UNESCAPED_SLASHES);
                if (!empty($pasienAdds) && isset($pasienAdds[0]) && is_array($pasienAdds[0])) {
                    foreach($pasienAdds as $adds) {
                        if (isset($jenisIdentitas[$adds['jenisidentitas']])) {
                            if (!empty($noIdentitas)) {$noIdentitas .= "\n";}
                            $noIdentitas .= $jenisIdentitas[$adds['jenisidentitas']] . ' - ' . $adds['no_identitas_pasien'];
                        }
                    }
                }
            }

            $tmp[1]  = $no;
            $tmp[2]  = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $tmp[3]  = $value['no_pendaftaran'];
            $tmp[4]  = $value['no_rekam_medik'];
            $tmp[5]  = $value['jenis_kelamin'];
            $tmp[6]  = $value['nama_pasien'];
            $tmp[7]  = $value['tanggal_lahir'];
            $tmp[8]  = $value['umur'];
            $tmp[9]  = $value['alamat_pasien'];
            $tmp[10] = !empty($noIdentitas) ? $noIdentitas : '-';
            $tmp[11] = $value['carabayar_nama'];
            $tmp[12] = $value['penjamin_nama'];
            $tmp[13] = $value['kelaspelayanan_nama'];
            $tmp[14]  = $value['jeniskasuspenyakit_nama'];
            $tmp[15]  = $value['instalasi_nama'];
            $tmp[16]  = $value['ruangan_nama'];
            $tmp[17]  = $value['dokterdpjp_nama'];

            if (!empty($value['diagnosa_penyerta'])) {
                $value['diagnosa_penyerta'] = str_replace('$', ', ', $value['diagnosa_penyerta']);
            }

            $tmp[18] = $value['diagnosa_utama'];
            $tmp[19] = $value['diagnosa_penyerta'];
            $tmp[20] = $value['status_periksa'];
            $tmp[21] = isset($value['no_telepon_pasien'])? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
            $tmp[22] = $value['kondisikeluar_nama'];
            $tmp[23] = $value['carakeluar_nama'];
            $tmp[24] = $value['kunjungan_nama'];
            $tmp[25] = isset($value['asalrujukan_nama']) ? $value['asalrujukan_nama'] : null;
            $tmp[26] = isset($value['rujukandari_nama']) ? $value['rujukandari_nama'] : null;
            $tmp[27] = isset($value['nama_perujuk']) ? $value['nama_perujuk'] : null;

            $row[] = $tmp;
            $prefix++;
            $no++;
        }

        return $row;
    }

    private function getObjectData()
    {
        $request = $this->filter;
        
        $model   = new LaporanKunjunganPasienRsDiagnosa();
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($request['advanced-filter'])) {
            if(isset($request['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $request['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($request['advanced-filter']['tgl_pendaftaran']);
            }

            if(isset($request['advanced-filter']['jenis_kelamin'])) {
                $request['advanced-filter']['jeniskelamin'] = $request['advanced-filter']['jenis_kelamin'];
                unset($request['advanced-filter']['jenis_kelamin']);
            }

            if(isset($request['advanced-filter']['diagnosa_utama'])) {
                $icdUtama = $request['advanced-filter']['diagnosa_utama'];
                unset($request['advanced-filter']['diagnosa_utama']);
                $query->andWhere(['diagnosa_utama_id' => $icdUtama]);
            }

            if(isset($request['advanced-filter']['diagnosa_penyerta'])) {
                $icdPenyerta = $request['advanced-filter']['diagnosa_penyerta'];
                unset($request['advanced-filter']['diagnosa_penyerta']);
                $query->andWhere(['ilike', 'diagnosa_penyerta_id', $icdPenyerta]);
            }

            if(isset($_GET['advanced-filter']['kelaspelayanan_id'])) {
                $kelasPelayanan_id = $_GET['advanced-filter']['kelaspelayanan_id'];
                unset($_GET['advanced-filter']['kelaspelayanan_id']);
                $query->andWhere(['kelaspelayanan_id' => $kelasPelayanan_id]);
            }

            if(isset($request['advanced-filter']['status_periksa'])) {
                $status_periksa = $request['advanced-filter']['status_periksa'];
                unset($request['advanced-filter']['status_periksa']);
                switch ($status_periksa) {
                    case self::DIPERIKSA_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::PULANG_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::BATAL_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::BELUM_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    default:
                        $query->andWhere(['id_status_periksa' => $status_periksa]);
                        break;
                }
            }

            if(isset($request['advanced-filter']['no_identitas'])) {
                $noIdentitas = $request['advanced-filter']['no_identitas'];
                unset($request['advanced-filter']['no_identitas']);
                $query->andWhere(['ilike', 'additional_pasien', $noIdentitas]);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->andWhere([
            'NOT IN', 'id_status_periksa', [
                DocoConstants::STATUS_PERIKSA_BTL_KUNJ,
                DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
                DocoConstants::STATUS_PERIKSA_BTL_KONSUL,
                DocoConstants::STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP,
                DocoConstants::STATUS_RANAP_BATAL_RAWAT
            ]
        ]);

        return DocoRestActiveFilter::advancedFilter($model, $query, $request);
    }

    protected function getListJenisIdentitas()
    {
        $jenisIds = [];
        $query = Lookup::find()->select([
            'lookup_id', 'lookup_name', 'lookup_value'
        ])->where([
            'lookup_type' => 'jenis_identitas'
        ])->orderBy(['lookup_urutan' => SORT_ASC])->all();

        if (!empty($query)) {
            foreach ($query as $model) {
                $jenisIds[$model->lookup_id] = $model->lookup_value;
            }
        }
        return $jenisIds;
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
        $filter = $this->filter;

        $row = $footer = [];
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data kunjungan',
                'progress' => 80
            ]),
        ]);
        $data = $this->getDataAttributes();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $row[] = $value;
            }
        }

        $start   = date('Y-m-d');
        $end     = date('Y-m-d');

        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pendaftaran']);
            }
        }

        $header = [
            'Tanggal Pendaftaran'     => $start.' Sampai Dengan '.$end,
            // 'No Rekam Medik'          => $no_rekam_medik,
            // 'Nama Pasien'             => $nama_pasien,
            // 'Jenis Kelamin'           => $jenis_kelamin,
            // 'Cara Bayar'              => $carabayar_nama,
            // 'Penjamin'                => $penjamin_nama,
            // 'Jenis Kasus Penyakit'    => $jeniskasuspenyakit_nama,
            // 'Instalasi'               => $instalasi_nama,
            // 'Ruangan'                 => $ruangan_nama,
            // 'Dokter Penanggung Jawab' => $nama_pegawai,
        ];

        $custHeader = [
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
                    'label'=>'No Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Rekam Medik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Kelamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Lahir',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Umur',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Alamat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Identitas',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara bayar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Penjamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kelas Pelayanan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Kasus Penyakit',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Instalasi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Dokter DPJP',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diagnosa Utama',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Diagnosa Penyerta',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status Periksa',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Telepon Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kondisi Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Pulang',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status Kunjungan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Asal Rujukan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Rujukan Dari',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Perujuk',
                    'rowspan'=>2,
                ],
            ]
        ];

        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Pasien Rumah Sakit', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);

        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrlRm();
        try {
            $response = $client->post('lap-kunjungan/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Kunjungan Pasien Rumah Sakit.xlsx'
                    ],
                ]
            ]);
            return json_decode($response->getBody(),true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if($e->hasResponse()) {
                $response = $e->getResponse();
                return $response->getBody();
            }
        }
    }

    private function setUrlRm()
    {
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->owner,
        ];

        $client =  new Client([
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);

        return $client;
    }
}