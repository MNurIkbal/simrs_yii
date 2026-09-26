<?php

namespace app\modules\igd\components\traits;

use Yii;

use app\components\DocoConstants;
use app\components\DocoHelpers;

use app\models\reseptur\GeneralResepturForm;
use app\models\reseptur\GeneralResepturDetailForm;
use app\models\reseptur\GeneralResepturNrDetailForm;
use app\models\reseptur\GeneralResepTempForm;
use yii\helpers\ArrayHelper;

trait PemeriksaanResepturTrait
{

    /**
     * Function for show reseptur modal form
     *
     * @param String $id
     * @param String $cppt_id
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFormModalReseptur()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $this->_pendaftaran_id;
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $cppt_id = $request->get('cppt_id', 'MA');

        $modelReseptur = new GeneralResepturForm;
        $modelResepturDetailNonRacikan = new GeneralResepturNrDetailForm;

        $url = [
            'form-action' => '/igd/pemeriksaan-igd/simpan-reseptur?id=' . $this->helper->encrypt($pendaftaran_id) . '&cppt_id=' . $cppt_id,
            'dokter_url' => '/igd/pemeriksaan-igd/list-dokter-reseptur',
            'actionTemplate' => '/igd/pemeriksaan-igd/form-template-reseptur',
            'urlSimpanReseptur' => '/igd/pemeriksaan-igd/simpan-template-reseptur',
            'urlDeleteTemplate' => '/igd/pemeriksaan-igd/delete-template-reseptur',
            'urlUpdateTemplate' => '/igd/pemeriksaan-igd/form-template-reseptur?reseptemp_id=#reseptemp_id#&is_update=true',
            'urlModalHistoryResep' => '/igd/pemeriksaan-igd/modal-history-resep?pasien_id=' . $this->_pasien_id
        ];

        $defaultData = $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-dpjp/default-data-reseptur',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                ]
            ]
        ]);

        $user = Yii::$app->session->get('user_identity');

        if (Yii::$app->docoVars->user("kelompokpegawai_id") != DocoConstants::KELOMPOK_MEDIS) {
            $user = [
                // Prevent ketika kolom dokter / dokter_id null, jadi gabisa order reseptur
                // 'id_pegawai' => $this->_data_pasien['dokter_id'],
                // 'nama_pegawai' => $this->_data_pasien['dokter']
                'id_pegawai' => !empty($this->_data_pasien['dokter_id']) ? $this->_data_pasien['dokter_id'] : $this->_data_pasien['dokter_jaga_id'],
                'nama_pegawai' => !empty($this->_data_pasien['dokter']) ? $this->_data_pasien['dokter'] : $this->_data_pasien['dokter_jaga']
            ];
        } else {
            if ($this->_data_pasien['dokter_jaga_id'] != $user['id_pegawai']) {
                $user['nama_pegawai'] = $this->_data_pasien['dokter_jaga'];
                $user['id_pegawai'] =  $this->_data_pasien['dokter_jaga_id'];
            }
        }

        $modelReseptur->diagnosa_id = ArrayHelper::getValue($defaultData, 'diagnosa_id');
        $modelReseptur->diagnosa_nama = ArrayHelper::getValue($defaultData, 'diagnosa_nama');
        $modelReseptur->berat_badan = ArrayHelper::getValue($defaultData, 'berat_badan');
        $modelReseptur->tinggi_badan = ArrayHelper::getValue($defaultData, 'tinggi_badan');
        if (($modelReseptur->berat_badan != '' && $modelReseptur->berat_badan != 0) && ($modelReseptur->tinggi_badan != '' && $modelReseptur->tinggi_badan != 0)) {
            $modelReseptur->luas_tubuh = number_format(sqrt(((float) $modelReseptur->berat_badan * (float) $modelReseptur->tinggi_badan) / 3600), 2, ',', '.');
        }
        $modelReseptur->depo_id = ArrayHelper::getValue($defaultData, 'depo_id');
        $modelReseptur->dokter = ArrayHelper::getValue($user, 'nama_pegawai');
        $modelReseptur->pegawai_id = ArrayHelper::getValue($user, 'id_pegawai');
        $data_pasien = $this->_data_pasien;

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

        $lookupTransaksi = $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'allow/get-lookup-transaksi',
            'payload' => [
                'query' => [
                    'kode_transaksi' => DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES
                ]
            ]
        ]);
        $konfigStokObatAlkes = ArrayHelper::getValue($lookupTransaksi, 'data.additional_value');
        $allowZeroStock = ArrayHelper::getValue($defaultData, 'lookuptransaksi_m.config_zero_stock', 0) ? true : false;
        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }
        
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
            'is_freetext' => $defaultData['is_freetext'],
            'is_others' => $is_others,
            'enable_split_kronis' => $enable_split_kronis,
            'hari_resep_kronis' => ArrayHelper::getValue($defaultData, 'hari_resep_kronis'),
            'actionTemplate' => '/igd/pemeriksaan-igd/form-template-reseptur',
            'urlSimpanReseptur' => '/igd/pemeriksaan-igd/simpan-template-reseptur',
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
        $dokterList = $this->guzzleExec($this->_restIgd, [
            'url' => 'allow/list-dokter-perujuk',
            'method' => 'get',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
        ]);
        return DocoHelpers::response($dokterList);
    }

    public function actionSimpanReseptur()
    {
        $model = new GeneralResepturForm;
        $modelResepturDetail = new GeneralResepturDetailForm;
        $modelResepturDetailNr = new GeneralResepturNrDetailForm;
        $cppt_id = Yii::$app->request->get('cppt_id', null);
        $resepturHeader = Yii::$app->request->post('reseptur_header');
        $model->attributes = $resepturHeader;
        $data_send = [
            'data_instruksi' => [
                'jenis_instruksi' => 458,
                'catatan_instruksi' => $resepturHeader['catatan'],
                'cppt_id' => !is_null($cppt_id) ? $this->helper->decrypt($cppt_id) : 'MA',
                'tgl_instruksi' => date('Y-m-d H:i:00'),
                'instruksi_id' => ''
            ],
            'data_reseptur' => $model->attributes,
            'data_resepturdetail' => Yii::$app->request->post('list_obat'),
            'pasien_id' => $this->_pasien_id,
            'ruangan' => $this->_ruangan_id
        ];

        $response =  $this->_restIgd->post('asesmen-dpjp/create-reseptur', [
            'form_params' => $data_send,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
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

            return $this->helper->guzzleExec($this->_restIgd, [
                'url' => 'asesmen-dpjp/update-template-reseptur',
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
            return $this->helper->guzzleExec($this->_restIgd, [
                'url' => 'asesmen-dpjp/save-template-reseptur',
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
        return $this->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-dpjp/delete-template-reseptur',
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
