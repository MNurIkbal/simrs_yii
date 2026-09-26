<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-08-15 08:01:21
 * @Last Modified by:   DOCOTEL
 * @Last Modified time: 2019-06-25 16:50:25
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;
use yii\db\ArrayExpression;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoPasienPulangRjRd;
use app\modules\v1\models\PasienBatalPulang;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\KesimpulanRD;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;



class InfPasienPulangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienPulangRJRD';
    protected $_title = 'Informasi Pasien Pulang';
	protected $nama_pegawai;

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

    private function model()
    {
        return InfoPasienPulangRJRD::find();
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPasienPulangRjRd;
            // $query = $this->model();
            $query = $model::find();

            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $orderby = ['tglpasienpulang' => SORT_DESC];
            $advancedFilters = $request->get('advanced-filter', []);

            if (isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    if ($advancedFilters['tgl_pendaftaran'] != ' - ') {
                        $explode = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                        $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_pendaftaran']);
                        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                    $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
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
            }
            $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);

            $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD]);
            $status = [
                DocoConstants::STATUS_PERIKSA_BLM_PERIKSA,
                DocoConstants::STATUS_PERIKSA_SET_DOKTER,
                DocoConstants::STATUS_PERIKSA_DIPERIKSA,
                DocoConstants::STATUS_PERIKSA_ANTR_POLI
            ];
            $query->andWhere(['<>', 'status_periksa', $status]);
            $query->orderby($orderby);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getData($id = "")
    {
        $model =  $this->model();
        if ($id) {
            $model->where(['pendaftaran_id' => $id]);
        }

        return $model;
    }

    public function actionDataInfoPasienPulang()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $getData = $this->getData($pendaftaran_id);
            $result = $getData->asArray()->one();

            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionBatalPulang()
    {
        $transaction = PasienBatalPulang::getDb()->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $pasienPulangId = $post['pasienpulang_id'];

            // checking password
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            $check = $jwt->katakunci_pemakai;
            $valid = Yii::$app->security->validatePassword($post['password'], $check);
            if (!$valid) {
                return ['data' => ['Password Salah'], 'status' => 422];
            }


            $modelBatalPulang = new PasienBatalPulang;
            $modelBatalPulang->attributes =  $post;
            if ($modelBatalPulang->validate()) {
                $pasienPulang = PasienPulang::findOne($pasienPulangId);
                if ($pasienPulang) {
                    $pendaftaran = Pendaftaran::findOne($pasienPulang->pendaftaran_id);
                    if(!empty($pendaftaran->pasienadmisi_id)){
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                            'title' => 'Terjadi Kesalahan',
                            'text' => 'Pasien sudah melakukan pendaftaran, Harap melakukan batal kunjungan terlebih dahulu',
                        ]);
                    }
                    if ($pendaftaran) {
                        $pendaftaran->pasienpulang_id = NULL;
                        $pendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_DIPERIKSA;
                        if ($pendaftaran->save(false)) {
                            if ($modelBatalPulang->save(false)) {
                                $pasienPulang->pasienbatalpulang_id = $modelBatalPulang->pasienbatalpulang_id;
                                $pasienPulang->is_deleted = true;
                                if ($pasienPulang->save(false)) {
                                    $kesimpulan = KesimpulanRD::find()
                                        ->andWhere(['pendaftaran_id' => $pasienPulang->pendaftaran_id])
                                        ->one();
                                    if ($kesimpulan) {
                                        $kesimpulan->is_deleted = true;
                                        if (!$kesimpulan->save(false)) {
                                            $transaction->rollBack();
                                            $errors = DocoHelpers::parseError($kesimpulan->errors, 'SetujuiPermintaanKonsul');
                                            return ['data' => $errors, 'status' => 422];
                                        }
                                    }
                                    (new \app\modules\v1\models\Triase)->updateTempatTidur($pasienPulang->pendaftaran_id);
                                    $transaction->commit();
                                    return [
                                        'message' => 'Data Berhasil di simpan',
                                    ];
                                } else {
                                    $transaction->rollBack();
                                    $errors = DocoHelpers::parseError($pasienPulang->errors, 'SetujuiPermintaanKonsul');
                                    return ['data' => $errors, 'status' => 422];
                                }
                            } else {
                                $transaction->rollBack();
                                $errors = DocoHelpers::parseError($modelBatalPulang->errors, 'SetujuiPermintaanKonsul');
                                return ['data' => $errors, 'status' => 422];
                            }
                        } else {
                            $transaction->rollBack();
                            $errors = DocoHelpers::parseError($pendaftaran->errors, 'SetujuiPermintaanKonsul');
                            return ['data' => $errors, 'status' => 422];
                        }
                    } else {
                        return ['data' => is_null($pendaftaran) ? ['Data pendaftaran tidak ditemukan'] : $pendaftaran->errors, 'status' => 422];
                    }
                } else {
                    return ['data' => is_null($pasienPulang) ? ['Data Pasien Pulang tidak ditemukan'] : $pasienPulang->errors, 'status' => 422];
                }
            } else {
                return ['data' => $modelBatalPulang->errors, 'status' => 422];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            // Status code
            \Yii::error($e->getTraceAsString());
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk menampilkan data table
     */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Informasi Pasien Pulang Rawat Darurat Rumah Sakit';
            $get = $request->get();
            
            $model = new InfoPasienPulangRjRd;
            $query = $this->model();

            $tgl_awal = $tgl_pendaftaran_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = $tgl_pendaftaran_akhir = date('Y-m-d 23:59:00');
            $orderby = ['tglpasienpulang' => SORT_DESC];
            $advancedFilters = $request->get('advanced-filter', []);

            if (isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    if ($advancedFilters['tgl_pendaftaran'] != ' - ') {
                        $explode = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                        $tgl_pendaftaran_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_pendaftaran_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_pendaftaran']);
                        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_pendaftaran_awal, $tgl_pendaftaran_akhir]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                    $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
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
            }

            if ( empty( $request->get('advanced-filter', [])['tgl_pendaftaran'] ) && empty( $request->get('advanced-filter', [])['tglpasienpulang'] )  ){
                $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby($orderby);
            $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD]);
            $resultData = $query->asArray()->all();

            $result = [];
            foreach ($resultData as $key => $value) {
                $result[] = $value;
            }

            $header = [
                'Periode' => date('d F Y H:i:s', strtotime($tgl_awal)) . ' - ' . date('d F Y H:i:s', strtotime($tgl_akhir))
            ];
            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Informasi Pasien Pulang Rawat Darurat Rumah Sakit';
            $model = new InfoPasienPulangRjRd;
            $query = $this->model();

            $tgl_awal = $tgl_pendaftaran_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = $tgl_pendaftaran_akhir = date('Y-m-d 23:59:00');
            $orderby = ['tglpasienpulang' => SORT_DESC];
            $advancedFilters = $request->get('advanced-filter', []);

            if (isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_pendaftaran'])) {
                    if ($advancedFilters['tgl_pendaftaran'] != ' - ') {
                        $explode = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                        $tgl_pendaftaran_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $tgl_pendaftaran_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                        unset($advancedFilters['tgl_pendaftaran']);
                        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_pendaftaran_awal, $tgl_pendaftaran_akhir]);
                    }
                }

                if (isset($advancedFilters['tglpasienpulang'])) {
                    $explode = explode(' - ', $advancedFilters['tglpasienpulang']);
                    $tgl_awal = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $tgl_akhir = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    unset($advancedFilters['tglpasienpulang']);
                    $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
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
            }

            if ( empty( $request->get('advanced-filter', [])['tgl_pendaftaran'] ) && empty( $request->get('advanced-filter', [])['tglpasienpulang'] )  ){
                $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby($orderby);
            $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD]);
            $resultData = $query->asArray()->all();

            $no = 0;
            foreach ($resultData as $key => $value) {
                $no++;
                $first_str_no_telepon = substr($value['no_telepon_pasien'],0, 1); //get karakter pertama, untuk set string jika value pertamanya bukan 0, untuk excel biar gak kebaca integer
                $data['No'] = $no;
                $data['Tanggal Pendaftaran'] = date('d M Y H:i:s', strtotime($value['tgl_pendaftaran']));
                $data['Tanggal Pulang'] = date('d M Y H:i:s', strtotime($value['tglpasienpulang']));
                $data['Nomor Pendaftaran'] = $value['no_pendaftaran'];
                $data['Nomor Rekam Medik'] = $value['no_rekam_medik'];
                $data['Nomor Telepon'] = $value['no_telepon_pasien'];
                $data['Nama Pasien'] = $value['nama_pasien'];
                $data['Ruangan'] = $value['ruangan_nama'];
                $data['Jenis Kelamin'] = $value['jenis_kelamin'];
                $data['Penjamin'] = $value['penjamin_nama'];
                $data['Dokter'] = ArrayHelper::getValue($value, 'dokter', '-');
                $data['Cara Pulang'] = ArrayHelper::getValue($value, 'carakeluar_nama', '-');
                $data['Dipulangkan Oleh'] = ArrayHelper::getValue($value, 'petugas_pemulang_nama', '-');
                $result[] = $data;
            }

            $header = [
                'Periode' => date('d F Y H:i:s', strtotime($tgl_pendaftaran_awal)) . ' - ' . date('d F Y H:i:s', strtotime($tgl_pendaftaran_akhir))
            ];

            $options = [
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'startRow' => 'B13',
                        'endRow' => 'AG13'
                    ],
                    [
                        'startRow' => 'B15',
                        'endRow' => 'AG15'
                    ],
                    [
                        'startRow' => 'B19',
                        'endRow' => 'AG19'
                    ],
                    [
                        'startRow' => 'F5',
                        'endRow' => 'F'. (count($resultData) + 5), // +5 karena awalnya dari F5 bukan dari F1
                        'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT
                    ],
                ],
            ];

            $filePath = DocoHelpers::exportExcel("Informasi Pasien Pulang Rawat Darurat Rumah Sakit", $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    /**
     * @controller actionPrintRincian
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

    public function actionPrintRincian($id)
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
                pendaftaran_id = {$id}

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
                pendaftaran_id = {$id}
        ")->queryOne();

        $detail = Yii::$app->db->createCommand("
            SELECT * FROM rincianpasiendetail_v WHERE pendaftaran_id = {$id}
        ")->queryAll();
        // return $detail;
        $ruangan = '';
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
                    'penunjang' => []
                ];
            }

            if ($is_obat) {
                $listData[$value['pendaftaran_id']]['obat'][] = $value;
            } else {
                // Penunjang
                if (in_array($instalasi, DocoConstants::$exceptPenunjang)) {
                    $listData[$value['pendaftaran_id']]['tindakan'][$instalasi]['data'][] = $value;
                    $listData[$value['pendaftaran_id']]['tindakan'][$instalasi]['title'] = $ruangan;
                } else {
                    $listData[$value['pendaftaran_id']]['penunjang'][$instalasi]['data'][] = $value;
                    $listData[$value['pendaftaran_id']]['penunjang'][$instalasi]['title'] = $ruangan;
                }
            }
        }
        Yii::warning($listData);
        
        $query = $header;
        $query_total = $header_total; // untuk menampung data total tagihan dan lain-lain
        // echo '<pre>';
        // print_r($query);
        // echo '</pre>';
        // exit;
        // return $query;
        // return $listData;
        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            // foreach ($header as $key => $query) {
            // $idParent = isset($query['pendaftaran_id']) ? $query['pendaftaran_id'] : null;

            $print->attributes = [
                '#printed_date#' => date('d/M/Y H:i'),
                '#printed_by#' => Yii::$app->jwt->user->nama_pemakai,
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
                '#status_bayar#' => isset($query['status']) ? $query['status'] : null,

                '#total_tagihan#' => isset($query_total['total_tagihan'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_tagihan']) : 0,
                '#total_uang_muka#' => isset($query_total['total_uang_muka'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_uang_muka']) : 0,
                '#total_dibayar#' => isset($query_total['total_tagihan_sdh_bayar'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_tagihan_sdh_bayar']) : 0,
                '#sisa_tagihan#' => DocoHelpers::rupiahDisplay($query_total['total_sisa_tagihan']),
                '#biaya_admin#' => isset($query_total['total_administrasi'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_administrasi']) : 0,
                '#pembulatan#' => isset($query_total['total_pembulatan'])
                    ? DocoHelpers::rupiahDisplay($query_total['total_pembulatan']) : 0,
                '#subsidi_asuransi#' => 0,
                '#detail_tindakan#' => $this->renderPartial('riwayat_pemeriksaan', [
                    'detail' => $listData[$id]
                ]),
            ];

            // $break = (($key + 1) == $countData) ? false : true;
            // $print->generateHtml($break);
            // }
            $print->Output();
        }
    }
}
