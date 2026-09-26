<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Aris
 * @Date:   2020-10-20 23:50:21
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\SkriningNrs;
use app\modules\v1\models\KesimpulanNrs;
use app\modules\v1\models\AsesmenAwalGiziNrs;
use app\modules\v1\models\AsesmenAwalGiziNrsDetail;
use Doco\models\Cppt;
use Doco\models\Lookup;
use Doco\models\Pendaftaran;
use Doco\models\Pasien;

use Doco\Services\KasirService;

class NrsController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\AsesmenAwalGizi';
    protected $_title = 'NRS';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        $verbs["save"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Function for handle default data Asesmen Keperawatan
     *
     * @param Integer pendaftaran_id
     * @return Array
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        $request  = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasienadmisi_id = Pendaftaran::find()->select([
            'pasienadmisi_id'
        ])
        ->where([
            'pendaftaran_id' => $pendaftaran_id
        ])->scalar();

        $lookup = Lookup::find()->select(['lookup_id', 'lookup_name', 'lookup_value'])->andWhere(['lookup_type'=>'jenis_skrining_nrs'])->orderBy(['lookup_id' => SORT_ASC])->asArray()->all();
        $skriningNrs = SkriningNrs::find()->select(['skriningnrs_nama', 'jenisskrining_id', 'skor', 'additional_data'])->orderBy(['skriningnrs_id' => SORT_ASC])->asArray()->all();
        $kesimpulan = KesimpulanNrs::find()->select(['skor_awal', 'skor_akhir', 'keterangan', 'is_anak'])->orderBy(['kesimpuannrs_id' => SORT_ASC])->asArray()->all();
        $data = AsesmenAwalGiziNrs::find()
            ->select(['asesmenawalgizinrs_t.is_imt', 'asesmenawalgizinrs_t.is_berat_badan', 'asesmenawalgizinrs_t.is_asupan_makan', 'asesmenawalgizinrs_t.is_anak',
                'asesmenawalgizinrs_t.is_penyakit_berat', 'asesmenawalgizinrsdetail_t.skriningnrs_id', 'asesmenawalgizinrsdetail_t.skor'])
            ->leftJoin('asesmenawalgizinrsdetail_t', 'asesmenawalgizinrs_t.asesmenawalgizinrs_id = asesmenawalgizinrsdetail_t.asesmenawalgizinrs_id AND asesmenawalgizinrsdetail_t.is_active = TRUE')
            ->andWhere([
                'asesmenawalgizinrs_t.pendaftaran_id' => $pendaftaran_id,
                'asesmenawalgizinrs_t.pasienadmisi_id' => $pasienadmisi_id,
            ])->asArray()->all();

        return [
            'lookup' => $lookup,
            'skriningNrs' => $skriningNrs,
            'kesimpulan' => $kesimpulan,
            'data' => $data,
        ];
    }

    public function actionSave()
    {
        $request  = Yii::$app->request;
        $data = $request->post();
        $pendaftaran_id = $data['pendaftaran_id'];

        $pasienadmisi_id = Pendaftaran::find()->select([
            'pasienadmisi_id'
        ])
        ->where([
            'pendaftaran_id' => $pendaftaran_id
        ])->scalar();

        $data['pasienadmisi_id'] = $pasienadmisi_id;
        $details = isset($data['details']) ? $data['details'] : [];

        $transaction = Yii::$app->db->beginTransaction();

        $model = AsesmenAwalGiziNrs::find()->andWhere(['pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => $pasienadmisi_id])->one();
        $model = isset($model) && !empty($model) ? $model : new AsesmenAwalGiziNrs;
        $model->attributes = $data;
        if(!$model->save()) {
            $transaction->rollback();
            return $this->responseJson(500, 'Simpan NRS Gagal!');
        }


        $updatedDetail = AsesmenAwalGiziNrsDetail::find()
            ->andWhere(['asesmenawalgizinrs_id' => $model->asesmenawalgizinrs_id,])
            ->andWhere(['NOT', ['skriningnrs_id' => ArrayHelper::getColumn($details, 'skriningnrs_id')]])
            ->all();
        foreach ($updatedDetail as $detail) {
            $detail->updateAttributes(['is_active' => false]);
        }

        foreach ($details as $detail) {
            $detail['asesmenawalgizinrs_id'] = $model->asesmenawalgizinrs_id;
            $detail['is_active'] = true;
            $modelDetail = AsesmenAwalGiziNrsDetail::find()
                ->andWhere(['asesmenawalgizinrs_id' => $model->asesmenawalgizinrs_id, 'skriningnrs_id' => $detail['skriningnrs_id']])->one();
            $modelDetail = isset($modelDetail) && !empty($modelDetail) ? $modelDetail : new AsesmenAwalGiziNrsDetail;
            $modelDetail->attributes = $detail;
            if(!$modelDetail->save()) {
                $transaction->rollback();
                return $this->responseJson(500, 'Simpan NRS Gagal!');
            }
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan NRS Berhasil!');
    }

    /**
    * @controller actionCetakNrs
    * @attribute #table# => table
    **/
    public function actionCetakNrs()
    {
        $request  = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', null);
        try {
            $data_header = AsesmenAwalGiziNrs::find()
                    ->select(['skriningnrs_m.skriningnrs_nama', 'asesmenawalgizinrsdetail_t.skor', 'is_imt', 'is_berat_badan', 'is_asupan_makan', 'is_penyakit_berat', 'asesmenawalgizinrs_t.is_anak', 'asesmenawalgizinrs_t.asesmenawalgizinrs_id'])
                    ->leftJoin('asesmenawalgizinrsdetail_t', 'asesmenawalgizinrs_t.asesmenawalgizinrs_id = asesmenawalgizinrsdetail_t.asesmenawalgizinrs_id')
                    ->leftJoin('skriningnrs_m', 'asesmenawalgizinrsdetail_t.skriningnrs_id=skriningnrs_m.jenisskrining_id AND asesmenawalgizinrsdetail_t.skor=skriningnrs_m.skor')
                    ->where(['pendaftaran_id' => $pendaftaran_id])
                    ->one();

            $data_detail = AsesmenAwalGiziNrsDetail::find()
                            ->select(['skriningnrs_m.skriningnrs_nama', 'asesmenawalgizinrsdetail_t.skor', 'skriningnrs_m.additional_data'])
                            ->leftJoin('skriningnrs_m', 'asesmenawalgizinrsdetail_t.skriningnrs_id=skriningnrs_m.jenisskrining_id AND asesmenawalgizinrsdetail_t.skor=skriningnrs_m.skor')
                            ->innerJoin('lookup_m', 'lookup_m.lookup_id=skriningnrs_m.jenisskrining_id')
                            ->where(['asesmenawalgizinrsdetail_t.asesmenawalgizinrs_id' => $data_header->asesmenawalgizinrs_id])
                            ->andWhere(['lookup_m.lookup_value' => $data_header['is_anak'] ? 'Anak' : 'Dewasa'])
                            ->asArray()->all();

            $pasien = Pasien::find()
                    ->select(['pendaftaran_t.pendaftaran_id', 'pasien_m.pasien_id', 'pasien_m.tanggal_lahir'])
                    ->leftJoin('pendaftaran_t', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                    ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])->one();

            if($pasien){
                $total_skor = 0;
                foreach($data_detail as $key => $value){
                    $total_skor += intval($value['skor']);
                }

                $pasien_umur = DocoHelpers::convertDateToAge($pasien->tanggal_lahir);
                $total_skor = $pasien_umur >= 70 ? $total_skor + 1 : $total_skor;

                $data_kesimpulan = KesimpulanNrs::find()->select(['keterangan', 'skor_akhir', 'skor_awal'])
                                        ->andWhere(['<=', 'skor_awal', $total_skor])
                                        ->andWhere(['>=', 'skor_akhir', $total_skor])
                                        ->andWhere(['=', 'is_anak', $data_header['is_anak']])
                                        ->one();

                $print = new DocoPrint();
                $print->attributes = [
                    '#table#' => $this->renderPartial('pdf', [
                        'data_header' => $data_header,
                        'data_detail' => $data_detail,
                        'data_kesimpulan' => $data_kesimpulan,
                        'data_total_skor' => $total_skor
                    ]),
                ];
                $print->Output();
            }
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }

    }
}
