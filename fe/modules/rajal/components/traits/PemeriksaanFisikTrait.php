<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-21 10:28:31
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-02 17:10:57
 * @Description:
 */

namespace app\modules\rajal\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\rajal\models\AsesmenMedisForm;
use app\modules\rajal\models\PemeriksaanFisikForm;
use app\modules\rajal\models\BodyMassIndex;
use app\components\Services\AksesFormService;
use app\modules\rajal\models\PemeriksaanAskepForm;
use app\modules\rajal\models\PemeriksaanFisikSpesialisForm;
use app\modules\rajal\models\PemeriksaanMataForm;
use app\modules\rajal\models\PemeriksaanThtForm;
use app\modules\rajal\models\PemeriksaanGigiForm;
use app\modules\rajal\models\PemeriksaanObgynForm;

trait PemeriksaanFisikTrait
{
    public function actionPeriksaFisik()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        try {
            $user = Yii::$app->session->get('user_identity');
            $isDokter = $user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS;
            $konsulPoliId = $request->get('konsulpoli_id');
            $mapingRuangan = $this->mapingRuanganSpesialis();
            $pathDefault = '__fisik';
            $path = $mapingRuangan ? $mapingRuangan : $pathDefault;
            $list_data_support = $this->getListData();
            $list_data = $this->getSupportDataFisik();
            $data_gcs = isset($list_data['data_gcs']) ? $list_data['data_gcs'] : [];
            $gcs_list_eye = isset($list_data['data_listgcs']['eye'])
                ? $list_data['data_listgcs']['eye'] : [];
            $gcs_list_verbal = isset($list_data['data_listgcs']['verbal'])
                ? $list_data['data_listgcs']['verbal'] : [];
            $gcs_list_motorik = isset($list_data['data_listgcs']['motorik'])
                ? $list_data['data_listgcs']['motorik'] : [];

            $gcsEyeOptions = [];
            $gcsVerbalOptions = [];
            $gcsMotorikOptions = [];
            if ($gcs_list_eye) {
                foreach ($gcs_list_eye as $keyEye => $valueEye) {
                    $gcsEyeOptions[$valueEye['metodegcs_id']]['data-nilai'] = $valueEye['metodegcs_nilai'];
                }
            }
            if ($gcs_list_verbal) {
                foreach ($gcs_list_verbal as $keyVerbal => $valueVerbal) {
                    $gcsVerbalOptions[$valueVerbal['metodegcs_id']]['data-nilai'] = $valueVerbal['metodegcs_nilai'];
                }
            }
            if ($gcs_list_motorik) {
                foreach ($gcs_list_motorik as $keyMotorik => $valueMotorik) {
                    $gcsMotorikOptions[$valueMotorik['metodegcs_id']]['data-nilai'] = $valueMotorik['metodegcs_nilai'];
                }
            }

            $data_bmi = json_encode($list_data['data_bmi']);
            $data_tekanandarah = json_encode($list_data['data_tekanandarah']);
            $list_perawat = $list_data_support['data_perawat'];
            $jeniskelamin = $this->_jeniskelamin;
            $data_nama_dokter = isset($this->_data_pasien['nama_pegawai']) ? $this->_data_pasien['nama_pegawai'] : '';
            $data_pasien_umur = !empty($this->_data_pasien['umur']) ? $this->_data_pasien['umur'] : '0 tahun 0 bulan 1 hari';
            $umur = DocoHelpers::getUmur($data_pasien_umur, false, true);
            $umur = json_encode($umur, JSON_FORCE_OBJECT);
            $optBagianTubuh = $list_data['data_bagiantubuh'];
            if (!is_numeric($pendaftaran_id)) {
                $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
            }
            $pasien_id = isset($this->_data_pasien['pasien_id']) ? $this->_data_pasien['pasien_id'] : '';
            // Get pemeriksaan fisik
            $responsePemeriksaanFisik = $this->_restRajal->get('tra-pemeriksaan/get-pemeriksaan-fisik?pendaftaranId=' . $pendaftaran_id . '&pasienId=' . $pasien_id);
            $model = json_decode($responsePemeriksaanFisik->getBody(), true);
            $model = ArrayHelper::getValue($model, 'response', []);
            $dataPasien = ArrayHelper::getValue($model, 'pasien', []);
            $modelAnamnesa = ArrayHelper::getValue($model, 'model_anamnesa');
            $enable_edit = !empty($model["enable_pulang"]) ? $model["enable_pulang"] : false;
            $resAnatomiTubuh = isset($model["anatomi_tubuh"]) ? $model["anatomi_tubuh"] : [];
            $model = ArrayHelper::getValue($model, 'model', []);
            $dataAnatomi = json_encode($resAnatomiTubuh, JSON_FORCE_OBJECT);
            $counter = count($resAnatomiTubuh) + 1;
            $pemeriksaanFisikId = ArrayHelper::getValue($model, 'pemeriksaanfisik_id');
            $tekananDarah = ArrayHelper::getValue($model, 'tekanandarah');

            $isSpesialis = ($path && $path != $pathDefault) ? true : false;

            // Pemeriksaan fisik form
            $mapingModel = $this->mapingModelSpesialis($path);
            $modelFisik = ArrayHelper::getValue($mapingModel, 'model');
            $modelName = ArrayHelper::getValue($mapingModel, 'modelName');
            $tdSystolic = $tdDiastolic = '';

            // form default
            if(!$isSpesialis) {
                // Load from database
                $modelFisik->load($model, '');
                $modelFisik->gcs_is_kapitis = isset($model['is_kapitis']) ? $model['is_kapitis'] : null;
                
                if ($tekananDarah) {
                    $td = explode("/", $tekananDarah);
                    if (isset($td[0]) && isset($td[1])) {
                        $model['td_systolic'] = $td[0];
                        $model['td_diastolic'] = $td[1];
                        
                    }
                } else if (isset($modelAnamnesa['tekanandarah'])) {
                    $td = explode("/", $modelAnamnesa['tekanandarah']);
                    if (isset($td[0]) && isset($td[1])) {
                        $model['td_systolic'] = $td[0];
                        $model['td_diastolic'] = $td[1];
                    }
                }

                $tdSystolic = ArrayHelper::getValue($model, 'td_systolic');
                $tdDiastolic = ArrayHelper::getValue($model, 'td_diastolic');
            }
            // form spesialis
            else {
                $modelAskep = new PemeriksaanAskepForm;
                $dataFisik = $dataAskep = [];
                $pemeriksaanFisikSpesialis = ArrayHelper::getValue($model, 'pemeriksaan_spesialis', []);
                if ($pemeriksaanFisikSpesialis) {
                    $pemeriksaanFisikSpesialis = json_decode($pemeriksaanFisikSpesialis, true);
                    $dataAskep = ArrayHelper::getValue($pemeriksaanFisikSpesialis, 'askep', []);
                    if (isset($pemeriksaanFisikSpesialis[$path])) {
                        $dataFisik = $pemeriksaanFisikSpesialis[$path];
                    }

                    if (isset($dataAskep['tekanandarah']) && !empty($dataAskep['tekanandarah'])) {
                        $tekananDarah = ArrayHelper::getValue($dataAskep, 'tekanandarah');
                    }
                }

                if(isset($dataAskep['nama_dokter']) && !empty($dataAskep['nama_dokter'])) {
                    $data_nama_dokter = ArrayHelper::getValue($dataAskep, 'nama_dokter');
                }

                $keadaanUmum = ArrayHelper::getValue($model, 'keadaanumum');
                if(isset($dataAskep['keadaanumum']) && !empty($dataAskep['keadaanumum'])) {
                    $keadaanUmum = ArrayHelper::getValue($dataAskep, 'keadaanumum');
                }

                if (!empty($tekananDarah)) {
                    $tekananDarahExp = explode('/', $tekananDarah);
                    $tdSystolic = ArrayHelper::getValue($tekananDarahExp, 0);
                    $tdDiastolic = ArrayHelper::getValue($tekananDarahExp, 1);
                }

                if(isset($dataAskep['td_systolic']) && !empty($dataAskep['td_systolic'])) {
                    $tdSystolic = ArrayHelper::getValue($dataAskep, 'td_systolic');
                }

                if(isset($dataAskep['td_diastolic']) && !empty($dataAskep['td_diastolic'])) {
                    $tdDiastolic = ArrayHelper::getValue($dataAskep, 'td_diastolic');
                }
                

                $modelFisik->attributes = $dataFisik;
                $modelAskep->attributes = $dataAskep;
                $modelAskep->keadaanumum = $keadaanUmum;
                $modelAskep->pernapasan = $this->getDefaultAskep($dataAskep, $modelAnamnesa, 'pernapasan');
                $modelAskep->td_systolic = $tdSystolic;
                $modelAskep->td_diastolic = $tdDiastolic;
                $modelAskep->beratbadan_kg = $this->getDefaultAskep($dataAskep, $modelAnamnesa, 'beratbadan_kg');
                $modelAskep->tinggibadan_cm = $this->getDefaultAskep($dataAskep, $modelAnamnesa, 'tinggibadan_cm');
                $modelAskep->detaknadi = $this->getDefaultAskep($dataAskep, $modelAnamnesa, 'detaknadi');
                $modelAskep->suhutubuh = $this->getDefaultAskep($dataAskep, $modelAnamnesa, 'suhutubuh');
                $modelAskep->bb_ideal = $this->getDefaultAskep($dataAskep, $modelAnamnesa, 'bb_ideal');
                $modelFisik->pendaftaran_id = $pendaftaran_id;
                $modelFisik->pasien_id = $pasien_id;
                $modelAskep->nama_dokter = $data_nama_dokter;
            }

            // Load ID manually
            $modelFisik->pemeriksaanfisik_id = $pemeriksaanFisikId;
            
            // Check post
            if ($post = $request->post()) {
                $fisikForm = ArrayHelper::getValue($post, $modelName, []);
                $askepForm = ArrayHelper::getValue($post, 'PemeriksaanAskepForm', []);
                $perawatId = ArrayHelper::getValue($askepForm, 'pegawaiperawat_id');
                
                if(!$isSpesialis) {
                    $modelFisik->attributes = $fisikForm;
                    $tdSystolic = ArrayHelper::getValue($fisikForm, 'td_systolic');
                    $tdDiastolic = ArrayHelper::getValue($fisikForm, 'td_diastolic');
                    $beratBadan = ArrayHelper::getValue($fisikForm, 'beratbadan_kg');
                    $tinggiBadan = ArrayHelper::getValue($fisikForm, 'tinggibadan_cm');
                    $suhuTubuh = ArrayHelper::getValue($fisikForm, 'suhutubuh');
                    $imt = ArrayHelper::getValue($fisikForm, 'imt');
                    $bbIdeal = ArrayHelper::getValue($fisikForm, 'bb_ideal');
                    $meanarteripressure = ArrayHelper::getValue($fisikForm, 'meanarteripressure');

                    $beratBadan = !empty($beratBadan) ? str_replace(',', '.', $beratBadan) : null;
                    $tinggiBadan = !empty($tinggiBadan) ? str_replace(',', '.', $tinggiBadan) : null;
                    $suhuTubuh = !empty($suhuTubuh) ? str_replace(',', '.', $suhuTubuh) : null;
                    $imt = !empty($imt) ? str_replace(',', '.', $imt) : null;
                    $bbIdeal = !empty($bbIdeal) ? str_replace(',', '.', $bbIdeal) : null;
                    $meanarteripressure = !empty($meanarteripressure) ? str_replace(',', '.', $meanarteripressure) : null;

                    $tglPeriksaFisik = ArrayHelper::getValue($model, 'tglperiksafisik');
                    $tglPeriksaFisik = date('Y-m-d H:i:s', strtotime($tglPeriksaFisik));

                    $modelFisik->tglperiksafisik = $tglPeriksaFisik;
                    $modelFisik->suhutubuh = $suhuTubuh;
                    $modelFisik->beratbadan_kg = $beratBadan;
                    $modelFisik->tinggibadan_cm = $tinggiBadan;
                    $modelFisik->imt = $imt;
                    $modelFisik->bb_ideal = $bbIdeal;
                    $modelFisik->meanarteripressure = $meanarteripressure;
                    $modelFisik->tekanandarah = $tdSystolic.'/'.$tdDiastolic;
                    $modelFisik->td_systolic = $tdSystolic;
                    $modelFisik->td_diastolic = $tdDiastolic;
                    if (empty($modelFisik->nama_dokter)) {
                        $modelFisik->nama_dokter = $data_nama_dokter;
                    }
                }
                else {
                    $modelFisik->ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
                    $modelFisik->konsulpoli_id = DocoHelpers::decrypt($konsulPoliId);
                    $modelFisik->tglperiksafisik = date('Y-m-d H:i:s');
                    $modelAskep->tglperiksafisik = date('Y-m-d H:i:s');
                    $modelFisik->pegawaiperawat_id = $perawatId;
                    $modelAskep->pegawaiperawat_id = $perawatId;
                    $modelAskep->attributes = $askepForm;
                    $modelFisik->attributes = $fisikForm;
                    $pemeriksaanSpesialis = [
                        'ruangan_id' => Yii::$app->docoVars->workspace('ruangan_id'),
                        'askep' => $modelAskep->attributes,
                        $path => $modelFisik->attributes
                    ];
                    $modelFisik->pemeriksaan_spesialis = json_encode($pemeriksaanSpesialis);
                }
                if ($modelFisik->validate()) {
                    $response = $this->_restRajal->post('tra-pemeriksaan/create-fisik', [
                        'form_params' => $modelFisik->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    Yii::$app->cache->delete($this->_pegawai_id . '-latest-data-asesmen-medis-rajal-' . DocoHelpers::encrypt($modelFisik->pendaftaran_id));
                    Yii::$app->cache->delete($this->_pegawai_id . '-updated-data-asesmen-medis-rajal-' . DocoHelpers::encrypt($modelFisik->pendaftaran_id));

                    return DocoHelpers::response($response, false, true);
                } else {
                    return DocoHelpers::response($modelFisik->errors, 422, $modelName);
                }
            }
            $status_update = $this->getStatusPeriksa($pendaftaran_id);
            if (!$isSpesialis) {
                if (!empty($modelFisik->suhutubuh)) {
                    $modelFisik->suhutubuh = str_replace('.', ',', $modelFisik->suhutubuh);
                } else if (!empty($modelAnamnesa["suhutubuh"])) {
                    $modelFisik->suhutubuh = str_replace('.', ',', $modelAnamnesa["suhutubuh"]);
                } else {
                    $modelFisik->suhutubuh = null;
                }

                // $modelFisik->beratbadan_kg = !empty($modelFisik->beratbadan_kg) ? str_replace('.', ',', $modelFisik->beratbadan_kg) : null;
                if (!empty($modelFisik->beratbadan_kg)) {
                    $modelFisik->beratbadan_kg = str_replace('.', ',', $modelFisik->beratbadan_kg);
                } else if (!empty($modelAnamnesa["beratbadan_kg"])) {
                    $modelFisik->beratbadan_kg = str_replace('.', ',', $modelAnamnesa["beratbadan_kg"]);
                } else {
                    $modelFisik->beratbadan_kg = null;
                }

                // $modelFisik->tinggibadan_cm = !empty($modelFisik->tinggibadan_cm) ? str_replace('.', ',', $modelFisik->tinggibadan_cm) : null;
                if (!empty($modelFisik->tinggibadan_cm)) {
                    $modelFisik->tinggibadan_cm = str_replace('.', ',', $modelFisik->tinggibadan_cm);
                } else if (!empty($modelAnamnesa["tinggibadan_cm"])) {
                    $modelFisik->tinggibadan_cm = str_replace('.', ',', $modelAnamnesa["tinggibadan_cm"]);
                } else {
                    $modelFisik->tinggibadan_cm = null;
                }

                $modelFisik->bb_ideal = !empty($modelFisik->bb_ideal) ? str_replace('.', ',', $modelFisik->bb_ideal) : null;
                $modelFisik->imt = !empty($modelFisik->imt) ? str_replace('.', ',', $modelFisik->imt) : null;
                $modelFisik->meanarteripressure = !empty($modelFisik->meanarteripressure) ? str_replace('.', ',', $modelFisik->meanarteripressure) : null;

                if (!empty($modelFisik->detaknadi)) {
                    $modelFisik->detaknadi = str_replace('.', ',', $modelFisik->detaknadi);
                } else if (!empty($modelAnamnesa["detaknadi"])) {
                    $modelFisik->detaknadi = str_replace('.', ',', $modelAnamnesa["detaknadi"]);
                } else {
                    $modelFisik->detaknadi = null;
                }

                if (!empty($modelFisik->pernapasan)) {
                    $modelFisik->pernapasan = str_replace('.', ',', $modelFisik->pernapasan);
                } else if (!empty($modelAnamnesa["pernapasan"])) {
                    $modelFisik->pernapasan = str_replace('.', ',', $modelAnamnesa["pernapasan"]);
                } else {
                    $modelFisik->pernapasan = null;
                }

                $modelFisik->tglperiksafisik = is_null($modelFisik->tglperiksafisik) ? date('d/m/Y', strtotime('NOW')) : date('d/m/Y', strtotime($modelFisik->tglperiksafisik));
                $modelFisik->bb_ideal = !empty($modelFisik->bb_ideal) ? str_replace('.', ',', $modelFisik->bb_ideal) : 0;
                $modelFisik->imt = !empty($modelFisik->imt) ? str_replace('.', ',', $modelFisik->imt) : 0;
                $modelFisik->meanarteripressure = !empty($modelFisik->meanarteripressure) ? str_replace('.', ',', $modelFisik->meanarteripressure) : '0,00';
                $modelFisik->tekanandarah_kategori = is_null($modelFisik->tekanandarah_kategori) ? '-' : $modelFisik->tekanandarah_kategori;
                $modelFisik->sirkulasi_nadicarotis = is_null($modelFisik->sirkulasi_nadicarotis) ? 0 : $modelFisik->sirkulasi_nadicarotis;
                $modelFisik->sirkulasi_nadiradialis = is_null($modelFisik->sirkulasi_nadiradialis) ? 0 : $modelFisik->sirkulasi_nadiradialis;
                $modelFisik->denyutjantung = is_null($modelFisik->denyutjantung) ? 'Irreguler' : $modelFisik->denyutjantung;
                $modelFisik->data_anatomi = $dataAnatomi;
                $modelFisik->td_systolic = $tdSystolic;
                $modelFisik->td_diastolic = $tdDiastolic;
            }

            $configVal = $this->getConfig('periksa_fisik');

            // jika ada perubahan dari form (belum disimpan), gunakan data cache
            $updated_asmed_cache = Yii::$app->cache->get($this->_pegawai_id . '-updated-data-asesmen-medis-rajal-' . Yii::$app->request->get('id'));
            
            $is_draft = 0;
            if ($updated_asmed_cache) {
                if($isSpesialis) {
                    $askep = ArrayHelper::getValue($updated_asmed_cache, 'askep', []);
                    $asmed = ArrayHelper::getValue($updated_asmed_cache, $modelName, []);
                    $modelAskep->attributes = $askep;
                    $modelFisik->attributes = $asmed;
                }
                else {
                    $modelFisik->attributes = $updated_asmed_cache;
                    $dataAnatomi = ArrayHelper::getValue($updated_asmed_cache, 'data_anatomi');
                    $jumlahAnatomi = !empty($dataAnatomi) ? json_decode($dataAnatomi, true) : [];
                    if (is_array($jumlahAnatomi)) {
                        $dataAnatomi = $dataAnatomi;
                        $counter = count($jumlahAnatomi) + 1;
                    }
                }
                
                $is_draft = 1;
            }
            // set cache data terbaru dari db
            else {
                if($isSpesialis) {
                    $dataCache = [
                        'askep' => $modelAskep->attributes,
                        $path => $modelFisik->attributes,
                    ];
                }
                
                $modelAttributes = $isSpesialis ? $dataCache : $modelFisik->attributes;
                Yii::$app->cache->set($this->_pegawai_id . '-latest-data-asesmen-medis-rajal-' . Yii::$app->request->get('id'), $modelAttributes, DocoConstants::EXPIRED_CACHE);
            }


            $cekAkses = (new AksesFormService)->execute($this->_data_pasien['pasien_id'], DocoConstants::FORM_ASESMEN_MEDIS);
            if ($cekAkses == true || $enable_edit) {
                $status_update = false;
            }

            return $this->renderAjax('fisik/' . $path, get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    private function getSupportDataFisik()
    {
        try {
            $response = $this->_restRajal->get('allow/allow-get-data-fisik');
            $body = json_decode($response->getBody(), true);

            $data_gcs = empty($body['response']['data-gcs']) ? [] : $body['response']['data-gcs'];
            $data_metodegcs = empty($body['response']['data-metodegcs']) ? [] : $body['response']['data-metodegcs'];
            $data_listgcs = empty($body['response']['data-listgcs']) ? [] : $body['response']['data-listgcs'];
            $data_bmi = empty($body['response']['data-bmi']) ? [] : $body['response']['data-bmi'];
            $data_tekanandarah = empty($body['response']['data-tekanandarah']) ? [] : $body['response']['data-tekanandarah'];
            $data_bagiantubuh = empty($body['response']['data-bagiantubuh']) ? [] : $body['response']['data-bagiantubuh'];

            return [
                'data_gcs' => $data_gcs,
                'data_metodegcs' => $data_metodegcs,
                'data_listgcs' => $data_listgcs,
                'data_bmi' => $data_bmi,
                'data_tekanandarah' => $data_tekanandarah,
                'data_bagiantubuh' => $data_bagiantubuh,
            ];
        } catch (Exception $e) {
            return [
                'data_gcs' => [],
                'data_metodegcs' => [],
                'data_listgcs' => [],
                'data_bmi' => [],
                'data_tekanandarah' => [],
                'data_bagiantubuh' => [],
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $e) {
            return [
                'data_gcs' => [],
                'data_metodegcs' => [],
                'data_listgcs' => [],
                'data_bmi' => [],
                'data_tekanandarah' => [],
                'data_bagiantubuh' => [],
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetNilaiGcs($metodegcs_id = null, $curval = 0, $kategori = '', $is_kapitis = false)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!$metodegcs_id) {
            return [
                'status' => 500,
                'message' => 'Tindakan tidak ditemukan',
            ];
        }

        try {
            $list_data = $this->getSupportDataFisik();
            $data_metode_gcs = $list_data['data_metodegcs'];

            $nilai = 0;
            foreach ($data_metode_gcs as $key => $value) {
                if ($value['metodegcs_id'] == $metodegcs_id) {
                    $nilai = $value['metodegcs_nilai'];
                    break;
                }
            }

            $nilai = $nilai + $curval;

            $data_metode_gcs = $list_data['data_gcs'];
            $gcs_id = null;
            foreach ($data_metode_gcs as $key => $value) {
                if ($is_kapitis === true) {
                    if (($nilai >= $value['gcs_nilaimin']) && ($nilai <= $value['gcs_nilaimax']) && ($value['is_kapitis'] == true)) {
                        $kategori = $value['gcs_nama'];
                        $gcs_id = $value['gcs_id'];
                        break;
                    }
                } else {
                    if (($nilai >= $value['gcs_nilaimin']) && ($nilai <= $value['gcs_nilaimax']) && ($value['is_kapitis'] == false)) {
                        $kategori = $value['gcs_nama'];
                        $gcs_id = $value['gcs_id'];
                        break;
                    }
                }
            }

            $return_data = [
                'nilai' => $nilai,
                'kategori' => $kategori,
                'gcs_id' => $gcs_id,
            ];

            return [
                'status' => 200,
                'data' => $return_data,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetKategoriGcsKapitis($curval = 0, $is_kapitis = false)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!$metodegcs_id) {
            return [
                'status' => 500,
                'message' => 'Tindakan tidak ditemukan',
            ];
        }

        try {
            $list_data = $this->getSupportDataFisik();
            $data_metode_gcs = $list_data['data_metodegcs'];

            $data_metode_gcs = $list_data['data_gcs'];
            foreach ($data_metode_gcs as $key => $value) {
                if ($is_kapitis === true) {
                    if (($curval >= $value['gcs_nilaimin']) && ($curval <= $value['gcs_nilaimax']) && ($value['is_kapitis'] == true)) {
                        $kategori = $value['gcs_nama'];
                        break;
                    }
                } else {
                    if (($curval >= $value['gcs_nilaimin']) && ($curval <= $value['gcs_nilaimax']) && ($value['is_kapitis'] == false)) {
                        $kategori = $value['gcs_nama'];
                        break;
                    }
                }
            }

            $return_data = [
                'kategori' => $kategori,
            ];

            return [
                'status' => 200,
                'data' => $return_data,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionSaveAnatomi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $data = $request->post('data');
            $pendaftaran_id = $request->post('pendaftaran_id');
            $pasien_id = $request->post('pasien_id');
            $pemeriksaanfisik_id = $request->post('pemeriksaanfisik_id');

            $send_data = [
                'data' => $data,
                'pendaftaran_id' => $pendaftaran_id,
                'pasien_id' => $pasien_id,
                'pemeriksaanfisik_id' => $pemeriksaanfisik_id,
            ];

            $response = $this->_restRajal->post('tra-pemeriksaan/save-periksatubuh', [
                'form_params' => $send_data
            ]);
            $response = json_decode($response->getBody(), true);

            return [
                'status' => 200,
                'message' => $response,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetHasilTd($pendaftaran_id, $nilai = '0/0')
    {
        $request = Yii::$app->request;
        $data_td = DocoConstants::TD_HASIL;
        if (count($data_td) < 1) {
            $response = $this->_restRajal->get('allow/get-data-klasifikasi-tekanan-darah');
            $body = json_decode($response->getBody(), true);
            $data_td = $body['response'];
        }
        $nilai = explode('/', $nilai);
        $nilai_systolic = (int) $nilai[0];
        $nilai_diastolic = (int) $nilai[1];
        $hasil = '';
        $klasifikasitekanandarah_id = 0;

        if (!empty($data_td)) {
            if ($nilai_systolic >= $data_td[4]['sistolik_min'] || $nilai_diastolic >= $data_td[4]['diastolik_min']) {
                $hasil = $data_td[4]['klasifikasitekanadarah'];
                $klasifikasitekanandarah_id = $data_td[4]['klasifikasitekanadarah_id'];
            } else {
                if ($nilai_systolic >= $data_td[3]['sistolik_min'] && $nilai_systolic <= $data_td[3]['sistolik_max'] || $nilai_diastolic >= $data_td[3]['diastolik_min'] && $nilai_diastolic <= $data_td[3]['diastolik_max']) {
                    $hasil = $data_td[3]['klasifikasitekanadarah'];
                    $klasifikasitekanandarah_id = $data_td[3]['klasifikasitekanadarah_id'];
                } else {
                    if ($nilai_systolic >= $data_td[2]['sistolik_min'] && $nilai_systolic <= $data_td[2]['sistolik_max'] || $nilai_diastolic >= $data_td[2]['diastolik_min'] && $nilai_diastolic <= $data_td[2]['diastolik_max']) {
                        $hasil = $data_td[2]['klasifikasitekanadarah'];
                        $klasifikasitekanandarah_id = $data_td[2]['klasifikasitekanadarah_id'];
                    } else {
                        if ($nilai_systolic >= $data_td[1]['sistolik_min'] && $nilai_diastolic <= $data_td[1]['sistolik_max']) {
                            $hasil = $data_td[1]['klasifikasitekanadarah'];
                            $klasifikasitekanandarah_id = $data_td[1]['klasifikasitekanadarah_id'];
                        } else {
                            $hasil = $data_td[0]['klasifikasitekanadarah'];
                            $klasifikasitekanandarah_id = $data_td[0]['klasifikasitekanadarah_id'];
                        }
                    }
                }
            }
        }

        $result = [
            'hasil' => $hasil,
            'klasifikasitekanandarah_id' => $klasifikasitekanandarah_id
        ];
        return DocoHelpers::response($result);
    }

    /**
     * return view form asesmen medis
     *
     * @return View
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionAsesmenMedis()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);

        $modelFisik = new AsesmenMedisForm;

        if ($post = $request->post()) {
            $modelFisik->attributes = $post;
            if ($modelFisik->validate()) {
                return $this->guzzleExec($this->_restRajal, [
                    'url' => 'tra-pemeriksaan/save-asesmen-medis',
                    'method' => 'post',
                    'payload' => [
                        'form_params' => array_merge($modelFisik->attributes, [
                            'details' => $post['daftarDiagnosa']
                        ]),
                        'query' => [
                            'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id)
                        ],
                    ],
                    'returnResponse' => true
                ]);
            } else {
                return DocoHelpers::response($modelFisik->errors, 422, 'PemeriksaanFisikNewForm');
            }
        }
        $recordAsesmenMedis = $this->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/asesmen-medis',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id)
                ]
            ]
        ]);
        $configVal = $this->getConfig('periksa_fisik');
        $modelFisik->attributes = $recordAsesmenMedis['data'];
        return $this->renderAjax('__asesmen_medis', [
            'modelFisik' => $modelFisik,
            'configVal' => $configVal,
            // 'nopendaftaran' => $nopendaftaran,
            'pendaftaran_id' => $pendaftaran_id,
            'fisikData' => $recordAsesmenMedis['data']
        ]);

    }

    // set cache ketika form asmed on change
    public function actionSetCacheAsmed()
    {
        
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id');
        if ($pendaftaran_id) {
            if (is_numeric($pendaftaran_id)) {
                $pendaftaran_id = DocoHelpers::encrypt($pendaftaran_id);
            }
        }

        $keyCache = $this->_pegawai_id . '-updated-data-asesmen-medis-rajal-' . $pendaftaran_id;
        $path = $this->mapingRuanganSpesialis();
        $modelMaping = $this->mapingModelSpesialis($path);
        $modelName = ArrayHelper::getValue($modelMaping, 'modelName');

        $is_draft = 0;
        $asmed_cache = [];
        if($path) {
            $updatedAskep = Yii::$app->request->get('PemeriksaanAskepForm');
            $updatedAsmed = Yii::$app->request->get($modelName);
            $asmed_cache = [
                'askep' => $updatedAskep,
                $modelName => $updatedAsmed,
            ];
            
            if(!empty($asmed_cache)) {
                $is_draft = 1;
            }
        }
        else {
            // cek perbedaan data pada form
            $asmed_cache = Yii::$app->cache->get($this->_pegawai_id . '-latest-data-asesmen-medis-rajal-' . $pendaftaran_id);

            $updated_asmed = Yii::$app->request->get('PemeriksaanFisikForm');
            $namaDokter = ArrayHelper::getValue($updated_asmed, 'nama_dokter');
            $gcsKategori = ArrayHelper::getValue($updated_asmed, 'gcs_kategori');
            $tdSystolic = ArrayHelper::getValue($updated_asmed, 'td_systolic');
            $tdDiastolic = ArrayHelper::getValue($updated_asmed, 'td_diastolic');
            $detakNadi = ArrayHelper::getValue($updated_asmed, 'detaknadi');

            unset($namaDokter);
            unset($gcsKategori);
            $updated_asmed['td_systolic'] = $tdSystolic == 0 ? null : $tdSystolic;
            $updated_asmed['td_diastolic'] = $tdDiastolic == 0 ? null : $tdDiastolic;
            $updated_asmed['detaknadi'] = $detakNadi == 0 ? null : $detakNadi;

            foreach ($asmed_cache as $key => $value) {
                if (isset($updated_asmed[$key])) {
                    if (is_null($value) && ($updated_asmed[$key] === '') || ($updated_asmed[$key] === '0'))
                        continue;
    
                    if ($value != $updated_asmed[$key]) {
                        $asmed_cache[$key] = $updated_asmed[$key];
                        if (!$is_draft)
                            $is_draft = 1;
                    }
    
                }
            }
        }
        
        // set cache jika ada perubahan, delete cache jika tetap sama
        if ($is_draft) {
            // digunakan di AsesmenMedisProcess actionSetCacheAsmed processFlow()
            Yii::$app->cache->set($keyCache, $asmed_cache, DocoConstants::EXPIRED_CACHE);
        } else {
            Yii::$app->cache->delete($keyCache);
        }

        return DocoHelpers::response(["is_draft" => $is_draft]);
    }

    private function mapingRuanganSpesialis()
    {
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');
        $response = Yii::$app->docoRest->rajal->get('asesmen-keperawatan/get-ruangan-spesialis', [
            'query' => [
                'ruangan_id' => $ruanganId
            ]
        ]);

        return json_decode($response->getBody(), true)['response'];
    }

    private function mapingModelSpesialis($path)
    {
        $model = New PemeriksaanFisikForm;
        $modelName = 'PemeriksaanFisikForm';
        switch ($path) {
            case DocoConstants::POLI_THT:
                $model = new PemeriksaanThtForm;
                $modelName = 'PemeriksaanThtForm';
                break;

            case DocoConstants::POLI_MATA:
                $model = new PemeriksaanMataForm;
                $modelName = 'PemeriksaanMataForm';
                break;

            case DocoConstants::POLI_GIGI:
                $model = new PemeriksaanGigiForm;
                $modelName = 'PemeriksaanGigiForm';
                break;

            case DocoConstants::POLI_OBGYN:
                $model = new PemeriksaanObgynForm;
                $modelName = 'PemeriksaanObgynForm';
                break;

            default:
                # code...
                break;
        }

        return [
            'model' => $model,
            'modelName' => $modelName,
        ];
    }

    private function getDefaultAskep($dataAskep, $modelAnamnesa, $key)
    {
        $defaultValue = ArrayHelper::getValue($modelAnamnesa, $key);
        if(isset($dataAskep[$key]) && !empty($dataAskep[$key])) {
            $defaultValue = ArrayHelper::getValue($dataAskep, $key);
        }

        return $defaultValue;
    }
}
