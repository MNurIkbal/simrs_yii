<?php

/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-07-23 14:18:39
 */

// Namespace
namespace app\modules\ranap\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

// Using model
use app\modules\ranap\models\InfoPasienRanapForm;
use app\modules\ranap\models\RekonsiliasiObatForm;
use app\modules\ranap\models\InfoPasienRanap;
use app\modules\ranap\models\PermintaanKonsulForm;


trait PemeriksaanPemintaanKonsulTrait
{
    public function actionPermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $is_modal = $request->get('is_modal', null);
            $preview_only = $request->get('preview_only', false);
            $pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $getPendaftaranId = $request->get('id', null);
            $model = new PermintaanKonsulForm;
            $model->scenario = 'form';
            $model->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $model->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $model->cppt_id = $request->get('cppt_id', null);
            $listRequest = [
                'lookup_type' => 'jenis_konsul',
                'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
                'pendaftaran_id' => !empty($getPendaftaranId) ? DocoHelpers::decrypt($getPendaftaranId) : $this->_data_pasien['pendaftaran_id']
            ];
            $response = $this->_restRanap->post('allow/list-permintaan-konsul', ['form_params' => $listRequest]);
            $body = json_decode($response->getBody(), True);
            $listfilter = $body['response'];
            $pasienId = $this->_data_pasien['pasien_id'];
            Yii::$app->session->get('user_identity');

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa($this->_data_pasien['pendaftaran_id']);
            if ($status_disabled == true) {
                $hide = 'hide()';
            } else {
                $status_disabled = 'false';
            }

            $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;

            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
            $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
            if ($isTitipan) {
                $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
            }

            $infoPasien = [
                'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan
            ];

            if($preview_only == true) {
                return $this->renderAjax('permintaan-konsul/__view_permintaan_konsul', get_defined_vars());
            }
            if ($is_modal == true) {
                return $this->renderAjax('permintaan-konsul/__modal_permintaan_konsul', get_defined_vars());
            }
            return $this->renderAjax('permintaan-konsul/__permintaan_konsul', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionGetPermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $permintaankonsul_id = DocoHelpers::decrypt($get['permintaankonsul_id']);
            $preview_only = $request->get('preview_only', false);
            $request = $this->_restRanap->get('pemeriksaan-permintaan-konsul/data-pasien-konsul?permintaankonsul_id=' . (int)$permintaankonsul_id . '&preview_only=' . $preview_only, ['form_params' => []]);
            $response = json_decode($request->getBody(), true);
            // echo "<pre>"; var_dump(DocoHelpers::response($response['response']));die();
            return DocoHelpers::response($response['response']);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionGetListPermintaanKonsul()
    {
        try {
            $userIdentity = Yii::$app->session->get('user_identity');
            $request = Yii::$app->request;

            $preview_only = $request->get('preview_only', false);
            $listRequest = ['pendaftaran_id' => DocoHelpers::decrypt($request->get('id')), 'preview_only' => $preview_only];
            $response = $this->_restRanap->request(
                'POST',
                'pemeriksaan-permintaan-konsul/index?',
                ['form_params' =>  $listRequest]
            );
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            rsort($body['response']['data']);
            $labelStatus = 'padding-left: 10px !important;font-weight: bold;';
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $permintaankonsul_id = DocoHelpers::encrypt($value['permintaankonsul_id']);
                $value['aksi'] = '&nbsp;&nbsp;';
                if($preview_only == true) { 
                    $value['aksi'] .= Html::a(
                        '<b><i class="fa fa-file-pdf-o"></i></b> ' . \Yii::t('fe', 'Permintaan Konsultasi'),
                        Url::to(['cetak-permintaan-pdf', 'permintaankonsul_id' => $permintaankonsul_id]),
                        ['class' => 'btn btn-info btn-labeled btn-xs', 'target' => '_blank']
                    );

                    $value['aksi']  .= '&nbsp;&nbsp;';

                    if($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && $value['jawaban_konsul'] != NULL) {
                        $value['aksi'] .= Html::a(
                            '<b><i class="fa fa-file-pdf-o"></i></b>' . \Yii::t('fe', 'Jawaban konsultasi'),
                            Url::to(['cetak-jawaban-pdf', 'permintaankonsul_id' => $permintaankonsul_id]),
                            ['class' => 'btn btn-info btn-labeled btn-xs', 'target' => '_blank']
                        );
                    } else {
                            $value['aksi'] .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', '-') . '</span>&nbsp;&nbsp;';
                    }
                } else {
                    if ($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_BATAL) {
                        $value['aksi'] .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', 'Dibatalkan') . '</span>&nbsp;&nbsp;';
                    } else {
                        // if($userIdentity['id'] == $value['creator'] ){
                        $value['aksi']  .= Html::a(
                            '<b><i class="fa fa-file-pdf-o"></i></b> ' . \Yii::t('fe', 'Permintaan Konsultasi'),
                            Url::to(['cetak-permintaan-pdf', 'permintaankonsul_id' => $permintaankonsul_id]),
                            ['class' => 'btn btn-info btn-labeled btn-xs', 'target' => '_blank']
                        );
                        $value['aksi']  .= '&nbsp;&nbsp;';
                        if (($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_TIDAK_SETUJU && $value['jawaban_konsul'] == NULL)) {
                            $value['aksi'] .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', 'Ditolak') . '</span>&nbsp;&nbsp;';
                        } else if (($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && $value['jawaban_konsul'] == NULL)) {
                            $value['aksi'] .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', 'Disetujui') . '</span>&nbsp;&nbsp;';
                        } elseif (($value['status_konsul'] == DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU && $value['jawaban_konsul'] != NULL)) {
                            $value['aksi'] .= Html::a(
                                '<b><i class="fa fa-file-pdf-o"></i></b>' . \Yii::t('fe', 'Jawaban konsultasi'),
                                Url::to(['cetak-jawaban-pdf', 'permintaankonsul_id' => $permintaankonsul_id]),
                                ['class' => 'btn btn-info btn-labeled btn-xs', 'target' => '_blank']
                            );
                        } elseif ($userIdentity['id'] == $value['creator']) {
                            $value['aksi']  .= Html::a(
                                '<b><i class="fa fa-times"></i></b>' . \Yii::t('fe', 'Batal'),
                                '#',
                                [
                                    'class' => 'btn btn-indian-red btn-xs btn-labeled data-batal',
                                    'action' =>  'batal-permintaan-konsul?id=' . $this->helper->encrypt($value['pendaftaran_id']) . '&consuleId=' . $permintaankonsul_id,
                                    'data-popup' => "tooltip",
                                    'title' => \Yii::t('fe', 'Batal'),
                                    'onclick' => 'batal(this)'
                                ]
                            );
                            $value['aksi']  .= '&nbsp;&nbsp;';
                            $value['aksi']  .= Html::a(
                                '<b><i class="fa fa-pencil"></i></b>' . \Yii::t('fe', 'Ubah'),
                                '#tab-ranap',
                                [
                                    'class' => 'btn btn-info btn-xs btn-labeled data-ubah',
                                    'action' =>  'ubah-permintaan-konsul?id=' . $permintaankonsul_id,
                                    'data-target' => "ranap/pemeriksaan-rawat-inap/periksa?id" . $permintaankonsul_id,
                                    'data-popup' => "tooltip",
                                    'onClick' => 'ubahData("' . $permintaankonsul_id . '")',
                                    'title' => \Yii::t('fe', 'Ubah'),
                                ]
                            );
                        } else {
                            $value['aksi'] .= '<span style="' . $labelStatus . '">' . \Yii::t('fe', '-') . '</span>&nbsp;&nbsp;';
                        }
                        // }else{
                        //      $value['aksi'] .= '<span style="'.$labelStatus.'">'.\Yii::t('fe', '-').'</span>&nbsp;&nbsp;';
                        // }                    
                    }
                }

                $value['waktu_permintaan'] = date('d/m/Y H:i:s', strtotime($value['waktu_permintaan']));
                $value['rowNum'] = $no;
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

    public function actionSavePermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $model = new PermintaanKonsulForm;
            $post = $request->post('PermintaanKonsulForm', []);
            $post['waktu_permintaan'] = date('Y-m-d H:i:s');
            $post['cppt_id'] = $this->helper->decrypt($post['cppt_id']);
            $model->scenario = 'form';
            $model->attributes = $post;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            // dummy
            $model->dokter_nama = '0';
            $model->jenis_konsul_nama = '0';

            if ($model->validate()) {
                $request = $this->_restRanap->post('pemeriksaan-permintaan-konsul/create-permintaan-konsul', [
                    'form_params' => $post
                ]);
                $response = json_decode($request->getBody(), true);
                return json_encode($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionUpdatePermintaanKonsul()
    {
        try {
            $request = Yii::$app->request;
            $model = new PermintaanKonsulForm;
            $post = $request->post();
            $post['PermintaanKonsulForm']['waktu_permintaan'] = date('Y-m-d H:i:s');
            $model->scenario = 'form';
            $model->attributes = $post['PermintaanKonsulForm'];
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            // dummy
            $model->dokter_id = 0;
            $model->dokter_nama = '0';
            $model->jenis_konsul = 0;
            $model->jenis_konsul_nama = '0';

            if ($model->validate()) {
                $request = $this->_restRanap->post('pemeriksaan-permintaan-konsul/update-permintaan-konsul', [
                    'form_params' => $post
                ]);
                $response = json_decode($request->getBody(), true);
                return json_encode($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
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
        $dateEncrypt = DocoHelpers::encrypt($date);
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

    public function actionBatalPermintaanKonsul($consuleId)
    {
        $id = DocoHelpers::decrypt($consuleId);
        try {
            $response = $this->_restRanap->post('pemeriksaan-permintaan-konsul/batal-permintaan-konsul', [
                'form_params' => ['permintaankonsul_id' => $id]
            ]);
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
}
