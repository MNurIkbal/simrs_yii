<?php
namespace app\modules\v1\businessLogic;

use Yii;
use yii\db\Expression;
use app\modules\v1\models\InpostOperasiDetail;
use app\modules\v1\models\InpostOperasi;
use app\modules\v1\models\TimOperasi;
use app\modules\v1\models\InfoPasienOperasiView;
use Doco\models\TarifTotalFn;
use Doco\components\DocoConstants;
use app\modules\v1\models\NewTimOperasiView;
use app\modules\v1\models\InfoInpostOperasiDetailView;

class TagihanBedah {
    public $tarifOperator;
    public $tarifTertinggi;
    public $tindakanOperator;
    public $tarifTindakan;
    
    const TIPE_TINDAKAN = 'tindakan';
    const TIPE_JASA = 'jasa';

    public function __construct()
    {
        $this->tarifOperator = [];
        $this->tindakanOperator = [];
        $this->tarifTertinggi = [];
        $this->tarifTindakan = [];
    }
    public function getBills($pasienpenunjangId)
    {
        $rsType = Yii::$app->params['rs-type'];
        $data = [];
        $totalBills = $totalCyto = $totalPenyulit = 0;
        switch ($rsType) {
            case 'mhkn':
                $getBillsHeader = InfoPasienOperasiView::find()
                        ->select([
                            'ruangan_id',
                            'penjamin_id',
                            'kelaspelayanan_id',
                            'kamarruangan_id'
                        ])
                        ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
                        ->asArray()
                        ->one();

                $tarifAkomodasiRuangan = (new TarifTotalFn([
                    'extParam' => [
                        $getBillsHeader['ruangan_id'],
                        $getBillsHeader['penjamin_id'],
                        $getBillsHeader['kelaspelayanan_id'],
                        'kamar'
                    ]
                ]))
                ->find()
                ->select([
                    'daftartindakan_id',
                    'daftartindakan_nama',
                    'kamarruangan_nokamar',
                    'harga_tariftindakan',
                    'persencyto_tindakan'
                ])
                ->where([
                    'kamarruangan_id' => $getBillsHeader['kamarruangan_id']
                ])
                ->asArray()
                ->one();
                $getServices = TimOperasi::find()
                    ->select([
                        'timoperasi_t.timoperasi_id',
                        'timoperasi_t.inpostoperasi_id',
                        'timoperasi_t.posisi_tim',
                        'timoperasi_t.harga',
                        'timoperasi_t.persentase',
                        'tindakanoperasi_mp.daftartindakan_id',
                        'timoperasi_t.pegawai_id',
                        'daftartindakan_m.daftartindakan_nama',
                        "golonganoperasi_nama" => new Expression('NULL'),
                        'pegawai_m.nama_pegawai',
                        "operasi_nama" => new Expression('NULL'),
                        'fgetnamalookup(timoperasi_t.posisi_tim) as posisi_operasi'
                    ])
                    ->rightJoin('tindakanoperasi_mp', 'timoperasi_t.posisi_tim = tindakanoperasi_mp.timoperasi_id')
                    ->rightJoin('inpostoperasi_t', 'inpostoperasi_t.inpostoperasi_id = timoperasi_t.inpostoperasi_id')
                    ->rightJoin('pasienmasukpenunjang_t', 'inpostoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
                    ->rightJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = tindakanoperasi_mp.daftartindakan_id')
                    ->rightJoin('pegawai_m', 'pegawai_m.pegawai_id = timoperasi_t.pegawai_id')
                    ->orderBy([
                        'timoperasi_t.posisi_tim' => SORT_ASC
                    ])
                    ->where(['pasienmasukpenunjang_t.pasienmasukpenunjang_id' => $pasienpenunjangId])->asArray()->all();
                
                $getBillsDetail = NewTimOperasiView::find()
                    ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
                    ->asArray()->all();
                
                $data = $rawdata = [];
                $isCyto = false;
                $isPenyulit = false;
                $daftarTindakanId = $timOperasiPersentase = $tindakanPegawai = [];
                $persencyto_tindakan = 0;
                $persen_penyulit = 0;
                $hargaCyto = 0;
                $hargaPenyulit = 0;
                foreach($getBillsDetail as $bill){
                    if( $bill['is_cyto'] ){
                        $isCyto = $bill['is_cyto'];
                    }
                    if( $bill['is_penyulit'] ){
                        $isPenyulit = $bill['is_penyulit'];
                    }
                    if(!in_array($bill['daftartindakan_id'], $daftarTindakanId) ){
                        $daftarTindakanId[] = $bill['daftartindakan_id'];
                    }
                    $tindakanPegawai[$bill['dokter_id']][] = $bill['daftartindakan_id'];
                    if($bill['posisi_operasi'] == DocoConstants::TIM_OPERASI_DOKTER_BEDAH) {
                        $rawdata[self::TIPE_TINDAKAN][] = [
                            'pasienmasukpenunjang_id' => $pasienpenunjangId,
                            'operasi_nama' => $bill['operasi_nama'],
                            'posisi_operasi' => $bill['posisi_tim'],
                            'golonganoperasi_nama' => $bill['jenisoperasi_nama'],
                            'nama_pegawai' => $bill['nama_pegawai'],
                            'dokter_id' => $bill['dokter_id'],
                            'perawat_id' => null,
                            'tipepaket_id' => null,
                            'daftartindakan_id' => $bill['daftartindakan_id'],
                            'is_cyto' => $isCyto,
                            'is_penyulit' => $isPenyulit,
                            'qty' => 1,
                            'harga' => $bill['harga_operasi'],
                            'daftartindakan_nama' => $bill['daftartindakan_nama'],
                            'timoperasi_id' => $bill['timoperasi_id'],
                            'persentase' => $bill['persentase'],
                            'posisi_tim' => $bill['posisi_tim'],
                            'useprice' => true,
                        ];
                    }
                }
                
                foreach($getServices as $service){
                    $timOperasiPersentase[$service['posisi_tim']][$service['timoperasi_id'].'-'.$service['daftartindakan_id']] = $service['persentase'];
                    if($service['posisi_tim'] == DocoConstants::TIM_OPERASI_DOKTER_BEDAH){
                        $tindakanOperator[$service['posisi_tim']][$service['pegawai_id']] = [];
                    }
                    if($service['posisi_tim'] != DocoConstants::TIM_OPERASI_DOKTER_BEDAH) {
                        $rawdata[self::TIPE_JASA][] = [
                            'pasienmasukpenunjang_id' => $pasienpenunjangId,
                            'operasi_nama' => '-',
                            'posisi_operasi' => $service['posisi_operasi'],
                            'golonganoperasi_nama' => '-',
                            'nama_pegawai' => $service['nama_pegawai'],
                            'dokter_id' => $service['pegawai_id'],
                            'perawat_id' => null,
                            'tipepaket_id' => null,
                            'daftartindakan_id' => $service['daftartindakan_id'],
                            'is_cyto' => $isCyto,
                            'is_penyulit' => $isPenyulit,
                            'qty' => 1,
                            'harga' => 0,
                            'daftartindakan_nama' => $service['daftartindakan_nama'],
                            'timoperasi_id' => $service['timoperasi_id'],
                            'posisi_tim' => $service['posisi_tim'],
                            'persentase' => $service['persentase'],
                            'useprice' => true
                        ];
                    }
                }
                
                $getTarifTindakan = (new TarifTotalFn([
                    'extParam' => [
                        $getBillsHeader['ruangan_id'],
                        $getBillsHeader['penjamin_id'],
                        $getBillsHeader['kelaspelayanan_id'],
                        'penunjang'
                    ]
                ]))
                ->find()
                ->select([
                    'daftartindakan_id',
                    'daftartindakan_nama',
                    'harga_tariftindakan',
                    'persencyto_tindakan',
                    'persen_penyulit',
                ])
                ->where([
                    'daftartindakan_id' => $daftarTindakanId
                ])
                ->asArray()
                ->all();

                $tarifFungsi = [];
                $tarifTertinggi = 0;

                foreach($getTarifTindakan as $tindakan){
                    $tarifFungsi[$tindakan['daftartindakan_id']] = $tindakan;
                }
                
                $totalHarga = $harga_cyto = $harga_penyulit = 0;
                foreach($rawdata as $cat_index => $cat){
                    foreach($cat as $index => $item){
                        $harga = isset($tarifFungsi[$item['daftartindakan_id']]) ? $tarifFungsi[$item['daftartindakan_id']]['harga_tariftindakan'] : 0;

                        $persencyto_tindakan = isset($tarifFungsi[$item['daftartindakan_id']]) ? $tarifFungsi[$item['daftartindakan_id']]['persencyto_tindakan'] : 0;
                        
                        $harga_penyulit = ($persen_penyulit/100) * $harga;

                        $persen_penyulit = isset($tarifFungsi[$item['daftartindakan_id']]) ? $tarifFungsi[$item['daftartindakan_id']]['persen_penyulit'] : 0;

                        if($cat_index == self::TIPE_TINDAKAN) {
                            if(isset($tarifFungsi[$item['daftartindakan_id']])) {
                                $tarifPerTindakan = $tarifFungsi[$item['daftartindakan_id']];
                                $tarifPerTindakan['pegawai_id'] = $item['dokter_id'];
                                $hargaTindakan = isset($tarifFungsi[$item['daftartindakan_id']]) ? $this->perhitunganCyto($tarifPerTindakan, $item['is_cyto'], $item['is_penyulit']) : 0;
                                $totalHarga = $hargaTindakan;
                            }
                        }
                        else {
                            $getPresentase = $this->getHighestPrecentage($timOperasiPersentase[$item['posisi_tim']]);
                            $harga = $this->setJasa([
                                'pegawai_id' => $item['dokter_id'],
                                'posisi_tim' => $item['posisi_tim'],
                                'harga' => $totalHarga,
                                'persencyto_tindakan' => $getPresentase
                            ], $item['is_cyto']);
                        }

                        if($item['is_cyto']) {
                            $harga_cyto = ($persencyto_tindakan/100) * $harga;
                        }
                        
                        if($item['is_penyulit']) {
                            $harga_penyulit = ($persen_penyulit/100) * $harga;
                        }
                        
                        $cat[$index]['harga'] = $harga;
                        $cat[$index]['persencyto_tindakan'] = $persencyto_tindakan;
                        $cat[$index]['persen_penyulit'] = $persen_penyulit;
                        $cat[$index]['harga_cyto'] = $harga_cyto;
                        $cat[$index]['harga_penyulit'] = $harga_penyulit;
                        $cat[$index]['total_harga'] = ($cat_index == self::TIPE_TINDAKAN) ? $totalHarga : ($harga + $harga_cyto + $harga_penyulit);
                        $data[] = $cat[$index];
                        $totalBills += $cat[$index]['harga'];
                        $totalCyto += $cat[$index]['harga_cyto'];
                        $totalPenyulit += $cat[$index]['harga_penyulit'];
                    }
                }
                if( !empty($tarifAkomodasiRuangan) ){
                    $tarifAkomodasiKamar = $this->setTarifAkomodasi($tarifAkomodasiRuangan, $isCyto, $pasienpenunjangId);
                    $data[] = $tarifAkomodasiKamar;
                    $totalBills += $tarifAkomodasiKamar['harga'];
                }
                break;
            default:
                $data = InpostOperasiDetail::find()->select([
                        'inpostoperasidetail_id',
                        'inpostoperasidetail_t.inpostoperasi_id',
                        'inpostoperasidetail_t.operasi_id',
                        'operasi_m.operasi_nama',
                        'inpostoperasidetail_t.golonganoperasi_id',
                        'golonganoperasi_m.golonganoperasi_nama',
                        'inpostoperasidetail_t.daftartindakan_id',
                        'daftartindakan_m.daftartindakan_nama',
                        'inpostoperasidetail_t.dokter_id',
                        'pegawai_m.nama_pegawai',
                        'fgetnamalookup(timoperasi_t.posisi_tim) as posisi_operasi',
                        'is_cyto',
                        'is_penyulit',
                        'inpostoperasidetail_t.harga',
                    ])->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $pasienpenunjangId])
                    ->rightJoin('inpostoperasi_t', 'inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id')
                    ->rightJoin('golonganoperasi_m', 'golonganoperasi_m.golonganoperasi_id = inpostoperasidetail_t.golonganoperasi_id')
                    ->rightJoin('operasi_m', 'operasi_m.operasi_id = inpostoperasidetail_t.operasi_id')
                    ->rightJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = inpostoperasidetail_t.daftartindakan_id')
                    ->rightJoin('timoperasi_t', 'timoperasi_t.pegawai_id = inpostoperasidetail_t.dokter_id')
                    ->rightJoin('pegawai_m', 'pegawai_m.pegawai_id = timoperasi_t.pegawai_id')
                    ->asArray()->all();
                break;
        }
        return [
            'detail' => $data,
            'total' => $totalBills,
            'total_harga' => ($totalBills + $totalCyto + $totalPenyulit),
            'totalCyto' => $totalCyto,
            'totalPenyulit' => $totalPenyulit,
        ];
    }

    /* 
    * array example: 
    * [
    *   [
    *       'daftartindakan_id' => xxx, 
    *       'persencyto_tindakan' => xxx , 
    *       'persen_penyulit' => xxx,
    *       'harga_tariftindakan' => xxx
    *       'pegawai_id' => xxx
    *   ]
    * ]
    */    
    public function perhitunganCyto($item, $is_cyto, $is_penyulit)
    {
        $total = $item['harga_tariftindakan'];
        $tarifCyto = $tarifPenyulit = 0;
        if($is_cyto){
            $tarifCyto = $total*($item['persencyto_tindakan'] / 100);
        }
        if($is_penyulit){
            $tarifPenyulit = $total*($item['persen_penyulit'] / 100);
        }
        $total = (int) $total + $tarifCyto + $tarifPenyulit;
        if( !isset($this->tarifTertinggi[$item['pegawai_id']] ) ){
            $this->tarifTertinggi[$item['pegawai_id']] = 0;
        }
        $this->tarifTindakan[$item['pegawai_id']][$item['daftartindakan_id']] = $total;
        $this->tarifTertinggi[$item['pegawai_id']] =  $total > $this->tarifTertinggi[$item['pegawai_id']] ? $total : $this->tarifTertinggi[$item['pegawai_id']];
        return $total;
    }

    /* 
    * array example: 
    * [
    *   [
    *       'posisi_tim' => xxx, 
    *       'harga_tariftindakan' => xxx, 
    *       'persencyto_tindakan' => xxx , 
    *   ]
    * ]
    */ 
    public function setJasa($item, $is_cyto)
    {
        return (int) $item['harga'] * ($item['persencyto_tindakan'] / 100);
    }
    /* 
    * array example: 
    * [
    *     509 => [
    *         '232-1094' => '40.00',
    *         '233-1094' => '30.00',
    *     ],
    * ]
    */ 
    public static function getHighestPrecentage($item)
    {
        if(count($item) > 1){
            $precentage = 0;
            foreach($item as $value){
                $precentage = $value > $precentage ? $value : $precentage;
            }
            $precentage = round($precentage / count($item));
        }else{
            $precentage = reset($item);
        }
        return $precentage;
    }
    /* 
    * array example: 
    * [
    *   [
    *       'daftartindakan_id' => xxx, 
    *       'daftartindakan_nama' => xxx, 
    *       'persencyto_tindakan' => xxx ,
    *       'harga_tariftindakan' => xxx,
    *       'kamarruangan_nokamar' => xxx
    *   ]
    * ]
    */   
    public static function setTarifAkomodasi($tarifAkomodasi, $is_cyto, $pasienpenunjang_id)
    {
        $data = [
            'pasienmasukpenunjang_id' => $pasienpenunjang_id,
            'operasi_nama' => '-',
            'posisi_operasi' => '-',
            'golonganoperasi_nama' => '-',
            'nama_pegawai' => '-',
            'dokter_id' => null,
            'perawat_id' => null,
            'tipepaket_id' => null,
            'daftartindakan_id' => $tarifAkomodasi['daftartindakan_id'],
            'is_cyto' => $is_cyto,
            'is_penyulit' => false,
            'qty' => 1,
            'harga' => 0,
            'daftartindakan_nama' => $tarifAkomodasi['daftartindakan_nama']. ' ' . $tarifAkomodasi['kamarruangan_nokamar'],
            'useprice' => true
        ];
        $getDataOperasi = InpostOperasi::find()->select([
            'inpostoperasi_t.mulai_operasi',
            'inpostoperasi_t.selesai_operasi',
            '(EXTRACT(EPOCH FROM inpostoperasi_t.selesai_operasi) - EXTRACT(EPOCH FROM inpostoperasi_t.mulai_operasi))/3600 as timediff',
            'kamarruangan_m.durasi'
        ])->where(['inpostoperasi_t.pasienmasukpenunjang_id' => $pasienpenunjang_id ])
        ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id')
        ->rightJoin('kamarruangan_m', 'kamarruangan_m.kamarruangan_id = pasienmasukpenunjang_t.kamarruangan_id')
        ->asArray()->one();
        $data['harga'] = $tarifAkomodasi['harga_tariftindakan'];
        if($is_cyto){
            $persenCyto = $tarifAkomodasi['persencyto_tindakan'];
            $data['harga'] += ($tarifAkomodasi['harga_tariftindakan'] * ($persenCyto/100));
        }
        $persenKenaikanDurasi = 25;
        if($getDataOperasi['timediff'] > $getDataOperasi['durasi']){
            $kelebihandurasi = $getDataOperasi['timediff'] - $getDataOperasi['durasi'];
            $persenKenaikanDurasi = $persenKenaikanDurasi * abs($kelebihandurasi);
            if($persenKenaikanDurasi >= 100){
                $persenKenaikanDurasi = 100;
            }
            $data['harga'] += ($tarifAkomodasi['harga_tariftindakan'] * ($persenKenaikanDurasi/100));
        }
        return $data;
    }
    private function getHighestOperator()
    {
        $total = 0;
        if( !empty($this->tarifOperator) ){
            foreach($this->tarifOperator as $tarif){
                $total = $total > $tarif ? $total  : $tarif;
            }
        }
        return $total;
    }
}
