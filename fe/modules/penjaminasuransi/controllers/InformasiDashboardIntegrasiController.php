<?php

/**
 * @author: [Maulana Muhammad Rizky]
 * A product of PT. Sirs
 * Powered by Sirs
 */

namespace Doco\penjaminasuransi\controllers;

use app\components\DocoConstants;
use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use GuzzleHttp\Exception\RequestException;

class InformasiDashboardIntegrasiController extends DocoController
{
    protected $_restPenjamin;
    protected $_restRajal;

    public function init()
    {
        parent::init();
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
        $this->_restRajal = Yii::$app->docoRest->rajal;
    }

    public function actionIndex()
    {
        $title = 'Informasi Dashboard Integrasi';
        $initData = $this->guzzleExec($this->_restPenjamin, [
            'url' => 'inf-dashboard-integrasi/init-data',
            'method' => 'GET',
            'returnResponse' => false
        ]);
        $statusPeriksa = ArrayHelper::getValue($initData, 'status_periksa', []);
        return $this->render('index', compact('title', 'statusPeriksa'));
    }

    /**
     * Action for get data integrasi
     * 
     * @author Maulana Muhammad Rizky.
     */
    public function actionGetDataIntegrasi()
    {
        $request = Yii::$app->request;

        try {
            $filter =  DocoDatatableHelper::advancedFilterParam();
            $response = $this->_restPenjamin->get('inf-dashboard-integrasi/get-data?' . http_build_query($filter), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $row = [];
            foreach ($body['response']['data'] as $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['primary'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['asuransiIdEncrypt'] = DocoHelpers::encrypt($value['asuransi_id']);
                $value['penjaminIdEncrypt'] = DocoHelpers::encrypt($value['penjamin_id']);
                $value['noKlaimEncrypt'] = DocoHelpers::encrypt($value['no_klaim']);
                $tanggalKeluar = isset($value['tgl_pulang']) ? date('d-m-Y h:i:s', strtotime($value['tgl_pulang'])) : '-';
                $value['tgl_daftar_keluar'] = '
                <div>
                    <p>Tanggal Pendaftaran: ' . date('d-m-Y h:i:s', strtotime($value['tgl_pendaftaran'])) . '</p>
                    <p>Tanggal Pulang: ' . $tanggalKeluar . '</p>
                </div>';
                $value['nama_pasien_lengkap'] = $value['nama_pasien'];
                $value['status_klaim'] = ArrayHelper::getValue($value, 'status_klaim');
                $row[] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount'],
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionProses()
    {
        $request = Yii::$app->request;
        try {
            $title = 'Lihat Pasien Asuransi';
            $pendaftaranId = $request->get('pendaftaran_id');
            $pendaftaranId = !is_numeric($pendaftaranId) ? DocoHelpers::decrypt($pendaftaranId) : $pendaftaranId;
            $asuransiId = $request->get('asuransi_id');
            $asuransiId = !is_numeric($asuransiId) ? DocoHelpers::decrypt($asuransiId) : $asuransiId;
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/detail-proses',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $pendaftaranId,
                        'asuransi_id' => $asuransiId,
                    ]
                ]
            ]);

            $diagnosaKode = ArrayHelper::getValue($response, 'diagnosa.kode');
            $diagnosaText = ArrayHelper::getValue($response, 'diagnosa.text');
            $noKlaim = ArrayHelper::getValue($response, 'identitas_pasien.no_klaim');
            $sisaLimit = ArrayHelper::getValue($response, 'sisa_limit.response.data');
            $summary = ArrayHelper::getValue($response, 'summary', []);
            $dataPeserta = [
                'penjamin_id' => ArrayHelper::getValue($response, 'identitas_pasien.penjamin_id'),
                'no_kartu' => ArrayHelper::getValue($response, 'identitas_pasien.no_kartu'),
                'no_klaim' => $noKlaim,
                'no_polis' => ArrayHelper::getValue($response, 'identitas_pasien.no_polis'),
                'additionalData' => ArrayHelper::getValue($response, 'additionalData.dataPeserta')
            ];
            $isCob = ArrayHelper::getValue($response, 'identitas_pasien.is_cob');
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
        
        return $this->render('proses', get_defined_vars());
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $filters =  DocoDatatableHelper::advancedFilterParam();
        try {
            $path = Yii::getAlias("@download") . "/informasi-dashboard-integrasi.xlsx";
            $this->_restPenjamin->get('inf-dashboard-integrasi/export-excel?' . http_build_query($filters), [
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Action for lihat detail
     * 
     * @author Maulana Muhammad Rizky.
     */
    public function actionLihatDetail()
    {
        return 'success';
    }

    public function actionCetakPendaftaran()
    {
        try {
            $request = Yii::$app->request;
            $path = Yii::getAlias("@download") . "/struk-pendaftaran.pdf";
            $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-integrasi-asuransi/cetak-struk-pendaftaran',
                'method' => 'get',
                'payload' => [
                    'save_to' => $path,
                    'query' => [
                        'no_klaim' => $request->get('no_klaim'),
                        'penjamin_id' => $request->get('penjamin_id')
                    ]
                ]
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCekPasien()
    {
        $title = 'Cek Pasien Asuransi';
        try {

            $penjamin = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-integrasi-asuransi/get-penjamin-terintegrasi',
                'method' => 'get',
            ]);
            $penjamin = ArrayHelper::getValue($penjamin, 'data', []);
            $penjamin = ArrayHelper::map($penjamin, 'penjamin_id', 'penjamin_nama');

            return $this->renderAjax('cek-pasien', compact('title', 'penjamin'));
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCekEligiblePeserta()
    {
        $request = Yii::$app->request;
        $noKartu = $request->post('no_kartu');
        $penjamin = $request->post('penjamin_id');

        try {
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-integrasi-asuransi/cek-eligible-peserta',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'no_kartu' => $noKartu,
                        'penjamin_id' => $penjamin
                    ]
                ]
            ]);

            $responseData = ArrayHelper::getValue($response, 'response', []);
            if ($responseData['status'] !== 0) {
                $errorData = [
                    'status' => 422,
                    'message' => ArrayHelper::getValue($responseData, 'message', [])
                ];
                return DocoHelpers::response($errorData, 422);
            }

            $tglLahir = ArrayHelper::getValue($responseData, 'data.dataPeserta.tanggallahir');
            $umur = DocoHelpers::getUmur($tglLahir);
            if (! isset($responseData['data']['dataPeserta']['umur'])) {
                $responseData['data']['dataPeserta']['umur'] = $umur;
            }
            
            return DocoHelpers::response($responseData);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Action for cetak pengesahan
     * 
     * @author Maulana Muhammad Rizky.
     */
    public function actionCetakPengesahan()
    {
        try {
            $request = Yii::$app->request;
            $path = Yii::getAlias("@download") . "/struk-pengesahan.pdf";
            $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-integrasi-asuransi/cetak-struk-pengesahan',
                'method' => 'get',
                'payload' => [
                    'save_to' => $path,
                    'query' => [
                        'no_klaim' => $request->get('no_klaim'),
                        'penjamin_id' => $request->get('penjamin_id')
                    ]
                ]
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Action for cetak jaminan
     *  
     * @author Maulana Muhammad Rizky
     */
    public function actionCetakJaminan()
    {
        return 'Cetak Jaminan';
    }

    public function actionGetGroupingData()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/get-grouping-data',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $request->get('pendaftaran_id'),
                        'penjamin_id' => $request->get('penjamin_id'),
                        'no_klaim' => $request->get('no_klaim'),
                    ]
                ]
            ]);
            $no = $request->get('start', 1);
            $newRow = [];
            foreach ($response['data'] as $value) {
                $no++;
                $value['rowNum'] = $no;
                $kelompoktindakanId = isset($value['kelompoktindakan_id']) ? $value['kelompoktindakan_id'] : $value['jenisobatalkes_id'];
                $value['kelompoktindakanobat_id'] = $kelompoktindakanId;
                $isobat = isset($value['jenisobatalkes_id']) ? 1 : 0;
                $button = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success',
                    'data-source' => "/penjamin-asuransi/informasi-dashboard-integrasi/expand-grouping?pendaftaran_id=" . $value['pendaftaran_id']."&kelompoktindakan_id=" . $kelompoktindakanId."&is_obat=" . $isobat.'&penjamin_id='.$request->get('penjamin_id').'&no_klaim='.$request->get('no_klaim'),
                    'onclick' => 'docoHelper.detail(this)'
                ]);

                $value['status'] = "";
                $value['item_button'] = $button;
                $value['price_mapping'] = number_format($value['total_tarif'], 0);
                $value['tarif_mapping'] = number_format($value['total_tarif'], 0);
                $newRow[] = $value;
            }

            $return = [
                'data' => $newRow,
                'draw' => $request->get('draw'),
                'recordsTotal' => $response['_meta']['totalCount'],
                'recordsFiltered' => $response['_meta']['totalCount'],
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExpandGrouping()
    {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $kelompoktindakanId = $request->get('kelompoktindakan_id');
        $penjaminId = $request->get('penjamin_id');
        $noKlaim = $request->get('no_klaim');
        $isobat = $request->get('is_obat');
        return $this->renderAjax('detail-tindakan', compact('pendaftaranId', 'kelompoktindakanId', 'isobat', 'penjaminId', 'noKlaim'));
    }

    public function actionGetGroupingDetail()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/get-grouping-detail',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $request->get('pendaftaran_id'),
                        'kelompoktindakan_id' => $request->get('kelompoktindakanId'),
                        'isobat' => $request->get('isobat'),
                        'penjamin_id' => $request->get('penjamin_id'),
                        'no_klaim' => $request->get('no_klaim'),
                    ]
                ]
            ]);
            
            $no = $request->get('start', 1);
            $newRow = [];
            foreach ($response['data'] as $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['status'] = ArrayHelper::getValue($value, 'note_admin');
                $value['tarif_satuan'] = number_format($value['tarif_satuan'], 0);
                $value['sub_total'] = number_format($value['sub_total'], 0);
                $newRow[] = $value;
            }

            $return = [
                'data' => $newRow,
                'draw' => $request->get('draw'),
                'recordsTotal' => $response['_meta']['totalCount'],
                'recordsFiltered' => $response['_meta']['totalCount'],
            ];
            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPembatalan()
    {
        try {
            $request = Yii::$app->request;
 
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/pembatalan',
                'method' => 'POST',
                'payload' => [
                    'query' => [
                        'penjamin_id' => $request->post('penjamin_id'),
                    ],
                    'form_params' => [
                        'noklaim' => $request->post('no_klaim'),
                        'pendaftaran_id' => $request->post('pendaftaran_id'),
                        'asuransi_id' => $request->post('asuransi_id')
                    ]
                ]
            ]);

            $bodyData = ArrayHelper::getValue($response, 'response', []);
            if (isset($bodyData['status']) && $bodyData['status'] == 201 || $bodyData['status'] == 200) {
                return DocoHelpers::response([
                    'status' => $bodyData['status'],
                    'message' => ArrayHelper::getValue($bodyData, 'message'),
                    'data' => $bodyData
                ]);
            }
            
            $return = [
                'status' => 422,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ];
            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPengesahan()
    {
        try {
            $request = Yii::$app->request;
            return $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/pengesahan',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pendaftaran_id' => $request->post('pendaftaran_id'),
                        'penjamin_id' => $request->post('penjamin_id'),
                        'no_klaim' => $request->post('no_klaim'),
                        'kode_icd' => $request->post('kode_icd'),
                        'kode_icd_text' => $request->post('kode_icd_text'),
                    ]
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionResendTransaksi()
    {
        try {
            $request = Yii::$app->request;
            return $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/resend-transaksi',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'kode_icd' => $request->post('kode_icd'),
                        'kode_icd_text' => $request->post('kode_icd_text'),
                        'pendaftaran_id' => $request->post('pendaftaran_id'),
                    ]
                ],
                'returnResponse' => true
            ]); 
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCobPasien()
    {
        $title = 'COB Pasien Asuransi';
        return $this->render('cob-pasien', get_defined_vars());
    }

    public function actionCobPasienContent()
    {
        try {
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/search-pasien-cob',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'no_pendaftaran' => Yii::$app->request->post('noPendaftaran'),
                    ]
                ]
            ]);
                        
            /**
             * Collecting data.
             */
            $dataKunjungan = ArrayHelper::getValue($response, 'data');
            $dataInvoice = ArrayHelper::getValue($response, 'invoice');
            $dataInacbgs = ArrayHelper::getValue($response, 'dataInacbgs');

            $penjamin = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-integrasi-asuransi/get-penjamin-terintegrasi',
                'method' => 'get',
            ]);
            $penjamin = ArrayHelper::getValue($penjamin, 'data', []);
            $penjamin = ArrayHelper::map($penjamin, 'penjamin_id', 'penjamin_nama');

            $totalBilling = ArrayHelper::getValue($dataInvoice, 'total_dijamin');
            $kodeInacbgs = ArrayHelper::getValue($dataInacbgs, 'cbg');
            $tarifInacbgs = ArrayHelper::getValue($dataInacbgs, 'group_tarif');
            $diagnosaPrimer = ArrayHelper::getValue($dataInacbgs, 'diagnosa_primer');
            $statusKunjunganId = ArrayHelper::getValue($dataInacbgs, 'status_kunjungan');
            $statusKunjungan = ArrayHelper::getValue($dataInacbgs, 'status_kunjungan_nama');
            $totalCob = 0;
            if (isset($totalBilling) && $totalBilling >= $tarifInacbgs) {
                $totalCob = $totalBilling - $tarifInacbgs;
            }
            
            return $this->renderAjax('partial/_cob_pasien', compact('dataKunjungan', 'totalBilling', 'kodeInacbgs', 'tarifInacbgs', 'penjamin', 'diagnosaPrimer', 'statusKunjungan', 'statusKunjunganId', 'totalCob'));
        } catch (\Exception $th) {
            $result['error'] = $th->getMessage();
            return $result;
        }
    }

    public function actionPendaftaranCob()
    {
        try {
            $request = Yii::$app->request;

            $payloadRequest = [
                "no_pendaftaran" => $request->post('no_pendaftaran'),
                "penjamin_id" => $request->post('penjamin_id'),
                "tanggalmasuk" => $request->post('tglpendaftaran'),
                "nokartu" => $request->post('no_kartu'),
                "kodebenefit" => $request->post('benefit_id'),
                "statusrujukan" => "N",
                "cobbpjs" => 1,
                "nomorsep" => $request->post('nomorsep'),
                "keterangan" => $request->post('no_pendaftaran'),
                "notransaksiprovider" => $request->post('no_pendaftaran'),
                "inacbgscode" => $request->post('inacbgscode'),
                "inacbgsamount" => $request->post('inacbgsamount')
            ];
            
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/pendaftaran-cob',
                'method' => 'POST',
                'payload' => [
                    'form_params' => $payloadRequest
                ],
                'returnResponse' => true
            ]);

            return $response;
        } catch (\Exception $th) {
            $result['error'] = $th->getMessage();
            return $result;
        }
    }

    public function actionGetPendaftaran()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->guzzleExec($this->_restPenjamin, [
                'url' => 'inf-dashboard-integrasi/search-list-pasien-cob',
                'method' => 'GET',
                'payload' => [
                    'query' => [
                        'term' => $request->get('search')
                    ]
                ],
            ]);
            $result = ArrayHelper::getValue($response, 'data', []);
            $mappingData = [];

            if (! empty($result)) {
                foreach ($result as $value) {
                    $mappingData[] = [
                        'id' => $value['no_pendaftaran'],
                        'text' => $value['no_pendaftaran'] . ' - ' . $value['nama_pasien'],
                    ];
                }
            }

            return DocoHelpers::response([
                'status' => 200,
                'message' => 'Success',
                'result' => $mappingData
            ]);
        } catch (\Exception $th) {
            $result['error'] = $th->getMessage();
            return $result;
        }
    }

    public function actionGetDiagnosa()
    {
        try {
            $q = Yii::$app->request->get('q');
            $page = Yii::$app->request->get('page');
            $limit = 20;
            $offset = ($page-1)*10;
            $result = [];
            $result['results'] = [];
            $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;

            $request = $this->_restRajal->get('allow/get-new-diagnosa',['query'=>['q'=>$q,'type'=>$type,'page'=> $page,'offset'=> $offset,'limit'=> $limit]]);
            $response = json_decode($request->getBody(), true);
            
            $list = $response['response'];
            foreach ($response['response'] as $value) {
                $result['results'][] = [
                    'id' => $value['diagnosa_kode'],
                    'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                ];
            }

            $result['pagination'] = [ 'more' => !empty($list)?true:false ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
