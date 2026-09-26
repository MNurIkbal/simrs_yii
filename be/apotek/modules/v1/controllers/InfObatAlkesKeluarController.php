<?php

/**
** @author yaya
** @since 20 mar 2018
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\ArrayExpression;
use app\modules\v1\models\InfoPemesananObatAlkesView;
use app\modules\v1\models\InfoDistribusiObatAlkesView;
use app\modules\v1\models\DetailPemesananObatAlkes;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoActiveController;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use app\modules\v1\models\PesanObatAlkes;
use app\modules\v1\models\PesanObatDetail;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\entities\StatusDistribusi;

class InfObatAlkesKeluarController extends DocoActiveController
{
    public $modelClass = '';
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-fillter"] = ["GET"];
        $verbs["get-data-detail"] = ["GET"];
        $verbs["get-detail"] = ["GET"];
        $verbs["delete"] = ["DELETE","POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        $actions['get-data'] = 'app\modules\v1\actions\GetDataDistribusiAction';
        $actions['get-data-detail'] = 'app\modules\v1\actions\General\ListMutasiObatAction';
        return $actions;
    }

    public function actionBatalPemesanan() {
        return Yii::$app->docoPlugin->execute('batal_pemesanan_obat');
    }

    public function actionIndex()
    {
        // $model = new InfoPemesananObatAlkesView;
        $model = new InfoDistribusiObatAlkesView;
        $query = $model::find(true);
        // $query->andWhere(['ruangan_id' => $ruangan_tujuan]);
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            // return $_GET['advanced-filter'];
            if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['instalasi_ruangan'])){
                $query->andWhere(['ruangan_id'=>$_GET['advanced-filter']['instalasi_ruangan']]);
                unset($_GET['advanced-filter']['instalasi_ruangan']);
            }
        }

        // return $end;
        // if($between) {
        $query->andWhere(['between', 'tglpemesanan', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        // return [$start, $end];

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // return $query->createCommand()->getRawSql();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDataEdit(){
        $pesanobatalkes_id = Yii::$app->request->get('pesanobatalkes_id', null);

        $query = DetailPemesananObatAlkes::find()->where([
                'pesanobatalkes_id' => $pesanobatalkes_id
            ])->asArray()->all();

        return [
            'data' => $query,
        ];
    }

    public function actionGetFillter()
    {
        $instalasi = ArrayHelper::map(Instalasi::find()->all(),'instalasi_id','instalasi_nama');
        $ruanganData = Ruangan::find()->leftJoin('instalasi_m','instalasi_m.instalasi_id = ruangan_m.instalasi_id')->select(['ruangan_id',"CONCAT(instalasi_nama,' - ',ruangan_nama) AS instalasi_ruangan"])->asArray()->all();
        $ruangan = ArrayHelper::map($ruanganData,'ruangan_id','instalasi_ruangan');
        $nopemesanan = ArrayHelper::map(InfoPemesananObatAlkesView::find()->all(),'nopemesanan','nopemesanan');
        $statusdistribusi = StatusDistribusi::getlist();
        return [
            'instalasi' => $instalasi,
            'ruangan' => $ruangan,
            'nopemesanan' => $nopemesanan,
            'statusdistribusi' => $statusdistribusi
        ];
    }

    public function actionGetDetail($id)
    {
        $query = (new Query())
                ->select([
                    "*",
                    "(SELECT
                    CASE konfigfarmasi_k.is_verifpemesanan
                    WHEN TRUE THEN
                     CASE
                     WHEN infopemesananobatalkes_v.statuspesan = '398' AND infopemesananobatalkes_v.status_verifikasi = 666
                     THEN infopemesananobatalkes_v.status_verifikasi_nama
                     WHEN infopemesananobatalkes_v.status_id = 400
                     THEN infopemesananobatalkes_v.status_mutasi
                     ELSE infopemesananobatalkes_v.status_pengiriman
                     END
                    ELSE infopemesananobatalkes_v.status_pengiriman
                    END
                    from konfigfarmasi_k LIMIT 1) AS statusdistribusiobat",
                    "(SELECT
                    CASE konfigfarmasi_k.is_verifpemesanan
                    WHEN TRUE THEN
                     CASE
                     WHEN infopemesananobatalkes_v.statuspesan = '398' AND infopemesananobatalkes_v.status_verifikasi = 666
                     THEN infopemesananobatalkes_v.status_verifikasi
                     ELSE infopemesananobatalkes_v.status_id
                     END
                    ELSE infopemesananobatalkes_v.status_id
                    END
                    from konfigfarmasi_k LIMIT 1) AS statusdistribusiobat_id"
                ])
                ->from('infopemesananobatalkes_v')
                ->where(['pesanobatalkes_id' => $id])->one();

        return [
            'data' => $query
        ];
    }

    public function actionEditPemesanan($pesanobatalkes_id){
        $request = Yii::$app->request;
        $post = $request->post('formData', []);
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $user_login = Yii::$app->user->identity->pegawai_id;
        try{
            $pesananOa = PesanObatAlkes::find()->where(['pesanobatalkes_id'=>$pesanobatalkes_id])->one();
            if(!($pesananOa->statuspesan == DocoConstants::STATUS_PESAN_BELUM_DIKIRIM && $pesananOa->status_verifikasi == DocoConstants::OBAT_BELUM_DIVERIFIKASI)){
                Yii::$app->response->statusCode = 422;
                return [
                    'status' => 422,
                    'message' => 'Terjadi Kesalahan',
                    'text' => 'Tidak bisa di simpan'
                ];
            }

            $pesananDetail = PesanObatDetail::find()->where(['pesanobatalkes_id'=>$pesanobatalkes_id])->asArray()->all();
            $newPesananDetail = $post['listObat'];
            if(count($pesananDetail) < 1){
                throw new \Exception("Tidak ada data detail", 1);
            }

            $pesanObatAlkes = PesanObatAlkes::findOne($pesanobatalkes_id);
            $pesanObatAlkes->keterangan_pesan = $post['catatan'];
            $pesanObatAlkes->update();

            $dataInsert = $dataDelete = [];
            $arr_detail_id = $arr_jumlah_pesan = $arr_jumlah_input =
            $arr_satuanbesar_id = $arr_satuankecil_id = $dataUpdate = [];
            $indexUpdate = 0;
            foreach ($newPesananDetail as $key => $value) {
                if($value['pesanobatdetail_id'] != "") {
                    if ($value['to_delete'] === "true") {
                        $dataDelete[] = $value['pesanobatdetail_id'];
                    }

                    $arr_detail_id[$indexUpdate] = (int) $value['pesanobatdetail_id'];
                    $arr_jumlah_pesan[$indexUpdate] = $value['qty_form'] * $value['nilai_konversi'];
                    $arr_jumlah_input[$indexUpdate] = (int) $value['qty_besar'];
                    $arr_satuanbesar_id[$indexUpdate] = (int) $value['satuanbesar_id'];
                    $arr_satuankecil_id[$indexUpdate] = (int) $value['satuankecil_id'];

                    $dataUpdate = [
                        'jumlah_pesan' => $arr_jumlah_pesan,
                        'jumlah_input' => $arr_jumlah_input,
                        'satuanbesar_id' => $arr_satuanbesar_id,
                        'satuankecil_id' => $arr_satuankecil_id
                    ];

                    $updateCondition = [
                        'pesanobatdetail_id' => $arr_detail_id
                    ];

                    $indexUpdate++;
                } else {
                    $dataInsert[] = [
                        'satuankecil_id' => $value['satuankecil_id'],
                        'pesanobatalkes_id' => $pesanobatalkes_id,
                        'obatalkes_id' => $value['obatalkes_id'],
                        'jumlah_pesan' => $value['qty_form'] * $value['nilai_konversi'],
                        'satuanbesar_id' => $value['satuanbesar_id'],
                        'jumlah_input' => $value['qty_besar'],
                        'created_by' => $user_login
                    ];
                }
            }

            ApotekComponent::updateMultiple('pesanobatdetail_t', $dataUpdate, $updateCondition);

            if(count($dataInsert) > 0) {
                ApotekComponent::insertMultiple('pesanobatdetail_t', $dataInsert);
            }

            if (count($dataDelete) > 0) {
                $data = PesanObatDetail::find()->where(['in', 'pesanobatdetail_id', $dataDelete])->all();
                foreach ($data as $del) {
                    $del->delete();
                }
            }

            $transaction->commit();

            return [
                'message' => 'OK',
                'text' => 'Berhasil'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch(\Exception $e){
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {

        $header = (new PesanObatAlkes)->delete($id);
        $detail = (new PesanObatDetail)->delete([
            'pesanobatalkes_id' => $id
        ]);

        return [
            'status' => 204
        ];
    }

    /**
    * @controller actionCetakPemesananObat
    * @attribute #tanggal_pemesanan# => Tanggal pemesanan
    * @attribute #tanggal_minta# => Tanggal di kirim
    * @attribute #no_pemesanan# => nomor pemesanan
    * @attribute #ruangan_tujuan# => Ruangan Tujuan Pemesanan
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail pemesanan
    * @attribute #ruangan# => Ruangan Pemesanan
    * @attribute #catatan# => Catatan Pemesanan
    **/
    public function actionCetakPemesananObat($id)
    {
        // Get Header
        $query = InfoPemesananObatAlkesView::find(true)->where([
            'pesanobatalkes_id' => $id
        ])->one();

        // Get Detail
        $detail = DetailPemesananObatAlkes::find(true)->where([
            'pesanobatalkes_id' => $id
        ])->orderBy(['obatalkes_namalain'=>SORT_ASC])->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal_pemesanan#' => !empty($query->tglpemesanan) ? date('d M Y H:i:s',strtotime($query->tglpemesanan)) : '',
            '#tanggal_minta#' => !empty($query->tglmintadikirim) ? date('d M Y H:i:s',strtotime($query->tglpemesanan)) : '',
            '#no_pemesanan#' => !empty($query->nopemesanan) ? $query->nopemesanan : '',
            '#ruangan_tujuan#' => !empty($query->ruangan_tujuan) ? $query->ruangan_tujuan : '',
            '#ruangan#' => !empty($query->ruangan_pemesan) ? ucfirst($query->ruangan_pemesan) : '',
            '#catatan#' => !empty($query->keterangan_pesan) ? $query->keterangan_pesan : '',
            '#tabel_detail#' => $this->renderPartial('index',[
                'detail' => $detail,
                'statusmutasiId' => $query->statusmutasi_id
            ]),
        ];

        $print->Output();
    }

    public function actionVerifikasiPemesanan($pesanobatalkes_id) {
        try {
            $model = PesanObatAlkes::findOne($pesanobatalkes_id);
            $model->status_verifikasi = DocoConstants::OBAT_SUDAH_DIVERIFIKASI;
            if($model->save()){
                return [
                    'status' => 200,
                    'message' => 'Berhasil',
                    'text' => 'Status verifikasi berhasil di update'
                ];
            }

            return [
                'status' => 422,
                'data' => $model->errors
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
