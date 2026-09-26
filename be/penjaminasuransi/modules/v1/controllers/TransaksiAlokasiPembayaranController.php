<?php

/** 
* @author yaya
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\TerimaBayarKlaimDetail;
use app\modules\v1\models\TerimaBayarKlaim;
use app\modules\v1\models\InfoPengajuanKlaim;
use app\modules\v1\models\InfoPengajuanKlaimDetail;
use app\modules\v1\models\PembayaranAlokasi;
use app\modules\v1\models\PembayaranAlokasiDetail;

class TransaksiAlokasiPembayaranController extends DocoActiveController
{

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $action = [
            'download-template' => 'app\modules\v1\actions\TransaksiAlokasiPembayaran\DownloadTemplateAction',
        ];
        return array_merge($actions, $action);
    }

    public function actionGetNoPengajuan()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        $start = $request->get('start');
        $end = $request->get('end');

        $start = !empty($start) ? date('Y-m-d',strtotime($start)) : date('Y-m-d');
        $end = !empty($end) ? date('Y-m-d',strtotime($end)) : date('Y-m-d');

        $model = PengajuanKlaim::find()->select([
            'pengajuanklaim_id',
            'no_pengajuanklaim'
        ]);
        $model->andWhere(['BETWEEN', 'tgl_pengajuanklaim', $start . ' 00:00:00', $end . ' 23:59:59'])
              ->andWhere(['ILIKE','no_pengajuanklaim',$term])
              ->andWhere(['status_pengajuanklaim' => [
                    DocoConstants::PROSES_PEMBAYARAN,
                    DocoConstants::BATAL_PEMBAYARAN
                ]]);
        return $model->asArray()->all();
    }

    public function actionGetNoPembayaran($id)
    {
        $request = Yii::$app->request;
        $term = $request->get('term');

        $model = TerimaBayarKlaimDetail::find()->select([
            'terimabayarklaim_t.terimabayarklaim_id',
            'terimabayarklaim_t.no_terimabayarklaim',
            'terimabayarklaimdetail_t.pembayaran',
        ])->joinWith([
            'parent' => function ($data) {
                $data->select([
                    'terimabayarklaim_t.terimabayarklaim_id'
                ]);
            }
        ]);
        $model->andWhere(['ILIKE','terimabayarklaim_t.no_terimabayarklaim',$term]);
        $model->andWhere(['!=','terimabayarklaimdetail_t.is_alokasi', true]);
        $model->andWhere(['terimabayarklaimdetail_t.pengajuanklaim_id' => $id]);
        return $model->asArray()->all();
    }

    public function actionGetDetail($id = null)
    {
        $header = InfoPengajuanKlaim::find()->where([
            'pengajuanklaim_id' => $id
        ])->one();
        return [
            'header' => $header
        ];
    }

    public function actionGetData($id)
    {
        $model = new InfoPengajuanKlaimDetail;
        $query = $model::find()->where([
            'pengajuanklaim_id' => $id
        ]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $_GET["per-page"] ? $_GET["per-page"] : 10,
            ],
        ]);
    }

    /**
    * @return \yii\db\Exception | \Exception | array
    * trigger yang di pake 
    * 1. update_pengajuanDetail()
    * 2. update_PengajuanKlaim()
    **/

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new PembayaranAlokasi;
            $terimaId = $request->post('no_pembayaran');
            $pengajuanId = $request->post('no_pengajuan');
            // Check Validasi Pengajuan
            $pengajuan = PengajuanKlaim::find()->where([
                'pengajuanklaim_id' => $pengajuanId,
                'status_pengajuanklaim' => DocoConstants::PROSES_PEMBAYARAN
            ])->one();

            if (empty($pengajuan)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Alokasi pembayaran tidak bisa diproses.'
                ];
            }

            // Update No_Pembayaran
            $modelNoPembayaran = TerimaBayarKlaimDetail::find();
            $modelNoPembayaran->andWhere(['=', 'terimabayarklaim_id', $terimaId]);
            $modelNoPembayaran->andWhere(['=', 'is_alokasi', false]);
            $dataNoPembayaran = $modelNoPembayaran->one();
            $dataNoPembayaran->is_alokasi = true;
            $dataNoPembayaran->save();
            // End
            $model->tgl_pembayaranalokasi = date('Y-m-d H:i:s',strtotime($request->post('tanggal_pembayaran')));
            $model->pengajuanklaim_id = $pengajuanId;
            $model->terimabayarklaim_id = $terimaId;
            $model->jumlah_pembayaran = $request->post('jumlah_pembayaran');
            $model->total_pengajuan = $request->post('total_pengajuan');
            $model->total_terbayar = $request->post('total_alokasi');
            $model->sisa_piutang = $request->post('sisa_piutang');
            $model->catatan = $request->post('catatan');
            if ($model->save()) {
                $idParent = $model->pembayaranalokasi_id;
                $detail = json_decode($request->post('detail_pembayaran','{}'),true);
                if (is_array($detail)) {
                    $tmp = [];
                    foreach ($detail as $key => $value) {
                        $tmp[] = [
                            'pembayaranalokasi_id' => $idParent,
                            'pengajuanklaimdetail_id' => $key,
                            'pendaftaran_id' => $value['pendaftaran_id'],
                            'pasienadmisi_id' => $value['pasienadmisi_id'],
                            'pasien_id' => $value['pasien_id'],
                            'jumlah_piutang' => $value['jumlah_piutang'],
                            'jumlah_telahbayar' => $value['jumlah_telahbayar'] + $value['jumlah_bayar'],
                            'jumlah_bayar' => $value['jumlah_bayar'],
                            'jumlah_sisapiutang' => $value['jumlah_piutang'] - ($value['jumlah_bayar'] + $value['jumlah_telahbayar']),
                        ];
                    }
                    if (!empty($tmp)) {
                        PembayaranAlokasiDetail::batchInsert($tmp,false);
                    }
                    $transaction->commit();
                    return [
                        'message' => 'Alokasi Berhasil disimpan'
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