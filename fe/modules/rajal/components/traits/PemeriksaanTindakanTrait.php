<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-14 09:44:13
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-20 13:35:19
 * @Description: trait pemeriksaan untuk tab tindakan
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

use app\modules\rajal\models\TindakanPelayananForm;
use app\modules\rajal\models\TindakankomponenForm;
use app\modules\rajal\models\TindakanBmhpForm;
use app\modules\rajal\models\ObatAlkesPasienForm;

trait PemeriksaanTindakanTrait 
{
    /*==============================================
    =            Section tindakan            =
    ==============================================*/

    public function actionTindakan()
    {
        // Get request
        $request = Yii::$app->request;
        $pendaftaran_id_encrypt = $request->get('id');
        $pendaftaran_id = $request->get('id') ? DocoHelpers::decrypt($request->get('id')) : null;
        $kelaspelayanan_id = $request->get('kelaspelayanan_id') ? DocoHelpers::decrypt($request->get('kelaspelayanan_id')) : null;
        $pasien_id = $request->get('pasien_id') ? DocoHelpers::decrypt($request->get('pasien_id')) : null;
        $no_pendaftaran = isset($this->_data_pasien['no_pendaftaran']) ? $this->_data_pasien['no_pendaftaran'] : 0;
        $penjamin_id = isset($this->_data_pasien['penjamin_id']) ? $this->_data_pasien['penjamin_id'] : null;

        // Declare model
        $modelTindakanBmhp = new TindakanBmhpForm;
        $modelTindakanPelayanan = new TindakanPelayananForm;
        $modelTindakanKomponen = new TindakankomponenForm;
        $modelBmhp = new ObatAlkesPasienForm;

        if (!empty($request->post())) {
            // Assign
            $post = $request->post();
            $post['pendaftaran_id'] = $pendaftaran_id;
            $response = $this->_restRajal->post('tindakan-bmhp/save-tindakan-bmhp', [
                'query' => ['pendaftaran_id'=>$pendaftaran_id],
                'form_params' => $post
            ]);

            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } else {

            $req_bundle_data = $this->_restRajal->get('tindakan-bmhp/get-bundle-data-tindakan-bmhp', [
                'query' => [
                    'no_pendaftaran' => $no_pendaftaran
                ]
            ]);
            $res_bundle_data = json_decode($req_bundle_data->getBody(), true);
            $bundle_data = $res_bundle_data['response'];
            $ruangan_depo = $bundle_data['ruangan_depo_nama'];
            
            // Get list data
            $data_tindakanruangan = $bundle_data['data_tindakanruangan'];
            $data_paket = $bundle_data['data_paketruangan'];
            $data_dokter = $bundle_data['data_dokter'];
            $data_perawat = $bundle_data['data_perawat'];
            $data_tindakanbmhp = $bundle_data['data_tindakanbmhp'];
            $data_group_obat = $bundle_data['data_group_obat'];
            $default_depo = $bundle_data['default_depo'];
            $data_group_obat = ArrayHelper::map($data_group_obat, 'lookup_id', 'lookup_value');
            
            $data_pasien = $this->_data_pasien;
            $ruangan_id = $this->_id_ruangan;
            $instalasi_id = $this->_instalasi_id;

            $session = Yii::$app->session;

            $session->set('tindakan', $data_tindakanruangan);
            $session->set('paket', $data_paket);
            // Set default value tindakan
            $modelTindakanBmhp->dokterpenanggungjawab_id = isset($data_pasien['pegawai_id']) ? $data_pasien['pegawai_id'] : null;
            $status_update = $this->_statusPeriksa;

            $map_perawat = [];
            if(count($data_perawat)>0){
                $map_perawat = ArrayHelper::map($data_perawat,'pegawai_id','nama_pegawai');
            }
            $userIdentity = Yii::$app->session->get('user_identity');
            $active_workspace = Yii::$app->session->get('active_workspace');
            $ruangan_nama = $active_workspace['ruangan_name'];
            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
                if(array_key_exists($userIdentity['id_pegawai'], $map_perawat)){
                    $modelTindakanBmhp->perawat1_id = $userIdentity['id_pegawai'];
                }
            }
            
            // Return
            return $this->renderAjax('tindakan/__tindakan', [
                'pendaftaran_id' => $pendaftaran_id_encrypt,
                'pasien_id' => $pasien_id,
                'modelTindakanBmhp' => $modelTindakanBmhp,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelBmhp' => $modelBmhp,
                'data_tindakanruangan' => $data_tindakanruangan,
                'data_tindakanbmhp' => $data_tindakanbmhp,
                'data_dokter' => $data_dokter,
                'data_perawat' => $data_perawat,
                'data_pasien' => $data_pasien,
                'ruangan_id' => $ruangan_id,
                'instalasi_id' => $instalasi_id,
                'status_update' => $status_update,
                'penjamin_id' => $penjamin_id,
                'data_group_obat' => $data_group_obat,
                'ruangan_nama' => $ruangan_nama,
                'default_depo' => $default_depo,
                'ruangan_depo' => $ruangan_depo,
                'kelompokpegawai_id' => $userIdentity['kelompokpegawai_id']
            ]);
        }
    }

    private function addSessionTindakan($data = null, $pendaftaran_id, $pasien_id)
    {
        $session = Yii::$app->session;
        $pendaftaran_id_encrypt = $pendaftaran_id;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        try {
            // get/set session
            if (isset($session['pemeriksaan_tindakan'])) {
                $session_tindakan = $session['pemeriksaan_tindakan'];
            }

            if (isset($session_tindakan[$pendaftaran_id_encrypt])){
                foreach ($session_tindakan[$pendaftaran_id_encrypt] as $key => $value) {
                    // prevent data sama
                    if (($value['daftartindakan_id'] == $data['daftartindakan_id'])){
                        return [
                            'status' => 500,
                            'message' => 'Terdapat data obat yang sama!',
                        ];
                    }
                }
            }

            $session_tindakan[$pendaftaran_id_encrypt][] = [
                'daftartindakan_id' => $data['daftartindakan_id'],
                'daftartindakan_nama' => $data['daftartindakan_nama'],
                'tgl_tindakan' => $data['tgl_tindakan'],
                'qty_tindakan' => $data['qty_tindakan'],
                'satuan_tindakan' => $data['satuan_tindakan'],
                'cyto_tindakan' => $data['cyto_tindakan'],
                'tarif_satuan' => $data['tarif_satuan'],
                'tarifcyto_tindakan' => $data['tarifcyto_tindakan'],
                'tarif_tindakan' => $data['tarif_tindakan'],
                'dokterpenanggungjawab_id' => $data['dokterpenanggungjawab_id'],
                'dokterdelegasi_id' => $data['dokterdelegasi_id'],
                'perawat1_id' => $data['perawat1_id'],
                'perawat2_id' => $data['perawat2_id'],
                'pasien_id' => $pasien_id,
                'pendaftaran_id' => $pendaftaran_id,
            ];
            $session->set('pemeriksaan_tindakan', $session_tindakan);

            return [
                'status' => 200,
                'message' => 'OK',
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionBatalSessionTindakan()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $pendaftaran_id = $request->get('pendaftaran_id');
            $daftartindakan_id = $request->get('daftartindakan_id');

            if (isset($session['pemeriksaan_tindakan'])) {
                $session_tindakan = $session['pemeriksaan_tindakan'];
                if (isset($session_tindakan[$pendaftaran_id])){
                    foreach ($session_tindakan[$pendaftaran_id] as $key => $value) {
                        if ($value['daftartindakan_id'] == $daftartindakan_id){
                            unset($session_tindakan[$pendaftaran_id][$key]);
                        }
                    }
                    $session->set('pemeriksaan_tindakan', $session_tindakan);
                }
            }else{
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }
            
            return DocoHelpers::responseTemplate(
                    200, 
                    Yii::t('fe', 'message_batal'), 
                    [], 
                    ['title' => 'Berhasil!', 'text' => Yii::t('fe', 'message_batal')]
                );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataTindakanSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');

        try {
            // get/set session
            if (isset($session['pemeriksaan_tindakan'][$pendaftaran_id])) {
                $temp_session = $session['pemeriksaan_tindakan'][$pendaftaran_id];

                $data_tables = [];
                foreach ($temp_session as $key => $value) {
                    $data_table['tgl_tindakan'] = $value['tgl_tindakan'];
                    $data_table['daftartindakan_id'] = $value['daftartindakan_id'];
                    $data_table['daftartindakan_nama'] = $value['daftartindakan_nama'];
                    $data_table['qty_tindakan'] = $value['qty_tindakan'];
                    $data_table['tarif_satuan'] = $value['tarif_satuan'];
                    $data_table['tarifcyto_tindakan'] = $value['tarifcyto_tindakan'];
                    $data_table['tarif_tindakan'] = $value['tarif_tindakan'];
                    $data_table['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-indian-red btn-xs delete-tindakan',
                            'action' => '/rajal/pemeriksaan/batal-session-tindakan?pendaftaran_id='. $pendaftaran_id .'&daftartindakan_id='. $value['daftartindakan_id'],
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                            'data-popup' => "tooltip",
                            'data-placement' => 'bottom',
                            'data-original-title' => Yii::t('fe', 'Batal'),
                        ]
                    );

                    array_push($data_tables, $data_table);
                }

                $data_tables = $return = [
                    'data' => $data_tables,
                    'draw' => $request->post('draw'),
                    'recordsTotal' => count($data_tables),
                    'recordsFiltered' => count($data_tables)
                ];

                return DocoHelpers::response($data_tables);
            } else {
                return DocoHelpers::dataTabelsException('');
            }

        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetTindakanDetail($id = null, $penjamin_id = null, $kelaspelayanan_id = null, $tipe = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($id == null) {
            // Return
            return [
                'status' => 500,
                'message' => 'Tindakan tidak ditemukan',
            ];
        }

        try {
            if ($tipe == 'tindakan') {
                $response_tindakan = $this->_restRajal->get('tra-pemeriksaan/get-tindakan-ruangan?ruanganId='.$this->_id_ruangan.'&kelasPelayananId='.$kelaspelayanan_id.'&id='.$id.'&penjaminId='.$penjamin_id);
            } else {
                $response_tindakan = $this->_restRajal->get('tra-pemeriksaan/get-paket-ruangan?ruanganId='.$this->_id_ruangan.'&kelasPelayananId='.$kelaspelayanan_id.'&id='.$id.'&penjaminId='.$penjamin_id);
            }
            
            // Body
            $body = json_decode($response_tindakan->getBody(), true);
            $data = $body['response'];
            
            // Return
            return [
                'status' => 200,
                'data' => $data,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetTindakanPaketSession()
    {
        // change to private API
        $session = Yii::$app->session;

        $data['tindakan'] = isset($session['tindakan']) ? $session['tindakan'] : [];
        $data['paket'] = isset($session['paket']) ? $session['paket'] : [];
        return json_encode([
            'response' => $data
        ]);
    }
    /*=====  End of Section tindakan  ======*/
    

    /*============================================
    =            Section bmhp - alkes            =
    ============================================*/
    
    public function actionGetDataBmhpSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');

        try {
            // get/set session
            if (isset($session['pemeriksaan_bmhpalkes'][$pendaftaran_id])) {
                $temp_session = $session['pemeriksaan_bmhpalkes'][$pendaftaran_id];

                $data_tables = [];
                foreach ($temp_session as $key => $value) {
                    $data_table['tglpelayanan'] = $value['tglpelayanan'];
                    $data_table['obatalkes_nama'] = $value['obatalkes_nama'];
                    $data_table['hargasatuan_oa'] = $value['hargasatuan_oa'];
                    $data_table['qty_oa'] = $value['qty_oa'];
                    $data_table['hargajual_oa'] = $value['hargajual_oa'];
                    $data_table['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-indian-red btn-xs delete-bmhpalkes',
                            'action' => '/rajal/pemeriksaan/batal-session-bmhpalkes?pendaftaran_id='. $pendaftaran_id .'&obatalkes_id='. $value['obatalkes_id']. '&daftartindakan_id='. $value['daftartindakan_id'],
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                            'data-popup' => "tooltip",
                            'data-placement' => 'bottom',
                            'data-original-title' => Yii::t('fe', 'Batal'),
                        ]
                    );

                    array_push($data_tables, $data_table);
                }

                $data_tables = $return = [
                    'data' => $data_tables,
                    'draw' => $request->post('draw'),
                    'recordsTotal' => count($data_tables),
                    'recordsFiltered' => count($data_tables)
                ];

                return DocoHelpers::response($data_tables);
            } else {
                return DocoHelpers::dataTabelsException('');
            }

        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function addSessionBmhpalkes($data = null, $pendaftaran_id, $pasien_id)
    {
        $session = Yii::$app->session;
        $pendaftaran_id_encrypt = $pendaftaran_id;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);

        try {
            // get/set session
            if (isset($session['pemeriksaan_bmhpalkes'])) {
                $session_bmhpalkes = $session['pemeriksaan_bmhpalkes'];
            }

            if (isset($session_bmhpalkes[$pendaftaran_id_encrypt])){
                foreach ($session_bmhpalkes[$pendaftaran_id_encrypt] as $key => $value) {
                    // prevent data sama
                    if (($value['obatalkes_id'] == $data['obatalkes_id'])){
                        return [
                            'status' => 500,
                            'message' => 'Terdapat data obat yang sama!',
                        ];
                    }
                }
            }

            $harga_jual = $data['hargasatuan_oa'] * $data['qty_oa'];

            $session_bmhpalkes[$pendaftaran_id_encrypt][] = [
                'obatalkes_nama' => $data['obatalkes_nama'],
                'ruangan_id' => $this->_id_ruangan,
                'carabayar_id' => $this->_data_pasien['carabayar_id'],
                'pegawai_id' => $this->_pegawai_id,
                'daftartindakan_id' => $data['daftartindakan_id'],
                'pendaftaran_id' => $pendaftaran_id,
                'obatalkes_id' => $data['obatalkes_id'],
                'pasien_id' => $this->_pasien_id,
                'penjamin_id' => $this->_data_pasien['penjamin_id'],
                'kelaspelayanan_id' => $this->_kelaspelayanan_id,
                'tglpelayanan' => date('Y-m-d H:i:s'),
                'qty_oa' => $data['qty_oa'],
                'hargasatuan_oa' => $data['hargasatuan_oa'],
                'hargajual_oa' => $harga_jual,
            ];
            $session->set('pemeriksaan_bmhpalkes', $session_bmhpalkes);

            return [
                'status' => 200,
                'message' => 'OK',
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionBatalSessionBmhpalkes()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $errors = 'Terdapat kesalahan';

        try {
            $pendaftaran_id = $request->get('pendaftaran_id');
            $daftartindakan_id = $request->get('daftartindakan_id');
            $obatalkes_id = $request->get('obatalkes_id');

            if (isset($session['pemeriksaan_bmhpalkes'])) {
                $session_bmhpalkes = $session['pemeriksaan_bmhpalkes'];
                if (isset($session_bmhpalkes[$pendaftaran_id])){
                    foreach ($session_bmhpalkes[$pendaftaran_id] as $key => $value) {
                        if (($value['daftartindakan_id'] == $daftartindakan_id) && $value['obatalkes_id'] == $obatalkes_id){
                            unset($session_bmhpalkes[$pendaftaran_id][$key]);
                        }
                    }
                    $session->set('pemeriksaan_bmhpalkes', $session_bmhpalkes);
                }
            }else{
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }
            
            return DocoHelpers::responseTemplate(
                    200, 
                    Yii::t('fe', 'message_batal'), 
                    [], 
                    ['title' => 'Berhasil!', 'text' => Yii::t('fe', 'message_batal')]
                );
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetObatalkesDetail($obatalkes_id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!$obatalkes_id){
            return [
                'status' => 500,
                'message' => 'Tindakan tidak ditemukan',
            ];
        }

        try {
            $response_obatalkes = $this->_restRajal->get('allow/allow-get-obatalkes?obatalkes_id='. $obatalkes_id);
            $body_obatalkes = json_decode($response_obatalkes->getBody(), true);
            $data_obatalkes = $body_obatalkes['response']['data_obatalkes'];
            $data_konfig = $body_obatalkes['response']['data_konfig'];

            $konfig_harga = isset($data_konfig['hargaygdigunakan']) ? $data_konfig['hargaygdigunakan'] : 'AVERAGE';
            if ($konfig_harga == 'MAX') {
                $harga_satuan = $data_obatalkes['hargamaksimum'];
            } elseif ($konfig_harga == 'MIN') {
                $harga_satuan = $data_obatalkes['hargaminimum'];
            } elseif ($konfig_harga == 'AVERAGE') {
                $harga_satuan = $data_obatalkes['hargaratarata'];
            }

            $data_return = $data_obatalkes;
            $data_return['harga_satuan'] = $harga_satuan;

            return [
                'status' => 200,
                'data' => $data_return,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }
    /*=====  End of bmhp - alkes  ======*/
    

    public function actionSaveSessionTindakanAll()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $errors = 'Terdapat kesalahan';

        try {
            $pendaftaran_id_encrypt = $request->get('pendaftaran_id', null);
            $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_encrypt);
            $modelTindakanPelayanan = new TindakanPelayananForm;

            // get/set session
            if (isset($session['pemeriksaan_tindakan'][$pendaftaran_id_encrypt])) {
                $session_tindakan = $session['pemeriksaan_tindakan'][$pendaftaran_id_encrypt];
            }else{
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }

            // variable sent data
            $data_sent = [];

            // session bmhp alkes
            $data_tindakan = [];
            foreach ($session_tindakan as $key => $value) {
                // data insert
                $data_tindakan_temp = [];
                $data_tindakan_temp['kelaspelayanan_id'] = $this->_kelaspelayanan_id;
                $data_tindakan_temp['pasien_id'] = $this->_pasien_id;
                $data_tindakan_temp['instalasi_id'] = $this->_instalasi_id;
                $data_tindakan_temp['daftartindakan_id'] = $value['daftartindakan_id'];
                $data_tindakan_temp['carabayar_id'] = $this->_data_pasien['carabayar_id'];
                $data_tindakan_temp['pendaftaran_id'] = $pendaftaran_id;
                $data_tindakan_temp['jeniskasuspenyakit_id'] = $this->_data_pasien['jeniskasuspenyakit_id'];
                $data_tindakan_temp['ruangan_id'] = $this->_id_ruangan;
                $data_tindakan_temp['penjamin_id'] = $this->_data_pasien['penjamin_id'];
                $data_tindakan_temp['tgl_tindakan'] = $value['tgl_tindakan'];
                $data_tindakan_temp['qty_tindakan'] = $value['qty_tindakan'];
                $data_tindakan_temp['satuan_tindakan'] = $value['satuan_tindakan'];
                $data_tindakan_temp['cyto_tindakan'] = $value['cyto_tindakan'];
                $data_tindakan_temp['tarif_satuan'] = $value['tarif_satuan'];
                $data_tindakan_temp['tarifcyto_tindakan'] = $value['tarifcyto_tindakan'];
                $data_tindakan_temp['tarif_tindakan'] = $value['tarif_tindakan'];
                $data_tindakan_temp['dokterpenanggungjawab_id'] = $value['dokterpenanggungjawab_id'];
                $data_tindakan_temp['dokterdelegasi_id'] = $value['dokterdelegasi_id'];
                $data_tindakan_temp['perawat1_id'] = $value['perawat1_id'];
                $data_tindakan_temp['perawat2_id'] = $value['perawat2_id'];

                $data_tindakan[] = $data_tindakan_temp;
            }

            // session tindakan komponen
            $data_komponen = [];

            // session bmhp alkes
            $data_bmhpalkes = [];
            if (isset($session['pemeriksaan_bmhpalkes'][$pendaftaran_id_encrypt])) {
                $session_bmhpalkes = $session['pemeriksaan_bmhpalkes'][$pendaftaran_id_encrypt];

                foreach ($session_bmhpalkes as $key => $value) {
                    // data insert
                    $data_bmhpalkes_temp = [];
                    $data_bmhpalkes_temp['ruangan_id'] = $value['ruangan_id'];
                    $data_bmhpalkes_temp['carabayar_id'] = $value['carabayar_id'];
                    $data_bmhpalkes_temp['pegawai_id'] = $value['pegawai_id'];
                    $data_bmhpalkes_temp['daftartindakan_id'] = $value['daftartindakan_id'];
                    $data_bmhpalkes_temp['pendaftaran_id'] = $value['pendaftaran_id'];
                    $data_bmhpalkes_temp['obatalkes_id'] = $value['obatalkes_id'];
                    $data_bmhpalkes_temp['pasien_id'] = $value['pasien_id'];
                    $data_bmhpalkes_temp['penjamin_id'] = $value['penjamin_id'];
                    $data_bmhpalkes_temp['kelaspelayanan_id'] = $value['kelaspelayanan_id'];
                    $data_bmhpalkes_temp['tglpelayanan'] = $value['tglpelayanan'];
                    $data_bmhpalkes_temp['qty_oa'] = $value['qty_oa'];
                    $data_bmhpalkes_temp['hargasatuan_oa'] = $value['hargasatuan_oa'];
                    $data_bmhpalkes_temp['hargajual_oa'] = $value['hargajual_oa'];

                    $data_bmhpalkes[] = $data_bmhpalkes_temp;
                }
            }

            $data_sent['tindakan_pelayanan'] = $data_tindakan;
            $data_sent['tindakan_komponen'] = $data_komponen;
            $data_sent['bmhp_alkes'] = $data_bmhpalkes;
            $data_sent['kelaspelayanan_id'] = $this->_kelaspelayanan_id;

            $response = $this->_restRajal->post('tra-pemeriksaan/create-tindakan', [
                    'form_params' => $data_sent
                ]);
            $response = json_decode($response->getBody(), true);

            if ($response['metadata']['status'] == 200){
                // unset session tindakan
                if (isset($session['pemeriksaan_tindakan'][$pendaftaran_id_encrypt])) {
                    $temp_session = $session['pemeriksaan_tindakan'];
                    if (isset($temp_session[$pendaftaran_id_encrypt])){
                        unset($temp_session[$pendaftaran_id_encrypt]);
                    }
                    $session->set('pemeriksaan_tindakan', $temp_session);
                }

                // unset session bmhp alkes
                if (isset($session['pemeriksaan_bmhpalkes'][$pendaftaran_id_encrypt])) {
                    $temp_session = $session['pemeriksaan_bmhpalkes'];
                    if (isset($temp_session[$pendaftaran_id_encrypt])){
                        unset($temp_session[$pendaftaran_id_encrypt]);
                    }
                    $session->set('pemeriksaan_bmhpalkes', $temp_session);
                }

                return DocoHelpers::responseTemplate(200, 'OK');
            }else{
                return DocoHelpers::responseTemplate(500, 'Error', $errors);
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Hapus session
    public function actionHapusSession()
    {
        // Try catch
        try {
            // Declare session
            $session = Yii::$app->session;

            // Get session
            $sessionTindakan = $session['pemeriksaan_tindakan'];
            $sessionBmhp = $session['pemeriksaan_bmhpalkes'];

            // Unset session
            unset($sessionTindakan);
            unset($sessionBmhp);
            
            // Return
            return json_encode(['data' => $session, 'message' => Yii::t('fe', 'Sesi berhasil dihapus')]);
        } catch (RequestException $e) {
            // Error message
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Eror message
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    // Get tindakan
    public function actionGetTindakan()
    {
        // Try catch
        try {
            // Get request
            $request = Yii::$app->request;

            // Format json
            Yii::$app->response->format = Response::FORMAT_JSON;

            // Get pendaftaran id
            $pendaftaranId = DocoHelpers::decrypt($request->get('id', null));

            // Get tindakan
            $response = $this->_restRajal->get('tra-pemeriksaan/get-tindakan-by-pendaftaran?id='.$pendaftaranId);
            $response = json_decode($response->getBody(), true);
            $data_pasien = $response["response"];

            // Start number and data
            $no = 0;
            $data = [];

            // Check body response
            if (!empty($body['response'])) {
                // Loop
                foreach ($body['response'] as $key => $value) {
                    // Manage data for datatables
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['tindakanpelayanan_id']);
                    $value['row'] = $no;
                    $data[$key] = $value;
                }

                // Assign result
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                // Return result
                return $result;
            }
            else {
                // Assign result
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                // Return result
                return $result;
            }
        } catch (RequestException $e) {
            // Error message
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Eror message
            return DocoHelpers::response(['message' => $e->getMessage()], 500);

        }
    }

    public function actionCetakTindakan($id,$ruangan_id = null)
    {
        $id = DocoHelpers::decrypt($id);
        if($ruangan_id == null){
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $no_pendaftaran = isset($this->_data_pasien['no_pendaftaran']) ? $this->_data_pasien['no_pendaftaran'] : 0;
        $path = Yii::getAlias("@download") . "/cetak-tindakanbmhp.pdf";
        try {
            $response = $this->_restRajal->get('tindakan-bmhp/cetak-tindakan',[
                'save_to' => $path,
                'query' => [
                    'no_pendaftaran' => $no_pendaftaran,
                    'ruangan_id' => $ruangan_id,
                ],
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetObatAlkesByJenis($jenis, $penjamin_id)
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $keyword = $request->get('q', null);
        $page = $request->get('page', 1);
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $limit = 11;
        $kelaspelayanan_id = $this->_kelaspelayanan_id;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $restApotek = Yii::$app->docoRest->apotek;

        $userIdentity = Yii::$app->session->get('user_identity');
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN)
        {
            $jenis = DocoConstants::GOUP_ALKES;
        }
        
        $params = [
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'group_jenisobat' => $jenis,
            'page' => $page,
            'keyword' => $keyword,
        ];
        
        $response = $restApotek->get('allow/get-list-stok-apotek', [ 
            'query' => $params,
        ]);
    
        $body = json_decode($response->getBody(), true);
        $response = isset($body['response']['data']) ? $body['response']['data'] : [];
        $data = [];
        foreach ($response as $key => $value) {
            $data[] = [
                'id'=> $value['obatalkes_id'],
                'text'=> $value['obatalkes_nama'].' - ('.$value['qty_tersedia'].')',
                'datavalue'=> $value ,
                'disabled' => ($value['qty_tersedia'] < 1) ? true : false,
            ];
        }
        return DocoHelpers::response([
            'result' => $data,
            'total_count' => count($data),
            'incomplete_results' => false,
            'pagination' => [ 'more' => count($data) === $limit ? true : false ]
        ]);
    }

    public function actionBatalTindakanBmhp()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($request->get('id','MQ'));
        $id_batal = $request->post('id_batal',0);
        $tipe = $request->post('tipe','TINDAKAN');
        try{
            $response = $this->_restRajal->post('tindakan-bmhp/hapus-tindakan',[
                'form_params' => [
                    'pendaftaran_id'=>$pendaftaran_id,
                    'id_batal'=>$id_batal,
                    'tipe'=>$tipe

                ]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body, false, true);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}