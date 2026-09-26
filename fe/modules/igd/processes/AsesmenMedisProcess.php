<?php
/**
 *
 * @author : iqbal.rukmana
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\igd\processes;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Doco\master\models\KamarForm;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\Services\AksesFormService;

use app\modules\igd\models\AsesmenMedisIgdForm;


class AsesmenMedisProcess extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Rajal :: Pemeriksaan pasien rawat jalan";
    protected $_module = '/rajal/pemeriksaan';

    public function getDataPasien($pendaftaran_id)
    {
       try {
            if (!empty($pendaftaran_id)) {
                $data_pasien_igd = Yii::$app->cache->get('data-pasien-igd-' .$pendaftaran_id);
                if (empty($data_pasien_igd)  || $data_pasien_igd === false ) {
                    $resultApi = $this->guzzleExec($this->_restIgd, [
                        'url' => 'pemeriksaan-igd/get-api',
                        'payload' => [
                            'query' => [
                                'id' => $this->_pendaftaran_id,
                                'pegawai_id' => $this->_pegawai_id,
                                'cppt' => true
                            ]
                        ]
                    ]);

                    // CPPT
                    $resultApi['data_pasien']['cppt'] = !empty($resultApi['cppt']) ? $resultApi['cppt'] : [];

                    $cacheDataPasien = isset($resultApi['data_pasien']) ? $resultApi['data_pasien'] : [];
                    Yii::$app->cache->set('data-pasien-igd-' . $this->_pendaftaran_id, $cacheDataPasien, 3600);
                    $data_pasien_igd = $cacheDataPasien;

                    $cacheDataPegawai = isset($resultApi['data_pegawai']) ? $resultApi['data_pegawai'] : [];
                    Yii::$app->cache->set('data-pegawai-' . $this->_pegawai_id, $cacheDataPegawai, 3600);
                    $data_pegawai = $cacheDataPegawai;
              } else {
                  $data_pasien = $data_pasien_igd;
              }
              $data_pasien['ruangan_id'] = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
              $data_pasien['ruangan_name'] = Yii::$app->docoVars->workspace('ruangan_name');
              return $data_pasien;
            }

            echo 'Pendaftaran ID Kosong';
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function processFlow($controller)
    {
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('id'));
        $user = Yii::$app->session->get('user_identity');
        $is_perawat = $user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS ? 1 : 0;

        // Get data pasien from this class
        $data_pasien = $this->getDataPasien($pendaftaran_id);

        $data = Yii::$app->docoRest->igd->get('asesmen-medis', [
              'query' => [
                  'pendaftaran_id' => DocoHelpers::decrypt(Yii::$app->request->get('id'))
              ]
        ]);

        $dataBmi = Yii::$app->docoRest->igd->get('allow/data-bmi', []);

        $jeniskelamin = !empty($data_pasien['jenis_kelamin']) ? $data_pasien['jenis_kelamin'] : '' ;
        $data = json_decode($data->getBody(), true)['response'];
        $dataBmi = json_decode($dataBmi->getBody(), true)['response'];

        $data_bmi     = empty($dataBmi['data-bmi']) ? [] : $dataBmi['data-bmi'];
        $data_bmi     = json_encode($data_bmi);
        //Tanggal Pasien Datang
        if (!empty($data['asesmenmedis']['tgl_pasien_datang'])) {
            $data['asesmenmedis']['tgl_pasien_datang'] = date('d/m/Y H:i:s', strtotime($data['asesmenmedis']['tgl_pasien_datang']));
        } else {
            $data['asesmenmedis']['tgl_pasien_datang'] = date('d/m/Y H:i:s', strtotime("now"));
            // $data['asesmenmedis']['resolusi_image'] = null;
        }
        //Tanggal Asesmen
        if (!isset($data['asesmenmedis']['tgl_asesmen'])) {
            $data['asesmenmedis']['tgl_asesmen'] = date('d/m/Y H:i:s', strtotime("now"));
        } else {
            $data['asesmenmedis']['tgl_asesmen'] = date('d/m/Y H:i:s', strtotime($data['asesmenmedis']['tgl_asesmen']));
        }
        //Riwayat Penyakit Dahulu
        if (!isset($data['asesmenmedis']['riwayat_penyakit_dahulu'])) {
            if (!empty($data['riwayat']['asesmenmedisrd_id']) && empty($data['riwayatasesmenmedis']['riwayat_penyakit_dahulu'])) {
                $data['asesmenmedis']['riwayat_penyakit_dahulu'] = $data['riwayat']['riwayat_penyakit_dahulu'];
            }
        }

        //Riwayat Terapi Sebelumnya
        if (!isset($data['asesmenmedis']['riwayat_terapi_sebelumnya']) || empty($data['asesmenmedis']['riwayat_terapi_sebelumnya'])) {
            if (!empty($data['obatPulang'][0]['reseptur_id'])) {
                $terapi = '';
                foreach ($data['obatPulang'] as $key => $value) {
                    $satuan_input = json_decode($data['obatPulang'][$key]['additional_data'], true);
                    $data['obatPulang'][$key]['additional_data'] = $satuan_input['satuan_input'];
                    if (empty($data['obatPulang'][$key]['r'])) {
                        $terapi .= '- ' . $data['obatPulang'][$key]['qty_reseptur'] . ' ' . $data['obatPulang'][$key]['additional_data'] . ' ' . $data['obatPulang'][$key]['obatalkes_namalain'] . ' ( ' . $data['obatPulang'][$key]['signa_nama'] . ' ) ';
                        $terapi .= nl2br("\n");
                    } else {
                        $terapi .= '-' . $data['obatPulang'][$key]['r'] . ' ' . $data['obatPulang'][$key]['rke'] . ' -- ' . $data['obatPulang'][$key]['qty_reseptur'] . ' ' . $data['obatPulang'][$key]['additional_data'] . ' ' . $data['obatPulang'][$key]['obatalkes_namalain'] . ' ( ' . $data['obatPulang'][$key]['signa_nama'] . ' ) ';
                        $terapi .= nl2br("\n");
                    }
                }
                $data['asesmenmedis']['riwayat_terapi_sebelumnya'] = $terapi;
            }
        }

        $data_bagiantubuh       = (isset($data['data-bagiantubuh']) && !empty($data['data-bagiantubuh'])) ? $data['data-bagiantubuh'] : [];
        $data_detailbagiantubuh = (isset($data['data-detailbagiantubuh']) && !empty($data['data-detailbagiantubuh'])) ? $data['data-detailbagiantubuh'] : [];

        $data_anatomiPasien = isset($data['data-anatomi']) ? $data['data-anatomi'] : [];

        $jsonAnatomi        = json_encode($data_anatomiPasien, JSON_FORCE_OBJECT);
        $counter = 0;
        if(! empty($data_anatomiPasien)) {
            foreach ($data_anatomiPasien as $key => $value) {
                if($counter < $value['counters']) {
                    $counter = (int) $value['counters'];
                }
            }

            $counter++;
        } else {
            $counter = count($data_anatomiPasien) + 1;
        }

        // jika ada perubahan dari form (belum disimpan), gunakan data cache
        $updated_asmed_cache = Yii::$app->cache->get($user['id_pegawai'].'-updated-data-asesmen-medis-igd-'.Yii::$app->request->get('id'));
        $is_draft = 0;
        if ($updated_asmed_cache) {
            $data['asesmenmedis'] = $updated_asmed_cache;
            $data['asesmenmedis']['diagnosis'] = json_encode($updated_asmed_cache['diagnosis_raw']);
            $jsonAnatomi = ArrayHelper::getValue($updated_asmed_cache, "periksatubuh");
            $counterAnatomi = json_decode($jsonAnatomi, true);
            if(is_array($counterAnatomi)) {
                $counter = count($counterAnatomi) + 1;
                foreach ($counterAnatomi as $key => $value) {
                    if($counter < $value['counters']) {
                        $counter = (int) $value['counters'];
                    }
                }
            }
            $is_draft = 1;
        }

        $model = new AsesmenMedisIgdForm;
        $model->load($data, '');

        // $model->resolusi_image = isset($data['asesmenmedis']['resolusi_image']) ? $data['asesmenmedis']['resolusi_image'] : null;
        // $explode = explode("x", $data['asesmenmedis']['resolusi_image']);
        // $width = isset($explode[0])? $explode[0]:400;
        // $height = isset($explode[1])? $explode[1]:400;
        $model->asesmenmedisrd_id = isset($data['asesmenmedis']['asesmenmedisrd_id']) ? $data['asesmenmedis']['asesmenmedisrd_id'] : null;
        $model->attributes = $data['asesmenmedis'];

        // Kebutuhan Secondary Survey
        $tidak_ada_kelainan = 'tidak_ada_kelainan';
        if (!isset($data['asesmenmedis']['survey_kepala'])) {
            $model->survey_kepala = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_mata'])) {
            $model->survey_mata = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_mulut'])) {
            $model->survey_mulut = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_telinga'])) {
            $model->survey_telinga = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_leher'])) {
            $model->survey_leher = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_extremitas'])) {
            $model->survey_extremitas = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_dada'])) {
            $model->survey_dada = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_abdomen'])) {
            $model->survey_abdomen = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_pelvis'])) {
            $model->survey_pelvis = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_medulla_spinalis'])) {
            $model->survey_medulla_spinalis = $tidak_ada_kelainan;
        }
        if (!isset($data['asesmenmedis']['survey_kolumna_vertebralis'])) {
            $model->survey_kolumna_vertebralis = $tidak_ada_kelainan;
        }

        // secondary survey radio button
        $radio_button_normal = ['1'=>'Normal', '0'=>'Tidak Normal'];
        $radio_button_pergerakan = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Asimetris')];
        $radio_button_ada = ['1'=>Yii::t('fe','Ada'), '0'=>Yii::t('fe','Tidak Ada')];
        $radio_button_reguler = ['1'=>Yii::t('fe','Reguler'), '0'=>Yii::t('fe','Inreguler')];
        $radio_button_tidak = ['1'=>Yii::t('fe','Tidak'), '0'=>Yii::t('fe','Ya')];

        $data['asesmenmedis']['pendaftaran_id'] = Yii::$app->request->get('id');
        $arrayConfig = $controller->getConfig('asesmen_medis_rd');

        $optDiagnosa = [];
        $valDiagnosa = $optDiagnosa = [];
        $diagnosa = !empty($data['asesmenmedis']['diagnosis']) ? json_decode($data['asesmenmedis']['diagnosis'], true) : [];
        foreach ($diagnosa as $value) {
            if (isset($value['id']) && isset($value['text'])) {
                $idDiag = $value['id'] . '_' . $value['text'];
                $textDiag = $value['text'];
                $valDiagnosa[] = $idDiag;
                $optDiagnosa[] = [$idDiag => $textDiag];
            } else if (isset($value['text'])) {
                $valDiagnosa[] = $value['text'];
                $optDiagnosa[] = [$value['text'] => $value['text']];
            }
        }
        // var_dump($optDiagnosa);die;
        $model->diagnosis = $valDiagnosa;
        
        // set default value
        $model->bb_ideal = is_null($model->bb_ideal) ? 0 : $model->bb_ideal;
        $model->nadi = is_null($model->nadi) ? 0 : $model->nadi;
        $model->pernapasan = is_null($model->pernapasan) ? 0 : $model->pernapasan;
        $model->skala_nyeri =  is_null($model->skala_nyeri) ? 11 : $model->skala_nyeri;
        $model->skala_nyeri_anak =  is_null($model->skala_nyeri_anak) ? 11 : $model->skala_nyeri_anak;
        $model->periksatubuh = $jsonAnatomi;

        // set cache data terbaru dari db, digunakan di AsesmenMedisTrait actionSetCacheAsmed()
        if ($updated_asmed_cache == false) {
            Yii::$app->cache->set($user['id_pegawai'].'-latest-data-asesmen-medis-igd-'.Yii::$app->request->get('id'), $model->attributes, DocoConstants::EXPIRED_CACHE);
        }

        $cekDataPasien = $controller->getStatusPeriksa();
        $status_update = $cekDataPasien['result'];
        $enable_edit = !empty($data['enable_pulang']) ? $data['enable_pulang'] : false;
        $cekAkses = (new AksesFormService)->execute($data_pasien['pasien_id'], DocoConstants::FORM_ASESMEN_MEDIS);

        if ($cekAkses == true || $enable_edit) {
            $status_update = false;
        }
        return $controller->renderAjax('@app/modules/igd/views/pemeriksaan-igd/asesmen-medis/__form', get_defined_vars());
    }

}
