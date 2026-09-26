<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\BatchUpdate;

use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PegawaiView;

use app\modules\v1\models\PembayaranAlokasi;
use app\modules\v1\models\PembayaranAlokasiDetail;
use app\modules\v1\models\InfoPembayaranAlokasi;
use app\modules\v1\models\InfoPembayaranAlokasiDetail;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\TerimaBayarKlaim;

class InformasiAlokasiPembayaranController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPembayaranAlokasi';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['get-pembayaran'] = ['GET'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new InfoPembayaranAlokasi;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_terimabayarklaim_awal']) && isset($_GET['advanced-filter']['tgl_terimabayarklaim_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_terimabayarklaim_awal'];
                $end = $_GET['advanced-filter']['tgl_terimabayarklaim_akhir'];
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['no_terimabayarklaim'])) {
                $no_terimabayarklaim = $_GET['advanced-filter']['no_terimabayarklaim'];
                $query->andWhere(['no_terimabayarklaim' => $no_terimabayarklaim]);
                unset($_GET['advanced-filter']['no_terimabayarklaim']);
            }
            
            if(isset($_GET['advanced-filter']['no_pengajuanklaim'])) {
                $no_pengajuanklaim = $_GET['advanced-filter']['no_pengajuanklaim'];
                $query->andWhere(['no_pengajuanklaim' => $no_pengajuanklaim]);
                unset($_GET['advanced-filter']['no_pengajuanklaim']);
            }
        }

        $query->andWhere(['between', 'tgl_pembayaranalokasi', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $advanced_filter = $request->get('advanced-filter');
        $model = new InfoPembayaranAlokasi;
        $query = $model::find();
        $title = 'Informasi Pembayaran Klaim';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $carabayar_id = $penjamin_id = null;
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_terimabayarklaim_awal']) && isset($_GET['advanced-filter']['tgl_terimabayarklaim_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_terimabayarklaim_awal'];
                $end = $_GET['advanced-filter']['tgl_terimabayarklaim_akhir'];
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['no_terimabayarklaim'])) {
                $no_terimabayarklaim = $_GET['advanced-filter']['no_terimabayarklaim'];
                $query->andWhere(['no_terimabayarklaim' => $no_terimabayarklaim]);
                unset($_GET['advanced-filter']['no_terimabayarklaim']);
            }
            
            if(isset($_GET['advanced-filter']['no_pengajuanklaim'])) {
                $no_pengajuanklaim = $_GET['advanced-filter']['no_pengajuanklaim'];
                $query->andWhere(['no_pengajuanklaim' => $no_pengajuanklaim]);
                unset($_GET['advanced-filter']['no_pengajuanklaim']);
            }
        }
        $header = [];
        $periode = date('d M Y', strtotime($start))." - ".date('d M Y', strtotime($end));
        $header['Tanggal Pembayaran'] = $periode;
        $advancedFilterHeaderNullable = [
            'No Pembayaran' => ArrayHelper::getValue($advanced_filter, 'no_terimabayarklaim', '-'),
            'No Pengajuan' => ArrayHelper::getValue($advanced_filter, 'no_pengajuanklaim', '-'),
            'Cara Bayar' => ArrayHelper::getValue($advanced_filter, 'carabayar_nama', '-'),
            'Penjamin' => ArrayHelper::getValue($advanced_filter, 'penjamin_nama', '-'),
        ];
        $header = array_merge($header, $advancedFilterHeaderNullable);
        $query->andWhere(['between', 'tgl_pembayaranalokasi', $start, $end]);
        $options = [
            "uploadPath" => "./uploads",
            "customFormatCode" => [
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ]
            ],
        ];
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $caraBayarPenjamin = $value['carabayar_nama'] . " /\n" . $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Tanggal Pengajuan')] = date('d M Y', strtotime($value['tgl_pengajuanklaim']));
            $newValue[\Yii::t('app', 'Tanggal Pembayaran')] = !empty($value['tgl_terimabayarklaim']) ?
                date('d M Y', strtotime($value['tgl_terimabayarklaim'])) : '';
            $newValue[\Yii::t('app', 'No Pembayaran')] = $value['no_terimabayarklaim'];
            $newValue[\Yii::t('app', 'No Pengajuan')] = $value['no_pengajuanklaim'];
            $newValue[\Yii::t('app', 'Cara Bayar / Penjamin')] = $caraBayarPenjamin;
            $newValue[\Yii::t('app', 'Total Pengajuan')] = $value['total_pengajuan'];
            $newValue[\Yii::t('app', 'Telah Bayar')] = $value['jumlah_pembayaran'];
            $newValue[\Yii::t('app', 'Total Pembayaran')] = $value['total_pembayaran'];
            $newValue[\Yii::t('app', 'Sisa')] = $value['sisa'];
            if (!empty($carabayar_id)) {
                $header['Cara Bayar'] = $value['carabayar_nama'];
            }
            if (!empty($penjamin_id)) {
                $header['Penjamin'] = $value['penjamin_nama'];
            }
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options,[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #jabatan# => jabatan
    * @attribute #nip# => nip
    * @attribute #pegawai# => pegawai mengetahui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Informasi Pembayaran Klaim';
        $get = $request->get();
        $model = new InfoPembayaranAlokasi;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_terimabayarklaim_awal']) && isset($_GET['advanced-filter']['tgl_terimabayarklaim_akhir'])) {
                $start = $_GET['advanced-filter']['tgl_terimabayarklaim_awal'];
                $end = $_GET['advanced-filter']['tgl_terimabayarklaim_akhir'];
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['no_terimabayarklaim'])) {
                $no_terimabayarklaim = $_GET['advanced-filter']['no_terimabayarklaim'];
                $query->andWhere(['no_terimabayarklaim' => $no_terimabayarklaim]);
                unset($_GET['advanced-filter']['no_terimabayarklaim']);
            }
            
            if(isset($_GET['advanced-filter']['no_pengajuanklaim'])) {
                $no_pengajuanklaim = $_GET['advanced-filter']['no_pengajuanklaim'];
                $query->andWhere(['no_pengajuanklaim' => $no_pengajuanklaim]);
                unset($_GET['advanced-filter']['no_pengajuanklaim']);
            }
        }

        $query->andWhere(['between', 'tgl_pembayaranalokasi', $start, $end]);
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai         = PegawaiView::find()->where(['jabatan_id' => $lookupTransaksi->kode_id])->andWhere(['not', ['pegawai_last_modified_date' => null]])->orderBy(['pegawai_last_modified_date' => SORT_DESC])->one();
        $jabatan = ($pegawai) ? $pegawai->jabatan_nama : '';
        $nip = ($pegawai) ? $pegawai->nomorindukpegawai : '';
        $mengetahui = ($pegawai) ? $pegawai->nama_pegawai : '';
        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
            '#tanggal#' => date('d M Y H:i:s'),
            '#pegawai#' => $mengetahui,
            '#jabatan#' => $jabatan,
            '#nip#' => $nip,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }

    public function actionUpdate($id)
    {   
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $model = PembayaranAlokasi::find()->where([
                'pembayaranalokasi_id' => $id
            ])->one();
            $tanggal = $request->post('tanggal_pembayaran', "today");
            $tanggal_pembayaran = strtotime($tanggal);
            $model->tgl_pembayaranalokasi = date('Y-m-d H:i:s', $tanggal_pembayaran);
            $model->catatan = $request->post('catatan');
            if ($model->save()) {
                $idParent = $id;
                $detail = json_decode($request->post('detail_pembayaran','{}'),true);
                if (is_array($detail)) {
                    $model = (new BatchUpdate(PembayaranAlokasiDetail::tableName(), function($query) use ($detail) {
                        $listKey = [];
                        foreach ($detail as $key => $value) {
                         if ($value['bayar_alokasi'] != $value['jumlah_bayar']) {
                            $query->set([
                                'jumlah_piutang' => $value['jumlah_piutang'],
                                'jumlah_telahbayar' => $value['jumlah_telahbayar'] + $value['jumlah_bayar'],
                                'jumlah_bayar' => $value['jumlah_bayar'],
                                'jumlah_sisapiutang' => $value['jumlah_piutang'] - ($value['jumlah_bayar'] + $value['jumlah_telahbayar']),
                                "last_modified_by" => Yii::$app->user->identity->id,
                                "last_modified_date" =>  "'".date('Y-m-d H:i:s')."'::TIMESTAMP",
                                "modified_count" => "pembayaranalokasidetail_t.modified_count + 1",
                            ], "pembayaranalokasidetail_id = {$key}",$key);
                            array_push($listKey, $key);
                        }
                        if(count($listKey) > 0){
                            $id = implode(", ", $listKey);
                            $query->where('pembayaranalokasidetail_id in('.$id.')');
                        }
                    }
                    }))->execute();
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

    public function actionGetDataTransaksi($id)
    {
        $query = InfoPembayaranAlokasi::find()->where([
            'pembayaranalokasi_id' => $id
        ])->one();

        return [
            'header' => $query
        ];
    }

    public function actionGetDataAlokasi($id)
    {
        $request = Yii::$app->request;
        $model = new InfoPembayaranAlokasiDetail;
        $query = $model::find();

        $query->andWhere([
            'pembayaranalokasi_id' => $id
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetPembayaran()
    {
        $request = Yii::$app->request;
        $term = $request->get('no_pembayaran');
        $model = new TerimaBayarKlaim;
        $query = $model::find()->where(['no_terimabayarklaim' => $term]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionCetakTransaksi
    * @attribute #tanggal_pengajuan# => Menampilkan Tanggal Pengajuan
    * @attribute #cara_bayar# => Menampilkan Cara Bayar
    * @attribute #penjamin# => Menampilkan nama Penjamin
    * @attribute #instalasi# => Menampilkan Instalasi
    * @attribute #ruangan# => Menampilkan Ruangan
    * @attribute #no_pengajuan# => Menampilkan No Pengajuan
    * @attribute #tanggal_pembayaran# => Menampilkan Tanggal Pembayaran
    * @attribute #no_pembayaran# => Menampilkan No Pembayaran
    * @attribute #tanggal_jt# => Menampilkan Tanggal jatuh Tempo
    * @attribute #total_pengajuan# => Menampilkan Total Pengajuan
    * @attribute #total_pembayaran# => Menampilkan Total Pembayaran
    * @attribute #sisa_piutang# => Menampilkan Sisa Piutang
    * @attribute #tabel_alokasi# => Menampilkan Detail Alokasi
    * @attribute #catatan# => Menampilkan Catatan
    * @attribute #tanggal# => Menampilkan Tanggal
    * @attribute #nip# => Menampilkan Nip Pegawai
    * @attribute #pegawai# => Menampilkan Data Pegawai
    * @attribute #jabatan# => Menampilkan Jabatan Pegawai
    */
    public function actionCetakTransaksi($id)
    {
        $header = InfoPembayaranAlokasi::find()->where([
            'pembayaranalokasi_id' => $id
        ])->one();

        $detail = InfoPembayaranAlokasiDetail::find()->where([
            'pembayaranalokasi_id' => $id
        ])->all();

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai         = PegawaiView::find()->where(['jabatan_id' => $lookupTransaksi->kode_id])->andWhere(['not', ['pegawai_last_modified_date' => null]])->orderBy(['pegawai_last_modified_date' => SORT_DESC])->one();
        $jabatan = ($pegawai) ? $pegawai->jabatan_nama : '';
        $nip = ($pegawai) ? $pegawai->nomorindukpegawai : '';
        $mengetahui = ($pegawai) ? $pegawai->nama_pegawai : '';
        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal_pengajuan#' => !empty($header->tgl_pengajuanklaim) ? date('d M Y', strtotime($header->tgl_pengajuanklaim)) : null,
            '#cara_bayar#' => $header->carabayar_nama,
            '#penjamin#' => $header->penjamin_nama,
            '#instalasi#' => $header->instalasi_nama,
            '#ruangan#' => $header->ruangan_nama,
            '#no_pengajuan#' => $header->no_pengajuanklaim,
            '#tanggal_pembayaran#' => !empty($header->tgl_pembayaranalokasi) ? date('d M Y',strtotime($header->tgl_pembayaranalokasi)) : null,
            '#no_pembayaran#' => $header->no_terimabayarklaim,
            '#tanggal_jt#' => !empty($header->tgl_jatuhtempo) ? date('d M Y',strtotime($header->tgl_jatuhtempo)) : null,
            '#total_pengajuan#' => DocoHelpers::formatNumber($header->total_pengajuan),
            '#total_pembayaran#' => DocoHelpers::formatNumber($header->total_pembayaran),
            '#sisa_piutang#' => DocoHelpers::formatNumber($header->sisa),
            '#catatan#' => $header->catatan,
            '#tanggal#' => date('d M Y H:i:s'),
            '#nip#' => $nip,
            '#jabatan#' => $jabatan,
            '#pegawai#' => $mengetahui,
            '#tabel_alokasi#' => $this->renderPartial('_detail', [
                'data' => $detail,
            ]),
        ];

        $print->Output();
    }

    public function actionDelete($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $header = (new PembayaranAlokasi)->delete([
                'pembayaranalokasi_id' => $id
            ]);

            $detail = (new PembayaranAlokasiDetail)->delete([
                'pembayaranalokasi_id' => $id
            ]);

            $transaction->commit();
            return [
                'title' => 'Proses Berhasil!',
                'text' => 'Data Alokasi berhasil dihapus'
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

    public function actionGenerateApi()
    {
        // cara bayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find()
            ->where(['is_active' => true])
            ->andWhere(['<>', 'carabayar_id', DocoConstants::PENJAMIN_UMUM]);

        $queryCaraBayar = $queryCaraBayar->asArray()->all();

        // cara bayar penjamin
        $queryCaraBayarPenjamin = [];

        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()->where(['is_active' => true])
            ->andWhere(['<>', 'carabayar_id', DocoConstants::PENJAMIN_UMUM]);
        $queryPenjamin = $queryPenjamin->asArray()->all();


        return [
            'cara_bayar' => $queryCaraBayar,
            'penjamin' => $queryPenjamin,
        ];
    }

}