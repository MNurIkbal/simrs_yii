<?php
namespace app\modules\v1\actions\InfKartuStok;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\components\ApotekComponent;
use app\modules\v1\models\KartuStokObatFn;
use app\modules\v1\models\ObatAlkes;

class ExcelRecalculateAction extends Action
{
    public function run()
    {
        $data = [];
        $model = $this->controller->getData();
        $range_tanggal = ArrayHelper::getValue(Yii::$app->request->get(),'advanced-filter.tanggal_transaksi');
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        if(!is_null($range_tanggal)){
            $range_explode = explode(' - ', $range_tanggal);
            if (count($range_explode) == 2) {
                $start_date = date('Y-m-d',strtotime($range_explode[0]));
                $end_date = date('Y-m-d',strtotime($range_explode[1]));
            }
        }

        $data = $model::find()->asArray()->all();
        $namaObat = null;
        $data_baru = [];
        $stok_awal = null;
        foreach ($data as $key => $value) {
            $namaObat = $value['obatalkes_nama'];
            if ($key == 0) {
                $stok_awal = $value['total'];
                $stok_out = $value['qtystok_out'];
                $stok_in = $value['qtystok_in'];
                $stok_awal += $stok_out - $stok_in;
            }
            $data_baru[] = [
                'Tanggal Transaksi' => date('d-M-Y',strtotime($value['tanggal_transaksi'])),
                'Kode Obat/Alkes' => $value['obatalkes_kode'],
                'Nama Obat/Alkes' => $namaObat,
                'Tanggal Kadaluarsa' => date('d-M-Y',strtotime($value['tglkadaluarsa'])),
                'No Transaksi' => $value['no_transaksi'],
                'Keterangan' => $value['keterangan'],
                'Reference' => $value['reference'],
                'Ruangan Asal' => $value['ruangan_asal_nama'],
                'Ruangan Tujuan' => $value['ruangan_tujuan_nama'],
                'Qty Masuk' => $value['qtystok_in'],
                'Qty Keluar' => $value['qtystok_out'],
                'Stok' => $value['total'],
                'Satuan' => $value['satuanunit_nama']
            ];
        }

        $header = [
            'Tanggal Transaksi' => date('d-M-Y',strtotime($start_date)) . ' s/d ' . date('d-M-Y',strtotime($end_date)),
            'Nama Obat/Alkes' => $namaObat,
            'Stok Awal' => $stok_awal
        ];

        $options = [
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'date',
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'I',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'J',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'K',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'L',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'M',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'N',
                    'formatCode' => 'general',
                ]
            ]
        ];

        $filePath = DocoHelpers::exportExcel("Informasi Kartu Stok", $data_baru, $header, $options,null,null,true);

        $filePath->save('php://output');
        die;
    }
}