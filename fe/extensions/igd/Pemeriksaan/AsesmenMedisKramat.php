<?php
/**
 *
 * @author : iqbal.rukmana
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\igd\Pemeriksaan;

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

use app\extensions\igd\models\AsesmenMedisIgdKramatForm;


class AsesmenMedisKramat extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Rawat Darurat :: Pemeriksaan pasien rawat darurat";
    protected $_module = '/igd/pemeriksaan-igd';

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

        if(Yii::$app->request->post()) {
            $model = new AsesmenMedisIgdKramatForm;
            $request = Yii::$app->request->post();

            $request['tgl_pasien_datang'] = (date_create_from_format('d/m/Y H:i:s', $request['tgl_pasien_datang'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $request['tgl_pasien_datang']), 'Y-m-d H:i:s') : $request['tgl_pasien_datang'];
            $request['tgl_asesmen'] = (date_create_from_format('d/m/Y H:i:s', $request['tgl_asesmen'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $request['tgl_asesmen']), 'Y-m-d H:i:s') : $request['tgl_asesmen'];
            $model->attributes = $request;
            $model->tekanandarah = empty($model->tekanandarah) ? trim(str_replace(',', '.', $model->tekanandarah)) : $model->tekanandarah;
            $model->nadi = empty($model->nadi) ? trim(str_replace(',', '.', $model->nadi)) : $model->nadi;
            $model->suhu = empty($model->suhu) ? trim(str_replace(',', '.', $model->suhu)) : $model->suhu;
            $model->saturasi_o2 = empty($model->saturasi_o2) ? trim(str_replace(',', '.', $model->saturasi_o2)) : $model->saturasi_o2;
            Yii::error($model->attributes);
            if(!$model->validate()){
                $response = $model->errors;
                $errors = DocoHelpers::parseError($response, 'AsesmenMedisIgdKramatForm');
                Yii::error('kesini');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

            $response =  Yii::$app->docoRest->igd->post('asesmen-medis/save-medis', [
                  'form_params' => [
                      'formdata' => array_merge($model->attributes, [
                          'pendaftaran_id' => DocoHelpers::decrypt($model->pendaftaran_id),
                          'anatomi'        => Yii::$app->request->post('periksatubuh')
                      ])
                  ],
                  'returnResponse' => true
              ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response, false, true);
        }
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
            $data['asesmenmedis']['tgl_asesmen'] = date('d-m-Y H:i:s', strtotime("now"));
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
        $counter            = count($data_anatomiPasien) + 1;
        $model = new AsesmenMedisIgdKramatForm;
        $model->load($data, '');

        $additional_data = isset($data['asesmenmedis']['additional_data']) ? json_decode($data['asesmenmedis']['additional_data'], true) : [];

        $diagnosa_primary = !empty($additional_data['diagnosa_primary']) ? json_decode($additional_data['diagnosa_primary'], true) : [];
        $diagnosa_secondary = !empty($additional_data['diagnosa_secondary']) ? json_decode($additional_data['diagnosa_secondary'], true) : [];

        $diagnosa_primary_id = $diagnosa_primary_text = null;
        if (isset($diagnosa_primary['id']) && $diagnosa_primary['text']) {
            $diagnosa_primary_id = $diagnosa_primary['id'] . '_' . $diagnosa_primary['text'];
            $diagnosa_primary_text = $diagnosa_primary['text'];
            $model->diagnosa_primary = ($diagnosa_primary['id'] == $diagnosa_primary_text) ? $diagnosa_primary_text : $diagnosa_primary_id;
        }

        $val_diagnosa_secondary = $opt_diagnosa_secondary = [];
        foreach ($diagnosa_secondary as $value) {
            if (isset($value['id']) && isset($value['text'])) {
                $diagnosa_secondary_id = $value['id'] . '_' . $value['text'];
                $diagnosa_secondary_text = $value['text'];
                $val_diagnosa_secondary[] = $diagnosa_secondary_id;
                $opt_diagnosa_secondary[] = ['id' => $diagnosa_secondary_id,
                                              'text' => $diagnosa_secondary_text];
            } else if (isset($value['text'])) {
                $val_diagnosa_secondary[] = $value['text'];
                $opt_diagnosa_secondary[] = ['id' => $value['text'],
                                             'text' => $value['text']];
            }
        }

        $opt_diagnosa_primary = [$diagnosa_primary_id => $diagnosa_primary_text];

        $model->asesmenmedisrd_id = isset($data['asesmenmedis']['asesmenmedisrd_id']) ? $data['asesmenmedis']['asesmenmedisrd_id'] : null;
        $umur = DocoHelpers::getUmur($data_pasien['tanggal_lahir'], true);
        $model->attributes = $data['asesmenmedis'];

        // Set Default Pilih Skala jika masih kosong
        if(!isset($data['asesmenmedis']['pilih_skala'])){
            $data['asesmenmedis']['pilih_skala'] = $umur > 16 ? 'dewasa' : 'anak';
            $data['asesmenmedis']['skala_nyeri'] = 1;
            $data['asesmenmedis']['skala_nyeri_anak'] = 1;
        }

        // Kebutuhan Secondary Survey
        if (isset($additional_data['objective'])) {
            $model->objective = $additional_data['objective'];
        }

        $data['asesmenmedis']['pendaftaran_id'] = Yii::$app->request->get('id');
        $arrayConfig = $controller->getConfig('asesmen_medis_rd');

        // Kramat Additional Config
        $arrayConfig['pilih_skala'] = array_reverse($arrayConfig['pilih_skala']); // Kebutuhan kramat dan igd, anak dahulu baru dewasa (defaultnya dewasa dulu)
        $arrayConfig['tindak_lanjut'] = array_slice($arrayConfig['tindak_lanjut'], 0, 3, true) +
                                        ['observasi' => 'Observasi'] +
                                        array_slice($arrayConfig['tindak_lanjut'], 3, NULL, true); // Tambah value baru untuk kebutuhan kramat, dibuat gini untuk menyesuaikan dengan config bawaan

        $cekDataPasien = $controller->getStatusPeriksa();
        $status_update = $cekDataPasien['result'];

        $cekAkses = (new AksesFormService)->execute($data_pasien['pasien_id'], DocoConstants::FORM_ASESMEN_MEDIS);

        if ($cekAkses == true) {
            $status_update = false;
        }

        return $controller->renderAjax('@app/extensions/igd/views/asesmen-medis/__form', compact(
                    'model', 'arrayConfig', 'data', 'pendaftaran_id', 'data_bagiantubuh', 'data_detailbagiantubuh',
                     'data_anatomiPasien', 'jsonAnatomi', 'counter', 'data_bmi', 'jeniskelamin', 'status_update',
                     'is_perawat', 'diagnosa_primary_text', 'opt_diagnosa_primary', 'opt_diagnosa_secondary',
                     'val_diagnosa_utama', 'val_diagnosa_secondary', 'data_pasien'));
    }

}
