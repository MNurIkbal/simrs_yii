<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LapPenerimaanKasirView;
use app\modules\v1\models\Bank;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;

class LapPenerimaanKasirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LapPenerimaanKasirView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
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
        $model = new LapPenerimaanKasirView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tanggal'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal']); // Unset Advanced Filter  date range
            }

            if(isset($_GET['advanced-filter']['transaksi'])) {
                $transaksi = $_GET['advanced-filter']['transaksi'];
                $query->where(['transaksi' => $transaksi]);
                unset($_GET['advanced-filter']['transaksi']);
            }

            if(isset($_GET['advanced-filter']['nama_bank'])) {
                $bank_id = $_GET['advanced-filter']['nama_bank'];
                $query->andWhere(['laporanpenerimaankasir_v.bank_id' => $bank_id]);
                unset($_GET['advanced-filter']['nama_bank']);
            }
            if(isset($_GET['advanced-filter']['instalasi_nama'])) {
                $instalasi_id = $_GET['advanced-filter']['instalasi_nama'];
                $query->andWhere(['laporanpenerimaankasir_v.instalasi_id' => $instalasi_id]);
                unset($_GET['advanced-filter']['instalasi_nama']);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_nama'];
                $query->andWhere(['laporanpenerimaankasir_v.ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }
        
        $query->andWhere(['between', 'tanggal', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        try {
            $model = new LapPenerimaanKasirView;
            $query = $model::find();
            $periode = date('Y-m-d');
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $namaKasir = $transaksi = $namaBank = $instalasi_nama = $ruangan_nama = '-';
            if(isset($_GET['advanced-filter'])) {
                $filter = $request->get('advanced-filter');
                if(isset($_GET['advanced-filter']['tanggal'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if(isset($_GET['advanced-filter']['kasir'])) {
                    $namaKasir = $filter['kasir'];
                    $query->andWhere(['ILIKE', 'kasir', $namaKasir]);
                }

                if(isset($_GET['advanced-filter']['transaksi'])) {
                    $transaksi = $filter['transaksi'];
                    $query->andWhere(['transaksi' => $transaksi]);
                }

                if(isset($_GET['advanced-filter']['nama_bank'])) {
                    $bank_id = $filter['nama_bank'];
                    $dataBank = $this->getBankData($bank_id);
                    $namaBank = ($dataBank) ? $dataBank->nama_bank : '-';                    
                    $query->andWhere(['laporanpenerimaankasir_v.bank_id' => $bank_id]);
                }
                if(isset($_GET['advanced-filter']['instalasi_nama'])) {
                    $instalasi_id = $filter['instalasi_nama'];
                    $dataInstalasi = Instalasi::findOne($instalasi_id);
                    $instalasi_nama = isset($dataInstalasi['instalasi_nama']) ? $dataInstalasi['instalasi_nama'] : '-';                  
                    $query->andWhere(['laporanpenerimaankasir_v.instalasi_id' => $instalasi_id]);
                }
                if(isset($_GET['advanced-filter']['ruangan_nama'])) {
                    $ruangan_id = $filter['ruangan_nama'];
                    $dataRuangan = Ruangan::findOne($ruangan_id);
                    $ruangan_nama = isset($dataRuangan['ruangan_nama']) ? $dataRuangan['ruangan_nama'] : '-';                  
                    $query->andWhere(['laporanpenerimaankasir_v.ruangan_id' => $ruangan_id]);
                }
            }

            $query->andWhere(['between', 'tanggal', $start, $end]);

            $query->orderby([
                'transaksi' => SORT_ASC,
                'kasir' => SORT_ASC,
                'no_kwitansi' => SORT_ASC,
            ]);
            $data = $query->asArray()->all();
            $data = $this->group_by("transaksi", $data);
            $result = [];
            $periode = '' . date('j M Y', strtotime($start)) . ' - ' . date('j M Y', strtotime($end));

            foreach ($data as $key => $val) {
                foreach ($val as $value) {
                    if(array_key_exists("kasir", $value)) {
                        $result[$key][$value["kasir"]][] = $value;
                    }
                    else {
                        $result[""][] = $value;
                    }
                }
            }
            
            $title = "Penerimaan Kasir";
            $row = [];
            $urutData = 0;
            $mergeHeader = [];
            $dataDetail = [];
            foreach ($result as $jenisTrans => $trans) {
                $newRow = [
                    'Tanggal' => $jenisTrans,
                    'No Kwitansi' => null,
                    'No Registrasi' => null,
                    'Info Pasien' => null,
                    'Rupiah' => null,
                    'Keterangan' => null,
                    'Cara Bayar' => null,
                    'Penjamin' => null,
                    'Kelas Pelayanan' => null,
                    'Deskripsi' => null,
                ];

                $row[] = $newRow;
                $mergeHeader[$urutData] = $jenisTrans;
                
                //group by kasir
                foreach ($trans as $kasir => $detailKasir) {
                    $newRow = [
                        'Tanggal' => $kasir,
                        'No Kwitansi' => null,
                        'No Registrasi' => null,
                        'Info Pasien' => null,
                        'Rupiah' => null,
                        'Keterangan' => null,
                        'Cara Bayar' => null,
                        'Penjamin' => null,
                        'Kelas Pelayanan' => null,
                        'Deskripsi' => null,
                    ];
                    $row[] = $newRow;
                    $urutData++;
                    $subTotal = 0;
                    
                    //detail data
                    foreach ($detailKasir as $detail) {
                        $subTotal += $detail['rupiah'];
                        $newRow = [
                            'Tanggal' => $detail['tanggal'],
                            'No Kwitansi' => $detail['no_kwitansi'],
                            'No Registrasi' => $detail['no_registrasi'],
                            'Info Pasien' => $detail['info_pasien'],
                            'Rupiah' => $detail['rupiah'],
                            'Keterangan' => $detail['keterangan'],
                            'Cara Bayar' => $detail['cara_bayar'],
                            'Penjamin' => $detail['penjamin'],
                            'Kelas Pelayanan' => isset($detail['kelaspelayanan_nama']) ? $detail['kelaspelayanan_nama'] : null,
                            'Deskripsi' => $detail['deskripsi'],
                        ];
                        $row[] = $newRow;
                        $urutData++;
                    }
                    $urutData++;
                    $row[] = [
                        'Tanggal' => null,
                        'No Kwitansi' => null,
                        'No Registrasi' => null,
                        'Info Pasien' => 'TOTAL',
                        'Rupiah' => $subTotal
                    ];
                }
            }

            $footer = [];
            $header = [
                'Tanggal' => $periode,
                'Kasir' => $namaKasir,
                'Metode Pembayaran' => $transaksi,
                'Bank' => $namaBank,
                'Instalasi' => $instalasi_nama,
                'Ruangan' => $ruangan_nama,
            ];
            $filePath = DocoHelpers::exportExcel($title, $row, $header,  array(
                "uploadPath" => "./uploads",
                "skipIncrement" => true,
                "mergeCells" => true,
                "mergeHeader" => $mergeHeader
            ),$footer,[],true);
            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function group_by($key, $data)
    {
        $result = array();

        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }

    private function getBankData($id)
    {
        $model = Bank::findOne($id);

        return $model;
    }

}