<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoConstants;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PengajuanKlaim;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoActiveController;
use app\modules\v1\models\LookupTransaksi;
use SirsCore\features\IntegrasiAkunting;
use app\modules\v1\models\PengajuanKlaimView;
use app\modules\v1\models\PengajuanKlaimDetail;
use app\modules\v1\actions\TransaksiPengajuanKlaim\CetakPengajuanAction;
use app\modules\v1\actions\TransaksiPengajuanKlaim\CetakListAction;

class TransaksiPengajuanKlaimController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PengajuanKlaimView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index']        = ['GET'];
        $verbs['export-excel'] = ['GET'];
        $verbs['export-pdf']   = ['GET'];
        $verbs['save']         = ['POST'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $defaultPaging = $request->get('per-page');
        $isPaging = $request->get('is_paging');
        if($isPaging === null) {
            $isPaging = $defaultPaging;
        }else{
            $isPaging = -1;
            unset($_GET['per-page']);
        }
        if (empty($_GET['carabayar_id'])) {
            \Yii::$app->response->statusCode = 400;

            return ['error' => 'Cara bayar id tidak ditemukan'];
        }

        if (empty($_GET['penjamin_id'])) {
            \Yii::$app->response->statusCode = 400;

            return ['error' => 'Penjamin id tidak ditemukan'];
        }
        
        $model = new PengajuanKlaimView;
        $query = $model::find()->where([
            'carabayar_id' => $_GET['carabayar_id'],
            'penjamin_id'  => $_GET['penjamin_id']
        ]);
        $query->andWhere(['not', 
            ['no_pembayaran' => null]
        ]);
        $query->andWhere(['pengajuanklaim_id' => null]);

        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpasienpulang_awal']) && isset($_GET['advanced-filter']['tglpasienpulang_akhir'])) {
                $start = $_GET['advanced-filter']['tglpasienpulang_awal'];
                $end   = $_GET['advanced-filter']['tglpasienpulang_akhir'];
            }

            if (isset($_GET['advanced-filter']['instalasi_nama'])) {
                $instalasi_id = $_GET['advanced-filter']['instalasi_nama'];
                $query->andWhere(['instalasi_id' => $instalasi_id]);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }
        
        $query->andWhere(['between', 'tglpasienpulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $isPaging
            ]
        ]);
    }

    public function actionExportExcel()
    {
        if (empty($_GET['carabayar_id'])) {
            \Yii::$app->response->statusCode = 400;

            return ['error' => 'Cara bayar id tidak ditemukan'];
        }

        if (empty($_GET['penjamin_id'])) {
            \Yii::$app->response->statusCode = 400;

            return ['error' => 'Penjamin id tidak ditemukan'];
        }

        $model = new PengajuanKlaimView;
        $query = $model::find()->where([
            'carabayar_id' => $_GET['carabayar_id'],
            'penjamin_id'  => $_GET['penjamin_id']
        ]);
        $query->andWhere(['not', 
            ['no_pembayaran' => null]
        ]);
        $query->andWhere(['pengajuanklaim_id' => null]);

        $title            = 'Transaksi Pengajuan Klaim';
        $tgl_awal         = date('Y-m-d 00:00:00');
        $tgl_akhir        = date('Y-m-d 23:59:59');
        $arrTanggalKeluar = [];
        $arrayInstalasi   = [];
        $arrayRuangan     = [];

        if (isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];

            // Tanggal
            if (isset($advancedFilters['tglpasienpulang'])) {
                $helper = new DocoHelpers;
                $tglpasienpulang = $helper->parsingRangeDate($advancedFilters['tglpasienpulang']);
                
                $tgl_awal  = $tglpasienpulang['startDate'];
                $tgl_akhir = $tglpasienpulang['endDate'];

                $arrTanggalKeluar = [Yii::t('app', "Tanggal Keluar") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            } else {
                $arrTanggalKeluar = [Yii::t('app', "Tanggal Keluar") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            }

            // Instalasi
            if (isset($advancedFilters['instalasi_nama'])) {
                $instalasi_id = $advancedFilters['instalasi_nama'];
                $query->andWhere(['instalasi_id' => $instalasi_id]);
                
                $instalasi      = Instalasi::findOne($instalasi_id);
                $instalasi_nama = $instalasi->instalasi_nama;
                $arrayInstalasi = [Yii::t('app', "Instalasi") => $instalasi_nama];
            } else {
                $arrayInstalasi = [Yii::t('app', "Instalasi") => '-'];
            }

            // Ruangan
            if (isset($advancedFilters['ruangan_nama'])) {
                $ruangan_id = $advancedFilters['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                
                $ruangan      = Ruangan::findOne($ruangan_id);
                $ruangan_nama = $ruangan->ruangan_nama;
                $arrayRuangan = [Yii::t('app', "Ruangan") => $ruangan_nama];
            } else {
                $arrayRuangan = [Yii::t('app', "Ruangan") => '-'];
            }

            // Title
            if (isset($advancedFilters['title'])) {
                $title = $advancedFilters['title'];
            }
        } else {
            $arrTanggalKeluar = [Yii::t('app', "Tanggal Keluar") => ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))))];
            $arrayInstalasi   = [Yii::t('app', "Instalasi") => '-'];
            $arrayRuangan     = [Yii::t('app', "Ruangan") => '-'];
        }

        $additional = array_merge(
            $arrTanggalKeluar,
            $arrayInstalasi,
            $arrayRuangan
        );

        $header = array_merge($additional);
        $query->andWhere(['between', 'tglpasienpulang', $tgl_awal, $tgl_akhir]);

        $options = [
            array("uploadPath" => "./uploads"),
            "titleStyle" => [
                "fontSize"   => 15,
                "alignment"  => "center",
                "fontWeight" => 600,
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'A',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'B',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode'   => 'general'
                ],
            ],
        ];

        $result = [];
        $totalAsuransi = 0;
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Data Pasien')]              = $value['nama_pasien'] . "\n" . $value['no_rekam_medik'] . "\n" . $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No Invoice')]               = $value['no_pembayaran'];
            $newValue[\Yii::t('app', 'Tanggal Masuk')]            = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $newValue[\Yii::t('app', 'Tanggal Keluar')]           = !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : '';
            $newValue[\Yii::t('app', 'No SEP')]                   = $value['nosep'] ? $value['nosep'] : '-';
            $newValue[\Yii::t('app', 'Instalasi / Ruangan')]      = $value['instalasi_nama'] . "\n" . $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Tagihan')]                  = $value['total_tagihan'];
            $newValue[\Yii::t('app', 'Jumlah Discount')]          = $value['total_discountpembayaran'];
            $newValue[\Yii::t('app', 'Jumlah Dibayarkan Pasien')] = $value['total_sdh_bayar'];
            $newValue[\Yii::t('app', 'Jumlah Pengajuan')]         = $value['total_asuransi'];
            // $totalAsuransi                                       += $value['total_asuransi'];
            $result[$key] = $newValue;
        }

        // $options['totalCount'] = [
        //     'title' => \Yii::t('app', 'Total Pengajuan'),
        //     'value' => $totalAsuransi,
        //     'columnValue' => 'K',
        //     'columnLabel' => 'G',
        // ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Data Table (Konten Table-nya)
    * @attribute #tanggal_keluar_range# => Tanggal Keluar Range (... s/d ...)
    * @attribute #cara_bayar# => Header Cara Bayar
    * @attribute #penjamin# => Header Nama Penjamin
    * @attribute #total_pengajuan# => Header Total Pengajuan
    * @attribute #tanggal# => Tanggal Cetak (Tanda Tangan)
    * @attribute #jabatan# => Jabatan Penannggung jawab (Tanda Tangan)
    * @attribute #pegawai# => Nama Pegawai Penannggung jawab (Tanda Tangan)
    * @attribute #nip# => NIP Pegawai Penannggung jawab (Tanda Tangan)
    */
    public function actionExportPdf()
    {
        $action = new CetakListAction('export-pdf', $this);
        return $action->run();
    }

    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $query = Penjamin::findOne($request->get('penjamin_id'));

        return $query;
    }

    public function actionSave()
    {
        $request           = Yii::$app->request;
        $post              = $request->post();
        $connection        = Yii::$app->db;
        $transaction       = $connection->beginTransaction();
        $model             = new PengajuanKlaim;
        $model->attributes = $post;
        
        $instalasi_id = Yii::$app->jwt->instalasi_id;
        $ruangan_id   = Yii::$app->jwt->ruangan_id;

        Yii::error($post);
        $model->instalasi_id        = $instalasi_id;
        $model->ruangan_id          = $ruangan_id;
        $model->total_terbayar      = 0;
        $model->tgl_jatuhtempo      = date('Y-m-d H:i:s', strtotime($post['tgl_jatuhtempo']));
        $model->tgl_pelayanandari   = date('Y-m-d H:i:s', strtotime($post['tgl_pelayanandari']));
        $model->tgl_pelayanansampai = date('Y-m-d H:i:s', strtotime($post['tgl_pelayanansampai']));
        $model->tgl_pengajuanklaim  = date('Y-m-d H:i:s', strtotime($post['tgl_pengajuanklaim']));
        $model->tgl_keluardari   = date('Y-m-d', strtotime($post['tgl_keluardari']));
        $model->tgl_keluarsampai   = date('Y-m-d', strtotime($post['tgl_keluarsampai']));

        $arr = [];
        if (isset($post['pendaftaran_id'])) {
            $pendaftaran_id = json_decode($post['pendaftaran_id'], true);
            $arr = $pendaftaran_id;
        }
        
        try {
            if ($model->validate() && $model->save()) {
                if (!empty($arr)) {
                    $insertDetail = [];
                    foreach ($arr as $key => $value) {
                        $idParent = $model->pengajuanklaim_id;
                        if (empty($value['piutang'])) continue;
                        $insertDetail[] = [
                            'pembayaranpelayanan_id' => !empty($value['pembayaranpelayanan_id']) ? $value['pembayaranpelayanan_id'] : null,
                            'pendaftaran_id'         => !empty($value['pendaftaran_id']) ? $value['pendaftaran_id'] : null,
                            'pasien_id'              => $value['pasien_id'],
                            'pasienadmisi_id'        => !empty($value['pasienadmisi_id']) ? $value['pasienadmisi_id'] : null,
                            'pengajuanklaim_id'      => $idParent,
                            'jumlah_piutang'         => $value['piutang'],
                            'jumlah_bayar'           => 0,
                            'jumlah_telahbayar'      => 0,
                            'jumlah_sisapiutang'     => $value['piutang'],
                        ];
                    }

                    PengajuanKlaimDetail::batchInsert($insertDetail);
                    $transaction->commit();
                    IntegrasiAkunting::integratePengajuanKlaim($idParent);
                    $pengajuanKlaimId = DocoHelpers::encrypt($model->pengajuanklaim_id);
                    return [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Pengajuan klaim berhasil disimpan',
                        'pengajuanklaim_id' => $pengajuanKlaimId,
                        'no_pengajuanklaim' => $model->no_pengajuanklaim,
                    ];
                }
            } else {
                return [
                    'data'   => $model->errors,
                    'status' => 422
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 400;

            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 400;

            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @controller actionCetakPengajuan
     * @attribute #no_pengajuan# => Nomor Pengajuan
     * @attribute #tanggal_keluar_range# => Tanggal Keluar Range (... s/d/ ...)
     * @attribute #total_pengajuan# => Total Pengajuan
     * @attribute #cara_bayar# => Cara Bayar
     * @attribute #catatan# => Catatan
     * @attribute #tanggal_pengajuan# => Tanggal Pengajuan
     * @attribute #tanggal_jatuh_tempo# => Tanggal Jatuh Tempo
     * @attribute #penjamin# => Penjamin
     * @attribute #datatable# => Data Table (Konten Table-nya)
     * @attribute #tanggal# => Tanggal Cetak (Tanda Tangan)
     * @attribute #jabatan# => Jabatan Penannggung jawab (Tanda Tangan)
     * @attribute #pegawai# => Nama Pegawai Penannggung jawab (Tanda Tangan)
     * @attribute #nip# => NIP Pegawai Penannggung jawab (Tanda Tangan)
     **/
    public function actionCetakPengajuan()
    {
        $action = new CetakPengajuanAction('cetak-pengajuan', $this);
        return $action->run();
    }
}