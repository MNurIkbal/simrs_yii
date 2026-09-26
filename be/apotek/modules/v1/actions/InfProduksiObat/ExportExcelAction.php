<?php

namespace app\modules\v1\actions\InfProduksiObat;

use app\modules\v1\models\InfoPemesananProduksiObatView;
use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\ReturResep;
use SirsCore\models\ReturResepDetail;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\Pegawai;
use Doco\models\LoginForm;
use yii\helpers\ArrayHelper;

class ExportExcelAction extends Action {
    public function run()
    {
        try {
            $result = [];
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $query = InfoPemesananProduksiObatView::find();
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglpemesanan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                    if (count($explode) == 2) {
                        $date_start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $date_end = date('Y-m-d 23:59:00', strtotime($explode[1]));

                        $query->andWhere(['between', 'tglpemesanan', $date_start, $date_end]);
                    }
                    unset($_GET['advanced-filter']['tglpemesanan']);
                }

                if(isset($_GET['advanced-filter']['nopemesanan'])){
                    $query->andWhere(['nopemesanan'=>$_GET['advanced-filter']['nopemesanan']]);
                    unset($_GET['advanced-filter']['nopemesanan']);
                }
                if(isset($_GET['advanced-filter']['status_pemesanan'])){
                    $query->andWhere(['status_pemesanan'=>$_GET['advanced-filter']['status_pemesanan']]);
                    unset($_GET['advanced-filter']['status_pemesanan']);
                }
            } else {
                $query->andWhere(['between', 'tglpemesanan', $start, $end]);
            }

            $query->orderby(['tglpemesanan' => SORT_ASC]);
            $no = 0;

            foreach ($query->asArray()->all() as $value) {
                $no++;
                $data['Tanggal Pemesanan'] = $value['tglpemesanan'];
                $data['No Pemesanan'] = $value['nopemesanan'];
                $data['Status Pemesanan'] = $value['status_pemesanan'];
                $data['Tanggal Approve'] = $value['tgl_aprove'];
                $data['No Produksi'] = "-";
                $data['Pegawai Pemesan'] = $value['pegawai_pemesanan'];
                $data['Obat'] = $value['obatalkes_nama'];
                $data['Catatan'] = $value['catatan_bahanbaku'];
                $result[] = $data;
            }

            $header = [];
            $filePath = DocoHelpers::exportExcel('Laporan Pemesanan Produksi Obat', $result, $header, [], [], [], true);

            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}