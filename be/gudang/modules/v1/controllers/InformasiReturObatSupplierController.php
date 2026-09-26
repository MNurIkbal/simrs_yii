<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\InfoReturPenerimaanObatDetail;
use app\modules\v1\models\InfoReturPenerimaanObat;

class InformasiReturObatSupplierController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoReturPenerimaanObatDetail';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $query = $this->getData();
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

    private function getData()
    {
        $request = Yii::$app->request;
        $model = new InfoReturPenerimaanObatDetail;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_retur'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_retur']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_retur']);
            }
        }
        $_GET['listen_tanggal'] = date('d M Y',strtotime($start)) . ' s/d ' . date('d M Y',strtotime($end));
        $query->andWhere(['between', 'tgl_retur', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    /**
    * @controller actionCetakTransaksi
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail retur
    * @attribute #pegawai_retur# => Untuk Menampilkan nama pegawai retur
    * @attribute #no_retur# => Untuk Menampilkan nomor retur
    * @attribute #tgl_retur# => Untuk Menampilkan tanggal retur
    * @attribute #nama_supplier# => Untuk Menampilkan nama supplier
    * @attribute #nama_petugas# => Untuk Menampilkan nama petugas
    * @attribute #alasan# => Untuk Menampilkan nama petugas
    **/

    public function actionCetakTransaksi($id)
    {
        $header = InfoReturPenerimaanObat::find()->where([
            'returpenerimaanobat_id' => $id
        ])->one();
        $detail = InfoReturPenerimaanObatDetail::find()->where([
            'returpenerimaanobat_id' => $id
        ])->orderBy([
            'obatalkes_nama' => SORT_ASC
        ])->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#pegawai_retur#' => !empty($header->pegawai_retur) ? $header->pegawai_retur : null,
            '#no_retur#' => !empty($header->no_returpenerimaanobat) ? $header->no_returpenerimaanobat : null,
            '#tgl_retur#' => !empty($header->tgl_retur) ? date('d M Y',strtotime($header->tgl_retur)) : null,
            '#nama_supplier#' => !empty($detail[0]->supplier_nama) ? $detail[0]->supplier_nama : null,
            '#nama_petugas#' => !empty($header->pegawai_retur) ? $header->pegawai_retur : null,
            '#alasan#' => !empty($header->alasan_retur) ? $header->alasan_retur : null,
            '#tabel_detail#' => $this->renderPartial('detail', [
                'query' => $detail
            ]),
        ];
        $print->Output();
    }

    /**
    * @controller actionExportPdf
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail retur
    * @attribute #kepala_ruangan# => Untuk Menampilkan nama pegawai ruangan
    **/

    public function actionExportPdf()
    {
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $data = $this->getData()->all();
        $print = new DocoPrint();
        $pegawai = PegawaiView::find()
            ->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])
            ->one();
        $print->attributes = [
            '#kepala_ruangan#' => !empty($pegawai->nama_pegawai) ? $pegawai->nama_pegawai : null,
            '#periode#' => !empty($_GET['listen_tanggal']) ? $_GET['listen_tanggal'] : null,
            '#tabel_detail#' => $this->renderPartial('index', [
                'query' => $data
            ]),
        ];

        $print->Output();
    }

    public function actionExportExcel()
    {
        $data = $this->getData()->all();
        $namaObat = null;
        $data_baru = [];
        $stok_awal = null;
        foreach ($data as $key => $value) {
            $data_baru[] = [
                'Tanggal Retur' => date('d M Y',strtotime($value['tgl_retur'])),
                'No Retur' => $value['no_returpenerimaanobat'],
                'No Penerimaan' => $value['no_penerimaan'],
                'No Faktur' => $value['no_faktur'],
                'Supplier' => $value['supplier_nama'],
                'Nama Obat' => $value['obatalkes_nama'],
                'Qty Retur' => $value['qty_input'] .' '. $value['satuanunit_nama'],
                'Alasan Retur' => $value['alasan_retur']
            ];
        }

        $header = [
            'Tanggal Retur' => !empty($_GET['listen_tanggal']) ? $_GET['listen_tanggal'] : null,
        ];

        $filePath = DocoHelpers::exportExcel("Informasi Retur Obat Supplier", $data_baru, $header, [],null,null,true);

        $filePath->save('php://output');
        die;
    }

}