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
use app\modules\v1\models\SyKunjungan;

class AllowBpjsController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';
    public $conf, $vclaim = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
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
        $conf = @parse_ini_file('' . realpath(Yii::$app->basePath) . '/config/env/.env', true);
        $this->vclaim = isset($conf['inacbg']['env_vclaim']) ? $conf['inacbg']['env_vclaim'] : '';
        $env = ($this->vclaim == DocoConstants::LOOKUP_BPJS_LIVE) ? DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new Bpjs;
            $no_sep = $post['nosep'];
            $peserta = [];
            if ($env == DocoConstants::LOOKUP_BPJS_LIVE) {
                $peserta = $model->cekbridging($no_sep);
            } else {
                $peserta = $model->findSep($no_sep);
            }
            if ($peserta['metaData']['code'] == 200) {
                /**
                 * @function : Cek Integrasi SEP dan Inacbg
                 */
                if ($env == DocoConstants::LOOKUP_BPJS_LIVE) {
                    $no_kartu = $peserta['response']['pesertasep']['noKartuBpjs'] ? $peserta['response']['pesertasep']['noKartuBpjs'] : '';
                    $no_mr = $peserta['response']['pesertasep']['noMr'] ? $peserta['response']['pesertasep']['noMr'] : '';
                    $hak_kelas_kode = '';

                    $peserta['response']['pesertasep']['kelasKode'] = '';
                    $detail = $model->peserta($no_kartu, date('Y-m-d'), false);
                    if ($detail) {
                        $peserta['response']['pesertasep']['kelasKode'] = $detail['response']['peserta']['hakKelas']['kode'];
                    }
                    $peserta['response']['islive'] = 'true';
                } else {
                    /**
                     * @function : Cari SEP 
                     */

                    $no_kartu = $peserta['response']['peserta']['noKartu'] ? $peserta['response']['peserta']['noKartu'] : '';
                    $no_mr = $peserta['response']['peserta']['noMr'] ? $peserta['response']['peserta']['noMr'] : '';
                    $hak_kelas_kode = '';

                    $peserta['response']['peserta']['kelasKode'] = '';
                    $detail = $model->peserta($no_kartu, date('Y-m-d'), false);

                    if ($detail) {
                        $peserta['response']['peserta']['kelasKode'] = $detail['response']['peserta']['hakKelas']['kode'];
                    }
                    $peserta['response']['islive'] = 'false';
                }

                $peserta['response']['duplikasi'] = $this->cekSep($no_sep);
            }
            return $peserta;
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

    public function actionRujukan()
    {
        $request = Yii::$app->request;

        $post = $request->post();
        $model = new Bpjs;
        $rujukan = $model->rujukan($post['nomor'], $post['asal_rujukan']);
        if ($rujukan) {
            if ($rujukan['metaData']['code'] != 200) {
                return $rujukan;
            }
            $no_peserta = $rujukan['response']['rujukan']['peserta']['noKartu'];

            // cari peserta dengan tanggal sesuai form
            $tempPeserta = $model->peserta($no_peserta, $post['tglSEP'], false);
            if ($tempPeserta['metaData']['code'] == 200) {
                $rujukan['response']['rujukan']['peserta'] = $tempPeserta['response']['peserta'];
            }

            $tgl_mulai = date('Y-m-d', strtotime('-3 month'));
            $tgl_selesai = date('Y-m-d');
            $params = [];
            $params['no_kartu'] = $no_peserta;
            $params['tgl_mulai'] = $tgl_mulai;
            $params['tgl_selesai'] = $tgl_selesai;

            $rujukan['response']['lastPoli'] = false;
            $rujukan['response']['dokterDpjp'] = false;
            // get history pelayanan dan list dokter dpjp berdasarkan history pelayanan terakhir
            $historiPelayanan = $this->actionHistoriPelayanan($params);
            $rujukan['response']['lastSep'] = $historiPelayanan;

            $no_sep_histori = '';
            if ($historiPelayanan['response']['histori']) {
                // return $historiPelayanan['response']['histori'];
                foreach ($historiPelayanan['response']['histori'] as $hist) {
                    if (strtoupper($hist['noRujukan']) == strtoupper($post['nomor'])) {
                        $lastPoli = $this->actionReferensiPoli(trim($hist['poli']));
                        $poliKode = "";
                        if (isset($lastPoli['response']) && isset($lastPoli['response']['poli'])) {
                            foreach ($lastPoli['response']['poli'] as $each) {
                                if ($each['nama'] == $hist['poli']) {
                                    $poliKode = $each['kode'];
                                    $rujukan['response']['lastPoli'] = $poliKode; //$lastPoli['response']['poli'][0]['kode'];

                                    $pelayanan = $rujukan['response']['rujukan']['pelayanan']['kode'];
                                    $tglsep = $hist['tglSep'];
                                    $no_sep_histori = $hist['noSep'];
                                    $spesialis = $poliKode; //$lastPoli['response']['poli'][0]['kode'];
                                    $rujukan['response']['param'] = $pelayanan . ' ' . $tglsep . ' ' . $spesialis;
                                    $rujukan['response']['dokterDpjp'] = $this->actionReferensiDokter($pelayanan, $tglsep, $spesialis);

                                    // break foreach
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            // get pasien_id
            $no_rekam_medik = $rujukan['response']['rujukan']['peserta']['mr']['noMR'];
            $rujukan['response']['pasien_id'] = null;
            if ($no_rekam_medik) {
                $pasien = PasienV::find()->where(['no_rekam_medik' => $no_rekam_medik])
                    ->asArray()->one();
                $rujukan['response']['pasien_id'] = $pasien ? $pasien['pasien_id'] : null;
            }

            $rujukan['response']['no_skdp'] = '';
            if ($no_sep_histori) {
                $bpjsHist = Bpjs::find()
                    ->where(['nosep' => $no_sep_histori])
                    ->one();
                if ($bpjsHist) {
                    $rujukan['response']['no_skdp'] = $bpjsHist->no_skdp;
                } else {
                    $rujukan['response']['no_skdp'] = '';
                }
            } else {
                $rujukan['response']['no_skdp'] = '';
            }
        }
        return $rujukan;
    }

    public function actionReferensiPoli($q = null)
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
            $param = $request->post()['q'];
            $faskes = $request->post()['faskes'];
            $model = new Bpjs;
            $result = $model->referensiFaskes($param, $faskes);
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

    public function actionReferensiDokter($pelayanan = null, $tglSep = null, $spesialis = null)
    {
        try {
            // $request = Yii::$app->request;
            // return $request->get();
            // $q = $request->get('q');
            // $pelayanan = $request->get('pelayanan');
            // $tglSep = $request->get('tglsep');
            $tglsep = date('Y-m-d', strtotime($tglSep));
            // $spesialis = $request->get('spesialis');

            // cibabat case, munculin dokter penyakit dalam kalau poli = hemodialisa
            if ($spesialis == 'HDL') {
                $spesialis = 'INT';
            }
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
            $result = $model->historiPelayanan($nokartu, $tglmulai, $tglselesai);
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
            foreach ($model->arr_sep as $key => $each) {
                $t_sep[$each] = $post[$each];
            }
            $model->t_sep = $t_sep;
            $result = $model->createSep();
            $saved = false;
            if ($result['metaData']['code'] == 200) {
                $model->setManualAttribute();
                $model->nosep = $result['response']['sep']['noSep'];
                $model->additional_data = json_encode($result['response']);
                $saved = $model->save() ? $model : false;
            }

            // save to rujukan
            $rujukan = new Rujukan;
            $rujukan->asalrujukan_id = 2; // wip
            $diagnosa = new Diagnosa;
            $diagnosa = $diagnosa->find()->where(['diagnosa_kode' => $t_sep['diagAwal']])->asArray()->one();
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

    public function actionCreateSepNew()
    {
        try {
            $bpjsForm = Yii::$app->request->post();

            $model = new Bpjs;
            $t_sep_new = [];

            $t_sep_new['noKartu'] = $bpjsForm['no_kartu'];
            $t_sep_new['tglSep'] = $bpjsForm['tanggal_sep'];
            $t_sep_new['jnsPelayanan'] = $bpjsForm['jenis_pelayanan'];
            $t_sep_new['klsRawat'] = $bpjsForm['kelas_rawat'];
            $t_sep_new['noMR'] = $bpjsForm['no_rekam_medik'];
            $t_sep_new['asalRujukan'] = $bpjsForm['asal_rujukan'];
            $t_sep_new['tglRujukan'] = $bpjsForm['tanggal_rujukan'];
            $t_sep_new['noRujukan'] = $bpjsForm['no_rujukan'];
            $t_sep_new['ppkRujukan'] = $bpjsForm['ppk_rujukan'];
            $t_sep_new['catatan'] = $bpjsForm['catatan_sep'];
            $t_sep_new['diagAwal'] = $bpjsForm['diagnosa_awal'];
            $t_sep_new['tujuan'] = $bpjsForm['poli_tujuan'];
            $t_sep_new['eksekutif'] = $bpjsForm['poli_eksekutif'];
            $t_sep_new['cob'] = $bpjsForm['cob'];
            $t_sep_new['katarak'] = $bpjsForm['katarak'];
            $t_sep_new['lakaLantas'] = $bpjsForm['kasus_kecelakaan'];



            if (!$bpjsForm['status_suplesi']) {
                $penjamin = '';
                if ($bpjsForm['kasus_kecelakaan'] == '1') { // kecekalaan lalu lintas dan bukan kecelakaan kerja
                    $penjamin = '1';
                } elseif ($bpjsForm['kasus_kecelakaan'] == '2') {
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
            $t_sep_new['kdPropinsi'] = isset($bpjsForm['kode_provinsi']) ? $bpjsForm['kode_provinsi'] : "";
            $t_sep_new['kdKabupaten'] = isset($bpjsForm['kode_kabupaten']) ? $bpjsForm['kode_kabupaten'] : "";
            $t_sep_new['kdKecamatan'] = isset($bpjsForm['kode_kecamatan']) ? $bpjsForm['kode_kecamatan'] : "";
            $t_sep_new['noSurat'] = $bpjsForm['no_surat_kontrol'];
            $t_sep_new['kodeDPJP'] = isset($bpjsForm['kode_dpjp']) ? $bpjsForm['kode_dpjp'] : '';
            $t_sep_new['noTelp'] = $bpjsForm['no_telp'];
            $t_sep_new['user'] = $bpjsForm['user'];
            $model->t_sep_new = $t_sep_new;

            $result = $model->createSepNew();

            $saved = false;
            if ($result['metaData']['code'] == 200) {
                $model->tglsep = $t_sep_new['tglSep'];
                $model->nosep = $result['response']['sep']['noSep'];
                $model->nokartuasuransi = $t_sep_new['noKartu'];
                $model->tglrujukan = $t_sep_new['tglRujukan'];
                $model->norujukan = $t_sep_new['noRujukan'];
                $model->ppkrujukan = $t_sep_new['ppkRujukan'];
                $model->ppkpelayanan = $model->ppkPelayanan;
                $model->jnspelayanan = $t_sep_new['jnsPelayanan'];
                $model->catatansep = $t_sep_new['catatan'];
                $model->diagnosaawal = $t_sep_new['diagAwal'];
                $model->politujuan = $t_sep_new['tujuan'];
                $model->klsrawat = $t_sep_new['klsRawat'];
                $model->nama_peserta = $result['response']['sep']['peserta']['nama'];
                $model->lakalantas = $t_sep_new['lakaLantas'];
                $model->additional_data = json_encode($result['response']);
                $model->additional_request = $model->t_sep_new;
                $saved = $model->save() ? $model : $model->errors;

                // if ($saved) {
                //     // save to rujukan
                //     $rujukan = new Rujukan;
                //     $rujukan->asalrujukan_id = 2; // wip
                //     $diagnosa = new Diagnosa;
                //     $diagnosa = $diagnosa->find()->where(['diagnosa_kode'=>$t_sep_new['diagAwal']])->asArray()->one();
                //     $rujukan->diagnosa_id = $diagnosa ? $diagnosa['diagnosa_id'] : null;
                //     $rujukan->no_rujukan = $t_sep_new['noRujukan'];
                //     $rujukan->tanggal_rujukan = date('Y-m-d', strtotime($t_sep_new['tglRujukan']));
                //     $rujukan->kodediagnosa_rujukan = $t_sep_new['diagAwal'];
                //     $rujukan->save();
                // }
            }

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
                    $errors = DocoHelpers::parseError($model->errors, 'BpjsForm');
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

    /**
     * @controller actionPrintSep
     * @attribute #sep# => layout SEP
     *
     **/
    public function actionPrintSep()
    {
        try {
            $model = new InfKunjunganRsView;
            $modelBpjs = new Bpjs;
            $modelPendaftaran = new Pendaftaran;
            $tgl_cetak = date('Y-m-d H:i:s');

            $request = Yii::$app->request;
            $pasien_id = $request->get('pasien_id', null);
            $pendaftaran_id = $request->get('pendaftaran_id', null);

            $tipePasien = $modelPendaftaran->find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();

            $kunjungan = $model::find()
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->orderBy('tgl_pendaftaran DESC')
                ->one();
            if (!$kunjungan) {
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $bpjs = $modelBpjs::findOne($kunjungan->bpjs_id);
            if (empty($bpjs)) {
                throw new \yii\web\NotFoundHttpException("Data BPJS Tidak Ditemukan", 404);
            }
            $getDataBpjs = $modelBpjs->peserta($bpjs->nokartuasuransi, date('Y-m-d', strtotime($bpjs->tglsep)));
            // update cetakan_ke & tgl_cetak
            $bpjs->cetakan_ke += 1;
            $bpjs->tgl_cetak = $tgl_cetak;
            $bpjs->save(false);
            $detailBpjs = $modelBpjs->findSep($kunjungan->nosep);
            $detailBpjs = $detailBpjs['response'];

            $print = new DocoPrint();
            $print->attributes = [
                '#sep#' => $this->renderPartial('print_sep', get_defined_vars()),

            ];
            $print->Output();
        } catch (Exception $e) {
            return ['Error' => $e->getMessage()];
        }
    }
    public function actionGetPropinsiBpjs()
    {
        $model = new Bpjs;
        return $model->getPropinsi();
    }
    public function actionGetKabupatenBpjs($kodepropinsi)
    {
        $model = new Bpjs;
        return $model->getKabupaten($kodepropinsi);
    }
    public function actionGetKecamatanBpjs($kodekabupaten)
    {
        $model = new Bpjs;
        return $model->getKecamatan($kodekabupaten);
    }
    public function actionGetPesertaRujukan($asal_rujukan = 1, $nokartu = null)
    {
        $model = new Bpjs;
        return $model->getPesertaRujukan($asal_rujukan, $nokartu);
    }
    public function actionGetHeader()
    {
        $model = new Bpjs;
        return $model->getHeader2();
    }

    public function actionPotensiSuplesi()
    {
        try {
            $request = Yii::$app->request;
            $nokartu = $request->post()['no_kartu'];
            $tglSep = $request->post()['tgl_sep'] ? date('Y-m-d', strtotime($request->post()['tgl_sep'])) : null;

            $model = new Bpjs;
            $result = $model->potensiSuplesi($nokartu, $tglSep);
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

    public function actionPengajuanSep()
    {
        try {
            $request = Yii::$app->request;
            $model = new Bpjs;
            if ($request->post()) {
                $model->attributes = $request->post();
                $model->tglsep = $model->tglsep . ' ' . date('H:i:s');

                $model->pengajuanSep();
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
     * @controller actionPrintSepManual
     * @attribute #sep# => layout SEP
     *
     **/
    public function actionPrintSepManual()
    {
        try {
            $request = Yii::$app->request;
            $print = $request->get('print', false);
            $no_sep = $request->get('no_sep');
            $no_pendaftaran = $request->get('no_pendaftaran', '-');
            $no_rekam_medik = $request->get('no_rekam_medik', '-');
            $tgl_cetak = date('Y-m-d H:i:s');
            $kunjungan = [];
            $tipePasien = ['pasienadmisi_id' => false];

            $bpjs = new Bpjs;
            $detailBpjs = $bpjs->findSep($no_sep);
            if (empty($detailBpjs['response'])) {
                return [
                    'status' => 500,
                    'message' => 'Data Tidak Ditemukan',
                    'title' => 'Cetak SEP Gagal!'
                ];
            }
            if (!$print) {
                return [
                    'status' => 200,
                    'message' => 'Data Ditemukan',
                    'title' => 'Cetak SEP Bisa Dilakukan!',
                    'nosep' => $no_sep
                ];
            }
            $detailBpjs = $detailBpjs['response'];
            $getDataBpjs = $bpjs->peserta($detailBpjs['peserta']['noKartu'], date('Y-m-d', strtotime($detailBpjs['tglSep'])));
            $print = new DocoPrint();
            $print->attributes = [
                '#sep#' => $this->renderPartial('print_sep_manual', get_defined_vars()),
            ];
            $print->Output(false, 'SEP Manual');
        } catch (Exception $e) {
            return ['Error' => $e->getMessage()];
        }
    }

    /**
     * @todo cek sep ke kunjungan 
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    private function cekSep($nosep)
    {
        $hasil = [];
        $cari = SyKunjungan::find()->where(['no_sep' => $nosep])->count();
        if ($cari > 0) {
            $hasil = SyKunjungan::find()->select(['no_pendaftaran', 'no_rekammedik', 'nama_pasien'])->where(['no_sep' => $nosep])->asArray()->one();
        } else {
            $hasil = false;
        }
        return $hasil;
    }
}
