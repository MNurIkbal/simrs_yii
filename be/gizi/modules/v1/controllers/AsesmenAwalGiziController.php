<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Rizal
 * @Date:   2018-11-28 23:50:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\InfoPasienGiziView;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\AsesmenAwalGizi;
use app\modules\v1\models\AsesmenAwalGiziView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\SkriningGizi;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class AsesmenAwalGiziController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AsesmenAwalGizi';
    protected $_title = 'Asesmen Awal Gizi';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionSimpanSga()
    {
        try {
            $request = Yii::$app->request;
            $model = new AsesmenAwalGizi;
            $model->attributes = $request->post();
            $pend_id = $model->pendaftaran_id;

            $pasienGiziModel = InfoPasienGiziView::find()
                ->select('pasienadmisi_id')
                ->andWhere(['pendaftaran_id'=>$pend_id])
                ->asArray()->one();
            $model->pasienadmisi_id = $pasienGiziModel['pasienadmisi_id'];

            $diagnosa = null;
            if (is_numeric($model->diagnosa_medis)) {
                $diagnosa = DiagnosaView::find()
                    ->andWhere(['diagnosa_id'=>$model->diagnosa_medis])
                    ->asArray()->one();
            }
            $temp = [];
            if ($diagnosa) {
                $temp = [
                    'id'=>$diagnosa['diagnosa_id'],
                    'text'=>$diagnosa['diagnosa_kode'].' - '.$diagnosa['diagnosa_nama']
                ];
            } else {
                $temp = [
                    'text'=>$model->diagnosa_medis
                ];
            }
            $model->diagnosa_medis = json_encode($temp);

            if($model->save()){
                $modelSkrining = SkriningGizi::find()
                    ->andWhere(['pendaftaran_id'=>$model->pendaftaran_id])
                    ->one();
                if($modelSkrining){
                    $modelSkrining->status_asesmen = DocoConstants::STATUSSKRININGGIZI_SUDAH;
                    $modelSkrining->save();
                }
                return [
                    'message' => 'Data Berhasil di simpan',
                    'asesmenawalgizi_id' => $model->asesmenawalgizi_id
                ];
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakSga
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #tanggal_pendaftaran# => tanggal pendaftaran
    * @attribute #no_pendaftaran# => nomor pendaftaran
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #kasus_penyakit# => jenis kasus penyakit
    * @attribute #tanggal_lahir# => tanggal lahir pasien
    * @attribute #umur# => umur pasien
    * @attribute #dokter_dpjp# => dokter admisi
    * @attribute #kelas_pelayanan# => kelas pelayanan
    * @attribute #no_kamar_no_bed# => no kamar + no tempat tidur
    * @attribute #carabayar_penjamin# => cara bayar + penjamin
    * @attribute #dietisen# => nama dietisen yang input
    * @attribute #detail#=> detail konten
    * @attribute #tgl_input#=> tanggal input asesmen
    * @attribute #jam_input#=> jam input asesmen
    * @attribute #tgl_cetak#=> tanggal cetak asesmen
    * @attribute #user_cetak#=> dicetak oleh
    **/
    public function actionCetakSga()
    {
        $jwt = Yii::$app->jwt;
        $user_pegawai_id = $jwt->user->pegawai_id;
        $user_pegawai = PegawaiView::find()->andWhere(['pegawai_id'=>$user_pegawai_id])
            ->asArray()->one();

        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $model = AsesmenAwalGiziView::find()
            ->andWhere(['pendaftaran_id'=>$pendaftaran_id])
            ->asArray()->one();

        if (isset($model['bb_biasanya']) && $model['bb_biasanya'] != '') {
            if(strpos($model['bb_biasanya'], '.') !== false) {
                $model['bb_biasanya'] = str_replace('.', ',', $model['bb_biasanya']);
            } else {
                if(strpos($model['bb_biasanya'], ',') !== false) {

                } else {
                    $model['bb_biasanya'] = $model['bb_biasanya'].',00';
                }
            }
        }

        if (isset($model['bb_saatini']) && $model['bb_saatini'] != '') {
            if(strpos($model['bb_saatini'], '.') !== false) {
                $model['bb_saatini'] = str_replace('.', ',', $model['bb_saatini']);
            } else {
                if(strpos($model['bb_saatini'], ',') !== false) {

                } else {
                    $model['bb_saatini'] = $model['bb_saatini'].',00';
                }
            }
        }

        if (isset($model['perubahan_kg']) && $model['perubahan_kg'] != '') {
            if(strpos($model['perubahan_kg'], '.') !== false) {
                $model['perubahan_kg'] = str_replace('.', ',', $model['perubahan_kg']);
            } else {
                if(strpos($model['perubahan_kg'], ',') !== false) {

                } else {
                    $model['perubahan_kg'] = $model['perubahan_kg'].',00';
                }
            }
        }

        if (isset($model['perubahan_persen']) && $model['perubahan_persen'] != '') {
            if(strpos($model['perubahan_persen'], '.') !== false) {
                $model['perubahan_persen'] = str_replace('.', ',', $model['perubahan_persen']);
            } else {
                if(strpos($model['perubahan_persen'], ',') !== false) {

                } else {
                    $model['perubahan_persen'] = $model['perubahan_persen'].',00';
                }
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekam_medik#' => $model['no_rekam_medik'],
            '#tanggal_pendaftaran#' => date('d M Y H:i:s', strtotime($model['tgl_pendaftaran'])),
            '#no_pendaftaran#' => $model['no_pendaftaran'],
            '#nama_pasien#' => $model['nama_pasien'],
            '#jenis_kelamin#' => $model['jenis_kelamin'],
            '#kasus_penyakit#' => $model['jeniskasuspenyakit_nama'],
            '#tanggal_lahir#' => date('d M Y', strtotime($model['tanggal_lahir'])),
            '#umur#' => $model['umur'],
            '#dokter_dpjp#' => $model['dokter_admisi'],
            '#kelas_pelayanan#' => $model['kelaspelayanan_nama'],
            '#no_kamar_no_bed#' => $model['kamarruangan_nokamar'] . ' - ' . $model['no_tempattidur'],
            '#carabayar_penjamin#' => $model['carabayar_nama'] . ' - ' . $model['penjamin_nama'],
            '#dietisen#' => $model['nama_pegawai'],
            '#detail#'=>$this->renderPartial('cetak_sga', ['model'=>$model]),
            '#tgl_input#' => date('d M Y', strtotime($model['created_date'])),
            '#jam_input#' => date('H:i:s', strtotime($model['created_date'])),
            '#user_cetak#' => $user_pegawai['nama_pegawai'],
            '#tgl_cetak#' => date('d M Y H:i:s'),
        ];
        $print->Output();
    }
}
