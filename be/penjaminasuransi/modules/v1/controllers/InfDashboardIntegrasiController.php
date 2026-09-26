<?php

namespace app\modules\v1\controllers;

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

use app\modules\v1\models\AsuransiT;
use Yii;
use app\modules\v1\models\DashboardIntegrasiAsuransiV;
use app\modules\v1\models\InfoTagihanPasienAsuransiV;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\RequestItemAsuransiView;
use app\modules\v1\models\SyKunjunganPasien;
use SirsCore\businessLogic\AsuransiDataSource;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoRestActiveFilter;
use Doco\models\DiagnosaView;
use Doco\models\IntegrasiTindakanObatAsuransi;
use Doco\models\kasir\InvoiceSudahBayarView;
use Doco\models\LogAsuransiTransaksi;
use Doco\models\Pasien;
use Doco\models\Pendaftaran;
use Doco\models\ResumeMedisRi;
use Doco\rabbitmq\RabbitBgProcess;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

class InfDashboardIntegrasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienBpjsView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['cron-integrasi-asuransi'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['cron-integrasi-asuransi'],
        ];

        return $behaviors;
    }

    public function actionInitData()
    {
        try {
            $statusPeriksa = Lookup::find()
                ->select([
                    'lookup_type',
                    'lookup_id',
                    'lookup_name',
                ])
                ->where(['lookup_id' => [
                    DocoConstants::STATUS_PERIKSA_ANTR_POLI,
                    DocoConstants::STATUS_PERIKSA_DIPERIKSA,
                    DocoConstants::STATUS_PERIKSA_PLG,
                    DocoConstants::STATUS_PERIKSA_SDH_PERIKSA,
                    DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
                    DocoConstants::STATUS_PERIKSA_RUJUK_RANAP,
                    DocoConstants::STATUS_PERIKSA_BTL_KONSUL,
                    DocoConstants::STATUS_PERIKSA_BLM_PERIKSA,
                    DocoConstants::STATUS_PERIKSA_BLM_DIJAWAB,
                    DocoConstants::STATUS_PERIKSA_SDH_DIJAWAB,
                    DocoConstants::ST_P_PEN_AMB_SAMP,
                    DocoConstants::ST_P_PEN_BLM_PRKS,
                    DocoConstants::ST_PERIKSA,
                    DocoConstants::ST_SELESAI,
                    DocoConstants::ST_P_PEN_BTL
                ]])
                ->orderBy(['lookup_name' => SORT_ASC])
                ->all();

            $newStatusPeriksa = [];
            foreach ($statusPeriksa as $value) {
                if ($value['lookup_type'] == 'status_periksa_penunjang') {
                    $newStatusPeriksa[$value['lookup_id']] = $value['lookup_name'] . ' (Penunjang)';
                }

                if ($value['lookup_type'] == 'status_periksa') {
                    $newStatusPeriksa[$value['lookup_id']] = $value['lookup_name'];
                }
            }
            $statusPeriksa = ArrayHelper::map($statusPeriksa, 'lookup_id', 'lookup_name');

            Yii::$app->response->statusCode = 200;
            return [
                'status_periksa' => $newStatusPeriksa,
                'message' => 'Get init data success !'
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status_periksa' => [],
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetData()
    {
        $model = new DashboardIntegrasiAsuransiV();
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_pendaftaran', $date_start, $date_end]);
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            } else {
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }

            if (isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    $query->andWhere(['between', 'tgl_pulang', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
        } else {
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        }

        $mappingData = $this->getDataDashboard($model, $query);

        return new ArrayDataProvider([
            'allModels' => $mappingData,
        ]);
    }

    public function actionExportExcel()
    {
        try {
            $title = 'Informasi Dashboard Integrasi';
            $model = new DashboardIntegrasiAsuransiV();
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                    if (count($explode) == 2) {
                        $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                        $query->andWhere(['between', 'tgl_pendaftaran', $date_start, $date_end]);
                    }
                    unset($_GET['advanced-filter']['tgl_pendaftaran']);
                } else {
                    $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
                }
            } else {
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            $data = $this->getDataDashboard($model, $query);
            $newData = [];

            foreach ($data as $value) {
                $newValue['Tanggal Pendaftaran'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $newValue['No Pendaftaran'] = $value['no_pendaftaran'];
                $newValue['Nama Pasien'] = $value['nama_pasien'];
                $newValue['Alamat'] = $value['alamat_pasien'];
                $newValue['Jenis Kelamin'] = $value['jk_nama'];
                $newValue['Poliklinik'] = $value['ruangan_nama'];
                $newValue['Jenis Kasus Penyakit'] = $value['jeniskasuspenyakit_nama'];
                $newValue['Kelas Pelayanan'] = $value['kelaspelayanan_nama'];
                $newValue['Dokter DPJP'] = $value['nama_pegawai'];
                $newValue['Cara Bayar'] = $value['carabayar_nama'];
                $newValue['Penjamin'] = $value['penjamin_nama'];
                $newValue['Status Periksa'] = isset($value['status_cetakan']) ? $value['status_cetakan'] : $value['status_periksa_nama'];
                $newValue['No Klaim'] = $value['no_klaim'];
                $newValue['Petugas'] = $value['petugas'];
                $newData[] = $newValue;
            }


            $filePath = DocoHelpers::exportExcel($title, $newData, [], [], [], [], true);
            $filePath->save('php://output');
            die;
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

    protected function getDataDashboard($model, $query)
    {
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $arrayData = $query->asArray()->all();
        $newRow = [];
        $pendaftaranId = [];
        $pendaftaranIdDouble = [];
        $pendaftaranPenunjang = [];

        foreach ($arrayData as $value) {
            if (isset($value['pasienmasukpenunjang_id'])) {
                if (! in_array($value['pendaftaran_id'], $pendaftaranId)) {
                    $newRow[] = $value;
                }

                $pendaftaranPenunjang[] = [
                    'no_pendaftaran' => $value['no_pendaftaran'],
                    'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                    'no_masukpenunjang' => $value['no_masukpenunjang'],
                    'status_periksa' => $value['status_periksa'],
                    'status_periksa_nama' => $value['status_periksa_nama'],
                    'pendaftaran_id' => $value['pendaftaran_id'],
                ];
            } else {
                $newRow[] = $value;
            }

            if (in_array($value['pendaftaran_id'], $pendaftaranId)) {
                $pendaftaranIdDouble[] = $value['pendaftaran_id'];
            }

            if (! in_array($value['pendaftaran_id'], $pendaftaranId)) {
                $pendaftaranId[] = $value['pendaftaran_id'];
            }
        }

        foreach ($newRow as $key => $value) {
            $penunjangData = [];
            foreach ($pendaftaranPenunjang as $valuePenunjang) {
                if ($value['pasienmasukpenunjang_id'] && $value['pendaftaran_id'] == $valuePenunjang['pendaftaran_id']) {
                    $penunjangData[] = $valuePenunjang;
                }
            }

            if (! empty($penunjangData)) {
                $overridePendaftaran = '';
                $overrideCetakan = '';
                foreach ($penunjangData as $clearingData) {
                    $overridePendaftaran .= $clearingData['no_masukpenunjang'] . ' - ' . $clearingData['status_periksa_nama'] . '<br/>';
                    $overrideCetakan .= $clearingData['no_masukpenunjang'] . ' - ' . $clearingData['status_periksa_nama'] . ', ';
                }

                $newRow[$key]['status_periksa_nama'] = $overridePendaftaran;
                $newRow[$key]['status_cetakan'] = $overrideCetakan;
            }
        }

        return $newRow;
    }

    public function actionDetailProses()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $dataAsuransi = AsuransiT::find()
                ->select([
                    'asuransi_id',
                    'additional_data',
                    'penjamin_id',
                    'no_klaim',
                    'no_kartu',
                    'no_polis',
                    'no_sep',
                    'provider_id',
                    'is_cob'
                ])
                ->where([
                    'asuransi_id' => $request->get('asuransi_id')
                ])->one();
            
            $logData = LogAsuransiTransaksi::find()
                ->select([
                    'kode_diagnosa'
                ])
                ->where(['asuransi_id' => $dataAsuransi['asuransi_id']])
                ->one();
            
            if (isset($logData['kode_diagnosa'])) {
                $diagnosa = json_decode($logData['kode_diagnosa']);
            } else {
                $diagnosa = $this->getDiagnosa($pendaftaranId, $dataAsuransi['is_cob']);
            }

            $penjaminId = ArrayHelper::getValue($dataAsuransi, 'penjamin_id');
            $additionalData = json_decode($dataAsuransi['additional_data'], true);
            $getSisaLimit = $this->sisaLimit($penjaminId, $dataAsuransi['no_kartu'], '00022');
            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'identitas_pasien' => $dataAsuransi,
                'sisa_limit' => $getSisaLimit,
                'additionalData' => ArrayHelper::getValue($additionalData, 'data'),
                'diagnosa' => $diagnosa
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetGroupingData()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $penjaminId = $request->get('penjamin_id');
            $noKlaim = $request->get('no_klaim');

            /**
             * Kondisi gabung billing.
             */
            $gabungBilling = $this->gabungBilling($pendaftaranId);
            $condition = isset($gabungBilling) ? 'ref_pendaftaran_id' : 'pendaftaran_id';
            $isGabil = isset($gabungBilling) ? true : false;

            $model = new InfoTagihanPasienAsuransiV();
            $query = $model::find()
            ->select([
                'pendaftaran_id',
                'kelompoktindakan_id',
                'jenisobatalkes_id',
                'kelompoktindakan_nama',
                'no_pendaftaran',
                'SUM(qty) as total_qty',
                'SUM(sub_total) as total_tarif',
            ])
            ->where([$condition => $pendaftaranId])
            ->groupBy(['kelompoktindakan_id', 'kelompoktindakan_nama', 'pendaftaran_id', 'no_pendaftaran', 'jenisobatalkes_id'])
            ->orderBy(['kelompoktindakan_nama' => SORT_ASC])
            ->asArray()
            ->all();
            
            if($isGabil) {
                $numberRegistration = $this->getNumberRegistration($pendaftaranId);
            }

            $summary = $this->mapingListItem($pendaftaranId, $penjaminId, $noKlaim);
            $keyKelompok = [];
            foreach ($query as $key => $value) {
                /**
                 * Handle kondisi gabung billing.
                 */
                if ($isGabil) {
                    $query[$key]['no_pendaftaran'] = isset($numberRegistration) ? $numberRegistration->no_pendaftaran : $value['no_pendaftaran'];
                    $query[$key]['pendaftaran_id'] = $pendaftaranId;

                    if (! isset($keyKelompok[$value['kelompoktindakan_id']])) {
                        $keyKelompok[$value['kelompoktindakan_id']] = $key;
                    } else {
                        if (isset($keyKelompok[$value['kelompoktindakan_id']])) {
                            $valueKelompok = $keyKelompok[$value['kelompoktindakan_id']];
                            $query[$valueKelompok]['total_tarif'] += $value['total_tarif'];

                            /**Unset key variabel */
                            unset($query[$key]);
                            continue;
                        }
                    }
                }

                $query[$key]['sumTotal'] = ArrayHelper::getValue($summary, 'sumTotal', 0);
                $query[$key]['sumPenjamin'] = ArrayHelper::getValue($summary, 'sumPenjamin', 0);
                $query[$key]['sumDijamin'] = ArrayHelper::getValue($summary, 'sumDijamin', 0);
                $query[$key]['sumDibayarPasien'] = ArrayHelper::getValue($summary, 'sumDibayarPasien', 0);
                $query[$key]['sumInacbgs'] = ArrayHelper::getValue($summary, 'sumInacbgs', 0);
                $query[$key]['sumCob'] = ArrayHelper::getValue($summary, 'sumCob', 0);
                $query[$key]['sumSudahDikirim'] = ArrayHelper::getValue($summary, 'sumSudahDikirim', 0);
                $query[$key]['selisih'] = ArrayHelper::getValue($summary, 'selisih', 0);
                $query[$key]['isValid'] = ArrayHelper::getValue($summary, 'isValid', false);
                $query[$key]['is_pengesahan'] = ArrayHelper::getValue($summary, 'is_pengesahan', false);
                $query[$key]['is_batal'] = ArrayHelper::getValue($summary, 'is_batal', false);
                $query[$key]['is_resend'] = ArrayHelper::getValue($summary, 'is_resend', false);
                $query[$key]['is_cob'] = ArrayHelper::getValue($summary, 'is_cob', false);
            }
            Yii::$app->response->statusCode = 200;
            return new ArrayDataProvider([
                'allModels' => $query,
            ]);
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetGroupingDetail()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaranId = $request->get('pendaftaran_id');
            $kelompoktindakanId = $request->get('kelompoktindakan_id');
            $isobat = filter_var($request->get('isobat'), FILTER_VALIDATE_BOOLEAN);
            $penjaminId = $request->get('penjamin_id');
            $noKlaim = $request->get('no_klaim');

            /**
             * Kondisi gabung billing.
             */
            $gabungBilling = $this->gabungBilling($pendaftaranId);
            $condition = isset($gabungBilling) ? 'ref_pendaftaran_id' : 'pendaftaran_id';
            $isGabil = isset($gabungBilling) ? true : false;

            $model = new InfoTagihanPasienAsuransiV;
            $query = $model::find()
                ->select([
                    'pendaftaran_id',
                    'tindakan_obat_id',
                    'tindakan_obat_nama',
                    'qty',
                    'sub_total',
                    'tarif_satuan',
                    'tarif_cyto',
                    'is_cyto',
                    'no_pendaftaran',
                    'daftartindakan_kode'
                ])
                ->where([
                    $condition => $pendaftaranId,
                ]);

            if ($isobat) {
                $query->andWhere(['jenisobatalkes_id' => $kelompoktindakanId]);
            } else {
                $query->andWhere(['kelompoktindakan_id' => $kelompoktindakanId]);
            }

            $groupedItem = $this->groupedItem($model, $query, $pendaftaranId, $penjaminId, $noKlaim, $isGabil);
            $detailTindakan = ArrayHelper::getValue($groupedItem, 'detailTindakan', []);
            return new ArrayDataProvider([
                'allModels' => $detailTindakan,
            ]);
        } catch (\Exception $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    protected function sisaLimit($penjaminId, $nokartu, $kodebenefit)
    {
        $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
        $data = [
            'nokartu' => $nokartu,
            'kodebenefit' => $kodebenefit
        ];

        $response = $insuranceClient->SisaLimit($data);

        return [
            'response' => $response
        ];
    }

    public function actionGetListItemRequest($penjaminId = null, $noklaim = null)
    {
        try {
            $request = Yii::$app->request;
            if (!$penjaminId) {
                $penjaminId = $request->get('penjamin_id');
            }

            if (!$noklaim) {
                $noklaim = $request->get('no_klaim');
            }

            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

            $data = [
                'no_claim' => $noklaim,
            ];

            $response = $insuranceClient->ListItemRequest($data);

            Yii::$app->response->statusCode = 200;
            return [
                'response' => $response
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionPembatalan()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->get('penjamin_id');
            $asuransiId = $request->post('asuransi_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $data = [
                'noklaim' => $request->post('noklaim'),
                'keterangan' => $request->post('noklaim')
            ];

            $response = $insuranceClient->Pembatalan($data);

            $transaksiData = LogAsuransiTransaksi::find()->where([
                'asuransi_id' => $asuransiId,
            ])->one();

            if (empty($transaksiData)) {
                $transaksiData = new LogAsuransiTransaksi();
            }

            if (isset($response['status']) && $response['status'] === 200) {
                $transaksiData->asuransi_id = $asuransiId;
                $transaksiData->pegawaibatal_id = Yii::$app->jwt->user->loginpemakai_id;
                $transaksiData->is_batal = true;
                $transaksiData->tgl_batal = date('Y-m-d H:i:s');
                $transaksiData->save();

                IntegrasiTindakanObatAsuransi::deleteAll([
                    'asuransi_id' => $asuransiId
                ]);
            }

            Yii::$app->response->statusCode = 200;
            return [
                'response' => $response
            ];
        } catch (\Throwable $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionPengesahan()
    {
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $pendaftaranId = ArrayHelper::getValue($data, 'pendaftaran_id');
            $query = $this->getDetailPasien($pendaftaranId);
            $tanggalKeluar = ArrayHelper::getValue($query, 'tanggal_keluar');
            $kodeDiagnosa = ArrayHelper::getValue($data, 'kode_icd');
            $kodeIcdText = $request->post('kode_icd_text');
            $dataAsuransi = $this->getDataAsuransi($pendaftaranId);
            if (! isset($dataAsuransi['asuransi_id']) || empty($dataAsuransi['asuransi_id'])) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Data Asuransi Tidak ditemukan !'
                ];
            }

            if (isset($dataAsuransi['is_batal']) && $dataAsuransi['is_batal'] == true) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Data Asuransi Sudah dibatalkan !'
                ];
            }

            if (!$kodeDiagnosa) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Kode ICD harus di Isi'
                ];
            }

            $isCob = ArrayHelper::getValue($dataAsuransi, 'is_cob', false);
            $noPendaftaran = ArrayHelper::getValue($dataAsuransi, 'no_pendaftaran');
            if ($isCob) {
                $dataInacbgs = $this->getDataPasienCob($noPendaftaran);
                $totalInacbgs = ArrayHelper::getValue($dataInacbgs, 'group_tarif', '');
                $kodeInacbgs = ArrayHelper::getValue($dataInacbgs, 'cbg', '');
            }

            $penjaminId = ArrayHelper::getValue($data, 'penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);
            $postData = [
                'noklaim' => ArrayHelper::getValue($data, 'no_klaim'),
                'tanggalkeluar' => date('Y-m-d', strtotime($tanggalKeluar)),
                'kodepoli' => ArrayHelper::getValue($query, 'kode_poli'),
                'statusrujukan' => ArrayHelper::getValue($data, 'statusrujukan'),
                'norujukan' => ArrayHelper::getValue($data, 'norujukan'),
                'asalrujukan' => ArrayHelper::getValue($data, 'asalrujukan'),
                'tujuanrujukan' => ArrayHelper::getValue($data, 'tujuanrujukan'),
                'dokter' => ArrayHelper::getValue($query, 'nama_dokter'),
                'statusresep' => ArrayHelper::getValue($data, 'statusresep'),
                'izinsakit' => ArrayHelper::getValue($data, 'izinsakit', 0),
                'kodediagnosa' => $kodeDiagnosa,
                'inacbgscode' => $isCob ? $kodeInacbgs : '',
                'inacbgsamount' => $isCob ? $totalInacbgs : '',
            ];
            $response = $insuranceClient->Discharging($postData);

            if (isset($response['status']) && $response['status'] != "success") {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => isset($response['message']) ? $response['message'] : 'Gagal melakukan pengesahan !'
                ];
            }
            
            $asuransiId = ArrayHelper::getValue($dataAsuransi, 'asuransi_id');
            $transaksiKirim = LogAsuransiTransaksi::find()->where([
                'asuransi_id' => $asuransiId,
            ])->one();

            if (empty($transaksiKirim)) {
                $transaksiKirim = new LogAsuransiTransaksi();
            }

            $transaksiKirim->is_pengesahan = true;
            $transaksiKirim->asuransi_id = $asuransiId;
            $transaksiKirim->pegawaipengesahan_id = Yii::$app->jwt->user->loginpemakai_id;
            $transaksiKirim->tgl_pengesahan = date('Y-m-d H:i:s');
            $transaksiKirim->response_pengesahan = json_encode($response);
            $transaksiKirim->kode_diagnosa = json_encode([
                'kode' => $kodeDiagnosa,
                'text' => $kodeIcdText
            ]);
            $transaksiKirim->save();

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Success',
                'data' => $response,
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionResendTransaksi()
    {
        $request = Yii::$app->request;
        try {
            $pendaftaranId = $request->post('pendaftaran_id');
            $kodeIcd = $request->post('kode_icd');
            $kodeIcdText = $request->post('kode_icd_text');
            $dataAsuransi = $this->getDataAsuransi($pendaftaranId);

            if (! isset($dataAsuransi['asuransi_id']) || empty($dataAsuransi['asuransi_id'])) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Data Asuransi Tidak ditemukan !'
                ];
            }

            if (isset($dataAsuransi['is_batal']) && $dataAsuransi['is_batal'] == true) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Data Asuransi Sudah dibatalkan !'
                ];
            }

            if (! isset($kodeIcd) || empty($kodeIcd)) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Kode ICD harus di Isi dan Tidak boleh freetext !'
                ];
            }

            $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);

            /**
             * Penambahan konfigurasi alias untuk prefix code.
             */
            $getProvider = Yii::$app->assuransiClient->getProvider();
            $config = is_object($getProvider->config) ? json_decode($getProvider->config->url_pendaftaran, true) : [];

            if (! isset($config['config'])) {
                \Yii::$app->response->statusCode = 404;
                return [
                    'status' => 404,
                    'message' => 'Config Asuransi URL Pendaftaran Tidak ditemukan / Belum Lengkap !'
                ];
            }
            $prefixCodeActive = filter_var($config['config']['is_alias'], FILTER_VALIDATE_BOOLEAN);
            $prefixActive = $prefixCodeActive ? $config['config']['alias'] : null;

            /**
             * Collecting Item Request.
             */
            $collectionRequest = (new AsuransiDataSource($pendaftaranId))->collectionItemRequest($prefixActive);

            $payloadItemRequest = ArrayHelper::getValue($collectionRequest, 'payloadItemRequest');
            $deleteItemRequest = ArrayHelper::getValue($collectionRequest, 'deleteItemRequest');

            $dataPayload = [
                'no_claim' => $dataAsuransi['no_klaim'],
                'icd' => $kodeIcd,
                'items_data' => $payloadItemRequest
            ];

            $response = $insuranceClient->ItemRequest($dataPayload);

            $transaksiKirim = LogAsuransiTransaksi::find()->where([
                'asuransi_id' => $dataAsuransi['asuransi_id'],
            ])->one();

            if (empty($transaksiKirim)) {
                $transaksiKirim = new LogAsuransiTransaksi();
            }

            $this->deleteItemRequest($dataAsuransi, $deleteItemRequest);
            $this->createLogItemRequest($dataAsuransi, $payloadItemRequest);
            if (isset($response['data']) && count($response['data']) > 0) {
                $transaksiKirim->is_resend = true;
            }

            $transaksiKirim->asuransi_id = $dataAsuransi['asuransi_id'];
            $transaksiKirim->pegawairesend_id = Yii::$app->jwt->user->loginpemakai_id;
            $transaksiKirim->tgl_resend = date('Y-m-d H:i:s');
            $transaksiKirim->kode_diagnosa = json_encode([
                'kode' => $kodeIcd,
                'text' => $kodeIcdText
            ]);

            $notExistItem = [];
            $kodeItemNotSent = ArrayHelper::getValue($response, 'kode_item');
            
            if (! empty($kodeItemNotSent)) {
                $dataItem = [];
                $expKodeItem = explode(',', $kodeItemNotSent);
                if (is_array($expKodeItem)) {
                    foreach ($expKodeItem as $value) {
                        $value = str_replace($prefixActive.'-','', $value);
                        $value = str_replace(' ','', $value);
                        $dataItem[] = $value;
                    }
                }
                
                if (! empty($dataItem)) {
                    /**
                     * Kondisi gabung billing.
                     */
                    $gabungBilling = $this->gabungBilling($pendaftaranId);
                    $condition = isset($gabungBilling) ? 'ref_pendaftaran_id' : 'pendaftaran_id';

                    $notExistItem = InfoTagihanPasienAsuransiV::find()
                    ->select([
                        'daftartindakan_kode',
                        new Expression("CASE 
                            WHEN kelompoktindakan_id IS NULL THEN jenisobatalkes_id
                            ELSE kelompoktindakan_id 
                        END AS kelompoktindakan_id"),
                        'tindakan_obat_id'
                    ])
                    ->where([
                        $condition => $pendaftaranId,
                    ])
                    ->andWhere([
                        'daftartindakan_kode' => $dataItem
                    ])
                    ->asArray()->all();
                }
            }


            $transaksiKirim->save();

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Success',
                'data' => $response,
                'item_not_found' => $notExistItem
            ];
        } catch (\Exception $th) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    protected function deleteItemRequest($dataAsuransi, $deleteItemRequest)
    {
        if (! empty($deleteItemRequest)) {
            $deleteItemCode = [];
            $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);
            foreach ($deleteItemRequest as $value) {
                $deleteItemCode[] = $value['item_code'];
                $dataPayload = [
                    'no_claim' => $dataAsuransi['no_klaim'],
                    'item_code' => $value['item_code']
                ];

                $insuranceClient->DeleteItemRequest($dataPayload);
            }

            if (!empty($deleteItemCode)) {
                IntegrasiTindakanObatAsuransi::deleteAll([
                    'asuransi_id' => $dataAsuransi['asuransi_id'],
                    'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                    'item_code' => $deleteItemCode
                ]);
            }
        }
    }

    protected function createLogItemRequest($dataAsuransi, $payloadItemRequest)
    {
        $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);
        $payloadLogItem = [];

        if (!empty($payloadItemRequest)) {
            $data = [
                'no_claim' => $dataAsuransi['no_klaim'],
            ];

            $listTerkirim = $insuranceClient->ListItemRequest($data);
            $listTerkirim = ArrayHelper::getValue($listTerkirim, 'data');

            $tindakanExist = [];
            $conditionDelete = [];

            if (!empty($listTerkirim)) {
                foreach ($listTerkirim as $dataVendor) {
                    foreach ($payloadItemRequest as $requestItem) {
                        $dataPayload = [
                            'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                            'asuransi_id' => $dataAsuransi['asuransi_id'],
                            'pelayanan_id' => $requestItem['pelayanan_id'],
                            'item_code' => $requestItem['item_code'],
                            'is_sending' => false,
                            'type' => $requestItem['category'],
                            'qty' => $requestItem['qty'],
                            'subtotal' => $requestItem['price'],
                        ];

                        if ($dataVendor['item_code'] == $requestItem['item_code']) {
                            $dataPayload['is_sending'] = true;
                            $payloadLogItem[] = $dataPayload;
                            $tindakanExist[] = $requestItem['pelayanan_id'];
                        }
                    }
                }
            }

            foreach ($payloadItemRequest as $requestItem) {
                if (! in_array($requestItem['item_code'], $conditionDelete)) {
                    $conditionDelete[] = $requestItem['item_code'];
                }

                if (!in_array($requestItem['pelayanan_id'], $tindakanExist)) {
                    $dataPayload = [
                        'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                        'asuransi_id' => $dataAsuransi['asuransi_id'],
                        'pelayanan_id' => $requestItem['pelayanan_id'],
                        'item_code' => $requestItem['item_code'],
                        'is_sending' => false,
                        'type' => $requestItem['category'],
                        'qty' => $requestItem['qty'],
                        'subtotal' => $requestItem['price'],
                    ];
                    $payloadLogItem[] = $dataPayload;
                }
            }
        }

        if (!empty($payloadLogItem)) {
            IntegrasiTindakanObatAsuransi::deleteAll([
                'asuransi_id' => $dataAsuransi['asuransi_id'],
                'pendaftaran_id' => $dataAsuransi['pendaftaran_id'],
                'item_code' => $conditionDelete
            ]);

            IntegrasiTindakanObatAsuransi::batchInsert($payloadLogItem);
        }
    }

    public function actionDeleteItemRequest()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->post('pendaftaran_id');

        $dataAsuransi = Pendaftaran::find()
            ->select([
                'pendaftaran_t.pendaftaran_id',
                'asuransi_t.asuransi_id',
                'asuransi_t.penjamin_id',
                'asuransi_t.no_klaim',
                'asuransi_t.provider_id'
            ])
            ->join('JOIN', 'asuransi_t', 'asuransi_t.asuransi_id = pendaftaran_t.asuransi_id')
            ->where(['pendaftaran_id' => $pendaftaranId])
            ->asArray()
            ->one();

        $insuranceClient = Yii::$app->assuransiClient->setProvider($dataAsuransi['penjamin_id']);
        $dataPayload = [
            'no_claim' => $dataAsuransi['no_klaim'],
            'item_code' => $request->post('item_code')
        ];

        $response = $insuranceClient->DeleteItemRequest($dataPayload);
        return [
            'status' => 200,
            'message' => 'Success',
            'data' => $response
        ];
    }

    public function actionCronIntegrasiAsuransi()
    {
        $dataAsuransi = RequestItemAsuransiView::find()->asArray()->all();

        if (! empty($dataAsuransi)) {
            foreach ($dataAsuransi as $value) {
                (new RabbitBgProcess())->send([
                    'pendaftaranId' => $value['pendaftaran_id']
                ], 'sync_integrasi_asuransi_data', 'sync_integrasi_asuransi');
                usleep(1000);
            }
        }
            
        return [
            'status' => 200,
            'message' => 'Success'
        ];
    }

    private function getTindakanDetail($pendaftaranId)
    {
        try {
            $gabungBilling = $this->gabungBilling($pendaftaranId);
            $condition = isset($gabungBilling) ? 'ref_pendaftaran_id' : 'pendaftaran_id';
            $model = new InfoTagihanPasienAsuransiV;
            $query = $model::find()
            ->select([
                'pendaftaran_id',
                'tindakan_obat_id',
                'tindakan_obat_nama',
                'qty',
                'sub_total',
                'tarif_satuan',
                'tarif_cyto',
                'is_cyto',
                'no_pendaftaran',
                'daftartindakan_kode',
                'kelompoktindakan_id',
                'harga_origin'
            ])
            ->where([
                $condition => $pendaftaranId,
            ]);

            return [
                'model' => $model,
                'query' => $query,
            ];
            
        } catch (\Exception $th) {
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    private function mapingListItem($pendaftaranId, $penjaminId, $noKlaim)
    {
        $detailTindakan = $this->getTindakanDetail($pendaftaranId);
        $model = ArrayHelper::getValue($detailTindakan, 'model');
        $query = ArrayHelper::getValue($detailTindakan, 'query');

        $data = $this->groupedItem($model,$query,$pendaftaranId, $penjaminId, $noKlaim);
        $dataAsuransi = $this->getDataAsuransi($pendaftaranId);
        $asuransiId = ArrayHelper::getValue($dataAsuransi, 'asuransi_id');
        $cekPengesahan = LogAsuransiTransaksi::find()->where(['asuransi_id' => $asuransiId])->one();
        $isPengesahan = ArrayHelper::getValue($cekPengesahan, 'is_pengesahan', false);
        $responsePengesahan = ArrayHelper::getValue($cekPengesahan, 'response_pengesahan');
        $isBatal = ArrayHelper::getValue($cekPengesahan, 'is_batal', false);
        $isResend = ArrayHelper::getValue($cekPengesahan, 'is_resend', false);
        $isCob = ArrayHelper::getValue($dataAsuransi, 'is_cob', false);
        $sumTotal = ArrayHelper::getValue($data, 'sumTotal', 0);
        $sumDijamin = ArrayHelper::getValue($data, 'sumDijamin', 0);
        $sumDibayarPasien = ArrayHelper::getValue($data, 'sumDibayarPasien', 0);
        $sumSudahDikirim = ArrayHelper::getValue($data, 'sumSudahDikirim', 0);
        $sumCob = 0;
    
        /**
         * Total Acc Cob muncul setelah pengesahan.
         */
        if ($isPengesahan && $isCob) {
            $dataPengesahan = json_decode($responsePengesahan, true);
            if (isset($dataPengesahan['data']['cover'])) {
                $dataCover = $dataPengesahan['data']['cover'];
                foreach ($dataCover as $value) {
                    $sumCob += $value['biayaaju'];
                }
            }
        }
        $sumInacbgs = 0;
        
        if ($isCob) {
            $noPendaftaran = ArrayHelper::getValue($dataAsuransi, 'no_pendaftaran');
            $dataInacbgs = $this->getDataPasienCob($noPendaftaran);
            $sumInacbgs = ArrayHelper::getValue($dataInacbgs, 'group_tarif', 0);
            $sumDibayarPasien = $sumTotal - $sumInacbgs - $sumCob;
            if ($sumDibayarPasien < 0) {
                $sumDibayarPasien = 0;
            }
        }

        return [
            'sumTotal' => $sumTotal,
            'sumPenjamin' => ArrayHelper::getValue($data, 'sumTotal', 0),
            'sumDijamin' => $sumDijamin,
            'sumDibayarPasien' => $sumDibayarPasien,
            'sumInacbgs' => $sumInacbgs,
            'sumSudahDikirim' => $sumSudahDikirim,
            'sumCob' => $sumCob,
            'selisih' => ArrayHelper::getValue($data, 'selisih', 0),
            'isValid' => ArrayHelper::getValue($data, 'isValid', false),
            'is_pengesahan' => $isPengesahan,
            'is_batal' => $isBatal,
            'is_resend' => $isResend,
            'is_cob' => $isCob
        ];
    }

    private function groupedItem($model,$query,$pendaftaranId,$penjaminId,$noKlaim, $isGabil = false)
    {
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $detailTindakan = $query->asArray()->all();
        $dataListGrouping = $dataListItem = [];
        $listItemRequest = $this->actionGetListItemRequest($penjaminId, $noKlaim);
        $listItemRequest = ArrayHelper::getValue($listItemRequest, 'response.data');
        $prefix = $this->getPrefix($penjaminId);
        $sumSudahDikirim = 0;
        if($listItemRequest) {
            foreach ($listItemRequest as $key => $value) {
                $itemCode = ArrayHelper::getValue($value, 'item_code');
                $sumSudahDikirim += ArrayHelper::getValue($value, 'total_amount', 0);
                $dataListGrouping[$itemCode] = [
                    'note_admin' => ArrayHelper::getValue($value, 'note_admin'),
                    'status_approve' => ArrayHelper::getValue($value, 'status_approve'),
                    'total_amount' => ArrayHelper::getValue($value, 'total_amount'),
                ];
            }
        }
        
        $selisih = $sumTotal = $sumPenjamin = $sumDijamin = $sumDibayarPasien = 0;
        $isValid = true;

        if($isGabil) {
            $numberRegistration = $this->getNumberRegistration($pendaftaranId);
        }
        
        if($detailTindakan) {
            foreach ($detailTindakan as $key => $value) {
                $selisihHarga = 0;
                $subTotal = ArrayHelper::getValue($value, 'sub_total', 0);
                $hargaOrigin = ArrayHelper::getValue($value, 'harga_origin');
                $qty = ArrayHelper::getValue($value, 'qty', 1);
                if($hargaOrigin) {
                    $selisihHarga = $subTotal - $hargaOrigin;
                }
                
                $sumTotal += $subTotal;

                $daftarTindakanKode = ArrayHelper::getValue($value, 'daftartindakan_kode');
                $daftarTindakanKode = $prefix ? $prefix.'-'.$daftarTindakanKode : $daftarTindakanKode;
                $groupKodeTindakan = ArrayHelper::getValue($dataListGrouping, $daftarTindakanKode, []);
                $totalAmount = ArrayHelper::getValue($groupKodeTindakan, 'total_amount', 0);

                /**
                 * Handle kondisi gabung billing.
                 */
                if ($isGabil) {
                    $detailTindakan[$key]['no_pendaftaran'] = isset($numberRegistration) ? $numberRegistration->no_pendaftaran : $value['no_pendaftaran'];
                    $detailTindakan[$key]['pendaftaran_id'] = $pendaftaranId;
                }

                if(!isset($groupKodeTindakan['note_admin'])) {
                    $selisih += $subTotal;
                    $detailTindakan[$key]['note_admin'] = 'Not Sent';
                    $detailTindakan[$key]['status_approve'] = 0;
                    $isValid = false;
                }
                else {
                    $noteAdmin = ArrayHelper::getValue($groupKodeTindakan, 'note_admin');
                    $statusApprove = ArrayHelper::getValue($groupKodeTindakan, 'status_approve');
                    $detailTindakan[$key]['note_admin'] = $noteAdmin;
                    $detailTindakan[$key]['status_approve'] = $statusApprove;

                    if($statusApprove == 1) {
                        $sumDijamin += $subTotal;
                    }
                    else {
                        if(is_null($statusApprove) && !empty($noteAdmin)) {
                            $sumDibayarPasien += $subTotal;
                            $isValid = false;
                        } else {
                            if ($statusApprove == 0) {
                                $sumDibayarPasien += $subTotal;
                            }
                        }

                        if ($totalAmount) {
                            $selisih += $subTotal - $totalAmount;
                        }
                    }
                }
            }
        }

        return [
            'detailTindakan' => $detailTindakan,
            'sumTotal' => $sumTotal,
            'sumPenjamin' => $sumTotal,
            'sumDijamin' => $sumDijamin,
            'sumDibayarPasien' => $sumDibayarPasien,
            'sumSudahDikirim' => $sumSudahDikirim,
            'selisih' => $selisih,
            'isValid' => $isValid
        ];
    }

    private function getDetailPasien($pendaftaranId)
    {
        $data = Pendaftaran::find()
            ->select(['pt3.tglpasienpulang', 'pendaftaran_t.tgl_stopakomodasi', 'pendaftaran_t.tgl_pendaftaran', 
                    'ruangan_ranap.kode_ruanganpoli AS ruangan_ranap', 
                    'ruangan_rajal.kode_ruanganpoli AS ruangan_rajal', 
                    'dokter_rajal.nama_pegawai as dokter_rajal', 
                'dokter_ranap.nama_pegawai as dokter_ranap'
            ])
            ->leftJoin('pasienadmisi_t pt2', 'pendaftaran_t.pendaftaran_id = pt2.pendaftaran_id')
            ->leftJoin('pasienpulang_t pt3', 'pendaftaran_t.pendaftaran_id = pt3.pendaftaran_id')
            ->leftJoin('ruangan_m ruangan_rajal', 'pendaftaran_t.ruangan_id = ruangan_rajal.ruangan_id')
            ->leftJoin('ruangan_m ruangan_ranap', 'pt2.ruangan_id = ruangan_ranap.ruangan_id')
            ->leftJoin('pegawai_m dokter_rajal', 'pendaftaran_t.pegawai_id = dokter_rajal.pegawai_id')
            ->leftJoin('pegawai_m dokter_ranap', 'pt2.pegawai_id = dokter_ranap.pegawai_id')
            ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaranId])->asArray()->one();
        
        $tglStopAkomodasi =  ArrayHelper::getValue($data, 'tgl_stopakomodasi');
        $tglPasienPulang =  ArrayHelper::getValue($data, 'tglpasienpulang');
        $ruanganRajal = ArrayHelper::getValue($data, 'ruangan_rajal');
        $ruanganRanap = ArrayHelper::getValue($data, 'ruangan_ranap');
        $dokterRajal = ArrayHelper::getValue($data, 'dokter_rajal');
        $dokterRanap = ArrayHelper::getValue($data, 'dokter_ranap');

        $tanggalKeluar = $tglStopAkomodasi ? $tglStopAkomodasi : $tglPasienPulang;
        if(!$tanggalKeluar) {
            $tanggalKeluar = ArrayHelper::getValue($data, 'tgl_pendaftaran');
        }

        return [
            'tanggal_keluar' => $tanggalKeluar,
            'kode_poli' => $ruanganRanap ? $ruanganRanap : $ruanganRajal,
            'nama_dokter' => $dokterRanap ? $dokterRanap : $dokterRajal,
        ];
    }

    private function getDiagnosa($pendaftaranId, $isCob = false)
    {
        $icdX = [];
        if (! $isCob) {
            $resumeMedis = ResumeMedisRi::find()->select([
                'diag_utama',
                'diag_awal'
            ])->where([
                'pendaftaran_id' => $pendaftaranId
            ])->one();

        $diganosaUtama = ArrayHelper::getValue($resumeMedis, 'diag_utama');
        $diganosaUtama = ArrayHelper::getValue($resumeMedis, 'diag_utama');
        $diganosaAwal = ArrayHelper::getValue($resumeMedis, 'diag_awal');
            $diganosaUtama = ArrayHelper::getValue($resumeMedis, 'diag_utama');
        $diganosaAwal = ArrayHelper::getValue($resumeMedis, 'diag_awal');
            $icdX = null;

            if (! is_array($diganosaUtama)) {
                $diganosaUtama = json_decode($diganosaUtama, true);
            }

            if (isset($diganosaUtama['text']) && isset($diganosaUtama['kode']) && $diganosaUtama['text'] != '' && $diganosaUtama['kode'] != '') {
                $icdX = [
                    'kode' => ArrayHelper::getValue($diganosaUtama, 'kode'),
                    'text' => ArrayHelper::getValue($diganosaUtama, 'text')
                ];
            } else {
                $icdX = [
                    'kode' => ArrayHelper::getValue($diganosaUtama, 'kode'),
                    'text' => ArrayHelper::getValue($diganosaUtama, 'text')
                ];
            }
        } else {
            $dataAsuransi = $this->getDataAsuransi($pendaftaranId);
            $noPendaftaran = ArrayHelper::getValue($dataAsuransi, 'no_pendaftaran');

            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'sy_koreksidiagnosa.diagnosa_id'
                ])
                ->JOIN("JOIN", "sy_koreksidiagnosa", 'sy_kunjungan.kunjungan_id = sy_koreksidiagnosa.kunjungan_id AND sy_koreksidiagnosa.is_icdprimer IS TRUE')
                ->where([
                    'sy_kunjungan.no_pendaftaran' => $noPendaftaran,
                    'sy_kunjungan.status_kunjungan' => DocoConstants::STATUS_FINAL_KLAIM
                ])->asArray()->one();
            
            $diagnosaId = ArrayHelper::getValue($dataKunjungan, 'diagnosa_id');
            $dataDiagnosa = DiagnosaView::find()->where(['diagnosa_id' => $diagnosaId])->one();
            
            $icdX = [
                'kode' => ArrayHelper::getValue($dataDiagnosa, 'diagnosa_kode'),
                'text' => ArrayHelper::getValue($dataDiagnosa, 'diagnosa_kode') . ' - ' . ArrayHelper::getValue($dataDiagnosa, 'diagnosa_nama')
            ];
        }

        return $icdX;
    }

    private function getPrefix($penjaminId)
    {
        $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

        /**
         * Penambahan konfigurasi alias untuk prefix code.
         */
        $getProvider = Yii::$app->assuransiClient->getProvider();
        $config = is_object($getProvider->config) ? json_decode($getProvider->config->url_pendaftaran, true) : [];

        if (! isset($config['config'])) {
            \Yii::$app->response->statusCode = 404;
            return [
                'status' => 404,
                'message' => 'Config Asuransi URL Pendaftaran Tidak ditemukan / Belum Lengkap !'
            ];
        }

        $prefixCodeActive = filter_var($config['config']['is_alias'], FILTER_VALIDATE_BOOLEAN);
        $prefixActive = $prefixCodeActive ? $config['config']['alias'] : null;

        return $prefixActive;
    }

    private function getDataAsuransi($pendaftaranId)
    {
        return Pendaftaran::find()
                ->select([
                    'pendaftaran_t.no_pendaftaran',
                    'pendaftaran_t.pendaftaran_id',
                    'asuransi_t.asuransi_id',
                    'asuransi_t.penjamin_id',
                    'asuransi_t.no_klaim',
                    'asuransi_t.provider_id',
                    'asuransi_t.is_cob',
                    'log_asuransitransaksi_t.is_batal',
                    'log_asuransitransaksi_t.is_pengesahan',
                    'log_asuransitransaksi_t.is_resend'
                ])
                ->join('JOIN', 'asuransi_t', 'asuransi_t.asuransi_id = pendaftaran_t.asuransi_id')
                ->join('LEFT JOIN', 'log_asuransitransaksi_t', 'log_asuransitransaksi_t.asuransi_id = asuransi_t.asuransi_id')
                ->where(['pendaftaran_id' => $pendaftaranId])
                ->asArray()
                ->one();
    }

    /**
     * Search data for patient COB.
     */
    public function actionSearchListPasienCob()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $listPatient = $this->getDataPasienCob($term, true);

            \Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Success',
                'data' => $listPatient,
                'term' => $term
            ];
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionSearchPasienCob()
    {
        try {
            $request = Yii::$app->request;
            $noPendaftaran = $request->post('no_pendaftaran');

            $dataKunjungan = $this->getDataPasienCob($noPendaftaran);

            $dataInvoice = InvoiceSudahBayarView::find()
                ->select([
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'total_dijamin'
                ])
                ->where([
                    'no_pendaftaran' => $noPendaftaran
                ])->one();

            $nosep = ArrayHelper::getValue($dataKunjungan, 'nosep');
            $dataInacbgs = [
                'status_kunjungan' => ArrayHelper::getValue($dataKunjungan, 'status_kunjungan'),
                'status_kunjungan_nama' => DocoConstants::$status_verif[ArrayHelper::getValue($dataKunjungan, 'status_kunjungan')],
                'nosep' => $nosep,
                'group_nama' => ArrayHelper::getValue($dataKunjungan, 'group_nama'),
                'group_tarif' => ArrayHelper::getValue($dataKunjungan, 'group_tarif'),
                'cbg' => ArrayHelper::getValue($dataKunjungan, 'cbg'),
                'diagnosa_primer' => ArrayHelper::getValue($dataKunjungan, 'diagnosa_primer'),
            ];

            \Yii::$app->response->statusCode = 200;
            return [
                'status' => 200,
                'message' => 'Success',
                'data' => $dataKunjungan,
                'invoice' => $dataInvoice,
                'dataInacbgs' => $dataInacbgs
            ];
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionPendaftaranCob()
    {
        try {
            $request = Yii::$app->request;
            $penjaminId = $request->post('penjamin_id');
            $insuranceClient = Yii::$app->assuransiClient->setProvider($penjaminId);

            $pendaftaran = Pendaftaran::find()->where([
                'no_pendaftaran' => $request->post('no_pendaftaran')
            ])->one();

            /**
             * Check dia ada pendaftaran atau tidak.
             */
            if (isset($pendaftaran)) {
                if ($pendaftaran->asuransi_id) {
                    $asuransi = AsuransiT::find()
                        ->select([
                            'asuransi_t.*',
                            'log_asuransitransaksi_t.is_batal',
                            'log_asuransitransaksi_t.is_pengesahan',
                            'log_asuransitransaksi_t.is_resend'
                        ])->JOIN('JOIN', 'log_asuransitransaksi_t', 'log_asuransitransaksi_t.asuransi_id = asuransi_t.asuransi_id')
                        ->where([
                            'asuransi_t.asuransi_id' => $pendaftaran->asuransi_id
                        ])
                        ->asArray()
                        ->one();

                    if (! empty($asuransi)) {
                        if ($asuransi['is_pengesahan'] == false && $asuransi['is_batal'] == false) {
                            $pasien = Pasien::find()->select(['pasien_id', 'nama_pasien'])->where([
                                'pasien_id' => $pendaftaran->pasien_id
                            ])->one();

                            \Yii::$app->response->statusCode = 422;
                            return [
                                'status' => 422,
                                'title' => "Proses Gagal !",
                                'message' => 'Nomor Pendaftaran ['.$pendaftaran->no_pendaftaran.' - '.$pasien->nama_pasien.'] sudah terdaftar di MQare. Silahkan Cek Kembali Data Pendaftaran Yang Akan Digunakan',
                            ];
                        }
                    }
                }
            }

            $payloadRequest = [
                "tanggalmasuk" => date('Y-m-d', strtotime($request->post('tanggalmasuk'))),
                "nokartu" => $request->post('nokartu'),
                "kodebenefit" => $request->post('kodebenefit'),
                "statusrujukan" => $request->post('statusrujukan'),
                "asalrujukan" => "",
                "cobbpjs" => $request->post('cobbpjs'),
                "nomorsep" => $request->post('nomorsep'),
                "keterangan" => $request->post('keterangan'),
                "notransaksiprovider" => $request->post('notransaksiprovider'),
                "inacbgscode" => $request->post('inacbgscode'),
                "inacbgsamount" => $request->post('inacbgsamount')
            ];

            $response = $insuranceClient->Pendaftaran($payloadRequest);

            if (isset($response['data']['dataPeserta']) && $response['status'] == 0) {
                $getProvider = Yii::$app->assuransiClient->getProvider();
                $dataPeserta = ArrayHelper::getValue($response, 'data.dataPeserta', []);
                $providerId = ArrayHelper::getValue($getProvider, 'config.attribute.provider_id', null);

                $newModel = new AsuransiT();
                $newModel->provider_id = $providerId;
                $newModel->penjamin_id = $penjaminId;
                $newModel->no_klaim = ArrayHelper::getValue($dataPeserta, 'noklaim');
                $newModel->no_sep = $request->post('nomorsep');
                $newModel->no_kartu = $request->post('nokartu');
                $newModel->no_polis = ArrayHelper::getValue($dataPeserta, 'nopolis');
                $newModel->additional_data = json_encode($response);
                $newModel->additional_pendaftaran = json_encode($pendaftaran->attributes);
                $newModel->is_cob = true;
                $newModel->benefit_id = $request->post('kodebenefit');
                $newModel->save();

                $pendaftaran->asuransi_id = $newModel->asuransi_id;
                $pendaftaran->save();
                
                (new RabbitBgProcess())->send([
                    'pendaftaranId' => $pendaftaran->pendaftaran_id
                ], 'sync_integrasi_asuransi_data', 'sync_integrasi_asuransi');

                \Yii::$app->response->statusCode = 200;
                return [
                    'status' => 200,
                    'message' => 'Pendaftaran COB Berhasil !',
                    'data' => $dataPeserta
                ];
            }
            
            \Yii::$app->response->statusCode = 422;
            Yii::error([
                'status' => 422,
                'message' => 'Pendaftaran Gagal !',
                'response' => $response
            ]);
            
            return [
                'status' => 422,
                'message' => 'Pendaftaran Gagal !',
                'response' => $response,
            ];
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    private function getDataPasienCob($term, $isList = false)
    {
        $listPatient = SyKunjunganPasien::find()
            ->select([
                'sy_kunjungan.kunjungan_id',
                'sy_kunjungan.no_pendaftaran',
                'sy_kunjungan.no_rekammedik',
                'sy_kunjungan.nama_pasien',
                'sy_kunjungan.jenis_kelamin',
                'sy_kunjungan.tgl_lahir',
                'sy_kunjungan.umur',
                'sy_kunjungan.tgl_pendaftaran',
                'sy_kunjungan.tgl_pulang',
                'sy_kunjungan.instalasi_id',
                'sy_kunjungan.instalasi_nama',
                'sy_kunjungan.ruangan_id',
                'sy_kunjungan.carabayar_nama',
                'sy_kunjungan.penjamin_nama',
                'sy_kunjungan.dokter_nama',
                'sy_kunjungan.status_kunjungan',
                'sy_kunjungan.pasien_id',
                'sy_kunjungan.nosep',
                'sy_klaiminacbg.klaimgroup_id',
                'sy_klaiminacbg.diagnosa_primer',
                'sy_klaiminacbg.diagnosa_sekunder',
                'sy_klaimgroup_t.group_nama',
                'sy_klaimgroup_t.total',
                'sy_klaimgroup_t.cbg',
                'sy_klaimgroup_t.group_tarif',
            ])
            ->JOIN('LEFT JOIN', 'sy_klaiminacbg', 'sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id AND sy_klaiminacbg.is_deleted = false AND sy_klaiminacbg.is_active = true')
            ->JOIN('LEFT JOIN', 'sy_klaimgroup_t', 'sy_klaimgroup_t.sy_klaimgroup_id = sy_klaiminacbg.klaimgroup_id');
            
        if ($isList) {
            $query = $listPatient->andWhere([
                'or',
                ['LIKE', 'nosep', $term],
                ['LIKE', 'no_pendaftaran', $term]
            ])
                ->limit(50)
                ->asArray()
                ->all();
        } else {
            $query = $listPatient->andWhere([
                'no_pendaftaran' => $term
            ])
                ->asArray()
                ->one();
        }

        return $query;
    }

    private function gabungBilling($pendaftaranId)
    {
        if(empty($pendaftaranId)) {
            return [];
        }

        $queryCode = Yii::$app->db->createCommand("
            SELECT ref_pendaftaran_id, pendaftaran_id FROM gabungpelayanandetail_t WHERE pendaftaran_id = {$pendaftaranId} OR ref_pendaftaran_id = {$pendaftaranId} AND is_deleted = FALSE
        ")->queryOne();

        if (empty($queryCode)) {
            return null;
        }

        return isset($queryCode['pendaftaran_id']) ? $queryCode['pendaftaran_id'] : null;
    }

    private function getNumberRegistration($pendaftaranId)
    {
        if(empty($pendaftaranId)) {
            return null;
        }

        return Pendaftaran::find()->select([
            'no_pendaftaran'
        ])->where([
            'pendaftaran_id' => $pendaftaranId
        ])->one();
    }
}
