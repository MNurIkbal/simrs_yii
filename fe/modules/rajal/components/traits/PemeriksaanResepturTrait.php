<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-09 10:54:38
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-15 13:38:44
 * @Description:
 */

namespace app\modules\rajal\components\traits;

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

    /*================================
    =            Reseptur            =
    ================================*/

    public function actionReseptur()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $modelReseptur = new ResepturForm;
        $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
        $modelResepturDetailRacikan = new ResepturDetailForm;
        $modelResepturDetailNonRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;
        $modelResepturDetailRacikan->scenario = ResepturDetailForm::SCENARIO_SESSION;

        $session = Yii::$app->session;
        $userIdentity = $session->get('user_identity');
        $classTrxReseptur = '';
        // MHBG Perawat Bisa Tambah Alkes
        // if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
        //     $classTrxReseptur = 'hidden';
        // }

        if ($post = $request->post()) {
            $data = [];
            if (isset($post['ResepturNrDetailForm'])) {
                $jenis_racikan = ResepturDetailForm::VC_NRC;
                $postResepturNr = $post['ResepturNrDetailForm'];
                $postResepturNr['iter'] = $post['iter'];
                $postResepturNr['jenis_racikan'] = $jenis_racikan;
                $modelResepturDetailNonRacikan->attributes = $postResepturNr;
                if (!$modelResepturDetailNonRacikan->validate()) {
                    $formName = substr(strrchr(get_class($modelResepturDetailNonRacikan), "\\"), 1);
                    $response = $modelResepturDetailNonRacikan->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
                $data = $postResepturNr;
            }
            if (isset($post['ResepturDetailForm'])) {
                $postReseptur = $post['ResepturDetailForm'];
                $jenis_racikan = ResepturDetailForm::VC_RC;
                $postReseptur['jenis_racikan'] = $jenis_racikan;
                $postReseptur['satuankecil_id'] = $postReseptur['satuaninput_id'];
                $modelResepturDetailRacikan->attributes = $postReseptur;
                if (!$modelResepturDetailRacikan->validate()) {
                    $formName = substr(strrchr(get_class($modelResepturDetailRacikan), "\\"), 1);
                    $response = $modelResepturDetailRacikan->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
                $data = $postReseptur;
            }

            $response = $this->addSessionReseptur($pendaftaran_id, $data);
            if ($response['status'] != 200) {
                $message = $response['message'];
                if ($response['status'] == 100) {
                    return DocoHelpers::responseTemplate(
                        422,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan!'),
                            'text' => $message,
                            'message' => $message,
                        ]
                    );
                }
            } else {
                $res = ['message' => 'Data Berhasil disimpan'];
                return DocoHelpers::response($res, false, true);
                // return DocoHelpers::response(['response' => ['message' => 'Data berhasil disimpan']]);
            }
        } else {
            $list_data = $this->getListDataReseptur();

            $list_data_apotek = $list_data['data_ruanganapotek'];
            $list_data_signa = $list_data['data_signa'];
            $data_template = $list_data['data_template'];
            $data_pendaftaran = $list_data['pendaftaran'];
            Yii::$app->cache->set('data_signa', $list_data_signa);

            $diagnosa = json_decode($list_data['diagnosa'], true);
            $idDiagnosa = isset($diagnosa['id']) ? $diagnosa['id'] : null;

            $data_pasien = $this->_data_pasien;
            $pd = $this->_pendaftaran_id;
            $id = $pendaftaran_id;
            $status_update = $this->getStatusPeriksa($id, $data_pendaftaran);
            // default depo berdasarkan dari ruangan sekarang
            // $default_depo = null;

            // if ($this->_instalasi_id == DocoConstants::INSTALASI_ID_RJ) {
            //     $modelReseptur->depo_id = DocoConstants::DEPO_APOTEK_RJ;
            //     $default_depo = DocoConstants::DEPO_APOTEK_RJ;
            // }

            // default depo berdasarkan config dari DB
            $default_depo = $list_data['default_depo'];

            return $this->renderAjax('reseptur/__reseptur', get_defined_vars());
        }
    }

    public function actionResepturModal()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $modelReseptur = new ResepturForm;
        $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
        $user = Yii::$app->session->get('user_identity');

        $url = [
            'form-action' => '/rajal/pemeriksaan/simpan-reseptur',
            'dokter_url' => '/rajal/pemeriksaan/list-dokter-reseptur',
            'actionTemplate' => '/rajal/pemeriksaan/form-template-reseptur',
            'urlSimpanReseptur' => '/rajal/pemeriksaan/simpan-template-reseptur',
            'urlDeleteTemplate' => '/rajal/pemeriksaan/delete-template-reseptur',
            'urlUpdateTemplate' => '/rajal/pemeriksaan/form-template-reseptur?reseptemp_id=#reseptemp_id#&is_update=true',
            'urlModalHistoryResep' => '/rajal/pemeriksaan/modal-history-resep?pasien_id=' . $this->_pasien_id
        ];

        $kode_transaksi = [DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES, 'config_zero_stock'];
        if($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) array_push($kode_transaksi, DocoConstants::GOUP_ALKES_PERAWAT_RESEP);

        $requestGetData = $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/default-data-reseptur-pelayanan',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                    'kode_transaksi' => $kode_transaksi,
                ]
            ]
        ]);
        $defaultData = isset($requestGetData['defaultData']) ? $requestGetData['defaultData'] : [];
        $lookupTransaksi = isset($requestGetData['lookupTransaksi']) ? $requestGetData['lookupTransaksi'] : [];

        if (Yii::$app->docoVars->user("kelompokpegawai_id") != DocoConstants::KELOMPOK_MEDIS) {
            $user = [
                'id_pegawai' => $this->_data_pasien['pegawai_id'],
                'nama_pegawai' => $this->_data_pasien['nama_pegawai']
            ];
        }

        $list_data_apotek = ArrayHelper::getValue($defaultData, 'list-depo', []);;

        $modelReseptur->diagnosa_id = ArrayHelper::getValue($defaultData, 'diagnosa_id');
        $modelReseptur->diagnosa_nama = ArrayHelper::getValue($defaultData, 'diagnosa_nama');
        $modelReseptur->berat_badan = ArrayHelper::getValue($defaultData, 'berat_badan');
        $modelReseptur->tinggi_badan = ArrayHelper::getValue($defaultData, 'tinggi_badan');
        $modelReseptur->dokter = ArrayHelper::getValue($user, 'nama_pegawai');
        $modelReseptur->pegawai_id = ArrayHelper::getValue($user, 'id_pegawai');

        $data_pasien = $this->_data_pasien;
        $pd = $this->_pendaftaran_id;
        $id = $pendaftaran_id;
        // default depo berdasarkan config dari DB
        $modelReseptur->depo_id = ArrayHelper::getValue($defaultData, 'depo_id');;

        $default_dokter = [
            $modelReseptur->pegawai_id => ArrayHelper::getValue($user, 'nama_pegawai'),
        ];

        $is_others = 0;
        if($defaultData['is_others']){
            $is_others = 1;
        }

        $enable_split_kronis = 0;
        if($defaultData['enable_split_kronis']) {
            $enable_split_kronis = 1;
        }
        
        $konfigStokObatAlkes = isset($lookupTransaksi[DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES]) ? $lookupTransaksi[DocoConstants::KONFIG_VALIDASI_STOK_OBAT_ALKES] : [];
        $konfigStokObatAlkes = ArrayHelper::getValue($konfigStokObatAlkes, 'additional_value');
        $allowZeroStock = isset($lookupTransaksi['config_zero_stock']) ? $lookupTransaksi['config_zero_stock'] : [];
        $allowZeroStock = ArrayHelper::getValue($allowZeroStock, 'kode_id', 0) ? true : false;

        $groupJenisobat = '';
        if(Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_KEPERAWATAN){
            $groupJenisobat = isset($lookupTransaksi[DocoConstants::GOUP_ALKES_PERAWAT_RESEP]) ? $lookupTransaksi[DocoConstants::GOUP_ALKES_PERAWAT_RESEP] : [];
            $groupJenisobat = isset($groupJenisobat['additional_value']) ? json_decode($groupJenisobat['additional_value']) : '';
        }

        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }

        return $this->renderAjax('//cppt/reseptur/__modal', [
            'model' => $modelReseptur,
            'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
            'list_data_apotek' => $list_data_apotek,
            'pendaftaran_id' => $pendaftaran_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'pegawai_id' => ArrayHelper::getValue($user, 'id_pegawai'),
            'ruanganreseptur_id' => $data_pasien['ruangan_id'],
            'data_pasien' => $data_pasien,
            'url' => $url,
            'is_ranap' => false,
            'dokterList' => $default_dokter,
            'is_freetext' => ArrayHelper::getValue($defaultData, 'is_freetext'),
            'is_others' => $is_others,
            'enable_split_kronis' => $enable_split_kronis,
            'hari_resep_kronis' => ArrayHelper::getValue($defaultData, 'hari_resep_kronis'),
            'actionTemplate' => '/rajal/pemeriksaan/form-template-reseptur',
            'urlSimpanReseptur' => '/rajal/pemeriksaan/simpan-template-reseptur',
            'konfigStokObatAlkes' => $konfigStokObatAlkes,
            'allowZeroStock' => $allowZeroStock,
            'groupJenisobat' => $groupJenisobat,
            'infoPasien' => [
                'nama_pasien' => ArrayHelper::getValue($data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ],
            'instalasi_id' => ArrayHelper::getValue($this->_data_pasien, 'instalasi_id')
        ]);
    }

    public function actionListDokterReseptur() {
        $dokterList = $this->guzzleExec($this->_restRajal, [
            'url' => 'allow/list-dokter-perujuk',
            'method' => 'get',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
        ]);
        return DocoHelpers::response($dokterList);
    }

    // public function actionSimpanReseptur()
    // {
    //     try {
    //         $request = Yii::$app->request;

    //         $header = $request->post('reseptur_header', []);
    //         $detail = $request->post('list_obat', []);
    //         $header['tglreseptur'] = Date('Y-m-d H:i:s');
    //         $payload = [
    //             'data_reseptur' => $header,
    //             'data_resepturdetail' => $detail
    //         ];

    //         $response = $this->_restRajal->post('tra-pemeriksaan/create-reseptur', [
    //             'form_params' => $payload
    //         ]);

    //         $response = json_decode($response->getBody(), true);
    //         return DocoHelpers::response($response);
    //     } catch (RequestException $e) {
    //         $error = json_decode($e->getResponse()->getBody(), true);
    //         $message = isset($error['response']['message']) ? $error['response']['message'] : $e->getMessage();
    //         return DocoHelpers::response(['message' => $message, 'response' => ['message' => $message]], $e->getResponse()->getStatusCode());
    //     } catch (\Exception $e) {
    //         return DocoHelpers::response(['message' => $e->getMessage()], 500);
    //     }
    // }
    
    public function actionSimpanReseptur()
    {
        $request = Yii::$app->request;
        $header = $request->post('reseptur_header', []);
        $detail = $request->post('list_obat', []);
        $header['tglreseptur'] = Date('Y-m-d H:i:s');
        $result = $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'tra-pemeriksaan/create-reseptur',
            'payload' => [
                'form_params' => [
                    'data_reseptur' => $header,
                    'data_resepturdetail' => $detail
                ]
            ],
            // 'returnResponse' => true
        ]);
        return DocoHelpers::response($result);
    }

    public function actionGetDataResepturSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $pendaftaran_id_encrypt = $request->get('pendaftaran_id');
        $ruangan_id = DocoHelpers::encrypt($this->_id_ruangan);
        $encryptedId = $pendaftaran_id_encrypt . $ruangan_id;

        try {
            // get/set session
            if (isset($session['pemeriksaan_reseptur'][$encryptedId])) {
                $temp_session = $session['pemeriksaan_reseptur'][$encryptedId];
                $ruangan_depo = Yii::$app->session->get('ruangan-obat-rajal');
                $this->_id_ruangan = $ruangan_depo;
                $data_tables = $this->generateTableReseptur($pendaftaran_id_encrypt, $temp_session);

                return DocoHelpers::response($data_tables);
            } else {
                $response = [];
                // return DocoHelpers::response($response);
                return DocoHelpers::dataTabelsException('Data kosong');
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function generateTableReseptur($pendaftaran_id, $datas = [])
    {
        // get data
        $request = Yii::$app->request;
        $data_signa = Yii::$app->cache->get('data_signa');
        $list_data_signa = !empty($data_signa) ? ArrayHelper::map($data_signa, 'signa_id', 'signa_nama') : [];

        $data_tables = [];
        $no = 1;
        // data get from sessio

        if ($datas) {
            foreach ($datas as $data) {
                $hargasatuan_reseptur = $data['hargasatuan_reseptur'];
                $qty_reseptur = $data['qty_reseptur'];
                $jumlah_harga = $hargasatuan_reseptur * $qty_reseptur;

                $data_table = [];
                $primaryKey = $data['session_key'];
                $data_table['no'] = $no;
                $data_table['session_key'] = $data['session_key'];
                $data_table['jenis_racikan'] = $data['jenis_racikan'];
                $data_table['nama_racikan'] = $data['nama_racikan'];
                $data_table['rke'] = !empty($data['rke']) ? $data['rke'] : '-';
                $data_table['signa_reseptur'] = $data['signa_reseptur'];
                $data_table['obatalkes_id'] = $data['obatalkes_id'];
                $data_table['obatalkes_nama'] = isset($data['obatalkes_nama']) ? $data['obatalkes_nama'] : null;
                $data_table['qty_reseptur'] = $qty_reseptur;
                $data_table['satuankecil_id'] = $data['satuankecil_id'];
                $data_table['satuankecil_nama'] = $data['satuankecil_nama'];
                $data_table['satuandefault_id'] = $data['satuandefault_id'];
                $data_table['satuandefault_nama'] = $data['satuandefault_nama'];
                $data_table['harga_konversi'] = $jumlah_harga;
                $data_table['total_konversi'] = $data['total_konversi'];
                $data_table['hargasatuan_reseptur'] = DocoHelpers::rupiahDisplay($hargasatuan_reseptur);
                $data_table['jumlah_harga'] = DocoHelpers::rupiahDisplay($jumlah_harga);
                $data_table['etiket'] = isset($data['etiket']) ? $data['etiket'] : '';
                //rizal
                $data_table['harganetto_reseptur'] = $data['harganetto_reseptur'];
                $data_table['signa_edit'] = Html::textInput('signa_edit_' . $primaryKey, $data['signa_reseptur'], [
                    'class' => 'form-control signa_edit input-xs',
                    'style' => 'width: 50px;'
                ]);

                $data_table['qty_edit'] = Html::textInput('qty_edit_' . $primaryKey, $data['qty_reseptur'], [
                    'class' => 'form-control qty_add input-xs docoNumberOnly qty' . $primaryKey,
                    'data-id' => $primaryKey,
                    'data-harga' => $data['hargasatuan_reseptur'],
                    'disabled' => true,
                    'style' => 'width: 50px;'
                ]);

                $data_table['aksi'] = Html::button(
                    '<i class="fa fa-times"></i>',
                    [
                        'class' => 'btn btn-indian-red btn-xs delete-reseptur',
                        'action' => '/rajal/pemeriksaan/batal-session-reseptur?id=' . $primaryKey . '&pendaftaran_id=' . $pendaftaran_id,
                        'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                    ]
                );

                array_push($data_tables, $data_table);
                $no++;
            }
        }

        $return = [
            'data' => $data_tables,
            'draw' => $request->get('draw', null),
            'recordsTotal' => count($data_tables),
            'recordsFiltered' => count($data_tables)
        ];

        return $return;
        // return $data_tables;
    }

    private function addSessionReseptur($pendaftaran_id, $data = null)
    {
        $session = Yii::$app->session;
        $pendaftaran_id_encrypt = $pendaftaran_id;
        $pendaftaran_id_decrypt = DocoHelpers::decrypt($pendaftaran_id_encrypt);

        $ruangan_id = $this->_id_ruangan;
        $ruangan_id_encrypt = DocoHelpers::encrypt($ruangan_id);
        $encryptedId = $pendaftaran_id_encrypt . $ruangan_id_encrypt;

        try {
            // get session
            $session_key = 1;
            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
            }

            if (isset($session_reseptur[$encryptedId])) {
                foreach ($session_reseptur[$encryptedId] as $key => $value) {
                    $session_key = $value['session_key'];
                }

                $session_key++;
            }

            $data_reseptur = $this->setAttrReseptur($data);
            $count_data_insert = $data_reseptur['count_data_insert'];
            $data_reseptur = $data_reseptur['data'];
            if (empty($session_reseptur[$encryptedId])) {
                $session_reseptur = [];
            }

            return $this->setSessionReseptur($count_data_insert, $session_reseptur, $encryptedId, $session_key, $data_reseptur);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionBatalSessionReseptur()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $id = $request->get('id');

            $pendaftaran_id_encrypt = $request->get('pendaftaran_id');
            $ruangan_id = DocoHelpers::encrypt($this->_id_ruangan);
            $encryptedId = $pendaftaran_id_encrypt . $ruangan_id;

            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
                if (isset($session_reseptur[$encryptedId])) {
                    foreach ($session_reseptur[$encryptedId] as $key => $value) {
                        if ($value['session_key'] == $id) {
                            unset($session_reseptur[$encryptedId][$key]);
                        }
                    }
                    $session->set('pemeriksaan_reseptur', $session_reseptur);
                }
            } else {
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }

            return DocoHelpers::responseTemplate(
                200,
                Yii::t('fe', 'message_batal'),
                [],
                ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_batal')]
            );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    private function resetSessionReseptur($pendaftaran_id_encrypt = null)
    {
        $session = Yii::$app->session;
        $pendaftaran_id = $pendaftaran_id_encrypt;
        $ruangan_id = DocoHelpers::encrypt($this->_id_ruangan);

        if ($pendaftaran_id_encrypt) {
            if (isset($session['pemeriksaan_reseptur'])) {
                $temp_session = $session['pemeriksaan_reseptur'];
                if (isset($temp_session[$pendaftaran_id_encrypt . $ruangan_id])) {
                    unset($temp_session[$pendaftaran_id_encrypt . $ruangan_id]);
                }
                $session->set('pemeriksaan_reseptur', $temp_session);
            }
        }

        if (isset($session['pemeriksaan_reseptur_db'])) {
            $session->remove('pemeriksaan_reseptur_db');
        }

        return true;
    }

    public function actionSaveSessionReseptur()
    {
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';

        $data_change = $request->post();
        $depo_tujuan = $data_change['ResepturForm']['depo_id'];
        $pendaftaran_id_encrypt = $request->get('pendaftaran_id');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
        $data_pasien = $cache->get('pasien-pendaftaran-id-' . $pendaftaran_id_encrypt);
        $diagnosa_id = $request->post('diagnosa_id');
        $ruangan_id = $this->_id_ruangan;
        $iter = $data_change['ResepturForm']['iter'] ? (int)$data_change['ResepturForm']['iter'] : null;
        $encryptedId = $pendaftaran_id_encrypt . DocoHelpers::encrypt($ruangan_id);

        if (isset($session['pemeriksaan_reseptur'])) {
            $session_reseptur = $session['pemeriksaan_reseptur'];
        }

        if (isset($session_reseptur[$encryptedId])) {
            $data_reseptur = $session_reseptur[$encryptedId];
        } else {
            $message = Yii::t('fe', 'Data belum ditambahkan!');
            return DocoHelpers::responseTemplate(
                500,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan'),
                    'text' => $message,
                    'message' => $message,
                ]
            );
        }

        // change data from table form
        foreach ($data_reseptur as $key => $value) {
            if (isset($data_change['signa_edit_' . $value['session_key']])) {
                $data_reseptur[$key]['signa_reseptur'] = $data_change['signa_edit_' . $value['session_key']];
            }

            if (isset($data_change['qty_edit_' . $value['session_key']])) {
                $data_reseptur[$key]['qty_reseptur'] = $data_change['qty_edit_' . $value['session_key']];
            }
        }


        $data_insert_reseptur = [];
        $data_insert_reseptur['ruangan_id'] = $depo_tujuan;
        $data_insert_reseptur['pasien_id'] = isset($data_pasien['pasien_id']) ? $data_pasien['pasien_id'] : null;
        $data_insert_reseptur['pegawai_id'] = isset($data_pasien['pegawai_id']) ? $data_pasien['pegawai_id'] : null;
        $data_insert_reseptur['pendaftaran_id'] = DocoHelpers::decrypt($pendaftaran_id_encrypt);
        $data_insert_reseptur['tglreseptur'] = date('Y-m-d H:i:s');
        $data_insert_reseptur['ruanganreseptur_id'] = $ruangan_id;
        $data_insert_reseptur['diagnosa_id'] = $diagnosa_id;

        // data insert resepturdetail_t
        $check_isracikan = false;
        $data_insert_resepturdetails = [];
        foreach ($data_reseptur as $key => $value) {
            $additional = [
                'satuaninput_id' => $value['satuankecil_id'],
                'satuan_input' => $value['satuankecil_nama'],
                'satuankonversi_id' => $value['satuandefault_id'],
                'satuan_konversi' => $value['satuandefault_nama'],
                'harga_konversi' => isset($value['harga_konversi']) ? $value['harga_konversi'] : 0,
                'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 0,
            ];

            $hargasatuan_reseptur = !empty($value['harga_konversi']) ? $value['harga_konversi'] : 0;

            $data_insert_resepturdetail = [];
            $data_insert_resepturdetail['obatalkes_id'] = $value['obatalkes_id'];
            $data_insert_resepturdetail['racikan_id'] = $value['jenis_racikan'];
            $data_insert_resepturdetail['satuankecil_id'] = $value['satuankecil_id'];
            $data_insert_resepturdetail['reseptur_id'] = 'null';
            $data_insert_resepturdetail['r'] = !empty($value['rke']) ? 'r' : null;
            $data_insert_resepturdetail['rke'] = !empty($value['rke']) ? $value['rke'] : null;
            $data_insert_resepturdetail['qty_reseptur'] = $value['qty_reseptur'];
            $data_insert_resepturdetail['hargasatuan_reseptur'] = $value['hargasatuan_reseptur'];
            $data_insert_resepturdetail['iter'] = $iter;
            $data_insert_resepturdetail['signa'] = $value['signa_reseptur'];
            $data_insert_resepturdetail['qty_konversi'] = $value['total_konversi'];
            $data_insert_resepturdetail['additional_data'] = json_encode($additional);
            $data_insert_resepturdetail['etiket'] = isset($value['etiket']) ? $value['etiket'] : '';
            // rizal
            $data_insert_resepturdetail['harganetto_reseptur'] = @$value['harganetto_reseptur'];
            // check is racikan / non racikan
            if ($check_isracikan === false && $value['jenis_racikan'] == ResepturDetailForm::VC_RC) {
                $check_isracikan = true;
            }

            array_push($data_insert_resepturdetails, $data_insert_resepturdetail);
        }

        // define is racikan / non racikan
        $data_insert_reseptur['racikan_id'] = ($check_isracikan === true) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC;

        $data_send = [
            'data_reseptur' => $data_insert_reseptur,
            'data_resepturdetail' => $data_insert_resepturdetails,
        ];

        $response = $this->_restRajal->post('tra-pemeriksaan/create-reseptur', [
            'form_params' => $data_send
        ]);

        $response = json_decode($response->getBody(), true);
        if ($response['metadata']['status'] == 200) {
            $this->resetSessionReseptur($pendaftaran_id_encrypt);
        }
        return DocoHelpers::response($response);
    }

    public function actionGetDataRiwayatReseptur()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
        $ruangan_id = $this->_id_ruangan;
        $draw = $request->get('draw', 1);
        $data = [];

        $userIdentity = Yii::$app->session->get('user_identity');
        $isPerawat = false;
        if (isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $isPerawat = true;
        }

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-riwayat-reseptur?ruangan_id=' . $ruangan_id . '&pendaftaran_id=' . $pendaftaran_id);
            $body = json_decode($response->getBody(), True);
            $data_body = $body['response']['data'];

            $no = 0;
            foreach ($data_body as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['reseptur_id']);
                $no_reseptur = DocoHelpers::encrypt($value['noresep']);

                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;

                // define aksi
                $value['aksi'] = Html::button(
                    '<i class="fa fa-eye"></i>',
                    [
                        'class' => 'btn btn-info btn-xs view-riwayat-reseptur',
                        'action' => '/rajal/pemeriksaan/detail-riwayat-reseptur?id=' . $primaryKey . '&no_reseptur=' . $no_reseptur . '&type=1',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '70%',
                        'data-tooltip' => "tooltip",
                        'data-width' => '70%',
                        'data-original-title' => Yii::t('fe', 'Lihat'),
                        'data-placement' => 'right',
                    ]
                );
                if ($value['status_reseptur_id'] == '346' && $isPerawat == false) {
                    $value['aksi'] .= '&nbsp;';
                    $value['aksi'] .= Html::button(
                        '<i class="fa fa-pencil"></i>',
                        [
                            'class' => 'btn btn-dark-turquise btn-xs edit-riwayat-reseptur',
                            'action' => '/rajal/pemeriksaan/detail-riwayat-reseptur?id=' . $primaryKey . '&no_reseptur=' . $no_reseptur . '&type=2',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-tooltip' => "tooltip",
                            'data-width' => '90%',
                            'data-original-title' => Yii::t('fe', 'Ubah'),
                            'data-placement' => 'right',
                        ]
                    );
                }
                $value['aksi'] .= '&nbsp;';
                // $value['aksi'] .= Html::a(
                //     '<i class="fa fa-print"></i>',
                //     '/rajal/pemeriksaan/cetak-antrian-farmasi?reseptur_id='. $primaryKey,
                //     [
                //         'class' => 'btn btn-dodger-blue btn-xs cetakantrian-riwayat-reseptur',
                //         'data-tooltip' => "tooltip",
                //         'data-original-title' => Yii::t('fe', 'Cetak Antrian Farmasi'),
                //         'data-placement' => 'right',
                //         'target' => '_blank',
                //         'rel'=>'noopener',
                //     ]
                // );
                $value['aksi'] .= '&nbsp;';
                $value['aksi'] .= Html::a(
                    '<i class="fa fa-file-pdf-o"></i>',
                    '/rajal/pemeriksaan/export-pdf-reseptur?reseptur_id=' . $primaryKey . '&noresep=' . $no_reseptur . '&pendaftaran_id=' . $pendaftaran_id,
                    [
                        'class' => 'btn btn-crimson btn-xs cetak-riwayat-reseptur',
                        'data-tooltip' => "tooltip",
                        'data-placement' => 'right',
                        'data-original-title' => Yii::t('fe', 'Cetak Resep'),
                        'target' => '_blank',
                    ]
                );
                $value['tglreseptur'] = date('d M Y H:i:s', strtotime($value['tglreseptur']));
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    // $id = reseptur_id
    public function actionDetailRiwayatReseptur()
    {
        // Init action
        // type 1 => view
        // type 2 => update

        try {
            $request = Yii::$app->request;
            $title = \Yii::t('fe', 'Reseptur');
            $type = $request->get('type');
            $action = ($type == 1) ? \Yii::t('fe', 'Lihat') : \Yii::t('fe', 'Ubah');
            $reseptur_id = DocoHelpers::decrypt($request->get('id'));
            $no_reseptur = DocoHelpers::decrypt($request->get('no_reseptur'));
            // reset session data
            // $this->resetSessionReseptur();

            return $this->renderAjax('reseptur/__reseptur_riwayat_form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataDetailRiwayatReseptur()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        // Init action
        // type 1 => view
        // type 2 => update
        $type = $request->get('type');
        $reseptur_id = $request->get('reseptur_id');
        $state = $request->get('state', false);

        try {
            if ($type && $reseptur_id) {
                // get/set session
                if (isset($session['pemeriksaan_reseptur_db'])) {
                    $data_session_detail = $session['pemeriksaan_reseptur_db'];
                    if (isset($data_session_detail[$reseptur_id])) {
                        $datas = $data_session_detail[$reseptur_id];
                    } else {
                        $datas = $data_session_detail;
                    }
                }

                if ($state) {
                    $response = $this->_restRajal->get('tra-pemeriksaan/get-detail-riwayat-reseptur?reseptur_id=' . $reseptur_id);
                    $body = json_decode($response->getBody(), True);
                    $datas_temp = $body['response']['data'];
                    $datas = $datas_temp;
                    $data_session_detail[$reseptur_id] = $datas_temp;
                    $session->set('pemeriksaan_reseptur_db', $data_session_detail);
                } else {
                    if (isset($data_session_detail[$reseptur_id])) {
                        $datas = $data_session_detail[$reseptur_id];
                    } else {
                        $datas = $data_session_detail;
                        // $response = $this->_restRajal->get('tra-pemeriksaan/get-detail-riwayat-reseptur?reseptur_id='. $reseptur_id);
                        // $body = json_decode($response->getBody(), True);
                        // $datas_temp = $body['response']['data'];
                        // $datas = $datas_temp;
                        // $data_session_detail[$reseptur_id] = $datas_temp;
                        // $session->set('pemeriksaan_reseptur_db', $data_session_detail);
                    }
                }

                $data_tables = $this->generateTableDetailRiwayatReseptur($datas, $type, $reseptur_id);

                return DocoHelpers::response($data_tables);
            } else {
                return DocoHelpers::response([]);
                // return DocoHelpers::dataTabelsException('Data kosong');
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function generateTableDetailRiwayatReseptur($datas = [], $type = null, $reseptur_id = null)
    {
        $data_tables = [];
        $no = 0;
        if ($datas) {
            foreach ($datas as $data) {
                $no++;
                $data_table = [];
                $data_table['rowNum'] = $no;
                $data_table['resepturdetail_id'] = $data['resepturdetail_id'];
                $data_table['nama_racikan'] = $data['racikan_nama'];
                $data_table['rke'] = !empty($data['rke']) ? $data['rke'] : '-';
                $data_table['obatalkes_nama'] = $data['obatalkes_nama'];
                $data_table['signa_edit'] = isset($data['signa']) && !empty($data['signa']) ? (is_array($data['signa']) ? $data['signa']['text'] : $this->helper->jsonToArray($data['signa'], 'text')) : $data['signa_nama'];
                $data_table['qty_edit'] = $data['qty_reseptur'];
                $data_table['satuankecil_nama'] = $data['satuan_input'];
                $data_table['hargasatuan_reseptur'] = $data['hargajual_satuan'];
                $data_table['jumlah_harga'] = $data['hargajual_satuan'] * $data['qty_reseptur'];
                $data_table['etiket'] = isset($data['etiket']) ? $data['etiket'] : '';
                array_push($data_tables, $data_table);
            }
        }

        return $data_tables;
    }

    public function actionSaveDetailRiwayatReseptur()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';

        try {
            $data_change = $request->post();
            $reseptur_id = $data_change['reseptur_id'];
            if (isset($session['pemeriksaan_reseptur_db'])) {
                $session_reseptur_detail = $session['pemeriksaan_reseptur_db'];
                $resepturdetail_id = [];
                if (isset($session_reseptur_detail[$reseptur_id])) {
                    $data_resepturdetail = $session_reseptur_detail[$reseptur_id];
                    if (!empty($data_resepturdetail)) {
                        foreach ($data_resepturdetail as $key => $value) {
                            if (isset($data_change['signa_edit_' . $value['resepturdetail_id']]) && $data_change['signa_edit_' . $value['resepturdetail_id']] != $value['signa_id']) {
                                $data_resepturdetail[$key]['signa_id'] = $data_change['signa_edit_' . $value['resepturdetail_id']];
                            }
                            if (isset($data_change['qty_edit_' . $value['resepturdetail_id']]) && $data_change['qty_edit_' . $value['resepturdetail_id']] != $value['qty_reseptur']) {
                                $data_resepturdetail[$key]['qty_reseptur'] = $data_change['qty_edit_' . $value['resepturdetail_id']];
                            }
                            // $data_resepturdetail[$key]['hargajual_reseptur'] = $data_resepturdetail[$key]['qty_reseptur'];
                        }
                    }

                    $data_send = [
                        'reseptur_id' => $reseptur_id,
                        'data_resepturdetail' => $data_resepturdetail,
                    ];

                    $response = $this->_restRajal->post('tra-pemeriksaan/update-reseptur', [
                        'form_params' => $data_send
                    ]);

                    $response = json_decode($response->getBody(), true);
                    // dump($response['response']);die;
                    if ($response['metadata']['status'] == 200) {
                        // unset session
                        $this->resetSessionReseptur();

                        return DocoHelpers::responseTemplate(200, 'OK');
                    } else {
                        return DocoHelpers::response($response);
                    }
                }
            }
        } catch (RequestException $e) {
            // $response = json_decode($e->getResponse()->getBody(), true);
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionBatalSessionDetailReseptur()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $resepturdetail_id = $request->get('id');
            $reseptur_id = $request->get('reseptur_id');
            if (isset($session['pemeriksaan_reseptur_db'])) {
                $session_reseptur_detail = $session['pemeriksaan_reseptur_db'];
                if (isset($session_reseptur_detail[$reseptur_id])) {
                    foreach ($session_reseptur_detail[$reseptur_id] as $key => $value) {
                        if ($value['resepturdetail_id'] == $resepturdetail_id) {
                            unset($session_reseptur_detail[$reseptur_id][$key]);
                        }
                    }
                    $session->set('pemeriksaan_reseptur_db', $session_reseptur_detail);
                }
            } else {
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }

            return DocoHelpers::responseTemplate(
                200,
                Yii::t('fe', 'message_batal'),
                [],
                ['title' => Yii::t('fe', 'message_berhasil'), 'text' => Yii::t('fe', 'message_batal')]
            );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
    public function actionResetResepturSession($pendaftaran_id)
    {
        try {
            $this->resetSessionReseptur($pendaftaran_id);
            return DocoHelpers::response(['message' => 'Sukses']);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
    public function actionExportPdfReseptur()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
        $reseptur_id = DocoHelpers::decrypt($request->get('reseptur_id'));
        $noresep = DocoHelpers::decrypt($request->get('noresep'));
        $path = Yii::getAlias("@download") . "/pemeriksaan-reseptur-{$noresep}.pdf";
        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/export-pdf-reseptur?pendaftaran_id=' . $pendaftaran_id . '&reseptur_id=' . $reseptur_id, [
                'save_to' => $path
            ]);

            // $response = json_decode($response->getBody(), true);
            // dump($response['response']);die;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakAntrianFarmasi()
    {
        $request = Yii::$app->request;

        $reseptur_id = DocoHelpers::decrypt($request->get('reseptur_id'));

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/cetak-antrian-farmasi?reseptur_id=' . $reseptur_id);
            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $data_header = @$response['header'];
            $data_body = @$response['body'];
            $data_footer = @$response['footer'];

            return $this->render('reseptur/cetak_antrian_farmasi', get_defined_vars());
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function checkStok($depo_id, $obatalkes_id, $qty)
    {
        $responseCheck = $this->_restRajal->request('GET', 'allow/check-stok?ruangan_id=' . $depo_id . '&obatalkes_id=' . $obatalkes_id);
        $body = json_decode($responseCheck->getBody(), true);
        if (isset($body['response']['qty_tersedia'])) {
            $qty_available = $body['response']['qty_tersedia'];
            if ((int)$qty > $qty_available) {
                $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                $result['response']['text'] = Yii::t('fe', 'Stok tidak mencukupi');

                return DocoHelpers::response($result, 500);
            }
        } else {
            $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
            $result['response']['text'] = Yii::t('fe', 'Stok tidak mencukupi');

            return DocoHelpers::response($result, 500);
        }
    }

    public function actionAddTemplate($id)
    {
        $model = new ResepTempForm;

        return $this->renderAjax('reseptur/__reseptur_save_template', get_defined_vars());
    }

    public function actionSaveTemplate($id)
    {
        $model = new ResepTempForm;
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model->load($post);
            if ($model->validate()) {
                $pendaftaran_id_encrypt = $request->get('id');
                $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);

                $ruangan_id = $this->_id_ruangan;
                $ruangan_id_encrypt = DocoHelpers::encrypt($ruangan_id);
                $encryptedId = $pendaftaran_id_encrypt . $ruangan_id_encrypt;

                if (isset($session['pemeriksaan_reseptur'])) {
                    $session_reseptur = $session['pemeriksaan_reseptur'];
                }

                if (isset($session_reseptur[$encryptedId])) {
                    $data_reseptur = $session_reseptur[$encryptedId];
                } else {
                    $message = Yii::t('fe', 'Data belum ditambahkan!');
                    return DocoHelpers::responseTemplate(
                        500,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Peringatan'),
                            'text' => $message,
                            'message' => $message,
                        ]
                    );
                }

                foreach ($data_reseptur as $key => $value) {
                    if (isset($post['signa_edit_' . $value['session_key']])) {
                        $data_reseptur[$key]['signa_reseptur'] = $post['signa_edit_' . $value['session_key']];
                    }

                    if (isset($post['qty_edit_' . $value['session_key']])) {
                        $data_reseptur[$key]['qty_reseptur'] = $post['qty_edit_' . $value['session_key']];
                    }
                }

                $data_resep_temp = [];
                $data_resep_temp['dokter_id'] = $pegawai_id;
                $data_resep_temp['reseptemp_nama'] = $post['ResepTempForm']['nama_template'];

                $check_isracikan = false;
                $data_resep_temp_details = [];

                foreach ($data_reseptur as $key => $value) {
                    $additional_data = [
                        'satuaninput_id' => $value['satuankecil_id'],
                        'satuan_input' => $value['satuankecil_nama'],
                        'satuankonversi_id' => $value['satuandefault_id'],
                        'satuan_konversi' => $value['satuandefault_nama'],
                        'harga_konversi' => isset($value['harga_konversi']) ? $value['harga_konversi'] : 0,
                        'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 0,
                        'harga_jual' => isset($value['harga_jual']) ? $value['harga_jual'] : 0,
                        'etiket' => isset($value['etiket']) ? $value['etiket'] : '',
                    ];

                    $data_resep_temp_detail = [];
                    $data_resep_temp_detail['obatalkes_id'] = $value['obatalkes_id'];
                    $data_resep_temp_detail['racikan_id'] = $value['jenis_racikan'];
                    $data_resep_temp_detail['satuankecil_id'] = $value['satuankecil_id'];
                    $data_resep_temp_detail['rke'] = !empty($value['rke']) ? $value['rke'] : 'null';
                    $data_resep_temp_detail['qty'] = $value['qty_reseptur'];
                    // $data_resep_temp_detail['signa_id'] = isset($value['signa_reseptur']) && is_int($value['signa_reseptur']) ? $value['signa_reseptur'] : null;
                    $data_resep_temp_detail['signa'] = isset($value['signa_reseptur']) ? json_encode(['text' => $value['signa_reseptur']]) : null;
                    $data_resep_temp_detail['additional_data'] = json_encode($additional_data);
                    // check is racikan / non racikan
                    if ($check_isracikan === false && $value['jenis_racikan'] == ResepturDetailForm::VC_RC) {
                        $check_isracikan = true;
                    }

                    array_push($data_resep_temp_details, $data_resep_temp_detail);
                }

                $data_send = [
                    'data_template' => $data_resep_temp,
                    'data_template_detail' => $data_resep_temp_details,
                ];

                $response = $this->_restRajal->post('tra-pemeriksaan/save-template', [
                    'form_params' => $data_send
                ]);
                $response = json_decode($response->getBody(), true);

                if (Yii::$app->request->isAjax) {
                    return DocoHelpers::response($response);
                } else {
                    Yii::$app->session->setFlash('success', "Template Resep Berhasil di simpan.");
                    return $this->redirect(['/rajal/pemeriksaan/periksa?id=' . $id]);
                }
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, 'ResepTempForm');
            }
        } catch (RequestException $e) {
            $response = json_decode($e->getMessage(), true);
            return DocoHelpers::responseTemplate(
                500,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan'),
                    'text' => 'Terdapat kesalahan',
                    'message' => $e->getMessage(),
                ]
            );
        } catch (\Exception $e) {
            $response = json_decode($e->getMessage(), true);
            return DocoHelpers::responseTemplate(
                500,
                'Error',
                [],
                [
                    'title' => Yii::t('fe', 'Peringatan'),
                    'text' => 'Terdapat kesalahan',
                    'message' => $e->getMessage(),
                ]
            );
            // echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGenerateTemplate($idResep, $id, $depo_id)
    {
        // try {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get('pasien-pendaftaran-id-' . $id);
        $data_pasien = isset($cacheData) ? $cacheData : [];
        $id = DocoHelpers::decrypt($id);
        $penjamin_id = isset($data_pasien['penjamin_id']) ? $data_pasien['penjamin_id'] : null;
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        $response = $this->_restRajal->request('GET', 'allow/generate-template', [
            'query' => [
                'id' => $idResep,
                'ruangan_depo_id' => $depo_id,
                'penjamin_id' => $penjamin_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        if ($body['metadata']['status'] == 422) {
            $result['response']['title'] = Yii::t('fe', 'Terjadi Kesalahan !');
            $result['response']['text'] = Yii::t('fe', $body['response']['message']);
            return DocoHelpers::response($result, 422);
        }
        $response = $this->addSessionTemplate($id, $body['response'], $depo_id);

        if (!empty($response['response']['data'])) {
            if (isset($response['metadata']['status']) && $response['metadata']['status'] == 100) {
                $result['response']['title'] = $response['response']['title'];
                $result['response']['text'] = $response['response']['text'];
                $result['response']['message'] = $response['response']['message'];
            } else {
                $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                $result['response']['text'] = Yii::t('fe', 'Stok tidak mencukupi');
            }
        } else {
            if ($response['status'] == 100) {
                $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                $result['response']['text'] = $response['message'];
            } else {
                return DocoHelpers::response(['response' => ['message' => 'Data berhasil disimpan']]);
            }
        }

        return DocoHelpers::response($result, 422);

        // } catch (RequestException $e) {
        //     return DocoHelpers::dataTabelsException($e->getMessage());
        // } catch (\Exception $e) {
        //     return DocoHelpers::dataTabelsException($e->getMessage());
        // }
    }

    private function addSessionTemplate($pendaftaran_id, $data = null, $depo_id)
    {
        $session = Yii::$app->session;
        $pendaftaran_id = DocoHelpers::encrypt($pendaftaran_id);
        $ruangan_id = DocoHelpers::encrypt($this->_id_ruangan);
        $encryptedId = $pendaftaran_id . $ruangan_id;

        try {
            // get session
            $session_key = 1;
            if (isset($session['pemeriksaan_reseptur'])) {
                $session_reseptur = $session['pemeriksaan_reseptur'];
            }

            if (isset($session_reseptur[$encryptedId])) {
                foreach ($session_reseptur[$encryptedId] as $key => $value) {
                    $session_key = $value['session_key'];
                }

                $session_key++;
            }

            foreach ($data as $key => $value) {
                if (!empty($session_reseptur[$encryptedId])) {
                    foreach ($session_reseptur[$encryptedId] as $k => $v) {
                        $racikan_id = isset($value['racikan_id']) ? $value['racikan_id'] : 0;
                        $obatalkes_id = isset($value['obatalkes_id']) ? $value['obatalkes_id'] : '';
                        $rke = isset($value['rke']) ? $value['rke'] : '';
                        $jenis_racikan = ($racikan_id  == 1) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC;

                        if (($v['obatalkes_id'] == $obatalkes_id) && ($v['jenis_racikan'] == $jenis_racikan) && $v['rke'] == $rke) {
                            $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama!');
                            return [
                                'status' => 100,
                                'message' => $message,
                            ];
                        }
                    }
                }

                // if ($value['hargasatuan_reseptur'] == 0) {
                //     $title = Yii::t('fe', 'Tidak bisa menambahkan template!');
                //     $message = Yii::t('fe', 'Tidak bisa menambahkan obat dengan harga 0.');
                //     return DocoHelpers::responseTemplate(
                //         100,
                //         'Error',
                //         [$message],
                //         [
                //             'title' => $title,
                //             'text' => $message,
                //             'message' => $message,
                //         ]
                //     );
                // }

                $jenis_racikan = !empty($value['rke']) ? ResepturDetailForm::VC_RC : ResepturDetailForm::VC_NRC;
                $nama_racikan = !empty($value['rke']) ? 'Obat Racikan' : 'Obat Non Racikan';
                $rke = @$value['rke'];
                $signa_reseptur = isset($value['signa_reseptur']) ? $value['signa_reseptur'] : '';

                $session_reseptur[$encryptedId][] = [
                    'session_key' => $session_key,
                    'jenis_racikan' => $jenis_racikan,
                    'nama_racikan' => $nama_racikan,
                    'rke' => $rke,
                    'signa_reseptur' => $signa_reseptur,
                    'obatalkes_id' => isset($value['obatalkes_id']) ? $value['obatalkes_id'] : null,
                    'obatalkes_nama' => isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '',
                    'qty_reseptur' => isset($value['qty_reseptur']) ? $value['qty_reseptur'] : 0,
                    'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                    'satuankecil_nama' => isset($value['satuankecil_nama']) ? $value['satuankecil_nama'] : '',
                    'hargasatuan_reseptur' => isset($value['hargasatuan_reseptur']) ? $value['hargasatuan_reseptur'] : 0,
                    'harganetto_reseptur' => isset($value['harganetto_reseptur']) ? $value['harganetto_reseptur'] : 0,
                    'satuandefault_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : '',
                    'satuandefault_nama' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : null,
                    'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 0,
                    'total_konversi' => isset($value['nilai_konversi']) ? $value['qty_reseptur'] * $value['nilai_konversi'] : 0,
                    'harga_konversi' => isset($value['harga_konversi']) ? $value['harga_konversi'] : 0,
                    'harga_jual' => isset($value['harga_jual']) ? $value['harga_jual'] : 0,
                    'jumlah_harga' => isset($value['harga_konversi']) ? ($value['harga_konversi']) : 0,
                    'etiket' => isset($value['etiket']) ? $value['etiket'] : '',
                ];

                $session_key++;
            }

            $session->set('pemeriksaan_reseptur', $session_reseptur);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function setAttrReseptur($data)
    {
        $count_data_insert = 0;
        $data_reseptur = [];
        $data_reseptur['jenis_racikan'] = $data['jenis_racikan'];
        $data_reseptur['nama_racikan'] = !empty($data['rke']) ? 'Obat Racikan' : 'Obat Non racikan';
        $data_reseptur['rke'] = @$data['rke'];
        $data_reseptur['signa_reseptur'] = $data['signa_reseptur'];
        $data_reseptur['etiket'] = $data['etiket'];
        if (is_array($data['obatalkes_id'])) {
            foreach ($data['obatalkes_id'] as $key => $value) {
                $data_reseptur['obatalkes_id'][] = $value;
                $count_data_insert++;
            }
        } else {
            $data_reseptur['obatalkes_id'] = $data['obatalkes_id'];
        }

        if (is_array($data['qty_reseptur'])) {
            foreach ($data['qty_reseptur'] as $key => $value) {
                $data_reseptur['qty_reseptur'][] = $value;
            }
        } else {
            $data_reseptur['qty_reseptur'] = $data['qty_reseptur'];
        }

        if (isset($data['obatalkes_nama'])) {
            if (is_array($data['obatalkes_nama'])) {
                foreach ($data['obatalkes_nama'] as $key => $value) {
                    $data_reseptur['obatalkes_nama'][] = $value;
                }
            } else {
                $data_reseptur['obatalkes_nama'] = $data['obatalkes_nama'];
            }
        }

        if (is_array($data['satuankecil_id'])) {
            foreach ($data['satuankecil_id'] as $key => $value) {
                $data_reseptur['satuankecil_id'][] = $value;
            }
        } else {
            $data_reseptur['satuankecil_id'] = $data['satuankecil_id'];
        }
        if (is_array($data['satuankecil_nama'])) {
            foreach ($data['satuankecil_nama'] as $key => $value) {
                $data_reseptur['satuankecil_nama'][] = $value;
            }
        } else {
            $data_reseptur['satuankecil_nama'] = $data['satuankecil_nama'];
        }

        if (is_array($data['hargasatuan_reseptur'])) {
            foreach ($data['hargasatuan_reseptur'] as $key => $value) {
                $data_reseptur['hargasatuan_reseptur'][] = $value;
            }
        } else {
            $data_reseptur['hargasatuan_reseptur'] = $data['hargasatuan_reseptur'];
        }

        if (is_array($data['nilai_konversi'])) {
            foreach ($data['nilai_konversi'] as $key => $value) {
                $data_reseptur['nilai_konversi'][] = $value;
            }
        } else {
            $data_reseptur['nilai_konversi'] = $data['nilai_konversi'];
        }

        if (is_array($data['satuandefault_id'])) {
            foreach ($data['satuandefault_id'] as $key => $value) {
                $data_reseptur['satuandefault_id'][] = $value;
            }
        } else {
            $data_reseptur['satuandefault_id'] = $data['satuandefault_id'];
        }

        if (is_array($data['satuandefault_nama'])) {
            foreach ($data['satuandefault_nama'] as $key => $value) {
                $data_reseptur['satuandefault_nama'][] = $value;
            }
        } else {
            $data_reseptur['satuandefault_nama'] = $data['satuandefault_nama'];
        }

        if (is_array($data['harga_konversi'])) {
            foreach ($data['harga_konversi'] as $key => $value) {
                $data_reseptur['harga_konversi'][] = $value;
            }
        } else {
            $data_reseptur['harga_konversi'] = $data['harga_konversi'];
        }

        if (isset($data['harga_satuan'])) {
            if (is_array($data['harga_satuan'])) {
                foreach ($data['harga_satuan'] as $key => $value) {
                    $data_reseptur['harga_satuan'][] = $value;
                }
            } else {
                $data_reseptur['harga_satuan'] = $data['harga_satuan'];
            }
        } else {
            if (isset($data['harga'])) {
                if (is_array($data['harga'])) {
                    foreach ($data['harga'] as $key => $value) {
                        $data_reseptur['harga_satuan'][] = $value;
                    }
                } else {
                    $data_reseptur['harga_satuan'] = $data['harga'];
                }
            }
        }

        if (isset($data['harga_jual'])) {
            if (is_array($data['harga_jual'])) {
                foreach ($data['harga_jual'] as $key => $value) {
                    $data_reseptur['harga_jual'][] = $value;
                }
            } else {
                $data_reseptur['harga_jual'] = $data['harga_jual'];
            }
        } else {
            if (isset($data['harga'])) {
                if (is_array($data['harga'])) {
                    foreach ($data['harga'] as $key => $value) {
                        $data_reseptur['harga_jual'][] = $value;
                    }
                } else {
                    $data_reseptur['harga_jual'] = $data['harga'];
                }
            }
        }

        if (isset($data['harganetto'])) {
            if (is_array($data['harganetto'])) {
                foreach ($data['harganetto'] as $key => $value) {
                    $data_reseptur['harganetto_reseptur'][] = $value;
                }
            } else {
                $data_reseptur['harganetto_reseptur'] = $data['harganetto'];
            }
        } else {
            if (isset($data['harganetto_reseptur'])) {
                if (is_array($data['harganetto_reseptur'])) {
                    foreach ($data['harganetto_reseptur'] as $key => $value) {
                        $data_reseptur['harganetto_reseptur'][] = $value;
                    }
                } else {
                    $data_reseptur['harganetto_reseptur'] = $data['harganetto_reseptur'];
                }
            }
        }

        if (is_array($data['stok_tersedia'])) {
            foreach ($data['stok_tersedia'] as $key => $value) {
                $data_reseptur['stok_tersedia'][] = $value;
                // $data_reseptur['stok_tersedia'] = $data['stok_tersedia'];
                // $stok_tersedia = $data['stok_tersedia'];
                // $qty_reseptur = $data['qty_reseptur'];
                // $nilai_konversi = $data['nilai_konversi'];
                // $data_reseptur['stok_sisa'] = ($stok_tersedia-($qty_reseptur*$nilai_konversi));

            }
        } else {
            $data_reseptur['stok_tersedia'] = $data['stok_tersedia'];
            // $stok_tersedia = $data['stok_tersedia'];
            // $qty_reseptur = $data['qty_reseptur'];
            // $nilai_konversi = $data['nilai_konversi'];
            // $data_reseptur['stok_sisa'] = ($stok_tersedia-($qty_reseptur*$nilai_konversi));
        }

        return [
            'data' => $data_reseptur,
            'count_data_insert' => $count_data_insert
        ];
    }

    private function setSessionReseptur($count_data_insert, $session_reseptur, $encryptedId, $session_key, $data_reseptur)
    {
        $session = Yii::$app->session;
        $datas = [];
        $session_reseptur_db = [];
        if (!empty($session->get('pemeriksaan_reseptur_db'))) {
            $reseptur = $session->get('pemeriksaan_reseptur_db');
            if (!empty($reseptur[$encryptedId])) {
                $session_reseptur_db = $reseptur[$encryptedId];
            }
        }

        $sessionMerge = array_merge($session_reseptur, $session_reseptur_db);
        if ($data_reseptur['jenis_racikan'] == ResepturDetailForm::VC_RC) {
            for ($i = 0; $i < $count_data_insert; $i++) {
                if (!empty($session_reseptur[$encryptedId])) {
                    foreach ($session_reseptur[$encryptedId] as $key => $value) {
                        if (($value['obatalkes_id'] == $data_reseptur['obatalkes_id'][$i]) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan']) && $value['rke'] == $data_reseptur['rke']) {
                            $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama!');
                            return [
                                'status' => 100,
                                'message' => $message,
                            ];
                        }
                    }
                }

                $sessionMerge[$encryptedId][] = [
                    'session_key' => $session_key,
                    'resepturdetail_id' => '',
                    'jenis_racikan' => $data_reseptur['jenis_racikan'],
                    'nama_racikan' => $data_reseptur['nama_racikan'],
                    'rke' => $data_reseptur['rke'],
                    'signa_reseptur' => $data_reseptur['signa_reseptur'],
                    'obatalkes_id' => $data_reseptur['obatalkes_id'][$i],
                    'qty_reseptur' => $data_reseptur['qty_reseptur'][$i],
                    'satuankecil_id' => $data_reseptur['satuankecil_id'][$i],
                    'satuankecil_nama' => $data_reseptur['satuankecil_nama'][$i],
                    'obatalkes_nama' => $data_reseptur['obatalkes_nama'][$i],
                    'satuandefault_id' => $data_reseptur['satuandefault_id'][$i],
                    'satuandefault_nama' => $data_reseptur['satuandefault_nama'][$i],
                    'hargasatuan_reseptur' => $data_reseptur['hargasatuan_reseptur'][$i],
                    'nilai_konversi' => $data_reseptur['nilai_konversi'][$i],
                    'total_konversi' => ($data_reseptur['qty_reseptur'][$i]) * ($data_reseptur['nilai_konversi'][$i]),
                    'harga_konversi' => ($data_reseptur['hargasatuan_reseptur'][$i] * $data_reseptur['qty_reseptur'][$i]),
                    'harganetto_reseptur' => $data_reseptur['harganetto_reseptur'][$i],
                    'harga_jual' => ($data_reseptur['hargasatuan_reseptur'][$i] * $data_reseptur['qty_reseptur'][$i]),
                    'jumlah_harga' => ($data_reseptur['qty_reseptur'][$i] * $data_reseptur['hargasatuan_reseptur'][$i]),
                    'etiket' => $data_reseptur['etiket'],
                    'stok_tersedia' => $data_reseptur['stok_tersedia'][$i],
                ];

                $session_key++;
            }

            if (empty($session_reseptur)) {
                if (!empty($sessionMerge[$encryptedId])) {
                    foreach ($sessionMerge[$encryptedId] as $key => $value) {
                        $arrObat[] = $value['obatalkes_id'];
                    }

                    $cekValid = $this->hasDuplicate($arrObat);
                    if (!$cekValid) {
                        $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama!');
                        return [
                            'status' => 100,
                            'message' => $message,
                        ];
                    } else {
                        $session->set('pemeriksaan_reseptur', $sessionMerge);
                        $session->set('pemeriksaan_reseptur_db', []);
                    }
                }
            } else {
                $session->set('pemeriksaan_reseptur', $sessionMerge);
                $session->set('pemeriksaan_reseptur_db', []);
            }
        } else {
            if (isset($session_reseptur[$encryptedId])) {
                foreach ($session_reseptur[$encryptedId] as $key => $value) {
                    if (($value['obatalkes_id'] == $data_reseptur['obatalkes_id']) && ($value['jenis_racikan'] == $data_reseptur['jenis_racikan'])) {

                        $message = Yii::t('fe', 'Tidak bisa menambahkan obat yang sama!');
                        return [
                            'status' => 100,
                            'message' => $message,
                        ];
                    }
                }
            }

            $harga_konversi = $data_reseptur['harga_konversi'] * $data_reseptur['qty_reseptur'];
            $sessionMerge[$encryptedId][] = [
                'session_key' => $session_key,
                'resepturdetail_id' => '',
                'jenis_racikan' => $data_reseptur['jenis_racikan'],
                'nama_racikan' => $data_reseptur['nama_racikan'],
                'rke' => $data_reseptur['rke'],
                'signa_reseptur' => $data_reseptur['signa_reseptur'],
                'obatalkes_id' => $data_reseptur['obatalkes_id'],
                'obatalkes_nama' => $data_reseptur['obatalkes_nama'],
                'qty_reseptur' => $data_reseptur['qty_reseptur'],
                'satuankecil_id' => $data_reseptur['satuankecil_id'],
                'satuankecil_nama' => $data_reseptur['satuankecil_nama'],
                'satuandefault_id' => $data_reseptur['satuandefault_id'],
                'satuandefault_nama' => $data_reseptur['satuandefault_nama'],
                'hargasatuan_reseptur' => $data_reseptur['harga_konversi'],
                'nilai_konversi' => $data_reseptur['nilai_konversi'],
                'total_konversi' => $data_reseptur['qty_reseptur'] * $data_reseptur['nilai_konversi'],
                'harga_konversi' => $harga_konversi,
                'harganetto_reseptur' => $data_reseptur['harganetto_reseptur'],
                'harga_jual' => $harga_konversi,
                'jumlah_harga' => $harga_konversi,
                'etiket' => $data_reseptur['etiket'],
                'stok_tersedia' => $data_reseptur['stok_tersedia'],
            ];

            $session->set('pemeriksaan_reseptur', $sessionMerge);
            $session->set('pemeriksaan_reseptur_db', []);
        }
    }

    private function hasDuplicate($array)
    {
        $defarray = array();
        $filterarray = array();
        foreach ($array as $val) {
            if (isset($defarray[$val])) {
                $filterarray[] = $val;
            }
            $defarray[$val] = $val;
        }

        return !empty($filterarray) ? false : true;
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

            return $this->helper->guzzleExec($this->_restRajal, [
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
            return $this->helper->guzzleExec($this->_restRajal, [
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
        return $this->guzzleExec($this->_restRajal, [
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
