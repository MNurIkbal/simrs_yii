<?php

/**
 * @author : ilham ()l4a4
 * Powered by Sirs
 */

namespace Extensions\gudang;

use Yii;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\modules\v1\models\LaporanPemakaianBarangView;

class ExcelGudangUmumKramat extends \Doco\processes\CetakInvoiceProcess
{

    protected $_title = 'Laporan Pemakaian Barang';
    protected function processFlow()
    {
   
    	$model = new LaporanPemakaianBarangView;
        try {
            $query = $model::find();
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $barang = '-';

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pemakaianbarang'])) {
                    $date = explode(' - ', $_GET['advanced-filter']['tgl_pemakaianbarang']);  
                    if(count($date) == 2) {
                        $startDate = explode('-', $date[0]);
                        $start = $startDate[2].'-'.date('m', strtotime($startDate[1])).'-'.$startDate[0];
                        $endDate = explode('-', $date[1]);
                        $end = $endDate[2].'-'.date('m', strtotime($endDate[1])).'-'.$endDate[0];
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
            $aa = [];


            foreach($query->asArray()->all() as $item) {
                if(!isset($data[$item['no_pemakaianbarang']])) {
                    $data[$item['no_pemakaianbarang']] = [];
                }
                $data[$item['no_pemakaianbarang']][] = [
                    'Tanggal Pemakaian Barang'  => date('d M Y', strtotime($item['tgl_pemakaianbarang'])),
                    'Nomor Pemakaian'           => $item['no_pemakaianbarang'],
                    'Nama Barang'               => $item['barang_nama'],
                    'Qty'                       => $item['jumlah_pakai'],
                    'Satuan Kecil'              => $item['satuan_kecil'],
                    'Keterangan'                => $item['keteranganpakai'],
                    'pegawai'                => $item['nama_pegawai'],
                ];
            }

            $result = [];
            $x = [];

            foreach ($data as $item) {
               $no = 1;

                $header1 = [];
                $header2 = [];
               foreach($item as $key){
                $key['Tanggal Pemakaian Barang'] = $no;
                   if(!$header1){
                    $header1 = [
                        'a' => 'Nomor Pemakaian',
                        'b' => isset($key['Nomor Pemakaian']) ? $key['Nomor Pemakaian'] : '',
                        'c' => 'Nama Penginput',
                        'd' => isset($key['pegawai']) ? $key['pegawai'] : '',
                        'e' => 'Tanggal Pemakaian',
                        'f' => isset($key['Tanggal Pemakaian Barang']) ? $key['Tanggal Pemakaian Barang'] : '',
                        'g' => '',
                    ];
                    $x[] = $header1;
                    $header2 = [
                        'a'   => 'No',
                        'b'   => 'Nama Barang',
                        'c'   => 'Qty',
                        'd'   => 'Nama Satuan Besar',
                        'e'   => 'Qty',
                        'f'   => 'Nama Satuan Kecil',
                        'g'   => 'Keterangan',
                    ];
                    $x[] = $header2;
                   }
                   $no++;
                  $x[] = $key;
               }
               unset($arr);
               $result = array_merge($result,$x);
            }
            // Directory Creation
            $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>1,
                    ],
                    [
                        'label'=>'Nama Barang',
                        'rowspan'=>1,
                    ],
                    [
                        'label'=>'Qty',
                        'rowspan'=>1,
                    ],
                    [
                        'label'=>'Nama Satuan Besar',
                        'rowspan'=>1,
                    ],
                    [
                        'label'=>'Qty',
                        'rowspan'=>1,
                    ],
                    [
                        'label'=>'Nama Satuan Kecil',
                        'rowspan'=>1,
                    ],
                    [
                        'label'=>'Keterangan',
                        'rowspan'=>1,
                    ],

                ]
            ];
            $header = array(
                Yii::t('app', "Tanggal pemakaian barang") => ((date('d M Y', strtotime($start))." - ".date('d M Y', strtotime($end)))),
            );

            $filePath = DocoHelpers::exportExcel($this->_title, $x, $header, array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ),[],[],true);

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