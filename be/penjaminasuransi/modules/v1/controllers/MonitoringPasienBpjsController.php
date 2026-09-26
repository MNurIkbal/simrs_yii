<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\MonitorSetDiagnosa;
use app\modules\v1\models\InfoMonitoringBpjsView;
use app\modules\v1\models\InfoMonitoringBpjsPersenView;
use app\modules\v1\models\MonitorBpjsPersen;
use app\modules\v1\models\InfoMonitoringBpjsDetailView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\Lookup;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\UploadForm;

class MonitoringPasienBpjsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoMonitoringBpjsView';

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
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = $this->getData();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoMonitoringBpjsView;
        $query = $model::find();

        $title = 'Monitoring Pasien Rawat Inap BPJS';

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $no_rekam_medik = $nama_pasien = $dokter_dpjp = 
        $penjamin_nama = $ruangan_nama = $namakamar = $statusMonitor = '';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran_awal']) && isset($_GET['advanced-filter']['tgl_pendaftaran_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_pendaftaran_awal'];
                $end = $_GET['advanced-filter']['tgl_pendaftaran_akhir'];
            }

            if(isset($_GET['advanced-filter']['dokter_dpjp'])) {
                $pegawai_id = $_GET['advanced-filter']['dokter_dpjp'];
                $query->andWhere(['pegawai_id' => $pegawai_id]);
                $dokter = Pegawai::findOne($pegawai_id);
                $dokter_dpjp = $dokter->nama_pegawai;
                unset($_GET['advanced-filter']['dokter_dpjp']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                $penjamin = Penjamin::findOne($penjamin_id);
                $penjamin_nama = $penjamin->penjamin_nama;
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                $ruangan = Ruangan::findOne($ruangan_id);
                $ruangan_nama = $ruangan->ruangan_nama;
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if(isset($_GET['advanced-filter']['kamarruangan_nokamar'])) {
                $kamarruangan_id = $_GET['advanced-filter']['kamarruangan_nokamar'];
                $query->andWhere(['kamarruangan_id' => $kamarruangan_id]);
                $kamar = KamarRuangan::findOne($kamarruangan_id);
                $namakamar = $kamar->kamarruangan_nokamar;
                unset($_GET['advanced-filter']['kamarruangan_nokamar']);
            }

            if(isset($_GET['advanced-filter']['status_monitor'])) {
                $status_monitor_id = $_GET['advanced-filter']['status_monitor'];
                $query->andWhere(['status_monitor_id' => $status_monitor_id]);
                $statusMonitor = ($status_monitor_id == 0) ? 'Belum di Monitor' : 'Sudah di Monitor';
                unset($_GET['advanced-filter']['status_monitor']);
            }

            if(isset($_GET['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $_GET['advanced-filter']['nama_pasien'];
            }
        }

        $periode = ((date('d M Y', strtotime($start))." - ".date('d M Y', strtotime($end))));

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->orderBy(['tgl_pendaftaran' => 'DESC']);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            
            $newValue[\Yii::t('app', 'Tanggal Masuk')] = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $newValue[\Yii::t('app', 'Tanggal Keluar')] = !empty($value['tglpasienpulang']) ?
                date('d M Y', strtotime($value['tglpasienpulang'])) : '';

            $diagnosaTindakanNama = $diagnosaPenyertaNama = '';
            if(!empty($value['set_diagnosatindakan'])) {
                $listDiagnosaTindakan = json_decode($value['set_diagnosatindakan'], true);
                if(!empty($listDiagnosaTindakan)) {
                    foreach ($listDiagnosaTindakan as $k => $v) {
                        $diagnosaTindakanNama = $v['kode'].' - '.$v['text'];
                    }
                }
            }

            if(!empty($value['set_diagnosapenyerta'])) {
                $listDiagnosaPenyerta = json_decode($value['set_diagnosapenyerta'], true);
                if(!empty($listDiagnosaPenyerta)) {
                    foreach ($listDiagnosaPenyerta as $k => $v) {
                        $diagnosaPenyertaNama = $v['kode'].' - '.$v['text'];
                    }
                }
            }

            if($value['tarif_inacbg'] == 0) {
                $persentase = 0;
            }
            else {
                $persentase = ceil(($value['tagihan_rs']/$value['tarif_inacbg']) * 100);
            }
        
            $newValue[\Yii::t('app', 'No Rekam Medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'No SEP')] = $value['nosep'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Kamar-Bed')] = $value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
            $newValue[\Yii::t('app', 'Hak Kelas')] = $value['hak_kelas'];
            $newValue[\Yii::t('app', 'Dokter Penanggung Jawab')] = $value['dokter_dpjp'];

            $newValue[\Yii::t('app', 'Diagnosa Utama')] = $value['set_diagnosautama'];
            $newValue[\Yii::t('app', 'Diagnosa Penyerta')] = $diagnosaPenyertaNama;
            $newValue[\Yii::t('app', 'Tindakan')] = $diagnosaTindakanNama;
            $newValue[\Yii::t('app', 'Tagihan RS')] = $value['tagihan_rs'];
            $newValue[\Yii::t('app', 'Tarif Inacbg')] = $value['tarif_inacbg'];
            $newValue[\Yii::t('app', 'Persentase')] = $persentase;
            $newValue[\Yii::t('app', 'Status')] = $value['status_monitor'];

            $header = [
                'Periode' => $periode,
                'No Rekam Medik' => $no_rekam_medik,
                'Nama Pasien' => $nama_pasien,
                'Dokter Penanggung Jawab' => $dokter_dpjp,
                'Penjamin' => $penjamin_nama,
                'Ruangan' => $ruangan_nama,
                'Kamar' => $namakamar,
                'Status' => $statusMonitor,
            ];

            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #tanggal# => tanggal sekarang
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Monitoring Pasien Rawat Inap BPJS';
        $get = $request->get();
        $model = new InfoMonitoringBpjsView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran_awal']) && isset($_GET['advanced-filter']['tgl_pendaftaran_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_pendaftaran_awal'];
                $end = $_GET['advanced-filter']['tgl_pendaftaran_akhir'];
            }
            
            if(isset($_GET['advanced-filter']['dokter_dpjp'])) {
                $pegawai_id = $_GET['advanced-filter']['dokter_dpjp'];
                $query->andWhere(['pegawai_id' => $pegawai_id]);
                unset($_GET['advanced-filter']['dokter_dpjp']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if(isset($_GET['advanced-filter']['kamarruangan_nokamar'])) {
                $kamarruangan_id = $_GET['advanced-filter']['kamarruangan_nokamar'];
                $query->andWhere(['kamarruangan_id' => $kamarruangan_id]);
                unset($_GET['advanced-filter']['kamarruangan_nokamar']);
            }

            if(isset($_GET['advanced-filter']['status_monitor'])) {
                $status_monitor_id = $_GET['advanced-filter']['status_monitor'];
                $query->andWhere(['status_monitor_id' => $status_monitor_id]);
                unset($_GET['advanced-filter']['status_monitor']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $data = $query->asArray()->all();
        // $ruangan_id = Yii::$app->jwt->ruangan_id;
        // $pegawai = PegawaiView::find()
        //     ->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])
        //     ->one();

        // $jabatan = ($pegawai) ? $pegawai->jabatan_nama : '';
        // $nip = ($pegawai) ? $pegawai->nomorindukpegawai : '';
        // $mengetahui = ($pegawai) ? $pegawai->nama_pegawai : '';
        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
            '#tanggal#' => date('d M Y'),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $data,
            ]),
        ];

        $print->Output();
    }

    public function actionGenerateApi()
    {
        // dokter dpjp
        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()
            ->where(['is_active' => true, 'kelompokpegawai_id' => 1])
            ->orderBy(['nama_pegawai' => 'ASC']);

        $queryDokter = $queryDokter->asArray()->all();

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()
            ->where(['is_active' => true, 'instalasi_id' => DocoConstants::INST_ID_RI])
            ->orderBy(['ruangan_nama' => 'ASC']);

        $queryRuangan = $queryRuangan->asArray()->all();

        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()
            ->where(['is_active' => true, 'carabayar_id' => DocoConstants::PENJAMIN_BPJS])
            ->orderBy(['penjamin_nama' => 'ASC']);

        $queryPenjamin = $queryPenjamin->asArray()->all();

        return [
            'dpjp' => $queryDokter,
            'penjamin' => $queryPenjamin,
            'ruangan' => $queryRuangan,
        ];
    }

    public function actionSave()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $diagUtamaId = ArrayHelper::getValue($post, 'diag_utama_id');
            $diagUtamaKode = ArrayHelper::getValue($post, 'diag_utama_kode');
            $pendaftaranId = ArrayHelper::getValue($post, 'pendaftaran_id');
            $pasienAdmisiId = ArrayHelper::getValue($post, 'pasienadmisi_id');
            $hakKelas = ArrayHelper::getValue($post, 'hak_kelas');
            $transaction = Yii::$app->db->beginTransaction();
            $expDiagUtamaKode = explode(' - ', $diagUtamaKode);
            if($expDiagUtamaKode) {
                $diagUtamaKode = ArrayHelper::getValue($expDiagUtamaKode, 0);
            }
            
            $post['diag_utama_kode'] = $diagUtamaKode;
            if (!empty($post)) {
                if(!empty($pendaftaranId) && !empty($pasienAdmisiId)) {
                    $model = MonitorSetDiagnosa::find()->where([
                        'pendaftaran_id' => $pendaftaranId,
                        'pasienadmisi_id' => $pasienAdmisiId
                    ])->orderBy('monitorsetdiagnosa_id DESC')->one();

                    if(is_null($model)) {
                        $model = new MonitorSetDiagnosa;
                    }

                    $model->attributes = $post;
                    $model->diag_utama_id = $diagUtamaId;
                    $hak_kelas = $hakKelas;
                    $tarifInaCbg = 0;
                    $dataInaCbg = $this->prosesBpjs($post);
                    if(!empty($dataInaCbg)) {
                        $dataInaCbg = json_decode($dataInaCbg, true);
                    }

                    $tarifAlt = ArrayHelper::getValue($dataInaCbg, 'tarif_alt', []);
                    if(!empty($tarifAlt)) {
                        foreach ($tarifAlt as $key => $value) {
                            $kelas = ArrayHelper::getValue($value, 'kelas');
                            if($kelas == 'kelas_'.$hak_kelas) {
                                $tarifInaCbg = ArrayHelper::getValue($value, 'tarif_inacbg', 0);
                            }
                        }
                    }

                    $model->total = $tarifInaCbg;
                    if ($model->save()) {
                        $transaction->commit();
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $transaction->rollBack();
                        $errors = DocoHelpers::parseError($model->errors, 'MonitorSetDiagnosaForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
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

    private function prosesBpjs($data)
    {

        $sepDummy = Lookup::find()
        ->select(['lookup_type', 'lookup_name', 'lookup_value'])
        ->where(['lookup_name' => 'bpjs_dummy'])
        ->asArray()
        ->one();

        $tarifInaCbg = Lookup::find()
        ->select(['lookup_type', 'lookup_name', 'lookup_value'])
        ->where(['lookup_name' => 'default_tarif'])
        ->asArray()
        ->one();

        if(empty($sepDummy)) {
            throw new \Exception("SEP Dummy tidak ditemukan!");
        }

        $noKartu = ArrayHelper::getValue($data, 'no_kartu');
        $noSep = ArrayHelper::getValue($sepDummy, 'lookup_value');
        $noRekamMedik = ArrayHelper::getValue($data, 'no_rekam_medik');
        $namaPasien = ArrayHelper::getValue($data, 'nama_pasien');
        $tanggalLahir = ArrayHelper::getValue($data, 'tanggal_lahir');
        $jenisKelamin = ArrayHelper::getValue($data, 'jeniskelamin');

        $hapusklaim = [
            'metadata' => [
                'method' => 'delete_claim',
            ],
            'data' => [
                'nomor_sep' => $noSep,
                'coder_nik' => $this->getCoderNik(),
            ],
        ];

        $hapusDataKlaim = json_decode(DocoHelpers::restInacbgs($hapusklaim), true);
        
        $newClaim['metadata']['method'] = 'new_claim';
        $newClaim['data']['nomor_kartu'] = $noKartu;
        $newClaim['data']['nomor_sep'] = $noSep;
        $newClaim['data']['nomor_rm'] = $noRekamMedik;
        $newClaim['data']['nama_pasien'] = $namaPasien;
        $newClaim['data']['tgl_lahir'] = $tanggalLahir;
        $newClaim['data']['gender'] = $jenisKelamin;
        $response = json_decode(DocoHelpers::restInacbgs($newClaim), true);
        if($response['metadata']['code'] != 200 ){
            if($response['metadata']['code'] != 400 && $response['metadata']['error_no'] != 'E2007'){
                throw new \Exception("Terjadi Kesalahan");
            }
        }

        $kodeProcedure = '#';
        $kodePenyerta = '';
        $diagTindakan = ArrayHelper::getValue($data, 'diag_tindakan');
        $diagPenyerta = ArrayHelper::getValue($data, 'diag_penyerta');
        $diagUtamaKode = ArrayHelper::getValue($data, 'diag_utama_kode');

        if(!empty($diagTindakan)) {
            $procedure = json_decode($diagTindakan, true);
            $kodeProcedure = [];
            if(!empty($procedure)) {
                foreach ($procedure as $key => $value) {
                    $kodeProcedure[] = ArrayHelper::getValue($value, 'kode');
                }
            }
            
            if(count($kodeProcedure) == 1) {
                $implodeKode = implode("", $kodeProcedure);
            } 
            else {
                $implodeKode = implode("#", $kodeProcedure);
            }

            $kodeProcedure = $implodeKode;
        }
        
        if(!empty($diagPenyerta)) {
            $penyerta = json_decode($diagPenyerta, true);
            $kodePenyerta = [];
            if(!empty($penyerta)) {
                foreach ($penyerta as $key => $value) {
                    $kodePenyerta[] = ArrayHelper::getValue($value, 'kode');
                }
            }
            
            if(count($kodePenyerta) == 1) {
                $implodeKode = implode("", $kodePenyerta);
            } 
            else {
                $implodeKode = implode("#", $kodePenyerta);
            }

            $kodePenyerta = $implodeKode;
        }

        $diagnosa = !empty($kodePenyerta) ? $diagUtamaKode.'#'.$kodePenyerta : $diagUtamaKode;
        $tgl_keluar = isset($data['tgl_keluar']) ? $data['tgl_keluar'] : date('Y-m-d H:i:s');
        $prosesKlaim['metadata']['method'] = 'set_claim_data';
        $prosesKlaim['metadata']['nomor_sep'] = $noSep;
        $prosesKlaim['data'] = [
            'nomor_sep' => $noSep,
            'nomor_kartu' => $noKartu,
            'tgl_masuk' => ArrayHelper::getValue($data, 'tgl_masuk'),
            'tgl_pulang' => $tgl_keluar,
            'jenis_rawat' => DocoConstants::CLAIM_RANAP,
            'kelas_rawat' => ArrayHelper::getValue($data, 'hak_kelas'),
            'discharge_status' => ArrayHelper::getValue($data, 'carapulang_id'),
            'diagnosa' => $diagnosa,
            'procedure' => !empty($kodeProcedure) ? $kodeProcedure : '#',
            "diagnosa_inagrouper" => $diagnosa,
            "procedure_inagrouper" => !empty($kodeProcedure) ? $kodeProcedure : '#',
            'nama_dokter' => ArrayHelper::getValue($data, 'dokter_dpjp'),
            'tarif_rs' =>[
                'prosedur_non_bedah' => ArrayHelper::getValue($data, 'prosedur_nonbedah', 0),
                'prosedur_bedah' => ArrayHelper::getValue($data, 'prosedur_bedah', 0),
                'konsultasi' => ArrayHelper::getValue($data, 'konsultasi', 0),
                'tenaga_ahli' => ArrayHelper::getValue($data, 'tenaga_ahli', 0),
                'keperawatan' => ArrayHelper::getValue($data, 'keperawatan', 0),
                'penunjang' => ArrayHelper::getValue($data, 'penunjang', 0),
                'radiologi' => ArrayHelper::getValue($data, 'radiologi', 0),
                'laboratorium' => ArrayHelper::getValue($data, 'laboratorium', 0),
                'pelayanan_darah' => ArrayHelper::getValue($data, 'pelayanan_darah', 0),
                'rehabilitasi' => ArrayHelper::getValue($data, 'rehabilitasi', 0),
                'kamar' => ArrayHelper::getValue($data, 'kamar', 0),
                'rawat_intensif' => ArrayHelper::getValue($data, 'rawat_intensif', 0),
                'obat' => ArrayHelper::getValue($data, 'obat', 0),
                'alkes' => ArrayHelper::getValue($data, 'alkes', 0),
                'bmhp' => ArrayHelper::getValue($data, 'bmhp', 0),
                'sewa_alat' => ArrayHelper::getValue($data, 'sewa_alat', 0),
            ],
            'kode_tarif'=> ArrayHelper::getValue($tarifInaCbg, 'lookup_value'),
            'coder_nik'=> $this->getCoderNik(),
            'payor_id' => $this->getPayorId(),
            'payor_cd' => $this->getPayorCd(),
        ];
        $klaim = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);
        $metadata = ArrayHelper::getValue($klaim, 'metadata', []);
        $code = ArrayHelper::getValue($metadata, 'code');
        $errorNo = ArrayHelper::getValue($metadata, 'error_no');
        $message = ArrayHelper::getValue($metadata, 'message');
        
        if(!empty($code) && $code != 200 ){
            $hapusklaim = [
                'metadata' => [
                    'method' => 'delete_claim',
                ],
                'data' => [
                    'nomor_sep' => $noSep,
                    'coder_nik' => $this->getCoderNik(),
                ],
            ];
            $hapus = json_decode(DocoHelpers::restInacbgs($hapusklaim), true);
            return [
                'status' => $code,
                'title' => $errorNo,
                'text' => $message
            ];
        }
        
        $grouping['metadata']['method'] = 'grouper';
        $grouping['metadata']['stage'] = '1';
        $grouping['data']['nomor_sep'] = $noSep;
        $groupResult = json_decode(DocoHelpers::restInacbgs($grouping), true);
        $metadataGroupResult = ArrayHelper::getValue($groupResult, 'metadata', []);
        $codeGroupResult = ArrayHelper::getValue($metadataGroupResult, 'code');
        $responseGroupResult = ArrayHelper::getValue($groupResult, 'response', []);
        $specialCmgOption = ArrayHelper::getValue($responseGroupResult, 'special_cmg_option', []);
        if(!empty($codeGroupResult) &&  $codeGroupResult != 200 ){
            throw new \Exception("Terjadi Kesalahan");
        }
        if(!empty($specialCmgOption)){
            $stage2 = [];
            foreach ($specialCmgOption as $key => $value) {
                $stage2[] = ArrayHelper::getValue($value, 'code');
            }
            $grouper2 = [
                'metadata'=>[
                    'method'=>'grouper',
                    'stage'=>"2"
                ],
                'data'=>[
                    'nomor_sep' => $noSep,
                    'special_cmg' => implode('#', $stage2),
                ],
            ];
            return DocoHelpers::restInacbgs($grouper2);
        }
        return json_encode($groupResult);
    }

    public function actionGetKelompokDiagnosa()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pasienadmisi_id = $request->get('pasienadmisi_id', null);
        $model = InfoMonitoringBpjsPersenView::find();
        if($pendaftaran_id && $pasienadmisi_id) {
            $model->andWhere([
                'pendaftaran_id' => $pendaftaran_id, 
                'pasienadmisi_id' => $pasienadmisi_id
            ]);
        }

        $modelDataPasien = InfoMonitoringBpjsView::find()->where([
            'pendaftaran_id' => $pendaftaran_id, 
            'pasienadmisi_id' => $pasienadmisi_id
        ])->asArray()->one();

        return [
            'model' => $model->asArray()->all(),
            'dataPasien' => $modelDataPasien
        ];
    }

    public function actionUpdatePersen()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post('data');
            $model = new MonitorBpjsPersen;
            if (!empty($post)) {
                $model->attributes = $post;
                // $model->total_persen = (($model->persen/100) * $post['subtotal']);
                // $model->persen_kelompok = $post['persen_tagihan'];
                // $model->total_persenkelompok = $post['persen_inacbg'];
                if($model->validate() && $model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                }
                else {
                    throw new \Exception("Terjadi Kesalahan");
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

    public function actionDetailTagihan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        $pasienadmisi_id = $request->get('pasienadmisi_id', null);
        $monitorbpjs_id = $request->get('monitorbpjs_id', null);

        $model = new InfoMonitoringBpjsDetailView;
        $query = $model::find()->where([
                    'pendaftaran_id' => $pendaftaran_id, 
                    'pasienadmisi_id' => $pasienadmisi_id,
                    'monitorbpjs_id' => $monitorbpjs_id,
                ]);

        return $query->asArray()->all();
        // if($pendaftaran_id && $pasienadmisi_id) {
        //     if($jenis == 'tindakan') {
        //         $query->andWhere([
        //             'pendaftaran_id' => $pendaftaran_id, 
        //             'pasienadmisi_id' => $pasienadmisi_id,
        //             'monitorbpjs_id' => $monitorbpjs_id,
        //             'jenis' => 'tindakan'
        //         ]);
        //     }
        //     else {
        //         $query->andWhere([
        //             'pendaftaran_id' => $pendaftaran_id, 
        //             'pasienadmisi_id' => $pasienadmisi_id,
        //             'monitorbpjs_id' => $monitorbpjs_id,
        //             'jenis' => 'obat'
        //         ]);
        //     }
        // }

        // $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);
    }

    private function getListDiagnosa($value)
    {
        $diagnosaUtamaNama = $diagnosaTindakanNama = $diagnosaPenyertaNama = '';
        if(!empty($value['diagnosa_utama'])) {
            $diagnosa_utama = json_decode($value['diagnosa_utama'], true);
            $diagnosaUtamaNama = $diagnosa_utama['text'];
        }

        if(!empty($value['diagnosa_tindakan'])) {
            $listDiagnosaTindakan = json_decode($value['diagnosa_tindakan'], true);
            if(!empty($listDiagnosaTindakan)) {
                foreach ($listDiagnosaTindakan as $k => $v) {
                    $diagnosaTindakanNama = $v['text'];
                }
            }
        }

        if(!empty($value['diagnosa_penyerta'])) {
            $listDiagnosaPenyerta = json_decode($value['diagnosa_penyerta'], true);
            if(!empty($listDiagnosaPenyerta)) {
                foreach ($listDiagnosaPenyerta as $k => $v) {
                    $diagnosaPenyertaNama = $v['text'];
                }
            }
        }

        return [
            'diagnosa_utama' => $diagnosaUtamaNama,
            'diagnosa_tindakan' => $diagnosaTindakanNama,
            'diagnosa_penyerta' => $diagnosaPenyertaNama,
        ];
    }

    public function actionGetDiagnosaByKode($diagnosaUtamaKode)
    {
        $model = DiagnosaView::find();
        if($diagnosaUtamaKode) {
            $model->where(['diagnosa_kode' => $diagnosaUtamaKode]);

            return $model->asArray()->one();
        }

        return $model->asArray()->all();
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $result = $resultData = [];
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $type = $request->get('type', []);
        $additionalPayload = $request->get('additionalPayload', []);
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        switch ($type) {
            case 'dokter_dpjp':
                $result = Pegawai::find()
                    ->select(['pegawai_id as id', 'nama_pegawai as text'])
                    ->where(['kelompokpegawai_id' => 1, 'is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
                }
                $result->orderBy(['nama_pegawai' => SORT_ASC]);
                break;
            
            case 'ruangan_id': 
                $result = Ruangan::find()
                    ->select(['ruangan_id as id', 'ruangan_nama as text'])
                    ->where(['is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['ruangan_nama' => SORT_ASC]);
                break;

            case 'kamar':
                $result = KamarRuangan::find()
                    ->select(['kamarruangan_id as id', 'kamarruangan_nokamar as text'])
                    ->where(['is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(kamarruangan_nokamar)', strtolower($term)]);
                }
                $result->orderBy(['kamarruangan_nokamar' => SORT_ASC]);
                break;
            
            case 'penjamin_id': 
                $result = Penjamin::find()
                    ->select(['penjamin_id as id', 'penjamin_nama as text'])
                    ->where(['is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['penjamin_nama' => SORT_ASC]);
                break;
        }
        $resultData = $result->asArray()->all();
        return $resultData;
    }

    private function getPayorId()
    {
        $staticPayorId = 3;
        $payorId = null;
        $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'bpjs_live', 'name' => 'payor_id']);
        $result = isset($result['response']) ? $result['response'] : [];
        foreach($result as $res){
            $payorId = $res['lookup_value'];
        }
        return (!empty($payorId) && !is_null($payorId)) ? $payorId : $staticPayorId;
    }

    private function getPayorCd()
    {
        $staticPayorCd = 3;
        $payorCd = null;
        $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'bpjs_live', 'name' => 'payor_cd']);
        $result = isset($result['response']) ? $result['response'] : [];
        foreach($result as $res){
            $payorCd = $res['lookup_value'];
        }
        return (!empty($payorCd) && !is_null($payorCd)) ? $payorCd : $staticPayorCd;
    }

    public function actionExportFile()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $advancedFilter = isset($get['advanced-filter']) ? $get['advanced-filter'] : [];
        $advancedFilterExcel = isset($get['advancedFilter']) ? $get['advancedFilter'] : [];
        $filter = !empty($advancedFilter) ? $advancedFilter : $advancedFilterExcel;
        $order = ArrayHelper::getValue($get, 'order');
        $type = ArrayHelper::getValue($get, 'type');
        $data = $this->getData(true);
        $totalData = count($data);
        $totalPerPage = $countData = $totalData;
      
        (new InternalService)->sendTo([
            'Sirs' => [
            'PenjaminAsuransi\MonitoringBpjs\DataExportFile' => [
                'token' => $auth,
                'xOwner' => $xOwner,
                'unique_str' => $randString,
                'filter' => $filter,
                'order' => $order,
                'type' => $type
            ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'PenjaminAsuransi\MonitoringBpjs\ExportFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $filter,
                    'type' => $type
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'PenjaminAsuransi\MonitoringBpjs\UploadFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'type' => $type
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    private function getData($isExcel = false)
    {
        $model = new InfoMonitoringBpjsView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $request = Yii::$app->request;
        $get = $request->get();
        $advancedFilter = ArrayHelper::getValue($get, 'advanced-filter');
        if(empty($advancedFilter)) {
            $advancedFilter = ArrayHelper::getValue($get, 'advancedFilter');
        }

        if(isset($advancedFilter)) {
            if(isset($advancedFilter['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_pendaftaran']);
            }
            if(!empty($advancedFilter['status_periksa'])) {
                $status_periksa = $advancedFilter['status_periksa'];
                $query->andWhere(['status_periksa' => $status_periksa]);
                unset($advancedFilter['status_periksa']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        if($isExcel) {
            $query = $query->asArray()->all();
        }
         
        return $query;
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;
            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path . '/' . $model->file;
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
        $type = $request->get('type', null);
        $filename = $request->get('filename', null);
        $rootPath = './uploads';
        $ext = ($type == 1) ? '.pdf' : '.xlsx';
        $fileName = $rootPath . '/'.$filename.$ext;
        if($type == 1) {
            if(file_exists($fileName)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header("Content-Disposition: inline; filename=$fileName");
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                ob_clean();
                flush();
                readfile($fileName);
                unlink($fileName);
                die();
            }
        }
        else {
            DocoHelpers::downloadFileExcel($fileName);
        }
    }

    private function getCoderNik()
    {
        $conf = @parse_ini_file('' . realpath(Yii::$app->basePath) . '/config/env/.env', true);
        $vclaim = isset($conf['inacbg']['env_vclaim']) ? $conf['inacbg']['env_vclaim'] : '';
        $env = ($vclaim == DocoConstants::LOOKUP_BPJS_LIVE) ? DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        $result = [];
        $coderNik = '';
        try {
            $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => $env, 'name' => 'coder_nik']);
            $result = isset($result['response']) ? $result['response'] : [];
            foreach ($result as $res) {
                $coderNik = $res['lookup_value'];
            }
            return $coderNik;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function actionGetLookup()
    {    
        $request = Yii::$app->request;
        $list =  $request->get();
        $lookup = Lookup::find()
                ->select(['lookup_id','lookup_type','lookup_name'])
                ->where(['in','lookup_id',$list])
                ->asArray()->all();

        return $lookup;
    }

}