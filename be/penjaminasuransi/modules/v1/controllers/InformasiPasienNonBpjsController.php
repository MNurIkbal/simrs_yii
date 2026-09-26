<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoPrint;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoConstants;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\SebabDiagnosa;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\SuratKetDokter;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\InfoKoreksiPasien;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\InfoPasienNonBpjsView;
use Doco\components\DocoConstansId;

class InformasiPasienNonBpjsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienNonBpjsView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index']        = ['GET'];
        $verbs['export-excel'] = ['GET'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPasienNonBpjsView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            // Tanggal Pasien Pulang/Keluar
            if (isset($_GET['advanced-filter']['tglpasienpulang_awal']) && isset($_GET['advanced-filter']['tglpasienpulang_akhir'])) {
                $start = $_GET['advanced-filter']['tglpasienpulang_awal'];
                $end   = $_GET['advanced-filter']['tglpasienpulang_akhir'];
            }

            // Cara Bayar
            if (isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            // Penjamin
            if (isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            // Instalasi
            if (isset($_GET['advanced-filter']['instalasi_nama'])) {
                $instalasi_id = $_GET['advanced-filter']['instalasi_nama'];
                $query->andWhere(['instalasi_id' => $instalasi_id]);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            // Ruangan
            if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            // Status SKD
            if (isset($_GET['advanced-filter']['status_skd'])) {
                $status_skd = $_GET['advanced-filter']['status_skd'];
                $status_skd = ($status_skd == 1) ? true : false;
                $query->andWhere(['is_skd' => $status_skd]);
                unset($_GET['advanced-filter']['status_skd']);
            }

            // Status Pengajuan
            if (isset($_GET['advanced-filter']['statuspengajuan_nama'])) {
                $statuspengajuan_nama = $_GET['advanced-filter']['statuspengajuan_nama'];
                $query->andWhere(['statuspengajuan_id' => $statuspengajuan_nama]);
                unset($_GET['advanced-filter']['statuspengajuan_nama']);
            }
        }

        $query->andWhere(['between', 'tglpasienpulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        // Cara Bayar
        $modelCaraBayar = new CaraBayar;
        $groupCaraBayarIds = [
            DocoConstants::GROUP_BPJS,
            DocoConstants::GROUP_JAMINAN,
        ];
        $queryCaraBayar = $modelCaraBayar::find()
            ->where(['is_active' => true])
            ->andWhere(['in', 'groupcarabayar_id', $groupCaraBayarIds]);
            $queryCaraBayar = $queryCaraBayar->asArray()->all();

        // Penjamin
        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()->where(['is_active' => true]);
        $queryPenjamin = $queryPenjamin->asArray()->all();

        // Instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()->where(['is_active' => true, 'instalasi_id'=>[DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RI, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_LAB, DocoConstants::INST_ID_RAD]])
            ->orderBy('instalasi_nama');
        $queryInstalasi = $queryInstalasi->asArray()->all();

        // Ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()->where(['is_active' => true, 'instalasi_id'=>[DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RI, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_LAB, DocoConstants::INST_ID_RAD]])
            ->orderBy('ruangan_nama');
        $queryRuangan = $queryRuangan->asArray()->all();

        // Status Klaim
        $modelStatus = new Lookup;
        $queryStatus = $modelStatus::find()
            ->where(['is_active' => true, 'lookup_type' => 'pengajuan_klaim']);
        $queryStatus = $queryStatus->asArray()->all();

        // Status Verifikasi
        $getStatusVerif = Lookup::find(true)->where(['lookup_type' => 'status_verifikasi'])->andWhere(['ILIKE', 'lookup_name', 'koreksi'])->asArray()->all();
        $defaultPenjaminBpjs = (new DocoConstansId)->actionGetId('penjamin_bpjs_default');

        return [
            'cara_bayar'       => $queryCaraBayar,
            'penjamin'         => $queryPenjamin,
            'instalasi'        => $queryInstalasi,
            'ruangan'          => $queryRuangan,
            'status_klaim'     => $queryStatus,
            'status_verifikasi'=> $getStatusVerif,
            'default_penjamin_bpjs' => $defaultPenjaminBpjs,
        ];
    }

    public function actionExportExcel()
    {
        $model = new InfoPasienNonBpjsView;
        $query = $model::find();

        $title              = 'Informasi Pasien Jaminan Asuransi';
        $tgl_awal           = date('Y-m-d 00:00:00');
        $tgl_akhir          = date('Y-m-d 23:59:59');
        $carabayar_nama     = '';
        $penjamin_nama      = '';
        $arrTanggalKeluar   = [];
        $arrayCaraBayar     = [];
        $arrayPenjamin      = [];
        $arrayInstalasi     = [];
        $arrayRuangan       = [];
        $arrayStatusPengajuan = [];
        $arrayStatus        = [];
        $arrayStatusKoreksi = [];
        $arrayNamaPasien    = [];
        $arrayNoPendaftaran = [];
        $arrayNoRm          = [];

        if (isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];

            // Tanggal Pasien Pulang/Keluar
            if (isset($advancedFilters['tglpasienpulang'])) {
                $helper          = new DocoHelpers();
                $tglPasienPulang = $helper->parsingRangeDate($advancedFilters['tglpasienpulang']);

                $advancedFilters['tglpasienpulang_awal']  = $tglPasienPulang['startDate'];
                $advancedFilters['tglpasienpulang_akhir'] = $tglPasienPulang['endDate'];
                
                $tgl_awal  = $advancedFilters['tglpasienpulang_awal'];
                $tgl_akhir = $advancedFilters['tglpasienpulang_akhir'];

                $arrTanggalKeluar = [Yii::t('app', 'Tanggal Keluar') => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            } else {
                $arrTanggalKeluar = [Yii::t('app', 'Tanggal Keluar') => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            }

            // Cara Bayar
            if (isset($advancedFilters['carabayar_nama'])) {
                $carabayar_id = $advancedFilters['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                $cara_bayar = CaraBayar::findOne($carabayar_id);
                $carabayar_nama = $cara_bayar->carabayar_nama;
                $arrayCaraBayar = [Yii::t('app', "Cara Bayar") => $carabayar_nama];
            } else {
                $arrayCaraBayar = [Yii::t('app', "Cara Bayar") => '-'];
            }

            // Penjamin
            if (isset($advancedFilters['penjamin_nama'])) {
                $penjamin_id = $advancedFilters['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                $penjamin = Penjamin::findOne($penjamin_id);
                $penjamin_nama = $penjamin->penjamin_nama;
                $arrayPenjamin = [Yii::t('app', "Penjamin") => $penjamin_nama];
            } else {
                $arrayPenjamin = [Yii::t('app', "Penjamin") => '-'];
            }

            // Instalasi Nama
            if (isset($advancedFilters['instalasi_nama'])) {
                $instalasi_id = $advancedFilters['instalasi_nama'];
                $query->andWhere(['instalasi_id' => $instalasi_id]);
                $instalasi = Instalasi::findOne($instalasi_id);
                $instalasi_nama = $instalasi->instalasi_nama;
                $arrayInstalasi = [Yii::t('app', "Instalasi") => $instalasi_nama];
            } else {
                $arrayInstalasi = [Yii::t('app', "Instalasi") => '-'];
            }

            // Ruangan Nama
            if (isset($advancedFilters['ruangan_nama'])) {
                $ruangan_id = $advancedFilters['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                $ruangan = Ruangan::findOne($ruangan_id);
                $ruangan_nama = $ruangan->ruangan_nama;
                $arrayRuangan = [Yii::t('app', "Ruangan") => $ruangan_nama];
            } else {
                $arrayRuangan = [Yii::t('app', "Ruangan") => '-'];
            }

            // Status Pengajuan
            if (isset($advancedFilters['statuspengajuan_nama'])) {
                $statuspengajuan_nama = $advancedFilters['statuspengajuan_nama'];
                $query->andWhere(['statuspengajuan_id' => $statuspengajuan_nama]);
                $pengajuanNama = Lookup::find()->where(['lookup_id' => $statuspengajuan_nama])->one();
                $arrayStatusPengajuan = [Yii::t('app', "Status Pengajuan") => $pengajuanNama->lookup_name];
            } else {
                $arrayStatusPengajuan = [Yii::t('app', "Status Pengajuan") => '-'];
            }

            // Status SKD
            if (isset($advancedFilters['status_skd'])) {
                $status_skd = $advancedFilters['status_skd'];
                $status_skd = ($status_skd == 1) ? true : false;
                $query->andWhere(['is_skd' => $status_skd]);
                $status = ($status_skd) ? 'Sudah Dibuat' : 'Belum Dibuat';
                $arrayStatus = [Yii::t('app', "Status SKD") => $status];
            } else {
                $arrayStatus = [Yii::t('app', "Status SKD") => '-'];
            }

            // Status Koreksi
            if (isset($advancedFilters['status_verifikasi'])) {
                $status_verif = $advancedFilters['status_verifikasi'];

                $query->andWhere(['status_verifikasi' => $status_verif]);
                
                $arrayStatusKoreksi = [Yii::t('app', "Status Koreksi") => $status];
            } else {
                $arrayStatusKoreksi = [Yii::t('app', "Status Koreksi") => '-'];
            }

            // No Rekap Medik
            if (isset($advancedFilters['no_rekam_medik'])) {
                $query->andWhere(['ILIKE', 'no_rekam_medik', $advancedFilters['no_rekam_medik']]);
                $arrayNoRm = [Yii::t('app', 'No Rekam Medik') => $advancedFilters['no_rekam_medik']];
            } else {
                $arrayNoRm = [Yii::t('app', 'No Rekam Medik') => '-'];
            }

            // No Pendaftaran
            if (isset($advancedFilters['no_pendaftaran'])) {
                $query->andWhere(['ILIKE', 'no_pendaftaran', $advancedFilters['no_pendaftaran']]);
                $arrayNoPendaftaran = [Yii::t('app', 'No Pendaftaran') => $advancedFilters['no_pendaftaran']];
            } else {
                $arrayNoPendaftaran = [Yii::t('app', 'No Pendaftaran') => '-'];
            }

            // Nama Pasien
            if (isset($advancedFilters['nama_pasien'])) {
                $query->andWhere(['ILIKE', 'nama_pasien', $advancedFilters['nama_pasien']]);
                $arrayNamaPasien = [Yii::t('app', 'Nama Pasien') => $advancedFilters['nama_pasien']];
            } else {
                $arrayNamaPasien = [Yii::t('app', 'Nama Pasien') => '-'];
            }
        } else {
            $arrTanggalKeluar = [Yii::t('app', 'Tanggal Keluar')   => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            $arrayCaraBayar = [Yii::t('app', "Cara Bayar")         => '-'];
            $arrayPenjamin = [Yii::t('app', "Penjamin")            => '-'];
            $arrayInstalasi = [Yii::t('app', "Instalasi")          => '-'];
            $arrayRuangan = [Yii::t('app', "Ruangan")              => '-'];
            $arrayStatusPengajuan = [Yii::t('app', 'Status Pengajuan') => '-'];
            $arrayStatus = [Yii::t('app', "Status SKD")            => '-'];
            $arrayStatusKoreksi = [Yii::t('app', 'Status Koreksi') => '-'];
            $arrayNamaPasien = [Yii::t('app', 'Nama Pasien')       => '-'];
            $arrayNoPendaftaran = [Yii::t('app', 'No Pendaftaran') => '-'];
            $arrayNoRm = [Yii::t('app', 'No Rekam Medik')          => '-'];
        }

        $additional = array_merge(
                        $arrTanggalKeluar,
                        $arrayCaraBayar,
                        $arrayPenjamin,
                        $arrayInstalasi,
                        $arrayRuangan,
                        $arrayStatusPengajuan,
                        $arrayStatus,
                        $arrayStatusKoreksi,
                        $arrayNamaPasien,
                        $arrayNoPendaftaran,
                        $arrayNoRm
                    );

        $header = array_merge($additional);
        $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);

        $options = [
            array("uploadPath" => "./uploads"),
            "titleStyle" => [
                "fontSize"   => 15,
                "alignment"  => "center",
                "fontWeight" => 600,
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'A',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'B',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'L',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode'   => 'general'
                ],
            ],
        ];

        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue                                          = [];
            $newValue[\Yii::t('app', 'Data Pasien')]           = $value['nama_pasien'] . "\n" . $value['no_rekam_medik'] . "\n" . $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'Tanggal Masuk')]         = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $newValue[\Yii::t('app', 'Tanggal Keluar')]        = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
            $newValue[\Yii::t('app', 'No Invoice')]            = DocoHelpers::coalesce($value['no_invoice'], '-');
            $newValue[\Yii::t('app', 'Cara Bayar / Penjamin')] = $value['carabayar_nama'] . "\n" . " / " . $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Instalasi / Ruangan')]   = $value['instalasi_nama'] . "\n" . " / " . $value['ruangan_nama'];
            $totalTagihan = isset($value['total_ditagihkan']) ? $value['total_ditagihkan'] : $value['total_tagihan'];
            $newValue[\Yii::t('app', 'Tagihan')]               = DocoHelpers::coalesce($totalTagihan, 0);
            $newValue[\Yii::t('app', 'Jumlah Dibayarkan Pasien')] = DocoHelpers::coalesce($value['total_sdh_bayar'], 0);
            $newValue[\Yii::t('app', 'Jumlah Piutang')]        = DocoHelpers::coalesce($value['total_asuransi'], 0);
            $newValue[\Yii::t('app', 'Jumlah Pembayaran')]     = DocoHelpers::coalesce($value['jumlah_pembayaran'], 0);
            $newValue[\Yii::t('app', 'Jumlah Diskon')]          = DocoHelpers::coalesce($value['total_discountpembayaran'], 0);
            $newValue[\Yii::t('app', 'Sisa Tagihan')]          = DocoHelpers::coalesce($value['total_sisa_tagihan'], 0);
            $newValue[\Yii::t('app', 'Status Pengajuan')]      = $value['statuspengajuan_nama'] ? $value['statuspengajuan_nama'] : '-';
            $newValue[\Yii::t('app', 'Status SKD')]            = $value['status_skd'];
            $newValue[\Yii::t('app', 'Status Koreksi')]        = $value['status_verif'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode#   => periode tanggal
    * @attribute #tanggal#   => tanggal sekarang
    * @attribute #jabatan#   => jabatan
    * @attribute #nip#       => nip
    * @attribute #title#     => title
    * @attribute #pegawai#   => pegawai mengetahui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title   = 'Informasi Pasien Jaminan Asuransi';
        $model   = new InfoPasienNonBpjsView;
        $query   = $model::find();

        $tgl_awal  = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        
        if ($request->get('advanced-filter')) {
            $advancedFilters = $request->get('advanced-filter');

            // Tanggal Keluar/Pulang
            if (isset($advancedFilters['tglpasienpulang'])) {
                $helper = new DocoHelpers;
                $tglPendaftaranRange = $helper->parsingRangeDate($advancedFilters['tglpasienpulang']);

                $tgl_awal = $tglPendaftaranRange['startDate'];
                $tgl_akhir = $tglPendaftaranRange['endDate'];
            }

            // Cara Bayar
            if (isset($advancedFilters['carabayar_nama'])) {
                $carabayar_id = $advancedFilters['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
            }

            // Penjamin Nama
            if (isset($advancedFilters['penjamin_nama'])) {
                $penjamin_id = $advancedFilters['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
            }

            // Instalasi Nama
            if (isset($advancedFilters['instalasi_nama'])) {
                $instalasi_id = $advancedFilters['instalasi_nama'];
                $query->andWhere(['instalasi_id' => $instalasi_id]);
            }

            // Ruangan Nama
            if (isset($advancedFilters['ruangan_nama'])) {
                $ruangan_id = $advancedFilters['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }

            // Status SKD
            if (isset($advancedFilters['status_skd'])) {
                $status_skd = $advancedFilters['status_skd'];
                $status_skd = ($status_skd == 1) ? true : false;
                $query->andWhere(['is_skd' => $status_skd]);
            }

            // No RM
            if (isset($advancedFilters['no_rekam_medik'])) {
                $query->andWhere(['ILIKE', 'no_rekam_medik', $advancedFilters['no_rekam_medik']]);
            }

            // No Pendaftaran
            if (isset($advancedFilters['no_pendaftaran'])) {
                $query->andWhere(['ILIKE', 'no_pendaftaran', $advancedFilters['no_pendaftaran']]);
            }

            // Nama Pasien
            if (isset($advancedFilters['nama_pasien'])) {
                $query->andWhere(['ILIKE', 'nama_pasien', $advancedFilters['nama_pasien']]);
            }

            if (isset($advancedFilters['statuspengajuan_nama'])) {
                $statusPengajuanId = $advancedFilters['statuspengajuan_nama'];
                $query->andWhere(['statuspengajuan_id' => $statusPengajuanId]);
            }
        }

        $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);

        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai         = PegawaiView::find()->where(['jabatan_id' => $lookupTransaksi->kode_id])->andWhere(['not', ['pegawai_last_modified_date' => null]])->orderBy(['pegawai_last_modified_date' => SORT_DESC])->one();

        $jabatan    = $pegawai ? $pegawai->jabatan_nama : '';
        $nip        = $pegawai ? $pegawai->nomorindukpegawai : '';
        $mengetahui = $pegawai ? $pegawai->nama_pegawai : '';
        $print      = new DocoPrint();
        $print->shrink_tables_to_fit = 1;
        $print->attributes = [
            '#periode#'   => date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir)),
            '#title#'     => $title,
            '#tanggal#'   => date('d M Y H:i:s'),
            '#pegawai#'   => $mengetahui,
            '#jabatan#'   => $jabatan,
            '#nip#'       => $nip,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }

    public function actionGetAttributes($id, $admisi)
    {
        $admisi = $admisi ? $admisi : null;

        $sebab = SebabDiagnosa::find()->select([
            'sebabdiagnosa_id',
            'sebabdiagnosa_nama'
        ])->where([
            'is_active' => true
        ])->all();

        $model = InfoKoreksiPasien::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->one();

        // $detail = InfoKoreksiPasienDetail::find()->where([
        $detail = KoreksiDiagnosaView::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->all();

        $skd = SuratKetDokter::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->one();

        $defaultDokter = [];
        if (!empty($skd->dokbedah_id)) {
            $pegawai = Pegawai::find()->select([
                'nama_pegawai'
            ])->where([
                'pegawai_id' => $skd->dokbedah_id
            ])->one();
            $defaultDokter[$skd->dokbedah_id] = $pegawai->nama_pegawai;
        } else {
            $condition = "pendaftaran_id = {$id} -- AND pasienadmisi_id IS NULL";
            // if ($admisi) {
            //     $condition = "pendaftaran_id = {$id} AND pasienadmisi_id = {$admisi}";
            // }
            $query = Yii::$app->db->createCommand("
                SELECT pegawai_m.nama_pegawai, pegawai_m.pegawai_id FROM rencanaoperasi_t
                JOIN pegawai_m ON pegawai_m.pegawai_id = rencanaoperasi_t.dr_operator_id
                WHERE {$condition}
            ")->queryOne();
            if (!empty($query)) {
                $defaultDokter[$query['pegawai_id']] = $query['nama_pegawai'];
            }
        }

        return [
            'header' => $model,
            'skd' => $skd,
            'sebab' => $sebab,
            'detail' => $detail,
            'default_dokter' => $defaultDokter
        ];
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $term = $request->get('type');
        $word = $request->get('term');
        if ($term && $word) {
            return InfoDiagnosa::find()->where([
                'ILIKE', 'LOWER(tabularlist_versi)', strtolower($term)
            ])->andFilterWhere([
                'OR',
                ['ILIKE', 'LOWER(diagnosa_kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_namalainnya)', strtolower($word)]
            ])->limit(10)->all();
        }
        return [];
    }

    public function actionSave($id, $admisi)
    {
        $admisi = $admisi ? $admisi : null;
        $request = Yii::$app->request;
        $skd = SuratKetDokter::find()->where([
            'pendaftaran_id' => $id,
            'pasienadmisi_id' => $admisi
        ])->one();
        if (empty($skd)) $skd = new SuratKetDokter;
        $skd->attributes = $request->post();
        $skd->pendaftaran_id = $id;
        $skd->pasienadmisi_id = $admisi;
        $skd->tgl_kecelakaan = !empty($skd->tgl_kecelakaan) ? date('Y-m-d',strtotime($skd->tgl_kecelakaan)) : null;
        $skd->tgl_diagnosa = !empty($skd->tgl_diagnosa) ? date('Y-m-d',strtotime($skd->tgl_diagnosa)) : null;
        $skd->tgl_konsul = !empty($skd->tgl_konsul) ? date('Y-m-d',strtotime($skd->tgl_konsul)) : null;
        $skd->tgl_gejala = !empty($skd->tgl_gejala) ? date('Y-m-d',strtotime($skd->tgl_gejala)) : null;
        if (empty($skd->is_kecelakaaan)) {
            $skd->tgl_kecelakaan = null;
            $skd->sebab_kecelakaan = null;
        }

        $skd->tgl_diag_sama = !empty($skd->tgl_diag_sama) ? date('Y-m-d',strtotime($skd->tgl_diag_sama)) : null;
        if (empty($skd->is_diag_sama)) {
            $skd->tgl_diag_sama = null;
            $skd->diag_sama = null;
            $skd->nama_rs = null;
            $skd->nama_dokter_rs = null;
        }

        $skd->tgl_konsultasi = !empty($skd->tgl_konsultasi) ? date('Y-m-d',strtotime($skd->tgl_konsultasi)) : null;
        if (empty($skd->is_konsultasi)) {
            $skd->tgl_konsultasi = null;
            $skd->diag_konsultasi = null;
            $skd->nam_rs_konsul = null;
            $skd->nama_dr_konsul = null;
        }

        if (empty($skd->is_rujukan)) {
            $skd->dokter_rujukan = null;
            $skd->alamat = null;
        }

        if ($skd->save()) {
            if ($id) {
                Yii::$app->db->createCommand("
                    UPDATE pendaftaran_t SET is_skd = true WHERE pendaftaran_id = {$id}
                ")->execute();
            }

            if ($admisi) {
                Yii::$app->db->createCommand("
                    UPDATE pasienadmisi_t SET is_skd = true WHERE pasienadmisi_id = {$admisi}
                ")->execute();
            }
            return [
                'messages' => 'Data Berhasil di simpan',
                'id_parent' => DocoHelpers::encrypt($skd->suratketdokter_id)
            ];
        } else {
            return [
                'status' => 422,
                'data' => $skd->errors
            ];
        }
    }

    /**
    * @controller actionCetakSkd
    * @attribute #nama_pasien# => Untuk menampilkan Nama pasien
    * @attribute #no_rm# => Untuk menampilkan No Rekammedik
    * @attribute #no_ktp# => Untuk menampilkan no ktp
    * @attribute #jenis_kelamin# => Untuk menampilkan jenis kelamin
    * @attribute #tanggal_perawatan# => Untuk menampilkan tanggal perawatan
    * @attribute #kelas_pelayanan# => Untuk menampilkan kelas pelayanan
    * @attribute #jenis_layanan# => Untuk menampilkan jenis layanan
    * @attribute #tanggal_gejala# => Untuk menampilkan tanggal gejala
    * @attribute #tanggal_konsul# => Untuk menampilkan tanggal konsultasi
    * @attribute #gejala_penyakit# => Untuk menampilkan gejala penyakit
    * @attribute #diag_utama# => Untuk menampilkan diagnosa utama
    * @attribute #diag_tambahan# => Untuk menampilkan diagnosa tambahan
    * @attribute #penyabab_diagnosa# => Untuk menampilkan penyebab diagnosa
    * @attribute #tanggal_diagnosa# => Untuk menampilkan tanggal diagnosa
    * @attribute #terapi_bedah# => Untuk menampilkan terapi bedah
    * @attribute #jenis_operasi# => Untuk menampilkan jenis operasi
    * @attribute #nama_dokter_bedah# => Untuk menampilkan nama dokter bedah
    * @attribute #hasil_penunjang# => Untuk menampilkan hasil penunjang
    * @attribute #hub_diagnosa# => Untuk menampilkan hubungan diagnosa
    * @attribute #tanggal_kecelakaan# => Untuk menampilkan tanggal kecelakaan
    * @attribute #penyabab_kecelakaan# => Untuk menampilkan penyebab kecelakaan
    * @attribute #tanggal_rawat# => Untuk menampilkan tanggal rawat
    * @attribute #diagnosa_rawat# => Untuk menampilkan diagnosa rawat
    * @attribute #nama_dokter_rawat# => Untuk menampilkan nama dokter rawat
    * @attribute #nama_rs_rawat# => Untuk menampilkan nama rumah sakit
    * @attribute #tgl_konsul# => Untuk menampilkan tanggal konsul
    * @attribute #diagnosa_konsul# => Untuk menampilkan diagnosa konsul
    * @attribute #nama_dokter_konsul# => Untuk menampilkan nama dokter konsul
    * @attribute #nama_rs_konsul# => Untuk menampilkan rs konsul
    * @attribute #dokter_rujuk# => Untuk menampilkan dokter rujukan
    * @attribute #alamat_rujuk# => Untuk menampilkan alamat rujuk
    */
    public function actionCetakSkd($id)
    {
        $skd = SuratKetDokter::find()->where([
            'suratketdokter_id' => $id
        ])->one();

        $idPendaftran = !empty($skd->pendaftaran_id) ? $skd->pendaftaran_id : null;
        $idAdmisi = !empty($skd->pasienadmisi_id) ? $skd->pasienadmisi_id : null;

        $sebab = SebabDiagnosa::find()->select([
            'sebabdiagnosa_id',
            'sebabdiagnosa_nama'
        ])->where([
            'is_active' => true
        ])->all();

        $model = InfoKoreksiPasien::find()->where([
            'pendaftaran_id' => $idPendaftran,
            'pasienadmisi_id' => $idAdmisi
        ])->one();

        $defaultDokter = '';
        if (!empty($skd->dokbedah_id)) {
            $pegawai = Pegawai::find()->select([
                'nama_pegawai'
            ])->where([
                'pegawai_id' => $skd->dokbedah_id
            ])->one();
            $defaultDokter = $pegawai->nama_pegawai;
        }
        $print = new DocoPrint();
        $tanggalPerawatan = (!empty($model->tgl_pendaftaran)
                    ? date('d-M-Y',strtotime($model->tgl_pendaftaran)) : '-') .' s/d ' . (!empty($model['tglpasienpulang'])
                                    ? date('d-M-Y',strtotime($model['tglpasienpulang'])) : date('d-M-Y'));

        $diagUtama = json_decode($skd->diag_utama,true);
        $diagTambahan = json_decode($skd->diag_tambahan,true);
        $tambahan = '';
        if (is_array($diagTambahan)) {
            foreach ($diagTambahan as $key => $value) {
                $tambahan .= '<p style="text-align:center">';
                    $tambahan .= $value['text'] ;
                $tambahan .= '</p>';
            }
        }

        $print->attributes = [
            '#nama_pasien#' => $model->nama_pasien,
            '#jenis_kelamin#' => $model->jenis_kelamin,
            '#no_rm#' => $model->no_rekam_medik,
            '#no_ktp#' => $model->no_identitas_pasien,
            '#tanggal_perawatan#' => $tanggalPerawatan,
            '#kelas_pelayanan#' => $model->kelaspelayanan_nama,
            '#jenis_layanan#' => $model->instalasi_nama,
            '#tanggal_gejala#' => !empty($skd->tgl_gejala)
                                                    ? date('d-M-Y',strtotime($skd->tgl_gejala)) : date('d-M-Y'),
            '#tanggal_konsul#' => !empty($skd->tgl_konsul)
                                                    ? date('d-M-Y',strtotime($skd->tgl_konsul)) : date('d-M-Y'),
            '#gejala_penyakit#' => $skd->gejala_penyakit,
            '#diag_utama#' => isset($diagUtama['text']) ? $diagUtama['text'] : '-',
            '#diag_tambahan#' => $tambahan,
            '#penyabab_diagnosa#' => $skd->faktor_penyebab,
            '#tanggal_diagnosa#' => !empty($skd->tgl_diagnosa)
                                                    ? date('d-M-Y',strtotime($skd->tgl_diagnosa)) : date('d-M-Y'),
            '#terapi_bedah#' => $skd->terapi_tindakan,
            '#jenis_operasi#' => isset($skd->jenis_operasi) ? $skd->jenis_operasi == 0 ? 'Elective' : 'Emergency' : '-',
            '#nama_dokter_bedah#' => $defaultDokter,
            '#hasil_penunjang#' => $skd->hasil_penunjang,
            '#hub_diagnosa#' => $this->renderPartial('check-box',[
                'data' => $sebab,
                'selected' => $skd->sebebdiagnosa_id
            ]),
            '#tanggal_kecelakaan#' => !empty($skd->tgl_kecelakaan)
                ? date('d-M-Y',strtotime($skd->tgl_kecelakaan)) : '-',
            '#penyabab_kecelakaan#' => $skd->sebab_kecelakaan ? $skd->sebab_kecelakaan : '-',
            '#tanggal_rawat#' => !empty($skd->tgl_diag_sama)
                ? date('d-M-Y',strtotime($skd->tgl_diag_sama)) : '-',
            '#diagnosa_rawat#' => $skd->diag_sama ? $skd->diag_sama : '-',
            '#nama_dokter_rawat#' => $skd->nama_dokter_rs ? $skd->nama_dokter_rs : '-',
            '#nama_rs_rawat#' => $skd->nama_rs ? $skd->nama_rs : '-',
            '#tgl_konsul#' => !empty($skd->tgl_konsultasi)
                ? date('d-M-Y',strtotime($skd->tgl_konsultasi)) : '-',
            '#diagnosa_konsul#' => $skd->diag_konsultasi ? $skd->diag_konsultasi : '-',
            '#nama_dokter_konsul#' => $skd->nama_dr_konsul ? $skd->nama_dr_konsul : '-',
            '#nama_rs_konsul#' => $skd->nam_rs_konsul ? $skd->nam_rs_konsul : '-',
            '#dokter_rujuk#' => $skd->dokter_rujukan ? $skd->dokter_rujukan : '-',
            '#alamat_rujuk#' => $skd->alamat ? $skd->alamat : '-',
        ];

        $print->Output();
        die;
    }

    /**
    * @controller actionPrintRincian
    * @attribute #tanggal# => Tanggal Pendaftaran
    * @attribute #no_rm# => Nomor Rekam Medik
    * @attribute #no_pendaftaran# => Nomor Pendaftaran
    * @attribute #nama# => Nama pasien
    * @attribute #jenis_kasus_penyakit# => Jenis Kasus Penyakit
    * @attribute #dokter# => Nama dokter
    * @attribute #ruangan# => Ruangan
    * @attribute #kelas_pelayanan# => Kelas Pelayanan
    * @attribute #penjamin# => Penjamin
    * @attribute #cara_bayar# => Cara Bayar
    * @attribute #status_bayar# => Status Bayar
    * @attribute #riwayat_pembayaran# => Menampilkan Tabel tagihan riwayat pasien
    * @attribute #detail_tindakan# => Menampilkan detail tindakan
    * @attribute #total_tagihan# => Menampilkan detail tindakan
    * @attribute #total_uang_muka# => Menampilkan detail tindakan
    * @attribute #total_dibayar# => Menampilkan detail tindakan
    * @attribute #sisa_tagihan# => Menampilkan detail tindakan
    * @attribute #biaya_admin# => Menampilkan detail tindakan
    * @attribute #pembulatan# => Menampilkan detail tindakan
    **/

    public function actionPrintRincian($id)
    {
        $header = InfoDataPendaftaran::find()->where([
            'pendaftaran_id' => $id
        ])->one();

        $tagihan_detail = Yii::$app->db->createCommand("
            SELECT * FROM rincian_header_tagihan_pasien WHERE pendaftaran_id = {$id}
        ")->queryOne();

        $detail = Yii::$app->db->createCommand("
            SELECT * FROM infotagihandetail_v WHERE pendaftaran_id = {$id}
        ")->queryAll();

        $listData =[];
        foreach ($detail as $value) {
            $is_obat = isset($value['is_obat']) ? $value['is_obat'] : null;
            $instalasi = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
            $ruangan = isset($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : null;
            if (!isset($listData[$id])) {
                $listData[$id] = [
                    'obat' => [],
                    'tindakan' => [],
                    'penunjang' => []
                ];
            }

            if ($is_obat) {
                $listData[$id]['obat'][] = $value;
            } else {
                // Penunjang
                if (in_array($instalasi, DocoConstants::$exceptPenunjang)) {
                    $listData[$id]['tindakan'][$instalasi]['data'][] = $value;
                    $listData[$id]['tindakan'][$instalasi]['title'] = $ruangan;
                } else {
                    $listData[$id]['penunjang'][$instalasi]['data'][] = $value;
                    $listData[$id]['penunjang'][$instalasi]['title'] = $ruangan;
                }
            }
        }

        $query = $header;
        if (!empty($header)) {
            $countData = count($header);
            $dokter = !empty($header['nama_dok_ri']) ? $header['nama_dok_ri'] : $header['nama_dok_rj_rd'];
            $print = new DocoPrint();
            $sisa_tagihan = $tagihan_detail['total_tagihan'] - $tagihan_detail['total_asuransi'] - $tagihan_detail['total_sdh_bayar'] + $tagihan_detail['total_administrasi'] + $tagihan_detail['total_pembulatan'];
                $print->attributes = [
                    '#tanggal#' => isset($query['tgl_pendaftaran']) ? $query['tgl_pendaftaran'] : null,
                    '#no_rm#' => isset($query['no_rekam_medik']) ? $query['no_rekam_medik'] : null,
                    '#no_pendaftaran#' => isset($query['no_pendaftaran']) ? $query['no_pendaftaran'] : null,
                    '#nama#' => isset($query['nama_pasien']) ? $query['nama_pasien'] : null,
                    '#jenis_kasus_penyakit#' => isset($query['jeniskasuspenyakit_nama'])
                        ? $query['jeniskasuspenyakit_nama'] : null,
                    '#dokter#' => $dokter,
                    '#ruangan#' => isset($query['ruangan_nama'])
                        ? $query['ruangan_nama'] : null,
                    '#kelas_pelayanan#' => isset($query['kelaspelayanan_nama']) ? $query['kelaspelayanan_nama'] : null,
                    '#penjamin#' => isset($query['penjamin_nama']) ? $query['penjamin_nama'] : null,
                    '#cara_bayar#' => isset($query['carabayar_nama']) ? $query['carabayar_nama'] : null,
                    '#status_bayar#' => !empty($sisa_tagihan <= 0) ? 'Lunas' : 'Belum Lunas',
                    '#total_tagihan#' => isset($tagihan_detail['total_tagihan'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_tagihan']) : DocoHelpers::rupiahDisplay(0),
                    '#total_uang_muka#' => isset($tagihan_detail['total_uang_muka'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_uang_muka']) : DocoHelpers::rupiahDisplay(0),
                    '#total_dibayar#' => isset($tagihan_detail['total_sdh_bayar'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_sdh_bayar']) : DocoHelpers::rupiahDisplay(0),
                    '#sisa_tagihan#' => isset($tagihan_detail['total_tagihan'])
                        ? DocoHelpers::rupiahDisplay($sisa_tagihan) : DocoHelpers::rupiahDisplay(0),
                    '#biaya_admin#' => isset($tagihan_detail['total_administrasi'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_administrasi']) : DocoHelpers::rupiahDisplay(0),
                    '#pembulatan#' => isset($tagihan_detail['total_pembulatan'])
                        ? DocoHelpers::rupiahDisplay($tagihan_detail['total_pembulatan']) : DocoHelpers::rupiahDisplay(0),
                    '#subsidi_asuransi#' => isset($tagihan_detail['total_asuransi'])
                                                ? DocoHelpers::rupiahDisplay($tagihan_detail['total_asuransi'])
                                                    : DocoHelpers::rupiahDisplay(0),
                    '#detail_tindakan#' => $this->renderPartial('riwayat_pembayaran', [
                        'detail' => isset($listData[$id]) ? $listData[$id] : []
                    ]),
                ];

            $print->Output();
        }
    }
}