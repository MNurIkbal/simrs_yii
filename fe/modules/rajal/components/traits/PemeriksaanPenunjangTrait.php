<?php

/**
 * @Author: rizal@docotel.com
 */

namespace app\modules\rajal\components\traits;

use Yii;
use DateTime;
use DateInterval;
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
use app\components\Pelayanan\PelayananHelpers;

use app\modules\rajal\models\InstruksiPenunjangForm;
use app\modules\rajal\models\JadwalOperasiForm;
use app\modules\rajal\models\DietPasienForm;

trait PemeriksaanPenunjangTrait
{
    public function actionPenunjang()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $jenis = $request->get('jenis');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        // $modelTindakan = new TindakanPelayananForm;
        $modelPenunjang = new InstruksiPenunjangForm;
        $title = Yii::t('fe', 'Riwayat');

        $is_bedah = false;

        // switch ($jenis) {
        //     case 'laboratorium':
        //         $modelPenunjang->instalasi_id = DocoConstants::INSTALASI_ID_LAB;
        //         $title = Yii::t('fe', 'Riwayat laboratorium');
        //         break;

        //     case 'radiologi':
        //         $modelPenunjang->instalasi_id = DocoConstants::INSTALASI_ID_RAD;
        //         $title = Yii::t('fe', 'Riwayat radiologi');
        //         break;

        //     case 'rehabmedis':
        //         $modelPenunjang->instalasi_id = DocoConstants::INSTALASI_ID_REHAB;
        //         $title = Yii::t('fe', 'Riwayat rehab medis');
        //         break;

        //     case 'bedahsentral':
        //         $is_bedah = true;
        //         $modelPenunjang->instalasi_id = DocoConstants::INSTALASI_ID_BEDAH;
        //         $title = Yii::t('fe', 'Riwayat bedah sentral');
        //         break;
        // }


        $response = $this->_restRajal->get(
            'allow/get-api-periksa-penunjang',
            ['query' => [
                'id' => $pendaftaran_id,
            ]]
        );
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];
        $data_pasien = $this->_data_pasien;
        $is_dokter = $response['is_dokter'];

        $data_pegawai = isset($response['data_pegawai']) ? $response['data_pegawai'] : null;
        $data_penunjang = [];
        $listInstalasiPenunjang = isset($response['data_instalasi']) ? $response['data_instalasi'] : [];
        $data_instalasi = ArrayHelper::map($listInstalasiPenunjang, 'instalasi_id', 'instalasi_nama');

        return $this->renderAjax('__penunjang', [
            'pendaftaran_id' => $pendaftaran_id,
            // 'modelTindakan' => $modelTindakan,
            'modelPenunjang' => $modelPenunjang,
            'data_pasien' => $data_pasien,
            'data_pegawai' => $data_pegawai,
            'data_penunjang' => $data_penunjang,
            'data_instalasi' => $data_instalasi,
            'title' => $title,
            'jenis' => $jenis,
            'is_bedah' => $is_bedah,
            'is_dokter' => $is_dokter,
            'status_periksa' => $this->_statusPeriksa
        ]);
    }


    public function actionGetDataHistoryPenunjang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = $request->get('pendaftaran_id');
        $id_encrypt = DocoHelpers::encrypt($id);
        $yiiRestfulParams['pendaftaran_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;

        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-riwayat-penunjang', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_kirimpasien'] = date('d M Y H:i:s', strtotime($value['tgl_kirimpasien']));
                $value['catatan'] = $value['alasan_batal'] ?: $value['catatan'];
                $value['aksi'] = Html::a(
                    '<i class="fa fa-print"></i>',
                    '/rajal/pemeriksaan/cetak-penunjang?id=' . $id_encrypt . '&pasienkirimkeunitlain_id=' . $value['pasienkirimkeunitlain_id'],
                    [
                        'class' => 'btn btn-success btn-xs cetak-penunjang',
                        'target' => '_blank',
                        'action' => '/rajal/pemeriksaan/cetak-penunjang?id=' . $id_encrypt . '&pasienkirimkeunitlain_id=' . $value['pasienkirimkeunitlain_id'],
                    ]
                );

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }


    /*
    author: Rizal Faidin CLONE FROM PENDAFTARAN
    usage: modal tambah pemeriksaan penunjang
    date: 26-07-2018
    */
    public function actionModalPemeriksaanPenunjang()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $listPemeriksaanPure = [];
            if ($get['instalasi_id'] == DocoConstants::INSTALASI_ID_LAB || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_RAD || $get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH || $get['instalasi_id'] == DocoConstants::INSTALASI_FISIOTERAPI) {
                $params = [
                    'ruangan_id'        => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                    'penjamin_id'       => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                    'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                    'instalasi_id'      => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
                ];
                $url = 'allow/get-tarif-tindakan-rj';
                $response = $this->_restRajal->get($url, ['query' => $params]);
                $body = json_decode($response->getBody(), true);
                $body = isset($body['response']) ? $body['response'] : [];
                $groupingtindakan_penunjang = isset($body['groupingtindakan_penunjang']) ? $body['groupingtindakan_penunjang'] : false;
                foreach ($body['data'] as $key => $value) {
                    if ($get['instalasi_id'] == DocoConstants::INSTALASI_ID_BEDAH) {
                        if ($groupingtindakan_penunjang) {
                            $result[$value['daftartindakan_nama']][$value['nama_kelompok']][] = $value;
                            continue;
                        }
                    }
                    $result[$value['jenispemeriksaanlab_nama']][$value['nama_kelompok']][] = $value;
                    $listPemeriksaanPure[] = $value;
                }
            }
            $title = $get['instalasi_id'] == DocoConstants::INSTALASI_FISIOTERAPI ? Yii::t('fe', 'Tambah terapi') : Yii::t('fe', 'Tambah pemeriksaan');
            $instalasiId = ArrayHelper::getValue($get, 'instalasi_id');
            return $this->renderAjax('//cppt/penunjang/__modal_order_penunjang', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionModalPemeriksaanPenunjangFisio()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new InstruksiPenunjangForm;
        try {
            $listPemeriksaanPure = [];
            $params = [
                'ruangan_id' => ArrayHelper::getValue($get, 'ruangan_id'),
                'penjamin_id' => ArrayHelper::getValue($get, 'penjamin_id'),
                'kelaspelayanan_id' => ArrayHelper::getValue($get, 'kelaspelayanan_id'),
                'instalasi_id' => ArrayHelper::getValue($get, 'instalasi_id'),
                'maksFrekuensi' => ArrayHelper::getValue($get, 'maks_frekuensi'),
                'spesialis_id' => ArrayHelper::getValue($get, 'spesialis_id'),
            ];
            $url = 'allow/get-tarif-tindakan-rj';
            $response = $this->_restRajal->get($url, ['query' => $params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $groupingtindakan_penunjang = isset($body['groupingtindakan_penunjang']) ? $body['groupingtindakan_penunjang'] : false;
            foreach ($body['data'] as $key => $value) {

                $result[$value['jenispemeriksaanlab_nama']][] = $value;
                $listPemeriksaanPure[] = $value;
            }
            $days = [
                'Mon' => 'Senin',
                'Tue' => 'Selasa',
                'Wed' => 'Rabu',
                'Thu' => 'Kamis',
                'Fri' => 'Jumat',
                'Sat' => 'Sabtu',
                'Sun' => 'Minggu',
            ];
            $title = 'Tambah Terapi';
            $model->has_jadwal = 0;
            $instalasiId = ArrayHelper::getValue($get, 'instalasi_id');
            $userIdentity = Yii::$app->session->get('user_identity');
            $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
            return $this->renderAjax('/modal-order-penunjang/fisioterapi/__modal_order_penunjang.php', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
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
            $url = 'allow/get-tarif-tindakan-rj';
            $response = $this->_restRajal->get($url, ['query' => $params]);
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

    public function actionSimpanTerapiPenunjangFisio()
    {
        try {
            $request = Yii::$app->request;
            $orders = $request->post('periksafisio');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $model = new InstruksiPenunjangForm;
            $model->attributes = $request->post('InstruksiPenunjangForm');
            $model->ruangan_id = $ruangan_id;
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

                $post = [
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $model->pasienadmisi_id,
                    'instalasi_id' => $model->instalasi_id,
                    'ruangan_id' => $model->ruangan_id,
                    'pegawai_id' => $model->pegawai_id,
                    'cppt_id' => $model->cppt_id,
                    'catatan_dokterpengirim' => $model->catatan_dokterpengirim,
                    'tgl_kirimpasien' => $model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH ? date('Y-m-d', strtotime($jadwal_operasi['tgl_kirimpasien'])) . ' ' . $jadwal_operasi['jam_mulai'] : date_format(date_create_from_format('d/m/Y', $model->tgl_kirimpasien), 'Y-m-d') . ' ' . date('H:i:s'),
                    'list_order' => $temps,
                    'catatan' => $model->catatan,
                    'jadwal_operasi' => $jadwal_operasi,
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
                $response = $this->_restRajal->post('tra-pemeriksaan/create-terapi-penunjang', [
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

    public function actionSimpanTerapiPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $orders = $request->post('periksalab');
            $orders = json_decode($orders, true);
            $ruanganasal_id = $request->post('ruanganperiksa_id') != null ? PelayananHelpers::decryptId($request->post('ruanganperiksa_id')) : null;

            $model = new InstruksiPenunjangForm;
            $model->attributes = $request->post('InstruksiPenunjangForm');
            $jadwal_operasi = [];
            if ($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH) {
                $jadwal_operasi = json_decode($request->post('jadwal_operasi'), true);
            }

            if ($model->validate()) {
                $temps = [];
                foreach ($orders as $key => $order) {
                    $temps[$key]['is_paketfisio'] = ArrayHelper::getValue($order, 'is_paketfisio');
                    $temps[$key]['parentdaftartindakan_id'] = ArrayHelper::getValue($order, 'parentdaftartindakan_id');
                    $temps[$key]['tariftindakan_id'] = $order['tariftindakan_id'];
                    $temps[$key]['daftartindakan_id'] = $order['daftartindakan_id'];
                    $temps[$key]['is_cyto'] = $order['is_cyto'] == 'true' ? true : false;
                    $temps[$key]['is_paket'] = isset($order['tipepaket_id']) ? true : false;
                    $temps[$key]['golongan_id'] = isset($order['kelompokpemeriksaanlab_id'])
                        ? $order['kelompokpemeriksaanlab_id']
                        : null;
                    $temps[$key]['kegiatan_id'] = isset($order['jenispemeriksaanlab_id'])
                        ? $order['jenispemeriksaanlab_id']
                        : null;
                    $temps[$key]['catatan'] = isset($order['catatan'])
                        ? $order['catatan']
                        : null;
                }

                $post = [
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'pasienadmisi_id' => $model->pasienadmisi_id,
                    'instalasi_id' => $model->instalasi_id,
                    'ruangan_id' => $model->ruangan_id,
                    'pegawai_id' => $model->pegawai_id,
                    'cppt_id' => $model->cppt_id,
                    'catatan_dokterpengirim' => $model->catatan_dokterpengirim,
                    'catatan' => $model->catatan,
                    'tgl_kirimpasien' => $model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH ? date('Y-m-d', strtotime($jadwal_operasi['tgl_kirimpasien'])) . ' ' . $jadwal_operasi['jam_mulai'] : date_format(date_create_from_format('d/m/Y', $model->tgl_kirimpasien), 'Y-m-d') . ' ' . date('H:i:s'),
                    'list_order' => $temps,
                    'jadwal_operasi' => $jadwal_operasi,
                    'is_puasa' => $model->is_puasa,
                    'pemakaian_implant' => $model->pemakaian_implant,
                    'sewa_vendor' => $model->sewa_vendor,
                    'sewa_alat_rs' => $model->sewa_alat_rs,
                    'jenis_operasi_cito' => $model->jenis_operasi_cito,
                    'jenis_operasi_elektif' => $model->jenis_operasi_elektif,
                    'jenis_operasi_odc' => $model->jenis_operasi_odc,
                    'is_rujukan' => $model->is_rujukan,
                    'diagnosis' => $model->diagnosis,
                    'frekuensi_terapi' => $model->frekuensi_terapi,
                    'schedule_details' => $model->schedule_details,
                    'diagnosa_utama' => isset($model->diagnosa_utama) ? json_decode($model->diagnosa_utama, true) : [],
                    'diagnosa_penyerta' => isset($model->diagnosa_penyerta) ? json_decode($model->diagnosa_penyerta, true) : [],
                    'ruangan_asal' => $ruanganasal_id != null ? $ruanganasal_id : ArrayHelper::getValue(Yii::$app->session->get('active_workspace'), 'ruangan_id')
                ];
                $response = $this->_restRajal->post('tra-pemeriksaan/create-terapi-penunjang', [
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

    /* Cetak pdf penunjang */
    public function actionCetakPenunjang($id, $pasienkirimkeunitlain_id)
    {
        $path = Yii::getAlias("@download") . "/terapi-penunjang.pdf";

        // try {
        $request = $this->_restRajal->get('tra-pemeriksaan/cetak-penunjang', [
            'query' => [
                'pendaftaran_id' => $id,
                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
        // } catch (RequestException $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // } catch (\Exception $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // }
    }

    public function actionModalJadwalOperasi()
    {
        try {
            $title = Yii::t('fe', 'Input Jadwal Prosedur / Operasi');
            $modelJadwalOperasi = new JadwalOperasiForm;
            return $this->renderAjax('//cppt/penunjang/_modal_jadwal_operasi', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataJadwalOperasi()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restRajal->get('tra-pemeriksaan/get-data-jadwal-operasi', [
                'query' => $request->post()
            ]);

            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            $ruangan = $data_jadwal = [];
            if ($response['metadata']['status'] == 200) {
                $ruangan = isset($response['response']['data_ruangan']) ? $response['response']['data_ruangan'] : [];
                $data_jadwal = isset($response['response']['jadwal_data']) ? $response['response']['jadwal_data'] : [];
            }

            return DocoHelpers::response([
                'ruangan' => $ruangan,
                'data_jadwal' => $data_jadwal
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actionSetJadwalOperasi()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = new JadwalOperasiForm;
            $model->load($post);
            if ($model->validate()) {
                $session = Yii::$app->session;
                $session->set('jadwal_operasi_rajal', $post);
                $response = $model->attributes;
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionModalHistoryTerraMedikSoap()
    {
        $request      = Yii::$app->request;
        $get          = $request->get();
        $pasien_terra = $get['pasien_id'];
        $pegawai_id   = $this->_pegawai_id;

        $response               = $this->_restRajal->get('allow/get-dokter-rajal');
        $resResponselist_dokter = json_decode($response->getBody(), True)['response']['list_dokter'];
        $getlist_dokter         = isset($resResponselist_dokter) ? $resResponselist_dokter : [];
        try {
            $title = Yii::t('fe', 'Arsip Riwayat SOAP');
            return $this->renderAjax('__modal_view_terra_medik_soap', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }


    // Get data
    public function actionGetDataHistoryCpptTerraMedik()
    {
        // Try catch
        try {
            // Inisiasi
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params                     = Yii::$app->request;
            $yiiRestfulParams           = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw                       = $params->get('draw', 1);
            $data                       = [];
            $pasien_terra               = DocoHelpers::decrypt($params->get('pasien_terra'));

            // Inisiasi result
            $result                    = [];
            $result['data']            = $data;
            $result['draw']            = $draw;
            $result['recordsTotal']    = 0;
            $result['recordsFiltered'] = 0;

            // Get request
            $request = $this->_restRajal->get('cppt/history-cppt-terra-medik?pasien_terra=' . $pasien_terra . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            // Inisiasi nomor
            $no = $params->get('start', 1);

            // Loop untuk membuat array dari response
            foreach ($response['response']["data"] as $key => $value) {
                $no++;
                // Assign data
                $data[$key]['primary'] = DocoHelpers::encrypt($value['riwayatsoap_id']);
                $data[$key]['no'] = $no;
                $data[$key]['ruang'] = $value['tipe_pendaftaran'] . '<br>' . date('d-m-Y / H:i:s', strtotime($value['tgl_soap'])) . '<br>' . $value['nama_dokter'];
                $data[$key]['soap'] = $value['soap'];
                $data[$key]['resep'] = $value['resep'];
                $data[$key]['dokter_id'] = $value['dokter_id'];
            }
            $result['data'] = $data;

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
        $inst_id = DocoConstants::INSTALASI_ID_RJ;
        $modelPenunjang = new InstruksiPenunjangForm;
        // is_puasa
        // get list ruangan by type
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->session->get('user_identity');
        if ($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS) {
            $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
        }
        $url = [
            'form-action' => '/rajal/pemeriksaan/simpan-terapi-penunjang',
            'modal-pemeriksaan' => '/rajal/pemeriksaan/modal-pemeriksaan-penunjang?id=' . $id . '&instalasi_id=#instalasi_id#&ruangan_id=#ruangan_id#&kelaspelayanan_id=#kelaspelayanan_id#&penjamin_id=#penjamin_id#',
            'jadwal-operasi' => '/rajal/pemeriksaan/modal-jadwal-operasi'
        ];
        $instalasiId = null;
        $penjaminId = $this->_data_pasien['penjamin_id']; //get from PemeriksaanController@init
        $kelaspelayananId = $this->_data_pasien['kelaspelayanan_id']; //get from PemeriksaanController@init
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
            $wardData = $this->guzzleExec($this->_restRajal, [
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

        if ($instalasiId == DocoConstants::INSTALASI_FISIOTERAPI) {
            $modelPenunjang->ruangan_id = sizeof($wardDropdown) > 0 ? reset(array_keys($wardDropdown)) : '';
        }

        $latest_cppt = $this->_restRajal->get('cppt/get-latest-cppt', [
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

        $modelPenunjang->pegawai_id = Yii::$app->docoVars->user("kelompokpegawai_id") != DocoConstants::KELOMPOK_MEDIS ? $this->_data_pasien['pegawai_id'] : $this->_pegawai_id;
        $modelPenunjang->instalasi_id = $instalasiId;
        $modelPenunjang->pendaftaran_id = $this->helper->decrypt($id);
        $modelPenunjang->cppt_id = Yii::$app->request->get('cppt_id', null);
        $modelPenunjang->has_jadwal = 0;
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
            'dokter_url' => '/rajal/pemeriksaan/list-dokter-perujuk',
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
        $inst_id = DocoConstants::INSTALASI_ID_RJ;
        $modelPenunjang = new InstruksiPenunjangForm;
        $userIdentity = Yii::$app->session->get('user_identity');
        $pegawaiId = ArrayHelper::getValue($userIdentity, 'loginpemakai_id');
        $id = Yii::$app->request->get('id');
        $user = Yii::$app->session->get('user_identity');
        $spesialisId = ArrayHelper::getValue($userIdentity, 'spesialis_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        if ($user['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS) {
            $user['nama_pegawai'] = $this->_data_pasien['nama_pegawai'];
        }
        $is_perawat = 'false';
        if ($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) {
            $is_perawat = 'true';
        }
        $url = [
            'form-action' => '/rajal/pemeriksaan/simpan-terapi-penunjang',
            'modal-pemeriksaan' => "/rajal/pemeriksaan/modal-pemeriksaan-penunjang-fisio?id=$id&spesialis_id=$spesialisId&instalasi_id=#instalasi_id#&ruangan_id=$ruangan_id&kelaspelayanan_id=#kelaspelayanan_id#&penjamin_id=#penjamin_id#",
            'jadwal-operasi' => '/rajal/pemeriksaan/modal-jadwal-operasi'
        ];
        $instalasiId = null;
        $penjaminId = $this->_data_pasien['penjamin_id']; //get from PemeriksaanController@init
        $kelaspelayananId = $this->_data_pasien['kelaspelayanan_id']; //get from PemeriksaanController@init
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
            $wardData = $this->guzzleExec($this->_restRajal, [
                'url' => 'allow/get-list-ruangan',
                'payload' => [
                    'query' => [
                        'instalasi_id' => $instalasiId
                    ]
                ]
            ]);
            foreach ($wardData as $value)
                $wardDropdown[$value['ruangan_id']] = $value['ruangan_nama'];

            $maksFrekTemp = $this->guzzleExec($this->_restRajal, [
                'url' => 'allow/get-fisio-non-paket-maks-frekuensi',
            ]);
        }
        $pendaftaran_id = $this->helper->decrypt($id);
        $pegawai_id = $this->_data_pasien['pegawai_id'];
        $isInputSpecialist = ArrayHelper::getValue($userIdentity, 'spesialis_id');
        $isInputKelompokMedis = Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS;
        if ($isInputSpecialist || $isInputKelompokMedis) {
            $pegawai_id = $this->_pegawai_id;
        }

        $responseSoapRehabMedic = $this->_restRajal->get('cppt/get-soap-rehab-medic?pendaftaran_id='. $pendaftaran_id .
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
        $modelPenunjang->cppt_id = Yii::$app->request->get('cppt_id', null);
        $modelPenunjang->has_jadwal = 0;
        $modelPenunjang->diagnosis = $diagUtamaText ? $diagUtamaText : null;
        $modelPenunjang->catatan_dokterpengirim = $intruksiText ? $intruksiText : null;
        $maksFrek = ArrayHelper::getValue($maksFrekTemp, 'additional_value');
        $maksFrekFixed = !is_null($maksFrekTemp) && !is_null($maksFrek) ? (int) $maksFrek : 10;
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
                'kelaspelayanan_nama' => ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-'),
            ],
        ]);
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
                $date1 = date('Y-m-d');
                $date = new DateTime($date1);
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
            throw new Exception("Payload tidak sesuai", 1);
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

    // public function actionFormModalDietPasien($id)
    // {
    //     try{
    //         $pendaftaran_id = DocoHelpers::decrypt($id);
    //         $model = new DietPasienForm;
    //         $model->pendaftaran_id = $pendaftaran_id;
    //         return $this->renderAjax('/pemeriksaan/__modal_diet_pasien', get_defined_vars());
    //     } catch (RequestException $e) {
    //         throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
    //     } catch (\Exception $e) {
    //         throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
    //     }
    // }

    // public function actionSaveDietPasien()
    // {
    //     try {
    //         $request    = Yii::$app->request;
    //         $pegawai_id = $this->_pegawai_id;

    //         $model = new DietPasienForm;
    //         $model->attributes = $request->post('DietPasienForm');
    //         if ($model->validate()) {
    //             $post = [
    //                 'pendaftaran_id' => $model->pendaftaran_id,
    //                 'catatan_diet'   => $model->catatan_diet,
    //                 'peg_pemesan_id' => $pegawai_id,
    //             ];
    //             $response = $this->_restRajal->post('tra-pemeriksaan/save-diet-pasien', [
    //                 'form_params' => $post
    //             ]);
    //             $response = json_decode($response->getBody(), true);
    //             $response = true;
    //         } else {
    //             $formName = substr(strrchr(get_class($model), "\\"), 1);
    //             $response = $model->errors;
    //             return DocoHelpers::response($response, 422, $formName);
    //         }

    //         return DocoHelpers::response($response);
    //     } catch (RequestException $e) {
    //         return DocoHelpers::response(['message' => $e->getMessage()],500);
    //     } catch (\Exception $e) {
    //         return DocoHelpers::response(['message' => $e->getMessage()],500);
    //     }
    // }

    public function actionListDokterPerujuk()
    {
        $dokterList = $this->guzzleExec($this->_restRajal, [
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
        $response = $this->guzzleExec($this->_restRajal, [
            'url' => 'allow/get-konfig-system',
            'method' => 'get',
        ]);
        return $response;
    }
}
