<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Rujukan;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\InfKunjunganRsView;
use app\modules\v1\models\InfoKunjunganRiView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\InfoJadwalDokterView;
use Doco\components\DocoMessages;
use yii\base\DynamicModel;
use app\modules\v1\services\Contracts\BpjsInterface;
use app\modules\v1\models\BpjsAplicare;
use Doco\models\bpjs\BpjsRujukanKhususT;

class AllowBpjsController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    const CONS_ID_KTP = 94;

    public $modelClass = '';
    const PELAYANAN_RAWAT_INAP = 1;
    const STRING_IGD = 'IGD';
    protected $service;

    public function __construct($id, $module, $config = [],BpjsInterface $service)
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["delete-sep-internal"] = ["POST"];
        $verbs["cari-data-rencana-kontrol-dan-spri"] = ["GET"];
        $verbs["hapus-data-rencana-kontrol-dan-spri"] = ["DELETE"];
        $verbs["get-pengajuan-sep"] = ["GET"];
        $verbs["create-pengajuan-sep"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    // allow all method without authentication
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    /**
    * @author Rizal
    * @since 2018-04-25 10:11:55
    * @desc ALL ABOUT BPJS BRIDGING
    */

    public function actionPeserta()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new Bpjs;
        $noKartu = isset($post['nokartu']) ? trim($post['nokartu']) : null;
        $tglSEP = isset($post['tglSEP']) ? $post['tglSEP'] : null;
        $isktp = isset($post['isktp']) ? $post['isktp'] : null;
        $result = [];
        $post_ranap = false;
        $lastRujukan = false;
        $peserta = $model->peserta($noKartu, $tglSEP, $isktp);
        if(!isset($peserta['metaData']) || $peserta['metaData']['code'] != 200) {
            return $peserta;
        }
        // untuk kebutuhan pencarian post ranap
        if($isktp) {
            $noBpjs = isset($peserta['response']['peserta']['noKartu']) ? $peserta['response']['peserta']['noKartu'] : $post['nokartu'];
            $history = $this->service->historyPelayanan($noBpjs);
        } else {
            $history = $this->service->historyPelayanan($noKartu);
        }
        $response_history = isset($history['response']) ? $history['response'] : null;

        $ppkPelayanan = Cache::getPpkPelayanan();

        if(isset($response_history['histori'])) {
            $history = $response_history['histori'];
            $lastHistory = $history[0];

            $ppkPelayanan_nama = $ppkPelayanan['nama'];
            list($namaPpk, $kotaPpk) = explode(" - ", $ppkPelayanan_nama);

            $post_ranap_sep = "";
            $post_ranap_ppk_pelayanan_name = "";
            $post_ranap_ppk_pelayanan_kode = $ppkPelayanan['kode'];

            if (!empty($lastHistory)) {
                if ($lastHistory['ppkPelayanan'] == $namaPpk && $lastHistory['jnsPelayanan'] == 1) {
                    $post_ranap = true;
                    $post_ranap_sep = $lastHistory['noSep'];
                    $post_ranap_ppk_pelayanan_name = $lastHistory['ppkPelayanan'];
                }
            }

            // foreach ($response_history['histori'] as $key => $value) {
            //     if ($value['ppkPelayanan'] == $namaPpk && !empty($value['noRujukan'])) {
            //         $post_ranap = true;
            //         $post_ranap_sep = $value['noSep'];
            //         $post_ranap_ppk_pelayanan_name = $value['ppkPelayanan'];
            //         break;
            //     }
            // }

            if($post_ranap) {
                $result = [
                    'noSep' => $post_ranap_sep,
                    'ppkPelayanan_nama' => $post_ranap_ppk_pelayanan_name,
                    'ppkPelayanan_kode' => $post_ranap_ppk_pelayanan_kode,
                ];
            }

        }

        // if(isset($response_history['histori'])) {
        //     $histori_terakhir = $response_history['histori'][0];
        //     $jenis_pelayanan = $histori_terakhir['jnsPelayanan'];
        //     $asal_rs = $histori_terakhir['ppkPelayanan'];
        //     $ppkPelayanan = Cache::getPpkPelayanan();
        //     $ppkPelayanan_nama = $ppkPelayanan['nama'];
        //     list($namaPpk, $kotaPpk) = explode(" - ", $ppkPelayanan_nama);

        //     if(!empty($namaPpk)) {
        //         if($asal_rs == $namaPpk && $jenis_pelayanan == 1) {
        //             $post_ranap = true;
        //         }
        //     }

        //     if($post_ranap) {
        //         $result = [
        //             'noSep' => $histori_terakhir['noSep'],
        //             'ppkPelayanan_nama' => $histori_terakhir['ppkPelayanan'],
        //             'ppkPelayanan_kode' => $ppkPelayanan['kode'],
        //         ];
        //     }
        // }

        $peserta['response']['pasien_baru'] = true;
        $peserta['response']['pasien_sesuai'] = false;
        $peserta['response']['nama_pasien'] = null;
        $peserta['response']['post_ranap'] = $post_ranap;
        if($post_ranap) {
            $peserta['response']['data_post_ranap'] = $result;
        } else {
            $asalRujukan = $request->post('asal_rujukan', null);
            // find last rujukan
            $getRujukan = $this->service->cariRujukanByNoKartu($peserta['response']['peserta']['noKartu'], $asalRujukan);
            if(isset($getRujukan['metaData']) && $getRujukan['metaData']['code'] == 200) {
                $lastRujukan = [
                    'noRujukan' => ArrayHelper::getValue($getRujukan, 'response.rujukan.noKunjungan'),
                    'tglRujukan' => ArrayHelper::getValue($getRujukan, 'response.rujukan.tglKunjungan'),
                    'ppk' => ArrayHelper::getValue($getRujukan, 'response.rujukan.provPerujuk.kode'),
                    'ppkNama' => ArrayHelper::getValue($getRujukan, 'response.rujukan.provPerujuk.nama'),
                    'asalFaskes' => ArrayHelper::getValue($getRujukan, 'response.asalFaskes'),
                    'diagnosa' => ArrayHelper::getValue($getRujukan, 'response.rujukan.diagnosa.kode'),
                    'diagnosaNama' => ArrayHelper::getValue($getRujukan, 'response.rujukan.diagnosa.nama'),
                    'poli' => ArrayHelper::getValue($getRujukan, 'response.rujukan.poliRujukan.kode'),
                    'poliNama' => ArrayHelper::getValue($getRujukan, 'response.rujukan.poliRujukan.nama'),
                ];
            }
        }

        $getPasien = [];
        if(isset($peserta['response']['peserta'])) {
            if (isset($peserta['response']['peserta']['mr'])) {
                $getPasien = $this->service->cariPasienBpjsByRm($peserta['response']['peserta']);

                if(empty($getPasien) && isset($peserta['response']['peserta']['noKartu'])) {
                    $getPasien = $this->service->cariPasienBpjsByNokartu($peserta['response']['peserta']);
                }

                if(empty($getPasien) && isset($peserta['response']['peserta']['nik'])) {
                    $getPasien = $this->service->cariPasienBpjsByNik($peserta['response']['peserta']);
                }

                if(empty($getPasien)) {
                    $getPasien = $this->service->cariPasienBpjsByNama($peserta['response']['peserta']);
                }
            } else {
                if(empty($getPasien) && isset($peserta['response']['peserta']['noKartu'])) {
                    $getPasien = $this->service->cariPasienBpjsByNokartu($peserta['response']['peserta']);
                }

                if(empty($getPasien) && isset($peserta['response']['peserta']['nik'])) {
                    $getPasien = $this->service->cariPasienBpjsByNik($peserta['response']['peserta']);
                }

                if(empty($getPasien)) {
                    $getPasien = $this->service->cariPasienBpjsByNama($peserta['response']['peserta']);
                }
            }
        }

        if(!empty($getPasien)) {
            $peserta['response']['nama_pasien'] = $getPasien['nama_pasien'];
            $peserta['response']['no_rekam_medik'] = $getPasien['no_rekam_medik'];
            $peserta['response']['pasien_baru'] = false;
            $peserta['response']['pasien_sesuai'] = true;
        }

        // if (isset($peserta['response']['peserta']['mr'])) {
        //     $resMr = $peserta['response']['peserta']['mr'];
        //     $namaPeserta = isset($peserta['response']['peserta']['nama']) ? $peserta['response']['peserta']['nama'] : null;
        //     if (!empty($resMr['noMR'])) {
        //         $noRm = $resMr['noMR'];
        //         $qPasien = Pasien::find()->select([
        //             'nama_pasien'
        //         ])->where([
        //             'no_rekam_medik' => $noRm
        //         ])->asArray()->one();
        //         if (!empty($qPasien)) {
        //             $peserta['response']['pasien_baru'] = false;
        //             $namaPasien = isset($qPasien['nama_pasien']) ? $qPasien['nama_pasien'] : null;
        //             $peserta['response']['nama_pasien'] = $namaPasien;
        //             if (strtoupper($namaPeserta) === strtoupper($namaPasien)) {
        //                 $peserta['response']['pasien_sesuai'] = true;
        //             }
        //         }
        //     }
        // }

        $peserta['response']['ppkPelayanan'] = $ppkPelayanan;
        $peserta['response']['lastRujukan'] = $lastRujukan;

        return $peserta;
    }

    public function actionRujukan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new Bpjs;
        $rujukan = $model->rujukan($post['nomor'], $post['asal_rujukan']);
        $post_ranap = false;
        if ($rujukan) {
            if ($rujukan['metaData']['code'] != 200) {
                if($rujukan['metaData']['code'] == 201) { // error code 201 ketika tidak ketemu
                    $rujukan = $this->getRujukanPostRanap($post, $model);
                }
                return $rujukan;
            }
            $rujukan['response']['pasien_baru'] = true;
            $rujukan['response']['pasien_sesuai'] = false;
            $rujukan['response']['nama_pasien'] = null;
            $rujukan['response']['lastPoli'] = false;
            $rujukan['response']['dokterDpjp'] = false;
            $no_peserta = $rujukan['response']['rujukan']['peserta']['noKartu'];


            if (isset($rujukan['response']['rujukan']['peserta'])) {
                $rujukan['response']['peserta'] = $rujukan['response']['rujukan']['peserta'];
                if(isset($rujukan['response']['peserta']['mr'])) {
                    $getPasien = $this->service->cariPasienBpjsByRm($rujukan['response']['peserta']);
                } else {
                    $getPasien = $this->service->cariPasienBpjsByNama($rujukan['response']['peserta']);
                }
                if(!empty($getPasien)) {
                    $rujukan['response']['nama_pasien'] = $getPasien['nama_pasien'];
                    $rujukan['response']['pasien_baru'] = false;
                    $rujukan['response']['pasien_sesuai'] = true;
                }
                unset($rujukan['response']['rujukan']['peserta']);
            }

            $rencana_kontrol = $this->service->cariDataRencanaKontrol(date('m'), date('Y'), $no_peserta, 2);
            if (isset($rencana_kontrol['response']['list']) && !empty($rencana_kontrol['response']['list'])) {
                $rencana_kontrol = array_values(array_filter($rencana_kontrol['response']['list'], function($val) {
                        return $val['tglRencanaKontrol'] == date('Y-m-d') && $val['jnsKontrol'] == DocoConstants::RK_JNS_KONTROL_RK;
                }));
            } else {
                $rencana_kontrol = [];
            }
            $rujukan['response']['rujukan']['rencana_kontrol'] = $rencana_kontrol;

            $rujukan['response']['rujukan']['rujukan_khusus'] = $this->getRujukanKhususByNomor($post['nomor']);

            // if (isset($rujukan['response']['peserta']['mr'])) {
            //     $resMr = $rujukan['response']['peserta']['mr'];
            //     $namaPeserta = isset($rujukan['response']['peserta']['nama']) ? $rujukan['response']['peserta']['nama'] : null;
            //     if (!empty($resMr['noMR'])) {
            //         $noRm = $resMr['noMR'];
            //         $qPasien = Pasien::find()->select([
            //             'nama_pasien'
            //         ])->where([
            //             'no_rekam_medik' => $noRm
            //         ])->asArray()->one();
            //         if (!empty($qPasien)) {
            //             $rujukan['response']['pasien_baru'] = false;
            //             $namaPasien = isset($qPasien['nama_pasien']) ? $qPasien['nama_pasien'] : null;
            //             $rujukan['response']['nama_pasien'] = $namaPasien;
            //             if (strtoupper($namaPeserta) === strtoupper($namaPasien)) {
            //                 $rujukan['response']['pasien_sesuai'] = true;
            //             }
            //         }
            //     }
            // }

            // get history pelayanan dan list dokter dpjp berdasarkan history pelayanan terakhir
            // $historiPelayanan = $this->service->historyPelayanan($no_peserta);
            // dirubah karena bpjs hanya mengeluarkan maksimal 10 data
            $historiPelayanan = (new Bpjs)->historyPelayananPasien($no_peserta);
            $jumlahSEP = $model->jumlahSEP([
                'jenisRujukan' => $post['asal_rujukan'],
                'noRujukan' => $post['nomor'],
            ]);
            $jumlahSEP = isset($jumlahSEP['response']['jumlahSEP']) ? intval($jumlahSEP['response']['jumlahSEP']) : 0;
            $rujukan['response']['lastSep'] = $historiPelayanan;

            // get dokter DPJP untuk case rujukan ke-sekian, ambil dari last SEP yang sesuai dengan nomor rujukan
            $no_sep_histori = '';
            if (isset($historiPelayanan['response']) && $historiPelayanan['response']['histori']) {
                foreach ($historiPelayanan['response']['histori'] as $hist) {
                    if (strtoupper($hist['noRujukan']) == strtoupper($post['nomor'])) {
                        $poliKode = $rujukan['response']['rujukan']['poliRujukan']['kode'];
                        $rujukan['response']['lastPoli'] = $poliKode;
                    }
                }

                $ppkPelayanan = Cache::getPpkPelayanan();
                $ppkPelayanan_nama = $ppkPelayanan['nama'];
                $rujukan['response']['ppkPelayananRs_nama'] = explode(' - ', $ppkPelayanan_nama)[0];

                $post_ranap_sep = "";
                $post_ranap_ppk_pelayanan_name = "";
                $post_ranap_ppk_pelayanan_kode = $ppkPelayanan['kode'];

                foreach ($historiPelayanan['response']['histori'] as $key => $value) {
                    // untuk post ranap harus jenis pelayana = 1 (RANAP) && ppk pelayanan = nama ppk rumah sakit tersebut
                    if($ppkPelayanan_nama == $value['ppkPelayanan'] && $value['jnsPelayanan'] == '1') {
                        $post_ranap = true;
                        $post_ranap_sep = $value['noSep'];
                        $post_ranap_ppk_pelayanan_name = $value['ppkPelayanan'];
                        break;
                    }
                }

                if($post_ranap && $jumlahSEP > 0) {
                    $rujukan['response']['post_ranap'] = [
                        'asal_rujukan' => $rujukan['response']['asalFaskes'],
                        'noSep' => $post_ranap_sep,
                        'ppkPelayanan_nama' => $post_ranap_ppk_pelayanan_name,
                        'ppkPelayanan_kode' => $post_ranap_ppk_pelayanan_kode,
                    ];
                }
            }
        } else {
            $rujukan = $this->getRujukanPostRanap($post, $model);
        }

        return $rujukan;
    }

    public function actionReferensiPoli($q=null)
    {
        try {
            $request = Yii::$app->request;
            if ($q === '') {
                return [];
            }
            if ($q) {
                $param = $q;
            } else {
                $param = $request->post()['q'];
            }
            $model = new Bpjs;
            $result = $model->referensiPoli($param);
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

    public function actionReferensiDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $param = $request->post('q');
            $model = new Bpjs;
            $result = $model->referensiDiagnosa($param);
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

    public function actionReferensiFaskes()
    {
        try {
            $request = Yii::$app->request;
            $q = $request->post('q');
            $asal_rujukan = $request->post('asal_rujukan');
            $param = $request->post();
            $model = new Bpjs;
            $result = $model->referensiFaskes($q, $asal_rujukan);
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

    public function actionReferensiDokter($pelayanan=null, $tglSep=null, $spesialis=null)
    {
        try {
            $tglsep = date('Y-m-d', strtotime($tglSep));
            $model = new Bpjs;
            $result = $model->referensiDokter($pelayanan, $tglsep, $spesialis);

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

    public function actionCreateSep()
    {
        try {
            $post = Yii::$app->request->post();
            $model = new Bpjs;
            $t_sep = [];
            foreach ($model->arr_sep as $key=>$each) {
                $t_sep[$each] = $post[$each];
            }
            $model->t_sep = $t_sep;
            $result = $model->createSep();
            $saved = false;

            if(isset($result['metadata'])) {
                if ($result['metadata']['code'] == 200) {
                    $model->setManualAttribute();
                    $model->nosep = $result['response']['sep']['noSep'];
                    $model->additional_data = json_encode($result['response']);
                    $saved = $model->save() ? $model : false;
                }
            }

            // save to rujukan
            $rujukan = new Rujukan;
            $rujukan->asalrujukan_id = 2; // wip
            $diagnosa = new Diagnosa;
            $diagnosa = $diagnosa->find()->where(['diagnosa_kode'=>$t_sep['diagAwal']])->asArray()->one();
            $rujukan->diagnosa_id = $diagnosa ? $diagnosa['diagnosa_id'] : null;
            $rujukan->no_rujukan = $t_sep['noRujukan'];
            $rujukan->tanggal_rujukan = date('Y-m-d', strtotime($t_sep['tglRujukan']));
            $rujukan->kodediagnosa_rujukan = $t_sep['diagAwal'];
            $rujukan->save();

            return [
                'result' => $result,
                'model' => $saved
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

    public function actionDeleteSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $model->t_sep = $request->post();
            $result = $model->deleteSep();
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            if ($request->post()) {
                $model->attributes = $request->post();
                $model->tglsep = $model->tglsep . ' ' . date('H:i:s');
                $model->tglrujukan = $model->tglrujukan . ' ' . date('H:i:s');
                if ($model->save()) {
                    return [
                        'bpjs_id' => $model->bpjs_id,
                        'message' => 'Data Berhasil di simpan'
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'BpjsForm');
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

    public function actionUpdateTanggalPulangSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $model->t_sep = $request->post();
            $result = $model->updateTanggalPulangSep();
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

    public function actionUpdateTanggalPulangSepNew()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $model->t_sep_new = $request->post();
            $result = $model->updateTanggalPulangSepNew();
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

    /**
     * @controller actionPrintSep
     * @attribute #sep# => layout SEP
     *
     **/
    public function actionPrintSep()
    {
        return Yii::$app->docoPlugin->execute('print_sep');
    }

    public function actionCreateSepNew()
    {
        try {
            $bpjsForm = Yii::$app->request->post();
            $model = new Bpjs;

            $t_sep_new = [];
            $t_sep_new['noKartu'] = trim($bpjsForm['no_kartu']);
            $t_sep_new['tglSep'] = $bpjsForm['tanggal_sep'];
            $t_sep_new['jnsPelayanan'] = $bpjsForm['jenis_pelayanan'];
            $t_sep_new['klsRawat'] = $bpjsForm['kelas_rawat'];
            $t_sep_new['noMR'] = $bpjsForm['no_rekam_medik'];
            $t_sep_new['asalRujukan'] = $bpjsForm['asal_rujukan'];
            $t_sep_new['tglRujukan'] = $bpjsForm['tanggal_rujukan'];
            $t_sep_new['noRujukan'] = trim($bpjsForm['no_rujukan']);
            $t_sep_new['ppkRujukan'] = $bpjsForm['ppk_rujukan'];
            $t_sep_new['catatan'] = $bpjsForm['catatan_sep'];
            $t_sep_new['diagAwal'] = $bpjsForm['diagnosa_awal'];
            $t_sep_new['tujuan'] = isset($bpjsForm['poli_tujuan']) ? $bpjsForm['poli_tujuan'] : self::STRING_IGD;
            $t_sep_new['eksekutif'] = $bpjsForm['poli_eksekutif'];
            $t_sep_new['cob'] = $bpjsForm['cob'];
            $t_sep_new['katarak'] = $bpjsForm['katarak'];
            $t_sep_new['lakaLantas'] = $bpjsForm['kasus_kecelakaan'];
            $t_sep_new['noSurat'] = isset($bpjsForm['no_surat_kontrol']) ? trim($bpjsForm['no_surat_kontrol']) : '';
            $t_sep_new['kodeDPJP'] = isset($bpjsForm['kode_dpjp']) ? $bpjsForm['kode_dpjp'] : '';

            if (!$bpjsForm['status_suplesi']) {
                $penjamin = '';
                if ($bpjsForm['kasus_kecelakaan'] == '1') { // kecekalaan lalu lintas dan bukan kecelakaan kerja
                    $penjamin = '1';
                }  elseif ($bpjsForm['kasus_kecelakaan'] == '2') {
                    $penjamin = '1,2';
                } elseif ($bpjsForm['kasus_kecelakaan'] == '3') {
                    $penjamin = '2,3';
                } else {
                    $penjamin = '';
                }
                $t_sep_new['penjamin'] = $penjamin;
            } else {
                $t_sep_new['penjamin'] = '';
            }

            $t_sep_new['tglKejadian'] = $bpjsForm['tanggal_kejadian'];
            $t_sep_new['keterangan'] = $bpjsForm['keterangan'];
            $t_sep_new['suplesi'] = $bpjsForm['status_suplesi'];
            $t_sep_new['noSepSuplesi'] = $bpjsForm['no_sep_suplesi'];
            $t_sep_new['kdPropinsi'] = $bpjsForm['kode_provinsi'];
            $t_sep_new['kdKabupaten'] = $bpjsForm['kode_kabupaten'];
            $t_sep_new['kdKecamatan'] = $bpjsForm['kode_kecamatan'];
            $t_sep_new['noSurat'] = isset($bpjsForm['no_surat_kontrol']) ? trim($bpjsForm['no_surat_kontrol']) : '';
            $t_sep_new['kodeDPJP'] = isset($bpjsForm['kode_dpjp']) ? $bpjsForm['kode_dpjp'] : '';
            $t_sep_new['noTelp'] = trim($bpjsForm['no_telp']);
            $t_sep_new['user'] = $bpjsForm['user'];

            $model->t_sep_new = $t_sep_new;
            $result = $model->createSepNew();
            if ($result['metaData']['code'] == 200) {
                $saved = false;
                $noSep = $result['response']['sep']['noSep'];
                $catatanSep = $result['response']['sep']['catatan'];
                $namaPeserta = $result['response']['sep']['peserta']['nama'];
                $model->tglsep = $t_sep_new['tglSep'];
                $model->nosep = $noSep;
                $model->nokartuasuransi = $t_sep_new['noKartu'];
                $model->tglrujukan = $t_sep_new['tglRujukan'];
                $model->norujukan = $t_sep_new['noRujukan'];
                $model->ppkrujukan = $t_sep_new['ppkRujukan'];
                $model->ppkpelayanan = $model->ppkPelayanan;
                $model->jnspelayanan = $t_sep_new['jnsPelayanan'];
                $model->catatansep = $catatanSep;
                $model->diagnosaawal = $t_sep_new['diagAwal'];
                $model->politujuan = $t_sep_new['tujuan'];
                $model->klsrawat = $t_sep_new['klsRawat'];
                $model->nama_peserta = $namaPeserta;
                $model->lakalantas = $t_sep_new['lakaLantas'];
                $model->additional_data = json_encode($result['response']);
                $model->additional_request = $model->t_sep_new;
                $model->pendaftaran_id = $bpjsForm['pendaftaran_id'];
                $model->pasienadmisi_id = $bpjsForm['pasienadmisi_id'];
                if($model->validate()) {
                    $model->save();
                    // save to rujukan
                    $rujukan = new Rujukan;
                    $rujukan->asalrujukan_id = 2; // wip
                    $diagnosa = new Diagnosa;
                    $diagnosa = $diagnosa->find()->where(['diagnosa_kode'=>$t_sep_new['diagAwal']])->asArray()->one();
                    $rujukan->diagnosa_id = $diagnosa ? $diagnosa['diagnosa_id'] : null;
                    $rujukan->no_rujukan = $t_sep_new['noRujukan'];
                    $rujukan->tanggal_rujukan = date('Y-m-d', strtotime($t_sep_new['tglRujukan']));
                    $rujukan->kodediagnosa_rujukan = $t_sep_new['diagAwal'];
                    $rujukan->save();

                    // update bpjs_id di pasienadmisi_t
                    if (!empty($model->pasienadmisi_id)) {
                        $pendaftaran = PasienAdmisi::findOne($model->pasienadmisi_id);
                        $pendaftaran->bpjs_id = $model->bpjs_id;
                        $pendaftaran->save();
                    } else {
                        // update bpjs_id di pendaftaran_t
                        $pendaftaran = Pendaftaran::findOne($model->pendaftaran_id);
                        $pendaftaran->bpjs_id = $model->bpjs_id;
                        $pendaftaran->save();
                    }
                }
                else {
                    $errors = DocoHelpers::parseError($model->errors,'BpjsNewForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }

            return [
                'result' => $result,
                'model' => $model
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

    public function actionReferensiDpjp()
    {
        try {
            $request = Yii::$app->request;
            $param = $request->post('q');
            $param1 = !empty($request->post('pelayanan')) ? (int) $request->post('pelayanan'): self::PELAYANAN_RAWAT_INAP; // rawat inap
            $param2 = date('Y-m-d');
            $param3 = self::STRING_IGD;

            if($param1 != self::PELAYANAN_RAWAT_INAP) {
                $param2 = !empty($request->post('tglSep')) ? date('Y-m-d', strtotime($request->post('tglSep'))) : date('Y-m-d');
                if(!empty($request->post('poli') && $request->post('poli') != self::STRING_IGD)) {
                    $param3 = $request->post('poli');
                } elseif (!empty($request->post('poli')) && $request->post('poli') == self::STRING_IGD) { // case igd skdpri
                    $param1 = self::PELAYANAN_RAWAT_INAP;
                }
            }

            $model = new Bpjs;
            $result = $model->referensiDpjp($param1, $param2, $param3);
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

    public function actionReferensiKelasRawat()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $result = $model->referensiKelasRawat();
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

    public function actionReferensiProvinsi()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $param = $request->post('q');
            $result = $model->referensiProvinsi();
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

    public function actionReferensiKabupaten()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $param = $request->post('kode_propinsi');
            $result = $model->referensiKabupaten($param);
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

    public function actionReferensiKecamatan()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $param = $request->post('kode_kabupaten');
            $result = $model->referensiKecamatan($param);
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

    public function actionGetDetailKunjunganBpjs()
    {
        $request = Yii::$app->request;
        $param = $request->get('param');
        $model = new Bpjs;
        $query = $model->detailHistoryBpjs($param);

        return $query;
    }

    public function actionReferensiCariSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $param = $request->post('nosep');
            $result = $model->referensiCariSep($param);
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

    /**
     * @author Budi
     * @since 28-10-2019 17:52
     * [actionCreateSepManual create sep manual]
     * @return [type] [description]
     */
    public function actionCreateSepManual()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new Bpjs;
            $bpjsForm = Yii::$app->request->post();
            $no_kartu = isset($bpjsForm['no_kartu']) ? $bpjsForm['no_kartu'] : '';
            $tglSEP = isset($bpjsForm['tanggal_sep']) ? $bpjsForm['tanggal_sep'] : '';
            $isKtp = false;
            $cekPeserta = $this->cekPeserta($no_kartu, $tglSEP, $isKtp);
            $responsePeserta = $cekPeserta['response']['peserta'];
            $hakKelas = isset($responsePeserta) ? $responsePeserta['hakKelas']['kode'] : '';
            $pasienadmisi_id = null;
            if(isset($bpjsForm['pasienadmisi_id'])) {
                $pasienadmisi_id = $bpjsForm['pasienadmisi_id'];
            }

            $model->attributes = $bpjsForm;
            $model->tglsep = $bpjsForm['tanggal_sep'];
            $model->nosep = $bpjsForm['nosep'];
            $model->nokartuasuransi = $bpjsForm['no_kartu'];
            $model->tglrujukan = $bpjsForm['tanggal_rujukan'];
            $model->norujukan = !empty($bpjsForm['no_rujukan']) ? $bpjsForm['no_rujukan'] : '-';
            $model->ppkrujukan = $bpjsForm['ppk_rujukan'];
            $model->jnspelayanan = ($bpjsForm['jenis_pelayanan'] == 'Rawat Jalan') ? 2 : 1;
            $model->diagnosaawal = $bpjsForm['diagnosa_awal'];
            $model->politujuan = $bpjsForm['ppk_rujukan'];
            $model->lakalantas = $bpjsForm['kasus_kecelakaan'];
            $model->pendaftaran_id = $bpjsForm['pendaftaran_id'];
            $model->pasienadmisi_id = $pasienadmisi_id;
            $model->klsrawat = $hakKelas;
            $additional_data = [
                'sep' => [
                    'catatan' => $bpjsForm['catatan_sep'],
                    'diagnosa' => $bpjsForm['diagnosa_awal'],
                    'informasi' => [
                        'dinsos' => null,
                        'noSKTM' => null,
                        'prolanisPRB' => null,
                    ],
                    'jnsPelayanan' => $bpjsForm['jenis_pelayanan'],
                    'kelasRawat' => $bpjsForm['kelas_rawat'],
                    'noRujukan' => $bpjsForm['no_rujukan'],
                    'noSep' =>  $bpjsForm['nosep'],
                    'penjamin' => isset($bpjsForm['penjamin']) ? $bpjsForm['penjamin'] : null,
                    'peserta' => [
                        'asuransi' => isset($bpjsForm['asuransi']) ? $bpjsForm['asuransi'] : null,
                        'hakKelas' => isset($bpjsForm['hak_kelas']) ? $bpjsForm['hak_kelas'] : null,
                        'jnsPeserta' => isset($bpjsForm['jenis_peserta']) ? $bpjsForm['jenis_peserta'] : null,
                        'kelamin' => isset($bpjsForm['jenis_kelamin']) ? $bpjsForm['jenis_kelamin'] : null,
                        'nama' => isset($bpjsForm['nama_pasien']) ? $bpjsForm['nama_pasien'] : null,
                        'noKartu' => isset($bpjsForm['no_kartu']) ? $bpjsForm['no_kartu'] : null,
                        'noMr' => isset($bpjsForm['noMr']) ? $bpjsForm['noMr'] : null,
                        'tglLahir' => isset($bpjsForm['tanggal_lahir']) ? $bpjsForm['tanggal_lahir'] : null,
                    ],
                    'poli' => isset($bpjsForm['poli_tujuan']) ? $bpjsForm['poli_tujuan'] : null,
                    'poliEksekutif' => isset($bpjsForm['poli_eksekutif']) ? $bpjsForm['poli_eksekutif'] : null,
                    'tglSep' => isset($bpjsForm['tanggal_sep']) ? $bpjsForm['tanggal_sep'] : null,
                ]
            ];

            $model->additional_data = json_encode($additional_data);

            if($model->validate() && $model->save()) {
                $bpjs_id = $model->bpjs_id;
                // save to rujukan
                $rujukan = new Rujukan;
                $rujukan->asalrujukan_id = $model->asal_rujukan;
                $diagnosa = Diagnosa::find()->where(['diagnosa_nama' => $model->diagnosaawal])
                    ->one();

                $rujukan->diagnosa_id = $diagnosa ? $diagnosa->diagnosa_id : null;
                $rujukan->no_rujukan = $model->norujukan;
                $rujukan->tanggal_rujukan = date('Y-m-d', strtotime($model->tglrujukan));
                $rujukan->kodediagnosa_rujukan = $diagnosa ? $diagnosa->diagnosa_kode : null;
                $rujukan->save();


                // update bpjs_id di pasienadmisi_t
                if ($model->pasienadmisi_id != null) {
                    $pasienAdmisi = PasienAdmisi::findOne($model->pasienadmisi_id);
                    $pasienAdmisi->bpjs_id = $bpjs_id;
                    $pasienAdmisi->keterangan = '-';
                    $pasienAdmisi->save();
                } else {
                    // update bpjs_id di pendaftaran_t
                    $pendaftaran = Pendaftaran::findOne($model->pendaftaran_id);
                    $pendaftaran->bpjs_id = $bpjs_id;
                    $pendaftaran->save();
                }

                $transaction->commit();
            }
            else {
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors,'BpjsNewForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }

            return [
                'status' => 200,
                'data' => $model
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /**
    *
    */
    public function actionHistoriPelayanan($params = null)
    {
        try {
            $request = Yii::$app->request;

            if ($params) {
                $nokartu = $params['no_kartu'];
                $tglmulai = $params['tgl_mulai'] ? date('Y-m-d', strtotime($params['tgl_mulai'])) : null;
                $tglselesai = $params['tgl_selesai'] ? date('Y-m-d', strtotime($params['tgl_selesai'])) : null;
            } else {
                $nokartu = $request->post()['no_kartu'];
                $tglmulai = $request->post()['tgl_mulai'] ? date('Y-m-d', strtotime($request->post()['tgl_mulai'])) : null;
                $tglselesai = $request->post()['tgl_selesai'] ? date('Y-m-d', strtotime($request->post()['tgl_selesai'])) : null;
            }

            $model = new Bpjs;
            $param = [
                'noKartu'=>$nokartu,
                'tglMulai'=>$tglmulai,
                'tglAkhir'=>$tglselesai,
            ];
            $result = $model->detailHistoryBpjs($param);
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

    private function cekPeserta($no_kartu, $tglSEP, $isKtp)
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $result = $model->peserta($no_kartu, $tglSEP, $isKtp);
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

    public function actionReferensiPeserta($nokartu, $tglSEP, $isKtp)
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $result = $model->peserta($nokartu, $tglSEP, $isKtp);
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

    public function actionReferensiKepesertaan() {
        $request = Yii::$app->request;
        $key = $request->post('q', null);
        $model = Penjamin::find()
            ->select(['penjamin_id', 'penjamin_nama', 'penjamin_kode'])
            ->where(['carabayar_id' => 6, 'is_active' => true])
            ->orWhere(['carabayar_id' =>6, 'is_active' => false])
            ->andWhere(['NOT', ['penjamin_kode' => null]]); //BPJS

        if(!empty($key)) {
            $model->andWhere(['ILIKE', 'LOWER(penjamin_nama)', strtolower($key)]);
        }

        return $model->asArray()->all();
    }

    public function actionGetConfigBpjs()
    {
        return Cache::getConfigBpjs();
    }


    public function actionCariSep()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $nosep = $request->get('noSep');

        if(empty($nosep)) return [
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Nosep harus di isi'
        ];

        $result = $model->referensiCariSep($nosep);
        return $result;
    }

    public function actionCariSepInternal()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $nosep = $request->get('noSep');

        if(empty($nosep)) return [
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Nosep harus di isi'
        ];

        $result = $model->cariSepInternal($nosep);
        return $result;
    }

    public function actionDeleteSepNew()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $nosep = $request->post('noSep');

        if(empty($nosep)) return [
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Nosep harus di isi'
        ];

        $result = $model->deleteSepNew($nosep);
        return $result;
    }

    public function actionDeleteSepInternal()
    {
        $request = Yii::$app->request;
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $username = $request->post('username');
        if (empty($username)) {
            $username = !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : null;
        }

        $no_sep = $request->post('noSep');
        $resCariKlaim = Yii::$app->db->createCommand("SELECT nosep FROM sy_infopasienbpjs_v WHERE nosep = :nosep LIMIT 1")->bindValue(':nosep',$no_sep)->queryScalar();
        if(!is_null($resCariKlaim) && $resCariKlaim !== FALSE){
            return [
                'metaData' => [
                    'code' => 422,
                    'message' => 'Data sudah dilakukan proses klaim!'
                ]
            ];
        }

        $model = new Bpjs;
        $payload = [
            'noSep' => $request->post('noSep'),
            'noSurat' => $request->post('noSurat'),
            'tglRujukanInternal' => $request->post('tglRujukanInternal'),
            'kdPoliTuj' => $request->post('kdPoliTuj'),
            'user' => $username
        ];

        $validator = DynamicModel::validateData($payload, [
            [['noSep', 'noSurat', 'tglRujukanInternal', 'kdPoliTuj'], 'required']
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        $result = $model->deleteSepInternal($payload);
        return $result;
    }

    public function actionManajemenCariSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            $param = $request->post('nosep');
            $result = $model->referensiCariSep($param);
            $peserta = [];
            if (!empty($result)){
                $peserta = $model->peserta($result['response']['peserta']['noKartu'], null, false);
            }
            $data = [
                'sep' => $result,
                'peserta' => $peserta
            ];
            return $data;
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

    public function actionUpdateSep()
    {
        $request = Yii::$app->request;
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $model = new Bpjs;
        $post = $request->post();
        if ($post['kasus_kecelakaan'] == 0){
            $post['keterangan'] = '';
            $post['status_suplesi'] = '';
            $post['no_sep_suplesi'] = '';
            $post['kode_provinsi'] = '';
            $post['kode_kabupaten'] = '';
            $post['kode_kecamatan'] = '';
        }

        $data = [
            'request'=>[
                't_sep'=>[
                    'noSep' => $post['no_sep'],
                    'klsRawat' => [
                        'klsRawatHak' => $post['kelas_rawat'],
                        'klsRawatNaik' => "",
                        'pembiayaan' => "",
                        'penanggungJawab' => ""
                    ],
                    'noMR' => $post['no_rekam_medik'],
                    'catatan' => $post['catatan_sep'],
                    'diagAwal' => $post['diagnosa_awal'],
                    'poli' => [
                        'tujuan' => $post['poli_tujuan'],
                        'eksekutif' => isset($post['poli_eksekutif']) ? $post['poli_eksekutif'] : '0'
                    ],
                    'cob' => [
                        'cob' => isset($post['cob']) ? $post['cob'] : '0'
                    ],
                    'katarak' => [
                        'katarak' => isset($post['katarak']) ? $post['katarak'] : '0'
                    ],
                    'jaminan' => [
                        'lakaLantas' => $post['kasus_kecelakaan'],
                        'penjamin' => [
                            'tglKejadian' => ($post['kasus_kecelakaan'] != 0) ? date('Y-m-d',strtotime($post['tanggal_kejadian'])) : '',
                            'keterangan' => $post['keterangan'],
                            'suplesi' => [
                                'suplesi' => $post['status_suplesi'],
                                'noSepSuplesi' => $post['no_sep_suplesi'],
                                'lokasiLaka' => [
                                    'kdPropinsi' => isset($post['kode_provinsi']) ? $post['kode_provinsi'] : '',
                                    'kdKabupaten' => isset($post['kode_kabupaten']) ? $post['kode_kabupaten'] : '',
                                    'kdKecamatan' => isset($post['kode_kecamatan']) ? $post['kode_kecamatan'] : '',
                                ],
                            ],
                        ],
                    ],
                    'dpjpLayan' => $post['kode_dpjp_melayani'],
                    'noTelp' => $post['no_telp'],
                    'user' => $post['user']
                ]
            ]
        ];

        $result = $model->updateSep($data);
        if ($result['metaData']['code'] == 200){
            $nosep = $request->post('no_sep');
            $sep = [];
            $bpjs = new Bpjs;
            $data_bpjs = $bpjs->find()->where(['nosep'=>$nosep, 'is_deleted' => false])->asArray()->one();
            $sep = $data_bpjs['additional_data'];
            if (!isset($sep['sep'])) {

                $temp_sep = $bpjs->referensiCariSep($nosep);
                $sep = ['sep' => $temp_sep['response']];
                $sep['sep']['noTelp'] = $post['no_telp'];
            }
            if (!empty($data_bpjs)){
                $modelBpjs = new Bpjs;
                $modelBpjs->tglsep = $data_bpjs['tglsep'];
                $modelBpjs->nosep = $nosep;
                $modelBpjs->nokartuasuransi = $data_bpjs['nokartuasuransi'];
                $modelBpjs->tglrujukan = $data_bpjs['tglrujukan'];
                $modelBpjs->norujukan = $data_bpjs['norujukan'];
                $modelBpjs->ppkrujukan = $data_bpjs['ppkrujukan'];
                $modelBpjs->ppkpelayanan = $data_bpjs['ppkpelayanan'];
                $modelBpjs->jnspelayanan = $data_bpjs['jnspelayanan'];
                $modelBpjs->catatansep = $post['catatan_sep'];
                $modelBpjs->diagnosaawal = $post['diagnosa_awal'];
                $modelBpjs->politujuan = $data_bpjs['politujuan'];
                $modelBpjs->klsrawat = $data_bpjs['klsrawat'];
                $modelBpjs->nama_peserta = $data_bpjs['nama_peserta'];
                $modelBpjs->lakalantas = $post['kasus_kecelakaan'];
                $modelBpjs->asal_rujukan = $data_bpjs['asal_rujukan'];
                $modelBpjs->additional_data = json_encode($sep);
                $modelBpjs->additional_request = json_encode($data);
                $modelBpjs->pendaftaran_id = $data_bpjs['pendaftaran_id'];
                $modelBpjs->kode_dpjp_melayani = $post['kode_dpjp_melayani'];
                $modelBpjs->nama_dpjp_melayani = $post['nama_dpjp_melayani'];
                $modelBpjs->kode_ppk_perujuk = $data_bpjs['kode_ppk_perujuk'];
                $modelBpjs->nama_ppk_perujuk = $data_bpjs['nama_ppk_perujuk'];
                $modelBpjs->klsrawatnaik = $data_bpjs['klsrawatnaik'];
                $modelBpjs->pembiayaan = $data_bpjs['pembiayaan'];
                $modelBpjs->penanggung_jawab = $data_bpjs['penanggung_jawab'];
                $modelBpjs->tujuan_kunj = $data_bpjs['tujuan_kunj'];
                $modelBpjs->flag_procedure = $data_bpjs['flag_procedure'];
                $modelBpjs->kd_penunjang = $data_bpjs['kd_penunjang'];
                $modelBpjs->assesment_pel = $data_bpjs['assesment_pel'];
                $modelBpjs->cetakan_ke = $data_bpjs['cetakan_ke'];
                $modelBpjs->tgl_cetak = $data_bpjs['tgl_cetak'];
                if ($modelBpjs->save()) {
                    $bpjsId = $modelBpjs->bpjs_id;
                    $qPendaftaran = "
                        UPDATE pendaftaran_t SET bpjs_id = {$bpjsId}
                        WHERE pendaftaran_id = {$modelBpjs->pendaftaran_id}
                    ";

                    $qUpdatePendaftaran = Yii::$app->db->createCommand($qPendaftaran)->execute();

                    $bpjsupdate = "
                        UPDATE bpjs_t SET is_deleted = true
                        WHERE bpjs_id = {$data_bpjs['bpjs_id']}
                    ";

                    $qUpdateBpjs = Yii::$app->db->createCommand($bpjsupdate)->execute();
                }
            }
        }

        return $result;
    }

    public function actionManajemenDeleteSep()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;
        $no_sep = $request->post('no_sep');
        $resCariKlaim = Yii::$app->db->createCommand("SELECT nosep FROM sy_infopasienbpjs_v WHERE nosep = :nosep LIMIT 1")->bindValue(':nosep',$no_sep)->queryScalar();
        if(!is_null($resCariKlaim) && $resCariKlaim !== FALSE){
            return [
                'metaData' => [
                    'code' => 422,
                    'message' => 'Data sudah dilakukan proses klaim!'
                ]
            ];
        }
        $result = $model->deleteSepNew($no_sep);
        $pendaftaran_ids = [];
        Yii::error($result);
        if ($result['metaData']['code'] == 200 || $result['metaData']['code'] == '200') {
            /** Get pendaftaran_id form bpjs_t */
            $data_bpjs = Bpjs::find()->select(['pendaftaran_id'])
                ->where(['nosep' => $no_sep])
                ->asArray()->all();

            foreach ($data_bpjs as $k => $v) {
                $pendaftaran_ids[] = $v['pendaftaran_id'];
            }

            /** Delete bpjs_id from pendaftaran_t */
            $set_pendaftaran = Pendaftaran::updateAll([
                'bpjs_id' => null
            ], ['in', 'pendaftaran_id', $pendaftaran_ids]);

            /** Change no SEP to null */
            $ubah = Bpjs::updateAll([
                'nosep' => null,
            ],['nosep' => $no_sep]);
        }

        return $result;
    }

    public function actionCekSepPasien()
    {
        $request = Yii::$app->request;

        $nosep = $request->get('nosep');

        if(empty($nosep)) return [
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Nosep harus di isi'
        ];
        $bpjs = new Bpjs;
        $result = $bpjs->find()->where(['nosep'=>$nosep, 'is_deleted' => false])->asArray()->one();
        // $dpjpServe = $result['kode_dpjp_melayani'];
        // $param1 = self::PELAYANAN_RAWAT_INAP;
        // $param2 = date('Y-m-d');
        // $param3 = self::STRING_IGD;

        // if(!empty($dpjpServe)) {
        //     $kodeDokter = (int) $dpjpServe;
        // }

        // if(!empty($kodeDokter)) {
        //     $lookDpjp = $bpjs->referensiDpjp($param1, $param2, $param3);
        //     if($lookDpjp['response']['list'] && !empty($lookDpjp['response']['list'])) {
        //         foreach($lookDpjp['response']['list'] as $k => $v) {
        //             if($kodeDokter == $v['kode']) {
        //                 $result['nama_dpjp'] = $v['nama'];
        //             }
        //         }
        //     }
        // }
        return $result;
    }

    public function actionPackTujuanKunjungan()
    {
        $request = Yii::$app->request;
        $lookup_type = $request->get('lookup_type', null);

        $list_data = Lookup::find()->select([
            'lookup_kode',
            'lookup_name'
        ])->where([
            'lookup_type' => $lookup_type
        ])->orderBy([
            'lookup_urutan' => SORT_ASC,
        ])->asArray()->all();

        return [
            'list_data' => $list_data,
        ];
    }

    /**
     * @controller actionPrintSepInternal
     * @attribute #sep# => layout SEP Internal
     *
     **/
    public function actionPrintSepInternal()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '360');
        try{
            $request = Yii::$app->request;
            $model = new InfKunjunganRsView;
            $modelBpjs = new Bpjs;
            $pasien_id = $request->get('pasien_id');
            $pendaftaran_id = $request->get('pendaftaran_id');
            $nosep = $request->get('nosep');
            $ruangan_id = $request->get('ruangan_id');
            $kode_dpjp_melayani = $request->get('kode_dpjp_melayani');
            $politujuan = $request->get('politujuan');
            $faskesKode = '-';
            $faskesNama = '-';
            $kelasRawat = '-';
            $kelasHak = '-';
            $dpjp = '-';
            $ktp = '-';
            $statusPasien = '-';
            $render_partial = 'print_sep_internal';

            $bpjs = $modelBpjs::find()->where([
                'nosep' => $nosep,
                'kode_dpjp_melayani' => $kode_dpjp_melayani,
                'politujuan' => $politujuan
            ])->one();

            if (empty($bpjs)) {
                throw new \yii\web\NotFoundHttpException("Data BPJS Tidak Ditemukan", 404);
            }

            $pendaftaran_id = $bpjs->pendaftaran_id;
            $kunjungan = $model::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['IS NOT', 'bpjs_id', null]);

            if ($ruangan_id == DocoConstants::RUANGAN_PENDAFTARAN_RANAP) {
                $kunjungan->andWhere(['IS NOT', 'pasienadmisi_id', null]);
            }

            $kunjungan = $kunjungan->one();

            if (!$kunjungan) {
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            if ($bpjs->cetakan_ke === 0) {
                $qPasien = Pasien::find()->select([
                    'additional_data'
                ])->where([
                    'pasien_id' => $kunjungan->pasien_id
                ])->asArray()->one();
                $getDataBpjs['response'] = json_decode($qPasien['additional_data'],true);

                // If data bpjs empty
                if (empty($getDataBpjs['response']['peserta']['noKartu'])) {
                    // Get data bpjs
                    $getDataBpjs = $modelBpjs->peserta($bpjs->nokartuasuransi, $bpjs->tglsep);
                }
                $noKartu = $getDataBpjs['response']['peserta']['noKartu'];

                // Get data faskes perujuk
                if ($bpjs->asal_rujukan && empty($bpjs->nama_ppk_perujuk) && empty($bpjs->kode_ppk_perujuk)) {
                    $faskesRujukan = $modelBpjs->referensiFaskes($bpjs->ppkrujukan, $bpjs->asal_rujukan);

                    if (isset($faskesRujukan['response']['faskes'][0])) {
                        $faskesKode = $faskesRujukan['response']['faskes'][0]['kode'];
                        $faskesNama = $faskesRujukan['response']['faskes'][0]['nama'];
                    }
                } else if (!empty($bpjs->kode_ppk_perujuk) && !empty($bpjs->nama_ppk_perujuk)) {
                    $faskesNama = $bpjs->nama_ppk_perujuk;
                    $faskesKode = $bpjs->kode_ppk_perujuk;
                }

                // Simpan rujukan terakhir
                $getDataBpjs['response']['peserta']['kodefaskes_perujukterakhir']['kode'] = $faskesKode;
                $getDataBpjs['response']['peserta']['namafaskes_perujukterakhir']['nama'] = $faskesNama;

                $qPasien = Pasien::updateAll([
                    'additional_data' => json_encode($getDataBpjs['response']),
                    'nopeserta_bpjs' => $noKartu,
                ],['pasien_id' => $kunjungan->pasien_id]);

                $additional = json_decode($bpjs->additional_data,true);
                $dataPeserta = $additional['sep'];
                if(!empty($additional) && isset($getDataBpjs['response']['peserta']['informasi'])) {
                    $additional['sep']['informasi']['prolanisPRB'] = $getDataBpjs['response']['peserta']['informasi']['prolanisPRB'];
                }
                $bpjs->additional_data = json_encode($additional);
                //** Handle condition cetakan sep kosong karena nosep dari vclaim */
                if(empty($additional_data) && !empty($bpjs->nosep)) {
                    $getDataBpjs = $modelBpjs->referensiCariSep($bpjs->nosep);
                    $add['sep'] = $getDataBpjs['response'];
                    $bpjs->additional_data = json_encode($add);
                }
            } else {
                $qPasien = Pasien::find()->select([
                    'additional_data'
                ])->where([
                    'pasien_id' => $kunjungan->pasien_id
                ])->asArray()->one();
                $additional = json_decode($qPasien['additional_data'],true);
                $dataPeserta = $additional['peserta'];
                $getDataBpjs['response'] = json_decode($qPasien['additional_data'],true);

                if (array_key_exists('namafaskes_perujukterakhir', $getDataBpjs['response']['peserta'])) {
                    if (array_key_exists('nama', $getDataBpjs['response']['peserta']['namafaskes_perujukterakhir'])) {
                        $faskesNama = $getDataBpjs['response']['peserta']['namafaskes_perujukterakhir']['nama'];
                    }
                } else if (isset($bpjs->nama_ppk_perujuk) && !empty($bpjs->nama_ppk_perujuk)) {
                    $faskesNama = $bpjs->nama_ppk_perujuk;
                }
            }

            // update cetakan_ke & tgl_cetak
            $additional_data = json_decode($bpjs->additional_data,true);
            $additional_request =  json_decode($bpjs->additional_request,true);

            //** Handle condition cetakan sep kosong karena nosep dari vclaim */
            if(empty($additional_data) && !empty($bpjs->nosep)) {
                $getDataBpjs = $modelBpjs->referensiCariSep($bpjs->nosep);
                $add['sep'] = $getDataBpjs['response'];
                $bpjs->additional_data = json_encode($add);
            }

            $bpjs->cetakan_ke += 1;
            $date = new \DateTime("now");
            $bpjs->tgl_cetak = $date->format('Y-m-d H:i:s');
            $bpjs->save(false);

            if (!empty($additional_data['sep']['noTelp'])){
                if (!empty($additional_request['request']['t_sep']['noTelp'])) {
                    # code...
                    $getDataBpjs['response']['peserta']['mr']['noTelepon'] = $additional_request['request']['t_sep']['noTelp'];
                }

                if (!empty($additional_data['sep']['noTelp'])) {
                    # code...
                    $getDataBpjs['response']['peserta']['mr']['noTelepon'] = $additional_data['sep']['noTelp'];
                }
            }

            $detailBpjs = $additional_data['sep'];
            $kelasRawat = isset($detailBpjs['kelasRawat']) && $detailBpjs['kelasRawat'] != '-' ? $detailBpjs['kelasRawat'] : '-';
            if (!empty($detailBpjs['peserta']['hakKelas'])){
                $exp = explode("Kelas", $detailBpjs['peserta']['hakKelas']);
                $kelasHak = isset($exp[1]) && $exp[1] != '-' ? $exp[1] : '-';
            }

            if(is_numeric($detailBpjs['jnsPelayanan'])) {
                $jnsPelayanan = ($detailBpjs['jnsPelayanan'] == 1) ? 'Rawat Inap' : 'Rawat Jalan';
            }
            else {
                $jnsPelayanan = $detailBpjs['jnsPelayanan'];
            }
            $exp = explode(" - ", $detailBpjs['diagnosa']." - ");
            $cnt = count($exp);

            $diagnosa = ($cnt == 3) ? $exp[1] : $exp[0];

            if ($kunjungan->instalasi_id == DocoConstants::INST_ID_RD) {
                $faskesNama = '-';
            }

            $rujukan = $modelBpjs->cariRujukanPeserta($bpjs->nokartuasuransi);
            if (isset($rujukan['response']['rujukan'])) {
                $poliRujuk = $rujukan['response']['rujukan']['poliRujukan']['nama'];
            } else {
                $poliRujuk = '-';
            }

            /** Get nama Dokter DPJP */
            if(isset($bpjs->nama_dpjp_melayani) && !empty($bpjs->nama_dpjp_melayani)) {
                $dpjp = $bpjs->nama_dpjp_melayani;
            } else if($bpjs->additional_request && !empty($bpjs->additional_request)) {
                $arrRequest = json_decode($bpjs->additional_request, true);
                $kodeDokter = '';
                $dpjpServe = $bpjs->kode_dpjp_melayani;
                $param1 = self::PELAYANAN_RAWAT_INAP;
                $param2 = date('Y-m-d', strtotime($bpjs->tglsep));
                $param3 = self::STRING_IGD;

                if(!empty($dpjpServe)) {
                    $kodeDokter = (int) $dpjpServe;
                }

                if(!empty($kodeDokter)) {
                    // $param1 = $bpjs->jnspelayanan;
                    // $param2 = date('Y-m-d', strtotime($bpjs->tglsep));
                    // $param3 = $bpjs->politujuan;

                    $lookDpjp = $bpjs->referensiDpjp($param1, $param2, $param3);
                    if($lookDpjp['response']['list'] && !empty($lookDpjp['response']['list'])) {
                        foreach($lookDpjp['response']['list'] as $k => $v) {
                            if($kodeDokter == $v['kode']) {
                                $dpjp = $v['nama'];
                            }
                        }
                    }
                }
            }

            if (isset($kunjungan->status_pasien)) {
                $statusPasien = Lookup::findOne($kunjungan->status_pasien);
            }

            if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                $additionalPasien = json_decode($kunjungan->additional_pasien);

                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == self::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                }
            }

            if (isset($kunjungan->namadepan) && $kunjungan->namadepan != '') {
                $kunjungan->nama_pasien = $kunjungan->namadepan.' '.$kunjungan->nama_pasien;
            }

            if (isset($kunjungan->umur) && $kunjungan->umur != '') {
                $kunjungan->umur = str_replace('Tahun', 'thn,', $kunjungan->umur);
                $kunjungan->umur = str_replace('Bulan', 'bln,', $kunjungan->umur);
                $kunjungan->umur = str_replace('Hari', 'hr', $kunjungan->umur);
            }

            $display_dokter = '';
            $tempText = explode(" ", $kunjungan->nama_pegawai);
            $display_dokter = implode("\n", $tempText);
            $print = new DocoPrint();
            error_reporting(0);
            $print->useSubstitutions=false;
            $print->simpleTables = true;
            $print->attributes = [
                '#sep#' => $this->renderPartial($render_partial, get_defined_vars()),
                '#no_pendaftaran#' => !empty($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : null,
                '#poli_tujuan#' => !empty($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : null,
                '#display_dokter#' => $display_dokter

            ];
            // $print->Output(false,'print-sep.pdf','F'); //error unable create file
            $print->Output();
            // return Yii::getLogger()->getElapsedTime();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
     * @method cari data nomor rencana kontrol dan SPRI by nokartu atau tidak
     * @param str $date
     * @param str $tahun
     * @param int $nokartu => kalau bukan cari pasien tertentu tidak perlu di sertakan
     * @param int $filter => isi 1 => pencariannya by tgl entri , isi 2 => pencariannya by tgl rencaka kontrol
     *
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function actionCariDataRencanaKontrolDanSpri()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;

        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');
        $nokartu = $request->get('nokartu');
        $filter = $request->get('filter');

        $result = $model->cariDataRencanaKontrol($bulan,$tahun, $nokartu, $filter);
        return $result;
    }

    public function actionHapusDataRencanaKontrolDanSpri()
    {
        $request = Yii::$app->request;
        $model = new Bpjs;

        $nosurat = $request->get('nosurat');

        $result = $model->hapusRencanaKontrol($nosurat);
        return $result;
    }

    public function actionGetPengajuanSep(){
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pendaftaran = new Pendaftaran;
        $pasien = new Pasien;

        $pendaftaran = $pendaftaran::findOne($pendaftaran_id);
        $pasien = $pasien::findOne($pendaftaran['pasien_id']);

        return [
            'pendaftaran' => $pendaftaran,
            'pasien' => $pasien
        ];
    }

    public function actionCreatePengajuanSep()
    {
        $request = Yii::$app->request;
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $username = $request->post('username');
        $pendaftaran_id = $request->post('pendaftaran_id');
        if (empty($username)) {
            $username = !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : null;
        }
        $pendaftaran = Pendaftaran::findOne($pendaftaran_id);
        $status_pengajuan = false;

        $model = new Bpjs;
        $payload = [
            'noKartu' => $request->post('no_kartu'),
            'tglSep' => $request->post('tgl_sep'),
            'jnsPelayanan' => $request->post('jenis_pelayanan'),
            'jnsPengajuan' => $request->post('jenis_pengajuan'),
            'keterangan' => $request->post('keterangan'),
            'user' => $username
        ];

        $validator = DynamicModel::validateData($payload, [
            [['noKartu', 'tglSep', 'jnsPelayanan', 'jnsPengajuan'], 'required']
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        $result = $model->createPengajuanSep($payload);

        if ($result['metaData']['code'] == 200 || $result['metaData']['code'] == "200"){
            $status_pengajuan = true;
        }

        if ($pendaftaran['is_pengajuan_sep'] == false){
            $additional = [
                'request' => $payload,
                'response' => $result
            ];
            $pendaftaran = Pendaftaran::updateAll([
                'additional_pengajuan_sep' => json_encode($additional),
                'is_pengajuan_sep' => $status_pengajuan
            ],['pendaftaran_id' => $pendaftaran_id]);
        }
        return $result;
    }

    public function getRujukanPostRanap($post, $model) {
        $rujukan = $model->referensiCariSep($post['nomor']);

        if($post['asal_rujukan'] == 2 && $rujukan) {
            if($rujukan['metaData']['code'] != 200) { // error code 200 berhasil
                return $rujukan;
            }

            $rkSep = $model->rencanaKontrolCariSep($post['nomor']);
            if($rkSep['metaData']['code'] != 200) {
                return $rkSep;
            }

            $diagnosa = explode("-", ArrayHelper::getValue($rkSep, 'response.diagnosa'));

            $rujukan['response']['pasien_baru'] = true;
            $rujukan['response']['pasien_sesuai'] = false;
            $rujukan['response']['nama_pasien'] = null;
            $rujukan['response']['lastPoli'] = false;
            $rujukan['response']['dokterDpjp'] = false;
            // $rujukan['response']['rujukan']['tglKunjungan'] = @$rujukan['response']['tglSep'];
            $rujukan['response']['rujukan'] = [
                'tglKunjungan' => @$rujukan['response']['tglSep'],
                'poliRujukan' => @$rujukan['response']['poli'],
                'provPerujuk' => [
                    'kode' => ArrayHelper::getValue($rkSep, 'response.provPerujuk.kdProviderPerujuk'),
                    'nama' => ArrayHelper::getValue($rkSep, 'response.provPerujuk.nmProviderPerujuk'),
                ],
                'diagnosa' => [
                    'kode' => isset($diagnosa[1]) ? rtrim($diagnosa[0]) : "",
                    'nama' => isset($diagnosa[1]) ? $diagnosa[1] : $diagnosa[0],
                ],
                'pelayanan' => [
                    'kode' => 2,
                    'nama' => 'Rawat Jalan',
                ]
            ];


            $noRm = isset($rujukan['response']['peserta']['noMr']) ? $rujukan['response']['peserta']['noMr'] : null;
            $namaPeserta = isset($rujukan['response']['peserta']['nama']) ? $rujukan['response']['peserta']['nama'] : null;
            if($noRm) {
                $qPasien = Pasien::find()->select([
                    'nama_pasien'
                ])->where([
                    'no_rekam_medik' => $noRm
                ])->asArray()->one();

                if (!empty($qPasien)) {
                    $rujukan['response']['pasien_baru'] = false;
                    $namaPasien = isset($qPasien['nama_pasien']) ? $qPasien['nama_pasien'] : null;
                    $rujukan['response']['nama_pasien'] = $namaPasien;
                    if (strtoupper($namaPeserta) === strtoupper($namaPasien)) {
                        $rujukan['response']['pasien_sesuai'] = true;
                    }
                }
            }

            $ppkPelayanan = Cache::getPpkPelayanan();
            $ppkPelayanan_nama = $ppkPelayanan['nama'];
            list($namaPpk, $kotaPpk) = explode(" - ", $ppkPelayanan_nama);
            $ppkPelayanan_kode = $ppkPelayanan['kode'];
            $post_ranap_sep = $post['nomor'];
            $rujukan['response']['ppkPelayananRs_nama'] = $namaPpk;

            $no_peserta = isset($rujukan['response']['peserta']['noKartu']) ? $rujukan['response']['peserta']['noKartu'] : null;
            $peserta = $model->peserta($no_peserta);
            $rujukan['response']['peserta'] = isset($peserta['response']['peserta']) ? $peserta['response']['peserta'] : $rujukan['response']['peserta'];

            $historiPelayanan = $this->service->historyPelayanan($no_peserta);
            $rujukan['response']['lastSep'] = $historiPelayanan;
            $post_ranap = false;
            $dataHistori = [];
            foreach ($historiPelayanan['response']['histori'] as $key => $value) {
                // untuk post ranap harus jenis pelayana = 1 (RANAP) && ppk pelayanan = nama ppk rumah sakit tersebut
                if($namaPpk == $value['ppkPelayanan'] && $value['jnsPelayanan'] == '1') {
                    $dataHistori = $value;
                    $post_ranap = true;
                    break;
                }
            }

            if($post_ranap) { // tidak pake jumlahSEP karena yg dipake noSEP jadi pasti post ranap
                $rujukan['response']['post_ranap'] = [
                    'asal_rujukan' => "",
                    'noSep' => $post_ranap_sep,
                    'ppkPelayanan_nama' => $ppkPelayanan_nama,
                    'ppkPelayanan_kode' => $ppkPelayanan_kode,
                ];

                $rujukan['response']['rujukan']['provPerujuk'] = [
                    'kode' => $ppkPelayanan_kode,
                    'nama' => $ppkPelayanan_nama
                ];
            }


            $rencana_kontrol = $this->service->cariDataRencanaKontrol(date('m'), date('Y'), $no_peserta, 2);
            if (isset($rencana_kontrol['response']['list']) && !empty($rencana_kontrol['response']['list'])) {
                $rencana_kontrol = array_values(array_filter($rencana_kontrol['response']['list'], function($val) {
                        return $val['tglRencanaKontrol'] == date('Y-m-d') && $val['jnsKontrol'] == DocoConstants::RK_JNS_KONTROL_RK;
                }));
            } else {
                $rencana_kontrol = [];
            }
            $rujukan['response']['rencana_kontrol'] = $rencana_kontrol;
        }

        return $rujukan;
    }

    public function actionShowConfigBpjs()
    {
        return (new Bpjs)->showConfig();
    }

    public function actionCariRujukanByKartu($noKartu,$list=false,$type=1)
    {
        return (new Bpjs)->cariRujukanPeserta($noKartu,$list,$type);
    }

    public function actionCariRujukanByNomor($noRujukan)
    {
        return (new Bpjs)->cariRujukan($noRujukan);
    }

    public function actionPencarianFingerprint($noKartu,$tglPelayanan)
    {
        return (new Bpjs)->pencarianFingerprint($noKartu,$tglPelayanan);
    }

    public function actionListPesertaFingerprint($tglPelayanan)
    {
        return (new Bpjs)->listPesertaFingerprint($tglPelayanan);
    }

    public function actionPengajuanSepManual()
    {
        $request = Yii::$app->request;
        $username = $request->post('user');
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        if (empty($username)) {
            $username = !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : null;
        }
        $payload = [
            'noKartu' => $request->post('noKartu'),
            'tglSep' => $request->post('tglSep'),
            'jnsPelayanan' => $request->post('jnsPelayanan'),
            'jnsPengajuan' => $request->post('jnsPengajuan'),
            'keterangan' => $request->post('keterangan'),
            'user' => $username
        ];
        $validator = DynamicModel::validateData($payload, [
            [['noKartu', 'tglSep', 'jnsPelayanan', 'jnsPengajuan','keterangan','user'], 'required'],
            [['keterangan'], 'string','min'=>6],
            [['noKartu'], 'string','length'=>13]
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        return (new Bpjs)->createPengajuanSep($payload);
    }

    public function actionApprovalFingerprint()
    {
        $request = Yii::$app->request;
        $username = $request->post('user');
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        if (empty($username)) {
            $username = !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : null;
        }
        $payload = [
            'noKartu' => $request->post('noKartu'),
            'tglSep' => $request->post('tglSep'),
            'jnsPelayanan' => $request->post('jnsPelayanan'),
            'jnsPengajuan' => $request->post('jnsPengajuan','2'),
            'keterangan' => $request->post('keterangan'),
            'user' => $username
        ];
        $validator = DynamicModel::validateData($payload, [
            [['noKartu', 'tglSep', 'jnsPelayanan', 'jnsPengajuan','keterangan','user'], 'required'],
            [['keterangan'], 'string','min'=>6],
            [['noKartu'], 'string','length'=>13]
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        return (new Bpjs)->approvalPengajuanSep($payload);
    }

    public function actionMonitoringKunjungan($tglSep,$jnsPelayanan)
    {
        return (new Bpjs)->monitoringKunjungan($tglSep,$jnsPelayanan);
    }

    public function actionDataNomorSuratKontrol($tglAwal,$tglAkhir,$filter)
    {
        return (new Bpjs)->dataNomorSuratKontrol($tglAwal,$tglAkhir,$filter);
    }

    public function actionDataPoliRencanaKontrol($jnsKontrol,$nomor,$tglRencanaKontrol)
    {
        return (new Bpjs)->dataPoliRencanaKontrol($jnsKontrol,$nomor,$tglRencanaKontrol);
    }

    public function actionDataDokterRencanaKontrol($jnsKontrol,$kdPoli,$tglRencanaKontrol)
    {
        return (new Bpjs)->dataDokterRencanaKontrol($jnsKontrol,$kdPoli,$tglRencanaKontrol);
    }

    public function actionSuplesiJasaRaharja($noKartu,$tglPelayanan)
    {
        return (new Bpjs)->suplesiJasaRaharja($noKartu,$tglPelayanan);
    }

    public function actionKecelakaanInduk($noKartu)
    {
        return (new Bpjs)->kecelakaanInduk($noKartu);
    }

    public function actionMonitoringJasaRaharja($jnsPelayanan,$tglMulai,$tglAkhir)
    {
        return (new Bpjs)->monitoringJasaRaharja($jnsPelayanan,$tglMulai,$tglAkhir);
    }

    public function actionJumlahSepRujukan($jenis_rujukan,$no_rujukan)
    {
        return (new Bpjs)->jumlahSEP([
            'jenisRujukan' => $jenis_rujukan,
            'noRujukan' => $no_rujukan,
        ]);
    }

    public function actionReferensiKamarAplicare() {
        return (new BpjsAplicare)->referensiKamarAplicare();
    }

    public function actionCekNoRujukanIsUsed()
    {
        $request = Yii::$app->request;
        $bpjs = Bpjs::find()
                  ->select(['pt.pendaftaran_id', 'pm.pasien_id', 'pt.no_pendaftaran', 'pm.nama_pasien', 'bpjs_t.norujukan'])
                  ->leftJoin('pendaftaran_t pt', 'pt.pendaftaran_id = bpjs_t.pendaftaran_id')
                  ->leftJoin('pasien_m pm', 'pm.pasien_id = pt.pasien_id')
                  ->where(['bpjs_t.norujukan' => $request->get('no_rujukan')])
                  ->asArray()->one();
        if ($bpjs) {
            return DocoHelpers::callBack(DocoMessages::KEY_DYNAMIC_STATUS, [
                'status' => 422,
                'title' => 'No Rujukan Sudah Digunakan',
                'text' => 'No Rujukan Sudah Digunakan oleh '. $bpjs['no_pendaftaran']. ' - '. $bpjs['nama_pasien'],
                'data' => $bpjs
            ]);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
          'title' => 'Proses Bisa Dilanjutkan',
          'text' => 'No Rujukan belum pernah dipakai'
        ]);

    }

    public function actionCariRencanaKontrolByNoRencanaKontrol($no_surat_kontrol)
    {
        return (new Bpjs)->cariRencanaKontrolByNoRencanaKontrol($no_surat_kontrol);
    }

    public function actionListRujukanKhusus($bulan, $tahun)
    {
        return (new Bpjs)->cariRujukanKhususByTanggal($bulan, $tahun);
    }

    public function actionListRujukanKhususWithRange()
    {
        $request = \Yii::$app->request;
        $start_date = $request->post('start_date', date('Y-m-d'));
        $max_month_backdate = $request->post('max_month_backdate', 3);
        $is_sync = $request->post('is_sync', false);
        $temp_list_rujukan_khusus = [];
        $messages = [];
        $inc_month = 0;

        while ($inc_month < $max_month_backdate) {
            $bulan = date('m', strtotime('-'.$inc_month.'month', strtotime($start_date)));
            $tahun = date('Y', strtotime('-'.$inc_month.'month', strtotime($start_date)));

            $list_rujukan_khusus = (new Bpjs)->cariRujukanKhususByTanggal($bulan, $tahun);
            
            if (ArrayHelper::getValue($list_rujukan_khusus, 'metaData.code') == 200) {
                $data_rujukan = ArrayHelper::getValue($list_rujukan_khusus, 'response.rujukan', []);
                $this->syncRujukanKhusus($data_rujukan, $start_date, $inc_month);
                $temp_list_rujukan_khusus = array_merge($temp_list_rujukan_khusus, $data_rujukan);
            } else if (ArrayHelper::getValue($list_rujukan_khusus, 'metaData.code') == 201) {
                array_push($messages, '['.ArrayHelper::getValue($list_rujukan_khusus, 'metaData.code').'] '.ArrayHelper::getValue($list_rujukan_khusus, 'metaData.message'));
            }
            
            $inc_month++;
        }

        if ($is_sync) {
            return ['message' => 'Sync Success'];
        } else {
            return [
                    'data' => $temp_list_rujukan_khusus,
                    'message' => implode($messages, ', ')
                ];
        }

    }

    private function syncRujukanKhusus($data, $start_date, $inc_month)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $default_column_insert = ['idrujukan', 'norujukan', 'nokapst', 'nmpst', 'diagppk', 'tglrujukan_awal', 'tglrujukan_berakhir'];
        $condition_start = date('Y-m-01', strtotime('-'.$inc_month.'month', strtotime($start_date)));
        $condition_end = date('Y-m-01', strtotime('-'.($inc_month-1).'month', strtotime($start_date)));

        if (empty($data)) {
            return false;
        }

        try {
            $connection->createCommand('DELETE from bpjs_rujukankhusus_t bt where bt.tglrujukan_awal::date >= \''.$condition_start.'\' and bt.tglrujukan_awal::date < \''.$condition_end.'\'')->execute();
            $connection->createCommand()->batchInsert('bpjs_rujukankhusus_t', $default_column_insert, $data)->execute();
            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollback();
        }
    }
    
    private function getRujukanKhususByNomor($nomor)
    {
        $rujukan_khusus = BpjsRujukanKhususT::find()->where(['norujukan' => $nomor])->orderBy('id', 'desc')->asArray()->one();
        if ($rujukan_khusus && !empty($rujukan_khusus['diagppk'])) {
            $rujukan_khusus['diagnosa'] = Diagnosa::find()->select(['diagnosa_id', 'diagnosa_kode', 'diagnosa_nama', 'diagnosa_namalainnya'])->where(['diagnosa_kode' => $rujukan_khusus['diagppk']])->asArray()->one();
        }
        return $rujukan_khusus;
    }
    
    public function actionGetLastNoSuratKontrol($no_peserta_bpjs)
    {
        return $this->getNoSuratKontrol($no_peserta_bpjs);
    }

    public function actionGetLastHistoryRanap($no_peserta_bpjs)
    {
        return $this->getHistoryPelayanan($no_peserta_bpjs);
    }

    private function getNoSuratKontrol($nokartubpjs, $is_skdp = true)
    {
        // Pengambilan nomor surat kontrol dengan range H-14 sampai H+14 hari
        $list_surat_kontrol = (new Bpjs)->dataNomorSuratKontrol(date('Y-m-d', strtotime('-14 days')), date('Y-m-d', strtotime('+14 days')), 2);
        $jnsKontrol = $is_skdp ? 2 : 1;

        $list_surat_kontrol = array_values(array_filter(ArrayHelper::getValue($list_surat_kontrol, 'response.list', []), function ($var) use ($jnsKontrol, $nokartubpjs) {
                return ($var['jnsKontrol'] == $jnsKontrol && $var['noKartu'] == $nokartubpjs);
            }));

        // sorting surat kontrol by tglRencanaKontrol Desc
        usort($list_surat_kontrol, function ($element1, $element2) {
                $datetime1 = strtotime($element1['tglRencanaKontrol']);
                $datetime2 = strtotime($element2['tglRencanaKontrol']);
                return $datetime2 - $datetime1;
            });
        return ArrayHelper::getValue($list_surat_kontrol, '0', null);
    }

    private function getLastSepHistoryRanap($nokartubpjs)
    {
      $history = (new Bpjs)->historyPelayananPasien($nokartubpjs);
      $ppkPelayanan = Cache::getPpkPelayanan();

      $ppkPelayanan_nama = ArrayHelper::getValue($ppkPelayanan, 'nama', null);
      list($namaPpk, $kotaPpk) = explode(" - ", $ppkPelayanan_nama);

      // $history = array_values(array_filter(ArrayHelper::getValue($history, 'response.histori', []), function ($var) use ($namaPpk) {
      //     return (ArrayHelper::getValue($var, 'ppkPelayanan', '') == $namaPpk && ArrayHelper::getValue($var, 'jnsPelayanan') == 1);
      // }));
      $history = ArrayHelper::getValue($history, 'response.histori', []);


      // // sorting histori pelayanan by tglSep Desc
      usort($history, function ($element1, $element2) {
              $datetime1 = strtotime($element1['tglPlgSep']);
              $datetime2 = strtotime($element2['tglPlgSep']);
              return $datetime2 - $datetime1;
      });

      $lastHistorySep = isset($history[0]) ? $history[0] : [];

      return (!empty($lastHistorySep) && $lastHistorySep['ppkPelayanan'] == $namaPpk && $lastHistorySep['jnsPelayanan'] == 1) ?
              $lastHistorySep
              : [];
    }

    public function actionGetRujukanBySurkon($noSuratKontrol)
    {
        $getSurkon = (new Bpjs)->cariRencanaKontrolByNoRencanaKontrol($noSuratKontrol);
        $getNoRujukan = ArrayHelper::getValue($getSurkon, 'response.sep.provPerujuk.noRujukan', null);
        $getNoSep = ArrayHelper::getValue($getSurkon, 'response.sep.noSep', null);
        $getAsalRujukan = ArrayHelper::getValue($getSurkon, 'response.sep.provPerujuk.asalRujukan', null);
        $getBpjsData = (new Bpjs)->cariRujukan($getNoRujukan);
        // double prevent
        if (isset($getBpjsData['metaData']['code']) && !empty($getBpjsData['metaData']['code']) && $getBpjsData['metaData']['code'] != 200) {
            $rujukan = (new Bpjs)->referensiCariSep($getNoSep);
            if($rujukan['metaData']['code'] != 200) { // error code 200 berhasil
                $bpjsMessage = isset($rujukan['metaData']['message']) && !empty($rujukan['metaData']['message']) ? $rujukan['metaData']['message'] : 'Rujukan Tidak Ada';
                $this->responseMessage = $bpjsMessage. ', ' . $this->responseMessage;
                return null;
            }
            $rkSep = (new Bpjs)->rencanaKontrolCariSep($getNoSep);
            if($rkSep['metaData']['code'] != 200) {
                $bpjsMessage = isset($rkSep['metaData']['message']) && !empty($rkSep['metaData']['message']) ? $rkSep['metaData']['message'] : 'Rujukan Tidak Ada';
                $this->responseMessage = $bpjsMessage. ', ' . $this->responseMessage;
                return null;
            }
            $diagnosa = explode("-", ArrayHelper::getValue($rkSep, 'response.diagnosa'));
			$getBpjsData = [
                'response' => [
                    'asalFaskes' => ArrayHelper::getValue($getSurkon, 'response.sep.provPerujuk.asalRujukan', null),
                    'rujukan' => [
                        'tglKunjungan' => ArrayHelper::getValue($getSurkon, 'response.tglRencanaKontrol', null),
                        'provPerujuk' => [
                            'kode' => ArrayHelper::getValue($rkSep, 'response.provPerujuk.kdProviderPerujuk', null),
                            'nama' => ArrayHelper::getValue($rkSep, 'response.provPerujuk.nmProviderPerujuk', null),
                        ],
                        'diagnosa' => [
                            'kode' => isset($diagnosa[1]) ? rtrim($diagnosa[0]) : "",
                            'nama' => isset($diagnosa[1]) ? $diagnosa[1] : $diagnosa[0],
                        ],
                        'peserta' => [
                        'hakKelas' => [
                            'kode' => ArrayHelper::getValue($getSurkon, 'response.sep.peserta.hakKelas', null)
                        ]
                        ]
                    ]
                ]
            ];
        }
        return $getBpjsData;
    }
}
