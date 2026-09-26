<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoKso;
use Doco\components\DocoMessages;

use app\modules\v1\models\ClosingKasir;
use app\modules\v1\models\RincianClosing;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\TandaBuktiBayar;
use app\modules\v1\models\TandaBuktiKeluar;
use app\modules\v1\models\ClosingKasirView;
use app\modules\v1\models\InfoClosingKasirView;
use app\modules\v1\models\InfoClosingKasirDetailView;
use app\modules\v1\models\InfoClosingKasirHeaderView;
//Penambahan KSO ali.padilah@docotel.com
use app\modules\v1\models\InvoiceksoView;

use app\modules\v1\cache\Cache;
use app\modules\v1\payload\ClosingKasirPayload;

class TandaBuktiBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TandaBuktiBayar';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-attributes"] = ["GET"];
        $verbs["save"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }


    public function actionGetAttributes($instalasi_id = null, $ruangan_id = null)
    {
        $listRuangan = Cache::getListRuangan($instalasi_id);
        $listNilaiUang = Cache::getNilaiUang();
        $listPegawaiRuangan = Cache::getListPegawaiRuangan($ruangan_id);
        $listShift = Cache::getShift();
        $user = Yii::$app->jwt;
        $userId = $user->user->pegawai_id;
        $ruanganId = !empty($user->ruangan_id) ? $user->ruangan_id : null;
        
        $sumClosing = Yii::$app->db->createCommand("
            SELECT 
                SUM(jmlpembayaran) as jmlpembayaran, 
                SUM(pembayaran_tunai) as pembayaran_tunai,
                SUM(pembayaran_nontunai) as pembayaran_nontunai,
                SUM(pembayaran_penjamin) as pembayaran_penjamin
            FROM closing_kasir_view
            WHERE pegawai1_id = {$userId} 
            AND ruangan_id = {$ruanganId} 
            AND closingkasir_id IS NULL
        ")->queryOne();
        // return $listNilaiUang;
        return [
            'list_ruangan' => $listRuangan,
            'list_nilai_uang' => $listNilaiUang,
            'list_pegawai_ruangan' => $listPegawaiRuangan,
            'list_shift' => $listShift,
            'attr_closing' => $sumClosing,
        ];
    }

    public function actionIndex($unpaid = false)
    {
        // $userId = Yii::$app->user->identity->loginpemakai_id;
        $user = Yii::$app->jwt;
        $userId = !empty($user->user->pegawai_id) ? $user->user->pegawai_id : null;
        $ruanganId = !empty($user->ruangan_id) ? $user->ruangan_id : null;
        $request = Yii::$app->request;
        $model = new ClosingKasirView;
        $query = $model::find();
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        // if(isset($_GET['advanced-filter'])) {
        //     if(isset($_GET['advanced-filter']['tglbuktibayar'])) {
        //         $explode = explode(" - ", $_GET['advanced-filter']['tglbuktibayar']);
        //         if(count($explode) == 2) {
        //             $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
        //             $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
        //         }
        //         unset($_GET['advanced-filter']['tglbuktibayar']); // Unset Advanced Filter  date range
        //     }
        // }
        // $query->andWhere(['between', 'tglbuktibayar', $start, $end]); 

        // filter berdasarkan login pemakai
        $query->andWhere(['pegawai1_id' => $userId]);
        $query->andWhere(['ruangan_id' => $ruanganId]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);


        if ($unpaid) {
            $query->andWhere(['closingkasir_id' => null]);
        }

        return new ActiveDataProvider([
            'query' => $query->asArray(),
        ]);
    }

    protected function getShift()
    {
        $time = strtotime(date("H:i:s"));
        $listShift = Cache::getShift();
        $shift_id = null;

        foreach ($listShift as $value) {
            $timeStart = strtotime($value['shift_jamawal']);
            $timeEnd = strtotime($value['shift_jamakhir']);
            if ($time >= $timeStart && $time <= $timeEnd) {
                $shift_id = $value['shift_id'];
                break;
            }
        }
        return $shift_id;
    }

    public function actionHeader($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirHeaderView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);
        
        return $query->one();
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);

        return $query->asArray()->all();
    }

    public function actionRincianClosing($id)
    {
        $request = Yii::$app->request;
        $model = new InfoClosingKasirDetailView;
        $query = $model::find();
        $query->andWhere(['closingkasir_id' => $id]);

        return $query->asArray()->all();
    }

    /**
    * @controller actionExportPdf
    * @attribute #no_closing_kasir# => Untuk menambilkan nomor klosing kasir
    * @attribute #tgl_closing# => untuk menampilkan tanggal closing
    * @attribute #ruangan# => untuk menampilkan Ruangan
    * @attribute #shift# => untuk menampilkan shift pegawai
    * @attribute #tabel_transaksi# => Untuk menampilkan tabel transaksi closing
    * @attribute #tabel_detail# => untuk menampilkan detail nominal closing kasir
    **/

    public function actionExportPdf($id)
    {
        $header = $this->actionHeader($id);

        $model = new InfoClosingKasirView;
        $detail = $this->actionDetail($id);
        $rincianClosing = $this->actionRincianClosing($id);
        // return $id;
        // return $detail;
        // return $header;
        // $query = ClosingKasir::find()
        //             ->select([
        //                 'closingkasir_t.*',
        //                 'shift_m.shift_nama',
        //             ])
        //             ->joinWith([
        //                 'rincianClosing' => function ($query) {
        //                     $query->select([
        //                         'rincianclosing_t.closingkasir_id',
        //                         'rincianclosing_t.nilaiuang',
        //                         'rincianclosing_t.banyakuang',
        //                         'rincianclosing_t.jumlahuang',
        //                     ]);
        //                 },
        //                 'view' => function ($query) {
        //                     $query->select([
        //                         'closing_kasir_view.closingkasir_id',
        //                         'closing_kasir_view.tglbuktibayar',
        //                         'closing_kasir_view.no_pendaftaran',
        //                         'closing_kasir_view.nama_pasien',
        //                         'closing_kasir_view.uangditerima',
        //                         'closing_kasir_view.jmlpembayaran',
        //                         'closing_kasir_view.penjamin_nama',
        //                         'closing_kasir_view.carabayar_nama',
        //                     ]);
        //                 },
        //                 'pegawai' => function ($query) {
        //                     $query->select([
        //                         'pegawai_m.pegawai_id',
        //                         'pegawai_m.nama_pegawai',
        //                     ]);
        //                 },
        //                 'shift' => function ($query) {
        //                     $query->select([
        //                         'shift_m.shift_id',
        //                         'shift_m.shift_nama',
        //                     ]);
        //                 },
        //                 'ruangan' => function ($query) {
        //                     $query->select([
        //                         'ruangan_m.ruangan_id',
        //                         'ruangan_m.ruangan_nama',
        //                     ]);
        //                 }
        //             ])->where([
        //                 'closingkasir_t.closingkasir_id' => $id
        //             ])->asArray()->one();

        // return $query;
        // $pegawai = Pegawai::find()->where([
        //     'pegawai_id' => $query['pegawaimengetahui_id']
        // ])->one();
        $print = new DocoPrint();
        $print->attributes = [
            '#no_closing_kasir#' => !empty($header->no_closingkasir) ? $header->no_closingkasir : null,
            '#tgl_closing#' => !empty($header->tgl_closingkasir) ? date('d-M-Y',strtotime($header->tgl_closingkasir)) : null,
            '#ruangan#' => !empty($header->ruangan_nama) ? $header->ruangan_nama : null,
            '#shift#' => !empty($header->shift_nama) ? $header->shift_nama : null,
            '#tabel_transaksi#' => $this->renderPartial('index',[
                'detail' => $detail
            ]),
            '#tabel_detail#' => $this->renderPartial('detail',[
                'detailPecahan' => $rincianClosing
            ]),
            '#tanggal#' => date('d-M-Y'),
            '#pegawai_megetahui#' => !empty($header->nama_pegawai) ? $header->nama_pegawai : null
        ];

        $print->Output();
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $user = Yii::$app->jwt;
        $pegawai_id = !empty($user->user->pegawai_id) ? $user->user->pegawai_id : null;
        
        $ruanganId = !empty($user->ruangan_id) ? $user->ruangan_id : null;
        $payload = new ClosingKasirPayload;
        $payload->attributes  = $request->post();
        $transaction = $connection->beginTransaction();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        
        try {
            if ($payload->validate()) {
                $model = ClosingKasirView::find()->andWhere([
                    'pegawai1_id' => $pegawai_id,
                    'ruangan_id' => $ruanganId,
                    'closingkasir_id' => null
                ])->orderBy([
                    'tglbuktibayar' => SORT_ASC
                ])->asArray()->all();
                if (!empty($model)) {
                    $listIdPembayaran = [];
                    $listIdRetur = [];
                    $startClosing = $endClosing = null;
                    $jumlahTrans = $totalUangMuka = $totalUangPelayanan = 0;
                    $piutang = $nonTunai = 0;
                    foreach ($model as $key => $value) {
                        $nilaiTunai = !empty($value['pembayaran_tunai']) ? $value['pembayaran_tunai'] : 0;
                        if ($key === 0) {
                            $startClosing = $value['tglbuktibayar'];
                        }
                        $endClosing = $value['tglbuktibayar'];

                        if (!empty($value['penjamin_id'])) {
                            $totalUangPelayanan += $nilaiTunai;
                        } else {
                            $totalUangMuka += $nilaiTunai;
                        }
                        $nonTunai += !empty($value['pembayaran_nontunai']) ? $value['pembayaran_nontunai'] : 0;
                        $piutang += !empty($value['pembayaran_penjamin']) ? $value['pembayaran_penjamin'] : 0;

                        if(!empty($value['tandabuktibayar_id'])) {
                            $listIdPembayaran[] = $value['tandabuktibayar_id'];
                        }

                        if(!empty($value['tandabuktikeluar_id'])) {
                            $listIdRetur[] = $value['tandabuktikeluar_id'];
                        }
                        
                        $jumlahTrans++;
                    }
                    $clsKasir = new ClosingKasir;
                    $clsKasir->shift_id = $payload->shift_id;
                    $clsKasir->closing_saldoawal = $payload->saldo_awal;
                    $clsKasir->pegawai_id = $pegawai_id;
                    $clsKasir->ruangan_id = $user->ruangan_id;
                    $clsKasir->terima_uangmuka = $totalUangMuka;
                    $clsKasir->terima_uangpelayanan = $totalUangPelayanan;
                    $clsKasir->piutang = $piutang;
                    $clsKasir->pembayaran_nontunai = $nonTunai;
                    $clsKasir->nilai_closingtransaksi = $totalUangPelayanan + $totalUangMuka;
                    $clsKasir->total_setoran = $totalUangPelayanan + $totalUangMuka;
                    $clsKasir->tgl_closingkasir = date('Y-m-d H:i:s');
                    $clsKasir->closing_dari = !empty($startClosing) ? date('Y-m-d H:i:s', strtotime($startClosing)) : date('Y-m-d H:i:s');
                    $clsKasir->sampai_dengan = !empty($endClosing) ? date('Y-m-d H:i:s', strtotime($endClosing)) : date('Y-m-d H:i:s');
                    $clsKasir->jumlah_transaksi = $jumlahTrans;
                    if ($clsKasir->validate() && $clsKasir->save()) {
                        $idParent = $clsKasir->closingkasir_id;
                        if (!empty($listIdPembayaran)) {
                            $inCondition = "(" . implode(",", $listIdPembayaran) . ")";
                            Yii::$app->db->createCommand("
                                UPDATE tandabuktibayar_t SET closingkasir_id = {$idParent}
                                WHERE tandabuktibayar_id IN {$inCondition}
                            ")->execute();
                        }

                        if(!empty($listIdRetur)) {
                            $inCondition = "(" . implode(",", $listIdRetur) . ")";
                            Yii::$app->db->createCommand("
                                UPDATE tandabuktikeluar_t SET closingkasir_id = {$idParent}
                                WHERE tandabuktikeluar_id IN {$inCondition}
                            ")->execute();
                        }
                        $transaction->commit();
                        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                            'additional' => [
                                'id_parent' => DocoHelpers::encrypt($idParent),
                            ]
                        ]);
                    }
                    else {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $clsKasir->errors
                        ]);
                    }
                }
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Tidak ada data yang harus diclosing.'
                ]);
            }
            else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }
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

    public function storeKso($data = [])
    {
        $sendData = [];
        try {
            $dataInvoice = InvoiceksoView::find(true)
                            ->where(['tandabuktibayar_id' => $data ])
                            ->asArray()->all();

            foreach ($dataInvoice as $key => $value) {
                $sendData[] = [
                    'tandabuktibayar_id' => $value['tandabuktibayar_id'],
                    'tgl_invoice' => $value['tgl_invoice'],
                    'no_invoice' => $value['no_invoice'],
                    'no_pendaftaran' => $value['no_pendaftaran'],
                    'nama_pasien' => $value['nama_pasien'],
                    'instalasi_id' => $value['instalasi_id'],
                    'total_tagihan' => $value['jmlpembayaran']
                ];
            }

            $data = [
                'pendapatan' => $sendData
            ];

            $return = DocoKso::sendKlaim($data, 'POST');

            $dataReturn = (array) json_decode($return);

            if ($dataReturn['error_code'] == 200) {
                $listIdPembayaran = $dataReturn['data'];
                $inCondition = "(" . implode(",", $listIdPembayaran) . ")";
                    Yii::$app->db->createCommand("
                        UPDATE tandabuktibayar_t SET is_kso = true
                        WHERE tandabuktibayar_id IN {$inCondition}
                    ")->execute();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }
}