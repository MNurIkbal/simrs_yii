<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\Instalasi;
use Integrasi\Service\Sirs\Models\Ruangan;
use Integrasi\Service\Sirs\Models\LaporanHasilSoView;
use Integrasi\Components\DocoRestActiveFilter;
use Doco\Repositories\KonfigRepositories;

class ExportLaporanHasilStokOpname extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $row = [];
        $no = 1;
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Laporan Hasil Stok Opname',
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
        
        $konfig = KonfigRepositories::getKonfigFarmasi();
        $basePrice = $konfig['base_price_so'];

        $model = new LaporanHasilSoView;
        $start = date('d M Y 00:00:00');
        $end = date('d M Y 23:59:59');
        $query = $model::find()->select([
            new Expression("SUM(total_harga_selisi) AS  total_selisih"),
            new Expression("SUM(weighted_avg*stok_fisik) AS  total_fisik"),
            new Expression("SUM(total_harga_sistem) AS  total_sistem")
        ]);
        
        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_form_so']);
                if(count($explode) == 2) {
                    $start = date('d M Y 00:00:00', strtotime($explode[0]));
                    $end = date('d M Y 23:59:59', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_form_so']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($filter['advanced-filter']['no_form_so'])){
                $form = $filter['advanced-filter']['no_form_so'];
                $query->andWhere(['ILIKE', 'no_form_so', $form]);
            }

            if(isset($filter['advanced-filter']['instalasi_ruangan'])) {
                $ruangan_id = $filter['advanced-filter']['instalasi_ruangan'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }
        }
        
        $query->andWhere(['between', 'tgl_form_so', $start, $end]);
        $data = $query->asArray()->one();

        $totalSelisih = DocoHelpers::formatNumber($data['total_selisih']);
        $totalFisik = DocoHelpers::formatNumber($data['total_fisik']);

        $db = Yii::$app->db;
        $form = $instalasi_ruangan = '';
        if (isset($filter['advanced-filter'])) {
            if (isset($filter['advanced-filter']['tgl_form_so'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_form_so']);
                if (count($explode) == 2) {
                    $start = date('d M Y', strtotime($explode[0]));
                    $end = date('d M Y', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_form_so']);
            }

            if (isset($filter['advanced-filter']['no_form_so'])) {
                $form = $filter['advanced-filter']['no_form_so'];
                unset($filter['advanced-filter']['no_form_so']);
            }
            
            if(isset($filter['advanced-filter']['instalasi_ruangan'])){
                $instalasi = $filter['advanced-filter']['instalasi_ruangan'];
                $ruangan = Ruangan::find()->where(['ruangan_id' => $instalasi])->select(['ruangan_nama'])->one();
                $instalasi_ruangan = $ruangan['ruangan_nama'];
                unset($filter['advanced-filter']['instalasi_ruangan']);
            }
        }
        
        $header = array(
            'Tanggal Form SO' => $start . ' s/d ' . $end,
            'No.Form SO' => $form,
            'Instalasi - Ruangan' => $instalasi_ruangan,
        );

        $footer = [
            'title' => ['TOTAL HARGA (Rp.)', 14],
            'data' => [
                '16' => $data['total_sistem'],
                '17' => $data['total_fisik'],
                '18' => $data['total_selisih'],
            ]
        ];

        $custHeader = $this->custHeader();
        if($basePrice == 0){
            $custHeader[0] = array_merge(array_slice($custHeader[0], 0,11),array(array('label' => 'Weighted Average (Rp.)','rowspan' => 2)), array_slice($custHeader[0], 11));
        }else{
            $custHeader[0] = array_merge(array_slice($custHeader[0], 0,11),array(array('label' => 'HNA','rowspan' => 2)), array_slice($custHeader[0], 11));
        }

        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        $filePath = DocoHelpers::exportExcel('LAPORAN HASIL STOK OPNAME', $row, $header,[
            "skipIncrement" => true,
            'customHeader' => $custHeader,
            "customFormatCode" => [
                ['selectColumn' => 'B', 'formatCode' => 'datetime'],
                ['selectColumn' => 'C', 'formatCode' => 'datetime'],
                ['selectColumn' => 'D', 'formatCode' => 'datetime'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
                ['selectColumn' => 'L', 'formatCode' => 'number'],
                ['selectColumn' => 'M', 'formatCode' => 'number'],
                ['selectColumn' => 'N', 'formatCode' => 'number'],
                ['selectColumn' => 'O', 'formatCode' => 'number'],
                ['selectColumn' => 'P', 'formatCode' => 'number'],
                ['selectColumn' => 'Q', 'formatCode' => 'number'],
                ['selectColumn' => 'R', 'formatCode' => 'number']
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
            'service' => 'Sirs-ExportLaporanHasilStokOpname',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function custHeader() 
    {
        return [
            [
                [
                    'label'=>'No',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Form SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Validasi SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Implementasi SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Divalidasi oleh',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No. Form SO',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Instalasi - Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat Alkes',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Item',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Item',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan Kecil',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Sistem',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Stok Fisik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Selisih',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga Sistem (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga Fisik (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Harga Selisih (Rp.)',
                    'rowspan'=>2,
                ],
            ]
        ];
    }
}