<?php

namespace app\modules\ranap\components\traits;

use Yii;
use DateTime;
use DateInterval;

use app\components\DocoConstants;
use app\modules\ranap\models\InstruksiPenunjangForm;
use app\components\DocoHelpers;
use app\components\Pelayanan\PelayananHelpers;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

trait PemeriksaanPenunjangTrait
{
    /**
     * This function will render modal penunjang [lab | rad]
     *
     * @param String $type Default lab
     * @return Html/Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFormModal($type)
    {
        $konfigSystem = $this->actionGetKonfigSystem();
        $orderBedahTanpaTindakan = $konfigSystem['order_bedah_tanpa_tindakan'];
        $inst_id = DocoConstants::INSTALASI_ID_RI;
        $modelPenunjang = new InstruksiPenunjangForm;
        // is_puasa
        // get list ruangan by type
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->session->get('user_identity');
        if ($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS) {
            $user['nama_pegawai'] = $this->_data_pasien['dokter_admisi'];
        }
        $url = [
            'form-action' => '/ranap/pemeriksaan-rawat-inap/simpan-terapi-penunjang?id=' . $id,
            'modal-pemeriksaan' => '/ranap/pemeriksaan-rawat-inap/modal-pemeriksaan-penunjang?id=' . $id . '&instalasi_id=#instalasi_id#&ruangan_id=#ruangan_id#&kelaspelayanan_id=#kelaspelayanan_id#&penjamin_id=#penjamin_id#',
            'jadwal-operasi' => '/ranap/pemeriksaan-rawat-inap/modal-jadwal-operasi?id=' . $id
        ];
        $instalasiId = null;
        $penjaminId = $this->_data_pasien['penjamin_id']; //get from PemeriksaanController@init
        $kelaspelayananId = isset($this->_data_pasien['previous_kelas_pelayanan']) && !empty($this->_data_pasien['previous_kelas_pelayanan']) ? $this->_data_pasien['previous_kelas_pelayanan'] : $this->_data_pasien['kelaspelayanan_id'];
        switch ($type) {
            case 'laboratorium':
                $instalasiId = DocoConstants::INSTALASI_ID_LAB;
                break;
            case 'radiologi':
                $instalasiId = DocoConstants::INSTALASI_ID_RAD;
                break;
            case 'bedah':
                $instalasiId = DocoConstants::INSTALASI_ID_BEDAH;
                break;
            case 'fisioterapi':
                $instalasiId = DocoConstants::INSTALASI_FISIOTERAPI;
                break;
            default:
                break;
        }
        $wardDropdown = [];
        if (!empty($instalasiId)) {
            $wardData = $this->guzzleExec($this->_restRanap, [
                'url' => 'allow/get-list-ruangan',
                'payload' => [
                    'query' => [
                        'instalasi_id' => $instalasiId
                    ]
                ]
            ]);
            foreach ($wardData as $value)
                $wardDropdown[$value['ruangan_id']] = $value['ruangan_nama'];
        }

        if($instalasiId == DocoConstants::INSTALASI_FISIOTERAPI) {
            $modelPenunjang->ruangan_id = sizeof($wardDropdown) > 0 ? reset(array_keys($wardDropdown)) : '';
        }

        $latest_cppt = $this->_restRanap->get('cppt/get-latest-cppt', [
                'query' => [
                        'pendaftaran_id' => PelayananHelpers::decryptId($id),
                        'is_dokter' => true, // get user login is dokter or not
                    ]
            ]);

        $latest_cppt = json_decode($latest_cppt->getBody(), true);
        $latest_cppt = isset($latest_cppt['response']['data']) ? $latest_cppt['response']['data'] : [];

        $opt_diagnosa_utama = !empty($latest_cppt['a_diag_utama']) ? json_decode($latest_cppt['a_diag_utama'], true) : [];
        $modelPenunjang->diagnosa_utama_text = isset($opt_diagnosa_utama['text']) ? $opt_diagnosa_utama['text'] : ' - ';
        $opt_diagnosa_penyerta = !empty($latest_cppt['a_diag_penyerta']) ? json_decode($latest_cppt['a_diag_penyerta'], true) : [];

        $modelPenunjang->pegawai_id = Yii::$app->docoVars->user("kelompokpegawai_id") != DocoConstants::KELOMPOK_MEDIS ? $this->_data_pasien['dokter_admisi_id'] : $this->_pegawai_id;
        $modelPenunjang->instalasi_id = $instalasiId;
        $modelPenunjang->pendaftaran_id = $this->helper->decrypt($id);
        $modelPenunjang->pasienadmisi_id = $this->_pasienadmisi_id;
        $modelPenunjang->cppt_id = Yii::$app->request->get('cppt_id', null);
        $modelPenunjang->has_jadwal = 0;
        $modelPenunjang->ruangan = $this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];
        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }

        return $this->renderAjax('//cppt/penunjang/__modal', [
            'drperujukId' => $modelPenunjang->pegawai_id != null ? $modelPenunjang->pegawai_id : $user['id_pegawai'],
            'type' => $type,
            'user' => $user,
            'model' => $modelPenunjang,
            'wards' => $wardDropdown,
            'penjaminId' => $penjaminId,
            'kelaspelayananId' => $kelaspelayananId,
            'id' => $id,
            'url' => $url,
            'inst_id' => $inst_id,
            'dokter_url' => '/ranap/pemeriksaan-rawat-inap/list-dokter-perujuk',
            'dokterList' => [
                $modelPenunjang->pegawai_id => $user['nama_pegawai'],
            ],
            'opt_diagnosa_utama' => $opt_diagnosa_utama,
            'opt_diagnosa_penyerta' => $opt_diagnosa_penyerta,
            'orderBedahTanpaTindakan' => $orderBedahTanpaTindakan,
            'infoPasien' => [
                'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ],
        ]);
    }

    public function actionFormModalFisio($type)
    {
        $inst_id = DocoConstants::INSTALASI_ID_RI;
        $modelPenunjang = new InstruksiPenunjangForm;
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->session->get('user_identity');
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
        $spesialisId = ArrayHelper::getValue($userIdentity, 'spesialis_id');
        $is_perawat = 'false';
        if ($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $is_perawat = 'true';
        }
        if ($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS) {
            $user['nama_pegawai'] = $this->_data_pasien['dokter_admisi'];
        }
        $url = [
            'form-action' => '/ranap/pemeriksaan-rawat-inap/simpan-terapi-penunjang?id=' . $id,
            'modal-pemeriksaan' => "/ranap/pemeriksaan-rawat-inap/modal-pemeriksaan-penunjang-fisio?id=$id&spesialis_id=$spesialisId&instalasi_id=#instalasi_id#&ruangan_id=#ruangan_id#&kelaspelayanan_id=#kelaspelayanan_id#&penjamin_id=#penjamin_id#",
            'jadwal-operasi' => '/ranap/pemeriksaan-rawat-inap/modal-jadwal-operasi?id=' . $id
        ];
        $instalasiId = null;
        $penjaminId = $this->_data_pasien['penjamin_id']; //get from PemeriksaanController@init
        $kelaspelayananId = isset($this->_data_pasien['previous_kelas_pelayanan']) && !empty($this->_data_pasien['previous_kelas_pelayanan']) ? $this->_data_pasien['previous_kelas_pelayanan'] : $this->_data_pasien['kelaspelayanan_id'];
        $restFisioterapi = Yii::$app->docoRest->fisioterapi;
        $responseGetInstalasiFisio = $this->guzzleExec($restFisioterapi, [
            'url' => 'allow/get-instalasi-fisioterapi',
            'payload' => [
                'query' => []
            ]
        ]);
        $instalasiId = ArrayHelper::getValue($responseGetInstalasiFisio, 'data');
        $wardDropdown = [];
        if (!empty($instalasiId)) {
            $wardData = $this->guzzleExec($this->_restRanap, [
                'url' => 'allow/get-list-ruangan',
                'payload' => [
                    'query' => [
                        'instalasi_id' => $instalasiId
                    ]
                ]
            ]);
            foreach ($wardData as $value)
                $wardDropdown[$value['ruangan_id']] = $value['ruangan_nama'];

            $maksFrekTemp = $this->guzzleExec($this->_restRanap, [
                'url' => 'allow/get-fisio-non-paket-maks-frekuensi',
            ]);
        }
        $pendaftaran_id = $this->helper->decrypt($id);
        $pegawai_id = $this->_data_pasien['dokter_admisi_id'];
        $isInputSpecialist = ArrayHelper::getValue($userIdentity, 'spesialis_id');
        $isInputKelompokMedis = Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS;
        if ($isInputSpecialist || $isInputKelompokMedis) {
            $pegawai_id = $this->_pegawai_id;
        }
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        $responseSoapRehabMedic = $this->_restRanap->get('cppt/get-soap-rehab-medic?pendaftaran_id='. $pendaftaran_id .
            '&ruangan_id='. $ruangan_id .
            '&kelompokpegawai_id='. DocoConstants::KELOMPOK_MEDIS, 
            [
                'form_params' => []
            ]
        );
        $response = json_decode($responseSoapRehabMedic->getBody(), true);

        $diagUtamaText = '';
        $diagPenyerta = null;
        $intruksiText = '';
        $data = $response['response']['data'];
        if(!empty($data)){
            if(isset($data['a_diag_utama']) && !empty($data['a_diag_utama'])){
                $diagUtamaText = $data['a_diag_utama']['text'];
            }
            if(isset($data['instruksi']) && !empty($data['instruksi'])){
                $intruksiText = $data['instruksi'];
            }
            if(isset($data['a_diag_penyerta']) && !empty($data['a_diag_penyerta'])){
                $diagPenyerta = $data['a_diag_penyerta'];
            }
        }
        $modelPenunjang->ruangan_id = sizeof($wardDropdown) > 0 ? reset(array_keys($wardDropdown)) : '';
        $modelPenunjang->pegawai_id = $pegawai_id;
        $modelPenunjang->instalasi_id = $instalasiId;
        $modelPenunjang->pendaftaran_id = $pendaftaran_id;
        $modelPenunjang->pasienadmisi_id = $this->_pasienadmisi_id;
        $modelPenunjang->cppt_id = Yii::$app->request->get('cppt_id', null);
        $modelPenunjang->has_jadwal = 0;
        $modelPenunjang->ruangan = $this->_data_pasien['ruangan_id'] . '@#' . @$this->_data_pasien['kamarruangan_id'] . '@#' . @$this->_data_pasien['kamartempattidur_id'] . '@#' . $this->_data_pasien['kamarruangan_nokamar'] . ' | ' . $this->_data_pasien['no_tempattidur'];
        $modelPenunjang->diagnosis = $diagUtamaText ? $diagUtamaText : null;
        $modelPenunjang->catatan_dokterpengirim = $intruksiText ? $intruksiText : null;
        $maksFrek = ArrayHelper::getValue($maksFrekTemp, 'additional_value');
        $maksFrekFixed = !is_null($maksFrekTemp) && !is_null($maksFrek) ? (int) $maksFrek : 10;

        $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
        $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
        if ($isTitipan) {
            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
        }

        return $this->renderAjax('/modal-order-penunjang/fisioterapi/__modal.php', [
            'type' => $type,
            'user' => $user,
            'model' => $modelPenunjang,
            'wards' => $wardDropdown,
            'penjaminId' => $penjaminId,
            'kelaspelayananId' => $kelaspelayananId,
            'maksFrekuensi' => $maksFrekFixed,
            'pegawaiId' => $pegawaiId,
            'id' => $id,
            'url' => $url,
            'inst_id' => $inst_id,
            'is_perawat' => $is_perawat,
            'diagPenyerta' => $diagPenyerta,
            'infoPasien' => [
                'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ],  
        ]);
    }

    public function actionDataPemeriksaanPenunjangFisio()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $get = $request->get();
        try {
            $params = [
                'ruangan_id' => ArrayHelper::getValue($get, 'ruangan_id'),
                'penjamin_id' => ArrayHelper::getValue($get, 'penjamin_id'),
                'kelaspelayanan_id' => ArrayHelper::getValue($get, 'kelaspelayanan_id'),
                'instalasi_id' => ArrayHelper::getValue($get, 'instalasi_id'),
                'maksFrekuensi' => ArrayHelper::getValue($get, 'maks_frekuensi'),
                'spesialis_id' => ArrayHelper::getValue($get, 'spesialis_id'),
            ];
            $searchText = $get['searching'];
            if($searchText){
                $params['daftartindakan_nama'] = $searchText;
            }
            $url = 'allow/get-tarif-tindakan-ri';
            $response = $this->_restRanap->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            foreach ($body['data'] as $key => $value) {
                $result[$value['jenispemeriksaanlab_nama']][] = $value;
                $listPemeriksaanPure[] = $value;
            }
            return json_encode($result);
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            (new DocoHelpers)->logError($e);
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionFormModalFisioSchedule()
    {
        $modelPenunjang = new InstruksiPenunjangForm;
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
        $frekuensi = Yii::$app->request->get('frekuensi', 0);
        $isDaily = Yii::$app->request->get('isDaily', 0);
        $days = Yii::$app->request->get('days', 0);
        $days = explode(",", $days);
        $countDays = count($days);
        $tempDate = [];
        $newDate = [];
        $listHari = [];
        if($isDaily){
            $count = 0;
            for ($i=0; $i <= $frekuensi; $i++) {
                $currentDate = date('Y-m-d');
                $formatedDate = date('Y-m-d',strtotime($currentDate . "+".$count."days"));
                $dayName = date('D', strtotime($formatedDate));
                $formatedDateValue = date('d-m-Y', strtotime($formatedDate));
                $newDate[$i] = $formatedDateValue;
                $listHari[$i] = $dayName;
                $count++;
            }
        }else{
            $arrSelisih = [];
            foreach ($days as $key => $value) {
                $i=0;
                do {
                    $currentDate = date('Y-m-d');
                    $formatedDate = date('Y-m-d',strtotime($currentDate . "+".$i."days"));
                    $dayName = date('D', strtotime($formatedDate));
                    $selisih = $i;
                    $i++;
                } while ($dayName != $value);
                $arrSelisih[] = $selisih;
            }

            for ($i=0; $i < count($arrSelisih); $i++) {
                $Date1 = date('Y-m-d');
                $date = new DateTime($Date1);
                $nearestDay = $date->add(new DateInterval('P'. $arrSelisih[$i] .'D'));
                $tempDate[] = $nearestDay->format('Y-m-d');
                for ($j=1; $j < $frekuensi; $j++) {
                    $tempDate[] = $nearestDay->add(new DateInterval('P7D'))->format('Y-m-d');
                }
            }
            sort($tempDate);
            $counter = 1;
            foreach ($tempDate as $key => $value) {
                $date = new DateTime($value);
                $listHari[] = $date->format('D');
                $value = date('d-m-Y', strtotime($value));
                $newDate[] = $value;
                if($counter == $frekuensi){
                    break;
                }
                $counter++;
            }
        }
        return $this->renderAjax('/modal-order-penunjang/fisioterapi/__schedule.php', [
            'frekuensi' => $frekuensi,
            'newDate' => $newDate,
            'listHari' => $listHari,
            'model' => $modelPenunjang,
            'pegawaiId' => $pegawaiId,
            'days' => $days,
            'isDaily' => $isDaily
        ]);
    }

    public function actionJadwalFisioterapi()
    {
        $pegawaiId = Yii::$app->request->get('pegawai_id');
        if(!$pegawaiId || !isset($pegawaiId) || is_null($pegawaiId) || empty($pegawaiId)){
            throw new \Exception("Payload tidak sesuai", 1);
        }
        return $this->renderAjax('/modal-order-penunjang/fisioterapi/jadwal-terapi.php', [
            'pegawaiId' => $pegawaiId
        ]);
    }

    public function actionGetDataTerapiFisio(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $helper = new DocoHelpers;
        $request = Yii::$app->request;
        $tglAwal = $request->get('tgl_penjadwalan_awal');
        $pegawaiId = $request->get('pegawai_id');
        $filter['tglAwal'] = $tglAwal;
        $filter['pegawaiId'] = $pegawaiId;
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'allow/get-jadwal-terapi',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        }
    }

    public function actionSimpanTerapiPenunjangFisio()
    {
        try {
            $request = Yii::$app->request;
            $orders = $request->post('periksafisio');

            $model = new InstruksiPenunjangForm;
            $model->attributes = $request->post('InstruksiPenunjangForm');
            $jadwal_operasi = [];
            if ($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH) {
                $jadwal_operasi = json_decode($request->post('jadwal_operasi'), true);
            }

            if ($model->validate()) {
                $temps = [];
                if($orders && !is_null($orders) && isset($orders) && !empty($orders)){
                    $orders = json_decode($orders, true);
                    foreach ($orders as $key => $order) {
                        $orderList = ArrayHelper::getValue($order, 'orders');
                        foreach($orderList as $k => $v){
                            $temps[$key]['orders'][$k]['is_paketfisio'] = ArrayHelper::getValue($v, 'is_paketfisio');
                            $temps[$key]['orders'][$k]['parentdaftartindakan_id'] = ArrayHelper::getValue($v, 'parentdaftartindakan_id');
                            $temps[$key]['orders'][$k]['tariftindakan_id'] = ArrayHelper::getValue($v, 'tariftindakan_id');
                            $temps[$key]['orders'][$k]['daftartindakan_id'] = ArrayHelper::getValue($v, 'daftartindakan_id');
                            $temps[$key]['orders'][$k]['is_cyto'] = ArrayHelper::getValue($v, 'is_cyto', false);
                            $temps[$key]['orders'][$k]['is_paket'] = ArrayHelper::getValue($v, 'tipepaket_id', false);
                            $temps[$key]['orders'][$k]['golongan_id'] = ArrayHelper::getValue($v, 'kelompokpemeriksaanlab_id');
                            $temps[$key]['orders'][$k]['kegiatan_id'] = ArrayHelper::getValue($v, 'jenispemeriksaanlab_id');
                            $temps[$key]['orders'][$k]['qty_pemeriksaan'] = ArrayHelper::getValue($v, 'qty_pemeriksaan');
                            $temps[$key]['orders'][$k]['catatan'] = ArrayHelper::getValue($v, 'catatan');
                        }
                        $temps[$key]['frekuensi'] = ArrayHelper::getValue($order, 'frekuensi');
                        $temps[$key]['schedule_details'] = ArrayHelper::getValue($order, 'schedule_details');
                    }
                }

                $model->tgl_kirimpasien = str_replace('/', '-', $model->tgl_kirimpasien);

                $post = [
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $model->pasienadmisi_id,
                    'instalasi_id' => $model->instalasi_id,
                    'ruangan_id' => $model->ruangan_id,
                    'pegawai_id' => $model->pegawai_id,
                    'cppt_id' => $model->cppt_id,
                    'catatan_dokterpengirim' => $model->catatan_dokterpengirim,
                    'catatan' => $model->catatan,
                    'tgl_kirimpasien' => $model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH ? date('Y-m-d', strtotime($jadwal_operasi['tgl_kirimpasien'])) . ' ' . $jadwal_operasi['jam_mulai'] : date('Y-m-d', strtotime($model->tgl_kirimpasien)) . ' ' . date('H:i:s'),
                    'list_order' => $temps,
                    'is_dokter' => isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS ? true : false,
                    'jadwal_operasi' => $jadwal_operasi,
                    'pasien_id' => ArrayHelper::getValue($this->_data_pasien, 'pasien_id'),
                    'ruangan' => $model->ruangan,
                    'is_puasa' => $model->is_puasa,
                    'pemakaian_implant' => $model->pemakaian_implant,
                    'sewa_vendor' => $model->sewa_vendor,
                    'sewa_alat_rs' => $model->sewa_alat_rs,
                    'jenis_operasi_cito' => $model->jenis_operasi_cito,
                    'jenis_operasi_elektif' => $model->jenis_operasi_elektif,
                    'jenis_operasi_odc' => $model->jenis_operasi_odc,
                    'is_rujukan' => $model->is_rujukan,
                    'diagnosis' => $model->diagnosis,
                    'a_diag_penyerta' => $model->a_diag_penyerta,
                    'ruangan_asal' => ArrayHelper::getValue(Yii::$app->session->get('active_workspace'), 'ruangan_id')
                ];
                $response = $this->_restRanap->post('cppt/create-terapi-penunjang', [
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
            if ($response['metadata']['status'] == 500) {
                return DocoHelpers::responseTemplate(
                    422,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Proses Gagal') . '!',
                        'text' => Yii::t('fe', $response['response']['message']),
                        'message' => Yii::t('fe', $response['response']['message']),
                    ]
                );
            }
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $this->logError($e);
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            $this->logError($e);
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionCekKetersediaanJadwal(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $helper = new DocoHelpers;
        $request = Yii::$app->request;
        $tglAwal = $request->get('tgl_penjadwalan_awal');
        $tglAkhir = $request->get('tgl_penjadwalan_akhir');
        $pegawaiId = $request->get('pegawai_id');
        $filter['tglAwal'] = $tglAwal;
        $filter['tglAkhir'] = $tglAkhir;
        $filter['pegawaiId'] = $pegawaiId;
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'allow/cek-ketersediaan-jadwal',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            $helper->logError($e);
            return ['error' => $e->getMessage()];
        }
    }

    public function actionListDokterPerujuk()
    {
        $dokterList = $this->guzzleExec($this->_restRanap, [
            'url' => 'allow/list-dokter-perujuk',
            'method' => 'get',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
        ]);
        return DocoHelpers::response($dokterList);
    }

    public function actionGetKonfigSystem()
    {
        $response = $this->guzzleExec($this->_restRanap, [
            'url' => 'allow/get-konfig-system',
            'method' => 'get',
        ]);
        return $response;
    }
}
