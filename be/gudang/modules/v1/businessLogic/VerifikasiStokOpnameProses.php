<?php 

namespace app\modules\v1\businessLogic;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\StokBarang as ModelStok;
use app\modules\v1\models\InfoStokOpnameBarangDetailView;
use app\modules\v1\models\Barang;
use app\modules\v1\models\KonfigGudang;
use Doco\components\DocoConstants;

class VerifikasiStokOpnameProses {

    protected $stokopnamebarang_id;
    protected $ruangan_id;
    protected $tgl_implementasi;

    protected $data_so_detail = [];
    protected $list_kartu_stok = [];
    protected $list_barang_id = [];

    protected $temp_insert_kartu_stoks = [];
    protected $list_of_out_stock = [];

    protected $error_message = [];

    protected $metode;

    public function __construct(array $params)
    {
        $this->stokopnamebarang_id = ArrayHelper::getValue($params, 'stokopnamebarang_id');
        $this->ruangan_id = ArrayHelper::getValue($params, 'ruangan_id');
        $this->tgl_implementasi = ArrayHelper::getValue($params, 'tgl_implementasi');
    }
    
    public function execute()
    {
        $this->getKonfigMetode();
        $this->getDataSOdetail();
        $this->getKartuStok();

        $this->mappingData();
        
        $this->saveData();
        return true;
    }

    private function getKonfigMetode()
    {
        $this->metode = KonfigGudang::find()->select('metodeantrian')->one(); 
    }

    /**
     * get Data Detail So Barang
     */
    private function getDataSOdetail()
    {
        $this->data_so_detail = InfoStokOpnameBarangDetailView::find()->select([
            'stokopnamebarangdetail_id',
            'barang_id',
            'barang_nama',
            'volume_fisik',
            'volume_sistem',
            'harganetto',
            'stok_sistem',
            'stok_selisih',
            'tglkadaluarsa',
            'satuankecil_id'
        ])
        ->where(['stokopnamebarang_id' => $this->stokopnamebarang_id])
        ->asArray()->all();

        $this->list_barang_id = count($this->data_so_detail) > 0 ? ArrayHelper::getColumn($this->data_so_detail, 'barang_id') : [];
    }

    /**
     * get Kartu stok
     */
    private function getKartuStok()
    {
        if (!empty($this->list_barang_id)) {
            $arrInCondition = [];
            foreach($this->list_barang_id as $key => $value) {
                $arrInCondition[":barang_id" . $key] = $value;
            }
            $barangsParams = implode(',', array_keys($arrInCondition));
    
            $tglCondition = "";
            if (!empty($this->tgl_implementasi)) {
                $tglCondition = "and COALESCE(tglstok_in, tglstok_out) <= :tgl_implementasi";
            }

            $order = $this->orderByMetode();
            $query = Yii::$app->db->createCommand("
                SELECT  a.stokbarang_id as id_stok,
                        a.barang_id,
                        a.ruangan_id,
                        a.satuankecil_id,
                        a.tglkadaluarsa,
                        COALESCE(a.tglstok_in, a.tglstok_out)::date AS tgl_transaksi,
                        CASE WHEN tmp.stokbarangasal_id is null 
                                THEN a.qtystok_in
                                ELSE a.qtystok_in - tmp.qtystok_out
                        end AS total_stok
                FROM stokbarang_t a
                LEFT JOIN (
                    SELECT 
                        b.stokbarangasal_id,
                        SUM(COALESCE(b.qtystok_out, 0)) as qtystok_out
                    FROM stokbarang_t b
                    WHERE b.barang_id IN ($barangsParams) AND b.ruangan_id = :ruangan_id {$tglCondition} 
                    GROUP BY b.stokbarangasal_id
                ) tmp ON a.stokbarang_id = tmp.stokbarangasal_id
                WHERE a.barang_id IN ($barangsParams) AND a.ruangan_id = :ruangan_id {$tglCondition} 
                AND (CASE WHEN tmp.stokbarangasal_id is null 
                        THEN a.qtystok_in 
                        ELSE a.qtystok_in - tmp.qtystok_out 
                    end > 0)
                ORDER BY {$order}
            ");
            $query->bindValues($arrInCondition);
            $query->bindValue(":ruangan_id", $this->ruangan_id);
            if (!empty($this->tgl_implementasi)) $query->bindValue(":tgl_implementasi", date('Y-m-d H:i:s', strtotime($this->tgl_implementasi)));
            $this->list_kartu_stok = $query->queryAll();
        }
    }

     /**
     * set stok awal
     */
    private function setStokAwal()
    {
        $temp_insert_kartu_stoks = [];
        $list_of_out_stock = [];
        if (is_array($this->data_so_detail) && !empty($this->data_so_detail)) {
            $tmpBarang = [];
            foreach($this->list_kartu_stok as $value) {
                $list_of_out_stock[] = ArrayHelper::getValue($value, 'id_stok');
                $tmpBarang[ArrayHelper::getValue($value, 'barang_id')] = $value;
            }
    
            $listBarang = [];
            foreach($this->data_so_detail as $key => $val) {
                $listBarang[ArrayHelper::getValue($val, 'barang_id')] = ArrayHelper::getValue($val, 'volume_fisik');
            }
    
            $tmpInsert_temp = [];
            foreach($this->data_so_detail as $key => $detailSo) {
                $barangTmp = isset($tmpBarang[$detailSo['barang_id']])  ? $tmpBarang[$detailSo['barang_id']] : [];
                $stok_fisik = ArrayHelper::getValue($detailSo, 'volume_fisik');
                $tmpInsert_temp['ruangan_id'] = $this->ruangan_id;
                $tmpInsert_temp['stokopnamebarangdetail_id'] = ArrayHelper::getValue($detailSo, 'stokopnamebarangdetail_id');
                $tmpInsert_temp['barang_id'] = ArrayHelper::getValue($detailSo, 'barang_id');
                $tmpInsert_temp['nobatch'] = ArrayHelper::getValue($barangTmp, 'nobatch', null);
                $tmpInsert_temp['satuankecil_id'] = ArrayHelper::getValue($barangTmp, 'satuankecil_id');
                $tmpInsert_temp['tglkadaluarsa'] = ArrayHelper::getValue($detailSo, 'tglkadaluarsa');
                $tmpInsert_temp['harganetto'] = ArrayHelper::getValue($detailSo, 'harganetto');
                $tmpInsert_temp['tglstok_in'] = date('Y-m-d H:i:s');
                $tmpInsert_temp['qtystok_in'] = $stok_fisik;
                $temp_insert_kartu_stoks[] = $tmpInsert_temp;
            }
        }

        $this->temp_insert_kartu_stoks = $temp_insert_kartu_stoks;
        $this->list_of_out_stock = $list_of_out_stock;
    }

    /**
     * set untuk penyesuaian kartu stok
     */
    private function mappingData()
    {
        $temp_insert_kartu_stoks = [];
        $list_of_out_stock = [];
        if (!empty($this->data_so_detail)) {
            $dataMasterBarang = $dataSatuanKecil = [];
            if (!empty($this->list_barang_id)) {
                $masterBarang = Barang::find()->select(['barang_id', 'barang_harganetto', 'satuankecil_id'])->where(['IN','barang_id',$this->list_barang_id])->asArray()->all();
                if (!empty($masterBarang)) {
                    $dataMasterBarang = array_column($masterBarang, 'barang_harganetto', 'barang_id');
                    $dataSatuanKecil = array_column($masterBarang, 'satuankecil_id', 'barang_id');
                }
            }
    
            $dataBarang = [];
            foreach ($this->list_kartu_stok as $key => $value) {
                if (isset($dataSatuanKecil[$value['barang_id']]) && empty($value['satuankecil_id'])) {
                    $value['satuankecil_id'] = $dataSatuanKecil[$value['barang_id']];
                }
                $dataBarang[ArrayHelper::getValue($value, 'barang_id')][] = $value;
            }
    
            $arr_key_aktif = array_keys($dataBarang);
            $insertTemp = [];
            foreach ($this->data_so_detail as $detail) {
                $primary = ArrayHelper::getValue($detail, 'barang_id');
                $isStokAktif = in_array($primary, $arr_key_aktif);
                $calculate = 0;
                if (!empty($this->tgl_implementasi)) {
                    $calculate = ArrayHelper::getValue($detail, 'volume_fisik', 0) - ArrayHelper::getValue($detail, 'volume_sistem', 0);
                } else {
                    $calculate = ArrayHelper::getValue($detail, 'stok_selisih');
                }
                $is_stokin = $calculate > 0 ? true : false;
                $qty_fisik = abs($calculate);
                if ($calculate != 0) {
                    if ($is_stokin > 0) {
                        $insertTemp = self::setPayloadStokBarang($detail, $qty_fisik, $is_stokin);
                        $temp_insert_kartu_stoks[] = $insertTemp;
                    } else {
                        if ($isStokAktif) {
                            if(!empty($this->tgl_implementasi)){
                                if ($qty_fisik > ArrayHelper::getValue($detail, 'volume_sistem', 0)) {
                                    throw new \Exception( 'Stok barang '.$detail['barang_nama'].'  tidak mencukupi', 1);
                                }
                            }else{
                                if ($qty_fisik > ArrayHelper::getValue($detail, 'stok_sistem', 0)) {
                                    throw new \Exception( 'Stok barang '.$detail['barang_nama'].'  tidak mencukupi', 1);
                                }
                            }
    
                            foreach($dataBarang[$primary] as $key => $val) {
                                $stokNow  = ArrayHelper::getValue($val, 'total_stok');
                                $isi = 0;
                                if ($qty_fisik >= $stokNow) {
                                    $isi = $stokNow;
                                    $qty_fisik -= $stokNow;
                                    $list_of_out_stock[] = $val['id_stok'];
                                    $stokNow = 0;
                                } else {
                                    $isi = $qty_fisik;
                                    $qty_fisik = 0;
                                }
                                $params = [
                                    'valStokBarang' => $val, 
                                    'isi' => $isi,
                                ];
    
                                $insertTemp = self::setPayloadStokBarang($detail, $qty_fisik, $is_stokin, $params);
                                $insertTemp['stokbarangasal_id'] = $val['id_stok'];
                                $temp_insert_kartu_stoks[] = $insertTemp;
                                if ($qty_fisik <= 0) break;
                            }
                        } else {
                            throw new \Exception( 'Terdapat Barang dengan stok sistem 0', 1);
                        }
                    }
                }
            }
        }

        $this->temp_insert_kartu_stoks = $temp_insert_kartu_stoks;
        $this->list_of_out_stock = $list_of_out_stock;
    }

    /**
     * Update Stok jadiin false
     */
    private function updateOutOfStock()
    {
        $inCondition = "(" . implode(",", $this->list_of_out_stock) . ")";
        Yii::$app->db->createCommand("
            UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
            WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
            AND stokbarang_aktif = true;
        ")->execute();
    }

    private function setPayloadStokBarang($detailSo, $qty_fisik, $is_stokin, $params = [])
    {
        $valStokBarang = ArrayHelper::getValue($params, 'valStokBarang', []);
        $isi = ArrayHelper::getValue($params, 'isi', 0);

        $insertTemp['ruangan_id'] = $this->ruangan_id;
        $insertTemp['stokopnamebarangdetail_id'] = ArrayHelper::getValue($detailSo, 'stokopnamebarangdetail_id');
        $insertTemp['barang_id'] = ArrayHelper::getValue($detailSo, 'barang_id');
        $insertTemp['nobatch'] = ArrayHelper::getValue($valStokBarang, 'nobatch', null);
        $insertTemp['satuankecil_id'] = ArrayHelper::getValue($detailSo, 'satuankecil_id');
        $insertTemp['tglkadaluarsa'] = ArrayHelper::getValue($valStokBarang, 'tglkadaluarsa');
        $insertTemp['harganetto'] = ArrayHelper::getValue($detailSo, 'harganetto');
        $insertTemp['stokbarang_aktif'] = $is_stokin ? TRUE : FALSE;
        if ($is_stokin) {
            $insertTemp['qtystok_in'] = $qty_fisik;
            $insertTemp['qtystok_out'] = 0;
            $insertTemp['tglstok_in'] = !empty($this->tgl_implementasi) ? $this->tgl_implementasi : date('Y-m-d H:i:s');
        } else {
            $insertTemp['qtystok_in'] = 0;
            $insertTemp['qtystok_out'] = abs($isi);
            $insertTemp['tglstok_in'] = null;
            $insertTemp['tglstok_out'] = !empty($this->tgl_implementasi) ? $this->tgl_implementasi : date('Y-m-d H:i:s');
        }
        return $insertTemp;
    }

    /**
     * temp_insert_kartu_stoks = insert new stok awal
     * list_of_out_stock = Update Stok jadiin false
     */
    protected function saveData()
    {
        ModelStok::batchInsert($this->temp_insert_kartu_stoks, false);
        if ($this->list_of_out_stock) $this->updateOutOfStock();
    }

    /**
     * set order by 
     * default Metode FIFO
     */
    private function orderByMetode()
    {
        $order = "";
        switch ($this->metode) {
            case DocoConstants::FIFO:
                $order = "a.stokbarang_id, a.barang_id ,COALESCE(a.tglstok_in, a.tglstok_out)::date ASC";
                break;
            case DocoConstants::LIFO:
                $order = "a.stokbarang_id, a.barang_id ,COALESCE(a.tglstok_in, a.tglstok_out)::date DESC";
                break;
            case DocoConstants::FEFO:
                $order = "a.tglkadaluarsa, COALESCE(a.tglstok_in, a.tglstok_out)::date ASC";
                break;    
            default:
                $order = "a.stokbarang_id, a.barang_id ,COALESCE(a.tglstok_in, a.tglstok_out)::date ASC";
                break;
        }
        return $order;
    }
}