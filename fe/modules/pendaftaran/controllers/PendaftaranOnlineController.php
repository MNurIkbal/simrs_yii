<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-26 10:36:19
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\Response;

class PendaftaranOnlineController extends DocoController
{
    protected $_restPendaftaran;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    /**
     * @todo Method untuk menampilkan halaman awal portal list pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $title = Yii::t('fe', 'Data Pendaftaran Online');
            $listPoliTujuan = [];

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-bundle-data');
            $body = json_decode($restPendaftaran->getBody(), true);
            $data = $body['response'];
            $listPoliTujuan = ArrayHelper::map($data['listPoliTujuan'], 'ruangan_nama', 'ruangan_nama');
            $listDokter = ArrayHelper::map($data['listDokter'], 'nama_pegawai', 'nama_pegawai');
            $listCaraBayar = ArrayHelper::map($data['listCaraBayar'], 'carabayar_nama', 'carabayar_nama');
            $listStatus = ArrayHelper::map($data['listStatus'], 'lookup_id', 'lookup_name');

            return $this->render('index', [
                'title' => $title,
                'listPoliTujuan' => $listPoliTujuan,
                'listDokter' => $listDokter,
                'listCaraBayar' => $listCaraBayar,
                'listStatus' => $listStatus,
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk melakukan pemrosesan pendaftaran online (setujui atau tolak)
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionProses($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $post = Yii::$app->request->post();

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-bundle-data-verifikasi?id='.$id);
            $body = json_decode($restPendaftaran->getBody(), true);
            $data = $body['response']['pendaftaranOnline'];
            $listKlasifikasiPasien = $body['response']['klasifikasiPasien'];
            $listKlasifikasiPasien = ArrayHelper::map($listKlasifikasiPasien, 'statuspasien_id', 'statuspasien_nama');
            $listCaraBayar = $body['response']['caraBayar'];
            $listCaraBayar = ArrayHelper::map($listCaraBayar, 'carabayar_id', 'carabayar_nama');
            $dataPasien = $body['response']['pasien'];

            $initPenjamin = [];
            if ($data['penjamin_id'] != '') {
                $initPenjamin = [$data['penjamin_id'] => $data['penjamin_nama']];
            }

            if (isset($data['nrp_nama'])) {
                $data['nrp'] = $data['nrp_nama'];

                if (isset($data['nrp_lainnya']) && $data['nrp_lainnya'] != '') {
                    $data['nrp'] = $data['nrp'].' - '.$data['nrp_lainnya'];
                }
            }

            if (isset($data['tgl_kunjungan']) && $data['tgl_kunjungan'] != '') {
                $data['jadwal'] = DocoHelpers::convDateTime($data['tgl_kunjungan'], true, false);

                if (isset($data['jam_kunjungan']) && $data['jam_kunjungan'] != '') {
                    $data['jadwal'] = $data['jadwal'].' '.$data['jam_kunjungan'];
                }
            }

            if (isset($data['tanggal_lahir'])) {
                $data['tanggal_lahir'] = date('Y-m-d H:i:s', strtotime($data['tanggal_lahir']));
                $data['tanggal_lahir'] = DocoHelpers::convDateTime($data['tanggal_lahir'], false, false);
            }

            if ($post) {
                if ($post['status_daftar_ol'] == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI) {
                    if (empty($data['no_rekam_medik'])) {
                        if (isset($post['no_rekam_medik']) && $post['no_rekam_medik'] == '') {
                            $return = [];
                            $return['metadata']['status'] = 422;
                            $return['metadata']['message'] = Yii::t('fe', 'Unprocessable Entity');
                            $return['response']['data']['no_rekam_medik'][] = Yii::t('fe', 'No Rekam Medik harus diisi.');

                            return DocoHelpers::response($return);
                        }
                    }

                    if (empty($dataPasien['stat_pasien'])) {
                        if (isset($post['stat_pasien']) && $post['stat_pasien'] == '') {
                            $return = [];
                            $return['metadata']['status'] = 422;
                            $return['metadata']['message'] = Yii::t('fe', 'Unprocessable Entity');
                            $return['response']['data']['stat_pasien'][] = Yii::t('fe', 'Klasifikasi Pasien harus diisi.');

                            return DocoHelpers::response($return);
                        }
                    }

                    if (isset($post['carabayar_id']) && $post['carabayar_id'] == '') {
                        $return = [];
                        $return['metadata']['status'] = 422;
                        $return['metadata']['message'] = Yii::t('fe', 'Unprocessable Entity');
                        $return['response']['data']['carabayar_id'][] = Yii::t('fe', 'Cara Bayar harus diisi.');

                        return DocoHelpers::response($return);
                    }

                    if ($post['carabayar_id'] != 1) {
                        if (isset($post['penjamin_id']) && $post['penjamin_id'] == '') {
                            $return = [];
                            $return['metadata']['status'] = 422;
                            $return['metadata']['message'] = Yii::t('fe', 'Unprocessable Entity');
                            $return['response']['data']['penjamin_id'][] = Yii::t('fe', 'Penjamin harus diisi.');

                            return DocoHelpers::response($return);
                        }
                    } else {
                        $post['penjamin_id'] = null;
                    }
                } else {
                    $post['no_rekam_medik'] = null;
                    $post['stat_pasien'] = null;
                }

                $post['tanggal_lahir'] = DocoHelpers::convertIndoToEnglish($post['tanggal_lahir']);

                $restPendaftaran = $this->_restPendaftaran->post('pendaftaran-online/proses', [
                    'form_params' => [
                        'pasien' => $post
                    ]
                ]);

                $body = json_decode($restPendaftaran->getBody(), true);

                return DocoHelpers::response($body);
            } else {
                return $this->renderAjax('form', [
                    'data' => $data,
                    'listKlasifikasiPasien' => $listKlasifikasiPasien,
                    'listCaraBayar' => $listCaraBayar,
                    'statusSetujui' => DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI,
                    'statusTolak' => DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK,
                    'initPenjamin' => $initPenjamin,
                    'dataPasien' => $dataPasien,
                ]);
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk menampilkan halaman cari nomor rekam medik dengan scanner
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekPendaftaranOnline($no = null)
    {
        try {
            $post = Yii::$app->request->post();

            if ($no) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-bundle-data-scanner?no='.$no);
                $data = json_decode($restPendaftaran->getBody(), true);

                if (isset($data['response']['pendaftaranOnline']['status'])) {
                    return json_encode(false);
                } else {
                    $data['response']['pendaftaranOnline']['pendaftaranol_id'] = DocoHelpers::encrypt($data['response']['pendaftaranOnline']['pendaftaranol_id']);

                    return json_encode($data['response']['pendaftaranOnline']);
                }
            } else {
                return json_encode(false);
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk melakukan cetak bukti pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakBukti($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/bukti-pendaftaran-online.pdf";

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/cetak-bukti?id='.$id, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Method untuk melakukan export pdf pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/bukti-pendaftaran-online.pdf";

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['tgl_kunjungan'])) {
                $tgl_kunjungan_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_kunjungan']);
                $tgl_awal = $tgl_kunjungan_range[0];
                $tgl_akhir = $tgl_kunjungan_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_kunjungan_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_kunjungan_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_kunjungan']);
            }

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/export-pdf?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Method untuk melakukan export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/bukti-pendaftaran-online.pdf";

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['tgl_kunjungan'])) {
                $tgl_kunjungan_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_kunjungan']);
                $tgl_awal = $tgl_kunjungan_range[0];
                $tgl_akhir = $tgl_kunjungan_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_kunjungan_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_kunjungan_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_kunjungan']);
            }

            $path = Yii::getAlias("@download") . "/data-pendaftaran-online.xlsx";
            $response = $this->_restPendaftaran->get('pendaftaran-online/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Method untuk mendapatkan data pendaftaran online
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPendaftaranOnline()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['tgl_kunjungan'])) {
                $tgl_kunjungan_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_kunjungan']);
                $tgl_awal = $tgl_kunjungan_range[0];
                $tgl_akhir = $tgl_kunjungan_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_kunjungan_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_kunjungan_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_kunjungan']);
            }

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($restPendaftaran->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $prefix = substr($value['no_pendaftaranol'], 0, 2);
                    $number = substr($value['no_pendaftaranol'], 10);
                    $jam_kunjungan = explode('-', $value['jam_kunjungan']);
                    $jam_kunjungan_awal = $jam_kunjungan[0];

                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['pendaftaranol_id']);
                    $value['primary'] = $primaryKey;
                    $value['no'] = $no;
                    $value['no_pendaftaranol'] = $value['no_pendaftaranol'];
                    $value['no_rekam_medik'] = $value['no_rekam_medik'] . ' - ' . $value['nama_pasien'];
                    $value['nama_pasien'] = $value['nama_pasien'];
                    $value['no_asuransi'] = $value['no_asuransi'];
                    $value['ruangan_nama'] = $value['ruangan_nama'];
                    $value['nama_pegawai'] = $value['nama_pegawai'];
                    $value['carabayar_nama'] = $value['carabayar_nama'];
                    $value['jam_kunjungan'] = date('H:i', strtotime($jam_kunjungan_awal));
                    $value['tgl_pendaftaran'] = date('j F Y', strtotime($value['tgl_pendaftaran']));
                    $value['tgl_kunjungan'] = date('j F Y', strtotime($value['tgl_kunjungan']));
                    $value['status_daftar_ol'] = $value['status_daftar_ol'];
                    $value['no_antrian'] = $prefix.$number . ' - ' . $value['no_antrian'];

                    if ($value['status_daftar_ol'] == DocoConstants::VAR_STATUS_DAFTAR_OL_BELUM_DIPROSES) {
                        $value['aksi'] = Html::button('<b><i class="fa fa-volume-up"></i></b>'.Yii::t('fe', ' Panggil'), [
                            'class' => 'btn btn-primary btn-labeled btn-xs btn-panggil',
                            'style' => 'margin-bottom: 5px;'
                        ]).' '.Html::button('<b><i class="fa fa-cog"></i></b>'.Yii::t('fe', 'Proses'), [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-proses',
                            'action' => Url::to([
                                '/pendaftaran/pendaftaran-online/proses',
                                'id' => $primaryKey
                            ]),
                            'data-target' => '#modal_backdrop_full',
                            'data-options' => 'link',
                            'data-toggle' => 'modal',
                        ]);
                    } else if ($value['status_daftar_ol'] == DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI) {
                        $color = '#26A65B';
                        $font = '#FFFfff';
                        $value['aksi'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'; margin-bottom:5px;">'.Yii::t('fe', 'Disetujui').'</span> '.
                            Html::a('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', 'Cetak'), Url::to([
                            '/pendaftaran/pendaftaran-online/cetak-bukti',
                            'id' => $primaryKey
                        ]), [
                            'class' => 'btn btn-primary btn-labeled btn-xs btn-cetak',
                            'target' => "_blank",
                        ]);
                    } else {
                        $color = '#D24D57';
                        $font = '#FFFfff';
                        $value['aksi'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.Yii::t('fe', 'Ditolak').'</span> ';
                    }

                    unset($value['fasilitasrs_id']);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Method untuk mendapatkan data penjamin
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPenjaminPortal($penjamin_id = null, $carabayar_id = null)
    {
        try {
            $params = '';
            $return = [];

            if ($penjamin_id) {
                $params = '?penjamin_id='.$penjamin_id;
            }

            if ($carabayar_id) {
                if ($penjamin_id) {
                    $params = $params.'&carabayar_id='.$carabayar_id;
                } else {
                    $params = '?carabayar_id='.$carabayar_id;
                }
            }

            $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-online/get-penjamin-portal'.$params);
            $data = json_decode($restPendaftaran->getBody(), true);

            if (isset($data['metadata']['status']) && $data['metadata']['status'] == 200) {
                $data = $data['response'];

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $return[$key]['id'] = $value['penjamin_id'];
                        $return[$key]['text'] = $value['penjamin_nama'];
                    }
                }
            }

            return json_encode($return);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}