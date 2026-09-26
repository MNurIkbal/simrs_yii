<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\JenisKasusPenyakit;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\Services\InternalService;

use app\modules\v1\models\ProfilRumahSakitView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\LaporanRekapUnitPelayanan;
use app\modules\v1\models\RLPembedahan;
use app\modules\v1\models\RLRadiologi;
use app\modules\v1\models\RLRadiologiHeader;
use app\modules\v1\models\RLPembedahanHeader;
use app\modules\v1\models\RLFasilitaasTempatTidurRanapHeader;
use app\modules\v1\models\RLFasilitaasTempatTidurRanapDetail;
use app\modules\v1\models\RLDataRs;
use app\modules\v1\models\RLDataRsTempatTidur;
use app\modules\v1\models\RLDataRsPegawai;
use app\modules\v1\models\RLKegiatanRawatInap;
use app\modules\v1\models\RLKegiatanRawatInapDetail;
use app\modules\v1\models\RLMorbiditasRanapHeader;
use app\modules\v1\models\RLFasilitaasMorbiditasRanapHeader;
use app\modules\v1\models\RLFasilitaasMorbiditasRanapDetail;
use app\modules\v1\models\RL5_4;
use app\modules\v1\models\RL5_2;
use app\modules\v1\models\RL5_2_detail;
use app\modules\v1\models\LaporanIndikatorRsView;
use app\modules\v1\models\Rl4BMorbiditasrawatjalanV;
use app\modules\v1\models\Rl4BMorbiditasrawatjalandetailV;
use app\modules\v1\models\RLMorbiditasRajalHeader;

use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoHelpers;
use yii\data\ArrayDataProvider;

class LaporanSirsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanRekapUnitPelayanan';
    const _LAKI = '_LAKI';
    const _PEREMPUAN = '_PEREMPUAN';
    const K_MENINGGAL = 4;
    const K_PLG = 1;

    public $messageBroker = [
        'get-data-morbiditas-ranap' => [
            'services' => [
                'Sirs' => [
                    'LaporanMorbiditasRanap' => [
                        'result' => true,
                        'successProcess'=>true,
                    ]
                ],
            ]
        ],
        'sync-data-laporan-morbiditas-rajal' => [
            'services' => [
                'Sirs' => [
                    'GetLaporanMorbiditasRajal' => [
                        'result' => true,
                        'succesProcess' => true,
                    ]
                ]
            ]
        ]
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

    public function actionGetLaporan()
    {
        $request = Yii::$app->request;
        $jenis_laporan = $request->get('jenis_laporan');
        switch ($jenis_laporan) {
            case 372:
                /** RL 1.1  Data Dasar Rumah Sakit**/
                return $this->laporanDataRs();
                break;
            case 373:
                /** RL 1.2  Indikator Pelayanan Rumah Sakit**/
                return $this->laporanIndikatorRs();
                break;
            case 374:
                /** RL 1.3  Fasilitas Tempat Tidur Ranap**/
                return $this->laporanFasilitasTempatTidurRanap();
            case 376:
                /** RL 3.1  Kegiatan Pelayanan Rawat Inap**/
                return $this->laporanRl_3_1();
                break;
            case 389:
                /** RL 3.14  Rujukan**/
                return $this->laporanRujukan();
                break;
            case 377:
                /** RL 3.2  Laporan Kunjungan IGD**/
                return $this->laporanKunjunganIgd();
            case 381:
                /** RL 3.6  Pembedahan**/
                return $this->laporanPembedahan();
                break;
            case 382:
                /** RL 3.7  Radiologi**/
                return $this->laporanRadiologi();
                break;
            case 383:
                /** RL 3.8  Lab**/
                return $this->laporanLaboratorium();
                break;
            case 391:
                /** RL 4.A  laporan Morbiditas Pasien Ranap**/
                return $this->laporanMorbiditasRanap();
                break;
            case 392:
                /** RL 4.B  laporan Morbiditas Pasien Rajal **/
                return $this->laporanMorbiditasRajal();
                break;
            case 393:
                /** RL 5.2 Laporan Pengunjung Rawat Jalan **/
                return $this->laporanPengunjungRajal();
                break;
            case 394:
                /** RL 5.2 Laporan Kunjungan Rawat Jalan **/
                return $this->laporanKunjunganRajal();
                break;
            case 395:
                /** RL 5.4 Daftar 10 Besar Penyakit Rawat Inap **/
                return $this->laporanRl_5_3();
                break;
            case 396:
                /** RL 5.4 Daftar 10 Besar Penyakit Rawat Jalan **/
                return $this->laporanRl_5_4();
                break;
            default:
               return;
                break;
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $jenis_laporan = $request->get('jenis_laporan');
        switch ($jenis_laporan) {
            case 372:
                /** RL 1.1  Data Dasar Rumah Sakit**/
                return $this->excelDataRs();
                break;
            case 373:
                /** RL 1.2  Indikator Pelayanan Rumah Sakit**/
                return $this->excelLaporanIndikatorRs();
                break;
            case 374:
                /** RL 1.3  Fasilitas Tempat Tidur Ranap**/
                return $this->excelFasilitasTempatTidurRanap();
                break;
            case 376:
                /** RL 3.1  Kegiatan Pelayanan Rawat Inap**/
                return $this->excelRl_3_1();
                break;
            case 377:
                /** RL 3.2  Laporan Kunjungan IGD**/
                return $this->excelKunjunganIgd();
            case 381:
                /** RL 3.6  Pembedahan**/
                return $this->excelPembedahan();
                break;
            case 382:
                /** RL 3.7  Radiologi**/
                return $this->excelRadiologi();
                break;
            case 383:
                /** RL 3.8  Lab**/
                return $this->excelLaboratorium();
                break;
            case 389:
                /** RL 3.14  Rujukan**/
                return $this->excelRujukan();
                break;
            case 391:
                /** RL 4.A  laporan Morbiditas Pasien Ranap **/
                return $this->excelMorbiditasRanap();
                break;
            case 392:
                /** RL 4.B  laporan Morbiditas Pasien Rajal **/
                return $this->excelMorbiditasRajal();
                break;
            case 393:
                /** RL 5.2 Laporan Pengunjung Rawat Jalan **/
                return $this->excelLaporanPengunjungRajal();
                break;
            case 394:
                /** RL 5.2 Laporan Kunjungan Rawat Jalan **/
                return $this->excelLaporanKunjunganRajal();
                break;
            case 395:
                /** RL 5.3 Daftar 10 Besar Penyakit Rawat Jalan **/
                return $this->excelLaporanPenyakitPasienRanap();
                break;
            case 396:
                /** RL 5.4 Daftar 10 Besar Penyakit Rawat Jalan **/
                return $this->excelRl_5_4();
                break;
            default:
               return;
                break;
        }
    }

    protected function laporanDataRs()
    {
        $request = Yii::$app->request;
        $contents = RLDataRs::find()->asArray()->one();

        $dateFormat = [
            'Tanggal Registrasi',
            '10.2  Tanggal',
            '10.5  Masa Berlaku s/d thn',
            '12.3  Tanggal Akreditasi'
        ];
        foreach ($contents as $key => $value) {
            if (in_array($key, $dateFormat)) {
                $contents[$key] = date('d F Y', strtotime($value));
            }
        }

        $contents['Jumlah Tempat Tidur'] = '';
        $tt = RLDataRsTempatTidur::find()->asArray()->all();
        $noUrut = '13.';
        $count = 1;
        foreach ($tt as $each) {
            $contents[$noUrut . $count++ . ' ' . $each['kelaspelayanan_nama']] = $each['jumlah_tempattidur'];
        }

        $contents['Jumlah Tenaga Medis'] = '';
        $tenagaMedis = RLDataRsPegawai::find()
            ->andWhere(['!=', 'pendkualifikasi_nama', 'Tenaga Non Kesehatan'])
            ->asArray()->all();
        $noUrut = '14.';
        $count = 1;
        foreach ($tenagaMedis as $each) {
            $contents[$noUrut . $count++ . ' ' . $each['pendkualifikasi_nama']] = $each['total'];
        }

        $tenagaMedisLain = RLDataRsPegawai::find()
            ->andWhere(['pendkualifikasi_nama' => 'Tenaga Non Kesehatan'])
            ->asArray()->one();
        $contents['Jumlah ' . $tenagaMedisLain['pendkualifikasi_nama']] = $tenagaMedisLain['total'];

        return $contents ? : [];
    }

    protected function laporanIndikatorRs()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');

        $resQueryDetail = LaporanIndikatorRsView::find()->orderBy([
            "tahun"=>SORT_DESC
        ])->all();

        //$resQueryDetail = Yii::$app->db->createCommand("{$query}")->queryAll();

        $item = [];
        foreach ($resQueryDetail as $value) {
            $item[$value['tahun']] =
            [
                "tahun"         => $value['tahun'],
                "bor"           => $value['bor'],
                "avlos"         => $value['avlos'],
                "bto"           => $value['bto'],
                "toi"           => $value['toi'],
                "ndr"           => $value['ndr'],
                "gdr"           => $value['gdr'],
                "avg_kunjungan" => $value['avg_kunjungan']
            ];
        }

        //$item[0] = ["Tahun I", 2, 3, 4, 5, 6, 7, 8];
        //$item[1] = ["Tahun II", 2, 3, 4, 5, 6, 7, 8];
        //$item[2] = ["Tahun III", 2, 3, 4, 5, 6, 7, 8];
        //$dataLaporan[] = $item;

        return [
            'data' => $item,
            'profil' => $this->getProfil()
        ];
    }

    /**
     * summary laporan data kunjungan rawat darurat
     *
     * @return void
     * @author rizal
     */
    protected function laporanKunjunganIgd()
    {
        $request = Yii::$app->request;
        $bulan = !empty($request->get('bulan')) ? $request->get('bulan') : null;
        $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');

        // $rs = $this->getProfil();

        $contents = [];
        $header = [];
        $q_ver = "
            SELECT jeniskasuspenyakit_id, jeniskasuspenyakit_nama
            FROM rl3_2_kegiatanrawatdarurat_v
            WHERE kolom = 'vertikal'
        ";
        $ver = Yii::$app->db->createCommand($q_ver)->queryAll();
        $q_hor1 = "
            SELECT carakeluar_id, carakeluar_nama
            FROM rl3_2_kegiatanrawatdarurat_v
            WHERE kolom = 'horizontal_1'
        ";
        $hor1 = Yii::$app->db->createCommand($q_hor1)->queryAll();
        $q_hor2 = "
            SELECT kondisikeluar_id, kondisikeluar_nama
            FROM rl3_2_kegiatanrawatdarurat_v
            WHERE kolom = 'horizontal_2'
        ";
        $hor2 = Yii::$app->db->createCommand($q_hor2)->queryAll();

        $q_data = "
                SELECT *
                FROM rl3_2_kegiatanrawatdaruratdetail_v
                WHERE (EXTRACT(year FROM tgl_pendaftaran) = '{$tahun}')
            ";
        if (!empty($bulan)) {
            $q_data .= " AND (EXTRACT(month FROM tgl_pendaftaran)= '{$bulan}')";
        }
        $data = Yii::$app->db->createCommand($q_data)->queryAll();

        // return $data;
        $counts = [];
        $carakeluars = [];
        $kondisikeluars = [];
        foreach ($data as $key=>$datum) {
            if ($datum['non_rujukan']) {
                if (isset($counts['non_rujukan']) && isset($counts['non_rujukan'][$datum['non_rujukan']])) {
                    $counts['non_rujukan'][$datum['non_rujukan']]++;
                } else {
                    $counts['non_rujukan'][$datum['non_rujukan']] = 1;
                }

                if (isset($carakeluars[$datum['non_rujukan']]) && isset($carakeluars[$datum['non_rujukan']][$datum['carakeluar_nonrujukan']])) {
                    $carakeluars[$datum['non_rujukan']][$datum['carakeluar_nonrujukan']]++;
                } else {
                    $carakeluars[$datum['non_rujukan']][$datum['carakeluar_nonrujukan']] = 1;
                }

                if (isset($kondisikeluars[$datum['non_rujukan']]) && isset($kondisikeluars[$datum['non_rujukan']][$datum['mati_nonrujukan']])) {
                    $kondisikeluars[$datum['non_rujukan']][$datum['mati_nonrujukan']]++;
                } else {
                    $kondisikeluars[$datum['non_rujukan']][$datum['mati_nonrujukan']] = 1;
                }
            }

            if ($datum['rujukan']) {
                if (isset($counts['rujukan']) && isset($counts['rujukan'][$datum['rujukan']])) {
                    $counts['rujukan'][$datum['rujukan']]++;
                } else {
                    $counts['rujukan'][$datum['rujukan']] = 1;
                }

                if (isset($carakeluars[$datum['rujukan']]) && isset($carakeluars[$datum['rujukan']][$datum['carakeluar_nonrujukan']])) {
                    $carakeluars[$datum['rujukan']][$datum['carakeluar_nonrujukan']]++;
                } else {
                    $carakeluars[$datum['rujukan']][$datum['carakeluar_nonrujukan']] = 1;
                }

                if (isset($kondisikeluars[$datum['rujukan']]) && isset($kondisikeluars[$datum['rujukan']][$datum['mati_rujukan']])) {
                    $kondisikeluars[$datum['rujukan']][$datum['mati_rujukan']]++;
                } else {
                    $kondisikeluars[$datum['rujukan']][$datum['mati_rujukan']] = 1;
                }
            }

        }
        $temp = [
            'kasuspenyakit'=>$counts,
            'carakeluar'=>$carakeluars,
            'kondisikeluar'=>$kondisikeluars,
        ];


        foreach ($ver as $each) { // jeniskasuspenyakit
            $jkp_id = $each['jeniskasuspenyakit_id'];
            $arr['jeniskasuspenyakit_nama'] = $each['jeniskasuspenyakit_nama'];
            $arr['non_rujukan'] = isset($temp['kasuspenyakit']['non_rujukan'][$jkp_id]) ? $temp['kasuspenyakit']['non_rujukan'][$jkp_id] : 0;
            $arr['rujukan'] = isset($temp['kasuspenyakit']['rujukan'][$jkp_id]) ? $temp['kasuspenyakit']['rujukan'][$jkp_id] : 0;

            foreach ($hor1 as $ckeach) { // horizontal 1 | cara keluar
                $i = strtolower(str_replace(' ', '_', $ckeach['carakeluar_nama']));
                $ck_id = $ckeach['carakeluar_id'];
                $arr[$i] = isset($temp['carakeluar'][$jkp_id][$ck_id]) ? $temp['carakeluar'][$jkp_id][$ck_id] : 0;
            }

            foreach ($hor2 as $kkeach) { // horizontal 2
                $j = strtolower(str_replace(' ', '_', $kkeach['kondisikeluar_nama']));
                $kk_id = $kkeach['kondisikeluar_id'];
                $arr[$j] = isset($temp['kondisikeluar'][$jkp_id][$kk_id]) ? $temp['kondisikeluar'][$jkp_id][$kk_id] : 0;
            }

            $contents[] = $arr;
        }

        return [
            'tabelHeader' => ['hor1'=>$hor1, 'hor2'=>$hor2],
            'contents'=> $contents ? : [],
        ];
    }

    protected function laporanPembedahan()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');
        $header = RLPembedahanHeader::find()->all();
        $query = "
            SELECT SUM(qty_tindakan) AS qty_tindakan, golonganoperasi_id, kegiatanoperasi_id
            FROM rl3_6_pembedahandetail_v
            WHERE (EXTRACT(year FROM tgl_tindakan) = '{$tahun}')";
        if (!empty($bulan)) {
            $query .= " AND (EXTRACT(month FROM tgl_tindakan)= '{$bulan}')";
        }

        $detail = Yii::$app->db->createCommand("{$query} GROUP BY golonganoperasi_id, kegiatanoperasi_id")->queryAll();

        $dataDetail = [];
        foreach ($detail as $value) {
            $dataDetail[$value['golonganoperasi_id']][$value['kegiatanoperasi_id']] = $value['qty_tindakan'];
        }
        $hor = $ver = [];
        foreach ($header as $value) {
            if ($value->kolom === "vertikal") {
                $ver[$value->kegiatanoperasi_id] = $value->kegiatanoperasi_nama;
                continue;
            }
            $hor[$value->golonganoperasi_id] = $value->golonganoperasi_nama;
        }
        return [
            'header' => $hor,
            'list' => $ver,
            'detail' => $dataDetail
        ];
    }

    protected function laporanFasilitasTempatTidurRanap()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $modelheader = new RLFasilitaasTempatTidurRanapHeader;
        $queryheader = $modelheader::find();
        $header = $queryheader->orderBy(['kelaspelayanan_nama' => SORT_ASC,
                                        'jeniskasuspenyakit_nama' => SORT_ASC])->all();
        $query = "
            SELECT count(kelaspelayanan_id) as total,
                count(kamartempattidur_id) as total_bed,
                jeniskasuspenyakit_id,
                kelaspelayanan_id
            FROM rl1_3_fasilitastempattidurdetail_v
            WHERE (EXTRACT(year FROM created_date) = '{$tahun}')";
        if (!empty($bulan)) {
            $query .= " AND (EXTRACT(month FROM created_date)= '{$bulan}')";
        }
        $resQueryDetail = Yii::$app->db->createCommand("{$query} GROUP BY kelaspelayanan_id, jeniskasuspenyakit_id")->queryAll();

        $dataDetail = [];
        foreach ($resQueryDetail as $value) {
            $dataDetail[$value['kelaspelayanan_id']][$value['jeniskasuspenyakit_id']] = $value['total'];
        }

        $dataBed = [];
        foreach ($resQueryDetail as $value) {
            $dataBed[$value['jeniskasuspenyakit_id']][] = $value['total_bed'];
        }
        $resBed = [];
        foreach ($dataBed as $key => $x) {
            $total_bed = 0;
            foreach ($x as $k => $s) {
                $total_bed += $s;
            }
            $resBed[$key] = $total_bed;
        }
        $resultHeader = $resultVer = [];
        foreach ($header as $value) {
            if ($value->kolom === "vertikal") {
                $resultVer[$value->jeniskasuspenyakit_id] = $value->jeniskasuspenyakit_nama;
                continue;
            }
            $resultHeader[$value->kelaspelayanan_id] = $value->kelaspelayanan_nama;
        }

        $profil = $this->getProfil();
        $getProfil = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
            'Tahun' => $tahun,
        ];

        return [
            'header' => $resultHeader,
            'list' => $resultVer,
            'detail' => $dataDetail,
            'dataBed' => $resBed,
            'profil' => $getProfil,

        ];
    }

    protected function laporanMorbiditasRanap()
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 300);
        $dataDetailDiagnosa = $dataDetailJenisKelamin = $listUmur = $tmp = $tmpDataHeader = $listGolongan = $data = $header = $columns= [];
        $staticHeaderAwal = [
            'no' => 'no',
            'no_dtd' => 'no_dtd',
            'note_dtd' => 'note_dtd',
            'diagnosa_nama' => 'diagnosa_nama',
        ];
        $staticHeaderAkhir = [
            'lk' => 'lk',
            'pr' => 'pr',
            'keluar_hidup' => 'keluar_hidup',
            'keluar_mati' => 'keluar_mati',
            'diagnosa_id' => 'diagnosa_id',
        ];

        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $getOnlyHeader = $request->get('header');
        $isExcel = $request->get('excel', null);

        $modelheader = new RLFasilitaasMorbiditasRanapHeader;
        $queryheader = $modelheader::find();
        $header = $queryheader->where(['kolom' => 'vertikal'])
        ->orderBy([
            'dtd_noterperinci' => SORT_ASC,
            'diagnosa_kode' => SORT_ASC,
        ])->all();

        $qryUmur = "
            SELECT
                golonganumur_id,
                golonganumur_nama,
                golonganumur_namalainnya
            FROM rl4_a_morbiditasrawatinap_v
            WHERE kolom='horizontal'
            ORDER BY golonganumur_id
        ";
        $listUmur = Yii::$app->db->createCommand("{$qryUmur} ")->queryAll();
        
        foreach($listUmur as $w => $x) {
            $golUmurNama = str_replace(' ', '_', $x['golonganumur_namalainnya']);
            if(!isset($listGolongan[$golUmurNama.self::_LAKI])){
                $listGolongan[$golUmurNama.self::_LAKI] = null;
            } 

            if(!isset($listGolongan[$golUmurNama.self::_PEREMPUAN])){
                $listGolongan[$golUmurNama.self::_PEREMPUAN] = null;
            }

            if(!isset($tmpDataHeader[$x['golonganumur_namalainnya']])){
                $tmpDataHeader[$x['golonganumur_namalainnya']] =  $x['golonganumur_namalainnya'];
            }
            
        }

        $header = array_merge($staticHeaderAwal, $listGolongan);
        $header = array_merge($header, $staticHeaderAkhir);


        if(!$getOnlyHeader) {
            $qryData = "SELECT * FROM laporanrl4a_fn('".$tahun."-".(!empty($bulan) ? $bulan : '01')."-01', '".$tahun."-".(!empty($bulan) ? $bulan : '12')."-31')";
            $listData = Yii::$app->db->createCommand("{$qryData} ")->queryAll();
            $index = 0;
            foreach ($listData as $value) {
                # code...
                $tmp[$index]['no'] = $index;
                $tmp[$index]['diagnosa_id'] = $value['diagnosa_id'];
                $tmp[$index]['no_dtd'] = $value['no_dtd'];
                $tmp[$index]['note_dtd'] = $value['dtd_noterperinci'];
                $tmp[$index]['diagnosa_nama'] = $value['diagnosa_nama'];
                $tmp[$index]['lk'] = $value['jml_laki'];
                $tmp[$index]['pr'] = $value['jml_perempuan'];
                $tmp[$index]['keluar_hidup'] = $value['jml_laki_perempuan'];
                $tmp[$index]['keluar_mati'] = $value['jml_laki_perempuan_mati'];

                $tmp[$index]['0_-_≤6_hari_LAKI']  = $value['pasien_0_6_hari_laki'];
                $tmp[$index]['0_-_≤6_hari_PEREMPUAN']  = $value['pasien_0_6_hari_perempuan'];
                $tmp[$index]['>6_-_≤28_hari_LAKI']  = $value['pasien_7_28_hari_laki'];
                $tmp[$index]['>6_-_≤28_hari_PEREMPUAN']  = $value['pasien_7_28_hari_perempuan'];
                $tmp[$index]['>28_-_≤1_tahun_LAKI']  = $value['pasien_29_365_hari_laki'];
                $tmp[$index]['>28_-_≤1_tahun_PEREMPUAN']  = $value['pasien_29_365_hari_perempuan'];
                $tmp[$index]['>1-_≤4_tahun_LAKI']  = $value['pasien_1_4th_laki'];
                $tmp[$index]['>1-_≤4_tahun_PEREMPUAN']  = $value['pasien_1_4th_perempuan'];
                $tmp[$index]['>4_-_≤_14_tahun_LAKI']  = $value['pasien_5_14th_laki'];
                $tmp[$index]['>4_-_≤_14_tahun_PEREMPUAN']  = $value['pasien_5_14th_perempuan'];
                $tmp[$index]['>14_-_≤24_tahun_LAKI']  = $value['pasien_15_24th_laki'];
                $tmp[$index]['>14_-_≤24_tahun_PEREMPUAN']  = $value['pasien_15_24th_perempuan'];
                $tmp[$index]['>24_-_≤_44_tahun_LAKI']  = $value['pasien_25_44th_laki'];
                $tmp[$index]['>24_-_≤_44_tahun_PEREMPUAN']  = $value['pasien_25_44th_perempuan'];
                $tmp[$index]['>44_-_≤_64_tahun_LAKI']  = $value['pasien_45_64th_laki'];
                $tmp[$index]['>44_-_≤_64_tahun_PEREMPUAN']  = $value['pasien_45_64th_perempuan'];
                $tmp[$index]['>_64_tahun_LAKI']  = $value['pasien_65th_laki'];
                $tmp[$index]['>_64_tahun_PEREMPUAN']  = $value['pasien_65th_perempuan'];
                $index++;
            }
            $no = 1;
            foreach($tmp as $k => $v) {
                if($v['keluar_hidup'] != 0 || $v['keluar_mati'] != 0) {
                    $v['no'] = $no++;
                    $data[] = $v;
                }
            }
            return new ArrayDataProvider([
                'allModels' => $data, 
                'pagination' => [
                    'pageSize' => $request->get('per-page'),
                ],
            ]);
        } else {
            foreach($header as $k => $v) {
                $visible = true;
                $search = false;
                $title = '';
                $nameLaki = substr($k, -5);
                $nameCewe = substr($k, -10);

                switch ($k) {
                    case 'diagnosa_id':
                        $title = 'Diagnosa';
                        $visible = false;
                        $search = true;
                        break;
                    case 'no_dtd':
                        $title = 'No. DTD';
                        break;
                    case 'note_dtd':
                        $title = 'No. Daftar terperinci';
                        break;
                    case 'diagnosa_nama':
                        $title = 'Golongan sebab penyakit';
                        break;
                    case 'lk':
                        $title = 'LK';
                        break;
                    case 'pr':
                        $title = 'PR';
                        break;
                    case 'keluar_hidup':
                        $title = 'Jumlah Pasien Keluar Hidup (23 + 24)';
                        break;
                    case 'keluar_mati':
                        $title = 'Jumlah Pasien Keluar mati';
                        break;
                    default:
                        $title =  $k;
                        break;
                }

                if($nameLaki == self::_LAKI) {
                    $title = 'L';
                }

                if($nameCewe == self::_PEREMPUAN) {
                    $title = 'P';
                }

                $columns[] =[
                    'title' => ucfirst($title),
                    'data' => $k,
                    'searchable' => $search,
                    'orderable' => false,
                    'visible' => $visible,
                ];
            }
        }
        return [
            'header' => $tmpDataHeader,
            'columns' => $columns,
            'data' => $data,
            'profil' => $this->getProfil()
        ];
    }

    public function actionGetDataMorbiditasRanap()
    {
        $data =[];
        $request = Yii::$app->request;
        $getData = $request->get();
        // Yii::error($request);
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $data['status'] = 'getData';

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'laporan-morbiditas-ranap',
            'message' => json_encode($data),
        ]);

        return [
            'message' => 'berhasil',
            'token' => $auth,
            'xOwner' => $xOwner,
            'filter' => $getData,
        ];
    }

    protected function laporanMorbiditasRajal()
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 300);
        $request = Yii::$app->request;
        $listUmur = $tmp = $tmpDataHeader = $listGolongan = $data = $header = $columns = [];

        $staticHeaderAwal = [
            'no' => 'no',
            'no_dtd' => 'no_dtd',
            'note_dtd' => 'note_dtd',
            'diagnosa_nama' => 'diagnosa_nama',
        ];
        $staticHeaderAkhir = [
            'lk' => 'lk',
            'pr' => 'pr',
            'jml_kasus_baru' => 'jml_kasus_baru',
            'jml_kunjungan' => 'jml_kunjungan',
        ];
        
        $qryUmur = "
            SELECT
                golonganumur_id,
                golonganumur_nama,
                golonganumur_namalainnya
            FROM rl4_b_morbiditasrawatjalan_v
            WHERE kolom='horizontal'
            ORDER BY golonganumur_id
        ";
        $listUmur = Yii::$app->db->createCommand("{$qryUmur} ")->queryAll();

        foreach($listUmur as $w => $x) {
            $golUmurNama = str_replace(' ', '_', $x['golonganumur_namalainnya']);

            if(!isset($listGolongan[$golUmurNama.self::_LAKI])){
                $listGolongan[$golUmurNama.self::_LAKI] = null;
            } 
            if(!isset($listGolongan[$golUmurNama.self::_PEREMPUAN])){
                $listGolongan[$golUmurNama.self::_PEREMPUAN] = null;
            }

            /** Set Header by golongan umur */
            if(!isset($tmpDataHeader[$x['golonganumur_namalainnya']])){
                $tmpDataHeader[$x['golonganumur_namalainnya']] =  $x['golonganumur_namalainnya'];
            }
        }

        /** Set Datatable Column */
        $header = array_merge($staticHeaderAwal, $listGolongan);
        $header = array_merge($header, $staticHeaderAkhir);
        foreach($header as $k => $v) {
            $visible = true;
            $search = false;
            $title = '';
            $nameLaki = substr($k, -5);
            $nameCewe = substr($k, -10);
    
            /** Set column title */
            switch ($k) {
                case 'no_dtd':
                    $title = 'No. DTD';
                    break;
                case 'note_dtd':
                    $title = 'No. Daftar terperinci';
                    break;
                case 'diagnosa_nama':
                    $title = 'Golongan sebab penyakit';
                    break;
                case 'lk':
                    $title = 'LK';
                    break;
                case 'pr':
                    $title = 'PR';
                    break;
                case 'jml_kasus_baru':
                    $title = 'Jumlah Kasus Baru (23 + 24)';
                    break;
                case 'jml_kunjungan':
                    $title = 'Jumlah Kunjungan';
                    break;
                default:
                    $title =  $k;
                    break;
            }

            /** Column title for golongan umur */
            if($nameLaki == self::_LAKI) {
                $title = 'L';
            }
            if($nameCewe == self::_PEREMPUAN) {
                $title = 'P';
            }
    
            $columns[] =[
                'title' => ucfirst($title),
                'data' => $k,
                'searchable' => $search,
                'orderable' => false,
                'visible' => $visible,
            ];
        }

        return [
            'header' => $tmpDataHeader,
            'columns' => $columns,
            'data' => $data,
            'profil' => $this->getProfil()
        ];
    }

    protected function laporanRl_3_1()
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun');
        $tahun_sebelum = (int)$tahun - 1;

        $header = RLKegiatanRawatInap::find()->all();
        $queryDetail = RLKegiatanRawatInapDetail::find()->select([
            'count(pendaftaran_id) as pasien_masuk',
            'sum(CASE
                     WHEN carakeluar_id IS NOT NULL AND carakeluar_id != 4 THEN 1
                     ELSE 0
                 END) AS pasien_keluar_hidup',
            'sum(CASE
                     WHEN carakeluar_id = 4 THEN 1
                     ELSE 0
                END) AS pasien_keluar_mati',
            'sum(CASE
                 WHEN carakeluar_id = 4 AND kondisikeluar_id IN(7,6)  THEN 1
                 ELSE 0
                END) AS pasien_keluar_mati_under48',
            'sum(CASE
                 WHEN carakeluar_id = 4 AND kondisikeluar_id =5  THEN 1
                 ELSE 0
                END) AS pasien_keluar_mati_48',
            'sum(CASE
                 WHEN lama_rawat IS NOT NULL AND tglpasienpulang IS NOT NULL THEN lama_rawat
                 ELSE 0
                END) AS lama_dirawat',
            'sum(CASE
                     WHEN tglpasienpulang IS NOT NULL THEN lama_rawat
                     WHEN tgl_admisi IS NOT NULL THEN date_part(\'day\', now()-tgl_admisi)
                     ELSE 0
             END) AS lama_hari_rawat',
            'jeniskasuspenyakit_id',
            'jeniskasuspenyakit_nama',
            'kelaspelayanan_id'
        ]);
        $queryDetail->groupBy(['kelaspelayanan_id','jeniskasuspenyakit_id','jeniskasuspenyakit_nama']);

        if (!empty($tahun)) {
            $queryDetail->where("date_part('year',tgl_admisi) = '{$tahun}'");
        }
        $resQueryDetail = $queryDetail->asArray()->all();

        $queryAwalAkhir = RLKegiatanRawatInapDetail::find()->select([
            "sum(CASE
                       WHEN date_part('year',tgl_admisi) = '{$tahun_sebelum}' AND (tglpasienpulang IS NULL OR date_part('year',tglpasienpulang) > '{$tahun_sebelum}') THEN 1
                       ELSE 0
               END) AS pasien_awal_tahun",
            "sum(CASE
                       WHEN date_part('year',tgl_admisi) = '{$tahun}' AND (tglpasienpulang IS NULL OR date_part('year',tglpasienpulang) > '{$tahun}') THEN 1
                       ELSE 0
               END) AS pasien_akhir_tahun",
            'jeniskasuspenyakit_id',
            'jeniskasuspenyakit_nama',
            'kelaspelayanan_id'
        ])->groupBy(['kelaspelayanan_id','jeniskasuspenyakit_id','jeniskasuspenyakit_nama']);
        $resQueryAwalAkhir = $queryAwalAkhir->asArray()->all();
        $dataDetail = $dataPelayanan = $listJenisKasusPenyakit = [];

        foreach ($resQueryAwalAkhir as $val_query_akhir) {
            $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_awal_tahun'] = isset($dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_awal_tahun']) ? $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_awal_tahun']+$val_query_akhir['pasien_awal_tahun']: $val_query_akhir['pasien_awal_tahun'];
            $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_akhir_tahun'] = isset($dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_akhir_tahun']) ? $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_akhir_tahun']+$val_query_akhir['pasien_akhir_tahun']: $val_query_akhir['pasien_akhir_tahun'];
        }
        foreach ($resQueryDetail as $value) {
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_masuk'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_masuk']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_masuk']+$value['pasien_masuk']: $value['pasien_masuk'];
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_hidup'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_hidup']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_hidup']+$value['pasien_keluar_hidup']: $value['pasien_keluar_hidup'];
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati']+$value['pasien_keluar_mati']: $value['pasien_keluar_mati'];
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_48'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_48']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_48']+$value['pasien_keluar_mati_48']: $value['pasien_keluar_mati_48'];
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_under48'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_under48']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_under48']+$value['pasien_keluar_mati_under48']: $value['pasien_keluar_mati_under48'];
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_dirawat'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_dirawat']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_dirawat']+$value['lama_dirawat']: $value['lama_dirawat'];
            $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_hari_rawat'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_hari_rawat']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_hari_rawat']+$value['lama_hari_rawat']: $value['lama_hari_rawat'];
            $dataDetail[$value['jeniskasuspenyakit_id']][$value['kelaspelayanan_id']] = $value['lama_hari_rawat'];
        }

        $resultHeader = $resultVer = [];
        foreach ($header as $value) {
            if ($value->kolom === "vertikal") {
                $resultVer[$value->jeniskasuspenyakit_id] = $value->jeniskasuspenyakit_nama;
                continue;
            }
            $resultHeader[$value->kelaspelayanan_id] = $value->kelaspelayanan_nama;
        }
        return [
            'header' => $resultHeader,
            'list' => $resultVer,
            'detail' => $dataDetail,
            'data-pelayanan' => $dataPelayanan,
            'profil' => $this->getProfil()
        ];
    }

    protected function laporanRujukan()
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun', date("Y"));
        $bulan = $request->get('bulan', date("m")) ?: null;
        $bulan = strlen($bulan) == 1 ? "0" . $bulan : $bulan;

        // Defined Variable First
        $data_jenis_penyakit = [];
        $asalRujukan = [];

        $subQuery = (new \yii\db\Query())
            ->select([
                "a.jeniskasuspenyakit_id",
                "b.jeniskasuspenyakit_nama",
                "d.asalrujukan_id",
                "d.asalrujukan_nama",
                "count(a.jeniskasuspenyakit_id) AS jumlah",
                "to_char(a.tgl_pendaftaran, 'MM'::text)::character varying AS bulan",
                "to_char(a.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun"
            ])
            ->from('pendaftaran_t a')
            ->join('INNER JOIN', 'jeniskasuspenyakit_m b', 'b.jeniskasuspenyakit_id = a.jeniskasuspenyakit_id')
            ->join('INNER JOIN', 'rujukan_t c', 'c.rujukan_id = a.rujukan_id')
            ->join('INNER JOIN', 'asalrujukan_m d', 'd.asalrujukan_id = c.asalrujukan_id')
            ->where([
                'a.is_deleted' => false,
                'b.is_deleted' => false,
            ])
            ->andWhere(['not', ['a.rujukan_id' => null]])
            ->groupBy([
                "a.jeniskasuspenyakit_id",
                "b.jeniskasuspenyakit_nama",
                "(to_char(a.tgl_pendaftaran, 'YYYY'::text))",
                "(to_char(a.tgl_pendaftaran, 'MM'::text)), d.asalrujukan_id",
                "d.asalrujukan_nama"
            ]);

            $query = (new \yii\db\Query())->select(["*"])
                                          ->from(["x" => $subQuery]);
            if(!empty($bulan) && $bulan != null){
                $query = $query->where(["bulan" => $bulan]);
            }

            $query = $query->where(["tahun" => $tahun])->all();

        if(!empty($query)) {
            $jenisPenyakit = JenisKasusPenyakit::find()->where(["is_deleted" => false])->all();
            $asalRujukan = AsalRujukan::find()->where(["is_deleted" => false])->all();
            foreach ($jenisPenyakit as $K => $v) {
                $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["nama"] = $v->jeniskasuspenyakit_nama;
                foreach ($asalRujukan as $o => $p) {
                    $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["rujukan"][$p->asalrujukan_id] = ["nama" => $p->asalrujukan_nama, "data" => []];
                    foreach ($query as $m => $n) {
                        if($n["jeniskasuspenyakit_id"] == $v->jeniskasuspenyakit_id) {
                            if($n["asalrujukan_id"] == $p->asalrujukan_id) {
                                $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["rujukan"][$p->asalrujukan_id] = ["nama" => $p->asalrujukan_nama, "data" => $n];
                            } else {
                                $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["rujukan"][$p->asalrujukan_id] = ["nama" => $p->asalrujukan_nama, "data" => []];
                            }
                        }
                    }
                }
            }
        }

         return [
             'data-rujukan' => $data_jenis_penyakit,
             'asal-rujukan' => $asalRujukan,
             'profil' => $this->getProfil(),
         ];
    }

    protected function laporanRl_5_4()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan',null);
        $tahun = $request->get('tahun',null);
        $tahun_sebelum = (int)$tahun - 1;
        try {
            $dataLaporan = [];
            $datasource5_4 = RL5_4::find();

            if ($bulan) {
                if ($bulan >= 1 && $bulan <= 9) {
                    $bulan = '0'.$bulan;
                }

                $datasource5_4->andWhere(['bulan' => $bulan]);
            }

            if ($tahun) {
                $datasource5_4->andWhere(['tahun' => $tahun]);
            }

            $datasource5_4 = $datasource5_4->limit(10)->asArray()->all();
            foreach ($datasource5_4 as $v_source5_4) {
                $temp5_4['kodeicd'] = $v_source5_4['diagnosa_kode'];
                $temp5_4['deskripsi'] = $v_source5_4['diagnosa_nama'];
                $temp5_4['kasusbaru_laki'] = $v_source5_4['jml_laki'];
                $temp5_4['kasusbaru_perempuan'] = $v_source5_4['jml_perempuan'];
                $temp5_4['jumlahkasusbaru'] = $v_source5_4['jml_laki'] + $v_source5_4['jml_perempuan'];
                $temp5_4['jumlahkunjungan'] = $v_source5_4['jumlah'];
                $dataLaporan[] = $temp5_4;
            }
        } catch (\Exception $e) {
            $dataLaporan = [];
        }
        return [
            'datarl5_4' => $dataLaporan,
            'profil' => $this->getProfil()
        ];
    }

    protected function laporanRadiologi()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');

        $query = "
            SELECT SUM(qty_tindakan) AS qty_tindakan,
            nama_kelompok,
            jenispemeriksaanrad_nama,
            kelompokpemeriksaanrad_id,
            jenispemeriksaanrad_id
            FROM rl3_7_radiologidetail_v
            WHERE (EXTRACT(year FROM tgl_tindakan) = '{$tahun}')";
        if (!empty($bulan)) {
            $query .= " AND (EXTRACT(month FROM tgl_tindakan)= '{$bulan}')";
        }

        $detail = Yii::$app->db->createCommand("{$query} GROUP BY nama_kelompok,
            jenispemeriksaanrad_nama,
            kelompokpemeriksaanrad_id,
            jenispemeriksaanrad_id")->queryAll();

        $header = Yii::$app->db->createCommand("
            SELECT * FROM rl3_7_radiologi_v
        ")->queryAll();

        $dataDetail = [];
        foreach ($header as $key => $value) {
            $kelompokId = $value['kelompokpemeriksaanrad_id'];
            $jenisId = $value['jenispemeriksaanrad_id'];
            if (empty($jenisId)) continue;
            if (!isset($dataDetail[$kelompokId])) {
                $dataDetail[$kelompokId] = [
                    'label' => $value['nama_kelompok'],
                    'data' => [],
                ];
            }

            if (!isset($dataDetail[$kelompokId]['data'][$jenisId])) {
                $dataDetail[$kelompokId]['data'][$jenisId] = [
                    'label' => $value['jenispemeriksaanrad_nama'],
                    'qty' => 0
                ];
            }
        }

        foreach ($detail as $value) {
            $kelompokId = $value['kelompokpemeriksaanrad_id'];
            $jenisId = $value['jenispemeriksaanrad_id'];
            if (!isset($dataDetail[$kelompokId])) {
                $dataDetail[$kelompokId] = [
                    'label' => $value['nama_kelompok'],
                    'data' => [],
                ];
            }

            if (!isset($dataDetail[$kelompokId]['data'][$jenisId])) {
                $dataDetail[$kelompokId]['data'][$jenisId] = [
                    'label' => $value['jenispemeriksaanrad_nama'],
                    'qty' => 0
                ];
            }

            $dataDetail[$kelompokId]['data'][$jenisId]['qty'] += $value['qty_tindakan'];

        }

        return [
            'detail' => $dataDetail
        ];
    }

    protected function laporanLaboratorium()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan');
        $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');

        $query = "
            SELECT SUM(qty_tindakan) AS qty_tindakan,
            ruangan_id,
            jenispemeriksaanlab_id,
            kelompokpemeriksaanlab_id,
            pemeriksaanlab_id
            FROM rl3_8_laboratoriumdetail_v
            WHERE (EXTRACT(year FROM tgl_tindakan) = '{$tahun}')";
        if (!empty($bulan)) {
            $query .= " AND (EXTRACT(month FROM tgl_tindakan)= '{$bulan}')";
        }

        $detail = Yii::$app->db->createCommand("{$query} GROUP BY ruangan_id,
            jenispemeriksaanlab_id,
            kelompokpemeriksaanlab_id,
            pemeriksaanlab_id")->queryAll();

        $header = Yii::$app->db->createCommand("
            SELECT * FROM rl3_8_laboratorium_v
        ")->queryAll();

        $dataDetail = [];
        foreach ($header as $key => $value) {
            $ruanganId = $value['ruangan_id'];
            $kelompokId = $value['kelompokpemeriksaanlab_id'];
            $jenisPemeriksaanId = $value['jenispemeriksaanlab_id'];
            $pemeriksaanLabId = $value['pemeriksaanlab_id'];

            if (!isset($dataDetail[$ruanganId])) {
                $dataDetail[$ruanganId] = [
                    'label' => $value['ruangan_nama'],
                    'data' => [],
                ];
            }

            if (!isset($dataDetail[$ruanganId]['data'][$kelompokId])) {
                $dataDetail[$ruanganId]['data'][$kelompokId] = [
                    'label' => $value['nama_kelompok'],
                    'data' => [],
                ];
            }

            if (!isset($dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId])) {
                $dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId] = [
                    'label' => $value['jenispemeriksaanlab_nama'],
                    'data' => [],
                ];
            }
            $dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId]['data'][$pemeriksaanLabId] = [
                    'label' => $value['pemeriksaanlab_nama'],
                    'qty' => 0
            ];
        }
        foreach ($detail as $value) {
            $ruanganId = $value['ruangan_id'];
            $kelompokId = $value['kelompokpemeriksaanlab_id'];
            $jenisPemeriksaanId = $value['jenispemeriksaanlab_id'];
            $pemeriksaanLabId = $value['pemeriksaanlab_id'];
            if (isset($dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId]['data'][$pemeriksaanLabId])) {
                $dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId]['data'][$pemeriksaanLabId]['qty']
                        += $value['qty_tindakan'];
            }
        }

        return [
            'detail' => $dataDetail
        ];
    }

    protected function excelDataRs()
    {
        try {
            $title = "Formulir RL 1.1 Data Dasar Rumah Sakit";
            $header = ['Tanggal' => date('d F Y')];

            $contents = RLDataRs::find()->asArray()->one();

            $dateFormat = [
                'Tanggal Registrasi',
                '10.2  Tanggal',
                '10.5  Masa Berlaku s/d thn',
                '12.3  Tanggal Akreditasi'
            ];
            foreach ($contents as $key => $value) {
                if (in_array($key, $dateFormat)) {
                    $contents[$key] = date('d F Y', strtotime($value));
                }
            }

            // jumlah tempat tidur
            $contents['Jumlah Tempat Tidur'] = '';
            $tt = RLDataRsTempatTidur::find()->asArray()->all();
            $noUrut = '13.';
            $count = 1;
            foreach ($tt as $each) {
                $contents[$noUrut . $count++ . ' ' . $each['kelaspelayanan_nama']] = $each['jumlah_tempattidur'];
            }
            // jumlah tenaga medis
            $contents['Jumlah Tenaga Medis'] = '';
            $tenagaMedis = RLDataRsPegawai::find()
                ->andWhere(['!=', 'pendkualifikasi_nama', 'Tenaga Non Kesehatan'])
                ->asArray()->all();
            $noUrut = '14.';
            $count = 1;
            foreach ($tenagaMedis as $each) {
                $contents[$noUrut . $count++ . ' ' . $each['pendkualifikasi_nama']] = $each['total'];
            }

            $tenagaMedisLain = RLDataRsPegawai::find()
                ->andWhere(['pendkualifikasi_nama' => 'Tenaga Non Kesehatan'])
                ->asArray()->one();
            $contents[$tenagaMedisLain['pendkualifikasi_nama']] = $tenagaMedisLain['total'];

            $no = 1;
            $row = [];
            foreach ($contents as $key => $content) {
                $row['No.'] = !ctype_digit($key[0]) ? $no++ : '';
                $row['Konten'] = $key;
                $row['value'] = $content;
                $rows[] = $row;
            }

            // return $rows;

            $footerInfo = [
                3, [
                    'CP Pengisi',
                    'Nama',
                    'Jabatan',
                    'Email',
                    'No. Telp',
                    'Tanggal',
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $rows, $header, [
                "uploadPath" => "./uploads",
                "skipIncrement" => true,
                "skipHeader" => true,
            ],[],$footerInfo,true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelLaporanIndikatorRs()
    {
        $request = Yii::$app->request;
        $tahun   = $request->get('tahun', null);
        $title   = Yii::t('app', 'Formulir RL 1.2  Indikator Pelayanan Rumah Sakit');
        $row     = $profil = $footer = [];
        try {
            $resQueryDetail = LaporanIndikatorRsView::find()->orderBy([
                "tahun"=>SORT_DESC
            ])->all();
            $header = [];

            foreach ($resQueryDetail as $key => $value) :
                $row[$key]['Tahun']                    = $value['tahun'] . ' ';
                $row[$key]['BOR']                      = $value['bor'] . ' ';
                $row[$key]['LOS']                      = $value['avlos'] . ' ';
                $row[$key]['BTO']                      = $value['bto'] . ' ';
                $row[$key]['TOI']                      = $value['toi'] . ' ';
                $row[$key]['NDR']                      = $value['ndr'] . ' ';
                $row[$key]['GDR']                      = $value['gdr'] . ' ';
                $row[$key]['Rata-rata Kunjungan/Hari'] = $value['avg_kunjungan'] . ' ';

                /*$tmp[1] = $value['tahun'];
                $tmp[2] = $value['bor'] . ' ';
                $tmp[3] = $value['avlos'] . ' ';
                $tmp[4] = $value['bto'] . ' ';
                $tmp[5] = $value['toi'] . ' ';
                $tmp[6] = $value['ndr'] . ' ';
                $tmp[7] = $value['gdr'] . ' ';
                $tmp[8] = $value['avg_kunjungan'] . ' ';
                $row[]  = $tmp;*/
            endforeach;

        } catch (Exception $e) {
            $header = [];
            $row = [];
        }
        $custHeader = [
                [
                    [
                        'label'=>'Tahun',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'BOR (%)',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'LOS   ',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'BTO   ',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'TOI   ',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'NDR (%)',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'GDR (%)',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Rata-rata Kunjungan/Hari',
                        'rowspan'=>2,
                    ]
                ]
            ];
        $filePath = DocoHelpers::exportExcel($title, $row, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    protected function excelRl_3_1()
    {
        try {
            $request = Yii::$app->request;
            $tahun = $request->get('tahun','2018');
            $tahun_sebelum = (int)$tahun - 1;
            $textBulan = null;

            $header = RLKegiatanRawatInap::find()->all();
            $queryDetail = RLKegiatanRawatInapDetail::find()->select([
                'count(pendaftaran_id) as pasien_masuk',
                'sum(CASE
                         WHEN carakeluar_id IS NOT NULL AND carakeluar_id != 4 THEN 1
                         ELSE 0
                     END) AS pasien_keluar_hidup',
                'sum(CASE
                         WHEN carakeluar_id = 4 THEN 1
                         ELSE 0
                    END) AS pasien_keluar_mati',
                'sum(CASE
                     WHEN carakeluar_id = 4 AND kondisikeluar_id IN(7,6)  THEN 1
                     ELSE 0
                    END) AS pasien_keluar_mati_under48',
                'sum(CASE
                     WHEN carakeluar_id = 4 AND kondisikeluar_id =5  THEN 1
                     ELSE 0
                    END) AS pasien_keluar_mati_48',
                'sum(CASE
                     WHEN lama_rawat IS NOT NULL AND tglpasienpulang IS NOT NULL THEN lama_rawat
                     ELSE 0
                    END) AS lama_dirawat',
                'sum(CASE
                         WHEN tglpasienpulang IS NOT NULL THEN lama_rawat
                         WHEN tgl_admisi IS NOT NULL THEN date_part(\'day\', now()-tgl_admisi)
                         ELSE 0
                 END) AS lama_hari_rawat',
                'jeniskasuspenyakit_id',
                'jeniskasuspenyakit_nama',
                'kelaspelayanan_id'
            ]);
            $queryDetail->groupBy(['kelaspelayanan_id','jeniskasuspenyakit_id','jeniskasuspenyakit_nama']);

            if (!empty($tahun)) {
                $queryDetail->where("date_part('year',tgl_admisi) = '{$tahun}'");
            }
            $resQueryDetail = $queryDetail->asArray()->all();

            $queryAwalAkhir = RLKegiatanRawatInapDetail::find()->select([
                "sum(CASE
                           WHEN date_part('year',tgl_admisi) = '{$tahun_sebelum}' AND (tglpasienpulang IS NULL OR date_part('year',tglpasienpulang) > '{$tahun_sebelum}') THEN 1
                           ELSE 0
                   END) AS pasien_awal_tahun",
                "sum(CASE
                           WHEN date_part('year',tgl_admisi) = '{$tahun}' AND (tglpasienpulang IS NULL OR date_part('year',tglpasienpulang) > '{$tahun}') THEN 1
                           ELSE 0
                   END) AS pasien_akhir_tahun",
                'jeniskasuspenyakit_id',
                'jeniskasuspenyakit_nama',
                'kelaspelayanan_id'
            ])->groupBy(['kelaspelayanan_id','jeniskasuspenyakit_id','jeniskasuspenyakit_nama']);
            $resQueryAwalAkhir = $queryAwalAkhir->asArray()->all();
            $dataDetail = $dataPelayanan = $listJenisKasusPenyakit = [];

            foreach ($resQueryAwalAkhir as $val_query_akhir) {
                $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_awal_tahun'] = isset($dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_awal_tahun']) ? $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_awal_tahun']+$val_query_akhir['pasien_awal_tahun']: $val_query_akhir['pasien_awal_tahun'];
                $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_akhir_tahun'] = isset($dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_akhir_tahun']) ? $dataPelayanan[$val_query_akhir['jeniskasuspenyakit_id']]['pasien_akhir_tahun']+$val_query_akhir['pasien_akhir_tahun']: $val_query_akhir['pasien_akhir_tahun'];
            }

            foreach ($resQueryDetail as $value) {
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_masuk'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_masuk']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_masuk']+$value['pasien_masuk']: $value['pasien_masuk'];
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_hidup'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_hidup']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_hidup']+$value['pasien_keluar_hidup']: $value['pasien_keluar_hidup'];
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati']+$value['pasien_keluar_mati']: $value['pasien_keluar_mati'];
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_48'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_48']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_48']+$value['pasien_keluar_mati_48']: $value['pasien_keluar_mati_48'];
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_under48'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_under48']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['pasien_keluar_mati_under48']+$value['pasien_keluar_mati_under48']: $value['pasien_keluar_mati_under48'];
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_dirawat'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_dirawat']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_dirawat']+$value['lama_dirawat']: $value['lama_dirawat'];
                $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_hari_rawat'] = isset($dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_hari_rawat']) ? $dataPelayanan[$value['jeniskasuspenyakit_id']]['lama_hari_rawat']+$value['lama_hari_rawat']: $value['lama_hari_rawat'];
                $dataDetail[$value['jeniskasuspenyakit_id']][$value['kelaspelayanan_id']] = $value['lama_hari_rawat'];
            }

            $resultHeader = $resultVer = [];
            foreach ($header as $value) {
                if ($value->kolom === "vertikal") {
                    $resultVer[$value->jeniskasuspenyakit_id] = $value->jeniskasuspenyakit_nama;
                    continue;
                }
                $resultHeader[$value->kelaspelayanan_id] = $value->kelaspelayanan_nama;
            }
            $title = "Formulir RL 3.1 Kegiatan Pelayanan Rawat Inap";
            $row = $dataFoot = [];
            $no = 1;
            asort($resultVer);
            $kelas_foot = [];
            foreach ($resultVer as $key_list => $val_list) {
                $no = 1;
                $newRow = [
                    'NO' => null,
                    'JENIS PELAYANAN' => $val_list,
                    'PASIEN AWAL TAHUN' => isset($dataPelayanan[$key_list]['pasien_awal_tahun'])?$dataPelayanan[$key_list]['pasien_awal_tahun']:0,
                    'PASIEN MASUK' => isset($dataPelayanan[$key_list]['pasien_masuk'])?$dataPelayanan[$key_list]['pasien_masuk']:0,
                    'PASIEN KELUAR HIDUP' => isset($dataPelayanan[$key_list]['pasien_keluar_hidup'])?$dataPelayanan[$key_list]['pasien_keluar_hidup']:0,
                    '<48' => isset($dataPelayanan[$key_list]['pasien_keluar_mati_under48'])?$dataPelayanan[$key_list]['pasien_keluar_mati_under48']:0,
                    '>48' => isset($dataPelayanan[$key_list]['pasien_keluar_mati_48'])?$dataPelayanan[$key_list]['pasien_keluar_mati_48']:0,
                    'JUMLAH LAMA DIRAWAT' => isset($dataPelayanan[$key_list]['lama_dirawat'])?$dataPelayanan[$key_list]['lama_dirawat']:0,
                    'PASIEN AKHIR TAHUN' => isset($dataPelayanan[$key_list]['pasien_akhir_tahun'])?$dataPelayanan[$key_list]['pasien_akhir_tahun']:0,
                    'JUMLAH HARI PERAWATAN' => isset($dataPelayanan[$key_list]['lama_hari_rawat'])?$dataPelayanan[$key_list]['lama_hari_rawat']:0,
                ];
                $dataFoot = [
                    'PASIEN AWAL TAHUN' => isset($dataFoot['PASIEN AWAL TAHUN'])?$dataFoot['PASIEN AWAL TAHUN'] += $newRow['PASIEN AWAL TAHUN']:0,
                    'PASIEN MASUK' => isset($dataFoot['PASIEN MASUK'])?$dataFoot['PASIEN MASUK'] += $newRow['PASIEN MASUK']:0,
                    'PASIEN KELUAR HIDUP' => isset($dataFoot['PASIEN KELUAR HIDUP'])?$dataFoot['PASIEN KELUAR HIDUP'] += $newRow['PASIEN KELUAR HIDUP']:0,
                    '<48' => isset($dataFoot['<48'])?$dataFoot['<48'] += $newRow['<48']:0,
                    '>48' => isset($dataFoot['>48'])?$dataFoot['>48'] += $newRow['>48']:0,
                    'JUMLAH LAMA DIRAWAT' => isset($dataFoot['JUMLAH LAMA DIRAWAT'])?$dataFoot['JUMLAH LAMA DIRAWAT'] += $newRow['JUMLAH LAMA DIRAWAT']:0,
                    'PASIEN AKHIR TAHUN' => isset($dataFoot['PASIEN AKHIR TAHUN'])?$dataFoot['PASIEN AKHIR TAHUN'] += $newRow['PASIEN AKHIR TAHUN']:0,
                    'JUMLAH HARI PERAWATAN' => isset($dataFoot['JUMLAH HARI PERAWATAN'])?$dataFoot['JUMLAH HARI PERAWATAN'] += $newRow['JUMLAH HARI PERAWATAN']:0,
                ];
                $newRowHeader=[];
                foreach ($resultHeader as $k_header => $v_header) {
                    $newRowHeader[$v_header]=isset($dataDetail[$key_list][$k_header]) ? $dataDetail[$key_list][$k_header] : 0;
                    if (!isset($kelas_foot[$v_header])) {
                        $kelas_foot[$v_header] = 0;
                    }
                    $kelas_foot[$v_header] += $newRowHeader[$v_header];
                }
                $dataFoot = array_merge($dataFoot,$kelas_foot);
                $newRow = array_merge($newRow,$newRowHeader);
                $row[] = $newRow;
            }
            $footer = [
                'title'=> ['Total', 1],
                'data'=> $dataFoot
            ];
            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Tahun' => $tahun,
            ];

            $filePath = DocoHelpers::exportExcel($title, $row, $header, array(
                "uploadPath" => "./uploads"
            ),$footer,[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelRujukan()
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun', date("Y"));
        $bulan = $request->get('bulan', date("m"));
        $bulan = strlen($bulan) == 1 ? "0" . $bulan : $bulan;

        $data_jenis_penyakit = [];

        $subQuery = (new \yii\db\Query())
            ->select([
                "a.jeniskasuspenyakit_id",
                "b.jeniskasuspenyakit_nama",
                "d.asalrujukan_id",
                "d.asalrujukan_nama",
                "count(a.jeniskasuspenyakit_id) AS jumlah",
                "to_char(a.tgl_pendaftaran, 'MM'::text)::character varying AS bulan",
                "to_char(a.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun"
            ])
            ->from('pendaftaran_t a')
            ->join('INNER JOIN', 'jeniskasuspenyakit_m b', 'b.jeniskasuspenyakit_id = a.jeniskasuspenyakit_id')
            ->join('INNER JOIN', 'rujukan_t c', 'c.rujukan_id = a.rujukan_id')
            ->join('INNER JOIN', 'asalrujukan_m d', 'd.asalrujukan_id = c.asalrujukan_id')
            ->where([
                'a.is_deleted' => false,
                'b.is_deleted' => false,
            ])
            ->andWhere(['not', ['a.rujukan_id' => null]])
            ->groupBy([
                "a.jeniskasuspenyakit_id",
                "b.jeniskasuspenyakit_nama",
                "(to_char(a.tgl_pendaftaran, 'YYYY'::text))",
                "(to_char(a.tgl_pendaftaran, 'MM'::text)), d.asalrujukan_id",
                "d.asalrujukan_nama"
            ]);

        $query = (new \yii\db\Query())->select(["*"])->from(["x" => $subQuery])->where(["bulan" => $bulan, "tahun" => $tahun])->all();

        if(!empty($query)) {
            $jenisPenyakit = JenisKasusPenyakit::find()->where(["is_deleted" => false])->all();
            $asalRujukan = AsalRujukan::find()->where(["is_deleted" => false])->all();
            foreach ($jenisPenyakit as $K => $v) {
                $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["nama"] = $v->jeniskasuspenyakit_nama;
                foreach ($asalRujukan as $o => $p) {
                    $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["rujukan"][$p->asalrujukan_id] = ["nama" => $p->asalrujukan_nama, "data" => []];
                    foreach ($query as $m => $n) {
                        if($n["jeniskasuspenyakit_id"] == $v->jeniskasuspenyakit_id) {
                            if($n["asalrujukan_id"] == $p->asalrujukan_id) {
                                $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["rujukan"][$p->asalrujukan_id] = ["nama" => $p->asalrujukan_nama, "data" => $n];
                            } else {
                                $data_jenis_penyakit[$v->jeniskasuspenyakit_id]["rujukan"][$p->asalrujukan_id] = ["nama" => $p->asalrujukan_nama, "data" => []];
                            }
                        }
                    }
                }
            }
            $data_excel = $row = [];
            foreach ($data_jenis_penyakit as $k => $v) {
                $data_excel = [];
                $data_excel['JENIS_PENYAKIT'] = $v['nama'];
                foreach ($v['rujukan'] as $kr => $vr) {
                    $nama = strtoupper($vr['nama']);
                    if(isset($data_excel[$nama])) {
                        $data_excel[$nama] = !empty($vr['data']) ? $data_excel[$nama] + $vr['data']['jumlah'] : $data_excel[$nama];
                    } else {
                        $data_excel[$nama] = !empty($vr['data']) ? $vr['data']['jumlah'] : 0;
                    }
                }
                $row[] = $data_excel;
            }

            $title = "Formulir RL 3.14 Rujukan";
            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Bulan' => $bulan,
                'Tahun' => $tahun,
            ];

            $filePath = DocoHelpers::exportExcel($title, $row, $header, array(
                "uploadPath" => "./uploads"
            ),[],[],true);

            $filePath->save('php://output');
            die;
        }
    }

    protected function excelRl_5_4()
    {
        try {
            $request = Yii::$app->request;
            $bulan = $request->get('bulan',null);
            $tahun = $request->get('tahun',date('Y'));
            $tahun_sebelum = (int)$tahun - 1;
            $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : '';

            $title = "Formulir RL 5.4 Daftar 10 Besar Penyakit Rawat Jalan";
            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Bulan' => $textBulan,
                'Tahun' => $tahun,
            ];
            $row = [];
            $footer = [];

            $custHeader = [
                [
                    [
                        'label'=>'No. Urut',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'KODE ICD 10',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'DESKRIPSI',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'KASUS BARU MENURUT JENIS KELAMIN',
                        'colspan'=>2,
                    ],
                    [
                        'label'=>'Jumlah Kasus Baru (4+5)',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jumlah Kunjungan',
                        'rowspan'=>2,
                    ],
                ],
                [
                    [
                        'label'=>'Laki-Laki',
                        'startfrom'=>4
                    ],
                    [
                        'label'=>'Perempuan',
                    ]
                ]
            ];
            $dataLaporan = [];
            $datasource5_4 = RL5_4::find();

            if ($bulan) {
                if ($bulan >= 1 && $bulan <= 9) {
                    $bulan = '0'.$bulan;
                }

                $datasource5_4->andWhere(['bulan' => $bulan]);
            }

            if ($tahun) {
                $datasource5_4->andWhere(['tahun' => $tahun]);
            }

            $datasource5_4 = $datasource5_4->limit(10)->asArray()->all();
            $no = 1;
            foreach ($datasource5_4 as $v_source5_4) {
                $temp5_4[1] = $no;
                $temp5_4[2] = $v_source5_4['diagnosa_kode'];
                $temp5_4[3] = $v_source5_4['diagnosa_nama'];
                $temp5_4[4] = $v_source5_4['jml_laki'];
                $temp5_4[5] = $v_source5_4['jml_perempuan'];
                $temp5_4[6] = $v_source5_4['jml_laki'] + $v_source5_4['jml_perempuan'];
                $temp5_4[7] = $v_source5_4['jumlah'];
                $row[] = $temp5_4;
                $dataLaporan[] = $temp5_4;
                $no++;
            }

            $filePath = DocoHelpers::exportExcel($title, $row, $header, array(
                "uploadPath" => "./uploads",
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ),$footer,[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelKunjunganIgd()
    {
        $request = Yii::$app->request;
        try {
            $title = "Formulir RL 3.2 Kunjungan Rawat Darurat";
            $profil = $this->getProfil();
            // contents here$request = Yii::$app->request;
            $bulan = $request->get('bulan');
            $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');
            $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : null;
            // $rs = $this->getProfil();

            $contents = [];
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Tahun' => $textBulan .' '. $tahun,
            ];

            $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Pelayanan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Total Pasien',
                        'colspan'=>2,
                    ],
                    [
                        'label'=>'Tindak Lanjut Pelayanan',
                        'colspan'=>3,
                    ],
                    [
                        'label'=>'Mati Di IGD',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'DOA',
                        'rowspan'=>2,
                    ],
                ],
                [
                    [
                        'label'=>'Rujukan',
                        'startfrom'=>3
                    ],
                    [
                        'label'=>'Non Rujukan',
                    ],
                    [
                        'label'=>'Dirawat',
                    ],
                    [
                        'label'=>'Dirujuk',
                    ],
                    [
                        'label'=>'Pulang',
                    ],
                ]
            ];

            $q_ver = "
                SELECT jeniskasuspenyakit_id, jeniskasuspenyakit_nama
                FROM rl3_2_kegiatanrawatdarurat_v
                WHERE kolom = 'vertikal'
            ";
            $ver = Yii::$app->db->createCommand($q_ver)->queryAll();
            $q_hor1 = "
                SELECT carakeluar_id, carakeluar_nama
                FROM rl3_2_kegiatanrawatdarurat_v
                WHERE kolom = 'horizontal_1'
            ";
            $hor1 = Yii::$app->db->createCommand($q_hor1)->queryAll();
            $q_hor2 = "
                SELECT kondisikeluar_id, kondisikeluar_nama
                FROM rl3_2_kegiatanrawatdarurat_v
                WHERE kolom = 'horizontal_2'
            ";
            $hor2 = Yii::$app->db->createCommand($q_hor2)->queryAll();

            $q_data = "
                    SELECT *
                    FROM rl3_2_kegiatanrawatdaruratdetail_v
                    WHERE (EXTRACT(year FROM tgl_pendaftaran) = '{$tahun}')
                ";
            if (!empty($bulan)) {
                $q_data .= " AND (EXTRACT(month FROM tgl_pendaftaran)= '{$bulan}')";
            }
            $data = Yii::$app->db->createCommand($q_data)->queryAll();

            // return $data;
            $counts = [];
            $carakeluars = [];
            $kondisikeluars = [];
            foreach ($data as $key=>$datum) {
                if ($datum['non_rujukan']) {
                    if (isset($counts['non_rujukan']) && isset($counts['non_rujukan'][$datum['non_rujukan']])) {
                        $counts['non_rujukan'][$datum['non_rujukan']]++;
                    } else {
                        $counts['non_rujukan'][$datum['non_rujukan']] = 1;
                    }

                    if (isset($carakeluars[$datum['non_rujukan']]) && isset($carakeluars[$datum['non_rujukan']][$datum['carakeluar_nonrujukan']])) {
                        $carakeluars[$datum['non_rujukan']][$datum['carakeluar_nonrujukan']]++;
                    } else {
                        $carakeluars[$datum['non_rujukan']][$datum['carakeluar_nonrujukan']] = 1;
                    }

                    if (isset($kondisikeluars[$datum['non_rujukan']]) && isset($kondisikeluars[$datum['non_rujukan']][$datum['mati_nonrujukan']])) {
                        $kondisikeluars[$datum['non_rujukan']][$datum['mati_nonrujukan']]++;
                    } else {
                        $kondisikeluars[$datum['non_rujukan']][$datum['mati_nonrujukan']] = 1;
                    }
                }

                if ($datum['rujukan']) {
                    if (isset($counts['rujukan']) && isset($counts['rujukan'][$datum['rujukan']])) {
                        $counts['rujukan'][$datum['rujukan']]++;
                    } else {
                        $counts['rujukan'][$datum['rujukan']] = 1;
                    }

                    if (isset($carakeluars[$datum['rujukan']]) && isset($carakeluars[$datum['rujukan']][$datum['carakeluar_nonrujukan']])) {
                        $carakeluars[$datum['rujukan']][$datum['carakeluar_nonrujukan']]++;
                    } else {
                        $carakeluars[$datum['rujukan']][$datum['carakeluar_nonrujukan']] = 1;
                    }

                    if (isset($kondisikeluars[$datum['rujukan']]) && isset($kondisikeluars[$datum['rujukan']][$datum['mati_rujukan']])) {
                        $kondisikeluars[$datum['rujukan']][$datum['mati_rujukan']]++;
                    } else {
                        $kondisikeluars[$datum['rujukan']][$datum['mati_rujukan']] = 1;
                    }
                }

            }
            $temp = [
                'kasuspenyakit'=>$counts,
                'carakeluar'=>$carakeluars,
                'kondisikeluar'=>$kondisikeluars,
            ];

            foreach ($ver as $each) { // jeniskasuspenyakit
                $jkp_id = $each['jeniskasuspenyakit_id'];
                $arr['jeniskasuspenyakit_nama'] = $each['jeniskasuspenyakit_nama'];
                $arr['non_rujukan'] = isset($temp['kasuspenyakit']['non_rujukan'][$jkp_id]) ? $temp['kasuspenyakit']['non_rujukan'][$jkp_id] : 0;
                $arr['rujukan'] = isset($temp['kasuspenyakit']['rujukan'][$jkp_id]) ? $temp['kasuspenyakit']['rujukan'][$jkp_id] : 0;

                foreach ($hor1 as $ckeach) { // horizontal 1 | cara keluar
                    $i = strtolower(str_replace(' ', '_', $ckeach['carakeluar_nama']));
                    $ck_id = $ckeach['carakeluar_id'];
                    $arr[$i] = isset($temp['carakeluar'][$jkp_id][$ck_id]) ? $temp['carakeluar'][$jkp_id][$ck_id] : 0;
                }

                foreach ($hor2 as $kkeach) { // horizontal 2
                    $j = strtolower(str_replace(' ', '_', $kkeach['kondisikeluar_nama']));
                    $kk_id = $kkeach['kondisikeluar_id'];
                    $arr[$j] = isset($temp['kondisikeluar'][$jkp_id][$kk_id]) ? $temp['kondisikeluar'][$jkp_id][$kk_id] : 0;
                }

                $contents[] = $arr;
            }

            $rows = [];
            $no = 1;
            $sums = [];
            $sumTemp = [];
            foreach ($contents as $content) {
                $temp = [
                    1 => $no++,
                    2 => $content['jeniskasuspenyakit_nama'], // jenis pelayanan
                    3 => $content['rujukan'], // rujukan
                    4 => $content['non_rujukan'], // non rujukan
                    5 => $content['di_rujuk_rawat_inap'], // dirawat
                    6 => $content['dirujuk_ke_rs_lain'], // dirujuk
                    7 => $content['dipulangkan'] + $content['pulang_paksa'] + $content['melarikan_diri'] + $content['lain-lain'], // pulang
                    8 => $content['meninggal_<_48_jam'] + $content['meninggal_>_48_jam'], // mati di igd
                    9 => $content['death_on_arrival'], // doa
                ];

                $sumTemp[3][] = $temp[3];
                $sumTemp[4][] = $temp[4];
                $sumTemp[5][] = $temp[5];
                $sumTemp[6][] = $temp[6];
                $sumTemp[7][] = $temp[7];
                $sumTemp[8][] = $temp[8];
                $sumTemp[9][] = $temp[9];

                $rows[] = $temp;
            }

            foreach ($sumTemp as $key=>$each) {
                $sums[$key] = array_sum($each) != 0 ? array_sum($each) : '0';
            }

            $footer = [
                'title'=> ['Total', 1],
                'data'=> $sums,
                // 'data'=> [
                //     'JUMLAH' => $qty
                // ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $rows, $header, [
                "uploadPath" => "./uploads",
                'skipIncrement' => true,
                'customHeader' => $custHeader,
            ],$footer,[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelPembedahan()
    {
        try {
            $request = Yii::$app->request;
            $textBulan = null;
            $bulan = $request->get('bulan');
            $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');
            $header = RLPembedahanHeader::find()->all();
            $textBulan = null;
            $query = "
                SELECT SUM(qty_tindakan) AS qty_tindakan, golonganoperasi_id, kegiatanoperasi_id
                FROM rl3_6_pembedahandetail_v
                WHERE (EXTRACT(year FROM tgl_tindakan) = '{$tahun}')";
            if (!empty($bulan)) {
                $textBulan = isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : null;
                $query .= " AND (EXTRACT(month FROM tgl_tindakan)= '{$bulan}')";
            }

            $detail = Yii::$app->db->createCommand("{$query} GROUP BY golonganoperasi_id, kegiatanoperasi_id")->queryAll();

            $dataDetail = [];
            foreach ($detail as $value) {
                $dataDetail[$value['golonganoperasi_id']][$value['kegiatanoperasi_id']] = $value['qty_tindakan'];
            }

            $hor = $ver = [];
            foreach ($header as $value) {
                if ($value->kolom === "vertikal") {
                    $ver[$value->kegiatanoperasi_id] = $value->kegiatanoperasi_nama;
                    continue;
                }
                $hor[$value->golonganoperasi_id] = $value->golonganoperasi_nama;
            }

            $title = "Formulir RL 3.6 Kegiatan Pembedahan";
            $row = $dataFoot = [];
            $no = 1;
            foreach ($ver as $key => $value) {
                $newRow = [
                    'SPESIALISASI' => strtolower($value)
                ];
                foreach ($hor as $k => $v) {
                    if (!isset($dataFoot[$v])) {
                        $dataFoot[$v] = 0;
                    }
                    $newRow[$v] = isset($dataDetail[$k][$key]) ? $dataDetail[$k][$key] : 0;
                    $dataFoot[$v] += $newRow[$v];
                }
                $row[] = $newRow;
                $no++;
            }
            $footer = [
                'title'=> ['Total', 1],
                'data'=> $dataFoot
            ];
            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Tahun' => $textBulan .' '. $tahun,
            ];

            $filePath = DocoHelpers::exportExcel($title, $row, $header, [
                "uploadPath" => "./uploads",
            ],$footer,[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelFasilitasTempatTidurRanap()
    {
        try {
            $request = Yii::$app->request;
            $textBulan = null;
            $bulan = $request->get('bulan');
            $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');
            $header = RLFasilitaasTempatTidurRanapHeader::find()->all();

            $queryDetail = "
                SELECT
                    count(kelaspelayanan_id) as total,
                    count(kamartempattidur_id) as total_bed,
                    jeniskasuspenyakit_id,
                    kelaspelayanan_id
                FROM rl1_3_fasilitastempattidurdetail_v
                WHERE (EXTRACT(year FROM created_date) = '{$tahun}')";

            if (!empty($bulan)) {
                $queryDetail .= " AND (EXTRACT(month FROM created_date)= '{$bulan}')";
            }

            $resQueryDetail = Yii::$app->db->createCommand("{$queryDetail} GROUP BY kelaspelayanan_id,
                jeniskasuspenyakit_id")->queryAll();

            $dataDetail = [];
            foreach ($resQueryDetail as $value) {
                $dataDetail[$value['kelaspelayanan_id']][$value['jeniskasuspenyakit_id']] = $value['total'];
            }

            $dataBed = [];
            foreach ($resQueryDetail as $value) {
                $dataBed[$value['jeniskasuspenyakit_id']][] = $value['total_bed'];
            }
            $resBed = [];
            foreach ($dataBed as $key => $x) {
                $total_bed = 0;
                foreach ($x as $k => $s) {
                    $total_bed += $s;
                }
                $resBed[$key] = $total_bed;
            }
            $resultHeader = $resultVer = [];
            foreach ($header as $value) {
                if ($value->kolom === "vertikal") {
                    $resultVer[$value->jeniskasuspenyakit_id] = $value->jeniskasuspenyakit_nama;
                    continue;
                }
                $resultHeader[$value->kelaspelayanan_id] = $value->kelaspelayanan_nama;
            }
            asort($resultHeader);
            asort($resultVer);

            $title = "Formulir RL 1.3 FASILITAS TEMPAT TIDUR RAWAT INAP";
            $row = $dataFoot = [];
            $no = 1;
            $total_beds = 0;
            foreach ($resultVer as $keyz => $value) {
                $total_bed = isset($resBed[$keyz]) ? $resBed[$keyz] : 0;
                $total_beds += $total_bed;
            }
            $newFooterCustom = ['Jumlah Tempat Tidur' => $total_beds
                            ];
            $newFooter = ['3' => $total_beds
                            ];
            $noFirst = 0;
            foreach ($resultVer as $key => $value) {
                $noFirst++;
                $total_bed = isset($resBed[$key]) ? $resBed[$key] : 0;
                $total_beds += $total_bed;
                $customHeader = [
                    'No'=>'NO',
                    'Jenis Pelayanan' => 'Jenis Pelayanan',
                    'Jumlah Tempat Tidur' => 'Jumlah Tempat Tidur',
                    'PERINCIAN TEMPAT TIDUR PER-KELAS' => ['PERINCIAN TEMPAT TIDUR PER-KELAS'=>$resultHeader
                                                        ]
                ];

                $newRowNumber = [
                    '1' => $noFirst,
                    '2' => $value,
                    '3' => $total_bed
                ];

                $newNumber = count($newRowNumber) ;
                foreach ($resultHeader as $k => $v) {
                    $newNumber++;
                    if (!isset($newFooter[$newNumber])) {
                        $newFooter[$newNumber] = 'test';
                    }
                    $newRowNumber[$newNumber] = isset($dataDetail[$k][$key]) ? $dataDetail[$k][$key] : '0';
                    $newRow[$v] = isset($dataDetail[$k][$key]) ? $dataDetail[$k][$key] : '0';
                    $newFooter[$newNumber] += $newRowNumber[$newNumber];
                }
                $row[] = $newRowNumber;
                $no++;
            }

            $newFooterToString = [];
            foreach ($newFooter as $m => $va) {
                $newFooterToString[$m] = (string)$va;
            }

            $footer = [
                'title'=> ['Total', 1,2],
                'data'=> $newFooterToString
            ];

            $headKelas = [];
            $indexing = 0;
            foreach ($resultHeader as $kh => $vh) {
                $indexing++;
                $headKelas[] = ['indexing' => $indexing,
                                'label'=>$vh,
                                'startfrom' => 4
                            ];
            }

            $z = 0;
            $x = -1;
            do{
                $z++;
                unset($headKelas[$z]['startfrom']);
            }while ( $z <= count($headKelas));

            do{
                $x++;
                unset($headKelas[$x]['indexing']);
            }while ( $x <= count($headKelas));

            $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Pelayanan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jumlah Tempat Tidur',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'PERINCIAN TEMPAT TIDUR PER-KELAS',
                        'colspan'=>count($resultHeader),
                    ],
                ],
                $headKelas
            ];

            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Tahun' => $textBulan .' '. $tahun,
            ];

            $filePath = DocoHelpers::exportExcel($title, $row, $header,  array(
                "uploadPath" => "./uploads",
                "skipIncrement" => true,
                'customHeader' => $custHeader,
                'subHeader' => 'RL 1.3 Fasilitas Tempat Tidur Rawat Inap'
            ),$footer,[],true);

            $filePath->save('php://output');
            die;

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelRadiologi()
    {
        try {
            $request = Yii::$app->request;
            $bulan = $request->get('bulan');
            $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');
            $textBulan = null;
            $header = RLRadiologiHeader::find()->all();

            $query = "
                SELECT SUM(qty_tindakan) AS qty_tindakan,
                nama_kelompok,
                jenispemeriksaanrad_nama,
                kelompokpemeriksaanrad_id,
                jenispemeriksaanrad_id
                FROM rl3_7_radiologidetail_v
                WHERE (EXTRACT(year FROM tgl_tindakan) = '{$tahun}')";
            if (!empty($bulan)) {
                $textBulan = isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : null;
                $query .= " AND (EXTRACT(month FROM tgl_tindakan)= '{$bulan}')";
            }

            $detail = Yii::$app->db->createCommand("{$query} GROUP BY nama_kelompok,
                jenispemeriksaanrad_nama,
                kelompokpemeriksaanrad_id,
                jenispemeriksaanrad_id")->queryAll();

            $header = Yii::$app->db->createCommand("
                SELECT * FROM rl3_7_radiologi_v
            ")->queryAll();

            $dataDetail = [];
            foreach ($header as $key => $value) {
                $kelompokId = $value['kelompokpemeriksaanrad_id'];
                $jenisId = $value['jenispemeriksaanrad_id'];
                if (empty($jenisId)) continue;
                if (!isset($dataDetail[$kelompokId])) {
                    $dataDetail[$kelompokId] = [
                        'label' => $value['nama_kelompok'],
                        'data' => [],
                    ];
                }

                if (!isset($dataDetail[$kelompokId]['data'][$jenisId])) {
                    $dataDetail[$kelompokId]['data'][$jenisId] = [
                        'label' => $value['jenispemeriksaanrad_nama'],
                        'qty' => 0
                    ];
                }
            }

            foreach ($detail as $value) {
                $kelompokId = $value['kelompokpemeriksaanrad_id'];
                $jenisId = $value['jenispemeriksaanrad_id'];
                if (!isset($dataDetail[$kelompokId])) {
                    $dataDetail[$kelompokId] = [
                        'label' => $value['nama_kelompok'],
                        'data' => [],
                    ];
                }

                if (!isset($dataDetail[$kelompokId]['data'][$jenisId])) {
                    $dataDetail[$kelompokId]['data'][$jenisId] = [
                        'label' => $value['jenispemeriksaanrad_nama'],
                        'qty' => 0
                    ];
                }

                $dataDetail[$kelompokId]['data'][$jenisId]['qty'] += $value['qty_tindakan'];

            }

            $title = "Formulir RL 3.7 Kegiatan Radiologi";
            $row = $dataFoot = [];
            $qty = 0;
            $urutData = 0;
            $mergeHeader = [];
            foreach ($dataDetail as $value) {
                $no = 1;
                $newRow = [
                    'NO' => null,
                    'JENIS KEGIATAN' => $value['label'],
                    'JUMLAH' => 0
                ];
                $row[] = $newRow;
                $mergeHeader[$urutData] = $value['label'];
                foreach ($value['data'] as $k => $v) {
                    $qty += $v['qty'];
                    $newRow = [
                        'NO' => $no,
                        'JENIS KEGIATAN' => $v['label'],
                        'JUMLAH' => $v['qty']
                    ];
                    $no++;
                    $urutData++;
                    $row[] = $newRow;
                }

            }
            $footer = [
                'title'=> ['Total', 1],
                'data'=> [
                    'JUMLAH' => $qty
                ]
            ];
            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Tahun' => $textBulan .' '. $tahun,
            ];

            $filePath = DocoHelpers::exportExcel($title, $row, $header,  array(
                "uploadPath" => "./uploads",
                "skipIncrement" => true,
                "mergeCells" => true,
                "mergeHeader" => $mergeHeader
            ),$footer,[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    protected function excelLaboratorium()
    {
        try {
            $request = Yii::$app->request;
            $bulan = $request->get('bulan');
            $tahun = !empty($request->get('tahun')) ? $request->get('tahun') : date('Y');
            $textBulan = null;
            $query = "
                SELECT SUM(qty_tindakan) AS qty_tindakan,
                ruangan_id,
                jenispemeriksaanlab_id,
                kelompokpemeriksaanlab_id,
                pemeriksaanlab_id
                FROM rl3_8_laboratoriumdetail_v
                WHERE (EXTRACT(year FROM tgl_tindakan) = '{$tahun}')";
            if (!empty($bulan)) {
                $textBulan = isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : null;
                $query .= " AND (EXTRACT(month FROM tgl_tindakan)= '{$bulan}')";
            }

            $detail = Yii::$app->db->createCommand("{$query} GROUP BY ruangan_id,
                jenispemeriksaanlab_id,
                kelompokpemeriksaanlab_id,
                pemeriksaanlab_id")->queryAll();

            $header = Yii::$app->db->createCommand("
                SELECT * FROM rl3_8_laboratorium_v
            ")->queryAll();

            $dataDetail = [];
            foreach ($header as $key => $value) {
                $ruanganId = $value['ruangan_id'];
                $kelompokId = $value['kelompokpemeriksaanlab_id'];
                $jenisPemeriksaanId = $value['jenispemeriksaanlab_id'];
                $pemeriksaanLabId = $value['pemeriksaanlab_id'];

                if (!isset($dataDetail[$ruanganId])) {
                    $dataDetail[$ruanganId] = [
                        'label' => $value['ruangan_nama'],
                        'data' => [],
                    ];
                }

                if (!isset($dataDetail[$ruanganId]['data'][$kelompokId])) {
                    $dataDetail[$ruanganId]['data'][$kelompokId] = [
                        'label' => $value['nama_kelompok'],
                        'data' => [],
                    ];
                }

                if (!isset($dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId])) {
                    $dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId] = [
                        'label' => $value['jenispemeriksaanlab_nama'],
                        'data' => [],
                    ];
                }
                $dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId]['data'][$pemeriksaanLabId] = [
                        'label' => $value['pemeriksaanlab_nama'],
                        'qty' => 0
                ];
            }
            foreach ($detail as $value) {
                $ruanganId = $value['ruangan_id'];
                $kelompokId = $value['kelompokpemeriksaanlab_id'];
                $jenisPemeriksaanId = $value['jenispemeriksaanlab_id'];
                $pemeriksaanLabId = $value['pemeriksaanlab_id'];
                if (isset($dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId]['data'][$pemeriksaanLabId])) {
                    $dataDetail[$ruanganId]['data'][$kelompokId]['data'][$jenisPemeriksaanId]['data'][$pemeriksaanLabId]['qty']
                            += $value['qty_tindakan'];
                }
            }

            $title = "Formulir RL 3.8 Kegiatan Laboratorium";
            $row = $dataFoot = [];
            $qty = 0;
            $urutData = 0;
            $mergeHeader = [];
            foreach ($dataDetail as $ruangan) {
                $newRow = [
                    'NO' => null,
                    'JENIS KEGIATAN' => $ruangan['label'],
                    'JUMLAH' => 0
                ];
                $row[] = $newRow;
                $mergeHeader[$urutData] = $ruangan['label'];
                $noKelompok = 1;
                $totalKegiatan = 0;
                foreach ($ruangan['data'] as $kelompok) {
                    $newRow = [
                        'NO' => $noKelompok .' ',
                        'JENIS KEGIATAN' => $kelompok['label'],
                        'JUMLAH' => ''
                    ];
                    $row[] = $newRow;
                    $noJenis = 1;
                    foreach ($kelompok['data'] as $jenis) {
                        $newRow = [
                            'NO' => $noKelompok . '.' . $noJenis .' ',
                            'JENIS KEGIATAN' => $jenis['label'],
                            'JUMLAH' => ''
                        ];
                        $row[] = $newRow;
                        $noTindakan = 1;
                        foreach ($jenis['data'] as $tindakan) {
                            $qty += $tindakan['qty'];
                            $totalKegiatan += $tindakan['qty'];
                            $newRow = [
                                'NO' => $noKelompok . '.' . $noJenis . '.' . $noTindakan,
                                'JENIS KEGIATAN' => $tindakan['label'],
                                'JUMLAH' => $tindakan['qty']
                            ];
                            $row[] = $newRow;
                            $noTindakan++;
                            $urutData++;
                        }
                        $noJenis++;
                        $urutData++;
                    }

                    $noKelompok++;
                    $urutData++;
                }
                $urutData++;
                $row[] = [
                    'NO' => null,
                    'JENIS KEGIATAN' => 'TOTAL ' . strtoupper($ruangan['label']),
                    'TOTAL' => $totalKegiatan
                ];
            }
            $footer = [
                'title'=> ['Total', 1],
                'data'=> [
                    'JUMLAH' => $qty
                ]
            ];
            $profil = $this->getProfil();
            $header = [
                'Kode RS' => $profil['nokode_rumahsakit'],
                'Nama RS' => $profil['nama_rumahsakit'],
                'Tahun' => $textBulan .' '. $tahun,
            ];
            $filePath = DocoHelpers::exportExcel($title, $row, $header,  array(
                "uploadPath" => "./uploads",
                "skipIncrement" => true,
                "mergeCells" => true,
                "mergeHeader" => $mergeHeader
            ),$footer,[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function getProfil()
    {
        $profil = Yii::$app->db->createCommand("
            SELECT nokode_rumahsakit, nama_rumahsakit FROM profilrumahsakit_m
        ")->queryOne();

        return $profil;
    }

    /**
     * @todo Fungsi untuk mendapatkan data laporan kunjungan rawat jalan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function laporanKunjunganRajal()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan', null);
        $tahun = $request->get('tahun', null);

        $data = [];
        $model = RL5_2::find();
        $modelDetail = RL5_2_detail::find();

        if ($bulan) {
            if ($bulan >= 1 && $bulan <= 9) {
                $bulan = '0'.$bulan;
            }

            $modelDetail->andWhere(['bulan' => $bulan]);
        }

        if ($tahun) {
            $modelDetail->andWhere(['tahun' => $tahun]);
        }

        $model = $model->asArray()->all();
        $modelDetail = $modelDetail->asArray()->all();
        $jumlah = ArrayHelper::map($modelDetail, 'jeniskasuspenyakit_id', 'jumlah');

        foreach ($model as $key => $value) {
            $model[$key]['jeniskasuspenyakit_id'] = $value['jeniskasuspenyakit_id'];
            $model[$key]['jeniskasuspenyakit_nama'] = $value['jeniskasuspenyakit_nama'];
            $model[$key]['jumlah'] = isset($jumlah[$value['jeniskasuspenyakit_id']]) ? $jumlah[$value['jeniskasuspenyakit_id']] : 0;
        }

        return [
            'data' => $model,
            'profil' => $this->getProfil()
        ];
    }

    /**
     * @todo Fungsi untuk mendapatkan data laporan sepuluh besar penyakit rawat inap
     * @author Rizqi Fitrianto <rizqi@docotel.com>
     */
    protected function laporanRl_5_3()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan',null);
        $tahun = $request->get('tahun',null);
        $tahun_sebelum = (int)$tahun - 1;
        try {
            $dataLaporan = [];
            // $query = (new \yii\db\Query())
            // ->from("rl_pgetdiagnosajumlahranap('{$tahun}')");
            // $datasource5_3 = $query->all();

            if ($bulan == null) {
                $query = Yii::$app->db->createCommand("SELECT * FROM rl_pgetdiagnosajumlahranap('{$tahun}', '')");
            }  else {
                if ($bulan >= 1 && $bulan <= 9) {
                    $bulan = '0'.$bulan;
                }
                $query = Yii::$app->db->createCommand("SELECT * FROM rl_pgetdiagnosajumlahranap('{$tahun}', '{$bulan}')");
            }

            $datasource5_3 = $query->queryAll();

            foreach ($datasource5_3 as $v_source5_3) {
                $temp5_3['kode_diagnosa'] = $v_source5_3['kode_diagnosa'];
                $temp5_3['nama_diagnosa'] = $v_source5_3['nama_diagnosa'];
                $temp5_3['jumlah_lakihidup'] = $v_source5_3['jumlah_lakihidup'];
                $temp5_3['jumlah_perempuanhidup'] = $v_source5_3['jumlah_perempuanhidup'];
                $temp5_3['jumlah_lakimati'] = $v_source5_3['jumlah_lakimati'];
                $temp5_3['jumlah_perempuanmati'] = $v_source5_3['jumlah_perempuanmati'];
                $temp5_3['jumlah_hidup_mati'] = $v_source5_3['jumlah_hidup_mati'];
                $dataLaporan[] = $temp5_3;
            }
        } catch (\Exception $e) {
            $dataLaporan = [];
        }
        return [
            'data' => $dataLaporan,
            'profil' => $this->getProfil()
        ];
    }

    protected function excelLaporanPenyakitPasienRanap()
    {
        $request = Yii::$app->request;
        $tahun = $request->get('tahun', null);
        $title = Yii::t('app', 'Formulir RL 5.3 Daftar 10 Besar Penyakit Rawat Inap');
        $row = $profil = $footer = [];
        try {
            $result = $this->laporanRl_5_3();
            $header = [
                'Kode RS' => $result['profil']['nokode_rumahsakit'],
                'Nama RS' => $result['profil']['nama_rumahsakit'],
                'Tahun' => $tahun,
            ];
            $no = 1;
            foreach ($result['data'] as $key => $value) {
                $tmp[1] = $no;
                $tmp[2] = $value['kode_diagnosa'];
                $tmp[3] = $value['nama_diagnosa'];
                $tmp[4] = $value['jumlah_lakihidup'];
                $tmp[5] = $value['jumlah_perempuanhidup'];
                $tmp[6] = $value['jumlah_lakimati'];
                $tmp[7] = $value['jumlah_perempuanmati'];
                $tmp[8] = $value['jumlah_hidup_mati'];
                $row[] = $tmp;
                $no++;
            }
        } catch (Exception $e) {
            $header = [
                'Kode RS' => '-',
                'Nama RS' => '-',
                'Tahun' => $tahun,
            ];
            $row = [];
        }
        $custHeader = [
                [
                    [
                        'label'=>'No. Urut',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'KODE ICD 10',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Deskripsi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Pasien Keluar Hidup Menurut Jenis Kelamin',
                        'colspan'=>2,
                    ],
                    [
                        'label'=>'Pasien Keluar Mati Menurut Jenis Kelamin',
                        'colspan'=>2,
                    ],
                    [
                        'label'=>'Total (Hidup dan Mati)',
                        'rowspan'=>2,
                    ],
                ],
                [
                    [
                        'label'=>'Laki-Laki',
                        'startfrom'=>4
                    ],
                    [
                        'label'=>'Perempuan',
                    ],
                    [
                        'label'=>'Laki-Laki',
                    ],
                    [
                        'label'=>'Perempuan',
                    ]
                ]
            ];
        $filePath = DocoHelpers::exportExcel($title, $row, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    /**
     * @todo Fungsi untuk mendapatkan data excel laporan kunjungan rawat jalan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function excelLaporanKunjunganRajal()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan', null);
        $tahun = $request->get('tahun', null);
        $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : '';
        $title = Yii::t('app', 'Formulir RL 5.2 Kunjungan Rawat Jalan');
        $row = [];
        $footer = [];

        $model = RL5_2::find();
        $modelDetail = RL5_2_detail::find();
        $profil = $this->getProfil();
        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
            'Bulan' => $textBulan,
            'Tahun' => $tahun,
        ];

        if ($bulan) {
            if ($bulan >= 1 && $bulan <= 9) {
                $bulan = '0'.$bulan;
            }

            $modelDetail->andWhere(['bulan' => $bulan]);
        }

        if ($tahun) {
            $modelDetail->andWhere(['tahun' => $tahun]);
        }

        $model = $model->asArray()->all();
        $modelDetail = $modelDetail->asArray()->all();
        $jumlah = ArrayHelper::map($modelDetail, 'jeniskasuspenyakit_id', 'jumlah');

        $no = 1;
        foreach ($model as $key => $value) {
            $row[$key]['Jenis Kasus Penyakit'] = $value['jeniskasuspenyakit_nama'];
            $row[$key]['Jumlah'] = isset($jumlah[$value['jeniskasuspenyakit_id']]) ? $jumlah[$value['jeniskasuspenyakit_id']] : 0;
            $no++;
        }

        $filePath = DocoHelpers::exportExcel($title, $row, $header, array(
            "uploadPath" => "./uploads",
        ),$footer,[],true);

        $filePath->save('php://output');
        die;
    }

    /**
     * @todo Fungsi untuk mendapatkan data laporan pengunjung rawat jalan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function laporanPengunjungRajal()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan', null);
        $tahun = $request->get('tahun', null);

        if ($bulan) {
            if ($bulan >= 1 && $bulan <= 9) {
                $bulan = '0'.$bulan;
            }
        }

        if ($bulan == null) {
            $query = Yii::$app->db->createCommand("SELECT * FROM rl_pget51pengunjungrs('', '{$tahun}')");
        }  else {
            $query = Yii::$app->db->createCommand("SELECT * FROM rl_pget51pengunjungrs('{$bulan}', '{$tahun}')");
        }

        return [
            'data' => $query->queryAll(),
            'profil' => $this->getProfil()
        ];
    }

    /**
     * @todo Fungsi untuk mendapatkan data excel laporan pengunjung rawat jalan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function excelLaporanPengunjungRajal()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan', null);
        $tahun = $request->get('tahun', null);
        $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : '';
        $title = Yii::t('app', 'Formulir RL 5.1 Pengunjung Rumah Sakit');
        $header = [];
        $footer = [];

        if ($bulan) {
            if ($bulan >= 1 && $bulan <= 9) {
                $bulan = '0'.$bulan;
            }
        }

        if ($bulan == null) {
            $query = Yii::$app->db->createCommand("SELECT * FROM rl_pget51pengunjungrs('', '{$tahun}')");
        }  else {
            $query = Yii::$app->db->createCommand("SELECT * FROM rl_pget51pengunjungrs('{$bulan}', '{$tahun}')");
        }

        $model = $query->queryAll();
        $profil = $this->getProfil();

        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
            'Bulan' => $textBulan,
            'Tahun' => $tahun,
        ];

        foreach ($model as $key => $value) {
            $model[$key]['Jenis Kegiatan'] = $value['jenis_kegiatan'];
            $model[$key]['Jumlah'] = $value['jumlah'];
            unset($model[$key]['jenis_kegiatan']);
            unset($model[$key]['jumlah']);
            unset($model[$key]['bulan']);
            unset($model[$key]['tahun']);
        }

        $filePath = DocoHelpers::exportExcel($title, $model, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    protected function excelMorbiditasRanap()
    {
        $request = Yii::$app->request;
        $bulan = $request->get('bulan', null);
        $tahun = $request->get('tahun', null);
        $_GET['excel'] = true;
        $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : '';
        $title = 'Formulir RL 4.a Data Keadaan Morbiditas Pasien Rawat Inap Rumah Sakit';
        $header = [];
        $footer = [];

        $data = $this->laporanMorbiditasRanap();
        $profil = $this->getProfil();

        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
            'Bulan' => $textBulan,
            'Tahun' => $tahun,
        ];
        $dataColumns = $data['columns'];
        $countGol = count($data['header']);

        $staticFirstRow = [
            [
                'label' => 'No',
                'rowspan' => 3
            ],
            [
                'label' => 'No. DTD',
                'rowspan' => 3
            ],
            [
                'label' => 'No. Daftar terperinci',
                'rowspan' => 3
            ],
            [
                'label' => 'Golongan sebab penyakit',
                'rowspan' => 3
            ],
            [
                'label' => 'Jumlah Pasien Hidup dan Mati menurut Golongan Umur & Jenis Kelamin',
                'colspan' => ($countGol * 2)
            ],
            [
                'label' => 'Pasien Keluar (Hidup & Mati) Menurut Jenis Kelamin',
                'colspan' => 2
            ],
            [
                'label' => 'Jumlah Pasien Keluar Hidup',
                'rowspan' => 3
            ],
            [
                'label' => 'Jumlah Pasien Keluar mati',
                'rowspan' => 3
            ],
        ];

        $staticSecondRow = [
            [
                'label' => 'LK',
                'rowspan' => 2
            ],[
                'label' => 'PR',
                'rowspan' => 2
            ],
        ];

        $loopCheckHeader = 0;
        foreach($data['header'] as $v) {
            if($loopCheckHeader == 0) {
                $listGol[] = [
                    'label' => $v,
                    'startfrom' => 5,
                    'colspan' => 2
                ];
            } else {
                $listGol[] = [
                    'label' => $v,
                    'colspan' => 2
                ];
            }
            $loopCheckHeader++;
        }

        $loopCheckGol = 0;
        foreach($dataColumns as $k => $v) {
            if ($k >= 4 && $k < (($countGol * 2) + 4) ) {
                if($loopCheckGol == 0) {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'startfrom' => 5,
                        'data' => $v['data']
                    ];
                } else {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'data' => $v['data']
                    ];
                }
                $loopCheckGol++;
            }
        }

        $listGol = array_merge($listGol, $staticSecondRow);

        $custHeader = [
            $staticFirstRow,
            $listGol,
            $jkGolUmur
        ];

        foreach($data['data'] as $k => $v) {
                $no = 5;
                $tmp[1] = $v['no'];
                $tmp[2] = $v['no_dtd'];
                $tmp[3] = $v['note_dtd'];
                $tmp[4] = $v['diagnosa_nama'];
                for($i=0; $i < count($jkGolUmur); $i++) {
                    $text = str_replace(' ', '_', $jkGolUmur[$i]['data']);
                    $tmp[$no] = $v[$text];
                    $no++;
                }
                $tmp[$no] = $v['lk'];
                $tmp[$no+1] = $v['pr'];
                $tmp[$no+2] = $v['keluar_hidup'];
                $tmp[$no+3] = $v['keluar_mati'];
                $newData[] = $tmp;
        }

        $filePath = DocoHelpers::exportExcel($title, $newData, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    protected function excelMorbiditasRajal()
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 300);
        $request = Yii::$app->request;
        $bulan = $request->get('bulan', null);
        $tahun = $request->get('tahun', null);
        $_GET['excel'] = true;
        $textBulan = $bulan && isset(DocoHelpers::$_bulan[$bulan]) ? DocoHelpers::$_bulan[$bulan] : '';
        $title = 'Formulir RL 4.b Data Keadaan Morbiditas Pasien Rawat Jalan Rumah Sakit';
        $header = [];
        $footer = [];

        $data = $this->laporanMorbiditasRajal();
        $profil = $this->getProfil();

        $header = [
            'Kode RS' => $profil['nokode_rumahsakit'],
            'Nama RS' => $profil['nama_rumahsakit'],
            'Bulan' => $textBulan,
            'Tahun' => $tahun,
        ];
        $dataColumns = $data['columns'];
        $countGol = count($data['header']);

        $staticFirstRow = [
            [
                'label' => 'No',
                'rowspan' => 3
            ],
            [
                'label' => 'No. DTD',
                'rowspan' => 3
            ],
            [
                'label' => 'No. Daftar terperinci',
                'rowspan' => 3
            ],
            [
                'label' => 'Golongan sebab penyakit',
                'rowspan' => 3
            ],
            [
                'label' => 'Jumlah Pasien Kasus menurut Golongan Umur & Jenis Kelamin',
                'colspan' => ($countGol * 2)
            ],
            [
                'label' => 'Kasus Baru Menurut Jenis Kelamin',
                'colspan' => 2
            ],
            [
                'label' => 'Jumlah Kasus Baru (23 + 24)',
                'rowspan' => 3
            ],
            [
                'label' => 'Jumlah Kunjungan',
                'rowspan' => 3
            ],
        ];

        $staticSecondRow = [
            [
                'label' => 'LK',
                'rowspan' => 2
            ],[
                'label' => 'PR',
                'rowspan' => 2
            ],
        ];

        $loopCheckHeader = 0;
        foreach($data['header'] as $v) {
            if($loopCheckHeader == 0) {
                $listGol[] = [
                    'label' => $v,
                    'startfrom' => 5,
                    'colspan' => 2
                ];
            } else {
                $listGol[] = [
                    'label' => $v,
                    'colspan' => 2
                ];
            }
            $loopCheckHeader++;
        }

        $loopCheckGol = 0;
        foreach($dataColumns as $k => $v) {
            if ($k >= 4 && $k < (($countGol * 2) + 4) ) {
                if($loopCheckGol == 0) {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'startfrom' => 5,
                        'data' => $v['data']
                    ];
                } else {
                    $jkGolUmur[] = [
                        'label' => $v['title'],
                        'data' => $v['data']
                    ];
                }
                $loopCheckGol++;
            }
        }

        $listGol = array_merge($listGol, $staticSecondRow);

        $custHeader = [
            $staticFirstRow,
            $listGol,
            $jkGolUmur
        ];

        foreach($data['data'] as $k => $v) {
                $no = 5;
                $tmp[1] = $v['no'];
                $tmp[2] = $v['no_dtd'];
                $tmp[3] = $v['note_dtd'];
                $tmp[4] = $v['diagnosa_nama'];
                for($i=0; $i < count($jkGolUmur); $i++) {
                    $text = str_replace(' ', '_', $jkGolUmur[$i]['data']);
                    $tmp[$no] = $v[$text];
                    $no++;
                }
                $tmp[$no] = $v['lk'];
                $tmp[$no+1] = $v['pr'];
                $tmp[$no+2] = $v['jml_kasus_baru'];
                $tmp[$no+3] = $v['jml_kunjungan'];
                $newData[] = $tmp;
        }

        $filePath = DocoHelpers::exportExcel($title, $newData, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $modelheader = new RLMorbiditasRanapHeader;
        $queryheader = $modelheader::find();
        $data = $queryheader->where(['kolom' => 'vertikal'])
        ->orderBy([
            'dtd_noterperinci' => SORT_ASC,
            'diagnosa_kode' => SORT_ASC,
        ])->asArray()->all();

        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanMorbiditasRanapExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportMorbiditasRanap' => [
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
                'UploadLaporanMorbiditasRanapExcel' => [
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

    public function actionSyncExportExcelMorbiditas()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $modelheader = new RLMorbiditasRajalHeader;
        $queryheader = $modelheader::find();
        $data = $queryheader->where(['kolom' => 'vertikal'])
        ->orderBy([
            'dtd_noterperinci' => SORT_ASC,
            'diagnosa_kode' => SORT_ASC,
        ])->asArray()->all();

        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanMorbiditasRajalExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportMorbiditasRajal' => [
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
                'UploadLaporanMorbiditasRajalExcel' => [
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

    public function actionSyncDataLaporanMorbiditasRajal()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $modelheader = new Rl4BMorbiditasrawatjalanV;
        $queryheader = $modelheader::find();
        $data = $queryheader->where(['kolom' => 'vertikal'])
        ->orderBy([
            'dtd_noterperinci' => SORT_ASC,
            'diagnosa_kode' => SORT_ASC,
        ])->asArray()->all();
        
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);
        
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
            'filter' => $getData,
            'unique_str' => $randString,
        ];
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
        $fileName = $dir.'/RL 4.a Laporan Morbiditas Ranap.xlsx';

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

    public function actionDownloadFileMorbiditas()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/RL 4.b Laporan Morbiditas Rajal.xlsx';

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
