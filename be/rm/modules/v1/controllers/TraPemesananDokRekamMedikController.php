<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-16 10:52:31
 * @Last Modified by: Randy Vianda Putra
 * @Last Modified time: 2018-05-28
 */

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use app\modules\v1\models\InfoPosisiDokRekamMedik;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PemesananDokRekamMedik;
use app\modules\v1\models\PemesananDokRekamMedikDetail;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoActiveController;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

class TraPemesananDokRekamMedikController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\PemesananDokRekamMedik';

    // Verbs
    public function verbs()
    {
        // Parent verbs
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Actions
    public function actions()
    {
        // Parent actions
        $actions = parent::actions();

        // Unset some actions
        unset($actions['index']);
        unset($actions['create']);

        // Return actions
        return $actions;
    }

    // Action create
    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $post = Yii::$app->request->post();
        $model = new PemesananDokRekamMedik();
        // Try catch
        try {
            // Assign manual
            $model->no_pesandokrm = $post['PemesananDokRekamMedik']['no_pesandokrm'];
            $model->ruanganpemesan_id = $post['PemesananDokRekamMedik']['ruanganpemesan_id'];
            $model->ruangantujuan_id = $post['PemesananDokRekamMedik']['ruangantujuan_id'];
            $model->tgl_pesandokrm = $post['PemesananDokRekamMedik']['tgl_pesandokrm'];
            $model->tgl_mintakirim = $post['PemesananDokRekamMedik']['tgl_mintakirim'];
            $model->status_pesan = $post['PemesananDokRekamMedik']['status_pesan'];
            $model->created_by =  Yii::$app->user->identity->pegawai_id;

            // Save
            if ($model->save()) {
                // Save detail
                if (isset($post['PemesananDokRekamMedikDetail']) && !empty($post['PemesananDokRekamMedikDetail'])) {
                    $attributes = $dokrm_id = [];
                    // Loop
                    foreach ($post['PemesananDokRekamMedikDetail'] as $index => $value) {
                        // Assign data manually
                        $attributes[] = [
                            'pesandokrm_id' => $model->pesandokrm_id,
                            'dokrekammedis_id' => $value['dokrekammedis_id'],
                        ];
                        $dokrm_id[] = $value['dokrekammedis_id'];
                    }

                    PemesananDokRekamMedikDetail::batchInsert($attributes);
                    $list_dokrm_id = '(' . implode(', ', $dokrm_id) . ')';
                    $sql_update = "UPDATE posisidokrm_r SET
                        is_pesan = true
                        WHERE
                            dokrekammedis_id in ".$list_dokrm_id."
                    ";
                    $connection->createCommand($sql_update)->execute();
                }

                $transaction->commit();

                return $model;
            }
            else {
                $transaction->rollBack();
                // Return
                return $model->errors;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Action get posisi dok rekam medik by norekmed
    public function actionGetPosisiDokRekamMedikByNorekmed($no_rekam_medik, $ruangan_id)
    {
        // Try catch
        try {
            // Define new model
            $model = new InfoPosisiDokRekamMedik;

            // Query
            $query = $model::find()->where(['is_pesan' => false])
                ->andWhere(['like', 'no_rekam_medik', $no_rekam_medik])
                ->andWhere(['=', 'ruanganakhir_id', $ruangan_id]);
            
            // Return
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Action get posisi dok rekam medik by id
    public function actionGetPosisiDokRekamMedikById($id)
    {
        // Try catch
        try {
            // Query
            $model = InfoPosisiDokRekamMedik::find()->where(['posisidokrm_id' => $id])->andWhere(['is_pesan' => false])->one();
            
            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Action instalasi
    public function actionGetInstalasi()
    {
        // Try catch
        try {
            // Model
            $model = Instalasi::find()->all();

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Action ruangan
    public function actionGetRuangan($instalasi_id)
    {
        // Try catch
        try {
            // Model
            $model = Ruangan::find()->where(['instalasi_id' => $instalasi_id])->all();

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_pdfdokrekammedik# => table 
    **/
    public function actionExportPdf($pesandokrm_id)
    {
        // Try catch
        try {
            // Get pemeriksaan fisik
            $model = PemesananDokRekamMedik::findOne($pesandokrm_id);
            $ruangan_nama = $instalasi_nama = '';
            if (!empty($model->ruangantujuan_id)) {
                $ruangan = Ruangan::findOne($model->ruangantujuan_id);
                $instalasi = Instalasi::findOne($ruangan->instalasi_id);
                $instalasi_nama = $instalasi->instalasi_nama;
                $ruangan_nama = $ruangan->ruangan_nama;
            }

            // Check model
            if (!empty($model)) {
                // Header
                $header = array(
                    Yii::t('app', "BUKTI PEMESANAN DOKUMEN REKAM MEDIK") => 'BUKTI PEMESANAN DOKUMEN REKAM MEDIK',
                );
                
                // Print
                $print = new DocoPrint();

                // Assign attributes
                $print->attributes = [
                    '#table_pdfdokrekammedik#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'ruangan' => $ruangan_nama,
                        'instalasi' => $instalasi_nama,
                        'model' => $model,
                    ]),
                ];

                // Print output
                $print->Output();
            }
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Create detail
    protected function createDetail($model, $post) {
        // Try catch
        try {
            // Check data
            if (isset($post['PemesananDokRekamMedikDetail']) && !empty($post['PemesananDokRekamMedikDetail'])) {
                // Loop
                foreach ($post['PemesananDokRekamMedikDetail'] as $index => $value) {
                    // Save detail
                    $modelDetail = new PemesananDokRekamMedikDetail();

                    // Assign data manually
                    $modelDetail->pesandokrm_id = $model->pesandokrm_id;
                    $modelDetail->dokrekammedis_id = $value['dokrekammedis_id'];

                    // Save
                    if ($modelDetail->save()) {
                        // Nothing to do
                    }
                    else {
                        // Return false
                        return false;
                    }
                }

                // Return true
                return true;
            }
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
?>