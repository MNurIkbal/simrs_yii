<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Penyimpanan Dokumen Rm
 * @copyright 31 Mei 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoPosisiDokRekamMedik;
use app\modules\v1\models\LokasiRakRekamMedik;
use app\modules\v1\models\Subrak;
use app\modules\v1\models\DokRekamMedis;

class TransaksiPenyimpananController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPosisiDokRekamMedik';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["create"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    public function actionGetApi($id)
    {
        try {
            // get posisi dok rm
            $posisi = new InfoPosisiDokRekamMedik;
            $query_posisi = $posisi::find();

            // get posisi dok rm by id
            $posisiById = InfoPosisiDokRekamMedik::findOne(['dokrekammedis_id'=>$id]);

            // get rak
            $rak = new LokasiRakRekamMedik;
            $query_rak = $rak::find();

            // get sub rak
            $sub_rak = new Subrak;
            $query_sub_rak = $sub_rak::find();

            return [
                'data-posisi' => $query_posisi->all(),
                'data-posisi-by-id' => $posisiById,
                'data-rak' => $query_rak->all(),
                'data-sub-rak' => $query_sub_rak->all(),
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

    public function actionGetDataById($id)
    {
        $posisi = InfoPosisiDokRekamMedik::findOne($id);

        return $posisi;
    }

    public function actionSave($id)
    {
        $now = date('Y-m-d H:i:s');
        $request = Yii::$app->request;
        $model = DokRekamMedis::findOne($id);
        $post = $request->post();
        try {
            if ($model && !empty($model)) {
                if (isset($post['TransaksiPenyimpananDokumenForm'])) {
                    $model->subrak_id = $post['TransaksiPenyimpananDokumenForm']['no_sub_rak'];
                    $model->lokasirak_id = $post['TransaksiPenyimpananDokumenForm']['no_rak'];
                    $model->tglmasukrak = $post['TransaksiPenyimpananDokumenForm']['tgl_akhir_masuk'];
                    $model->tglmasukakhir = $post['TransaksiPenyimpananDokumenForm']['tgl_akhir_masuk'];
                    $model->is_indexing = $post['TransaksiPenyimpananDokumenForm']['status_indexing'];
                    $model->is_assembling = $post['TransaksiPenyimpananDokumenForm']['status_assembling'];
                    if ($model->save()) {
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, 'TransaksiPenyimpananDokumenForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'TransaksiPenyimpananDokumenForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    public function actionGetSubrak()
    {
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $lokasirak_id = $request->get('lokasirak_id');
        // Query
        $sql = "SELECT subrak_id, subrak_nama
            FROM subrak_m
            WHERE lokasirak_id = ".$lokasirak_id." 
            AND is_deleted = false 
            AND is_active = true
        ";
        
        // Result
        $result = $db->createCommand($sql)->queryAll();

        // Return
        return $result;
    }
}