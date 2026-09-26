<?php
/*
 * @Author: metafiliana
 * @Date: 2018-01-29 13:10:59
 * @Last Modified by: Anggoro <tri.anggoro@docotel.com>
 * @Last Modified time: 2019-06-21
 * @Description:
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\db\Expression;

use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\PemberianInfus;
use app\modules\v1\models\PemberianInfusRespon;
use Doco\models\Pendaftaran;

class PemberianInfusController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PemberianInfus';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["get-tindakan"] = ["GET"];
        $verbs["get-data"] = ["GET"];
        $verbs["respon-detail"] = ["GET"];
        $verbs["save"] = ["POST"];
        $verbs["save-respon"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    public function actionGetData($pendaftaran_id) {
        $pasienadmisi_id = Pendaftaran::find()->select(['pasienadmisi_id'])->where(['pendaftaran_id'=>$pendaftaran_id])->scalar();
        $request = Yii::$app->request;

        $offset = intval($request->get('offset', 0));
        $limit = intval($request->get('limit', 10));

        $baseQuery = '
            SELECT
                pemberianinfus_t.pemberianinfus_id,
                pemberianinfus_t.tgl_pemasangan, 
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_nama,
                daftartindakan_m.daftartindakan_nama,
                obatalkes_m.obatalkes_nama,
                pemberianinfus_t.volume,
                pemberianinfus_t.durasi,
                pemberianinfus_t.jumlah_tetesan
            FROM pemberianinfus_t
            JOIN jsonb_array_elements_text(pemberianinfus_t.instruksitindakanbmhp_id::jsonb) instruksitindakanbmhp_json(instruksitindakanbmhp_id) 
                ON TRUE
            JOIN (SELECT instruksitindakan_id, daftartindakan_id FROM instruksitindakan_t) instruksitindakan_t 
                ON instruksitindakan_t.instruksitindakan_id = pemberianinfus_t.instruksitindakan_id
            LEFT JOIN (SELECT daftartindakan_id, daftartindakan_nama FROM daftartindakan_m) daftartindakan_m
                ON instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            LEFT JOIN (SELECT instruksitindakanbmhp_id, obatalkes_id FROM instruksitindakanbmhp_t) instruksitindakanbmhp_t
                ON instruksitindakanbmhp_t.instruksitindakanbmhp_id = instruksitindakanbmhp_json.instruksitindakanbmhp_id::integer
            LEFT JOIN (SELECT obatalkes_id, obatalkes_nama from obatalkes_m) obatalkes_m
                ON instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id
            LEFT JOIN (SELECT nama_pegawai, pegawai_id, kelompokpegawai_id FROM pegawai_m) pegawai_m
                ON pegawai_m.pegawai_id = pemberianinfus_t.pegawai_id
            LEFT JOIN (SELECT kelompokpegawai_id, kelompokpegawai_nama FROM kelompokpegawai_m) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
            WHERE pemberianinfus_t.pendaftaran_id = :pendaftaran_id';

        $groupQuery = 'SELECT 
                t.pemberianinfus_id,
                t.tgl_pemasangan,
                t.nama_pegawai,
                t.kelompokpegawai_nama,
                t.daftartindakan_nama,
                json_agg(t.obatalkes_nama) as obatalkes_nama,
                t.volume,
                t.durasi,
                t.jumlah_tetesan
            FROM (' . $baseQuery . ') t
            GROUP BY 
                pemberianinfus_id,
                tgl_pemasangan,
                nama_pegawai,
                kelompokpegawai_nama,
                daftartindakan_nama,
                volume,
                durasi,
                jumlah_tetesan
            ORDER BY tgl_pemasangan desc';

        $count = Yii::$app->db->createCommand('select count(*) from (' . $groupQuery . ') c', [
            ':pendaftaran_id' => $pendaftaran_id,
        ])->queryScalar();

        return new SqlDataProvider([
            'sql' => $groupQuery,
            'params' => [
                ':pendaftaran_id' => $pendaftaran_id,
            ],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => $limit,
            ],
        ]);
    }

    public function actionSave($pendaftaran_id) {
        $pasienadmisi_id = Pendaftaran::find()->select(['pasienadmisi_id'])->where(['pendaftaran_id'=>$pendaftaran_id])->scalar();
        $data = Yii::$app->request->post('data');
        $data['pendaftaran_id'] = $pendaftaran_id;
        $data['pasienadmisi_id'] = $pasienadmisi_id;
        $data['pegawai_id'] = Yii::$app->jwt->user->pegawai_id;
        $data['instruksitindakanbmhp_id'] = empty($data['instruksitindakanbmhp_id']) ? null : json_encode($data['instruksitindakanbmhp_id']);

        $model = new PemberianInfus;
        $model->attributes = $data;
        if($model->save()) {
            return [
                'message' => 'Data Berhasil di simpan'
            ];
        } else {
            return [
                'status' => 422,
                'message' => 'Data Gagal di simpan'
            ];
        }
    }

    public function actionSaveRespon($id) {
        $data = Yii::$app->request->post('data');
        $data['pegawai_id'] = Yii::$app->jwt->user->pegawai_id;
        $data['pemberianinfus_id'] = $id;
        $model = new PemberianInfusRespon;
        $model->attributes = $data;
        if($model->save()) {
            return [
                'message' => 'Data Berhasil di simpan'
            ];
        } else {
            return [
                'status' => 422,
                'message' => 'Data Gagal di simpan'
            ];
        }
    }

    public function actionGetTindakan($pendaftaran_id, $tipe_instruksi) {
        if($tipe_instruksi == 'TINDAKAN') {
            $listPemberian = PemberianInfus::find()->select(['instruksitindakan_id'])->asArray()->column();
        } else {
            $list = PemberianInfus::find()->select(['instruksitindakanbmhp_id'])->asArray()->column();
            $listPemberian = [];
            foreach ($list as $data) {
                $data = json_decode($data);
                $listPemberian = array_merge($listPemberian, $data);
            }
        }
        $data = InfoInstruksiView::find()
            ->select([
                'tindakaninstruksi_nama',
                'instruksitindakan_id',
            ])
            ->andWhere([
                'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id'),
                'grouping_tipe' => 'TINDAKANBMHP',
                'tipe_instruksi' => $tipe_instruksi,

            ])
            ->andWhere([
                'IS NOT', 'pasienadmisi_id', null
            ])
            ->andWhere([
                'IS NOT', 'instruksi', null
            ])
            ->andWhere([
                'NOT', ['instruksitindakan_id' => $listPemberian]
            ])
            ->asArray()->all();
        return $data;
    }


    public function actionResponDetail($id) {
        $data = PemberianInfusRespon::find()
            ->select([
                'pemberianinfusrespon_t.tgl_respon',
                'pemberianinfusrespon_t.respon',
                'pegawai_m.nama_pegawai',
                'kelompokpegawai_m.kelompokpegawai_nama',
            ])
            ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = pemberianinfusrespon_t.pegawai_id')
            ->leftJoin('kelompokpegawai_m', 'pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id')

            ->andWhere([
                'pemberianinfusrespon_t.pemberianinfus_id' => $id,
            ])
            ->orderBy([
                'pemberianinfusrespon_t.tgl_respon' => SORT_DESC
            ])
            ->asArray()->all();
        return $data;
    }   
}
