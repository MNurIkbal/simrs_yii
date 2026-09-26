<?php

/*
@author: Jend. Yafi | yafi.maulana@sirs.co.id
*/

namespace app\components\Traits\Pelayanan;

use Yii;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\components\Traits\Pelayanan\NursingNoteForm;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use yii\web\Response;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\Pelayanan\PelayananHelpers;


trait TerraMedikTrait
{
    public $type;

    public $url;

    public $serviceRest;

    public $urlRest;

    private function servicePath()
    {
        $url = null;
        $serviceRest = null;
        $urlRest = 'riwayat-pasien';
        switch ($this->type) {
            case 'RJ':
                $url = '/rajal/riwayat-pasien';
                $serviceRest = Yii::$app->docoRest->rajal;
                break;
            case 'RI':
                $url = '/ranap/riwayat-pasien';
                $serviceRest = Yii::$app->docoRest->ranap;
                break;
            case 'RD':
                $url = '/igd/riwayat-pasien';
                $serviceRest = Yii::$app->docoRest->igd;
                break;
            default:
                break;
        }
        $this->serviceRest = $serviceRest;
        $this->url = $url;
        $this->urlRest = $urlRest;
    }

    public function actionModalHistoryTerraMedik()
    {
        $this->servicePath();
        $request      = Yii::$app->request;
        $pasien_terra = $request->get('pasien_id', null);
        $url = $this->url;

        try {
            $title = Yii::t('fe', 'Arsip Riwayat Pasien');
            return $this->renderAjax('//pelayanan/terra-medik/__modal_view_terra_medik', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Get data
    public function actionGetDataTerraSoap()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            $payload['is_perawat']      = PelayananHelpers::isNurse();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-soap-terra-medik',
                    'payload' => [
                        'query' => $payload,
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Loop untuk membuat array dari response
            foreach ($response["data"] as $key => $value) {
                $no++;
                // Assign data
                $dokter = isset($value['nama_dokter']) ? $value['nama_dokter'] : null;
                $data[$key]['primary'] = DocoHelpers::encrypt($value['riwayatsoap_id']);
                $data[$key]['no'] = $no;
                $data[$key]['ruang'] = $value['tipe_pendaftaran'] . '<br>' . date('d/m/Y / H:i:s', strtotime($value['tgl_soap'])) . '<br>' . $dokter;
                $data[$key]['soap'] = $value['soap'];
                $data[$key]['resep'] = $value['resep'];
                $data[$key]['dokter_id'] = $value['dokter_id'];
                $data[$key]['tanggal'] = $value['tgl_soap'];
                $data[$key]['is_dokter'] = isset($value['dokter_id']) ? true : false;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response["total"];
            $result['recordsFiltered'] = $response["total"];

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Get data
    public function actionGetDataTerraResep()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-resep-terra-medik',
                    'payload' => [
                        'query' => $payload
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Group by the array
            $array_group_by = ArrayHelper::index($response["data"], null, [function($element){
                        return $element['kode_trans'];
                }, 'no_resep']);

            foreach ($array_group_by as $key => $val_index) {
                foreach ($val_index as $key => $val) {
                    $no++;
                    array_push($data, [
                        'id' => $val[0]['kode_trans'],
                        'no' => $no,
                        'no_rm' => $val[0]['no_rm'],
                        'no_resep' => $val[0]['no_resep'],
                        'nama_dokter' => $val[0]['nama_dokter'],
                        'dokter_id' => $val[0]['dokter_id'],
                        'tanggal' => $val[0]['tgl_transaksi'],
                        'detail' => ArrayHelper::getColumn($val, function ($element) {
                                return [
                                    'nama_barang' => $element['nama_barang'],
                                    'satuan' => $element['satuan'],
                                    'jumlah' => $element['jumlah'],
                                    'signa' => $element['signa']
                                ];
                             })
                    ]);
                }

            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Get data
    public function actionGetDataTerraLaboratorium()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-lab-terra-medik',
                    'payload' => [
                        'query' => $payload
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Group by the array
            $array_group_by = ArrayHelper::index($response["data"], null, [function($element){
                        return $element['order_id'];
                }, 'finish_note']);

            foreach ($array_group_by as $key => $val_index) {
                foreach ($val_index as $key => $val) {
                    $no++;
                    array_push($data, [
                        'id' => $val[0]['order_id'],
                        'no' => $no,
                        'no_rm' => $val[0]['no_rm'],
                        'order_pemeriksa' => implode(', ', ArrayHelper::getColumn($val, 'nama_pemeriksaan')),
                        'dokter_id' => $val[0]['dokter_id'],
                        'tanggal' => $val[0]['order_date'],
                        'detail' => ArrayHelper::getColumn($val, function ($element) {
                                return [
                                    'doctor_periksa' => $element['doctor_periksa'],
                                    'group_pemeriksaan' => $element['group_pemeriksaan'],
                                    'nama_pemeriksaan' => $element['nama_pemeriksaan'],
                                    'result' => $element['result'],
                                    'nilai_normal' => $element['nilai_normal'],
                                    'satuan_pemeriksaan' => $element['satuan_pemeriksaan']
                                ];
                             })
                    ]);
                }

            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Get data
    public function actionGetDataTerraRadiologi()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-radiologi-terra-medik',
                    'payload' => [
                        'query' => $payload
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Group by the array
            $array_group_by = ArrayHelper::index($response["data"], null, [function($element){
                        return $element['order_id'];
                }]);


            foreach ($array_group_by as $key => $val) {
                $no++;
                array_push($data, [
                    'id' => $val[0]['order_id'],
                    'no' => $no,
                    'no_rm' => $val[0]['no_rm'],
                    'tgl_order' => $val[0]['tgl_order'],
                    'tgl_selesai' => $val[0]['tgl_selesai'],
                    'doctor_perujuk' => $val[0]['doctor_perujuk'],
                    'dokter_id' => $val[0]['dokter_id'],
                    'tanggal' => $val[0]['tgl_order'],
                    'detail' => ArrayHelper::getColumn($val, function ($element) {
                            return [
                                'jenis_periksa' => $element['jenis_periksa'],
                                'doctor_rad' => $element['doctor_rad'],
                                'diagnosa' => $element['diagnosa'],
                                'diagnosa_klinik' => $element['diag_klinik'],
                            ];
                         })
                ]);
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Get data
    public function actionGetDataTerraBedah()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-bedah-terra-medik',
                    'payload' => [
                        'query' => $payload
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Group by the array
            $array_group_by = ArrayHelper::index($response["data"], null, [function($element){
                        return $element['order_id'];
                }]);


            foreach ($array_group_by as $key => $val) {
                $no++;
                array_push($data, [
                    'id' => $val[0]['order_id'],
                    'no' => $no,
                    'regis_id' => $val[0]['regis_id'],
                    'tgl_tindakan' => $val[0]['tgl_tindakan'],
                    'tanggal' => $val[0]['tgl_tindakan'],
                    'tgl_selesai' => $val[0]['tgl_selesai'],
                    'nama_tindakan' => $val[0]['tindakan_name'],
                    'jenis_pembedahan' => $val[0]['jenis_pembedahan'],
                    'dokter_id' => $val[0]['regis_id'], // keynya (dokter_id) dipake buat filter, tapi datanya gak kepake sebenernya
                    'tenaga_medis' => [
                            'dokter_operator' => $val[0]['dokter_operator'],
                            'dokter_anastesi' => $val[0]['dokter_anastesi'],
                            'asisten_operator' => $val[0]['asisten_operator'],
                            'asisten_anastesi' => $val[0]['asisten_anastesi']
                         ],
                    'detail' => ArrayHelper::getColumn($val, function ($element) {
                            return [
                                'log_operasi' => $element['log_operasi'],
                                'post_operasi' => $element['post_operasi'],
                                'jaringan_incisi' => $element['jaringan_incisi'],
                                'luas_operasi' => $element['luas_operasi'],
                                'pemeriksaan_patologi_anatomi' => $element['pemeriksaan_patologi_anatomi'] == 1 ? 'Ya' : 'Tidak',
                                'hasil' => $element['hasil'],
                            ];
                         })
                ]);
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataTerraMcu()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-mcu-terra-medik',
                    'payload' => [
                        'query' => $payload
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Group by the array
            $array_group_by = ArrayHelper::index($response["data"], null, [function($element){
                        return $element['mcu_paket_id'];
                }]);

            foreach ($array_group_by as $key => $val) {
                $no++;
                array_push($data, [
                    'id' => $val[0]['mcu_paket_id'],
                    'no' => $no,
                    'regis_id' => $val[0]['regis_id'],
                    'paket_pemeriksaan' => $val[0]['paket_name'],
                    'dokter_koordinator' => $val[0]['koordinator_tim_dokter'],
                    'dokter_id' => $val[0]['dokter_id'],
                    'tanggal' => $val[0]['mcu_paket_id'],
                    'detail' => ArrayHelper::getColumn($val, function ($element) {
                            return [
                                'nama_pemeriksaan' => $element['module_name'],
                                'hasil_pemeriksaan' => !empty($element['data']) ? $this->getHtmlHasilPemeriksaan(unserialize($element['data'])) : [],
                            ];
                         })
                ]);
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getHtmlHasilPemeriksaan($array){
        $html = '';
        foreach ($array as $key => $value) {
            $html .= !empty($value[1]) ? $value[1] : ''; // index 1 = valuenya
            if($key != end($array)){
                $html .= !empty($value[1]) ? ', ' : ''; // tambah koma
            }
        }

        return $html;
    }

    // Get data
    public function actionGetDataTerraFisioterapi()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-fisioterapi-terra-medik',
                    'payload' => [
                        'query' => $payload,
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Loop untuk membuat array dari response
            foreach ($response["data"] as $key => $value) {
                $no++;
                // Assign data
                $tglOrder = ArrayHelper::getValue($value, 'tgl_order');
                if ($tglOrder) $tglOrder = date('d/m/Y H:i:s', strtotime($tglOrder));
                $tglSelesai = ArrayHelper::getValue($value, 'tgl_selesai');
                if ($tglSelesai) $tglSelesai = date('d/m/Y H:i:s', strtotime($tglSelesai));
                $data[$key]['primary'] = DocoHelpers::encrypt($value['id']);
                $data[$key]['no'] = $no;
                $data[$key]['no_pendaftaran'] = ArrayHelper::getValue($value, 'no_pendaftaran');
                $data[$key]['tgl_order'] = $tglOrder;
                $data[$key]['tgl_selesai'] = $tglSelesai;
                $data[$key]['tgl_order_selesai'] = "$tglOrder - $tglSelesai";
                $data[$key]['nama_pemeriksa'] = ArrayHelper::getValue($value, 'nama_pemeriksa');
                $data[$key]['dokter_perujuk'] = ArrayHelper::getValue($value, 'referrer_internal_doctor');
                $data[$key]['diagnosa'] = ArrayHelper::getValue($value, 'diagnosa');
                $data[$key]['tanggal'] = $tglOrder; // For Filter Only
                $data[$key]['dokter_id'] = ArrayHelper::getValue($value, 'dokter_id'); // For Filter Only
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response["total"];
            $result['recordsFiltered'] = $response["total"];

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // Get data
    public function actionGetDataTerraBbl()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-bbl-terra-medik',
                    'payload' => [
                        'query' => $payload,
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Loop untuk membuat array dari response
            foreach ($response["data"] as $key => $value) {
                $no++;
                // Assign data
                $tglLahir = ArrayHelper::getValue($value, 'date_birth_bayi');
                if ($tglLahir) $tglLahir = date('d/m/Y', strtotime($tglLahir));
                $hariLahir = ArrayHelper::getValue($value, 'hari_lahir');
                $jamLahir = ArrayHelper::getValue($value, 'jam_lahir');
                $menitLahir = ArrayHelper::getValue($value, 'menit_lahir');
                if($jamLahir < 10) $jamLahir = "0$jamLahir";
                if($menitLahir < 10) $menitLahir = "0$menitLahir";
                $data[$key]['primary'] = DocoHelpers::encrypt($value['id']);
                $data[$key]['no'] = $no;
                $data[$key]['nama_bayi'] = ArrayHelper::getValue($value, 'nama_bayi');
                $data[$key]['hari_lahir'] = $hariLahir;
                $data[$key]['jam_lahir'] = $jamLahir;
                $data[$key]['menit_lahir'] = $menitLahir;
                $data[$key]['hari_tanggal_jam'] = "$hariLahir, $tglLahir <br/>$jamLahir:$menitLahir";
                $data[$key]['panjang'] = ArrayHelper::getValue($value, 'panjang');
                $data[$key]['berat'] = ArrayHelper::getValue($value, 'berat');
                $data[$key]['dokter'] = ArrayHelper::getValue($value, 'doctor');
                $data[$key]['tanggal'] = null; // For Filter Only
                $data[$key]['dokter_id'] = null; // For Filter Only
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response["total"];
            $result['recordsFiltered'] = $response["total"];

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

        /**
     * Get Data Terramedik History Resume Medis
     */
    public function actionGetDataTerraResumemedis()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $payload = DocoDatatableHelper::advancedFilterParam($params->get());
            $payload['is_dokter'] = PelayananHelpers::isDokter();
            $draw = $params->get('draw', 1);
            $data = [];
            $payload['advanced-filter']['pasien_id'] = is_numeric($params->get('pasien_terra', null)) ?
                $params->get('pasien_terra', null) :
                DocoHelpers::decrypt($params->get('pasien_terra', null));

            if (isset($payload['advanced-filter']['regdate'])) {
                $tgl_pendaftaran_range = explode('-', $payload['advanced-filter']['regdate']);
                $tgl_awal = str_replace('/', '-', $tgl_pendaftaran_range[0]);
                $tgl_akhir = str_replace('/', '-', $tgl_pendaftaran_range[1]);
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $payload['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
                $payload['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            }
            unset($payload['advanced-filter']['regdate']);

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                'method' => 'GET',
                'url' => $this->urlRest .'/history-resumemedis-terra-medik',
                'payload' => [
                    'query' => $payload
                ]
            ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $value['no'] = $no;
                $value['regdate'] = date('d/m/Y H:i:s', strtotime($value['regdate']));
                $value['dokter_id'] = $value;
                $value['tanggal'] = date('d/m/Y H:i:s', strtotime($value['regdate']));; //  Dibutuhkan key tanggal;
                $diagnosa_sekunder = $value['diagnosa_sekunder'] == '-' ? $value['diagnosa_sekunder'] : explode("\n", $value['diagnosa_sekunder']);
                $value['diagnosa'] = 'Diagnosa Awal : ' . $value['diagnosa_awal'] .
                    '<br>Diagnosa Utama : ' . $value['diagnosa_utama'] .
                    '<br>Diagnosa Sekunder : ';
                if (is_array($diagnosa_sekunder)) {
                    $numDiag = 1;
                    foreach ($diagnosa_sekunder as $k => $v) {
                        if ($v != '' && $v != "\r") {
                            $value['diagnosa'] .= '<br>' . $numDiag . '. ' . $v;
                            $numDiag++;
                        }
                    }
                } else {
                    $value['diagnosa'] .= $diagnosa_sekunder;
                }

                $value['detail'][0] = [
                    'alergi' => $value['alergi'],
                    'pemeriksaan_fisik' => $value['pemeriksaan_fisik'],
                    'riwayat_penyakit' => $value['riwayat_penyakit'],
                    'indikasi_pasien_dirawat' => $value['indikasi_pasien_dirawat'],
                    'lab' => $value['lab'],
                    'radiologi' => $value['radiologi'],
                    'lainlain' => $value['lainlain'],
                    'terapi_dan_tindakan_medis' => $value['terapi_dan_tindakan_medis'],
                    'konsultasi' => $value['konsultasi'],
                    'obat_selama_di_rs' => $value['obat_selama_di_rs'],
                    'obat_dibawa_pulang' => $value['obat_dibawa_pulang'],
                    'kondisi_keluar' => $value['kondisi_keluar'],
                    'tindak_lanjut' => $value['tindak_lanjut'],
                ];

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataRiwayatKunjungan()
    {
        // Try catch
        try {
            // Inisiasi
            $this->servicePath();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $payload                    = DocoDatatableHelper::advancedFilterParam($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $payload['pasien_id']       = is_numeric($params->get('pasien_terra', null)) ? $params->get('pasien_terra', null) : DocoHelpers::decrypt($params->get('pasien_terra', null));;
            $payload['is_dokter']       = PelayananHelpers::isDokter();
            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            if (isset($payload['advanced-filter']['dokter_id'])) {
                if ($payload['advanced-filter']['dokter_id'] == '%' || empty($payload['advanced-filter']['dokter_id'])) {
                    $payload['advanced-filter']['dokter_id'] = null;
                    $payload['is_dokter'] = false;
                }
            }

            // Get request
            $response = $this->helper->guzzleExec($this->serviceRest, [
                    'method' => 'GET',
                    'url' => $this->urlRest .'/history-kunjungan-terra-medik',
                    'payload' => [
                        'query' => $payload
                    ]
                ]);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            foreach ($response['data'] as $key => $value) {
                $no++;
                $value['no']             = $no;
                $response['data'][$key]  = $value;
            }

            $result['data'] = $response['data'];
            $result['recordsTotal'] = $response['total'];
            $result['recordsFiltered'] = $response['total'];

            return $result;
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionAllDokterList()
    {
        $this->servicePath();
        $dokterList = $this->helper->guzzleExec($this->serviceRest, [
            'url' => $this->urlRest.'/all-dokter-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);

        $payload = Yii::$app->request->get('payload', []);
        if ($payload['page'] == 1 && !empty($dokterList['data'])) {
            $data = $dokterList['data'];
            $allData = [
                'id' => '%',
                'text' => \Yii::t('fe', 'Semua Dokter')
            ];
            array_unshift($data, $allData);
            $dokterList['data'] = $data;
        }

        return $dokterList;
    }


}
