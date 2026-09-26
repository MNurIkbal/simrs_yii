<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\InfoPasienBpjsView;
use app\modules\v1\models\InfoPasienBpjsDiagnosaView;
use app\modules\v1\models\InfoPasienBpjsKlaimViewRi;
use app\modules\v1\models\KoreksiDiagnosaView;
use app\modules\v1\models\KoreksiDiagnosa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoKlaimInacbg;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KlaimInacbg;
use app\modules\v1\models\KlaimInacbgDetail;
use app\modules\v1\models\KlaimInacbgGroup;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\InfoPasienKoreksiView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganDetailView;
use app\modules\v1\models\SyKunjunganAdjusmentDetail;
use app\modules\v1\models\SyKunjunganPotonganTagihan;
use app\modules\v1\models\InfoDiagnosa;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\KlaimEpisode;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\DokumenEklaimParamV;
use app\modules\v1\models\DokumenUploadT;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\KonfigDokumenEklaimK;
use app\modules\v1\models\KoreksiDiagnosaNewView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\SyInfoKlaimInacbg;
use app\modules\v1\models\SyInfoPasienBpjsDiagnosaView;
use app\modules\v1\models\SyInfoPasienBpjsKlaimListView;
use app\modules\v1\models\SyInfoPasienBpjsKlaimView;
use app\modules\v1\models\SyInfoPasienBpjsView;
use app\modules\v1\models\SyKlaimGroup;
use app\modules\v1\models\SyKlaimInacbg;
use app\modules\v1\models\SyKlaimInacbgDetail;
use app\modules\v1\models\SyKoreksiDiagnosa;
use app\modules\v1\models\SyKoreksiDiagnosaView;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\UploadForm;

use yii\web\UploadedFile;
use app\modules\v1\models\LogDokumenEklaim;
use app\modules\v1\models\DokumenEklaimView;
use DateTime;
use Doco\components\DocoConstansId;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Services\InternalService;
use Exception;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use yii\db\Expression;

class InfPasienRanapBpjsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienBpjsView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }
    public function actionInitIndex()
    {
        $result['ruangan'] = [];
        $result['penjamin'] = [];
        $result['status_verif'] = [];
        try {

            $result['instalasi'] = Instalasi::find()
            ->select(['instalasi_id', 'instalasi_singkatan', 'instalasi_nama'])
            ->asArray()
            ->all();
            $validasiUnduh = (new DocoConstansId)->actionGetAdditional('validasi_button_unduh', true);
            $validasiCutoff = (new DocoConstansId)->actionGetAdditional('cutoff_eklaim_idrg');

            $result['ruangan'] = Yii::$app->runAction('v1/allow/get-ruangan', ['id' => DocoConstants::INST_ID_RI]);
            $result['ruangan'] = isset($result['ruangan']['response']['ruangan']) ? $result['ruangan']['response']['ruangan'] : [];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin', ['id' => 6]);
            $result['penjamin'] = isset($result['penjamin']['response']) ? $result['penjamin']['response'] : [];
            $result['status_verif'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'status_verifikasi']);
            $result['status_verif'] = isset($result['status_verif']['response']) ? $result['status_verif']['response'] : [];
            // $result['status_verif'] = isset($result['status_verif']['response']) ? $result['status_verif']['response'] : [];
            $result['validasi_unduh'] = ! empty($validasiUnduh) ? $validasiUnduh : [];
            $result['validasi_cutoff'] = ! empty($validasiCutoff) ? $validasiCutoff : [];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    private function getData()
    {
        $model = new SyInfoPasienBpjsKlaimListView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['status_kunjungan'])) {
                $status_kunjungan = $_GET['advanced-filter']['status_kunjungan'];
                $query->where(['in', 'status_kunjungan', $status_kunjungan]);
                unset($_GET['advanced-filter']['status_kunjungan']);
            }
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

            if (isset($_GET['advanced-filter']['no_rekamedik'])) {
                $noRekammedik = ArrayHelper::getValue($_GET['advanced-filter'], 'no_rekamedik');
                if ($noRekammedik != "" || $noRekammedik != null) {
                    $query->andWhere(['=', 'no_rekammedik', $noRekammedik]);
                }
                unset($_GET['advanced-filter']['no_rekamedik']);
            }

            if (isset($_GET['advanced-filter']['instalasi_pasien'])) {
                $instalasi = ArrayHelper::getValue($_GET['advanced-filter'], 'instalasi_pasien');
                $instalasi = preg_replace('/\s+/', '', $instalasi);
                if ($instalasi != "" || $instalasi != null) {
                    if ($instalasi != "-Semua-") {
                        $query->andWhere(['=', 'instalasi_kode', $instalasi]);
                    }
                }
                unset($_GET['advanced-filter']['instalasi_pasien']);
            }

            if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruanganKode = ArrayHelper::getValue($_GET['advanced-filter'], 'ruangan_nama');
                if ($ruanganKode != "" || $ruanganKode != null) {
                    if ($ruanganKode != "-Semua-") {
                        $query->andWhere(['like', 'ruangan_nama', $ruanganKode]);
                    }
                }
                unset($_GET['advanced-filter']['ruangan_nama']);
            }

            if (isset($_GET['advanced-filter']['cara_bayar'])) {
                $caraBayar = ArrayHelper::getValue($_GET['advanced-filter'], 'cara_bayar');
                if ($caraBayar != "" || $caraBayar != null) {
                    if ($caraBayar != "-Semua-") {
                        $query->andWhere(['=', 'carabayar_nama', $caraBayar]);
                    }
                }
                unset($_GET['advanced-filter']['cara_bayar']);
            }

            if (isset($_GET['advanced-filter']['penjamin_kode'])) {
                $pejaminKode = ArrayHelper::getValue($_GET['advanced-filter'], 'penjamin_kode');
                $pejaminKode = preg_replace('/\s+/', '', $pejaminKode);
                if ($pejaminKode != "" || $pejaminKode != null) {
                    if ($pejaminKode != "-Semua-") {
                        $query->andWhere(['=', 'penjamin_kode', $pejaminKode]);
                    }
                }
                unset($_GET['advanced-filter']['penjamin_kode']);
            }

            if (isset($_GET['advanced-filter']['status_unduh_dokumen'])) {
                $statusUnduhDokumen = ArrayHelper::getValue($_GET['advanced-filter'], 'status_unduh_dokumen');
                $statusUnduhDokumen = preg_replace('/\s+/', '', $statusUnduhDokumen);
                if ($statusUnduhDokumen != "" || $statusUnduhDokumen != null) {
                    if ($statusUnduhDokumen != "-Semua-") {
                        if ($statusUnduhDokumen == DocoConstants::BELUM_UNDUH_DOKUMEN) {
                            $query->andWhere(['is', 'status_unduh_dokumen', new \yii\db\Expression('null')]);
                        } else {
                            $query->andWhere(['=', 'status_unduh_dokumen', $statusUnduhDokumen]);
                        }
                    }
                }
                unset($_GET['advanced-filter']['status_unduh_dokumen']);
            }

            if (isset($_GET['advanced-filter']['checkAll'])) {
                $query->select('kunjungan_id');
                $validasiUnduh = (new DocoConstansId)->actionGetAdditional('validasi_button_unduh', true);
                $query->andWhere(['in', 'status_kunjungan', $validasiUnduh]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return $query;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $query = $this->getData();
        // $query = isset($data['query']) ? $data['query'] : [];

        $params = [
            'query' => $query,
        ];

        if (isset($_GET['advanced-filter']['checkAll'])) {
            $params = [
                'query' => $query,
                'pagination' => false,
            ];
        }

        return new ActiveDataProvider($params);
    }

    public function actionGetRequest()
    {
        $request = Yii::$app->request;
        $ruangan = $status_kunjungan = $carabayar = [];
        $model = new SyKunjunganView;
        $kunjungan = $model::find()->select(['ruangan_kode', 'ruangan_nama'])->where(['<>', 'instalasi_kode', DocoConstants::RJAL])->andWhere(['ILIKE', 'LOWER(no_pendaftaran)', 'RI'])->groupBy(['ruangan_kode', 'ruangan_nama'])->orderBy(['ruangan_nama' => SORT_ASC])->all();

        if ($kunjungan) {
            foreach ($kunjungan as $key => $value) {
                if (!isset($ruangan[$value->ruangan_kode])) {
                    $ruangan[$value->ruangan_kode] = [
                        'id' => $value->ruangan_kode,
                        'label' => $value->ruangan_kode . '-' . $value->ruangan_nama
                    ];
                }
            }
        }

        $model = new CaraBayar;
        $res = $model::find()->select(['carabayar_nama', 'carabayar_namalainnya'])->groupBy(['carabayar_namalainnya', 'carabayar_nama'])->orderBy(['carabayar_nama' => SORT_ASC])->all();
        if ($res) {
            foreach ($res as $key => $value) {
                $carabayar[$value->carabayar_namalainnya] = [
                    'id' => strtoupper($value->carabayar_namalainnya),
                    'label' => strtoupper($value->carabayar_nama)
                ];
            }
        }

        return $response = [
            'ruangan'   => ArrayHelper::map($ruangan, 'id', 'label'),
            'status_kunjungan' => DocoConstants::$status_kunjungan,
            'carabayar' => ArrayHelper::map($carabayar, 'id', 'label'),
        ];
    }

    public function actionView($id)
    {
        try {
            $model = SyInfoPasienBpjsView::find()->where(['kunjungan_id' => $id])->asArray()->one(); // INI dihapus jenis
            $pasienId = ArrayHelper::getValue($model, "pasien_id");
            $jenisKelamin = ArrayHelper::getValue($model, "jenis_kelamin");
            $noPendaftaran = ArrayHelper::getValue($model, 'no_pendaftaran');
            $additional = SyKunjunganPasien::find()->select(['additional_data', 'status_inacbg'])->where(['kunjungan_id' => $id])->asArray()->one();
            if(empty($model['no_asuransi'])) {
                $pasienId = ArrayHelper::getValue($model, "pasien_id");
                if (isset($pasienId)) {
                    $viewPasien = Pasien::find()->where(['pasien_id' => $pasienId])->one();
                    $model['no_asuransi'] = isset($viewPasien->nopeserta_bpjs) ? $viewPasien->nopeserta_bpjs : null;
                }
            }
            if (isset($jenisKelamin)) {
                $gender = Lookup::find()->select(['lookup_value', 'lookup_kode'])->where(['lookup_id' => $jenisKelamin])->one();
                // Jenis kelamin untuk klaim
                $model['jeniskelamin'] = isset($gender->lookup_kode) ? $gender->lookup_kode : $jenisKelamin;
                // Jenis kelamin untuk proses
                $model['jenis_kelamin'] = isset($gender->lookup_value) ? $gender->lookup_value : $jenisKelamin;
            }
            $result = $model;
            $result['additional_data'] = ArrayHelper::getValue($additional, 'additional_data');
            $result['status_inacbg'] = ArrayHelper::getValue($additional, 'status_inacbg');
            return $result;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionGetKoreksi($id)
    {
        try {
            $model = SyKoreksiDiagnosaView::find()->where(['kunjungan_id' => $id]);
            $model->orderBy(['kelompokdiagnosa_id' => SORT_ASC]);
            return $model->asArray()->all();
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionViewClaim($id)
    {
        try {
            $model = SyInfoKlaimInacbg::find()->where(['kunjungan_id' => $id])->asArray()->one();
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    protected function historyKlaim($id)
    {
        try {
            $klaimAktif = SyInfoKlaimInacbg::find()->where(['kunjungan_id' => $id])->asArray()->one();
            if(is_null($klaimAktif)){
                $model = SyKlaimInacbg::find(true)->where(['kunjungan_id' => $id])->orderBy(['created_date'=> SORT_DESC ])->asArray()->one();
                return $model;
            }else{
                return [];
            }
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionResetGrouping($id)
    {
        $tgl_pulang = SyInfoPasienBpjsView::find()->select('tgl_pulang')->where(['kunjungan_id' => $id])->scalar();
        if(is_null($tgl_pulang)){
            return null;
        }else{
            return [
                'data' => $this->getDetailKlaim($id,$tgl_pulang)
            ];
        }
    }

    public function actionProsesKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        // $transaction = $connection->beginTransaction();
        $stateUpdate = false;
        // try {
        $pendaftaran_id = $post['pendaftaran_id'];
        $getDiagnosa = $this->getDiagnosa($pendaftaran_id);
        $arrOldDiagnosa = [];
        if (count($getDiagnosa) > 0) {
            foreach ($getDiagnosa as $key => $value) {
                $arrOldDiagnosa[$value['koreksidiagnosa_id']] = $value;
            }
        } else {
            $stateUpdate = true;
        }
        foreach ($post['data'] as $key => $value) {
            if (!$stateUpdate && isset($arrOldDiagnosa[$value['koreksidiagnosa_id']])) {
                if (($arrOldDiagnosa[$value['koreksidiagnosa_id']]['is_icdprimer'] != $value['is_icdprimer']) || ($arrOldDiagnosa[$value['koreksidiagnosa_id']]['is_inacbg'] != $value['is_inacbg'])) {
                    $stateUpdate = true;
                }
            }
            // return "MAULANA"
            $model = SyKoreksiDiagnosa::find()->where(['sy_koreksidiagnosa_id' => $value['koreksidiagnosa_id']])->one();
            // return $model;
            $model->is_inacbg = $value['is_inacbg'];
            $model->is_icdprimer = $value['is_icdprimer'];
            $save = $model->save();
            if (!$save) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Proses eklaim gagal.'
                ];
            }
        }
        $find = SyKunjunganPasien::find()->where(['kunjungan_id' => $pendaftaran_id])->one();
        if (!empty($find)) {
            if ($find->status_kunjungan != DocoConstants::STATUS_SUDAH_KOREKSI) {
                $find->status_kunjungan = DocoConstants::STATUS_SUDAH_KOREKSI;
                $find->save();
            }
            // $transaction->commit();
        } else {
            // $save = $find->save();
            // $transaction->rollBack();

            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Proses eklaim gagal pasien tidak ditemukan!'
            ];
        }
        return ['update' => $stateUpdate];
        // } catch (\Exception $e) {
        //     $transaction->rollBack();
        //     throw new \Exception($e->getMessage());
        // } catch (\yii\db\Exception $e) {
        //     $transaction->rollBack();
        //     throw new \Exception($e->getMessage());
        // }
    }

    public function getDiagnosa($id)
    {
        try {
            $model = SyInfoPasienBpjsDiagnosaView::find()->where(['pendaftaran_id' => $id, 'jenis' => 'RI'])->asArray()->all();
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function getDiagnosaInacbgs($id)
    {
        try {
            $model = SyInfoPasienBpjsDiagnosaView::find()->where(['kunjungan_id' => $id])->asArray()->all(); // Hapus jenis
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionGetData($id)
    {
        try {
            $klaimgroup = [];
            $info = $this->actionView($id);
            $result['klaim'] = $this->actionViewClaim($id);
            $result['historyKlaim'] = $this->historyKlaim($id);
            $result['info'] = $info;
            $result['diagnosa'] = $this->getDiagnosaInacbgs($id);
            $result['opsi'] = $this->initKlaim();
            $result['detailtarif'] = [];
            $result['inacbg'] = $this->getLastKlaim($id);
            $result['tarif_ranap'] = [];
            if ($result['info']) {
                $norm = $result['info']['no_rekammedik'];
                $tgl_pulang = date('Y-m-d', strtotime($result['info']['tgl_pulang']));
                $result['detailtarif'] = $this->getDetailKlaim($id, $tgl_pulang);
            }

            $klaiminacbg_id = (isset($result['inacbg']['header']['sy_klaiminacbg_id']) && $result['inacbg']['header']['sy_klaiminacbg_id']) ? $result['inacbg']['header']['sy_klaiminacbg_id'] : '';
            $result['naikkelas'] = [];

            if ($klaiminacbg_id) {
                $klaimgroup = $this->getKlaimGroup($klaiminacbg_id);
            }
            $result['klaimgroup'] = $klaimgroup;

            if ($result['klaim']) {
                $episode = $this->getEpisode($result['klaim']['klaiminacbg_id']);
                $result['episode'] = $episode ? $episode : [];
            }
            return $result;
        } catch (\Exception $e) {
            return $result = ['info' => [], 'diagnosa' => [], 'opsi' => [], 'detailtarif' => [], 'inacbg' => []];
        }
    }
    public function getNaikKelas($admisi)
    {
        try {
            $model = MasukKamar::find()->where(['pasienadmisi_id' => $admisi])->asArray()->all();
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function getDetailKlaim($id, $tglPlg)
    {
        try {
            $model = SyInfoPasienBpjsKlaimView::find()->where(['kunjungan_id' => $id])->andWhere(" tgl_pulang::date='$tglPlg'")->asArray()->all();
            $result = [];
            $adjustment = [];
            $hasil = [];
            foreach ($model as $key => $value) {
                $noPendaftaran = $value['no_pendaftaran'];

                if ($value['groupinacbg_nama'] == 'Kamar/Akomodasi'  || $value['groupinacbg_nama']  == 'Kamar') {
                    $value['groupinacbg_nama'] = 'Kamar Akomodasi';
                }
                $name = str_replace(' ', '_', strtolower($value['groupinacbg_nama']));
                $tindakanKode = $value['layanan_kode'];
                if (isset($result[$name][$tindakanKode])) {
                    $result[$name][$tindakanKode] += $value['layanan_tarif'];
                } else {
                    $result[$name][$tindakanKode] = $value['layanan_tarif'];
                }
            }

            // $adj = SyKunjunganAdjusmentDetail::find()->where(['no_pendaftaran' => $noPendaftaran, 'is_deleted' => false])->asArray()->all();

            // if ($adj) {
            //     foreach ($adj as $key => $value) {
            //         $kode = $value['kode_layanan'];
            //         foreach ($result as $k => $vv) {
            //             if (isset($result[$k][$kode])) {
            //                 $result[$k][$kode] -= $value['total_rs'];
            //                 $result[$k][$kode] -= $value['total_dokter'];
            //             }
            //         }
            //     }
            // }

            foreach ($result as $key => $v) {
                $hasil[$key] = array_sum($v);
            }

            return $hasil;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function getLastKlaim($id)
    {
        try {
            $detail = [];
            $model = SyKlaimInacbg::find()->where(['kunjungan_id' => $id])->select(['kunjungan_id', 'is_deleted', 'sy_klaiminacbg_id', 'jenis_kelasrawat', 'status_klaim', 'naik_kelas', 'is_naikkelas', 'lama_naikkelas', 'is_kelasintensif', 'kelas_intensif', 'lama_kelasintensif', 'ventilator'])->one();
            if ($model) {
                $detail = SyKlaimInacbgDetail::find()->where(['sy_klaiminacbg_id' => $model['sy_klaiminacbg_id'], 'is_deleted' => false])->select(['sy_klaiminacbg_id', 'is_deleted', 'diagnosa_id', 'icd_versi', 'kode_diagnosa', 'nama_diagnosa'])->asArray()->all();
            }
            return ['header' => $model, 'detailinacbg' => $detail];
        } catch (\yii\db\Exception $e) {
            return ['header' => [], 'detailinacbg' => []];
        }
    }

    public function initKlaim()
    {
        $result['carakeluar'] = [];
        $result['tarifrs'] = [];
        $conf = @parse_ini_file('' . realpath(Yii::$app->basePath) . '/config/env/.env', true);
        $type_tarif = (isset($conf['inacbg']['env_vclaim']) && $conf['inacbg']['env_vclaim'] ==  DocoConstants::LOOKUP_BPJS_LIVE) ?  DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        try {
            $result['carakeluar'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'carapulang_inacbg']);
            $result['carakeluar'] = isset($result['carakeluar']['response']) ? $result['carakeluar']['response'] : [];
            $result['tarifrs'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => $type_tarif, 'name' => 'default_tarif']);
            $result['tarifrs'] = isset($result['tarifrs']['response']) ? $result['tarifrs']['response'] : [];
            $result['rujukanrs'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'rujukanbpjs']);
            $result['rujukanrs'] = isset($result['rujukanrs']['response']) ? $result['rujukanrs']['response'] : [];
            $result['jenis_identitas'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'jenis_identitas_bpjs']);
            $result['jenis_identitas'] = isset($result['jenis_identitas']['response']) ? $result['jenis_identitas']['response'] : [];
            $result['inacbg_penjamin'] = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'inacbg_penjamin']);
            $result['inacbg_penjamin'] = isset($result['inacbg_penjamin']['response']) ? $result['inacbg_penjamin']['response'] : [];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function actionExportExcel()
    {
        try {
            $filters = isset($_GET['advanced-filter']) ? $_GET['advanced-filter'] : [];
            $query = $this->getData()->asArray()->all();
            $data = [];
            $header = [];
            foreach ($query as $key => $value) {
                if (isset($filters['tgl_pendaftaran'])) {
                    $explode = explode(' - ', $filters['tgl_pendaftaran']);
                    if (count($explode) == 2) {
                        $start = date('d M Y', strtotime($explode[0]));
                        $end = date('d M Y', strtotime($explode[1]));
                    }
                    $header['Tanggal pendaftaran'] = $start . ' - ' . $end;
                }
                if (isset($filters['tglpasienpulang'])) {
                    $explode = explode(' - ', $filters['tglpasienpulang']);
                    if (count($explode) == 2) {
                        $start = date('d M Y', strtotime($explode[0]));
                        $end = date('d M Y', strtotime($explode[1]));
                    }
                    $header['Tanggal pulang'] = $start . ' - ' . $end;
                }
                if (isset($filters['no_rekam_medik'])) {
                    $header['No Rekam Medik'] = $filters['no_rekam_medik'];
                }
                if (isset($filters['no_pendaftaran'])) {
                    $header['No Pendaftaran'] = $filters['no_pendaftaran'];
                }
                if (isset($filters['nama_pasien'])) {
                    $header['Nama Pasien'] = $filters['nama_pasien'];
                }
                if (isset($filters['carabayar_id'])) {
                    if (!isset($header['Cara Bayar'])) {
                        $header['Cara Bayar'] = $value['carabayar_nama'];
                    }
                }
                if (isset($filters['ruangan_id'])) {
                    if (!isset($header['Ruangan'])) {
                        $header['Ruangan'] = $value['ruangan_nama'];
                    }
                }
                if (isset($filters['kamarruangan_id'])) {
                    if (!isset($header['Kamar'])) {
                        $header['Kamar'] = $value['kamarruangan_nokamar'];
                    }
                }
                if (isset($filters['pegawai_id'])) {
                    if (!isset($header['Dokter Penanggung Jawab'])) {
                        $header['Dokter Penanggung Jawab'] = $value['dokter_dpjp'];
                    }
                }
                if (isset($filters['status_kunjungan'])) {
                    if (!isset($header['Status'])) {
                        $header['Status'] = $value['status_verif'];
                    }
                }
                $newData = [];
                $newData['Tanggal Masuk'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $newData['No Rekam Medik'] = $value['no_rekam_medik'];
                $newData['No Pendaftaran'] = $value['no_pendaftaran'];
                $newData['Nama Pasien'] = $value['nama_pasien'];
                $newData['Cara Bayar'] = $value['carabayar_nama'];
                $newData['Penjamin'] = $value['penjamin_nama'];
                $newData['Ruangan'] = $value['ruangan_nama'];
                $newData['Kamar'] = $value['kamarruangan_nokamar'];
                $newData['Jenis Kasus Penyakit'] = $value['jeniskasuspenyakit_nama'];
                $newData['Dokter Penanggung Jawab'] = $value['dokter_dpjp'];
                $newData['Status'] = $value['status_verif'];
                $data[] = $newData;
            }
            $filePath = DocoHelpers::exportExcel('Pasien Rawat Inap BPJS', $data, $header, array("uploadPath" => "./uploads"), [], [], true);

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

    /**
     * @controller actionExportPdf
     * @attribute #table_laporan# => Menampilkan Table Pasien Rajal Bpjs
     * @attribute #periode# => Periode laporan
     * @attribute #nama_pengguna# => Nama Pengguna
     * @attribute #tgl_cetak# => Nama Pengguna
     * @attribute #nip# => NIP
     **/

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $periode = date('d M Y') . ' - ' . date('d M Y');
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                    $periode = $start . ' - ' . $end;
                }
            }
        }
        $getKepalaRuangan = PegawaiView::find()->where(['ruangan_id' => Yii::$app->jwt->ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();
        try {
            $data = $this->getData()->asArray()->all();
            $print = new DocoPrint();
            $print->attributes = [
                '#table_laporan#' => $this->renderPartial('index', ['data' => $data]),
                '#periode#' => $periode,
                '#nama_pengguna#' => isset($getKepalaRuangan['nama_pegawai']) ? $getKepalaRuangan['nama_pegawai'] : '',
                '#tgl_cetak#' => date('d F Y'),
                '#nip#' => isset($getKepalaRuangan['nomorindukpegawai']) ? $getKepalaRuangan['nomorindukpegawai'] : '',
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
    public function actionProsesEklaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_SUDAH_KOREKSI;
        try {
            $modelParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $post['data']['kunjungan_id']])->one();
            $dokterAdditional = [];
            if (isset($post['data']['dokter_additional'])) {
                $arrDokter = $post['data']['dokter_additional'];
                foreach($arrDokter as $key => $name){
                    $dokterAdditional[] = [
                        'text' => $name
                    ];
                }
                unset($post['data']['dokter_additional']);
            }
            
            if (!$modelParent) {
                $modelParent = new SyKlaimInacbg;
            }
            if (!empty($post['data']['dokter_id'])) {
                $selectedDokter = $this->cariDokter($post['data']['dokter_id']);
                $modelParent->dokterdpjp_id = $selectedDokter['dokter_id'];
                $modelParent->nama_dokter = $selectedDokter['nama_pegawai'];
                $post['data']['nama_dokter'] = $selectedDokter['nama_pegawai'];
            }

            $modelParent->attributes = $post['data'];
            // $kunjunganId = (int) $modelParent->kunjungan_id;
            $kunjunganId = ArrayHelper::getValue($post['data'], 'kunjungan_id');
            $dokterdpjp_id = ArrayHelper::getValue($post['data'], 'dokterdpjp_id');
            $dokterdpjp_nama = ArrayHelper::getValue($post['data'], 'nama_dokter');
            $sistole = ArrayHelper::getValue($post['data'], 'sistole');
            $diastole = ArrayHelper::getValue($post['data'], 'diastole');
            $caramasuk = ArrayHelper::getValue($post['data'], 'rujukanrs');
            $modelParent->is_kelasintensif = isset($post['data']['is_rawatintensif']) ? $post['data']['is_rawatintensif'] : false;
            $modelParent->lama_kelasintensif = isset($post['data']['lama_rawatintensif']) ? $post['data']['lama_rawatintensif'] : 0;
            $modelParent->lama_naikkelas = isset($post['data']['lama_rawatkelas']) ?  $post['data']['lama_rawatkelas'] : 0;
            if ($post['data']['ventilator'] == 1) {
                $modelParent->ventilator = isset($post['data']['ventilator']) ? $post['data']['ventilator'] : 0;
                $modelParent->ventilator_start = isset($post['data']['intubasi']) ? date('Y-m-d h:i:s', strtotime($post['data']['intubasi'])) : null;
                $modelParent->ventilator_stop = isset($post['data']['ekstubasi']) ? date('Y-m-d h:i:s', strtotime($post['data']['ekstubasi'])) : null;
            }
            $modelParent->is_rawatintensif = isset($post['data']['is_rawatintensif']) ? $post['data']['is_rawatintensif'] : false;
            $eksDiagnosaPrimer = explode("#", $modelParent->diagnosa_primer);
            $eksDiagnosaSekunder = explode("#", $modelParent->diagnosa_sekunder);
            if (isset($post['deleted']) && !empty($post['deleted'])) {
                $arrDelDiag = [];
                $arrKode10 = [];
                $arrKode9 = [];
                foreach ($post['deleted'] as $key => $value) {
                    $arrDelDiag[] = $value['diagnosa_id'];
                    if ($value['diagnosa_type'] == '10') {
                        $arrKode10[] = $value['kode_diagnosa'];
                    } else {
                        $arrKode9[] = $value['kode_diagnosa'];
                    }
                }
                $arrDelDiag = implode($arrDelDiag, ",");
                if ($arrKode10 || $arrKode9) {
                    $newDiagnosaPrimer = [];
                    $newDiagnosaSekunder = [];
                    if (isset($post['primer']) && !empty($post['primer'])) {
                        $newDiagnosaPrimer[] = $post['primer'];
                        array_push($arrKode10, $post['primer']);
                    }
                    if ($arrKode10) {
                        foreach ($eksDiagnosaPrimer as $key => $value) {
                            if (!in_array($value, $arrKode10)) {
                                $newDiagnosaPrimer[$key + 1] = $value;
                            }
                        }
                        $modelParent->diagnosa_primer = implode($newDiagnosaPrimer, "#");
                        $post['data']['diagnosa_primer'] = $modelParent->diagnosa_primer;
                    }
                    if ($arrKode9) {
                        foreach ($eksDiagnosaSekunder as $key => $value) {
                            if (!in_array($value, $arrKode9)) {
                                $newDiagnosaSekunder[$key] = $value;
                            }
                        }
                        $modelParent->diagnosa_sekunder = implode($newDiagnosaSekunder, "#");
                        $post['data']['diagnosa_sekunder'] = $modelParent->diagnosa_sekunder;
                    }
                }
                $sql = 'update koreksidiagnosa_t SET is_deleted = true where diagnosa_id IN (' . $arrDelDiag . ') and pendaftaran_id = ' . $post['data']['pendaftaran_id'] . ' ';
                $hapus = Yii::$app->db->createCommand($sql)->execute();
            }


            // set primer
            if (isset($post['primer']) && !empty($post['primer'])) {
                if (!isset($arrDelDiag)) {
                    $primer = [];
                    $finalPrimer[] = $post['primer'];
                    array_push($primer, $post['primer']);
                    foreach ($eksDiagnosaPrimer as $key => $value) {
                        if (!in_array($value, $primer)) {
                            $finalPrimer[$key + 1] = $value;
                        }
                    }
                    $modelParent->diagnosa_primer = implode($finalPrimer, "#");
                    $post['data']['diagnosa_primer'] = $modelParent->diagnosa_primer;
                }
            }

            if ($modelParent->save()) {
                $klaiminacbg_id = $modelParent->sy_klaiminacbg_id;
                if (isset($post['detail'])) {
                    // $hapus = $connection->createCommand('update klaiminacbgdetail_t set is_deleted = true where klaiminacbg_id = ' . $klaiminacbg_id)->execute();
                    // $detail = [];
                    // foreach ($post['detail'] as $key => $value) {
                    //     $newArr = [];
                    //     $newArr['klaiminacbg_id'] = $klaiminacbg_id;
                    //     $newArr['diagnosa_id'] = $value['diagnosa_id'];
                    //     $newArr['kode_diagnosa'] = $value['kode_diagnosa'];
                    //     $newArr['nama_diagnosa'] = $value['nama_diagnosa'];
                    //     $newArr['icd_versi'] = $value['icd_versi'];
                    //     $detail[] = $newArr;
                    // }
                    // KlaimInacbgDetail::batchInsert($detail);
                }
            }

            $resultAdditional = SyKunjunganPasien::find()->select(['additional_data'])->where(['kunjungan_id' => $kunjunganId])->asArray()->one();
            $additionalData = json_decode(ArrayHelper::getValue($resultAdditional, 'additional_data'), true);
            if (!empty($resultAdditional)) {
                $additionalData['dokter_additional'] = $dokterAdditional;
            }

            if ($kunjunganId) {
                $result = $this->prosesBpjs($post['data']);
                if (isset($result['status'])) {
                    if ($result['status'] != 200) {
                        return $result;
                    }
                }
                $connection->createCommand()->update('sy_kunjungan', [
                    'status_kunjungan' => $status, 
                    'dokter_kode' => $dokterdpjp_id, 
                    'dokter_nama' => $dokterdpjp_nama,
                    'tgl_pendaftaran' => isset($post['data']['tgl_masuk']) ? $post['data']['tgl_masuk'] : date('Y-m-d H:i'),
                    'tgl_pulang' => isset($post['data']['tgl_keluar']) ? $post['data']['tgl_keluar'] : date('Y-m-d H:i'),
                    'sistole' => $sistole,
                    'diastole' => $diastole,
                    'caramasuk' => $caramasuk,
                    'additional_data' => json_encode($additionalData)
                ], 
                    ' kunjungan_id =' . $kunjunganId . '')->execute();
                (new InternalService)->sendTo([
                    'Sirs' => [
                        'SinkronDataBpjs\TriggerFreezeBilling' => [
                            'type_sinkron' => 'confirm',
                            'kunjungan_id' => $kunjunganId
                        ]
                    ]
                ], true);
                $transaction->commit();
                return $result;
            } else {
                $transaction->rollBack();
                throw new \Exception("Pasien Tidak Ditemukan");
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return json_encode($e->getMessage());
            throw new \Exception("Terjadi Kesalahan");
        } catch (\Exception $e) {
            $transaction->rollBack();
            return json_encode($e->getMessage());
            throw new \Exception("Terjadi Kesalahan");
        }
    }
    public function prosesBpjs($data)
    {
        /**
         * Cek dulu apakah pasien sudah terdaftar di BPJS
         */
        $newClaim['metadata']['method'] = 'new_claim';
        $newClaim['data']['nomor_kartu'] = $data['no_kartu'];
        $newClaim['data']['nomor_sep'] = $data['no_sep'];
        $newClaim['data']['nomor_rm'] = $data['no_rekam_medik'];
        $newClaim['data']['nama_pasien'] = $data['nama_pasien'];
        $newClaim['data']['tgl_lahir'] = $data['tgl_lahir'];
        $newClaim['data']['gender'] = ($data['jeniskelamin'] == DocoConstants::LAKI) ? DocoConstants::JENIS_LAKI : DocoConstants::JENIS_PEREMPUAN;
        $response = json_decode(DocoHelpers::restInacbgs($newClaim), true);
        if ($response['metadata']['code'] != 200) {
            if ($response['metadata']['code'] != 400 && $response['metadata']['error_no'] != 'E2007') {
                throw new \Exception("Terjadi Kesalahan");
            }

            if ($response['metadata']['code'] == 400 && $response['metadata']['error_no'] == 'E2043') {
                return [
                    'status' => $response['metadata']['code'],
                    'code' => $response['metadata']['code'],
                    'title' => $response['metadata']['error_no'],
                    'text' => $response['metadata']['message']
                ];
            }
        }
        if ($data['naik_kelas'] == 'kelas_4' || $data['naik_kelas'] == 4) {
            $naikKelas = 'vip';
        } elseif ($data['naik_kelas'] == 'kelas_5' || $data['naik_kelas'] == 5) {
            $naikKelas = 'vvip';
        } else {
            $naikKelas = $data['naik_kelas'];
        }

        if ($data['ventilator'] == 1) {
            $intubasi = new DateTime($data['intubasi']);
            $ekstubasi = new DateTime($data['ekstubasi']);
            $ventilatorHour = $ekstubasi->diff($intubasi);
            if (!empty($ventilatorHour)) {
                if ($ventilatorHour->h != 0) {
                    if ($ventilatorHour->days != 0) {
                        $convertDay = $ventilatorHour->days * 24;
                        $ventilatorHour = $ventilatorHour->h + $convertDay;
                    } else {
                        $ventilatorHour = $ventilatorHour->h;
                    }
                } else {
                    $ventilatorHour = $ventilatorHour->d * 24;
                }
            }
            $ventilator = [
                "use_ind" => $data['ventilator'],
                "start_dttm" => date('Y-m-d H:i', strtotime($data['intubasi'])),
                "stop_dttm" =>  date('Y-m-d H:i', strtotime($data['ekstubasi']))
            ];
        } else {
            $ventilator = "";
        }

        $prosesKlaim['metadata']['method'] = 'set_claim_data';
        $prosesKlaim['metadata']['nomor_sep'] = $data['no_sep'];
        $prosesKlaim['data'] = [
            'nomor_sep' => $data['no_sep'],
            'nomor_kartu' => $data['no_kartu'],
            'tgl_masuk' => date('Y-m-d H:i:s', strtotime($data['tgl_masuk'])),
            'tgl_pulang' => date('Y-m-d H:i:s', strtotime($data['tgl_keluar'])),
            'adl_sub_acute' => $data['adl_subacute'],
            'adl_chronic' => $data['adl_cronic'],
            'jenis_rawat' => $data['jenis'],
            'kelas_rawat' => $data['jenis_kelasrawat'],
            'upgrade_class_ind' => $data['is_naikkelas'],
            'upgrade_class_class' => $naikKelas,
            'upgrade_class_los' => $data['lama_rawatkelas'],
            'birth_weight' => $data['berat_lahir'],
            'icu_indikator' => $data['is_rawatintensif'],
            'icu_los' => $data['lama_rawatintensif'],
            'discharge_status' => $data['carapulang_id'],
            // 'diagnosa' => $data['diagnosa_primer'], Deprecated
            // 'procedure' => $data['diagnosa_sekunder'], Deprecated
            // "diagnosa_inagrouper" => $data['diagnosa_primer_ina'], Deprecated
            // "procedure_inagrouper" => $data['diagnosa_sekunder_ina'],  Deprecated
            'nama_dokter' => $data['nama_dokter'],
            "sistole" => $data['sistole'],
            'diastole' => $data['diastole'],
            'upgrade_class_payor' => 'peserta',
            'cara_masuk' => ArrayHelper::getValue($data, 'rujukanrs'),
            'ventilator_hour' => isset($ventilatorHour) ? (string) $ventilatorHour : '',
            'ventilator' => $ventilator,
            'dializer_single_use' => '0',
            'kantong_darah' => 0,
            'apgar' => '',
            'persalinan' => '',
            'tarif_rs' => [
                'prosedur_non_bedah' => $data['prosedur_nonbedah'],
                'prosedur_bedah' => $data['prosedur_bedah'],
                'konsultasi' => $data['konsultasi'],
                'tenaga_ahli' => $data['tenaga_ahli'],
                'keperawatan' => $data['keperawatan'],
                'penunjang' => $data['penunjang'],
                'radiologi' => $data['radiologi'],
                'laboratorium' => $data['laboratorium'],
                'pelayanan_darah' => $data['pelayanan_darah'],
                'rehabilitasi' => $data['rehabilitasi'],
                'kamar' => $data['kamar_akomodasi'],
                'rawat_intensif' => $data['rawat_intensif'],
                'obat' => $data['obat'],
                'obat_kronis' => $data['obat_kronis'],
                'obat_kemoterapi' => $data['obat_kemoterapi'],
                'alkes' => $data['alkes'],
                'bmhp' => $data['bmhp'],
                'sewa_alat' => $data['sewa_alat'],
            ],
            'tarif_poli_eks' => ArrayHelper::getValue($data, 'tarif_poli_eks'),
            'kode_tarif' => $data['tarif'],
            'covid19_status_cd' => '0',
            'covid19_cc_ind' => '0',
            'covid19_rs_darurat_ind' => '0',
            'covid19_co_insidense_ind' => '0',
            'payor_id' => $this->getPayorId(),
            'payor_cd' => $this->getPayorCd(),
            'coder_nik' => $this->getCoderNik(),
            'coder_nik' => $this->getCoderNik(),
        ];

        // return $prosesKlaim;

        $klaim = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);
        if ($klaim['metadata']['code'] != 200) {
            return [
                'status' => $klaim['metadata']['code'],
                'code' => $klaim['metadata']['code'],
                'title' => $klaim['metadata']['error_no'],
                'text' => $klaim['metadata']['message']
            ];
        }

        return [
            'status' => 200,
            'code' => $klaim['metadata']['code'],
            'title' => 'Proses Berhasil !',
            'text' => $klaim['metadata']['message'],
            'response' => $klaim
        ];
    }

    public function actionHapusKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pendaftaran_id = $post['pendaftaran_id'];
        $nosep = $post['no_sep'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $status = DocoConstants::STATUS_BELUM_KOREKSI;

        try {
            $findParent = SyKlaimInacbg::findOne(['kunjungan_id' => $pendaftaran_id]);
            if ($findParent) {
                $hapusklaim = [
                    'metadata' => [
                        'method' => 'delete_claim',
                    ],
                    'data' => [
                        'nomor_sep' => $nosep,
                        'coder_nik' => $this->getCoderNik(),
                    ],
                ];
                $hapus = json_decode(DocoHelpers::restInacbgs($hapusklaim), true);
                if (isset($hapus['metadata']['code'])) {
                    if ($hapus['metadata']['code'] != 200) {
                        return [
                            'status' => $hapus['metadata']['code'],
                            'title' => $hapus['metadata']['error_no'],
                            'text' => $hapus['metadata']['message']
                        ];
                    }
                }

                $klaiminacbg_id = $findParent['sy_klaiminacbg_id'];
                $deleteGroup = $connection->createCommand('update sy_klaimgroup_t set is_deleted = true where sy_klaiminacbg_id = ' . $klaiminacbg_id . ' and is_deleted = false')->execute();
                $delete = $findParent->delete();

                if (!$delete) {
                    throw new \Exception("Hapus Terjadi Kesalahan");
                }

                $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => $status, "no_klaimcovid" => null], ' kunjungan_id =' . $pendaftaran_id . '')->execute();

                (new InternalService)->sendTo([
                    'Sirs' => [
                        'SinkronDataBpjs\TriggerFreezeBilling' => [
                            'type_sinkron' => 'cancel',
                            'kunjungan_id' => $pendaftaran_id
                        ]
                    ]
                ], true);
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return false;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }
        return true;
    }
    public function actionFinalKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        if ($post['nosep'] != "") {
            $nosep = $post['nosep'];
        } else {
            $nosep = $post['data']['no_klaimcovid'];
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $statusEdit = DocoConstants::STATUS_PROSES_KLAIM;
        $statusFinal = DocoConstants::STATUS_FINAL_KLAIM;

        try {
            $modelKlaim = SyKlaimInacbg::find()->where(['kunjungan_id' => $post['data']['kunjungan_id'], 'is_deleted' => false])->one();
            if (empty($modelKlaim)) {
                return [
                    'status' => 400,
                    'title' => "Proses gagal! ",
                    'text' => "Data klaim tidak temukan !",
                ];
            }
            $klaiminacbg_id = $modelKlaim->sy_klaiminacbg_id;
            $pendaftaran_id = $post['data']['kunjungan_id'];

            $modelGroup = new SyKlaimGroup;

            if (isset($post['grouper'])) {
                $modelGrouper = new SyKlaimGroup;
                $modelGrouper->attributes = $post['grouper'];
                $modelGrouper->sy_klaiminacbg_id = $klaiminacbg_id;
                $item = [
                    'is_pemulasaranjenazah' => isset($post['data']['is_pemulasaranjenazah']) ? $post['data']['is_pemulasaranjenazah'] : false,
                    'is_kantongjenazah' => isset($post['data']['is_kantongjenazah']) ? $post['data']['is_kantongjenazah'] : false,
                    'is_petijenazah' => isset($post['data']['is_petijenazah']) ? $post['data']['is_petijenazah'] : false,
                    'is_plastikerat' => isset($post['data']['is_plastikerat']) ? $post['data']['is_plastikerat'] : false,
                    'is_desinfektanjenazah' => isset($post['data']['is_desinfektanjenazah']) ? $post['data']['is_desinfektanjenazah'] : false,
                    'is_transport' => isset($post['data']['is_transport']) ? $post['data']['is_transport'] : false,
                    'is_desinfektanmobil' => isset($post['data']['is_desinfektanmobil']) ? $post['data']['is_desinfektanmobil'] : false,
                ];
                $modelGrouper->add_jenazah = json_encode($item);
                $diagnosaKode = [];
                if (isset($post['additional'])) {
                    $add = $post['additional'];
                    $arrSpecial = [];
                    $modelGrouper->group_nama = $post['additional']['cbg_desc'];
                    $modelGrouper->cbg = $post['additional']['cbg_code'];
                    $modelGrouper->group_tarif = $post['additional']['cbg_tarif'];
                    $modelGrouper->additional_data = json_encode($add);
                    $stringDiagnosa = isset($add['diagnosa_kode']) ? $add['diagnosa_kode'] : null;
                    $stringDiagnosa = ltrim($stringDiagnosa, "-");
                    if (isset($add['proc_code']) && isset($add['proc_name'])) {
                        array_push($arrSpecial, $add['proc_code']);
                        $modelGrouper->sp_procedure_kode = $add['proc_code'];
                        $modelGrouper->sp_procedure_nama = $add['proc_name'];
                    }
                    if (isset($add['pros_code']) && isset($add['pros_name'])) {
                        array_push($arrSpecial, $add['pros_code']);
                        $modelGrouper->sp_prosthesis_kode = $add['pros_code'];
                        $modelGrouper->sp_prosthesis_nama = $add['pros_name'];
                    }
                    if (isset($add['drug_code']) && isset($add['drug_name'])) {
                        array_push($arrSpecial, $add['drug_code']);
                        $modelGrouper->sp_drug_kode = $add['drug_code'];
                        $modelGrouper->sp_drug_nama = $add['drug_name'];
                    }
                    if (isset($add['inv_code']) && isset($add['inv_name'])) {
                        array_push($arrSpecial, $add['inv_code']);
                        $modelGrouper->sp_investigation_kode = $add['inv_code'];
                        $modelGrouper->sp_investigation_nama = $add['inv_name'];
                    }
                    $modelGrouper->special_group = (!empty($arrSpecial) ? implode(',', $arrSpecial) : '');
                    if(isset($add['diagnosa_kode'])) {
                        $arr = explode(",", $add['diagnosa_kode']);
                        if(is_array($arr)) {
                            foreach ($arr as $value) {
                                if(! empty($value)) {
                                    $diagnosaKode[] = $value;
                                }
                            }
                        }
                    }
                }

                $modelGrouper->save();
                $modelKlaim->klaimgroup_id = $modelGrouper->sy_klaimgroup_id;

                /**
                 * Deprecated Semenjak eklaim 5.10
                 */
                // if ($modelGrouper->save()) {
                //     $upgradeClassClass = '';
                //     $modelKlaim->klaimgroup_id = $modelGrouper->sy_klaimgroup_id;
                //     if ($modelKlaim->naik_kelas == 'kelas_4') {
                //         $upgradeClassClass = 'vip';
                //     } elseif ($modelKlaim->naik_kelas == 'kelas_5') {
                //         $upgradeClassClass = 'vvip';
                //     }

                //     if ($modelKlaim->is_naikkelas) {
                //         $editKlaim = [
                //             'metadata' => [
                //                 'method' => 'set_claim_data',
                //                 'nomor_sep' => $nosep,
                //             ],
                //             'data' => [
                //                 'nomor_sep' => $post['data']['no_sep'],
                //                 'nomor_kartu' => $post['data']['no_kartu'],
                //                 'tgl_masuk' => isset($post['data']['tgl_masuk']) ? $post['data']['tgl_masuk'] : date('Y-m-d H:i'),
                //                 'tgl_pulang' => isset($post['data']['tgl_keluar']) ? $post['data']['tgl_keluar'] : date('Y-m-d H:i'),
                //                 'jenis_rawat' => isset($post['data']['jenis']) ? $post['data']['jenis'] : "",
                //                 'kelas_rawat' => isset($post['data']['jenis_kelasrawat']) ? $post['data']['jenis_kelasrawat'] : "",
                //                 'payor_cd' => $this->getPayorCd(),
                //                 'payor_id' => $this->getPayorId(),
                //                 'coder_nik' => $this->getCoderNik(),
                //                 'add_payment_pct' => $modelGrouper->persen_tambahan,
                //                 'upgrade_class_ind' => $modelKlaim->is_naikkelas,
                //                 'upgrade_class_class' => isset($post['data']['naik_kelas']) ? $post['data']['naik_kelas'] : $upgradeClassClass,
                //                 'upgrade_class_los' => $modelKlaim->lama_naikkelas,
                //                 'upgrade_class_payor' => isset($post['data']['pembayar_selisih_biaya']) ? $post['data']['pembayar_selisih_biaya'] : "peserta"
                //             ]
                //         ];

                //         $editKlaim = json_decode(DocoHelpers::restInacbgs($editKlaim), true);
                //         if (isset($editKlaim['metadata']['status'])) {
                //             if ($editKlaim['metadata']['status'] != 200) {
                //                 return [
                //                     'status' => 422,
                //                     'code' => 422,
                //                     'title' => "Proses gagal! ",
                //                     'message' => $editKlaim['metadata']['message']
                //                 ];
                //             }
                //         }
                //     }else{
                //         $editKlaim = [
                //             'metadata' => [
                //                 'method' => 'set_claim_data',
                //                 'nomor_sep' => $nosep,
                //             ],
                //             'data' => [
                //                 'nomor_sep' => $post['data']['no_sep'],
                //                 'nomor_kartu' => $post['data']['no_kartu'],
                //                 'dializer_single_use' => isset($post['data']['dializer']) ? $post['data']['dializer'] : 0,
                //                 'kantong_darah' => isset($post['data']['transfusi_darah']) ? $post['data']['transfusi_darah'] : 0,
                //                 'tgl_masuk' => isset($post['data']['tgl_masuk']) ? $post['data']['tgl_masuk'] : date('Y-m-d H:i'),
                //                 'tgl_pulang' => isset($post['data']['tgl_keluar']) ? $post['data']['tgl_keluar'] : date('Y-m-d H:i'),
                //                 'jenis_rawat' => isset($post['data']['jenis']) ? $post['data']['jenis'] : "",
                //                 'kelas_rawat' => isset($post['data']['jenis_kelasrawat']) ? $post['data']['jenis_kelasrawat'] : "",
                //                 'payor_cd' => $this->getPayorCd(),
                //                 'payor_id' => $this->getPayorId(),
                //                 'coder_nik' => $this->getCoderNik(),
                //                 'add_payment_pct' => $modelGrouper->persen_tambahan,
                //             ]
                //         ];
                //         $editKlaim = json_decode(DocoHelpers::restInacbgs($editKlaim), true);
                //         if (isset($editKlaim['metadata']['code'])) {
                //             if ($editKlaim['metadata']['code'] != 200) {
                //                 return [
                //                     'status' => 422,
                //                     'code' => 422,
                //                     'title' => "Proses gagal! ",
                //                     'message' => $editKlaim['metadata']['message']
                //                 ];
                //             }
                //         }
                //     }
                // } else {
                //     throw new \Exception(json_encode($modelGrouper->getErrors()));
                // }
            }
            
            $modelKlaim->status_klaim = true;
            $modelKlaim->is_komplikasi = isset($post['data']['is_komplikasi']) ? $post['data']['is_komplikasi'] : false;
            $modelKlaim->is_pemulasaranjenazah = isset($post['data']['is_pemulasaranjenazah']) ? $post['data']['is_pemulasaranjenazah'] : false;
            $modelKlaim->is_kantongjenazah = isset($post['data']['is_kantongjenazah']) ? $post['data']['is_kantongjenazah'] : false;
            $modelKlaim->is_petijenazah = isset($post['data']['is_petijenazah']) ? $post['data']['is_petijenazah'] : false;
            $modelKlaim->is_plastikerat = isset($post['data']['is_plastikerat']) ? $post['data']['is_plastikerat'] : false;
            $modelKlaim->is_desinfektanjenazah = isset($post['data']['is_desinfektanjenazah']) ? $post['data']['is_desinfektanjenazah'] : false;
            $modelKlaim->is_transport = isset($post['data']['is_transport']) ? $post['data']['is_transport'] : false;
            $modelKlaim->is_desinfektanmobil = isset($post['data']['is_desinfektanmobil']) ? $post['data']['is_desinfektanmobil'] : false;
            $modelKlaim->dializer = isset($post['data']['dializer']) ? $post['data']['dializer'] : null;
            $modelKlaim->transfusi_darah = isset($post['data']['transfusi_darah']) ? $post['data']['transfusi_darah'] : null;
            if (!$modelKlaim->save()) {
                throw new \Exception(json_encode($modelKlaim->getErrors()));
            }
            $finalklaim = [
                'metadata' => [
                    'method' => 'claim_final',
                ],
                'data' => [
                    'nomor_sep' => $nosep,
                    'coder_nik' => $this->getCoderNik(),
                ]
            ];
            $finalklaim = json_decode(DocoHelpers::restInacbgs($finalklaim), true);
            if (isset($finalklaim['metadata']['status'])) {
                if ($finalklaim['metadata']['status'] != 200) {
                    return [
                        'status' => 422,
                        'code' => 422,
                        'title' => "Proses gagal! ",
                        'message' => $finalklaim['metadata']['message']
                    ];
                }
            }
            if ($finalklaim) {
                $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => $statusFinal], ' kunjungan_id =' . $pendaftaran_id . '')->execute();
            }
            $transaction->commit();
            return $finalklaim;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }
    public function actionUpdateKlaim()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pendaftaran_id = $post['pendaftaran_id'];
        $nosep = $post['nomor_sep'];
        $connection = Yii::$app->db;
        $status = DocoConstants::STATUS_PROSES_KLAIM;
        $transaction = $connection->beginTransaction();
        try {
            // $findParent = KlaimInacbg::find()->where(['pasienadmisi_id'=>$post['admisi']])->one();
            $findParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $pendaftaran_id])->one();
            if (empty($findParent)) {
                return [
                    'status' => 400,
                    'title' => "Proses gagal! ",
                    'text' => "Data klaim tidak temukan !",
                ];
            }

            $findParent->status_klaim = false;
            if ($findParent->save()) {
                $deleteGroup = $connection->createCommand('update sy_klaimgroup_t set is_deleted = true where sy_klaiminacbg_id = ' . $findParent->sy_klaiminacbg_id . ' and is_deleted = false')->execute();
                $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => $status], ' kunjungan_id =' . $pendaftaran_id . '')->execute();
                // $updateAdmisi = PasienAdmisi::find()->where(['pasienadmisi_id'=>$post['admisi']])->one();
                // $updateAdmisi->status_kunjungan = DocoConstants::STATUS_PROSES_KLAIM;
                // $saveAdmisi = $updateAdmisi->save();
                // if(!$saveAdmisi){
                //     throw new \Exception("Data gagal disimpan");
                // }
                $reedit = [
                    'metadata' => [
                        'method' => 'reedit_claim',
                    ],
                    'data' => [
                        'nomor_sep' => $nosep,
                    ],
                ];
                $response = json_decode(DocoHelpers::restInacbgs($reedit), true);
                if ($response['metadata']['code'] != 200) {
                    return [
                        'status' => 422,
                        'title' => "Proses gagal! ",
                        'text' => $response['metadata']['message'],
                    ];
                }
                $transaction->commit();
                return true;
            } else {
                throw new \Exception("\Terjadi Kesalahan");
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    private function getKlaimGroup($klaiminacbg_id)
    {
        $data = KlaimInacbgGroup::find()->where(['klaiminacbg_id' => $klaiminacbg_id])->one();

        return $data;
    }

    public function actionKirimKlaimOnline()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $pendaftaran_id = $post['pendaftaran_id'];
        $nosep = $post['nomor_sep'];
        $connection = Yii::$app->db;
        try {

            $data = [
                'metadata' => [
                    'method' => 'send_claim_individual',
                ],
                'data' => [
                    'nomor_sep' => $nosep,
                ],
            ];
            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            if ($response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => $response['metadata']['message']
                ];
            }

            $findParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $pendaftaran_id, 'is_deleted' => false])->one();
            $findParent->is_terkirim = true;
            if (!$findParent->save()) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Data gagal disimpan'
                ];
            }
            return true;
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionUpdateData()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kunjungan_id = $post['kunjungan_id'];
        $no_sep       = $post['no_sep'];
        $no_kartu     = $post['no_kartu'];
        $kelas_kode   = $post['kelas_kode'];

        try {
            $model = SyKunjunganPasien::find()->where(['kunjungan_id' => $kunjungan_id])->one();
            $model->no_sep = $no_sep;
            $model->no_asuransi = $no_kartu;
            $model->hak_kelasbpjs = $kelas_kode;

            if (!$model->save()) {
                $model->errors;
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Proses update no sep gagal.'
                ];
            } else {
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Proses update no sep berhasil.'
                ];
            }
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    public function actionDetail($id)
    {
        $old = [];
        $fl_detail = false;
        $model = $this->actionGetData($id);

        $detail = KoreksiDiagnosaNewView::find()->where(['kunjungan_id' => $id])->all();
        if (!$detail) {
            $fl_detail = true;
            $detail = SyKunjunganDetailView::find()->where([
                'kunjungan_id' => $id
            ])->all();
        } else {
            $old = SyKunjunganDetailView::find()->where([
                'kunjungan_id' => $id
            ])->all();
        }
        $tmp = [
            'diagnosa_utama' => [],
            'diagnosa_utama_ina' => [],
            'diagnosa_tambahan' => [],
            'diagnosa_tambahan_ina' => [],
            'diagnosa_opertindakan' => [],
            'diagnosa_opertindakan_ina' => [],
            'diagnosa_luar' => [],
            'diagnosa_morfologi' => [],
        ];
        $tmp_old = [
            'diagnosa_utama' => [],
            'diagnosa_utama_ina' => [],
            'diagnosa_tambahan' => [],
            'diagnosa_tambahan_ina' => [],
            'diagnosa_opertindakan' => [],
            'diagnosa_opertindakan_ina' => [],
            'diagnosa_luar' => [],
            'diagnosa_morfologi' => [],
        ];
        if ($old) {
            $i = 1;
            foreach ($old as $key => $value) {
                $inacbg = true;
                $icdprime = null;

                $diagnosa = InfoDiagnosa::find()->where([
                    'diagnosa_kode' => $value['diagnosa_kode']
                ])->select([
                    'diagnosa_id as id', 'diagnosa_kode as kode', 'diagnosa_nama as nama'
                ])->asArray()->one();

                if ($diagnosa) {
                    if (isset($value['is_inacbg']) || isset($value['is_icdprimer'])) {
                        $inacbg = $value['is_inacbg'];
                        $icdprime = $value['is_icdprimer'];
                    }
                    $diagnosa['is_inacbg'] = false;
                    $diagnosa['is_icdprimer'] = null;

                    $diagnosa['text'] = '';
                    $diagnosa_lama = $diagnosa['kode'] . ' - ' . $diagnosa['nama'];
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_UTAMA) {
                        if (!isset($tmp_old['diagnosa_utama'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_utama'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_TAMBAHAN || $value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_PENYERTA) {
                        if (!isset($tmp_old['diagnosa_tambahan'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_tambahan'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }

                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_OPERTINDAKAN || $value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_K) {
                        if (!isset($tmp_old['diagnosa_opertindakan'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_opertindakan'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_LUAR) {
                        if (!isset($tmp_old['diagnosa_luar'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_luar'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_MORFOLOGI) {
                        if (!isset($tmp_old['diagnosa_morfologi'][$diagnosa['kode']])) {
                            $tmp_old['diagnosa_morfologi'][$diagnosa['kode']] = [
                                'id'    => '',
                                'kode'  => '',
                                'nama'  => '',
                                'is_inacbg' => false,
                                'is_icdprimer' => null,
                                'text'  => '',
                                'diagnosa_lama'   => $diagnosa_lama,
                                'is_update' => false
                            ];
                        }
                    }
                }
            }
        }
        if ($detail) {
            $i = 1;
            foreach ($detail as $key => $value) {
                $inacbg = true;
                $icdprime = null;

                $diagnosa = InfoDiagnosa::find()->where([
                    'diagnosa_kode' => $value['diagnosa_kode']
                ])->select([
                    'diagnosa_id as id', 'diagnosa_kode as kode', 'diagnosa_nama as nama'
                ])->asArray()->one();
                if ($diagnosa) {
                    if (isset($value['is_inacbg']) || isset($value['is_icdprimer'])) {
                        $inacbg = $value['is_inacbg'];
                        $icdprime = $value['is_icdprimer'];
                    }
                    $diagnosa['is_inacbg'] = $inacbg;
                    $diagnosa['is_icdprimer'] = $icdprime;

                    $diagnosa['text'] = $diagnosa['kode'] . ' - ' . $diagnosa['nama'];

                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_UTAMA) {
                        if ($fl_detail) {
                            $diagnosa['is_icdprimer'] = true;
                        }
                        if (!isset($tmp['diagnosa_utama'][$diagnosa['kode']])) {
                            $tmp['diagnosa_utama'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false,
                                'is_inagrouper' => ArrayHelper::getValue($value, 'is_inagrouper')
                            ];
                        }
                        if (!isset($tmp['diagnosa_utama_ina'][$diagnosa['kode']])) {
                            $is_inagrouper = ArrayHelper::getValue($value, 'is_inagrouper');
                            if ($is_inagrouper == true) {
                                $tmp['diagnosa_utama_ina'][$diagnosa['kode']] = [
                                    'id'    => $diagnosa['id'],
                                    'kode'  => $diagnosa['kode'],
                                    'nama'  => $diagnosa['nama'],
                                    'is_inacbg' => $diagnosa['is_inacbg'],
                                    'is_icdprimer' => $diagnosa['is_icdprimer'],
                                    'text'  => $diagnosa['text'],
                                    'diagnosa_lama'   => '-',
                                    'is_update' => false,
                                    'is_inagrouper' => ArrayHelper::getValue($value, 'is_inagrouper')
                                ];
                            }
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_utama'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_TAMBAHAN || $value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_PENYERTA) {
                        if (!isset($tmp['diagnosa_tambahan'][$diagnosa['kode']])) {
                            $tmp['diagnosa_tambahan'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false,
                                'is_inagrouper' => ArrayHelper::getValue($value, 'is_inagrouper')
                            ];
                        }
                        if (!isset($tmp['diagnosa_tambahan_ina'][$diagnosa['kode']])) {
                            $is_inagrouper = ArrayHelper::getValue($value, 'is_inagrouper');
                            if ($is_inagrouper == true) {
                                $tmp['diagnosa_tambahan_ina'][$diagnosa['kode']] = [
                                    'id'    => $diagnosa['id'],
                                    'kode'  => $diagnosa['kode'],
                                    'nama'  => $diagnosa['nama'],
                                    'is_inacbg' => $diagnosa['is_inacbg'],
                                    'is_icdprimer' => $diagnosa['is_icdprimer'],
                                    'text'  => $diagnosa['text'],
                                    'diagnosa_lama'   => '-',
                                    'is_update' => false,
                                    'is_inagrouper' => $is_inagrouper
                                ];
                            }
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_tambahan'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_OPERTINDAKAN || $value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_TERAPI) {
                        if (!isset($tmp['diagnosa_opertindakan'][$diagnosa['kode']])) {
                            $tmp['diagnosa_opertindakan'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false,
                                'is_inagrouper' => ArrayHelper::getValue($value, 'is_inagrouper')
                            ];
                        }
                        if (!isset($tmp['diagnosa_opertindakan_ina'][$diagnosa['kode']])) {
                            $is_inagrouper = ArrayHelper::getValue($value, 'is_inagrouper');
                            if ($is_inagrouper == true) {
                                $tmp['diagnosa_opertindakan_ina'][$diagnosa['kode']] = [
                                    'id'    => $diagnosa['id'],
                                    'kode'  => $diagnosa['kode'],
                                    'nama'  => $diagnosa['nama'],
                                    'is_inacbg' => $diagnosa['is_inacbg'],
                                    'is_icdprimer' => $diagnosa['is_icdprimer'],
                                    'text'  => $diagnosa['text'],
                                    'diagnosa_lama'   => '-',
                                    'is_update' => false,
                                    'is_inagrouper' => $is_inagrouper
                                ];
                            }
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_opertindakan'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_LUAR) {
                        if (!isset($tmp['diagnosa_luar'][$diagnosa['kode']])) {
                            $tmp['diagnosa_luar'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false,
                                'is_inagrouper' => ArrayHelper::getValue($value, 'is_inagrouper')
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_luar'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                    if ($value['kelompok_diagnosa'] == DocoConstants::DIAGNOSA_MORFOLOGI) {
                        if (!isset($tmp['diagnosa_morfologi'][$diagnosa['kode']])) {
                            $tmp['diagnosa_morfologi'][$diagnosa['kode']] = [
                                'id'    => $diagnosa['id'],
                                'kode'  => $diagnosa['kode'],
                                'nama'  => $diagnosa['nama'],
                                'is_inacbg' => $diagnosa['is_inacbg'],
                                'is_icdprimer' => $diagnosa['is_icdprimer'],
                                'text'  => $diagnosa['text'],
                                'diagnosa_lama'   => '-',
                                'is_update' => false,
                                'is_inagrouper' => ArrayHelper::getValue($value, 'is_inagrouper')
                            ];
                        }
                        if ($fl_detail) {
                            $tmp['diagnosa_morfologi'][$diagnosa['kode']]['diagnosa_lama'] = $diagnosa['text'];
                        }
                    }
                }
            }
        }
        $result = [];
        if ($old) {
            $i = 0;
            foreach ($tmp_old as $diagnosa => $value) {
                if (!isset($result[$diagnosa])) {
                    $result[$diagnosa] = [];
                }
                if ($tmp_old[$diagnosa]) {
                    $total_diagnosa_lama = count($tmp_old[$diagnosa]);
                    $total_diagnosa_baru = count($tmp[$diagnosa]);
                    $tmp_check = 1;
                    foreach ($tmp_old[$diagnosa] as $code => $old_value) {
                        $item = [
                            'id' => '',
                            'kode' => '',
                            'nama' => '',
                            'text' => '',
                            'diagnosa_lama' => '',
                            'is_inacbg' => false,
                            'is_icdprimer' => null,
                            'is_update' => true,
                            'is_inagrouper' => false,
                        ];
                        if ($total_diagnosa_lama < $total_diagnosa_baru) {
                            if ($tmp_check <= $total_diagnosa_baru) {
                                foreach ($tmp[$diagnosa] as $new_code => $items) {
                                    if (isset($tmp[$diagnosa][$code]) && !$tmp[$diagnosa][$code]['is_update']) {
                                        $item['id'] = $tmp[$diagnosa][$code]['id'];
                                        $item['kode'] = $tmp[$diagnosa][$code]['kode'];
                                        $item['nama'] = $tmp[$diagnosa][$code]['nama'];
                                        $item['text'] = $tmp[$diagnosa][$code]['text'];
                                        $item['is_inacbg'] = $tmp[$diagnosa][$code]['is_inacbg'];
                                        $item['is_icdprimer'] = $tmp[$diagnosa][$code]['is_icdprimer'];
                                        $item['is_inagrouper'] = $tmp[$diagnosa][$code]['is_inagrouper'];
                                        $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                        $tmp[$diagnosa][$code]['is_update'] = true;
                                        $tmp_check++;
                                        array_push($result[$diagnosa], $item);
                                    } else {
                                        if ($tmp_check <= $total_diagnosa_baru) {
                                            foreach ($tmp[$diagnosa] as $new_code => $items) {
                                                $item = [
                                                    'id' => '',
                                                    'kode' => '',
                                                    'nama' => '',
                                                    'text' => '',
                                                    'diagnosa_lama' => '-',
                                                    'is_inacbg' => false,
                                                    'is_icdprimer' => null,
                                                    'is_update' => true,
                                                    'is_inagrouper' => false,
                                                ];
                                                if (!$items['is_update']) {
                                                    $item['id'] = $items['id'];
                                                    $item['kode'] = $items['kode'];
                                                    $item['nama'] = $items['nama'];
                                                    $item['text'] = $items['text'];
                                                    $item['is_inacbg'] = $items['is_inacbg'];
                                                    $item['is_icdprimer'] = $items['is_icdprimer'];
                                                    $item['is_inagrouper'] = $items['is_inagrouper'];
                                                    $tmp[$diagnosa][$new_code]['is_update'] = true;
                                                    array_push($result[$diagnosa], $item);
                                                    $tmp_check++;
                                                    break;
                                                }
                                            }
                                        } else {
                                            // $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                            $tmp[$diagnosa][$new_code]['is_update'] = true;
                                            array_push($result[$diagnosa], $item);
                                            $tmp_check++;
                                        }
                                    }
                                }
                            }
                        } else {
                            if (isset($tmp[$diagnosa][$code]) && !$tmp[$diagnosa][$code]['is_update']) {
                                $item['id'] = $tmp[$diagnosa][$code]['id'];
                                $item['kode'] = $tmp[$diagnosa][$code]['kode'];
                                $item['nama'] = $tmp[$diagnosa][$code]['nama'];
                                $item['text'] = $tmp[$diagnosa][$code]['text'];
                                $item['is_inacbg'] = $tmp[$diagnosa][$code]['is_inacbg'];
                                $item['is_icdprimer'] = $tmp[$diagnosa][$code]['is_icdprimer'];
                                $item['is_inagrouper'] = $tmp[$diagnosa][$code]['is_inagrouper'];
                                $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                $tmp[$diagnosa][$code]['is_update'] = true;
                                $tmp_check++;
                                array_push($result[$diagnosa], $item);
                            } else {
                                if ($tmp_check <= $total_diagnosa_baru) {
                                    foreach ($tmp[$diagnosa] as $new_code => $items) {
                                        $item = [
                                            'id' => '',
                                            'kode' => '',
                                            'nama' => '',
                                            'text' => '',
                                            'diagnosa_lama' => $old_value['diagnosa_lama'],
                                            'is_inacbg' => false,
                                            'is_icdprimer' => null,
                                            'is_update' => true,
                                            'is_inagrouper' => false
                                        ];
                                        if (!$items['is_update']) {
                                            $item['id'] = $items['id'];
                                            $item['kode'] = $items['kode'];
                                            $item['nama'] = $items['nama'];
                                            $item['text'] = $items['text'];
                                            $item['is_inacbg'] = $items['is_inacbg'];
                                            $item['is_icdprimer'] = $items['is_icdprimer'];
                                            $item['is_inagrouper'] = $items['is_inagrouper'];
                                            $tmp[$diagnosa][$new_code]['is_update'] = true;
                                            array_push($result[$diagnosa], $item);
                                            $tmp_check++;
                                            break;
                                        }
                                    }
                                } else {
                                    $item['diagnosa_lama'] = $old_value['diagnosa_lama'];
                                    $tmp_check++;
                                    array_push($result[$diagnosa], $item);
                                }
                            }
                        }
                    }
                } else {
                    if ($tmp[$diagnosa]) {
                        foreach ($tmp[$diagnosa] as $code => $items) {
                            $item = [
                                'id' => $items['id'],
                                'kode' => $items['kode'],
                                'nama' => $items['nama'],
                                'text' => $items['text'],
                                'diagnosa_lama' => $items['diagnosa_lama'],
                                'is_inacbg' => $items['is_inacbg'],
                                'is_icdprimer' => $items['is_icdprimer'],
                                'is_update' => true,
                                'is_inagrouper' => ArrayHelper::getValue($items, 'is_inagrouper')
                            ];
                            array_push($result[$diagnosa], $item);
                        }
                    }
                }
            }
        } else {
            if ($tmp) {
                foreach ($tmp as $diagnosa => $value) {
                    if (!isset($result[$diagnosa])) {
                        $result[$diagnosa] = [];
                    }
                    if ($tmp[$diagnosa]) {
                        foreach ($tmp[$diagnosa] as $code => $items) {
                            $item = [
                                'id' => $items['id'],
                                'kode' => $items['kode'],
                                'nama' => $items['nama'],
                                'text' => $items['text'],
                                'diagnosa_lama' => $items['diagnosa_lama'],
                                'is_inacbg' => $items['is_inacbg'],
                                'is_icdprimer' => $items['is_icdprimer'],
                                'is_update' => true,
                                'is_inagrouper' => ArrayHelper::getValue($items, 'is_inagrouper')
                            ];
                            array_push($result[$diagnosa], $item);
                        }
                    }
                }
            }
        }

        return [
            'header' => $model,
            'detail' => $detail,
            'mapping' => DocoConstants::$mapp_kel_diagnosa,
            'hasil_diagnosa' => $result,
            'fl_detail' => $fl_detail
        ];
    }

    public function actionGetKunjunganDetail($id)
    {
        try {
            $find = SyKunjungan::find()->where(['kunjungan_id' => $id])->andWhere(['<>', 'status_kunjungan', 0])->asArray()->one();
            return $find;
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $temp_inacbg = [];
        $transaction = $connection->beginTransaction();

        try {
            $kunjunganId = $request->post('kunjungan_id');
            $data_koreksi = $request->post('data_koreksi');
            $data_koreksi = json_decode($data_koreksi, true);
            $is_inacbg = $request->post('is_inacbg');
            $is_icdprimer = $request->post('is_icdprimer');
            $is_icdprimer_ina = $request->post('is_icdprimer_ina');
            $edit_koreksi = $request->get('edit_koreksi');
            $existKoreksi = SyKoreksiDiagnosa::find()
                ->where(['kunjungan_id' => $kunjunganId])
                ->one();

            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'kunjungan_id',
                    'status_kunjungan'
                ])
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->asArray()->one();

            if (!empty($dataKunjungan)) {
                $status_kunjungan = ArrayHelper::getValue($dataKunjungan, 'status_kunjungan');
                if ($status_kunjungan == DocoConstants::STATUS_PROSES_KLAIM) {
                    return [
                        'status' => 400,
                        'message' => "Status sedang di proses klaim"
                    ];
                }

                if ($status_kunjungan == DocoConstants::STATUS_FINAL_KLAIM) {
                    return [
                        'status' => 400,
                        'message' => "Status sudah di final klaim"
                    ];
                }
            }

            $delete = (new SyKoreksiDiagnosa)->delete([
                'kunjungan_id' => $kunjunganId
            ]);
            if (is_array($is_inacbg)) {
                foreach ($is_inacbg as $key => $value) {
                    $temp_inacbg[] = $key;
                }
            }
            if (is_array($data_koreksi)) {
                $tmp = [];
                $listCheck = [];

                $checkKasus = SyKoreksiDiagnosa::find()->where([
                    'kunjungan_id' => $kunjunganId
                ])->asArray()->all();

                foreach ($checkKasus as $value) {
                    $listCheck[$value['diagnosa_id']] = true;
                }
                foreach ($data_koreksi as $value) {
                    $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
                    $diagnosaText = ArrayHelper::getValue($value, 'diagnosa_text');
                    $diagnosaKelompok = ArrayHelper::getValue($value, 'diagnosa_kelompok');
                    $dianosaInagrouper = ArrayHelper::getValue($value, 'diagnosa_inacgrouper');

                    if (empty($diagnosaId)) continue;

                    $diagnosaKode = InfoDiagnosa::find()->where([
                        'diagnosa_id' => $diagnosaId
                    ])->select([
                        'diagnosa_kode'
                    ])->asArray()->one();

                    $detailKunjungan = SyKunjunganDetailView::find()->where([
                        'kunjungan_id' => $kunjunganId,
                        'diagnosa_kode' => $diagnosaKode
                    ])->asArray()->one();

                    $item = [
                        'kunjungan_id' => (int) $kunjunganId,
                        'tgl_koreksidiagnosa' => date('Y-m-d H:i:s'),
                        'kelompokdiagnosa_id' => (int) $diagnosaKelompok,
                        'diagnosa_id' => (int) $diagnosaId,
                        'diagnosaasal_id' => ArrayHelper::getValue($detailKunjungan, 'kunjungandetail_id'),
                        'diag_asal_masuk' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_TAMBAHAN ? @$diagnosaText : null,
                        'diag_asal_utama' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_UTAMA ? @$diagnosaText : null,
                        'diag_asal_penyerta' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN ? @$diagnosaText : null,
                        'diag_asal_terapi' => $diagnosaKelompok == DocoConstants::MAP_DIAGNOSA_LUAR ? @$diagnosaText : null,
                        'is_diagnosa_baru' => isset($listCheck[$diagnosaId]) ? false : true,
                        'is_inacbg' => false,
                        'is_icdprimer' => false,
                        'is_inagrouper' => !empty($dianosaInagrouper) && $dianosaInagrouper == 1 ? true : false
                    ];
                    if (in_array($diagnosaId, $temp_inacbg)) {
                        $item['is_inacbg'] = true;
                    }
                    if ($is_icdprimer == $diagnosaId) {
                        $item['is_icdprimer'] = true;
                    }
                    if (!empty($dianosaInagrouper) && $dianosaInagrouper == 1) {
                        if ($is_icdprimer_ina == $diagnosaId) {
                            $item['is_icdprimer'] = true;
                        }
                    }
                    array_push($tmp, $item);
                }
            }
            SyKoreksiDiagnosa::batchInsert($tmp, false);
            $connection->createCommand()->update('sy_kunjungan', ['status_kunjungan' => DocoConstants::STATUS_SUDAH_KOREKSI], ' kunjungan_id =' . $kunjunganId . '')->execute();
            $transaction->commit();
            return [
                'status' => 200,
                'message' => 'Data berhasil disimpan'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $term = $request->get('type');
        $word = $request->get('term');
        $not_in = $request->get('not_in') ? $request->get('not_in') : [];

        if ($term && $word) {
            return InfoDiagnosa::find()->where([
                'ILIKE', 'LOWER(tabularlist_versi)', strtolower($term)
            ])->andFilterWhere([
                'OR',
                ['ILIKE', 'LOWER(kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_kode)', strtolower($word)],
                ['ILIKE', 'LOWER(diagnosa_namalainnya)', strtolower($word)]
            ])->andWhere(['NOT IN', 'diagnosa_id', $not_in])->limit(10)->all();
        }
        return [];
    }

    public function actionGetKlaimGroup($id)
    {
        try {
            $find = KlaimInacbg::find()->select('klaimgroup_t.spesial_procedure, klaimgroup_t.spesial_prosthesis, klaimgroup_t.spesial_investigation, klaimgroup_t.spesial_drug, klaimgroup_t.total')->join('INNER JOIN', 'klaimgroup_t', 'klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id')->where(['klaiminacbg_t.pendaftaran_id' => $id, 'klaiminacbg_t.is_deleted' => false])->asArray()->one();
            return $find;
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan");
        }
    }

    private function cariDokter($dokter_id)
    {
        $dokter = Pegawai::find()->where([
            'pegawai_id' => $dokter_id,
            'kelompokpegawai_id' => 1
        ])->select([
            'pegawai_id', 'nama_pegawai', 'dokter_id'
        ])->asArray()->one();

        return $dokter;
    }

    public function actionSetPrimer()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $id = $post['id'];
        $dig = $post['diagnosa'];
        $connection = Yii::$app->db;
        try {
            $new = KoreksiDiagnosa::find()->where([
                'pendaftaran_id' => $id,
                'diagnosa_id' => $dig,
                'is_deleted' => false
            ])->one();

            $old = KoreksiDiagnosa::find()->where([
                'pendaftaran_id' => $id,
                'is_icdprimer' => true,
                'is_deleted' => false
            ])->one();

            if ($new && $old) {
                if ($new->diagnosa_id == $old->diagnosa_id) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal !',
                        'text' => 'Diagnosa Sudah Primer!'
                    ];
                }
                $new->is_icdprimer = true;
                $new->kelompokdiagnosa_id = DocoConstants::MAP_DIAGNOSA_UTAMA;
                $old->kelompokdiagnosa_id = DocoConstants::MAP_DIAGNOSA_TAMBAHAN;
                $old->is_icdprimer = false;
                if ($new->save() && $old->save()) {
                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil!',
                        'text' => 'Set Primer Berhasil.'
                    ];
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Terjadi Kesalahan'
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionAddDiagnosaTambahan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        try {
            $koreksiTambahan = new SyKoreksiDiagnosa;
            $koreksiTambahan->kunjungan_id = (int) $post['pendaftaran_id'];
            $koreksiTambahan->kelompokdiagnosa_id = ($post['type'] == 10) ? (int) DocoConstants::MAP_DIAGNOSA_TAMBAHAN : (int) DocoConstants::MAP_DIAGNOSA_OPERTINDAKAN;
            $koreksiTambahan->diagnosa_id = (int) $post['diagnosa_id'];
            $koreksiTambahan->tgl_koreksidiagnosa = date('Y-m-d H:i:s');
            $koreksiTambahan->is_inacbg = true;
            $koreksiTambahan->is_icdprimer = false;
            $koreksiTambahan->is_diagnosa_baru = true;
            if ($koreksiTambahan->save(false)) {
                return true;
            }
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $e->getMessage()
            ];
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

    private function getTotalRs($id)
    {
        $query = "
        SELECT
    sy_kunjungan.kunjungan_id AS pendaftaran_id,
    sy_kunjungan.no_pendaftaran,
    sy_kunjungan.instalasi_kode AS instalasi_id,
    sy_kunjungan.no_rekammedik AS no_rekam_medik,
    sy_kunjungan.nama_pasien,
    sy_kunjungan.no_sep AS nosep,
    tagihan.total AS tarif,
    COALESCE(adjusment.adj,0) AS adjusment,
    COALESCE(diskon.total_diskon,0) as diskon,
    COALESCE(tagihan.total,0) - COALESCE(adjusment.adj,0) AS total_akhir
    FROM sy_kunjungan
        JOIN (SELECT 
                    min(sy_kunjungan_1.kunjungan_id) AS kunjungan_id,
                    sy_kunjungan_1.no_pendaftaran
            FROM sy_kunjungan sy_kunjungan_1
            WHERE (sy_kunjungan_1.is_deleted = false)
            GROUP BY sy_kunjungan_1.no_pendaftaran
            ) kunjungan_1 ON sy_kunjungan.kunjungan_id = kunjungan_1.kunjungan_id
        LEFT JOIN (SELECT
                    sy_adjusmentdetail.no_pendaftaran,
                    sum(sy_adjusmentdetail.total_rs) as adj
                FROM sy_adjusmentdetail
                WHERE sy_adjusmentdetail.status_bayar is null 
                    OR sy_adjusmentdetail.status_bayar not in ('L')
                GROUP BY sy_adjusmentdetail.no_pendaftaran
                ) adjusment ON sy_kunjungan.no_pendaftaran = adjusment.no_pendaftaran  
        LEFT JOIN (SELECT
                        sy_kunjungantagihan.no_pendaftaran,
                        sy_kunjungantagihan.no_rekammedik,
                        sy_kunjungan.tgl_pulang,
                        SUM (sy_kunjungantagihan.layanan_tarif) AS total 
                    FROM sy_kunjungantagihan 
                    JOIN sy_kunjungan ON sy_kunjungantagihan.no_pendaftaran = sy_kunjungan.no_pendaftaran and sy_kunjungan.is_deleted=FALSE
                    JOIN (SELECT 
                            min(sy_kunjungan_1.kunjungan_id) AS kunjungan_id,
                            sy_kunjungan_1.no_pendaftaran
                        FROM sy_kunjungan sy_kunjungan_1
                        WHERE (sy_kunjungan_1.is_deleted = false)
                        GROUP BY sy_kunjungan_1.no_pendaftaran
                        ) min_kunjungan ON sy_kunjungan.kunjungan_id = min_kunjungan.kunjungan_id
                    JOIN (SELECT tagihan.kel_report,
                                tagihan.no_pendaftaran,
                                tagihan.no_buktitrans
                        FROM sy_kunjungantagihan tagihan
                        WHERE tagihan.is_deleted = false 
                            AND tagihan.kel_report IS NOT NULL
                        ) bukti_trans ON sy_kunjungantagihan.no_buktitrans = bukti_trans.kel_report
                    WHERE sy_kunjungantagihan.is_deleted = false
                    and  sy_kunjungantagihan.status_bayar is null
                    or sy_kunjungantagihan.status_bayar not in ('B', 'L')
                    GROUP BY sy_kunjungantagihan.no_pendaftaran, sy_kunjungantagihan.no_rekammedik, sy_kunjungan.tgl_pulang
                    ) tagihan ON sy_kunjungan.no_rekammedik = tagihan.no_rekammedik and tagihan.tgl_pulang = sy_kunjungan.tgl_pulang        
            LEFT JOIN (select 
                        sy_potongantagihan.no_pendaftaran,
                        sum(sy_potongantagihan.total) as total_diskon
                        from sy_potongantagihan
                        where sy_potongantagihan.is_deleted=FALSE
                        GROUP BY sy_potongantagihan.no_pendaftaran
                    ) diskon ON sy_kunjungan.no_pendaftaran = diskon.no_pendaftaran      
            WHERE  sy_kunjungan.is_deleted=false and sy_kunjungan.kunjungan_id ='" . $id . "'";

        $data = Yii::$app->db->createCommand($query)->queryOne();
        return $data;
    }

    public function actionGetListDiagnosa()
    {
        ini_set('memory_limit', '-1');

        try {
            $request = Yii::$app->request;
            $model = new DiagnosaView;
            $type = $request->get('type');
            $column = [
                'diagnosa_id',
                'diagnosa_kode',
                'diagnosa_nama',
                'tabularlist_versi',
                'tabularlist_chapter',
                'lower(diagnosa_nama) as diagnosa_lower',
                'LOWER(diagnosa_kode) as diagnosa_kode_lower',
                'validcode',
                'accpdx',
                'asterik',
                'ina_grouper',
                'validcode_idrg'
            ];
            switch ($type) {
                case 'ina':
                    $diagnosa = $model::find()->select($column)
                        ->where(new Expression("type = 'IDRG' OR type IS NULL"))
                        ->asArray()->all();
                    break;
                case 'unu':
                    $diagnosa = $model::find()->select($column)
                        ->where(new Expression("type = 'INACBG' OR type IS NULL"))
                        ->asArray()->all();
                    break;
                default:
                    $diagnosa = $model::find()->select($column)->asArray()->all();
                    break;
            }

            return $response = [
                'diagnosa'   => $diagnosa,
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $e->getMessage()
            ];
        }
    }

    public function getEpisode($id)
    {
        try {
            $model = KlaimEpisode::find()->where(['klaiminacbg_id' => $id])->asArray()->all();
            return $model;
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionUploadBerkas()
    {


        $request = Yii::$app->request;
        $post = $request->post();

        // $connection = Yii::$app->db;
        try {
            $data = [
                'metadata' => [
                    'method' => 'file_upload',
                    "nomor_sep" => $post['nosep'],
                    "file_class" => $post['label'],
                    "file_name" => $post['filename']
                ],
                'data' => $post['data']
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            return $response;
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionDeleteBerkas()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        try {
            $data = [
                'metadata' => [
                    'method' => 'file_delete',
                ],
                'data' => [
                    'nomor_sep' => $post['nosep'],
                    'file_id'   => $post['file_id']
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            return $response['metadata'];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionGetBerkas()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $data = [
                'metadata' => [
                    'method' => 'file_get',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep']
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            return isset($response['response']) ? $response['response'] : $response;
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    private function getPayorId()
    {
        $staticPayorId = 3;
        $payorId = null;
        $result = Yii::$app->runAction('v1/allow/get-lookup-by-type', ['type' => 'bpjs_live', 'name' => 'payor_id']);
        $result = isset($result['response']) ? $result['response'] : [];

        foreach ($result as $res) {
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

        foreach ($result as $res) {
            $payorCd = $res['lookup_value'];
        }

        return (!empty($payorCd) && !is_null($payorCd)) ? $payorCd : $staticPayorCd;
    }

    public function actionUpdateNoklaim()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $kunjunganId = ArrayHelper::getValue($get, 'kunjungan_id');
            $isJaminan = ArrayHelper::getValue($get, 'is_jaminan');

            if (!empty($kunjunganId)) {
                $existingData = SyKunjunganPasien::find()
                    ->where(['kunjungan_id'  => $kunjunganId])
                    ->one();

                if ($existingData) {
                    if ($isJaminan == true) {
                        $existingData->no_klaimcovid = null;
                    }
                    // $existingData->status_kunjungan = DocoConstants::STATUS_SUDAH_KOREKSI;
                    $existingData->save();

                    return  [
                        'status' => 200,
                        'message' => "Data berhasil di update!"
                    ];
                }

                return  [
                    'status' => 200,
                    'message' => "Data ditemukan, Namun tidak di update!"
                ];
            }

            return  [
                'status' => 200,
                'message' => "Data kunjungan tidak ditemukan"
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Terjadi Kesalahan'
            ];
        }
    }

    public function actionValidasiSitb()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        try {
            $getklaim = [
                'metadata' => [
                    'method' => 'get_claim_data',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep'],
                ],
            ];
            $res = DocoHelpers::restInacbgs($getklaim);
            $res = json_decode($res, true);
            if ($res['metadata']['code'] != 200) {
                $newClaim['metadata']['method'] = 'new_claim';
                $newClaim['data']['nomor_kartu'] = $get['nomer_peserta'];
                $newClaim['data']['nomor_sep'] = $get['nosep'];
                $newClaim['data']['nomor_rm'] = $get['no_rekammedik'];
                $newClaim['data']['nama_pasien'] = $get['nama_pasien'];
                $newClaim['data']['tgl_lahir'] = $get['tanggal_lahir'];
                $newClaim['data']['gender'] = ($get['jenis_kelamin'] == DocoConstants::LAKI) ? DocoConstants::JENIS_LAKI : DocoConstants::JENIS_PEREMPUAN;
                $response = json_decode(DocoHelpers::restInacbgs($newClaim), true);
                if ($response['metadata']['code'] != 200) {
                    if ($response['metadata']['code'] != 400 && $response['metadata']['error_no'] != 'E2007') {
                        throw new \Exception("Terjadi Kesalahan");
                    }
                }
            }

            $data = [
                'metadata' => [
                    'method' => 'sitb_validate',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep'],
                    "nomor_register_sitb" => ArrayHelper::getValue($get, 'nomer_sitb')
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            if ($response['metadata']['code'] == 400) {
                return [
                    "code" => $response['metadata']['code'],
                    "message" => $response['metadata']['message'],
                    "response" => []
                ];
            }

            $batalvalidate = [
                'metadata' => [
                    'method' => 'sitb_invalidate',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep'],
                ]
            ];

            $batalvalidate = json_decode(DocoHelpers::restInacbgs($batalvalidate), true);

            return [
                "code" => $response['metadata']['code'],
                "message" => $response['metadata']['message'],
                "response" => $response['response']
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionBatalValidasiSitb()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $kunjungan_id = ArrayHelper::getValue($get, 'kunjungan_id');

        try {
            if (!empty($kunjungan_id)) {
                $modelParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjungan_id])->one();
                if (!empty($modelParent)) {
                    $modelParent->is_pasientb = false;
                    $modelParent->number_pasientb = null;
                    $modelParent->save();
                }
            }
            $data = [
                'metadata' => [
                    'method' => 'sitb_invalidate',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep'],
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            if ($response['metadata']['code'] == 400) {
                return [
                    "code" => $response['metadata']['code'],
                    "message" => $response['metadata']['message'],
                    "response" => []
                ];
            }
            return [
                "code" => $response['metadata']['code'],
                "message" => $response['metadata']['message'],
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $th->getMessage()
            ];
        }
    }

    public function actionKonfirmasiSitb()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $kunjungan_id = ArrayHelper::getValue($get, 'kunjungan_id');

        try {
            $modelParent = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjungan_id])->one();
            if (!$modelParent) {
                $modelParent = new SyKlaimInacbg;
            }

            $data = [
                'metadata' => [
                    'method' => 'sitb_validate',
                ],
                'data' => [
                    'nomor_sep' => $get['nosep'],
                    "nomor_register_sitb" => ArrayHelper::getValue($get, 'nomer_sitb')
                ]
            ];

            $response = json_decode(DocoHelpers::restInacbgs($data), true);
            if ($response['metadata']['code'] == 400) {
                return [
                    "code" => $response['metadata']['code'],
                    "message" => $response['metadata']['message'],
                    "response" => []
                ];
            }

            if ($response['metadata']['code'] = 200) {
                $modelParent->kunjungan_id = $kunjungan_id;
                $modelParent->number_pasientb =  ArrayHelper::getValue($get, 'nomer_sitb');
                $modelParent->is_pasientb = true;
                $modelParent->save();
            }

            return [
                "code" => $response['metadata']['code'],
                "message" => $response['metadata']['message'],
                "response" => $response['response']
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionFilters()
    {

        $payload = Yii::$app->request->get('additionalPayload', []);
        $page = Yii::$app->request->get('page', 1);
        $type = Yii::$app->request->get('type', null);
        $term = Yii::$app->request->get('term', null);
        $case = Yii::$app->request->get('case', null);
        $page = Yii::$app->request->get('page', 1);
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $result = [];
        if ($type == 'ruangan') {
            $instalasi = preg_replace('/\s+/', '', $case);

            $instalasiId = Instalasi::find()
                ->select(['instalasi_id'])
                ->where(['=', 'instalasi_singkatan', $instalasi])
                ->asArray()
                ->one();

            $instalasiId = ArrayHelper::getValue($instalasiId, 'instalasi_id');

            $result = Ruangan::find()->select(['ruangan_nama AS id', 'ruangan_nama AS text'])->where(['is_active' => true])
                ->where(['instalasi_id' => $instalasiId]);

            if (!empty($term)) {
                $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
            }

            $limit = 1000;

            $result->orderBy(['ruangan_nama' => SORT_ASC]);
        } elseif ($type == 'status') {
            $result = Lookup::find()
                ->select(['lookup_id AS id', 'lookup_name AS text'])
                ->where(['lookup_type' => 'status_verifikasi', 'is_active' => true]);

            if (!empty($term)) {
                $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
            }

            $result->orderBy(['lookup_name' => SORT_ASC]);
        } elseif ($type == 'carabayar') {
            $result = CaraBayar::find()
                ->select(['carabayar_nama AS id', 'carabayar_nama AS text'])
                ->where(['is_active' => true]);

            $result->orderBy(['carabayar_nama' => SORT_ASC]);
        } elseif ($type == 'penjamin') {
            $caraBayarPenjamin = CaraBayar::find()
                ->select(['carabayar_id'])
                ->where(['is_active' => true, 'carabayar_nama' => $case])
                ->asArray()
                ->one();

            $result = Penjamin::find()
                ->select(['penjamin_kode AS id', 'penjamin_nama AS text'])
                ->where(['is_active' => true, 'carabayar_id' => $caraBayarPenjamin]);

            $limit = 3000;

            $result->orderBy(['penjamin_nama' => SORT_ASC]);
        }

        if (!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }

    public function actionUnduhDokumen()
    {
        $request = Yii::$app->request;
        $arr_kunjungan = $request->post('kunjungan_id');

        // update status unduh dokumen
        $update = SyKunjunganPasien::updateAll(['status_unduh_dokumen' => DocoConstants::ON_PROGRES_UNDUH_DOKUMEN], ['in', 'kunjungan_id', $arr_kunjungan]);
        if($update){
            foreach ($arr_kunjungan as $key => $value) {
                (new RabbitBgProcess())->send([
                    'kunjungan_id' => $value
                ], 'integrasi_dokumen_eklaim', 'sync_dokumen_eklaim');
            }
            return [
                'status' => 200,
                'title' => 'Proses Berhasil !',
                'text' => 'Status Berhasil Diubah'
            ];
        } else {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Status Gagal Diubah'
            ];
        }
    }

    public function actionFtpSendFile()
    {
        $request = Yii::$app->request;
        $params = Yii::$app->params['iniFile'];
        $path = $request->get('filePath');
        $pendaftaran_id = $request->get('pendaftaran_id');
        try {
            if (file_exists($path)) {
                $host = isset($params['konfigftp']) ? $params['konfigftp']['host'] : null;
                $user = isset($params['konfigftp']) ? $params['konfigftp']['username'] : null;
                $password = isset($params['konfigftp']) ? $params['konfigftp']['password'] : null;
                $ftpConn = ftp_connect($host);
                $login = ftp_login($ftpConn, $user, $password);
                ftp_pasv($ftpConn, true);

                if ((!$ftpConn) || (!$login)) {
                    Yii::error('FTP connection has failed! Attempted to connect to ' . $host . ' for user ' . $user . '.');
                } else {

                    // Cari data kunjungan dulu.
                    $dokumenEklaim = DokumenEklaimParamV::find()
                        ->select([
                            'no_pendaftaran'
                        ])
                        ->where(['pendaftaran_id' =>  $pendaftaran_id])
                        ->asArray()->one();

                    $dataKunjungan = SyKunjunganPasien::find()
                        ->select([
                            'no_pendaftaran',
                            'nama_pasien',
                            'nosep',
                            'no_rekammedik'
                        ])
                        ->where(['no_pendaftaran' => $dokumenEklaim['no_pendaftaran']])
                        ->asArray()->one();
                    if (!empty($dataKunjungan)) {
                        $remote_file = isset($params['konfigftp']) ? $params['konfigftp']['path'] : null;
                        $remote_file = $remote_file . $dataKunjungan['no_rekammedik'] . '_' . $dataKunjungan['nama_pasien'] . '_' . $dataKunjungan['nosep'];
                        $dirExists = ftp_nlist($ftpConn, $remote_file);
                        $status = null;
                        if ($dirExists == false) {
                            @ftp_mkdir($ftpConn, $remote_file);
                            // ftp_chmod($ftpConn, 0777, $remote_file); Server windows belum support
                        }

                        $filename = pathinfo($path, PATHINFO_BASENAME);
                        if(ftp_put($ftpConn, $remote_file . '/' . $filename, $path, FTP_BINARY)){
                            $status = true;
                        }else{
                            $status = false;
                        }
                    }
                }
                ftp_close($ftpConn);
                
                // kondisi simpan log untuk CPPT Ranap
                $log = new LogDokumenEklaim;
                $log->dokumen_id = DocoConstants::CPPT_RANAP;
                $log->pendaftaran_id = $pendaftaran_id;
                $log->type_dokumen = 'konfig_dokumen';
                $log->status = $status;
                $log->save();

                Yii::error( [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Proses Kirim File Berhasil !',
                    'request' => $request->get('filePath')
                ]);
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Proses Kirim File Berhasil !'
                ];
            }
            Yii::error([
                'status' => 404,
                'title' => 'Proses Berhasil !',
                'text' => 'Proses Kirim File Berhasil !',
                'path' => $request->get('filePath'),
                'get' => $_GET
            ]);
            return [
                'status' => 404,
                'title' => 'Proses Berhasil !',
                'text' => 'Proses Kirim File Berhasil !'
            ];
        } catch (\Exception $th) {
            Yii::error([
                'status' => 500,
                'title' => 'Proses Gagal',
                'text' => $th->getMessage()
            ]);
            return [
                'status' => 500,
                'title' => 'Proses Gagal',
                'text' => $th->getMessage()
            ];
        }
    }

    public function actionGetDataDokumenUpload()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->post('pendaftaran_id');
    
        try {
            $dokumen = DokumenUploadT::find()
                ->select([
                    'dokumenupload_t.pendaftaran_id',
                    'dokumenupload_t.path',
                    'dokumenupload_t.filename',
                    'pendaftaran_t.no_pendaftaran',
                    'sy_kunjungan.nama_pasien',
                    'sy_kunjungan.nosep',
                    'sy_kunjungan.no_rekammedik',
                    'dokumenupload_t.dokumenupload_id',
                ])
                ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = dokumenupload_t.pendaftaran_id')
                ->leftJoin('sy_kunjungan', 'sy_kunjungan.no_pendaftaran = pendaftaran_t.no_pendaftaran')
                ->where(['IN', 'dokumenupload_t.pendaftaran_id', $pendaftaranId])
                ->andWhere(['dokumenupload_t.is_eklaim' => true])
                ->asArray()->all();

            return [
                'status' => 200,
                'data' => $dokumen,
                'message' => "Get data successfully"
            ];
        } catch (\Exception $th) {
            \Yii::$app->response->statusCode = 500;
            return [
                'status' => 500,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }

    public function actionGetLogActivity()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LogActivityR;
            $query = $model::find();
            if (isset($_GET['advanced-filter']['transaksi_id'])) {
                $query->andWhere(['=', 'transaksi_id', $_GET['advanced-filter']['transaksi_id']]);
                unset($_GET['advanced-filter']['transaksi_id']);
            }
            if (isset($_GET['advanced-filter']['tipe'])) {
                $query->andWhere(['=', 'tipe', $_GET['advanced-filter']['tipe']]);
                unset($_GET['advanced-filter']['tipe']);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query
            ]);
        } catch (Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionSaveLogActivity()
    {
        $request = Yii::$app->request;
        $post = $request->get();

        // simpan log activity
        $user = Pegawai::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->where(['pegawai_id' => Yii::$app->user->identity->pegawai_id])
            ->asArray()->one();

        $payloadLog = [
            'tipe' => 'EKLAIM',
            'aksi' => !empty($request->get('aksi')) ? $request->get('aksi') : null,
            'keterangan' => $user['nama_pegawai'],
            'transaksi_id' => !empty($request->get('kunjungan_id')) ? $request->get('kunjungan_id') : null,
            'additional_data' => !empty($request->get('response')) ? $request->get('response') : null,
            'additional_detail' => !empty($request->get('hasil')) ? $request->get('hasil') : null
        ];

        DocoHelpers::saveLogActivity($payloadLog);

        return $this->responseJson(200, 'Berhasil');
    }
    public function actionCariDiagnosa()
    {
        $request = Yii::$app->request;
        $kunjungan_id = $request->get('kode_diagnosa');

        $prosesKlaim['metadata']['method'] = 'search_diagnosis_inagrouper';
        $prosesKlaim['data']['keyword'] = $kunjungan_id;
        $groupResult = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);

        return $groupResult;
    }

    public function actionImportKoding()
    {
        $request = Yii::$app->request;
        $kunjungan_id = $request->post('kunjungan_id');
        $nosep = $request->post('nosep');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $koding = SyKoreksiDiagnosa::find()->select([
                'sy_koreksidiagnosa.*',
                'diagnosa_m.diagnosa_kode'
            ])->where([
                'kunjungan_id' => $kunjungan_id,
                'is_idrg' => true
            ])
                ->join('INNER JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id = sy_koreksidiagnosa.diagnosa_id')
                ->orderBy(['sy_koreksidiagnosa_id' => SORT_ASC])
                ->asArray()
                ->all();

            if (!empty($koding)) {
                $mappingDiagnosa = [];
                $tmpKoding = [];
                $tempDiagnosa = [];
                $tempProcedure = [];
                foreach ($koding as $key => $value) {
                    $value['is_inacbg'] = true;
                    $value['is_idrg'] = false;
                    $value['created_date'] = date('Y-m-d h:i:s');

                    if (! in_array($value['diagnosa_kode'], $mappingDiagnosa)) {
                        if ($value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI) {
                            $value['multiplicity'] = 1;
                            $kodeProcedure = $value['diagnosa_kode'];
                            // if (isset($value['multiplicity']) && $value['multiplicity'] > 1) {
                            //     $kodeProcedure = $value['diagnosa_kode'].'+'.$value['multiplicity'];
                            // }

                            if (! in_array($kodeProcedure, $tempProcedure)) {
                                $tempProcedure[] = $kodeProcedure;
                            }
                        } else {
                            $tempDiagnosa[] = $value['diagnosa_kode'];
                        }

                        $mappingDiagnosa[] = $value['diagnosa_kode'];
                        unset($value['diagnosa_kode']);
                        $tmpKoding[] = $value;
                    }
                }

                /**
                 * Update koding INACBGS.
                 */
                if (!empty($tmpKoding)) {
                    SyKoreksiDiagnosa::updateAll(['is_deleted' => true], ['kunjungan_id' => $kunjungan_id, 'is_inacbg' => true, 'is_deleted' => false]);

                    SyKoreksiDiagnosa::batchInsert($tmpKoding);

                    if (! empty($tempDiagnosa)) {
                        $implodeData = implode('#', $tempDiagnosa);
                        $addIdrg['metadata'] = [
                            'method' => 'inacbg_diagnosa_set',
                            'nomor_sep' => $nosep,
                        ];

                        $addIdrg['data'] = [
                            'diagnosa' => $implodeData
                        ];

                        $responseDiagnosa = json_decode(DocoHelpers::restInacbgs($addIdrg), true);
                        if (isset($responseDiagnosa['metadata']['code']) && $responseDiagnosa['metadata']['code'] != 200) {
                            $transaction->rollBack();
                            return [
                                'status' => 422,
                                'code' => 422,
                                'title' => $responseDiagnosa['metadata']['error_no'],
                                'message' => $responseDiagnosa['metadata']['message']
                            ];
                        }
                    }

                    if (! empty($tempProcedure)) {
                        $implodeProcedure = implode('#', $tempProcedure);
                    } else {
                        $implodeProcedure = '#';
                    }

                    $addProcedure['metadata'] = [
                        'method' => 'inacbg_procedure_set',
                        'nomor_sep' => $nosep,
                    ];

                    $addProcedure['data'] = [
                        'procedure' => $implodeProcedure
                    ];

                    $responseProcedure = json_decode(DocoHelpers::restInacbgs($addProcedure), true);
                    if (isset($responseProcedure['metadata']['code']) && $responseProcedure['metadata']['code'] != 200) {
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'code' => 422,
                            'message' => $responseProcedure['metadata']['message']
                        ];
                    }

                    SyKunjunganPasien::updateAll(['status_inacbg' => DocoConstants::STATUS_INACBG_KOREKSI], ['kunjungan_id' => $kunjungan_id]);
                    
                    $transaction->commit();
                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil !',
                        'text' => 'Import Koding Berhasil !',
                        'response_diagnosa' => isset($responseDiagnosa) ? $responseDiagnosa : null,
                        'response_procedure' => isset($responseProcedure) ? $responseProcedure : null
                    ];
                }

                $transaction->commit();
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Tidak ada import koding !'
                ];
            }
        } catch (\Exception $th) {
            $transaction->rollBack();
            return [
                'status' => 500,
                'title' => 'Proses Gagal',
                'text' => $th->getMessage()
            ];
        }
    }

    private function getheader()
    {
        $column = [];


        // $value['tanggal_masuk'] = date('d-M-Y H:i:s', strtotime(ArrayHelper::getValue($value, 'tgl_pendaftaran')));
        // $value['tanggal_keluar'] = date('d-M-Y H:i:s', strtotime(ArrayHelper::getValue($value, 'tgl_pulang')));
        // $value['nama_pasien'] = ArrayHelper::getValue($value, 'nama_pasien');
        // $value['no_rm'] = ArrayHelper::getValue($value, 'no_rekammedik');
        // $value['no_registrasi'] = ArrayHelper::getValue($value, 'no_pendaftaran');
        // $value['no_pembayaran'] = ArrayHelper::getValue($value, 'no_pembayaran');
        // $value['instalasi'] = ArrayHelper::getValue($value, 'instalasi_nama');
        // $value['ruangan'] = ArrayHelper::getValue($value, 'ruangan_nama');
        // $value['cara_bayar'] = ArrayHelper::getValue($value, 'carabayar_nama');
        // $value['penjamin'] = ArrayHelper::getValue($value, 'penjamin_nama');
        // $value['dokter_dpjp'] = ArrayHelper::getValue($value, 'dokter_nama');
        // $value['total_tagihan_rs'] = ArrayHelper::getValue($value, 'tarif_rs', 0);
        // $value['total_klaim_bpjs'] = ArrayHelper::getValue($value, 'plafon', 0);
        // $value['status'] = ArrayHelper::getValue($value, 'status_kunjungan');

        $column = [
            [
                'title' => 'No',
                'data' => 'no',
            ],
            [
                'title' => 'Tanggal Masuk',
                'data' => 'tanggal_masuk',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Tanggal Keluar',
                'data' => 'tanggal_keluar',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Nama Pasien',
                'data' => 'nama_pasien',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Nomor Rekam Medik',
                'data' => 'no_rm',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'No Registrasi',
                'data' => 'no_pendaftaran',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'No Pembayaran',
                'data' => 'no_pembayaran',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Instalasi',
                'data' => 'instalasi',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Ruangan',
                'data' => 'ruangan',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Cara Bayar',
                'data' => 'carabayar',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Penjamin',
                'data' => 'penjamin',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Dokter DPJP',
                'data' => 'dokter_dpjp',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Total Tagihan RS',
                'data' => 'total_tagihan_rs',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Total Klaim BPJS',
                'data' => 'total_klaim_bpjs',
                'searchable' => false,
                'visible' => true,
            ],
            [
                'title' => 'Status',
                'data' => 'status',
                'searchable' => false,
                'visible' => true,
            ],
        ];

        return $column;
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function actionExportExcelBgProcess()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $advancedFilter = ArrayHelper::getValue($get, 'advanced-filter');
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $headerExcel = [];
        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        /** set header excel */
        $startPulang = $endPulang = $startMasuk = $endMasuk =  '';

        if (isset($advancedFilter)) {
            if (isset($advancedFilter['tgl_pulang']) && !empty($advancedFilter['tgl_pulang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pulang']);
                if (count($explode) == 2) {
                    $startPulang = date('d-M-Y', strtotime($explode[0]));
                    $endPulang = date('d-M-Y', strtotime($explode[1]));
                }
            }
            if (isset($advancedFilter['tgl_pendaftaran']) && !empty($advancedFilter['tgl_pendaftaran'])) {
                $explode = explode(" - ", $advancedFilter['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $startMasuk = date('d-M-Y', strtotime($explode[0]));
                    $endMasuk = date('d-M-Y', strtotime($explode[1]));
                }
            }
        }

        $noPendaftaranFilter = isset($advancedFilter['no_pendaftaran']) && $advancedFilter['no_pendaftaran'] != '' ? $advancedFilter['no_pendaftaran'] : '-';
        $noRekamMedikFilter = isset($advancedFilter['no_rekamedik']) && $advancedFilter['no_rekamedik'] != '' ? $advancedFilter['no_rekamedik'] : '-';
        $namaPasienFilter = isset($advancedFilter['nama_pasien']) && $advancedFilter['nama_pasien'] != '' ? $advancedFilter['nama_pasien'] : '-';
        $instalasiNamaFilter = isset($advancedFilter['instalasi_pasien']) ? $advancedFilter['instalasi_pasien'] : '-';
        $ruanganNamaFilter = isset($advancedFilter['ruangan_nama']) && $advancedFilter['ruangan_nama'] != '-Semua-' ? $advancedFilter['ruangan_nama'] : '-';
        $statusFilter = isset($advancedFilter['status_kunjungan']) ? $advancedFilter['status_kunjungan'] : '-';
        $caraBayarFilter = isset($advancedFilter['cara_bayar']) ? $advancedFilter['cara_bayar'] : '-';
        $penjaminFilter = isset($advancedFilter['penjamin_kode']) && $advancedFilter['penjamin_kode'] != '-Semua-' ? $advancedFilter['penjamin_kode'] : '-';
        $dokterDpjpFilter = isset($advancedFilter['dokter_nama']) && $advancedFilter['dokter_nama'] != '' ? $advancedFilter['dokter_nama'] : '-';
        $noPembayaranFilter = isset($advancedFilter['no_pembayaran']) && $advancedFilter['no_pembayaran'] != '' ? $advancedFilter['no_pembayaran'] : '-';
        // $carabayarNamaFilter = isset($advancedFilter['carabayar_nama_text']) && !empty($advancedFilter['carabayar_id']) ? $advancedFilter['carabayar_nama_text']:'-';
        // $penjaminNamaFilter = isset($advancedFilter['penjamin_text']) ? str_replace('__',' | ',$advancedFilter['penjamin_text']):'-';


        $headerExcel = [
            "Tanggal Masuk" => $startMasuk . ' - ' . $endMasuk,
            "Tanggal Pulang" => $startPulang . ' - ' . $endPulang,
            "Nama Ruangan" => $ruanganNamaFilter,
            "No Pendaftaran" => $noPendaftaranFilter,
            "Nama Pasien" => $namaPasienFilter,
            "No Rekam Medik" => $noRekamMedikFilter,
            "Instalasi" => $instalasiNamaFilter,
            "Ruangan" => $ruanganNamaFilter,
            "Status" => $statusFilter,
            "Dokter Penanggung Jawab" => $dokterDpjpFilter,
            "Cara Bayar" => $caraBayarFilter,
            "Penjamin" => $penjaminFilter,
            "No Pembayaran" => $noPembayaranFilter,
        ];

        /** end set header excel */
        $header = $this->getheader();
        $query = $this->getDataLaporan();

        // overrride headerExcel from lookup or master id by get first row of queries
        $statuses = [];
        if (isset($advancedFilter['status_kunjungan'])) {
            foreach ($advancedFilter['status_kunjungan'] as $each) {
                $statuses[] = DocoConstants::$status_verif[$each];
            }
        }
        $headerExcel['Status'] = $statuses ? implode(', ', $statuses) : '-';

        $headerExcel['Penjamin'] = '-';
        if (!empty($advancedFilter['penjamin_kode']) && $advancedFilter['penjamin_kode'] != '-Semua-' && !empty($query[0]['penjamin'])) {
            $headerExcel['Penjamin'] = $query[0]['penjamin'];
        }
        // end of override

        $countData = count($query);
        $totalPerPage = count($query);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        $uri_penjamin = Yii::$app->docoRest->getBaseUri('penjaminasuransi');
        $params = [
            'sendToUrl' => 'inf-pasien-ranap-bpjs/drop-file',
            'getDataUrl' => 'inf-pasien-ranap-bpjs/get-data-laporan',
            'base_uri' => $uri_penjamin,
        ];
        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                    'params' => $params
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'title' => 'Informasi Klaim',
                    'headerExcel' => $headerExcel,
                    'footer' => [],
                    'options' => $options,
                    'header' => $header,
                    'customData' => $query
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
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

            $path = "uploads/";
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

    public function getDataLaporan()
    {
        $dataSet = $this->getData();
        $query = isset($dataSet) ? $dataSet : [];
        $data = $query->asArray()->all();
        $result = [];
        $counter = 1;
        foreach ($data as $key => $datum) {
            $status_kunjungan = '';
            switch ($datum['status_kunjungan']) {
                case DocoConstants::STATUS_VERIFIKASI_BPJS_BLM:
                    $status_kunjungan = 'Belum Koreksi';
                    break;
                case DocoConstants::STATUS_VERIFIKASI_BPJS_SDH:
                    $status_kunjungan = 'Sudah Koreksi';
                    break;
                case DocoConstants::STATUS_VERIFIKASI_BPJS_PRS:
                    $status_kunjungan = 'Proses Klaim';
                    break;
                case DocoConstants::STATUS_VERIFIKASI_BPJS_FNL:
                    $status_kunjungan = 'Final Klaim';
                    break;
                default:
                    $status_kunjungan = ' - ';
                    break;
            }
            $value['no'] = $counter;
            $value['tanggal_masuk'] = date('d-M-Y H:i:s', strtotime(ArrayHelper::getValue($datum, 'tgl_pendaftaran')));
            $value['tanggal_keluar'] = date('d-M-Y H:i:s', strtotime(ArrayHelper::getValue($datum, 'tgl_pulang')));
            $value['nama_pasien'] = ArrayHelper::getValue($datum, 'nama_pasien');
            $value['no_rm'] = ArrayHelper::getValue($datum, 'no_rekammedik');
            $value['no_registrasi'] = ArrayHelper::getValue($datum, 'no_pendaftaran');
            $value['no_pembayaran'] = ArrayHelper::getValue($datum, 'no_pembayaran');
            $value['instalasi'] = ArrayHelper::getValue($datum, 'instalasi_nama');
            $value['ruangan'] = ArrayHelper::getValue($datum, 'ruangan_nama');
            $value['cara_bayar'] = ArrayHelper::getValue($datum, 'carabayar_nama');
            $value['penjamin'] = ArrayHelper::getValue($datum, 'penjamin_nama');
            $value['dokter_dpjp'] = ArrayHelper::getValue($datum, 'dokter_nama');
            $value['total_tagihan_rs'] = ArrayHelper::getValue($datum, 'tarif_rs', 0);
            $value['total_klaim_bpjs'] = ArrayHelper::getValue($datum, 'plafon', 0);
            $value['status'] = $status_kunjungan;
            $result[$key] = $value;
            $counter++;
        }
        return $result;
    }

    public function actionGetDataLaporan()
    {
        try {
            return $this->getDataLaporan();
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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath . '/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionGetCekDokumen(){
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new DokumenEklaimView;
            $query = $model::find();
            if(isset($_GET['advanced-filter']['pendaftaranId'])){
                $query->andWhere(['=','pendaftaran_id', $_GET['advanced-filter']['pendaftaranId']]);
                unset($_GET['advanced-filter']['pendaftaran_id']);
            }
            if(isset($_GET['advanced-filter']['nama_dokumen'])){
                $query->andWhere(['=','nama_dokumen', $_GET['advanced-filter']['nama_dokumen']]);
                unset($_GET['advanced-filter']['nama_dokumen']);
            }
            $query->groupBy(['type','pendaftaran_id','nama_dokumen','dokumen_id','status','latest_unduh']);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query
            ]);
        } catch (Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
	}

    public function actionGetListDokumen(){
        $request = Yii::$app->request;

        $model = new DokumenEklaimView;
        $query = $model::find()->where(['pendaftaran_id' => $request->get('id')]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionUnduhDokumenResepKronis()
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $chunk = $request->get('chunk', null);
        $query = (new \yii\db\Query())
            ->select([
                'pm2.no_rekam_medik',
                'pm2.nama_pasien',
                'pt2.pembayaran_id',
                'pt.noresep',
                'pt.penjualanresep_id',
            ])
            ->from('penjualanresep_t pt')
            ->innerJoin('penjamin_m pm', 'pm.penjamin_id = pt.penjamin_id')
            ->innerJoin('pembayaranpelayanan_t pt2', 'pt.penjualanresep_id = pt2.penjualanresep_id')
            ->innerJoin('pasien_m pm2', 'pm2.pasien_id = pt.pasien_id')
            ->leftJoin('dokumenresepkronis_r dr', 'dr.penjualanresep_id = pt.penjualanresep_id')
            ->where([
                'and',
                'resep_kronis_asal_id IS NOT NULL',
                ['pm.carabayar_id' => 6],
                [
                    'between',
                    new \yii\db\Expression('DATE(tglresep)'),
                    $start_date,
                    $end_date
                ],
                ['pt.is_deleted' => false],
                [
                    'or',
                    ['dr.is_sent' => false],
                    ['dr.is_sent' => null],
                ]
            ])
            ->orderBy('pt.penjualanresep_id', 'ASC')
            ->limit(150);

        foreach ($query->batch($chunk) as $rows) {
            (new RabbitBgProcess())->send([
                'data' => $rows
            ], 'dokumen_resep_kronis', 'sync_dokumen_resep_kronis');
        }
        return true;
    }

    public function actionKoreksiDiagnosa()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjungan_id');
            $idrg = $request->post('idrg');

            $tempDiagnosa = [];
            $tempProcedure = [];
            foreach ($idrg as $value) {
                if ($value['is_diagnosa_idrg']) {
                    $tempDiagnosa[] = $value['kode'];
                } else {
                    $tempProcedure[] = $value['kode'];
                }
            }

            $implodeData = implode('#', $tempDiagnosa);
            $implodeProcedure = [];

            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'kunjungan_id',
                    'status_kunjungan',
                    'nosep'
                ])
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->asArray()->one();


            if (empty($dataKunjungan)) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $tmpData = [];
            if (is_array($idrg) && count($idrg) > 0) {
                foreach ($idrg as $key => $value) {
                    if ($value['is_diagnosa_idrg']) {
                        $kelompokdiagnosa = $value['isPrimer'] ? DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA : DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
                    } else {
                        $kelompokdiagnosa = DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
                        $kodeProcedure = $value['kode'];
                        if (isset($value['multiplicity']) && $value['multiplicity'] > 1) {
                            $kodeProcedure = $value['kode'].'+'.$value['multiplicity'];
                        }

                        $implodeProcedure[] = $kodeProcedure;
                    }

                    $item = [
                        'kunjungan_id' => (int) $kunjunganId,
                        'tgl_koreksidiagnosa' => date('Y-m-d H:i:s'),
                        'kelompokdiagnosa_id' => (int) $kelompokdiagnosa,
                        'diagnosa_id' => (int) $value['id'],
                        'is_icdprimer' => ArrayHelper::getValue($value, 'isPrimer'),
                        'is_diagnosa_baru' => true,
                        'multiplicity' => isset($value['multiplicity']) ? $value['multiplicity'] : 1,
                        'is_idrg' => true
                    ];

                    array_push($tmpData, $item);
                }    
            }

            /**
             * Delete koreksi diagnosa
             */
            SyKoreksiDiagnosa::updateAll(['is_deleted' => true], ['kunjungan_id' => $kunjunganId, 'is_idrg' => true, 'is_deleted' => false]);

            SyKoreksiDiagnosa::batchInsert($tmpData, false);

            $addIdrg['metadata'] = [
                'method' => 'idrg_diagnosa_set',
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];

            $addIdrg['data'] = [
                'diagnosa' => $implodeData
            ];
            
            $responseSet = json_decode(DocoHelpers::restInacbgs($addIdrg), true);
            if (isset($responseSet['metadata']['code']) && $responseSet['metadata']['code'] != 200) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'code' => 422,
                    'title' => $responseSet['metadata']['error_no'],
                    'message' => $responseSet['metadata']['message']
                ];
            }

            $addProcedure['metadata'] = [
                'method' => 'idrg_procedure_set',
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];

            $implodeProcedure = implode('#', $implodeProcedure);
            $addProcedure['data'] = [
                'procedure' => $implodeProcedure == "" ? '#' : $implodeProcedure
            ];
            
            $responseSet = json_decode(DocoHelpers::restInacbgs($addProcedure), true);
            if (isset($responseSet['metadata']['code']) && $responseSet['metadata']['code'] != 200) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $responseSet['metadata']['message']
                ];
            }

            $groupingIdrg['metadata'] = [
                "method" => 'grouper',
                "stage" => "1",
                "grouper" => "idrg"
            ];

            $groupingIdrg['data'] = [
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];

            $responseGrouper = json_decode(DocoHelpers::restInacbgs($groupingIdrg), true);
            if (isset($responseGrouper['metadata']['code']) && $responseGrouper['metadata']['code'] != 200) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $responseSet['metadata']['message']
                ];
            }

            $updateKunjungan = SyKunjunganPasien::find()
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->one();

            $updateKunjungan->status_kunjungan = DocoConstants::STATUS_SUDAH_KOREKSI;
            $updateKunjungan->status_idrg = DocoConstants::STATUS_KOREKSI_IDRG;
            $updateKunjungan->save();

            $transaction->commit();
            return [
                'status' => 200,
                'code' => 200,
                'message' => 'Koreksi Diagnosa Berhasil',
                'response' => $responseGrouper
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionGetClaimData()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'kunjungan_id',
                    'status_kunjungan',
                    'nosep',
                    'status_inacbg',
                    'status_idrg'
                ])
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->asArray()->one();

            if (empty($dataKunjungan)) {
                return [
                    'status' => 404,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'get_claim_data',
            ];

            $payload['data'] = [
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);
            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            $klaimInacbg = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjunganId])->asArray()->one();
            if (isset($klaimInacbg['spesial_cmg_option'])) {
                $response['response']['data']['grouper']['response']['option_special_cmg'] = json_decode($klaimInacbg['spesial_cmg_option'], true);              
            }

            $response['response']['status_inacbg'] = ArrayHelper::getValue($dataKunjungan, 'status_inacbg');
            $response['response']['status_idrg'] = ArrayHelper::getValue($dataKunjungan, 'status_idrg');

            return [
                'status' => 200,
                'message' => $response['metadata']['message'],
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionGetIdrgData()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'kunjungan_id',
                    'status_kunjungan',
                    'nosep'
                ])
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->asArray()->one();

            if (empty($dataKunjungan)) {
                return [
                    'status' => 404,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'idrg_diagnosa_get',
            ];

            $payload['data'] = [
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);

            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'status' => 500,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            return [
                'status' => 200,
                'message' => $response['metadata']['message'],
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    /**
     * Final IDRG
     *
     * Mengubah status IDRG menjadi final
     *
     * @return array response
     */
    public function actionFinalIdrg()
    {
        try {          
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dataKunjungan = SyKunjunganPasien::find()
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->one();

            if (empty($dataKunjungan)) {
                return [
                    'code' => 422,
                    'status' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'idrg_grouper_final',
            ];

            $payload['data'] = [
                'nomor_sep' => $dataKunjungan->nosep,
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);

            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            $dataKunjungan->status_idrg = DocoConstants::STATUS_FINAL_IDRG;
            $dataKunjungan->save();

            return [
                'status' => 200,
                'code' => 200,
                'message' => 'Final IDRG Berhasil',
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    /**
     * Edit IDRG
     *
     * Mengedit data IDRG yang sebelumnya telah di grup
     *
     * @return array response
     */
    public function actionEditIdrg()
    {
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dataKunjungan = SyKunjunganPasien::find()
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->one();

            if (empty($dataKunjungan)) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'idrg_grouper_reedit',
            ];

            $payload['data'] = [
                'nomor_sep' => $dataKunjungan->nosep,
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);

            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            $dataKunjungan->status_idrg = DocoConstants::STATUS_KOREKSI_IDRG;
            $dataKunjungan->save();

            return [
                'status' => 200,
                'code' => 200,
                'message' => $response['metadata']['message'],
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }
    

    /**
     * Add diagnosa inacbg & Grouping Diagnosa.
     *
     * @return json
     *
     * @throws \Throwable
     */
    public function actionAddDiagnosaInacbgs()
     {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $kunjunganId = $request->post('kunjungan_id');
            $diagnosa = $request->post('diagnosa');
            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'kunjungan_id',
                    'status_kunjungan',
                    'nosep'
                ])
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->asArray()->one();

            if (empty($dataKunjungan)) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $tempDiagnosa = [];
            $tempProcedure = [];
            foreach ($diagnosa as $value) {
                if ($value['is_diagnosa']) {
                    $tempDiagnosa[] = $value['kode'];
                } else {
                    $tempProcedure[] = $value['kode'];
                }
            }

            $implodeData = implode('#', $tempDiagnosa);
            $implodeProcedure = [];

            $tmpData = [];
            if (is_array($diagnosa) && count($diagnosa) > 0) {
                foreach ($diagnosa as $key => $value) {
                    if ($value['is_diagnosa']) {
                        $kelompokdiagnosa = $value['isPrimer'] ? DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA : DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
                    } else {
                        $kelompokdiagnosa = DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
                        $kodeProcedure = $value['kode'];
                        if (isset($value['multiplicity']) && $value['multiplicity'] > 1) {
                            $kodeProcedure = $value['kode'].'+'.$value['multiplicity'];
                        }

                        $implodeProcedure[] = $kodeProcedure;
                    }

                    $item = [
                        'kunjungan_id' => (int) $kunjunganId,
                        'tgl_koreksidiagnosa' => date('Y-m-d H:i:s'),
                        'kelompokdiagnosa_id' => (int) $kelompokdiagnosa,
                        'diagnosa_id' => (int) $value['id'],
                        'is_icdprimer' => ArrayHelper::getValue($value, 'isPrimer'),
                        'is_diagnosa_baru' => true,
                        'multiplicity' => isset($value['multiplicity']) ? $value['multiplicity'] : 1,
                        'is_inacbg' => true
                    ];

                    array_push($tmpData, $item);
                }    
            }
            
            /**
             * Delete koreksi diagnosa
             */
            SyKoreksiDiagnosa::updateAll([
                'is_deleted' => true,
            ],[
                'kunjungan_id' => $kunjunganId,
                'is_inacbg' => true,
                'is_deleted' => false
            ]);

            SyKoreksiDiagnosa::batchInsert($tmpData, false);

            SyKunjunganPasien::updateAll(['status_inacbg' => DocoConstants::STATUS_INACBG_GROUPING], ['kunjungan_id' => $kunjunganId]);
            /**
             * Set Diagnosa & Procedure INACBGS.
             */
            $addIdrg['metadata'] = [
                'method' => 'inacbg_diagnosa_set',
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];

            $addIdrg['data'] = [
                'diagnosa' => $implodeData
            ];
            
            $responseDiagnosa = json_decode(DocoHelpers::restInacbgs($addIdrg), true);
            if (isset($responseDiagnosa['metadata']['code']) && $responseDiagnosa['metadata']['code'] != 200) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'code' => 422,
                    'title' => $responseDiagnosa['metadata']['error_no'],
                    'message' => $responseDiagnosa['metadata']['message']
                ];
            }

            $addProcedure['metadata'] = [
                'method' => 'inacbg_procedure_set',
                'nomor_sep' => ArrayHelper::getValue($dataKunjungan, 'nosep'),
            ];
            
            if (!empty($implodeProcedure)) {
                $implodeProcedure = implode('#', $implodeProcedure);
            } else {
                $implodeProcedure = '#';
            }

            $addProcedure['data'] = [
                'procedure' => $implodeProcedure
            ];

            $responseSet = json_decode(DocoHelpers::restInacbgs($addProcedure), true);
            if (isset($responseSet['metadata']['code']) && $responseSet['metadata']['code'] != 200) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $responseSet['metadata']['message']
                ];
            }

            /**
             * End Set Diagnosa & Procedure INACBGS.
             */


            /**
             * Start Process Grouping INACBGS.
             */
            $grouping['metadata']['method'] = 'grouper';
            $grouping['metadata']['stage'] = '1';
            $grouping['metadata']['grouper'] = 'inacbg';
            $grouping['data']['nomor_sep'] = ArrayHelper::getValue($dataKunjungan, 'nosep');
            $groupResult = json_decode(DocoHelpers::restInacbgs($grouping), true);
            if ($groupResult['metadata']['code'] != 200) {
                 return [
                    'status' => 422,
                    'code' => 422,
                    'title' => $groupResult['metadata']['error_no'],
                    'message' => $groupResult['metadata']['message'],
                    'data' => $groupResult
                ];
            }

            $klaimInacbg = SyKlaimInacbg::findOne(['kunjungan_id' => $kunjunganId]);
            if (empty($klaimInacbg)) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'title' => 'Proses Gagal !',
                    'message' => 'Klaim INACBGS Tidak Ditemukan',
                ];
            }

            $klaimInacbg->spesial_cmg_option = isset($groupResult['special_cmg_option']) ? json_encode($groupResult['special_cmg_option']): null;
            $klaimInacbg->save();
            
            $transaction->commit();
            return [
                'status' => 200,
                'code' => 200,
                'message' => 'Grouping diagnosa berhasil !',
                'response' => $groupResult,
                'response_diagnosa' => $responseDiagnosa,
                'response_procedure' => isset($responseSet) ? $responseSet : null
            ];
        } catch (\Throwable $th) {
            $transaction->rollBack();
            $this->logError($th);
            return [
                'status' => 500,
                'code' => 500,
                'message' => $th->getMessage(),
            ];
        }
    }

    /**
     * Final INACBGS.
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function actionFinalInacbgs()
    {
        try {          
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dataKunjungan = SyKunjunganPasien::find()
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->one();

            if (empty($dataKunjungan)) {
                return [
                    'code' => 422,
                    'status' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'inacbg_grouper_final',
            ];

            $payload['data'] = [
                'nomor_sep' => $dataKunjungan->nosep,
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);
            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            $dataKunjungan->status_inacbg = DocoConstants::STATUS_INACBG_FINAL;
            $dataKunjungan->status_kunjungan = DocoConstants::STATUS_PROSES_KLAIM;
            $dataKunjungan->save();

            return [
                'status' => 200,
                'code' => 200,
                'message' => 'Final INACBGS Berhasil !',
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionEditInacbgs()
    {
        try {          
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dataKunjungan = SyKunjunganPasien::find()
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->one();

            if (empty($dataKunjungan)) {
                return [
                    'code' => 422,
                    'status' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'inacbg_grouper_reedit',
            ];

            $payload['data'] = [
                'nomor_sep' => $dataKunjungan->nosep
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);

            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            $dataKunjungan->status_kunjungan = DocoConstants::STATUS_SUDAH_KOREKSI;
            $dataKunjungan->save();

            return [
                'status' => 200,
                'code' => 200,
                'message' => 'Edit ulang INACBGS Berhasil !',
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionKoreksiTransfusiDarah()
    {
        try {
            /**
             * Re - Edit Final iDRG
             */
            $request = Yii::$app->request;
            $kunjunganId = $request->get('kunjungan_id');
            $dializer = $request->get('dializer');
            $kantongDarah = $request->get('kantong_darah');
            $dataKunjungan = SyKunjunganPasien::find()
                ->select([
                    'kunjungan_id',
                    'status_kunjungan',
                    'nosep'
                ])
                ->where([
                    'kunjungan_id' => $kunjunganId
                ])->one();

            if (empty($dataKunjungan)) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => 'Kunjungan Tidak Ditemukan'
                ];
            }

            $payload['metadata'] = [
                'method' => 'idrg_grouper_reedit',
            ];

            $payload['data'] = [
                'nomor_sep' => $dataKunjungan->nosep,
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);
            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                if (isset($response['metadata']['error_no']) != 'E2103') {
                    return [
                        'status' => 422,
                        'code' => 422,
                        'message' => $response['metadata']['message'],
                        'data' => $response
                    ];
                }
            }
            
            /**
             * Set Claim Data
             */
            $prosesKlaim['metadata']['method'] = 'set_claim_data';
            $prosesKlaim['metadata']['nomor_sep'] = $dataKunjungan->nosep;
            $prosesKlaim['data'] = [
                'dializer_single_use' => $dializer,
                'kantong_darah' => $kantongDarah,
                'payor_id' => $this->getPayorId(),
                'payor_cd' => $this->getPayorCd(),
                'coder_nik' => $this->getCoderNik(),
            ];

            $klaim = json_decode(DocoHelpers::restInacbgs($prosesKlaim), true);
            if ($klaim['metadata']['code'] != 200) {
                return [
                    'status' => $klaim['metadata']['code'],
                    'code' => $klaim['metadata']['code'],
                    'title' => $klaim['metadata']['error_no'],
                    'text' => $klaim['metadata']['message']
                ];
            }

            /**
             * Set Final iDRG
             */
            $payload['metadata'] = [
                'method' => 'idrg_grouper_final',
            ];

            $payload['data'] = [
                'nomor_sep' => $dataKunjungan->nosep,
            ];

            $response = json_decode(DocoHelpers::restInacbgs($payload), true);
            if (isset($response['metadata']['code']) && $response['metadata']['code'] != 200) {
                return [
                    'status' => 422,
                    'code' => 422,
                    'message' => $response['metadata']['message'],
                    'data' => $response
                ];
            }

            /**
             * Grouping ulang
             */
            $grouping['metadata']['method'] = 'grouper';
            $grouping['metadata']['stage'] = '1';
            $grouping['metadata']['grouper'] = 'inacbg';
            $grouping['data']['nomor_sep'] = $dataKunjungan->nosep;
            $groupResult = json_decode(DocoHelpers::restInacbgs($grouping), true);
            if ($groupResult['metadata']['code'] != 200) {
                 return [
                    'status' => 422,
                    'code' => 422,
                    'title' => $groupResult['metadata']['error_no'],
                    'message' => $groupResult['metadata']['message'],
                    'data' => $groupResult
                ];
            }

            $modelKlaim = SyKlaimInacbg::find()->where(['kunjungan_id' => $kunjunganId, 'is_deleted' => false])->one();
            $modelKlaim->dializer = $dializer;
            $modelKlaim->transfusi_darah = $kantongDarah;
            $modelKlaim->save();
            
            return [
                'status' => 200,
                'code' => 200,
                'message' => $response['metadata']['message'],
                'data' => $response
            ];
        } catch (\Throwable $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionValidasiDiagnosa()
    {
        $request = Yii::$app->request;
        $kunjungan_id = $request->post('kunjungan_id');
        $nosep = $request->post('nosep');
        try {
            $koding = SyKoreksiDiagnosa::find()->select([
                'sy_koreksidiagnosa.*',
                'diagnosa_m.diagnosa_kode'
            ])->where([
                'kunjungan_id' => $kunjungan_id,
                'is_inacbg' => true
            ])
                ->join('INNER JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id = sy_koreksidiagnosa.diagnosa_id')
                ->orderBy(['sy_koreksidiagnosa_id' => SORT_ASC])
                ->asArray()
                ->all();

            if (!empty($koding)) {
                $tmpKoding = [];
                $tempDiagnosa = [];
                $tempProcedure = [];
                foreach ($koding as $key => $value) {
                    if ($value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI) {
                        $kodeProcedure = $value['diagnosa_kode'];
                        if (isset($value['multiplicity']) && $value['multiplicity'] > 1) {
                            $kodeProcedure = $value['diagnosa_kode'].'+'.$value['multiplicity'];
                        }

                        $tempProcedure[] = $kodeProcedure;
                    } else {
                        $tempDiagnosa[] = $value['diagnosa_kode'];
                    }

                    unset($value['diagnosa_kode']);
                    $tmpKoding[] = $value;
                }

                /**
                 * Validate Diagnosa.
                 */
                if (!empty($tmpKoding)) {

                    if (! empty($tempDiagnosa)) {
                        $implodeData = implode('#', $tempDiagnosa);
                        $addIdrg['metadata'] = [
                            'method' => 'inacbg_diagnosa_set',
                            'nomor_sep' => $nosep,
                        ];

                        $addIdrg['data'] = [
                            'diagnosa' => $implodeData
                        ];

                        $responseDiagnosa = json_decode(DocoHelpers::restInacbgs($addIdrg), true);
                        if (isset($responseDiagnosa['metadata']['code']) && $responseDiagnosa['metadata']['code'] != 200) {
                            return [
                                'status' => 422,
                                'code' => 422,
                                'title' => $responseDiagnosa['metadata']['error_no'],
                                'message' => $responseDiagnosa['metadata']['message']
                            ];
                        }
                    }

                    if (! empty($tempProcedure)) {
                        $implodeProcedure = implode('#', $tempProcedure);
                        $addProcedure['metadata'] = [
                            'method' => 'inacbg_procedure_set',
                            'nomor_sep' => $nosep,
                        ];

                        $addProcedure['data'] = [
                            'procedure' => $implodeProcedure
                        ];

                        $responseProcedure = json_decode(DocoHelpers::restInacbgs($addProcedure), true);
                        if (isset($responseProcedure['metadata']['code']) && $responseProcedure['metadata']['code'] != 200) {
                            return [
                                'status' => 422,
                                'code' => 422,
                                'message' => $responseProcedure['metadata']['message']
                            ];
                        }
                    }

                    return [
                        'status' => 200,
                        'title' => 'Proses Berhasil !',
                        'text' => 'Validate Koding Berhasil !',
                        'response_diagnosa' => isset($responseDiagnosa) ? $responseDiagnosa : null,
                        'response_procedure' => isset($responseProcedure) ? $responseProcedure : null
                    ];
                }

                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Tidak ada validate koding !'
                ];
            }
        } catch (\Exception $th) {
            return [
                'status' => 500,
                'title' => 'Proses Gagal',
                'text' => $th->getMessage()
            ];
        }
    }
}
