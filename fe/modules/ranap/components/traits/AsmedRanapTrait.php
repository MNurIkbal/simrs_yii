<?php

namespace app\modules\ranap\components\traits;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\modules\ranap\models\GeriatriForm;
use app\modules\ranap\models\InspeksiusForm;
use app\modules\ranap\models\ImunologiForm;
use app\modules\ranap\models\KekerasanForm;
use app\modules\ranap\models\TerminalForm;
use app\modules\ranap\models\NyeriKronikForm;
use app\modules\ranap\models\NeonatusForm;
use app\modules\ranap\models\KecanduanForm;
use app\modules\ranap\models\PsikiatrisForm;
use app\modules\ranap\models\AnakForm;
use app\modules\ranap\models\PraBedahForm;
use app\modules\ranap\models\LukaBakarForm;
use app\modules\ranap\models\GinekologiForm;
use app\modules\ranap\models\KebidananForm;
use app\modules\ranap\models\MataForm;
use app\modules\ranap\models\KulitForm;
use app\modules\ranap\models\SyarafForm;
use app\modules\ranap\models\MedisBedahForm;
use app\modules\ranap\models\KesehatanAnakForm;
use app\modules\ranap\models\DefaultAsesmenRanapForm;
use app\modules\ranap\models\PenyakitDalamForm;

trait AsmedRanapTrait
{
    public function actionAsmedRanap($id, $pasienadmisi_id, $pasien_id)
    {
        $title = 'Asesmen Medis Rawat Inap';
        $pendaftaranId = !is_numeric($id) ? DocoHelpers::decrypt($id) : $id;
        $pasienAdmisiId = !is_numeric($pasienadmisi_id) ? DocoHelpers::decrypt($pasienadmisi_id) : $pasienadmisi_id;
        $pasienId = !is_numeric($pasien_id) ? DocoHelpers::decrypt($pasien_id) : $pasien_id;
        $optionsFormAsmed = $this->getFormAsmed($pendaftaranId, $pasienAdmisiId);
        $lookup = ArrayHelper::getValue($optionsFormAsmed, 'lookup', []);
        $lookup = ArrayHelper::map($lookup, 'lookup_kode', 'lookup_name');
        $dataAsmed = ArrayHelper::getValue($optionsFormAsmed, 'data_asmed', []);
        $isDokumenEklaim = false;
        return $this->renderAjax('asesmen-medis/__ranap', compact('title', 'pasienId', 'pendaftaranId', 'pasienAdmisiId', 'dataAsmed', 'isDokumenEklaim', 'lookup'));
    }

    public function actionMapingForm()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('id');
        $pasienAdmisiId = $request->get('pasienadmisi_id');
        $formAsesmenCode = $request->get('formasesmen_id');
        $pasienId = $request->get('pasien_id');
        $asesmenMedisId = $request->get('asesmenmedis_id');
        $pendaftaranId = !is_numeric($pendaftaranId) ? DocoHelpers::decrypt($pendaftaranId) : $pendaftaranId;
        $pasienAdmisiId = !is_numeric($pasienAdmisiId) ? DocoHelpers::decrypt($pasienAdmisiId) : $pasienAdmisiId;
        $pasienId = !is_numeric($pasienId) ? DocoHelpers::decrypt($pasienId) : $pasienId;
        $mapingForm = $this->mapingForm($formAsesmenCode, $pendaftaranId, $pasienAdmisiId);
        $model = ArrayHelper::getValue($mapingForm, 'model');
        $model->pendaftaran_id = $pendaftaranId;
        $model->pasienadmisi_id = $pasienAdmisiId;
        $model->pasien_id = $pasienId;
        $path = ArrayHelper::getValue($mapingForm, 'path');
        $title = ArrayHelper::getValue($mapingForm, 'title');
        $title = ($asesmenMedisId) ? 'Edit Form '. $title : 'Input Form '. $title;
        $model->pendaftaran_id = $pendaftaranId;
        $model->pasienadmisi_id = $pasienAdmisiId;
        $model->pasien_id = $pasienId;
        $isDokumenEklaim = false;
        $pegawaiId = Yii::$app->docoVars->user('id_pegawai');
        $pegawaiNama = Yii::$app->docoVars->user('nama_pegawai');
        $dataDokter = [];
        if($formAsesmenCode == DocoConstants::KODE_FORM_PENYAKIT_DALAM) {
            $dataDokter = $this->getDataDokter();
            if($dataDokter) {
                $dataDokter = ArrayHelper::map($dataDokter, 'pegawai_id', 'nama_pegawai');
            }
        }
        
        if($asesmenMedisId) {
            $dataAsmed = $this->getDataAsmed($asesmenMedisId);
            if($dataAsmed) {
                $pemeriksaanSpesialis = ArrayHelper::getValue($dataAsmed, 'pemeriksaan_spesialis', []);
                if($pemeriksaanSpesialis) {
                    $pemeriksaanSpesialis = json_decode($pemeriksaanSpesialis, true);
                    $model->attributes = $pemeriksaanSpesialis;
                }
                $model->pendaftaran_id = ArrayHelper::getValue($dataAsmed, 'pendaftaran_id');
                $model->pasienadmisi_id = ArrayHelper::getValue($dataAsmed, 'pasienadmisi_id');
                $isDokumenEklaim = ArrayHelper::getValue($dataAsmed, 'is_dokumen_eklaim');
                $model->asesmenmedis_id = $asesmenMedisId;
            }
        }
        
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();
            $isUnduhDokumen = ArrayHelper::getValue($data, 'is_dokumen_eklaim', 0);
            $isUnduhDokumen = $isUnduhDokumen == 1 ? true : false;
            $modelName = substr(strrchr(get_class($model), "\\"), 1);
            $formData = ArrayHelper::getValue($data, $modelName, []);
            $model->attributes = $formData;
            $model->formasesmen_code = $formAsesmenCode;
            $model->pemeriksaan_spesialis = $model->attributes;
            if($formAsesmenCode == DocoConstants::KODE_FORM_LUKA_BAKAR) {
                $this->setAttributeCatatanLuka($model);
            }
            $isDokumenEklaim = ArrayHelper::getValue($data, 'is_dokumen_eklaim', false);
            $model->is_dokumen_eklaim = $isDokumenEklaim;
            
            if ($model->validate()) {
                $response = $this->_restRanap->post('asesmen-medis/save-asmed-ranap', [
                    'form_params' => [
                        $modelName => $model->attributes,
                        'form_name' => $modelName,
                    ]
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $modelName);
            }
        }

        return $this->renderAjax('asesmen-medis/' . $path, compact(
            'title', 'model', 'isDokumenEklaim', 'asesmenMedisId', 'pegawaiId', 'pegawaiNama', 'modelName', 'dataDokter'
        ));
    }

    private function mapingForm($formAsesmenCode, $pendaftaranId, $pasienAdmisiId)
    {
        $optionsFormAsmed = $this->getFormAsmed($pendaftaranId, $pasienAdmisiId);
        $lookup = ArrayHelper::getValue($optionsFormAsmed, 'lookup', []);
        $lookup = ArrayHelper::map($lookup, 'lookup_kode', 'lookup_name');
        $title = ArrayHelper::getValue($lookup, $formAsesmenCode, 'Halaman tidak ditemukan');

        switch ($formAsesmenCode) {
            case DocoConstants::KODE_FORM_INSPEKSIUS:
                $model = new InspeksiusForm;
                $path = 'form_inspeksius';
                break;

            case DocoConstants::KODE_FORM_IMUNOLOOGI:
                $model = new ImunologiForm;
                $path = 'form_imunologi';
                break;

            case DocoConstants::KODE_FORM_GERIATRI:
                $model = new GeriatriForm;
                $path = 'form_geriatri';
                break;

            case DocoConstants::KODE_FORM_KEKERASAN:
                $model = new KekerasanForm;
                $path = 'form_kekerasan';
                break;

            case DocoConstants::KODE_FORM_TERMINAL:
                $model = new TerminalForm;
                $path = 'form_terminal';
                break;

            case DocoConstants::KODE_FORM_KRONIK:
                $model = new NyeriKronikForm;
                $path = 'form_kronik';
                break;

            case DocoConstants::KODE_FORM_NEONATUS:
                $model = new NeonatusForm;
                $path = 'form_neonatus';
                break;

            case DocoConstants::KODE_FORM_KECANDUAN:
                $model = new KecanduanForm;
                $path = 'form_kecanduan';
                break;

            case DocoConstants::KODE_FORM_PSIKIATRIS:
                $model = new PsikiatrisForm;
                $path = 'form_psikiatris';
                break;

            case DocoConstants::KODE_FORM_ANAK:
                $model = new AnakForm;
                $path = 'form_anak';
                break;

            case DocoConstants::KODE_FORM_BEDAH:
                $model = new PraBedahForm;
                $path = 'form_bedah';
                break;

            case DocoConstants::KODE_FORM_LUKA_BAKAR:
                $model = new LukaBakarForm;
                $path = 'form_luka_bakar';
                break;

            case DocoConstants::KODE_FORM_GINEKOLOGI:
                $model = new GinekologiForm;
                $path = 'form_ginekologi';
                break;

            case DocoConstants::KODE_FORM_KEBIDANAN:
                $model = new KebidananForm;
                $path = 'form_kebidanan';
                break;

            case DocoConstants::KODE_FORM_MATA:
                $model = new MataForm;
                $path = 'form_mata';
                break;

            case DocoConstants::KODE_FORM_KULIT:
                $model = new KulitForm;
                $path = 'form_kulit';
                break;

            case DocoConstants::KODE_FORM_SYARAF:
                $model = new SyarafForm;
                $path = 'form_syaraf';
                break;

            case DocoConstants::KODE_FORM_MEDIS_BEDAH:
                $model = new MedisBedahForm;
                $path = 'form_medis_bedah';
                break;

            case DocoConstants::KODE_FORM_KESEHATAN_ANAK:
                $model = new KesehatanAnakForm;
                $path = 'form_kesehatan_anak';
                break;

            case DocoConstants::KODE_FORM_PENYAKIT_DALAM:
                $model = new PenyakitDalamForm;
                $path = 'form_penyakit_dalam';
            break;

            default:
                $model = new DefaultAsesmenRanapForm;
                $path = 'form_default';
                break;
        }

        return [
            'model' => $model,
            'path' => $path,
            'title' => $title,
            'model' => $model,
        ];
    }

    private function getFormAsmed($pendaftaranId, $pasienAdmisiId)
    {
        $response = $this->_restRanap->get('asesmen-medis/get-form-asmed', [
            'query' => [
                'pendaftaran_id' => $pendaftaranId,
                'pasienadmisi_id' => $pasienAdmisiId,
            ]
        ]);

        return json_decode($response->getBody(), true)['response'];
    }

    public function actionModalAsesmenSpesialis($id, $pasienadmisi_id = null)
    {
        $pendaftaranId = DocoHelpers::decrypt($id);
        $pasienAdmisiId = DocoHelpers::decrypt($pasienadmisi_id);
        $infoPasien = [];
        try {
            $user = Yii::$app->session->get('user_identity');
            $listData = [];
            $response = $this->_restIgd->get('riwayat-pasien/list-asmed-spesialis', [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId,
                    'pasienadmisi_id' => $pasienAdmisiId,
                ],
                'form_params' => []
            ]);
            $response = json_decode($response->getBody(), true);
            $response = ArrayHelper::getValue($response, 'response', []);
            $listData = ArrayHelper::getValue($response, 'asmed', []);
            $infoPasien = ArrayHelper::getValue($response, 'info_pasien', []);
        } catch (\Exception $e) {
            $infoPasien = null;
            $this->logError($e);
            $listData = [];
        }

        return $this->renderAjax('asesmen-medis/_modal_asmed_spesialis', ['infoPasien' => $infoPasien, 'listData' => $listData]);
    }

    private function getDataAsmed($asesmenMedisId)
    {
        $response = $this->_restRanap->get('asesmen-medis/get-data-asmed', [
            'query' => [
                'asesmenmedis_id' => $asesmenMedisId,
            ]
        ]);

        return json_decode($response->getBody(), true)['response'];
    }

    private function setAttributeCatatanLuka($model)
    {
        $countCatatanLokasi = count($model->panjang_luka);
        $catatanLuka = [];
        for ($i=0; $i < $countCatatanLokasi; $i++) {
            $catatanLuka[] = [
                'panjang_luka' => isset($model->panjang_luka[$i]) ? $model->panjang_luka[$i] : $model->panjang_luka,
                'lebar_luka' => isset($model->lebar_luka[$i]) ? $model->lebar_luka[$i] : $model->lebar_luka,
                'kedalaman_luka' => isset($model->kedalaman_luka[$i]) ? $model->kedalaman_luka[$i] : $model->kedalaman_luka,
                'lokasi_goa' => isset($model->lokasi_goa[$i]) ? $model->lokasi_goa[$i] : $model->lokasi_goa,
                'panjang_goa' => isset($model->panjang_goa[$i]) ? $model->panjang_goa[$i] : $model->panjang_goa,
                'lokasi_sinus' => isset($model->lokasi_sinus[$i]) ? $model->lokasi_sinus[$i] : $model->lokasi_sinus,
                'panjang_sinus' => isset($model->panjang_sinus[$i]) ? $model->panjang_sinus[$i] : $model->panjang_sinus,
                'hitam' => isset($model->hitam[$i]) ? $model->hitam[$i] : $model->hitam,
                'kuning' => isset($model->kuning[$i]) ? $model->kuning[$i] : $model->kuning,
                'merah' => isset($model->merah[$i]) ? $model->merah[$i] : $model->merah,
                'pink' => isset($model->pink[$i]) ? $model->pink[$i] : $model->pink,
                'terluka' => isset($model->terluka[$i]) ? $model->terluka[$i] : $model->terluka,
                'kulit_luka' => isset($model->kulit_luka[$i]) ? $model->kulit_luka[$i] : $model->kulit_luka,
                'stadium_luka' => isset($model->stadium_luka[$i]) ? $model->stadium_luka[$i] : $model->stadium_luka,
                'tanda_infeksi' => isset($model->tanda_infeksi[$i]) ? $model->tanda_infeksi[$i] : $model->tanda_infeksi,
                'nyeri_luka' => isset($model->nyeri_luka[$i]) ? $model->nyeri_luka[$i] : $model->nyeri_luka,
                'jumlah_exudate' => isset($model->jumlah_exudate[$i]) ? $model->jumlah_exudate[$i] : $model->jumlah_exudate,
                'warna_exudate' => isset($model->warna_exudate[$i]) ? $model->warna_exudate[$i] : $model->warna_exudate,
                'bau_exudate' => isset($model->bau_exudate[$i]) ? $model->bau_exudate[$i] : $model->bau_exudate,
                'pembersihan_luka' => isset($model->pembersihan_luka[$i]) ? $model->pembersihan_luka[$i] : $model->pembersihan_luka,
                'tanggal_assesmen' => isset($model->tanggal_assesmen[$i]) ? $model->tanggal_assesmen[$i] : $model->tanggal_assesmen,
                'pegawai_assesmen' => isset($model->pegawai_assesmen[$i]) ? $model->pegawai_assesmen[$i] : $model->pegawai_assesmen,
                'pegawai_assesmen_nama' => isset($model->pegawai_assesmen_nama[$i]) ? $model->pegawai_assesmen_nama[$i] : $model->pegawai_assesmen_nama,
            ];
        }
        $model->pemeriksaan_spesialis['catatan_luka'] = $catatanLuka;
        return $model;
    }

    private function saveAsmed($model, $formAsesmenCode)
    {
        $modelName = substr(strrchr(get_class($model), "\\"), 1);
        $data = Yii::$app->request->post();
        $isUnduhDokumen = ArrayHelper::getValue($data, 'is_dokumen_eklaim', 0);
        $isUnduhDokumen = $isUnduhDokumen == 1 ? true : false;
        $formData = ArrayHelper::getValue($data, $modelName, []);
        $model->attributes = $formData;
        $model->formasesmen_code = $formAsesmenCode;
        $model->pemeriksaan_spesialis = $model->attributes;
        if($formAsesmenCode == DocoConstants::KODE_FORM_LUKA_BAKAR) {
            $this->setAttributeCatatanLuka($model);
        }
        $isDokumenEklaim = ArrayHelper::getValue($data, 'is_dokumen_eklaim', false);
        $model->is_dokumen_eklaim = $isDokumenEklaim;
        if ($model->validate()) {
            $response = $this->_restRanap->post('asesmen-medis/save-asmed-ranap', [
                'form_params' => [
                    $modelName => $model->attributes,
                    'form_name' => $modelName,
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $modelName);
        }
    }

    public function actionModalAsmed()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('id');
        $pasienAdmisiId = $request->get('pasienadmisi_id');
        $pasienId = $request->get('pasien_id');
        $formAsesmenId = $request->get('formasesmen_id');
        $asesmenMedisId = $request->get('asesmenmedis_id');
        $isDokumenEklaim = $request->get('isDokumenEklaim');
        if($asesmenMedisId) {
            $dataAsmed = $this->getDataAsmed($asesmenMedisId);
            $isDokumenEklaim = ArrayHelper::getValue($dataAsmed, 'is_dokumen_eklaim');
        }
        
        $mapingForm = $this->mapingForm($formAsesmenId, $pendaftaranId, $pasienAdmisiId);
        $title = ArrayHelper::getValue($mapingForm, 'title');
        $title = ($asesmenMedisId) ? 'Edit Form '. $title : 'Input Form '. $title;
        return $this->renderAjax('asesmen-medis/_modal_asmed', compact(
                'title', 'pendaftaranId', 'pasienAdmisiId', 'pasienId', 'formAsesmenId', 'isDokumenEklaim', 'asesmenMedisId'
            )
        );
    }

    private function setAttribute($key, $value)
    {
        return isset($value[$key]) ? $value[$key] : $value;
    }

    private function getDataDokter()
    {
        $response = $this->_restRanap->get('asesmen-medis/get-data-dokter');

        return json_decode($response->getBody(), true)['response'];
    }
}
