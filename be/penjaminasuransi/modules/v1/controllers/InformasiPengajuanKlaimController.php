<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\PegawaiView;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\InfoPengajuanKlaim;
use app\modules\v1\models\PengajuanKlaimView;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\PengajuanKlaimDetail;
use app\modules\v1\models\InfoPengajuanKlaimView;
use app\modules\v1\models\InfoPengajuanKlaimDetail;

class InformasiPengajuanKlaimController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPengajuanKlaimView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPengajuanKlaimView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {

            if (isset($_GET['advanced-filter']['tgl_pengajuanklaim_awal']) && isset($_GET['advanced-filter']['tgl_pengajuanklaim_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_pengajuanklaim_awal'];
                $end   = $_GET['advanced-filter']['tgl_pengajuanklaim_akhir'];
            }

            if (isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if (isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if (isset($_GET['advanced-filter']['s_pengajuanklaim'])) {
                $s_pengajuanklaim = $_GET['advanced-filter']['s_pengajuanklaim'];
                $query->andWhere(['status_pengajuanklaim' => $s_pengajuanklaim]);
                unset($_GET['advanced-filter']['s_pengajuanklaim']);
            }
        }
        
        $query->andWhere(['between', 'tgl_pengajuanklaim', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $additionalData = [
            'summary' => [
                'total_pengajuan' => $query->sum('total_piutang')
            ]
        ];
        $hasil = $this->activeDataProvider($query, $additionalData);
        return $hasil;

    }

    public function actionExportExcel()
    {
        $model = new InfoPengajuanKlaimView;
        $query = $model::find();
        $title = 'Informasi Pengajuan Klaim';

        $tgl_awal  = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        
        $arrayCaraBayar   = [];
        $arrayPenjamin    = [];
        $arrayNoPengajuan = [];
        $arrayStatus      = [];

        if (isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];

            if (isset($advancedFilters['tgl_pengajuanklaim'])) {
                $helper = new DocoHelpers;
                $tglPengajuanKlaimRange = $helper->parsingRangeDate($advancedFilters['tgl_pengajuanklaim']);

                $tgl_awal  = $tglPengajuanKlaimRange['startDate'];
                $tgl_akhir = $tglPengajuanKlaimRange['endDate'];
            }

            if (isset($advancedFilters['no_pengajuanklaim'])) {
                $noPengajuan = $advancedFilters['no_pengajuanklaim'];
                $noPengajuan = trim($noPengajuan);
                $query->andWhere(['ILIKE', 'no_pengajuanklaim', $noPengajuan]);
                $arrayNoPengajuan = [Yii::t('app', 'No Pengajuan') => $noPengajuan];
            } else {
                $arrayNoPengajuan = [Yii::t('app', 'No Pengajuan') => '-'];
            }

            if (isset($advancedFilters['carabayar_nama'])) {
                $carabayar_id = $advancedFilters['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                $cara_bayar = CaraBayar::findOne($carabayar_id);
                $carabayar_nama = $cara_bayar->carabayar_nama;
                $arrayCaraBayar = [Yii::t('app', "Cara Bayar") => $carabayar_nama];
            } else {
                $arrayCaraBayar = [Yii::t('app', "Cara Bayar") => "-"];
            }

            if (isset($advancedFilters['penjamin_nama'])) {
                $penjamin_id = $advancedFilters['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                $penjamin = Penjamin::findOne($penjamin_id);
                $penjamin_nama = $penjamin->penjamin_nama;
                $arrayPenjamin = [Yii::t('app', "Penjamin") => $penjamin_nama];
            } else {
                $arrayPenjamin = [Yii::t('app', "Penjamin") => "-"];
            }

            if (isset($advancedFilters['s_pengajuanklaim'])) {
                $s_pengajuanklaim = $advancedFilters['s_pengajuanklaim'];
                $query->andWhere(['status_pengajuanklaim' => $s_pengajuanklaim]);
                $status = Lookup::findOne($s_pengajuanklaim);
                $arrayStatus = [Yii::t('app', "Status") => $status->lookup_name];
            } else {
                $arrayStatus = [Yii::t('app', "Status") => "-"];
            }

            if (isset($advancedFilters['title'])) {
                $title = $advancedFilters['title'];
            }
        } else {
            $periode          = [ Yii::t('app', "Tanggal Pengajuan") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            $arrayNoPengajuan = [Yii::t('app', "No Pengajuan") => "-"];
            $arrayCaraBayar   = [Yii::t('app', "Cara Bayar") => "-"];
            $arrayPenjamin    = [Yii::t('app', "Penjamin") => "-"];
        }

        $periode    = [ Yii::t('app', "Tanggal Pengajuan") => ((date('d M Y', strtotime($tgl_awal)) . " - " . date('d M Y', strtotime($tgl_akhir))))];
        $additional = array_merge($arrayNoPengajuan, $arrayCaraBayar, $arrayPenjamin, $arrayStatus);
        $header     = array_merge($periode, $additional);

        $query->andWhere(['between', 'tgl_pengajuanklaim', $tgl_awal, $tgl_akhir]);

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
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode'   => 'general',
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ]
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
            ],
        ];

        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pengajuan')] = date('d M Y', strtotime($value['tgl_pengajuanklaim']));
            $newValue[\Yii::t('app', 'Tanggal Jatuh Tempo')] = !empty($value['tgl_jatuhtempo']) ? date('d M Y', strtotime($value['tgl_jatuhtempo'])) : '';
            $newValue[\Yii::t('app', 'No Pengajuan')] = $value['no_pengajuanklaim'];
            $newValue[\Yii::t('app', 'Cara Bayar / Penjamin')] = $value['carabayar_nama'] . "\n" . $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Total Pengajuan')] = $value['total_piutang'];
            $newValue[\Yii::t('app', 'Status')] = $value['s_pengajuanklaim'];
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
        $title   = 'Informasi Pengajuan Klaim';
        $model   = new InfoPengajuanKlaimView;
        $query   = $model::find();
        
        $tgl_awal  = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        
        if ($request->get('advanced-filter')) {
            $advancedFilters = $request->get('advanced-filter');

            if (isset($advancedFilters['tgl_pengajuanklaim'])) {
                $helper                 = new DocoHelpers;
                $tglPengajuanKlaimRange = $helper->parsingRangeDate($advancedFilters['tgl_pengajuanklaim']);
                
                $tgl_awal  = $tglPengajuanKlaimRange['startDate'];
                $tgl_akhir = $tglPengajuanKlaimRange['endDate'];
            }

            if (isset($advancedFilters['carabayar_nama'])) {
                $carabayar_id = $advancedFilters['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
            }

            if (isset($advancedFilters['penjamin_nama'])) {
                $penjamin_id = $advancedFilters['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
            }

            if (isset($advancedFilters['s_pengajuanklaim'])) {
                $s_pengajuanklaim = $advancedFilters['s_pengajuanklaim'];
                $query->andWhere(['status_pengajuanklaim' => $s_pengajuanklaim]);
            }

            if (isset($advancedFilters['no_rekam_medik'])) {
                $no_rekam_medik = $advancedFilters['no_rekam_medik'];
                $no_rekam_medik = trim($no_rekam_medik);
                $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
            }

            if (isset($advancedFilters['no_pendaftaran'])) {
                $no_pendaftaran = $advancedFilters['no_pendaftaran'];
                $no_pendaftaran = trim($no_pendaftaran);
                $query->andWhere(['ILIKE', 'no_pendaftaran', $no_pendaftaran]);
            }

            if (isset($advancedFilters['nama_pasien'])) {
                $nama_pasien = $advancedFilters['nama_pasien'];
                $nama_pasien = trim($nama_pasien);
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
            }

            if (isset($advancedFilters['no_pengajuanklaim'])) {
                $no_pengajuanklaim = $advancedFilters['no_pengajuanklaim'];
                $no_pengajuanklaim = trim($no_pengajuanklaim);
                $query->andWhere(['ILIKE', 'no_pengajuanklaim', $no_pengajuanklaim]);
            }
        }
        
        $query->andWhere(['between', 'tgl_pengajuanklaim', $tgl_awal, $tgl_akhir]);
        $query->orderBy(['tgl_pengajuanklaim' => SORT_DESC]);

        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai         = PegawaiView::find()->where(['jabatan_id' => $lookupTransaksi->kode_id])->andWhere(['not', ['pegawai_last_modified_date' => null]])->orderBy(['pegawai_last_modified_date' => SORT_DESC])->one();

        $jabatan           = ($pegawai) ? $pegawai->jabatan_nama : '';
        $nip               = ($pegawai) ? $pegawai->nomorindukpegawai : '';
        $mengetahui        = ($pegawai) ? $pegawai->nama_pegawai : '';
        $print             = new DocoPrint();
        $print->shrink_tables_to_fit  = 1;
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

    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $update = PengajuanKlaim::find()->where(['pengajuanklaim_id' => $id])->one();
            $update->status_pengajuanklaim = DocoConstants::BELUM_MELAKUKAN_PENGAJUAN;
            $update->save();

            (new PengajuanKlaim)->delete([
                'pengajuanklaim_id' => $id
            ]);

            (new Query)
            ->createCommand()
            ->delete('pengajuanklaimdetail_t', ['pengajuanklaim_id' => $id])
            ->execute();
    
            $transaction->commit();

            return [
                'title' => 'Proses Berhasil!',
                'text'  => 'Data pengajuan berhasil dihapus'
            ];
        } catch (\Throwable $th) {
            $transaction->rollBack();

            return ['error' => $th->getMessage()];
        }
    }

    public function actionDetail($id)
    {
        $header = InfoPengajuanKlaim::find()->where([
            'pengajuanklaim_id' => $id
        ])->one();
        return [
            'header' => $header
        ];
    }

    public function actionGetDataPengajuan($id)
    {
        $request = Yii::$app->request;
        $model = new InfoPengajuanKlaimDetail;
        $query = $model::find()->where([
            'pengajuanklaim_id' => $id
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
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

    /**
    * @controller actionListPengajuanPdf
    * @attribute #tanggal#             => Tanggal Pendaftaran 
    * @attribute #tanggal_masuk#'      => Untuk Menampilkan tanggal masuk
    * @attribute #tanggal_pengajuan#'  => Untuk menampilkan tanggal pengajuan
    * @attribute #cara_bayar#          => Untuk menampilkan cara bayar
    * @attribute #penjamin#            => Untuk menampilkan penjamin
    * @attribute #instalasi#           => Untuk menampilkan instalasi
    * @attribute #ruangan#             => Untuk menampilkan ruangan
    * @attribute #catatan#             => Untuk menampilkan catatan
    * @attribute #tanggal_pengajuan#   => Untuk menampilkan tanggal pengajuan
    * @attribute #tanggal_jatuh_tempo# => Untuk menampilkan tanggal jatuh tempo
    * @attribute #no_pengajuan#        => Untuk menampilkan no pengajuan
    * @attribute #total_pengajuan#     => Untuk menampilkan total pengajuan
    * @attribute #tanggal#             => Untuk menampilkan tanggal hari ini
    * @attribute #pegawai#             => Untuk menampilkan nama pegawai
    * @attribute #nip#                 => Untuk menampilkan nip pegawai
    * @attribute #list_data#           => Untuk menampilka list data pengajuan
    **/
    public function actionListPengajuanPdf($id)
    {
        $header = InfoPengajuanKlaim::find()->where(['pengajuanklaim_id' => $id])->asArray()->one();
        $query  = InfoPengajuanKlaimDetail::find()->where(['pengajuanklaim_id' => $id]);
        
        $tanggalMulai   = isset($header['tgl_pelayanandari']) ? date('d M Y',strtotime($header['tgl_pelayanandari'])) . ' s/d ' : '';
        $tanggalSelesai = isset($header['tgl_pelayanansampai']) ? date('d M Y',strtotime($header['tgl_pelayanansampai'])) : '';

        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai         = PegawaiView::find()->where(['jabatan_id' => $lookupTransaksi->kode_id])->andWhere(['not', ['pegawai_last_modified_date' => null]])->orderBy(['pegawai_last_modified_date' => SORT_DESC])->one();
        
        $jabatan = ($pegawai) ? $pegawai->jabatan_nama : '';
        $nip = ($pegawai) ? $pegawai->nomorindukpegawai : '';
        $mengetahui = ($pegawai) ? $pegawai->nama_pegawai : '';

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal_masuk#'       => "{$tanggalMulai}{$tanggalSelesai}",
            '#tanggal_pengajuan#'   => isset($header['tgl_pengajuanklaim']) ? date('d M Y H:i:s', strtotime($header['tgl_pengajuanklaim'])) : '-',
            '#cara_bayar#'          => isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '-' ,
            '#penjamin#'            => isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-',
            '#instalasi#'           => isset($header['instalasi_nama']) ? $header['instalasi_nama'] : '-',
            '#ruangan#'             => isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '-',
            '#catatan#'             => isset($header['catatan']) ? $header['catatan'] : '',
            '#tanggal_jatuh_tempo#' => isset($header['tgl_jatuhtempo']) ? date('d M Y', strtotime($header['tgl_jatuhtempo'])) : '-',
            '#no_pengajuan#'        => isset($header['no_pengajuanklaim']) ? $header['no_pengajuanklaim'] : '-',
            '#total_pengajuan#'     => 'Rp.' . (isset($header['total_piutang']) ? DocoHelpers::formatNumber($header['total_piutang'],0) : 0),
            '#tanggal#'             => date('d M Y H:i:s'),
            '#pegawai#'             => $mengetahui,
            '#jabatan#'             => $jabatan,
            '#nip#'                 => $nip,
            '#list_data#'           => $this->renderPartial('_list_pengajuan', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }

    public function actionListPengajuanExcel($id)
    {
        $header = InfoPengajuanKlaim::find()->where(['pengajuanklaim_id' => $id])->asArray()->one();
        $tanggalMulai = isset($header['tgl_pelayanandari']) ? date('d-M-Y',strtotime($header['tgl_pelayanandari'])) . ' s/d ' : '';
        $tanggalSelesai = isset($header['tgl_pelayanansampai']) ? date('d-M-Y',strtotime($header['tgl_pelayanansampai'])) : '';
        $query = InfoPengajuanKlaimDetail::find()->where(['pengajuanklaim_id' => $id]);        
        $title = 'Informasi Detail Pengajuan Klaim';

        $header = [
            'Tanggal Keluar'      => "{$tanggalMulai}{$tanggalSelesai}",
            'Tanggal Pengajuan'   => isset($header['tgl_pengajuanklaim']) ? date('d-M-Y', strtotime($header['tgl_pengajuanklaim'])) : '-',
            'Tanggal jatuh Tempo' => isset($header['tgl_jatuhtempo']) ? date('d-M-Y', strtotime($header['tgl_jatuhtempo'])) : '-',
            'No pengajuan'        => isset($header['no_pengajuanklaim']) ? $header['no_pengajuanklaim'] : '-',
            'Cara Bayar'          => isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '-',
            'Penjamin'            => isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-',
            'Total Pengajuan'     => 'Rp.' . (isset($header['total_piutang']) ? DocoHelpers::formatNumber($header['total_piutang'],0) : 0),
            'Catatan'             => isset($header['catatan']) ? $header['catatan'] : '',
        ];

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
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode'   => 'date'
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
                ]
            ],
        ];

        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $result[$key] = [
                'Data Pasien'              => $value['nama_pasien'] . "\n" . $value['no_rekam_medik'] . "\n" . $value['no_pendaftaran'],
                'No Invoice'               => DocoHelpers::coalesce($value['no_invoice'], '-'),
                'Tanggal Masuk'            => date(' d M Y', strtotime($value['tgl_pendaftaran'])),
                'Tanggal Keluar'           => DocoHelpers::coalesce(date('d M Y', strtotime($value['tglpasienpulang'])), ''),
                'No SEP'                   => DocoHelpers::coalesce($value['nosep'], '-'),
                'Instalasi / Ruangan'      => $value['instalasi_nama'] . "\n" . $value['ruangan_nama'],
                'Tagihan'                  => $value['total_tagihan'],
                'Jumlah Diskon'            => $value['total_discountpembayaran'],
                'Jumlah Dibayarkan Pasien' => $value['jumlah_telahbayar'],
                'Sisa Tagihan'             => $value['jumlah_piutang'],
            ];
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionBatalPengajuanPasien($id)
    {
        $connection  = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $detail = PengajuanKlaimDetail::find()->where([
                'pengajuanklaimdetail_id' => $id
            ])->one();

            $header = PengajuanKlaim::find()->where([
                'pengajuanklaim_id' => $detail->pengajuanklaim_id
            ])->one();

            $piutang = $header->total_piutang - $detail->jumlah_piutang;

            $header->total_piutang = ($piutang < 0) ? 0 : $piutang;
            
            $return = [
                'status' => 422,
                'text'   => 'Pengajuan gagal dibatalakn',
                'title'  => 'Proses Gagal!'
            ];
            
            if ($header->save()) {
                $delete = (new PengajuanKlaimDetail)->delete([
                    'pengajuanklaimdetail_id' => $id
                ]);
                
                if ($delete) {
                    $return = [
                        'text'  => 'Pengajuan berhasil dibatalakn',
                        'title' => 'Proses Berhasil!'
                    ];
                }
            }

            $transaction->commit();
            
            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            
            return [
                'messages' => $e->getMessage(),
                'status'   => 500
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            
            return [
                'messages' => $e->getMessage(),
                'status'   => 500
            ];
        }
    }

    public function actionGetDataListPasien($id)
    {
        $request = Yii::$app->request;
        $header = InfoPengajuanKlaim::find()->where([
            'pengajuanklaim_id' => $id
        ])->asArray()->one();
        $model = new PengajuanKlaimView;
        $query = $model::find()
            ->where([
                'carabayar_id' => $header['carabayar_id'], 
                'penjamin_id' => $header['penjamin_id']
            ]);
        $start = isset($header['tgl_pelayanandari']) 
            ? date('Y-m-d',strtotime($header['tgl_pelayanandari'])) : date('Y-m-d');
        $end = isset($header['tgl_pelayanansampai']) 
            ? date('Y-m-d',strtotime($header['tgl_pelayanansampai'])) 
            : date('Y-m-d');
        $query->andWhere(['not', 
            ['no_pembayaran' => null]
        ]);
        $query->andWhere(['not', 
            ['tglpasienpulang' => null]
        ]);
        $query->andWhere(['pengajuanklaim_id' => null]);
        $tglPasienPulang = ArrayHelper::getValue($request->get(), 'advanced-filter.tglpasienpulang');
        if ($tglPasienPulang) {
            $dates = explode(' - ', $tglPasienPulang);
            $startDate = ArrayHelper::getValue($dates, '0');
            $endDate = ArrayHelper::getValue($dates, '1');
            $startDate = date('Y-m-d', strtotime($startDate));
            $endDate = date('Y-m-d', strtotime($endDate));
            $startDate = "$startDate 00:00:00";
            $endDate = "$endDate 23:59:59";
            $query->andWhere(['between', 'tglpasienpulang', $startDate, $endDate]);
        }
        // $query->andWhere(['between', 'tgl_pendaftaran', $start . ' 00:00:00', $end . ' 23:59:59']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSimpanTambahPasien($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $dataPengajuan = $request->post('data_pengajuan');
            $header = PengajuanKlaim::find()->where([
                'pengajuanklaim_id' => $id
            ])->one();
            if (!empty($dataPengajuan)) {
                $list = json_decode($dataPengajuan,true);
                if (is_array($list)) {
                    $insert = [];
                    $piutang = $header->total_piutang;
                    foreach ($list as $key => $value) {
                        $piutang += $value['piutang'];
                        $insert[] = [
                            'pendaftaran_id' => $value['pendaftaran_id'],
                            'pasien_id' => $value['pasien_id'],
                            'pasienadmisi_id'=>$value['pasienadmisi_id'],
                            'pengajuanklaim_id' => $id,
                            'jumlah_piutang' => $value['piutang'],
                            'jumlah_bayar' => 0,
                            'jumlah_telahbayar' => 0,
                            'jumlah_sisapiutang' => 0,
                            'pembayaranpelayanan_id' => $value['pembayaranpelayanan_id']
                        ];
                    }
                    $header->total_piutang = $piutang;
                    if ($header->save()) {
                        PengajuanKlaimDetail::batchInsert($insert, false);
                        $transaction->commit();
                        return [
                            'text' => 'Penambahan pengajuan berhasil',
                            'title' => 'Proses Berhasil!'
                        ];
                    }
                }
            }
            return [
                'status' => 422,
                'text' => 'Penambahan pengajuan gagal',
                'title' => 'Proses Gagal!'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }

    public function actionUpdatePengajuan($id)
    {
        $request = Yii::$app->request;
        $model = PengajuanKlaim::find()->where([
            'pengajuanklaim_id' => $id
        ])->one();
        if (!empty($model)) {
            $model->total_piutang = $request->post('total_pengajuan');
            $model->catatan = $request->post('catatan');
            $model->tgl_jatuhtempo = $request->post('tgl_jatuhtempo');
            if ($model->save()) {
                return [
                    'text' => 'Update data pengajuan berhasil',
                    'title' => 'Proses Berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'text' => 'Update data pengajuan gagal',
            'title' => 'Proses Gagal!'
        ];

    }

    public function actionGetAttribute()
    {
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find()
            ->where(['is_active' => true])
            ->andWhere(['<>', 'carabayar_id', DocoConstants::PENJAMIN_UMUM]);

        $queryCaraBayar = $queryCaraBayar->asArray()->all();

        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()->where(['is_active' => true])
            ->andWhere(['<>', 'carabayar_id', DocoConstants::PENJAMIN_UMUM]);
        $queryPenjamin = $queryPenjamin->asArray()->all();


        // status klaim
        $modelStatus = new Lookup;
        $queryStatus = $modelStatus::find()
            ->where(['is_active' => true, 'lookup_type' => 'pengajuan_klaim']);
        $queryStatus = $queryStatus->asArray()->all();


        return [
            'cara_bayar' => $queryCaraBayar,
            'penjamin' => $queryPenjamin,
            'status_klaim' => $queryStatus,
        ];
    }

}