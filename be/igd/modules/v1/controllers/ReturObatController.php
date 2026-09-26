<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-21 10:55:03
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\PermintaanRetur;
use app\modules\v1\models\PermintaanReturDetail;
use app\modules\v1\models\PermintaanReturView;
use app\modules\v1\models\PermintaanReturDetailView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\StokObatPasien;
use app\modules\v1\models\StokReturPasienView;

class ReturObatController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ReturObat';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /* Fungsi untuk mendapatkan list data riwayat permintaan retur */
    public function actionGetDataRiwayatPermintaanRetur()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];

            $data = $this->getDataRiwayatPermintaanRetur($pendaftaran_id);
            $dataDetail = $this->getDataRiwayatPermintaanReturDetail($data);

            return [
                'riwayatPermintaanRetur' => $data,
                'riwayatPermintaanReturDetail' => $dataDetail,
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* Fungsi untuk mendapatkan data retur obat */
    public function actionGetDataReturObat()
    {
        try {
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];

            $data = StokReturPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->all();

            return [
                'dataReturObat' => $data
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* Fungsi untuk mendapatkan data detail permintaan retur */
    public function actionGetDataDetailReturObat()
    {
        try {
            $params = Yii::$app->request->get();
            $permintaanretur_id = $params['id'];

            $data = PermintaanReturDetailView::find()->where(['permintaanretur_id' => $permintaanretur_id])->all();

            return [
                'dataReturObat' => $data
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* Fungsi untuk mendapatkan data retur obat ubah */
    public function actionGetDataReturObatUntukUbah()
    {
        try {
            $additionalField = [];
            $params = Yii::$app->request->get();
            $pendaftaran_id = $params['id'];
            $permintaanretur_id = $params['permintaanretur_id'];

            $stokReturPasien = StokReturPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->all();
            $permintaanRetur = PermintaanReturDetail::find()->where(['permintaanretur_id' => $permintaanretur_id])->all();

            if (!empty($stokReturPasien)) {
                foreach ($stokReturPasien as $key => $value) {
                    if (!empty($permintaanRetur)) {
                        foreach ($permintaanRetur as $index => $content) {
                            if (($value['obatalkespasien_id'] == $content['obatalkespasien_id']) && ($value['obatalkes_id'] == $content['obatalkes_id'])) {
                                $additionalField[$key]['jumlah_retur'] = $content['qty_retur'];
                                $additionalField[$key]['alasan'] = $content['alasan'];
                                $additionalField[$key]['permintaanreturdetail_id'] = $content['permintaanreturdetail_id'];
                            }
                        }
                    }
                }
            }

            return [
                'dataReturObat' => $stokReturPasien,
                'additionalField' => $additionalField,
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* Fungsi untuk menyimpan permintaan retur */
    public function actionSimpanPermintaanRetur()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $post = Yii::$app->request->post();
            $isUbah = false;
            $permintaanRetur = $post['permintaanRetur'];
            $permintaanReturDetail = $post['permintaanReturDetail'];
            $signaObat = ArrayHelper::map(SignaObat::find()->all(), 'signa_nama', 'signa_id');

            if (isset($permintaanRetur['permintaanretur_id']) && $permintaanRetur['permintaanretur_id'] != '') {
                $model = PermintaanRetur::findOne($permintaanRetur['permintaanretur_id']);
            } else {
                $model = new PermintaanRetur;
            }

            if ($model->load($permintaanRetur, '')) {
                if ($model->save()) {
                    if (!empty($permintaanReturDetail)) {
                        foreach ($permintaanReturDetail as $key => $value) {
                            $permintaanReturDetail[$key]['permintaanretur_id'] = $model->permintaanretur_id;
                            $permintaanReturDetail[$key]['signa'] = isset($signaObat[$value['signa']]) ? $signaObat[$value['signa']] : null;
                            $lastQty = 0;

                            if ($value['permintaanreturdetail_id'] != '') {
                                $isUbah = true;
                                $modelPermintaanReturDetail = PermintaanReturDetail::findOne($value['permintaanreturdetail_id']);
                                $lastQty = $modelPermintaanReturDetail->qty_retur;
                                $modelPermintaanReturDetail->load($permintaanReturDetail[$key], '');
                                $modelPermintaanReturDetail->save();
                            } else {
                                unset($value['permintaanreturdetail_id']);
                                $modelPermintaanReturDetail = new PermintaanReturDetail;
                                $modelPermintaanReturDetail->load($permintaanReturDetail[$key], '');
                                $modelPermintaanReturDetail->save();
                            }

                            $stokObatPasienModel = StokObatPasien::find()->where(['obatalkespasien_id' => $value['obatalkespasien_id']])->one();

                            if ($isUbah == true) {
                                if ($stokObatPasienModel) {
                                    $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa + (float)$lastQty;
                                    $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa - (float)$value['qty_retur'];
                                    $stokObatPasienModel->save();
                                    $isUbah = false;
                                }
                            } else {
                                if ($stokObatPasienModel) {
                                    $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa - (float)$value['qty_retur'];
                                    $stokObatPasienModel->save();
                                }
                            }
                        }

                        $transaction->commit();
                        return [
                            'message' => 'Data Berhasil di simpan'
                        ];
                    }
                }
            }

            return ['message' => 'Data gagal disimpan'];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* Fungsi untuk menghapus permintaan retur */
    public function actionHapusPermintaanRetur()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $id = Yii::$app->request->post('id');

            if (isset($id) && $id != '') {
                $permintaanRetur = PermintaanRetur::findOne($id);

                if ($permintaanRetur) {
                    $permintaanReturDetail = PermintaanReturDetail::find()->where(['permintaanretur_id' => $id])->all();

                    if (!empty($permintaanReturDetail)) {
                        foreach ($permintaanReturDetail as $key => $value) {
                            $stokObatPasienModel = StokObatPasien::find()->where(['obatalkespasien_id' => $value['obatalkespasien_id']])->one();

                            if ($stokObatPasienModel) {
                                $stokObatPasienModel->stok_retur_sisa = (float)$stokObatPasienModel->stok_retur_sisa + (float)$value['qty_retur'];
                                $stokObatPasienModel->save();
                            }
                        }
                    }

                    (new PermintaanReturDetail)->delete([
                        'permintaanretur_id' => $id
                    ]);
                }

                (new PermintaanRetur)->delete($id);
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Gagal menghapus data'
                ];
            }

            $transaction->commit();
            return [
                'message' => 'Data Berhasil di hapus'
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakRiwayatRetur
    * @attribute #table# => table
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #tanggal# => tanggal
    **/
    public function actionCetakRiwayatRetur()
    {
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $riwayatRetur = [];

        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();

        $riwayatRetur = (new \yii\db\Query())
        ->select([
            PermintaanReturView::tableName().'.*',
        ])
        ->from(PermintaanReturView::tableName())
        ->where([PermintaanReturView::tableName().'.pendaftaran_id' => $pendaftaran_id])
        ->orderBy([PermintaanReturView::tableName().'.tgl_permintaanretur' => SORT_DESC])
        ->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#table#' => $this->renderPartial('pdf', [
                'data' => $riwayatRetur,
            ]),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#tanggal#' => date('d F Y h:i:s', strtotime('NOW')),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakDetailRetur
    * @attribute #table# => table
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #kepala_ruangan# => kepala_ruangan
    * @attribute #pegawai_approve# => pegawai_approve
    * @attribute #tanggal# => tanggal
    **/
    public function actionCetakDetailRetur()
    {
        $permintaanretur_id = DocoHelpers::decrypt(Yii::$app->request->get('permintaanretur_id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $jabatan_id = DocoConstants::VAR_JABATAN_KEPALA_RUANGAN;
        $detailRetur = [];

        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();
        $kepalaRuangan = PegawaiView::find()->where(['jabatan_id' => $jabatan_id, 'ruangan_id' => $ruangan_id])->one();

        $permintaanRetur = (new \yii\db\Query())
        ->select([
            PermintaanReturView::tableName().'.*',
        ])
        ->from(PermintaanReturView::tableName())
        ->where([PermintaanReturView::tableName().'.permintaanretur_id' => $permintaanretur_id])
        ->one();

        $detailRetur = (new \yii\db\Query())
        ->select([
            PermintaanReturDetailView::tableName().'.*',
        ])
        ->from(PermintaanReturDetailView::tableName())
        ->where([PermintaanReturDetailView::tableName().'.permintaanretur_id' => $permintaanretur_id])
        ->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#table#' => $this->renderPartial('pdf_detail', [
                'data' => $detailRetur,
            ]),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#kepala_ruangan#' => isset($kepalaRuangan['nama_pegawai']) ? $kepalaRuangan['nama_pegawai'] : '',
            '#pegawai_approve#' => $permintaanRetur['pegawai_approve'],
            '#tanggal#' => date('d F Y h:i:s', strtotime('NOW')),
        ];
        $print->Output();
    }

    /* Fungsi untuk mendapatkan data riwayat permintaan retur */
    private function getDataRiwayatPermintaanRetur($pendaftaran_id)
    {
        try {
            $pemberianObat = (new \yii\db\Query())
            ->select([
                PermintaanReturView::tableName().'.*',
            ])
            ->from(PermintaanReturView::tableName())
            ->where([PermintaanReturView::tableName().'.pendaftaran_id' => $pendaftaran_id])
            ->all();

            return $pemberianObat;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* Fungsi untuk mendapatkan data riwayat permintaan retur detail */
    private function getDataRiwayatPermintaanReturDetail($data)
    {
        try {
            $permintaanReturDetail = [];

            if (!empty($data)) {
                foreach ($data as $value) {
                    $model = PermintaanReturDetailView::find()->where(['permintaanretur_id' => $value['permintaanretur_id']])->all();

                    if (!empty($model)) {
                        foreach ($model as $content) {
                            $permintaanReturDetail[] = $content;
                        }
                    }
                }
            }

            return $permintaanReturDetail;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
