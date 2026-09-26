<?php

namespace app\components\rabbitmq\laporan;

use Doco\rabbitmq\task\ReportTask;
use app\modules\v1\models\LaporanPenjualanObatalkesView;
use Doco\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoSpout;
use Doco\components\DocoConstants;

class LaporanPenjualanResepTask extends ReportTask
{
    protected $header;
    protected $headerExcel;
    protected $customData;
    protected $footer;
    protected $title;
    protected $totalPerPage;
    protected $countData;

    protected $list_data = [];

    /** proses get Data */
    protected function prosesGetData()
    {
        sleep(1);
        $this->list_data = $this->getDataAttibutes();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
    }

    /** proses export */
    protected function prosesExport()
    {

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang menyiapkan file excel.',
                'progress' => 80
            ]),
        ]);

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel.',
                'progress' => 85
            ]),
        ]);

        $path = 'web/'.'uploads/'. $this->unique_str .'.csv';
        $options = [
            "skipIncrement" => true,
            "customHeader" => $this->custHeader(),
            "uploadPath"=>'./web/uploads/',
            "fileName"=> $this->unique_str
        ];

        $filePath = DocoSpout::exportCsv($this->title, $this->list_data, $this->headerExcel, $options, [], [], true);
        // $filePath->save($path);
        // $this->generateExcel();

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil.',
                'progress' => 90
            ]),
        ]);

        // if (file_exists($path)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-excel:'.$this->unique_str,
                'message' => json_encode([
                     'status' => 'finish', 
                     'messageProcess' => 'Proses berhasil.',
                     'progress' => 100,
                     'filename' => $this->unique_str
                 ]),
            ]);
        // }
    }

    private function getDataAttibutes()
    {
        try {
            $data =  $this->getDataLaporanExcel();
            $result = [];
            $no = 1;
            foreach($data as $key => $value) {
                $newData['No'] = $no++;
                $newData['Tanggal Transaksi'] = ArrayHelper::getValue($value, 'tgltransaksi');
                $newData['No Pendaftaran'] = ArrayHelper::getValue($value, 'no_pendaftaran');
                $newData['Jenis Penjualan'] = ArrayHelper::getValue($value, 'jenis_resep');
                $newData['Ruangan'] = ArrayHelper::getValue($value, 'ruangan_nama');
                $newData['No Resep'] = ArrayHelper::getValue($value, 'noresep');
                $newData['Nama Dokter'] = ArrayHelper::getValue($value, 'nama_dokter');
                $newData['No Rekam Medik'] = ArrayHelper::getValue($value, 'no_rekammedik');
                $newData['Nama Pasien'] = ArrayHelper::getValue($value, 'nama_pasien');
                $newData['Tanggal Lahir'] = ArrayHelper::getValue($value, 'tanggal_lahir');
                $newData['Nomor Hp'] = $this->getNomorHpAttribute($value);
                $newData['Alamat Pembeli'] = $this->getAlamatPembeliAttribute($value);
                $newData['R-Ke'] = ArrayHelper::getValue($value, 'rke');
                $newData['Kode Obat'] = ArrayHelper::getValue($value, 'kode_obat');
                $newData['Nama Obat'] = ArrayHelper::getValue($value, 'nama_obat');
                $newData['Jenis Obat'] = ArrayHelper::getValue($value, 'jenisobatalkes_nama');
                $newData['Jumlah Obat'] = ArrayHelper::getValue($value, 'jumlah_obat');
                $newData['Satuan'] = ArrayHelper::getValue($value, 'satuan');
                $newData['Total Tagihan (Rp.)'] = ArrayHelper::getValue($value, 'totaltagihan');
                $newData['Status'] = ArrayHelper::getValue($value, 'status_reseptur_nama');
                $newData['Cara Bayar'] = ArrayHelper::getValue($value, 'carabayar_nama');
                $newData['Penjamin'] = ArrayHelper::getValue($value, 'penjamin_nama');
                $newData['Formularium'] = ArrayHelper::getValue($value, 'is_formularium')?'Ya':'Tidak';
                $newData['Psikotropika'] = ArrayHelper::getValue($value, 'is_psycothropica')?'Ya':'Tidak';
                $newData['Narkotika'] = ArrayHelper::getValue($value, 'is_narcotic')?'Ya':'Tidak';
                $newData['Supplier'] = ArrayHelper::getValue($value, 'supplier');
                $newData['Principle'] = ArrayHelper::getValue($value, 'principle');
                $newData['User'] = ArrayHelper::getValue($value, 'user');
                $result[] = $newData;
            }
            return $result;
            
		} catch (\Exception $e) {
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
		}
    }

    public function getDataLaporanExcel()
    {
        $advanced_filter = ArrayHelper::getValue($this->filter, 'advanced-filter', []);
        $model = new LaporanPenjualanObatalkesView;
        $query = $model::find()->select([
            'tgltransaksi' ,'no_pendaftaran' ,'jenispenjualan' ,'ruangan_nama' ,'noresep' ,'nama_pasien' ,'tanggal_lahir', 'no_telepon_pembeli', 'no_mobile_pembeli', 'alamat_pembeli' ,'rke' ,'kode_obat' ,'nama_obat' ,
            'jenisobatalkes_nama' ,'jumlah_obat' ,'satuan' ,'totaltagihan' ,'status_reseptur_nama' ,'nama_pasien' ,'penjamin_nama' ,'is_formularium' ,'is_psycothropica' ,'is_narcotic' ,'supplier' ,'principle' ,'user','nama_dokter','jenis_resep','carabayar_nama','no_rekammedik'
        ]);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $tanggal_transaksi = ArrayHelper::getValue($advanced_filter, 'tgltransaksi');
        $jenis_penjualan = ArrayHelper::getValue($advanced_filter, 'jenispenjualan');
        $jenis_obat = ArrayHelper::getValue($advanced_filter, 'jenisobatalkes_nama');
        $cara_bayar = ArrayHelper::getValue($advanced_filter, 'carabayar_nama');
        $penjamin =ArrayHelper::getValue($advanced_filter, 'penjamin_nama');
        $no_rekammedik =ArrayHelper::getValue($advanced_filter, 'no_rekammedik');
        $nama_pasien =ArrayHelper::getValue($advanced_filter, 'nama_pasien');
        $nama_obat =ArrayHelper::getValue($advanced_filter, 'nama_obat');
        $jenis_resep =ArrayHelper::getValue($advanced_filter, 'jenis_resep');
        $is_psycothropica =ArrayHelper::getValue($advanced_filter, 'is_psycothropica');
        $nama_dokter =ArrayHelper::getValue($advanced_filter, 'nama_dokter');
        $ruangan_nama =ArrayHelper::getValue($advanced_filter, 'ruangan_nama');
        $is_narcotic =ArrayHelper::getValue($advanced_filter, 'is_narcotic');
        $noresep = ArrayHelper::getValue($advanced_filter, 'noresep');
        $tanggal_lahir = ArrayHelper::getValue($advanced_filter, 'tanggal_lahir');
        $status_reseptur = ArrayHelper::getValue($advanced_filter, 'status_reseptur');
       if($tanggal_transaksi){
            $explode = explode(" - ", $tanggal_transaksi);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }
       if($jenis_penjualan){
            $query->andWhere(['jenispenjualan' => $jenispenjualan]);
        }
       if($is_psycothropica){
            $query->andWhere(['is_psycothropica' => $is_psycothropica]);
        }
       if($is_narcotic){
            $query->andWhere(['is_narcotic' => $is_narcotic]);
        }
       if($jenis_obat){
            $query->andWhere(['ILIKE', 'jenisobatalkes_nama', $jenis_obat]);
        }
       if($cara_bayar){
            $query->andWhere(['=', 'carabayar_id', $cara_bayar]);
        }
       if($penjamin){
            $query->andWhere(['=', 'penjamin_id', $penjamin]);
        }
        if($status_reseptur){
            $query->andWhere(['=', 'status_reseptur', $status_reseptur]);
        }
       if($no_rekammedik){
            $query->andWhere(['ILIKE', 'no_rekammedik', $no_rekammedik]);
        }
       if($ruangan_nama){
            $query->andWhere(['ILIKE', 'ruangan_nama', $ruangan_nama]);
        }
       if($nama_dokter){
            $query->andWhere(['ILIKE', 'nama_dokter', $nama_dokter]);
        }
       if($jenis_resep){
            $query->andWhere(['ILIKE', 'jenis_resep', $jenis_resep]);
        }
       if($nama_pasien){
            $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
        }
       if($nama_obat){
            $query->andWhere(['ILIKE', 'nama_obat', $nama_obat]);
        }
       if($noresep){
            $query->andWhere(['ILIKE', 'noresep', $noresep]);
        }
       if($tanggal_lahir){
            $tanggalMew = date('Y-m-d', strtotime($tanggal_lahir));
            $query->andWhere(['=', 'tanggal_lahir', $tanggalMew]);
        }
        $query->andWhere(['between', new \yii\db\Expression('(tgltransaksi::date)'), $start, $end]);
        $data = $query->asArray()->all();
        
        return $data;
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
                    'label'=>'Tanggal Transaksi',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Pendaftaran',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Penjualan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Ruangan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Resep',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Dokter',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'No Rekam Medik',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Pasien',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Tanggal Lahir',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nomor HP',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Alamat Pembeli',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'R-Ke',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Kode Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Nama Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jenis Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Jumlah Obat',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Satuan',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Total Tagihan (Rp.)',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Status',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Cara Bayar',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Penjamin',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Formularium',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Psikotropika',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Narkotika',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Supplier',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'Principle',
                    'rowspan'=>2,
                ],
                [
                    'label'=>'User',
                    'rowspan'=>2,
                ],
                
            ]
        ];
    }

    private function generateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:W1');
        $sheet->setCellValue("A1", $this->title);
        $row = 2;
        foreach ($this->headerExcel as $key => $val) {
            $sheet->mergeCells("A{$row}:W{$row}");
            $sheet->setCellValue("A{$row}", $key . " : {$val}");
            $row++;
        }

        $styleTitle = array(
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        $sheet->getStyle('A1:W1')->applyFromArray($styleTitle);

        if (isset($this->list_data[0])) {
            $headers = array_keys($this->list_data[0]); // get headers from source array
            foreach ($headers as $key => $str) {
                $headers[$key] = str_replace('_', ' ', $str);
            }
            array_unshift($this->list_data, array_map('ucwords', $headers)); 
        }

        $timer = microtime(true);
        $sheet->fromArray(
            $this->list_data,
            null,
            'A7'
        );
        
        $writer = new Xlsx($spreadsheet);
        $filePath = 'web/'.'uploads/'. $this->unique_str .'.xlsx';
        $writer->save($filePath);
        $foo = 'elapsed time: '. round(microtime(true) - $timer, 3). ' sec';
    }

    private function getNomorHpAttribute($value)
    {
        if (ArrayHelper::getValue($value, 'jenispenjualan') == DocoConstants::PENJUALAN_RESEP_BEBAS) {
            return 'Penjualan Bebas';
        } else {
            return (isset($value['no_telepon_pembeli']) && $value['no_telepon_pembeli'] != null ? $value['no_telepon_pembeli'] : ' - ').'/'.(isset($value['no_mobile_pembeli']) && $value['no_mobile_pembeli'] != null ? $value['no_mobile_pembeli'] : ' - ');
        }
    }

    private function getAlamatPembeliAttribute($value)
    {
        if (ArrayHelper::getValue($value, 'jenispenjualan') == DocoConstants::PENJUALAN_RESEP_BEBAS) {
            return 'Penjualan Bebas';
        } else {
            return ArrayHelper::getValue($value, 'alamat_pembeli', '');
        }
    }
}