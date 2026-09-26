<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use app\modules\v1\models\KonfigLaporan;
use app\modules\v1\models\LaporanSensusHarianRjV;
use app\modules\v1\models\LaporanSensusHarianPasienRajalFn;
use app\modules\v1\models\Lookup;
use Doco\components\DocoHelpers;

class LaporanSensusHarianRajalTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $title;
    protected $totalPerPage;
    protected $countData;
    protected $manual_excel;
    protected $list_data = [];

    const JK = 'jenis_kelamin';
    const JP = 'jenis_pendaftaran';
    const TITLE = 'Laporan Sensus Harian Pasien Rawat Jalan';
    const POLIKLINIK = 722;
    const PMED = 725;
    const IGD = 724;
    const MCU = 723;
    const RUANGAN_NAMA = 'ruangan_nama';
    const BARU_JML = 'baru_jml';
    const LAMA_JML = 'lama_jml';
    const JML_BARU = 'jml_baru';
    const JML_LAMA = 'jml_lama';
    const KUNJUNGAN = 'kunj';
    const HP = 'hp';
    const ADOA = 'adoa';
    const ADOAD = 'adoad';
    const ADOAPP = 'adoapp';
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const JENIS_PENDAFTARAN = 'jenis_pendaftaran';
    const TOTAL = 'TOTAL';
    const JENIS_RUANGAN = 'jenis_ruangan';
    const IS_HEADER = 'is_header';
    const SUBTOT = 'subtotal';
    const _LAMA_LAKI = '_LAMA_LAKI';
    const _LAMA_PEREMPUAN = '_LAMA_PEREMPUAN';
    const _BARU_LAKI = '_BARU_LAKI';
    const _BARU_PEREMPUAN = '_BARU_PEREMPUAN';
    const LAMA_LAKI = 'lama_laki';
    const LAMA_PEREMPUAN = 'lama_perempuan';
    const BARU_LAKI = 'baru_laki';
    const BARU_PEREMPUAN = 'baru_perempuan';
    const JENIS_RUANGAN_NAMA = 'jenis_ruangan_nama';
    const STRTOLOWER = 'strtolower';
    const STRTOUPPER = 'strtoupper';
    const UCFIRST = 'ucfirst';
    const UCWORD = 'ucword';
    const VAR_IGD_ID = '2';

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttibutes($this->filter);
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

        $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        $this->uploadFile();

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                 'status' => 'finish', 
                 'messageProcess' => 'Proses berhasil.',
                 'progress' => 100,
                 'filename' => $this->unique_str,
                 'jenis_laporan' => $this->filter['jenis_laporan'],
                 'tgl_pendaftaran' => $this->filter['tgl_pendaftaran']
             ]),
         ]);
    }

    private function getDataAttibutes()
    {
        try {
            $range_tanggal = ArrayHelper::getValue($this->filter, 'tgl_pendaftaran');
            $jenis_laporan = ArrayHelper::getValue($this->filter, 'jenis_laporan');
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:59');

            if ($range_tanggal) {
                $explode = explode(" - ", $range_tanggal);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }

            $countDay = self::countHp($start,$end);
            $data = $this->generateData($start,$end, $jenis_laporan, $countDay);

            return $data;
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    private function generateExcel()
    {
        ini_set('memory_limit', '-1');
		set_time_limit(0);
        
        $row = [];
        $footer = [];

        $range_tanggal = ArrayHelper::getValue($this->filter, 'tgl_pendaftaran');
        $jenis_laporan = ArrayHelper::getValue($this->filter, 'jenis_laporan');
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if ($range_tanggal) {
            $explode = explode(" - ", $range_tanggal);
            if (count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }

        $countDay = self::countHp($start,$end);
        $data = $this->generateData($start,$end, $jenis_laporan, $countDay);
        $dataHeader = $this->generateHeaderColumns($data['carabayar'], $data['carabayarList']);

        foreach($data['data'] as $idx => $columns) {
            $cols = []; $ctr = 0;
            foreach($dataHeader['header'] as $key => $label) {
                if (in_array($label, [self::TGL_PENDAFTARAN, self::JENIS_PENDAFTARAN, self::IS_HEADER])) {
                    continue;
                } else {
                    $cols[$ctr+1] = $columns[$label];
                    $ctr++;
                }
            }
            $row[] = $cols;
        }

        $profil = $this->getProfil();
        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
        ];
        $custHeader = $this->custHeader($data, $dataHeader);
        $path = 'web/'.'uploads/'. $this->unique_str .'.xlsx';

        $filePath = DocoHelpers::exportExcel('Laporan Sensus Harian Pasien Rawat Jalan', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ], $footer, [], true);
        $filePath->save($path);

        return json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private static function countHp($start, $end)
    {
        $dateOne = new \DateTime($start);
        $dateTwo = new \DateTime($end);
        $countDay = ($dateTwo->diff($dateOne)->days) + 1;

        return $countDay;
    }

    private function generateData($start = null, $end = null, $jenis = null, $countDay = 1)
    {
        $data = [];
        $tmp  = [];
        $result = [];
        $carabayarList = [];
        $carabayar = [];

        $jenisRuangan = $this->getHeader(true);

        $data = LaporanSensusHarianPasienRajalFn::getData($start, $end, $jenis);

        foreach($data as $key => $value) {
            $tmp[$value['ruangan_id']][$value['carabayar_id']] = $value;
            $carabayar[$value['carabayar_nama']] = null;
        }

        $tmpData = [];
        foreach($tmp as $k => $v) {
            foreach($v as $kk => $vv) {
                $ruanganId = $vv['ruangan_id'];
                $instalasi = $vv['instalasi_id'];
                $caraBayarName = str_replace(' ', '_', $vv['carabayar_nama']);

                if(isset($tmpData[$ruanganId])) {
                    $tmpData[$ruanganId][self::HP] = $vv[self::HP] ;
                    $tmpData[$ruanganId][self::JML_BARU] += $vv[self::JML_BARU] ;
                    $tmpData[$ruanganId][self::JML_LAMA] += $vv[self::JML_LAMA] ;
                    $tmpData[$ruanganId][self::KUNJUNGAN] += $vv['kunjungan'];
                } else {
                    $tmpData[$ruanganId]['no'] = null;
                    $tmpData[$ruanganId][self::RUANGAN_NAMA] = $vv[self::RUANGAN_NAMA];
                    $tmpData[$ruanganId][self::JENIS_RUANGAN] = $vv[self::JENIS_RUANGAN];
                    $tmpData[$ruanganId][self::JML_BARU] = $vv[self::JML_BARU];
                    $tmpData[$ruanganId][self::KUNJUNGAN] = $vv['kunjungan'];
                    $tmpData[$ruanganId][self::JML_LAMA] = $vv[self::JML_LAMA];
                    $tmpData[$ruanganId][self::ADOA] = 0;
                    $tmpData[$ruanganId][self::ADOAD] = 0;
                    $tmpData[$ruanganId][self::ADOAPP] = 0;
                    $tmpData[$ruanganId][self::HP] = 0; 
                    $tmpData[$ruanganId][self::IS_HEADER] = 1; 
                    $tmpData[$ruanganId][self::JENIS_PENDAFTARAN] = null;
                    $tmpData[$ruanganId][self::TGL_PENDAFTARAN] = null; 
                }

                if($instalasi == self::VAR_IGD_ID) {
                    $tmpData[$ruanganId][self::HP] = $countDay;
                }

                if(isset($tmpData[self::TOTAL])) {
                    $tmpData[self::TOTAL][self::JML_BARU] += $vv[self::JML_BARU] ;
                    $tmpData[self::TOTAL][self::JML_LAMA] += $vv[self::JML_LAMA] ;
                    $tmpData[self::TOTAL][self::KUNJUNGAN] += $vv['kunjungan'];
                } else {
                    $tmpData[self::TOTAL]['no'] = null;
                    $tmpData[self::TOTAL][self::RUANGAN_NAMA] = self::TOTAL;
                    $tmpData[self::TOTAL][self::JENIS_RUANGAN] = 99999;
                    $tmpData[self::TOTAL][self::JML_BARU] = $vv[self::JML_BARU];
                    $tmpData[self::TOTAL][self::KUNJUNGAN] = $vv['kunjungan'];
                    $tmpData[self::TOTAL][self::JML_LAMA] = $vv[self::JML_LAMA];
                    $tmpData[self::TOTAL][self::ADOA] = 0;
                    $tmpData[self::TOTAL][self::ADOAD] = 0;
                    $tmpData[self::TOTAL][self::ADOAPP] = 0;
                    $tmpData[self::TOTAL][self::HP] = $countDay; 
                    $tmpData[self::TOTAL][self::IS_HEADER] = 2; 
                    $tmpData[self::TOTAL][self::JENIS_PENDAFTARAN] = null;
                    $tmpData[self::TOTAL][self::TGL_PENDAFTARAN] = null; 
                }

                if(isset($tmpData[$ruanganId][$caraBayarName.self::_BARU_LAKI])) {
                    $tmpData[$ruanganId][$caraBayarName.self::_BARU_LAKI] += $vv[self::BARU_LAKI];
                }else{
                    $tmpData[$ruanganId][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                }

                if(isset($tmpData[$ruanganId][$caraBayarName.self::_BARU_PEREMPUAN])) {
                    $tmpData[$ruanganId][$caraBayarName.self::_BARU_PEREMPUAN] += $vv[self::BARU_PEREMPUAN];
                }else{
                    $tmpData[$ruanganId][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                }
                
                if(isset($tmpData[$ruanganId][$caraBayarName.self::_LAMA_LAKI])) {
                    $tmpData[$ruanganId][$caraBayarName.self::_LAMA_LAKI] += $vv[self::LAMA_LAKI];
                }else{
                    $tmpData[$ruanganId][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                }

                if(isset($tmpData[$ruanganId][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                    $tmpData[$ruanganId][$caraBayarName.self::_LAMA_PEREMPUAN] += $vv[self::LAMA_PEREMPUAN];
                }else{
                    $tmpData[$ruanganId][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                }

                if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_BARU_LAKI])) {
                    $tmpData[self::TOTAL][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                } else {
                    $tmpData[self::TOTAL][$caraBayarName.self::_BARU_LAKI] += $vv[self::BARU_LAKI];
                }

                if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_BARU_PEREMPUAN])) {
                    $tmpData[self::TOTAL][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                } else {
                    $tmpData[self::TOTAL][$caraBayarName.self::_BARU_PEREMPUAN] += $vv[self::BARU_PEREMPUAN];
                }

                if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_LAMA_LAKI])) {
                    $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                } else {
                    $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_LAKI] += $vv[self::LAMA_LAKI];
                }

                if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                    $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                } else {
                    $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_PEREMPUAN] += $vv[self::LAMA_PEREMPUAN];
                }

                if(!isset($carabayarList[$caraBayarName.self::_BARU_LAKI])) {
                    $carabayarList[$caraBayarName.self::_BARU_LAKI] = null;
                }

                if(!isset($carabayarList[$caraBayarName.self::_BARU_PEREMPUAN])) {
                    $carabayarList[$caraBayarName.self::_BARU_PEREMPUAN] = null;
                }

                if(!isset($carabayarList[$caraBayarName.self::_LAMA_LAKI])) {
                    $carabayarList[$caraBayarName.self::_LAMA_LAKI] = null;
                }

                if(!isset($carabayarList[$caraBayarName.self::_LAMA_PEREMPUAN])) {
                    $carabayarList[$caraBayarName.self::_LAMA_PEREMPUAN] = null;
                }

                foreach($jenisRuangan as $key => $value) { 
                    if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]])) {
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]]['no'] = null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::RUANGAN_NAMA] =  $value[self::JENIS_RUANGAN_NAMA];
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_RUANGAN] =  (int) $value['jenis_ruangan'];
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::HP] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::ADOA] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::ADOAD] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::ADOAPP] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::IS_HEADER] = 2;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_PENDAFTARAN] =  null;
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::TGL_PENDAFTARAN] =  null;
                    }

                    if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI])) {
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] = null;
                    }
    
                    if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN])) {
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] = null;
                    }
    
                    if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI])) {
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] = null;
                    }
    
                    if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                        $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] = null;
                    }

                    if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]])) {
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]]['no'] = null;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::RUANGAN_NAMA] = ucfirst(self::SUBTOT).' '. $value[self::JENIS_RUANGAN_NAMA];
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_PENDAFTARAN] = null;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] = 0;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] = null;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::HP] = $countDay;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] = 0;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::ADOA] = 0;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::ADOAD] = 0;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::ADOAPP] = 0;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::IS_HEADER] = 0;
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_RUANGAN] = (int) $value['jenis_ruangan'];
                        $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::TGL_PENDAFTARAN] = null;

                        if($value['jenis_ruangan'] == $vv['jenis_ruangan']) {
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] += $vv[self::JML_BARU];
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] += $vv[self::JML_LAMA];
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] += $vv['kunjungan'];
    
                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                            }

                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                            }
            
                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                            } 

                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                            }     
                        }
                    } else {
                        if($value['jenis_ruangan'] == $vv['jenis_ruangan']) {
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] += $vv[self::JML_BARU];
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] += $vv[self::JML_LAMA];
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] += $vv['kunjungan'];
    
                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                            } else {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] += $vv[self::BARU_LAKI];
                            }
            
                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                            } else {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] += $vv[self::BARU_PEREMPUAN];
                            }
            
                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                            } else {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] += $vv[self::LAMA_LAKI];
                            }
            
                            if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                            } else {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] += $vv[self::LAMA_PEREMPUAN];
                            }        
                        }
                    } 
                }
            }
        }

        ArrayHelper::multisort($tmpData, [self::JENIS_RUANGAN, self::IS_HEADER, self::RUANGAN_NAMA], [SORT_ASC, SORT_DESC, SORT_ASC]);
        
        $no = 0;
        foreach($tmpData as $key => $value) {
            $baru = $value[self::JML_BARU];
            $lama = $value[self::JML_LAMA];
            if($value[self::KUNJUNGAN] != 0 && $value[self::HP] != 0) {
                $value[self::ADOA] = number_format($value[self::KUNJUNGAN] / $value[self::HP],2);
            }

            if($baru != 0 && $value[self::HP] != 0) {
                $value[self::ADOAD] = number_format($baru / $value[self::HP], 2);
            }

            if($baru != 0 && $lama != 0) {
                $value[self::ADOAPP] = number_format(($baru + $lama) / $baru, 2);
            }

            if($value[self::IS_HEADER] == 1) {
                $no++;
                $value['no'] = $no;
            } else {
                if($value[self::IS_HEADER] == 2 || $value[self::IS_HEADER] == 0) {
                    $value[self::RUANGAN_NAMA] = $value[self::RUANGAN_NAMA];
                }
                $value['no'] = '';
            }
            unset($value[self::JENIS_RUANGAN]);
            $result[] = $value;
        }

        return [
            'data' => $result,
            'carabayarList' => $carabayarList,
            'carabayar' => $carabayar
        ];
    }

    private function getHeader($isGroup = false)
    {
        $data = [];
        $model   = new LaporanSensusHarianRjV;
        if($isGroup) {
            $query   = $model::find()->select(['jenis_ruangan', 'jenis_ruangan_nama'])->all();
        } else{
            $query   = $model::find()->all();
        }
            
        foreach($query as $k => $v) {
            if($isGroup) {
                $data[$v['jenis_ruangan']] = $v;
            } else {
                $data[$v['ruangan_id']] = $v;
            }
        }

        return $data;
    }

    private function generateHeaderColumns($carabayar, $carabayarList) 
    {
        $header  = [];
        $columns = [];
        $tmpCarabayar = [];
        $tmpBaru = [];
        $tmpLama = [];
        $tmpHeader = [];
        $newCabar  = [];

        $staticHeaderAwal = [
            'no' => 'no',
            self::RUANGAN_NAMA => self::RUANGAN_NAMA
        ];

        $staticHeaderTengah = [
            self::JML_BARU => self::JML_BARU
        ];

        $staticHeaderAkhir = [
            self::JML_LAMA => self::JML_LAMA,
            self::KUNJUNGAN =>self::KUNJUNGAN,
            self::HP =>self::HP,
            self::ADOA =>self::ADOA,
            self::ADOAD =>self::ADOAD,
            self::ADOAPP =>self::ADOAPP,
            self::JENIS_PENDAFTARAN => self::JENIS_PENDAFTARAN,
            self::TGL_PENDAFTARAN => self::TGL_PENDAFTARAN,
        ];
        
        foreach($carabayarList as $k => $v) {
            $nameLaki = strtolower(substr($k, -9));
            $nameCewe = strtolower(substr($k, -14));
            if($nameLaki == self::BARU_LAKI || $nameCewe == self::BARU_PEREMPUAN) {
                $tmpBaru[$k] = $k;
            } else {
                $tmpLama[$k] = $k;
            }
        }

        $tmpHeader = array_merge($staticHeaderAwal, $tmpBaru);
        $tmpHeader = array_merge($tmpHeader, $staticHeaderTengah);
        $tmpHeader = array_merge($tmpHeader, $tmpLama);
        $tmpHeader = array_merge($tmpHeader, $staticHeaderAkhir);

        foreach($tmpHeader as $k => $v) {
            $visible = true;
            $search = false;
            $title = $k;
            $nameLaki = strtolower(substr($k, -9));
            $nameCewe = strtolower(substr($k, -14));

            if($k == self::JENIS_PENDAFTARAN || $k == self::TGL_PENDAFTARAN) {
                $visible = false;
                $search = true;
            }

            if($nameLaki == self::BARU_LAKI || $nameLaki == self::LAMA_LAKI) {
                $title = 'L';
            }

            if($nameCewe == self::BARU_PEREMPUAN || $nameCewe == self::LAMA_PEREMPUAN) {
                $title = 'P';
            }

            if($k == self::IS_HEADER) {
                $visible = false;
                $search = false;
            }

            $columns[] =[
                'title' => self::formatToReadable($title, self::STRTOUPPER),
                'data' => $k,
                'searchable' => $search,
                'orderable' => false,
                'visible' => $visible,
            ];
        }

        foreach($carabayar as $kei => $val) {
            $newCabar[] = $kei;
        }

        return [
            'header' => $tmpHeader,
            'columns' => $columns,
            'carabayar' => $newCabar,
        ];
    }

    private static function formatToReadable($string , $format = null)
    {
        switch ($string) {
            case self::RUANGAN_NAMA:
                $string = 'Department';
                break;
            case self::JML_BARU:
                $string = 'Jumlah Baru';
                break;
            case self::JML_LAMA:
                $string = 'Jumlah Lama';
                break;
            case self::KUNJUNGAN:
                $string = 'Kunjungan';
                break;
            case self::TGL_PENDAFTARAN:
                $string = 'Tanggal Pendaftaran';
                break;
            case self::JENIS_PENDAFTARAN:
                $string = 'Jenis Pendaftaran';
                break;
            default:
                $string = $string;
                break;
        }

        switch ($format) {
            case self::UCFIRST:
                $string = ucfirst($string);
                break;
            case self::UCWORD:
                $string = ucwords($string);
                break;
            case self::STRTOUPPER:
                $string = strtoupper($string);
                break;
            case self::STRTOLOWER:
                $string = strtolower($string);
                break;
            default:
                $string = $string;
                break;
        }

        return $string;
    }

    private function custHeader($dataHeader)
    {
        $jk = $this->getLookupByType(self::JK)->all();
        $countCarabayar = count($dataHeader['carabayar']);
        $colspanCounter = $countCarabayar * 2;
        $coljumlahpasien = ($colspanCounter * 2) + 2;
        $staticFirstRow = [
            [
                'label' => 'NO',
                'rowspan' => 4
            ],
            [
                'label' => 'DEPARTMENT',
                'rowspan' => 4
            ],
            [
                'label' => 'JUMLAH PASIEN',
                'colspan' => $coljumlahpasien
            ],
            [
                'label' => 'JUMLAH',
                'colspan' => 2
            ],
            [
                'label' => strtoupper(self::ADOA),
                'rowspan' => 4
            ],
            [
                'label' => strtoupper(self::ADOAD),
                'rowspan' => 4
            ],
            [
                'label' => strtoupper(self::ADOAPP),
                'rowspan' => 4
            ],
        ];

        $staticSecondRow = [
            [
                'label' => 'BARU',
                'colspan' => $colspanCounter,
                'startfrom' => 3
            ],
            [
                'label' => 'JUMLAH BARU',
                'rowspan' => 3,
            ],
            [
                'label' => 'LAMA',
                'colspan' => $colspanCounter,
            ],
            [
                'label' => 'JUMLAH LAMA',
                'rowspan' => 3,
            ],
            [
                'label' => 'KUNJUNGAN',
                'rowspan' => 3,
            ],
            [
                'label' => 'HP',
                'rowspan' => 3,
            ],
        ];

        foreach($dataHeader['carabayar'] as $k => $v) {
            $tmpCabarBaru[] = [
                'label' => $k,
                'colspan' => 2,
            ];
            $tmpCabarLama[] = [
                'label' => $k,
                'colspan' => 2,
            ];
        }
        $tmpCabarBaru[0]['startfrom'] = 3;
        $tmpCabarLama[0]['startfrom'] = 2;
        $listCaraBayar = array_merge($tmpCabarBaru, $tmpCabarLama);

        $listJk = [];
        for ($i=0; $i < $colspanCounter; $i++) { 
            $jkCodeL = $jk[0]['lookup_kode'];
            $jkCodeP = $jk[1]['lookup_kode'];
            $tmpJkL = [
                'label' => $jkCodeL,
            ];
            $tmpJkP = [
                'label' => $jkCodeP,
            ];

            if($i == 0) {
                $tmpJkL['startfrom'] = 3;
            }

            if($i == $countCarabayar) {
                $tmpJkL['startfrom'] = 2;
            }
            array_push($listJk, $tmpJkL);
            array_push($listJk, $tmpJkP);
        }

        return [
            $staticFirstRow,
            $staticSecondRow,
            $listCaraBayar,
            $listJk
        ];
    }

    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->andWhere(['lookup_type' => $type]);
            $result->andWhere(['is_active' => TRUE]);
        }
        $result->orderBy(['lookup_urutan' => SORT_ASC]);
        return $result;
    }

    private function getProfil()
    {
        $profil = Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();

        return $profil;
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('lap-sensus-harian-pasien-rajal/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('web/'.'uploads/'. $this->unique_str .'.xlsx'),
                        'filename' => 'LAP_SENSUS_HARIAN_PASIEN_RAWAT_JALAN.xlsx'
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

    private function setUrl()
    {
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];
        $client =  new Client([
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);
        return $client;
    }
}