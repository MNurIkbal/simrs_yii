<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\LaporanSensusPasienRanapFn;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\TempatTidurTersediaV;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\Services\Cache;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LaporanKinerjaProfesionalLosV;
use app\modules\v1\models\GetHasilAkhirSensusFn;

class LaporanSensusPasienRanapController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanSensusPasienRanapFn';
    public $messageBroker = [
        'recalculate' => [
            'services' => [
                'Sirs' => [
                    'RecalculateSensusRanap' => [
                        'payload' => ['date_start','isRecalculateAll'],
                        'result' => true
                    ],
                ],
            ],
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['recalculate'] = ['POST'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function actionGetDataOptions()
    {
        $ruangan = Ruangan::find()
        ->where(['is_active' => true, 'instalasi_id' => DocoConstants::INST_ID_RI])
        ->orderBy(['ruangan_nama' => SORT_ASC])
        ->all();

        $kelas = KelasPelayanan::find()
        ->where(['is_active' => true])
        ->orderBy(['kelaspelayanan_nama' => SORT_ASC])
        ->all();

        $jumlah_bed = $this->actionGetJumlahBed(null);

        return [
            'ruangan' => ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'),
            'kelas' => ArrayHelper::map($kelas, 'kelaspelayanan_id', 'kelaspelayanan_nama'),
            'jumlah_bed' => $jumlah_bed['jumlah_bed']
        ];
    }

    public function actionIndex()
    {
        $getRequest = Yii::$app->request->get();

        return $this->generateDataNew($getRequest);
    }

    public function actionExportExcel()
    {
        $getRequest = Yii::$app->request->get();
        $start = date('Y-m-01', strtotime('NOW'));
        $footer = array();
        $tempFooter = array();
        $kelas = '-';
        $ruangan = '-';
        $data = $this->generateDataNew($getRequest, true);
        $title = 'Laporan Bulanan Sensus Pasien Rawat Inap';
        if (array_key_exists('advanced-filter', $getRequest)) {
            if (array_key_exists('bulan', $getRequest['advanced-filter']) && array_key_exists('tahun', $getRequest['advanced-filter'])) {
                $date = $getRequest['advanced-filter']['tahun'].'-'.$getRequest['advanced-filter']['bulan'].'-01 00:00:01';
                $start = date('Y-m-01', strtotime($date));
            }

            if (array_key_exists('kelaspelayanan_id', $getRequest['advanced-filter'])) {
                $kelas = KelasPelayanan::findOne($getRequest['advanced-filter']['kelaspelayanan_id'])->kelaspelayanan_nama;
            }

            if (array_key_exists('ruangan_id', $getRequest['advanced-filter'])) {
                $ruangan = Ruangan::findOne($getRequest['advanced-filter']['ruangan_id'])->ruangan_nama;
            }
        }

        if (!empty($data)) {
            foreach ($data as $k => $subArray) {
                foreach ($subArray as $id => $value) {
                    if (array_key_exists($id, $tempFooter)) {
                        $tempFooter[$id] += $value;
                    } else {
                        $tempFooter[$id] = $value;
                    }
                }
            }

            if (array_key_exists('1', $tempFooter)) {
                $tempFooter['1'] = 'Total:';
            }

            // if (!empty($tempFooter)) {
            //     foreach ($tempFooter as $index => $val) {
            //         $newKey = ucfirst($index);
            //         $newKey = str_replace('_', ' ', $newKey);
            //         $tempFooter[$newKey] = $val;
            //         unset($tempFooter[$index]);
            //     }
            // }
        }

        $header = [
            'INST' => Yii::t('app', 'RAWAT INAP'),
            'BULAN' => date('M Y', strtotime($start)),
            'KELAS' => $kelas,
            'RUANGAN' => $ruangan
        ];
        $footer = [
            'title' => ['', 1],
            'data' => $tempFooter
        ];
        $filePath = DocoHelpers::exportExcel(
            $title,
            $data,
            $header,
            [
                'uploadPath' => './uploads',
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'startRow' => 'S9',
                        'endRow' => 'S45'
                    ],
                    [
                        'formatCode' => '0.000',
                        'startRow' => 'T9',
                        'endRow' => 'T45'
                    ],
                    [
                        'formatCode' => '0.000',
                        'startRow' => 'U9',
                        'endRow' => 'U45'
                    ],
                ],
                'customHeader' => $this->customHeaderExportExcel(),
            ],
            $footer,
            [],
            true
        );
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #title# => judul laporan
    * @attribute #bulan# => bulan laporan
    * @attribute #kelas# => kelas pelayanan
    * @attribute #ruangan# => ruangan
    * @attribute #pdfsensusbulananranap# => table
    **/
    public function actionExportPdf() 
    {
        $getRequest = Yii::$app->request->get();
        $start = date('Y-m-01', strtotime('NOW'));
        $kelas = '-';
        $ruangan = '-';
        $print = new DocoPrint();
        $data = $this->generateDataNew($getRequest);

        if (array_key_exists('advanced-filter', $getRequest)) {
            if (array_key_exists('bulan', $getRequest['advanced-filter']) && array_key_exists('tahun', $getRequest['advanced-filter'])) {
                $date = $getRequest['advanced-filter']['tahun'].'-'.$getRequest['advanced-filter']['bulan'].'-01 00:00:01';
                $start = date('Y-m-01', strtotime($date));
            }

            if (array_key_exists('kelaspelayanan_id', $getRequest['advanced-filter'])) {
                $kelas = KelasPelayanan::findOne($getRequest['advanced-filter']['kelaspelayanan_id'])->kelaspelayanan_nama;
            }

            if (array_key_exists('ruangan_id', $getRequest['advanced-filter'])) {
                $ruangan = Ruangan::findOne($getRequest['advanced-filter']['ruangan_id'])->ruangan_nama;
            }
        }

        $print->attributes = [
            '#title#' => 'LAPORAN SENSUS PASIEN RANAP',
            '#bulan#' => date('M Y', strtotime($start)),
            '#kelas#' => $kelas,
            '#ruangan#' => $ruangan,
            '#pdfsensusbulananranap#' => $this->renderPartial('index', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }

    private function generateData($filter, $is_excel = false)
    {
        $date = date('Y-m-01', strtotime('NOW'));
        $bulan = date('m', strtotime($date));
        $tahun = date('Y', strtotime($date));
        $kelaspelayanan_id = null;
        $ruangan_id = null;
        $data = array();
        $konfig = Cache::getKonfigSystem();

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('bulan', $filter['advanced-filter']) && array_key_exists('tahun', $filter['advanced-filter'])) {
                $bulan = $filter['advanced-filter']['bulan'];
                $tahun = $filter['advanced-filter']['tahun'];
                $date = $filter['advanced-filter']['tahun'].'-'.$filter['advanced-filter']['bulan'].'-01';
            }

            if (array_key_exists('kelaspelayanan_id', $filter['advanced-filter'])) {
                $kelaspelayanan_id = $filter['advanced-filter']['kelaspelayanan_id'];
            }

            if (array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruangan_id = $filter['advanced-filter']['ruangan_id'];
            }
        }

        $days = date('t', strtotime($date));

        for ($i=1; $i <= $days; $i++) {
            $day = $i;

            if ($i < 10) {
                $day = '0'.$i;
            }

            $data[] = [
                'tgl_sensus' => date('Y-m-'.$day, strtotime($date)),
                'tanggal' => $day,
                'awal' => 0,
                'masuk' => 0,
                'pindahan' => 0,
                'jml_234' => 0,
                'klr_hidup' => 0,
                'dipindahkan' => 0,
                'meninggal_jml' => 0,
                'meninggal_kur48jam' => 0,
                'meninggal_leb48jam' => 0,
                'jml_678' => 0,
                'pasien_akhir' => 0,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'ruangan_id' => $ruangan_id,
                'rujukrs_lain' => 0
            ];
        }

        

        $total_sebelumnya = 0;
        foreach ($data as $key => $value) {
            $query = (new \yii\db\Query())
            ->select([
                'SUM(pasien_masuk) AS pasien_masuk',
                'SUM(pasien_pindahan) AS pasien_pindahan',
                'SUM(pasien_keluarhidup) AS pasien_keluarhidup',
                'SUM(pasien_keluardipindahkan) AS pasien_keluardipindahkan',
                'SUM(pasien_keluarmeniggalkur48) AS pasien_keluarmeniggalkur48',
                'SUM(pasien_keluarmeniggalleb48) AS pasien_keluarmeniggalleb48',
                'SUM(pasien_akhir) AS pasien_akhir'
            ])
            ->from('sensuspasienranap_v')
            ->where(['tgl_sensus' => $value['tgl_sensus']]);

            if ($kelaspelayanan_id) {
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                $query->addGroupBy('kelaspelayanan_id');
            }

            if ($ruangan_id) {
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                $query->addGroupBy('ruangan_id');
            }

            $model = $query->one();

            //rujuk rs lain
            $query2 = (new \yii\db\Query())
            ->select([
                'COUNT(*) as jumlah'
            ])
            ->from('pasienadmisi_t')
            ->leftjoin("pasienpulang_t", "pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id")
            ->where(['date(pasienpulang_t.tglpasienpulang)' => $value['tgl_sensus']]);
            
            $query2->andWhere(['and','pasienpulang_t.pasienadmisi_id is not null']);
            $query2->andWhere(['pasienpulang_t.carakeluar_id' => 2]);
            $query2->andWhere(['pasienpulang_t.kondisikeluar_id' => 3]);
            if ($kelaspelayanan_id) {
                $query2->andWhere(['pasienadmisi_t.kelaspelayanan_id' => $kelaspelayanan_id]);
            }

            if ($ruangan_id) {
                $query2->andWhere(['pasienpulang_t.ruanganakhir_id' => $ruangan_id]);
            }

            $model2 = $query2->one();
            $tgl_sebelumnya = date('Y-m-d', strtotime('-1 days', strtotime($value['tgl_sensus']))); //kurang tanggal sebanyak 1 hari

            if ($konfig['set_tgl_sensus'] > $tgl_sebelumnya){
                $tgl_sebelumnya = $konfig['set_tgl_sensus'];
            }

            //hitung rujuk rs lain
            $queryHitung = (new \yii\db\Query())
            ->select([
                'COUNT(*) as jumlah'
            ])
            ->from('pasienadmisi_t')
            ->leftjoin("pasienpulang_t", "pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id")
            ->where(['between','date(pasienpulang_t.tglpasienpulang)', $konfig['set_tgl_sensus'], $tgl_sebelumnya]);
            
            $queryHitung->andWhere(['and','pasienpulang_t.pasienadmisi_id is not null']);
            $queryHitung->andWhere(['pasienpulang_t.carakeluar_id' => 2]);
            $queryHitung->andWhere(['pasienpulang_t.kondisikeluar_id' => 3]);
            if ($kelaspelayanan_id) {
                $queryHitung->andWhere(['pasienadmisi_t.kelaspelayanan_id' => $kelaspelayanan_id]);
            }

            if ($ruangan_id) {
                $queryHitung->andWhere(['pasienpulang_t.ruanganakhir_id' => $ruangan_id]);
            }

            $modelHitung = $queryHitung->one();
            
            // Pasien awal
            $subQuery = (new \yii\db\Query())
            ->select([
                'MAX(id) AS id'
            ])
            ->from('sensuspasienranap_v')
            ->where(['<', 'tgl_sensus', $value['tgl_sensus']]);

            if($kelaspelayanan_id) {
                $subQuery->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            }

            if($ruangan_id) {
                $subQuery->andWhere(['ruangan_id' => $ruangan_id]);
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
            $awal = array_key_exists('pasien_akhir', $resultQuery) ? (int)$resultQuery['pasien_akhir'] : 0;

            //mengurangi pasien rujuk rs lain
            $rujukrs_lain = $model2['jumlah'];
            $awal = $awal - $modelHitung['jumlah'];

            $masuk = isset($model['pasien_masuk']) ? (int)$model['pasien_masuk'] : 0;
            $pindahan = isset($model['pasien_pindahan']) ? (int)$model['pasien_pindahan'] : 0;
            $klr_hidup = isset($model['pasien_keluarhidup']) ? (int)$model['pasien_keluarhidup'] : 0;
            $dipindahkan = isset($model['pasien_keluardipindahkan']) ? (int)$model['pasien_keluardipindahkan'] : 0;
            $meninggal_kur48jam = isset($model['pasien_keluarmeniggalkur48']) ? (int)$model['pasien_keluarmeniggalkur48'] : 0;
            $meninggal_leb48jam = isset($model['pasien_keluarmeniggalleb48']) ? (int)$model['pasien_keluarmeniggalleb48'] : 0;
            $jml_234 = $awal + $masuk;
            $meninggal_jml = $meninggal_kur48jam + $meninggal_leb48jam;
            $jml_678 = $klr_hidup + $meninggal_kur48jam + $meninggal_leb48jam + $rujukrs_lain;
            $pasien_akhir = $jml_234 - $jml_678;

            $los = $this->getLos($value['tgl_sensus']);
            $kapasitas = $this->actionGetJumlahBed(null);
            $kapasitas = $kapasitas['jumlah_bed'];
            $alos = ($los != 0 && $klr_hidup != 0) ? round(($los / $klr_hidup)) : 0;
            $bor = ( $pasien_akhir != 0  && $kapasitas != 0) ? (float) ( $pasien_akhir / (1 * $kapasitas)) * 100: 0;
            $hari = date('d', strtotime($value['tgl_sensus']));
            $bor_until = ( $pasien_akhir != 0  && $kapasitas != 0) ? (float) ( $pasien_akhir / ($hari * $kapasitas)) * 100: 0;
            $toi = ($klr_hidup != 0) ? round(((1 * $kapasitas) -  $pasien_akhir) / $klr_hidup) : 0;
            $bto = ($klr_hidup != 0 && $kapasitas != 0) ? (float) ($klr_hidup / $kapasitas) : 0;
            $ndr = ($meninggal_leb48jam != 0 && $klr_hidup != 0) ? (float) $meninggal_leb48jam / $klr_hidup * 1000: 0;
            $gdr = ($meninggal_kur48jam != 0 || $meninggal_leb48jam && $klr_hidup != 0) ? (float) ($meninggal_kur48jam + $meninggal_leb48jam) / $klr_hidup * 1000: 0;

            if ($is_excel) {
                if ($value['tgl_sensus'] <= date('Y-m-d')) {
                    $data[$key] = [
                        'tanggal' => $value['tanggal'],
                        'awal' => $awal,
                        'masuk' => $masuk,
                        'pindahan' => $pindahan,
                        'jumlah_pasien' => $jml_234,
                        'keluar_hidup' => $klr_hidup,
                        'keluar_dipindahkan' => $dipindahkan,
                        'jumlah_keluar_meninggal' => $meninggal_jml,
                        'meninggal_<_48_jam' => $meninggal_kur48jam,
                        'meninggal_>_48_jam' => $meninggal_leb48jam,
                        'rujukrs_lain' => $rujukrs_lain,
                        'jumlah_pasien_keluar' => $jml_678,
                        'pasien_akhir' => $pasien_akhir,
                        'los' => $los,
                        'alos' => $alos,
                        'bor' => $bor,
                        'bor_until' => $bor_until,
                        'toi' => $toi,
                        'bto' => $bto,
                        'ndr' => $ndr,
                        'gdr' => $gdr
                    ];
                } else {
                    unset($data[$key]['tgl_sensus']);
                    unset($data[$key]['bulan']);
                    unset($data[$key]['tahun']);
                    unset($data[$key]['kelaspelayanan_id']);
                    unset($data[$key]['ruangan_id']);
                }
            } else {
                if ($value['tgl_sensus'] <= date('Y-m-d')) {
                    $data[$key] = [
                        'tanggal' => $value['tanggal'],
                        'awal' => $awal,
                        'masuk' => $masuk,
                        'pindahan' => $pindahan,
                        'jml_234' => $jml_234,
                        'klr_hidup' => $klr_hidup,
                        'dipindahkan' => $dipindahkan,
                        'meninggal_jml' => $meninggal_jml,
                        'meninggal_kur48jam' => $meninggal_kur48jam,
                        'meninggal_leb48jam' => $meninggal_leb48jam,
                        'jml_678' => $jml_678,
                        'pasien_akhir' => $pasien_akhir,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'kelaspelayanan_id' => $kelaspelayanan_id,
                        'ruangan_id' => $ruangan_id,
                        'rujukrs_lain' => $rujukrs_lain,
                        'los' => $los,
                        'alos' => $alos,
                        'bor' => $bor,
                        'bor_until' => $bor_until,
                        'toi' => $toi,
                        'bto' => $bto,
                        'ndr' => $ndr,
                        'gdr' => $gdr
                    ];
                }
            }
            if ($rujukrs_lain > 0){
                $total_sebelumnya = $total_sebelumnya + $rujukrs_lain;
            }
        }

        return $data;
    }

    private function generateDataNew($filter, $is_excel = false)
    {
        $date = date('Y-m-01', strtotime('NOW'));
        $bulan = date('m', strtotime($date));
        $tahun = date('Y', strtotime($date));
        $kelaspelayanan_id = null;
        $ruangan_id = null;
        $data = array();
        $konfig = Cache::getKonfigSystem();

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('bulan', $filter['advanced-filter']) && array_key_exists('tahun', $filter['advanced-filter'])) {
                $bulan = $filter['advanced-filter']['bulan'];
                $tahun = $filter['advanced-filter']['tahun'];
                $date = $filter['advanced-filter']['tahun'].'-'.$filter['advanced-filter']['bulan'].'-01';
            }

            if (array_key_exists('kelaspelayanan_id', $filter['advanced-filter'])) {
                $kelaspelayanan_id = $filter['advanced-filter']['kelaspelayanan_id'];
            }

            if (array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruangan_id = $filter['advanced-filter']['ruangan_id'];
            }
        }

        $days = date('t', strtotime($date));

        for ($i=1; $i <= $days; $i++) {
            $day = $i;

            if ($i < 10) {
                $day = '0'.$i;
            }

            $data[] = [
                'tgl_sensus' => date('Y-m-'.$day, strtotime($date)),
                'tanggal' => $day,
                'awal' => 0,
                'masuk' => 0,
                'pindahan' => 0,
                'jml_234' => 0,
                'klr_hidup' => 0,
                'dipindahkan' => 0,
                'meninggal_jml' => 0,
                'meninggal_kur48jam' => 0,
                'meninggal_leb48jam' => 0,
                'jml_678' => 0,
                'pasien_akhir' => 0,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'ruangan_id' => $ruangan_id,
                'rujukrs_lain' => 0,
                'los' => 0,
                'alos' => 0,
                'bor' => 0,
                'bor_until' => 0,
                'toi' => 0,
                'bto' => 0,
                'ndr' => 0,
                'gdr' => 0
            ];
        }

        $tgl_awal = date('Y-m-01', strtotime($date));
        $tgl_akhir = date('Y-m-'.$days, strtotime($date));
        $dataMasuk = $this->getDataMasuk($tgl_awal, $tgl_akhir, $konfig['set_tgl_sensus']);
        $dataPindahan = $this->getDataPindahan($tgl_awal, $tgl_akhir, $konfig['set_tgl_sensus']);
        $keluar = $this->getDataKeluar($tgl_awal, $tgl_akhir, $konfig['set_tgl_sensus']);
        $keluarPindah = $this->getDataKeluarPindah($tgl_awal, $tgl_akhir, $konfig['set_tgl_sensus']);
        $meninggalKurang = $this->getDataMeninggalKurang($tgl_awal, $tgl_akhir, $konfig['set_tgl_sensus']);
        $meninggalLebih = $this->getDataMeninggalLebih($tgl_awal, $tgl_akhir, $konfig['set_tgl_sensus']);

        $tgl_akhir_bulan = date('Y-m-d', strtotime('-1 days', strtotime($tgl_awal)));
        $getAwal =  (new GetHasilAkhirSensusFn(['extParam'=>[$tgl_akhir_bulan]]))->find()->asArray()->one();
        $awal = $getAwal['hasil_sensus'];

        $total_sebelumnya = 0;
        $getPasienAkhir = 0;

        $total_masuk = 0;
        $total_pindahan = 0;
        $total_123 = 0;
        $total_keluar = 0;
        $total_dipindahkan = 0;
        $total_meninggal = 0;
        $total_meninggal_krg = 0;
        $total_meninggal_lbh = 0;
        $total_rujuk_rs_lain = 0;
        $total_678 = 0;
        $total_hp = 0;
        $total_los = 0;

        $kapasitas = $this->actionGetJumlahBed(null);
        $kapasitas = $kapasitas['jumlah_bed'];
        $sum_bor = 0;

        foreach ($data as $key => $value) {
            
            if ($key > 0){
                $awal = $getPasienAkhir;
            }             

            //mengurangi pasien rujuk rs lain
            $rujukrs_lain = $this->getRujukRsLain($value['tgl_sensus'], $konfig['set_tgl_sensus'], $kelaspelayanan_id, $ruangan_id);

            $masuk = isset($dataMasuk[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_masuk']) ? (int)$dataMasuk[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_masuk'] : 0;
            $pindahan = isset($dataPindahan[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_pindahan']) ? (int)$dataPindahan[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_pindahan'] : 0;
            $klr_hidup = isset($keluar[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluarhidup']) ? (int)$keluar[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluarhidup'] : 0;
            $dipindahkan = isset($keluarPindah[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluardipindahkan']) ? (int)$keluarPindah[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluardipindahkan'] : 0;
            $meninggal_kur48jam = isset($meninggalKurang[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluarmeninggalkur48']) ? (int)$meninggalKurang[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluarmeninggalkur48'] : 0;
            $meninggal_leb48jam = isset($meninggalLebih[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluarmeninggalleb48']) ? (int)$meninggalLebih[$value['tgl_sensus']][$value['tgl_sensus']]['pasien_keluarmeninggalleb48'] : 0;
            $jml_234 = $awal + $masuk;
            $meninggal_jml = $meninggal_kur48jam + $meninggal_leb48jam;
            $jml_678 = $klr_hidup + $meninggal_kur48jam + $meninggal_leb48jam + $rujukrs_lain;
            $pasien_akhir = $jml_234 - $jml_678;
            $getPasienAkhir = $pasien_akhir;

            $los = $this->getLos($value['tgl_sensus']);
            
            
            $alos = ($los != 0 && $klr_hidup != 0) ? round(($los / ($klr_hidup + $rujukrs_lain + $meninggal_kur48jam + $meninggal_leb48jam))) : 0;
            $bor = ( $pasien_akhir != 0  && $kapasitas != 0) ? round(( $pasien_akhir / (1 * $kapasitas)) * 100): 0;
            $hari = date('d', strtotime($value['tgl_sensus']));
            $sum_bor = $sum_bor + $pasien_akhir;
            $bor_until = ( $pasien_akhir != 0  && $kapasitas != 0) ? round(( $sum_bor / ($hari * $kapasitas)) * 100): 0;
            $toi = ($klr_hidup != 0) ? round(((1 * $kapasitas) -  $pasien_akhir) / ($klr_hidup + $rujukrs_lain + $meninggal_kur48jam + $meninggal_leb48jam)) : 0;
            $bto = ($klr_hidup != 0 && $kapasitas != 0) ? (float) ((($klr_hidup + $rujukrs_lain) + $meninggal_kur48jam + $meninggal_leb48jam) / $kapasitas) : 0;
            $ndr = ($meninggal_leb48jam != 0 && $klr_hidup != 0) ? (float) $meninggal_leb48jam / (($klr_hidup + $rujukrs_lain) + $meninggal_leb48jam) : 0;
            $gdr = ($meninggal_kur48jam != 0 || $meninggal_leb48jam && $klr_hidup != 0) ? (float) ($meninggal_kur48jam + $meninggal_leb48jam) / (($klr_hidup + $rujukrs_lain) + $meninggal_kur48jam + $meninggal_leb48jam): 0;
            
            if ($value['tgl_sensus'] <= date('Y-m-d')) {
                $total_masuk = $total_masuk + $masuk;
                $total_pindahan = $total_pindahan + $pindahan;
                $total_123 = $total_123 + $jml_234;
                $total_keluar = $total_keluar + $klr_hidup;
                $total_dipindahkan = $total_dipindahkan + $dipindahkan;
                $total_meninggal = $total_meninggal + $meninggal_jml;
                $total_meninggal_krg = $total_meninggal_krg + $meninggal_kur48jam;
                $total_meninggal_lbh = $total_meninggal_lbh + $meninggal_leb48jam;
                $total_rujuk_rs_lain = $total_rujuk_rs_lain + $rujukrs_lain;
                $total_678 = $total_678 + $jml_678;
                $total_hp = $total_hp + $pasien_akhir;
                $total_los = $total_los + $los;
            }

            if ($is_excel) {
                if ($value['tgl_sensus'] <= date('Y-m-d')) {
                    $data[$key] = [
                        'tanggal' => $value['tanggal'],
                        'awal' => $awal,
                        'masuk' => $masuk,
                        'pindahan' => $pindahan,
                        'jumlah_pasien' => $jml_234,
                        'keluar_hidup' => $klr_hidup,
                        'keluar_dipindahkan' => $dipindahkan,
                        'jumlah_keluar_meninggal' => $meninggal_jml,
                        'meninggal_<_48_jam' => $meninggal_kur48jam,
                        'meninggal_>_48_jam' => $meninggal_leb48jam,
                        'rujukrs_lain' => $rujukrs_lain,
                        'jumlah_pasien_keluar' => $jml_678,
                        'pasien_akhir' => $pasien_akhir,
                        'los' => $los,
                        'alos' => $alos,
                        'bor' => $bor,
                        'bor_until' => $bor_until,
                        'toi' => $toi,
                        'bto' => number_format($bto, 2),
                        'ndr' => number_format($ndr, 3),
                        'gdr' => number_format($gdr, 3)
                    ];
                } else {
                    unset($data[$key]['tgl_sensus']);
                    unset($data[$key]['bulan']);
                    unset($data[$key]['tahun']);
                    unset($data[$key]['kelaspelayanan_id']);
                    unset($data[$key]['ruangan_id']);
                }
            } else {
                if ($value['tgl_sensus'] <= date('Y-m-d')) {
                    $data[$key] = [
                        'tanggal' => $value['tanggal'],
                        'awal' => $awal,
                        'masuk' => $masuk,
                        'pindahan' => $pindahan,
                        'jml_234' => $jml_234,
                        'klr_hidup' => $klr_hidup,
                        'dipindahkan' => $dipindahkan,
                        'meninggal_jml' => $meninggal_jml,
                        'meninggal_kur48jam' => $meninggal_kur48jam,
                        'meninggal_leb48jam' => $meninggal_leb48jam,
                        'jml_678' => $jml_678,
                        'pasien_akhir' => $pasien_akhir,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'kelaspelayanan_id' => $kelaspelayanan_id,
                        'ruangan_id' => $ruangan_id,
                        'rujukrs_lain' => $rujukrs_lain,
                        'los' => $los,
                        'alos' => $alos,
                        'bor' => $bor,
                        'bor_until' => $bor_until,
                        'toi' => $toi,
                        'bto' => number_format($bto, 2),
                        'ndr' => number_format($ndr, 3),
                        'gdr' => number_format($gdr, 3)
                    ];
                }
            }
            if ($rujukrs_lain > 0){
                $total_sebelumnya = $total_sebelumnya + $rujukrs_lain;
            }
        }

        $total_alos = ($total_los != 0 && $total_keluar != 0) ? round(($total_los / ($total_keluar + $total_rujuk_rs_lain + $total_meninggal_krg + $total_meninggal_lbh))) : 0;
        $total_bor = ( $total_hp != 0  && $kapasitas != 0) ? (float) ( $total_hp / ($days * $kapasitas)) * 100: 0;
        $total_bor_until = ( $total_hp != 0  && $kapasitas != 0) ? (float) ( $total_hp / ($days * $kapasitas)) * 100: 0;
        $total_toi = ($total_keluar != 0) ? round((($days * $kapasitas) -  $total_hp) / ($total_keluar + $total_rujuk_rs_lain + $total_meninggal_krg + $total_meninggal_lbh)) : 0;
        $total_bto = ($total_keluar != 0 && $kapasitas != 0) ? (float) ((($total_keluar + $total_rujuk_rs_lain) + $total_meninggal_krg + $total_meninggal_lbh) / $kapasitas) : 0;
        $total_ndr = ($total_meninggal_lbh != 0 && $total_keluar != 0) ? (float) $total_meninggal_lbh / (($total_keluar + $total_rujuk_rs_lain) + $total_meninggal_lbh): 0;
        $total_gdr = ($total_meninggal_krg != 0 || $total_meninggal_lbh && $total_keluar != 0) ? (float) ($total_meninggal_krg + $total_meninggal_lbh) / (($total_keluar + $total_rujuk_rs_lain) + $total_meninggal_krg + $total_meninggal_lbh): 0;

        $data[$days] = [
            'tanggal' => 'Jumlah',
            'awal' => $getPasienAkhir,
            'masuk' => $total_masuk,
            'pindahan' => $total_pindahan,
            'jml_234' => $total_123,
            'klr_hidup' => $total_keluar,
            'dipindahkan' => $total_dipindahkan,
            'meninggal_jml' => $total_meninggal,
            'meninggal_kur48jam' => $total_meninggal_krg,
            'meninggal_leb48jam' => $total_meninggal_lbh,
            'rujukrs_lain' => $total_rujuk_rs_lain,
            'jml_678' => $total_678,
            'pasien_akhir' => $total_hp,
            'los' => $total_los,
            'alos' => $total_alos,
            'bor' => round($total_bor),
            'bor_until' => round($total_bor_until),
            'toi' => $total_toi,
            'bto' => number_format($total_bto, 2),
            'ndr' => number_format($total_ndr, 3),
            'gdr' => number_format($total_gdr, 3),
            'bulan' => null,
            'tahun' => null,
            'kelaspelayanan_id' => null,
            'ruangan_id' => null,
        ];

        return $data;
    }

    public function actionGetJumlahBed($ruangan_id = null, $kelaspelayanan_id = null)
    {
        $ruangan_id = null; 
        $kelaspelayanan_id = null;
        // $where = 'WHERE kamartempattidur_m.is_active=TRUE AND kamartempattidur_m.is_deleted=FALSE AND kamarruangan_m.is_kamarthruput = TRUE';
        // $where = 'WHERE kamarruangan_m.is_active=TRUE 
        //         AND kamarruangan_m.is_deleted=FALSE 
        //         AND kamarruangan_m.is_rekapkinerjaprofesi = TRUE
        //         AND kamartempattidur_m.is_active = TRUE
        //         AND kamartempattidur_m.is_deleted = FALSE
        //         AND ruangan_m.instalasi_id ='.DocoConstants::INST_ID_RI;

        // if ($ruangan_id) {
        //     $where = $where.' AND kamarruangan_m.ruangan_id='.$ruangan_id;
        // }

        // if ($kelaspelayanan_id) {
        //     $where = $where.' AND kamarruangan_m.kelaspelayanan_id='.$kelaspelayanan_id;
        // }

        // $query = Yii::$app->db->createCommand('
        //     SELECT COUNT(kamartempattidur_m.kamartempattidur_id) AS jumlah_bed
        //     FROM kamartempattidur_m
        //     LEFT JOIN kamarruangan_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
        //     '.$where.'
        // ');

        //     $query = Yii::$app->db->createCommand('
        //     SELECT COUNT(kamarruangan_m.kamarruangan_id) AS jumlah_bed
        //     FROM kamarruangan_m
        //     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
        //     JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
        //     '.$where.'
        // ');
        //     $data = $query->queryOne();

        $jumlah_bed = TempatTidurTersediaV::find()->select('sum(jumlah_bed) as jumlah_bed');

        if ($ruangan_id) {
            $jumlah_bed->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($kelaspelayanan_id) {
            $jumlah_bed->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
        }

        return $jumlah_bed->asArray()->one();
    }

    public function actionRecalculate()
    {
        return 'Sync Berhasil';
    }

    private function getLos($tgl)
    {
        //Perhitungan los
        $modelLos = new LaporanKinerjaProfesionalLosV;
        $awal_los = date('Y-m-d', strtotime($tgl));
        $akhir_los = date('Y-m-d', strtotime($tgl));
        $modelLos = $modelLos::find()
                    ->select(['pendaftaran_id', 'pasienadmisi_id', 'min(tgl_masukkamar) tgl_masukkamar',  'max(tgl_keluarkamar) tgl_keluarkamar'])
                    //->where(['between', 'date(tgl_masukkamar)', $awal_los, $akhir_los])
                    ->where(['between', 'date(tgl_keluarkamar)', $awal_los, $akhir_los])
                    //->orderBy(['pasienadmisi_id' => SORT_ASC, 'tgl_masukkamar' => SORT_ASC])
                    ->groupBy(['pendaftaran_id','pasienadmisi_id']);
        
        // if ($ruanganId) {
        //     $modelLos->andWhere(['ruangan_id' => $ruanganId]);
        // }

        $rekapanLos = $modelLos->asArray()->all();
        $total = 0;

        if (!empty($rekapanLos)){
            foreach ($rekapanLos as $key => $value) {
                if ($value['tgl_keluarkamar'] == null){
                    $value['tgl_keluarkamar'] = $tgl;
                } else if ($value['tgl_keluarkamar'] > $tgl){
                    $value['tgl_keluarkamar'] = $tgl;
                }
                $tgl_awal = date('Y-m-d', strtotime($value['tgl_masukkamar']));
                $tgl_akhir = date('Y-m-d', strtotime($value['tgl_keluarkamar']));
                $jarak = self::countDays($tgl_awal, $tgl_akhir);
                if ($tgl_awal == $tgl_akhir){
                    $total = $total + $jarak;
                } else {
                    $total = $total + ($jarak - 1);
                }
            }
        }
        
        return $total;
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

    private static function getRujukRsLain($tgl, $tgl_sensus, $kelaspelayanan_id, $ruangan_id, $sebelum = false)
    {
        //rujuk rs lain
        $query2 = (new \yii\db\Query())
        ->select([
            'COUNT(*) as jumlah'
        ])
        ->from('pasienadmisi_t')
        ->leftjoin("pasienpulang_t", "pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id");

        if($sebelum == false){
            $query2->andWhere(['date(pasienpulang_t.tglpasienpulang)' => $tgl]);
        } else {
            $tgl_sebelumnya = date('Y-m-d', strtotime('-1 days', strtotime($tgl))); //kurang tanggal sebanyak 1 hari

            if ($tgl_sensus > $tgl_sebelumnya){
                $tgl_sebelumnya = $tgl_sensus;
            }
            $query2->andWhere(['between','date(pasienpulang_t.tglpasienpulang)', $tgl_sensus, $tgl_sebelumnya]);
        }
        
        $query2->andWhere(['and','pasienpulang_t.pasienadmisi_id is not null']);
        $query2->andWhere(['pasienpulang_t.carakeluar_id' => 2]);
        $query2->andWhere(['pasienpulang_t.kondisikeluar_id' => 3]);
        if ($kelaspelayanan_id) {
            $query2->andWhere(['pasienadmisi_t.kelaspelayanan_id' => $kelaspelayanan_id]);
        }

        if ($ruangan_id) {
            $query2->andWhere(['pasienpulang_t.ruanganakhir_id' => $ruangan_id]);
        }

        $model = $query2->one();

        return $model['jumlah'];
    }

    private static function getDataMasuk($tgl_awal, $tgl_akhir, $set_tgl_sensus)
    {
        $queryMasuk = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_masuk, pasienadmisi_t.tgl_admisi::date
            FROM pasienadmisi_t
            JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            WHERE pasienadmisi_t.tgl_admisi::DATE BETWEEN '{$tgl_awal}' and '{$tgl_akhir}'
            AND pasienadmisi_t.status_ranap != 453
            AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
            AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
            group by pasienadmisi_t.tgl_admisi::date
        ")->queryAll();

        $resultMasuk = ArrayHelper::index($queryMasuk, 'tgl_admisi', [function ($element) {
            return $element['tgl_admisi'];
        }]);

        return $resultMasuk;
    }

    private static function getDataPindahan($tgl_awal, $tgl_akhir, $set_tgl_sensus)
    {
        $queryPindahan = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_pindahan, pindahkamar_t.tgl_pindahkamar::date
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE BETWEEN '{$tgl_awal}' and '{$tgl_akhir}'
            AND pasienadmisi_t.tgl_admisi >= '{$set_tgl_sensus}'
            group by pindahkamar_t.tgl_pindahkamar::date
        ")->queryAll();

        $resultPindah = ArrayHelper::index($queryPindahan, 'tgl_pindahkamar', [function ($element) {
            return $element['tgl_pindahkamar'];
        }]);
        return $resultPindah;
    }

    private static function getDataKeluar($tgl_awal, $tgl_akhir, $set_tgl_sensus)
    {
        $queryKeluar = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarhidup, pasienpulang_t.tglpasienpulang::date
            FROM pendaftaran_t 
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            WHERE pasienpulang_t.tglpasienpulang::date BETWEEN '{$tgl_awal}' and '{$tgl_akhir}'
            AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienadmisi_t.tgl_admisi >= '{$set_tgl_sensus}'
            group by pasienpulang_t.tglpasienpulang::date
        ")->queryAll();

        $resultKeluar = ArrayHelper::index($queryKeluar, 'tglpasienpulang', [function ($element) {
            return $element['tglpasienpulang'];
        }]);
        return $resultKeluar;
    }

    private static function getDataKeluarPindah($tgl_awal, $tgl_akhir, $set_tgl_sensus)
    {
        $queryKeluarPindah = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluardipindahkan, pindahkamar_t.tgl_pindahkamar::DATE
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE BETWEEN '{$tgl_awal}' and '{$tgl_akhir}'
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            AND pasienadmisi_t.tgl_admisi >= '{$set_tgl_sensus}'
            group by pindahkamar_t.tgl_pindahkamar::DATE
        ")->queryAll();

        $resultKeluarPindah = ArrayHelper::index($queryKeluarPindah, 'tgl_pindahkamar', [function ($element) {
            return $element['tgl_pindahkamar'];
        }]);
        return $resultKeluarPindah;
    }

    private static function getDataMeninggalKurang($tgl_awal, $tgl_akhir, $set_tgl_sensus)
    {
        $queryMeninggalKrg = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarmeninggalkur48, pasienpulang_t.tglpasienpulang::DATE
            FROM pasienadmisi_t
            JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
            WHERE pasienpulang_t.tglpasienpulang::DATE  BETWEEN '{$tgl_awal}' and '{$tgl_akhir}'
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 7
            AND pasienadmisi_t.tgl_admisi >= '{$set_tgl_sensus}'
            group by pasienpulang_t.tglpasienpulang::DATE
        ")->queryAll();

        $resultMeninggalKrg = ArrayHelper::index($queryMeninggalKrg, 'tglpasienpulang', [function ($element) {
            return $element['tglpasienpulang'];
        }]);
        return $resultMeninggalKrg;
    }

    private static function getDataMeninggalLebih($tgl_awal, $tgl_akhir, $set_tgl_sensus)
    {
        $queryMeninggalLbh = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarmeninggalleb48, pasienpulang_t.tglpasienpulang::DATE
            FROM pasienadmisi_t
            JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
            WHERE pasienpulang_t.tglpasienpulang::DATE BETWEEN '{$tgl_awal}' and '{$tgl_akhir}'
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 5
            AND pasienadmisi_t.tgl_admisi >= '{$set_tgl_sensus}'
            group by pasienpulang_t.tglpasienpulang::DATE
        ")->queryAll();
        
        $resultMeninggalLebih = ArrayHelper::index($queryMeninggalLbh, 'tglpasienpulang', [function ($element) {
            return $element['tglpasienpulang'];
        }]);
        return $resultMeninggalLebih;
    }

    private function customHeaderExportExcel() {
        $custHeader = [];
        $one = [
            [
                'label'=>'Tanggal',
                'rowspan'=>2,
            ],
            [
                'label'=>'Pasien',
                'colspan'=>3,
            ],
            [
                'label'=>"Jumlah (2 ,3, 4)",
                'rowspan'=>2,
            ],
            [
                'label'=>'Pasien Keluar',
                'colspan'=>6,
            ],
            [
                'label'=>"Jumlah (6, 7, 8)",
                'rowspan'=>2,
            ],
            [
                'label'=>'Pasien Akhir',
                'rowspan'=>2
            ],
            [
                'label'=>'LOS',
                'rowspan'=>2
            ],
            [
                'label'=>'ALOS',
                'rowspan'=>2
            ],
            [
                'label'=>'BOR HARI INI RS',
                'rowspan'=>2
            ],
            [
                'label'=>'BOR SAMPAI HARI INI RS',
                'rowspan'=>2
            ],
            [
                'label'=>'TOI',
                'rowspan'=>2
            ],
            [
                'label'=>'BTO',
                'rowspan'=>2
            ],
            [
                'label'=>'NDR',
                'rowspan'=>2
            ],
            [
                'label'=>'GDR',
                'rowspan'=>2
            ],
        ];
        $two = [
            [
                'label'=>'Awal',
                'startfrom' => 3,
                'rowspan'=>1,
            ],
            [
                'label'=>'Masuk',
                'rowspan'=>1,
            ],
            [
                'label'=>'Pindahan',
                'rowspan'=>1,
            ],
            [
                'label'=>'Keluar Hidup',
                'startfrom' => 2,
                'rowspan'=>1,
            ],
            [
                'label'=>'Dipindahkan',
                'rowspan'=>1,
            ],
            [
                'label'=>'Meninggal',
                'colspan'=>3,
            ],
        ];
        // $three = [
        //     [
        //         'label'=>'Jumlah',
        //         'startfrom' => 9,
        //     ],
        //     [
        //         'label'=>'< 48 Jam',
        //     ],
        //     [
        //         'label'=>'> 48 Jam',
        //     ],
        // ];
        $four = [
            [
                'label'=>'1',
            ],
            [
                'label'=>'2',
            ],
            [
                'label'=>'3',
            ],
            [
                'label'=>'4',
            ],
            [
                'label'=>'5',
            ],
            [
                'label'=>'6',
            ],
            [
                'label'=>'7',
            ],
            [
                'label'=>'7',
            ],
            [
                'label'=>'8',
            ],
            [
                'label'=>'9',
            ],
            [
                'label'=>'10',
            ],
            [
                'label'=>'11',
            ],
            [
                'label'=>'12',
            ],
        ];
        return $custHeader = [
            $one,
            //$two
        ];
    }

}