<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\pendaftaran\InformasiPasien;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\PenanggungBiayaForm;
use app\modules\pendaftaran\models\EditPendaftaranForm;

class InformasiPasienUpdateSty extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        if(empty($id)) {
            $id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
        }
        $model = new EditPendaftaranForm;

        $modelAsuransi = new AsuransiForm;
        $modelPenanggungBiaya = new PenanggungBiayaForm;
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        if($request->get('type')) {
            $ruanganId = null;
            foreach(DocoConstants::PARAM_DFTR as $key => $value) {
                if($request->get('type') == $value) {
                    $ruanganId = $key;
                    break;
                }
            }
            if(!empty($ruanganId)) {
                $idx = array_search($ruanganId, array_column($active_workspace['ruangan_lain'], 'id'));
                $this->_titleUpdate = $active_workspace['ruangan_lain'][$idx]['name'];
            }            
        } else {
            $this->_titleUpdate = isset($active_workspace['ruangan_name']) ? $active_workspace['ruangan_name'] : null;
            $ruanganId = $active_workspace['ruangan_id'];
        }
        $instalasi_id = $active_workspace['instalasi_id'];
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_titleUpdate);
        $idRuanganPendaftaran = null;
        $jenis_pendaftaran = DocoConstants::PARAM_DFTR[$ruanganId];
        $model->jenis = $jenis_pendaftaran;
        // $model->scenario = ($jenis_pendaftaran != 'ranap') ? 'default' : 'ranap';
        switch($jenis_pendaftaran){
            case 'ranap':
                $model->scenario = 'ranap-sty';
                break;
            case 'penunjang':
                $model->scenario = 'st-yusup-penunjang';
                break;
            default:
                $model->scenario = 'default';
                break;
        }
        $is_bpjs = false;
        $payLoadRequest = [
            'kunjungan' => [],
            'asuransi' => []
        ];

        $post = $request->post();
        if ($post) {
            $model->attributes = $post['EditPendaftaranForm'];
            $carabayar_id = $model->carabayar_id;
            $model->tgl_admisi = date('Y-m-d H:i:s', strtotime($model->tgl_admisi));
            $model->tgl_pendaftaran = date('Y-m-d H:i:s', strtotime($model->tgl_pendaftaran));
            if($model->group_carabayar == DocoConstants::GROUP_BPJS) {
                $model->scenario = 'edit-bpjs';
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

            if ($carabayar_id == 41 || $carabayar_id == 44 || $carabayar_id == 45) {
                $modelPenanggungBiaya->attributes = $post['PenanggungBiayaForm'];
                $payLoadRequest['penanggungbiaya'] = $modelPenanggungBiaya->attributes;
            }

            $payLoadRequest['kunjungan'] = $model->attributes;
            $response = [];
            $res = Yii::$app->docoRest->pendaftaran->put('inf-pasien/update-pendaftaran?id='.$id, [
                'form_params' => $payLoadRequest
            ]);

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
            $response = Yii::$app->docoRest->pendaftaran->get('tra-pasien-rawat-jalan/view-sty', [
                'query' => [
                    'id' => $id,
                    'jenis' => $jenis_pendaftaran,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
            $model->attributes = $attributes['data_update'];
            $model->tgl_admisi = date('d-m-Y H:i', strtotime($model->tgl_admisi));
            $model->tgl_pendaftaran = date('d-m-Y H:i', strtotime($model->tgl_pendaftaran));
            $model->keterangan = $attributes['data_update']['keterangan_pendaftaran'];
            $model->nomor_urut = $attributes['selectedNomorUrut'];
            $model->temp_nomor_urut = $attributes['selectedNomorUrut'];
            // case bpjs
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
            $modelAsuransi->masaberlakukartu = !empty($data_asuransi['masaberlakukartu']) 
                    ? date('d-m-Y', strtotime($data_asuransi['masaberlakukartu'])) : null;
            $modelAsuransi->status_konfirmasi = $data_asuransi['status_konfirmasi'];
            $status_periksa = isset($attributes['data_update']['status_periksa_id']) 
                    ? $attributes['data_update']['status_periksa_id'] : null;
            
            $disabled = ($status_periksa == 1) ? false : true;

            // case form penanggung biaya
            $data_penanggungbiaya = $attributes['data_penanggungbiaya'];
            $modelPenanggungBiaya->penanggungbiaya_id = $data_penanggungbiaya['penanggungbiaya_id'];
            $modelPenanggungBiaya->penanggungbiaya_nama = $data_penanggungbiaya['penanggungbiaya_nama'];
            $modelPenanggungBiaya->namabagian = $data_penanggungbiaya['namabagian'];
            $modelPenanggungBiaya->noindukkaryawan = $data_penanggungbiaya['noindukkaryawan'];
            $modelPenanggungBiaya->instansi = $data_penanggungbiaya['instansi'];
            $modelPenanggungBiaya->pasien_id = $data_penanggungbiaya['pasien_id'];
            $modelPenanggungBiaya->carabayar_id = $data_penanggungbiaya['carabayar_id'];
            $modelPenanggungBiaya->ruangcarabayar_id = $data_penanggungbiaya['ruangcarabayar_id'];

            //case ranap
            if($jenis_pendaftaran == 'ranap') {
                $model->kamarruangan_nokamar = isset($attributes['data_update']['kamarruangan_nokamar']) 
                        ? $attributes['data_update']['kamarruangan_nokamar']. ' - ' . $attributes['data_update']['no_tempattidur'] : null;
                $model->pegawai_id = isset($attributes['data_update']['dokter_admisi_id']) 
                        ? $attributes['data_update']['dokter_admisi_id'] : null;
                $status_periksa = isset($attributes['data_update']['status_ranap']) 
                        ? $attributes['data_update']['status_ranap'] : null;
                $jeniskelamin_id = isset($attributes['data_update']['jeniskelamin_id']) 
                        ? $attributes['data_update']['jeniskelamin_id'] : null;
                $model->dokterkonsul_id = !empty($model->dokterkonsul_id)? json_decode($model->dokterkonsul_id) : null;

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
            $allDokterList = $attributes['data_all_dokter'];
            $penyakitList = $attributes['data_penyakit'];
            $carabayarList = $attributes['data_carabayar'];
            $penjaminList = $attributes['data_penjamin'];
            $kelasList = $attributes['data_kelas'];
            $carabayarOptions = $attributes['carabayarOptions'];
            $nomorUrutList = $attributes['nomorUrutList'];
            $isNomorUrut = $attributes['konfig']['is_nourut'];
            $isLimitTagihan = $attributes['konfig']['is_limit_tagihan'];
            $ruanganList = ArrayHelper::map($attributes['data_ruangan'], 'ruangan_id', 'ruangan_nama');
            $instalasiList = ArrayHelper::map($attributes['data_instalasi'], 'instalasi_id', 'instalasi_nama');
            $rujukandariList = ArrayHelper::map($attributes['data_rujukandari'], 'instalasi_id', 'instalasi_namalainnya');
            $bagianList = ArrayHelper::map($attributes['data_bagian'], 'ruangancarabayar_id', 'ruangcarabayar_nama');
            $prosedurMasukList = (count($attributes['data_prosedur_masuk']) > 0) ? ArrayHelper::map($attributes['data_prosedur_masuk'],'additional_data','ruangan_nama') : [];

            if (isset($body['konfig']['is_nourut']) && $body['konfig']['is_nourut']) {
                $model->scenario = 'default-nomor-urut';
            }
            
            return $controller->render('@app/extensions/pendaftaran/views/informasi-pasien/updateSty', get_defined_vars());
        }
    }
}