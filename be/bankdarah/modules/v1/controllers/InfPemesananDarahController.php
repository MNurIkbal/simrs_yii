<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanDarahPmi;
use app\modules\v1\models\PesanDarahPmiDetail;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\JenisDarah;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoPesanDarahPmiView;
use app\modules\v1\models\InfoPesanDarahPmiDetailView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\TerimaDarahPmi;
use app\modules\v1\models\TerimaDarahPmiDetail;
use app\modules\v1\models\InfoTerimaDarahPmiView;
use app\modules\v1\models\InfoTerimaDarahPmiDetailView;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class InfPemesananDarahController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\PesanDarahPmi';
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPesanDarahPmiView;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pesandarahpmi'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesandarahpmi']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pesandarahpmi']);
                }
            }
            
            $query->andWhere(['between', 'tgl_pesandarahpmi', $start, $end]);
            $query->orderBy(['tgl_pesandarahpmi', 'DESC']);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoPesanDarahPmiView;
        $query = $model::find();
        $title = 'Informasi Pemesanan Darah PMI';
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        
        $namaPmi = [];
        $no_pesandarahpmi = [];
        $periode = [];
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_pesandarahpmi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesandarahpmi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
            if(isset($advancedFilters['supplier_nama'])) {
                $supplier_nama = $advancedFilters['supplier_nama'];
                $query->andWhere(['ILIKE', 'supplier_nama', $supplier_nama]);
                $namaPmi = [
                    Yii::t('app', 'Nama PMI') => $supplier_nama
                ];
            }
            if(isset($advancedFilters['no_pesandarahpmi'])) {
                $no_pesandarahpmi = $advancedFilters['no_pesandarahpmi'];
                $query->andWhere(['ILIKE', 'no_pesandarahpmi', $no_pesandarahpmi]);
                $no_pesandarahpmi = [
                    Yii::t('app', 'Nomor Pemesanan') => $no_pesandarahpmi
                ];
            }
        }

        $query->andWhere(['between', 'tgl_pesandarahpmi', $start, $end]);
        $periode = [
            Yii::t('app', "Periode") => date('d-M-Y H:i:s', strtotime($start)).' - '.date('d-M-Y H:i:s', strtotime($end))
        ];

        $header = array_merge($periode, $namaPmi, $no_pesandarahpmi);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pemesanan')] = date('d-M-Y', strtotime($value['tgl_pesandarahpmi']));
            $newValue[\Yii::t('app', 'Nomor Pemesanan')] = $value['no_pesandarahpmi'];
            $newValue[\Yii::t('app', 'Nama PMI')] = $value['supplier_nama'];
            $newValue[\Yii::t('app', 'Jumlah Pemesanan')] = is_null($value['qty_pesan']) ? 0 : $value['qty_pesan'];
            $newValue[\Yii::t('app', 'Jumlah Diterima')] = is_null($value['qty_diterima']) ? 0 : $value['qty_diterima'];
            $newValue[\Yii::t('app', 'Sisa')] = is_null($value['qty_sisa']) ? 0 : $value['qty_sisa'];
            $result[$key] = $newValue;
        }

        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Tanggal Pemesanan' => 'Tanggal Unduh : ' . date('d M Y'),
            ]
        ];


        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],$footer,[],true);
        $filePath->save('php://output');
        die;
    }

    public function actionGetDataPemesanan($pesandarahpmi_id)
    {
        $data = InfoPesanDarahPmiView::find()
            ->where(['pesandarahpmi_id' => $pesandarahpmi_id])
            ->one();

        return $data;
    }

    public function actionDataDetail($pesandarahpmi_id)
    {
        try {
            
            $request = Yii::$app->request;
            $model = new InfoPesanDarahPmiDetailView;
            $query = $model::find()->where(['pesandarahpmi_id' => $pesandarahpmi_id]);
            
            return $query->asArray()->all();

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

    public function actionGetDataPetugas()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = $get['ruangan_id'];
        $term = $get['term'];
        $query = PegawaiView::find()
            ->where(['ruangan_id' => $ruangan_id]);
        
        if($term) {
            $query->andFilterWhere(['or',
            ['like','LOWER(nama_pegawai)',strtolower($term)],
            ['like','LOWER(nomorindukpegawai)',strtolower($term)]]);
        }

        return $query->limit(10)->asArray()->all();
    }

    public function actionSavePenerimaan()   
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = $no_terimadarahpmi = null;
            $request = Yii::$app->request;
            $header = $request->post('header');
            $dataJson = $request->post('detail',"{}");
            return $dataJson;
            
            // Insert Ke teransakasi penerimaan darah
            $model = new TerimaDarahPmi;
            $model->dataDetail = $dataJson;
            $model->penerima_id = $header['penerima_id'];
            $model->ruangan_id = $header['ruangan_id'];
            $model->pesandarahpmi_id = $header['pesandarahpmi_id'];
            $model->tgl_terimadarahpmi = date('Y-m-d H:i:s');
            
            if ($model->validate() && $model->save()) {
                $dataInsert = [];
                $totalHarga = 0;
                $totalKantong = 0;
                $idParent = $model->terimadarahpmi_id;
                $no_terimadarahpmi = $model->no_terimadarahpmi;
                foreach ($dataJson as $key => $value) {
                    if(is_array($value)) {
                        foreach ($value as $attr) {
                            $totalHarga += $attr['harga_satuan'];
                            $totalKantong += $attr['qty_pesan'];
                            $dataInsert[] = [
                                'terimadarahpmi_id' => $idParent,
                                'pesandarahpmidetail_id' => $attr['pesandarahpmidetail_id'],
                                'jenisdarah_id' => $attr['jenisdarah_id'],
                                'golongandarah_id' => $attr['golongandarah_id'],
                                'no_kantongdarah' => $attr['no_kantongdarah'],
                                'tgl_pengambilan' => date('Y-m-d', strtotime($attr['tgl_pengambilan'])),
                                'harga' => $attr['harga_satuan'],
                            ];
                        }
                    }
                }

                // Insert Ke teransakasi penerimaan darah detail
                TerimaDarahPmiDetail::batchInsert($dataInsert, false);
                $modelPmi = TerimaDarahPmi::findOne($idParent);
                $modelPmi->total_harga = $totalHarga;
                $modelPmi->total_kantongdarah = $totalKantong;
                $modelPmi->save(false);

                $transaction->commit();
                return [
                    'message' => 'sukses',
                    'id_parent' => $idParent,
                    'no_terimadarahpmi' => $modelPmi->no_terimadarahpmi
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdfPenerimaan
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #nama_pmi# => nama pmi
    * @attribute #no_terimadarahpmi# => nomor penerimaan
    * @attribute #title# => title
    * @attribute #alamat# => alamat
    * @attribute #no_tlp# => no_tlp
    * @attribute #tanggal# => tanggal
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #jabatan# => jabatan
    * @attribute #tgl_terimadarahpmi# => tgl_terimadarahpmi
    * @attribute #tgl_pesandarahpmi# => tgl_pesandarahpmi
    */
   
    public function actionExportPdfPenerimaan()
    {
        $request = Yii::$app->request;
        $no_terimadarahpmi = $request->get('no_terimadarahpmi');
        $title = 'Penerimaan Pemesanan Darah PMI';
        $model = new InfoTerimaDarahPmiView;
        $header = $model::find()->where(['no_penerimaan' => $no_terimadarahpmi])->one();
        $detail = InfoTerimaDarahPmiDetailView::find()
            ->where(['terimadarahpmi_id' => $header->terimadarahpmi_id])
            ->asArray()->all();

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $pegawai = PegawaiView::find()->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])->one();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#no_terimadarahpmi#' => $no_terimadarahpmi,
            '#no_pemesanan#' => $header->no_pemesanan,
            '#nama_pmi#' => $header->nama_pmi,
            '#tgl_pesandarahpmi#' => date('d-M-Y', strtotime($header->tgl_pesandarahpmi)),
            '#alamat#' => $header->supplier_alamat,
            '#no_tlp#' => $header->no_tlp,
            '#tgl_terimadarahpmi#' => date('d-M-Y', strtotime($header->tgl_terimadarahpmi)),
            '#tanggal#' => date('d-M-Y'),
            '#nama_pegawai#' => isset($pegawai) ? $pegawai->nama_pegawai : "",
            '#datatable#' => $this->renderPartial('_cetak_penerimaan', [
                'data' => $detail,
            ]),
        ];

        $print->Output();
    }

    /**
    * @controller actionExportPdfTransaksi
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #nama_pmi# => nama pmi
    * @attribute #no_pesandarahpmi# => nomor pemesanan
    * @attribute #title# => title
    * @attribute #alamat# => alamat
    * @attribute #no_tlp# => no_tlp
    * @attribute #tanggal# => tanggal
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #no_pemesanan# => no_pemesanan
    * @attribute #namaRs# => namaRs
    * @attribute #alamatRs# => alamatRs
    */
   
    public function actionExportPdfTransaksi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $namaRs = $request->get('namaRs');
        $alamatRs = $request->get('alamatRs');
        $title = 'Pemesanan Darah '.$namaRs;
        $model = new InfoPesanDarahPmiView;

        $header = $model::find()->where(['pesandarahpmi_id' => $id])->one();
        $detail = InfoPesanDarahPmiDetailView::find()
            ->where(['pesandarahpmi_id' => $id])
            ->asArray()->all();

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $pegawai = PegawaiView::find()->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])->one();
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => $title,
            '#namaRs#' => $namaRs,
            '#alamatRs#' => $alamatRs,
            '#no_pesandarahpmi#' => $header->no_pesandarahpmi,
            '#nama_pmi#' => $header->supplier_nama,
            '#alamat#' => $header->supplier_alamat,
            '#no_tlp#' => $header->no_tlp,
            '#tanggal#' => date('d-M-Y'),
            '#nama_pegawai#' => isset($pegawai) ? $pegawai->nama_pegawai : "",
            '#jabatan#' => isset($pegawai) ? $pegawai->jabatan_nama : "",
            '#datatable#' => $this->renderPartial('_cetak_pemesanan_transaksi', [
                'data' => $detail,
            ]),
        ];

        $print->Output();
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #jabatan# => jabatan
    * @attribute #title# => title
    * @attribute #pegawai# => pegawai mengetahui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $namaRs = $get['namaRs'];
        $title = 'Pemesanan Darah '.$namaRs;
        $model = new InfoPesanDarahPmiView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_pesandarahpmi'])) {
                $explode = explode(" - ", $advancedFilters['tgl_pesandarahpmi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
            if(isset($advancedFilters['no_pesandarahpmi'])) {
                $query->andWhere(['ILIKE', 'no_pesandarahpmi', $advancedFilters['no_pesandarahpmi']]);
            }
            if(isset($advancedFilters['supplier_nama'])) {
                $query->andWhere(['ILIKE', 'supplier_nama', $advancedFilters['supplier_nama']]);
            }
        }

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $pegawai = PegawaiView::find()->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])->one();

        $query->andWhere(['between', 'tgl_pesandarahpmi', $start, $end]);
        $query->orderBy($get['order']);
        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#title#' => $title,
            '#pegawai#' => isset($pegawai) ? $pegawai->nama_pegawai : "",
            '#jabatan#' => isset($pegawai) ? $pegawai->jabatan_nama : "",
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];
        $print->Output();
    }
}