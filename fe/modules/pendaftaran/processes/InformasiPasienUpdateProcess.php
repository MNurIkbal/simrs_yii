<?php
/**
 *
 * @author : Fajar (fajar.supriadi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\EditPendaftaranForm;
use app\modules\pendaftaran\components\Lookup;
use app\modules\pendaftaran\models\MultiCarabayarForm;

class InformasiPasienUpdateProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $request = Yii::$app->request;
        $primaryKey = $request->get('id');
        $id = DocoHelpers::decrypt($request->get('id'));
        if(empty($id)) {
            $id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
            $primaryKey = $request->get('pendaftaran_id');
        }
        $model = new EditPendaftaranForm;
        $multiPayer = new MultiCarabayarForm;

        $modelAsuransi = new AsuransiForm;
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $this->_titleUpdate = isset($active_workspace['ruangan_name']) ? $active_workspace['ruangan_name'] : null;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_titleUpdate);
        $instalasi_id = $active_workspace['instalasi_id'];
        $ruanganId = $active_workspace['ruangan_id'];
        $idRuanganPendaftaran = null;
        // $jenis_pendaftaran = DocoConstants::PARAM_DFTR[$ruanganId];
        $jenis_pendaftaran = ArrayHelper::getValue((new Lookup)->getValueFromLookupT($ruanganId, 'workspace_pendaftaran'), 'additional_value');
        $model->jenis = $jenis_pendaftaran;
        $model->scenario = ($jenis_pendaftaran != 'ranap') ? 'default' : 'ranap';
        $is_bpjs = false;
        $payLoadRequest = [
            'kunjungan' => [],
            'asuransi' => []
        ];

        $post = $request->post();
        if ($post) {
            $model->attributes = $post['EditPendaftaranForm'];
            $model->tgl_admisi = date('Y-m-d H:i:s', strtotime($model->tgl_admisi));
            if($model->group_carabayar == DocoConstants::GROUP_BPJS) {
                if($controller->_is_SEP_mandatory) {
                    $model->scenario =  'edit-bpjs-w-sep';
                } else {
                    $model->scenario =  'edit-bpjs';
                }
                $allow_bpjs = "";
                $is_bpjs = true;
                if($post['EditPendaftaranForm']['allow-bpjs'] == 1) {
                    $allow_bpjs = $post['EditPendaftaranForm']['allow-bpjs'];
                }
                if($model->jenis == 'ranap') {
                    $model->is_pasientitipan = $post['pasientitipan'];
                    $model->is_aps = $post['pasienaps'];
                }
                $model->allow_bpjs = $allow_bpjs;
                $model->is_bpjs = $is_bpjs;
            } else if ($model->group_carabayar == DocoConstants::GROUP_JAMINAN) {
                $modelAsuransi->scenario = 'edit-pendaftaran';
                $modelAsuransi->attributes = $post['AsuransiForm'];
                $modelAsuransi->tgl_konfirmasi = !empty($modelAsuransi->tgl_konfirmasi)
                        ? date('Y-m-d', strtotime($modelAsuransi->tgl_konfirmasi)) : null;
                $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
            }

            $model->konfig_referral_required = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                    && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';

            if(!$model->validate()) {
                $response = [
                    'metadata' => [
                        'status' => 422
                    ],
                    'response' => [
                        'data' => DocoHelpers::parseError($model->errors, 'EditPendaftaranForm'),
                    ]
                ];
                return DocoHelpers::response($response);
            }

            if($model->is_multi_payer && !empty($request->post('MultiCarabayarForm'))) {
                $postMultipayer = $request->post('MultiCarabayarForm');
                $multiPayer->scenario = ArrayHelper::getValue($postMultipayer, 'carabayar_id') == DocoConstants::CARA_BAYAR_ASU ? 'first-validate-asuransi' : 'default' ;
                $multiPayer->attributes = $postMultipayer;
                $multiPayer->add_namapemilikasuransi_1 = ArrayHelper::getValue($postMultipayer, 'add_namapemilikasuransi_1');

                if (!$multiPayer->validate()) {
                    $response = [
                        'metadata' => [
                            'status' => 422
                        ],
                        'response' => [
                            'data' => DocoHelpers::parseError($multiPayer->errors, 'EditPendaftaranForm'),
                        ]
                    ];
                    return DocoHelpers::response($response);
                }
                $payLoadRequest['multi_payer'] = $multiPayer->attributes;
            }

            $payLoadRequest['kunjungan'] = $model->attributes;
            $response = [];
            $res = Yii::$app->docoRest->pendaftaran->put('inf-pasien/update-pendaftaran?id='.$id, [
                'form_params' => $payLoadRequest
            ]);

            // reset cache
            Yii::$app->cache->delete('gizi-pendaftaran-id-'. $primaryKey);

            $body = json_decode($res->getBody(), true);
            if (isset($body['metadata']['status'])) {
                $status = $body['metadata']['status'];
                if ($status == 422) {
                    $error = [];
                    if (isset($body['response']['data'])) {
                        $data = $body['response']['data'];
                        if(isset($data['kunjungan'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['kunjungan'], 'EditPendaftaranForm'));
                        }

                        if(isset($data['asuransi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['asuransi'], 'AsuransiForm'));
                        }

                        $response = [
                            'metadata' => [
                                'status' => 422
                            ],
                            'response' => [
                                'data' => $error
                            ]
                        ];

                        return DocoHelpers::response($response);
                    } elseif(isset($body['response']['is_bpjs'])) {
                        return DocoHelpers::response($body, false , 'EditPendaftaranForm');
                    }
                }
            }
            return DocoHelpers::response($body);
        } else {
            $response = Yii::$app->docoRest->pendaftaran->get('tra-pasien-rawat-jalan/view', [
                'query' => [
                    'id' => $id,
                    'jenis' => $jenis_pendaftaran,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
            $model->attributes = $attributes['data_update'];
            $model->tgl_admisi = date('d-m-Y H:i', strtotime($model->tgl_admisi));
            $model->keterangan = $attributes['data_update']['keterangan_pendaftaran'];
            $model->nomor_urut = $attributes['selectedNomorUrut'];
            $model->temp_nomor_urut = $attributes['selectedNomorUrut'];
            $model->is_multi_payer = !empty($attributes['data_additional_payer']) ? true : false;

            if ($model->is_multi_payer) {
                $multiPayer->add_no_asuransi_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.nokartuasuransi');
                $multiPayer->add_namapemilikasuransi_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.namapemilikasuransi');
                $multiPayer->add_asuransipasien_id_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.asuransipasien_id');
                $multiPayer->add_nomorpokokperusahaan_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.nomorpokokperusahaan');
                $multiPayer->add_kelastanggungan_id_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.kelastanggunganasuransi_id');
                $multiPayer->add_namaperusahaan_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.namaperusahaan');
                $multiPayer->add_tgl_konfirmasi_1 = isset($attributes['data_additional_payer']['tgl_konfirmasi']) ?  date('d-m-Y', strtotime($attributes['data_additional_payer']['tgl_konfirmasi'])) : null;
                $multiPayer->add_carabayar_id_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.carabayar_id');
                $multiPayer->add_penjamin_id_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.penjamin_id');
                $multiPayer->add_status_konfirmasi_1 = ArrayHelper::getValue($attributes, 'data_additional_payer.status_konfirmasi');
            }

            $jenisPelayanan = '';
            $data_bpjs = $attributes['data_bpjs'];
            if(!empty($data_bpjs)) {
                $peserta = isset($data_bpjs['peserta']) ? $data_bpjs['peserta'] : '';
                $jenisPelayanan = ($data_bpjs['jnsPelayanan'] == DocoConstants::KLS_PLYN_RANAP_BPJS)
                        ? 'Rawat Inap' : 'Rawat Jalan';
            }

            $model->nosep = isset($data_bpjs['noSep']) ? $data_bpjs['noSep'] : '';
            $nama_peserta = isset($peserta['nama']) ? $peserta['nama'] : '';
            $no_kartu = isset($peserta['noKartu']) ? $peserta['noKartu'] : '-';
            $jenis_pelayanan = isset($data_bpjs['jnsPelayanan']) ? $jenisPelayanan : '-';
            $poli_tujuan = isset($data_bpjs['poli']) ? $data_bpjs['poli'] : '-';
            $kelas_rawat = isset($peserta['hakKelas']) ? $peserta['hakKelas'] : '-';
            $nmjenispeserta = isset($peserta['jnsPeserta']) ? $peserta['jnsPeserta'] : '-';
            $tgl_lahir = isset($peserta['tglLahir']) ?
                date('d-M-Y', strtotime($peserta['tglLahir'])) : '-';

            // case form asuransi
            $data_asuransi = $attributes['data_asuransi'];

            $modelAsuransi->asuransipasien_id = $data_asuransi['asuransipasien_id'];
            $modelAsuransi->nokartuasuransi = $data_asuransi['nokartuasuransi'];
            $modelAsuransi->namapemilikasuransi = $data_asuransi['namapemilikasuransi'];
            $modelAsuransi->nomorpokokperusahaan = $data_asuransi['nomorpokokperusahaan'];
            $modelAsuransi->kelastanggungan_id = $data_asuransi['kelastanggunganasuransi_id'];
            $modelAsuransi->namaperusahaan = $data_asuransi['namaperusahaan'];
            $modelAsuransi->tgl_konfirmasi = !empty($data_asuransi['tgl_konfirmasi'])
                    ? date('d-m-Y', strtotime($data_asuransi['tgl_konfirmasi'])) : null;
            $modelAsuransi->status_konfirmasi = $data_asuransi['status_konfirmasi'];
            $status_periksa = isset($attributes['data_update']['status_periksa_id'])
                    ? $attributes['data_update']['status_periksa_id'] : null;

            $disabled = ($status_periksa == 1) ? false : true;

            //case ranap
            if($jenis_pendaftaran == 'ranap') {
                $model->kamarruangan_nokamar = isset($attributes['data_update']['kamarruangan_nokamar'])
                        ? $attributes['data_update']['kamarruangan_nokamar']. ' - ' . $attributes['data_update']['no_tempattidur'] : null;
                $model->pegawai_id = isset($attributes['data_update']['admisi_dokter_id'])
                        ? $attributes['data_update']['admisi_dokter_id'] : null;
                $status_periksa = isset($attributes['data_update']['status_ranap'])
                        ? $attributes['data_update']['status_ranap'] : null;
                $jeniskelamin_id = isset($attributes['data_update']['jeniskelamin_id'])
                        ? $attributes['data_update']['jeniskelamin_id'] : null;

                $statusPeriksa = DocoConstants::STATUS_RANAP_BELUM_PERIKSA;

                // if (!empty($attributes['data_update']['is_pasientitipan_pk'])) {
                //     if($attributes['data_update']['is_pasientitipan_pk'] == true && $attributes['data_update']['is_stoppasientitipan'] == false){
                //         $model->kelaspelayanan_id = $attributes['data_update']['kelas_ditagihkan_id'];
                //         $model->kelaspelayanan_nama = $attributes['data_update']['kelas_ditagihkan_nama'];
                //     }
                // } else if (empty($attributes['data_update']['is_pasientitipan_pk'])) {
                //     if($attributes['data_update']['is_pasientitipan'] == true && $attributes['data_update']['is_stoppasientitipan'] == false){
                //         $model->kelaspelayanan_id = $attributes['data_update']['kelas_ditagihkan_id'];
                //         $model->kelaspelayanan_nama = $attributes['data_update']['kelas_ditagihkan_nama'];
                //     }
                // }

                $class = ($status_periksa === $statusPeriksa) ? 'tgl_admisi' : '';
                $disabled = ($status_periksa === $statusPeriksa) ? false : true;

                $cache = Yii::$app->cache;
                $masterWarnaTempatTidur = $cache->getOrSet("warna-tempat-tidur", function() {
                    $masterWarnaTempatTidur = Yii::$app->docoRest->pendaftaran->get(
                        'allow-antrian/get-warna-tempat-tidur', [
                            'query' => [],
                        ]
                    );
                    return json_decode($masterWarnaTempatTidur->getBody(), true)['response']['warna_tempat_tidur'];
                });
            }

            $dokterList = $attributes['data_dokter'];
            $data_ruangan = ArrayHelper::getValue($attributes, 'data_ruangan', []);
            $ruanganList = ArrayHelper::map($data_ruangan, 'ruangan_id', 'ruangan_nama');
            $penyakitList = $attributes['data_penyakit'];
            $carabayarList = $carabayarSecondList = $attributes['data_carabayar'];
            unset(
                $carabayarSecondList[DocoConstants::CARA_BAYAR_PRIVATE],
                $carabayarSecondList[DocoConstants::CARA_BAYAR_BPJS],
                $carabayarSecondList[DocoConstants::CARA_BAYAR_BPJS_KETENAGAKERJAAN]
            );
            $penjaminList = $attributes['data_penjamin'];
            $kelasList = $attributes['data_kelas'];
            $carabayarOptions = $attributes['carabayarOptions'];
            $nomorUrutList = $attributes['nomorUrutList'];
            $isNomorUrut = $attributes['konfig']['is_nourut'];
            $isLimitTagihan = $attributes['konfig']['is_limit_tagihan'];
            $isOnlyCreateSep = ArrayHelper::getValue($attributes, 'isOnlyCreateSep', false);
            $support_multipayer = ArrayHelper::getValue($attributes, 'konfig.support_multipayer');
            $data_instalasi_id = ArrayHelper::getValue($attributes, 'data_update.instalasi_id');

            $cekBayar = Yii::$app->docoRest->pendaftaran->get(
                'tra-pasien-rawat-jalan/get-pembayaran', [
                    'query' => [
                        'pendaftaran_id' => $id
                    ],
                ]
            );
            $cekBayar = json_decode($cekBayar->getBody(), true);
            $countBayar = ArrayHelper::getValue($cekBayar, 'response.count_pembayaran');
            $disabledCaraBayarPenjamin = ($countBayar > 0) ? true : false;

            if (isset($body['konfig']['is_nourut']) && $body['konfig']['is_nourut']) {
                $model->scenario = 'default-nomor-urut';
            }

            $notMcuPenunjang = !in_array($jenis_pendaftaran,['penunjang','mcu']);

            $showReferral = ArrayHelper::getValue($attributes, 'konfig.show_referal_pendaftaran');
            $konfigReferralRequired = ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE) == TRUE 
                    && strtoupper(ArrayHelper::getValue((new Lookup)->getValueFromLookupT(NULL, 'required_referral'), 'additional_value', FALSE)) == 'TRUE';
            $listReferral = $konfigReferralRequired
                    ? ArrayHelper::getValue($attributes, 'referral_marketing', [])
                    : ArrayHelper::map(ArrayHelper::getValue($attributes, 'pegawai.pegawai', []), 'pegawai_id', 'nama_pegawai');
            $model->referal = $konfigReferralRequired 
                    ? ArrayHelper::getValue($attributes,'data_update.referal_luar') 
                    : ArrayHelper::getValue($attributes,'data_update.referal_pegawai_id');
            
            return $controller->render('form_update', get_defined_vars());
        }
    }
}
