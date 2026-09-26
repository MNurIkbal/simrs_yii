<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Iqbal
 * @Date:   2018-07-27 16:21:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\AsesmenMedisIGD;
use app\modules\v1\models\AsesmenPerawatRD;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\InfoTagihanDetailView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;

use Doco\Services\RmService;


class InfPasienIgdController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienRdV';
    protected $_title = 'Informasi Pasien Rawat Darurat';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        // $verbs["update"] = ["POST", "PUT"];
        // $verbs["komponen-tarif"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $ruanganId = $request->get('idruangan');
            $model = new InfoPasienRdV;
            $query = $model::find();
            // $query->joinWith([
            //     'gantiDokterPj'=>function($query) {
            //         $query->onCondition([
            //             'gantidokterpj_t.jenis_dokter'=>DocoConstants::JNS_DKTR_KNSL, 
            //             'gantidokterpj_t.is_active'=>true,
            //             'gantidokterpj_t.is_deleted'=>false
            //         ]);
            //     }
            // ]);
            $query->orderby('infopasienrd_v.tgl_pendaftaran DESC');

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $advancedFilters = $request->get('advanced-filter', []);

            if (isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($advancedFilters['tgl_pendaftaran']); // Unset Advanced Filter  date range
                }

                if (isset($advancedFilters['no_antrian'])) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_antrian)', strtolower($advancedFilters['no_antrian'])]);
                }

                if (isset($advancedFilters['no_pendaftaran'])) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['no_pendaftaran'])]);
                }

                if (isset($advancedFilters['no_rekam_medik'])) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_rekam_medik)', strtolower($advancedFilters['no_rekam_medik'])]);
                }

                if (isset($advancedFilters['nama_pasien'])) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($advancedFilters['nama_pasien'])]);
                }

                if (isset($advancedFilters['status_periksa_id'])) {
                    $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa_id']]);
                }
            }

            $query->andWhere(['between', 'infopasienrd_v.tgl_pendaftaran', $start, $end]);
            $ruangan_id = $_GET['idruangan'];

            $jwt = Yii::$app->jwt;
            // $pegawai_id = !empty($jwt->user->pegawai_id) ? $jwt->user->pegawai_id : null;
            $pegawai_id = false;

            if ($ruangan_id) {
                $query->andWhere(['infopasienrd_v.ruangan_id' => $ruangan_id]);
                if ($pegawai_id) {
                    $dokter = DokterView::find()
                        // ->andWhere(['ruangan_id'=>$ruangan_id])
                        ->andWhere(['pegawai_id' => $pegawai_id])
                        ->one();
                    if ($dokter) {
                        $query->andWhere('
                            (infopasienrd_v.dokter_jaga_id = ' . $pegawai_id . ' 
                            OR infopasienrd_v.dokter_id = ' . $pegawai_id . '
                            )');
                    }
                }
            } else {
                if ($pegawai_id) {
                    $dokter = DokterView::find()
                        // ->andWhere(['ruangan_id'=>$ruangan_id])
                        ->andWhere(['pegawai_id' => $pegawai_id])
                        ->one();
                    if ($dokter) {
                        $query->andWhere('
                            (infopasienrd_v.dokter_id = ' . $pegawai_id . '
                            OR gantidokterpj_t.dokterbaru_id = ' . $pegawai_id . '
                            )');
                    }
                }
            }

            $status = [
                DocoConstants::STATUS_PERIKSA_BLM_PERIKSA,
                DocoConstants::STATUS_PERIKSA_SET_DOKTER,
                DocoConstants::STATUS_PERIKSA_DIPERIKSA,
                DocoConstants::STATUS_PERIKSA_ANTR_POLI
            ];

            $query->andWhere(['status_periksa_id' => $status]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            // $filter = $query->createCommand()->getRawSql();

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateStatusPeriksa($id)
    {
        try {
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
            if ($pendaftaran && $pendaftaran->status_periksa == DocoConstants::STATUS_PERIKSA_SET_DOKTER) {
                $pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_DIPERIKSA;
                $pendaftaran->save(false);
            }
            $status_periksa = Lookup::find()->select(['lookup_id', 'lookup_name'])->where(['lookup_id' => $pendaftaran->status_periksa])->asArray()->one();
            return [
                'message' => 'Update Status Periksa Berhasil!',
                'data' => [
                    'status_periksa_id' => $status_periksa['lookup_id'],
                    'status_periksa_nama' => $status_periksa['lookup_name']
                ]
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSetDokter()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $dokter_id = $request->post('dokter_id');
            $tgl_masukperiksa = $request->post('tgl_masukperiksa');

            $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
            if ($pendaftaran) {
                if(isset($pendaftaran->status_periksa) && $pendaftaran->status_periksa == DocoConstants::STATUS_PERIKSA_DIPERIKSA){
                    
                    $data_dokter = InfoPasienRdV::find()->select(['dokter_jaga'])
                    ->where(['pendaftaran_id' => $pendaftaran_id])
                    ->asArray()->one();
                    $dokter = isset($data_dokter['dokter_jaga']) ? $data_dokter['dokter_jaga'] : ' - ';
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                        'title' => 'Gagal Assign Dokter',
                        'text' => 'Pasien Sudah Memiliki Dokter Jaga!<br>Dokter Jaga : '.$dokter,
                    ]);
                }
                $pendaftaran->pegawai_id = $dokter_id;
                $pendaftaran->tgl_masukperiksa = $tgl_masukperiksa;
                $pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_SET_DOKTER;

                if($pendaftaran->save(false)) {
                    $rmService = new RmService;
                    $respn = $rmService->periksa([
                        'pendaftaran_id' => [$pendaftaran_id],
                        'status' => DocoConstants::MONITORING_RM_ISSUE,
                    ]);
                }
                return [
                    'message' => 'Data Berhasil di simpan',
                    'data' => $pendaftaran
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDataInstruksiTindakan($pendaftaran_id)
    {
        return $this->getDataInstruksiTindakan($pendaftaran_id);
    }

    private function getDataInstruksiTindakan($pendaftaran_id = "")
    {
        $query = InfoInstruksiView::find();
        $query->where([
            'not in', 'status_implementasi', [
                DocoConstants::ST_P_PEN_SDH_OPRS,            //483
                DocoConstants::RESEPTUR_SUDAH_DIPROSES,        //347 
                DocoConstants::IMPLEMENTASI_SUDAH_IMPLEMENTASI, //455
                DocoConstants::DISETUJUI,                      //471
                DocoConstants::ST_SELESAI,            //475

                DocoConstants::BTL_APPROVE,        //472
                DocoConstants::BTL_PERIKSA_LAB,    //476
                DocoConstants::VAR_B_R,            //432
                DocoConstants::DI_TOLAK,            //451

                DocoConstants::LAB_BELUM_PERIKSA,  //477
                DocoConstants::ST_P_PEN_PRKS,  //473
                DocoConstants::ST_P_PEN_AMB_SAMP,  //474
            ]
        ]);

        if ($pendaftaran_id) {
            $query->andWhere(['pendaftaran_id' => $pendaftaran_id]);
        }
        $result = $query->all();

        return $result;
    }

    public function actionBatal($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $pendaftaran = Pendaftaran::findOne($id);
            if ($pendaftaran) {
                $modelBatal = new PasienBatalPeriksa;
                $modelBatal->attributes = $request->post();
                $modelBatal->tgl_batal = date('Y-m-d');
                $modelBatal->alasan_batal = $request->post('alasan_batal');
                $modelBatal->password = $request->post('password');
                if ($modelBatal->validate()) {
                    $modelBatal->save(false);
                    $pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                    $pendaftaran->pasienbatalperiksa_id = $modelBatal->pasienbatalperiksa_id;
                    $pendaftaran->save(false);

                    $tindakanpelayanan_karcis_id = '';
                    $listTindakanKarcis = [];
                    $tagihanKarcis = InfoTagihanDetailView::find()->where(['pendaftaran_id' => $id, 'kelompoktindakan_id' => DocoConstants::VAR_KEL_KRCS])->asArray()->all();
                    if (!empty($tagihanKarcis)) {
                        foreach ($tagihanKarcis as $key => $value) {
                            $listTindakanKarcis[] = $value['pelayanan_id'];
                        }
                    }

                    if (!empty($listTindakanKarcis)) {
                        $tindakanpelayanan_karcis_id = "(" . implode(",", $listTindakanKarcis) . ")";
                    }

                    if (!empty($tindakanpelayanan_karcis_id)) {
                        Yii::$app->db->createCommand("
                                UPDATE tindakanpelayanan_t SET is_deleted = TRUE WHERE (tindakanpelayanan_id IN {$tindakanpelayanan_karcis_id})
                            ")->execute();
                    }

                    $transaction->commit();
                    return [
                        'message' => 'Data Pasien Berhasil Dibatalkan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($modelBatal->errors, 'PasienBatalPeriksaForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionExportRincianTagihanPdf
     * @attribute #tanggal# => Tanggal Pendaftaran 
     * @attribute #no_pendaftaran# => Nomor Pendaftaran
     * @attribute #no_rm# => Nomor Rekam Medik 
     * @attribute #nama# => Nama pasien 
     * @attribute #jenis_kasus_penyakit# => Jenis Kasus Penyakit 
     * @attribute #ruangan# => Ruangan 
     * @attribute #dokter# => Nama dokter 
     * @attribute #kelas_pelayanan# => Kelas Pelayanan 
     * @attribute #penjamin# => Penjamin 
     * @attribute #cara_bayar# => Cara Bayar 
     * @attribute #status_bayar# => Status Bayar 
     * @attribute #total_tagihan# => Menampilkan detail tindakan
     * @attribute #riwayat_pembayaran# => Menampilkan Tabel tagihan riwayat pasien 
     * @attribute #detail_tindakan# => Menampilkan detail tindakan
     * @attribute #total_uang_muka# => Menampilkan detail tindakan
     * @attribute #total_dibayar# => Menampilkan detail tindakan
     * @attribute #sisa_tagihan# => Menampilkan detail tindakan
     * @attribute #biaya_admin# => Menampilkan biaya Admin
     * @attribute #pembulatann# => Menampilkan biaya Pembulatan
     * @attribute #subsidi_asuranasi# => Menampilkan biaya subsidi asuransi
     * @attribute #detail_tindakan# => Tindakan Laboratorium
     * 
     **/
    public function actionExportRincianTagihanPdf($pendaftaran_id)
    {
        $header = Yii::$app->db->createCommand("
             SELECT
                tgl_pendaftaran,
                no_rekam_medik,
                no_pendaftaran,
                nama_pasien,
                jeniskasuspenyakit_nama,
                dok_pendaftaran,
                ruangan_pendaftaran,
                kelaspelayanan_nama,
                penjamin_nama,
                carabayar_nama,
                status_bayar
            FROM
                rincianpasien_v
            WHERE
                pendaftaran_id = {$pendaftaran_id}            
        ")->queryOne();

        $header_total = Yii::$app->db->createCommand("
             SELECT
                total_tagihan AS total_tagihan,
                total_sdh_bayar AS total_tagihan_sdh_bayar,
                total_sisa_tagihan AS total_sisa_tagihan,
                total_uang_muka AS total_uang_muka,
                total_administrasi AS total_administrasi,
                total_pembulatan AS total_pembulatan,
                total_asuransi AS total_asuransi
            FROM
                rincianpasiendetail2_v
            WHERE
                pendaftaran_id = {$pendaftaran_id}
        ")->queryOne();

        $detail = Yii::$app->db->createCommand("
            SELECT * FROM rincianpasiendetail_v WHERE pendaftaran_id = {$pendaftaran_id}
        ")->queryAll();
        // return $detail;
        $ruangan = '';
        $totalTagihan = 0;
        $arrPemeriksaan = [];
        foreach ($detail as $value) {
            $is_obat = isset($value['is_obat']) ? $value['is_obat'] : null;
            $instalasi = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
            if (!empty($value['ruangan_pelayanan']) && $value['ruangan_pelayanan'] != "") {
                $ruangan = isset($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : null;
            }
            if (!isset($listData[$value['pendaftaran_id']])) {
                $listData[$value['pendaftaran_id']] = [
                    'obat' => [],
                    'tindakan' => [],
                    'pemeriksaan' => [],
                    'penunjang' => []
                ];
            }

            if ($is_obat) {
                $listData[$value['pendaftaran_id']]['obat'][] = $value;
            } else {
                if (!empty($value['ruangan_pelayanan'])) {
                    $arrPemeriksaan[$value['ruangan_pelayanan_id']][$value['ruangan_pelayanan']][] = $value;
                }

                if (in_array($instalasi, DocoConstants::$exceptPenunjang)) {
                    // $listData[$value['pendaftaran_id']]['tindakan'][$instalasi]['data'][] = $value;
                    // $listData[$value['pendaftaran_id']]['tindakan'][$instalasi]['title'] = $ruangan;
                } else {
                    $listData[$value['pendaftaran_id']]['penunjang'][$instalasi]['data'][] = $value;
                    $listData[$value['pendaftaran_id']]['penunjang'][$instalasi]['title'] = $ruangan;
                }
            }
            $totalTagihan += $value['jumlah_tarif'];
        }

        foreach ($arrPemeriksaan as $key => $value) {
            if (in_array($key, DocoConstants::$exceptPenunjang)) {
                foreach ($value as $kt => $vt) {
                    $pendaftaran_id = isset($vt[0]['pendaftaran_id']) ? $vt[0]['pendaftaran_id'] : null;
                    $instalasi_id = isset($vt[0]['instalasi_id']) ? $vt[0]['instalasi_id'] : null;

                    $listData[$pendaftaran_id]['tindakan'][$instalasi_id]['data'][] = $vt;
                    $listData[$pendaftaran_id]['tindakan'][$instalasi_id]['title'] = $kt;
                }
            } else {
                foreach ($value as $kp => $vp) {
                    $pendaftaran_id = isset($vp[0]['pendaftaran_id']) ? $vp[0]['pendaftaran_id'] : null;
                    $instalasi_pelayanan = isset($vp[0]['instalasi_pelayanan']) ? $vp[0]['instalasi_pelayanan'] : null;

                    $listData[$pendaftaran_id]['pemeriksaan'][$instalasi_pelayanan]['data'][] = $vp;
                    $listData[$pendaftaran_id]['pemeriksaan'][$instalasi_pelayanan]['title'] = $kp;
                }
            }
        }

        $query = $header;
        $query_total = $header_total; // untuk menampung data total tagihan dan lain-lain
        $sisa_tagihan = 0;

        $total_asuransi = isset($query_total['total_asuransi']) ? $query_total['total_asuransi'] : 0;
        $total_tagihan_sdh_bayar = isset($query_total['total_tagihan_sdh_bayar']) ? $query_total['total_tagihan_sdh_bayar'] : 0;
        $sisa_tagihan = $totalTagihan - $total_asuransi - $total_tagihan_sdh_bayar;

        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();

            $print->attributes = [
                '#tanggal#' => isset($query['tgl_pendaftaran']) ? $query['tgl_pendaftaran'] : null,
                '#no_rm#' => isset($query['no_rekam_medik']) ? $query['no_rekam_medik'] : null,
                '#no_pendaftaran#' => isset($query['no_pendaftaran']) ? $query['no_pendaftaran'] : null,
                '#nama#' => isset($query['nama_pasien']) ? $query['nama_pasien'] : null,
                '#jenis_kasus_penyakit#' => isset($query['jeniskasuspenyakit_nama'])
                    ? $query['jeniskasuspenyakit_nama'] : null,
                '#dokter#' => isset($query['dok_pendaftaran'])
                    ? $query['dok_pendaftaran'] : null,
                '#ruangan#' => isset($query['ruangan_pendaftaran'])
                    ? $query['ruangan_pendaftaran'] : null,
                '#kelas_pelayanan#' => isset($query['kelaspelayanan_nama']) ? $query['kelaspelayanan_nama'] : null,
                '#penjamin#' => isset($query['penjamin_nama']) ? $query['penjamin_nama'] : null,
                '#cara_bayar#' => isset($query['carabayar_nama']) ? $query['carabayar_nama'] : null,
                '#status_bayar#' => isset($query['status_bayar']) ? $query['status_bayar'] : null,

                '#total_tagihan#' => isset($totalTagihan)
                    ? DocoHelpers::rupiahDisplay($totalTagihan) : 0,
                '#total_uang_muka#' => isset($query_total['total_uang_muka'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_uang_muka']) : 0,
                '#total_dibayar#' => DocoHelpers::rupiahDisplay($total_tagihan_sdh_bayar),
                '#sisa_tagihan#' => DocoHelpers::rupiahDisplay($sisa_tagihan),
                '#biaya_admin#' => isset($query_total['total_administrasi'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_administrasi']) : 0,
                '#pembulatan#' => isset($query_total['total_pembulatan'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_pembulatan']) : 0,
                '#subsidi_asuranasi#' => DocoHelpers::rupiahDisplay($total_asuransi),
                '#detail_tindakan#' => $this->renderPartial('riwayat_pemeriksaan', [
                    'detail' => $listData[$pendaftaran_id]
                ]),
            ];

            $print->Output();
        }
    }

    /**
     * @controller actionExportPdf
     * @attribute #table_pasien# => Untuk Menampilkan Tabel pasien ranap
     * @attribute #periode# => untuk menampilkan periode data
     * @attribute #tanggal# => untuk menampilkan tanggal sekarang
     * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
     * @attribute #jenis# => untuk menampilkan title
     * @attribute #cetak_oleh# => untuk menampilkan pencetak
     * @attribute #kepala# => untuk menampilkan nama kepala ruangan
     * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
     **/

    public function actionExportPdf($idruangan)
    {
        $data = [];
        $jenis_title = 'Informasi Pasien Rawat Darurat';
        $ruangan_id = $_GET['idruangan'];

        $request = Yii::$app->request;
        $model = new InfoPasienRdV;
        $query = $model::find();
        /*$query->joinWith([
            'gantiDokterPj'=>function($query) {
                $query->onCondition([
                    'gantidokterpj_t.jenis_dokter'=>DocoConstants::JNS_DKTR_KNSL, 
                    'gantidokterpj_t.is_active'=>true,
                    'gantidokterpj_t.is_deleted'=>false
                ]);
            }
        ]);*/
        $query->orderby('infopasienrd_v.tgl_pendaftaran DESC');

        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $dokter = DokterView::find()->andWhere(['pegawai_id' => $pegawai_id])->one();

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');


        $dataKepala = (PegawaiView::find()->where(['ruangan_id' => $idruangan, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id' => $idruangan, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one() : '';

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
            if (count($explode) == 2) {
                $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($advancedFilters['tgl_pendaftaran']); // Unset Advanced Filter  date range
        }


        $queries = $this->queryListPasien($request, $query);

        $resQuery = DocoRestActiveFilter::advancedFilter($model, $queries);
        $data = $resQuery->asArray()->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#table_pasien#' => $this->renderPartial('_cetak_pdf', [
                'detail' => $data
            ]),
            '#periode#' => date('d F Y', strtotime($tgl_awal)) . ' - ' . date('d F Y', strtotime($tgl_akhir)),
            '#tanggal#' => date('d F Y'),
            '#tanggal_cetak#' => date('d F Y H:i:s'),
            '#jenis#' => $jenis_title,
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#kepala#' => ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
            '#kepalanip#' => ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
        ];

        $print->Output();
    }

    public function queryListPasien($request, $query)
    {

        $start = date('Y-01-d 00:00:00');
        $end = date('Y-12-d 23:59:59');
        $advancedFilters = $request->get('advanced-filter', []);

        if (isset($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($advancedFilters['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }

            if (isset($advancedFilters['no_antrian'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(no_antrian)', strtolower($advancedFilters['no_antrian'])]);
            }

            if (isset($advancedFilters['no_pendaftaran'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['no_pendaftaran'])]);
            }

            if (isset($advancedFilters['no_rekam_medik'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(no_rekam_medik)', strtolower($advancedFilters['no_rekam_medik'])]);
            }

            if (isset($advancedFilters['nama_pasien'])) {
                $query->andFilterWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($advancedFilters['nama_pasien'])]);
            }

            if (isset($advancedFilters['status_periksa_id'])) {
                $query->andWhere(['status_periksa_id' => $advancedFilters['status_periksa_id']]);
            }
        }

        $query->andWhere(['between', 'infopasienrd_v.tgl_pendaftaran', $start, $end]);
        $ruangan_id = $request->get('idruangan');

        if ($ruangan_id) {
            $query->andWhere(['infopasienrd_v.ruangan_id' => $ruangan_id]);
        }

        $status = [
            DocoConstants::STATUS_PERIKSA_BLM_PERIKSA,
            DocoConstants::STATUS_PERIKSA_SET_DOKTER,
            DocoConstants::STATUS_PERIKSA_DIPERIKSA,
            DocoConstants::STATUS_PERIKSA_ANTR_POLI
        ];
        $query->andWhere(['status_periksa_id' => $status]);

        return $query;
    }

    public function actionExportExcel($idruangan)
    {
        $data = [];
        $title = 'Informasi Pasien Rawat Darurat';
        $ruangan_id = $_GET['idruangan'];

        $request = Yii::$app->request;
        $model = new InfoPasienRdV;
        $query = $model::find();

        $query->orderby('infopasienrd_v.tgl_pendaftaran DESC');

        // $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        // $dokter = DokterView::find()->andWhere(['pegawai_id'=>$pegawai_id])->one();

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');


        // $dataKepala = (PegawaiView::find()->where(['ruangan_id'=>$idruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id'=>$idruangan, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one() : '';

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
            if (count($explode) == 2) {
                $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($advancedFilters['tgl_pendaftaran']); // Unset Advanced Filter  date range
        }


        $queries = $this->queryListPasien($request, $query);

        $resQuery = DocoRestActiveFilter::advancedFilter($model, $queries);
        $data = $resQuery->asArray()->all();
        $result = [];
        $counter = 0;
        foreach ($data as $key => $value) {
            $counter++;
            $dataValue['Tanggal Pendaftaran'] =  date('d F Y H:i:s', strtotime($value['tgl_pendaftaran']));
            $dataValue['Nomor Pendaftaran'] = $value['no_pendaftaran'];
            $dataValue['Nomor Rekam Medik'] = $value['no_rekam_medik'];
            $dataValue['Nama Pasien'] = $value['nama_pasien'];
            $dataValue['Jenis Kelamin'] = $value['jenis_kelamin'];
            $dataValue['Penjamin'] = $value['penjamin_nama'];
            $dataValue['Dokter Jaga'] = $value['dokter_jaga'];
            $dataValue['Dokter Penanggung Jawab'] = $value['dokter'];
            $dataValue['Status Pasien'] = $value['status_periksa'];
            $result[] = $dataValue;
        }

        $header = array(
            Yii::t('app', "Periode") => date('d F Y', strtotime($tgl_awal)) . ' - ' . date('d F Y', strtotime($tgl_akhir)),
            Yii::t('app', "Nomor pendaftaran") => isset($advancedFilters['no_pendaftaran']) ? $advancedFilters['no_pendaftaran'] : '-',
            Yii::t('app', "Nomor rekam medik") => isset($advancedFilters['no_rekam_medik']) ? $advancedFilters['no_rekam_medik'] : '-',
            Yii::t('app', "Nama Pasien") => isset($advancedFilters['nama_pasien']) ? $advancedFilters['nama_pasien'] : '-',
            Yii::t('app', "Jenis Kelamin") => isset($advancedFilters['jenis_kelamin']) ? $advancedFilters['jenis_kelamin'] : '-',
            Yii::t('app', "Penjamin") => isset($advancedFilters['penjamin_nama']) ? $advancedFilters['penjamin_nama'] : '-',
            Yii::t('app', "Dokter Jaga") => isset($advancedFilters['dokter_jaga']) ? $advancedFilters['dokter_jaga'] : '-',
            Yii::t('app', "Dokter Penanggung Jawab") => isset($advancedFilters['dokter']) ? $advancedFilters['dokter'] : '-',
            Yii::t('app', "Status Pasien") => isset($advancedFilters['status_periksa']) ? $advancedFilters['status_periksa'] : '-',
        );
        // $header = ['Periode'=> date('d F Y', strtotime($tgl_awal)).' - '.date('d F Y', strtotime($tgl_akhir))];
        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);

        $filePath->save('php://output');
        die;
    }

    public function actionBundleDataBatalPeriksa()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $countApprovedInstruksi = 0;

            $asKep = AsesmenPerawatRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            $asMed = AsesmenMedisIGD::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            $instruksi = InfoInstruksiView::find()->where([
                'pendaftaran_id' => $pendaftaran_id,
                'tindakan_deleted' => false
            ])->asArray()->all();

            if (!empty($instruksi)) {
                $arrStatus = array_column($instruksi, 'status_implementasi');
                /**
                 * Reseptur
                 * 347 = Sudah Diproses
                 * 
                 * Implementasi
                 * 455 = Sudah Implementasi
                 * 
                 * Penunjang
                 * 477 = Sudah disetujui/belum periksa penunjang
                 * 472 = Batal
                 * 473 = Periksa
                 * 475 = Selesai
                 * 476 = Batal
                 * 482 = Sedang operasi
                 * 483 = Sudah operasi
                 * 541 = Ditolak
                 */
                foreach ($arrStatus as $eachStatus) {
                    $countApprovedInstruksi += in_array((int) $eachStatus, [347, 455, 477, 472, 473, 475, 476, 482, 483, 541]);
                }
            }

            return [
                'data_askep' => $asKep,
                'data_asmed' => $asMed,
                'data_instruksi' => $instruksi,
                'count_approved_instruksi' => $countApprovedInstruksi
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => 'Terjadi kesalahan pada server'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => 'Terjadi kesalahan pada server'
            ];
        }
    }
}
