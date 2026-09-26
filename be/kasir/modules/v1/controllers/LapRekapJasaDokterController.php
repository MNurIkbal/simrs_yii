<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRekapJasaDokterView;
use app\modules\v1\models\PegawaiMasterView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use app\modules\v1\models\LaporanJasaDokterSumView;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\RincianJasaDokterView;
use app\modules\v1\models\LaporanPemeriksaanDokterView;
use app\modules\v1\models\PemeriksaanDokterDetailView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Lookup;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;
use Doco\models\Pendaftaran;

class LapRekapJasaDokterController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanRekapJasaDokterView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = $this->getData();
        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $page = $_GET["page"] ? $_GET["page"] : 1;
        $pageSize =  $_GET["per-page"] ? $_GET["per-page"] : 10;

        //offset
        $offset = $page == 1 ? null : ($page - 1) * $pageSize;
        $query->limit($pageSize)->offset($offset);

        return [
            'data' => $query->asArray()->all(),
            '_meta' => [
                'totalCount' => $provider->getTotalCount(),
                'pageCount' => $provider->getPagination()->getPageCount(),
                'currentPage' => $provider->getPagination()->getPage() ? $provider->getPagination()->getPage() : 1,
                'perPage' => $provider->getPagination()->getPageSize(),
            ],
            'summary' => [
                'bruto' => $query->sum('bruto'),
                'dpp' => $query->sum('dpp'),
                'jasa' => $query->sum('tarif_tindakankomp'),
            ]
        ];
    }

    public function actionListRequest()
    {
        // Get pegawai
        $queryPegawai = PegawaiMasterView::find()
            ->where(['kelompokpegawai_id' => 1])
            ->orderBy(['nama_pegawai' => SORT_ASC])
            ->all();
        
            // Get jasa
        $modelStatusBilling = new LaporanRekapJasaDokterView;
        $queryStatusBilling = $modelStatusBilling::find();
        $queryStatusBilling = DocoRestActiveFilter::advancedFilter($modelStatusBilling, $queryStatusBilling);
        $queryStatusBilling = new ActiveDataProvider([
            'query' => $queryStatusBilling,
        ]);

        // Get ruangan
        $queryRuangan = Ruangan::find()
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['ruangan_nama' => SORT_ASC])
            ->all();
        
        // Get cara bayar
        $queryCaraBayar = CaraBayar::find()
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['carabayar_nama' => SORT_ASC])
            ->all();
        
        // Get penjamin
        $queryPenjamin = Penjamin::find()
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['penjamin_nama' => SORT_ASC])
            ->all();        
        
        $queryPelayanan = Lookup::find()
            ->select(['lookup_id', 'lookup_name'])
            ->where(['lookup_type' => 'pelayanan'])
            ->all();
        
        // Get kelas pelayanan
        $queryKelasPelayanan = KelasPelayanan::find()
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['kelaspelayanan_nama' => SORT_ASC])
            ->all();

        // Lookup type status bayar 
        $queryStatusBayar = Lookup::find()
            ->select(['lookup_id', 'lookup_name'])
            ->where(['lookup_type' => DocoConstants::VAR_LU_SB])
            ->orWhere(['lookup_id' => DocoConstants::STATUS_BAYAR_NULL])
            ->all();


        return [
            'status_bayar' => $queryStatusBilling->getModels(),
            'pegawai'      => ArrayHelper::map($queryPegawai, 'pegawai_id', 'nama_pegawai'),
            'ruangan'      => ArrayHelper::map($queryRuangan, 'ruangan_id', 'ruangan_nama'),
            'cara_bayar'   => ArrayHelper::map($queryCaraBayar, 'carabayar_id', 'carabayar_nama'),
            'penjamin'     => ArrayHelper::map($queryPenjamin, 'penjamin_id', 'penjamin_nama'),
            'pelayanan'     => ArrayHelper::map($queryPelayanan, 'lookup_name', 'lookup_name'),
            'kelas_pelayanan' => ArrayHelper::map($queryKelasPelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama'),
            'get_status_biling' => ArrayHelper::map($queryStatusBayar, 'lookup_id', 'lookup_name')
        ];
    }


    protected $_title = "Laporan Rekap Jasa Dokter";
    public function actionExportExcel()
    {
        $model = new LaporanRekapJasaDokterView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:00');
        $nama_pegawai = '-';
        
        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];

            if (isset($advancedFilter['tgl_pasienpulang']) && !empty($advancedFilter['tgl_pasienpulang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pasienpulang']);
                
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                unset($advancedFilter['tgl_pasienpulang']);
            }

            if (isset($advancedFilter['dokterpenanggungjawab_id']) && !empty($advancedFilter['dokterpenanggungjawab_id'])) {
                $pegawai = PegawaiMasterView::find()->where(['pegawai_id' => $advancedFilter['dokterpenanggungjawab_id']])->one();
                $nama_pegawai = ($pegawai) ? $pegawai['nama_pegawai'] : '-';
            } else {
                $nama_pegawai = '-';
            }

            if (isset($advancedFilter['status_bayar']) && !empty($advancedFilter['status_bayar'])) {
                $status_bayar = $advancedFilter['status_bayar'];
                $query->andWhere(['status_bayar' => $status_bayar]);
            } else {
                $status_bayar = '-';
            }

            if (isset($advancedFilter['komponentarif_nama']) && !empty($advancedFilter['komponentarif_nama'])) {
                $komponentarif_nama = $advancedFilter['komponentarif_nama'];
            } else {
                $komponentarif_nama = '-';
            }

            if (isset($advancedFilter['daftartindakan_nama']) && !empty($advancedFilter['daftartindakan_nama'])) {
                $daftartindakan_nama = $advancedFilter['daftartindakan_nama'];
            } else {
                $daftartindakan_nama = '-';
            }

            if (isset($advancedFilter['jenis_transaksi']) && !empty($advancedFilter['jenis_transaksi'])) {
                $jenis_transaksi = $advancedFilter['jenis_transaksi'];
            } else {
                $jenis_transaksi = '-';
            }

            if (isset($advancedFilter['carabayar_id']) && !empty($advancedFilter['carabayar_id'])) {
                $dataCaraBayar = CaraBayar::find()->where(['carabayar_id' => $advancedFilter['carabayar_id']])->one();
                $carabayar_nama = ($dataCaraBayar) ? $dataCaraBayar['carabayar_nama'] : '-';
            } else {
                $carabayar_nama = '-';
            }

            if(isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
                $dataPenjamin = Penjamin::find()->where(['penjamin_id' => $advancedFilter['penjamin_id']])->one();
                $penjamin_nama = ($dataPenjamin) ? $dataPenjamin['penjamin_nama'] : '-';
            } else {
                $penjamin_nama = '-';
            }

            if (isset($advancedFilter['ruangan_id']) && !empty($advancedFilter['ruangan_id'])) {
                $dataRuangan = Ruangan::find()->where(['ruangan_id' => $advancedFilter['ruangan_id']])->one();
                $ruangan_nama = ($dataRuangan) ? $dataRuangan['ruangan_nama'] : '-';
            } else {
                $ruangan_nama = '-';
            }

            if (isset($advancedFilter['jenis_transaksi']) && !empty($advancedFilter['jenis_transaksi'])) {
                $jenis_transaksi = $advancedFilter['jenis_transaksi'];
            } else {
                $jenis_transaksi = '-';
            }

            if (isset($advancedFilter['komponentarif_nama']) && !empty($advancedFilter['komponentarif_nama'])) {
                $komponentarif_nama = $advancedFilter['komponentarif_nama'];
            } else {
                $komponentarif_nama = '-';
            }

            if (isset($advancedFilter['daftartindakan_nama']) && !empty($advancedFilter['daftartindakan_nama'])) {
                $daftartindakan_nama = $advancedFilter['daftartindakan_nama'];
            } else {
                $daftartindakan_nama = '-';
            }
        }
        
        $query->andWhere(['between', 'tgl_pasienpulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $value['tarif_tindakankomp'] = number_format($value['tarif_tindakankomp'], 2, ".", "");
            $value['tgl_pasienpulang'] = date("j M Y", strtotime($value['tgl_pasienpulang']));
            $bruto = ceil($value['tarif_tindakankomp']  * (100/90));
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal')] = $value['tgl_pasienpulang'];
            $newValue[\Yii::t('app', 'Nama dokter')] = $value['nama_pegawai'];
            $newValue[\Yii::t('app', 'Status Billing')] = $value['status_bayar'];
            $newValue[\Yii::t('app', 'No pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No rekam medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Keterangan')] = $value['daftartindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Jasa')] = $value['komponentarif_nama'];
            $newValue[\Yii::t('app', 'Jasa (Rp.)')] = $value['tarif_tindakankomp'];
            $newValue[\Yii::t('app', 'Bruto (Rp.)')] = $value['bruto'] ;
            $newValue[\Yii::t('app', 'DPP (Rp.)')] = $value['dpp'];
            // $newValue[\Yii::t('app', 'Bruto (Rp.)')] = $bruto;
            // $newValue[\Yii::t('app', 'DPP (Rp.)')] = ((50/100) * $bruto);
            $newValue[\Yii::t('app', 'Jenis Transaksi')] = $value['jenis_transaksi'];
            $newValue[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
            $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Pelayanan')] = $value['pelayanan'];
            $result[$key] = $newValue;
        }

        $header = array(
            Yii::t("app", "Tanggal Tindakan") => ((date('d M Y',strtotime($start))." - ".date('d M Y', strtotime($end)))),
            Yii::t("app", "Nama Dokter")      => strtoupper($nama_pegawai),
            Yii::t("app", "Nama Jasa")        => strtoupper($komponentarif_nama),
            Yii::t("app", "Keterangan")       => strtoupper($daftartindakan_nama),
            Yii::t("app", "Jenis Transaksi")  => strtoupper($jenis_transaksi),
            Yii::t("app", "Status")           => strtoupper($status_bayar),
            Yii::t("app", "Cara Bayar")       => strtoupper($carabayar_nama),
            Yii::t("app", "Penjamin")         => strtoupper($penjamin_nama),
            Yii::t("app", "Ruangan")          => strtoupper($ruangan_nama),
            Yii::t("app", "Pelayanan")        => (isset($advancedFilter['pelayanan']) && !empty($advancedFilter['pelayanan'])) ? strtoupper($advancedFilter['pelayanan']) : '-',
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
            "uploadPath" => "./uploads",
        ),[],[],true);
        
        $filePath->save('php://output');
        die;
    }
    /**
    * @controller actionExportPdf
    * @attribute #table_data# => table
    * @attribute #tgl_pembayaran# => tanggal pembayaran
    * @attribute #nama_rs# => nama rs
    * @attribute #alamat_rs# => alamat rs
    * @attribute #telp_rs# => no telpon rs
    * @attribute #fax_rs# => no fax rs
    * @attribute #email_rs# => email rs
    * @attribute #website_rs# => website rs
    **/
    public function actionExportPdf()
    {
        try {
            $model = new LaporanJasaDokterSumView;
            $query = $model::find();
            $start = date('Y-m-d');
            $end = date('Y-m-d');
            $startPulang = date('Y-m-d');
            $endPulang = date('Y-m-d');
            $carabayar_nama = $penjamin_nama = "-";
            if(isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if(isset($advancedFilter['tgl_tindakan']) && !empty($advancedFilter['tgl_tindakan'])) {
                    $explode = explode(" - ", $advancedFilter['tgl_tindakan']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($advancedFilter['tgl_tindakan']);
                }
                if(isset($advancedFilter['tgl_pasienpulang']) && !empty($advancedFilter['tgl_pasienpulang'])) {
                    $explode = explode(" - ", $advancedFilter['tgl_pasienpulang']);
                    if(count($explode) == 2) {
                        $startPulang = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $endPulang = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    $query->andWhere(['between', 'tgl_tindakan', $startPulang, $endPulang]);
                    unset($advancedFilter['tgl_pasienpulang']);
                }
                if(isset($advancedFilter['dokterpenanggungjawab_id']) && !empty($advancedFilter['dokterpenanggungjawab_id'])) {
                    $pegawai_id = $advancedFilter['dokterpenanggungjawab_id'];
                    $query->andWhere(['pegawai_id' => $pegawai_id]);
                }

                if(isset($advancedFilter['carabayar_id']) && !empty($advancedFilter['carabayar_id'])) {
                    $carabayar_id = $advancedFilter['carabayar_id'];
                    $dataCaraBayar = CaraBayar::find()->where(['carabayar_id' => $carabayar_id])->one();
                    $carabayar_nama = ($dataCaraBayar) ? $dataCaraBayar['carabayar_nama'] : '';
                } else {
                    $carabayar_nama = '-';
                } 

                if(isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
                    $penjamin_id = $advancedFilter['penjamin_id'];
                    $dataPenjamin = Penjamin::find()->where(['penjamin_id' => $penjamin_id])->one();
                    $penjamin_nama = ($dataPenjamin) ? $dataPenjamin['penjamin_nama'] : '-';
                } else {
                    $penjamin_nama = '-';
                }
            }

            $query->andWhere(['between', 'tgl_tindakan', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->orderBy(['keterangan' => SORT_ASC, 'instalasi' => SORT_ASC])->asArray()->all();
            $data = $this->group_by("nama_pegawai", $data);
            $profilRs = Cache::getProfileRs();
            $groupBpjs = DocoConstants::GROUP_BPJS;
            $result = [];
            foreach ($data as $value) {
                foreach ($value as $key => $val) {
                    if(!isset($result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']])) {
                        $result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']] = [
                            'total_jasanetto_non_bpjs' => 0,
                            'total_jasanetto_bpjs' => 0,
                            'total_jasanetto' => 0,
                            'total_bruto' => 0,
                            'total_dpp' => 0
                        ];
                    }

                    $result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']]['total_jasanetto'] += $val['total_jasanetto'];
                    $result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']]['total_bruto'] += $val['total_bruto'];
                    $result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']]['total_dpp'] += $val['total_dpp'];
                    $groupcarabayar_id = $val['groupcarabayar_id'];
                    if($groupcarabayar_id != $groupBpjs) {
                        $result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']]['total_jasanetto_non_bpjs'] += $val['total_jasanetto'];
                    }
                    else {
                        $result[$val['nama_pegawai']][$val['keterangan']][$val['instalasi']]['total_jasanetto_bpjs'] += $val['total_jasanetto'];
                    }
                }
            }

            $print = new DocoPrint('lap-rekap-jasa-dokter');
            $print->attributes = [
                '#nama_rs#' => $profilRs['nama_rumahsakit'],
                '#alamat_rs#' => $profilRs['kota'],
                '#telp_rs#' => $profilRs['no_telp_profilrs'],
                '#fax_rs#' => $profilRs['no_faksimili'],
                '#email_rs#' => $profilRs['email'],
                '#website_rs#' => $profilRs['website'],
                '#table_data#' => $this->renderPartial('print_pdf', [
                    'data' => $result,
                    'periode' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
                    'carabayar_nama' => $carabayar_nama,
                    'penjamin_nama' => $penjamin_nama,
                ])
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            $result['errror'] = $e->getMessage();

            return $result;
        }
         catch (\yii\db\Exception $e) {
            $result['errror'] = $e->getMessage();

            return $result;
        }
    }

    private function group_by($key, $data) {
        $result = array();

        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }

    /**
    * @controller actionCetakRincianJasdok
    * @attribute #datatable# => table
    * @attribute #nama_rs# => nama rumah sakit
    * @attribute #alamat_rs# => alamat rumah sakit
    * @attribute #telp_rs# => telp rumah sakit
    * @attribute #fax_rs# => fax rumah sakit
    * @attribute #email_rs# => email rumah sakit
    * @attribute #website_rs# => website rumah sakit
    **/
    public function actionCetakRincianJasdok()
    {
        try {
            $model = new RincianJasaDokterView;
            $query = $model::find();
            $start = date('Y-m-d');
            $end   = date('Y-m-d');
            $carabayar_nama = $penjamin_nama = "-";
            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];

                if (isset($advancedFilter['tgl_pasienpulang']) && !empty($advancedFilter['tgl_pasienpulang'])) {
                    $explode = explode(" - ", $advancedFilter['tgl_pasienpulang']);

                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }

                    unset($advancedFilter['tgl_pasienpulang']);
                }

                if (isset($advancedFilter['dokterpenanggungjawab_id']) && !empty($advancedFilter['dokterpenanggungjawab_id'])) {
                    $pegawai_id = $advancedFilter['dokterpenanggungjawab_id'];
                    $query->andWhere(['pegawai_id' => $pegawai_id]);
                }

                if (isset($advancedFilter['carabayar_id']) && !empty($advancedFilter['carabayar_id'])) {
                    $carabayar_id = $advancedFilter['carabayar_id'];
                    $dataCaraBayar = CaraBayar::find()->where(['carabayar_id' => $carabayar_id])->one();
                    $carabayar_nama = ($dataCaraBayar) ? $dataCaraBayar['carabayar_nama'] : '-';
                } else {
                    $carabayar_nama = '-';
                }

                if (isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
                    $penjamin_id = $advancedFilter['penjamin_id'];
                    $dataPenjamin = Penjamin::find()->where(['penjamin_id' => $penjamin_id])->one();
                    $penjamin_nama = ($dataPenjamin) ? $dataPenjamin['penjamin_nama'] : '-';
                } else {
                    $penjamin_nama = '-';
                }
            }

            $query->andWhere(['between', 'tgl_pasienpulang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->orderBy(['nama_pegawai' => SORT_ASC, 'keterangan' => SORT_ASC, 'instalasi' => SORT_ASC])->asArray()->all();
            $data = $this->group_by("nama_pegawai", $data);
            $data_details = [];

            foreach ($data as $key => $value) {
                foreach ($value as $k => $v) {
                    $data_details[$key][$v['keterangan']][] = $v;
                }
            }

            $profilRs = Cache::getProfileRs();
            $print = new DocoPrint('detail-jasa-dokter');
            $print->attributes = [
                '#nama_rs#' => !empty($profilRs['nama_rumahsakit']) ? $profilRs['nama_rumahsakit'] : '-',
                '#alamat_rs#' => !empty($profilRs['kota']) ? $profilRs['kota'] : '-',
                '#telp_rs#' => !empty($profilRs['no_telp_profilrs']) ? $profilRs['no_telp_profilrs'] : '-',
                '#fax_rs#' => !empty($profilRs['no_faksimili']) ? $profilRs['no_faksimili'] : '-',
                '#email_rs#' => !empty($profilRs['email']) ? $profilRs['email'] : '-',
                '#website_rs#' => !empty($profilRs['website']) ? $profilRs['website'] : '-',
                '#datatable#' => $this->renderPartial('cetak-rincian-jasdok', [
                    'data' => $data,
                    'data_details' => $data_details,
                    'periode' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
                    'carabayar_nama' => $carabayar_nama,
                    'penjamin_nama' => $penjamin_nama,
                ]),
            ];

            $print->Output();
        } catch (\yii\db\Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (\yii\db\Exception $e) {
            $result['error'] = $e->getMessage();
        }
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        if($type == 'dokter') {
            $result = PegawaiMasterView::find()->select(['pegawai_id as id', 'nama_pegawai as text']);
            if (!empty($term)) {
                $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
            }
            $result->orderBy(['nama_pegawai' => SORT_ASC]);
        }
        elseif($type == 'carabayar') {
            $result = CaraBayar::find()->select(['carabayar_id as id', 'carabayar_nama as text']);
            if (!empty($term)) {
                $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
            }
            $result->orderBy(['carabayar_nama' => SORT_ASC]);
        }
        else {
            $result = Penjamin::find()->select(['penjamin_id as id', 'penjamin_nama as text']);
            if (!empty($term)) {
                $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
            }
            $result->orderBy(['penjamin_nama' => SORT_ASC]);
        }
        
        $result->limit(($limit + 1))->offset($limit * ($page - 1));
        $result = $result->asArray()->all();
        return $result;
    }

    public function actionGetJasaMedis()
    {
        $model = new LaporanPemeriksaanDokterView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_masuk'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_masuk']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }

            if(isset($_GET['advanced-filter']['dokterpenanggungjawab_id'])) {
                $dokterpenanggungjawab_id = $_GET['advanced-filter']['dokterpenanggungjawab_id'];
                $query->andWhere(['dokterpenanggungjawab_id' => $dokterpenanggungjawab_id]);
                unset($_GET['advanced-filter']['dokterpenanggungjawab_id']);
            }

            if(isset($_GET['advanced-filter']['type'])) {
                $type = $_GET['advanced-filter']['type'];
                if($type != 0) {
                    if($type == 1) {
                        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RJ, 'carabayar_id' => DocoConstants::CB_PEN_UMUM]);
                    }
                    elseif($type == 2) {
                        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RJ]);
                        $query->andWhere(['<>', 'carabayar_id', DocoConstants::CB_PEN_UMUM]);
                    }
                    elseif($type == 3) {
                        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD]);
                        $query->andWhere(['carabayar_id' => DocoConstants::CB_PEN_UMUM]);
                    }
                    elseif($type == 4) {
                        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD]);
                        $query->andWhere(['<>', 'carabayar_id', DocoConstants::CB_PEN_UMUM]);
                    }
                    elseif($type == 5) {
                        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI]);
                        $query->andWhere(['carabayar_id' => DocoConstants::CB_PEN_UMUM]);
                    }
                    elseif($type == 6) {
                        $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RI]);
                        $query->andWhere(['<>', 'carabayar_id', DocoConstants::CB_PEN_UMUM]);
                    }
                    elseif($type == 7) {
                        $query->andWhere(['carabayar_id' => DocoConstants::CB_PEN_UMUM]);
                    }
                    else {
                        $query->andWhere(['<>', 'carabayar_id', DocoConstants::CB_PEN_UMUM]);
                    }
                }
                unset($_GET['advanced-filter']['type']);
            }
        }
        
        $query->andWhere(['between', 'tgl_masuk', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return [
            'data' => $provider->getModels(),
            '_meta' => [
                'totalCount' => $provider->getTotalCount(),
                'pageCount' => $provider->getPagination()->getPageCount(),
                'currentPage' => $provider->getPagination()->getPage() ? $provider->getPagination()->getPage() : 1,
                'perPage' => $provider->getPagination()->getPageSize(),
            ],
        ];
    }

    public function actionDetail()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $dokter_id = $request->get('dokter_id', null);
        return LaporanPemeriksaanDokterView::find()->where([
            'pendaftaran_id' => $id,
            'dokterpenanggungjawab_id' => $dokter_id,
        ])->one();
    }

    public function actionGetDataDetail()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $dokter_id = $request->get('dokter_id', null);
        $model = new PemeriksaanDokterDetailView;
        $query = $model::find(true);
        $query->where(['pendaftaran_id' => $id, 'dokterpenanggungjawab_id' => $dokter_id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $provider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return [
            'data' => $provider->getModels(),
            '_meta' => [
                'totalCount' => $provider->getTotalCount(),
                'pageCount' => $provider->getPagination()->getPageCount(),
                'currentPage' => $provider->getPagination()->getPage() ? $provider->getPagination()->getPage() : 1,
                'perPage' => $provider->getPagination()->getPageSize(),
            ],
        ];
    }

    private function getData()
    {
        $model = new LaporanRekapJasaDokterView;
        $query = $model::find(true);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startPulang = date('Y-m-d 00:00:00');
        $endPulang = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tgl_tindakan']) && !empty($advancedFilter['tgl_tindakan'])) {
                $explode = explode(" - ", $advancedFilter['tgl_tindakan']);
                
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_tindakan']);
            }

            if (isset($advancedFilter['tgl_pasienpulang']) && !empty($advancedFilter['tgl_pasienpulang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pasienpulang']);
                
                if(count($explode) == 2) {
                    $startPulang = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endPulang = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                $query->andWhere(['between', 'tgl_pasienpulang', $startPulang, $endPulang]);
                unset($_GET['advanced-filter']['tgl_pasienpulang']);
            }

            if(isset($advancedFilter['tgl_flag']) && !empty($advancedFilter['tgl_flag'])) {
                $startFlag = date('Y-m-d 00:00:00');
                $endFlag = date('Y-m-d 23:59:59');
        
                $explode = explode(" - ", $advancedFilter['tgl_flag']);
                if(count($explode) == 2) {
                    $startFlag = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endFlag = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', 'pembayaranjasadokter_t.tgl_flag', $startFlag, $endFlag]);
                unset($_GET['advanced-filter']['tgl_flag']);
            }

            if (isset($advancedFilter['status_bayar_id']) && !empty($advancedFilter['status_bayar_id'])) {
                $status_bayar_id = $advancedFilter['status_bayar_id'];
                $query->andWhere(['status_bayar_id' => $status_bayar_id]);
                unset($_GET['advanced-filter']['status_bayar_id']);
            }

            if (isset($advancedFilter['ruangan_id']) && !empty($advancedFilter['ruangan_id'])) {
                $ruangan_id = $advancedFilter['ruangan_id'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if (isset($advancedFilter['carabayar_id']) && !empty($advancedFilter['carabayar_id'])) {
                $carabayar_id = $advancedFilter['carabayar_id'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_id']);
            }

            if (isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
                $penjamin_id = $advancedFilter['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_id']);
            }

            if (isset($advancedFilter['kelaspelayanan_id']) && !empty($advancedFilter['kelaspelayanan_id'])) {
                $kelaspelayanan_id = $advancedFilter['kelaspelayanan_id'];
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
                unset($_GET['advanced-filter']['kelaspelayanan_id']);
            }

            if (isset($advancedFilter['pelayanan']) && !empty($advancedFilter['pelayanan'])) {
                $pelayanan = $advancedFilter['pelayanan'];
                $query->andWhere(['pelayanan' => $pelayanan]);
                unset($_GET['advanced-filter']['pelayanan']);
            }

            if (isset($advancedFilter['dokterpenanggungjawab_id']) && !empty($advancedFilter['dokterpenanggungjawab_id'])) {
                $dokterpenanggungjawab_id = $advancedFilter['dokterpenanggungjawab_id'];
                $query->andWhere(['dokterpenanggungjawab_id' => $dokterpenanggungjawab_id]);
                unset($_GET['advanced-filter']['dokterpenanggungjawab_id']);
            }

            if(isset($advancedFilter['flag_jasdok']) && !empty($advancedFilter['flag_jasdok'])) {
                if($advancedFilter['flag_jasdok'] == 1) {
                    $query->andWhere(['not', ['pembayaranjasadokter_t.tgl_flag' => null]]);
                } else {
                    $query->andWhere(['pembayaranjasadokter_t.tgl_flag' => null]);
                }
                unset($_GET['advanced-filter']['flag_jasdok']);
            }
        }
        
        $query->andWhere(['between', 'tgl_tindakan', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->getData();
        $limit = 20;
        $countData = count($data->asArray()->all());
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$limit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanRekapJasaDokterExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportLaporanRekapJasaDokter' => [
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
                'UploadLaporanRekapJasaDokter' => [
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

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Rekap Jasa Dokter.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionFlagJasaDokter() {
        $request = Yii::$app->request;
        try {
            if($request->post()) {
                $data = $field = [];
                $post = $request->post();
                foreach($post as $k => $v) {
                    $additional = json_encode($v);
                    $concat = [$v["no_pendaftaran"], $v["dokterpenanggungjawab_id"], $v["tindakanpelayanan_id"]];
                    $hash = implode("", $concat);
                    $data[] = [
                        date("Y-m-d H:i:s"),
                        $v["pendaftaran_id"],
                        $v["dokterpenanggungjawab_id"],
                        $v["tindakanpelayanan_id"],
                        $v["tarif_tindakankomp"],
                        $additional,
                        $hash,
                        date("Y-m-d H:i:s"),
                        Yii::$app->user->identity->pegawai_id,
                    ];
                }
                $field= [
                    "tgl_flag",
                    "pendaftaran_id",
                    "pegawai_id",
                    "tindakanpelayanan_id",
                    "tarif_tindakan",
                    "additional_data",
                    "flag_key",
                    "created_date",
                    "created_by",
                ];
                $insert = Yii::$app->db->createCommand()->batchInsert('pembayaranjasadokter_t',$field,$data)->execute();
                if($insert) {
                    return [
                        'success' => 
                        [
                            'status' => 200,
                            'title' => "Proses berhasil",
                            'text' => "Data berhasil disimpan",
                        ],
                        'message' => "Data berhasil disimpan",
                    ];
                }
                else {
                    return [
                        'false' => 
                        [
                            'status' => 422,
                            'title' => "Proses gagal",
                            'text' => "Terjadi kesalahan pada server",
                        ],
                        'message' => "Data gagal disimpan",
                    ];
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
}