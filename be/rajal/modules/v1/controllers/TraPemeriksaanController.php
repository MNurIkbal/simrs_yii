<?php
/*
 * @Author: metafiliana
 * @Date: 2018-01-29 10:13:50
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-14 17:56:59
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-05-21 11:27:11
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-08-10 16:23:25
 * @Last Modified time: 2018-09-03 15:06:33
 * @Last Modified by: rizal
 * @Last Modified time: 2019-02-13 13:11:00
 * @Description: change get list pasien asalnya tidak filter ruangan
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use \DateTime;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\models\DocMapping;
use Doco\models\bpjs\Bpjs as BpjsCore;
use Doco\models\AntrianKonsul;
use Doco\models\pendaftaran\PendaftaranOnline;
use Doco\models\antrian\KonfigAntrian;
use Doco\models\LookupTransaksi;
use Doco\models\TerapiRjView;
use app\modules\v1\models\InfoRiwayatPasienView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\PasienDirujukKeluar;
use app\modules\v1\models\PasienMorbiditas;
use app\modules\v1\models\PembebasanTarif;
use app\modules\v1\models\Konsulpoli;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\BuatJanjiPoli;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\KonfigAntrianFarmasi;
use app\modules\v1\models\InfoReseptur;
use app\modules\v1\models\InfoResepturDetail;
use app\modules\v1\models\AntrianView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\InfoTagihanPasien;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\RiwayatPemeriksaanFisik;
use app\modules\v1\models\ResepTemp;
use app\modules\v1\models\ResepTempDetail;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\DiagnosaRuanganView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienDiagnosa;
use app\modules\v1\models\RiwayatPenunjangDetailView;
use app\modules\v1\models\RujukBalik;
use app\modules\v1\models\ResumeMedisRi;

use app\modules\v1\models\PemeriksaanFisio;
use app\modules\v1\models\OrderPenunjangView;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\Barang;
use app\modules\v1\models\InfoDataPendaftaran;
use app\modules\v1\models\JadwalBukaPoliklinik;
use app\modules\v1\models\Infokonsulpoli;
use app\modules\v1\controllers\AllowController;
use app\modules\v1\payload\ParamModel;
use app\modules\v1\models\Lantai;
use app\modules\v1\models\InfoTagihanDetailView;
use app\modules\v1\models\Suku;
use app\modules\v1\models\ProgramTerapi;
use app\modules\v1\models\ProgramTerapiDetail;
use app\modules\v1\Traits\AsesmenMedisTrait;
use Doco\Services\KasirService;
use app\modules\v1\models\RiwayatSoapTerra;
use app\modules\v1\models\RiwayatResepTerra;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\SoapRj;
use app\modules\v1\models\PermintaanMakan;
use Doco\Traits\GiziTrait;
use app\modules\v1\models\RujukanPulang;
use app\modules\v1\models\WorklistResepView;
use app\modules\v1\models\AnamnesaDetail;
use Doco\Traits\GeneralResepturTrait;
use app\modules\v1\Traits\RujukBalikTrait;
use Doco\Traits\TerraMedikTrait;
use app\modules\v1\models\SoapRjView;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\KonfigSystem;
use Doco\Traits\PasienTrait;
use Doco\Traits\SuratKeteranganTrait;
use Doco\Traits\ICareTrait;

use Doco\Services\FarmasiService;
use Doco\Repositories\LookUpTransaksiRepositories;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\actions\TraPemeriksaan\OrderRajalAction;
use app\modules\v1\actions\TraPemeriksaan\ProgramFisioRajalAction;
use app\modules\v1\models\JadwalDokter;
use Doco\components\PelayananHelpers;
use Doco\Services\Cache;
use Doco\Services\UpdateEklaimService;
use SirsCore\businessLogic\MonitoringTtvLogic;
use Doco\Traits\ObservasiEwsTrait;
use Doco\Traits\SbarTrait;

class TraPemeriksaanController extends DocoActiveController
{
    use AsesmenMedisTrait;
    use GiziTrait;
    use GeneralResepturTrait;
    use RujukBalikTrait;
    use TerraMedikTrait;
    use PasienTrait;
    use SuratKeteranganTrait;
    use ICareTrait;
    use ObservasiEwsTrait;
    use SbarTrait;
    public $modelClass = 'app\modules\v1\models\InfoRiwayatPasienView';
    const BATAL_RESEPTUR = 432;

    protected $type;
    protected $cpptModel;
    protected $askepModel;
    protected $asmedModel;
    public $konfigCpptKosong;

    public $messageBroker = [
        'pemulangan-pasien' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => '5',
                        'isPendingTask' => true,
                        'update_from' => 'pemulangan'
                    ],
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'pemulangan_pasien'
                    ]
                ],
                'Satusehat' => [
                    'EncounterFinish' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'update'
                    ]
                ],
                // Dipindahkan menjadi endpoint fisio per sprint 27 Mei 2022
                // Dicomment bila diperlukan tinggal diaktifkan.
                // 'Fisioterapi' => [
                //     'UpdateStatusProgramClose' => [
                //         'payload' => [
                //             'pasien_id' => 'modelpulangpasien.pasien_id',
                //             'pendaftaran_id' => 'modelpulangpasien.pendaftaran_id',
                //             'carakeluar_id' => 'modelpulangpasien.carakeluar_id'
                //         ]
                //     ]
                // ]
            ]
        ],
        'save-diet-pasien' => [
            'services' => [
                'SatuSehat' => [
                    'Composition' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ],
        'create-reseptur' => [
            'services' =>[
                'SatuSehat' => [
                    'Medication' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                    'MedicationRequest' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                    'MedicationDispense' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
                'Sirs' => [
                    'AddAntrianFarmasiJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                    ]
                ]
            ]
        ],
        'create-terapi-penunjang' => [
            'services' => [
                'SatuSehat' => [
                    'ServiceRequest' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ]
    ];
    public function verbs()
    {
        $verbs = parent::verbs();

        $verbs["get-pasien"] = ["GET"];
        $verbs["proses-anamnesa"] = ["GET", "POST"];
        $verbs["export-pdf-periksa-fisik"] = ["GET", "POST"];
        $verbs["get-pemeriksaan-fisik"] = ["GET", "POST"];
        $verbs["update-pegawai"] = ["GET", "POST"];

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    public function init()
    {
        parent::init();
        $this->type = 'RJ';
        $this->cpptModel = (new SoapRj);
        $this->asmedModel = (new PemeriksaanFisik);
        $this->askepModel = (new Anamnesa);

        $this->konfigSystemCppt();
    }

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
    }

    private function getDefaultData()
    {
        $jwt = Yii::$app->jwt->user;
        return (object)array(
            'date' => date('Y-m-d H:i:s', time()),
            'by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1',
        );
    }
    /**
     *
     * @see Fungsi get data inforiwayatpasien_v
     * @var params integer pendaftaran_id = pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    public function actionGetInfoKunjunganRajal()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $getDiagnosa = PasienMorbiditas::find()->select([
            'diagnosa_pasien'
        ])->where([
            'pendaftaran_id' => $pendaftaran_id,
            'kelompokdiagnosa_id' => DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA
        ])->asArray()->one();
        $diagnosa = isset($getDiagnosa['diagnosa_pasien']) ? $getDiagnosa['diagnosa_pasien'] : null;
        $result = InfoKunjunganRajal::find();
        if ($pendaftaran_id) {
            $result->where(['pendaftaran_id' => $pendaftaran_id]);
        }
        $result = $result->asArray()->one();
        $result['diagnosa_pasien'] = $diagnosa;
        return $result;
    }

    /**
     *
     * @see Fungsi get data inforiwayatpasien_v
     * @var params integer pendaftaran_id = pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    public function actionGetInfoStatusRiwayat()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $result = InfoRiwayatPasienView::find();
        if ($pendaftaran_id) {
            $result->where(['pendaftaran_id' => $pendaftaran_id]);
        }
        return $result->asArray()->one();
    }

    private function dataStatusPeriksa()
    {
        return [
            DocoConstants::STATUS_ANTRIAN_POLI,
            DocoConstants::STATUS_PERIKSA,
            DocoConstants::STATUS_PULANG,
            DocoConstants::STATUS_BATAL_PERIKSA,
            // DocoConstants::STATUS_RUJUK_RAWAT_INAP,
        ];
    }

    /*===================================
    =            List pasien            =
    ===================================*/
    /**
     *
     * @see Fungsi get list data pasien
     * @return array
     *
     */
    public function queryListPasien($request, $query)
    {

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $orderby = ['status_periksa' => SORT_ASC, 'tgl_pendaftaran' => SORT_ASC];
        $beginOfDay = strtotime("2018-01-01 00:00:00");
        $endOfDay = "2018-08-10 23:59:59";
        // $tgl_awal = date('Y-m-d 00:00:00', strtotime($beginOfDay));
        // $tgl_akhir = date('Y-m-d 23:59:59', strtotime($endOfDay));
        $advancedFilters = $request->get('advanced-filter', []);

        if (isset($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                unset($advancedFilters['tgl_pendaftaran']);
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

            if (isset($advancedFilters['kelompok_medis'])) {
                if ($advancedFilters['kelompok_medis'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER) {
                    $orderby = ['tgl_pendaftaran' => SORT_DESC];
                }

                if (isset($advancedFilters['pegawai_id'])) {
                    $query->andWhere(['pegawai_id' => $advancedFilters['pegawai_id']]);
                }
                $query->where(['not in', 'status_periksa', [DocoConstants::STATUS_ANTRIAN_POLI]]);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        $query->andWhere(['in', 'status_periksa', $this->dataStatusPeriksa()]);

        $query->AndWhere([
            'not', ['no_antrian' => null],
        ]);

        return $query;
    }

    public function actionListPasien()
    {
        try {
            $pegawai_id = Yii::$app->jwt->user->pegawai_id;
            $request = Yii::$app->request;
            $model = new InfoKunjunganRajal;
            $query = InfoKunjunganRajal::find();
            $orderby = ['status_periksa' => SORT_ASC, 'tgl_pendaftaran' => SORT_ASC];
            // $beginOfDay = strtotime("2018-01-01 00:00:00");
            // $endOfDay = "2018-08-10 23:59:59";
            // $tgl_awal = date('Y-m-d 00:00:00', strtotime($beginOfDay));
            // $tgl_akhir = date('Y-m-d 23:59:59', strtotime($endOfDay));
            $advancedFilters = $request->get('advanced-filter', []);
            $isDokterUmum = $request->get('dokter_umum', false);
            if (isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                    $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                    $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                    unset($advancedFilters['tgl_pendaftaran']);
                }

                if (isset($advancedFilters['no_antrian'])) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_antrian)', strtolower($advancedFilters['no_antrian'])]);
                }

                if (isset($advancedFilters['no_pendaftaran'])) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(no_pendaftaran)', strtolower($advancedFilters['no_pendaftaran'])]);
                }
            }

            $queries = $this->queryListPasien($request, $query);

            $query = DocoRestActiveFilter::advancedFilter($model, $queries);

            // modified by rizal
            $ruangan_id = $request->get('ruangan_id', null);
            $kelompokpegawai_id = $request->get('kelompokpegawai_id', null);
            if ($kelompokpegawai_id != DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
                if ($ruangan_id) {
                    $query->andWhere([
                        'ruangan_id' => $ruangan_id
                    ]);
                }
                if (!$isDokterUmum) {
                    $query->andWhere([
                        'pegawai_id' => $pegawai_id
                    ]);
                }
            }

            $query->orderby($orderby);

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

    public function actionExportPdf($jenis, $ruangan)
    {
        $data = [];
        $jenis_title = 'Informasi Pasien Rawat Jalan';
        $ruangan_id = $_GET['idruangan'];

        $model = new InfoKunjunganRajal;
        $query = InfoKunjunganRajal::find()->where(['ruangan_id' => $ruangan]);

        $orderby = ['status_periksa' => SORT_ASC, 'tgl_pendaftaran' => SORT_ASC];
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $dokter = DokterView::find()->andWhere(['pegawai_id' => $pegawai_id])->one();

        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');


        $dataKepala = (PegawaiView::find()->where(['ruangan_id' => $ruangan, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id' => $ruangan, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one() : '';

        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
        }
        $queries = $this->queryListPasien($request, $query);

        $resQuery = DocoRestActiveFilter::advancedFilter($model, $queries);
        $resQuery->orderby($orderby);
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $ruangan_id = $request->get('ruangan_id', null);
        $data = [];
        $title = 'Informasi Pasien Rawat Jalan';

        $model = new InfoKunjunganRajal;
        $query = InfoKunjunganRajal::find()->where(['ruangan_id' => $ruangan_id]);

        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        if (isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
        }

        $orderby = ['status_periksa' => SORT_ASC, 'tgl_pendaftaran' => SORT_ASC];
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $dokter = DokterView::find()->andWhere(['pegawai_id' => $pegawai_id])->one();


        $dataKepala = (PegawaiView::find()->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one() : '';


        $queries = $this->queryListPasien($request, $query);

        $resQuery = DocoRestActiveFilter::advancedFilter($model, $queries);
        $resQuery->orderby($orderby);
        $data = $resQuery->asArray()->all();
        $result = [];
        $counter = 0;
        foreach ($data as $key => $value) {
            $counter++;
            $dataValue['No Antrian'] = $value['no_antrian'];
            $dataValue['Tanggal Pendaftaran'] =  date('d F Y H:i:s', strtotime($value['tgl_pendaftaran']));
            $dataValue['Nomor Rekam Medik'] = $value['no_rekam_medik'];
            $dataValue['Nama Pasien'] = $value['nama_pasien'];
            $dataValue['Ruangan Asal'] = $value['ruangan_nama'];
            $dataValue['Jenis Kelamin'] = $value['jenis_kelamin'];
            $dataValue['Cara Bayar'] = $value['carabayar_nama'];
            $dataValue['Penjamin'] = $value['penjamin_nama'];
            $dataValue['Nama Dokter'] = $value['nama_pegawai'];
            $dataValue['Status'] = $value['status_periksa1'];
            $result[] = $dataValue;
        }

        $header = ['Periode' => date('d F Y', strtotime($tgl_awal)) . ' - ' . date('d F Y', strtotime($tgl_akhir))];
        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);

        $filePath->save('php://output');
        die;
    }

    /**
     *
     * @see Fungsi get list data
     * @return array
     *
     */
    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;

            $find_statusperiksa = $this->getStatusPeriksa();
            $data_statusperiksa = $find_statusperiksa->asArray()->all();

            $find_pegawai = $this->getPegawaiRuangan($request->get('id_ruangan', null), $request->get('instalasi_id', null));
            $data_pegawai = $find_pegawai->asArray()->all();

            $find_penjamin = $this->getPenjamin();
            $data_penjamin = $find_penjamin->asArray()->all();

            // Get ruangan
            $modelRuangan = $this->getRuangan();
            $dataRuangan = $modelRuangan->andWhere(['instalasi_id' => DocoConstants::VAR_I_RJ])->asArray()->all();

            // Get jenis kelamin
            $modelJenisKelamin = $this->getJenisKelamin();
            $dataJenisKelamin = $modelJenisKelamin->asArray()->all();

            $modelCaraBayar = $this->getCaraBayar();
            $dataCaraBayar = $modelCaraBayar->asArray()->all();

            // get data lantai
            $modelLantai = $this->getLantai();
            $dataLantai = $modelLantai->asArray()->all();

            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
                'data-penjamin' => $data_penjamin,
                'data-ruangan' => $dataRuangan,
                'data-jenis-kelamin' => $dataJenisKelamin,
                'data-cara-bayar' => $dataCaraBayar,
                'data-lantai' => $dataLantai
            ];
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

    private function getCaraBayar()
    {
        $sql = "SELECT carabayar_id, carabayar_nama, carabayar_namalainnya, metode_pembayaran FROM carabayar_m WHERE is_deleted = FALSE";
        $result = CaraBayar::findBySql($sql);

        return $result;
    }

    /**
     *
     * @see Fungsi get data pasien
     * @return array
     *
     */
    public function actionGetPasienDetail($id)
    {
        try {
            $params = [];

            $find = $this->getPasienDetail($id)->asArray()->one();

            return [
                'data' => $find
            ];
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

    /**
     *
     * @see Fungsi create janji poli
     * @return array
     *
     */
    public function actionBuatJanjiPoli()
    {
        try {
            $request = Yii::$app->request;

            if ($data_post = $request->post()) {
                $modelBuatJanjiPoli = BuatJanjiPoli::find()->where([
                    'pendaftaran_id' => $data_post['pendaftaran_id']
                ])->one();
                if (empty($modelBuatJanjiPoli)) $modelBuatJanjiPoli = new BuatJanjiPoli;
                $modelBuatJanjiPoli->attributes = $data_post;

                if ($modelBuatJanjiPoli->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($modelBuatJanjiPoli->errors, 'BuatJanjiPoliForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * @see Fungsi ubah data pasien pendaftaran
     * @return array
     *
     */
    public function actionUbahDokter()
    {
        $request = Yii::$app->request;
        try {
            if ($post = $request->post()) {
                $data_pendaftaran = Pendaftaran::find()
                    ->where([
                        'pendaftaran_id' => $post["pendaftaran_id"],
                        'is_deleted' => false,
                    ])->one();

                if ($data_pendaftaran) {
                    $data_pendaftaran->pegawai_id = $post["pegawai_id"];
                    $data_pendaftaran->status_periksa = 2;
                    $data_pendaftaran->save();

                    return [
                        'message' => 'status_success'
                    ];
                } else {
                    \Yii::$app->response->statusCode = 203;

                    return [
                        'message' => 'status_notfound'
                    ];
                }
            }
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

    /**
     *
     * @see Fungsi get data penjamin
     * @return array, activeQueryRecords
     *
     */
    private function getPenjamin()
    {
        $penjamin = Penjamin::find()->select([
            "penjamin_id",
            "penjamin_nama",
        ]);

        return $penjamin;
    }

    /**
     *
     * @see Fungsi get data pegawai ruangan
     * @return array, activeQueryRecords
     *
     */
    private function getPegawaiRuangan($id_ruangan, $instalasi_id = null)
    {
        $join = '';
        $conditions = 'ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $params = [
            ':ruangan_id' => $id_ruangan
        ];
        if (!is_null($instalasi_id)) {
            $join = 'JOIN ruangan_m ON ruangan_m.ruangan_id = ruanganpegawai_mp.ruangan_id';
            $conditions = 'ruangan_m.instalasi_id = :instalasi_id AND pegawai_m.kelompokpegawai_id = :kelompokpegawai_id';
            $params = [
                ':instalasi_id' => $instalasi_id,
                ':kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
            ];
        }
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id ' . $join . ' WHERE ' . $conditions;
        $result = RuanganPegawai::findBySql($sql, $params);

        return $result;
    }

    /**
     *
     * @see Fungsi get data status periksa
     * @return array, activeQueryRecords
     *
     */
    private function getStatusPeriksa()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa1
                FROM lookup_m
                WHERE lookup_type = 'status_periksa'
                AND lookup_id IN (1,2,4,402)
                ";
        $result = Lookup::findBySql($sql);

        return $result;
    }

    /**
     *
     * @see Fungsi get data jenis kelamin
     * @return array, activeQueryRecords
     *
     */
    private function getJenisKelamin()
    {
        // Query
        $sql = "SELECT lookup_id, lookup_name, lookup_name as jenis_kelamin FROM lookup_m WHERE lookup_type = 'jenis_kelamin'";

        // Result
        $result = Lookup::findBySql($sql);

        // Return
        return $result;
    }

    /**
     *
     * @see Fungsi get data ruangan
     * @return array, activeQueryRecords
     *
     */
    private function getRuangan()
    {
        // Query
        $sql = "SELECT ruangan_id, ruangan_nama, ruangan_nama as ruangan_nama FROM ruangan_m";

        // Result
        $result = Ruangan::find()->select(['ruangan_id', 'ruangan_nama']);

        // Return
        return $result;
    }

    /**
     *
     * @see Fungsi get data pasien joins
     * @var params array
     * @return array, activeQueryRecords
     *
     */
    private function getPasienDetail($id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.antrian_id,
                    pendaftaran_t.pegawai_id as pegawai_id,
                    pendaftaran_t.ruangan_id as ruangan_id,
                    pendaftaran_t.carabayar_id as carabayar_id,
                    pendaftaran_t.penjamin_id as penjamin_id,
                    pendaftaran_t.tgl_pendaftaran as tgl_pendaftaran,
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pendaftaran_t.no_pendaftaran AS no_pendaftaran,
                    pasien_m.nama_pasien AS nama_pasien,
                    pasien_m.pasien_id AS pasien_id,
                    bpjs_t.bpjs_id AS bpjs_id,
                    bpjs_t.nosep AS nosep,
                    pendaftaran_t.rujukan_id as rujukan_id,
                    buatjanjipoli_t.tgl_buatjanji
                FROM
                    pendaftaran_t
                JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
                LEFT JOIN buatjanjipoli_t ON pendaftaran_t.pendaftaran_id = buatjanjipoli_t.pendaftaran_id
                WHERE
                    pendaftaran_t.is_deleted = FALSE
            ";

        // filter
        if ($id) {
            $sql .= " AND pendaftaran_t.pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        $result = Pendaftaran::findBySql($sql, $condition);
        return $result;
    }
    /*=====  End of list pasien  ======*/

    /**
     *
     * @see Fungsi override action index
     * @return array, activeQueryRecords data viiew riwayat pasien
     *
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;

        $cache_key = 'riwayatpasienview_';

        try {
            $pasien_id = $request->get('pasien_id');
            $cache_key = 'riwayatpasienview_' . $pasien_id;

            //cek cache riwayat pasien
            $cache_riwayatpasien = $cache->get($cache_key);

            if ($cache_riwayatpasien) {
                $query = $cache_riwayatpasien;
            } else {
                // $query = $this->getData($pasien_id)->all();
                $query = $this->getDataRiwayatPasien($pasien_id)->asArray()->all();

                // set cache 15 menit
                $cache->set($cache_key, $query, 900);
            }

            return [
                'data' => $query,
                'count' => count($query)
            ];
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

    /**
     *
     * @see Fungsi get pasien
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetPasien()
    {
        // $cache = Yii::$app->cache;
        // $data = $cache->get('$key');
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id');
            $registrationRecord = $this->getDataPasien($pendaftaran_id)->limit(1)->asArray()->one();
            
            $pasienRegData = $this->getDataPendaftaranPasien($pendaftaran_id);
            $instalasiId = ArrayHelper::getValue($pasienRegData, 'instalasi_id');
            $registrationRecord['instalasi_id'] = $instalasiId;
            $regData = !empty($pasienRegData) ? json_decode($pasienRegData['pendaftaran'], true) : [];
            $registrationRecord['riwayat_pasien'] = $this->getRiwayatPasienTerbaru($registrationRecord['pasien_id']); //@use PasienTrait Function
            $odcRuangan = [
                'ruangan_odc' => $this->constans->actionGetId('ruangan_odc')
            ];
            $registrationRecord = array_merge($registrationRecord, $odcRuangan, $regData);
            $constCathlab = $this->constans->actionGetId('kdtindakan_cathlab');
            $hasCathlab = 0;
            if (!empty($pendaftaran_id)) {
                $hasCathlab = TindakanPelayanan::find()->select([
                    'daftartindakan_m.daftartindakan_id',
                ])
                    ->rightJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id')
                    ->andWhere([
                        'tindakanpelayanan_t.pendaftaran_id' => $pendaftaran_id,
                        'daftartindakan_m.kelompoktindakan_id' => $constCathlab
                    ])->count();
            }
            $showTtvTab = false;
            $showEwsTab = false;
            $showSbarTab = false;
            $getKonfigSystem = Cache::getKonfigSistem();
            $konfigSystem = ArrayHelper::getValue($getKonfigSystem, 'konfig_monitoring_ttv', null);
            $konfigObservation = ArrayHelper::getValue($getKonfigSystem, 'konfig_observasi_ews', null);
            $konfigSbar = ArrayHelper::getValue($getKonfigSystem, 'konfig_sbar', null);
            
            if(!is_null($konfigSystem) && !is_array($konfigSystem)) {
                $konfigSystem = json_decode($konfigSystem, true);
            }

            if (isset($konfigSystem['RJ']) && $konfigSystem['RJ'] == true) {
                $showTtvTab = true;
            }

            if (!is_null($konfigObservation) && !is_array($konfigObservation)) {
                $konfigObservation = json_decode($konfigObservation, true);
            }

            if (isset($konfigObservation['RJ']) && $konfigObservation['RJ'] == true) {
                $showEwsTab = true;
            }

            if (!is_null($konfigSbar) && !is_array($konfigSbar)) {
                $konfigSbar = json_decode($konfigSbar, true);
            }

            if (isset($konfigSbar['RJ']) && $konfigSbar['RJ'] == true) {
                $showSbarTab = true;
            }

            $registrationRecord['status_periksa_id'] = $registrationRecord['status_periksa'];
            $registrationRecord['hasCathlab'] = $hasCathlab ? true : false;
            $registrationRecord['showTtvTab'] = $showTtvTab ? true : false;
            $registrationRecord['showEwsTab'] = $showEwsTab ? true : false;
            $registrationRecord['showSbarTab'] = $showSbarTab ? true : false;
            $registrationRecord['icare_identifier'] = null;

            // get no kartu BPJS untuk kebutuhan iCare
            $lastDataBpjs = json_decode($pasienRegData['bpjs'], true);
            if(isset($lastDataBpjs['nokartuasuransi']) && !empty($lastDataBpjs['nokartuasuransi'])) {
                $registrationRecord['icare_identifier'] = $lastDataBpjs['nokartuasuransi'];
            } else {
                $pasien = json_decode($pasienRegData['pasien'], true);
                $registrationRecord['icare_identifier'] = isset($pasien['nopeserta_bpjs']) ? $pasien['nopeserta_bpjs'] : $pasien['no_identitas_pasien'];
            }
            
            if ($request->get('cppt')) {
                // get askep from anamnesa
                $anamnesa = json_decode($pasienRegData['anamnesa'], true);
                // $askep = array_filter($anamnesa, function($a) {return !is_null($a);});

                $soaprj = json_decode($pasienRegData['soaprj'], true);
                $cppt = !empty($soaprj['a_diag_utama']) ? $soaprj : null;

                return [
                    'registration' => $registrationRecord,
                    'askep' => !empty($askep) ? $askep : null,
                    'cppt' => $cppt
                ];
            } else {
                return [
                    'data' => $registrationRecord
                ];
            }
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

    /**
     *
     * @see Fungsi get penunjang
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id'); // pendaftaran_id
            $ruangan_id = $request->get('ruangan_id'); // ruangan_id

            $data = $this->getDataPenunjang($pendaftaran_id, $ruangan_id)
                ->asArray()->all();

            return [
                'data' => $data
            ];
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

    /**
     *
     * @see Fungsi get anamnesa pasien
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetAnamnesa($id = null)
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = ($id != null) ? $id : $request->get('id');

            $condition = [];
            $query = '
                SELECT
                    anamnesa_t.*,
                    anamesa_id,
                    anamnesa_t.pendaftaran_id as pendaftaran_id,
                    pendaftaran_t.tgl_pendaftaran as tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran as no_pendaftaran,
                    dokter.nama_pegawai as nama_dokter,
                    tgl_anamnesis,
                    pendaftaran_t.status_periksa,
                    perawat.nama_pegawai as nama_perawat,
                    penjamin_m.penjamin_nama as penjamin_nama,
                    status_periksa.lookup_name AS nama_status_periksa,
                    ruangan_m.ruangan_nama,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama as jeniskasuspenyakit_nama,
                    carabayar_m.carabayar_nama as carabayar_nama,
                    kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                    lookup_m.lookup_value as jk,
                    pendaftaran_t.umur,
                    pasien_m.no_rekam_medik as no_rekam_medik,
                    pasien_m.nama_pasien as nama_pasien,
                    pasien_m.tanggal_lahir as tgl_lahir,
                    pendidikan.pendidikan_nama as pendidikan,
                    darah.lookup_name as golongan_darah
                FROM anamnesa_t
                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
                LEFT JOIN pegawai_m as dokter ON dokter.pegawai_id = anamnesa_t.pegawaidokter_id
                LEFT JOIN pegawai_m as perawat ON perawat.pegawai_id = anamnesa_t.pegawaiperawat_id
                LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = pendaftaran_t.penjamin_id
                JOIN lookup_m status_periksa ON pendaftaran_t.status_periksa::integer = status_periksa.lookup_id
                LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = pendaftaran_t.ruangan_id
                LEFT JOIN jeniskasuspenyakit_m ON jeniskasuspenyakit_m.jeniskasuspenyakit_id = pendaftaran_t.jeniskasuspenyakit_id
                LEFT JOIN carabayar_m ON carabayar_m.carabayar_id = pendaftaran_t.carabayar_id
                LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
                LEFT JOIN lookup_m ON lookup_m.lookup_id = pasien_m.jeniskelamin::integer
                LEFT JOIN pendidikan_m as pendidikan ON pendidikan.pendidikan_id = pasien_m.pendidikan_id
                LEFT JOIN lookup_m as darah ON darah.lookup_id = pasien_m.golongandarah::integer
                WHERE
                    anamnesa_t.is_deleted = FALSE
            ';
            // filter
            if ($pendaftaran_id) {
                $query .= " AND anamnesa_t.pendaftaran_id = :pendaftaran_id";
                $condition[':pendaftaran_id'] = $pendaftaran_id;
            }
            $result = Anamnesa::findBySql($query, $condition);
            $result_data = $result->asArray()->all();
            $result_one = $result->one();
            if (isset($result_one['pegawaiverifikasigizi_id']) && !empty($result_one['pegawaiverifikasigizi_id'])) {
                $result_one['pegawaiverifikasigizi_nama'] = Pegawai::find()->select(['nama_pegawai'])->where(['pegawai_id' => $result_one['pegawaiverifikasigizi_id']])->scalar();
            }
            $riwayat_penyakit_terdahulu = $result_one['riwayat_penyakitterdahulu'];
            $riwayat_penyakit_terdahulu = json_decode($riwayat_penyakit_terdahulu, true);
            $tampung_diagnosa = [];
            /*if(count($riwayat_penyakit_terdahulu) > 0){
                foreach ($riwayat_penyakit_terdahulu as $key => $value) {
                    $diagnosa = Diagnosa::findOne(['diagnosa_id'=>$value]);
                    if(!empty($diagnosa)){
                        $tampung_diagnosa[$key]['diagnosa_id'] = $value;
                        $tampung_diagnosa[$key]['diagnosa_text'] = $diagnosa->diagnosa_nama;
                    }
                }
                // $result_one['riwayat_penyakitterdahulu'] = json_encode($tampung_diagnosa);
            }*/

            return [
                'data' => $result_data,
                'data_one' => $result_one,
            ];
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

    /**
     *
     * @see Fungsi create anamnesa pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionCreateAnamnesa()
    {
        try {
            $request = Yii::$app->request;
            $model = new Anamnesa;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan', [
                        'pendaftaran__id' => $model->pendaftaran_id,
                        'ketergantungan_jenis' => $model->ketergantungan_jenis,
                        'riwayat_penyakit_keluarga_list' => $model->riwayat_penyakit_keluarga_list,
                        'status_ekonomi' => $model->status_ekonomi
                    ]];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'AnamnesaForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * Fungsi update anamnesa pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionUpdateAnamnesa($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Anamnesa::findOne($id);

            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->update()) {
                    return ['message' => 'Data Berhasil di ubah', [
                        'ketergantungan_jenis' => $model->ketergantungan_jenis,
                        'riwayat_penyakit_keluarga_list' => $model->riwayat_penyakit_keluarga_list,
                        'status_ekonomi' => $model->status_ekonomi
                    ]];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'AnamnesaForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * Fungsi delete anamnesa pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionDeleteAnamnesa($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Anamnesa::findOne($id);

            if ($model->delete()) {
                return ['message' => 'Data Berhasil di ubah'];
            } else {
                return [
                    'data' => 'Gagal hapus',
                    'status' => 422
                ];
            }
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
    /**
     * @controller actionCetakAnamnesa
     * @attribute #no_pendaftaran# => Menamplkan nomor pendaftaran
     * @attribute #tgl_pendaftaran# => Menamplkan tanggal pendaftaran
     * @attribute #status_periksa# => Menamplkan nama status periksa
     * @attribute #penjamin_nama# => Menamplkan nama penjamin
     * @attribute #umur# => Menamplkan umur
     * @attribute #jeniskasuspenyakit_nama# => Menamplkan nama jenis kasus penyakit
     * @attribute #carabayar_nama# => Menamplkan nama cara bayar
     * @attribute #kelaspelayanan_nama# => Menamplkan nama kelas pelayanan
     * @attribute #poliklinik# => Menamplkan poliklinik
     * @attribute #jeniskelamin# => Menamplkan jenis kelamin
     * @attribute #no_rekam_medik# => Menamplkan no rekam medik
     * @attribute #tanggal_lahir# => Menamplkan tanggal lahir
     * @attribute #nama_pasien# => Menamplkan nama pasien
     * @attribute #tanggal_anamnesa# => Menamplkan tanggal anamnesa
     * @attribute #dokter# => Menamplkan nama dokter
     * @attribute #perawat# => Menamplkan nama perawat
     * @attribute #keluhan_utama# => Menamplkan keluhan utama
     * @attribute #keluhan_tambahan# => Menamplkan keluhan tambahan
     * @attribute #riwayat_penyakit_pasien# => Menamplkan riwayat penyakit pasien
     * @attribute #lama_sakit# => Menamplkan lama sakit
     * @attribute #riwayat_penyakit_terdahulu# => Menamplkan riwayat penyakit terdahulu
     * @attribute #riwayat_penyakit_keluarga# => Menamplkan riwayat penyakit keluarga
     * @attribute #riwayat_imunisasi# => Menamplkan riwayat imunisasi
     * @attribute #status_merokok# => Menamplkan status merokok
     * @attribute #jumlahbatang_rokok# => Menamplkan jumlah batang rokok
     * @attribute #obat_sudahdiberikan# => Menamplkan obat yang sudah di berikan
     * @attribute #riwayat_makanan# => Menamplkan riwayat makanan
     * @attribute #riwayat_kelahiran# => Menamplkan riwayat kelahiran
     * @attribute #riwayat_alergiobat# => Menamplkan riwayat alergi obat
     * @attribute #keterangan_anamnesa# => Menamplkan keterangan anamnesa
     * @attribute #tgl_skrg# => Menamplkan tanggal sekarang
     * @attribute #resiko_jatuh# => Menamplkan resiko jatuh
     * @attribute #status_nyeri# => Menamplkan status nyeri
     * @attribute #lokasi_nyeri# => Menamplkan lokasi nyeri
     * @attribute #skala_nyeri# => Menamplkan skala nyeri
     * @attribute #history_riwayat_penyakitDahulu# => Menamplkan riwayat penyakit terdahulu
     * @attribute #history_riwayat_penyakitKeluarga# => Menamplkan riwayat penyakit keluarga
     * @attribute #history_riwayat_imunisasi# => Menamplkan riwayat imunisasi
     * @attribute #history_riwayat_makanan# => Menamplkan riwayat makanan
     * @attribute #history_riwayat_kelahiran# => Menamplkan riwayat kelahiran
     * @attribute #history_riwayat_alergiobat# => Menamplkan riwayat alergi obat
     * @attribute #kerabat_n# => kerabat nama
     * @attribute #kerabat_h# => kerabat hubungan
     * @attribute #kerabat_k# => kerabat kontak
     **/
    public function actionCetakAnamnesa($id)
    {
        $configData = require_once(Yii::$app->basePath . '/config/files/asesmen_keperawatan.php');
        $data = $this->actionGetAnamnesa($id)['data_one'];

        $arrSearch = ['[', ']', '"'];
        $arrRep = ['', '', ''];
        $riwayat_penyakit_terdahulu = str_replace($arrSearch, $arrRep, $data['riwayat_penyakitterdahulu']);
        $riwayat_penyakit_keluarga = str_replace($arrSearch, $arrRep, $data['riwayat_penyakitkeluarga']);
        $riwayat_alergiobat = str_replace($arrSearch, $arrRep, $data['riwayat_alergiobat']);
        $intImunisasi = [];
        $strImunisasi = [];
        if (count($data['riwayat_imunisasi']) > 0) {
            foreach (json_decode($data['riwayat_imunisasi']) as $key => $value) {
                if (!filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $intImunisasi[] = $value;
                } else {
                    $strImunisasi[] = $value;
                }
            }
        }
        $impImunisasiStr = implode(',', $strImunisasi);
        $riwayat_imunisasi = $this->getDiagnosa($intImunisasi);
        $fusionImunisasi = ($impImunisasiStr != '') ? $impImunisasiStr . ',' . $riwayat_imunisasi : $riwayat_imunisasi;
        $history_riwayat_penyakitDahulu     = [];
        $history_riwayat_penyakitKeluarga   = [];
        $history_riwayat_imunisasi          = [];
        $history_riwayat_makanan            = [];
        $history_riwayat_kelahiran          = [];
        $history_riwayat_alergiobat         = [];
        $sumber_data = $status_psikologi = $status_ekonomi = $kebutuhann_edukasi_row1 = $kebutuhann_edukasi_row2 = $hasil_resiko = $frekuensi_nyeri_kronis_pertama = $frekuensi_nyeri_kronis_kedua = $ket_nyeri = $kualitas_nyeri = $faktor_pereda_nyeri = $diagnosa_khusus_row1 = $diagnosa_khusus_row2 = '';


        foreach ($configData['sumber_data'] as $key => $value) {
            $sumber_data .= strpos($data['sumber_data'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['sumber_data'], '00') !== false ? $sumber_data .= ': ' . ucwords(substr($data['sumber_data'], strpos($data['sumber_data'], '00') + 3)) : null;

        $rujukan_no = $data['rujukan'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $rujukan_ya = $data['rujukan'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $rs = $data['tujuan_rujukan'] == 'rs' ? '<input checked="checked" type="checkbox" /> RS ' . $data['rujukan_rs'] : '<input type="checkbox" /> RS';
        $puskesmas = $data['tujuan_rujukan'] == 'puskesmas' ? '<input checked="checked" type="checkbox" /> Puskesmas' : '<input type="checkbox" /> Puskesmas';
        $dokter = $data['tujuan_rujukan'] == 'dokter' ? '<input checked="checked" type="checkbox" /> Dokter' : '<input type="checkbox" /> Dokter';
        $rujukan = $rujukan_no . '&emsp;' . $rujukan_ya . '&emsp;' . $rs . '&emsp;' . $puskesmas . '&emsp;' . $dokter;

        // 3. Riwayat Kesehatan Dahulu
        // A. Riwayat Penyakit Dahulu
        $riwayat_tidak =  $data['riwayat_penyakit'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $riwayat_ya =  $data['riwayat_penyakit'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $riwayat_penyakit = $riwayat_tidak . '&emsp;' . $riwayat_ya . '&emsp;' . $data['riwayat_penyakit_nama'];
        //- Pernah Dirawat
        $dirawat_tidak =  $data['dirawat'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $dirawat_ya =  $data['dirawat'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $dirawat = $dirawat_tidak . '&emsp;' . $dirawat_ya . '&emsp;  Diagnosa :&emsp;' . $data['dirawat_diagnosa'] . '&emsp;  Kapan :&emsp;' . $data['dirawat_waktu'] . '&emsp;  Di :&emsp;' . $data['dirawat_tempat'];
        // - Pernah DiOperasi
        $dioperasi_tidak =  $data['dioperasi'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $dioperasi_ya =  $data['dioperasi'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $dioperasi = $dioperasi_tidak . '&emsp;' . $dioperasi_ya . '&emsp;  Jenis Operasi :&emsp;' . $data['dioperasi_diagnosa'] . '&emsp;  Kapan :&emsp;' . $data['dioperasi_waktu'];
        // - Obat-obatan yang dikonsumsi
        $obat_tidak =  $data['obat_dikonsumsi'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $obat_ya =  $data['obat_dikonsumsi'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $obat_konsumsi = $obat_tidak . '&emsp;' . $obat_ya . '&emsp;' . $data['obat_dikonsumsi_nama'];
        // B. Riwayat penyakit keluarga
        $riwayat_keluarga_tidak =  $data['riwayat_penyakit_keluarga'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $riwayat_keluarga_ya =  $data['riwayat_penyakit_keluarga'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $riwayat_penyakit_keluarga = $riwayat_keluarga_tidak . '&emsp;' . $riwayat_keluarga_ya . '&emsp;';
        foreach ($configData['riwayat_penyakit_keluarga_list'] as $key => $value) {
            $riwayat_penyakit_keluarga .= strpos($data['riwayat_penyakit_keluarga_list'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['riwayat_penyakit_keluarga_list'], '00') !== false ? $riwayat_penyakit_keluarga .= ': ' . ucwords(substr($data['riwayat_penyakit_keluarga_list'], strpos($data['riwayat_penyakit_keluarga_list'], '00') + 3)) : null;

        // C. Ketergantungan terhadap
        $ketergantungan_tidak =  $data['ketergantungan'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $ketergantungan_ya =  $data['ketergantungan'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $ketergantungan = $ketergantungan_tidak . '&emsp;' . $ketergantungan_ya . '&emsp;';
        foreach ($configData['ketergantungan_jenis'] as $key => $value) {
            $ketergantungan .= strpos($data['ketergantungan_jenis'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['ketergantungan_jenis'], '00') !== false ? $ketergantungan .= ': ' . ucwords(substr($data['ketergantungan_jenis'], strpos($data['ketergantungan_jenis'], '00') + 3)) : null;
        // D. Riwayat Pekerjaan (apakah berhubungan dengan zat-zat berbahaya?)
        $riwayat_pekerjaan_tidak =  $data['riwayat_pekerjaan'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $riwayat_pekerjaan_ya =  $data['riwayat_pekerjaan'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $riwayat_pekerjaan = $riwayat_pekerjaan_tidak . '&emsp;' . $riwayat_pekerjaan_ya . '&emsp;' . $data['riwayat_pekerjaan_nama'];
        // E. Riwayat alergi
        $alergi_tidak =  $data['alergi'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
        $alergi_ya =  $data['alergi'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $alergi_obat = !empty($data['alergi_obat'])  ? '<input checked="checked" type="checkbox" /> Obat : &nbsp;' . $data['alergi_obat'] : '<input type="checkbox" /> Obat';
        $alergi_makanan = !empty($data['alergi_makanan'])  ? '<input checked="checked" type="checkbox" /> Makanan : &nbsp;' . $data['alergi_makanan'] : '<input type="checkbox" /> Makanan';
        $alergi_lainnya = !empty($data['alergi_lainnya'])  ? '<input checked="checked" type="checkbox" /> Lainnya : &nbsp;' . $data['alergi_lainnya'] : '<input type="checkbox" /> Lainnya';
        $alergi = $riwayat_pekerjaan_tidak . '&emsp;' . $riwayat_pekerjaan_ya . '&emsp;' . $alergi_obat . '&emsp;' . $alergi_makanan . '&emsp;' . $alergi_lainnya;

        // 4. Riwayat Psikososial dan Spiritual
        // A. Status Psikologi
        foreach ($configData['status_psikologi'] as $key => $value) {
            $status_psikologi .= strpos($data['status_psikologi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }

        // B. Status Sosial
        // Hubungan pasien dan keluarga
        $hub_pasien_tidak =  $data['status_sosial'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak Baik' : '<input type="checkbox" /> Tidak Baik';
        $hub_pasien_ya =  $data['status_sosial'] == "1" ? '<input checked="checked" type="checkbox" /> Baik' : '<input type="checkbox" /> Baik';
        $hub_pasien = $hub_pasien_tidak . '&emsp;' . $hub_pasien_ya;
        // C. Status Ekonomi
        foreach ($configData['status_ekonomi'] as $key => $value) {
            $status_ekonomi .= strpos($data['status_ekonomi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['status_ekonomi'], '00') !== false ? ($status_ekonomi .= ': ' . ucwords(substr($data['status_ekonomi'], strpos($data['status_ekonomi'], '00') + 3))) : null;

        // E. Kultural
        if (isset($data['suku_id']) && !empty($data['suku_id'])) {
            $kultural = Suku::find()->select([
                'suku_nama'
            ])->where([
                'suku_id' => $data['suku_id']
            ])->asArray()->one();
        }

        // 5. Kebutuhan Komunikasi dan Edukasi
        // Kesediaan menerima informasi
        $kesediaan_informasi_tidak =  $data['kesediaan_menerima_informasi'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $kesediaan_informasi_ya =  $data['kesediaan_menerima_informasi'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $kesediaan_informasi = $kesediaan_informasi_tidak . '&emsp;' . $kesediaan_informasi_ya;
        // Kemampuan Membaca
        $kemampuan_membaca_tidak =  $data['kemampuan_membaca'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak Mampu ' : '<input type="checkbox" /> Tidak Mampu';
        $kemampuan_membaca_ya =  $data['kemampuan_membaca'] == "1" ? '<input checked="checked" type="checkbox" /> Mampu' : '<input type="checkbox" /> Mampu';
        $kemampuan_membaca = $kemampuan_membaca_tidak . '&emsp;' . $kemampuan_membaca_ya;

        // Dibutuhkan penerjemah
        // #penerjemah#
        $penerjemah_tidak =  $data['butuh_penerjemah'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $penerjemah_ya =  $data['butuh_penerjemah'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $bahasa_isyarat_tidak =  $data['bahasa_isyarat'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $bahasa_isyarat_ya =  $data['bahasa_isyarat'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $penerjemah = $kemampuan_membaca_tidak . '&emsp;' . $kemampuan_membaca_ya . '&nbsp; Kebutuhan :&nbsp;' . $data['butuh_penerjemah_nama'] . '&emsp; Bahasa isyarat : &emsp;' . $bahasa_isyarat_tidak . '&emsp;' . $bahasa_isyarat_ya;

        // Terdapat hambatan dalam pembelajaran
        // #hambatan#
        $hambatan_tidak =  $data['hambatan'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $hambatan_ya =  $data['hambatan'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $hambatan_row1 = $hambatan_tidak . '&emsp;' . $hambatan_ya . '&emsp;';
        foreach ($configData['jenis_hambatan']['first_row'] as $key => $value) {
            $hambatan_row1 .= strpos($data['jenis_hambatan'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        $hambatan_row2 = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;';
        foreach ($configData['jenis_hambatan']['second_row'] as $key => $value) {
            $hambatan_row2 .= strpos($data['jenis_hambatan'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['jenis_hambatan'], '00') !== false ? $hambatan_row2 .= ': ' . ucwords(substr($data['jenis_hambatan'], strpos($data['jenis_hambatan'], '00') + 3)) : null;

        // Kebutuhan edukasi (pilih topik edukasi pada kotak yang tersedia)
        // #kebutuhan_edukasi#

        foreach ($configData['kebutuhan_edukasi']['first_row'] as $key => $value) {
            $kebutuhann_edukasi_row1 .= strpos($data['kebutuhan_edukasi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        foreach ($configData['kebutuhan_edukasi']['second_row'] as $key => $value) {
            $kebutuhann_edukasi_row2 .= strpos($data['kebutuhan_edukasi'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['kebutuhan_edukasi'], '00') !== false ? $kebutuhann_edukasi_row2 .= ': ' . ucwords(substr($data['kebutuhan_edukasi'], strpos($data['kebutuhan_edukasi'], '00') + 3)) : null;

        // 6. Resiko Cedera/Jatuh
        // A. Perhatikan cara berjalan pasien saat akan duduk di kursi. Apakah pasien tampat tidak seimbang (sempoyongan)
        // #resiko_cedera_pertama#
        $resiko_pertama_tidak =  $data['resiko_cedera_pertama'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $resiko_pertama_ya =  $data['resiko_cedera_pertama'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $resiko_cedera_pertama = $resiko_pertama_tidak . '&emsp;' . $resiko_pertama_ya;
        // B. Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk
        // #resiko_cedera_kedua#
        $resiko_kedua_tidak =  $data['resiko_cedera_kedua'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $resiko_kedua_ya =  $data['resiko_cedera_kedua'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $resiko_cedera_kedua = $resiko_kedua_tidak . '&emsp;' . $resiko_kedua_ya;
        // Hasil
        // #hasil_resiko#
        foreach ($configData['hasil_resiko'] as $key => $value) {
            $hasil_resiko .= strpos($data['hasil_resiko'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        // 7. Status Fungsional
        // #aktivitas#
        $aktivitas_tidak =  $data['aktivitas'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $aktivitas_ya =  $data['aktivitas'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $bantuan_aktivitas = isset($data['bantuan_aktivitas']) ? ': ' . $data['bantuan_aktivitas'] : '';
        $aktivitas = $aktivitas_tidak . '&emsp;' . $aktivitas_ya . '&emsp;' . $bantuan_aktivitas;
        // #alat_bantu#
        $alat_bantu_jalan = isset($data['alat_bantu_jalan']) ? 'Alat Bantu : ' . $data['alat_bantu_jalan'] : 'Alat Bantu : ';


        // 8. Skala Nyeri
        $skala_nyeri = $data['skala_nyeri'];
        $nyeri = $data['is_nyeri'];
        if ($nyeri == '1') {
            $is_nyeri = '<input type="checkbox" /> Tidak <input checked="checked" type="checkbox" /> Ya';
            $nol      = ($skala_nyeri == 0) ? '<span style=" border: 5px solid #04C86B;border-radius: 50px;background-color:#04C86B; color:white;">O</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">O</span>';
            $satu     = ($skala_nyeri == 1) ? '<span style=" border: 5px solid #4FC354;border-radius: 50px;background-color:#4FC354; color:white;">1</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">1</span>';
            $dua      = ($skala_nyeri == 2) ? '<span style=" border: 5px solid #8DBD33;border-radius: 50px;background-color:#8DBD33; color:white;">2</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">2</span>';
            $tiga     = ($skala_nyeri == 3) ? '<span style=" border: 5px solid #C5DA2C;border-radius: 50px;background-color:#C5DA2C; color:white;">3</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">3</span>';
            $empat    = ($skala_nyeri == 4) ? '<span style=" border: 5px solid #F0F221;border-radius: 50px;background-color:#F0F221; color:white;">4</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">4</span>';
            $lima     = ($skala_nyeri == 5) ? '<span style=" border: 5px solid #F2D51A;border-radius: 50px;background-color:#F2D51A; color:white;">5</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">5</span>';
            $enam     = ($skala_nyeri == 6) ? '<span style=" border: 5px solid #F2B610;border-radius: 50px;background-color:#F2B610; color:white;">6</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">6</span>';
            $tujuh    = ($skala_nyeri == 7) ? '<span style=" border: 5px solid #F09409;border-radius: 50px;background-color:#F09409; color:white;">7</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">7</span>';
            $delapan  = ($skala_nyeri == 8) ? '<span style=" border: 5px solid #EF7800;border-radius: 50px;background-color:#EF7800; color:white;">8</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">8</span>';
            $sembilan = ($skala_nyeri == 9) ? '<span style=" border: 5px solid #E54209;border-radius: 50px;background-color:#E54209; color:white;">9</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">9</span>';
            $sepuluh  = ($skala_nyeri == 10) ? '<span style=" border: 5px solid #D61F01;border-radius: 50px;background-color:#D61F01; color:white;">10</span>' : '<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">10</span>';
            $skala_number = '<br>' . $nol . '&emsp;&emsp;' . $satu . '&emsp;&emsp;' . $dua . '&emsp;&emsp;' . $tiga . '&emsp;&emsp;' . $empat . '&emsp;&emsp;' . $lima . '&emsp;&emsp;'
                . $enam . '&emsp;&emsp;' . $tujuh . '&emsp;&emsp;' . $delapan . '&emsp;&emsp;' . $sembilan . '&emsp;&emsp;' . $sepuluh . '&emsp;&emsp;';

            $nyeri_kronis_pertama = $data['nyeri_kronis_pertama'] == "1" ? '<input checked="checked" type="checkbox" /> Nyeri kronis' : '<input type="checkbox" /> Nyeri kronis';
            $lokasi_nyeri_kronis_pertama = isset($data['lokasi_nyeri_kronis_pertama']) ? 'Lokasi : ' . $data['lokasi_nyeri_kronis_pertama'] : 'Lokasi : ';
            foreach ($configData['frekuensi_nyeri_kronis'] as $key => $value) {
                $frekuensi_nyeri_kronis_pertama .= strpos($data['frekuensi_nyeri_kronis_pertama'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
            }
            $durasi_nyeri_kronis_pertama = isset($data['durasi_nyeri_kronis_pertama']) ? 'Durasi : ' . $data['durasi_nyeri_kronis_pertama'] : 'Durasi : ';
            $rekap_nyeri_kronis_pertama = $nyeri_kronis_pertama . '&emsp;' . $lokasi_nyeri_kronis_pertama . '&emsp;' . $frekuensi_nyeri_kronis_pertama . '&emsp;' . $durasi_nyeri_kronis_pertama . '<br>';

            $nyeri_kronis_kedua = $data['nyeri_kronis_pertama'] == "1" ? '<input checked="checked" type="checkbox" /> Nyeri kronis' : '<input type="checkbox" /> Nyeri kronis';
            $lokasi_nyeri_kronis_kedua = isset($data['lokasi_nyeri_kronis_kedua']) ? 'Lokasi : ' . $data['lokasi_nyeri_kronis_pertama'] : 'Lokasi : ';
            foreach ($configData['frekuensi_nyeri_kronis'] as $key => $value) {
                $frekuensi_nyeri_kronis_kedua .= strpos($data['frekuensi_nyeri_kronis_kedua'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
            }
            $durasi_nyeri_kronis_kedua = isset($data['durasi_nyeri_kronis_kedua']) ? 'Durasi : ' . $data['durasi_nyeri_kronis_kedua'] : 'Durasi : ';
            $rekap_nyeri_kronis_kedua = $nyeri_kronis_kedua . '&emsp;' . $lokasi_nyeri_kronis_kedua . '&emsp;' . $frekuensi_nyeri_kronis_kedua . '&emsp;' . $durasi_nyeri_kronis_kedua . '<br>';

            $skor_nyeri = isset($data['skor_nyeri']) ? 'Skor Nyeri : ' . $data['skor_nyeri'] : 'Skor Nyeri : ';

            $nyeri_menjalar_ya =  $data['nyeri_menjalar'] == "t" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
            $nyeri_menjalar_tidak =  $data['nyeri_menjalar'] == "f" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
            $nyeri_menjalar = '<br>' . $nyeri_menjalar_tidak . '&emsp;' . $nyeri_menjalar_ya;

            foreach ($configData['kualitas_nyeri'] as $key => $value) {
                $kualitas_nyeri .= strpos($data['kualitas_nyeri'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
            }
            foreach ($configData['faktor_pereda_nyeri'] as $key => $value) {
                $faktor_pereda_nyeri .= '<br>' . ((strpos($data['faktor_pereda_nyeri'], $key) !== false) ? ('<input checked="checked" type="checkbox" /> ' . $value . ' ') : ('<input type="checkbox" /> ' . $value . ' '));
            }
            strpos($data['faktor_pereda_nyeri'], '00') !== false ? $faktor_pereda_nyeri .= ': ' . ucwords(substr($data['faktor_pereda_nyeri'], strpos($data['faktor_pereda_nyeri'], '00') + 3)) : null;

            $ket_nyeri = '<br>' . $rekap_nyeri_kronis_pertama . $rekap_nyeri_kronis_kedua . $skor_nyeri . $nyeri_menjalar . '<br>' . $kualitas_nyeri . '<br>' . $faktor_pereda_nyeri;
        } else if ($nyeri == '0') {
            $is_nyeri = '<input checked="checked" type="checkbox" /> Tidak <input type="checkbox" /> Ya';
        } else {
            $is_nyeri = '<input type="checkbox" /> Tidak <input type="checkbox" /> Ya';
        }

        // 9. Nutrisi
        // 1.
        // 2.
        // 3.
        $diagnosa_khusus_tidak =  $data['diagnosa_khusus'] == "0" ? '<input checked="checked" type="checkbox" /> Tidak ' : '<input type="checkbox" /> Tidak';
        $diagnosa_khusus_ya =  $data['diagnosa_khusus'] == "1" ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
        $diagnosa_khusus_row1 = $diagnosa_khusus_tidak . '&emsp;' . $diagnosa_khusus_ya . '&emsp;';
        foreach ($configData['jenis_diagnosa_khusus']['first_row'] as $key => $value) {
            $diagnosa_khusus_row1 .= strpos($data['jenis_diagnosa_khusus'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        $diagnosa_khusus_row2 = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;';
        foreach ($configData['jenis_diagnosa_khusus']['second_row'] as $key => $value) {
            $diagnosa_khusus_row2 .= strpos($data['jenis_diagnosa_khusus'], $key) !== false ? '<input checked="checked" type="checkbox" /> ' . $value . ' ' : '<input type="checkbox" /> ' . $value . ' ';
        }
        strpos($data['jenis_diagnosa_khusus'], '00') !== false ? $diagnosa_khusus_row2 .= ': ' . ucwords(substr($data['diagnosa_khusus'], strpos($data['diagnosa_khusus'], '00') + 3)) : null;

        // 10 ----> disatuin pada file nutrisi
        $diagnosa_keperawatan = AnamnesaDetail::find()->select([
            'diagnosa_keperawatan',
            'tujuan_terukur'
        ])->where(['anamnesa_id' => $data['anamesa_id']])->asArray()->all();

        try {
            $data_request = AllowController::actionListPackAsesmen($id);
            $history_riwayat_penyakitDahulu = $data_request['data_riwayat']['riwayat_penyakitDahulu'];
            $history_riwayat_penyakitKeluarga = $data_request['data_riwayat']['riwayat_penyakitKeluarga'];
            $history_riwayat_imunisasi = $data_request['data_riwayat']['riwayat_imunisasi'];
            $history_riwayat_makanan = $data_request['data_riwayat']['riwayat_makanan'];
            $history_riwayat_kelahiran = $data_request['data_riwayat']['riwayat_kelahiran'];
            $history_riwayat_alergiobat = $data_request['data_riwayat']['riwayat_alergiobat'];
        } catch (Exception $e) {
            $history_riwayat_penyakitDahulu     = [];
            $history_riwayat_penyakitKeluarga   = [];
            $history_riwayat_imunisasi          = [];
            $history_riwayat_makanan            = [];
            $history_riwayat_kelahiran          = [];
            $history_riwayat_alergiobat         = [];
        }
        $print = new DocoPrint();
        $print->attributes = [
            // Informasi Pasien
            '#no_pendaftaran#' => $data['no_pendaftaran'],
            '#tgl_pendaftaran#' => isset($data['tgl_pendaftaran']) ? date('d F Y H:i:s', strtotime($data['tgl_pendaftaran'])) : '-',
            '#penjamin#' => $data['penjamin_nama'],
            '#status_periksa#' => $data['nama_status_periksa'],
            '#poliklinik#' => $data['ruangan_nama'],
            '#jeniskasuspenyakit_nama#' => $data['jeniskasuspenyakit_nama'],
            '#cara_bayar#' => $data['carabayar_nama'],
            '#kelaspelayanan_nama#' => $data['kelaspelayanan_nama'],
            '#jenis_kelamin#' => $data['jk'],
            '#umur#' => $data['umur'],
            '#no_rm#' => $data['no_rekam_medik'],
            '#tanggal_lahir#' => isset($data['tgl_lahir']) ? date('d F Y', strtotime($data['tgl_lahir'])) : '-',
            '#nama_pasien#' => $data['nama_pasien'],
            '#agama#' => $data['carabayar_nama'],
            '#gol_darah#' => $data['golongan_darah'] ?: '-',
            '#pendidikan#' => $data['pendidikan'] ?: '-',
            '#tgl_anamesa#' => isset($data['tgl_anamnesis']) ? date('d F Y', strtotime($data['tgl_anamnesis'])) : '-',

            '#sumber_data#' => $sumber_data,
            '#rujukan#' => $rujukan,
            '#diagnosa_rujukan#' => $data['diagnosa_rujukan'],

            '#dokter_pemeriksa#' => $data['nama_dokter'],
            '#perawat#' => $data['nama_perawat'],

            // pemeriksaan fisik
            '#bb#' => $data['berat_badan'],
            '#tb#' => $data['tinggi_badan'],
            '#td#' => $data['td'],
            '#nadi#' => $data['nadi'],
            '#rr#' => $data['rr'],
            '#suhu#' => $data['suhu'],

            // 3. Riwayat Penyakit
            // A. Riwayat Penyakit Dahulu
            '#riwayat_penyakit#' => $riwayat_penyakit,
            //- Pernah Dirawat
            '#pernah_dirawat#' => $dirawat,
            // - Pernah DiOperasi
            '#pernah_dioperasi#' => $dioperasi,
            // - Obat-obatan yang dikonsumsi
            '#obat_konsumsi#' => $obat_konsumsi,
            // B. Riwayat penyakit keluarga
            '#riwayat_penyakit_keluarga#' => $riwayat_penyakit_keluarga,
            // C. Ketergantungan terhadap
            '#ketergantungan#' => $ketergantungan,
            // D. Riwayat Pekerjaan (apakah berhubungan dengan zat-zat berbahaya?)
            '#riwayat_pekerjaan#' => $riwayat_pekerjaan,
            // E. Riwayat alergi
            '#riwayat_alergi#' => $alergi,
            '#reaksi#' => 'Reaksi : ' . $data['reaksi_alergi'],

            // 4. Riwayat Psikososial dan Spiritual
            // A. Status Psikologi
            '#status_psikologi#' => $status_psikologi,
            // B. Status Sosial
            // Hubungan pasien dan keluarga
            '#hub_pasien#' => $hub_pasien,
            // Kerabat yang bisa dihubungi
            '#kerabat_n#' => 'Nama     &emsp;: ' . $data['nama_kerabat_terdekat'],
            '#kerabat_h#' => 'Hubungan &emsp;: ' . $data['hubungan_kerabat_terdekat'],
            '#kerabat_k#' => 'Kontak   &emsp;: ' . $data['kontak_kerabat_terdekat'],
            // C. Status Ekonomi
            '#status_ekonomi#' => $status_ekonomi,
            // D. Nilai-nilai
            '#nilai_nilai#' => $data['nilai_kebudayaan'],
            // E. Kultural
            '#kultural#' => isset($kultural) ? $kultural : '',

            // 5. Kebutuhan Komunikasi dan Edukasi
            // Kesediaan menerima informasi
            '#kesediaan_informasi#' => $kesediaan_informasi,
            // Kemampuan Membaca
            '#kemampuan_baca#' => $kemampuan_membaca,
            '#bahasa#' => 'Bahasa : &emsp;' . $data['bahasa'],

            // Dibutuhkan penerjemah
            // #penerjemah#
            '#penerjemah#' => $penerjemah,

            // Terdapat hambatan dalam pembelajaran
            // #hambatan#
            '#hambatan_row1#' => $hambatan_row1,
            '#hambatan_row2#' => $hambatan_row2,
            // Kebutuhan edukasi (pilih topik edukasi pada kotak yang tersedia)
            // #kebutuhan_edukasi#
            '#kebutuhan_edukasi_row1#' => $kebutuhann_edukasi_row1,
            '#kebutuhan_edukasi_row2#' => $kebutuhann_edukasi_row2,

            // 6. Resiko Cedera/Jatuh
            // A. Perhatikan cara berjalan pasien saat akan duduk di kursi. Apakah pasien tampat tidak seimbang (sempoyongan)
            // #resiko_cedera_pertama#
            '#resiko_cedera_pertama#' => $resiko_cedera_pertama,
            // B. Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk
            // #resiko_cedera_kedua#
            '#resiko_cedera_kedua#' => $resiko_cedera_kedua,
            // Hasil
            // #hasil_resiko#
            '#hasil_resiko#' => $hasil_resiko,
            // 7. Status Fungsional
            // #aktivitas#
            // #alat_bantu#
            '#aktivitas#' => $aktivitas,
            '#alat_bantu#' => $alat_bantu_jalan,

            // 8. Skala Nyeri
            '#is_nyeri#' => $is_nyeri,
            '#skala_number#' => isset($skala_number) ? $skala_number : '',
            '#ket_nyeri#' => isset($ket_nyeri) ? $ket_nyeri : '',

            // 9. Nutrisi
            '#skrining_gizii#' => $this->renderPartial('_nutrisi', compact('data', 'diagnosa_khusus_row1', 'diagnosa_khusus_row2')),

            // 10. Diagnosa Keperawatan
            '#diagnosa_keperawatan#' => $this->renderPartial('diagnosa_keperawatan', compact('diagnosa_keperawatan')),

            '#tgl_cetak#' => date('d M Y'),
            '#keluhan_utama#' => $data['keluhan_utama'],
            '#keluhan_tambahan#' => $data['keluhan_tambahan'],
            // '#riwayat_penyakit_pasien#' => $data['riwayat_perjalananpasien'],
            // '#lama_sakit#' => $data['lama_sakit'],
            // '#riwayat_penyakit_terdahulu#' => $riwayat_penyakit_terdahulu,
            // '#riwayat_penyakit_keluarga#' => $riwayat_penyakit_keluarga,
            // '#riwayat_imunisasi#' => $fusionImunisasi,
            // '#status_merokok#' => ($data['status_merokok'] == true) ? "Ya" : "Tidak",
            // '#jumlahbatang_rokok#' => ($data['status_merokok'] == true) ? $data['jmlrokok_btgperhari'] : " ",
            // '#obat_sudahdiberikan#' => $data['riwayat_obatygsering'],
            // '#riwayat_makanan#' => $data['riwayat_makanan'],
            // '#riwayat_kelahiran#' => $data['riwayat_kelahiran'],
            // '#riwayat_alergiobat#' => $riwayat_alergiobat,
            // '#keterangan_anamnesa#' => $data['keterangan_anamesa'],
            // '#tgl_skrg#' => date('d-F-Y'),
            // '#resiko_jatuh#' => ($data['is_resikojatuh'] == true) ? "Ya" : "Tidak",
            // '#status_nyeri#' => ($data['is_nyeri'] == true) ? "Ya" : "Tidak",
            // '#lokasi_nyeri#' => ($data['is_nyeri'] == true) ? $data['lokasi_nyeri'] : " ",
            // '#skala_nyeri#' => ($data['is_nyeri'] == true) ? $data['skala_nyeri'] : " ",
            // '#history_riwayat_penyakitDahulu#' => $this->renderPartial('_history_riwayat_penyakitDahulu', [
            //     'data' => $history_riwayat_penyakitDahulu,
            // ]),
            // '#history_riwayat_penyakitKeluarga#' => $this->renderPartial('_history_riwayat_penyakitKeluarga', [
            //     'data' => $history_riwayat_penyakitKeluarga,
            // ]),
            // '#history_riwayat_imunisasi#' => $this->renderPartial('_history_riwayat_imunisasi', [
            //     'data' => $history_riwayat_imunisasi,
            // ]),
            // '#history_riwayat_makanan#' => $this->renderPartial('_history_riwayat_makanan', [
            //     'data' => $history_riwayat_makanan,
            // ]),
            // '#history_riwayat_kelahiran#' => $this->renderPartial('_history_riwayat_kelahiran', [
            //     'data' => $history_riwayat_kelahiran,
            // ]),
            // '#history_riwayat_alergiobat#' => $this->renderPartial('_history_riwayat_alergiobat', [
            //     'data' => $history_riwayat_alergiobat,
            // ]),

        ];
        // Print output
        $print->Output();
    }

    public function getDiagnosa($data)
    {
        if (count($data) == 0) {
            return '';
        }
        $data = Diagnosa::find()->where(['diagnosa_id' => $data])->select(['diagnosa_nama']);
        $data_diagnosa = $data->asArray()->all();
        $return = [];
        foreach ($data_diagnosa as $key => $value) {
            $return[] = $value['diagnosa_nama'];
        }
        if (count($return) > 0) {
            $return = implode(',', $return);
        } else {
            $return = '';
        }
        return $return;
    }

    public function getDiagnosaByName($data)
    {
        if (count($data) == 0) {
            return '';
        }
        $data = Diagnosa::find()->where(['diagnosa_nama' => $data])->select(['diagnosa_nama']);
        $data_diagnosa = $data->asArray()->all();
        $return = [];
        foreach ($data_diagnosa as $key => $value) {
            $return[] = $value['diagnosa_nama'];
        }
        if (count($return) > 0) {
            $return = implode(',', $return);
        } else {
            $return = '';
        }
        return $return;
    }

    public function getObatalkes($data)
    {
        if (count($data) == 0) {
            return '';
        }

        if (!is_array($data)) {
            return $data;
        } else {
            $result = ObatAlkes::find()->where(['obatalkes_id' => $data])->select(['obatalkes_nama']);
            $result = $result->asArray()->all();

            $return = [];
            foreach ($result as $key => $value) {
                $return[] = $value['obatalkes_nama'];
            }

            if (count($return) > 0) {
                $return = implode(',', $return);
            } else {
                $return = '';
            }
            return $return;
        }
    }

    /**
     *
     * @see Fungsi get diagnosa
     * @return array
     *
     */
    public function actionGetDiagnosa($ruangan_id = null)
    {
        try {
            $request = Yii::$app->request;

            $find_diagnosa = $this->getDataDiagnosaRuangan($ruangan_id);

            //cache here

            $data_diagnosa = $find_diagnosa->asArray()->all();

            return [
                'data-diagnosa' => $data_diagnosa,
            ];
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

    /**
     *
     * @see Fungsi get pasien morbiditas
     * @return array
     *
     */
    public function actionGetPasienMorbiditas($ruangan_id = null, $pendaftaran_id = null, $pasien_id = null)
    {
        try {
            $request = Yii::$app->request;
            $get =
                $find = $this->getDataPasienMorbiditas($ruangan_id, $pendaftaran_id);

            //cache here

            $data = $find->asArray()->all();
            if (count($data) < 1) {
                $data = PasienDiagnosa::find()->where(['pasien_id' => $pasien_id])->orderBy(['pasiendiagnosa_id' => SORT_DESC])->asArray()->one();
                if (count($data) > 0) {
                    $diagnosapasien = json_decode($data['diagnosa_pasien'], true);
                    // $diagnosapasien = $data['diagnosa_pasien'];
                    if (isset($diagnosapasien)) {
                        $idDiag = $diagnosapasien['id'];
                        $kodeDiag = $diagnosapasien['kode'];
                        $textDiag = $diagnosapasien['text'];
                    } else {
                        $idDiag = '';
                        $kodeDiag = '';
                        $textDiag = $diagnosapasien['text'];
                    }
                    $result[] = [
                        'kelompokdiagnosa_id' => 9,
                        'kelompokdiagnosa_nama' => 'Diagnosa Kerja',
                        'diagnosa_id' => $idDiag,
                        'diagnosa_kode' => $diagnosapasien['kode'],
                        'diagnosa_nama' => $kodeDiag,
                        'diagnosa_text' => $textDiag,
                        'diagnosa_pasien' => $data['diagnosa_pasien'],
                        'pasien_id' => $pasien_id,
                        'pendaftaran_id' => $pendaftaran_id,
                        'ruangan_id' => $ruangan_id,
                        'tglmorbiditas' => date('Y-m-d H:i:s'),
                        'is_deleted' => false,
                        'is_diagnosakerja' => true,
                        'is_diagnosautama' => false,
                        'is_diagnosamasuk' => false,
                    ];
                    $data = $result;
                }
            }
            return [
                'data' => $data,
            ];
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

    /**
     *
     * @see Fungsi get data diagnosa pasienmorbiditas_t
     * @var params integer ruangan_id = ruangan_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataPasienMorbiditas($ruangan_id = null, $pendaftaran_id = null)
    {
        $diagnosa_utama = DocoConstants::VAR_PM_DU;
        $diagnosa_masuk = DocoConstants::VAR_PM_DM;
        $diagnosa_kerja = DocoConstants::VAR_KELOMPOK_DIAGNOSA_KERJA;

        $condition = [];
        $sql = "
                SELECT
                    pasienmorbiditas_t.pasienmorbiditas_id,
                    pasienmorbiditas_t.tglmorbiditas,
                    kelompokdiagnosa_m.kelompokdiagnosa_nama AS kelompokdiagnosa_nama,
                    klasifikasidiagnosa_m.klasifikasidiagnosa_nama AS klasifikasidiagnosa_nama,
                    diagnosa_pasien->>'kode' AS diagnosa_kode,
                    diagnosa_m.diagnosa_id,
                    diagnosa_pasien->>'text' AS diagnosa_nama,
                    diagnosa_m.diagnosa_namalainnya AS diagnosa_namalainnya,
                    diagnosa_m.diagnosa_katakunci AS diagnosa_katakunci,
                    pasienmorbiditas_t.is_deleted,
                    pasienmorbiditas_t.kelompokdiagnosa_id,
                    pasienmorbiditas_t.pasien_id,
                    CASE
                        WHEN status_diagnosa = {$diagnosa_utama}
                        THEN
                            TRUE
                        ELSE
                            FALSE
                    END AS is_diagnosautama,
                    CASE
                        WHEN status_diagnosa = {$diagnosa_masuk}
                        THEN
                            TRUE
                        ELSE
                            FALSE
                    END AS is_diagnosamasuk,
                    CASE
                        WHEN pasienmorbiditas_t.kelompokdiagnosa_id = {$diagnosa_kerja}
                        THEN
                            TRUE
                        ELSE
                            FALSE
                    END AS is_diagnosakerja
                FROM
                    pasienmorbiditas_t
                JOIN kelompokdiagnosa_m ON kelompokdiagnosa_m.kelompokdiagnosa_id = pasienmorbiditas_t.kelompokdiagnosa_id
                LEFT JOIN diagnosa_m ON diagnosa_m.diagnosa_id = pasienmorbiditas_t.diagnosa_id
                LEFT JOIN klasifikasidiagnosa_m ON klasifikasidiagnosa_m.klasifikasidiagnosa_id = diagnosa_m.klasifikasidiagnosa_id
                WHERE
                    pasienmorbiditas_t.is_deleted = FALSE
                    AND diagnosa_pasien IS NOT NULL
                    AND diagnosa_pasien->>'text' != ''
            ";

        // filter
        if ($ruangan_id) {
            $sql .= " AND pasienmorbiditas_t.ruangan_id = :ruangan_id";
            $condition[':ruangan_id'] = $ruangan_id;
        }

        if ($pendaftaran_id) {
            $sql .= " AND pasienmorbiditas_t.pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $pendaftaran_id;
        }

        $result = PasienMorbiditas::findBySql($sql, $condition);
        return $result;
    }

    /**
     *
     * @see Fungsi insert diagnosa pemeriksaan
     * @return array response
     *
     */
    public function actionCreateDiagnosa()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;

            if ($request->post()) {
                $post = $request->post();
                $data = isset($post['diagnosa_baru']) ? $post['diagnosa_baru'] : [];
                $deleted = isset($post['diagnosa_hapus']) ? $post['diagnosa_hapus'] : null;
                $utamaid = isset($post['utamaid']) ? $post['utamaid'] : null;
                $penyerta = isset($post['penyerta']) ? json_encode(['penyerta' => $post['penyerta']]) : null;
                if ($deleted) {
                    $delete = (new PasienMorbiditas)->delete([
                        'pasienmorbiditas_id' => $deleted
                    ]);
                }
                if ($data) {
                    foreach ($data as $key => $value) {
                        $value = $this->checkMorbiditas($value);

                        $modelPasienMorbiditas = new PasienMorbiditas;
                        $modelPasienMorbiditas->attributes = $value;

                        if (!$modelPasienMorbiditas->save()) {
                            $errors = DocoHelpers::parseError($modelPasienMorbiditas->errors, 'PasienMorbiditasForm');
                            return [
                                'data' => $errors,
                                'status' => 422
                            ];
                        }
                    }
                }
                if (!empty($utamaid) && !empty($penyerta)) {
                    $getMorbiditas = PasienMorbiditas::find()->where(['pasienmorbiditas_id' => $utamaid])->one();
                    $getMorbiditas->additional_data = $penyerta;
                    $getMorbiditas->save();
                }
            }
            $transaction->commit();
            return ['message' => 'Data Berhasil di simpan'];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi delete pasienmorbiditas_t
     * @return array
     *
     */
    public function actionDeleteDiagnosa($id = null)
    {
        try {
            $request = Yii::$app->request;

            $model = PasienMorbiditas::findOne($id);
            if (!empty($model)) {
                if ($model->delete()) {
                    return [
                        'Status' => '200',
                        'message' => 'OK'
                    ];
                } else {
                    return [
                        'message' => 'Gagal hapus',
                        'data' => 'Gagal hapus',
                        'Status' => '422'
                    ];
                }
            } else {
                return [
                    'message' => 'Gagal hapus',
                    'data' => 'Gagal hapus',
                    'Status' => '422'
                ];
            }
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

    /**
     *
     * fungsi cek morbiditas pasien, kasus diagnosa, data pasien
     * @param array data morbiditas pasien
     * @return array data + detail morbiditas pasien
     *
     */
    private function checkMorbiditas($data = [])
    {
        try {
            if ($data) {
                // $detail_pasien = $this->getDataPasien($data['pendaftaran_id'])->asArray()->one();
                if ($data['diagnosa_id'] == '') {
                    $data['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_BARU; // kasus baru
                } else {
                    $sql_morbiditas = "
                        SELECT
                            COUNT(pasienmorbiditas_id)
                        FROM
                            pasienmorbiditas_t
                        WHERE is_deleted = FALSE AND pasien_id = " . $data['pasien_id'] . " AND diagnosa_id = " . $data['diagnosa_id'];
                    $detail_morbiditas = PasienMorbiditas::findBySql($sql_morbiditas)->asArray()->one();

                    if ($detail_morbiditas['count'] == 0) {
                        $data['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_BARU; // kasus baru
                    } else {
                        $data['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_LAMA; // kasus lama
                    }
                }

                // if ($detail_pasien){
                //     $data['golonganumur_id'] = $detail_pasien['golonganumur_id'];
                //     $data['jeniskasuspenyakit_id'] = $detail_pasien['jeniskasuspenyakit_id'];
                // }

                return $data;
            }
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return $data;
        }
    }

    /**
     *
     * @see Fungsi get data inforiwayatpasien_v
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    private function getData($id = null)
    {
        $result = InfoRiwayatPasienView::find();

        if ($id) {
            // $result->where(['pendaftaran_id' => $id]);
            $result->where(['pasien_id' => $id]);
        }

        return $result;
    }

    /**
     *
     * @see Fungsi get data pasienpendaftaran pendaftaran_t
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     * MODIFIED BY Rizal
     */
    private function getDataPasien($id = null)
    {
        $konsulpoli_id = Yii::$app->request->get('konsulpoli_id', null);
        $is_jenis = Yii::$app->request->get('is_jenis', 'rj');

        $model = InfoKunjunganRajal::find()
            ->select([
                'pendaftaran_id',
                'tgl_pendaftaran',
                'no_pendaftaran',
                'no_rekam_medik',
                'pasien_id',
                'nama_pasien',
                'pegawai_id',
                'nama_pegawai',
                'penjamin_id',
                'penjamin_nama',
                'antrian_id',
                'carabayar_id',
                'carabayar_nama',
                'tanggal_lahir',
                'jenis_kelamin',
                'poliklinik',
                'umur',
                'alamat_pasien',
                'status_periksa1 as status_periksa_nama',
                'kelaspelayanan_nama',
                'jeniskasuspenyakit_nama',
                'jeniskasuspenyakit_id',
                'golonganumur_id',
                'pekerjaan_nama',
                'kelaspelayanan_id',
                'catatanpenting_pasien',
                'status_periksa',
                'pasienpulang_id',
                'no_telepon_pasien',
                'jenis',
                'carabayar_kode_warna',
                'status_periksa',
                'jenisidentitas_nama',
                'no_identitas_pasien',
                'ruangan_id',
            ]);
        // filter
        if ($id) {
            $model->andWhere(['pendaftaran_id' => $id]);
        }

        if(strtolower($is_jenis) == 'mcu'){
            if (!empty($konsulpoli_id)) {
                $model->andWhere(['konsulpoli_id' => $this->helper->decrypt($konsulpoli_id)]);
            }
        }elseif(!empty($konsulpoli_id)){
            $model->andWhere(['konsulpoli_id' => PelayananHelpers::decryptId($konsulpoli_id)]);
        }else{
            $model->andWhere(['konsulpoli_id' => null]);
        }

        return $model;
    }

    private function getDataPendaftaran($id = null)
    {
        $model = Pendaftaran::find()
            ->select([
                'rujukan_id',
                'bpjs_id',
            ]);
        if ($id) {
            $model->andWhere(['pendaftaran_id' => $id]);
        }
        return $model;
    }

    private function getDataPendaftaranPasien($pendaftaran_id)
    {
        return Yii::$app->db->createCommand("
            SELECT
              pendaftaran_t.instalasi_id,
              json_build_object(
                'rujukan_id', pendaftaran_t.rujukan_id,
                'bpjs_id', pendaftaran_t.bpjs_id
              ) AS pendaftaran,
              json_build_object(
                'pasien_id', pasien_m.pasien_id,
                'no_rekam_medik', pasien_m.no_rekam_medik,
                'no_identitas_pasien', pasien_m.no_identitas_pasien,
                'nopeserta_bpjs', pasien_m.nopeserta_bpjs
              ) AS pasien,
              json_build_object(
                'pendaftaran_id', pendaftaran_t.pendaftaran_id,
                'bpjs_id', bpjs_t.bpjs_id,
                'nokartuasuransi', bpjs_t.nokartuasuransi
              ) AS bpjs,
              json_build_object(
                'ketergantungan_jenis', anamnesa_t.ketergantungan_jenis,
                'riwayat_penyakit_keluarga_list', anamnesa_t.riwayat_penyakit_keluarga_list,
                'status_ekonomi', anamnesa_t.status_ekonomi,
                'alergi', anamnesa_t.alergi
              ) AS anamnesa,
              json_build_object(
                'a_diag_utama', soaprj_t.a_diag_utama::text
              ) AS soaprj
            FROM pendaftaran_t
            JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pasien_m.is_deleted IS FALSE
            LEFT JOIN (
              SELECT a.pendaftaran_id, a.bpjs_id, a.nokartuasuransi
              FROM bpjs_t a
              ORDER BY a.bpjs_id DESC
            ) AS bpjs_t ON bpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN (
              SELECT a.pendaftaran_id, a.ketergantungan_jenis, a.riwayat_penyakit_keluarga_list, a.status_ekonomi, a.alergi
              FROM anamnesa_t a
              WHERE a.is_deleted IS FALSE
            ) AS anamnesa_t ON anamnesa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            LEFT JOIN (
              SELECT a.pendaftaran_id, b.kelompokpegawai_id, a.a_diag_utama
              FROM soaprj_t a
              JOIN pegawai_m b ON b.pegawai_id = a.pegawai_id AND b.kelompokpegawai_id = :kelompokpegawai_id
              WHERE a.is_deleted IS FALSE
            ) AS soaprj_t ON soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
            WHERE pendaftaran_t.pendaftaran_id = :pendaftaran_id
        ")->bindValues([
            ':pendaftaran_id' => $pendaftaran_id,
            ':kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER
        ])->queryOne();
    }

    /**
     *
     * @see Fungsi get data penunjang
     * @var params integer id = primary key pendaftaran_id
     * @var params integer id = ruangan_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataPenunjang($id = null, $ruangan_id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    ruangan_m.ruangan_nama as ruangan_nama
                FROM
                    pasienmasukpenunjang_t
                LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = pasienmasukpenunjang_t.ruangan_id
                WHERE
                    pasienmasukpenunjang_t.is_deleted = FALSE
            ";

        // filter
        if ($id) {
            $sql .= " AND pasienmasukpenunjang_t.pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        if ($ruangan_id) {
            $sql .= " AND pasienmasukpenunjang_t.ruangan_id = :ruangan_id";
            $condition[':ruangan_id'] = $ruangan_id;
        }

        $result = PasienMasukPenunjang::findBySql($sql, $condition);
        return $result;
    }

    /**
     *
     * @see Fungsi get data diagnosa per ruangan
     * @var params integer id = ruangan_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataDiagnosaRuangan($ruangan_id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    d.diagnosa_id,
                    d.diagnosa_nama,
                    d.diagnosa_kode,
                    d.diagnosa_namalainnya,
                    d.diagnosa_katakunci
                FROM
                    kasuspenyakitruangan_mp kpr
                LEFT JOIN kasuspenyakitdiagnosa_mp kpd ON kpd.jeniskasuspenyakit_id = kpr.jeniskasuspenyakit_id
                JOIN diagnosa_m d ON d.diagnosa_id = kpd.diagnosa_id
                WHERE
                    kpr.is_deleted = FALSE
            ";

        if ($ruangan_id) {
            $sql .= " AND kpr.ruangan_id = :ruangan_id";
            $condition[':ruangan_id'] = $ruangan_id;
        }

        $result = KasusPenyakitRuangan::findBySql($sql, $condition);
        return $result;
    }

    /**
     *
     * @see Fungsi create rujukan pasien pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionCreateRujukanPasien()
    {
        try {
            $request = Yii::$app->request;
            $model = new PasienDirujukKeluar;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PasienDirujukKeluarForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * @see Fungsi get pasien
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetRujukanPasien()
    {
        try {
            $request = Yii::$app->request;

            $query = $this->getDataRujukanPasien($request->get('id'), $request->get('id_ruangan'));

            return [
                'data' => $query->one()
            ];
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

    /**
     *
     * @see Fungsi get data rujukan pasien
     * @var params integer id = primary key pendaftaran_id
     * @var params integer id = id_ruangan
     * @return array, activeQueryRecords
     *
     */
    public function getDataRujukanPasien($id = null, $id_ruangan = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pasiendirujukkeluar_t.pasiendirujukkeluar_id,
                    pasiendirujukkeluar_t.tgldirujuk,
                    pasiendirujukkeluar_t.sampaidengan,
                    pasiendirujukkeluar_t.nosuratrujukan,
                    pasiendirujukkeluar_t.pegawai_id,
                    pasiendirujukkeluar_t.rujukankeluar_id,
                    pasiendirujukkeluar_t.kepadayth,
                    pasiendirujukkeluar_t.dirujukkebagian,
                    pasiendirujukkeluar_t.catatandokterperujuk,
                    pasiendirujukkeluar_t.hasilpemeriksaan_ruj,
                    pasiendirujukkeluar_t.alasandirujuk,
                    pasiendirujukkeluar_t.diagnosasementara_ruj,
                    pasiendirujukkeluar_t.lainlain_ruj,
                    ruangan_m.ruangan_nama as ruangan_nama
                FROM
                    pasiendirujukkeluar_t
                LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = pasiendirujukkeluar_t.ruanganasal_id
                WHERE
                    pasiendirujukkeluar_t.is_deleted = FALSE
            ";

        // filter
        if ($id) {
            $sql .= " AND pasiendirujukkeluar_t.pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        if ($id_ruangan) {
            $sql .= " AND pasiendirujukkeluar_t.ruanganasal_id = :id_ruangan";
            $condition[':id_ruangan'] = $id_ruangan;
        }

        $result = PasienDirujukKeluar::findBySql($sql, $condition);

        return $result;
    }

    /**
     *
     * Fungsi update anamnesa pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionUpdateRujukanPasien($id)
    {
        try {
            $request = Yii::$app->request;
            $model = PasienDirujukKeluar::findOne($id);

            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->update()) {
                    return ['message' => 'Data Berhasil di ubah'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PasienDirujukKeluarForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * Fungsi get pembebasan tarif
     * @return array, message/model validate errors
     *
     */
    public function actionGetPembebasanTarif()
    {
        try {

            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id');

            $condition = [];
            $query = '
                SELECT
                    pembebasantarif_t.*,
                    pembebasantarif_t.pendaftaran_id as pendaftaran_id,
                    pendaftaran_t.tgl_pendaftaran as tgl_pendaftaran
                FROM pembebasantarif_t
                LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = pembebasantarif_t.pendaftaran_id
                WHERE
                    pembebasantarif_t.is_deleted = FALSE
            ';
            // filter
            if ($pendaftaran_id) {
                $query .= " AND pembebasantarif_t.pendaftaran_id = :pendaftaran_id";
                $condition[':pendaftaran_id'] = $pendaftaran_id;
            }
            $query .= " ORDER BY pembebasantarif_t.created_date";
            $result = PembebasanTarif::findBySql($query, $condition);

            return [
                'data' => $result->all()
            ];
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

    /**
     *
     * @see Fungsi create pembebasan tarif
     * @return array, message/model validate errors
     *
     */
    public function actionCreatePembebasanTarif()
    {
        try {
            $request = Yii::$app->request;
            $model = new PembebasanTarif;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PembebasanTarifForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * Fungsi update pembebasan tarif pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionUpdatePembebasanTarif($id)
    {
        try {
            $request = Yii::$app->request;
            $model = PembebasanTarif::findOne($id);

            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->update()) {
                    return ['message' => 'Data Berhasil di ubah'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PembebasanTarifForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
     *
     * @see Fungsi create pembebasan tarif
     * @return array, message/model validate errors
     *
     */
    public function actionGetPembebasanTarifTotalTagihan()
    {

        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $query = new Query;
            $query->select('rinciantagihapasien_v.tarif_tindakan')
                ->from('rinciantagihapasien_v')
                ->andWhere('rinciantagihapasien_v.pendaftaran_id = :id', [':id' => $id]);
            $sum_total_tindakan = $query->sum('tarif_tindakan');

            $query = new Query;
            $query->select('pembebasantarif_t.total_pembebasantarif')
                ->from('pembebasantarif_t')
                ->andWhere('pembebasantarif_t.is_deleted = FALSE')
                ->andWhere('pembebasantarif_t.pendaftaran_id = :id', [':id' => $id]);
            $sum_total_pembebasantarif = $query->sum('total_pembebasantarif');

            $total_tindakan = is_null($sum_total_tindakan) ? 0 : $sum_total_tindakan;
            $total_pembebasantarif = is_null($sum_total_pembebasantarif) ? 0 : $sum_total_pembebasantarif;

            $res = $total_tindakan - $total_pembebasantarif;
            return [
                'data' => $res
            ];
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

    /**
     *
     * @see Fungsi create konsul poli
     * @return array response
     *
     */
    public function actionCreateKonsulpoli()
    {
        $jwt = Yii::$app->jwt;
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if ($request->post()) {
                $data = $request->post();
                // untuk kebutuhan validasi api

                $modelPayload = new ParamModel;
                // $modelPayload->scenario = 'konsul-poli';
                $ruangan_id = isset($data['ruangan_id']) ? $data['ruangan_id'] : null;
                $pegawai_id = isset($data['pegawai_id']) ? $data['pegawai_id'] : null;
                $modelPayload->ruangan_id = $ruangan_id;
                $modelPayload->pegawai_id = $pegawai_id;

                if ($modelPayload->validate()) {
                    $konfig_system = Cache::getKonfigSistem();
                    $modelKonsulpoli = new Konsulpoli;
                    $modelKonsulpoli->attributes = $data;

                    $ruangan_id = isset($data['jadwaldokter_id']) ? $data['jadwaldokter_id'] : null;
                    $tgl_konsulpoli = isset($data['tgl_konsulpoli']) ? $data['tgl_konsulpoli'] : date('Y-m-d H:i:s');
                    $pendaftaran_id = isset($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null;
                    $pasien_id = isset($data['pasien_id']) ? $data['pasien_id'] : null;
                    $asalpoliklinikkonsul_id = isset($data['asalpoliklinikkonsul_id']) ? $data['asalpoliklinikkonsul_id'] : null;
                    $jadwaldokter_id = isset($data['jadwal_id']) ? $data['jadwal_id'] : null;

                    $modelKonsulpoli->ruangan_id = $ruangan_id;
                    $modelKonsulpoli->jadwaldokter_id = null;

                    //add value status periksa from constanta
                    $modelKonsulpoli->status_periksa = DocoConstants::STATUS_PERIKSA_ANTRIAN_POLI;
                    $modelKonsulpoli->tgl_konsulpoli = date('Y-m-d H:i:s', strtotime($tgl_konsulpoli));

                    // status approve menyesuaikan dengan konfig dari konfig system k RPP-775
                    $pendaftaran = Pendaftaran::find()->select(['pendaftaran_t.pendaftaran_id', 'pendaftaran_t.tgl_pendaftaran', 'pendaftaran_t.carabayar_id', 'bt.bpjs_id', 'bt.nosep', 'bt.nokartuasuransi'])
                                    ->leftJoin('bpjs_t bt','bt.pendaftaran_id = pendaftaran_t.pendaftaran_id AND bt.is_deleted IS FALSE')
                                    ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])
                                    ->asArray()->one();
                    if (!empty($pendaftaran)) {
                        if (date('Y-m-d', strtotime($pendaftaran['tgl_pendaftaran'])) == date('Y-m-d', strtotime($modelKonsulpoli->tgl_konsulpoli)) && ArrayHelper::getValue($konfig_system, 'is_auto_approve_konsul', false)) {
                            $modelKonsulpoli->status_approve = DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI;
                        } else {
                            $modelKonsulpoli->status_approve = DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES;
                        }
                    }
                    // lepas validasi konsul dengan  ruangan yang sama << 05/04/2022

                    // if ($modelKonsulpoli->ruangan_id === $jwt->ruangan_id) {
                    //     return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    //         'text' => 'Anda tidak bisa konsul ke Poli yang sama.'
                    //     ]);
                    // }

                    // cek jika sudah konsul ke poli yang sama
                    $cekKonsulExist = Konsulpoli::find()
                                ->where([
                                    'pendaftaran_id' => $pendaftaran_id,
                                    'tgl_konsulpoli' => $modelKonsulpoli->tgl_konsulpoli
                                ])->asArray()->all();

                    if (!empty($cekKonsulExist)) {
                        $ruanganId = [];
                        $dokter_konsul = [];
                        foreach ($cekKonsulExist as $key => $value) {
                            $statusApprove = isset($value['status_approve']) ? $value['status_approve'] : null;
                            $statusPeriksa = isset($value['status_periksa']) ? $value['status_periksa'] : null;
                            // VAR_STATUS_DAFTAR_OL_DITOLAK
                            if ($statusApprove != DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK
                                    && $statusPeriksa != DocoConstants::STATUS_PERIKSA_BTL_KNSL) {
                                $ruanganId[] = $value['ruangan_id'];
                                $dokter_konsul[] = $value['pegawai_id'];
                            }
                        }

                        // validasi untuk ruangan yang sama dengan dokter yang sama
                        if (in_array($ruangan_id, $ruanganId)) {
                            if(in_array($pegawai_id, $dokter_konsul)){
                                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                    'text' => 'Anda tidak bisa konsul ke Poli yang sama dengan Dokter yang sama ditanggal yang sama.'
                                ]);
                            }

                        }
                    }

                    if (date('Y-m-d', strtotime($pendaftaran['tgl_pendaftaran'])) == date('Y-m-d', strtotime($modelKonsulpoli->tgl_konsulpoli))) {
                        $konfigAntrian = KonfigAntrian::find()->where([
                            'ruangan_id' => $ruangan_id,
                            'pegawai_id' => $pegawai_id
                        ])->one();
                        $konfigantrian_id = ArrayHelper::getValue($konfigAntrian, 'konfigantrian_id', null);

                        //add to antrian
                        $modelAntrianKonsul = new AntrianKonsul;
                        $modelAntrianKonsul->pendaftaran_id = $pendaftaran_id;
                        $modelAntrianKonsul->pegawai_id = $pegawai_id;
                        $modelAntrianKonsul->pasien_id = $pasien_id;
                        $modelAntrianKonsul->tgl_antrian = $tgl_konsulpoli;
                        $modelAntrianKonsul->ruangan_id = $ruangan_id;
                        $modelAntrianKonsul->jadwaldokter_id = $jadwaldokter_id;
                        $modelAntrianKonsul->is_konsulpoli = true;
                        $modelAntrianKonsul->jenisantrian_id = 312;
                        $modelAntrianKonsul->is_online = true;
                        $modelAntrianKonsul->konfigantrian_id = $konfigantrian_id;
                        $modelAntrianKonsul->carabayar_id = ArrayHelper::getValue($pendaftaran, 'carabayar_id');

                        $isBpjs = isset($pendaftaran->carabayar_id) && $pendaftaran->carabayar_id == DocoConstants::CARA_BAYAR_BPJS ? true : false;
                        $getEstimasi = PendaftaranOnline::getEstimasiByJadwal($jadwaldokter_id,date('Y-m-d'),$isBpjs);
                        $modelAntrianKonsul->estimasidilayani = ArrayHelper::getValue($getEstimasi,'estimasidilayani_inmilisecond');
                        $modelAntrianKonsul->slot_sequence = ArrayHelper::getValue($getEstimasi,'sequence');

                        $total_kuota = JadwalDokter::find()
                            ->select([
                                'jadwaldokter_m.kuota_online',
                            ])
                            ->where(['jadwaldokter_m.is_deleted' => FALSE])
                            ->andWhere(['jadwaldokter_id' => $jadwaldokter_id])
                            ->one();

                        $kuota_booked = Antrian::find()
                            ->where(['jadwaldokter_id' => $jadwaldokter_id])
                            ->andWhere(['=', new \yii\db\Expression('(tgl_antrian::date)'), $tgl_konsulpoli])
                            ->count();

                        if ($total_kuota->kuota_online <= 0 || ($total_kuota->kuota_online - $kuota_booked) <= 0) {
                            $modelAntrianKonsul->skip_kuota = true; //jika tidak diisi, default false
                        }

                        if ($modelAntrianKonsul->save()) {
                            $modelKonsulpoli->antrian_id = $modelAntrianKonsul->antrian_id;
                        } else {
                            $transaction->rollBack();
                            return [
                                'data' => 'Antrian Konsul Gagal Dibuat',
                                'status' => 422
                            ];
                        }
                    }

                    if ($modelKonsulpoli->save()) {
                        $transaction->commit();
                        $message = 'Rencana kontrol berhasil dibuat';

                        if (date('Y-m-d', strtotime($pendaftaran['tgl_pendaftaran'])) < date('Y-m-d', strtotime($modelKonsulpoli->tgl_konsulpoli))) {
                            if ($pendaftaran['carabayar_id'] == DocoConstants::CARA_BAYAR_BPJS && !empty($pendaftaran['nosep'])) {
                                $surat_kontrol = $this->createSuratKontrol($pendaftaran, $modelKonsulpoli, $jadwaldokter_id);
                                $error_message_rencana_kontrol = ArrayHelper::getValue($surat_kontrol, 'error_message_rencana_kontrol', []);
                                $message = empty($error_message_rencana_kontrol) ? 'Rencana kontrol dan surat rencana kontrol berhasil dibuat' : $message;
                            }
                        }

                        return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                            'status' => 200,
                            'title' => 'Success',
                            'text' => $message,
                            'data' => compact('error_message_rencana_kontrol')
                        ], 200);

                    } else {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($modelKonsulpoli->errors, 'KonsulpoliForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                } else {
                    return [
                        'data' => $modelPayload->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /*
     * Fungsi view pembebasan tarif pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionViewPembebasanTarif($id)
    {
        $request = Yii::$app->request;

        $model = new PembebasanTarif;
        // $query = PembebasanTarif::findOne($id);
        $query = PembebasanTarif::find()
            ->select('pembebasantarif_t.*')
            ->where(['pembebasantarif_t.pembebasantarif_id' => $id])
            ->one();
        return $query;
    }

    /**
     *
     * Fungsi delete pembebasan tarif pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionDeletePembebasanTarif($id)
    {
        try {
            $request = Yii::$app->request;
            $model = PembebasanTarif::findOne($id);

            if ($model->delete()) {
                return ['text' => 'Data Berhasil di hapus'];
            } else {
                return [
                    'data' => 'Gagal hapus',
                    'status' => 422
                ];
            }
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

    /*======================================
    =            riwayat pasien            =
    ======================================*/
    private function getDataRiwayatPasien($pasien_id, $pendaftaran_id = null)
    {
        $diagnosa_utama = 2;

        $query = "
            SELECT
                pt.pasien_id,
                pt.pendaftaran_id,
                rm.ruangan_id,
                pt.no_pendaftaran,
                pt.tgl_pendaftaran AS tgl_pendaftaran,
                pm.nama_pegawai AS dokter_pemeriksa,
                rm.ruangan_nama AS poliklinik,
                pmr.diagnosa_nama,
                rk.rumahsakit_rujukan
            FROM
                pendaftaran_t pt
            LEFT JOIN (
                SELECT DISTINCT
                    pegawai_id,
                    nama_pegawai
                FROM
                    pegawai_m
            ) pm ON pm.pegawai_id = pt.pegawai_id
            LEFT JOIN (
                SELECT DISTINCT
                    ruangan_id,
                    ruangan_nama
                FROM
                    ruangan_m
            ) rm ON rm.ruangan_id = pt.ruangan_id
            LEFT JOIN (
                SELECT DISTINCT
                    md.pasien_id,
                    md.diagnosa_nama
                FROM
                    (
                        SELECT DISTINCT
                            pmt.pasien_id,
                            dm.diagnosa_nama
                        FROM
                            pasienmorbiditas_t pmt
                        JOIN diagnosa_m dm ON dm.diagnosa_id = pmt.diagnosa_id
                        WHERE
                            pmt.pasien_id = {$pasien_id} AND pmt.kelompokdiagnosa_id = {$diagnosa_utama}
                    ) md
            ) pmr ON pmr.pasien_id = pt.pasien_id
            LEFT JOIN (
                SELECT DISTINCT
                    pdrk.pasiendirujukkeluar_id,
                    pdrk.rumahsakit_rujukan
                FROM
                    (
                        SELECT
                            pdk.pasiendirujukkeluar_id,
                            rk.rumahsakit_rujukan
                        FROM
                            pasiendirujukkeluar_t pdk
                        JOIN rujukankeluar_m rk ON rk.rujukankeluar_id = pdk.rujukankeluar_id
                    ) pdrk
            ) rk ON rk.pasiendirujukkeluar_id = pt.rujukan_id
            WHERE
                pt.pasien_id = {$pasien_id} ORDER BY pt.pendaftaran_id DESC
        ";

        $result = Pendaftaran::findBySql($query);

        return $result;
    }
    /*=====  End of riwayat pasien  ======*/


    /*===============================================
    =            Section tindakan & bmhp            =
    ===============================================*/
    /**
     *
     * @see Fungsi insert tindakan
     * @return array response
     *
     */
    public function actionCreateTindakan()
    {
        // Declare connection

    }

    /**
     *
     * @see Fungsi create pemeriksaan fisik pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionCreateFisik()
    {
        try {
            $request = Yii::$app->request;
            $groupEmployee = Pegawai::find()
            ->select(['kelompokpegawai_id', 'spesialis_id'])
            ->andWhere(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])
            ->asArray()
            ->one();

            if ($data_post = $request->post()) {
                $model = new PemeriksaanFisik;
                $pemeriksaanFisikId = ArrayHelper::getValue($data_post, 'pemeriksaanfisik_id');
                $pemeriksaanSpesialis = ArrayHelper::getValue($data_post, 'pemeriksaan_spesialis');
                $konsulPoliId = ArrayHelper::getValue($data_post, 'konsulpoli_id');
                if(empty($konsulPoliId)) {
                    $konsulPoliId = ArrayHelper::getValue($data_post, 'konsulpoliId');
                }

                if(!is_numeric($konsulPoliId)) {
                    $konsulPoliId = DocoHelpers::decrypt($konsulPoliId);
                }

                unset($model->pemeriksaanfisik_id);
                if (!empty($pemeriksaanFisikId)) {
                    $model = PemeriksaanFisik::find()->where([
                        'pemeriksaanfisik_id' => $data_post['pemeriksaanfisik_id']
                    ])->one();
                }
                
                if($groupEmployee['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER ){
                    $model->dokter_id = Yii::$app->jwt->user->pegawai_id;
                }
                $model->gcs_eye = isset($data_post['gcs_eye']) ? $data_post['gcs_eye'] : null;
                $model->gcs_verbal = isset($data_post['gcs_verbal']) ? $data_post['gcs_verbal'] : null;
                $model->gcs_motorik = isset($data_post['gcs_motorik']) ? $data_post['gcs_motorik'] : null;
                $model->gcs_hasil_metode = isset($data_post['gcs_hasil_metode']) ? $data_post['gcs_hasil_metode'] : null;
                $model->attributes = $data_post;
                $model->is_kapitis = ArrayHelper::getValue($data_post, 'gcs_is_kapitis');
                $model->pemeriksaan_spesialis = $pemeriksaanSpesialis;
                $model->konsulpoli_id = $konsulPoliId;
                if ($model->validate() && $model->save()) {
                    MonitoringTtvLogic::feedData($data_post, DocoConstants::ASESMEN_MEDIS, DocoConstants::INSTALASI_RAWAT_JALAN);

                    return [
                        'status' => 200,
                        'message' => 'Data Berhasil di simpan',
                        'pemeriksaanfisik_id' => $model->pemeriksaanfisik_id
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'PemeriksaanFisikForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     *
     * @see Fungsi simpan periksa tubuh via pemeriksaan fisik - pemeriksaan rajal
     * @return array, message/model validate errors
     *
     */
    public function actionSavePeriksatubuh()
    {
        try {
            $request = Yii::$app->request;
            $data_post = $request->post('data');
            $pendaftaran_id = $request->post('pendaftaran_id');
            $pasien_id = $request->post('pasien_id');
            $pemeriksaanfisik_id = $request->post('pemeriksaanfisik_id');

            $data_bagiantubuh = $data_post;

            $model = new PeriksaTubuh;
            if ($pemeriksaanfisik_id) {
                $count = 0;
                $data_insert_periksatubuh = [];
                foreach ($data_bagiantubuh as $key => $value) {
                    $data_insert_periksatubuh_temp = [];
                    $data_insert_periksatubuh_temp['pemeriksaanfisik_id'] = $pemeriksaanfisik_id;
                    $data_insert_periksatubuh_temp['bagiantubuh_id'] = $value['bagiantubuh_id'];
                    $data_insert_periksatubuh_temp['catatan_tubuh'] = $value['catatan_tubuh'];
                    $data_insert_periksatubuh_temp['koordinat_y'] = $value['koordinat_y'];
                    $data_insert_periksatubuh_temp['koordinat_x'] = $value['koordinat_x'];
                    $data_insert_periksatubuh_temp['counters'] = $value['counters'];
                    $data_insert_periksatubuh[] = $data_insert_periksatubuh_temp;
                }

                if ($data_insert_periksatubuh) {
                    $list_columns = [
                        'pemeriksaanfisik_id',
                        'bagiantubuh_id',
                        'catatan_tubuh',
                        'koordinat_y',
                        'koordinat_x',
                        'counters',
                    ];
                    Yii::$app->db->createCommand("
                            DELETE FROM periksatubuh_t WHERE pemeriksaanfisik_id = $pemeriksaanfisik_id
                        ")->execute();
                    Yii::$app->db->createCommand()
                        ->batchInsert(PeriksaTubuh::tableName(), $list_columns, $data_insert_periksatubuh)
                        ->execute();
                }

                return [
                    'message' => 'Data Berhasil di simpan',
                    'status' => 200
                ];
            } else {
                return [
                    'message' => 'Data fisik kosong',
                    'status' => 500
                ];
            }
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

    // Get pemeriksaan fisik
    public function actionGetPemeriksaanFisik($pendaftaranId, $pasienId)
    {
        try {
            $model = PemeriksaanFisik::find()->where([
                'pendaftaran_id' => $pendaftaranId,
                'pasien_id' => $pasienId
            ])->one();
            
            $anatomiTubuh = [];
            if (!empty($model->pemeriksaanfisik_id)) {
                $anatomiTubuh = PeriksaTubuh::find()->select([
                    'periksatubuh_t.bagiantubuh_id',
                    'periksatubuh_t.catatan_tubuh',
                    'periksatubuh_t.koordinat_x',
                    'periksatubuh_t.koordinat_y',
                    'periksatubuh_t.counters',
                    'bagian' => 'bagiantubuh_m.namabagtubuh'
                ])->joinWith([
                    'bagianTubuh' => function ($query) {
                        $query->select([
                            'bagiantubuh_m.bagiantubuh_id'
                        ]);
                    }
                ])->where([
                    'pemeriksaanfisik_id' => $model->pemeriksaanfisik_id
                ])->asArray()->all();
            }
            $modelAnamnesa = Anamnesa::find()
                ->select([
                    'pendaftaran_id',
                    'riwayat_penyakit_nama as riwayat_penyakit_dahulu',
                    'riwayat_penyakit_keluarga_list as riwayat_penyakit_keluarga',
                    'keluhan_utama',
                    'berat_badan as beratbadan_kg',
                    'tinggi_badan as tinggibadan_cm',
                    'td as tekanandarah',
                    'nadi as detaknadi',
                    'rr as pernapasan',
                    'suhu as suhutubuh',
                    'pegawaiperawat_id',
                ])
                ->andWhere([
                    'pendaftaran_id' => $pendaftaranId,
                    'pasien_id' => $pasienId
                ])
                ->asArray()
                ->one();
                
            $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');
            $pasien = Pasien::find()
                ->select(['no_rekam_medik', 'nama_pasien', 'jeniskelamin', 'tanggal_lahir'])
                ->where(['pasien_id' => $pasienId])
                ->one();

            return [
                'model' => $model,
                'model_anamnesa' => $modelAnamnesa,
                'anatomi_tubuh' => $anatomiTubuh,
                'enable_pulang' => $enable_pulang,
                'pasien' => $pasien,
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @controller actionExportPdfDiagnosa
     * @attribute #data_diagnosa_anamnesa# => print
     **/
    public function actionExportPdfDiagnosa()
    {
        $request = Yii::$app->request;
        $title = Yii::t('app', 'Pemeriksaan diagnosa');

        try {
            $ruangan_id = $request->get('ruangan_id');
            $pendaftaran_id = $request->get('pendaftaran_id');

            $data = $this->getDataPasienMorbiditas($ruangan_id, $pendaftaran_id)->asArray()->all();
            $data_pasien = $this->getDataPasien($pendaftaran_id)->asArray()->one();
            $header = [];
            $print = new DocoPrint();
            $print->attributes = [
                '#poliklinik#' => $data_pasien['poliklinik'],
                '#jeniskelamin#' => $data_pasien['jenis_kelamin'],
                '#no_pendaftaran#' => $data_pasien['no_pendaftaran'],
                '#no_rekam_medik#' => $data_pasien['no_rekam_medik'],
                '#nama_pasien#' => $data_pasien['nama_pasien'],
                '#tanggal_lahir#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($data_pasien['tanggal_lahir'])), false, false),
                '#data_diagnosa_anamnesa#' => $this->renderPartial('index_diagnosa', [
                    'data' => $data,
                ]),
            ];
            $print->Output();

            // return true;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionExportPdfPeriksaFisik
     * @attribute #poliklinik# => Untuk Menampilkan nama Poliklinik
     * @attribute #no_pendaftaran# => Untuk Menampilkan No Pendaftaran
     * @attribute #no_rm# => Untuk Menampilkan No Rekam Medik
     * @attribute #nama_pasien# => Untuk Menampilkan nama pasien
     * @attribute #jenis_kelamin# => Untuk Menampilkan nama pasien
     * @attribute #tanggal_lahir# => Untuk Menampilkan Jenis Kelamin
     * @attribute #cara_bayar# => Untuk Menampilkan cara Bayar
     * @attribute #penjamin# => Untuk Menampilkan Penjamin
     * @attribute #dokter_pemeriksa# => Untuk Menampilkan Dokter pemeriksa
     * @attribute #perawat# => Untuk Menampilkan Nama Perawat
     * @attribute #tanggal_periksa# => Untuk Menampilkan Tanggal periksa
     * @attribute #keadaan_umum# => Untuk Menampilkan Keadaan Umum
     * @attribute #tekanan_darah# => Untuk Menampilkan Tekanan Darah
     * @attribute #klasifikasitekanandarah# => Untuk Menampilkan Klasifikasi Tekanan Darah
     * @attribute #mean_arteri_preassure# => Untuk Menampilkan Mean Arteri Preassure
     * @attribute #detak_nadi# => Untuk Menampilkan Detak Nadi
     * @attribute #denyut_jantung# => Untuk Menampilkan Denyut Jantung
     * @attribute #pernafasan# => Untuk Menampilkan Pernafasan
     * @attribute #suhu_tubuh# => Untuk Menampilkan Suhu Tubuh
     * @attribute #tinggi_badan# => Untuk Menampilkan Tinggi Badan
     * @attribute #berat_badan# => Untuk Menampilkan Berat Badan
     * @attribute #massa_index_tubuh# => Untuk Menampilkan massa index tubuh
     * @attribute #kelainan_tubuh# => Untuk Menampilkan Kelainan pada bagian tubuh
     * @attribute #inspeksi# => Untuk Menampilkan Inspeksi
     * @attribute #palpasi# => Untuk Menampilkan Palpasi
     * @attribute #perkusi# => Untuk Menampilkan Perkusi
     * @attribute #auskultasi# => Untuk Menampilkan Auskultasi
     * @attribute #gcs_eye# => Untuk Menampilkan GCS Eye
     * @attribute #metodegcs_eye# => Untuk Menampilkan metode GCS Eye
     * @attribute #nilaigcs_eye# => Untuk Menampilkan nilai GCS Eye
     * @attribute #gcs_verbal# => Untuk Menampilkan GCS Verbal
     * @attribute #metodegcs_verbal# => Untuk Menampilkan metode GCS Verbal
     * @attribute #nilaigcs_verbal# => Untuk Menampilkan nilai GCS Verbal
     * @attribute #gcs_motorik# => Untuk Menampilkan GCS Motorik
     * @attribute #nilaigcs_motorik# => Untuk Menampilkan nilai GCS Motorik
     * @attribute #gcs_is_kapitis# => Untuk Menampilkan is kapitis
     * @attribute #gcs_kategori# => Untuk Menampilkan gsc nama
     * @attribute #hasil_metode_gcs# => Untuk Menampilkan Hasil Metode GCS
     * @attribute #pernapasan_gerakan# => Untuk Menampilkan List PERNAPASAN GERAKAN DADA
     * @attribute #jalan_nafas# => Untuk Menampilkan List JALAN NAFAS DAN PERNAFASAN
     * @attribute #sirkulasi# => Untuk Menampilkan List SIRKULASI
     * @attribute #gambar_anatomi# => Untuk Menampilkan Gambar Anatomi Tubuh
     * @attribute #list_tabel_anatomi# => Untuk Menampilkan List bagian anatomi tubuh
     * @attribute #tanggal_pemeriksaan# => Untuk Menampilkan Tanggal Pemeriksaan
     * @attribute #tgl_cetak# => Untuk Menampilkan Tanggal saat ini dicetak
     * @attribute #imt_kategori# => Untuk Menampilkan imt kategori / bmi_definisi
     * @attribute #umur# => Untuk Menampilkan umur Pasien
     * @attribute #kelaspelayanan_nama# => Untuk Menampilkan kelas pelayanan  Pasien
     * @attribute #tgl_pendaftaran# => Untuk Menampilkan tgl pendaftaran  Pasien
     * @attribute #jeniskasuspenyakit_nama# => Untuk Menampilkan Jenis Penyakit  Pasien
     * @attribute #status_periksa# => Untuk Menampilkan Status Periksa  Pasien
     **/
    public function actionExportPdfPeriksaFisik($pemeriksaanfisik_id)
    {
        // Get pemeriksaan fisik
        $result = RiwayatPemeriksaanFisik::find()->where(['pemeriksaanfisik_id' => $pemeriksaanfisik_id])->asArray()->one();
        $resultForTable = RiwayatPemeriksaanFisik::find()->where(['pemeriksaanfisik_id' => $pemeriksaanfisik_id])->asArray()->all();
        
        $checkData  = PemeriksaanFisik::find()
                        ->select([
                            'pendaftaran_id',
                            'pemeriksaan_spesialis'
                        ])
                        ->andWhere(['pemeriksaanfisik_id' => $pemeriksaanfisik_id])
                        ->asArray()->one();
        
        if (!empty($checkData['pemeriksaan_spesialis'])) {
            $toArray = json_decode($checkData['pemeriksaan_spesialis'],true);
            if (is_array($toArray)) {
                $no = 0;
                $keyReport = null;
                foreach ($toArray as $key => $value) {
                    if ($no == 2) {
                        $keyReport = $key;
                    }
                    $no++;
                }

                if (!empty($keyReport)) {
                    return [
                        'pendaftaran_id' => isset($checkData['pendaftaran_id']) ? $checkData['pendaftaran_id'] : null,
                        'key_report' => $keyReport
                    ];
                }
                
            }
        }

        // $image = Yii::$app->urlManagerFrontend->createUrl('') . "media/img/img-pemeriksaan/bagian_tubuh.jpg";
        $image = Yii::$app->getBasePath()."/web/media/img/img-pemeriksaan/bagian_tubuh.jpg";

        // Check model
        if (!empty($result)) {
            // Header
            $header = array(
                Yii::t('app', "Pemeriksaan Fisik") => 'PEMERIKSAAN FISIK',
            );

            // Print
            $print = new DocoPrint();

            // Cek berat badan
            if (isset($result['beratbadan_kg']) && $result['beratbadan_kg'] != '') {
                // Assign
                $beratBadan = $result['beratbadan_kg'];
            } else {
                // Assign
                $beratBadan = 0;
            }

            // Cek tinggi badan
            if (isset($result['tinggibadan_cm']) && $result['tinggibadan_cm'] != '') {
                // Assign
                $tinggiBadan = $result['tinggibadan_cm'];
            } else {
                // Assign
                $tinggiBadan = 0;
            }
            if (($tinggiBadan == 0) || ($beratBadan == 0)) {
                $bmi = 0;
            } else {
                // Menghitung BMI
                $bmi = $beratBadan / (($tinggiBadan / 100) * ($tinggiBadan / 100));
            }

            if (isset($result['kesadaran']) && $result['kesadaran'] != '') {
                // Assign
                $kesadaran = ucwords($result['kesadaran']);
            } else {
                // Assign
                $kesadaran = '';
            }
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $image);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
            // curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
            // Yii::error(['ch' => $ch]);
            // $data = curl_exec($ch);
            // curl_close($ch);

            $data = file_get_contents($image);

            $imagecreate  = imagecreatefromstring($data);
            $white  = imagecolorallocate($imagecreate, 255, 255, 255);
            $orange = imagecolorallocate($imagecreate, 255, 112, 67);
            $fontImage = "fonts/arialbd.ttf";

            // Generate tabel pemeriksaan anatomi
            // Variable
            $no = 1;
            $htmlTable = '<table border="1" cellpadding="1" cellspacing="0" style="width:390px">';
            $htmlTable .= '<tbody>';
            $htmlTable .= '<tr>';
            $htmlTable .= '<th>No</th>';
            $htmlTable .= '<th>Bagian Tubuh</th>';
            $htmlTable .= '<th>Catatan</th>';
            $htmlTable .= '</tr>';

            // Loop
            foreach ($resultForTable as $value) {
                // Generate table
                $htmlTable .= '<tr><td>' . $no . '</td><td>' . $value['namabagtubuh'] . '</td><td>' . $value['catatan_tubuh'] . '</td></tr>';

                if (isset($value['koordinat_x']) && isset($value['koordinat_y'])) {
                    $notext = $no . "";
                    imagefilledarc($imagecreate, $value['koordinat_x'] * 600 / 665, $value['koordinat_y'] * 503 / 557, 25, 25, 0, 360, $orange, IMG_ARC_PIE);
                    imagefttext($imagecreate, 10, 0, $value['koordinat_x'] * 600 / 665 - (3.5 * strlen($notext)), $value['koordinat_y'] * 503 / 557 + 5, $white, $fontImage, $notext);
                }
                // Plus counter
                $no++;
            }

            // Close tag
            $htmlTable .= '</tbody>';
            $htmlTable .= '</table>';

            $fileTempPath = Yii::getAlias("@webroot") . "/assets/tmp_bagian_tubuh.png";
            imagepng($imagecreate, $fileTempPath);
            $htmlImage = '
                    <div>
                        <img src="' . $fileTempPath . '" width="350.0334" height="282.26">
                    </div>';
            // Set checkbox
            $is_kapitis = isset($result['is_kapitis']) && $result['is_kapitis'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_paten = isset($result['jn_paten']) && $result['jn_paten'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_obstruktifpartial = isset($result['jn_obstruktifpartial']) && $result['jn_obstruktifpartial'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_obstruktifpartial = isset($result['jn_obstruktifpartial']) && $result['jn_obstruktifpartial'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_obstruktifnormal = isset($result['jn_obstruktifnormal']) && $result['jn_obstruktifnormal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_stridor = isset($result['jn_stridor']) && $result['jn_stridor'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_gargling = isset($result['jn_gargling']) && $result['jn_gargling'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_normal = isset($result['pgp_normal']) && $result['pgp_normal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_kussmaul = isset($result['pgp_kussmaul']) && $result['pgp_kussmaul'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_takipnea = isset($result['pgp_takipnea']) && $result['pgp_takipnea'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_retraktif = isset($result['pgp_retraktif']) && $result['pgp_retraktif'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_dangkal = isset($result['pgp_dangkal']) && $result['pgp_dangkal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgd_simetri = isset($result['pgd_simetri']) && $result['pgd_simetri'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgd_asimetri = isset($result['pgd_asimetri']) && $result['pgd_asimetri'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $cfr_kecil_2 = isset($result['cfr_kecil_2']) && $result['cfr_kecil_2'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $cfr_besar_2 = isset($result['cfr_besar_2']) && $result['cfr_besar_2'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_normal = isset($result['kulit_normal']) && $result['kulit_normal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_jaundice = isset($result['kulit_jaundice']) && $result['kulit_jaundice'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_cyanosis = isset($result['kulit_cyanosis']) && $result['kulit_cyanosis'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_pucat = isset($result['kulit_pucat']) && $result['kulit_pucat'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_berkeringat = isset($result['kulit_berkeringat']) && $result['kulit_berkeringat'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';

            $nilaigcs_eye = isset($result['nilaigcs_eye']) ? $result['nilaigcs_eye'] : null;
            $nilaigcs_verbal = isset($result['nilaigcs_verbal']) ? $result['nilaigcs_verbal'] : null;
            $nilaigcs_motorik = isset($result['nilaigcs_motorik']) ? $result['nilaigcs_motorik'] : null;
            $hasilGcs = $nilaigcs_eye + $nilaigcs_verbal + $nilaigcs_motorik;
            // Assign attributes
            $print->attributes = [
                '#status_periksa#' => isset($result['status_periksa']) ? $result['status_periksa'] : null,
                '#jeniskasuspenyakit_nama#' => isset($result['jeniskasuspenyakit_nama']) ? $result['jeniskasuspenyakit_nama'] : null,
                '#umur#' => isset($result['umur']) ? $result['umur'] : null,
                '#kelaspelayanan_nama#' => isset($result['kelaspelayanan_nama']) ? $result['kelaspelayanan_nama'] : null,
                '#tgl_pendaftaran#' => isset($result['tgl_pendaftaran']) ? date('d F Y H:i:s', strtotime($result['tgl_pendaftaran'])) : null,
                '#poliklinik#' => isset($result['ruangan_nama']) ? $result['ruangan_nama'] : null,
                '#no_pendaftaran#' => isset($result['no_pendaftaran']) ? $result['no_pendaftaran'] : null,
                '#no_rm#' => isset($result['no_rekam_medik']) ? $result['no_rekam_medik'] : null,
                '#nama_pasien#' => isset($result['nama_pasien']) ? $result['nama_pasien'] : null,
                '#jenis_kelamin#' => isset($result['jenis_kelamin']) ? $result['jenis_kelamin'] : null,
                '#tanggal_lahir#' => isset($result['tanggal_lahir']) ? date('d F Y', strtotime($result['tanggal_lahir'])) : null,
                '#cara_bayar#' => isset($result['carabayar_nama']) ? $result['carabayar_nama'] : null,
                '#penjamin#' => isset($result['penjamin_nama']) ? $result['penjamin_nama'] : null,
                '#dokter_pemeriksa#' => isset($result['dokter']) ? $result['dokter'] : null,
                '#perawat#' => isset($result['perawat']) ? $result['perawat'] : null,
                '#tanggal_periksa#' => isset($result['tglperiksafisik']) ? date('d F Y', strtotime($result['tglperiksafisik'])) : null,
                '#keadaan_umum#' => isset($result['keadaanumum']) ? $result['keadaanumum'] : null,
                '#tekanan_darah#' => isset($result['tekanandarah']) ? $result['tekanandarah'] : null,
                '#klasifikasitekanandarah#' => isset($result['klasifikasitekanadarah']) ? $result['klasifikasitekanadarah'] : null,
                '#mean_arteri_preassure#' => isset($result['meanarteripressure']) ? trim(str_replace('.', ',', $result['meanarteripressure'])) : null,
                '#detak_nadi#' => isset($result['detaknadi']) ? $result['detaknadi'] : null,
                '#denyut_jantung#' => isset($result['denyutjantung']) ? $result['denyutjantung'] : null,
                '#pernafasan#' => isset($result['pernapasan']) ? $result['pernapasan'] : null,
                '#kategori_pernafasan#' => isset($result['kategori_pernapasan']) ? $result['kategori_pernapasan'] : null,
                '#suhu_tubuh#' => isset($result['suhutubuh']) ? trim(str_replace('.', ',', $result['suhutubuh'])) : null,
                '#tinggi_badan#' => isset($result['tinggibadan_cm']) ? trim(str_replace('.', ',', $result['tinggibadan_cm'])) : null,
                '#bb_ideal#' => isset($result['bb_ideal']) ? trim(str_replace('.', ',', $result['bb_ideal'])) : null,
                '#massa_index_tubuh#' => number_format((float)$bmi, 2, ',', ''),
                '#kelainan_tubuh#' => isset($result['kelainanpadabagtubuh']) ? $result['kelainanpadabagtubuh'] : null,
                '#kesadaran#' => $kesadaran,
                '#inspeksi#' => isset($result['inspeksi']) ? $result['inspeksi'] : null,
                '#palpasi#' => isset($result['palpasi']) ? $result['palpasi'] : null,
                '#perkusi#' => isset($result['perkusi']) ? $result['perkusi'] : null,
                '#auskultasi#' => isset($result['auskultasi']) ? $result['auskultasi'] : null,
                '#gcs_eye#' => isset($result['gcs_eye']) ? $result['gcs_eye'] : null,
                '#metodegcs_eye#' => isset($result['metodegcs_eye']) ? $result['metodegcs_eye'] : null,
                '#nilaigcs_eye#' => $nilaigcs_eye,
                '#gcs_verbal#' => isset($result['gcs_verbal']) ? $result['gcs_verbal'] : null,
                '#metodegcs_verbal#' => isset($result['metodegcs_verbal']) ? $result['metodegcs_verbal'] : null,
                '#nilaigcs_verbal#' => $nilaigcs_verbal,
                '#gcs_motorik#' => isset($result['gcs_motorik']) ? $result['gcs_motorik'] : null,
                '#metodegcs_motorik#' => isset($result['metodegcs_motorik']) ? $result['metodegcs_motorik'] : null,
                '#nilaigcs_motorik#' => $nilaigcs_motorik,
                '#gcs_is_kapitis#' => $is_kapitis,
                '#gcs_kategori#' => isset($result['gcs_nama']) ? $result['gcs_nama'] : null,
                '#hasil_metode_gcs#' => $hasilGcs,
                '#pernapasan_gerakan#' => isset($result['pernapasan_gerakan']) ? $result['pernapasan_gerakan'] : null,
                '#jalan_nafas#' => isset($result['jalan_nafas']) ? $result['jalan_nafas'] : null,
                '#sirkulasi#' => isset($result['sirkulasi']) ? $result['sirkulasi'] : null,
                '#gambar_anatomi#' => $htmlImage,
                '#list_tabel_anatomi#' => $htmlTable,
                '#tanggal_pemeriksaan#' => isset($result['tanggal_pemeriksaan']) ? $result['tanggal_pemeriksaan'] : null,
                '#berat_badan#' => isset($result['beratbadan_kg']) ? trim(str_replace('.', ',', $result['beratbadan_kg'])) : null,
                '#imt_kategori#' => isset($result['bmi_defenisi']) ? $result['bmi_defenisi'] : null,
                '#paten#' => $jn_paten,
                '#obstruktif_partial#' => $jn_obstruktifpartial,
                '#obstruktif_total#' => $jn_obstruktifnormal,
                '#stridor#' => $jn_stridor,
                '#gargling#' => $jn_gargling,
                '#normal#' => $pgp_normal,
                '#kussmaul#' => $pgp_kussmaul,
                '#takipnea#' => $pgp_takipnea,
                '#retraktif#' => $pgp_retraktif,
                '#dangkal#' => $pgp_dangkal,
                '#simetri#' => $pgd_simetri,
                '#asimetri#' => $pgd_asimetri,
                '#sirkulasi_nadicarotis#' => isset($result['sirkulasi_nadicarotis']) ? $result['sirkulasi_nadicarotis'] : null,
                '#sirkulasi_nadiradialis#' => isset($result['sirkulasi_nadiradialis']) ? $result['sirkulasi_nadiradialis'] : null,
                '#cfr_kecil_2#' => $cfr_kecil_2,
                '#cfr_besar_2#' => $cfr_besar_2,
                '#kulit_normal#' => $kulit_normal,
                '#kulit_jaundice#' => $kulit_jaundice,
                '#kulit_cyanosis#' => $kulit_cyanosis,
                '#kulit_pucat#' => $kulit_pucat,
                '#kulit_berkeringat#' => $kulit_berkeringat,
                '#akral#' => isset($result['akral']) ? $result['akral'] : null,
                '#tgl_cetak#' => date('d F Y'),
            ];
            // Print output
            $print->Output();
            unlink($fileTempPath);
        }
    }

    /**
     *
     * @see Fungsi insert reseptur pemeriksaan
     * @return array response
     *
     */
    public function actionCreateReseptur()
    {
        $request = Yii::$app->request;
        $data_reseptur = $request->post('data_reseptur', []);
        $data_resepturdetail = $request->post('data_resepturdetail', []);
        $vPayload = self::extractValidationPayload($data_reseptur, $data_resepturdetail);
        $obatalkes_tidak_tersedia = self::validateResepturDetail($vPayload['resepturdetail'], $vPayload['ruangan_id'], $vPayload['penjamin_id'], $vPayload['kelaspelayanan_id']);
        if (count($obatalkes_tidak_tersedia) > 0) {
            return [
                'metadata' => [
                    'status' => 422,
                    'message' => 'Unprocessable Entity',
                ], 
                'response' => [
                    'title' => 'Proses tidak Bisa Dilanjutkan',
                    'text' => 'Terdapat obat yang tidak tersedia di depo tujuan',
                    'data' => [
                        'obatalkes_tidak_tersedia' => $obatalkes_tidak_tersedia
                  ]
                ]
            ];
        }

        $stok_tidak_tersedia = self::validateResepturDetailStok($vPayload['resepturdetail'], $vPayload['ruangan_id']);
        if (!empty($stok_tidak_tersedia)) {
            return [
                'metadata' => [
                    'status' => 422,
                    'message' => 'Unprocessable Entity',
                ], 
                'response' => [
                    'title' => 'Proses tidak Bisa Dilanjutkan',
                    'text' => 'Qty tidak boleh melebihi stok tersedia',
                    'data' => [
                        'stok_tidak_tersedia' => $stok_tidak_tersedia
                    ]
                ]
            ];
        }
        return Yii::$app->docoPlugin->execute('reseptur');
    }

    /**
     * @todo check stok
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param (integer ruangan_id
     * @param arra obatalkes_id
     */
    private function checkStok($ruangan_id = null, array $obatalkes_id)
    {
        $listObatId = implode(', ', $obatalkes_id);
        $connection = Yii::$app->db;
        $sql = "SELECT obatalkes_id, qty_tersedia FROM stokobatalkes_r";

        if ($ruangan_id && $obatalkes_id) {
            $sql .=  " WHERE ruangan_id = {$ruangan_id} AND obatalkes_id IN ({$listObatId})";
        }

        $stok = $connection->createCommand($sql)->queryAll();

        return !empty($stok) ? $stok : [];
    }

    /**
     *
     * @see Fungsi get data pasien
     * @return array
     *
     */
    public function actionGetRiwayatReseptur()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $ruangan_id = $request->get('ruangan_id');

            $find = InfoReseptur::find();
            if ($pendaftaran_id) {
                $find->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ]);
            }
            if ($ruangan_id) {
                $find->andWhere([
                    'ruanganreseptur_id' => $ruangan_id
                ]);
            }
            $find->orderBy(['tglreseptur' => SORT_DESC]);
            $data = $find->asArray()->all();

            return [
                'data' => $data
            ];
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

    // Update pegawai
    public function actionUpdatePegawai()
    {
        // Try catch
        try {
            // Get reqeust
            $request = Yii::$app->request;

            // Check post
            if (!empty($request->post())) {
                // Assign request
                $post = $request->post();

                // Assign post
                $pendaftaranId = $post['pendaftaranId'];
                $pegawaiId = $post['pegawaiId'];

                // Get data
                $model = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaranId])->one();

                // Change pegawai
                $model->pegawai_id = $pegawaiId;

                // Save
                $model->save();

                // Return
                return [
                    'data' => $model,
                    'status' => 200
                ];
            }
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

    /**
     *
     * @see Fungsi get data pasien
     * @return array
     *
     */
    public function actionGetDetailRiwayatReseptur()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('reseptur_id');

            $find = InfoResepturDetail::find()->select(['resepturdetail_id', 'racikan_nama', 'rke', 'obatalkes_nama', 'signa_nama', 'qty_reseptur', 'satuan_input', 'hargajual_satuan', 'hargajual_satuan', 'qty_reseptur', 'etiket', 'signa']);
            if ($id) {
                $find->andWhere([
                    'reseptur_id' => $id
                ]);
            }
            $data = $find->asArray()->all();

            return [
                'data' => $data
            ];
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

    /**
     *
     * @see Fungsi update reseptur pemeriksaan
     * @return array response
     *
     */
    public function actionUpdateReseptur()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;

            $data = $request->post();
            $reseptur_id = $data['reseptur_id'];
            $data_resepturdetail = isset($data['data_resepturdetail']) ? $data['data_resepturdetail'] : [];

            $data_user = $this->getDefaultData();
            // return $data_resepturdetail;
            /*
            * author: Rizqi Febian
            * start multiple update using case when
            */
            $updatedSigna = $updatedQty = $updatedHargaSatuan = $updatedHargaNetto = $updatedHarga = '';
            $resepturdetail_id = [];
            /** Penambahan Kondisi reseptur ketika data detailnya di hapus semua */
            if (!empty($data_resepturdetail)) {
                foreach ($data_resepturdetail as $key => $value) {
                    $resepturdetail_id[] = $value['resepturdetail_id'];
                    $updatedSigna .= "WHEN resepturdetail_id = {$value['resepturdetail_id']} THEN {$value['signa_id']} ";
                    $updatedQty .= "WHEN resepturdetail_id = {$value['resepturdetail_id']} THEN {$value['qty_reseptur']} ";
                    $updatedHargaSatuan .= "WHEN resepturdetail_id = {$value['resepturdetail_id']} THEN {$value['hargajual_satuan']} ";
                    $updatedHargaNetto .= "WHEN resepturdetail_id = {$value['resepturdetail_id']} THEN {$value['harga_netto']} ";
                    $updatedHarga .= "WHEN resepturdetail_id = {$value['resepturdetail_id']} THEN {$value['harga_konversi']} ";
                }
                $resepturdetail_id = implode(',', $resepturdetail_id);
                $query = "UPDATE resepturdetail_t SET signa_id = (CASE {$updatedSigna}END), qty_reseptur = (CASE {$updatedQty}END), is_deleted = false,hargasatuan_reseptur = (CASE {$updatedHargaSatuan}END),hargajual_reseptur = (CASE {$updatedHarga}END),harganetto_reseptur = (CASE {$updatedHargaNetto}END) WHERE reseptur_id = {$reseptur_id} and resepturdetail_id IN({$resepturdetail_id});";

                // return $query;
                $connection->createCommand($query)->execute();
                //hapus data detail sebelumnya
                $hapus_reseptur_detail = ResepturDetail::updateAll(
                    [
                        'is_deleted' => true,
                        'deleted_date' => $data_user->date,
                        'deleted_by' => $data_user->by
                    ],
                    'reseptur_id = :reseptur_id AND is_deleted = :deleted AND resepturdetail_id NOT IN(' . $resepturdetail_id . ')',
                    ['reseptur_id' => $reseptur_id, 'deleted' => 'false']
                );
                $return = ['message' => 'Data Berhasil di simpan'];
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Reseptur Tidak Boleh Kosong'
                ];
            }
            /*
            * end
            */

            $transaction->commit();

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionExportPdfReseptur
     * @attribute #table# => table data
     * @attribute #dokter_perujuk# => dokter perujuk
     * @attribute #depo_tujuan# => depo tujuan
     * @attribute #tanggal_reseptur# => tanggal reseptur
     * @attribute #nomor_reseptur# => nomor reseptur
     * @attribute #nama_pasien# => nama pasien
     * @attribute #umur# => umur
     * @attribute #alamat# => alamat
     * @attribute #no_rekam_medik# => no rekam medik
     **/
    public function actionExportPdfReseptur()
    {
        $request = Yii::$app->request;
        $title = Yii::t('app', 'R/');

        try {
            $pendaftaran_id = $request->get('pendaftaran_id');
            $reseptur_id = (int)$request->get('reseptur_id');
            $find = InfoResepturDetail::find();
            if ($reseptur_id) {
                $find->andWhere([
                    'reseptur_id' => $reseptur_id
                ]);
            }
            $data = $find->asArray()->all();
            $data_pasien = $this->getDataPasien($pendaftaran_id)->asArray()->one();
            $data_reseptur = InfoReseptur::findOne(['reseptur_id' => $reseptur_id]);
            $data_pasien_resep = WorklistResepView::find()->select([
                'dokter',
            ])->asArray()->one();
            $print = new DocoPrint();
            $print->attributes = [
                '#table#' => $this->renderPartial('index_reseptur', [
                    'title' => $title,
                    'data' => $data,
                ]),
                '#dokter_perujuk#' => $data_pasien_resep['dokter'],
                '#depo_tujuan#' => $data_reseptur['ruangan_tujuan'],
                '#tanggal_reseptur#' => $data_reseptur['tglreseptur'],
                '#nomor_reseptur#' => $data_reseptur['noresep'],
                '#nama_pasien#' => $data_pasien['nama_pasien'],
                '#umur#' => $data_pasien['umur'],
                '#alamat#' => $data_pasien['alamat_pasien'],
                '#no_rekam_medik#' => $data_pasien['no_rekam_medik'],
            ];
            $print->Output();

            // return true;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionCetakAntrianFarmasi
     * @attribute #no_antrian# => data no_antrian
     * @attribute #depo_tujuan# => data depo tujuan
     * @attribute #carabayar# => data cara bayar
     * @attribute #racikan# => data racikan
     * @attribute #currentdate# => data racikan
     **/
    public function actionCetakAntrianFarmasi()
    {
        $request = Yii::$app->request;

        try {
            $reseptur_id = $request->get('reseptur_id');

            $query = "
                SELECT
                    antrian_v.no_antrian,
                    antrian_v.ruangan_nama,
                    antrian_v.carabayar_nama,
                    antrian_v.racikan_nama
                FROM reseptur_t
                JOIN antrian_v ON reseptur_t.antrian_id = antrian_v.antrian_id
                WHERE reseptur_t.reseptur_id = :reseptur_id
            ";
            $result = Reseptur::findBySql($query, [':reseptur_id' => $reseptur_id])->asArray()->one();

            $print = new DocoPrint();
            $print->attributes = [
                '#no_antrian#' => @$result['no_antrian'],
                '#depo_tujuan#' => @$result['ruangan_nama'],
                '#carabayar#' => @$result['carabayar_nama'],
                '#racikan#' => @$result['racikan_nama'],
                '#currentdate#' => date('d/m/Y H:i:s'),
            ];
            $ret = $print->OutputHtml();

            return $ret;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get tindakan ruangan
     * @return object
     *
     */
    public function actionGetTindakanRuangan($ruanganId = null, $kelasPelayananId = null, $id = null, $penjaminId = null, $komponenTarifId = DocoConstants::KOMPONEN_TARIF)
    {
        // Try catch
        try {
            // Get data
            $model = InfoTarifRs::find()->where(['daftartindakan_id' => $id]);

            // Check condition
            if ($ruanganId != null) {
                // Add condition
                $model->andWhere(['ruangan_id' => $ruanganId]);
            }

            // Check condition
            if ($kelasPelayananId != null) {
                // Add condition
                $model->andWhere(['kelaspelayanan_id' => $kelasPelayananId]);
            }

            // Check condition
            if ($penjaminId != null) {
                // Add condition
                $model->andWhere(['penjamin_id' => $penjaminId]);
            }

            // Check condition
            if ($komponenTarifId != null) {
                // Add condition
                $model->andWhere(['komponentarif_id' => $komponenTarifId]);
            }

            // Return model
            return $model->one();
        } catch (\yii\db\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get paket ruangan
     * @return object
     *
     */
    public function actionGetPaketRuangan($ruanganId = null, $kelasPelayananId = null, $id = null, $penjaminId = null)
    {
        // Try catch
        try {
            // Get data
            $model = InfoTarifRs::find()->where(['tipepaket_id' => $id, 'komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);

            // Check condition
            if ($ruanganId != null) {
                // Add condition
                $model->andWhere(['ruangan_id' => $ruanganId]);
            }

            // Check condition
            if ($kelasPelayananId != null) {
                // Add condition
                $model->andWhere(['kelaspelayanan_id' => $kelasPelayananId]);
            }

            // Check condition
            if ($penjaminId != null) {
                // Add condition
                $model->andWhere(['penjamin_id' => $penjaminId]);
            }

            $db = Yii::$app->db;
            $sql = "SELECT daftartindakan_nama
                FROM paketdetail_v
                WHERE
                    tipepaket_id = {$id}
            ";
            $data_detail = $db->createCommand($sql)->queryAll();
            $data['tindakan_data'] = $model->one();
            $data['paket_data'] = $data_detail;
            // Return model
            return $data;
        } catch (\yii\db\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionPrintTindakan
     * @attribute #data_tindakan_bmhp# => print
     **/
    public function actionPrintTindakan()
    {
        $id = Yii::$app->request->get('id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $queryHeader =  RiwayatTindakanView::find()->where([
            'ruangan_pelayanan_id' => $ruangan_id,
            'pendaftaran_id' => $id
        ]);
        $queryTindakan =  RiwayatTindakanView::find()
            // ->where([
            //     'between', 'tgl_tindakan', $start, $end
            // ])
            ->where('tipe_pelayanan != :tipe_pelayanan AND ruangan_pelayanan_id = :ruangan_pelayanan_id AND pendaftaran_id = :pendaftaran_id ', [
                ':tipe_pelayanan' => DocoConstants::VAR_TPT,
                'ruangan_pelayanan_id' => $ruangan_id,
                'pendaftaran_id' => $id
            ])->orderBy(['tgl_tindakan' => SORT_DESC]);
        $queryBmhp =  RiwayatTindakanView::find()
            // ->where([
            //     'between', 'tgl_tindakan', $start, $end
            // ])
            ->where([
                'tipe_pelayanan' => DocoConstants::VAR_TPB,
                'ruangan_pelayanan_id' => $ruangan_id,
                'pendaftaran_id' => $id
            ])->orderBy(['tgl_tindakan' => SORT_DESC]);

        $data_header = $queryHeader->asArray()->one();
        $data_tindakan = $queryTindakan->asArray()->all();
        $data_bmhp = $queryBmhp->asArray()->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#data_tindakan_bmhp#' => $this->renderPartial(
                'pdf_tindakan_bmhp',
                [
                    'data_header' => $data_header,
                    'data_tindakan' => $data_tindakan,
                    'data_bmhp' => $data_bmhp,
                ]
            ),
        ];
        $print->Output();
    }

    /**
     * @todo get obat/alkes by jenis obatalkes
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetObatAlkes()
    {
        $request = Yii::$app->request;
        $jenis_obatalkes_id = $request->get('id');
        $jenis = $request->get('jenis');
        $ruangan_id = $request->get('ruangan_id');
        $query = InfoStokObatAlkesFn::find()->select([
            'obatalkes_id',
            'obatalkes_nama as obatalkes_namalain',
            'qty_tersedia',
            'harganetto',
            'hargamaksimum',
            'hargaminimum',
            'hargaratarata',
            'ruangan_id',
            'hargaygdipakai',
            'jml_hargajual',
            'group_jenisobat',
        ])->where(['ruangan_id' => $ruangan_id]);
        if (!empty($jenis)) {
            $query->andWhere(['ILIKE', 'jenisobatalkes_nama', $jenis]);
        }

        if (!empty($jenis_obatalkes_id)) {
            $query->andWhere(['=', 'jenisobatalkes_id', $jenis_obatalkes_id]);
        }
        $data = $query->asArray()->all();

        return empty($data) ? [] : $data;
    }

    /**
     *
     * @see Fungsi get data tindakan bmhp
     * @return array response
     *
     */
    public function actionGetDataTindakanBmhp()
    {
        // Try catch
        try {
            // Get request
            $request = Yii::$app->request;
            $id = $request->get('id');
            $ruangan_id = $request->get('ruangan_id', 0);

            // Check request
            if ($id != null) {
                // Get data
                $data = RiwayatTindakanView::find()
                    ->where(['pendaftaran_id' => $id, 'ruangan_pelayanan_id' => $ruangan_id])
                    ->orderBy(['tgl_tindakan' => SORT_DESC])
                    ->all();

                // Return
                return $data;
            }
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi update stok obat alkes
     * @return array response
     *
     */
    public function actionUpdateStokObatAlkes()
    {
        // Try catch
        try {
            // Get request
            $request = Yii::$app->request;
            $id = $request->get('id');
            $ruangan_id = $request->get('ruangan_id');
            $type = $request->get('type');
            $data = $request->post();

            // Check request
            if ($id != null && $ruangan_id != null && $type != '') {
                // Get data
                $model = StokObatAlkesR::find()->where(['obatalkes_id' => $id])->andWhere(['ruangan_id' => $ruangan_id])->one();

                // Check model
                if (!empty($model)) {
                    // Check type
                    if ($type == 'update') {
                        // Calculate qty tersedia
                        $model->qty_tersedia = floatval($model->qty_tersedia) - floatval($data['jumlah']);
                    } else {
                        // Calculate qty tersedia
                        $model->qty_tersedia = floatval($model->qty_tersedia) + floatval($data['jumlah']);
                    }

                    // Save
                    if ($model->save()) {
                        // Return model
                        return $model;
                    } else {
                        // Return errors
                        return $model->errors;
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
        }
    }

    public function actionGetListCaraKeluar()
    {
        $all_CaraKeluar = CaraKeluar::getCaraKeluar();
        return $all_CaraKeluar;
    }

    public function actionGetCekMorbiditas($id = null)
    {
        $data['morbiditas'] = count(PasienMorbiditas::find()->where(['pendaftaran_id' => $id])->asArray()->all());
        $data['anamnesa'] = Anamnesa::find()->where(['pendaftaran_id' => $id])->asArray()->one();
        return $data;
    }

    /**
     * @method actionPemulanganPasien (Pemulangan Pasien)
     * @return Array
     */
    public function actionPemulanganPasien()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        // try {
            $request = Yii::$app->request;
            $dataPulangPasien = $request->post('modelpulangpasien');
            $dataRujukanPulang = $request->post('modelRujukanPulang');

            if ($request->post()) {
                $payload = $request->post();
                $t_sep_new = [];
                if(isset($payload['nosep'])) {
                    $carakeluarBpjs = (new DocoConstansId)->actionGetAdditional('cara_pulang_bpjs',true);
                    $caraPulangBpjs = isset($carakeluarBpjs[$payload['modelpulangpasien']['carakeluar_id']]) ? $carakeluarBpjs[$payload['modelpulangpasien']['carakeluar_id']] : 5;
                    $model = new Bpjs;

                    $t_sep_new['noSep'] = $payload['nosep'];
                    $t_sep_new['statusPulang'] = $caraPulangBpjs;
                    $t_sep_new['noSuratMeninggal'] = $payload['modelpulangpasien']['carakeluar_id'] == 4 ? $payload['modelpulangpasien']['no_surat_kematian'] : '';
                    $t_sep_new['tglMeninggal'] = $payload['modelpulangpasien']['carakeluar_id'] == 4 ?  date('Y-m-d', strtotime($payload['modelpulangpasien']['tgl_meninggal'])) : '';
                    $t_sep_new['tglPulang'] = date('Y-m-d', strtotime($payload['modelpulangpasien']['tglpasienpulang']));
                    $t_sep_new['noLPManual'] = '';
                    $t_sep_new['user'] = $payload['user'];
                    $model->t_sep = $t_sep_new;

                    if ($t_sep_new['noSep'] != null && $t_sep_new['tglPulang'] != null) {
                        $result = $model->updateTanggalPulangSepNew();
                    }
                }

                $pendaftaranId = ArrayHelper::getValue($payload, 'modelpulangpasien.pendaftaran_id');
                $konsulPoliId = ArrayHelper::getValue($payload, 'konsulpoliId');
                $pasienRecord = PasienPulang::find()
                    ->select(['pendaftaran_id'])
                    ->andWhere('pasienbatalpulang_id IS NULL')
                    ->andWhere(['pendaftaran_id' => $pendaftaranId])
                    ->andWhere(['konsulpoli_id' => $konsulPoliId])
                    ->asArray()
                    ->one();
                if (!empty($pasienRecord)) {
                    return [
                        'message' => 'Pasien Sudah Melakukan Pemulangan',
                        'status' => 422
                    ];
                }
                $pelayananJenazah = false;
                $modelPasienPulang = new PasienPulang;
                $modelPasienPulang->attributes = $dataPulangPasien;

                if (!empty($modelPasienPulang->tglpasienpulang)) {
                    $modelPasienPulang->tglpasienpulang = date('Y-m-d H:i:s', strtotime($modelPasienPulang->tglpasienpulang));
                }
                if (!empty($modelPasienPulang->tgl_meninggal)) {
                    $modelPasienPulang->tgl_meninggal = date('Y-m-d H:i:s', strtotime($modelPasienPulang->tgl_meninggal));
                }
                // Perubahan format tanggal tgl_kremasi dan waktu_pemeriksaan_jenazah
                if (!empty($modelPasienPulang->tgl_kremasi)) {
                    $modelPasienPulang->tgl_kremasi = date('Y-m-d H:i:s', strtotime($modelPasienPulang->tgl_kremasi));
                }
                if (!empty($modelPasienPulang->waktu_pemeriksaan_jenazah)) {
                    $modelPasienPulang->waktu_pemeriksaan_jenazah = date('Y-m-d H:i:s', strtotime($modelPasienPulang->waktu_pemeriksaan_jenazah));
                }
                $id = $modelPasienPulang->pendaftaran_id;

                /** CARA KELUAR = DIRAWAT */
                if ($modelPasienPulang->carakeluar_id == 5) {
                    $statusPeriksa = DocoConstants::STATUS_RUJUK_RAWAT_INAP;
                } else {
                    if ($modelPasienPulang->carakeluar_id == 4) {
                        $pelayananJenazah = ($dataPulangPasien['persetujuanpelayanan']) ? true : false;
                    }
                    $statusPeriksa = DocoConstants::STATUS_PULANG;
                }

                if (!empty($request->post('konsulpoliId'))) {
                    $modelPasienPulang->konsulpoli_id = $request->post('konsulpoliId');
                }
                if (!empty($dataRujukanPulang)) {
                    $mRujukanKeluar = RujukanPulang::find()->where(['pendaftaran_id' => $id])->one();
                    if (is_null($mRujukanKeluar)) {
                        $mRujukanKeluar = new RujukanPulang();
                    }
                    $mRujukanKeluar->attributes = $dataRujukanPulang;
                    if (!$mRujukanKeluar->validate()) {
                        \Yii::$app->response->statusCode = 422;
                        return [
                            'data' => $mRujukanKeluar->errors,
                            'status' => 422,
                        ];
                    }
                    if (!$mRujukanKeluar->save()) {
                        \Yii::$app->response->statusCode = 422;
                        return [
                            'data' => $mRujukanKeluar->errors,
                            'status' => 422,
                        ];
                    }
                }

                if ($modelPasienPulang->save()) {
                    if(isset($dataPulangPasien['is_prb']) && $dataPulangPasien['is_prb']) {
                        $modelPendaftaran = Pendaftaran::findOne($id);
                        $modelBpjs = BpjsCore::findOne($modelPendaftaran->bpjs_id);
                        $modelPasien = Pasien::findOne($modelPendaftaran->pasien_id);

                        $obat = [];
                        $data_reseptur = json_decode($request->post('resepturPrb', '[]'), true);
                        if(isset($data_reseptur) && is_array($data_reseptur)) {
                            foreach ($data_reseptur as $reseptur) {
                                if(isset($reseptur['kode_bpjs']) && !empty($reseptur['kode_bpjs'])) {
                                    $obat[] = [
                                        "kdObat" => $reseptur["kode_bpjs"],
                                        "signa1" => $reseptur["qty_signa"] ? : "1",
                                        "signa2" => $reseptur["iterasi_signa"] ? : "1",
                                        "jmlObat" => $reseptur["qty_reseptur"],
                                    ];
                                }
                            }
                        }
                        $t_prb = [
                            "noSep" => $modelBpjs->nosep,
                            "noKartu" => $modelBpjs->nokartuasuransi,
                            "alamat" => $modelPasien->alamat_sekarang ? : '-',
                            "email" => $modelPasien->alamatemail ? : 'default@email.com',
                            "programPRB" => $dataRujukanPulang['diagnosa_prb'],
                            "kodeDPJP" => $dataRujukanPulang['pegawai_kode_bpjs'],
                            "keterangan" => $dataRujukanPulang['tindakan_lainnya'] ? : '-',
                            "saran" => $dataRujukanPulang['tindakan_terapi'] ? : '-',
                            "user" => $modelPasien->no_rekam_medik,
                            "obat" => $obat,
                        ];

                        $respPRB = $modelBpjs->insertPRB($t_prb);
                        if(!isset($respPRB['response']['noSRB']) || empty($respPRB['response']['noSRB'])) {
                            $transaction->rollBack();
                            return [
                                'title' => 'Terjadi Kesalahan',
                                'text' => 'Gagal Kirim Data Rujuk Balik',
                                'status' => 422
                            ];
                        }

                        $modelRujukBalik = new RujukBalik;
                        $modelRujukBalik->attributes = [
                            'pendaftaran_id' => $id,
                            'tgl_rujukbalik' => date('Y-m-d H:i:s'),
                            'alamat' => $modelPasien->alamat_sekarang,
                            'email' => $modelPasien->alamatemail,
                            'kode_dpjp' => $dataRujukanPulang['pegawai_kode_bpjs'],
                            'nama_dokter' => $dataRujukanPulang['pegawai_nama'],
                            'saran' => $dataRujukanPulang['tindakan_terapi'],
                            'diagnosa' => $dataRujukanPulang['diagnosa_prb'],
                            'data_reseptur' => $request->post('resepturPrb', '[]'),
                            'no_srb' => $respPRB['response']['noSRB'],
                            'additional_data' => json_encode(['t_prb' => $t_prb, 'res_prb' => $respPRB]),
                        ];

                        if(!$modelRujukBalik->save()) {
                            $transaction->rollBack();
                            return [
                                'title' => 'Terjadi Kesalahan',
                                'text' => 'Gagal Menyimpan Data Rujuk Balik',
                                'status' => 422
                            ];
                        }
                    }

                    if (empty($request->post('konsulpoliId'))) {
                        // pendaftaran poli biasa
                        $modelPendaftaran = Pendaftaran::findOne($id);
                        $modelPendaftaran['status_periksa'] = $statusPeriksa;
                        $modelPendaftaran['pasienpulang_id'] = $modelPasienPulang->pasienpulang_id;
                        if ($modelPendaftaran->save()) {
                            /**
                             * update tanggal pulang SEP untuk pasien BPJS
                             * per 01/04/22 ada request untuk non aktifkan update karena sudah dibackup dari pendaftaran
                             */
                            //update tgl pulang
                            // if (isset($modelPendaftaran->bpjs_id)) {
                            //     $mBpjs = Bpjs::findOne($modelPendaftaran->bpjs_id);
                            //     $mBpjs->tglpulang = $modelPasienPulang->tglpasienpulang;
                            //     //bypass error pulang gagal update tanggal pulang bpjs_t
                            //     // if(!$mBpjs->save()){
                            //     //     return [
                            //     //         'message' => 'Gagal Update Tanggal Pulang',
                            //     //         'status' => 422
                            //     //     ];
                            //     // }

                            //     $mUpdateBpjs = new Bpjs;
                            //     //bypass error pulang gagal update tanggal pulang bpjs_t
                            //     // $mUpdateBpjs->t_sep = ['noSep'=>$mBpjs->nosep,'tglPulang'=>$mBpjs->tglpulang,'ppkPelayanan'=>$mBpjs->ppkpelayanan];
                            //     if (!empty($mBpjs->nosep))
                            //     {
                            //         $mUpdateBpjs->t_sep = ['noSep' => $mBpjs->nosep, 'tglPulang' => $modelPasienPulang->tglpasienpulang, 'ppkPelayanan' => $mBpjs->ppkpelayanan];
                            //         $resultUpdateBpjs = $mUpdateBpjs->updateTanggalPulangSep();
                            //         if (!$resultUpdateBpjs) {
                            //             return [
                            //                 'message' => 'Gagal Update Bpjs',
                            //                 'status' => 422
                            //             ];
                            //         }
                            //     }
                            // }
                            if ($modelPasienPulang->is_meninggal && $pelayananJenazah) {
                                $saveJenazah = $this->orderPelayananJenazah($id, $request->post('modeljenazah'));
                                if (!$saveJenazah) {
                                    $transaction->rollBack();
                                    return [
                                        'title' => 'Terjadi Kesalahan',
                                        'text' => 'Gagal Menyimpan Form Jenazah',
                                        'status' => 422
                                    ];
                                }
                            }
                            $transaction->commit();

                            /* update tgl pulang sync eklaim BPJS - Pemulangan Rawat Jalan */
                            $dataPendaftaran = Pendaftaran::find()
                                                ->select(['pendaftaran_id', 'no_pendaftaran'])
                                                ->andWhere(['pendaftaran_id' => $id])
                                                ->asArray()->one();

                            if(!empty($dataPendaftaran)) {
                                $registration = [
                                    'no_pendaftaran' => $dataPendaftaran['no_pendaftaran'],
                                    'instalasi_kode' => [DocoConstants::INSTALASI_RAWAT_JALAN]
                                ];

                                (new UpdateEklaimService)->updateTglPulang($registration);
                            }
                            
                            return [
                                'message' => 'Data Berhasil di simpan',
                                'pendaftaran_id' => $id
                            ];
                        } else {
                            $transaction->rollBack();
                            $errors = DocoHelpers::parseError($modelPasienPulang->errors, 'PasienPulang');
                            return [
                                'data' => $errors,
                                'status' => 422
                            ];
                        };
                        # code...
                    } else {
                        // konsul poli
                        $modelKonsulpoli = Konsulpoli::findOne($request->post('konsulpoliId'));
                        $modelKonsulpoli['status_periksa'] = (string)$statusPeriksa;
                        $modelKonsulpoli['pasienpulang_id'] = $modelPasienPulang->pasienpulang_id;

                        if ($modelKonsulpoli->save()) {
                            $modelPasienPulang->ruanganakhir_id = $modelKonsulpoli->ruangan_id;
                            $modelPasienPulang->save();

                            $transaction->commit();

                            /* update tgl pulang sync eklaim BPJS - Pemulangan Rawat Jalan */
                            $dataPendaftaran = Pendaftaran::find()
                                                ->select(['pendaftaran_id', 'no_pendaftaran'])
                                                ->andWhere(['pendaftaran_id' => $id])
                                                ->asArray()->one();

                            if(!empty($dataPendaftaran)) {
                                $registration = [
                                    'no_pendaftaran' => $dataPendaftaran['no_pendaftaran'],
                                    'instalasi_kode' => [DocoConstants::INSTALASI_RAWAT_JALAN]
                                ];

                                (new UpdateEklaimService)->updateTglPulang($registration);
                            }

                            return [
                                'message' => 'Data Berhasil di simpan',
                            ];
                        } else {
                            $transaction->rollBack();
                            $errors = DocoHelpers::parseError($modelKonsulpoli->errors, 'PasienPulang');
                            return [
                                'data' => $errors,
                                'status' => 422
                            ];
                        };
                    }
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($modelPasienPulang->errors, 'PasienPulang');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        // } catch (\yii\db\Exception $e) {
        //     $transaction->rollBack();
        //     \Yii::$app->response->statusCode = 500;
        //     Yii::error([
        //         'msg' => $e->getMessage()
        //     ]);
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     // Status code
        //     $transaction->rollBack();
        //     \Yii::$app->response->statusCode = 500;
        //     Yii::error([
        //         'msg' => $e->getMessage()
        //     ]);
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
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
     * @attribute #subsidi_asuransi# => subsidi asuransi
     **/

    public function actionPrintRincian($id)
    {
        $header = InfoDataPendaftaran::find()->where([
            'pendaftaran_id' => $id
        ])->one();
        // return $header;
        $tagihan_detail = Yii::$app->db->createCommand("
            SELECT * FROM rincian_header_tagihan_pasien WHERE pendaftaran_id = {$id}
        ")->queryOne();

        $detail = Yii::$app->db->createCommand("
            SELECT * FROM infotagihandetail_v WHERE pendaftaran_id = {$id}
        ")->queryAll();

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
                '#detail_tindakan#' => $this->renderPartial('riwayat_pemeriksaan', [
                    'detail' => isset($listData[$id]) ? $listData[$id] : []
                ]),
            ];

            $print->Output();
        }
    }

    public function actionSaveTemplate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $data_template = $data['data_template'];
            $data_template_detail = $data['data_template_detail'];
            $racikanKode = [];

            $list_racikan = Racikan::find()->all();
            $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

            $modelResepTemp = new ResepTemp;
            $modelResepTemp->attributes = $data_template;

            if ($modelResepTemp->validate()) {
                if ($modelResepTemp->save()) {
                    $idResepTemp = $modelResepTemp->reseptemp_id;
                    // define missing attributes
                    foreach ($data_template_detail as $key => $value) {
                        $data_template_detail[$key]['reseptemp_id'] = $idResepTemp;
                        $data_template_detail[$key]['racikan_id'] = $list_racikan[$value['racikan_id']];
                        $data_template_detail[$key]['harga'] = $value['qty'];
                        if ($value['rke'] == 'null') {
                            $data_template_detail[$key]['rke'] = '';
                        }
                    }

                    $a = ResepTempDetail::batchInsert($data_template_detail);
                    $transaction->commit();
                    $data_template_dokter = ResepTemp::find()->where([
                        'dokter_id' => $data_template['dokter_id']
                    ])->all();

                    $return = ['message' => 'Data Berhasil di simpan', 'data' => $data_template_dokter];
                } else {
                    $transaction->rollBack();
                }
            } else {
                $errors = DocoHelpers::parseError($modelResepTemp->errors, 'ResepTemp');
                $return = [
                    'data' => $errors,
                    'message' => $errors,
                    'status' => 422
                ];

                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
        }
    }

    public function actionAutoDiagnosa()
    {
        try {
            $request = Yii::$app->request;

            $get = $request->post();

            $model = new Diagnosa;
            $query = $model::find()->select(['diagnosa_id', 'diagnosa_nama', 'is_deleted', 'is_active'])->where(['is_deleted' => false, 'is_active' => true])->groupBy(['diagnosa_id', 'diagnosa_nama', 'is_deleted', 'is_active']);

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
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan icd ruangan by versi tabular
     * @author Sigit Arif Munandar <sigit@docotel.com>
     **/
    public function actionGetDiagnosaByVersiTabular()
    {
        try {
            $params = Yii::$app->request->get();

            $model = DiagnosaView::find();

            // if (isset($params['ruangan_id']) && $params['ruangan_id'] != '') {
            //     $model->andWhere(['ruangan_id' => $params['ruangan_id']]);
            // }

            if (isset($params['versi_tabular']) && $params['versi_tabular'] != '') {
                $model->andWhere(['tabularlist_versi' => $params['versi_tabular']]);
            }
            $model->limit(50);
            return $model->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * summary
     *
     * @return void
     * @author rizal faidin
     */
    public function actionCreateTerapiPenunjang()
    {
        try {
            $lookUpTransaksi = new LookUpTransaksiRepositories;
            $instalasiFisioId = $lookUpTransaksi->getInstalasiIdFisio();
            $statusProgramOpenId = $lookUpTransaksi->getStatusOpenProgramFisio();
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request;
            $posts = $request->post();
            $posts['tgl_kirimpasien'] = date('Y-m-d H:i:s', strtotime($posts['tgl_kirimpasien']));

            $posts['instruksi_id'] = null;
            $posts['pasienadmisi_id'] = null;
            $posts['list_order'] = isset($posts['list_order']) ? json_encode($posts['list_order']) : null;
            $posts['ruangan_asal'] = !empty($posts['ruangan_asal']) ? PelayananHelpers::decryptId($posts['ruangan_asal']) : null;

            if ($posts['instalasi_id'] == DocoConstants::VAR_I_LAB) {
                //hit api backend laboratorium
                $restLab = Yii::$app->docoRest->laboratorium;
                $request = $restLab->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == DocoConstants::VAR_I_RAD) {
                //hit api backend radiologi
                $restRad = Yii::$app->docoRest->radiologi;
                $request = $restRad->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == DocoConstants::INST_ID_BEDAH) {
                // set jadwal operasi
                $posts['jam_mulai'] = $posts['jadwal_operasi']['jam_mulai'];
                $posts['jam_selesai'] = $posts['jadwal_operasi']['jam_selesai'];
                $posts['dr_operator_id'] = $posts['jadwal_operasi']['dr_operator_id'];
                $posts['dr_anastesi_id'] = !empty($posts['jadwal_operasi']['dr_anestesi_id']) ? $posts['jadwal_operasi']['dr_anestesi_id'] : null;

                //hit api backend bedah
                $restBedah = Yii::$app->docoRest->bedah;
                $request = $restBedah->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == $instalasiFisioId) {
                //hit api backend fisioterapi
                $response = OrderRajalAction::saveOrder($posts);
            }

            if ($response['metadata']['status'] == 200) {
                $transaction->commit();
                $url = "/rajal/pemeriksaan/cetak-penunjang?id=#pendaftaran_id#&pasienkirimkeunitlain_id=#pasienkirimkeunitlain_id#";
                $keys = ['#pendaftaran_id#', '#pasienkirimkeunitlain_id#'];
                $replacements = [DocoHelpers::encrypt($posts['pendaftaran_id']), $response['response']['pasienkirimkeunitlain_id']];
                return [
                    'messages' => 'Data berhasil di simpan',
                    'pasienkirimkeunitlain_id' => $response['response']['pasienkirimkeunitlain_id'],
                    'url' => str_replace($keys, $replacements, $url),
                    'pendaftaran_id'=> DocoHelpers::encrypt($posts['pendaftaran_id']),
                ];
            } elseif ($response['metadata']['status'] == 422) {
                $transaction->rollBack();
                return [
                    'status' => 500,
                    'title' => 'Proses Gagal !',
                    'message' => $response['response']['text']
                ];
            } else {
                $transaction->rollBack();
                return [
                    'status' => 500,
                    'title' => 'Proses Gagal !',
                    'message' => $response['response']['text']
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            (new DocoHelpers)->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            (new DocoHelpers)->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionCetakPenunjang
     * @attribute #poliklinik# => poliklinik
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #no_rekam_medik# => no_rekam_medik
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #dokter_perujuk# => dokter_perujuk
     * @attribute #unit_penunjang# => unit_penunjang
     * @attribute #jenis_kelamin# => jenis_kelamin
     * @attribute #tgl_lahir# => tgl_lahir
     * @attribute #cara_bayar# => cara_bayar
     * @attribute #penjamin# => penjamin
     * @attribute #tgl_permintaan# => tgl_permintaan
     * @attribute #no_rujukan# => no_rujukan
     * @attribute #table_list_order# => table
     **/
    public function actionCetakPenunjang()
    {
        try {
            $request = Yii::$app->request;

            $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
            $pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id');

            $model = OrderPenunjangView::find()
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                ])->asArray()->all();
            $header = ArrayHelper::getValue($model, '0', []);
            $detail = $this->renderPartial('cetak_penunjang', ['model' => $model]);
            // return $detail;
            $print = new DocoPrint();

            $print->attributes = [
                '#poliklinik#' => ArrayHelper::getValue($header, 'ruangan_nama'),
                '#no_pendaftaran#' => ArrayHelper::getValue($header, 'no_pendaftaran'),
                '#no_rekam_medik#' => ArrayHelper::getValue($header, 'no_rekam_medik'),
                '#nama_pasien#' => ArrayHelper::getValue($header, 'nama_pasien'),
                '#dokter_perujuk#' => ArrayHelper::getValue($header, 'dokter_perujuk'),
                '#unit_penunjang#' => ArrayHelper::getValue($header, 'instalasi_penunjang') . ' - ' . ArrayHelper::getValue($header, 'ruangan_penunjang'),
                '#jenis_kelamin#' => ArrayHelper::getValue($header, 'jenis_kelamin'),
                '#tgl_lahir#' => date('d-m-Y', strtotime(ArrayHelper::getValue($header, 'tanggal_lahir'))),
                '#cara_bayar#' => ArrayHelper::getValue($header, 'carabayar_nama'),
                '#penjamin#' => ArrayHelper::getValue($header, 'penjamin_nama'),
                '#tgl_permintaan#' => date('d-m-Y H:i:s', strtotime(ArrayHelper::getValue($header, 'tgl_kirimpasien'))),
                '#no_rujukan#' => ArrayHelper::getValue($header, 'no_orderkeunitlain'),
                '#table_list_order#' => $detail,
            ];
            $print->Output();
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

    /**
     * summary
     *
     * @return list riwayat penunjang
     * @author rizal
     */
    public function actionGetRiwayatPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $model = new RiwayatPenunjangDetailView;
            $query = RiwayatPenunjangDetailView::find()
                ->select('tgl_kirimpasien, pasienkirimkeunitlain_id, instalasi_nama, ruangan_nama, no_orderkeunitlain, nama_pegawai, catatan_dokterpengirim, catatan, status, alasan_batal')
                ->where([
                    // 'instalasi_id' => $request->get('instalasi_id', null),
                    'pendaftaran_id' => $request->get('pendaftaran_id'),
                ])
                ->groupBy('tgl_kirimpasien, pasienkirimkeunitlain_id, instalasi_nama, ruangan_nama, no_orderkeunitlain, nama_pegawai, catatan_dokterpengirim, catatan, status, alasan_batal');
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            // return $query->createCommand()->getRawSql();
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
    public function actionIsDokter()
    {
        return true;
    }
    public function actionBundlePasienPulang()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $konsulpoli_id = $request->get('konsulpoli_id', null);
        $jeniskelamin = $hubungan_keluarga = $carakeluar = [];
        $state = true;
        $message = null;
        try {
            /*
            * konfig untuk task jkn rpp 930 *kedepannya kudu direfactor
            */
            $get_konfig_task_jkn = LookupTransaksi::find()->select([
                'additional_value'
            ])
            ->where(['kode_transaksi' => DocoConstants::KONFIG_FLOW_TASKID])
            ->asArray()->one();
            
            if ($get_konfig_task_jkn['additional_value'] && $get_konfig_task_jkn['additional_value'] != 'false' && $get_konfig_task_jkn['additional_value'] != '' ) {
                $soap_dokter = SoapRj::find()->select('soaprj_id')->innerJoin('pegawai_m', 'pegawai_m.pegawai_id = soaprj_t.pegawai_id')
                                  ->where(['soaprj_t.pendaftaran_id' => $id, 'pegawai_m.kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER])->asArray()->one(); 
                $resume_medis = ResumeMedisRi::find()->where(['pendaftaran_id' => $id])->asArray()->one();
                if (!$soap_dokter || !$resume_medis) {
                    return DocoHelpers::responseTemplate(422, 'Pasien belum memiliki resume medis dan soap dokter, silahkan isi resume medis dan soap dokter terlebih dahulu');
                }
            }
            
            $find = $this->getPasienDetail($id)->asArray()->one();
            $carakeluar = CaraKeluar::getCaraKeluar();
            $listDokterSpesialis = [];
            $getLookup = Lookup::find()->where(['lookup_type' => ['hubungan_keluarga', 'jenis_kelamin']])->asArray()->all();
            $get_konfig_keramat = LookupTransaksi::find()->select([
                'additional_value'
            ])
            ->where(['kode_transaksi' => 'keramat_spri'])
            ->asArray()->one();
            $konfig_keramat_spri = false;
            if(isset($get_konfig_keramat)){
                $konfig_keramat_spri = json_decode($get_konfig_keramat['additional_value']);
            }
            foreach ($getLookup as $key => $value) {
                if ($value['lookup_type'] == 'hubungan_keluarga') {
                    $hubungan_keluarga[] = $value;
                }
                if ($value['lookup_type'] == 'jenis_kelamin') {
                    $jeniskelamin[] = $value;
                }
            }
            $data_morbiditas = count(PasienMorbiditas::find()->where(['pendaftaran_id' => $id])->asArray()->all());
            $data_anamnesa = Anamnesa::find()->where(['pendaftaran_id' => $id])->asArray()->one();
            if (empty($data_anamnesa)) {
                if ($data_morbiditas > 0) {
                    $state = false;
                    $message = 'Keluhan utama belum di inputkan';
                } else {
                    $state = false;
                    $message = 'Diagnosa utama dan keluhan utama belum di inputkan';
                }
            } else {
                if ($data_anamnesa["keluhan_utama"] == "" and $data_morbiditas == 0) {
                    $state = false;
                    $message = 'Diagnosa utama dan keluhan utama belum di inputkan';
                } else {
                    if ($data_anamnesa["keluhan_utama"] == "" and $data_morbiditas > 0) {
                        $state = false;
                        $message = 'Keluhan utama belum di inputkan';
                    } elseif ($data_anamnesa["keluhan_utama"] != "" and $data_morbiditas == 0) {
                        $state = false;
                        $message = 'Diagnosa utama belum di inputkan';
                    }
                }
            }

            $query = TerapiRjView::find()
            ->select(['tgl_tindakan', 'grouping_tipe', 'instalasi_penunjang_id', 'tindakan_nama'])
            ->andWhere(['pendaftaran_id' => $id]);

            $query->orderBy(['tgl_tindakan' => SORT_ASC]);

            $listInstruksi = $query->asArray()->distinct()->all();
            $no = 0;
            $catatan_tindakan = '';
            if(isset($listInstruksi)){
                foreach ($listInstruksi as $instruksi) {
                    if($instruksi['grouping_tipe'] == 'Tindakan & BMHP' ||
                        ($instruksi['grouping_tipe'] == 'Penunjang' && in_array($instruksi['instalasi_penunjang_id'], [DocoConstants::INST_ID_LAB, DocoConstants::INST_ID_RAD,DocoConstants::INST_ID_BEDAH]))) {
                            $no++;
                            $catatan_tindakan .= $no.". Pemeriksaan - " . $instruksi['tindakan_nama'] . "\n";
                        }
                }
            }

            $instruksi_gizi = InfoInstruksiView::find()
            ->select(['tgl_instruksi', 'tipe_instruksi', 'catatan_instruksi'])
            ->andWhere(['pendaftaran_id' => $id])
            ->orderBy(['tgl_instruksi' => SORT_ASC])
            ->asArray()->distinct()->all();

            if(isset($instruksi_gizi)){
                foreach ($instruksi_gizi as $instruksi) {
                    if($instruksi['tipe_instruksi'] == 'DIET' ) {
                            $no++;
                            $catatan_tindakan .= $no.". Pemeriksaan - " . $instruksi['catatan_instruksi'] . "\n";
                        }
                }
            }

            $modulKonsulPoli = Konsulpoli::find()->where(['konsulpoli_id' => $konsulpoli_id])->one();
            $find['tgl_konsulpoli'] = (empty($modulKonsulPoli->tgl_konsulpoli)) ? null : $modulKonsulPoli->tgl_konsulpoli;
            return DocoHelpers::responseTemplate(200, 'Data Berhasil Ditemukan', [
                'datapasien' => $find,
                'carakeluar' => $carakeluar,
                'morbiditas' => [
                    'state' => true, //set true for hide warning at pemulangan form
                    'message' => $message
                ],
                'hubungan_keluarga' => $hubungan_keluarga,
                'jeniskelamin' => $jeniskelamin,
                'konfig_keramat_spri' => $konfig_keramat_spri,
                'catatan_tindakan' => $catatan_tindakan
            ]);
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => 500
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function actionCariTindakanJenazah($ruangan_id = null)
    {
        $params = Yii::$app->request;
        $term = $params->get('term', '');
        $pendaftaran_id = $params->get('pendaftaran_id', 0);

        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);

        $dataKunjungan = InfoKunjunganRajal::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        if (is_null($dataKunjungan)) {
            throw new \Exception("Terjadi Kesalahan Pada ID Pendaftaran", 1);
        }

        $dataTindakan = InfoTarifRs::find()
            ->select("
                        infotarifrs_v.*,
                        array_to_json(ARRAY(
                            SELECT
                                (
                                    SELECT row_to_json(d)
                                    FROM (SELECT ins.*) d
                                )::text as item_list
                            FROM infotarifrs_v ins
                            WHERE
                            ins.daftartindakan_id = infotarifrs_v.daftartindakan_id AND
                            ins.ruangan_id = '{$ruangan_id}' AND
                            ins.tipepaket_id IS NULL AND
                            ins.komponentarif_id <> 6 AND
                            ins.kelaspelayanan_id = '{$dataKunjungan->kelaspelayanan_id}' AND
                            ins.penjamin_id = '{$dataKunjungan->penjamin_id}'
                        )) as list_komponen
                        ")
            ->where('tipepaket_id IS NULL')
            ->andWhere(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF])
            ->andWhere(['kelaspelayanan_id' => $dataKunjungan->kelaspelayanan_id])
            ->andWhere(['penjamin_id' => $dataKunjungan->penjamin_id]);

        if ($ruangan_id) {
            $dataTindakan->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($term) {
            $dataTindakan->andWhere("LOWER(infotarifrs_v.daftartindakan_nama) LIKE LOWER('%" . $term . "%')");
        }

        return $dataTindakan->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionCariObatJenazah()
    {
        try {
            $params = Yii::$app->request;
            $term = $params->get('term', '');
            $ruangan_id = $params->get('ruangan_id', null);

            $page = $params->get('page', 0);
            $limit = $params->get('limit', 5);
            $offset = $params->get('offset', 0);
            $data = InfoStokObatAlkesFn::find()->select(['obatalkes_nama', 'obatalkes_id', 'ruangan_id', 'obatalkes_kode', 'qty_tersedia', 'satuankecil_id', 'satuankecil_nama', 'hargaygdipakai as hargajual', 'harganetto_ygdipakai as harganetto', 'jml_margin as jmlmargin', 'jml_discount as jmldiscount', 'jml_ppn as jmlppn', 'persen_ppn as persenppn', 'persen_disc as persendiscount', 'persen_margin as persenmargin'])->where(['ruangan_id' => $ruangan_id]);
            if ($term) {
                $data->andWhere("LOWER(infostokobatalkes_v.obatalkes_nama) LIKE LOWER('%" . $term . "%')");
            }

            $items = $data->offset($offset)->limit($limit)->asArray()->all();

            return $items;
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

    public function actionCariLinenJenazah()
    {
        $params = Yii::$app->request;
        $term = $params->get('term', '');
        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);
        try {
            $getData = Barang::find()->where(['is_active' => TRUE, 'is_deleted' => FALSE]);
            if ($term) {
                $getData->andWhere(['ILIKE', 'barang_nama', $term]);
            }
            return $getData->offset($offset)->limit($limit)->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionCariAlatJenazah()
    {
        $params = Yii::$app->request;
        $term = $params->get('term', '');
        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);
        try {
            $getData = ObatAlkes::find()->where(['is_active' => TRUE, 'is_deleted' => FALSE]);
            if ($term) {
                $getData->andWhere(['ILIKE', 'obatalkes_nama', $term]);
            }
            return $getData->offset($offset)->limit($limit)->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }
    /*
        Trigger Order Pelayanan Jenazah Ketika Transaksi Pulang
    *   return: boolean
    */
    protected function orderPelayananJenazah($pendaftaran_id, $form_attribute)
    {
        try {
            $restJenazah = Yii::$app->docoRest->jenazah;
            $request = $restJenazah->post('order/create', [
                'query' => ['id' => $pendaftaran_id],
                'form_params' => $form_attribute
            ]);
            $response = json_decode($request->getBody(), true);
            return $response;
        } catch (RequestException $e) {
            return false;
        } catch (\yii\base\Exception $e) {
            return false;
        }
    }

    public function actionGetKonsulPoli()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('id', null);
            $preview_only = $request->get('preview_only', false);
            $model = new Infokonsulpoli;
            $query = $model->find()->where(['pendaftaran_id' => $pendaftaran_id]);
            if($preview_only == true) {
                $query->andWhere(['NOT', ['status_konsul_id' => DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK]]);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi create konsul poli
     * @return array response
     *
     */
    public function actionUpdatePermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $data = $request->post();
                $konsulpoli_id = DocoHelpers::decrypt($data['konsulpoli_id']);
                $modelPayload = new ParamModel;
                $modelPayload->scenario = 'permintaan-konsul';
                $modelPayload->attributes = $data;
                $modelPayload->status_konsul = DocoConstants::STATUS_KONSUL_DIJAWAB;
                $modelPayload->tgl_selesaikonsul = date('Y-m-d H:i:s');
                $modelPayload->pendaftaran_id = DocoHelpers::decrypt($modelPayload->pendaftaran_id);
                if ($modelPayload->validate()) {
                    $pendaftaran_id = $modelPayload->pendaftaran_id;
                    $ruangan_id = $modelPayload->ruangan_id;

                    $model = Konsulpoli::find()
                        // ->where(['pendaftaranbaru_id' => $pendaftaran_id, 'ruangan_id' => $ruangan_id])
                        ->where(['konsulpoli_id' => $konsulpoli_id])
                        ->one();

                    $model->ruangan_id = $ruangan_id;
                    $model->pegawai_id = $modelPayload->pegawai_id;
                    $model->tgl_selesaikonsul = $modelPayload->tgl_selesaikonsul;
                    $model->jawaban_konsul = $modelPayload->jawaban_konsul;
                    $model->status_konsul = $modelPayload->status_konsul;
                    if (!$model->validate()) {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }

                    $model->save();
                } else {
                    return [
                        'data' => $modelPayload->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getLantai()
    {
        $sql = "SELECT lantai_id, lantai_nama, gedung FROM lantai_m WHERE is_deleted = FALSE AND is_active = TRUE";
        $result = Lantai::findBySql($sql);

        return $result;
    }

    public function actionGetDataJadwalOperasi()
    {
        try {
            $request = Yii::$app->request;
            $restBedah = Yii::$app->docoRest->bedah;
            $response = $restBedah->get('jadwal-operasi/index', [
                'query' => $request->get()
            ]);
            $response = json_decode($response->getBody(), true);

            return $response;
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

    /**
     * @controller actionCetakRincianTagihan
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
    public function actionCetakRincianTagihan()
    {
        $request = Yii::$app->request;
        try {
            $id = $request->get('id', null);
            $header = InfoDataPendaftaran::find()
                ->select([
                    'infodatapendaftaran_v.tgl_pendaftaran',
                    'infodatapendaftaran_v.pendaftaran_id',
                    'infodatapendaftaran_v.no_rekam_medik',
                    'infodatapendaftaran_v.no_pendaftaran',
                    'infodatapendaftaran_v.nama_pasien',
                    'infodatapendaftaran_v.nama_dok_rj_rd',
                    'infodatapendaftaran_v.rua_nama',
                    'infodatapendaftaran_v.kelaspelayanan_nama',
                    'infodatapendaftaran_v.penjamin_nama',
                    'infodatapendaftaran_v.carabayar_nama',
                    'lookup_m.lookup_name AS status_bayar'
                ])
                ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id = infodatapendaftaran_v.status_bayar')
                ->where(['infodatapendaftaran_v.pendaftaran_id' => $id])->asArray()->one();

            $detail = InfoTagihanDetailView::find()
                ->where(['pendaftaran_id' => $id])
                ->orderBy(['tindakan_obat_nama' => SORT_ASC])
                ->all();

            $print = new DocoPrint();
            $print->attributes = [
                '#tgl_pendaftaran#' => date('d/M/Y', strtotime($header['tgl_pendaftaran'])),
                '#no_rekam_medik#' => isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : ' - ',
                '#no_pendaftaran#' => isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : ' - ',
                '#nama_pasien#' => isset($header['nama_pasien']) ? $header['nama_pasien'] : ' - ',
                '#nama_dok_rj_rd#' => isset($header['nama_dok_rj_rd']) ? $header['nama_dok_rj_rd'] : ' - ',
                '#rua_nama#' => isset($header['rua_nama']) ? $header['rua_nama'] : ' - ',
                '#kelaspelayanan_nama#' => isset($header['kelaspelayanan_nama']) ? $header['kelaspelayanan_nama'] : ' - ',
                '#penjamin_nama#' => isset($header['penjamin_nama']) ? $header['penjamin_nama'] : ' - ',
                '#carabayar_nama#' => isset($header['carabayar_nama']) ? $header['carabayar_nama'] : ' - ',
                '#status_bayar#' => isset($header['status_bayar']) ? $header['status_bayar'] : ' - ',
                '#table#' => $this->renderPartial('rincian_rajal', [
                    'data' => $detail,
                ]),
            ];

            $print->Output();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDokterList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = DokterView::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ])
            ->andWhere([
                'ruangan_id' => Yii::$app->jwt->ruangan_id
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'LOWER(nama_pegawai)',
                strtolower($term)
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionPerawatList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = PegawaiView::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ])
            ->andWhere([
                'kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_PERAWAT
            ])
            ->andWhere([
                'ruangan_id' => Yii::$app->jwt->ruangan_id
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'LOWER(nama_pegawai)',
                strtolower($term)
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionSukuList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = Suku::find()
            ->select([
                'suku_id as id',
                'suku_nama as text'
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'LOWER(suku_nama)',
                strtolower($term)
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionCaraBayarList()
    {
        $query = CaraBayar::find()
            ->select([
                'carabayar_nama',
                'carabayar_kode_warna'
            ])
            ->andWhere(['not', ['is_active' => false]])
            ->orderBy(['carabayar_kode_warna' => SORT_ASC])
            ->asArray()
            ->all();
        return $query;
    }

    public function actionAllDokterList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = Pegawai::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ]);

        $query = $query->where(['kelompokpegawai_id' => '1'])
            ->orderBy(['nama_pegawai' => SORT_ASC]);

        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'LOWER(nama_pegawai)',
                strtolower($term)
            ]);
        }

        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionHistoryCpptTerraMedik()
    {
        $params       = Yii::$app->request;
        $pasien_terra = $params->get('pasien_terra', 0);
        $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

        $data_cppt = RiwayatSoapTerra::find()
            ->where(['pasien_id' => $pasien_terra])
            ->orderBy(['tgl_soap' => SORT_DESC]);

        if (isset($_GET['advanced-filter']['dokter_id'])) {
            $data_cppt = $data_cppt->andWhere(['dokter_id' => $_GET['advanced-filter']['dokter_id']]);
        }

        $total = count($data_cppt->asArray()->all());

        $data_cppt = $data_cppt->limit($_GET['per-page'])
            ->offset(($_GET['page'] - 1) * $_GET['per-page']);
        $data_cppt = $data_cppt->asArray()->all();

        return [
            'data'  => $data_cppt,
            'total' => $total,
        ];
    }

    public function actionHistoryResepTerraMedik()
    {
        $params       = Yii::$app->request;
        $pasien_terra = $params->get('pasien_terra', 0);
        $pegawai_id   = Yii::$app->jwt->user->pegawai_id;

        $data_resep = RiwayatResepTerra::find()
            ->select(['id', 'kode_trans', 'kode_trans_detil', 'no_resep', 'tgl_transaksi', 'no_rm', 'riwayat_resep.nama_pasien', 'nama_barang', 'satuan', 'jumlah', 'concat(signa,\' \',signa_tambahan) as signa', 'pegawai_m.pegawai_id as dokter_id'])
            ->leftJoin('pendaftaran_t', 'pendaftaran_t.no_pendaftaran = history.riwayat_resep.regis_id')
            ->leftJoin('pegawai_m', 'pegawai_m.nama_pegawai = history.riwayat_resep.nama_dokter')
            ->where(['pendaftaran_t.pasien_id' => $pasien_terra])
            ->orderBy(['tgl_transaksi' => SORT_DESC]);

        if (isset($_GET['advanced-filter']['dokter_id'])) {
            $data_resep = $data_resep->andWhere(['dokter_id' => $_GET['advanced-filter']['dokter_id']]);
        }

        $total = count($data_resep->asArray()->all());

        $data_resep = $data_resep->limit($_GET['per-page'])
            ->offset(($_GET['page'] - 1) * $_GET['per-page']);
        $data_resep = $data_resep->asArray()->all();

        return [
            'data'  => $data_resep,
            'total' => $total,
        ];
    }

    public function actionIcare() { // untuk hak akses button icare
        return $this->getUrlIcare();
    }

    private function createSuratKontrol($pendaftaran, $modelKonsulpoli, $jadwaldokter_id)
    {
        $error_message_rencana_kontrol = [];
        $res_create_rencana_kontrol = [];

        $tgl_rencana_kontrol = date('Y-m-d', strtotime($modelKonsulpoli->tgl_konsulpoli));

        // $data_dokter = Pegawai::find()->select(['pegawai_m.kode_dokter_bpjs', 'pegawai_m.nama_pegawai', 'rm2.ruangan_nama', 'rm2.kode_ruangan_bpjs'])
        //     ->leftJoin('ruanganpegawai_mp rm', 'rm.pegawai_id = pegawai_m.pegawai_id AND rm.is_deleted IS FALSE')
        //     ->leftJoin('ruangan_m rm2', 'rm2.ruangan_id = rm.ruangan_id AND rm2.is_deleted IS FALSE')
        //     ->where(['pegawai_m.pegawai_id' => $pegawai_id]);
        //     $data_dokter->asArray()->one();

        $data_dokter = JadwalDokter::find()->select(['pm.kode_dokter_bpjs', 'pm.nama_pegawai', 'rm.ruangan_nama', 'rm.kode_ruangan_bpjs'])
            ->leftJoin('ruangan_m rm', 'rm.ruangan_id = jadwaldokter_m.ruangan_id')
            ->leftJoin('pegawai_m pm', 'pm.pegawai_id = jadwaldokter_m.pegawai_id')
            ->where(['jadwaldokter_m.jadwaldokter_id' => $jadwaldokter_id])
            ->asArray()->one();

        if (!empty($data_dokter)) {
            if (empty($data_dokter['kode_dokter_bpjs'])) array_push($error_message_rencana_kontrol, 'Kode dokter bpjs belum dimapping');

            if (empty($data_dokter['kode_ruangan_bpjs'])) array_push($error_message_rencana_kontrol, 'Kode ruangan bpjs tidak ditemukan');
        } else {
            array_push($error_message_rencana_kontrol, 'Dokter konsul tidak ditemukan');
        }

        if (empty($error_message_rencana_kontrol)) {
            // // get nama poli by bpjs
            $res_data_poli = (new BpjsCore)->dataPoliRencanaKontrol(DocoConstants::RK_JNS_KONTROL_RK, $pendaftaran['nosep'], $tgl_rencana_kontrol);

            if (ArrayHelper::getValue($res_data_poli, 'metaData.code', 500) == 200) {
                $data_poli = ArrayHelper::getValue($res_data_poli, 'response.list', []);
                // check ruangan konsul tersedia untuk pada cek api data poli bpjs dengan rencana kontrol sesuai tgl konsulnya
                $is_poli_exist = array_values(array_filter($data_poli,
                    function ($val) use ($data_dokter) {
                        return $val['kodePoli'] == $data_dokter['kode_ruangan_bpjs'];
                    }
                ));

                if (!empty($is_poli_exist)) {
                    $payload_create_kontrol = [
                        'RencanaKontrolForm' => [
                            'no_sep' => $pendaftaran['nosep'],
                            'bpjs_id' => $pendaftaran['bpjs_id'],
                            'pendaftaran_id' => $pendaftaran['pendaftaran_id'],
                            'tgl_rencana_inap' => null,
                            'tgl_rencanakontrol' => $modelKonsulpoli->tgl_konsulpoli,
                            'jenis_pelayanan' => 'Rawat Jalan',
                            'nama_spesialis' => ArrayHelper::getValue($is_poli_exist, '0.namaPoli'),
                            'kode_poli' => $data_dokter['kode_ruangan_bpjs'],
                            'dokterdpjp_kode' => $data_dokter['kode_dokter_bpjs'],
                            'dokterdpjp_nama' => $data_dokter['nama_pegawai'],
                            'no_kartu' => $pendaftaran['nokartuasuransi'],
                            'konsulpoli_id' => $modelKonsulpoli->konsulpoli_id,
                        ],
                        'user' => Yii::$app->jwt->user->nama_pemakai,
                        'jenis_rencana' => 1, // 1 = RJ, 2 = RI. tidak bisa mennggunakan dococonstants karena saling berbeda
                    ];

                    $res_create_rencana_kontrol = Yii::$app->docoRest->pendaftaran->post('rencana-kontrol-inap/create', [
                        'form_params' => $payload_create_kontrol
                    ]);

                    $res_create_rencana_kontrol = json_decode($res_create_rencana_kontrol->getBody(), true);

                    if (ArrayHelper::getValue($res_create_rencana_kontrol, 'metadata.status') == 200) {
                        if (ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.metaData.code') != 200) {
                            array_push($error_message_rencana_kontrol, ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.metaData.message'));
                        }
                    } else {
                        array_push($error_message_rencana_kontrol, ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.metaData.message'));
                    }
                } else {
                    array_push($error_message_rencana_kontrol, 'Poli tujuan untuk rencana kontrol tidak tersedia');
                }
            } else {
                array_push($error_message_rencana_kontrol, 'Terjadi kesalahan saat komunikasi dengan server BPJS');
            }
        }

        return [
            'data' => ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.response', []),
            'error_message_rencana_kontrol' => $error_message_rencana_kontrol
        ];
    }
}
