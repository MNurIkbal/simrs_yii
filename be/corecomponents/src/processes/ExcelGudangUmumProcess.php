<?php

/**
 * @author : ilham ()
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\modules\v1\models\LaporanPemakaianBarangView;

class ExcelGudangUmumProcess extends \Doco\components\DocoBaseProcessExtension
{

    protected $_title = 'Laporan Pemakaian Barang';

    protected function processFlow()
    {
        $model = new LaporanPemakaianBarangView;
        try {
            $query = $model::find(true);
            $between = true;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $barang = '-';

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pemakaianbarang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pemakaianbarang']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pemakaianbarang']); // Unset Advanced Filter  date range
                    $between = true;
                }

                if(isset($_GET['advanced-filter']['barang_nama'])) {
                    $barang = $_GET['advanced-filter']['barang_nama'];
                }
            }
            
            $query->andWhere(['between', 'tgl_pemakaianbarang', $start, $end]);    
            $query->orderBy(['tgl_pemakaianbarang' => SORT_ASC]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // data export
            $data = [];

            foreach($query->asArray()->all() as $item) {
                $data[] = [
                    'Tanggal Pemakaian Barang'  => date('d M Y', strtotime($item['tgl_pemakaianbarang'])),
                    'Nomor Pemakaian'           => $item['no_pemakaianbarang'],
                    'Nama Barang'               => $item['barang_nama'],
                    'Qty'                       => $item['jumlah_pakai'],
                    'Satuan Kecil'              => $item['satuan_kecil'],
                    'Keterangan'                => $item['keteranganpakai']
                ];
            }

            $result = $data;

            // Directory Creation
            $header = array(
                Yii::t('app', "Tanggal pemakaian barang") => ((date('d M Y', strtotime($start))." - ".date('d M Y', strtotime($end)))),
                Yii::t('app', "Nama Barang") => $barang
            );
            
            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
