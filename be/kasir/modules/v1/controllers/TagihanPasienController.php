<?php

/**
* @author yaya
*/

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoMessages;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\CetakKwitansiBkm;
use app\modules\v1\models\InformasiPasienRincian;
use app\modules\v1\models\InformasiPasienPenunjangRincian;
use app\modules\v1\models\InfoTagihanObatDetailView;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\KomponenTarif;
use app\modules\v1\models\KonfirmasiUnitView;
use app\modules\v1\models\TindakanKomponen;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\InfoPasienBpjsKlaimView;
use app\modules\v1\models\TmpInfoTagihanPasien;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\Kamar;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\GabungPelayananDetail;

use app\modules\v1\payload\JasaDokterPayload;
use app\modules\v1\businessLogic\TagihanPasien;
use app\modules\v1\businessLogic\TagihanHelper;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\ApprovalDiskonT;
use app\modules\v1\models\LogEditTagihan;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use app\modules\v1\models\PemberianPiutang;
use app\modules\v1\models\NotifikasiJobOrderView;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\exceptions\ValidationException;
use Doco\models\Pendaftaran;
use app\modules\v1\models\LogCloseBill;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\PenjaminDiskonView;
use SirsCore\models\PasienMasukPenunjang;

class TagihanPasienController extends DocoActiveController
{
    public $modelClass = InfoDataPendaftaran::class;
    const PAKET = 'PAKET';
    const TINDAKAN = 'TINDAKAN';
    /**
     * Untuk Kebutuhan Integerasi GE
     * @var array
     */
    public $messageBroker = [
        'save' => [
            'services' => [
                'Ris' => [
                    'RisBroker' => [
                        'payload' => ['pendaftaran_id']
                    ]
                ],
                'InaBroker' => [
                    'RisBroker' => [
                        'query_params' => ['pasienmasukpenunjang_id', 'pembayaran_id'],
                        'payload' => ['pendaftaran_id','pasienmasukpenunjang_id','pembayaran_id'],
                        'result' => true,
                        'successProcess' => true
                    ]
                ],
                'Lis' => [
                    'BridgingLis' => [
                        'query_params' => ['pasienmasukpenunjang_id'],
                        'payload' => ['pendaftaran_id','pasienmasukpenunjang_id','pasienkirimkeunitlain_id'],
                        'result' => true,
                        'successProcess' => true
                    ]
                ],
                'Mhg' => [
                    'BslBilling' => [
                        'payload' => ['pendaftaran_id']
                    ],
                    'UpdateAppointment' => [
                        'result' => true,
                        'successProcess'=>true,
                    ],
                ],
                'Roche' => [
                    'Order' => [
                        'payload' => ['pendaftaran_id'],
                        'successProcess'=> false,
                    ]
                ],
                'InaCbgs' => [
                    'Registrasi' =>[
                        'payload' => ['pendaftaran_id'],
                        'query_params' => ['id', 'nama_pegawai']
                    ] 
                ],
                'Fisioterapi' => [
                    'UpdateStatusPembayaranPaket' => [
                        'payload' => ['pendaftaran_id', 'pasienmasukpenunjang_id']
                    ]
                ]
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["save"] = ["POST"];
        $verbs["get-jasa-dokter"] = ["GET"];
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
        return Cache::getKonfigSistem();
    }

    public function actionSave()
    {
        return TagihanPasien::execute();
    }

    public function actionDiscount()
    {
        return true;
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

    public function actionPrintRincian($id, $tipe_pasien = null)
    {
        $cacheItem = Yii::$app->cache;
        $cache = $cacheItem->get('history-trans-'.$id, 3600);
        $filter = false;
        if (!empty($cache)) {
            $filter = $cache;
        }

        $primary_field = 'pendaftaran_id';

        if (!is_null($tipe_pasien)) {
            if ($tipe_pasien == 'pasien_bebas') {
                $primary_field = 'penjualanresep_id';
            }
        }

        $header = Yii::$app->db->createCommand("
            SELECT * FROM rinciantagihansudahbayarheader_v WHERE {$primary_field} = {$id}
            AND pembayaranpelayanan_id IS NOT NULL
            ORDER BY tandabuktibayar_id DESC
        ")->queryAll();

        $detail = Yii::$app->db->createCommand("
            SELECT * FROM rinciantagihanpasiensudahbayar_v WHERE {$primary_field} = {$id}
            AND pembayaranpelayanan_id IS NOT NULL
            ORDER BY tandabuktibayar_id DESC
        ")->queryAll();

        foreach ($detail as $value) {
            $is_obat = isset($value['is_obat']) ? $value['is_obat'] : null;
            $instalasi = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
            $ruangan = isset($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : null;
            $ruangan_id = isset($value['ruangan_id']) ? $value['ruangan_id'] : null;
            if (!isset($listData[$value['pembayaranpelayanan_id']])) {
                $listData[$value['pembayaranpelayanan_id']] = [
                    'obat' => [],
                    'tindakan' => [],
                    'penunjang' => []
                ];
            }

            if ($is_obat) {
                $listData[$value['pembayaranpelayanan_id']]['obat'][] = $value;
            } else {
                // Penunjang
                if (in_array($instalasi, DocoConstants::$exceptPenunjang)) {
                    $listData[$value['pembayaranpelayanan_id']]['tindakan'][$instalasi.'-'.$ruangan_id]['data'][]
                            = $value;
                    $listData[$value['pembayaranpelayanan_id']]['tindakan'][$instalasi.'-'.$ruangan_id]['title']
                            = $ruangan;
                } else {
                    $listData[$value['pembayaranpelayanan_id']]['penunjang'][$instalasi]['data'][] = $value;
                    $listData[$value['pembayaranpelayanan_id']]['penunjang'][$instalasi]['title'] = $ruangan;
                }
            }
        }

        if (!empty($header)) {
            $countData = !empty($filter) ? $filter : count($header);
            $print = new DocoPrint();
            $no = 1;
            foreach ($header as $key => $query) {
                if (!empty($filter) && ($no > $filter)) {
                    break;
                }

                $idParent = isset($query['pembayaranpelayanan_id']) ? $query['pembayaranpelayanan_id'] : null;
                $dokter = isset($query['dok_pendaftaran'])
                                                    ? $query['dok_pendaftaran'] : null;
                if (!empty($query['dok_ranap'])) {
                    $dokter = $query['dok_ranap'];
                }

                $ruangan = isset($query['r_pendaftaran']) ? $query['r_pendaftaran'] : null;
                if (!empty($query['r_ranap'])) {
                    $ruangan = $query['r_ranap'];
                }
                $sisa_tagihan = isset($query['total_sisatagihan']) ? $query['total_sisatagihan'] : 0;
                if (empty($listData[$idParent])) {
                    $no++;
                    continue;
                }

                $print->attributes = [
                    '#tanggal#' => isset($query['tgl_pendaftaran']) ? $query['tgl_pendaftaran'] : "-",
                    '#no_rm#' => isset($query['no_rekam_medik']) ? $query['no_rekam_medik'] : "-",
                    '#no_pendaftaran#' => isset($query['no_pendaftaran']) ? $query['no_pendaftaran'] : "-",
                    '#nama#' => isset($query['nama_pasien']) ? $query['nama_pasien'] : "-",
                    '#jenis_kasus_penyakit#' => isset($query['jeniskasuspenyakit_nama'])
                                                    ? $query['jeniskasuspenyakit_nama'] : "-",
                    '#dokter#' => $dokter,
                    '#ruangan#' => $ruangan,
                    '#kelas_pelayanan#' => isset($query['kelaspelayanan_nama']) ? $query['kelaspelayanan_nama'] : "-",
                    '#penjamin#' => isset($query['penjamin_nama']) ? $query['penjamin_nama'] : "-",
                    '#cara_bayar#' => isset($query['carabayar_nama']) ? $query['carabayar_nama'] : "-",
                    // '#status_bayar#' => isset($query['status_bayar']) ? $query['status_bayar'] : null,
                    '#status_bayar#' => $sisa_tagihan > 0 ? 'Belum Lunas' : 'Lunas',
                    '#total_tagihan#' => isset($query['total_tagihan']) ? DocoHelpers::rupiahDisplay($query['total_tagihan']) : "-",
                    '#total_uang_muka#' => isset($query['total_uang_muka'])
                                                ? DocoHelpers::rupiahDisplay($query['total_uang_muka']) : "-",
                    '#total_dibayar#' => isset($query['total_sudah_dibayarkan'])
                                                ? DocoHelpers::rupiahDisplay(round($query['total_sudah_dibayarkan'])) : "-",
                    '#sisa_tagihan#' => DocoHelpers::rupiahDisplay($sisa_tagihan),
                    '#subsidi_asuransi#' => isset($query['total_subsidiasuransi'])
                                                ? DocoHelpers::rupiahDisplay($query['total_subsidiasuransi']) : "-",
                    '#biaya_admin#' => isset($query['biaya_administrasi'])
                                                ? DocoHelpers::rupiahDisplay($query['biaya_administrasi']) : "-",
                    '#pembulatan#' => isset($query['pembulatan'])
                                                ? DocoHelpers::rupiahDisplay($query['pembulatan']) : "-",
                    '#detail_tindakan#' => $this->renderPartial('riwayat',[
                        'detail' => isset($listData[$idParent]) ? $listData[$idParent] : []
                    ]),
                ];

                $break = (($key + 1) == $countData) ? false : true;
                $print->generateHtml($break);
                $no++;
            }
            $print->Output(true);
        }
    }

    /**
    * @controller actionPrintKwitansi
    * @attribute #nama_pasien# => nama pasien
    * @attribute #total_terbayar# => total
    * @attribute #keterangan# => keterangan pembayaran
    * @attribute #terbilang# => terbilang
    * @attribute #no_kwitansi# => no kwitansi
    * @attribute #kasir# => nama kasir
    * @attribute #tanggal# => tanggal sekarang
    **/

    public function actionPrintKwitansi($id, $tipe_pasien = null)
    {
        $cacheItem = Yii::$app->cache;
        $cache = $cacheItem->get('history-trans-'.$id, 3600);
        $filter = false;
        if (!empty($cache)) {
            $filter = $cache;
        }

        $primary_field = 'pendaftaran_id';

        if (!is_null($tipe_pasien)) {
            if ($tipe_pasien == 'pasien_bebas') {
                $primary_field = 'penjualanresep_id';
            }
        }

        $model = new CetakKwitansiBkm;
        $result = $model::find()
                    ->where([$primary_field => $id])
                    ->andWhere(['NOT', ['pembayaranpelayanan_id' => null]])
                    ->orderBy([
                        'pembayaranpelayanan_id' => SORT_DESC
                    ]);
        if (!empty($filter)) {
            $result->limit($filter);
        }
        $result = $result->all();
        $tanggal = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $tanggal_sekarang = date('d').' '.$tanggal['bulan'].' '.$tanggal['tahun'];

        if (!empty($result)) {
            $countData = count($result);
            $print = new DocoPrint();
            foreach ($result as $key => $query) {
                $tgl_pulang = is_null($query->tglpulang_pendaftaran) ? '' : ' s/d '. date('d M Y',strtotime($query->tglpulang_pendaftaran));
                $keterangan_tanggal = date('d M Y',strtotime($query->tgl_pendaftaran)).$tgl_pulang;
                $terbilang = DocoHelpers::Terbilang((int)$query->total_terbayar). 'Rupiah';
                $print->attributes = [
                    '#nama_pasien#' => $query->nama_pasien,
                    '#total_terbayar#' => DocoHelpers::formatNumber($query->total_terbayar),
                    '#terbilang#' => $terbilang,
                    '#no_kwitansi#' => $query->no_kwitansi,
                    '#tanggal#' => $tanggal_sekarang,
                    '#instalasi_nama#' => $query->instalasi_nama,
                    '#keterangan_tanggal#' => $keterangan_tanggal,
                    '#no_pendaftaran#' => $query->no_pendaftaran,
                    '#kasir#' => $query->kasir,
                ];
                $break = (($key + 1) == $countData) ? false : true;
                $print->generateHtml($break);
            }
            $print->Output(true);
        }
    }

    /**
    * @controller actionPrintBkm
    * @attribute #nama_pasien# => nama pasien
    * @attribute #total_terbayar# => total
    * @attribute #keterangan# => keterangan pembayaran
    * @attribute #terbilang# => terbilang
    * @attribute #no_bkm# => no bkm
    * @attribute #kasir# => nama kasir
    * @attribute #instalasi_nama# => nama instalasi
    * @attribute #keterangan_tanggal# => keterangan tanggal
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #tanggal# => tanggal sekarang
    **/
    public function actionPrintBkm($id, $tipe_pasien = null)
    {
        $cacheItem = Yii::$app->cache;
        $cache = $cacheItem->get('history-trans-'.$id, 3600);
        $filter = false;
        if (!empty($cache)) {
            $filter = $cache;
        }

        $primary_field = 'pendaftaran_id';

        if (!is_null($tipe_pasien)) {
            if ($tipe_pasien == 'pasien_bebas') {
                $primary_field = 'penjualanresep_id';
            }
        }

        $model = new CetakKwitansiBkm;
        $result = $model::find()
                    ->where([$primary_field => $id])
                    ->andWhere(['NOT', ['pembayaranpelayanan_id' => null]])
                    ->orderBy([
                        'pembayaranpelayanan_id' => SORT_DESC
                    ]);
        if (!empty($filter)) {
            $result->limit($filter);
        }

        $result = $result->all();
        $tanggal = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
        $tanggal_sekarang = date('d').' '.$tanggal['bulan'].' '.$tanggal['tahun'];
        if (!empty($result)) {
            $countData = count($result);
            $print = new DocoPrint();
            foreach ($result as $key => $query) {
                $tgl_pulang = is_null($query->tglpulang_pendaftaran) ? '' : ' s/d '. date('d M Y',strtotime($query->tglpulang_pendaftaran));
                $keterangan_tanggal = date('d M Y',strtotime($query->tgl_pendaftaran)).$tgl_pulang;
                $terbilang = DocoHelpers::Terbilang((int)$query->total_tagihan). 'Rupiah';
                $print->attributes = [
                    '#nama_pasien#' => $query->nama_pasien,
                    '#total_terbayar#' => DocoHelpers::formatNumber($query->total_tagihan),
                    '#terbilang#' => $terbilang,
                    '#no_bkm#' => $query->no_bkm,
                    '#tanggal#' => $tanggal_sekarang,
                    '#instalasi_nama#' => $query->instalasi_nama,
                    '#keterangan_tanggal#' => $keterangan_tanggal,
                    '#no_pendaftaran#' => $query->no_pendaftaran,
                    '#kasir#' => $query->kasir,
                ];
                $break = (($key + 1) == $countData) ? false : true;
                $print->generateHtml($break);
            }
            $print->Output(true);
        }
    }

    /**
    * @controller actionPrintKarcis
    * @attribute #no_antrian# => data no_antrian
    * @attribute #groupcarabayar# => data group cara bayar
    * @attribute #namadokter# => data nama dokter
    * @attribute #status_pasien# => data status pasien
    * @attribute #namaruangan# => data nama ruangan
    * @attribute #norekammedik# => data nomor rekam medik
    * @attribute #namapasien# => data nama pasien
    **/

    public function actionPrintKarcis($id, $type = null)
    {
        $result = Yii::$app->db->createCommand("
            SELECT * FROM antrian_v WHERE pendaftaran_id = {$id} AND jenisantrian_id = {$type}
        ")->queryAll();
        if (!empty($result)) {
            $countData = count($result);
            $print = new DocoPrint();
            foreach ($result as $key => $query) {
                $print->attributes = [
                    '#no_antrian#' => @$query['no_antrian'],
                    '#groupcarabayar#' => @$query['namagroupcarabayar'],
                    '#namadokter#' => @$query['nama_pegawai_lengkap'],
                    '#status_pasien#' => @$query['stat_pasien'],
                    '#namaruangan#' => @$query['ruangan_nama'],
                    '#norekammedik#' => @$query['no_rekam_medik'],
                    '#namapasien#' => @$query['nama_pasien'],
                ];
                $break = (($key + 1) == $countData) ? false : true;
                $print->generateHtml($break);
            }
            $print->Output(true);
        }
        throw new \yii\web\HttpException(500,"Tidak Di temukan");

    }

    /**
     * @var $id integer [pendaftaran, resptur atau penunjang]
     * @var $kelompok string [DocoConstants::PASIEN_PENUNJANG, DocoConstants::PASIEN_KARCIS, DocoConstants::PASIEN_PULANG]
     * @return  array
     */
    public function actionGetJasaDokter($id, $kelompok)
    {
        $payload = new JasaDokterPayload;
        $payload->id = $id;
        $payload->kelompok = $kelompok;
        if (!$payload->validate()) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }

        $result = [
            'list_dokter' => [],
            'list_jasa_dokter' => [],
        ];
        $cond = [
            'is_obat' => false
        ];
        switch ($kelompok) {
            case DocoConstants::PASIEN_PENUNJANG:
                $cond['pasienmasukpenunjang_id'] = $id;
                break;
            case DocoConstants::PASIEN_PULANG:
                $cond['pendaftaran_id'] = $id;
                break;
            default:
                return $result;
                break;
        }

        $detailTagihan = InfoTagihanPasien::find()->where($cond)->asArray()->all();
        if (!empty($detailTagihan)) {
            /**  mendapatkan komponen jasa dokter */
            $listKomponen = KomponenTarif::find()->select([
                'komponentarif_id'
            ])->where([
                'is_dokter' => true
            ])->all();
            $mappKomponen = ArrayHelper::getColumn($listKomponen, 'komponentarif_id');
            $listKelas = $listPenjamin = $listTindakan = [];
            $listPaket = $listRuangan = $listDokter = [];
            $listJasaDokter = $tindakanToDokter = [];
            $listPelayanan = $detail = [];
            $history = [
                self::PAKET => [],
                self::TINDAKAN => []
            ];

            foreach ($detailTagihan as $key => $value) {
                $dokterId = !empty($value['dokterpenanggungjawab_id']) ? $value['dokterpenanggungjawab_id'] : null;
                $tindakan_paket = isset($value['tindakan_obat_id']) ? $value['tindakan_obat_id'] : null;
                $kelasId = isset($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id'] : null;
                $penjaminPelayananId = isset($value['penjamin_pelayanan_id']) ? $value['penjamin_pelayanan_id'] : null;
                $ruanganId = isset($value['ruangan_id']) ? $value['ruangan_id'] : null;
                $pelayananId = isset($value['pelayanan_id']) ? $value['pelayanan_id'] : null;
                /** get Dokter penanggung jawab */
                if (!empty($dokterId) && !isset($listDokter[$dokterId])) {
                    $listDokter[$dokterId] = !empty($value['dokterpenanggungjawab_nama']) ? $value['dokterpenanggungjawab_nama'] : null;
                }

                if (isset($value['is_valid']) && $value['is_valid'] === false && $value['is_valid'] !== null) {
                    $newValue = $value;
                    $newValue['key'] = $key;
                    /** List Seluruh Kelas per tindakan */
                    if (!empty($kelasId) && !in_array($kelasId, $listKelas)) {
                        $listKelas[] = $kelasId;
                    }
                    /** List Seluruh Kelas per penjamin */
                    if (!empty($penjaminPelayananId) && !in_array($penjaminPelayananId, $listPenjamin)) {
                        $listPenjamin[] = $penjaminPelayananId;
                    }
                    /** List Ruangan */
                    if (!empty($ruanganId) && !in_array($ruanganId, $listRuangan)) {
                        $listRuangan[] = $ruanganId;
                    }

                    /** Mapp tindakan yang false untuk di cari ualang */
                    if (!empty($value['kelompoktindakan_id'])) {
                        $history[self::TINDAKAN][$tindakan_paket][] = $newValue;
                        if (!in_array($tindakan_paket, $listTindakan)) {
                            $listTindakan[] = $tindakan_paket;
                        }
                    } else {
                        /** Paket */
                        $history[self::PAKET][$tindakan_paket][] = $newValue;
                        if (!in_array($tindakan_paket, $listPaket)) {
                            $listPaket[] = $tindakan_paket;
                        }
                    }
                } else {
                    $listPelayanan[] = $pelayananId;
                    $tindakanToDokter[$pelayananId] = $dokterId;
                }
                $detail[] = $value;
            }

            if (!empty($listPenjamin) && !empty($listKelas)) {
                $tarifRs = InfoTarifRsView::find()->andWhere([
                    'penjamin_id' => $listPenjamin,
                    'kelaspelayanan_id' => $listKelas
                ]);
                /** Filter berdasarkan ruangan */
                if (!empty($listRuangan)) {
                    $tarifRs->andWhere(['ruangan_id' => $listRuangan]);
                }

                if (!empty($listPaket) && !empty($listTindakan)) {
                    $tarifRs->andWhere(['or',
                        ['tipepaket_id' => $listPaket],
                        ['daftartindakan_id' => $listTindakan],
                    ]);
                } else {
                    /** Filter berdasarkan paket */
                    if (!empty($listPaket)) {
                        $tarifRs->andWhere(['tipepaket_id' => $listPaket]);
                    }

                    /** Filter berdasarkan daftar tindakan */
                    if (!empty($listTindakan)) {
                        $tarifRs->andWhere(['daftartindakan_id' => $listTindakan]);
                    }
                }
                $resulttarifRs = $tarifRs->asArray()->all();
                if (!empty($resulttarifRs)) {
                    foreach ($resulttarifRs as $key => $value) {
                        $kelasTarif = isset($value['kelaspelayanan_id']) 
                                                ? $value['kelaspelayanan_id'] : null;
                        $penjaminTarif = isset($value['penjamin_id']) 
                                                ? $value['penjamin_id'] : null;
                        $tindakanPaket = isset($value['daftartindakan_id']) 
                                                ? $value['daftartindakan_id'] : null;
                        $komponenTarif = isset($value['komponentarif_id']) 
                                                ? $value['komponentarif_id'] : null;
                        $harga = isset($value['harga_tariftindakan']) 
                                                ? $value['harga_tariftindakan'] : 0;
                        $persenCyto = isset($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : 0;
                        $tipePaket = isset($value['tipepaket_id']) 
                                                ? $value['tipepaket_id'] : null;
                        $keyHist = self::TINDAKAN;

                        if (!empty($tipePaket)) {
                            $tindakanPaket = isset($value['tipepaket_id']) 
                                                ? $value['tipepaket_id'] : null;
                            $keyHist = self::PAKET;
                        }

                        if (in_array($komponenTarif, $mappKomponen)) {
                            if (isset($history[$keyHist][$tindakanPaket])) {
                                foreach ($history[$keyHist][$tindakanPaket] as $key => $valHist) {
                                    $kelasId = $valHist['kelaspelayanan_id'];
                                    $penjaminPelayananId = isset($valHist['penjamin_pelayanan_id']) 
                                                                ? $valHist['penjamin_pelayanan_id'] : null;
                                    if ($kelasId !== $kelasTarif && $penjaminPelayananId !== $penjaminTarif) continue;
                                    $tindPelayanan = isset($valHist['tindakanpelayanan_id']) 
                                                            ? $valHist['tindakanpelayanan_id'] : null;
                                    $isCyto = isset($valHist['is_cyto']) 
                                                            ? $valHist['is_cyto'] : null;
                                    $qtyTindakan = isset($valHist['qty']) ? $valHist['qty'] : 0;
                                    $keyDetail = isset($valHist['key']) ? $valHist['key'] : null;
                                    $totalHarga = $harga * $qtyTindakan;
                                    $hargaCyto = 0;
                                    if ($isCyto) {
                                        $hargaCyto = ($persenCyto / 100) * $harga;
                                        $totalHarga = ($harga + $hargaCyto)  * $qtyTindakan;
                                    }

                                    if (isset($detail[$keyDetail])) {
                                        $dokterDpjp = isset($detail[$keyDetail]['dokterpenanggungjawab_id']) 
                                                ? $detail[$keyDetail]['dokterpenanggungjawab_id'] : null;
                                        if (empty($dokterDpjp)) continue;
                                        if (!isset($listJasaDokter[$dokterDpjp])) {
                                            $listJasaDokter[$dokterDpjp] = 0;
                                        }
                                        $listJasaDokter[$dokterDpjp] += $totalHarga;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($listPelayanan)) {
                $getKomponen = TindakanKomponen::find()->select([
                    'tindakanpelayanan_id',
                    'komponentarif_id',
                    'tarif_tindakankomp',
                ])->where([
                    'tindakanpelayanan_id' => $listPelayanan,
                    'komponentarif_id' => $mappKomponen,
                ])->asArray()->all();
                foreach ($getKomponen as $value) {
                    $hargaJasa = isset($value['tarif_tindakankomp']) ? $value['tarif_tindakankomp'] : 0;
                    $dokterDpjp = isset($tindakanToDokter[$value['tindakanpelayanan_id']]) 
                            ? $tindakanToDokter[$value['tindakanpelayanan_id']] : null;
                    if (empty($dokterDpjp)) continue;
                    if (!isset($listJasaDokter[$dokterDpjp])) {
                        $listJasaDokter[$dokterDpjp] = 0;
                    }
                    $listJasaDokter[$dokterDpjp] += $hargaJasa;
                }
            }

            $result['list_dokter'] = $listDokter;
            $result['list_jasa_dokter'] = $listJasaDokter;
        }

        return $result;
    }

    /**
     * @var $id integer [pendaftaran, resptur atau penunjang]
     * @var $kelompok string [DocoConstants::PASIEN_PENUNJANG, DocoConstants::PASIEN_KARCIS, DocoConstants::PASIEN_PULANG]
     * @var $status boolean [Status bayar]
     * @var $tipe_pasien string [tipe pasien dari resptur]
     * @return  array
     */

    public function actionView($id, $kelompok, $status = null, $tipe_pasien = null)
    {
        $opt_cara_bayar = Cache::getCaraBayar();
        $config_sistem = Cache::getKonfigSistem();
        // $config_sistem['edit_billing'] = true;
        $biayaAdmResep = 0; 
        $getCondition = $this->getCondition($id, $kelompok, $status, $tipe_pasien);
        $cond = isset($getCondition['cond']) ? $getCondition['cond'] : [];
        $isPenunjang = isset($getCondition['isPenunjang']) ? $getCondition['isPenunjang'] : false;
        $params = isset($getCondition['params']) ? $getCondition['params'] : [];
        $condBill = isset($getCondition['condBill']) ? $getCondition['condBill'] : [];

        $infoPasien = InfoDataPendaftaran::find()->where($cond)->asArray()->one();
        $kelasPelayanan = isset($infoPasien['kelaspelayanan_id']) ? $infoPasien['kelaspelayanan_id'] : null;
        if(isset($infoPasien['kelas_ditagihkan_id']) && $infoPasien['kelas_ditagihkan_id'] != null){
            $kelasPelayanan = $infoPasien['kelas_ditagihkan_id'];
        }
        $penjamin = isset($infoPasien['penjamin_id']) ? $infoPasien['penjamin_id'] : null;
        $regisId = !empty($infoPasien['pendaftaran_id']) ? $infoPasien['pendaftaran_id'] : null;
        $admisiId = !empty($infoPasien['pasienadmisi_id']) ? $infoPasien['pasienadmisi_id'] : null;
        $admTindakanId = !empty($config_sistem['adm_tindakan_id']) ? $config_sistem['adm_tindakan_id'] : null;
        $groupcarabayarpasien = !empty($infoPasien['group_carabayar']) ? $infoPasien['group_carabayar'] : null;

        /** Kode untuk dapat tarif maksimal sudah tidak dipakai*/ 
        /** Tarif maksimal sudah diakomodir di helper beserta biaya adm nya, jadi kodenya diset empty mencegah error */
        $administrasi_ri = [];

        $groupcarabayarumum_id =  DocoConstants::GROUP_UMUM;
        $queryPenjamin = Yii::$app->db->createCommand("
                    SELECT
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        fgetnamalookup(carabayar_m.groupcarabayar_id) as group_carabayar,
                        pendaftaran_multipayer_t.carabayar_id,
                        carabayar_m.carabayar_nama,
                        pendaftaran_multipayer_t.penjamin_id,
                        penjamin_m.penjamin_nama,
                        pendaftaranpenjamin_t.nokartuasuransi,
                        pendaftaranpenjamin_t.nominal_dijamin
                    FROM pendaftaran_t
                        LEFT JOIN pendaftaran_multipayer_t ON pendaftaran_t.pendaftaran_id = pendaftaran_multipayer_t.pendaftaran_id
                        LEFT JOIN penjamin_m ON pendaftaran_multipayer_t.penjamin_id = penjamin_m.penjamin_id
                        LEFT JOIN carabayar_m ON pendaftaran_multipayer_t.carabayar_id = carabayar_m.carabayar_id
                        LEFT JOIN pendaftaranpenjamin_t ON pendaftaran_t.pendaftaran_id = pendaftaranpenjamin_t.pendaftaran_id
                    WHERE pendaftaran_t.pendaftaran_id = :pendaftaran_id AND groupcarabayar_id is not NULL AND groupcarabayar_id != :groupcarabayarumum ")
            ->bindParam(':pendaftaran_id', $regisId)
            ->bindParam(':groupcarabayarumum',$groupcarabayarumum_id)
            ->queryAll();

        if ($isPenunjang) {
            $params['pendaftaran_id'] = $regisId;
        }

        $header = [];
        $resultTarifRs = [];

        $detailTagihan = $this->getDetailTagihan($condBill, $params, $isPenunjang);
        $tindakan = $paket = $detail = [];

        $history = [
            self::PAKET => [],
            self::TINDAKAN => []
        ];
        $listRuangan = [];
        $listTindakan = [];
        $listPaket = [];
        $listPenjamin = [];
        $listKelas = [];
        $total_tagihan = 0;
        $total_jpk = 0; // Total jasa pelayanan keperawatan
        $jpk_id = $this->constans->actionGetAdditional("JPK", true);
        $tindakan_visitdokter = $this->constans->actionGetAdditional('tindakan_keperawatan', true);

        if (!empty($detailTagihan)) {
            foreach ($detailTagihan as $key => $value) {
                if(!empty($value['tindakan_obat_id']) && in_array($value['tindakan_obat_id'], $jpk_id)){
                    $total_jpk += round($value['sub_total'],2);
                }
                $total_tagihan += round($value['sub_total'],2);
                $detail[] = $value;
            }
        }

        $cond_administrasi = $id;
        if (!is_null($tipe_pasien)) {
            if ($tipe_pasien == 'pasien_rs' || $tipe_pasien == 'pasien_bebas') {
                $cond_administrasi =  [
                    'penjualanresep_id' => $id
                ];
               
            } 
        }

        $total_tagihan_admin = $total_tagihan - $total_jpk; 
         // update rumus, untuk hitung biaya admin, maka total tagihan akan dikurangi jasa pelayanan keperawatan
        $total_admin =  TagihanHelper::getInstance($cond_administrasi, $penjamin, $kelasPelayanan,$total_tagihan_admin, $admisiId);
        $tmpTagihan = [];
        if (!empty($params)) {
            $id = isset($cond['pasienmasukpenunjang_id']) ? $cond['pasienmasukpenunjang_id'] : $regisId;
            $tmpTagihan = $this->actionGetTmpTagihan($params);
        }
        $idTmpPenjamin = null;
        $x = 0;

        foreach ($tmpTagihan as $val){
            if(!empty($val)){
                foreach ($val as $valTmp){
                    $defPenjamin = (!empty($valTmp->defaultPenjamin) ) ? $valTmp['defaultPenjamin'] : null;
                    if(!empty($defPenjamin)){
                        foreach($defPenjamin as $key=>$valTmp2){
                            if($key == 'id'){
                                $idTmpPenjamin = $valTmp2;
                                break;
                            }
                        }
                    }
                }
                if($idTmpPenjamin != null){
                    break;
                }
            }
            if($idTmpPenjamin != null){
                break;
            }
        }
        if($infoPasien ["penjamin_id"] != $idTmpPenjamin){
            TmpInfoTagihanPasien::deleteAll(['id'=>$regisId]);
        }

        // Informasi Konfirmasi
        $instalasi_map =[];
        $konfirmasi_view = KonfirmasiUnitView::find()
        ->where(['pendaftaran_id'=> $id])->asArray()->all();
        $confirm_inst = ( new DocoConstansId )->actionGetAdditional('konfirm_instalasi', TRUE);
        if(!empty($confirm_inst)){
            $instalasi_map = Instalasi::find()->where(['instalasi_id' => $confirm_inst])->asArray()->all();
        }
        
        $gabungBilling = $this->getGabungTagihan($id);
        if(!empty($gabungBilling)) {
            $pendaftaranIdGabung = ArrayHelper::getValue($gabungBilling, 'pendaftaran_id');
            $piutangGabung = PemberianPiutang::find()->select(['SUM(total_sisapiutang) AS total_sisapiutang'])->where(['pendaftaran_id' => $pendaftaranIdGabung])->groupBy(['pendaftaran_id'])->one();
            $sisaPiutangGabung = ArrayHelper::getValue($piutangGabung, 'total_sisapiutang', 0);
            if(!empty($sisaPiutangGabung)) {
                $infoPasien['total_piutang'] += $sisaPiutangGabung;
            }
        }
        $disableSip = 0;

        $discountInsurance = $this->findDiscountInsurance($penjamin);
        $configOtoritasPenjamin = (new DocoConstansId)->actionGetAdditional('otoritas_penjamin_kasir');
        return [
            'info' => $infoPasien,
            'detail_tagihan' => $detail,
            'konfirmasi_view' => $konfirmasi_view,
            'instalasi_map' => $instalasi_map,
            'konfirm_instalasi' => $confirm_inst,
            'cara_bayar' => $opt_cara_bayar,
            'instance_adm' => (array) $total_admin->biayaAdm,
            'konfig_sistem' => $config_sistem,
            'header' => $header,
            'administrasi_ri' => $administrasi_ri,
            'total_admin' => $total_admin->biayaAdm->totalBiayaAdm,
            'total_tagihan' => $total_tagihan,
            'listPenjamin' => $queryPenjamin,
            'tindakan_visitdokter' =>$tindakan_visitdokter,
            'tmpTagihan' => $tmpTagihan,
            'konfig_sip' => $disableSip,
            'discountInsurance' => $discountInsurance,
            'configOtoritasPenjamin' => $configOtoritasPenjamin
        ];
    }

    public function actionInvoice()
    {
        return Yii::$app->docoPlugin->execute('cetak_invoice');
    }

    public function actionDetailInvoice()
    {
        return Yii::$app->docoPlugin->execute('cetak_detail_invoice');
    }

    private function getProfileRs()
    {
        $profilRs = Cache::getProfileRs();
        $kota = '-';
        $namaRs = '-';
        if (!empty($profilRs['nama_rumahsakit'])) {
            $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
            if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
                $pattern = "KOTA ADM. ";
            }
            elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
                $pattern = "KAB. ADM. ";
            }
            elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
                $pattern = "KAB. ";
            }
            elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
                $pattern = "KOTA ";
            }

            $kota = str_replace($pattern,"", $profilRs['kota']);
        }
        
        return [
            'namaRs' => $namaRs,
            'kota' => $kota,
            'alamat' => $profilRs['alamatlokasi_rumahsakit'],
            'no_telp' => $profilRs['no_telp_profilrs']
        ];
    }

    private function getUmur($tgl_lahir, $yearOnly = false)
    {
        $umur = '-';
        if (!empty($tgl_lahir)) {
            if($yearOnly) {
                $umur = DocoHelpers::getUmur($tgl_lahir, true, false).' Years';
            }
            else {
                $umur = DocoHelpers::getUmur($tgl_lahir);
                $umur = str_replace("tahun", "Year(s)", $umur);
                $umur = str_replace("bulan", "Month(s)", $umur);
                $umur = str_replace("hari", "Day(s)", $umur);
            }
        }

        return $umur;
    }

    /**
    * @controller actionDetailInvoiceInacbg
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #printed_by# => user login
    * @attribute #printed_date# => tanggal cetak
    */
    public function actionDetailInvoiceInacbg($id)
    {
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $infoPasien = $db->createCommand("
            SELECT 
                carabayar_nama,
                no_rekam_medik, 
                no_pendaftaran, 
                nama_pasien,
                alamat_pasien, 
                umur,
                jeniskelamin as jenis_kelamin,
                ruangan_nama,
                kelaspelayanan_nama,
                kamarruangan_nokamar,
                no_tempattidur,
                instalasi_id,
                dokter_dpjp as dokter_admisi,
                tgl_pendaftaran as tgl_admisi,
                tgl_stopakomodasi,
                nosep,
                tanggal_lahir
            FROM infopasienbpjs_v 
            WHERE pendaftaran_id = {$id}
        ")->queryOne();
        
        if (empty($infoPasien)) {
            throw new \yii\web\HttpException(500,"Pendaftaran tidak ditemukan.");
        }

        $nama_pegawai = $request->get('nama_pegawai', null);

        $claimBpjs = InfoPasienBpjsKlaimView::find()
                        ->asArray()
                        ->where([
                            'pendaftaran_id' => $id
                        ])->asArray()->all();
        $dataProcedures = [];
        $dataDrugs = $dataConsultation = [
            'label' => null,
            'data' => []
        ];

        foreach ($claimBpjs as $value) {
            $groupinacbg_nama = $value['groupinacbg_nama'];
            $layanan_jenis = $value['jenis'];
            $is_konsultasi = $value['is_konsultasi'];
            if ($layanan_jenis == 'OBAT') {
                if (empty($dataDrugs['label'])) {
                    $dataDrugs['label'] = $groupinacbg_nama;
                }
                $dataDrugs['data'][] = $value;
            } else {
                if ($is_konsultasi) {
                    if (empty($dataConsultation['label'])) {
                        $dataConsultation['label'] = $groupinacbg_nama;
                    }
                    $dataConsultation['data'][] = $value;
                } else {
                    if(!empty($groupinacbg_nama)) {
                        $dataProcedures[$groupinacbg_nama][] = $value;
                    }
                }
            }
        }

        // return compact('dataProcedures', 'dataDrugs', 'dataConsultation', 'id');
        $profilRs = $this->getProfileRs();
        $kota = $profilRs['kota'];
        $tanggal_lahir = !empty($infoPasien['tanggal_lahir']) ? $infoPasien['tanggal_lahir'] : null;
        $umur = $this->getUmur($tanggal_lahir);
        $konfigSystem = Cache::getKonfigSistem();
        $no_pendaftaran = !empty($infoPasien['no_pendaftaran']) ? $infoPasien['no_pendaftaran'] : null;
        $no_rekam_medik = !empty($infoPasien['no_rekam_medik']) ? $infoPasien['no_rekam_medik'] : null;
        $nama_pasien = !empty($infoPasien['nama_pasien']) ? $infoPasien['nama_pasien'] : null;
        $gender = !empty($infoPasien['jenis_kelamin']) ? $infoPasien['jenis_kelamin'] : null;
        $sep_no = !empty($infoPasien['nosep']) ? $infoPasien['nosep'] : null;
        $alamat_pasien = !empty($infoPasien['alamat_pasien']) ? $infoPasien['alamat_pasien'] : null;

        $kelurahan_nama = !empty($infoPasien['kelurahan_nama']) ? $infoPasien['kelurahan_nama'] : '';
        $kecamatan_nama = !empty($infoPasien['kecamatan_nama']) ? $infoPasien['kecamatan_nama'] : '';
        $kabupaten_nama = !empty($infoPasien['kabupaten_nama']) ? $infoPasien['kabupaten_nama'].' '.$infoPasien['propinsi_nama'] : '';

        $ward = !empty($infoPasien['ruangan_nama']) ? $infoPasien['ruangan_nama'] : '-';
        $bed_type = !empty($infoPasien['kelas_pelayanan']) ? $infoPasien['kelas_pelayanan'] : '-';
        $bed_no = !empty($infoPasien['kamarruangan_nokamar']) ? $infoPasien['kamarruangan_nokamar'] . '-' . $infoPasien['no_tempattidur'] : '-';

        $primary_doctor = !empty($infoPasien['dokter_admisi']) ? $infoPasien['dokter_admisi'] : '-';
       
        if($infoPasien['instalasi_id'] == 1){
            $admission = "-";
            $discharge_date = "-";
        }else if($infoPasien['instalasi_id'] == 2){
            $admission = "-";
            $discharge_date = "-";
        }else if ($infoPasien['instalasi_id'] == 3){
            $admission = !empty($infoPasien['tgl_admisi']) ? date('d/m/Y H:i', strtotime($infoPasien['tgl_admisi'])) : '-';
            $discharge_date = !empty($infoPasien['tgl_stopakomodasi']) ? date('d/m/Y H:i', strtotime($infoPasien['tgl_stopakomodasi'])) : '-';
        }
        

        $print = new DocoPrint('detail-invoice-inacbg');
        $print->attributes = [
            '#printed_by#' => $nama_pegawai,
            '#printed_date#' => date('d/M/Y H:i'),
            '#datatable#' => $this->renderPartial('invoice-detail-inacbg', [
                'umur' => $umur,
                'sep_no' => $sep_no,
                'no_pendaftaran' => $no_pendaftaran,
                'gender' => $gender,
                'no_rekam_medik' => $no_rekam_medik,
                'nama_pasien' => $nama_pasien,
                'address' => $alamat_pasien,
                'address2' => $kelurahan_nama,
                'address3' => $kecamatan_nama,
                'address4' => $kabupaten_nama,
                'ward' => $ward,
                'bed_type' => $bed_type,
                'bed_no' => $bed_no,
                'primary_doctor' => $primary_doctor,
                'admission' => $admission,
                'discharge_date' => $discharge_date,
                'namaPasien' => $nama_pasien,
                'dataConsultation' => $dataConsultation,
                'dataProcedures' => $dataProcedures,
                'dataDrugs' => $dataDrugs,
                'tgl_admisi' => !empty($infoPasien['tgl_admisi']) ? $infoPasien['tgl_admisi'] : null,
                'tgl_stopakomodasi' => !empty($infoPasien['tgl_stopakomodasi']) ? $infoPasien['tgl_stopakomodasi'] : null,
                'bed_type' => $bed_type,
                'bed_no' => $bed_no,
            ]),
        ];
        ini_set('memory_limit', '-1');
        $print->Output();
    }
    
    /**
    * @controller actionCetakRincian
    * @attribute #tgl_pendaftaran# => tanggal daftar
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #nama_pasien# => nama pasien
    * @attribute #nama_dok_rj_rd# => dokter
    * @attribute #rua_nama# => ruangan
    * @attribute #kelaspelayanan_nama# => kelas pelayanan
    * @attribute #penjamin_nama# => penjamin
    * @attribute #carabayar_nama# => cara bayar
    * @attribute #status_bayar# => status bayar
    * @attribute #table# => table detail
    **/
    public function actionCetakRincian(){
        return Yii::$app->docoPlugin->execute('cetak_rincian');
    }

    /**
    * @controller actionCetakDetailRincian
    * @attribute #tgl_pendaftaran# => tanggal daftar
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #no_pendaftaran# => no pendaftaran
    * @attribute #nama_pasien# => nama pasien
    * @attribute #nama_dok_rj_rd# => dokter
    * @attribute #rua_nama# => ruangan
    * @attribute #kelaspelayanan_nama# => kelas pelayanan
    * @attribute #penjamin_nama# => penjamin
    * @attribute #carabayar_nama# => cara bayar
    * @attribute #status_bayar# => status bayar
    * @attribute #table# => table detail
    **/
    public function actionCetakDetailRincian() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $id = isset($getData['id']) ? $getData['id'] : null;
        if(!is_numeric($id)) {
			$id = DocoHelpers::decrypt($id);
		}
        $noPendaftaran = isset($getData['no_pendaftaran']) ? $getData['no_pendaftaran'] : null;
        if(!empty($noPendaftaran)) {
            $noPendaftaran = DocoHelpers::encrypt($noPendaftaran);
        }
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 30;
        $detail = $this->getDataDetailTagihan($id);
        $countData = count($detail);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'DataDetailRincian' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'id' => $id,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakDetailRincian' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadDetailInvoice' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'noPendaftaran' => $noPendaftaran
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    private function getDataPendaftaranRincian($id) 
    {
        return InfoDataPendaftaran::find()
        ->select(['infodatapendaftaran_v.tgl_pendaftaran', 
            'infodatapendaftaran_v.pendaftaran_id', 
            'infodatapendaftaran_v.no_rekam_medik', 
            'infodatapendaftaran_v.no_pendaftaran', 
            'infodatapendaftaran_v.nama_pasien', 
            'infodatapendaftaran_v.nama_dok_rj_rd', 
            'infodatapendaftaran_v.nama_dok_ri', 
            'infodatapendaftaran_v.rua_nama', 
            'infodatapendaftaran_v.kelaspelayanan_nama', 
            'infodatapendaftaran_v.kelas_ditagihkan', 
            'infodatapendaftaran_v.penjamin_nama', 
            'infodatapendaftaran_v.carabayar_nama', 
            'infodatapendaftaran_v.pasienadmisi_id', 
            'infodatapendaftaran_v.kelaspelayanan_id', 
            'infodatapendaftaran_v.kelas_ditagihkan_id', 
            'infodatapendaftaran_v.penjamin_id', 
            'infodatapendaftaran_v.tagihan_belumbayar', 
            'lookup_m.lookup_name AS status_bayar', 
            'infobayaruangmuka_v.sisa_uangmuka AS sisa_uangmuka',
            'pendaftaranpenjamin_t.nominal_dijamin AS nominal_dijamin',
            'infodatapendaftaran_v.ruangan_nama',
            'infodatapendaftaran_v.ruangan_titipan_nama',
        ])
        ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id = infodatapendaftaran_v.status_bayar')
        ->leftJoin('pendaftaranpenjamin_t', 'pendaftaranpenjamin_t.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->leftJoin('infobayaruangmuka_v', 'infobayaruangmuka_v.pendaftaran_id = infodatapendaftaran_v.pendaftaran_id')
        ->where(['infodatapendaftaran_v.pendaftaran_id' => $id])->asArray()->one();
        
    }

    private function getDataDetailTagihan($id) 
    {
        $db = Yii::$app->db;
        return  $db->createCommand("
            SELECT *
            FROM infotagihanpasien_v 
            WHERE pendaftaran_id = {$id} 
            and sub_total > 0
            order by tgl_pelayanan ASC
        ")->queryAll();

    }

    /**
    * @controller actionCetakSip
    * @attribute #no_pendaftaran# => nomor pendaftaran
    * @attribute #nama_pasien# => nama pasien
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #jenis_kelamin# => jenis kelamin
    * @attribute #tgl_admisi# => tanggal rawatan
    * @attribute #ruangan_nama# => nama ruangan
    * @attribute #lokasi# => kota rs
    * @attribute #rs_name# => nama rs
    * @attribute #nama_pegawai# => nama pegawai
    */
    public function actionCetakSip($id)
    {
        $db = Yii::$app->db;
        $request = Yii::$app->request;
        $nama_pegawai = $request->get('nama_pegawai', null);
        $infoPasien = InfoPasienRiView::find()->select([
            'no_rekam_medik',
            'no_pendaftaran',
            'nama_pasien',
            'jenis_kelamin',
            'ruangan_nama',
            'kamarruangan_nokamar',
            'tgl_admisi',
            'tgl_stopakomodasi',
        ])->where([
            'pendaftaran_id' => $id
        ])->asArray()->one();
        $profilRs = $this->getProfileRs();
        $print = new DocoPrint('sip');
        $print->attributes = [
            '#no_pendaftaran#' => $infoPasien['no_pendaftaran'],
            '#no_rekam_medik#' => $infoPasien['no_rekam_medik'],
            '#nama_pasien#' => $infoPasien['nama_pasien'],
            '#jenis_kelamin#' => $infoPasien['jenis_kelamin'],
            '#tgl_admisi#' => date('d/m/Y g:i A', strtotime($infoPasien['tgl_admisi'])),
            '#ruangan_nama#' => $infoPasien['ruangan_nama'].' '.$infoPasien['kamarruangan_nokamar'],
            '#lokasi#' => $profilRs['kota']. ', ' .date('d-M-Y'),
            '#rs_name#' => !empty($profilRs['namaRs']) ? $profilRs['namaRs'] : '-',
            '#nama_pegawai#' => $nama_pegawai
        ];
        $print->Output();
    }

    public function getAkomodasi($admisiId)
    {
        $db = Yii::$app->db;
        $result = [];
        $data = [];
        try {
            $response = Yii::$app->docoRest->ranap->get('api/get-akomodasi-sementara',[
                'query' => [
                    'admisiId' => $admisiId,
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            $datas = isset($body['response']['tindakan_akomodasi']) ? $body['response']['tindakan_akomodasi'] : [];
            $tmpTindakan = [];
            $tmpRuangan = [];
            $tmpKelasPelayanan = [];
            $totalAkomodasi = 0;
            if(!empty($datas)) {
                foreach($datas as $key => $value) {
                    $qtyTindakan = isset($value['qty_tindakan']) ? $value['qty_tindakan'] : 0;
                    $tarifSatuan = isset($value['tarif_satuan']) ? $value['tarif_satuan'] : 0;
                    $totalAkomodasi += $qtyTindakan * $tarifSatuan;
                    $tindakanId = $value['daftartindakan_id'];
                    $ruanganId = $value['ruangan_id'];
                    $kelasPelayananId = $value['kelaspelayanan_id'];
        
                    if ((!isset($tmpRuangan[$ruanganId]))&&(!isset($tmpKelasPelayanan[$kelasPelayananId]))) {
                        $kelaspelayanan_ruangan = Kamar::find()->select([
                            'ruangan_nama',
                            'kelaspelayanan_nama',
                        ])->where([
                            'ruangan_id' => $ruanganId,
                            'kelaspelayanan_id' => $kelasPelayananId,
                        ])->asArray()->one();
                        $tmpRuangan[$ruanganId] = $kelaspelayanan_ruangan['ruangan_nama'];
                        $tmpKelasPelayanan[$kelasPelayananId] = $kelaspelayanan_ruangan['kelaspelayanan_nama'];
                    }
        
                    if (!isset($tmpTindakan[$tindakanId])) {
                        $tindakan = DaftarTindakan::find()->select([
                            'daftartindakan_nama',
                            'kelompoktindakan_id',
                        ])->where([
                            'daftartindakan_id' => $tindakanId,
                        ])->asArray()->one();
                        $tmpTindakan[$tindakanId] = $tindakan['daftartindakan_nama'];
                        $kelompoktindakanId = $tindakan['kelompoktindakan_id'];
                    }
        
        
                    $value['kelompok_tindakan'] = $kelompoktindakanId;
                    $value['daftartindakan_nama'] = $tmpTindakan[$tindakanId];
                    $value['ruangan_nama'] = $tmpRuangan[$ruanganId];
                    $value['kelaspelayanan_nama'] = $tmpKelasPelayanan[$kelasPelayananId];
                    $data[$key] = $value;
                }
            }

            $kelompoktindakanId = $value['kelompok_tindakan'];
            $kelompoktindakanNama = $db->createCommand("
                SELECT 
                kelompoktindakan_nama
                FROM kelompoktindakan_m 
                WHERE kelompoktindakan_id = {$tindakan['kelompoktindakan_id']}
            ")->queryOne();

            return [
                'tindakan_akomodasi' => $data,
                'total_akomodasi' => $totalAkomodasi,
                'kelompok_tindakan' => $kelompoktindakanNama['kelompoktindakan_nama']
            ];
        }
        catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return [];
        }
    }

    public function actionSaveTmpTagihan($id, $kelompok, $status = null, $tipe_pasien = null)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $tagihanPasien = json_decode($request->post('_tagihanPasien', "[]"),true);
        $pendaftaran_id = $request->post('pendaftaran_id');
        $pasienmasukpenunjang_id = $request->post('pasienmasukpenunjang_id');
        $verify_uid = $request->post('verify_uid');
        $plafon_payer = $request->post('plafon_payer');
        $plafon_subpayer = $request->post('plafon_subpayer');
        $pendaftaran = [];
        if (!empty($pendaftaran_id)) {
            $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
        }
        $isCloseBill = isset($pendaftaran['is_close_bill']) ? $pendaftaran['is_close_bill'] : false;
        if(empty($tagihanPasien) && $isCloseBill) {
            $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $errorMessage
            ];
        }
        $getCondition = $this->getCondition($id, $kelompok, $status, $tipe_pasien);

        $cond = isset($getCondition['cond']) ? $getCondition['cond'] : [];
        $isPenunjang = isset($getCondition['isPenunjang']) ? $getCondition['isPenunjang'] : false;
        $params = isset($getCondition['params']) ? $getCondition['params'] : false;
        $condBill = isset($getCondition['condBill']) ? $getCondition['condBill'] : [];

        $detailTagihan = $this->getDetailTagihan($condBill, $params, $isPenunjang);
        $totalData = count($detailTagihan);

        $listTransObat = $listTransTnd = [];
        $listInsert = $biayaAdm = $list_pelayanan_id = [];

        $params = [
            'id' => $id,
            'kelompok' => $kelompok,
            'status' => $status,
            'tipe_pasien' => $tipe_pasien
        ];
        foreach ($tagihanPasien as $data) {
            $is_obat = isset($data['is_obat']) ? $data['is_obat'] : false;
            $pelayanan_id = isset($data['pelayanan_id']) ? $data['pelayanan_id'] : null;
            $data['plafon_payer'] = isset($plafon_payer) ? $plafon_payer : null;
            $data['plafon_subpayer'] = isset($plafon_subpayer) ? $plafon_subpayer : null;
            $row = array_merge($data, $params);
            array_push($list_pelayanan_id, $pelayanan_id);
            if (!empty($pelayanan_id)) {
                if ($is_obat) {
                    $listTransObat[$pelayanan_id] = $this->mappTmpTabel($row, $pendaftaran_id, $pasienmasukpenunjang_id);
                } else {
                    $listTransTnd[$pelayanan_id] = $this->mappTmpTabel($row, $pendaftaran_id, $pasienmasukpenunjang_id);;
                }
            } else {
                if (empty($biayaAdm)) {
                    $listInsert[] = $this->mappTmpTabel($row, $pendaftaran_id, $pasienmasukpenunjang_id)->attributes;
                    $biayaAdm = true;
                }
            }
        }
        $transaction = $connection->beginTransaction();
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        // if(!empty($list_pelayanan_id)){
        //     $params['pelayanan_id'] = $list_pelayanan_id;
        // }
        try {
            $cekDataExist = TmpInfoTagihanPasien::find()->andWhere($params)->exists();
            if(!empty($cekDataExist)){
                TmpInfoTagihanPasien::deleteAll($params);
            }

            $cekDataExistList = TmpInfoTagihanPasien::find()->andWhere(['pelayanan_id'=>$list_pelayanan_id])->exists();
            if(!empty($cekDataExistList)){
                TmpInfoTagihanPasien::deleteAll(['pelayanan_id'=>$list_pelayanan_id]);
            }
            $logTagihan = $updateKomponen = [];
            foreach ($detailTagihan as $value) {
                $isObat = isset($value['is_obat']) ? $value['is_obat'] : false;
                $pelayanan_id = isset($value['pelayanan_id']) ? $value['pelayanan_id'] : null;
                $tarif_satuan = isset($value['tarif_satuan']) ? round($value['tarif_satuan'],2) : 0;
                $tarif_cyto = isset($value['tarif_cyto']) ? round($value['tarif_cyto'],2) : 0;
                $tarifpenyulit_tindakan = isset($value['tarifpenyulit_tindakan']) ? round($value['tarifpenyulit_tindakan'],2) : 0;

                /** base harga sebelum di ubah */
                $harga_origin = isset($value['harga_origin']) ? round($value['harga_origin'],2) : 0;
                $cyto_origin = isset($value['cyto_origin']) ? round($value['cyto_origin'],2) : 0;
                $penyulit_origin = isset($value['penyulit_origin']) ? round($value['penyulit_origin'],2) : 0;

                $qty = isset($value['qty']) ? $value['qty'] : 0;
                $isOverWrite = !empty($value['is_overwrite']) ? $value['is_overwrite'] : false;
                if ($isObat) {
                    if (isset($listTransObat[$pelayanan_id])) {
                        $row = $listTransObat[$pelayanan_id];
                        $row->harga_origin = $tarif_satuan;
                        $row->cyto_origin = $tarif_cyto;
                        $row->penyulit_origin = $tarifpenyulit_tindakan;

                        $hargaTmp = (float) $row->harga;
                        $subtotal = $hargaTmp * $qty;
                        if ($hargaTmp != $tarif_satuan) {
                            $defaultUpdate = [
                                'hargasatuan_oa' => $hargaTmp,
                                'hargajual_oa' => $subtotal,
                            ];

                            if (empty($isOverWrite)) {
                                $defaultUpdate['harga_origin'] = $row->harga_origin;
                                $defaultUpdate['is_overwrite'] = true;
                            }

                            $this->updateTindakan('obatalkespasien_t',$defaultUpdate, ['obatalkespasien_id' => $pelayanan_id]);

                            $logTagihan[] = $this->setLogEdit($row, $verify_uid);
                        }

                        if ($isOverWrite) {
                            $row->harga_origin = $harga_origin;
                            $row->cyto_origin = $cyto_origin;
                            $row->penyulit_origin = $penyulit_origin;
                        }

                        $listInsert[] = $row->attributes;
                    }
                } else {
                    if (isset($listTransTnd[$pelayanan_id])) {
                        $row = $listTransTnd[$pelayanan_id];

                        $row->harga_origin = $tarif_satuan;
                        $row->cyto_origin = $tarif_cyto;
                        $row->penyulit_origin = $tarifpenyulit_tindakan;

                        $hargaTmp = (float) $row->harga;

                        if ($hargaTmp != $tarif_satuan) {
                            if (empty($isOverWrite)) {
                                $percentUpdate = $tarif_satuan != 0 ? $hargaTmp/$tarif_satuan : 0;
                                $tarif_cyto *= $percentUpdate; 
                                $tarifpenyulit_tindakan *= $percentUpdate;
                                $subtotal = ($hargaTmp + $tarif_cyto + $tarifpenyulit_tindakan) * $qty;

                                $defaultUpdate = [
                                    'tarif_satuan' => $hargaTmp,
                                    'tarif_tindakan' => $subtotal,
                                    'tarifcyto_tindakan' => $tarif_cyto,
                                    'tarifpenyulit_tindakan' => $tarifpenyulit_tindakan,
                                    'harga_origin' => $row->harga_origin,
                                    'cyto_origin' => $row->cyto_origin,
                                    'penyulit_origin' => $row->penyulit_origin,
                                    'is_overwrite' => true,
                                ];
                            } else {
                                $percentUpdate = $harga_origin != 0 ? $hargaTmp/$harga_origin : 0;
                                $tarif_cyto = $cyto_origin * $percentUpdate; 
                                $tarifpenyulit_tindakan = $penyulit_origin * $percentUpdate;
                                $subtotal = ($hargaTmp + $tarif_cyto + $tarifpenyulit_tindakan) * $qty;

                                $defaultUpdate = [
                                    'tarif_satuan' => $hargaTmp,
                                    'tarif_tindakan' => $subtotal,
                                    'tarifcyto_tindakan' => $tarif_cyto,
                                    'tarifpenyulit_tindakan' => $tarifpenyulit_tindakan,
                                ];
                            }

                            $updateKomponen[] = [
                                'pelayanan_id' => $pelayanan_id,
                                'percentUpdate' => $percentUpdate,
                                'pendaftaran_id' => $pendaftaran_id,
                                'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1'
                            ];

                            $this->updateTindakan('tindakanpelayanan_t',$defaultUpdate, ['tindakanpelayanan_id' => $pelayanan_id]);
                            if(isset($value['tipepaket_id']) && !empty($value['tipepaket_id'])){
                                $hargasebelum = isset($value['harga_origin']) ? $harga_origin : $tarif_satuan;
                                $updateDetailPaket = $this->updateTindakanInPaket('tindakanpelayanan_t',$hargasebelum,$hargaTmp,  $pelayanan_id);
                            }
                            $logTagihan[] = $this->setLogEdit($row,$verify_uid);
                        }

                        if ($isOverWrite) {
                            $row->harga_origin = $harga_origin;
                            $row->cyto_origin = $cyto_origin;
                            $row->penyulit_origin = $penyulit_origin;
                        }
                        $listInsert[] = $row->attributes;
                    }
                }
            }
            if (!empty($listInsert)) TmpInfoTagihanPasien::batchInsert($listInsert);
            
            $transaction->commit();

            if (!empty($listInsert)) {
                (new InternalService)->sendTo([
                    'Sirs' => [
                        'LogEditTagihan' => [
                            'log' => $listInsert,
                            'params' => $params,
                            'listPelayananId' => $list_pelayanan_id,
                        ]
                    ]
                ]);
            }

            if (!empty($logTagihan)) {
                (new InternalService)->sendTo([
                    'Sirs' => [
                        'LogTarifTagihan' => [
                            'log' => $logTagihan
                        ]
                    ]
                ]);
            }

            if (!empty($updateKomponen)) {
                (new InternalService)->sendTo([
                    'Sirs' => [
                        'UpdateKomponenTindakan' => [
                            'update_data' => $updateKomponen
                        ]
                    ]
                ]);
            }

            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'line' => $e->getLine(),
                'status' => 500
            ];
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'line' => $e->getLine(),
                'status' => 500
            ];
        }
    }

    protected function setLogEdit($row, $verify_by)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        return [
            'is_tindakan' => !$row->is_obat,
            'pelayanan_id' => $row->pelayanan_id,
            'hargasatuan_sebelum' => $row->harga_origin,
            'hargasatuan_sesudah' => $row->harga,
            'hargacyto_sebelum' => $row->cyto_origin,
            'hargacyto_sesudah' => $row->cyto,
            'hargapenyulit_sebelum' => $row->penyulit_origin,
            'hargapenyulit_sesudah' => $row->penyulit,
            'created_date' => date('Y-m-d H:i:s', time()),
            'created_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1',
            'verify_by' => $verify_by,
        ];
    }

    protected function updateTindakan($tabel, $fieldUpdate = [], $cond)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $default = array_merge([
            'modified_count' => new \yii\db\Expression('COALESCE(modified_count,0) + 1'),
            'last_modified_date' => date('Y-m-d H:i:s', time()),
            'last_modified_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1',
        ], $fieldUpdate);

        return Yii::$app->db->createCommand()
                        ->update($tabel, $default, $cond)
                        ->execute();
    }

    protected function updateTindakanInPaket($tabel, $tarifsebelum, $tarifsesudah, $parent_id)
    {
        $detailPaket = TindakanPelayanan::find()->where(['parent_id'=>$parent_id])->asArray()->all();
        $fieldUpdateDetail=[];
        $ids_tindakanpelayanan = [];
        foreach($detailPaket as $_detail){
            $ids_tindakanpelayanan[] = $_detail['tindakanpelayanan_id'];
            if($tarifsebelum != 0){
                $nominalTarif = $_detail['harga_origin']/$tarifsebelum * $tarifsesudah;
                $fieldUpdateDetail['tarif_satuan'][] = round($nominalTarif,2);
            }else{
                $fieldUpdateDetail['tarif_satuan'][] = (float) $_detail['tarif_satuan'];
            }
        }
        return TindakanPelayanan::batchUpdate($fieldUpdateDetail,['tindakanpelayanan_id' => $ids_tindakanpelayanan]);
    }

    private function mappTmpTabel($val, $registId, $penunjangId)
    {
        $model = new TmpInfoTagihanPasien;
        $defaultPenjamin = !empty($val['defaultPenjamin']) ? json_encode($val['defaultPenjamin']) : '';
        $subPenjamin = !empty($val['subPenjamin']) ? json_encode($val['subPenjamin']) : null;
        $value = !empty($val['value']) ? json_encode($val['value']) : '';
        $tanggal =  !empty($val['tanggal']) ? date('Y-m-d',strtotime($val['tanggal'])): '';
        $tindakan_obat_id = !empty($val['tindakan_obat_id']) ? $val['tindakan_obat_id'] : null;
        $plafon_payer = !empty($val['plafon_payer']) ? $val['plafon_payer'] : null;
        $plafon_subpayer = !empty($val['plafon_subpayer']) ? $val['plafon_subpayer'] : null;
        $keterangan = !empty($val['keterangan']) ? $val['keterangan'] : '';
        $model->attributes = $val;
        $model->pendaftaran_id = $registId;
        $model->pasienmasukpenunjang_id = $penunjangId;
        $model->defaultPenjamin = $defaultPenjamin;
        $model->subPenjamin = $subPenjamin;
        $model->tanggal = $tanggal;
        $model->value = $value;
        $model->keterangan = $keterangan;
        $model->plafon_payer = $plafon_payer;
        $model->plafon_subpayer = $plafon_subpayer;

        return $model;
    }

    private function getCondition($id, $kelompok, $status = null, $tipe_pasien = null)
    {
        $cond = [
            'pendaftaran_id' => $id
        ];

        $isPenunjang = false;
        if ($kelompok == DocoConstants::PASIEN_PENUNJANG) {
            $isPenunjang = true;

            $getPendaftaran = Yii::$app->db->createCommand(
                "SELECT pendaftaran_id FROM pasienmasukpenunjang_t WHERE pasienmasukpenunjang_id = {$id}"
            )->queryOne();

            $cond = [
                'pendaftaran_id' => ArrayHelper::getValue($getPendaftaran, 'pendaftaran_id'),
                'pasienmasukpenunjang_id' => $id
            ];
        } else if (!is_null($tipe_pasien) && in_array($tipe_pasien, ['pasien_bebas','pasien_rs'])) {
            $cond = [
                'penjualanresep_id' => $id
            ];
            $penjualanResep = PenjualanResep::find()->select([
                'pendaftaran_id',
                'biayaadministrasi',
            ])->andWhere($cond)->asArray()->one();

            if (!empty($penjualanResep)) {
                $biayaAdmResep = isset($penjualanResep['biayaadministrasi']) ? $penjualanResep['biayaadministrasi'] : 0;
                /** check pendaftaran awal Gabung Billing */
                $gabungBilling = GabungPelayananDetail::find()->select([
                    'pendaftaran_id',
                    'ref_pendaftaran_id',
                ])->andWhere(['pendaftaran_id' => $penjualanResep['pendaftaran_id']])->one();
                if ($tipe_pasien == 'pasien_rs') {
                    $cond = [
                        'pendaftaran_id' => !empty($gabungBilling) ? $gabungBilling['ref_pendaftaran_id'] : $penjualanResep['pendaftaran_id']
                    ];
                }
            }
        }

        $condBill = $cond;
        switch ($kelompok) {
            case DocoConstants::PASIEN_KARCIS :
                // $condBill['kelompoktindakan_id'] = DocoConstants::VAR_KEL_KRCS;
                break;
            case DocoConstants::PASIEN_ALKES :
                $condBill['is_obat'] = true;
                break;
            default:
                # code...
                break;
        }


        $params = [
            'id' => $id,
            'kelompok' => $kelompok,
            'status' => $status,
            'tipe_pasien' => $tipe_pasien,
            'pendaftaran_id' => ArrayHelper::getValue($cond, 'pendaftaran_id')
        ];

        return compact('cond','isPenunjang', 'params', 'condBill');
    }

    private function getDetailTagihan($cond, $params, $isPenunjang = false)
    {
        $id = $isPenunjang && isset($params['pendaftaran_id']) ? $params['pendaftaran_id'] : $params['id'];
        $gabungTagihan = $this->getGabungTagihan($id);
        if ($params['status']) {
            if (isset($cond['is_obat'])) {
                unset($cond['is_obat']);
            }
            if ($isPenunjang) {
                $header = InformasiPasienPenunjangRincian::find()->where($cond)->one();
            } else {
                $header = InformasiPasienRincian::find()->where($cond)->one();
            }
            $detailTagihan = InfoTagihanPasien::find()->where($cond);
        } else {
            if (in_array($params['tipe_pasien'], ['pasien_bebas','pasien_rs'])) {
                $detailTagihan = InfoTagihanObatDetailView::find()->where([
                    'penjualanresep_id' => $params['id']
                ]);
            } else {
                if(!empty($gabungTagihan)) {
                    $cond = ['ref_pendaftaran_id' => $id];
                }
                $detailTagihan = InfoTagihanPasien::find()->where($cond);
            }
        }

        if ($params['kelompok'] == DocoConstants::PASIEN_ALKES) {
            $detailTagihan->andWhere(['NOT', ['penjualanresep_id' => NULL]]);
        }

        if ($params['kelompok'] == DocoConstants::PASIEN_PENUNJANG) {
            $detailTagihan->andWhere(['NOT IN', 'instalasi_id', DocoConstants::$exceptPenunjang]);
        }

        return $detailTagihan->asArray()->all();
    }

    public function actionGetKontrakPenjamin(){
        $request = Yii::$app->request;
        try {
            if ($request->post()) {
                $penjamin = json_decode($request->post('penjamin', []), true);
                if(is_array($penjamin) && !empty($penjamin)){
                    $listPenjamin = [];
                    foreach($penjamin as $key => $row){
                        if($row['id']){
                            $listPenjamin[] = $row['id']; 
                        }
                    }
                }
                
                $grade = $request->post('grade', null);
                $tgl_pendaftaran = $request->post('tgl_pendaftaran', date("Y-m-d H:i:s"));
                $cond = 'kontrakpenjamin_m.is_active = true AND (:tgl_pendaftaran BETWEEN kontrakpenjamin_m.tgl_mulai AND kontrakpenjamin_m.tgl_selesai) AND
                kontrakpenjamin_m.penjamin_id IN ('.implode($listPenjamin, ",").') AND tipediskon_m.is_active = true';

                $queryKontrakPenjamin = Yii::$app->db->createCommand("
                    SELECT 
                        kontrakpenjamin_m.nama_kontrak,
                        kontrakpenjamin_m.penjamin_id,
                        kontrakpenjamin_m.is_active,
                        kontrakpenjamin_m.tgl_mulai,
                        kontrakpenjamin_m.tgl_selesai,
                        kontrakpenjamin_m.created_date AS created_by,
                        kontrakpenjamindetail_m.tipediskon_id,
                        kontrakpenjamindetail_m.lob_id,
                        kontrakpenjamindetail_m.penjamingrade_id,
                        tipediskondetail_m.tipediskondetail_id,
                        tipediskondetail_m.jenislayanan_id,
                        tipediskondetail_m.layanan_id,
                        tipediskondetail_m.disc_persen,
                        tipediskondetail_m.max_dijamin,
                        tipediskon_m.tipediskon_nama
                    from kontrakpenjamin_m
                        LEFT JOIN kontrakpenjamindetail_m ON kontrakpenjamin_m.kontrakpenjamin_id = kontrakpenjamindetail_m.kontrakpenjamin_id
                        LEFT JOIN tipediskondetail_m ON tipediskondetail_m.tipediskon_id = kontrakpenjamindetail_m.tipediskon_id
                        LEFT JOIN tipediskon_m ON tipediskondetail_m.tipediskon_id = tipediskon_m.tipediskon_id
                    WHERE 
                        " . $cond . " 
                    ORDER BY kontrakpenjamin_m.created_date "
                        )

                ->bindParam(':tgl_pendaftaran',$tgl_pendaftaran)
                ->queryAll();

                $dataKontrakPenjamin = [];
                foreach($queryKontrakPenjamin as $val){
                    $layanan_id = isset($val['layanan_id']) ? $val['layanan_id'] : null;
                    $jenislayanan_id = isset($val['jenislayanan_id']) ? $val['jenislayanan_id'] : null;
                    $lob_id = isset($val['lob_id']) ? $val['lob_id'] : null;
                    $penjamingrade_id = isset($val['penjamingrade_id']) ? $val['penjamingrade_id'] : null;
                    $penjamin_id = isset($val['penjamin_id']) ? $val['penjamin_id'] : null;
                    if($layanan_id != null || $layanan_id != null || $jenislayanan_id != null || $lob_id != null || $penjamingrade_id != null){
                        $dataKontrakPenjamin[$penjamin_id][$penjamingrade_id][$lob_id][$jenislayanan_id][$layanan_id] = $val; 
                    }
                }
                return $dataKontrakPenjamin;
            }else{
                return DocoHelpers::responseTemplate(200, 'Error', 'Params required.');
            }
        } catch (\Exception $e) {
            Yii::error([$e]);
        }
    }

    public function actionGetTmpTagihan($params)
    {
        $infoPasien = TmpInfoTagihanPasien::find()
                            ->andWhere($params)->asArray()->all();
        $result = [];
        $obatTindakan = [
            'obat' => [],
            'tindakan' => [],
            'adm' => [],
        ];
        foreach($infoPasien as $val){

            $defaultPenjamin = !empty($val['defaultPenjamin']) ? json_decode($val['defaultPenjamin']) : '';
            $value = !empty($val['value']) ? json_decode($val['value'], true) : '';
            $tanggal =  !empty($val['tanggal']) ? date('d-M-Y',strtotime($val['tanggal'])): '';
            $keterangan = !empty($val['keterangan']) ? $val['keterangan'] : ' ';
            $dijamin = !empty($val['dijamin']) ? (float)$val['dijamin'] : 0;
            $harga = !empty($val['harga']) ? (float)$val['harga'] : 0;
            $nominal_diskon = !empty($val['nominal_diskon']) ? (float)$val['nominal_diskon'] : 0;
            $persen_diskon = !empty($val['persen_diskon']) ? (float)$val['persen_diskon'] : 0;
            $subtotal = !empty($val['subtotal']) ? (float)$val['subtotal'] : 0;
            $subtotal_origin = !empty($val['subtotal_origin']) ? (float)$val['subtotal_origin'] : 0;
            $total_dibayar = !empty($val['total_dibayar']) ? (float)$val['total_dibayar'] : 0;
            $plafon_payer = !empty($val['plafon_payer']) ? (float)$val['plafon_payer'] : 0;
            $plafon_subpayer = !empty($val['plafon_subpayer']) ? (float)$val['plafon_subpayer'] : 0;
            $isPenjamin = $val['isPenjamin'] == "true" ? true : false;
            $checkPenjamin = $val['checkPenjamin'] == "true" ? true : false;
            $is_obat = $val['is_obat'];
            $val['defaultPenjamin'] =  $defaultPenjamin;
            $val['value'] =  $value;
            $val['tanggal'] =  $tanggal;
            $val['keterangan'] =  $keterangan;
            $val['dijamin'] =  $dijamin;
            $val['harga'] =  $harga;
            $val['nominal_diskon'] =  $nominal_diskon;
            $val['persen_diskon'] =  $persen_diskon;
            $val['subtotal'] =  $subtotal;
            $val['subtotal_origin'] =  $subtotal_origin;
            $val['total_dibayar'] =  $total_dibayar;
            $val['isPenjamin'] =  $isPenjamin;
            $val['checkPenjamin'] =  $checkPenjamin;
            $val['plafon_payer'] =  $plafon_payer;
            $val['plafon_subpayer'] =  $plafon_subpayer;
            $result[] = $val;
        
            $pelId = isset($val['pelayanan_id']) ? $val['pelayanan_id'] : null;
            if($is_obat && !empty($pelId)){
                $obatTindakan['obat'][$pelId]= $val;
            }else{
                if(!empty($pelId)){
                    $obatTindakan['tindakan'][$pelId] = $val;
                }else{
                    $obatTindakan['adm'] = $val;
                }
            }
        }
        return $obatTindakan;
    }

    public function actionDataDetailRincian($id)
    {
        return Yii::$app->docoPlugin->execute('path_detail_rincian');
    }

    private function getDataAdmin(){

        $confSistem = Cache::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $model = new DaftarTindakan;
        $query = $model::find()->where(['daftartindakan_id'=>$admTindakanId])->asArray()->one();
        return isset($query['daftartindakan_nama']) ? $query['daftartindakan_nama'] : 'Administration Fee';
    }

    public function actionCetakDetailInvoice()
    {
        $request = Yii::$app->request;
        $postData = $request->post();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $id = isset($postData['id']) ? $postData['id'] : null;
        $invoice_id = isset($postData['invoice_id']) ? $postData['invoice_id'] : null;
        $jenis_invoice = isset($postData['jenis_invoice']) ? $postData['jenis_invoice'] : 1;
        $nama_pegawai = isset($postData['nama_pegawai']) ? $postData['nama_pegawai'] : null;
        $penjamin_id = isset($postData['penjamin_id']) ? $postData['penjamin_id'] : null;
        $fetchLimit = 20;
        $countData = $this->getTotalData($invoice_id, $jenis_invoice);
        $randString = DocoHelpers::generateRandomString();
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'DataDetailInvoice' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'id' => $id,
                    'invoice_id' => $invoice_id,
                    'jenis_invoice' => $jenis_invoice,
                    'nama_pegawai' => $nama_pegawai,
                    'penjamin_id' => $penjamin_id,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakDetailInvoice' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadDetailInvoice' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    private function getTotalData($invoice_id, $jenis_invoice)
    {
        $db = Yii::$app->db;
        $whereClause = '';
        $sql = "SELECT pembayaran_t.pasienadmisi_id
            FROM pendaftaran_t 
            JOIN pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
            WHERE pembayaran_t.pembayaran_id = {$invoice_id}";

        $data = $db->createCommand($sql)->queryOne();
        $pasienadmisi_id = ($data) ? $data['pasienadmisi_id'] : null;
        if($jenis_invoice == 3) {
            $whereClause = 'AND tarif_dijamin > 0';
        }
        elseif($jenis_invoice == 2) {
            $whereClause = 'AND tarif_dibayarkan > 0';
        }
        // if(!empty($pasienadmisi_id)) {
            $countData = $db->createCommand("
                SELECT COUNT(*)
                FROM invoicesudahbayardetail_v 
                WHERE pembayaran_id = {$invoice_id} AND pembayaranpelayanan_id IS NOT NULL 
                {$whereClause}
                GROUP BY tandabuktibayar_id 
                ORDER BY tandabuktibayar_id DESC
            ")->queryScalar();
        // }
        // else {
        //     $countData = Yii::$app->db->createCommand("
        //         SELECT COUNT(*)
        //             FROM invoicesudahbayardetail_v 
        //             WHERE pembayaran_id = {$invoice_id} AND pembayaranpelayanan_id IS NOT NULL 
        //             {$whereClause}
        //             GROUP BY tandabuktibayar_id 
        //             ORDER BY tandabuktibayar_id DESC
        //     ")->queryScalar();
        // }

        if(!$countData) {
            $countData = 0;
        }

        return $countData;
    }

    public function actionSendFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            $path = 'uploads/'. $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path.'/'.$model->file;
            if($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDownloadInvoice()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    private function getGabungTagihan($pendaftaran_id)
    {
        if(empty($pendaftaran_id)) {
            return [];
        }
        
        return Yii::$app->db->createCommand("
            SELECT * FROM gabungpelayanandetail_t WHERE ref_pendaftaran_id = {$pendaftaran_id} AND is_deleted = FALSE
        ")->queryOne();
    }

    public function actionGetLogActivity()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);

        try {
            $tmpTagihan = [];
            $data = LogEditTagihan::find(true)->select([
                'logedittagihan_r.*',
                'pegawailogin.nama_pegawai'
            ])
            ->leftJoin('pegawai_m pegawailogin', 'logedittagihan_r.created_by = pegawailogin.pegawai_id')
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy(['logedittagihan_r.logedittagihan_id' => SORT_DESC])
            ->asArray()
            ->all();
            
            return [
                'status' => 200,
                'data' => $data,
                'message' => "Get data berhasil !"
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 500,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionCountJobOrder()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id', null);
        return $this->getDataOrder($pendaftaranId)->asArray()->all();
    }

    public function actionGetDataOrder()
    {
        $query = $this->getDataOrder();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getDataOrder()
    {
        $request = Yii::$app->request;
        $model = new NotifikasiJobOrderView;
        $pendaftaranId = $request->get('pendaftaran_id', null);
        $query = $model::find()->where(['pendaftaran_id' => $pendaftaranId]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionCloseBill()
    {
        $request = Yii::$app->request;
        $noPendaftaran = $namaPasien = null;
        $pendaftaranId = $request->get('pendaftaran_id');
        $isCloseBill = $request->post('is_close_bill');
        $alasan = $request->post('alasan', null);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (empty($pendaftaranId)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Data Tidak Di Temukan'
                ];
            }
            
            $dataPendaftaran = InfoDataPendaftaran::find()->where(['pendaftaran_id' => $pendaftaranId])->one();

            if(empty($dataPendaftaran)) {
                /**
                 * ? Case Penjualan Resep & Penunjang
                 */
                $penjualanResep = PenjualanResep::findOne($pendaftaranId);
                $pasienPenunjang = Yii::$app->db->createCommand("SELECT * FROM pasienmasukpenunjang_t WHERE pasienmasukpenunjang_id = {$pendaftaranId}")->queryOne();
                if($penjualanResep) {
                    $pendaftaranId = ArrayHelper::getValue($penjualanResep, 'pendaftaran_id');
                }
                else if($pasienPenunjang) {
                    $pendaftaranId = ArrayHelper::getValue($pasienPenunjang, 'pendaftaran_id');
                }
            }

            $noPendaftaran = ArrayHelper::getValue($dataPendaftaran, 'no_pendaftaran');
            $namaPasien = ArrayHelper::getValue($dataPendaftaran, 'nama_pasien');
            $model = Pendaftaran::findOne($pendaftaranId);
            if($model) {
                $model->is_close_bill = ($isCloseBill == 1) ? false : true;
                if(!$model->validate()) {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
                $model->save();
                $text = ($isCloseBill == 1) ? 'Buka' : 'Tutup';
                $keterangan = ($model->is_close_bill == true) ? 'Lock Bill' : 'Unlock Bill';
                $message = 'Billing Pendaftaran: <b>'.$noPendaftaran.'</b> - <b>'.$namaPasien.'</b> berhasil di '.$text;
                $this->insertLogCloseBill($pendaftaranId, $keterangan, $alasan);
                $transaction->commit();
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => $message,
                    'is_close_bill' => $model->is_close_bill
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function insertLogCloseBill($pendaftaranId, $keterangan, $alasan)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $log = new LogCloseBill;
        $log->pendaftaran_id = $pendaftaranId;
        $log->tgl_close_bill = date('Y-m-d H:i:s');
        $log->tipe = 'Proses Lock Billing';
        $log->keterangan = $keterangan;
        $log->alasan = $alasan;
        $log->created_date = date('Y-m-d H:i:s');
        $log->created_by = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1';
        $log->save();
    }

    public function findDiscountInsurance($penjamin_id)
    {
        try {
            $penjamin = PenjaminDiskonView::find()
            ->select([
                'penjamindiskon_id',
                'penjamin_id',
                'carabayar_id',
                'carabayar_nama',
                'penjamin_kode',
                'penjamin_nama',
                'diskon_otomatis',
            ])->where(['penjamin_id' => [$penjamin_id, DocoConstants::NEW_PENJAMIN_UMUM]])
            ->andWhere(['is_deleted' => false])
            ->andWhere(['is_active' => true])
            ->asArray()->all();
            return $penjamin;
        } catch (\Exception $th) {
            Yii::error(json_encode([
                'status' => 500,
                'data' => [],
                'message' => $th->getMessage()
            ]));
        }
    }
}

