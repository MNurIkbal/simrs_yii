<?php

namespace app\modules\v1\actions\InfProduksiObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\models\LoginForm;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoProduksiObatAlkesView;

class ExportExcelProduksiAction extends Action {
    public function run()
    {
        try {
            $result = [];
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $query = InfoProduksiObatAlkesView::find();
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglpemesanan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglpemesanan']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($_GET['advanced-filter']['tglproduksiobat'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglproduksiobat']);
                    if (count($explode) == 2) {
                        $start_produksi = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end_produksi = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    $query->andWhere(['between', 'tglproduksiobat', $start_produksi, $end_produksi]);
                    unset($_GET['advanced-filter']['tglproduksiobat']);
                }
                if(isset($_GET['advanced-filter']['nopemesanan'])){
                    $query->andWhere(['nopemesanan'=>$_GET['advanced-filter']['nopemesanan']]);
                    unset($_GET['advanced-filter']['nopemesanan']);
                }
                if(isset($_GET['advanced-filter']['status_produksi'])){
                    $query->andWhere(['status_produksi'=>$_GET['advanced-filter']['status_produksi']]);
                    unset($_GET['advanced-filter']['status_produksi']);
                }
                if(isset($_GET['advanced-filter']['noproduksiobat'])){
                    $query->andWhere(['noproduksiobat'=>$_GET['advanced-filter']['noproduksiobat']]);
                    unset($_GET['advanced-filter']['noproduksiobat']);
                }
            }
            $query->andWhere(['between', 'tglpemesanan', $start, $end]);

            $query->orderby(['tglpemesanan' => SORT_ASC]);
            $no = 0;

            foreach ($query->asArray()->all() as $value) {
                $no++;
                $data['No Pemesanan'] = $value['nopemesanan'];
                $data['Pemesan'] = $value['pegawai_pemesanan'];
                $data['Status'] = $value['status_produksi'];
                $data['No Produksi'] = $value['noproduksiobat'];
                $data['Tanggal Produksi'] = $value['tglproduksiobat'];
                $data['Approval'] = $value['pegawai_approve'];
                $data['Tanggal Pemesanan'] = $value['tglpemesanan'];
                $result[] = $data;
            }

            $header = [];
            $filePath = DocoHelpers::exportExcel('Laporan Produksi Obat', $result, $header, [], [], [], true);

            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}