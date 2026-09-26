<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\ranap\components\traits;

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

use app\models\reseptur\GeneralResepturForm as ResepturForm;
use app\models\reseptur\GeneralResepturDetailForm as ResepturDetailForm;
use app\models\reseptur\GeneralResepturNrDetailForm as ResepturNrDetailForm;
use app\modules\rajal\models\ResepTempForm;

trait PemeriksaanResepturTrait
{
    public function actionFormModalReseptur()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $this->_pendaftaran_id;
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $pasienadmisi_id = $this->_pasienadmisi_id;
        $cppt_id = $request->get('cppt_id', 0);
        $modelReseptur = new ResepturForm;
        $modelResepturDetailNonRacikan = new ResepturNrDetailForm;

        $url = [
            'form-action' => '/ranap/pemeriksaan-rawat-inap/simpan-reseptur?id=' . $request->get('id', null) . '&cppt_id=' . $cppt_id,
            'dokter_url' => '/ranap/pemeriksaan-rawat-inap/list-dokter-reseptur',
            'actionTemplate' => '/ranap/pemeriksaan-rawat-inap/form-template-reseptur',
            'urlSimpanReseptur' => '/ranap/pemeriksaan-rawat-inap/simpan-template-reseptur',
            'urlDeleteTemplate' => '/ranap/pemeriksaan-rawat-inap/delete-template-reseptur',
            'urlUpdateTemplate' => '/ranap/pemeriksaan-rawat-inap/form-template-reseptur?reseptemp_id=#reseptemp_id#&is_update=true',
            'urlModalHistoryResep' => '/ranap/pelayanan/modal-history-resep?pasien_id=' . $this->_pasien_id
        ];

        $defaultData = $this->helper->guzzleExec($this->_restRanap, [
            'url' => 'cppt/default-data-reseptur',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                    'pasienadmisi_id' => $pasienadmisi_id,
                    'ruangan_id' => $this->_id_ruangan
                ]
            ]
        ]);

        $user = $this->getPegawai();
        $kelompokpegawai_id = ArrayHelper::getValue($user, 'kelompokpegawai_id');

        $modelReseptur->diagnosa_id = ArrayHelper::getValue($defaultData, 'diagnosa_id');
        $modelReseptur->diagnosa_nama = ArrayHelper::getValue($defaultData, 'diagnosa_nama');
        $modelReseptur->berat_badan = ArrayHelper::getValue($defaultData, 'berat_badan');
        $modelReseptur->tinggi_badan = ArrayHelper::getValue($defaultData, 'tinggi_badan');
        $modelReseptur->dokter = ArrayHelper::getValue($user, 'nama_pegawai');
        $modelReseptur->pegawai_id = ArrayHelper::getValue($user, 'id_pegawai');
        $data_pasien = $this->_data_pasien;

        $modelReseptur->depo_id = ArrayHelper::getValue($defaultData, 'depo_id');

        $default_dokter = [
            $modelReseptur->pegawai_id => ArrayHelper::getValue($user, 'nama_pegawai')
        ];

        $is_others = 0;
        if($defaultData['is_others']){
            $is_others = 1;
        }

        $enable_split_kronis = 0;
        if($defaultData['enable_split_kronis']) {
            $enable_split_kronis = 1;
        }

        $getKategoriResep = $this->guzzleExec($this->_restRajal, [
            'url' => 'allow/get-lookup-type',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'type' => 'kategori_resep'
                ]
            ]
        ]);

        $kategori_resep = $getKategoriResep;
        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }

        $lookupTransaksi = $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'allow/get-lookup-transaksi',
            'payload' => [
                'query' => [
                    'kode_transaksi' => DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES
                ]
            ]
        ]);
        $konfigStokObatAlkes = ArrayHelper::getValue($lookupTransaksi, 'data.additional_value');
        $allowZeroStock = ArrayHelper::getValue($defaultData, 'lookuptransaksi_m.config_zero_stock', 0) ? true : false;

        return $this->renderAjax('//cppt/reseptur/__modal', [
            'model' => $modelReseptur,
            'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
            'list_data_apotek' => ArrayHelper::getValue($defaultData, 'list-depo', []),
            'pendaftaran_id' => $pendaftaran_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'pegawai_id' => ArrayHelper::getValue($user, 'id_pegawai'),
            'ruanganreseptur_id' => $data_pasien['ruangan_id'],
            'data_pasien' => $data_pasien,
            'url' => $url,
            'is_ranap' => true,
            'dokterList' => $default_dokter,
            'is_freetext' => ArrayHelper::getValue($defaultData, 'is_freetext'),
            'is_others' => $is_others,
            'enable_split_kronis' => $enable_split_kronis,
            'hari_resep_kronis' => ArrayHelper::getValue($defaultData, 'hari_resep_kronis'),
            'actionTemplate' => '/ranap/pemeriksaan-rawat-inap/form-template-reseptur',
            'urlSimpanReseptur' => '/ranap/pemeriksaan-rawat-inap/simpan-template-reseptur',
            'kategori_resep' => $kategori_resep,
            'konfigStokObatAlkes' => $konfigStokObatAlkes,
            'allowZeroStock' => $allowZeroStock,
            'infoPasien' => [
                'nama_pasien' => ArrayHelper::getValue($data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ],
            'instalasi_id' => ArrayHelper::getValue($this->_data_pasien, 'instalasi_id')
        ]);
    }

    public function actionListDokterReseptur() {
        $dokterList = $this->guzzleExec($this->_restRanap, [
            'url' => 'allow/list-dokter-perujuk',
            'method' => 'get',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
        ]);
        return DocoHelpers::response($dokterList);
    }

    public function actionSimpanReseptur($cppt_id)
    {
        try {
            $request = Yii::$app->request;

            $header = $request->post('reseptur_header', []);
            $detail = $request->post('list_obat', []);
            $header['tglreseptur'] = Date('Y-m-d H:i:s');
            $user = $this->getPegawai();
            $instruksi = [
                'catatan_instruksi' => $header['catatan'],
                'cppt_id' => DocoHelpers::decrypt($cppt_id),
                'jenis_instruksi' => '458',
                'is_puasa' => $header['is_puasa'],
                'tgl_instruksi' => Date('Y-m-d H:i:s'),
                'pendaftaran_id' => $header['pendaftaran_id'],
                'pegawai_id' => $header['pegawai_id'],
                'pasien_id' => $this->_pasien_id,
                'admisi_id' => $this->_pasienadmisi_id,
                'ruangan' => $this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur']
            ];

            $payload = [
                'data_instruksi' => $instruksi,
                'data_reseptur' => $header,
                'data_resepturdetail' => $detail
            ];

            $response =  $this->_restRanap->post('cppt/simpan-reseptur', [
                'form_params' => $payload
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
            
        } catch (RequestException $e) {
            $error = json_decode($e->getResponse()->getBody(), true);
            $message = isset($error['response']['message']) ? $error['response']['message'] : $e->getMessage();
            return DocoHelpers::response(['message' => $message, 'response' => ['message' => $message]], $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    private function getPegawai()
    {
        $user = Yii::$app->session->get('user_identity');
        if ($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS) {
            $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
            $user['id_pegawai'] = $this->_data_pasien['dokter_admisi_id'];
        }

        return $user;
    }

    public function actionFormTemplateReseptur() {
        $reseptemp_id = Yii::$app->request->get('reseptemp_id', null);
        $is_update = Yii::$app->request->get('is_update', false);
        return $this->renderAjax('//cppt/reseptur/_template', get_defined_vars());
    }

    public function actionSimpanTemplateReseptur() {
        $data_template = [
            'dokter_id' => Yii::$app->request->post('dokter_id'),
            'reseptemp_nama' => Yii::$app->request->post('reseptemp_nama'),
        ];

        $listDetail = Yii::$app->request->post('list_obat');
        $data_template_detail = [];
        $listAttr = [ 'racikan_id', 'rke', 'obatalkes_id', 'satuankecil_id', 'qty', 'signa_id', 'signa', 'is_kronis', 'additional_data'];
        foreach ($listDetail as $detail) {
            $additional_data = $detail;
            foreach ($listAttr as $attr) {
                unset($additional_data[$attr]);
            }
            $detail['additional_data'] = isset($detail['additional_data']) ? json_decode($detail['additional_data'], true) : [];
            $det = [
                'racikan_id' => isset($detail['racikan_id']) ? $detail['racikan_id'] : null,
                'rke' => isset($detail['rke']) ? $detail['rke'] : null,
                'obatalkes_id' => isset($detail['obatalkes_id']) ? $detail['obatalkes_id'] : null,
                'satuankecil_id' => isset($detail['satuankecil_id']) ? $detail['satuankecil_id'] : null,
                'qty' => isset($detail['qty_reseptur']) ? $detail['qty_reseptur'] : null,
                'signa_id' => isset($detail['signa_id']) ? $detail['signa_id'] : null,
                'signa' =>  array_merge(isset($detail['signa']) ? ['text'=> $detail['signa']] : [], isset($detail['signa_id']) ? ['id'=> $detail['signa_id']] : []),
                'is_kronis' => isset($detail['is_kronis']) ? $detail['is_kronis'] : false,
                'additional_data' => array_merge($detail['additional_data'], $additional_data),
            ];
            $data_template_detail[] = $det;
        }

        // Update Template Resep
        if (Yii::$app->request->post('reseptemp_id')) {
            $data_template['reseptemp_id'] = Yii::$app->request->post('reseptemp_id');

            return $this->helper->guzzleExec($this->_restRanap, [
                'url' => 'cppt/update-template-reseptur',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'data_template' => $data_template,
                        'data_template_detail' => $data_template_detail,
                    ]
                ],
                'returnResponse' => true
            ]);
        } else {
            return $this->helper->guzzleExec($this->_restRanap, [
                'url' => 'cppt/save-template-reseptur',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'data_template' => $data_template,
                        'data_template_detail' => $data_template_detail,
                    ]
                ],
                'returnResponse' => true
            ]);
        }
    }

    public function actionDeleteTemplateReseptur()
    {
        return $this->guzzleExec($this->_restRanap, [
            'url' => 'cppt/delete-template-reseptur',
            'method' => 'POST',
            'payload' => [
                'form_params' => Yii::$app->request->post()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionModalHistoryResep()
    {
        $pasien_id = Yii::$app->request->get('pasien_id', null);
        return $this->renderAjax('//cppt/reseptur/_history_resep', get_defined_vars());
    }
}
