<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\mcu\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\mcu\models\RiwayatPenyakitForm;
use app\modules\mcu\models\RiwayatPenyakitFormPrima;

class PemeriksaanPeriksaProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Riwayat Medis');
        $config = $this->checkConfigPrima();

        if ($config == 'prima') {
            $model = new RiwayatPenyakitFormPrima;
        } else {
            $model = new RiwayatPenyakitForm;
        }

        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id', null);
        if ($request->post()) {
            $model->load($request->post());
            $model->pendaftaran_id = $pendaftaran_id;
            if ($model->validate()) {
                try {
                    $response = Yii::$app->docoRest->mcu->post('pemeriksaan/save-riwayat', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    $result = $body;
                } catch (RequestException $e) {
                    $result = $e->getMessage();
                } catch (\Exception $e) {
                    $result = $e->getMessage();
                }
                return DocoHelpers::response($result, 200, $formName);
            } else {
                $response = $model->errors;
            }
            return DocoHelpers::response($response, 422, $formName);
        } else {
            $option = [
                0 => Yii::t('fe', 'Tidak'),
                1 => Yii::t('fe', 'Ya')
            ];
            $optionDiet = [
                Yii::t('fe', 'Turun Berat Badan') => Yii::t('fe', 'Turun Berat Badan'),
                Yii::t('fe', 'Naik Berat Badan') => Yii::t('fe', 'Naik Berat Badan'),
                Yii::t('fe', 'Tidak Diet') => Yii::t('fe', 'Tidak Diet'),
            ];
            $optionTidur = [
                Yii::t('fe', '< 4 Jam') => Yii::t('fe', '< 4 Jam'),
                Yii::t('fe', '4 - 6 Jam') => Yii::t('fe', '4 - 6 Jam'),
                Yii::t('fe', '> 6 Jam') => Yii::t('fe', '> 6 Jam'),
            ];

            if ($config == 'prima') {
                $cekExistData = $this->recordLatestMcu($pendaftaran_id);
                $this->setAdditionalPrima($model, $cekExistData);
                $riwayatRekap = [];
                if (!empty($model->tahun_mulai)) {
                    foreach ($model->tahun_mulai as $key => $value) {
                        $data = [
                            "tahun_mulai" => isset($model->tahun_mulai[$key]) ? $model->tahun_mulai[$key] : null,
                            "tahun_selesai"  => isset($model->tahun_selesai[$key]) ? $model->tahun_selesai[$key] : null,
                            "perusahaan"  => isset($model->perusahaan[$key]) ? $model->perusahaan[$key] : null,
                            "jabatan"  => isset($model->jabatan[$key]) ? $model->jabatan[$key] : null
                        ];

                        $riwayatRekap[] = $data;
                    }
                }

                $currentYear = Date('Y');
                $earliestYear = 1970;
                $currentSelectYear = [];
                $currentSelectEndYear = [];
                $currentSelectEndYear['Sekarang'] = "Sekarang";
                while ($currentYear >= $earliestYear) {
                    $currentSelectYear[$currentYear] = $currentYear;
                    $currentSelectEndYear[$currentYear] = $currentYear;
                    $currentYear -= 1;
                }

                return $controller->renderAjax('__riwayat_penyakit_prima', [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasien_id' => $pasien_id,
                    'title' => $title,
                    'model' => $model,
                    'option' => $option,
                    'optionDiet' => $optionDiet,
                    'optionTidur' => $optionTidur,
                    'riwayatRekap' => $riwayatRekap,
                    'currentSelectYear' => $currentSelectYear,
                    'currentSelectEndYear' => $currentSelectEndYear,
                ]);
            } else {
                $cekExistData = $this->cekExistData($pendaftaran_id);
                $this->setData($model, $cekExistData);
                return $controller->renderAjax('__riwayat_penyakit', [
                    'pendaftaran_id' => $pendaftaran_id,
                    'pasien_id' => $pasien_id,
                    'title' => $title,
                    'model' => $model,
                    'option' => $option,
                    'optionDiet' => $optionDiet,
                    'optionTidur' => $optionTidur
                ]);
            }
        }
    }

    private function cekExistData($pendaftaran_id)
    {
        $response = Yii::$app->docoRest->mcu->get('pemeriksaan/cek-riwayat-penyakit?pendaftaran_id=' . $pendaftaran_id);
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];

        return $response;
    }

    private function setData($model, $data)
    {
        $data = isset($data['additional_data']) ? json_decode($data['additional_data'], true) : [];

        $riwayat_diderita = $riwayat_alergi = $riwayat_dirawat_rs = $riwayat_operasi =
            $riwayat_imunisasi = $rokok = $alkohol = $kopi = 0;

        $r_diderita_catatan = $r_alergi_catatan = $r_dirawat_rs_catatan = $r_operasi_catatan =
            $r_imunisasi_catatan = $menstruasi = $riwayat_kontrasepsi = $riwayat_melahirkan =
            $riwayat_keguguran = $sedang_hamil = $riwayat_pap_smear = $riwayat_penyakit_keluarga =
            $olahraga = $obat_rutin = '';

        $diet = Yii::t('fe', 'Turun Berat Badan');
        $tidur = Yii::t('fe', '< 4 Jam');

        if (!empty($data)) {
            $riwayat = ArrayHelper::getValue($data, 'riwayat_penyakit_terdahulu');
            $perempuan = ArrayHelper::getValue($data, 'khusus_perempuan');
            $kebiasaan = ArrayHelper::getValue($data, 'kebiasaan');

            // riwayat penyakit
            $riwayat_diderita = ArrayHelper::getValue($riwayat, 'riwayat_diderita');
            $riwayat_alergi = ArrayHelper::getValue($riwayat, 'riwayat_alergi');
            $riwayat_dirawat_rs = ArrayHelper::getValue($riwayat, 'riwayat_dirawat_rs');
            $riwayat_operasi = ArrayHelper::getValue($riwayat, 'riwayat_operasi');
            $riwayat_imunisasi = ArrayHelper::getValue($riwayat, 'riwayat_imunisasi');
            $r_diderita_catatan = ArrayHelper::getValue($riwayat, 'riwayat_diderita_catatan');
            $r_alergi_catatan = ArrayHelper::getValue($riwayat, 'riwayat_alergi_catatan');
            $r_dirawat_rs_catatan = ArrayHelper::getValue($riwayat, 'riwayat_dirawat_rs_catatan');
            $r_operasi_catatan = ArrayHelper::getValue($riwayat, 'riwayat_operasi_catatan');
            $r_imunisasi_catatan = ArrayHelper::getValue($riwayat, 'riwayat_imunisasi_catatan');

            // khusus perempuan
            $menstruasi = ArrayHelper::getValue($perempuan, 'menstruasi');
            $riwayat_kontrasepsi = ArrayHelper::getValue($perempuan, 'riwayat_kontrasepsi');
            $riwayat_melahirkan = ArrayHelper::getValue($perempuan, 'riwayat_melahirkan');
            $riwayat_keguguran = ArrayHelper::getValue($perempuan, 'riwayat_keguguran');
            $sedang_hamil = ArrayHelper::getValue($perempuan, 'sedang_hamil');
            $riwayat_pap_smear = ArrayHelper::getValue($perempuan, 'riwayat_pap_smear');
            $riwayat_penyakit_keluarga = ArrayHelper::getValue($perempuan, 'riwayat_penyakit_keluarga');

            // kebiasaan
            $rokok = ArrayHelper::getValue($kebiasaan, 'rokok');
            $alkohol = ArrayHelper::getValue($kebiasaan, 'alkohol');
            $kopi = ArrayHelper::getValue($kebiasaan, 'kopi');
            $olahraga = ArrayHelper::getValue($kebiasaan, 'olahraga');
            $diet = ArrayHelper::getValue($kebiasaan, 'diet');
            $tidur = ArrayHelper::getValue($kebiasaan, 'tidur');
            $obat_rutin = ArrayHelper::getValue($kebiasaan, 'obat_rutin');
        }

        // riwayat penyakit terdahulu
        $model->riwayat_diderita = $riwayat_diderita;
        $model->riwayat_diderita_catatan = $r_diderita_catatan;
        $model->riwayat_alergi = $riwayat_alergi;
        $model->riwayat_alergi_catatan = $r_alergi_catatan;
        $model->riwayat_dirawat_rs = $riwayat_dirawat_rs;
        $model->riwayat_dirawat_rs_catatan = $r_dirawat_rs_catatan;
        $model->riwayat_operasi = $riwayat_operasi;
        $model->riwayat_operasi_catatan = $r_operasi_catatan;
        $model->riwayat_imunisasi = $riwayat_imunisasi;
        $model->riwayat_imunisasi_catatan = $r_imunisasi_catatan;

        // khusus perempuan
        $model->menstruasi = $menstruasi;
        $model->riwayat_kontrasepsi = $riwayat_kontrasepsi;
        $model->riwayat_melahirkan = $riwayat_melahirkan;
        $model->riwayat_keguguran = $riwayat_keguguran;
        $model->sedang_hamil = $sedang_hamil;
        $model->riwayat_pap_smear = $riwayat_pap_smear;
        $model->riwayat_penyakit_keluarga = $riwayat_penyakit_keluarga;

        // kebiasaan
        $model->rokok = $rokok;
        $model->alkohol = $alkohol;
        $model->kopi = $kopi;
        $model->olahraga = $olahraga;
        $model->diet = $diet;
        $model->tidur = $tidur;
        $model->obat_rutin = $obat_rutin;

        $model->attributes = $data;
        return $model->attributes;
    }

    /**
     * Check config for RS Prima.
     * 
     * @author Maulana Muhammad Rizky
     * @return bool
     */
    private function checkConfigPrima()
    {
        $response = Yii::$app->docoRest->mcu->get('allow/get-konfig-mcu-prima');
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];
        $konfig = ArrayHelper::getValue($response, 'konfig');

        return $konfig;
    }


    /**
     * Set Model for RS Prima.
     * 
     * @author Maulana Muhammad Rizky.
     */
    private function setAdditionalPrima($model, $data)
    {
        $data = isset($data['additional_data']) ? json_decode($data['additional_data'], true) : [];

        if (!empty($data['pasien_phr'])) {
            $model->pekerjaan = ArrayHelper::getValue($data['pasien_phr'], 'pekerjaan');
            $model->lokasi_kerja = ArrayHelper::getValue($data['pasien_phr'], 'lokasi_kerja');
            $model->matriks_pemeriksaan = ArrayHelper::getValue($data['pasien_phr'], 'matriks_pemeriksaan');
            $model->nama_perusaahan = ArrayHelper::getValue($data['pasien_phr'], 'nama_perusaahan');
            $model->tipe_pekerja = ArrayHelper::getValue($data['pasien_phr'], 'tipe_pekerja');
            $model->prosedur_pemeriksaan = ArrayHelper::getValue($data['pasien_phr'], 'prosedur_pemeriksaan');
            $model->prosedur_pemeriksaan_text = ArrayHelper::getValue($data['pasien_phr'], 'prosedur_pemeriksaan_text');
        }

        if (!empty($data['riwayat_pekerjaan'])) {
            $model->tahun_mulai = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'tahun_mulai');
            $model->tahun_selesai = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'tahun_selesai');
            $model->perusahaan = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'perusahaan');
            $model->jabatan = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'jabatan');
            $model->uraian_singkat = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'uraian_singkat');
            $model->paparan_tempat_kerja = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_tempat_kerja');
            $model->paparan_tidak_ada = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_tidak_ada');
            $model->paparan_bising = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_bising');
            $model->paparan_kimia = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_kimia');
            $model->paparan_radiasi = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_radiasi');
            $model->paparan_stress = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_stress');
            $model->paparan_ergonomis = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_ergonomis');
            $model->paparan_lainnya = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_lainnya');
            $model->paparan_secara_singkat = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'paparan_secara_singkat');
            $model->resiko_confined_space = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_confined_space');
            $model->resiko_security = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_security');
            $model->resiko_pengemudi = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_pengemudi');
            $model->resiko_lain = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_lain');
            $model->resiko_operator_berat = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_operator_berat');
            $model->resiko_bekerja_ketinggian = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_bekerja_ketinggian');
            $model->resiko_fire_brigade = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_fire_brigade');
            $model->resiko_tangki_penyelam = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_tangki_penyelam');
            $model->resiko_awak_mobil = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_awak_mobil');
            $model->resiko_lain_text = ArrayHelper::getValue($data['riwayat_pekerjaan'], 'resiko_lain_text');
        }

        if (!empty($data['tanda_vital'])) {
            $model->vital_tekanan_darah = ArrayHelper::getValue($data['tanda_vital'], 'vital_tekanan_darah');
            $model->vital_nadi = ArrayHelper::getValue($data['tanda_vital'], 'vital_nadi');
            $model->vital_suhu = ArrayHelper::getValue($data['tanda_vital'], 'vital_suhu');
            $model->vital_respirasi = ArrayHelper::getValue($data['tanda_vital'], 'vital_respirasi');
        }

        if (!empty($data['keluhan_saatini'])) {
            $model->keluhan_saat_ini = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_saat_ini');
            $model->keluhan_migrain = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_migrain');
            $model->keluhan_migrain_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_migrain_text');
            $model->keluhan_epilepsi = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_epilepsi');
            $model->keluhan_epilepsi_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_epilepsi_text');
            $model->keluhan_gangguan_pengelihatan = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_gangguan_pengelihatan');
            $model->keluhan_gangguan_pengelihatan_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_gangguan_pengelihatan_text');
            $model->keluhan_gangguan_pendengaran = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_gangguan_pendengaran');
            $model->keluhan_masalah_hidung = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_masalah_hidung');
            $model->keluhan_tbc = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_tbc');
            $model->keluhan_pneumonia = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_pneumonia');
            $model->keluhan_asma = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_asma');
            $model->keluhan_gangguang_saluran = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_gangguang_saluran');
            $model->keluhan_hernia = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_hernia');
            $model->keluhan_nyeri_dada = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_nyeri_dada');
            $model->keluhan_penyakit_ginjal = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_penyakit_ginjal');
            $model->keluhan_batu_ginjal = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_batu_ginjal');
            $model->keluhan_penyakit_kulit = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_penyakit_kulit');
            $model->keluhan_riwayat_kecelakaan = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_riwayat_kecelakaan');
            $model->keluhan_riwayat_inap_rs = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_riwayat_inap_rs');
            $model->keluhan_riwayat_operasi = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_riwayat_operasi');
            $model->keluhan_alergi = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_alergi');
            $model->keluhan_demam_reumatik = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_demam_reumatik');
            $model->keluhan_demam_typhoid = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_demam_typhoid');
            $model->keluhan_demam_berdarah = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_demam_berdarah');
            $model->keluhan_malaria = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_malaria');
            $model->keluhan_hepatitis = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_hepatitis');
            $model->keluhan_diabetes = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_diabetes');
            $model->keluhan_nyeri_sendi = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_nyeri_sendi');
            $model->keluhan_nyeri_punggung = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_nyeri_punggung');
            $model->keluhan_varises = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_varises');
            $model->keluhan_kanker = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_kanker');
            $model->keluhan_psikiatrik = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_psikiatrik');
            $model->keluhan_penyakit_kelamin = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_penyakit_kelamin');
            $model->keluhan_masalah_kebidanan = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_masalah_kebidanan');
            $model->keluhan_hpht = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_hpht');
            $model->keluhan_menarche = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_menarche');
            $model->keluhan_keteranganobat = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_keteranganobat');
            $model->keluhan_perubahanbb = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_perubahanbb');
            $model->keluhan_perubahanbb_type = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_perubahanbb_type');
            $model->keluhan_perubahanbb_kg = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_perubahanbb_kg');
            $model->keluhan_perubahanbb_napsumakan = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_perubahanbb_napsumakan');
            $model->keluhan_merokok = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_merokok');
            $model->keluhan_merokok_jenis = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_merokok_jenis');
            $model->keluhan_merokok_jumlah = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_merokok_jumlah');
            $model->keluhan_merokok_sejak = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_merokok_sejak');
            $model->keluhan_alkohol = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_alkohol');
            $model->keluhan_alkohol_jenis = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_alkohol_jenis');
            $model->keluhan_alkohol_jumlah = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_alkohol_jumlah');
            $model->keluhan_alkohol_sejak = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_alkohol_sejak');
            $model->keluhan_gangguan_pendengaran_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_gangguan_pendengaran_text');
            $model->keluhan_masalah_hidung_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_masalah_hidung_text');
            $model->keluhan_tbc_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_tbc_text');
            $model->keluhan_pneumonia_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_pneumonia_text');
            $model->keluhan_asma_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_asma_text');
            $model->keluhan_gangguang_saluran_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_gangguang_saluran_text');
            $model->keluhan_hernia_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_hernia_text');
            $model->keluhan_nyeri_dada_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_nyeri_dada_text');
            $model->keluhan_penyakit_ginjal_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_penyakit_ginjal_text');
            $model->keluhan_batu_ginjal_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_batu_ginjal_text');
            $model->keluhan_penyakit_kulit_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_penyakit_kulit_text');
            $model->keluhan_riwayat_kecelakaan_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_riwayat_kecelakaan_text');
            $model->keluhan_riwayat_inap_rs_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_riwayat_inap_rs_text');
            $model->keluhan_riwayat_operasi_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_riwayat_operasi_text');
            $model->keluhan_alergi_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_alergi_text');
            $model->keluhan_demam_reumatik_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_demam_reumatik_text');
            $model->keluhan_demam_typhoid_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_demam_typhoid_text');
            $model->keluhan_demam_berdarah_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_demam_berdarah_text');
            $model->keluhan_malaria_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_malaria_text');
            $model->keluhan_hepatitis_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_hepatitis_text');
            $model->keluhan_diabetes_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_diabetes_text');
            $model->keluhan_nyeri_sendi_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_nyeri_sendi_text');
            $model->keluhan_nyeri_punggung_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_nyeri_punggung_text');
            $model->keluhan_varises_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_varises_text');
            $model->keluhan_kanker_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_kanker_text');
            $model->keluhan_psikiatrik_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_psikiatrik_text');
            $model->keluhan_penyakit_kelamin_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_penyakit_kelamin_text');
            $model->keluhan_keteranganobat_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_keteranganobat_text');
            $model->keluhan_lainnya_text = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_lainnya_text');
            $model->keluhan_lainnya = ArrayHelper::getValue($data['keluhan_saatini'], 'keluhan_lainnya');
        }

        if (!empty($data['kebiasaan_olahraga'])) {
            $model->tingkat_kebiasaan_olahraga = ArrayHelper::getValue($data['kebiasaan_olahraga'], 'tingkat_kebiasaan_olahraga');
            $model->jenis_olahraga = ArrayHelper::getValue($data['kebiasaan_olahraga'], 'jenis_olahraga');
        }

        if (!empty($data['penyakit_keluarga'])) {
            $model->penyakit_jantung_stroke = ArrayHelper::getValue($data['penyakit_keluarga'], 'penyakit_jantung_stroke');
            $model->penyakit_kanker_tumor = ArrayHelper::getValue($data['penyakit_keluarga'], 'penyakit_kanker_tumor');
            $model->penyakit_riwayat_saudara = ArrayHelper::getValue($data['penyakit_keluarga'], 'penyakit_riwayat_saudara');
        }
        // $model->attributes = $data;
        return $model->attributes;
    }

    /**
     * @param int $pendaftaran_id
     */
    private function recordLatestMcu($pendaftaran_id)
    {
        $response = Yii::$app->docoRest->mcu->get('pemeriksaan/cek-riwayat-penyakit?pendaftaran_id=' . $pendaftaran_id);
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];

        return $response;
    }
}
