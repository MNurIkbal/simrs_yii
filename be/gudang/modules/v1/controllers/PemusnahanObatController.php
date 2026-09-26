<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-12-26 14:33:12
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-12-28 11:01:15
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

use app\modules\v1\cache\Cache;

use app\modules\v1\models\InfoObatExpired;
use app\modules\v1\models\InfoObatPemusnahan;
use app\modules\v1\models\PemusnahanObat;
use app\modules\v1\models\PemusnahanObatDetail;
use app\modules\v1\models\InfoPemusnahanObatView;
use app\modules\v1\models\InfoPemusnahanObatDetailView;

use app\modules\v1\models\PemusnahanForm;

class PemusnahanObatController extends DocoActiveController
{
    public $modelClass = PemusnahanObat::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["GET"];
        $verbs["get-options"] = ["GET"];
        $verbs["save"] = ["POST"];
        $verbs["print"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['save']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoObatPemusnahan;
            $query = $model::find();

            $query->andWhere(['>', 'stok_exp', 0]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionPrint
    * @attribute #table_pemusnahan# => Untuk Menampilkan Tabel pemakaian obat alkes
    * @attribute #no_pemusnahan# => untuk menampilkan nomor pemakaian
    * @attribute #tgl_pemusnahan# => untuk menampilkan Tanggal pemakaian
    * @attribute #instalasi# => untuk menampilkan Ruangan pemakaian
    * @attribute #ruangan# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_menyetujui# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_mengetahui# => untuk menampilkan Ruangan pemakaian
    * @attribute #pegawai_retur# => untuk menampilkan Ruangan pemakaian
    **/
    public function actionPrint($id)
    {
        $request = Yii::$app->request;
        $print = new DocoPrint();

        $header = InfoPemusnahanObatView::find()->where([
            'pemusnahanobat_id' => $id
        ])->asArray()->one();

        $query = InfoPemusnahanObatDetailView::find()->where([
            'pemusnahanobat_id' => $id
        ])->asArray()->orderBy([
            'obatalkes_nama' => SORT_ASC
        ])->all();

        $print->attributes = [
            '#table_pemusnahan#' => $this->renderPartial('index',[
                'detail' => $query
            ]),
            '#no_pemusnahan#' => $header['nopemusnahan'],
            '#tgl_pemusnahan#' => date('d-M-Y',strtotime($header['tglpemusnahan'])),
            '#instalasi#' => $header['instalasi_nama'],
            '#ruangan#' => $header['ruangan_nama'],
            '#pegawai_menyetujui#' => $header['pegawai_menyetujui'],
            '#pegawai_mengetahui#' => $header['pegawai_mengetahui'],
            '#pegawai_retur#' => $header['nama_pegawai'],
        ];
        $print->Output();
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $user = Yii::$app->jwt;
        $transaction = $connection->beginTransaction();
        $parentId = null;
        try {
            $model = new PemusnahanForm;
            $model->attributes = $request->post();
            if ($model->validate()) {
                $tanggal = $request->post('tanggal_pemusnahan',null);
                $tanggal = !empty($tanggal) ? date('Y-m-d',strtotime($tanggal)) : date('Y-m-d');
                $pemusnahan = new PemusnahanObat;
                $pemusnahan->keterangan = "Pemusnahan Obat";
                $pemusnahan->ruangan_id = $user->ruangan_id;
                $pemusnahan->pegawai_id = $user->user->pegawai_id;
                $pemusnahan->pegawaimengetahui_id = $request->post('pegawai_mengetahui');
                $pemusnahan->pegawaimenyetujui_id = $request->post('pegawai_meyetujui');
                $pemusnahan->tglpemusnahan = $tanggal .' '. date("H:i:s");
                $pemusnahan->total_harganetto = $request->post('total_netto',0);
                if ($pemusnahan->save(false)) {
                    $detail = [];
                    $detailItems = $request->post('detail',[]);
                    $detailItems = is_array($detailItems) ? $detailItems : json_decode($detailItems, true);
                    $parentId = $pemusnahan->pemusnahanobat_id;
                    foreach ($detailItems as $key => $value) {
                        $detail[] = [
                            'pemusnahanobat_id' => $parentId,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'jumlah' => $value['qty_pemusnahan'],
                            'tglkadaluarsa' => $value['tglkadaluarsa'],
                            'nobatch' => isset($value['nobatch']) ? $value['nobatch'] : null,
                            'kondisibarang' => 'Kadaluarsa',
                            'harganetto' => $value['total_harganetto'],
                            'satuan_id' => $value['satuankecil_id'],
                            'additional_data' => json_encode([
                                'id_stok' => $value['id_stok'],
                                'ruangan_id' => $value['ruangan_id'],
                                'satuankecil_id' => $value['satuankecil_id']
                            ])
                        ];
                    }
                    PemusnahanObatDetail::batchInsert($detail, false);
                    $getPemusnahan = PemusnahanObat::find()->where(['pemusnahanobat_id'=>$parentId])->asArray()->one();
                    $transaction->commit();
                    return [
                        'message' => "success",
                        'parent_id' => DocoHelpers::encrypt($parentId),
                        'nopemusnahan' => isset($getPemusnahan['nopemusnahan']) ? $getPemusnahan['nopemusnahan'] : '',
                    ];
                }
            }

            return [
                'status' => 422,
                'data' => $model->errors
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
