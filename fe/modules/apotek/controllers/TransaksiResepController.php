<?php

/**
 * @author : Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @edited by : Anggoro (tri.anggoro@docotel.com)
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;
use Doco\apotek\models\TransaksiResepForm;
use yii\helpers\ArrayHelper;
use Doco\apotek\models\PenjualanResepPasienForm;

class TransaksiResepController extends DocoController
{

    protected $_title = "Penjualan Resep";
    protected $_module = '/apotek/transaksi-resep';
    protected $_restApotek;
    protected $_restMaster;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions() {
        $actions = parent::actions();
        $action = [
            'edit-reseptur'             => 'Doco\apotek\actions\TransaksiResep\EditResepturAction',
            'edit-resep'                => 'Doco\apotek\actions\TransaksiResep\EditResepAction',
            'mark-deleted'              => 'Doco\apotek\actions\TransaksiResep\MarkDeletedAction',
            'save-cache-edit'           => 'Doco\apotek\actions\TransaksiResep\SaveCacheEditAction',
            'update-cache-edit'         => 'Doco\apotek\actions\TransaksiResep\UpdateCacheEditAction',
            'save-multiple-cache-edit'  => 'Doco\apotek\actions\TransaksiResep\SaveMultipleCacheEditAction',
            'save-edit-resep'           => 'Doco\apotek\actions\TransaksiResep\SaveEditResepAction',
            'save-edit-reseptur'        => 'Doco\apotek\actions\TransaksiResep\SaveEditResepturAction',
            'approve-reseptur'          => 'Doco\apotek\actions\TransaksiResep\ApproveResepturAction',
            'approve-resep'             => 'Doco\apotek\actions\TransaksiResep\ApproveResepAction',
            'save-approve-reseptur'     => 'Doco\apotek\actions\TransaksiResep\SaveApproveResepturAction',
            'save-approve-resep'     => 'Doco\apotek\actions\TransaksiResep\SaveApproveResepAction',
            'modal-multiple-etiket'     => 'Doco\apotek\actions\TransaksiResep\ModalMultipleEtiketAction',
            'print-multiple-etiket'     => 'Doco\apotek\actions\TransaksiResep\PrintMultipleEtiketAction',
        ];
        $actions = array_merge($actions,$action);
        return $actions;
    }

    public function groupingResep($cacheResep)
    {
        $kelompok_resep = [];
        foreach ($cacheResep as $item_obat) {
            if(!isset($item_obat['posisi'])) {
                continue;
            }

            $posisi = $item_obat['posisi'];

            if ($item_obat['is_racikan']) {
                $kelompok_resep[$item_obat['r_ke']][$posisi] = $item_obat;
            } else {
                $kelompok_resep['non'][$posisi] = $item_obat;
            }
        }

        $final_list = [];
        foreach ($kelompok_resep as $kelompok) {
            $final_list = array_merge($final_list, $kelompok);
        }
        return $final_list;
    }


    public function actionRumahSakit($id)
    {
        $title = $this->_title . ' Pasien Rumah Sakit';
        $cache = Yii::$app->cache;
        $model = new TransaksiResepForm;
        $id = DocoHelpers::decrypt($id);
        $data = $this->getData($id);

        $decId = $id;
        $list_signa = ArrayHelper::map($data['data_signa'], 'signa_id', 'signa_nama');
        $checkBackdate = (DHtml::cekHakAkses('is-backdate')) ? '1' : '2';
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $no_resep = !empty($data['data_resep']['noresep']) ? $data['data_resep']['noresep'] : '-';
        $no_pendaftaran = !empty($data['data_resep']['no_pendaftaran']) ? $data['data_resep']['no_pendaftaran'] : '-';
        $no_rekam_medik = !empty($data['data_resep']['no_rekam_medik']) ? $data['data_resep']['no_rekam_medik'] : '-';
        $nama_pasien = !empty($data['data_resep']['nama_pasien']) ? $data['data_resep']['nama_pasien'] : '-';
        $nama_pegawai = !empty($data['data_resep']['nama_pegawai']) ? $data['data_resep']['nama_pegawai'] : '-';
        $instalasi_nama = !empty($data['data_resep']['instalasi_reseptur']) ? $data['data_resep']['instalasi_reseptur'] : '-';
        $ruangan_nama = !empty($data['data_resep']['ruangan_reseptur']) ? $data['data_resep']['ruangan_reseptur'] : '-';
        $carabayar_nama = !empty($data['data_resep']['carabayar_nama']) ? $data['data_resep']['carabayar_nama'] : '-';
        $penjamin_nama = !empty($data['data_resep']['penjamin_nama']) ? $data['data_resep']['penjamin_nama'] : '-';
        $tanggal_lahir = !empty($data['data_resep']['tanggal_lahir']) ? date('d-m-Y', strtotime($data['data_resep']['tanggal_lahir'])) : '-';
        $penjualanresep_id = !empty($data['data_resep']['penjualanresep_id']) ? $data['data_resep']['penjualanresep_id'] : null;

        if(isset($data['data_resep']['diagnosa_id'])){
            $diagnosa = $data['data_resep']['diagnosa_nama'];
        } else if(isset($data['data_resep']['diagnosa_text'])) {
            $diagnosa = $data['data_resep']['diagnosa_text'];
        } else {
            $diagnosa = null;
        }

        $cacheLabel = 'addObatRs' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabel, null);
        $cacheLabelTrackStock = 'trackObatRs' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabelTrackStock, null);

        $alergi = !empty($data['data_alergi']) ? $data['data_alergi'] : null;
        $iter = !empty($data['data_resep']['iter']) ? $data['data_resep']['iter'] : 0;
        $temp_cache = [];
        // Yii::$app->cache->delete('addObatRs' . $decId.'-'.$ruangan_id.'-'.$pegawai_id);
        // Yii::$app->cache->delete('urutObatRs' . $decId.'-'.$ruangan_id.'-'.$pegawai_id);
        // Yii::$app->cache->delete('delResepturDetailId' . $decId.'-'.$ruangan_id.'-'.$pegawai_id);
        $transApotek = $cacheLabel;
        $apotek = json_decode($transApotek, true);
        $delResepturDetailId = Yii::$app->cache->get('delResepturDetailId' . $ruangan_id . '-' . $pegawai_id);
        $get_dataResep = isset($data['data_resep']) ? $data['data_resep'] : [] ;
        $disabled = false;
        $display = 'block';
        $catatan = '';
        if (count($get_dataResep) > 0) {
            if ($get_dataResep['status_reseptur'] == 'Sudah Diproses') {
                $disabled = true;
                $display = 'none';
            }
            $catatan = $get_dataResep['catatan'];
        }
        // if ($transApotek === false) {
        // if ($transApotek === false || $transApotek == '[]') {
            $apotek = json_decode($transApotek, true);
            $list_cache = isset($data['detail_resep']) ? $data['detail_resep'] : [] ;
            $list = [];
            if(count($list_cache) > 0):
                foreach($list_cache as $key => $value):
                    $harga_jual_oa = $value['hargajual_satuan']*$value['qty_reseptur'];
                    // $additional_reseptur = $value['additional_reseptur'];
                    // $additional_reseptur = json_decode($additional_reseptur);
                    $list[$key] = [
                        'posisi'            => $key,
                        'pegawai_id'        => $pegawai_id,
                        'resepturdetail_id' => $value['resepturdetail_id'],
                        'obatalkes_id'      => $value['obatalkes_id'],
                        'obatalkes_nama'    => $value['obatalkes_nama'],
                        'signa'             => ($value['signa_nama'] == null) ? '-' : $value['signa_nama'],
                        'signa_id'          => $value['signa_id'],
                        'racikan_id'        => $value['racikan_id'],
                        'jenis_racikan'     => ($value['racikan_id'] == 1) ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                        'qty'               => $value['qty_reseptur'],
                        'harganetto'        => ($value['harga_netto'] != null) ? $value['harga_netto'] : 0,
                        'hargajual'         => $value['hargajual_satuan'],
                        'ppn'               => isset($value['ppn']) ? $value['ppn'] : 0,
                        'persendiscount'    => isset($value['persendiscount']) ? $value['persendiscount'] : 0,
                        'jmldiscount'       => isset($value['jmldiscount']) ? $value['jmldiscount'] : 0,
                        'persenppn'         => isset($value['persenppn']) ? $value['persenppn'] : 0,
                        'jmlppn'            => isset($value['jmlppn']) ? $value['jmlppn'] : 0,
                        'persenmargin'      => isset($value['persenmargin']) ? $value['persenmargin'] : 0,
                        'jmlmargin'         => isset($value['jmlmargin']) ? $value['jmlmargin'] : 0,
                        'satuankecil_id'    => $value['satuankecil_id'],
                        'harga'             => $value['hargajual_satuan'],
                        'is_racikan'        => !empty($value['rke']) ? $value['rke'] : false,
                        'r_ke'              => !empty($value['rke']) ? $value['rke'] : '-',
                        'subtotal'          => $harga_jual_oa,

                        'qty_konversi'      => isset($value['qty_konversi']) ? $value['qty_konversi'] : 0,
                        'satuaninput_id'    => isset($value['qty_konversisatuaninput_id']) ? $value['satuaninput_id'] : 0,
                        'satuan_input'      => isset($value['satuan_input']) ? $value['satuan_input'] : 0,
                        'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : 0,
                        'satuan_konversi'   => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : 0,
                        'harga_konversi'    => isset($value['harga_konversi']) ? $value['harga_konversi'] : 0,
                        'etiket'            => isset($value['etiket']) ? strip_tags($value['etiket']) : '-',
                        'catatan'            => isset($value['catatan']) ? strip_tags($value['catatan']) : '-',
                    ];
                endforeach;
                Yii::$app->cache->set('urutObatRs' . $ruangan_id . '-' . $pegawai_id, count($list_cache) - 1);
            endif;

            $list_obat = json_encode($list);
            Yii::$app->cache->set('addObatRs' . $ruangan_id . '-' . $pegawai_id, $list_obat);
            $urutObatRs =  Yii::$app->cache->get('urutObatRs' . $ruangan_id . '-' . $pegawai_id);
            $transApotek = $list_obat;
        $konfig_pembulatan = isset($data['konfig']['pembulatanharga']) ? $data['konfig']['pembulatanharga'] : 0;
        $isPembulatan = isset($data['konfigsys']['is_pembulatankeatas']) ? $data['konfigsys']['is_pembulatankeatas'] : false;
        $satuanPembulatan = isset($data['konfigsys']['satuanpembulatan']) ? $data['konfigsys']['satuanpembulatan'] : 0;
        return $this->render('rumah-sakit', get_defined_vars());
    }

    /**
     * @todo get all data ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer reseptur_id
     */
    private function getData($id = null)
    {
        $cache = Yii::$app->cache;
        $cacheDuration = 60 * 5;
        $return = [
                'data_resep'      => [],
                'detail_resep'    => [],
                'data_dokter'     => [],
                'data_pasien'     => [],
                'data_alergi'     => [],
                'data_karyawan'   => [],
                'data_stok'       => [],
                'data_cara_bayar' => [],
                'data_penjamin'   => [],
                'data_pegawai'    => [],
                'data_signa'      => [],
                'konfig'          => [],
                'konfigsys'       => [],
                'data_obat_ruangan' => []
            ];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;

            $return = $cache->getOrSet([
                'ajax-transaksi-resep',
                'instalasi_id' => $instalasi_id,
                'ruangan_id' => $ruangan_id,
                'reseptur_id' => $id
            ],function() use($instalasi_id,$ruangan_id,$id){

                $response = Yii::$app->docoRest->apotek->request('GET', 'allow/ajax', [
                    'query' => [
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id,
                        'reseptur_id' => $id
                    ]
                ]);
                $row = [];
                $body = json_decode($response->getBody(),TRUE);
                return [
                    'data_resep' => $body['response']['data-resep'],
                    'detail_resep' => $body['response']['detail-resep'],
                    'data_dokter' => $body['response']['data-dokter'],
                    'data_pasien' => $body['response']['data-pasien'],
                    'data_alergi' => $body['response']['data-alergi'],
                    'data_karyawan' => $body['response']['data-karyawan'],
                    'data_stok' => $body['response']['data-stok'],
                    'data_cara_bayar' => $body['response']['data-cara-bayar'],
                    'data_penjamin' => $body['response']['data-penjamin'],
                    'data_pegawai' => $body['response']['data-pegawai'],
                    'data_signa' => $body['response']['data-signa'],
                    'konfig' => $body['response']['konfig'],
                    'konfigsys' => $body['response']['konfigsys'],
                    'data_obat_ruangan' => @$body['response']['data-obat-ruangan']
                ];
            },$cacheDuration);
            return $return;
        } catch (RequestException $e) {
            Yii::trace($e->getMessage());
            return $return;
        } catch (\Exception $e) {
            Yii::trace($e->getMessage());
            return $return;
        }
    }

    private function getDataBundleResep()
    {
        $return = [
                'data_cara_bayar' => [],
                'data_signa' => [],
                'konfig' => [],
                'konfigsys' => []
            ];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $response = $this->_restApotek->request('GET', 'allow/get-bundle-resep-bebas', [
                'query' => [
                ]
            ]);
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_cara_bayar' => $body['response']['data-cara-bayar'],
                'data_signa' => $body['response']['data-signa'],
                'konfig' => $body['response']['konfig'],
                'konfigsys' => $body['response']['konfigsys']
            ];
            return $return;
        } catch (RequestException $e) {
            return $return;
        } catch (\Exception $e) {
            return $return;
        }
    }

    /**
     * @todo get all detail data resep from api
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetDetailResep()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $response = $this->_restApotek->request('GET', 'transaksi-resep/detail-resep', [
                            'query' => ['id' => $get['id']]
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $return = $body['response']['detail-resep'];

            return json_encode($return);
        } catch (RequestException $e) {
            return "{}";
        } catch (\Exception $e) {
            return "{}";
        }
    }

    /**
     * @todo get data list opsi resep
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionListResep()
    {
        try {
            $title = 'Nomor resep';
            return $this->renderPartial('list-resep',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /**
     * @todo get data list resep
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetListResep()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->request('POST', 'transaksi-resep/data-resep',[
                'form_params' => $post
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['noresep']);
                $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-resep',
                    'data-resep' => $value['noresep'],
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    /**
     * @todo save resep rs
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveRs($id)
    {
        try {
            $request = Yii::$app->request->get();
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

            $cacheLabel = 'addObatRs' . $ruangan_id . '-' . $pegawai_id;
            $urutLabel = 'urutObatRs' . $ruangan_id . '-' . $pegawai_id;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $list_obat = json_decode($cacheTrans, true);

            $listObat = json_decode($cacheTrans, true);
            if(count($listObat) < 1){
                return DocoHelpers::response(['response'=>['title'=>'Terjadi Kesalahan','message' => 'Obat Tidak Boleh Kosong!']],500);
            }

            $delResepturDetailId = Yii::$app->cache->get('delResepturDetailId' . $ruangan_id . '-' . $pegawai_id);
            $del_getResepturDetailId = [];
            if ($delResepturDetailId) {
                $listDelResepturDetailId = json_decode($delResepturDetailId, true);
                foreach ($listDelResepturDetailId as $key => $value) {
                    $value = (int) $value;
                    if($value){
                        $del_getResepturDetailId[$value] = $value;
                    }
                }
            }

            $post = [
                'reseptur_id' => $id,
                'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
                'list_obat' => $listObat,
                'del_ResepturDetailId' => $del_getResepturDetailId,
                'totalharga_netto' => Yii::$app->request->post('totalharga_netto', 0),
                'totalharga_jual' => Yii::$app->request->post('totalharga_jual', 0),
                'keterangan' => Yii::$app->request->post('keterangan', null),
                'biayaadministrasi' => Yii::$app->request->post('biayaadministrasi', 0)
            ];

            $response = $this->_restApotek->request('POST', 'transaksi-resep/save-resep-rs',[
                'form_params' => $post
            ]);

            $response = json_decode($response->getBody(),true);
            if ($response['metadata']['status'] == 200) {
                Yii::$app->cache->delete('reseptur' . $ruangan_id.'-'.$pegawai_id);
                Yii::$app->cache->delete('addObatRs' . $ruangan_id.'-'.$pegawai_id);
                Yii::$app->cache->delete('delResepturDetailId' . $ruangan_id.'-'.$pegawai_id);
            }
            return DocoHelpers::response($response,false,true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionSavePasien()
    {
        try {
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $post = $request->post();

            $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $urutLabel = 'urutObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $list_obat = json_decode($cacheTrans, true);

            if (count($list_obat) <= 0) {
                return DocoHelpers::response(['response'=>['title'=>'Terjadi Kesalahan','message' => 'Obat Tidak Boleh Kosong!']],500);
            }

            $info_pasien = $post['info_pasien'];

            $dataPost = [
                "tanggal_penjualan" => date('Y-m-d H:i:s', strtotime($info_pasien['tglpenjualan'])),
                "jenispenjualan" => DocoConstants::JUAL_RS,
                "pendaftaran_id" => $info_pasien['pendaftaran_id'],
                "pasien_id" => $info_pasien['pasien_id'],
                "pasienadmisi_id" => $info_pasien['pasienadmisi_id'],
                "kelaspelayanan_id" => $info_pasien['kelaspelayanan_id_hidden'],
                "pegawai_id" => !empty($info_pasien['dokter_id']) ? $info_pasien['dokter_id'] : '',
                "iter" => $info_pasien['iter'],
                "carabayar_id" => $info_pasien['carabayar_id'],
                "penjamin_id" => $info_pasien['penjamin_id'],
                "ruangan_id" => $ruangan_id,
                "totalharga_netto" => $post['totalharga_netto'],
                "total_obat" => $post['totalharga_jual'],
                "list_obat" => $list_obat,
                "etiket" => $info_pasien['catatan'],
                "catatan" => $info_pasien['catatan'],
                "biayaadministrasi" => $post['biayaadministrasi']
            ];

            $response = $this->_restApotek->request('POST', 'penjualan-resep/save', [
                'form_params' => $dataPost
            ]);

            $response = json_decode($response->getBody(), true);
            if ($response['metadata']['status'] == 200) {
                $response['status'] = true;
                $response['response']['encNomor'] = isset($response['response']['nomor']) ? DocoHelpers::encrypt($response['response']['nomor']) : DocoHelpers::encrypt(1);
                Yii::$app->cache->delete($cacheLabel);
                Yii::$app->cache->delete($urutLabel);
            } else {
                $response['status'] = false;
                $response['data_obat'] = $list_obat;
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }

    }

    public function actionBebas()
    {
        $title = $this->_title . ' Bebas';
        $model = new TransaksiResepForm;
        $data = $this->getDataBundleResep();
        $data_cara_bayar = isset($data['data_cara_bayar']) ? $data['data_cara_bayar'] : [];
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $checkBackdate = (DHtml::cekHakAkses('is-backdate')) ? '1' : '2';
        $temp_cache = [];
        $transApotek = Yii::$app->cache->get('addObatBebas' . $ruangan_id . '-' . $pegawai_id);
        if ($transApotek === false || $transApotek == '[]') {
            $list_cache = [];
            $list_obat = json_encode($list_cache);
            Yii::$app->cache->set('addObatBebas'.  $ruangan_id . '-' . $pegawai_id, $list_obat);
        }
        $transApotek = Yii::$app->cache->get('addObatBebas' . $ruangan_id . '-' . $pegawai_id);
        $konfig_pembulatan = isset($data['konfig']['pembulatanharga']) ? $data['konfig']['pembulatanharga'] : 0;
        $isPembulatan = isset($data['konfigsys']['is_pembulatankeatas']) ? $data['konfigsys']['is_pembulatankeatas'] : false;
        $satuanPembulatan = isset($data['konfigsys']['satuanpembulatan']) ? $data['konfigsys']['satuanpembulatan'] : 0;
        $carabayar = Yii::$app->cache->get('carabayar');
        if (!$carabayar) {
            $response = $this->_restMaster->get('cara-bayar/index?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), true);
            $carabayar_data = ArrayHelper::map($body['response']['data'], 'carabayar_id', 'carabayar_nama');
            \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
            $carabayar = $carabayar_data;
        }

        return $this->render('bebas', get_defined_vars());
    }

    public function actionKaryawan()
    {
        $title = $this->_title . ' Karyawan';
        $model = new TransaksiResepForm;
        $data = $this->getDataBundleResep();
        $data_cara_bayar = isset($data['data_cara_bayar']) ? $data['data_cara_bayar'] : [];
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $checkBackdate = (DHtml::cekHakAkses('is-backdate')) ? '1' : '2';
        $temp_cache = [];
        $transApotek = Yii::$app->cache->get('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
        if ($transApotek === false) {
            $list_cache = [];
            $list_obat = json_encode($list_cache);
            Yii::$app->cache->set('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id, $list_obat);
        }
        $transApotek = Yii::$app->cache->get('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
        $konfig_pembulatan = isset($data['konfig']['pembulatanharga']) ? $data['konfig']['pembulatanharga'] : 0;
        $isPembulatan = isset($data['konfigsys']['is_pembulatankeatas']) ? $data['konfigsys']['is_pembulatankeatas'] : false;
        $satuanPembulatan = isset($data['konfigsys']['satuanpembulatan']) ? $data['konfigsys']['satuanpembulatan'] : 0;
        $carabayar = Yii::$app->cache->get('carabayar');
        if (!$carabayar) {
            $response = $this->_restMaster->get('cara-bayar/index?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), true);
            $carabayar_data = ArrayHelper::map($body['response']['data'], 'carabayar_id', 'carabayar_nama');
            \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
            $carabayar = $carabayar_data;
        }

        return $this->render('karyawan', get_defined_vars());
    }


    /**
     * @todo generate list data dokter
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetDataDokter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('allow/list-dokter?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs select-dokter',
                    'data-pegawai' => $value['pegawai_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    /**
     * @todo modal list dokter
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSearchDokter()
    {
        try {
            $dokter = $this->_restApotek->get('allow/list-dokter');
            $dokter = json_decode($dokter->getBody(), true);
        } catch (\RequestException $e) {
            $dokter = [];
        } catch (\Exception $e) {
            $dokter = [];
        }

        return $this->renderPartial('list-dokter', get_defined_vars());
    }

    /**
     * @todo generate list data pasien
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetDataPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('allow/list-pasien?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs select-pasien',
                    'data-pasien' => $value['pasien_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo modal list pasien
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSearchPasien()
    {
        try {
            $pasien = $this->_restApotek->get('allow/list-pasien');
            $pasien = json_decode($pasien->getBody(), true);
        } catch (Exception $e) {
            $pasien = [];
        } catch (\RequestException $e) {
            $pasien = [];
        }

        return $this->renderPartial('list-pasien', get_defined_vars());
    }

    /**
     * @todo generate list data stok apotek
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetDataStokApotek()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $id = Yii::$app->docoVars->workspace('instalasi_id');
        try {
            $response = $this->_restApotek->get('allow/list-stok-apotek?instalasi_id='.$id .'&'. http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs select-obat',
                    'data-obat' => $value['obatalkes_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @todo modal list pasien
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSearchStok()
    {
        $id = Yii::$app->docoVars->workspace('instalasi_id');
        try {
            $stok = $this->_restApotek->get('allow/list-stok-apotek?instalasi_id='.$id);
            $stok = json_decode($stok->getBody(), true);
        } catch (\RequestException $e) {
            $stok = [];
        } catch (\Exception $e) {
            $stok = [];
        }

        return $this->renderPartial('list-stok-apotek', get_defined_vars());
    }

    public function actionGetDetailObat()
    {
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $request = Yii::$app->request;
        $penjamin_id = is_null($request->get("penjamin_id")) ? 1 : $request->get("penjamin_id");
        $kelaspelayanan_id = empty($request->get("kelaspelayanan_id")) ? 0 : $request->get("kelaspelayanan_id");
        $cache_label = is_null($request->get("cache_label")) ? 1 : $request->get("cache_label");

        $return = [];
        try {
            $response = $this->_restApotek->get('allow/get-detail-obat',[
                'query' => [
                    'penjamin_id'  => $penjamin_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'ruangan_id'   => $ruangan_id,
                    'obatalkes_id' => $request->get('obatalkes_id',null)
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $detail = $body['response']['data']['detail'];

            $cacheLabelTrackStock = $cache_label . $ruangan_id . '-' . $pegawai_id;
            $cacheTrackStok = Yii::$app->cache->get($cacheLabelTrackStock);
            $trackStock = json_decode($cacheTrackStok, true);

            $obatalkes_id = $detail['obatalkes_id'];
            $stok_terpakai = isset($trackStock[$obatalkes_id]) ? $trackStock[$obatalkes_id] : 0;
            $detail['qty_tersedia'] -= $stok_terpakai;

            $return = [
                'detail' => $detail
            ];
            return DocoHelpers::response($return);
        }catch (\Exception $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return DocoHelpers::response($return);
        }
    }

    /**
     * @todo get data ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetDataAjax()
    {
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $request = Yii::$app->request;
        $payload = $request->get();
        $penjamin_id = is_null($request->get("penjamin_id")) ? 1 : $request->get("penjamin_id");
        $kelaspelayanan_id = is_null($request->get("kelaspelayanan_id")) ? 1 : $request->get("kelaspelayanan_id");

        $return = [];
        try {
            $response = $this->_restApotek->get('allow/get-list-stok-apotek',[
                'query' => [
                    'instalasi_id' => $instalasi_id,
                    'penjamin_id'  => $penjamin_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'ruangan_id'   => $ruangan_id,
                    'keyword'      => isset($payload['q']) ? $payload['q'] : '',
                    'page'         => $payload['page'],
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $data_stok = $body['response']['data'];

            $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $cacheTrackStok = Yii::$app->cache->get($cacheLabelTrackStock);
            $trackStock = json_decode($cacheTrackStok, true);

            foreach ($data_stok as $index => $stok_obat) {
                $obatalkes_id = $stok_obat['obatalkes_id'];
                $stok_terpakai = isset($trackStock[$obatalkes_id]) ? $trackStock[$obatalkes_id] : 0;
                $data_stok[$index]['qty_tersedia'] -= $stok_terpakai;
            }

            $return = [
                'data_stok' => $data_stok,
                'payload' => $payload
            ];
            return DocoHelpers::response($return);
        }catch (\Exception $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return DocoHelpers::response($return);
        }
    }

    public function actionSaveMultipleCache($type = 4)
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $post = $request->post();
        $get = $request->get();

        try {
            $list_obat = $post['data'];

            $konfigFarmasi = $this->getKonfigFarmasi(true);
            $embalase_racikan = $konfigFarmasi['embalase_racikan'];
            $embalase_nonracikan = $konfigFarmasi['embalase_nonracikan'];

            if($type == 3){
                $cacheLabel = 'addObatRs' . $ruangan_id . '-' . $pegawai_id;
                $urutLabel = 'urutObatRs' . $ruangan_id . '-' . $pegawai_id;
                $cacheLabelTrackStock = 'trackObatRs' . $ruangan_id . '-' . $pegawai_id;
            } else if($type == 5) {
                $cacheLabel = 'addObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
                $urutLabel = 'urutObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
                $cacheLabelTrackStock = 'trackObatEditReseptur' . $ruangan_id . '-' . $pegawai_id;
            }else {
                $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
                $urutLabel = 'urutObatPasien' . $ruangan_id . '-' . $pegawai_id;
                $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;
            }

            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $transApotek = json_decode($cacheTrans, true);

            $no_urut = Yii::$app->cache->get($urutLabel);

            if ($no_urut === false || $no_urut < 1) {
                $start_urut = 0;
                Yii::$app->cache->set($urutLabel, $start_urut);
            }

            if (!isset($post['posisi'])) {
                $posisi = $no_urut + 1;
            }

            foreach ($list_obat as $obat_racik) {
                if($obat_racik['hargajual'] == 0) {
                    $embalase = 0;
                    $harga_dgn_embalase = 0;
                } else {
                    $harga_jual = $obat_racik['hargajual'] * $obat_racik['nilai_konversi'];
                    $harga_jual_oa = $harga_jual * $obat_racik['qty'];
                    $harga_netto_oa = ($obat_racik['harganetto'] * $obat_racik['nilai_konversi']) * $obat_racik['qty'];

                    $embalase = 0;
                    $harga_dgn_embalase = $harga_jual + ($embalase / $obat_racik['qty']);
                }

                $return = [
                    'posisi' => $posisi,
                    'embalase' => $embalase,
                    'pegawai_id' => $pegawai_id,
                    'obatalkes_id' => $obat_racik['obatalkes_id'],
                    'obatalkes_nama' => $obat_racik['obat_nama'],
                    'signa' => ( $obat_racik['signa_nama'] == '— Pilih —' || $obat_racik['signa_nama'] == '') ? '-' :  $obat_racik['signa_nama'],
                    'signa_id' => $obat_racik['signa'],
                    'racikan_id' => 1 ,
                    'jenis_racikan' => Yii::t('fe', 'Racikan'),
                    'qty' => $obat_racik['qty'],
                    'ppn' => $obat_racik['ppn'],
                    'kronis' => isset($obat_racik['kronis']) ? $obat_racik['kronis'] : null,
                    'harganetto' => $obat_racik['harganetto'],
                    'hargajual' => $obat_racik['hargajual'],
                    'persendiscount' => $obat_racik['persendiscount'],
                    'jmldiscount' => $obat_racik['jmldiscount'],
                    'persenppn' => $obat_racik['persenppn'],
                    'jmlppn' => $obat_racik['jmlppn'],
                    'persenmargin' => $obat_racik['persenmargin'],
                    'jmlmargin' => $obat_racik['jmlmargin'],
                    'satuankecil_id' => $obat_racik['satuankecil_id'],
                    'harga' => $harga_dgn_embalase,
                    'is_racikan' => true,
                    'r_ke' => $obat_racik['r_ke'],
                    'subtotal' => $harga_dgn_embalase * ceil($obat_racik['qty']),
                    'catatan' => $obat_racik['catatan'],
                    'qty_konversi' => $obat_racik['qty_konversi'],
                    'satuaninput_id' => $obat_racik['satuaninput_id'],
                    'satuan_input' => $obat_racik['satuan_input'],
                    'satuankonversi_id' => $obat_racik['satuankonversi_id'],
                    'satuan_konversi' => $obat_racik['satuan_konversi'],
                    'harga_konversi' => $obat_racik['harga_kecil'],
                    'nilai_konversi' => $obat_racik['nilai_konversi'],
                    'etiket' => $obat_racik['catatan'],
                    'nama_racikan' => $obat_racik['nama_racikan'],
                    'satuan_racikan_id' => $obat_racik['satuan_racikan_id'],
                    'satuan_racikan_obat_nama' => $obat_racik['satuan_racikan_obat_nama'],
                    'qty_racikan' => $obat_racik['qty_racikan']
                ];
                $transApotek[$posisi] = $return;
                $posisi++;

                $this->trackStokResep(
                    /*cache_label*/     $cacheLabelTrackStock,
                    /*obatalkes_id*/    $return['obatalkes_id'],
                    /*qty*/             $return['qty_konversi']
                );
            }

            Yii::$app->cache->delete($urutLabel);
            Yii::$app->cache->set($urutLabel, $posisi);

            $transApotek = $this->recalculatingCache($transApotek);

            $listTrans = json_encode($transApotek);
            Yii::$app->cache->set($cacheLabel, $listTrans);

            $result['data'] = $transApotek;
            $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     * @todo save obat to cache
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveCache()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $post = $request->post();
        $get = $request->get();

        $transaksiObatMinus = false;
        $konfigFarmasi = $this->getKonfigFarmasi(true);

        if(isset($konfigFarmasi['is_transaksiobat_0']) && $konfigFarmasi['is_transaksiobat_0'] == true) {
            $transaksiObatMinus = true;
        }

        try {
            $response = $this->_restApotek->request('GET', 'allow/check-stok-new?ruangan_id=' . $ruangan_id . '&obatalkes_id=' . $post['obatalkes_id']);
            $body = json_decode($response->getBody(), true);
            if (isset($body['response']['qty_stok'])) {
                $qty_available = $body['response']['qty_stok'];
                if ($post['qty'] > $qty_available && !$transaksiObatMinus) {
                    return DocoHelpers::response([
                      'message' => 'Stok tidak mencukupi'
                    ],500);
                }
            } else {
                return DocoHelpers::response(['message' => 'Stok tidak ditemukan'], 500);
            }
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $posisi = 0;
            $is_racikan = isset($post['is_racikan']) ? true : false;
            $r_ke = isset($post['r_ke']) ? $post['r_ke'] : null;
            $racikanId = isset($post['racikan_id']) ? $post['racikan_id'] : null;

            $transApotek = [];
            $countTransApotek = count(array($transApotek));
            if (isset($post['type'])) {
                $harga_jual = $post['hargajual'] * $post['nilai_konversi'];
                $harga_jual_oa = $harga_jual * ceil($post['qty']);
                $harga_netto_oa = ($post['harganetto']*$post['nilai_konversi']) * ceil($post['qty']);

                if ($post['type'] == 1) {
                    $cacheTrans = Yii::$app->cache->get('addObatBebas' . $ruangan_id . '-' . $pegawai_id);
                    $transApotek = json_decode($cacheTrans, true);
                    $no_urut = Yii::$app->cache->get('urutObat' . $ruangan_id . '-' . $pegawai_id);
                    if ($no_urut === false or $no_urut < 1) {
                        $start_urut = 0;
                        Yii::$app->cache->set('urutObat' . $ruangan_id . '-' . $pegawai_id, $start_urut);
                    }
                    $no_urut = Yii::$app->cache->get('urutObat' . $ruangan_id . '-' . $pegawai_id);
                    if (!isset($post['posisi'])) {
                        $posisi = $no_urut + 1;
                    }
                    $res_transApotek = [];
                    $idObat_same = false;
                    $position = 0;
                    if ($countTransApotek > 0 && !empty($transApotek) && !empty($transApotek)) {

                       foreach ($transApotek as $key => $value) {
                            $type_racikan = ($racikanId == "on") ? 1 : 2 ;
                            $dataValidate = $value['obatalkes_id'] .'-'.$value['racikan_id'] .'-'.$value['r_ke'];
                            $checkValidate = $post['obatalkes_id'].'-'.$type_racikan.'-'.$r_ke;

                            if ( $dataValidate == $checkValidate ) {
                                return DocoHelpers::response([
                                  'message' => 'Obat sudah diinputkan!'
                                ],500);

                                $value['pegawai_id'] = $pegawai_id;
                                $value['obatalkes_id'] = $post['obatalkes_id'];
                                $value['obatalkes_nama'] = $post['obat_nama'];
                                $value['signa'] = ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'];
                                $value['signa_id'] = $post['signa'];
                                $value['racikan_id'] = ($racikanId == "on") ? 1 : 2 ;
                                $value['jenis_racikan'] = ($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan');
                                $value['qty'] = $post['qty'];
                                $value['ppn'] = $post['ppn'];
                                $value['harganetto'] = $post['harganetto'];
                                $value['hargajual'] = $post['hargajual'];
                                $value['persendiscount'] = $post['persendiscount'];
                                $value['jmldiscount'] = $post['jmldiscount'];
                                $value['persenppn'] = $post['persenppn'];
                                $value['jmlppn'] = $post['jmlppn'];
                                $value['persenmargin'] = $post['persenmargin'];
                                $value['jmlmargin'] = $post['jmlmargin'];
                                $value['satuankecil_id'] = $post['satuankecil_id'];
                                $value['harga'] = $harga_jual;
                                $value['is_racikan'] = $is_racikan;
                                $value['r_ke'] = $r_ke;
                                $value['subtotal'] = $harga_jual_oa;
                                $value['catatan'] = $post['catatan'];
                                $value['qty_konversi'] = $post['qty_konversi'];
                                $value['satuaninput_id'] = $post['satuaninput_id'];
                                $value['satuan_input'] = $post['satuan_input'];
                                $value['satuankonversi_id'] = $post['satuankonversi_id'];
                                $value['satuan_konversi'] = $post['satuan_konversi'];
                                $value['harga_konversi'] = $post['harga_kecil'];
                                $value['etiket'] = $post['etiket'];

                                $position = $value['posisi'];
                                $res_transApotek[$position] = $value;
                                $transApotek[$key] = $value;

                                $idObat_same = true;

                            }
                        }
                    }
                    if ($idObat_same == true ) {
                        $transApotek = $transApotek;
                        $listTrans = json_encode($transApotek);
                        $posisi = $posisi;
                        $transApotek[$posisi] = $res_transApotek[$position];
                    }else{
                        $return = [
                            'posisi' => $posisi,
                            'pegawai_id' => $pegawai_id,
                            'obatalkes_id' => $post['obatalkes_id'],
                            'obatalkes_nama' => $post['obat_nama'],
                            'signa' => ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'],
                            'signa_id' => $post['signa'],
                            'racikan_id' => ($racikanId == "on") ? 1 : 2 ,
                            'jenis_racikan' => ($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                            'qty' => $post['qty'],
                            'ppn' => $post['ppn'],
                            'harganetto' => $post['harganetto'],
                            'hargajual' => $post['hargajual'],
                            'persendiscount' => $post['persendiscount'],
                            'jmldiscount' => $post['jmldiscount'],
                            'persenppn' => $post['persenppn'],
                            'jmlppn' => $post['jmlppn'],
                            'persenmargin' => $post['persenmargin'],
                            'jmlmargin' => $post['jmlmargin'],
                            'satuankecil_id' => $post['satuankecil_id'],
                            'harga' => $harga_jual,
                            'is_racikan' => $is_racikan,
                            'r_ke' => $r_ke,
                            'subtotal' => $harga_jual_oa,

                            'qty_konversi' => $post['qty_konversi'],
                            'satuaninput_id' => $post['satuaninput_id'],
                            'satuan_input' => $post['satuan_input'],
                            'satuankonversi_id' => $post['satuankonversi_id'],
                            'satuan_konversi' => $post['satuan_konversi'],
                            'harga_konversi' => $post['harga_kecil'],
                            'etiket' => $post['etiket']

                        ];

                        Yii::$app->cache->delete('urutObat' . $ruangan_id . '-' . $pegawai_id);
                        Yii::$app->cache->set('urutObat' . $ruangan_id . '-' . $pegawai_id, $posisi);

                        $transApotek[$posisi] = $return;
                        $listTrans = json_encode($transApotek);
                    }

                    Yii::$app->cache->set('addObatBebas' . $ruangan_id . '-' . $pegawai_id, $listTrans);
                } elseif ($post['type'] == 2) {
                    $cacheTrans = Yii::$app->cache->get('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
                    $transApotek = json_decode($cacheTrans, true);
                    $no_urut = Yii::$app->cache->get('urutObatKaryawan' . $pegawai_id);
                    if ($no_urut === false or $no_urut < 1) {
                        $start_urut = 0;
                        Yii::$app->cache->set('urutObatKaryawan' . $pegawai_id, $start_urut);
                    }
                    $no_urut = Yii::$app->cache->get('urutObatKaryawan' . $pegawai_id);
                    if (!isset($post['posisi'])) {
                        $posisi = $no_urut + 1;
                    }
                    /*start*/
                    $res_transApotek = [];
                    $idObat_same = false;
                    $position = 0;
                    if ($countTransApotek > 0 && !empty($transApotek) && !empty($transApotek)) {
                        foreach ($transApotek as $key => $value) {
                            $type_racikan = ($racikanId == "on") ? 1 : 2 ;
                            $dataValidate = $value['obatalkes_id'] .'-'.$value['racikan_id'] .'-'.$value['r_ke'];
                            $checkValidate = $post['obatalkes_id'].'-'.$type_racikan.'-'.$r_ke;

                            if ( $dataValidate == $checkValidate ) {
                                return DocoHelpers::response([
                                  'message' => 'Obat sudah diinputkan!'
                                ],500);

                                $value['pegawai_id'] = $pegawai_id;
                                $value['obatalkes_id'] = $post['obatalkes_id'];
                                $value['obatalkes_nama'] = $post['obat_nama'];
                                $value['signa'] = ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'];
                                $value['signa_id'] = $post['signa'];
                                $value['racikan_id'] = ($racikanId == "on") ? 1 : 2 ;
                                $value['jenis_racikan'] = ($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan');
                                $value['qty'] = $post['qty'];
                                $value['ppn'] = $post['ppn'];
                                $value['harganetto'] = $post['harganetto'];
                                $value['hargajual'] = $post['hargajual'];
                                $value['persendiscount'] = $post['persendiscount'];
                                $value['jmldiscount'] = $post['jmldiscount'];
                                $value['persenppn'] = $post['persenppn'];
                                $value['jmlppn'] = $post['jmlppn'];
                                $value['persenmargin'] = $post['persenmargin'];
                                $value['jmlmargin'] = $post['jmlmargin'];
                                $value['satuankecil_id'] = $post['satuankecil_id'];
                                $value['harga'] = $harga_jual;
                                $value['is_racikan'] = $is_racikan;
                                $value['r_ke'] = $r_ke;
                                $value['subtotal'] = $harga_jual_oa;

                                $value['qty_konversi'] = $post['qty_konversi'];
                                $value['satuaninput_id'] = $post['satuaninput_id'];
                                $value['satuan_input'] = $post['satuan_input'];
                                $value['satuankonversi_id'] = $post['satuankonversi_id'];
                                $value['satuan_konversi'] = $post['satuan_konversi'];
                                $value['harga_konversi'] = $post['harga_kecil'];
                                $value['etiket'] = $post['etiket'];

                                $position = $value['posisi'];
                                $res_transApotek[$position] = $value;
                                $transApotek[$key] = $value;

                                $idObat_same = true;
                            }
                        }
                    }
                    if ($idObat_same == true ) {
                        $transApotek = $transApotek;
                        $listTrans = json_encode($transApotek);
                        $posisi = $posisi;
                        $transApotek[$posisi] = $res_transApotek[$position];
                    }else{
                        $return = [
                            'posisi' => $posisi,
                            'pegawai_id' => $pegawai_id,
                            'obatalkes_id' => $post['obatalkes_id'],
                            'obatalkes_nama' => $post['obat_nama'],
                            'signa' => ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'],
                            'signa_id' => $post['signa'],
                            'racikan_id' => ($racikanId == "on") ? 1 : 2 ,
                            'jenis_racikan' =>($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                            'qty' => $post['qty'],
                            'ppn' => $post['ppn'],
                            'harganetto' => $post['harganetto'],
                            'hargajual' => $post['hargajual'],
                            'persendiscount' => $post['persendiscount'],
                            'jmldiscount' => $post['jmldiscount'],
                            'persenppn' => $post['persenppn'],
                            'jmlppn' => $post['jmlppn'],
                            'persenmargin' => $post['persenmargin'],
                            'jmlmargin' => $post['jmlmargin'],
                            'satuankecil_id' => $post['satuankecil_id'],
                            'harga' => $harga_jual,
                            'is_racikan' => $is_racikan,
                            'r_ke' => $r_ke,
                            'subtotal' => $harga_jual_oa,

                            'qty_konversi' => $post['qty_konversi'],
                            'satuaninput_id' => $post['satuaninput_id'],
                            'satuan_input' => $post['satuan_input'],
                            'satuankonversi_id' => $post['satuankonversi_id'],
                            'satuan_konversi' => $post['satuan_konversi'],
                            'harga_konversi' => $post['harga_kecil'],
                            'etiket' => $post['etiket']
                        ];

                        Yii::$app->cache->delete('urutObatKaryawan' . $pegawai_id);
                        Yii::$app->cache->set('urutObatKaryawan' . $pegawai_id, $posisi);

                        $transApotek[$posisi] = $return;
                        $listTrans = json_encode($transApotek);
                    }
                    /*end*/
                    Yii::$app->cache->set('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id, $listTrans);
                }else if($post['type'] == 3){
                    $cacheLabel = 'addObatRs' . $ruangan_id . '-' . $pegawai_id;
                    $urutLabel = 'urutObatRs' . $ruangan_id . '-' . $pegawai_id;
                    $cacheLabelTrackStock = 'trackObatRs' . $ruangan_id . '-' . $pegawai_id;

                    $konfigFarmasi = $this->getKonfigFarmasi(true);
                    $embalase_racikan = $konfigFarmasi['embalase_racikan'];
                    $embalase_nonracikan = $konfigFarmasi['embalase_nonracikan'];

                    $cacheTrans = Yii::$app->cache->get($cacheLabel);
                    $transApotek = json_decode($cacheTrans, true);

                    $no_urut = Yii::$app->cache->get($urutLabel);

                    if ($no_urut === false or $no_urut < 1) {
                        $start_urut = 0;
                        Yii::$app->cache->set($urutLabel, $start_urut);
                    }
                    $no_urut = Yii::$app->cache->get($urutLabel);
                    if (!isset($post['posisiNo'])) {
                        $posisi = $no_urut + 1;
                    } else {
                        $posisi = (int) $post['posisiNo'];
                    }
                    $res_transApotek = [];
                    $idObat_same = false;
                    $position = 0;
                    $count_r_ke = [];
                    $totalqty_r_ke = [];

                    if ($countTransApotek > 0 && !empty($transApotek) && !empty($transApotek)) {

                        foreach ($transApotek as $key => $value) {
                            $type_racikan = ($racikanId == "on") ? 1 : 2 ;
                            $dataValidate = $value['obatalkes_id'] .'-'.$value['racikan_id'] .'-'.$value['r_ke'];
                            $checkValidate = $post['obatalkes_id'].'-'.$type_racikan.'-'.$r_ke;

                            if ( $dataValidate == $checkValidate ) {
                                return DocoHelpers::response([
                                  'message' => 'Obat sudah diinputkan!'
                                ],500);

                            }

                            if (isset($value['r_ke']) && $value['r_ke'] == $r_ke) {
                                if (!isset($count_r_ke[$value['r_ke']])) {
                                    $count_r_ke[$value['r_ke']] = 1;
                                    $totalqty_r_ke[$value['r_ke']] = $value['qty'];
                                } else {
                                    $count_r_ke[$value['r_ke']] = $count_r_ke[$value['r_ke']] + 1;
                                    $totalqty_r_ke[$value['r_ke']] += $value['qty'];
                                }
                            }
                        }
                    }

                    if ($r_ke != null && isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0) {
                        foreach ($transApotek as $noUrut => $detail_obat) {
                            if ($detail_obat['r_ke'] == $r_ke) {
                                $embalase = 0;
                                if ($racikanId == "on") {
                                    $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ?
                                        $count_r_ke[$r_ke] : 0;
                                    $embalase = $embalase_racikan / ($jumlah_obat + 1);
                                } else {
                                    $embalase = $embalase_nonracikan;
                                }

                                $hrgaJual = $detail_obat['hargajual'] * $detail_obat['qty_konversi'];
                                $transApotek[$noUrut]['harga'] = $hrgaJual + ($embalase / $detail_obat['qty']);
                                $transApotek[$noUrut]['subtotal'] = ( $hrgaJual + ($embalase / $detail_obat['qty'])) * $detail_obat['qty'];
                            }
                        }
                    }

                    $embalase = 0;
                    if ($racikanId == "on") {
                        $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ?
                             $count_r_ke[$r_ke] : 0;
                        $embalase = $embalase_racikan / ($jumlah_obat + 1);
                    } else {
                        $embalase = $embalase_nonracikan;
                    }

                    if($harga_jual == 0) {
                        $harga_dgn_embalase = 0;
                        $embalase = 0;
                    } else {
                        $harga_dgn_embalase = $harga_jual + ($embalase / $post['qty']);
                        $harga_dgn_embalase = ceil($harga_dgn_embalase);
                    }

                    $return = [
                        'posisi' => $posisi,
                        'pegawai_id' => $pegawai_id,
                        'obatalkes_id' => $post['obatalkes_id'],
                        'obatalkes_nama' => $post['obat_nama'],
                        'signa' => ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'],
                        'signa_id' => $post['signa'],
                        'racikan_id' => ($racikanId == "on") ? 1 : 2 ,
                        'jenis_racikan' => ($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                        'qty' => $post['qty'],
                        'ppn' => $post['ppn'],
                        'harganetto' => $post['harganetto'],
                        'hargajual' => $post['hargajual'],
                        'persendiscount' => $post['persendiscount'],
                        'jmldiscount' => $post['jmldiscount'],
                        'persenppn' => $post['persenppn'],
                        'jmlppn' => $post['jmlppn'],
                        'persenmargin' => $post['persenmargin'],
                        'jmlmargin' => $post['jmlmargin'],
                        'satuankecil_id' => $post['satuankecil_id'],
                        'harga' => $harga_dgn_embalase,
                        'embalase' => $embalase,
                        'is_racikan' => $is_racikan,
                        'r_ke' => $r_ke,
                        'nilai_konversi' => $post['nilai_konversi'],
                        'subtotal' => $harga_dgn_embalase * ceil($post['qty']),
                        'catatan' => strip_tags($post['catatan']),
                        'qty_konversi' => $post['qty_konversi'],
                        'satuaninput_id' => $post['satuaninput_id'],
                        'satuan_input' => $post['satuan_input'],
                        'satuankonversi_id' => $post['satuankonversi_id'],
                        'satuan_konversi' => $post['satuan_konversi'],
                        'harga_konversi' => $post['harga_kecil'],
                        'etiket' => strip_tags($post['catatan'])
                    ];
                    $transApotek[$posisi] = $return;

                    $this->trackStokResep(
                        /*cache_label*/     $cacheLabelTrackStock,
                        /*obatalkes_id*/    $return['obatalkes_id'],
                        /*qty*/             $return['qty_konversi']
                    );

                    Yii::$app->cache->delete($urutLabel);
                    Yii::$app->cache->set($urutLabel, $posisi);
                    $listTrans = json_encode($transApotek);

                    Yii::$app->cache->set($cacheLabel, $listTrans);
                }else if($post['type'] == 4) {
                    $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
                    $urutLabel = 'urutObatPasien' . $ruangan_id . '-' . $pegawai_id;
                    $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;

                    $konfigFarmasi = $this->getKonfigFarmasi(true);
                    $embalase_racikan = $konfigFarmasi['embalase_racikan'];
                    $embalase_nonracikan = $konfigFarmasi['embalase_nonracikan'];

                    $cacheTrans = Yii::$app->cache->get($cacheLabel);
                    $transApotek = json_decode($cacheTrans, true);

                    $no_urut = Yii::$app->cache->get($urutLabel);

                    if ($no_urut === false or $no_urut < 1) {
                        $start_urut = 0;
                        Yii::$app->cache->set($urutLabel, $start_urut);
                    }
                    $no_urut = Yii::$app->cache->get($urutLabel);
                    if (!isset($post['posisi'])) {
                        $posisi = $no_urut + 1;
                    }
                    $res_transApotek = [];
                    $idObat_same = false;
                    $position = 0;
                    $count_r_ke = [];
                    if (count($transApotek) > 0) {

                        foreach ($transApotek as $key => $value) {
                            $type_racikan = ($racikanId == "on") ? 1 : 2 ;
                            $dataValidate = $value['obatalkes_id'] .'-'.$value['racikan_id'] .'-'.$value['r_ke'];
                            $checkValidate = $post['obatalkes_id'].'-'.$type_racikan.'-'.$r_ke;

                            if ( $dataValidate == $checkValidate ) {
                                return DocoHelpers::response([
                                  'message' => 'Obat sudah diinputkan!'
                                ],500);

                            }

                            if (isset($value['r_ke']) && $value['r_ke'] == $r_ke) {
                                if (!isset($count_r_ke[$value['r_ke']])) {
                                    $count_r_ke[$value['r_ke']] = 1;
                                } else {
                                    $count_r_ke[$value['r_ke']] = $count_r_ke[$value['r_ke']] + 1;
                                }
                            }
                        }
                    }

                    if ($r_ke != null && isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0) {
                        foreach ($transApotek as $noUrut => $detail_obat) {
                            if ($detail_obat['r_ke'] == $r_ke) {
                                $embalase = 0;
                                if ($racikanId == "on") {
                                    $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ?
                                        $count_r_ke[$r_ke] : 0;
                                    $embalase = $embalase_racikan / ($jumlah_obat + 1);
                                } else {
                                    $embalase = $embalase_nonracikan;
                                }

                                $hrgaJual = $detail_obat['hargajual'] * $detail_obat['qty_konversi'];
                                $transApotek[$noUrut]['harga'] = $hrgaJual + ($embalase / $detail_obat['qty']);
                                $transApotek[$noUrut]['subtotal'] = ( $hrgaJual + ($embalase / $detail_obat['qty'])) * $detail_obat['qty'];
                            }
                        }
                    }

                    $embalase = 0;
                    if ($racikanId == "on") {
                        $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ?
                             $count_r_ke[$r_ke] : 0;
                        $embalase = $embalase_racikan / ($jumlah_obat + 1);
                    } else {
                        $embalase = $embalase_nonracikan;
                    }

                    if($harga_jual == 0) {
                        $harga_dgn_embalase = 0;
                        $embalase = 0;
                    } else {
                        $harga_dgn_embalase = $harga_jual + ($embalase / $post['qty']);
                        $harga_dgn_embalase = ceil($harga_dgn_embalase);
                    }

                    $return = [
                        'posisi' => $posisi,
                        'pegawai_id' => $pegawai_id,
                        'obatalkes_id' => $post['obatalkes_id'],
                        'obatalkes_nama' => $post['obat_nama'],
                        'signa' => ( $post['signa_nama'] == '— Pilih —' || $post['signa_nama'] == '') ? '-' :  $post['signa_nama'],
                        'signa_id' => $post['signa'],
                        'racikan_id' => ($racikanId == "on") ? 1 : 2 ,
                        'jenis_racikan' => ($racikanId == "on") ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan'),
                        'qty' => $post['qty'],
                        'ppn' => $post['ppn'],
                        'harganetto' => $post['harganetto'],
                        'hargajual' => $post['hargajual'],
                        'persendiscount' => $post['persendiscount'],
                        'jmldiscount' => $post['jmldiscount'],
                        'persenppn' => $post['persenppn'],
                        'jmlppn' => $post['jmlppn'],
                        'persenmargin' => $post['persenmargin'],
                        'jmlmargin' => $post['jmlmargin'],
                        'satuankecil_id' => $post['satuankecil_id'],
                        'harga' => $harga_dgn_embalase,
                        'embalase' => $embalase,
                        'is_racikan' => $is_racikan,
                        'r_ke' => $r_ke,
                        'nilai_konversi' => $post['nilai_konversi'],
                        'subtotal' => $harga_dgn_embalase * ceil($post['qty']),
                        'catatan' => strip_tags($post['catatan']),
                        'qty_konversi' => $post['qty_konversi'],
                        'satuaninput_id' => $post['satuaninput_id'],
                        'satuan_input' => $post['satuan_input'],
                        'satuankonversi_id' => $post['satuankonversi_id'],
                        'satuan_konversi' => $post['satuan_konversi'],
                        'harga_konversi' => $post['harga_kecil'],
                        'etiket' => strip_tags($post['catatan'])
                    ];
                    $transApotek[$posisi] = $return;

                    $this->trackStokResep(
                        /*cache_label*/     $cacheLabelTrackStock,
                        /*obatalkes_id*/    $return['obatalkes_id'],
                        /*qty*/             $return['qty_konversi']
                    );

                    Yii::$app->cache->delete($urutLabel);
                    Yii::$app->cache->set($urutLabel, $posisi);
                    $listTrans = json_encode($transApotek);

                    Yii::$app->cache->set($cacheLabel, $listTrans);
                } else {
                    throw new \Exception("Type cannot be null", 1);

                }
            }
            $result['data'] = $transApotek;
            $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            // echo $e->getMessage();
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDeleteCache($id, $type)
    {
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $transApotek = $listTrans = json_encode([]);
        try {
            $result = $cache = false;
            if ($type == 1) {
                $cacheTrans = Yii::$app->cache->get('addObatBebas' . $ruangan_id . '-' . $pegawai_id);
                if ($cacheTrans !== false) {
                    $transApotek = json_decode($cacheTrans, true);
                    if (isset($transApotek[$id])) {
                        unset($transApotek[$id]);
                        if (!count($transApotek)) {
                            Yii::$app->cache->delete('addObatBebas' . $ruangan_id . '-' . $pegawai_id);
                            Yii::$app->cache->delete('urutObat' . $ruangan_id . '-' . $pegawai_id);
                            $cache = true;
                        }
                        $listTrans = json_encode($transApotek);
                        $result = true;
                    }
                }

                $listTrans = json_encode($transApotek);
                Yii::$app->cache->set('addObatBebas' . $ruangan_id . '-' . $pegawai_id, $listTrans);
            } elseif ($type == 2) {
                $cacheTrans = Yii::$app->cache->get('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
                if ($cacheTrans !== false) {
                    $transApotek = json_decode($cacheTrans, true);
                    if (isset($transApotek[$id])) {
                        unset($transApotek[$id]);
                        if (!count($transApotek)) {
                            Yii::$app->cache->delete('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
                            Yii::$app->cache->delete('urutObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
                            $cache = true;
                        }
                        $listTrans = json_encode($transApotek);
                        $result = true;
                    }
                }

                $listTrans = json_encode($transApotek);
                Yii::$app->cache->set('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id, $listTrans);
            } elseif ($type == 3) {
                $cacheLabel = 'addObatRs' . $ruangan_id . '-' . $pegawai_id;
                $urutObatPasien = 'urutObat' . $ruangan_id . '-' . $pegawai_id;
                $cacheLabelTrackStock = 'trackObatRs' . $ruangan_id . '-' . $pegawai_id;

                $cacheTrans = Yii::$app->cache->get($cacheLabel);
                if ($cacheTrans !== false) {
                    $resepturdetail_id = $_GET['resepturdetail_id'];
                    $transApotek = json_decode($cacheTrans, true);
                    if (isset($transApotek[$id])) {
                        $obat = $transApotek[$id];
                        $this->trackStokResep(
                            /*cache_label*/   $cacheLabelTrackStock,
                            /*obat_alkes_id*/ $obat['obatalkes_id'],
                            /*qty*/           abs($obat['qty_konversi']) * -1
                        );

                        unset($transApotek[$id]);
                        if (!count($transApotek)) {
                            Yii::$app->cache->delete($cacheLabel);
                            Yii::$app->cache->delete($urutObatPasien);
                            $cache = true;
                        }
                        $listTrans = json_encode($transApotek);

                        $listResepturDetailId = Yii::$app->cache->get('delResepturDetailId' . $ruangan_id . '-' . $pegawai_id);
                        $listResepturDetailId = json_decode($listResepturDetailId);
                        $listResepturDetailId = ($listResepturDetailId != NULL ) ? $listResepturDetailId : [];
                        array_push($listResepturDetailId, $resepturdetail_id);
                        $listResDetailID = json_encode($listResepturDetailId);
                        Yii::$app->cache->set('delResepturDetailId' . $ruangan_id . '-' . $pegawai_id, $listResDetailID);

                        $result = true;
                    }
                }

                // Update Harga Obat
                $transApotek = $this->recalculatingCache($transApotek);

                $listTrans = json_encode($transApotek);
                Yii::$app->cache->set($cacheLabel, $listTrans);
            } elseif ($type == 4) {
                $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
                $urutObatPasien = 'urutObat' . $ruangan_id . '-' . $pegawai_id;
                $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;

                $cacheTrans = Yii::$app->cache->get($cacheLabel);
                if ($cacheTrans !== false) {
                    $transApotek = json_decode($cacheTrans, true);
                    if (isset($transApotek[$id])) {

                        $obat = $transApotek[$id];
                        $this->trackStokResep(
                            /*cache_label*/   $cacheLabelTrackStock,
                            /*obat_alkes_id*/ $obat['obatalkes_id'],
                            /*qty*/           abs($obat['qty_konversi']) * -1
                        );

                        unset($transApotek[$id]);
                        if (!count($transApotek)) {
                            Yii::$app->cache->delete($cacheLabel);
                            Yii::$app->cache->delete($urutObatPasien);
                            $cache = true;
                        }
                        $listTrans = json_encode($transApotek);
                        $result = true;
                    }
                }

                // Update Harga Obat
                $transApotek = $this->recalculatingCache($transApotek);

                $listTrans = json_encode($transApotek);
                Yii::$app->cache->set($cacheLabel, $listTrans);
            }

            $return['detail'] = $transApotek;
            $return['message'] = 'Success';

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            // echo json_encode($e->getMessage()); die;
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionUpdateCache($id, $type)
    {
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restApotek->request('GET', 'allow/check-stok?ruangan_id=' . $ruangan_id . '&obatalkes_id=' . $post['obatalkes_id']);
            $body = json_decode($response->getBody(), true);
            if (isset($body['response']['qty_tersedia'])) {
                $qty_available = $body['response']['qty_tersedia'];
                if ($post['qty'] > $qty_available) {
                    header('Content-type: application/json');
                    http_response_code(500);
                    $result['message'] = Yii::t('fe', 'Stok tidak mencukupi');
                    echo json_encode($result);
                    exit;
                }
            } else {
                header('Content-type: application/json');
                http_response_code(500);
                $result['message'] = Yii::t('fe', 'Stok tidak ditemukan');
                echo json_encode($result);
                exit;
            }
            $transApotek = [];
            // $data = $this->getData();
            // $konfig_harga = isset($data['konfig']['hargaygdigunakan']) ? $data['konfig']['hargaygdigunakan'] : 'AVERAGE';
            // if ($konfig_harga == 'MAX') {
            //     $harga_jual = $post['hargamax'];
            // } elseif ($konfig_harga == 'MIN') {
            //     $harga_jual = $post['hargamin'];
            // } elseif ($konfig_harga == 'AVERAGE') {
            //     $harga_jual = $post['hargarata'];
            // }
            $harga_jual = $post['hargajual'];
            $harga_jual_oa = $harga_jual * $post['qty'];
            $harga_netto_oa = $post['harganetto'] * $post['qty'];

            if ($type == 1) {
                $cacheTrans = Yii::$app->cache->get('addObatBebas' . $ruangan_id . '-' . $pegawai_id);
                $transApotek = json_decode($cacheTrans, true);
                $return = [];
                if (isset($transApotek[$id])) {
                    $is_racikan = isset($post['is_racikan']) ? true : false;
                    $return = [
                        'posisi' => $post['posisi'],
                        'pegawai_id' => $pegawai_id,
                        'obatalkes_id' => $post['obatalkes_id'],
                        'obatalkes_nama' => $post['obatalkes_nama'],
                        'signa' => $post['signa'],
                        'racikan_id' => $post['racikan_id'],
                        'qty' => $post['qty'],
                        'ppn' => $post['ppn'],
                        'harganetto' => $harga_netto_oa,
                        'satuankecil_id' => $post['satuankecil_id'],
                        'harga' => $harga_jual,
                        'is_racikan' => $is_racikan,
                        'r_ke' => $post['r_ke'],
                        'subtotal' => $harga_jual_oa,

                        'qty_konversi' => $post['qty_konversi'],
                        'satuaninput_id' => $post['satuaninput_id'],
                        'satuan_input' => $post['satuan_input'],
                        'satuankonversi_id' => $post['satuankonversi_id'],
                        'satuan_konversi' => $post['satuan_konversi'],
                        'harga_konversi' => $post['harga_kecil'],
                        'etiket' => $post['etiket']

                    ];

                }
                $transApotek[$id] = $return;
                $listTrans = json_encode($transApotek);
                Yii::$app->cache->set('addObatBebas' . $ruangan_id . '-' . $pegawai_id, $listTrans);
            } elseif ($type == 2) {
                $cacheTrans = Yii::$app->cache->get('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id);
                $transApotek = json_decode($cacheTrans, true);
                $return = [];
                if (isset($transApotek[$id])) {
                    $is_racikan = isset($post['is_racikan']) ? true : false;
                    $return = [
                        'posisi' => $post['posisi'],
                        'pegawai_id' => $pegawai_id,
                        'obatalkes_id' => $post['obatalkes_id'],
                        'obatalkes_nama' => $post['obatalkes_nama'],
                        'signa' => $post['signa'],
                        'racikan_id' => $post['racikan_id'],
                        'qty' => $post['qty'],
                        'ppn' => $post['ppn'],
                        'harganetto' => $harga_netto_oa,
                        'satuankecil_id' => $post['satuankecil_id'],
                        'harga' => $harga_jual,
                        'is_racikan' => $is_racikan,
                        'r_ke' => $post['r_ke'],
                        'subtotal' => $harga_jual_oa,

                        'qty_konversi' => $post['qty_konversi'],
                        'satuaninput_id' => $post['satuaninput_id'],
                        'satuan_input' => $post['satuan_input'],
                        'satuankonversi_id' => $post['satuankonversi_id'],
                        'satuan_konversi' => $post['satuan_konversi'],
                        'harga_konversi' => $post['harga_kecil'],
                        'etiket' => $post['etiket']

                    ];

                }
                $transApotek[$id] = $return;
                $listTrans = json_encode($transApotek);
                Yii::$app->cache->set('addObatKaryawan' . $ruangan_id . '-' . $pegawai_id, $listTrans);
            }


            $result['data'] = $transApotek[$id];
            $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     * @todo save resep bebas
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveBebas() {
        try {
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $post = $request->post();
            $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $urutLabel = 'urutObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $list_obat = json_decode($cacheTrans, true);

            if (count($list_obat) <= 0) {
                return DocoHelpers::response(['response'=>['title'=>'Terjadi Kesalahan','message' => 'Obat Tidak Boleh Kosong!']],500);
            }

            $info_pasien = $post['info_pasien'];
            $explodeNamaPembeli = explode(' / ',$info_pasien['pasien']);
            $namaPembeli = $explodeNamaPembeli[0];

            if(count($explodeNamaPembeli) > 1) {
                $namaPembeli = $explodeNamaPembeli[1] . ' / ' . $explodeNamaPembeli[0];
            }

            $dataPost = [
                "jenispenjualan" => DocoConstants::JUAL_BEBAS,
                "carabayar_id" => $info_pasien['carabayar_id'],
                "iter" => $info_pasien['iter'],
                "nama_pembeli" => $namaPembeli,
                "pegawai_id" => !empty($info_pasien['dokter_id']) ? $info_pasien['dokter_id'] : '',
                "tglpenjualan" => $info_pasien['tglpenjualan'],
                "penjamin_id" => $info_pasien['penjamin_id'],
                "total_obat" => $post['totalharga_jual'],
                "biayaadministrasi" => $post['biayaadministrasi'],
                "ruangan_id" => $ruangan_id,
                "catatan" => $info_pasien['catatan'],
                "etiket" => $info_pasien['catatan'],
                "totalharga_netto" => $post['totalharga_netto'],
                "list_obat" => $list_obat,
                "tgl_lahir" => empty($post['info_pasien']['tgl_lahir']) ? null : $post['info_pasien']['tgl_lahir']
            ];

            $response = $this->_restApotek->request('POST', 'penjualan-resep/save', [
                'form_params' => $dataPost
            ]);
            $response = json_decode($response->getBody(), true);
            if ($response['metadata']['status'] == 200) {
                $response['status'] = true;
                $response['response']['encNomor'] = isset($response['response']['nomor']) ? DocoHelpers::encrypt($response['response']['nomor']) : DocoHelpers::encrypt(1);
                Yii::$app->cache->delete($cacheLabel);
                Yii::$app->cache->delete($urutLabel);
            } else {
                $response['status'] = false;
                $response['data_obat'] = $list_obat;
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @todo save resep karyawan
     * @author Randy Vianda Putra <randy@docotel.com>
     * @edited by Novia Sukmasari Putri <novia.putri@docotel.com>
     */
    public function actionSaveKaryawan() {
        try {
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $post = $request->post();

            $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $urutLabel = 'urutObatPasien' . $ruangan_id . '-' . $pegawai_id;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $list_obat = json_decode($cacheTrans, true);

            if (count($list_obat) <= 0) {
                return DocoHelpers::response(['response'=>['title'=>'Terjadi Kesalahan','message' => 'Obat Tidak Boleh Kosong!']],500);
            }

            $info_pasien = $post['info_pasien'];

            $dataPost = [
                "jenispenjualan" => DocoConstants::JUAL_KARYAWAN,
                "karyawan_id" => $info_pasien['pasien_id'],
                "nama_pembeli" => $info_pasien['pasien'],
                "iter" => $info_pasien['iter'],
                "pegawai_id" => !empty($info_pasien['dokter_id']) ? $info_pasien['dokter_id'] : '',
                "total_obat" => $post['totalharga_jual'],
                "biayaadministrasi" => $post['biayaadministrasi'],
                "ruangan_id" => $ruangan_id,
                "catatan" => $info_pasien['catatan'],
                "etiket" => $info_pasien['catatan'],
                "totalharga_netto" => $post['totalharga_netto'],
                "list_obat" => $list_obat,
                "carabayar_id" => $info_pasien['carabayar_id'],
                "penjamin_id" => $info_pasien['penjamin_id']
            ];

            $response = $this->_restApotek->request('POST', 'penjualan-resep/save', [
                'form_params' => $dataPost
            ]);

            $response = json_decode($response->getBody(), true);
            if ($response['metadata']['status'] == 200) {
                    $response['status'] = true;
                    $response['response']['encNomor'] = isset($response['response']['nomor']) ? DocoHelpers::encrypt($response['response']['nomor']) : DocoHelpers::encrypt(1);
                    Yii::$app->cache->delete($cacheLabel);
                    Yii::$app->cache->delete($urutLabel);
            } else {
                $response['status'] = false;
                $response['data_obat'] = $list_obat;
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/list-penjamin', [
                'query' => [
                        'carabayar_id' => $parent_label,
                    ],
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $key => $value) {
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value
                ];
                if (empty($selected)) {
                    $result['selected'] = $key;
                    $selected = $key;
                }
            }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCetakRs($id)
    {
        // $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $path = Yii::getAlias("@download") . "/cetak-resep-pasien-rs.pdf";
        try {
            $response = $this->_restApotek->get('transaksi-resep/print-resep-rs',[
                'save_to' => $path,
                'query' => [
                        'id' => $id,
                    ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakPdf($id, $type)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/transaksi-resep.pdf";
        try {
            $response = $this->_restApotek->get('transaksi-resep/cetak-pdf?id=' . $id . '&type=' . $type,[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionResetCache($type = 1){
        $request = Yii::$app->request;
        try{
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $cacheName = 'addObatBebas' . $ruangan_id . '-' . $pegawai_id;
            $urutanName = 'urutObat' . $ruangan_id . '-' . $pegawai_id;
            if($type == 2){
                $cacheName = 'addObatKaryawan' . $ruangan_id . '-' . $pegawai_id;
                $urutanName = 'urutObatKaryawan' . $ruangan_id . '-' . $pegawai_id;
            }
            Yii::$app->cache->delete($cacheName);
            Yii::$app->cache->delete($urutanName);

            return true;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDepListAmpuls()
    {
        try{
            $request      = Yii::$app->request;
            $post         = $request->post();
            $obatalkes_id = $post['depdrop_parents'][0];

            if (!empty($obatalkes_id)) {
                $obatalkes_id  = (int)$obatalkes_id;
                $responses = Yii::$app->cache->getOrSet('list-ampul-'.$obatalkes_id,function() use($obatalkes_id){
                    $ampulsRequest = Yii::$app->docoRest->apotek->get('transaksi-resep/list-ampuls',['query'=>['obatalkes_id'=>$obatalkes_id]]);
                    $body          = json_decode($ampulsRequest->getBody(),TRUE);
                    return $body['response']['data'];
                },60*3);
                $id_kecil = $responses['0']['satuankecil_id'];
            } else {
                return DocoHelpers::response(['output'=>[], 'selected'=>null]);
            }

            $out = [];
            foreach($responses as $rowdata) {
                $out[] = [
                    'id'       => $rowdata['satuanbesar_id'],
                    'name'     => $rowdata['satuan_besar'],
                    'konversi' => $rowdata['nilai_konversi'],
                ];
            }
            return DocoHelpers::response(['output'=>$out, 'selected'=>$id_kecil]);
        } catch(Exception $e){
            return DocoHelpers::responseTemplate(500,$e->getMessage());
        }
    }

    public function actionGetNilaiKonversi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request                     = Yii::$app->request;
        $get                         = $request->get();
        $satuanbesar_id              = $get['satuanbesar_id'];
        $obatalkes_id                = $get['obatalkes_id'];
        try {
            $request  = $this->_restApotek->get('transaksi-resep/get-konversi?obatalkes_id='. $obatalkes_id.'&satuanbesar_id='. $satuanbesar_id);
            $body     = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionPasien()
    {
        $title = $this->_title . ' Pasien';
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $modelForm = new PenjualanResepPasienForm;

        $cacheLabel = 'addObatPasien' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabel, null);
        $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;
        Yii::$app->cache->set($cacheLabelTrackStock, null);
        $cacheLabelTrackPasien = "trackObatPasien";

        $transApotek = 'null';
        $urutObatPasien = 'null';
        $cacheDuration = 60 * 5;
        $cacheKey = [
                'filler-transaksi-resep-pasien',
                'ruangan_id' => $ruangan_id
            ];
        $cacheParam = Yii::$app->request->get('cache',null);
        if($cacheParam != null && $cacheParam == 0){
            Yii::$app->cache->delete($cacheKey);
        }

        // \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
        $data = Yii::$app->cache->getOrSet($cacheKey,function() use($ruangan_id,$cacheParam){
            $response = Yii::$app->docoRest->apotek->request('GET', 'transaksi-resep/filler-pasien', [
                'query' => [
                    'ruangan_id' => $ruangan_id,
                    'cache' => $cacheParam
                ]
            ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            return [
                'carabayar_karyawan' => $body['response']['data']['penjamin_karyawan'],
                'data_signa' => $body['response']['data']['signa'],
                'satuan_unit' => $body['response']['data']['satuan_unit'],
                'data_cara_bayar' => $body['response']['data']['data_cara_bayar'],
            ];

        }, $cacheDuration,new \yii\caching\TagDependency(['tags' => 'obat']));
        $carabayarpenjamin_karyawan = $data['carabayar_karyawan'];

        $penjaminkaryawan_id = DocoHelpers::decrypt($carabayarpenjamin_karyawan['penjamin_id']);
        $penjaminkaryawan_nama = $carabayarpenjamin_karyawan['penjamin_nama'];
        $carabayarkaryawan_id = DocoHelpers::decrypt($carabayarpenjamin_karyawan['carabayar_id']);;
        $carabayarkaryawan_nama = $carabayarpenjamin_karyawan['carabayar_nama'];

        $list_signa = ArrayHelper::map($data['data_signa'],'signa_id','kode_nama');
        $data_signa = json_encode($data['data_signa']);
        $list_obat_ruangan = $list_obat_ruangan_options = [];
        $satuan_unit = ArrayHelper::map($data['satuan_unit'], 'satuanunit_id', 'satuanunit_nama');
        $data_cara_bayar = ArrayHelper::map($data['data_cara_bayar'], 'carabayar_id', 'carabayar_nama');

        return $this->render('pasien', get_defined_vars());
    }

    public function actionListDokter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $query = http_build_query($request->get());

        try {
            $response = $this->_restApotek->get('allow/list-dokter?'.$query, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $data = $body['response']['data'];

            $list = [];
            foreach ($data as $value) {
                $item = [
                    "id" => $value['pegawai_id'],
                    "text" => $value['nama_pegawai'],
                ];

                $list[] = $item;
            }

            return $list;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionSearchPasienSelect2()
    {
        return Yii::$app->docoPlugin->execute($this, 'search_pasien_reseptur');
    }

    public function actionSearchMasterPasienSelect2()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $payload = $request->get();
        $term = $request->get('nama_pasien');
        try {
            $response = Yii::$app->docoRest->apotek->get('allow/search-data-master-pasien?', [
                'form_params' => [],
                'query' => [
                    'term' => $term,
                    'page' => $payload['page']
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];

            $list = [];
            foreach ($data as $row_item) {
                $list[] = [
                    "id" => $row_item['pasien_id'],
                    "text" => $row_item['no_rekam_medik'] . " / " . $row_item['nama_pasien'],
                    "data" => $row_item
                ];
            }

            return [
                'list' => $list,
                'data' => $data,
                'more' => isset($body['response']['data']) ? count($body['response']['data']) >= 10 : false,
                'payload' => $request->get(),
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionSearchKaryawanSelect2()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $payload = $request->get();
        $term = $request->get('nip');
        try {
            $response = $this->_restApotek->get('allow/search-data-karyawan?', ['form_params' => [],
                'query' => [
                    'term' => $term,
                    'page' => $payload['page']
                ],
            ]);
            $body = json_decode($response->getBody(), true);

            $data = $body['response']['data'];

            $list_no_duplicate = [];
            foreach ($data as $pegawai) {
                $list_no_duplicate[$pegawai['nomorindukpegawai']] = $pegawai;
            }
            $data = $list_no_duplicate;

            $list = [];
            foreach ($data as $row_item) {
                $item = [
                    "id" => $row_item['pegawai_id'],
                    "text" => $row_item['nomorindukpegawai']. " / " .$row_item['nama_pegawai'],
                    "data" => $row_item
                ];
                $list[] = $item;
            }

            return [
                'list' => $list,
                'data' => $data,
                'more' => isset($body['response']['data']) ? count($data) >= 10 : false,
                'payload' => $request->get(),
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getKonfigFarmasi($get_new = false)
    {
        try {
            // $getKonfig = Yii::$app->cache->get('konfigFarmasi');
            // if(!$getKonfig || $get_new) {
            $getKonfig = $this->_restApotek->request('GET', 'allow/konfig-farmasi');
            $bodyFarmasi = json_decode($getKonfig->getBody(), true);

            $setKonfig = Yii::$app->cache->set('konfigFarmasi', $bodyFarmasi);
            $getKonfig = $bodyFarmasi;
            // }

            return $getKonfig['response'];
        } catch (\Exception $e) {
            return [
                'messages' => $e->getMessage()
            ];
        }
    }

    public function recalculatingCache($transApotek, $isEditReseptur = false)
    {
        $konfigFarmasi = $this->getKonfigFarmasi(true);
        $embalase_racikan = $konfigFarmasi['embalase_racikan'];
        $embalase_nonracikan = $konfigFarmasi['embalase_nonracikan'];

        $racikan = ArrayHelper::map($transApotek, "r_ke", "r_ke");

        $count_r_ke = [];
        $totalqty_r_ke = [];
        foreach ($racikan as $r_ke) {
            foreach ($transApotek as $key => $value) {
                if (isset($value['r_ke']) && $value['r_ke'] == $r_ke && empty($value['is_deleted'])) {
                    if (!isset($count_r_ke[$value['r_ke']])) {
                        $count_r_ke[$value['r_ke']] = 1;
                        $totalqty_r_ke[$value['r_ke']] = isset($value['det']) ? ceil($value['det']) : ceil($value['qty']);
                    } else {
                        $count_r_ke[$value['r_ke']]++;
                        $totalqty_r_ke[$value['r_ke']] += isset($value['det']) ? ceil($value['det']) : ceil($value['qty']);
                    }
                }
            }
        }

        foreach ($transApotek as $noUrut => $detail_obat) {
            if(!isset($detail_obat['r_ke'])) {
                continue;
            }

            $r_ke = $detail_obat['r_ke'];

            $embalase = 0;
            if ($detail_obat['racikan_id'] == 1) {
                $jumlah_obat = isset($count_r_ke[$r_ke]) && $count_r_ke[$r_ke] > 0 ? $count_r_ke[$detail_obat['r_ke']] : 1;
                $total_qty = isset($totalqty_r_ke[$r_ke]) && $totalqty_r_ke[$r_ke] > 0 ? $totalqty_r_ke[$detail_obat['r_ke']] : 1;
                $embalase = $embalase_racikan;
                $embalaseMinQty = $embalase / ceil($total_qty);
            } else {
                $embalase = $embalase_nonracikan;
                $embalaseMinQty = $embalase / ceil($detail_obat['qty']);
            }

            if(isset($detail_obat['obatalkespasien_id']) && $detail_obat['satuan_input'] != $detail_obat['satuan_konversi']) {
                $hrgaJual = isset($detail_obat['hargajual_tanpaembalase']) ? $detail_obat['hargajual_tanpaembalase'] : $detail_obat['hargajual'];
            } else {
                $nilai_konversi = isset($detail_obat['nilai_konversi']) ? $detail_obat['nilai_konversi'] : 1;
                $hargajualtanpa_embalase = isset($detail_obat['hargajual_tanpaembalase']) ? $detail_obat['hargajual_tanpaembalase'] : $detail_obat['hargajual'];
                $hrgaJual = $hargajualtanpa_embalase * $nilai_konversi;
            }

            if($detail_obat['hargajual'] == 0 || $detail_obat['qty'] == 0) {
                $transApotek[$noUrut]['harga'] = 0;
                $transApotek[$noUrut]['embalase'] = 0;
                $transApotek[$noUrut]['subtotal'] = 0;
            } else {
                $harga = ceil($hrgaJual) + $embalaseMinQty;
                $subtotal = ceil(($hrgaJual + $embalaseMinQty)) * ceil($detail_obat['qty']);
                $transApotek[$noUrut]['harga'] = ceil($harga);
                $transApotek[$noUrut]['embalase'] = $embalase;
                $transApotek[$noUrut]['subtotal'] = ceil($subtotal);
            }

            if($isEditReseptur && $transApotek[$noUrut]['is_deleted']) {
                $transApotek[$noUrut]['subtotal'] = 0;
            }
        }

        return $transApotek;
    }

    public function cekStokRuangan($obatalkes_id, $qty_akan_dipakai, $label_cache = null)
    {
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $response = $this->_restApotek->request('GET', 'allow/check-stok-new', [
            'query' => [
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id,
            ],
        ]);
        $body = json_decode($response->getBody(), true);

        $qty_tersedia = isset($body['response']['qty_stok']) ?
            $body['response']['qty_stok'] : 0;

        if ($label_cache !== null) {
            $cacheTrackStok = Yii::$app->cache->get($label_cache);
            $trackStock = json_decode($cacheTrackStok, true);

            $stok_terpakai = isset($trackStock[$obatalkes_id]) ? $trackStock[$obatalkes_id] : 0;
            $qty_tersedia -= $stok_terpakai;
        }

        return ($qty_tersedia - $qty_akan_dipakai) >= 0;
    }

    public function trackStokResep($cache_label, $obat_alkes_id, $qty)
    {
        $cacheTrackStok = Yii::$app->cache->get($cache_label);
        $track_stock_obat = json_decode($cacheTrackStok, true);

        $qty_terpakai = isset($track_stock_obat[$obat_alkes_id])
            ? $track_stock_obat[$obat_alkes_id] : 0;

        $qty_terpakai += $qty;
        $track_stock_obat[$obat_alkes_id] = $qty_terpakai < 0 ? 0 : $qty_terpakai;
        Yii::$app->cache->set($cache_label, json_encode($track_stock_obat));
    }

    public function actionListObatAlkesDepo()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $penjamin_id = isset($get['penjamin_id']) ? $get['penjamin_id'] : 1;
        $kelaspelayanan_id = isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : null;
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
        $keyword = isset($get['q']) ? $get['q'] : '';
        $page = isset($get['page']) ? $get['page'] : 1;
        $return = [];
        $data = [];
        $transaksiObatMinus = false;
        $konfigFarmasi = $this->getKonfigFarmasi(true);

        if(isset($konfigFarmasi['is_transaksiobat_0']) && $konfigFarmasi['is_transaksiobat_0'] == true) {
            $transaksiObatMinus = true;
        }
        
        $noresep = isset($get['noresep']) ? $get['noresep'] : null;

        if (
            isset($get['kelastitipan_id'])
            && !is_null($get['kelastitipan_id'])
            && !empty($get['kelastitipan_id'])
        ) {
            $kelaspelayanan_id = $get['kelastitipan_id'];
        }

        $jenis = null;
        $userIdentity = Yii::$app->session->get('user_identity');
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN)
        {
            $jenis = DocoConstants::GOUP_ALKES;
        }

        try {
            $request = $this->_restApotek->get('allow/get-list-stok-apotek',[
                'query' => [
                    // 'instalasi_id' => $instalasi_id,
                    'instalasi_id' => ArrayHelper::getValue($get, 'instalasi_id', 0),
                    'penjamin_id'  => $penjamin_id,
                    'group_jenisobat' => $jenis,
                    'ruangan_id'   => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'keyword'      => $keyword,
                    'page'         => $page,
                ]
            ]);
            $body = json_decode($request->getBody(),TRUE);
            if(!empty($body['response']['data'])) {
                $data = $body['response']['data'];
                $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id . '-' . $noresep;
                $cacheTrackStok = Yii::$app->cache->get($cacheLabelTrackStock);
                $trackStock = json_decode($cacheTrackStok, true);

                foreach ($data as $index => $stok_obat) {
                    $obatalkes_id = $stok_obat['obatalkes_id'];
                    $stok_terpakai = isset($trackStock[$obatalkes_id]) ? $trackStock[$obatalkes_id] : 0;
                    $data[$index]['qty_tersedia'] -= $stok_terpakai;
                }
            }

            $return = [
                'data_stok' => $data,
                'payload' => $get,
                'transaksi_obat_minus' => $transaksiObatMinus
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            echo json_encode(['data_stok'=>[], 'payload'=>$get]);
            return;
        }
    }


    /**
     * Source Data dropdown
     *
     * @param String $type
     * @param Array $payload
     * @return JSON
     * @author iqbal.rukmana
     **/
    public function actionSourceData()
    {
        return $this->guzzleExec($this->_restApotek, [
            'url' => 'allow/list-signa',
            'payload' => [
                'query' => Yii::$app->request->get('payload', []),
            ],
            'returnResponse' => true
        ]);
    }
}
