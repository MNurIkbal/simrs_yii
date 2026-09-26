<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
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
use app\modules\mcu\models\PemeriksaanFisikDefaultForm;
use app\modules\mcu\models\PemeriksaanFisikNewForm;

class PemeriksaanFisikPeriksaProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pemeriksaan Fisik');
        $config = $this->checkConfigPrima();
        if ($config == 'prima') {
            $modelFisik = new PemeriksaanFisikNewForm;
        } else {
            $modelFisik = new PemeriksaanFisikDefaultForm;
        }
        $formName = substr(strrchr(get_class($modelFisik), "\\"), 1);
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id', null);
        $jenis_kelamin = $controller->_jenis_kelamin;
        if ($request->post()) {
            $modelFisik->load($request->post());
            $modelFisik->pendaftaran_id = $pendaftaran_id;
            if ($modelFisik->validate()) {
                try {
                    $response = Yii::$app->docoRest->mcu->post('pemeriksaan/save-pemeriksaan-fisik-default', [
                        'form_params' => $modelFisik->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    $result = $body;
                } catch (RequestException $e) {
                    $result = $e->getMessage();
                } catch (\Exception $e) {
                    $result = $e->getMessage();
                }
                return DocoHelpers::response($result,200,$formName);
            }
            else {
                $response = $modelFisik->errors;
            }
            return DocoHelpers::response($response,422,$formName);
        }
        else {
            $option = [
              0 => Yii::t('fe', 'Normal'),
              1 => Yii::t('fe', 'Tidak')
            ];
            $option_extremitas = [ //new
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Terbatas')
            ];
            $option_cervical = [ //new
                0 => Yii::t('fe', 'Negatif'),
                1 => Yii::t('fe', 'Positif')
            ];
            $option_range_of_motion = [ //new
                0 => Yii::t('fe', 'Tidak Diperiksa'),
                1 => Yii::t('fe', 'Diperiksa')
            ];
            $option_leher = [ //new
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Tidak Normal')
            ];
            $option_yesorno_new = [
                0 => Yii::t('fe', 'Ya'),
                1 => Yii::t('fe', 'Tidak'),
            ];
            $option_kepala_rambut = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Rontok'),
                2 => Yii::t('fe', 'Ketombe'),
                3 => Yii::t('fe', 'Rambut Bercabang'),
            ];
            $option_yesorno = [
                0 => Yii::t('fe', 'Tidak'),
                1 => Yii::t('fe', 'Ya'),
            ];
            $option_sklera = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Ikterik'),
            ];
            $option_pupil = [
                0 => Yii::t('fe', 'Isokor'),
                1 => Yii::t('fe', 'Anisokor'),
                2 => Yii::t('fe', 'Reflex Cahaya Kurang'),
            ];
            $option_lensa = [
                0 => Yii::t('fe', 'Jernih'),
                1 => Yii::t('fe', 'Keruh'),
            ];
            $option_adadantiada = [
                0 => Yii::t('fe', 'Tidak Ada'),
                1 => Yii::t('fe', 'Ada'),
            ];
            $option_liang_telinga = [
                0 => Yii::t('fe', 'Bersih'),
                1 => Yii::t('fe', 'Tidak Bersih'),
            ];
            $option_timpani = [
                0 => Yii::t('fe', 'Utuh'),
                1 => Yii::t('fe', 'Tidak Utuh'),
            ];
            $option_hidung = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Tidak'),
            ];
            $option_gigi = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Karies'),
                2 => Yii::t('fe', 'Kalkulus'),
                3 => Yii::t('fe', 'Radix'),
                4 => Yii::t('fe', 'Abrasi'),
                5 => Yii::t('fe', 'Missing'),
                6 => Yii::t('fe', 'Mobility'),
                7 => Yii::t('fe', 'Impaksi'),
            ];
            $option_faring = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Hiperemis'),
            ];
            $option_tonsil = [
                0 => Yii::t('fe', 'T1/T1'),
            ];
            $option_ictus = [
                0 => Yii::t('fe', 'Tidak Nampak'),
                1 => Yii::t('fe', 'Nampak'),
            ];
            $option_batas_jantung = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Kesan Membesar'),
            ];
            $option_irama = [
                0 => Yii::t('fe', 'Ireguler'),
                1 => Yii::t('fe', 'Reguler'),
            ];
            $option_inspeksi = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Scaphoid'),
                2 => Yii::t('fe', 'Protuberant'),
            ];
            $option_auskultasi = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Bising Usus Meningkat'),
                2 => Yii::t('fe', 'Bising Usus Menurun'),
            ];
            $option_perkusi = [
                0 => Yii::t('fe', 'Timpani'),
                1 => Yii::t('fe', 'Hipertimpani'),
                2 => Yii::t('fe', 'Pekak'),
                3 => Yii::t('fe', 'Keterangan'),
            ];
            $option_toucher = [
                0 => Yii::t('fe', 'Normal'),
                1 => Yii::t('fe', 'Hemorrhoid'),
                2 => Yii::t('fe', 'Prostat Teraba Besar'),
                3 => Yii::t('fe', 'Lendir Darah +'),
                4 => Yii::t('fe', 'Tidak'),
            ];
            $option_plusminus = [
                0 => Yii::t('fe', '+'),
                1 => Yii::t('fe', '-'),
            ];
            $getButaParsialResponse = Yii::$app->docoRest->mcu->get('allow/get-lookup-by?column=lookup_type&value=' . DocoConstants::BUTA_WARNA_PARSIAL);
            $butaParsialData = json_decode($getButaParsialResponse->getBody(), true);
            $buta_parsial = ArrayHelper::getValue($butaParsialData, 'response');
            $buta_parsial = ArrayHelper::map($buta_parsial, 'lookup_name', 'lookup_value');
            // Get pemeriksaan fisik Kulit
            $list_data = $this->getSupportDataFisik();
            $optBagianTubuh = $list_data['data_bagiantubuh'];
            $optBagianTubuhGigi = $list_data['detail_gigi'];
            $optBagianTubuhabdomen = $list_data['detail_abdomen'];
            $cekExistData = $this->cekExistData($pendaftaran_id);
            $data_bmi = ArrayHelper::getValue($cekExistData, 'data-bmi', []);
            
            $responsePemeriksaanFisik = Yii::$app->docoRest->mcu->post('pemeriksaan/get-pemeriksaan-fisik?pendaftaranId=' . $pendaftaran_id);
            $model = json_decode($responsePemeriksaanFisik->getBody(), true);
            $resAnatomiTubuh = isset($model["response"]["bagian_kulit"]) ? $model["response"]["bagian_kulit"] : [];
            $dataAnatomi =  json_encode($resAnatomiTubuh, JSON_FORCE_OBJECT);
            $counter = count($resAnatomiTubuh) + 1;
            $resAnatomiTubuhGigi = isset($model["response"]["bagian_gigi"]) ? $model["response"]["bagian_gigi"] : [];
            $dataAnatomiGigi =  json_encode($resAnatomiTubuhGigi, JSON_FORCE_OBJECT);
            $counterGigi = count($resAnatomiTubuhGigi) + 1;
            $resAnatomiTubuhAbdomen = isset($model["response"]["bagian_abdomen"]) ? $model["response"]["bagian_abdomen"] : [];
            $dataAnatomiAbdomen =  json_encode($resAnatomiTubuhAbdomen, JSON_FORCE_OBJECT);
            $counterAbdomen = count($resAnatomiTubuhAbdomen) + 1;
            
            if ($config == 'prima') {
                $this->getDataNew($modelFisik, $cekExistData);
                return $controller->renderAjax('__pemeriksaan_fisik_prima', [
                  'optBagianTubuhGigi' => $optBagianTubuhGigi, 'pendaftaran_decrypt' => $pendaftaran_id,
                  'pasien_id' => $pasien_id, 'optBagianTubuhabdomen' => $optBagianTubuhabdomen,
                  'title' => $title,'dataAnatomi' => $dataAnatomi, 'counter' => $counter,
                  'modelFisikNew' => $modelFisik, 'pendaftaran_id' => DocoHelpers::encrypt($pendaftaran_id),
                  'list_data' => $list_data, 'pasien_id' => DocoHelpers::decrypt($pasien_id),
                  'optBagianTubuh' => $optBagianTubuh, 'dataAnatomiGigi' => $dataAnatomiGigi,
                  'option_leher' => $option_leher, 'counterGigi' => $counterGigi,
                  'option_extremitas' => $option_extremitas, 'dataAnatomiAbdomen' => $dataAnatomiAbdomen,
                  'option_cervical' => $option_cervical, 'counterAbdomen' => $counterAbdomen,
                  'option_range_of_motion' => $option_range_of_motion,
                  'option_yesorno' => $option_yesorno_new,
                  'option_adadantiada' => $option_adadantiada,
                  'buta_parsial' => $buta_parsial,
              ]);
            } else {
                $this->getData($modelFisik, $cekExistData);
                return $controller->renderAjax('__pemeriksaan_fisik', get_defined_vars());
            }
        }
    }

    /**
     * Get Data Pemeriksaan
     */
    private function getData($modelFisik, $data)
    {
        $dataFisik = isset($data['data']['pemeriksaan_fisik']) ? json_decode($data['data']['pemeriksaan_fisik'], true) : [];

        $modelFisik->berat_badan = isset($data['data']['berat_badan']) ? $data['data']['berat_badan'] : null;
        $modelFisik->tinggi_badan = isset($data['data']['tinggi_badan']) ? $data['data']['tinggi_badan'] : null;
        $modelFisik->td_sistolik = isset($data['data']['td_sistolik']) ? $data['data']['td_sistolik'] : null;
        $modelFisik->td_diastolik = isset($data['data']['td_diastolik']) ? $data['data']['td_diastolik'] : null;
        $modelFisik->pernafasan = isset($data['data']['pernafasan']) ? $data['data']['pernafasan'] : null;
        $modelFisik->detak_nadi = isset($data['data']['detak_nadi']) ? $data['data']['detak_nadi'] : null;
        $modelFisik->suhu = isset($data['data']['suhu']) ? $data['data']['suhu'] : null;
        $modelFisik->imt = isset($data['data']['imt']) ? $data['data']['imt'] : null;
        $modelFisik->kategori_bb = isset($data['data']['kategori_bb']) ? $data['data']['kategori_bb'] : null;
        $modelFisik->td_kategori = isset($data['data']['td_kategori']) ? $data['data']['td_kategori'] : null;

        // kulit
        $kulitRambut = ArrayHelper::getValue($dataFisik, 'kulit_rambut');
        $modelFisik->kulit = ArrayHelper::getValue($kulitRambut, 'kulit', 0);
        $modelFisik->tato = ArrayHelper::getValue($kulitRambut, 'tato');
        $modelFisik->tindik = ArrayHelper::getValue($kulitRambut, 'tindik', 0);
        $modelFisik->limfonodi = ArrayHelper::getValue($kulitRambut, 'limfonodi', 0);
        $modelFisik->kepala_rambut = ArrayHelper::getValue($kulitRambut, 'kepala_rambut', 0);
        $modelFisik->note_kulit = ArrayHelper::getValue($kulitRambut, 'note_kulit');
        $modelFisik->note_limfonodi = ArrayHelper::getValue($kulitRambut, 'note_limfonodi');
        $modelFisik->note_tato = ArrayHelper::getValue($kulitRambut, 'note_tato');
        $modelFisik->note_tindik = ArrayHelper::getValue($kulitRambut, 'note_tindik');

        // mata
        $mata = ArrayHelper::getValue($dataFisik, 'mata', []);
        $modelFisik->visus_tanpa_kaca_mata_kanan = ArrayHelper::getValue($mata, 'visus_tanpa_kaca_mata_kanan');
        $modelFisik->visus_tanpa_kaca_mata_kiri = ArrayHelper::getValue($mata, 'visus_tanpa_kaca_mata_kiri');
        $modelFisik->visus_dengan_kaca_mata_kanan = ArrayHelper::getValue($mata, 'visus_dengan_kaca_mata_kanan');
        $modelFisik->visus_dengan_kaca_mata_kiri = ArrayHelper::getValue($mata, 'visus_dengan_kaca_mata_kiri');
        $modelFisik->buta_warna = ArrayHelper::getValue($mata, 'buta_warna', 0);
        $modelFisik->kelopak_mata = ArrayHelper::getValue($mata, 'kelopak_mata', 0);
        $modelFisik->kanjungtiva = ArrayHelper::getValue($mata, 'kanjungtiva', 0);
        $modelFisik->sklera = ArrayHelper::getValue($mata, 'sklera', 0);
        $modelFisik->pupil = ArrayHelper::getValue($mata, 'pupil', 0);
        $modelFisik->bola_mata = ArrayHelper::getValue($mata, 'bola_mata', 0);
        $modelFisik->lensa_mata = ArrayHelper::getValue($mata, 'lensa_mata', 0);
        $modelFisik->koreksi_kanan = ArrayHelper::getValue($mata, 'koreksi_kanan');
        $modelFisik->koreksi_kiri = ArrayHelper::getValue($mata, 'koreksi_kiri');
        $modelFisik->note_kelopak_mata = ArrayHelper::getValue($mata, 'note_kelopak_mata');
        $modelFisik->note_kanjungtiva = ArrayHelper::getValue($mata, 'note_kanjungtiva');
        $modelFisik->gerakan_bola_mata = ArrayHelper::getValue($mata, 'gerakan_bola_mata');
        $modelFisik->note_gerakan_bola_mata = ArrayHelper::getValue($mata, 'note_gerakan_bola_mata');

        // ? get attribute telinga
        $telinga = ArrayHelper::getValue($dataFisik, 'telinga', []);
        $modelFisik->kelainan_daun_telinga = ArrayHelper::getValue($telinga, 'kelainan_daun_telinga', 0);
        $modelFisik->serumen_prop = ArrayHelper::getValue($telinga, 'serumen_prop', 0);
        $modelFisik->liang_telinga_luar = ArrayHelper::getValue($telinga, 'liang_telinga_luar', 0);
        $modelFisik->membran_timpani = ArrayHelper::getValue($telinga, 'membran_timpani', 0);
        $modelFisik->note_kelainan_daun_telinga = ArrayHelper::getValue($telinga, 'note_kelainan_daun_telinga');
        $modelFisik->note_serumen_prop = ArrayHelper::getValue($telinga, 'note_serumen_prop');
        $modelFisik->note_membran_timpani = ArrayHelper::getValue($telinga, 'note_membran_timpani');

        // ? get attribute hidung
        $periksa_hidung = ArrayHelper::getValue($dataFisik, 'periksa_hidung', []);
        $modelFisik->hidung = ArrayHelper::getValue($periksa_hidung, 'hidung', 0);
        $modelFisik->note_hidung = ArrayHelper::getValue($periksa_hidung, 'note_hidung');

        // ? get attribute hidung dan tenggorokan
        $mulut_tenggorokan = ArrayHelper::getValue($dataFisik, 'mulut_tenggorokan', []);
        $modelFisik->gigi = ArrayHelper::getValue($mulut_tenggorokan, 'gigi', 0);
        $modelFisik->faring = ArrayHelper::getValue($mulut_tenggorokan, 'faring', 0);
        $modelFisik->tonsil = ArrayHelper::getValue($mulut_tenggorokan, 'tonsil', 0);
        $modelFisik->tonsil_text = ArrayHelper::getValue($mulut_tenggorokan, 'tonsil_text');
        $modelFisik->kelenjar_tiroid = ArrayHelper::getValue($mulut_tenggorokan, 'kelenjar_tiroid', 0);
        $modelFisik->note_kelenjar_tiroid = ArrayHelper::getValue($mulut_tenggorokan, 'note_kelenjar_tiroid');

        // ? get attribute paruparu
        $paruparu = ArrayHelper::getValue($dataFisik, 'paruparu', []);
        $modelFisik->inspeksi_paru = ArrayHelper::getValue($paruparu, 'inspeksi_paru', 0);
        $modelFisik->palpasi_paru = ArrayHelper::getValue($paruparu, 'palpasi_paru', 0);
        $modelFisik->perkusi_paru = ArrayHelper::getValue($paruparu, 'perkusi_paru', 0);
        $modelFisik->auskultasi_paru = ArrayHelper::getValue($paruparu, 'auskultasi_paru', 0);
        $modelFisik->note_inspeksi_paru = ArrayHelper::getValue($paruparu, 'note_inspeksi_paru');
        $modelFisik->note_palpasi_paru = ArrayHelper::getValue($paruparu, 'note_palpasi_paru');
        $modelFisik->note_perkusi_paru = ArrayHelper::getValue($paruparu, 'note_perkusi_paru');
        $modelFisik->note_auskultasi_paru = ArrayHelper::getValue($paruparu, 'note_auskultasi_paru');

        // ? get attribute jantung
        $jantung = ArrayHelper::getValue($dataFisik, 'jantung', []);
        $modelFisik->ictus_cordis = ArrayHelper::getValue($jantung, 'ictus_cordis', 0);
        $modelFisik->batas_jantung = ArrayHelper::getValue($jantung, 'batas_jantung', 0);
        $modelFisik->irama = ArrayHelper::getValue($jantung, 'irama', 0);
        $modelFisik->bunyi_jantung = ArrayHelper::getValue($jantung, 'bunyi_jantung');
        $modelFisik->note_bunyi_jantung = ArrayHelper::getValue($jantung, 'note_bunyi_jantung');

        // ? get attribute abdomenn
        $abdomenn = ArrayHelper::getValue($dataFisik, 'abdomenn', []);
        $modelFisik->inspeksi_abdomen = ArrayHelper::getValue($abdomenn, 'inspeksi_abdomen', 0);
        $modelFisik->auskultasi_abdomen = ArrayHelper::getValue($abdomenn, 'auskultasi_abdomen', 0);
        $modelFisik->perkusi_abdomen = ArrayHelper::getValue($abdomenn, 'perkusi_abdomen', 0);
        $modelFisik->palpasi_abdomen = ArrayHelper::getValue($abdomenn, 'palpasi_abdomen', 0);
        $modelFisik->rectal_toucher = ArrayHelper::getValue($abdomenn, 'rectal_toucher', 0);
        $modelFisik->genital = ArrayHelper::getValue($abdomenn, 'genital', 0);
        $modelFisik->note_inspeksi_abdomen = ArrayHelper::getValue($abdomenn, 'note_inspeksi_abdomen');
        $modelFisik->note_auskultasi_abdomen = ArrayHelper::getValue($abdomenn, 'note_auskultasi_abdomen');
        $modelFisik->note_perkusi_abdomen = ArrayHelper::getValue($abdomenn, 'note_perkusi_abdomen');
        $modelFisik->note_palpasi_abdomen = ArrayHelper::getValue($abdomenn, 'note_palpasi_abdomen');
        $modelFisik->note_rectal_toucher = ArrayHelper::getValue($abdomenn, 'note_rectal_toucher');
        $modelFisik->note_genital = ArrayHelper::getValue($abdomenn, 'note_genital');

        // ? get attribute MUSKULOSKELETAL
        $muskuloskeletal = ArrayHelper::getValue($dataFisik, 'muskuloskeletal', []);
        $modelFisik->deformitas_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'deformitas_kanan_atas');
        $modelFisik->deformitas_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'deformitas_kiri_atas');
        $modelFisik->deformitas_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'deformitas_kanan_bawah');
        $modelFisik->deformitas_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'deformitas_kiri_bawah');
        $modelFisik->note_deformitas_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'note_deformitas_kanan_atas');
        $modelFisik->note_deformitas_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'note_deformitas_kiri_atas');
        $modelFisik->note_deformitas_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_deformitas_kanan_bawah');
        $modelFisik->note_deformitas_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_deformitas_kiri_bawah');
        $modelFisik->fungsi_motorik_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'fungsi_motorik_kanan_atas');
        $modelFisik->fungsi_motorik_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'fungsi_motorik_kiri_atas');
        $modelFisik->fungsi_motorik_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'fungsi_motorik_kanan_bawah');
        $modelFisik->fungsi_motorik_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'fungsi_motorik_kiri_bawah');
        $modelFisik->note_fungsi_motorik_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_motorik_kanan_atas');
        $modelFisik->note_fungsi_motorik_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_motorik_kiri_atas');
        $modelFisik->note_fungsi_motorik_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_motorik_kanan_bawah');
        $modelFisik->note_fungsi_motorik_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_motorik_kiri_bawah');
        $modelFisik->fungsi_sensorik_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'fungsi_sensorik_kanan_atas');
        $modelFisik->fungsi_sensorik_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'fungsi_sensorik_kiri_atas');
        $modelFisik->fungsi_sensorik_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'fungsi_sensorik_kanan_bawah');
        $modelFisik->fungsi_sensorik_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'fungsi_sensorik_kiri_bawah');
        $modelFisik->note_fungsi_sensorik_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_sensorik_kanan_atas');
        $modelFisik->note_fungsi_sensorik_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_sensorik_kiri_atas');
        $modelFisik->note_fungsi_sensorik_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_sensorik_kanan_bawah');
        $modelFisik->note_fungsi_sensorik_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_fungsi_sensorik_kiri_bawah');
        $modelFisik->refleks_fisiologis_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'refleks_fisiologis_kanan_atas', 0);
        $modelFisik->refleks_fisiologis_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'refleks_fisiologis_kiri_atas', 0);
        $modelFisik->refleks_fisiologis_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'refleks_fisiologis_kanan_bawah', 0);
        $modelFisik->refleks_fisiologis_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'refleks_fisiologis_kiri_bawah', 0);
        $modelFisik->note_refleks_fisiologis_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_fisiologis_kanan_atas');
        $modelFisik->note_refleks_fisiologis_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_fisiologis_kiri_atas');
        $modelFisik->note_refleks_fisiologis_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_fisiologis_kanan_bawah');
        $modelFisik->note_refleks_fisiologis_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_fisiologis_kiri_bawah');
        $modelFisik->refleks_patologis_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'refleks_patologis_kanan_atas', 0);
        $modelFisik->refleks_patologis_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'refleks_patologis_kiri_atas', 0);
        $modelFisik->refleks_patologis_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'refleks_patologis_kanan_bawah', 0);
        $modelFisik->refleks_patologis_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'refleks_patologis_kiri_bawah', 0);
        $modelFisik->note_refleks_patologis_kanan_atas = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_patologis_kanan_atas');
        $modelFisik->note_refleks_patologis_kiri_atas = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_patologis_kiri_atas');
        $modelFisik->note_refleks_patologis_kanan_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_patologis_kanan_bawah');
        $modelFisik->note_refleks_patologis_kiri_bawah = ArrayHelper::getValue($muskuloskeletal, 'note_refleks_patologis_kiri_bawah');
        $modelFisik->pemeriksaan_lainnya = ArrayHelper::getValue($muskuloskeletal, 'pemeriksaan_lainnya');;

        return $modelFisik;
    }

    /**
     * Check config for RS Prima.
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
     * Get Data Pemeriksaan New
     */
    private function getDataNew($modelFisik, $data)
    {
        $dataFisik = isset($data['data']['pemeriksaan_fisik']) ? json_decode($data['data']['pemeriksaan_fisik'], true) : [];

        // kesadaran umum
        $keadaan_umum = ArrayHelper::getValue($dataFisik, 'keadaan_umum');
        $modelFisik->kesadaran_umum_batas_normal = ArrayHelper::getValue($keadaan_umum, 'kesadaran_umum_batas_normal');
        $modelFisik->kesadaran = ArrayHelper::getValue($keadaan_umum, 'kesadaran');
        $modelFisik->kontak = ArrayHelper::getValue($keadaan_umum, 'kontak');
        $modelFisik->gangguan_berjalan = ArrayHelper::getValue($keadaan_umum, 'gangguan_berjalan');
        $modelFisik->sakit_saat_berjalan = ArrayHelper::getValue($keadaan_umum, 'sakit_saat_berjalan');
        $modelFisik->postur = ArrayHelper::getValue($keadaan_umum, 'postur');
        $modelFisik->keterangan_keadaan_umum = ArrayHelper::getValue($keadaan_umum, 'keterangan_keadaan_umum');
        // kelenjar getah bening
        $kelenjar_getah_bening = ArrayHelper::getValue($dataFisik, 'kelenjar_getah_bening');
        $modelFisik->kelenjar_getah_bening_umum_batas_normal = ArrayHelper::getValue($kelenjar_getah_bening, 'kelenjar_getah_bening_umum_batas_normal');
        $modelFisik->leher = ArrayHelper::getValue($kelenjar_getah_bening, 'leher');
        $modelFisik->aksilla = ArrayHelper::getValue($kelenjar_getah_bening, 'aksilla');
        $modelFisik->inguinal = ArrayHelper::getValue($kelenjar_getah_bening, 'inguinal');
        $modelFisik->lainnya_kelenjar_getah_bening = ArrayHelper::getValue($kelenjar_getah_bening, 'lainnya_kelenjar_getah_bening');
        $modelFisik->note_lainnya_kelenjar_getah_bening = ArrayHelper::getValue($kelenjar_getah_bening, 'note_lainnya_kelenjar_getah_bening');
        $modelFisik->keterangan_kelenjar_getah_bening = ArrayHelper::getValue($kelenjar_getah_bening, 'keterangan_kelenjar_getah_bening');
        // kepala
        $kepala = ArrayHelper::getValue($dataFisik, 'kepala');
        $modelFisik->kepala_umum_batas_normal = ArrayHelper::getValue($kepala, 'kepala_umum_batas_normal');
        $modelFisik->bentuk_wajah = ArrayHelper::getValue($kepala, 'bentuk_wajah');
        $modelFisik->kulit_kepala = ArrayHelper::getValue($kepala, 'kulit_kepala');
        $modelFisik->rambut = ArrayHelper::getValue($kepala, 'rambut');
        $modelFisik->keterangan_kepala = ArrayHelper::getValue($kepala, 'keterangan_kepala');
        // saraf
        $saraf = ArrayHelper::getValue($dataFisik, 'saraf');
        $modelFisik->saraf_umum_batas_normal = ArrayHelper::getValue($saraf, 'saraf_umum_batas_normal');
        $modelFisik->motorik = ArrayHelper::getValue($saraf, 'motorik');
        $modelFisik->sensorik = ArrayHelper::getValue($saraf, 'sensorik');
        $modelFisik->ref_fisologis = ArrayHelper::getValue($saraf, 'ref_fisologis');
        $modelFisik->ref_patologis = ArrayHelper::getValue($saraf, 'ref_patologis');
        $modelFisik->n_facialis = ArrayHelper::getValue($saraf, 'n_facialis');
        // tes provokasi radiks cervical
        $radiks_cervical = ArrayHelper::getValue($dataFisik, 'radiks_cervical');
        $modelFisik->cervical_umum_batas_normal = ArrayHelper::getValue($radiks_cervical, 'cervical_umum_batas_normal');
        $modelFisik->tes_spurling = ArrayHelper::getValue($radiks_cervical, 'tes_spurling');
        $modelFisik->tes_distraksi = ArrayHelper::getValue($radiks_cervical, 'tes_distraksi');
        $modelFisik->keterangan_cervacal = ArrayHelper::getValue($radiks_cervical, 'keterangan_cervacal');
        // tes provokasi radiks lumbar
        $lumbar = ArrayHelper::getValue($dataFisik, 'lumbar');
        $modelFisik->lumbar_umum_batas_normal = ArrayHelper::getValue($lumbar, 'lumbar_umum_batas_normal');
        $modelFisik->tes_lasegue = ArrayHelper::getValue($lumbar, 'tes_lasegue');
        $modelFisik->tes_braggard = ArrayHelper::getValue($lumbar, 'tes_braggard');
        $modelFisik->keterangan_lumbar = ArrayHelper::getValue($lumbar, 'keterangan_lumbar');
        // mata
        $mata = ArrayHelper::getValue($dataFisik, 'mata');
        // dump($mata);exit;
        $modelFisik->mata_umum_batas_normal = ArrayHelper::getValue($mata, 'mata_umum_batas_normal');
        $modelFisik->persepsi_mata = ArrayHelper::getValue($mata, 'persepsi_mata');
        $modelFisik->persepsi_mata_note = ArrayHelper::getValue($mata, 'persepsi_mata_note');
        $modelFisik->stabisnus = ArrayHelper::getValue($mata, 'stabisnus');
        $modelFisik->kelopak_mata_kanan = ArrayHelper::getValue($mata, 'kelopak_mata_kanan');
        $modelFisik->kelopak_mata_kiri = ArrayHelper::getValue($mata, 'kelopak_mata_kiri');
        $modelFisik->bulu_mata_kanan = ArrayHelper::getValue($mata, 'bulu_mata_kanan');
        $modelFisik->bulu_mata_kiri = ArrayHelper::getValue($mata, 'bulu_mata_kiri');
        $modelFisik->konjungtiva_kanan = ArrayHelper::getValue($mata, 'konjungtiva_kanan');
        $modelFisik->konjungtiva_kiri = ArrayHelper::getValue($mata, 'konjungtiva_kiri');
        $modelFisik->note_konjungtiva_kanan = ArrayHelper::getValue($mata, 'note_konjungtiva_kanan');
        $modelFisik->note_konjungtiva_kiri = ArrayHelper::getValue($mata, 'note_konjungtiva_kiri');
        $modelFisik->sklera_kanan = ArrayHelper::getValue($mata, 'sklera_kanan');
        $modelFisik->sklera_kiri = ArrayHelper::getValue($mata, 'sklera_kiri');
        $modelFisik->kornea_kanan = ArrayHelper::getValue($mata, 'kornea_kanan');
        $modelFisik->kornea_kiri = ArrayHelper::getValue($mata, 'kornea_kiri');
        $modelFisik->lensa_kanan = ArrayHelper::getValue($mata, 'lensa_kanan');
        $modelFisik->lensa_kiri = ArrayHelper::getValue($mata, 'lensa_kiri');
        $modelFisik->pupil_kanan = ArrayHelper::getValue($mata, 'pupil_kanan');
        $modelFisik->pupil_kiri = ArrayHelper::getValue($mata, 'pupil_kiri');
        $modelFisik->pupil_isokor = ArrayHelper::getValue($mata, 'pupil_isokor');
        $modelFisik->pupil_diameter = ArrayHelper::getValue($mata, 'pupil_diameter');
        $modelFisik->direk_kanan = ArrayHelper::getValue($mata, 'direk_kanan');
        $modelFisik->direk_kiri = ArrayHelper::getValue($mata, 'direk_kiri');
        $modelFisik->indirek_kanan = ArrayHelper::getValue($mata, 'indirek_kanan');
        $modelFisik->indirek_kiri = ArrayHelper::getValue($mata, 'indirek_kiri');
        $modelFisik->lapang_pandang_kanan = ArrayHelper::getValue($mata, 'lapang_pandang_kanan');
        $modelFisik->diameter = ArrayHelper::getValue($mata, 'diameter');
        $modelFisik->tanpa_koreksi_OD = ArrayHelper::getValue($mata, 'tanpa_koreksi_OD');
        $modelFisik->tanpa_koreksi_OS = ArrayHelper::getValue($mata, 'tanpa_koreksi_OS');
        $modelFisik->tanpa_koreksi_ODS_jauh = ArrayHelper::getValue($mata, 'tanpa_koreksi_ODS_jauh');
        $modelFisik->tanpa_koreksi_ODS_dekat = ArrayHelper::getValue($mata, 'tanpa_koreksi_ODS_dekat');
        $modelFisik->dengan_koreksi_OD = ArrayHelper::getValue($mata, 'dengan_koreksi_OD');
        $modelFisik->dengan_koreksi_OS = ArrayHelper::getValue($mata, 'dengan_koreksi_OS');
        $modelFisik->dengan_koreksi_ODS_jauh = ArrayHelper::getValue($mata, 'dengan_koreksi_ODS_jauh');
        $modelFisik->dengan_koreksi_ODS_dekat = ArrayHelper::getValue($mata, 'dengan_koreksi_ODS_dekat');
        $modelFisik->keterangan_mata = ArrayHelper::getValue($mata, 'keterangan_mata');
        $modelFisik->lapang_pandang = ArrayHelper::getValue($mata, 'lapang_pandang');

        //telinga
        $telinga = ArrayHelper::getValue($dataFisik, 'telinga');
        $modelFisik->telinga_umum_batas_normal = ArrayHelper::getValue($telinga, 'telinga_umum_batas_normal');
        $modelFisik->liang_telinga_kanan = ArrayHelper::getValue($telinga, 'liang_telinga_kanan');
        $modelFisik->liang_telinga_kiri = ArrayHelper::getValue($telinga, 'liang_telinga_kiri');
        $modelFisik->serumen_kanan = ArrayHelper::getValue($telinga, 'serumen_kanan');
        $modelFisik->serumen_kiri = ArrayHelper::getValue($telinga, 'serumen_kiri');
        $modelFisik->membran_timpani_telinga_kanan = ArrayHelper::getValue($telinga, 'membran_timpani_telinga_kanan');
        $modelFisik->membran_timpani_telinga_kiri = ArrayHelper::getValue($telinga, 'membran_timpani_telinga_kiri');
        $modelFisik->pendengaran_telinga_kanan = ArrayHelper::getValue($telinga, 'pendengaran_telinga_kanan');
        $modelFisik->pendengaran_telinga_kiri = ArrayHelper::getValue($telinga, 'pendengaran_telinga_kiri');
        $modelFisik->keterangan_telinga = ArrayHelper::getValue($telinga, 'keterangan_telinga');
        // hidung
        $hidung = ArrayHelper::getValue($dataFisik, 'hidung');
        $modelFisik->hidung_umum_batas_normal = ArrayHelper::getValue($hidung, 'hidung_umum_batas_normal');
        $modelFisik->metus_nasi = ArrayHelper::getValue($hidung, 'metus_nasi');
        $modelFisik->septum_nasi = ArrayHelper::getValue($hidung, 'septum_nasi');
        $modelFisik->konka_nasal = ArrayHelper::getValue($hidung, 'konka_nasal');
        $modelFisik->nyeri_tekan_sinus = ArrayHelper::getValue($hidung, 'nyeri_tekan_sinus');
        $modelFisik->penciuman = ArrayHelper::getValue($hidung, 'penciuman');
        $modelFisik->keterangan_hidung = ArrayHelper::getValue($hidung, 'keterangan_hidung');
        // tenggorokan
        $tenggorokan = ArrayHelper::getValue($dataFisik, 'tenggorokan');
        $modelFisik->tenggorokan_umum_batas_normal = ArrayHelper::getValue($tenggorokan, 'tenggorokan_umum_batas_normal');
        $modelFisik->pharinx = ArrayHelper::getValue($tenggorokan, 'pharinx');
        $modelFisik->tonsil = ArrayHelper::getValue($tenggorokan, 'tonsil');
        $modelFisik->ukuran = ArrayHelper::getValue($tenggorokan, 'ukuran');
        $modelFisik->palatum = ArrayHelper::getValue($tenggorokan, 'palatum');
        $modelFisik->keterangan_tenggorokan = ArrayHelper::getValue($tenggorokan, 'keterangan_tenggorokan');
        // mulut
        $mulut = ArrayHelper::getValue($dataFisik, 'mulut');
        $modelFisik->mulut_umum_batas_normal = ArrayHelper::getValue($mulut, 'mulut_umum_batas_normal');
        $modelFisik->bibir = ArrayHelper::getValue($mulut, 'bibir');
        $modelFisik->lidah = ArrayHelper::getValue($mulut, 'lidah');
        $modelFisik->gusi = ArrayHelper::getValue($mulut, 'gusi');
        $modelFisik->mukosa = ArrayHelper::getValue($mulut, 'mukosa');
        // gigi
        $gigi = ArrayHelper::getValue($dataFisik, 'gigi');
        // leher
        $leher = ArrayHelper::getValue($dataFisik, 'leher');
        $modelFisik->leher_umum_batas_normal = ArrayHelper::getValue($leher, 'leher_umum_batas_normal');
        $modelFisik->otot_leher = ArrayHelper::getValue($leher, 'otot_leher');
        $modelFisik->kelenjar_thyroid = ArrayHelper::getValue($leher, 'kelenjar_thyroid');
        $modelFisik->jegular_vein_pressure = ArrayHelper::getValue($leher, 'jegular_vein_pressure');
        $modelFisik->trakea = ArrayHelper::getValue($leher, 'trakea');
        $modelFisik->keterangan_leher = ArrayHelper::getValue($leher, 'keterangan_leher');
        // torak
        $torax = ArrayHelper::getValue($dataFisik, 'torax');
        $modelFisik->torax_umum_batas_normal = ArrayHelper::getValue($torax, 'torax_umum_batas_normal');
        $modelFisik->bentuk_torax = ArrayHelper::getValue($torax, 'bentuk_torax');
        $modelFisik->note_bentuk_torax = ArrayHelper::getValue($torax, 'note_bentuk_torax');
        $modelFisik->mamae = ArrayHelper::getValue($torax, 'mamae');
        // paru
        $paru = ArrayHelper::getValue($dataFisik, 'paru');
        $modelFisik->paru_umum_batas_normal = ArrayHelper::getValue($paru, 'paru_umum_batas_normal');
        $modelFisik->pergerakan = ArrayHelper::getValue($paru, 'pergerakan');
        $modelFisik->perkusi_kanan = ArrayHelper::getValue($paru, 'perkusi_kanan');
        $modelFisik->perkusi_kiri = ArrayHelper::getValue($paru, 'perkusi_kiri');
        $modelFisik->bunyi_napas_kanan = ArrayHelper::getValue($paru, 'bunyi_napas_kanan');
        $modelFisik->bunyi_napas_kiri = ArrayHelper::getValue($paru, 'bunyi_napas_kiri');
        $modelFisik->membran_timpani_paru_kanan = ArrayHelper::getValue($paru, 'membran_timpani_paru_kanan');
        $modelFisik->membran_timpani_paru_kiri = ArrayHelper::getValue($paru, 'membran_timpani_paru_kiri');
        $modelFisik->pendengaran_paru_kanan = ArrayHelper::getValue($paru, 'pendengaran_paru_kanan');
        $modelFisik->pendengaran_paru_kiri = ArrayHelper::getValue($paru, 'pendengaran_paru_kiri');
        $modelFisik->keterangan_paru = ArrayHelper::getValue($paru, 'keterangan_paru');
        // jantung
        $jantung = ArrayHelper::getValue($dataFisik, 'jantung');
        $modelFisik->jantung_umum_batas_normal = ArrayHelper::getValue($jantung, 'jantung_umum_batas_normal');
        $modelFisik->bunyi_jantung = ArrayHelper::getValue($jantung, 'bunyi_jantung');
        $modelFisik->note_bunyi_jantung = ArrayHelper::getValue($jantung, 'note_bunyi_jantung');
        $modelFisik->ictus_cordis = ArrayHelper::getValue($jantung, 'ictus_cordis');
        $modelFisik->batas_kiri_jantung = ArrayHelper::getValue($jantung, 'batas_kiri_jantung');
        $modelFisik->note_batas_kiri_jantung = ArrayHelper::getValue($jantung, 'note_batas_kiri_jantung');
        $modelFisik->keterangan_jantung = ArrayHelper::getValue($jantung, 'keterangan_jantung');
        // abdomen
        $abdomen = ArrayHelper::getValue($dataFisik, 'abdomen');
        $modelFisik->abdomen_umum_batas_normal = ArrayHelper::getValue($abdomen, 'abdomen_umum_batas_normal');
        $modelFisik->inspeksi = ArrayHelper::getValue($abdomen, 'inspeksi');
        $modelFisik->auskultasi_abdomen = ArrayHelper::getValue($abdomen, 'auskultasi_abdomen');
        $modelFisik->perkusi = ArrayHelper::getValue($abdomen, 'perkusi');
        $modelFisik->palpasi = ArrayHelper::getValue($abdomen, 'palpasi');
        $modelFisik->hati = ArrayHelper::getValue($abdomen, 'hati');
        $modelFisik->limfa = ArrayHelper::getValue($abdomen, 'limfa');
        $modelFisik->nyeri_tekan = ArrayHelper::getValue($abdomen, 'nyeri_tekan');
        $modelFisik->nyeri_ketok_CVA_kanan = ArrayHelper::getValue($abdomen, 'nyeri_ketok_CVA_kanan');
        $modelFisik->nyeri_ketok_CVA_kiri = ArrayHelper::getValue($abdomen, 'nyeri_ketok_CVA_kiri');
        $modelFisik->keterangan_abdomen = ArrayHelper::getValue($abdomen, 'keterangan_abdomen');
        // extremitas dan vertebrata
        $extremitas = ArrayHelper::getValue($dataFisik, 'extremitas');
        $modelFisik->extremitas_umum_batas_normal = ArrayHelper::getValue($extremitas, 'extremitas_umum_batas_normal');
        $modelFisik->range_of_motion = ArrayHelper::getValue($extremitas, 'range_of_motion');
        $modelFisik->cervical = ArrayHelper::getValue($extremitas, 'cervical');
        $modelFisik->note_cervical = ArrayHelper::getValue($extremitas, 'note_cervical');
        $modelFisik->extremitas_atas = ArrayHelper::getValue($extremitas, 'extremitas_atas');
        $modelFisik->lumbar = ArrayHelper::getValue($extremitas, 'lumbar');
        $modelFisik->extremitas_bawah = ArrayHelper::getValue($extremitas, 'extremitas_bawah');
        $modelFisik->manual_muscle_test = ArrayHelper::getValue($extremitas, 'manual_muscle_test');
        $modelFisik->extremitas_atas_tes = ArrayHelper::getValue($extremitas, 'extremitas_atas_tes');
        $modelFisik->extremitas_bawah_tes = ArrayHelper::getValue($extremitas, 'extremitas_bawah_tes');
        $modelFisik->note_extremitas_atas = ArrayHelper::getValue($extremitas, 'note_extremitas_atas');
        $modelFisik->note_lumbar = ArrayHelper::getValue($extremitas, 'note_lumbar');
        $modelFisik->note_extremitas_bawah = ArrayHelper::getValue($extremitas, 'note_extremitas_bawah');
        $modelFisik->note_extremitas_atas_tes = ArrayHelper::getValue($extremitas, 'note_extremitas_atas_tes');
        $modelFisik->note_extremitas_bawah_tes = ArrayHelper::getValue($extremitas, 'note_extremitas_bawah_tes');
        $modelFisik->phallen = ArrayHelper::getValue($extremitas, 'phallen');
        $modelFisik->reverse_phallen = ArrayHelper::getValue($extremitas, 'reverse_phallen');
        $modelFisik->tinnel_sign = ArrayHelper::getValue($extremitas, 'tinnel_sign');
        $modelFisik->edema_tungkai = ArrayHelper::getValue($extremitas, 'edema_tungkai');
        $modelFisik->keterangan_extremitas = ArrayHelper::getValue($extremitas, 'keterangan_extremitas');
        // kuli
        $kulit = ArrayHelper::getValue($dataFisik, 'kulit');
        $modelFisik->kulit_umum_batas_normal = ArrayHelper::getValue($kulit, 'kulit_umum_batas_normal');
        $modelFisik->distribusi = ArrayHelper::getValue($kulit, 'distribusi');
        $modelFisik->lesi = ArrayHelper::getValue($kulit, 'lesi');
        $modelFisik->karakteristik = ArrayHelper::getValue($kulit, 'karakteristik');
        $modelFisik->efloresensi = ArrayHelper::getValue($kulit, 'efloresensi');
        // pemeriksaan fisik
        $pemeriksaanFisik = ArrayHelper::getValue($dataFisik, 'pemeriksaan_fisik');
        $modelFisik->berat_badan = ArrayHelper::getValue($pemeriksaanFisik, 'berat_badan');
        $modelFisik->tinggi_badan = ArrayHelper::getValue($pemeriksaanFisik, 'tinggi_badan');
        $modelFisik->imt = ArrayHelper::getValue($pemeriksaanFisik, 'imt');
        $modelFisik->detak_nadi = ArrayHelper::getValue($pemeriksaanFisik, 'detak_nadi');
        $modelFisik->pernafasan = ArrayHelper::getValue($pemeriksaanFisik, 'pernafasan');
        $modelFisik->suhu = ArrayHelper::getValue($pemeriksaanFisik, 'suhu');
        $modelFisik->td_sistolik = ArrayHelper::getValue($pemeriksaanFisik, 'td_sistolik');
        $modelFisik->td_diastolik = ArrayHelper::getValue($pemeriksaanFisik, 'td_diastolik');
        return $modelFisik;
        
    }

    /**
     * @param int $pendaftaran_id
     */
    private function cekExistData($pendaftaran_id)
    {
        $response = Yii::$app->docoRest->mcu->get('pemeriksaan/cek-pemeriksaan-fisik-default?pendaftaran_id=' . $pendaftaran_id);
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];

        return $response;
    }

    
    /**
     * Get Data bagian tubuh
     */
    private function getSupportDataFisik()
    {
        try {
            $response = Yii::$app->docoRest->mcu->get('allow/allow-get-data-fisik');
            $body = json_decode($response->getBody(), true);
            $data_bagiantubuh = empty($body['response']['data-bagiantubuh']) ? [] : $body['response']['data-bagiantubuh'];
            $detail_gigi = empty($body['response']['detail-bagiantubuh-gigi']) ? [] : $body['response']['detail-bagiantubuh-gigi'];
            $detail_abdomen = empty($body['response']['detail-bagiantubuh-abdomen']) ? [] : $body['response']['detail-bagiantubuh-abdomen'];

            return [
                'data_bagiantubuh' => $data_bagiantubuh,
                'detail_gigi' => $detail_gigi,
                'detail_abdomen' => $detail_abdomen,
            ];
        } catch (Exception $e) {
            return [
                'data_bagiantubuh' => [],
                'detail_gigi' => [],
                'detail_abdomen' => [],
                'message' => $e->getMessage(),
            ];
        } catch (RequestException $e) {
            return [
                'data_bagiantubuh' => [],
                'detail_gigi' => [],
                'detail_abdomen' => [],
                'message' => $e->getMessage(),
            ];
        }
    }
}