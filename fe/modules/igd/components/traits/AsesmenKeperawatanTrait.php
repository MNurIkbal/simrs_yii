<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\igd\models\AsesmenKeperawatanIgdForm;
use app\modules\igd\models\AsesmenKeperawatanResikoJatuh;
use app\modules\igd\models\AsesmenPerawatRDForm;
use app\modules\igd\models\SydneyForm;
use app\modules\igd\models\HumptyDumptyForm;
use app\modules\igd\models\MorseForm;
use app\components\Services\AksesFormService;

trait AsesmenKeperawatanTrait
{
    public function actionAsesmenKeperawatan($id)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $model = new AsesmenPerawatRDForm;

            $data_pasien = $this->_data_pasien;
            $userIdentity = $this->_user_identity;
            $jeniskelamin = $this->_jeniskelamin;

            $dokter_jaga_id = isset($data_pasien['dokter_jaga_id']) ? $data_pasien['dokter_jaga_id'] : 0;
            $dokter_jaga = isset($data_pasien['dokter_jaga']) ? $data_pasien['dokter_jaga'] : '';
            $no_rm = isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : null;

            $valDiagPenyerta = [];
            $callbackDiagPenyerta = [];
            $keyDiagPenyerta = [];

            $model->pendaftaran_id = $pendaftaran_id;
            $model->ruangan_id = $this->_ruangan_id;
            $model->tgl_asesmen = date('d-m-Y h:i:s');
            $model->perawat_id = isset($userIdentity['id_pegawai']) ? $userIdentity['id_pegawai'] : null;
            $model->perawat_nama = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
            $model->tgl_pendaftaran = isset($data_pasien['tgl_pendaftaran']) ? date('d-m-Y H:i:s', strtotime($data_pasien['tgl_pendaftaran'])) : null;
            $response = $this->_restIgd->get(
                'asesmen-perawat/bundle-data-asesmen-perawat',
                ['query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'no_rm' => $no_rm
                ]]
            );
            $databundle = json_decode($response->getBody(), true);
            $databundle = $databundle['response'];

            $data_asesmen_perawat = $databundle['data-asesmen-perawat'];
            $data_rajal = $databundle['data-rajal'];
            $data_bmi = empty($databundle['data-bmi']) ? [] : $databundle['data-bmi'];
            $data_pengantar = $databundle['data-pengantar'];
            $data_jenisasmen = $databundle['data-jenisasesmen'];
            $data_keadaanumum = $databundle['data-keadaanumum'];
            $data_asmennyeri = $databundle['data-asmennyeri'];

            // Triage
            $model->tgl_datang = isset($data_asesmen_perawat['tgl_datang']) ? $data_asesmen_perawat['tgl_datang'] : '';
            $model->prioritas_triage = isset($data_asesmen_perawat['prioritas_triage']) ? $data_asesmen_perawat['prioritas_triage'] : '';
            $model->pasien_datang = isset($data_asesmen_perawat['pasien_datang']) ? $data_asesmen_perawat['pasien_datang'] : '';
            $model->jenis_asmenperawat = isset($data_asesmen_perawat['jenis_asmenperawat']) ? $data_asesmen_perawat['jenis_asmenperawat'] : '';
            $model->alasan_kunjungan = isset($data_asesmen_perawat['alasan_kunjungan']) ? $data_asesmen_perawat['alasan_kunjungan'] : '';
            $model->is_alergi = isset($data_asesmen_perawat['is_alergi']) ? ($data_asesmen_perawat['is_alergi'] == true ? 1 : 0) : '';
            $model->is_alergiobat = isset($data_asesmen_perawat['is_alergiobat']) ? $data_asesmen_perawat['is_alergiobat'] : '';
            $model->alergi_obat = isset($data_asesmen_perawat['alergi_obat']) ? $data_asesmen_perawat['alergi_obat'] : '';
            $model->is_alergilainnya = isset($data_asesmen_perawat['is_alergilainnya']) ? $data_asesmen_perawat['is_alergilainnya'] : '';
            $model->alergi_lainnya = isset($data_asesmen_perawat['alergi_lainnya']) ? $data_asesmen_perawat['alergi_lainnya'] : '';
            $model->keadaan_umum = isset($data_asesmen_perawat['keadaan_umum']) ? json_decode($data_asesmen_perawat['keadaan_umum'], true) : '';
            $model->is_nyeri = isset($data_asesmen_perawat['is_nyeri']) ? ($data_asesmen_perawat['is_nyeri'] == true ? 1 : 0) : '';
            $model->lokasi_nyeri = isset($data_asesmen_perawat['lokasi_nyeri']) ? $data_asesmen_perawat['lokasi_nyeri'] : '';
            $model->skala_nyeri = isset($data_asesmen_perawat['skala_nyeri']) ? $data_asesmen_perawat['skala_nyeri'] : '';
            $model->metode_nyeri = isset($data_asesmen_perawat['metode_nyeri']) ? json_decode($data_asesmen_perawat['metode_nyeri'], true) : '';
            $model->is_resikojatuh = isset($data_asesmen_perawat['is_resikojatuh']) ? ($data_asesmen_perawat['is_resikojatuh'] == true ? 1 : 0) : '';
            // Fisik
            $model->keluhan = isset($data_asesmen_perawat['keluhan']) ? $data_asesmen_perawat['keluhan'] : '';
            $model->lama_sakit = isset($data_asesmen_perawat['lama_sakit']) ? $data_asesmen_perawat['lama_sakit'] : '';
            if (isset($data_asesmen_perawat['r_penyakitdahulu'])) {
                $arr_diag_penyerta = json_decode($data_asesmen_perawat['r_penyakitdahulu'], TRUE);
                foreach ($arr_diag_penyerta as $valDiag) {
                    if (isset($valDiag['id'])) {
                        $keyDiagPenyerta[] = $valDiag['id'] . '_' . $valDiag['text'];
                        $valDiagPenyerta[] = [$valDiag['id'] . '_' . $valDiag['text'] => $valDiag['text']];
                        $callbackDiagPenyerta[] = [
                            'id' => $valDiag['id'],
                            'text' => $valDiag['text']
                        ];
                    } else {
                        $valDiagPenyerta[] = [$valDiag['text'] => $valDiag['text']];
                        $keyDiagPenyerta[] = $valDiag['text'];
                        $callbackDiagPenyerta[] = [
                            'id' => $valDiag['text'],
                            'text' => $valDiag['text']
                        ];
                    }
                }
                $model->r_penyakitdahulu = $keyDiagPenyerta;
                unset($data_asesmen_perawat['r_penyakitdahulu']);
            }
            $model->r_penyakitkeluarga = isset($data_asesmen_perawat['r_penyakitkeluarga']) ? $data_asesmen_perawat['r_penyakitkeluarga'] : '';
            $model->catatan_asesmen = isset($data_asesmen_perawat['catatan_asesmen']) ? $data_asesmen_perawat['catatan_asesmen'] : '';
            $model->gcseye_id = isset($data_asesmen_perawat['gcseye_id']) ? $data_asesmen_perawat['gcseye_id'] : '';
            $model->gcsverbal_id = isset($data_asesmen_perawat['gcsverbal_id']) ? $data_asesmen_perawat['gcsverbal_id'] : '';
            $model->gcsmotorik_id = isset($data_asesmen_perawat['gcsmotorik_id']) ? $data_asesmen_perawat['gcsmotorik_id'] : '';
            $model->is_kapitis = isset($data_asesmen_perawat['is_kapitis']) ? $data_asesmen_perawat['is_kapitis'] : '';
            $model->hasil_gcs = isset($data_asesmen_perawat['hasil_gcs']) ? $data_asesmen_perawat['hasil_gcs'] : '';
            $model->td_systolic = isset($data_asesmen_perawat['td_systolic']) ? $data_asesmen_perawat['td_systolic'] : '';
            $model->td_diastolic = isset($data_asesmen_perawat['td_diastolic']) ? $data_asesmen_perawat['td_diastolic'] : '';
            $model->detak_nadi = isset($data_asesmen_perawat['detak_nadi']) ? $data_asesmen_perawat['detak_nadi'] : '';
            $model->pernapasan = isset($data_asesmen_perawat['pernapasan']) ? $data_asesmen_perawat['pernapasan'] : '';
            $model->suhu_tubuh = isset($data_asesmen_perawat['suhu_tubuh']) ? $data_asesmen_perawat['suhu_tubuh'] : '';
            $model->tinggi_badan = isset($data_asesmen_perawat['tinggi_badan']) ? str_replace('.', ',', $data_asesmen_perawat['tinggi_badan']) : '';
            $model->berat_badan = isset($data_asesmen_perawat['berat_badan']) ? str_replace('.', ',', $data_asesmen_perawat['berat_badan']) : '';
            $model->spo2 = isset($data_asesmen_perawat['spo2']) ? $data_asesmen_perawat['spo2'] : '';
            $model->kelaianan_tubuh = isset($data_asesmen_perawat['kelaianan_tubuh']) ? $data_asesmen_perawat['kelaianan_tubuh'] : '';

            $model->hasil_td = isset($data_rajal['klasifikasitekanadarah']) ? $data_rajal['klasifikasitekanadarah'] : '';
            $model->bb_ideal = isset($data_rajal['bb_ideal']) ? $data_rajal['bb_ideal'] : '';
            $tb = isset($data_rajal['tinggibadan_cm']) ? $data_rajal['tinggibadan_cm'] : 0;
            $bb = isset($data_rajal['beratbadan_kg']) ? $data_rajal['beratbadan_kg'] : 0;
            $imt = ($bb !== 0) ? ($bb / (($tb / 100) * ($tb / 100))) : 0;
            $model->imt = DocoHelpers::formatDecimal($imt);
            $model->ket_imt = isset($data_rajal['bmi_defenisi']) ? $data_rajal['bmi_defenisi'] : '';

            $data_gcs = (isset($databundle['data-gcs']) && !empty($databundle['data-gcs'])) ? $databundle['data-gcs'] : [];
            $data_listgcs = (isset($databundle['data-listgcs']) && !empty($databundle['data-listgcs'])) ? $databundle['data-listgcs'] : [];
            $data_gcsEye = (isset($data_listgcs['eye']) && !empty($data_listgcs['eye'])) ? $data_listgcs['eye'] : [];
            $data_gcsVerbal = (isset($data_listgcs['verbal']) && !empty($data_listgcs['verbal'])) ? $data_listgcs['verbal'] : [];
            $data_gcsMotorik = (isset($data_listgcs['motorik']) && !empty($data_listgcs['motorik'])) ? $data_listgcs['motorik'] : [];
            $gcsEyeOptions = [];
            $gcsVerbalOptions = [];
            $gcsMotorikOptions = [];
            if (count($data_gcsEye) > 0) {
                foreach ($data_gcsEye as $keyEye => $valueEye) {
                    $gcsEyeOptions[$valueEye['metodegcs_id']]['data-nilai'] = $valueEye['metodegcs_nilai'];
                }
            }
            if (count($data_gcsVerbal) > 0) {
                foreach ($data_gcsVerbal as $keyVerbal => $valueVerbal) {
                    $gcsVerbalOptions[$valueVerbal['metodegcs_id']]['data-nilai'] = $valueVerbal['metodegcs_nilai'];
                }
            }
            if (count($data_gcsMotorik) > 0) {
                foreach ($data_gcsMotorik as $keyMotorik => $valueMotorik) {
                    $gcsMotorikOptions[$valueMotorik['metodegcs_id']]['data-nilai'] = $valueMotorik['metodegcs_nilai'];
                }
            }

            $list_ada_tidak = [0 => 'Tidak Ada', 1 => 'Ada'];
            $list_ya_tidak = [0 => 'Tidak', 1 => 'Ya'];

            return $this->renderAjax('asesmen-keperawatan/index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionSaveAsesment()
    {
        $request = Yii::$app->request;
        $model = new AsesmenPerawatRDForm;
        $model->load($request->post());
        if ($model->is_alergiobat == 1) {
            $model->scenario = 'is_alergiobat_true';
        }
        if ($model->is_alergilainnya == 1) {
            $model->scenario = 'is_alergilainnya_true';
        }
        if ($model->is_nyeri == 1) {
            $model->scenario = 'is_nyeri_true';
        }
        $model->attributes = $request->post();
        if ($model->validate()) {
            $response = $this->_restIgd->post('asesmen-perawat/save', [
                'form_params' => $model->attributes
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response, false, 'AsesmenPerawatRDForm');
        } else {
            return DocoHelpers::response($model->errors, 422, 'AsesmenPerawatRDForm');
        }
    }

    public function actionGetHasilTd($pendaftaran_id, $nilai = '0/0')
    {
        $request = Yii::$app->request;
        $data_td = DocoConstants::TD_HASIL;
        if (count($data_td) < 1) {
            $response = $this->_restIgd->get('allow/get-data-klasifikasi-tekanan-darah');
            $body = json_decode($response->getBody(), true);
            $data_td = $body['response'];
        }
        $nilai = explode('/', $nilai);
        $nilai_systolic = (int) $nilai[0];
        $nilai_diastolic = (int) $nilai[1];
        $systolic = '';
        $diastolic = '';
        $urutan_systolic = 0;
        $urutan_diastolic = 0;
        $hasil = '';
        $klasifikasitekanandarah_id = 0;

        if (!empty($data_td)) {
            foreach ($data_td as $key => $value) {
                if ($nilai_systolic >= $value['sistolik_min'] && $nilai_systolic <= $value['sistolik_max']) {
                    $systolic = $value['klasifikasitekanadarah'];
                    $urutan_systolic = $value['urutan'];
                    $klasifikasitekanandarah_id = $value['klasifikasitekanadarah_id'];
                }

                if ($nilai_diastolic >= $value['diastolik_min'] && $nilai_diastolic <= $value['diastolik_max']) {
                    $diastolic = $value['klasifikasitekanadarah'];
                    $urutan_diastolic = $value['urutan'];
                    $klasifikasitekanandarah_id = $value['klasifikasitekanadarah_id'];
                }
            }
        }

        if ($urutan_systolic > $urutan_diastolic) {
            $hasil = $systolic;
            $klasifikasitekanandarah_id = $klasifikasitekanandarah_id;
        } else if ($urutan_systolic < $urutan_diastolic) {
            $hasil = $diastolic;
            $klasifikasitekanandarah_id = $klasifikasitekanandarah_id;
        } else {
            $hasil = $systolic;
            $klasifikasitekanandarah_id = $klasifikasitekanandarah_id;
        }

        $result = [
            'hasil' => $hasil,
            'klasifikasitekanandarah_id' => $klasifikasitekanandarah_id
        ];
        return DocoHelpers::response($result);
    }

    /**
     * This function will return new form of asesmen keperawatan
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFormAsesmenKeperawatan()
    {
        $pendaftaran_id = $this->helper->decrypt(Yii::$app->request->get('id'));
        $data = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id'))
                ]
            ]
        ]);

        $dataBmi = $this->guzzleExec($this->_restIgd, [
            'url' => 'allow/data-bmi',
        ]);
        $jeniskelamin = $this->_jeniskelamin;
        $data_bmi     = empty($dataBmi['data-bmi']) ? [] : $dataBmi['data-bmi'];
        $data_bmi     = json_encode($data_bmi);
        $model = new AsesmenKeperawatanIgdForm;
        $model->attributes = $data['asesmenkeperawatan'];
        if (!empty($model->diagnosa_keperawatan)) {
            $diagnosaKeperawatan = [];
            foreach (json_decode($model->diagnosa_keperawatan, true) as $index => $diagnosa) {
                if (empty($diagnosa['id']) && empty($diagnosa['kode'])) {
                    $id = $diagnosa['text'];
                } else {
                    $id = $diagnosa['id'] . '_' . $diagnosa['kode'] . ' - ' . $diagnosa['text'];
                }
                $diagnosaKeperawatan[$id] = (!empty($diagnosa['kode']) ? $diagnosa['kode'] . ' - ' : '') . $diagnosa['text'];
            }
            $model->diagnosa_keperawatan = $diagnosaKeperawatan;
        }
        $data['asesmenkeperawatan']['pendaftaran_id'] = Yii::$app->request->get('id');
        $modelResiko = new AsesmenKeperawatanResikoJatuh;
        $arrayConfig = $this->getConfig('asesmen_keperawatan_rd');
        if (isset($data['asesmenkeperawatan']['keluhan_utama'])) {
            $model->keluhan = $data['asesmenkeperawatan']['keluhan_utama'];
        }
        if (isset($data['asesmenkeperawatan']['riwayat_penyakit_sekarang'])) {
            $model->r_penyakitsaatini = $data['asesmenkeperawatan']['riwayat_penyakit_sekarang'];
        }
        if (isset($data['asesmenkeperawatan']['riwayat_penyakit_dahulu'])) {
            $model->r_penyakitdahulu = $data['asesmenkeperawatan']['riwayat_penyakit_dahulu'];
        }
        if (isset($data['asesmenkeperawatan']['riwayat_terapi_sebelumnya'])) {
            $model->r_pengobatan = $data['asesmenkeperawatan']['riwayat_terapi_sebelumnya'];
        }
        if (isset($data['asesmenkeperawatan']['alergi_obat']) || isset($data['asesmenkeperawatan']['alergi_lainnya'])) {
            $model->is_alergi = (!is_null($data['asesmenkeperawatan']['alergi_obat']) || !is_null($data['asesmenkeperawatan']['alergi_lainnya'])) ? true : false;
            if (isset($data['asesmenkeperawatan']['alergi_obat'])) {
                $model->is_alergiobat = !is_null($data['asesmenkeperawatan']['alergi_obat']) ? true : false;
            }
            if (isset($data['asesmenkeperawatan']['alergi_lainnya'])) {
                $model->is_alergilainnya = !is_null($data['asesmenkeperawatan']['alergi_lainnya']) ? true : false;
            }
        }

        $tidak_ada_kelainan = 'tidak_ada_kelainan';
        if (!isset($data['asesmenkeperawatan']['survey_kepala'])) {
            $model->survey_kepala = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_mata'])) {
            $model->survey_mata = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_mulut'])) {
            $model->survey_mulut = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_telinga'])) {
            $model->survey_telinga = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_leher'])) {
            $model->survey_leher = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_extremitas'])) {
            $model->survey_extremitas = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_dada'])) {
            $model->survey_dada = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_abdomen'])) {
            $model->survey_abdomen = $tidak_ada_kelainan;
        }

        if (!isset($data['asesmenkeperawatan']['survey_pelvis'])) {
            $model->survey_pelvis = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_medulla_spinalis'])) {
            $model->survey_medulla_spinalis = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenkeperawatan']['survey_kolumna_vertebralis'])) {
            $model->survey_kolumna_vertebralis = $tidak_ada_kelainan;
        }

        $model->tgl_datang = date('d/m/Y H:i:s', strtotime($model->tgl_datang));
        $model->tgl_keluar = !empty($data["asesmenkeperawatan"]['tgl_keluar']) ? date('d/m/Y H:i:s', strtotime($data["asesmenkeperawatan"]['tgl_keluar'])) : date('d/m/Y H:i:s');

        $enable_edit = !empty($data['enable_pulang']) ? $data['enable_pulang'] : false;
        $cekDataPasien = $this->getStatusPeriksa();
        $status_update = $cekDataPasien['result'];
        $cekAkses = (new AksesFormService)->execute($this->_pasien_id, DocoConstants::FORM_ASESMEN_AWAL_KEP);

        if ($cekAkses == true || $enable_edit) {
            $status_update = false;
        }

        // Nurse validation
        $is_perawat = 0;
        if ($this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $is_perawat = 1;
        }

        // Status Btn Verifikasi Gizi
        $disableGiziBtn = 1;
        if ($this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_GIZI && !$model->is_verifikasigizi && (!empty($model->nutrisi_1a) || !empty($model->nutrisi_1b) || !empty($model->nutrisi_2) || !empty($model->strongkids_kurus) || !empty($model->strongkids_turunbb) || !empty($model->strongkids_kondisikhusus) || !empty($model->strongkids_keadaan_beresiko))) {
            $disableGiziBtn = 0;
        }

        return $this->renderAjax('asesmen-keperawatan/__form', compact('model', 'arrayConfig', 'modelResiko', 'data', 'pendaftaran_id', 'data_bmi', 'jeniskelamin', 'status_update', 'is_perawat', 'disableGiziBtn'));
    }

    public function actionFormSydney($pendaftaran_id)
    {
        $modelSydney = new SydneyForm;
        $sydneyData = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/get-sydney',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        $sydneyData = empty($sydneyData['data']) ? [] : $sydneyData['data'];
        $configVal = $this->getConfig('sydney');
        return $this->renderAjax('asesmen-keperawatan/modal/__sydney', compact('modelSydney', 'configVal', 'pendaftaran_id', 'sydneyData'));
    }

    public function actionSaveSydney()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        return $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/save-sydney',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
    }

    public function actionFormDumpty($pendaftaran_id)
    {
        $modelDumpty  = new HumptyDumptyForm;
        $tanggal      = date('d-m-Y');
        $jam          = date('H:i');
        $totalTanggal = 1;
        $dumptyData = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/get-dumpty',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        $dumptyData = empty($dumptyData['data']) ? [] : $dumptyData['data'];
        $historyData = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/get-history-dumpty',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);

        foreach ($historyData['group'] as $key => $value) {
            $totalTanggal += $value['count'];
            $historyData['group'][$key]['tanggal'] = date('d-m-Y', strtotime($value['tanggal']));
        }

        foreach ($historyData['history'] as $key => $value) {
            $historyData['history'][$key]['jam'] = date('H:i', strtotime($value['jam']));
        }

        return $this->renderAjax('asesmen-keperawatan/modal/__dumpty', compact('modelDumpty', 'pendaftaran_id', 'dumptyData', 'historyData', 'totalTanggal', 'tanggal', 'jam'));
    }

    public function actionSaveDumpty()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        return $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/save-dumpty',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
    }

    /**
     * List dropdown of resiko jatuh
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionResikoJatuhList()
    {
        return $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/resiko-jatuh-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    public function actionFormMorse($pendaftaran_id)
    {
        $modelMorse   = new MorseForm;
        $tanggal      = date('d-m-Y');
        $jam          = date('H:i');
        $totalTanggal = 1;
        $morseData = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/get-morse',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        $morseData = empty($morseData['data']) ? [] : $morseData['data'];
        $historyData = $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/get-history-morse',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);

        foreach ($historyData['group'] as $key => $value) {
            $totalTanggal += $value['count'];
            $historyData['group'][$key]['tanggal'] = date('d-m-Y', strtotime($value['tanggal']));
        }

        foreach ($historyData['history'] as $key => $value) {
            $historyData['history'][$key]['jam'] = date('H:i', strtotime($value['jam']));
        }

        return $this->renderAjax('asesmen-keperawatan/modal/__morse', compact('modelMorse', 'pendaftaran_id', 'morseData', 'historyData', 'totalTanggal', 'tanggal', 'jam'));
    }

    public function actionSaveMorse()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        return $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-keperawatan/save-morse',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
    }

    /**
     * This function will save data from view
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmenKeperawatan()
    {
        $model = new AsesmenKeperawatanIgdForm;
        $request = Yii::$app->request->post();
        $resikoJatuh = Yii::$app->request->post('resiko_jatuh');
        $resikoJatuh['skala_nyeri'] = isset($resikoJatuh['skala_nyeri']) ? str_replace('-undefined', '', $resikoJatuh['skala_nyeri']) : null;
        $request['tgl_datang'] = (date_create_from_format('d/m/Y H:i:s', $request['tgl_datang'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $request['tgl_datang']), 'Y-m-d H:i:s') : $request['tgl_datang'];
        $request['tgl_keluar'] = (date_create_from_format('d/m/Y H:i:s', $request['tgl_keluar'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $request['tgl_keluar']), 'Y-m-d H:i:s') : $request['tgl_keluar'];


        $model->attributes = $request;

        // Mapping asesmen allo or auto
        if ($model->allo_or_auto == '0') {
            $model->asesmen_auto = 1;
        } else if ($model->allo_or_auto == '1') {
            $model->asesmen_allo = 1;
        } else {
            $model->asesmen_auto = null;
            $model->asesmen_auto = null;
        }
        $model->allo_or_auto = null;

        if (!$model->validate()) {
            return $this->responseJson(422, 'Silakan cek kembali inputan.', $this->mapErrorForm($model->errors, 'AsesmenKeperawatanIgdForm'));
        } else {
            $model->tinggi_badan = str_replace(',', '.', $model->tinggi_badan);
            $model->berat_badan = str_replace(',', '.', $model->berat_badan);
            $arrayPayload = [];
            if ($model->diagnosa_keperawatan) {
                $arrDiagnosa = [];
                foreach ($model->diagnosa_keperawatan as $index => $diagnosa) {
                    $explodeDiagnosa = explode(' - ', $diagnosa);
                    $diagnosaId = null;
                    $diagnosaKode = null;
                    $diagnosaText = $explodeDiagnosa[0];
                    if (count($explodeDiagnosa) > 1) {
                        $explodeDiagnosaIdText = explode('_', $explodeDiagnosa[0]);
                        $diagnosaId = isset($explodeDiagnosaIdText[0]) ? $explodeDiagnosaIdText[0] : '';
                        $diagnosaKode = isset($explodeDiagnosaIdText[1]) ? $explodeDiagnosaIdText[1] : '';
                        $diagnosaText = $explodeDiagnosa[1];
                    }
                    $arrDiagnosa[] = [
                        'id' => $diagnosaId,
                        'kode' => $diagnosaKode,
                        'text' => $diagnosaText
                    ];
                }
                $model->diagnosa_keperawatan = json_encode($arrDiagnosa);
            }
            foreach ($model->attributes as $fieldName => $valueField) {
                $arrayPayload[$fieldName] = is_null($valueField) ? '' : $valueField;
            }
            $result = $this->guzzleExec($this->_restIgd, [
                'url' => 'asesmen-keperawatan/save-asesmen',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'formdata' => array_merge($arrayPayload, [
                            'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id')),
                            'resiko_jatuh' => $resikoJatuh
                        ])
                    ]
                ]
            ]);

            $cache = Yii::$app->cache;
            $pendId = $this->helper->decrypt(Yii::$app->request->get('id'));
            $cacheData = $cache->get('data-pasien-igd-' . $pendId);

            $cacheData['pekerjaan_nama'] = $request['pekerjaan_nama'];

            $cache->set('data-pasien-igd-' . $pendId, $cacheData, 3600);
            $cache->delete('data-riwayat-pasien-' . $this->_data_pasien['pasien_id']); // reset data riwayat pasien ketika save data

            return $this->helper->response($result, 200);
        }
    }

    public function actionCetakAskepRd($id)
    {
        $path = Yii::getAlias("@download") . "/asesmen-keperawatan-igd.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);

        $request = $this->_restIgd->get('asesmen-keperawatan/cetak-askep-rd', [
            'query' => [
                'pendaftaran_id' => $pendaftaran_id,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
    }
}
