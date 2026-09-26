<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\LaporanrekapkinerjaV;
use app\modules\v1\models\LaporanrekapkinerjaheaderV;
use app\modules\v1\models\SensuspasienranapV;
use app\modules\v1\models\LaporanKinerjaProfesionalLosV;
use app\modules\v1\models\GetHasilAkhirSensusFn;
use app\modules\v1\models\GetPasienAwalRekapKinerjaFn;
use app\modules\v1\models\LaporanRekapKinerjaFn;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\Services\Cache;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use app\modules\v1\models\UploadForm;


class LapRekapHarianKinerjaProfesionalController extends DocoActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'app\modules\v1\models\LaporanrekapkinerjaV';
    const JUMLAH = 'jumlah';
    const I_RI = 3;
    const INSTALASI_ID = 'instalasi_id';
    public static $LIST_INSTALASI = [
        self::I_RI,        
    ];
    
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $getRequest = $request->get();
        
        return $this->generateData($getRequest);
    }

    private static function model($new = false)
    {
        if($new) {
            return new LaporanrekapkinerjaV;
        } else {
            return LaporanrekapkinerjaV::find();
        }
    }

    private static function modelHeader($new = false)
    {
        if($new) {
            return new LaporanrekapkinerjaheaderV;
        } else {
            return LaporanrekapkinerjaheaderV::find();
        }
    }

    private function generateData($filter, $exportExcel = false)
    {
        $start = date('Y-m-d');
        $end = date('Y-m-d');

        $data = [];
        $ruanganId = 0;

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('tgl_sensus', $filter['advanced-filter'])) {
                $explode = explode(' - ', $filter['advanced-filter']['tgl_sensus']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_sensus']);
            }

            if(array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruanganId = (int) $filter['advanced-filter']['ruangan_id'];
                unset($_GET['advanced-filter']['ruangan_id']);
            }
        }

        $modelHeader = Yii::$app->db->createCommand('
            SELECT * FROM laporanrekapkinerjaprofesional_fn(:xstart_date, :xend_date, :xruangan_id);
        ')
        ->bindValue('xstart_date', $start)
        ->bindValue('xend_date', $end)
        ->bindValue('xruangan_id', ArrayHelper::getValue($filter, 'advanced-filter.ruangan_id', null))
        ->queryAll();

        return $modelHeader;
    }

    private static function countDays($start , $end = null)
    {
        $dateOne = new \DateTime($start);

        if($end) {
            $dateTwo = new \DateTime($end);
        } else {
            $dateTwo = new \DateTime();
        }
        $tmpResult = ($dateTwo->diff($dateOne));

        $result = $tmpResult->days + 1;

        return $result;
    }

    public function actionExportExcel()
    {
        $data = [];
        $getRequest = Yii::$app->request->get();
        $title = 'Laporan Rekapitulasi harian Perbandingan Data Kinerja Profesional';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $ruanganNama = '';

        if (array_key_exists('advanced-filter', $getRequest)) {
            if (array_key_exists('tgl_sensus', $getRequest['advanced-filter'])) {
                $explode = explode(' - ', $getRequest['advanced-filter']['tgl_sensus']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                
            }

            if(array_key_exists('ruangan_id', $getRequest['advanced-filter'])) {
                $ruanganId = (int) $getRequest['advanced-filter']['ruangan_id'];
                $qryRuangan = Ruangan::findOne($ruanganId);
                $ruanganNama = $qryRuangan->ruangan_nama;
            }
        }

        $tmpData = $this->generateData($getRequest, true);
        $no = 1;
        foreach ($tmpData as $key => $value) {
            $tmp[1] = (strtolower($value['kelas_nama']) == strtolower(self::JUMLAH)) ? '' : $no;
            $tmp[2] = $value['kelas_nama'];
            $tmp[3] = $value['jmltt_kapasitas'];
            $tmp[4] = $value['jmltt_tersedia'];
            $tmp[5] = $value['jml_pasiensebelumnya'];
            $tmp[6] = $value['pasien_masuk'];
            $tmp[7] = $value['pasien_pindahan'];
            $tmp[8] = $value['jml_pasien'];
            $tmp[9] = $value['pasien_keluarhidup'];
            $tmp[10] = $value['pasien_keluardipindahkan'];
            $tmp[11] = $value['pasien_rujukrslain'];
            $tmp[12] = $value['pasien_keluarmeninggalkur48'];
            $tmp[13] = $value['pasien_keluarmeninggalleb48'];
            $tmp[14] = $value['pasien_keluarjumlah'];
            $tmp[15] = $value['hp'];
            $tmp[16] = $value['los'];
            $tmp[17] = $value['alos'];
            $tmp[18] = $value['bor'];
            $tmp[19] = $value['toi'];
            $tmp[20] = $value['bto'];
            $tmp[21] = $value['ndr'];
            $tmp[22] = $value['gdr'];
            $data[] = $tmp;
            $no++;
        }

        $header = [
            'Tanggal' => date('d-m-Y', strtotime($start))." - ".  date('d-m-Y', strtotime($end)),
        ];

        if(!empty($ruanganNama)) {
            if(!isset($header['Ruangan'])) {
                $header['Ruangan'] = $ruanganNama;
            }
        }

        $firstRow = [
            [
                'label' => 'No',
                'rowspan' => 3
            ],
            [
                'label' => 'RAWAT INAP (RUANGAN)',
                'rowspan' => 3
            ],
            [
                'label' => 'JUMLAH TEMPAT TIDUR',
                'colspan' => 2
            ],
            [
                'label' => 'JUMLAH PASIEN SEBELUM',
                'rowspan' => 3
            ],
            [
                'label' => 'PASIEN MASUK RAWAT',
                'colspan' => 3
            ],
            [
                'label' => 'PASIEN KELUAR',
                'colspan' => 6
            ],
            [
                'label' => 'HP (Pasien Sisa)',
                'rowspan' => 3
            ],
            [
                'label' => 'LOS Lama Rawat (Hari)',
                'rowspan' => 3
            ],
            [
                'label' => 'ALOS (Hari)',
                'rowspan' => 3
            ],
            [
                'label' => 'BOR (%)',
                'rowspan' => 3
            ],
            [
                'label' => 'TOI (Hari)',
                'rowspan' => 3
            ],
            [
                'label' => 'BTO (KALI)',
                'rowspan' => 3
            ],
            [
                'label' => 'NDR (%)',
                'rowspan' => 3
            ],
            [
                'label' => 'GDR (%)',
                'rowspan' => 3
            ],
        ];
        $secondRow = [
            [
                'label' => 'KAPASITAS',
                'startfrom' => 3,
                'rowspan' => 2
            ],
            [
                'label' => 'TERSEDIA',
                'rowspan' => 2
            ],
            [
                'label' => 'MASUK',
                'startfrom' => 2, // last loop index awal ditambah target pos
                'rowspan' => 2
            ],
            [
                'label' => 'PINDAHAN',
                'rowspan' => 2
            ],
            [
                'label' => 'JUMLAH',
                'rowspan' => 2
            ],
            [
                'label' => 'HIDUP',
                'rowspan' => 2
            ],
            [
                'label' => 'DIPINDAHKAN',
                'rowspan' => 2
            ],
            [
                'label' => 'RUJUK RS LAIN',
                'rowspan' => 2
            ],
            [
                'label' => 'MENINGGAL',
                'colspan' => 2
            ],
            [
                'label' => 'JUMLAH',
                'rowspan' => 2
            ],
        ];
        $thirdRow = [
            [
                'label' => '< 48 JAM',
                'startfrom' => 12,
            ],
            [
                'label' => '> 48 JAM',
            ],
        ];

        $custHeader = [
            $firstRow,
            $secondRow,
            $thirdRow
        ];

        $customFormatCode = [
            // [
            //     'startRow' => 'P8',
            //     'endRow' => 'P25'
            // ],
            [
                'startRow' => 'Q8',
                'endRow' => 'Q25'
            ],
            [
                'startRow' => 'R8',
                'endRow' => 'R25'
            ],
            [
                'startRow' => 'S8',
                'endRow' => 'S25'
            ],
            [
                'startRow' => 'T8',
                'endRow' => 'T25'
            ],
            [
                'startRow' => 'U8',
                'endRow' => 'U25'
            ],
            [
                'startRow' => 'V8',
                'endRow' => 'V25'
            ],
        ];

        $filePath = DocoHelpers::exportExcel($title, $data, $header ,array(
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            'customFormatCode' => $customFormatCode
        ), [], [], true);
        $filePath->save('php://output');
        die();
    }

    private function generatePasienSebelumnyaNew($start, $ruanganId = null)
    {
        $sql = "
            SELECT 
                SUM(pasien_akhir) AS pasien_akhir,
                kelaspelayanan_id
            FROM sensuspasienranap_v";
        if($ruanganId) {
            $sql .= " WHERE id IN (
               SELECT MAX(id) FROM sensuspasienranap_v WHERE tgl_sensus < :date AND ruangan_id = {$ruanganId} GROUP BY ruangan_id, kelaspelayanan_id
            )
            GROUP BY kelaspelayanan_id";
        } else {
            $sql .= " WHERE id IN (
               SELECT MAX(id) FROM sensuspasienranap_v WHERE tgl_sensus < :date GROUP BY ruangan_id, kelaspelayanan_id
            )
            GROUP BY kelaspelayanan_id";
        }
        
        return Yii::$app->db->createCommand($sql)
        ->bindValue(':date', $start)
        ->queryAll();
    }

    private function generatePasienSebelumnya($start, $listKelas = null)
    {
        $data = $tmpData = $tmp = [];
        $prev_date = date('Y-m-d', strtotime($start .' -1 day'));
        $first_date_month = date('Y-m-01', strtotime($prev_date));
        $last_date_month = date('Y-m-t', strtotime($prev_date));
        $prev_days = (int) date('j', strtotime($prev_date));
        $start_days = (int) date('j', strtotime($start));

        $bulan = date('m', strtotime($prev_date));
        $tahun = date('Y', strtotime($prev_date));
        $days = date('t', strtotime($prev_date));

        for ($i=1; $i <= $days; $i++) {
            $day = $i;

            if ($i < 10) {
                $day = (int) '0'.$i;
            }

            if(!empty($listKelas)) {
                foreach($listKelas as $k) {
                    if(!isset($tmp[$i][$k])) {
                        $tmp[$i][$k] = [
                            'tgl_sensus' => date('Y-m-'.$day, strtotime($prev_date)),
                            'vtanggal' => $day,
                            'pasien_hari_sebelumnya' => 0,
                            'pasien_masuk' => 0,
                            'pasien_pindahan' => 0,
                            'jml_123' => 0,
                            'keluar_hidup' => 0,
                            'keluar_dipindahkan' => 0,
                            'keluar_meninggaljml' => 0,
                            'keluar_meninggalkur48' => 0,
                            'keluar_meninggalleb48' => 0,
                            'jml_567' => 0,
                            'pasien_akhir' => 0,
                        ];
                    }
                }
            }
        }

        $query = (new \yii\db\Query())
        ->select([
            'kelaspelayanan_id',
            'SUM(pasien_masuk) AS pasien_masuk',
            'SUM(pasien_pindahan) AS pasien_pindahan',
            'SUM(pasien_keluarhidup) AS pasien_keluarhidup',
            'SUM(pasien_keluardipindahkan) AS pasien_keluardipindahkan',
            'SUM(pasien_keluarmeniggalkur48) AS pasien_keluarmeniggalkur48',
            'SUM(pasien_keluarmeniggalleb48) AS pasien_keluarmeniggalleb48',
            'SUM(pasien_akhir) AS pasien_akhir',
            'tgl_sensus'
        ])
        ->from('sensuspasienranap_v')
        ->where(['between', 'tgl_sensus', $first_date_month, $last_date_month])
        ->groupBy(['kelaspelayanan_id', 'tgl_sensus']);    
        $modelData = $query->all();   
        foreach ($tmp as $key => $value) {
            foreach($value as $k => $v) {
                $kelas = $k;

                if ($key == 1) {
                    $awal = 0;
                } else {
                    $awal = isset($tmpData[$key-1][$kelas]['pasien_akhir']) ? $tmpData[$key-1][$kelas]['pasien_akhir'] : 0;
                }
                
                if(!empty($modelData)) {
                    foreach($modelData as $z => $model) {
                        if($kelas == $model['kelaspelayanan_id'] && $model['tgl_sensus'] == $tmp[$key][$kelas]['tgl_sensus']) {
                            $masuk = isset($model['pasien_masuk']) ? (int)$model['pasien_masuk'] : 0;
                            $pindahan = isset($model['pasien_pindahan']) ? (int)$model['pasien_pindahan'] : 0;
                            $klr_hidup = isset($model['pasien_keluarhidup']) ? (int)$model['pasien_keluarhidup'] : 0;
                            $dipindahkan = isset($model['pasien_keluardipindahkan']) ? (int)$model['pasien_keluardipindahkan'] : 0;
                            $meninggal_kur48jam = isset($model['pasien_keluarmeniggalkur48']) ? (int)$model['pasien_keluarmeniggalkur48'] : 0;
                            $meninggal_leb48jam = isset($model['pasien_keluarmeniggalleb48']) ? (int)$model['pasien_keluarmeniggalleb48'] : 0;
                            $jml_234 = $awal + $masuk + $pindahan;
                            $meninggal_jml = $meninggal_kur48jam + $meninggal_leb48jam;
                            $jml_678 = $klr_hidup + $dipindahkan + $meninggal_kur48jam + $meninggal_leb48jam;
                            $pasien_akhir = $jml_234 - $jml_678;
                            $tmpData[$key][$kelas] = [
                                'vtanggal' => $v['vtanggal'],
                                'pasien_hari_sebelumnya' => $awal,
                                'pasien_masuk' => $masuk,
                                'pasien_pindahan' => $pindahan,
                                'jml_123' => $jml_234,
                                'keluar_hidup' => $klr_hidup,
                                'keluar_dipindahkan' => $dipindahkan,
                                'keluar_meninggaljml' => $meninggal_jml,
                                'keluar_meninggalkur48' => $meninggal_kur48jam,
                                'keluar_meninggalleb48' => $meninggal_leb48jam,
                                'jml_567' => $jml_678,
                                'pasien_akhir' => $pasien_akhir,
                            ];
                        } 
                        else if(!isset($tmpData[$key][$kelas])) {
                            $masukKosong = 0;
                            $pindahanKosong = 0;
                            $klr_hidupKosong = 0;
                            $dipindahkanKosong = 0;
                            $meninggal_kur48jamKosong = 0;
                            $meninggal_leb48jamKosong = 0;
                            $jml_234Kosong = $awal + $masukKosong + $pindahanKosong;
                            $meninggal_jml = $meninggal_kur48jamKosong + $meninggal_leb48jamKosong;
                            $jml_678Kosong = $klr_hidupKosong + $dipindahkanKosong + $meninggal_kur48jamKosong + $meninggal_leb48jamKosong;
                            $pasien_akhir = $jml_234Kosong - $jml_678Kosong;
                            $tmpData[$key][$kelas] = [
                                'vtanggal' => $v['vtanggal'],
                                'pasien_hari_sebelumnya' => $awal,
                                'pasien_masuk' => $masukKosong,
                                'pasien_pindahan' => $pindahanKosong,
                                'jml_123' => $jml_234Kosong,
                                'keluar_hidup' => $klr_hidupKosong,
                                'keluar_dipindahkan' => $dipindahkanKosong,
                                'keluar_meninggaljml' => $meninggal_jml,
                                'keluar_meninggalkur48' => $meninggal_kur48jamKosong,
                                'keluar_meninggalleb48' => $meninggal_leb48jamKosong,
                                'jml_567' => $jml_678Kosong,
                                'pasien_akhir' => $pasien_akhir,
                            ];
                        }
                    }
                }
            }
        }

        if(!empty($tmpData)) {
            foreach($tmpData as $key => $value) {
                foreach($value as $k => $v) {
                    if($v['vtanggal'] == $prev_days) {
                        $data[$k] = [
                            // 'pasien_masuk' => $v['pasien_masuk'],
                            // 'pasien_pindahan' => $v['pasien_pindahan'],
                            // 'keluar_hidup' => $v['keluar_hidup'],
                            // 'keluar_dipindahkan' => $v['keluar_dipindahkan'],
                            // 'keluar_meninggaljml' => $v['keluar_meninggaljml'],
                            // 'pasien_hari_sebelumnya' => $v['pasien_hari_sebelumnya'],
                            // 'jml_123' => $v['jml_123'],
                            // 'jml_567' => $v['jml_567'],
                            'pasien_akhir' => (int)$v['pasien_akhir'],
                        ];
                    }
                }
            }
        }

        return $data;
    }

    private function generatePasienAwal($start, $kp, $ruanganId = null)
    {
        $pasienAwal = 0;
        $dateTarget = date('Y-m-d', strtotime($start));

        $checkRecord = SensuspasienranapV::find()
        ->where(['kelaspelayanan_id' => $kp, 'tgl_sensus' => $dateTarget]);
        if($ruanganId) {
            $checkRecord = $checkRecord->andWhere(['ruangan_id' => $ruanganId]);
        }
        $checkRecord = $checkRecord->exists();

        if($checkRecord != true) {
            $getData = SensuspasienranapV::find()
            ->where(['kelaspelayanan_id' => $kp]);
            if($ruanganId) {
                $getData = $getData->andWhere(['ruangan_id' => $ruanganId]);
            }
            $getData = $getData->orderBy(['tgl_sensus' => SORT_DESC])
            ->one();

            if(!empty($getData)) {
                $getNewDateTarget = date('Y-m-d', strtotime($getData['tgl_sensus']));
                if($dateTarget > $getNewDateTarget) {
                    $tmp = $this->generatePasienSebelumnyaNew($getNewDateTarget, $kp, true, $ruanganId);
                    $pasienAwal = $tmp[0]['pasien_akhir'];
                }
            }
        } else {
            $tmp = $this->generatePasienSebelumnyaNew($dateTarget, $kp, false, $ruanganId);
            $pasienAwal = $tmp[0]['pasien_akhir'];
        }

        return $pasienAwal;
    }

    public function actionGetOptions()
    {
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()
        ->where(['in', self::INSTALASI_ID,self::$LIST_INSTALASI]);
        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
            'pagination' => false
        ]);

        return [
            'ruangan' => $queryRuangan->getModels() ?: []
        ];
    }

    private static function countHours($start , $end = null)
    {
        $dateOne = new \DateTime($start);

        if($end) {
            $dateTwo = new \DateTime($end);
        } else {
            $dateTwo = new \DateTime();
        }
        $tmpResult = ($dateTwo->diff($dateOne));
        $result = $tmpResult->i;

        return $result;
    }

    private static function getPasienAwal($tglValue, $key, $ruanganId)
    {
        $subQuery = (new \yii\db\Query())
        ->select([
            'MAX(id) AS id'
        ])
        ->from('sensuspasienranap_v')
        ->where(['<', 'tgl_sensus', $tglValue]);

        if($key) {
            $subQuery->andWhere(['kelaspelayanan_id' => $key]);
        }

        if($ruanganId) {
            $subQuery->andWhere(['ruangan_id' => $ruanganId]);
        }

        $subQry = $subQuery->groupBy('ruangan_id, kelaspelayanan_id')
        ->orderBy(['ruangan_id' => SORT_ASC, 'kelaspelayanan_id' => SORT_ASC]);

        $query = (new \yii\db\Query())
        ->select([
            'SUM(pasien_akhir) AS pasien_akhir'
        ])
        ->from('sensuspasienranap_v')
        ->where(['IN', 'id', $subQry]);

        $resultQuery = $query->one();

        return array_key_exists('pasien_akhir', $resultQuery) ? (int)$resultQuery['pasien_akhir'] : 0;
    }

    private static function getRujukRsLain($start, $end, $ruanganId)
    {
        $queryHitung = (new \yii\db\Query())
        ->select([
            'COUNT(pasienadmisi_t.pendaftaran_id) as jumlah,
            pasienadmisi_t.kelaspelayanan_id'
        ])
        ->from('pasienadmisi_t')
        ->leftjoin("pasienpulang_t", "pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id")
        ->where(['between','date(pasienpulang_t.tglpasienpulang)', $start, $end]);
        
        $queryHitung->andWhere(['and','pasienpulang_t.pasienadmisi_id is not null']);
        $queryHitung->andWhere(['pasienpulang_t.carakeluar_id' => 2]);
        $queryHitung->andWhere(['pasienpulang_t.kondisikeluar_id' => 3]);
        $queryHitung->groupBy(['pasienadmisi_t.kelaspelayanan_id']);

        if ($ruanganId) {
            $queryHitung->andWhere(['pasienpulang_t.ruanganakhir_id' => $ruanganId]);
        }

        return $queryHitung->all();
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        
        //$data = $this->getDataLaporanExcel($getData)->asArray()->all();

        $data = $this->generateData($getData, true);

        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = 50;
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LapRekapHarianKinerjaProfresional' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'data' => $data
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportLapRekapHarianKinerjaExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLapRekapHarianKinerja' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function getDataLaporanExcel($params)
    {
        $request = Yii::$app->request;
        $model   = new LaporanDokterRujukanV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        if(isset($params['advanced-filter'])) {
            if(isset($params['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $params['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($params['advanced-filter']['tgl_pendaftaran']);
            }

            if(isset($params['advanced-filter']['dok_rujukan_nama'])) {
                $dok_rujukan_id = $params['advanced-filter']['dok_rujukan_nama'];

                unset($params['advanced-filter']['dok_rujukan_nama']);
            }

            if(isset($params['advanced-filter']['spes_rujukan_nama'])) {
                $spes_rujukan_id = $params['advanced-filter']['spes_rujukan_nama'];

                unset($params['advanced-filter']['spes_rujukan_nama']);
            }

            if(isset($params['advanced-filter']['jenis_referal_nama'])) {
                $jenis_referal_id = $params['advanced-filter']['jenis_referal_nama'];

                unset($params['advanced-filter']['jenis_referal_nama']);
            }

        }

        $query->Where(['between', 'tgl_pendaftaran', $start, $end]);

        if (!empty($dok_rujukan_id)){
            $query->andWhere(['dok_rujukan_id' => $dok_rujukan_id]);
        }

        if (!empty($spes_rujukan_id)){
            $query->andWhere(['=', 'spes_rujukan_id', $spes_rujukan_id]);
        }

        if (!empty($jenis_referal_id)){
            $query->andWhere(['jenis_referal_id' => $jenis_referal_id]);
        }

        
        
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Rekapitulasi harian Perbandingan Data Kinerja Profesional.xlsx';

        if (file_exists($fileName)) 
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }

}