<?php

/**
 * @Author: Aris Munandar
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use Mpdf\Mpdf;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\igd\models\TriaseForm;
use app\modules\igd\models\TriaseGcsForm;

trait FormulirTriaseTrait
{
    public function actionFormulirTriase($id)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $triase_id = Yii::$app->request->get('triase_id', null);
            $userIdentity = $this->_user_identity;
            $disabled = (!empty($this->_data_pasien['pasienpulang_id'])) ? true : false;

            $response = $this->guzzleExec(Yii::$app->docoRest->igd, [
                'url' => 'allow/bundle-data-triage'
            ]);
            $dataBed = isset($response['dataBed']) ? $response['dataBed'] : [];

            $triaseData = $this->guzzleExec($this->_restIgd, [
                'url' => 'formulir-triase/get-triase',
                'payload' => [
                    'query' => compact('pendaftaran_id', 'triase_id')
                ]
            ]);
            if (!empty($triaseData['data']['tgl_triase'])) {
                $triaseData['data']['tgl_triase'] = date('d/m/Y H:i:s', strtotime($triaseData['data']['tgl_triase']));
            } else {
                $triaseData['data']['tgl_triase'] = date('d/m/Y H:i:s', strtotime("now"));
            }

            if (isset($triaseData['data']['dokter_id']) || isset($triaseData['data']['perawat_id'])) {
                if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($triaseData['data']['dokter_id'])) {
                    $triaseData['data']['dokter_id'] = $userIdentity['id_pegawai'];
                    $triaseData['data']['dokter_nama'] = $userIdentity['nama_pegawai'];
                } else if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN && empty($triaseData['data']['perawat_id'])) {
                    $triaseData['data']['perawat_id'] = $userIdentity['id_pegawai'];
                    $triaseData['data']['perawat_nama'] = $userIdentity['nama_pegawai'];
                }
            }

            //alergi pendaftaran sebelumnya
            if (!isset($triaseData['data']['alergi'])) {
                if (!empty($triaseData['alergi']['triase_id'])) {
                    $triaseData['data']['alergi']         = $triaseData['alergi']['alergi_triase'];
                    $triaseData['data']['alergi_obat']    = $triaseData['alergi']['alergi_obat_triase'];
                    $triaseData['data']['alergi_lainnya'] = $triaseData['alergi']['alergi_lainnya_triase'];
                }
                if (!empty($triaseData['alergi']['asesmenperawatrd_id'])) {
                    $triaseData['data']['alergi']         = $triaseData['alergi']['alergi_askep'];
                    $triaseData['data']['alergi_obat']    = $triaseData['alergi']['alergi_obat_askep'];
                    $triaseData['data']['alergi_lainnya'] = $triaseData['alergi']['alergi_lainnya_askep'];
                }
                if (!empty($triaseData['alergi']['asesmenmedisrd_id'])) {
                    if (isset($triaseData['alergi']['alergi_lainnya_asmed']) && !empty($triaseData['alergi']['alergi_lainnya_asmed'])) {
                        $triaseData['data']['alergi'] = '1';
                    }
                    $triaseData['data']['alergi_lainnya'] = $triaseData['alergi']['alergi_lainnya_asmed'];
                }
            }
            if (isset($triaseData['data']['tekanan_darah_sistolik']) && isset($triaseData['data']['tekanan_darah_diastolik'])) {
                $triaseData['data']['tekanan_darah'] =
                    $triaseData['data']['tekanan_darah_sistolik'] . '/' . $triaseData['data']['tekanan_darah_diastolik'];
            }

            $configVal = $this->getConfig('formulir_triase');
            $configRules = $this->getConfig('konfig_formulir_triase');
            return $this->renderAjax('formulir-triase/index', [
                'model'           => new TriaseForm,
                'pendaftaranId'   => $pendaftaran_id,
                'configVal'       => $configVal,
                'configRules'     => $configRules,
                'triaseData'      => empty($triaseData['data']) ? [] : $triaseData['data'],
                'tgl_pendaftaran' => $triaseData['data']['tgl_pendaftaran'],
                'dataBed'         => $dataBed,
                'disabled' => $disabled,
            ]);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionCetakFormulirTriase()
    {
        try {
            $payload        = Yii::$app->request->post();
            $triase_id = $pendaftaran_id = $triaseData = $dataCetak = null;
            $is_triase = false;
            $numberField    = ['tekanan_darah', 'nafas', 'suhu', 'nadi'];
            $pendaftaran_id = Yii::$app->request->get('id', null);
            $is_cetak = Yii::$app->request->get('is_cetak', null);

            if(!empty($payload)) {
                $payload['tgl_triase'] = (date_create_from_format('d/m/Y H:i:s', $payload['tgl_triase'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $payload['tgl_triase']), 'Y-m-d H:i:s') : $payload['tgl_triase'];
                $pendaftaran_id = $payload['nomor'];
                $triase_id = !empty($payload['triase_id']) ? $payload['triase_id'] : null;
            }

            $userIdentity = $this->_user_identity;
            $disabled = (!empty($this->_data_pasien['pasienpulang_id'])) ? true : false;

            $response = $this->guzzleExec(Yii::$app->docoRest->igd, [
                'url' => 'allow/bundle-data-triage'
            ]);
            $dataBed = isset($response['dataBed']) ? $response['dataBed'] : [];

            if(!empty($pendaftaran_id)) {
                $pendaftaran_id = !empty($is_cetak) ? DocoHelpers::decrypt($pendaftaran_id) : $pendaftaran_id;
                $triaseData = $this->guzzleExec($this->_restIgd, [
                    'url' => 'formulir-triase/get-triase',
                    'payload' => [
                        'query' => compact('pendaftaran_id', 'triase_id')
                    ]
                ]);

                if (!empty($triaseData['data']['tgl_triase'])) {
                    $triaseData['data']['tgl_triase'] = date('Y-m-d H:i:s', strtotime($triaseData['data']['tgl_triase']));
                    $is_triase = true;
                } else {
                    $triaseData['data']['tgl_triase'] = date('Y-m-d H:i:s', strtotime("now"));
                }

                if (isset($triaseData['data']['dokter_id']) || isset($triaseData['data']['perawat_id'])) {
                    if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && empty($triaseData['data']['dokter_id'])) {
                        $triaseData['data']['dokter_id'] = $userIdentity['id_pegawai'];
                        $triaseData['data']['dokter_nama'] = $userIdentity['nama_pegawai'];
                    } else if ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN && empty($triaseData['data']['perawat_id'])) {
                        $triaseData['data']['perawat_id'] = $userIdentity['id_pegawai'];
                        $triaseData['data']['perawat_nama'] = $userIdentity['nama_pegawai'];
                    }
                }
    
                //alergi pendaftaran sebelumnya
                if (!isset($triaseData['data']['alergi'])) {
                    if (!empty($triaseData['alergi']['triase_id'])) {
                        $triaseData['data']['alergi']         = $triaseData['alergi']['alergi_triase'];
                        $triaseData['data']['alergi_obat']    = $triaseData['alergi']['alergi_obat_triase'];
                        $triaseData['data']['alergi_lainnya'] = $triaseData['alergi']['alergi_lainnya_triase'];
                    }
                    if (!empty($triaseData['alergi']['asesmenperawatrd_id'])) {
                        $triaseData['data']['alergi']         = $triaseData['alergi']['alergi_askep'];
                        $triaseData['data']['alergi_obat']    = $triaseData['alergi']['alergi_obat_askep'];
                        $triaseData['data']['alergi_lainnya'] = $triaseData['alergi']['alergi_lainnya_askep'];
                    }
                    if (!empty($triaseData['alergi']['asesmenmedisrd_id'])) {
                        if (isset($triaseData['alergi']['alergi_lainnya_asmed']) && !empty($triaseData['alergi']['alergi_lainnya_asmed'])) {
                            $triaseData['data']['alergi'] = '1';
                        }
                        $triaseData['data']['alergi_lainnya'] = $triaseData['alergi']['alergi_lainnya_asmed'];
                    }
                }
                if (isset($triaseData['data']['tekanan_darah_sistolik']) && isset($triaseData['data']['tekanan_darah_diastolik'])) {
                    $triaseData['data']['tekanan_darah'] =
                        $triaseData['data']['tekanan_darah_sistolik'] . '/' . $triaseData['data']['tekanan_darah_diastolik'];
                }
            }

            $configVal = $this->getConfig('formulir_triase');
            $configRules = $this->getConfig('konfig_formulir_triase');

            $triaseForm = new TriaseForm;
            if(!empty($payload)) {
                $triaseForm->attributes = $payload;
            } else if(!empty($triaseData)) {
                $triaseForm->attributes = $triaseData['data'];
            }

            if(!empty($payload)) {
                $dataCetak = $payload;
            } else if(!empty($triaseData['data'])) {
                $dataCetak = $triaseData['data'];
            }

            foreach ($numberField as $eachField) {
                if (isset($dataCetak[$eachField])) {
                    $dataCetak[$eachField] = trim(str_replace('.', '', $dataCetak[$eachField]));
                }
            }

            $raw = $this->renderPartial('formulir-triase/cetak', [
                'model'           => $triaseForm,
                'pendaftaranId'   => $pendaftaran_id,
                'configVal'       => $configVal,
                'configRules'     => $configRules,
                'tgl_pendaftaran' => !empty($triaseData['data']['tgl_pendaftaran']) ? $triaseData['data']['tgl_pendaftaran'] : null,
                'dataBed'         => $dataBed,
                'disabled'        => $disabled,
                'dataForm'        => $dataCetak,
            ]);

            if($is_triase === false && $is_cetak == 1) {
                $raw = "<p align='center'><strong>Data Triase Tidak Ditemukan!</strong></p>";
            }
            $mpdf = new Mpdf([
                'format' => 'A4',
                'tempDir' => Yii::getAlias("@download"),
                'autoPageBreak' => true,
                'default_font' => 'helvetica',
                'mode' => 'utf-8',
            ]);
            $mpdf->AddPageByArray([
                'margin-left' => 5,
                'margin-right' => 5,
                'margin-top' => 5,
                'margin-bottom' => 5,
            ]);

            $raw = str_replace('checked','checked="checked"',$raw);

            $mpdf->WriteHTML($raw);
            $path = Yii::getAlias("@download") . "/cetak-formulir-triase.pdf";
            $mpdf->Output($path, 'I');
            die;

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }
    
    public function actionHitungGcs($pendaftaran_id)
    {
        $model = new TriaseGcsForm;
        $response = $this->_restIgd->get('formulir-triase/get-data-gcs', []);
        $databundle = json_decode($response->getBody(), true);
        $databundle = $databundle['response'];

        $data_gcs = (isset($databundle['data-gcs']) && !empty($databundle['data-gcs'])) ? $databundle['data-gcs'] : [];
        $data_listgcs = (isset($databundle['data-listgcs']) && !empty($databundle['data-listgcs'])) ? $databundle['data-listgcs'] : [];
        $data_gcsEye = (isset($data_listgcs['eye']) && !empty($data_listgcs['eye'])) ? $data_listgcs['eye'] : [];
        $data_gcsVerbal = (isset($data_listgcs['verbal']) && !empty($data_listgcs['verbal'])) ? $data_listgcs['verbal'] : [];
        $data_gcsMotorik = (isset($data_listgcs['motorik']) && !empty($data_listgcs['motorik'])) ? $data_listgcs['motorik'] : [];
        $gcsEyeOptions = [];
        $gcsVerbalOptions = [];
        $gcsMotorikOptions = [];
        if ($data_gcsEye) {
            foreach ($data_gcsEye as $keyEye => $valueEye) {
                $gcsEyeOptions[$valueEye['metodegcs_id']]['data-nilai'] = $valueEye['metodegcs_nilai'];
                $data_gcsEye[$keyEye]['nama_and_nilai'] = $valueEye['metodegcs_nama'] . ' - ' . $valueEye['metodegcs_nilai'];
            }
        }
        if ($data_gcsVerbal) {
            foreach ($data_gcsVerbal as $keyVerbal => $valueVerbal) {
                $gcsVerbalOptions[$valueVerbal['metodegcs_id']]['data-nilai'] = $valueVerbal['metodegcs_nilai'];
                $data_gcsVerbal[$keyVerbal]['nama_and_nilai'] = $valueVerbal['metodegcs_nama'] . ' - ' . $valueVerbal['metodegcs_nilai'];
            }
        }
        if ($data_gcsMotorik) {
            foreach ($data_gcsMotorik as $keyMotorik => $valueMotorik) {
                $gcsMotorikOptions[$valueMotorik['metodegcs_id']]['data-nilai'] = $valueMotorik['metodegcs_nilai'];
                $data_gcsMotorik[$keyMotorik]['nama_and_nilai'] = $valueMotorik['metodegcs_nama'] . ' - ' . $valueMotorik['metodegcs_nilai'];
            }
        }

        return $this->renderAjax('formulir-triase/_hitunggcs', get_defined_vars());
    }

    /**
     * List dropdown of doctor
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionDokterList()
    {
        return $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'formulir-triase/dokter-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    /**
     * List dropdown of nurse
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionPerawatList()
    {
        return $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'formulir-triase/perawat-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    public function actionSaveTriase()
    {
        $payload        = Yii::$app->request->post();
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $payload['tgl_triase'] = (date_create_from_format('d/m/Y H:i:s', $payload['tgl_triase'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $payload['tgl_triase']), 'Y-m-d H:i:s') : $payload['tgl_triase'];
        $numberField    = ['tekanan_darah', 'nafas', 'suhu', 'nadi'];
        $payload['pendaftaran_id'] = $pendaftaran_id;
        foreach ($numberField as $eachField) {
            if (isset($payload[$eachField])) {
                $payload[$eachField] = trim(str_replace('.', '', $payload[$eachField]));
            }
        }
        Yii::error([
            'payload' => $payload
        ]);
        // $modelValidation = new TriaseForm;
        // $modelValidation->attributes = $payload;
        // if(!$modelValidation->validate()){
        //     return $this->helper->macroResponseJson(422, 'Silakan cek kembali inputan.', $this->helper->mapErrorForm($modelValidation->errors, 'AsesmenKeperawatan'));
        // } else {
        return $this->guzzleExec($this->_restIgd, [
            'url' => 'formulir-triase/save-triase',
            'returnResponse' => true,
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => $payload
                ]
            ]
        ]);
        // }
    }

    public function actionModalListTriase()
    {
        $title = Yii::t('fe', 'List Triase');
        $getData = $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'formulir-triase/bundle-list-triase'
        ]);

        return $this->renderAjax('formulir-triase/_modal_list_triase', get_defined_vars());
    }

    public function actionGetListTriase()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $getData = $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'formulir-triase/bundle-list-triase',
            'payload' => [
                'query' => [
                    'start' => $request->get('start', 0),
                    'length' => $request->get('length', 10)
                ]
            ]
        ]);

        foreach ($getData['data'] as $key => $value) {
            $getData['data'][$key]['pegawai_nama'] = !empty($value['dokter_nama']) ? $value['dokter_nama'] : $value['perawat_nama'];
        }

        return [
            'data' => $getData['data'],
            'draw' => $draw,
            'recordsTotal' => $getData['totalCount'],
            'recordsFiltered' => $getData['totalCount']
        ];
    }
}
