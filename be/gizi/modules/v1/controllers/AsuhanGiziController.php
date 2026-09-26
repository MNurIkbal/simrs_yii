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

use app\modules\v1\models\AsuhanGizi;
use app\modules\v1\models\InfoPasienGiziView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\AsuhanGiziView;
use app\modules\v1\models\JenisDiet;
use app\modules\v1\models\Lookup;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class AsuhanGiziController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AsuhanGizi';
    protected $_title = 'Asuhan Gizi';

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

    public function actionSimpanAsuhanGizi()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post('AsuhanGiziForm');
            $model = new AsuhanGizi;
            $model->pendaftaran_id = $post['pendaftaran_id'];
            $model->tgl_asuhangizi = date('Y-m-d H:i:s');
            $jwt = Yii::$app->jwt;
            $model->peg_gizi_id = $jwt->user->pegawai_id;

            $pasienGiziModel = InfoPasienGiziView::find()
                ->select('pasienadmisi_id')
                ->andWhere(['pendaftaran_id'=>$model->pendaftaran_id])
                ->asArray()->one();
            $model->pasienadmisi_id = $pasienGiziModel['pasienadmisi_id'];

            foreach ($model->arr as $key=>$listAttr) {
                $temp = [];
                foreach ($listAttr as $attrVal) {
                    if (isset($post[$attrVal])) {
                        $temp[$attrVal] = $post[$attrVal];
                    }
                }
                $model->$key = json_encode($temp);
            }

            if($model->save()){
                return [
                    'message' => 'Data Berhasil di simpan',
                    'asuhangizi_id' => DocoHelpers::encrypt($model->asuhangizi_id)
                ];
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
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

    /**
    * @controller actionCetakAsuhanGizi 
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
    public function actionCetakAsuhanGizi()
    {

        $request = Yii::$app->request;
        $asuhangizi_id = $request->get('asuhangizi_id');

        $jwt = Yii::$app->jwt;
        $user_pegawai_id = $jwt->user->pegawai_id;
        $user_pegawai = PegawaiView::find()->andWhere(['pegawai_id'=>$user_pegawai_id])
            ->asArray()->one();

        $model = AsuhanGiziView::find()
            ->andWhere(['asuhangizi_id'=>$asuhangizi_id])
            ->asArray()->one();
        $dietisen = PegawaiView::find()->andWhere(['pegawai_id'=>$model['dietisen_id']])
            ->asArray()->one();
        $temp = json_decode($model['antropometri'], true);

        $r_penyakitkeluarga = [];
        $diagnosa = $peskk = '';
        if (isset($model['r_penyakitkeluarga']) && $model['r_penyakitkeluarga'] != '') {
            $r_penyakitkeluarga =json_decode($model['r_penyakitkeluarga']);
        }
        if(isset($model['diagnosa_nama']) && $model['diagnosa_nama'] != ''){
            $diagnosa = $model['diagnosa_nama'];
        }
        if(isset($model['r_peskk']) && $model['r_peskk'] != ''){
            $peskk = $model['r_peskk'];
        }
        if (!empty($r_penyakitkeluarga)) {
            foreach ($r_penyakitkeluarga as $key => $value) {
                $exploded = explode('_', $value);

                if (count($exploded) == 2) {
                    $r_penyakitkeluarga[$key] = $exploded[1];
                } else {
                    $r_penyakitkeluarga[$key] = $exploded[0];
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
            '#dietisen#' => $dietisen['nama_pegawai'],
            '#detail#'=>$this->renderPartial('cetak_asuhan_gizi', [
                'model' => $model,
                'diagnosa' => $diagnosa,
                'peskk' => $peskk,
                'r_penyakitkeluarga' => !empty($r_penyakitkeluarga) ? implode(', ', $r_penyakitkeluarga) : '-',
                'r_sosialekonomi' => !empty($r_sosialekonomi) ? implode(', ', $r_sosialekonomi) : '-',
            ]),
            '#tgl_input#' => date('d M Y', strtotime($model['created_date'])),
            '#jam_input#' => date('H:i:s', strtotime($model['created_date'])),
            '#user_cetak#' => $user_pegawai['nama_pegawai'],
            '#tgl_cetak#' => date('d M Y H:i:s'),
        ];
        $print->Output();
    }

}