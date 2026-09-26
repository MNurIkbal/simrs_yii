<?php

/**
 * @Author: Iqbal@docootel.com
 * @Date:   2018-07-23 17:00:42
 * @Description:
 */

namespace Doco\ranap\controllers;

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

use app\modules\ranap\models\InfoPasienRanapForm;
use app\modules\ranap\models\InfoPasienRanap;
use app\modules\ranap\models\PendaftaranForm;
use app\modules\ranap\models\PasienBatalPeriksaForm;
use app\modules\ranap\models\PermintaanKonsulForm;


class InfPasienKonsulController extends DocoController
{
    protected $allowAction = ['*'];

    protected $_restRanap;
    protected $_restMaster;
    protected $_module = 'ranap/inf-pasien-konsul/';

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $dataList = [];
        try {
            $userIdentity = Yii::$app->session->get('user_identity');
            $response = $this->_restRanap->get('allow/list-filter-pasien-konsul?dokter_id=' . $userIdentity['id_pegawai'], ['form_params' => []]);
            $body = json_decode($response->getBody(), TRUE);
            $response = $body['response'];
            $dataList['pegawai'] = ArrayHelper::map($response['master']['pegawai'], 'pegawai_id', 'nama_pegawai');
            $dataList['carabayar'] = ArrayHelper::map($response['master']['carabayar'], 'carabayar_id', 'carabayar_nama');
            $dataList['kelaspelayanan'] = ArrayHelper::map($response['master']['kelaspelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama');
            $dataList['ruangan'] = ArrayHelper::map($response['master']['ruangan'], 'ruangan_id', 'ruangan_nama');
            $dataList['jenis_konsul'] = ArrayHelper::map($response['lookup']['jenis_konsul'], 'lookup_id', 'lookup_name');
            $_isSetuju = DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU;
            $_jenis_rb = DocoConstants::JNS_KNSL_RB;

            $hasAccessDetail = DocoHelpers::checkButtonAccess('/ranap/inf-pasien-konsul', 'data-pasien-konsul');
            $hasAccessPeriksa = DocoHelpers::checkButtonAccess('/ranap/worklist', 'view');

            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            $dataList['pegawai'] = [];
            $dataList['carabayar'] = [];
            $dataList['kelaspelayanan'] = [];
            $dataList['jenis_konsul'] = [];
        }
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
            $userIdentity = Yii::$app->session->get('user_identity');
            // $yiiRestfulParams['advanced-filter']['dokter_id'] = $userIdentity['id_pegawai'];
            $yiiRestfulParams['advanced-filter']['kelompokpegawai_id'] = Yii::$app->user->identity->kelompokpegawai_id;
            $response = $this->_restRanap->get('inf-pasien-konsul/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $data = $body['response']['data'];

            $row = [];
            $no = $request->get('start', 1);
            $labelStatus = 'padding-left: 10px !important;font-weight: bold;';

            $hasAccessSetujui = DocoHelpers::checkButtonAccess('/ranap/inf-pasien-konsul', 'setujui-permintaan-konsul');
            $hasAccessJawaban = DocoHelpers::checkButtonAccess('/ranap/inf-pasien-konsul', 'jawaban-permintaan-konsul');
            $hasAccessDetail = DocoHelpers::checkButtonAccess('/ranap/inf-pasien-konsul', 'data-pasien-konsul');

            foreach ($data as $key => $value) {
                $no++;
                $permintaankonsul_id = DocoHelpers::encrypt($value['permintaankonsul_id']);
                $value['status_konsul_nama'] = '';
                if ($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT) {
                    $value['aksi'] = Html::button('<i class="fa fa-check-square-o"></i>&nbsp;' . \Yii::t('fe', 'Persetujuan'), [
                        'class' => 'btn btn-labeled btn-primary btn-xs',
                        'action' => Url::home() . $this->_module . 'persetujuan?permintaankonsul_id=' . $permintaankonsul_id,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip", 'data-placement' => 'bottom',
                        'title' => \Yii::t('fe', 'Persetujuan'),
                        'style' => 'padding-left: 10px !important;'.(!$hasAccessSetujui ? 'display:none;' : ''),
                    ]);
                    $value['status_konsul_nama'] = \Yii::t('fe', '-');
                } elseif ($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL) {
                    $value['aksi'] = '<span style="' . $labelStatus . '">' . \Yii::t('fe', 'Dibatalkan') . '</span>&nbsp;&nbsp;';
                    $value['status_konsul_nama'] = \Yii::t('fe', 'Dibatalkan');
                } elseif ($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_TIDAK_SETUJU) {
                    $value['aksi'] = '<span style="' . $labelStatus . '">' . \Yii::t('fe', 'Ditolak') . '</span>&nbsp;&nbsp;';
                    $value['status_konsul_nama'] = \Yii::t('fe', 'Ditolak');
                } elseif (($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && $value['jawaban_konsul'] == NULL)) {
                    $value['aksi'] = Html::button('<i class="fa fa-check-square-o""></i>&nbsp;' . \Yii::t('fe', 'Buat jawaban'), [
                        'class' => 'btn btn-labeled btn-primary btn-xs',
                        'action' => Url::home() . $this->_module . 'buat-jawaban?permintaankonsul_id=' . $permintaankonsul_id,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip", 'data-placement' => 'bottom',
                        'title' => \Yii::t('fe', 'Buat jawaban'),
                        'style' => 'padding-left: 10px !important;'.(!$hasAccessJawaban ? 'display:none;' : ''),
                    ]);
                    $value['status_konsul_nama'] = \Yii::t('fe', 'Disetujui');
                } elseif (($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && $value['jawaban_konsul'] != NULL)) {
                    $value['aksi'] = Html::button('<i class="fa fa fa-reorder"></i>&nbsp;' . \Yii::t('fe', 'Lihat Jawaban'), [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'action' => Url::home() . $this->_module . 'detail?id=' . $permintaankonsul_id,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-popup' => "tooltip", 'data-placement' => 'bottom',
                        'title' => \Yii::t('fe', 'Lihat Jawaban'),
                        'style' => 'padding-left: 10px !important;'.(!$hasAccessDetail ? 'display:none;' : ''),
                    ]);
                    $value['status_konsul_nama'] = \Yii::t('fe', 'Disetujui');
                } else {
                    $value['aksi'] = '<span style="' . $labelStatus . '">' . \Yii::t('fe', 'Done') . '</span>&nbsp;&nbsp;';
                    $value['status_konsul_nama'] = \Yii::t('fe', 'Done');
                }

                $tgl_admisi = date('Y-m-d', strtotime($value['tgl_admisi'])); //date from database 
                $now = date('Y-m-d', strtotime('NOW'));
                $value['permintaankonsul_id'] = $permintaankonsul_id;
                $value['primary'] = $permintaankonsul_id;
                $value['id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['waktu_permintaan'] = DocoHelpers::convDateTime($value['waktu_permintaan'], true);
                $value['info_kunjungan'] = $value['no_pendaftaran'] . '<br>'
                    . $value['no_rekam_medik'] . ' - ' . $value['nama_pasien']
                    . ' (' . ($value['jenis_kelamin_id'] == 15 ? 'L' : 'P') . ')';
                $value['info_carabayar'] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
                $value['hakKelas'] = ($value['kls_hak'] ?: '-') . ' / ' . $value['kls_rawat'];
                $value['info_kamar'] = $value['ruangan_nama'] . '<br>'
                    . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
                $date1 = new \DateTime($tgl_admisi);
                $date2 = new \DateTime($now);
                $diff = $date1->diff($date2);
                $value['hariRawat'] = $diff->days;
                $value['rowNum'] = $no;

                $value['url_konsul'] = '';
                if ( $value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU ) {
                    $value['url_konsul'] = DocoHelpers::crossUrl('jumpto', [
                        'ruangan_id' => $value['ruangan_id'],
                        'instalasi_id' => Yii::$app->docoVars->workspace("instalasi_id"),
                        'modul' => 'ranap',
                        'url' => 'ranap/pemeriksaan-rawat-inap/periksa?id='. DocoHelpers::encrypt($value['pendaftaran_id']) .'&konsul_id=' . $permintaankonsul_id,
                    ]);
                }
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }


    public function actionPersetujuan()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = DocoHelpers::decrypt($get['permintaankonsul_id']);
            $getDataPermintaan = $this->GetDetailPermintaanKonsul($id);
            $title = 'Persetujuan Permintaan Konsultasi';
            $model = new PermintaanKonsulForm;
            $persetujuan = [
                DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU => 'Setujui',
                DocoConstants::STATUS_PERMINTAAN_KONSUL_TIDAK_SETUJU => 'Tolak'
            ];

            return $this->renderPartial('persetujuan_form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionBuatJawaban()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = DocoHelpers::decrypt($get['permintaankonsul_id']);
            $getDataPermintaan = $this->GetDetailPermintaanKonsul($id);
            $title = 'Jawaban Permintaan Konsultasi';
            $model = new PermintaanKonsulForm;
            $model->scenario = 'jawaban';

            return $this->renderPartial('jawaban_form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = DocoHelpers::decrypt($get['id']);
            $getDataPermintaan = $this->GetDetailPermintaanKonsul($id);
            $title = 'Detail Permintaan Konsultasi';
            $permintaankonsul_id = DocoHelpers::encrypt($getDataPermintaan['permintaankonsul_id']);

            return $this->renderPartial('detail_form', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function GetDetailPermintaanKonsul($id = null)
    {
        try {
            $response = $this->_restRanap->request('GET', 'inf-pasien-konsul/data-pasien-konsul', [
                'query' => ['permintaankonsul_id' => $id]
            ]);
            $row = [];
            $body = json_decode($response->getBody(), TRUE);
            $return = $body['response'];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionSetujui()
    {
        try {
            $request = Yii::$app->request;
            $model = new PermintaanKonsulForm;
            $post = $request->post('PermintaanKonsulForm');
            $permintaankonsul_id = $post['permintaankonsul_id'];
            $statusKonsul = $post['status_konsul'];
            $model->scenario = 'persetujuan';
            $model->attributes = $post;
            if ($model->validate()) {
                if ($post) {
                    $response = $this->_restRanap->post('inf-pasien-konsul/setujui-permintaan-konsul', [
                        'form_params' => [
                            'permintaankonsul_id' => $permintaankonsul_id,
                            'status_konsul' => $statusKonsul,
                            'waktu_persetujuan' => date('Y-m-d h:i:s', strtotime('NOW')),
                        ]
                    ]);
                    $body = json_decode($response->getBody(), True);
                    $data = [
                        'title' => \Yii::t('fe', 'Proses berhasil') . " !",
                        'text' => \Yii::t('fe', "Status berhasil diubah.")
                    ];
                    return DocoHelpers::responseTemplate(
                        $response->getStatusCode(),
                        "OK",
                        [],
                        $data
                    );
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::response($response, 422, 'PermintaanKonsulForm');
            }
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ') . " !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            var_dump(json_decode($e->getResponse()->getBody(), TRUE));
            exit;
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionCetakPermintaanPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $date = date('Y-m-d h:i:s', strtotime('NOW'));
        $path = Yii::getAlias("@download") . "/permintaan-konsul.pdf";
        $permintaankonsul_id = $request->get('permintaankonsul_id');
        $id = DocoHelpers::decrypt($permintaankonsul_id);
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $urlReport = 'permintaan-konsultasi';
        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport, [
                   'queryParameter' => [
                        'permintaankonsul_id' => $id,
                        'pegawai_id'=>$pegawai_id,

                   ],
            ]);
        }
      
    }

    public function actionCetakJawabanPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $date = date('Y-m-d h:i:s', strtotime('NOW'));
        $path = Yii::getAlias("@download") . "/jawaban-konsul.pdf";
        $permintaankonsul_id = $request->get('permintaankonsul_id');
        $id = DocoHelpers::decrypt($permintaankonsul_id);
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $urlReport = 'jawaban-konsultasi';
        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport, [
                   'queryParameter' => [
                        'permintaankonsul_id' => $id,
                        'pegawai_id'=>$pegawai_id,

                   ],
            ]);
        }
       
    }

    public function actionJawaban()
    {
        try {
            $request = Yii::$app->request;
            $model = new PermintaanKonsulForm;
            $post = $request->post('PermintaanKonsulForm');
            $permintaankonsul_id = $post['permintaankonsul_id'];
            $jawaban_konsul = $post['jawaban_konsul'];
            $model->scenario = 'jawaban';
            $model->attributes = $post;
            // $model->load($post);
            if ($model->validate()) {
                if ($post) {
                    $response = $this->_restRanap->post('inf-pasien-konsul/jawaban-permintaan-konsul', [
                        'form_params' => [
                            'permintaankonsul_id' => $permintaankonsul_id,
                            'jawaban_konsul' => $jawaban_konsul,
                        ]
                    ]);
                    $body = json_decode($response->getBody(), True);
                    $data = [
                        'title' => \Yii::t('fe', 'Proses berhasil') . " !",
                        'text' => \Yii::t('fe', "Status berhasil diubah.")
                    ];
                    return DocoHelpers::responseTemplate(
                        $response->getStatusCode(),
                        "OK",
                        [],
                        $data
                    );
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::response($response, 422, 'PermintaanKonsulForm');
            }
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ') . " !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            if (isset($_GET['advanced-filter'])) {
                $filter = $_GET['advanced-filter'];
                $between = false;
                $start = date('Y-m-01 00:00:00');
                $end = date('Y-m-d 23:59:00');
                if (isset($filter['waktu_permintaan'])) {
                    $tgl_pendaftaran_range = explode(" - ", $filter['waktu_permintaan']);
                    $tgl_awal = $tgl_pendaftaran_range[0];
                    $tgl_akhir = $tgl_pendaftaran_range[1];
                    $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                    $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                    unset($filter['waktu_permintaan']); // Unset Advanced Filter  date range
                    $between = true;
                    $yiiRestfulParams['advanced-filter']['waktu_permintaan_awal'] = $tgl_awal_format;
                    $yiiRestfulParams['advanced-filter']['waktu_permintaan_akhir'] = $tgl_akhir_format;
                }

                if (isset($filter['gab_noRmPdft'])) {
                    $explode = explode(" / ", $filter['gab_noRmPdft']);
                    $yiiRestfulParams['advanced-filter']['no_rekam_medik'] = $explode[0];
                    $yiiRestfulParams['advanced-filter']['no_pendaftaran'] = $explode[1];
                }

                if (isset($filter['caraBayarPenjamin'])) {
                    $explode = explode(" / ", $filter['caraBayarPenjamin']);
                    $yiiRestfulParams['advanced-filter']['carabayar_nama'] = $explode[0];
                    $yiiRestfulParams['advanced-filter']['penjamin_nama'] = $explode[1];
                }

                if (isset($filter['noRuangan'])) {
                    $explodeSpace = explode("@#", $filter['noRuangan']);
                    $yiiRestfulParams['advanced-filter']['ruangan_nama'] = $explode[0];
                    $yiiRestfulParams['advanced-filter']['kamarruangan_nokamar'] = $explode[1];
                    $yiiRestfulParams['advanced-filter']['no_tempattidur'] = $explode[2];
                }
            }
            $userIdentity = Yii::$app->session->get('user_identity');
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
            $yiiRestfulParams['advanced-filter']['dokter_id'] = $userIdentity['id_pegawai'];

            $path = Yii::getAlias("@download") . "/informasi-pasien-konsul.pdf";
            $response = $this->_restRanap->get('inf-pasien-konsul/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

            if (isset($_GET['advanced-filter'])) {
                $filter = $_GET['advanced-filter'];
                $between = false;
                $start = date('Y-m-01 00:00:00');
                $end = date('Y-m-d 23:59:00');
                if (isset($filter['waktu_permintaan'])) {
                    $tgl_pendaftaran_range = explode(" - ", $filter['waktu_permintaan']);
                    $tgl_awal = $tgl_pendaftaran_range[0];
                    $tgl_akhir = $tgl_pendaftaran_range[1];
                    $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                    $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                    unset($filter['waktu_permintaan']); // Unset Advanced Filter  date range
                    $between = true;
                    $yiiRestfulParams['advanced-filter']['waktu_permintaan_awal'] = $tgl_awal_format;
                    $yiiRestfulParams['advanced-filter']['waktu_permintaan_akhir'] = $tgl_akhir_format;
                }

                if (isset($filter['gab_noRmPdft'])) {
                    $explode = explode(" / ", $filter['gab_noRmPdft']);
                    $yiiRestfulParams['advanced-filter']['no_rekam_medik'] = $explode[0];
                    $yiiRestfulParams['advanced-filter']['no_pendaftaran'] = $explode[1];
                }

                if (isset($filter['caraBayarPenjamin'])) {
                    $explode = explode(" / ", $filter['caraBayarPenjamin']);
                    $yiiRestfulParams['advanced-filter']['carabayar_nama'] = $explode[0];
                    $yiiRestfulParams['advanced-filter']['penjamin_nama'] = $explode[1];
                }

                if (isset($filter['noRuangan'])) {
                    $explodeSpace = explode("@#", $filter['noRuangan']);
                    $yiiRestfulParams['advanced-filter']['ruangan_nama'] = $explode[0];
                    $yiiRestfulParams['advanced-filter']['kamarruangan_nokamar'] = $explode[1];
                    $yiiRestfulParams['advanced-filter']['no_tempattidur'] = $explode[2];
                }
            }
            $userIdentity = Yii::$app->session->get('user_identity');
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
            $yiiRestfulParams['advanced-filter']['dokter_id'] = $userIdentity['id_pegawai'];

            $path = Yii::getAlias("@download") . "/informasi-pasien-konsul.xlsx";

            $response = $this->_restRanap->get('inf-pasien-konsul/export-excel', [
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            return $e;
        } catch (\Exception $e) {
            return $e;
        }
    }
}

    /*public function actionExportExcel($jenis)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_admisi'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_admisi']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_admisi_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_admisi_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_admisi']);
        }
        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/export-excel?jenis='.$jenis.'&ruangan='.Yii::$app->docoVars->workspace("ruangan_id").'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($url);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }*/
