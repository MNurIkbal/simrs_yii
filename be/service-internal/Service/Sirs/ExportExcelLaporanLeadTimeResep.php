<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\LaporanLeadTimeResepView;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Components\DocoRestActiveFilter;

class ExportExcelLaporanLeadTimeResep extends \Integrasi\Contracts\DocoImplement {
	public function execute() {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = $footer = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Laporan Lead Time Resep',
                'progress' => 80
            ]),
        ]);

        for ($x = 0; $x < $totalPerPage; $x++) {
            $data = $cacheFiles->get($this->unique_str .'-'. $x);
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $row[] = $value;
                }
            }
            $cacheFiles->delete($this->unique_str .'-'. $x);
        }
        
        $model = new LaporanLeadTimeResepView;
        $query = $model::find();
        
        $ruangan = "";
        $start = date('d M Y');
        $end = date('d M Y');
        if(isset($filter['advance_filter'])) {
            if(isset($filter['advance_filter']['tgl_resep']) && $filter['advance_filter']['tgl_resep'] != '') {
                $explode = explode(" - ", $filter['advance_filter']['tgl_resep']);
                if(count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
            }

            if(isset($filter['advance_filter']['ruangan_id']) && $filter['advance_filter']['ruangan_id'] != '') {
                $ruangan = Ruangan::find()->select(['ruangan_nama'])->where(['ruangan_id' => $filter['advance_filter']['ruangan_id']])->asArray()->one();
                $ruangan = $ruangan['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $filter['advance_filter']['ruangan_id']]);
            }
        }
        $query->andWhere(['between', 'tgl_resep', $start, $end]);
        
        $header = array(
            'Tanggal Resep' => $start . ' s/d ' . $end,
            'Ruangan' => $ruangan
        );

        $custHeader = $this->custHeader();
        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        $filePath = DocoHelpers::exportExcel('Laporan Lead Time Resep', $row, $header, [
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'C', 'formatCode' => 'shortdate'],
                ['selectColumn' => 'F', 'formatCode' => 'number'],
                ['selectColumn' => 'H', 'formatCode' => 'number'],
                ['selectColumn' => 'I', 'formatCode' => 'datetime'],
                ['selectColumn' => 'J', 'formatCode' => 'datetime'],
                ['selectColumn' => 'K', 'formatCode' => 'datetime'],
                ['selectColumn' => 'L', 'formatCode' => 'datetime']
            ],
        ], $footer, [], true);
        $filePath->save($path);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-ExportExcelLaporanLeadTimeResep',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader()  {
        return [
            [
                [ 'label' => 'No', 'rowspan' => 2],
                [ 'label' => 'Ruangan', 'rowspan' => 2 ],
                [ 'label' => 'Tanggal Resep', 'rowspan' => 2 ],
                [ 'label' => 'No. Resep', 'rowspan' => 2 ],
                [ 'label' => 'Jenis Resep', 'rowspan' => 2 ],
                [ 'label' => 'Jumlah R/', 'rowspan' => 2 ],
                [ 'label' => 'Dokter', 'rowspan' => 2 ],
                [ 'label' => 'Jumlah Item', 'rowspan' => 2 ],
                [ 'label' => 'Jam Resep Masuk', 'rowspan' => 2 ],
                [ 'label' => 'Jam Resep Dibayarkan', 'rowspan' => 2 ],
                [ 'label' => 'Jam Production', 'rowspan' => 2 ],
                [ 'label' => 'Jam Resep Siap Diserahkan', 'rowspan' => 2 ],
                [ 'label' => 'Waktu Tunggu Obat', 'rowspan' => 2 ]
            ]
        ];
    }
}