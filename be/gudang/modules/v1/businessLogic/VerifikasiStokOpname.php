<?php 
namespace app\modules\v1\businessLogic;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\StokBarang as ModelStok;
use app\modules\v1\models\InfoStokOpnameBarangDetailView;
use app\modules\v1\models\Barang;

class VerifikasiStokOpname
{
    public static function updateStokBarang($stokopname_id, $ruangan_id, $tgl_implementasi = null)
    {
        if (empty($stokopname_id)) return true;
        $mappingData = [];
        $tmpInsert = $listOfOutStock = $error = [];
        
        $dataSoDetail = self::getDataSOdetail($stokopname_id);
        $idBarang = count($dataSoDetail) > 0 ? ArrayHelper::getColumn($dataSoDetail, 'barang_id') : [];
        $listKartuStok = self::getKartuStok($ruangan_id, $idBarang, $tgl_implementasi);
        
        $mappingData = self::setPenyesuaian($listKartuStok, $dataSoDetail, $idBarang, $ruangan_id, $tgl_implementasi);
        
        $error = ArrayHelper::getValue($mappingData, 'message');
        if ($error) return $mappingData;
        
        $tmpInsert = ArrayHelper::getValue($mappingData, 'tmpInsert', []); 
        $listOfOutStock = ArrayHelper::getValue($mappingData, 'listOfOutStock', []);
        
        // insert new stok awal
        ModelStok::batchInsert($tmpInsert, false);
        /** Update Stok jadiin false **/
        if ($listOfOutStock) {
            self::updateOutOfStock($listOfOutStock);
        }
        return true;
    }

    /**
     * set stok awal
     */
    private static function setStokAwal($listKartuStok, $dataSoDetail, $ruangan_id)
    {
        $tmpInsert = [];
        $listOfOutStock = [];
        if (is_array($dataSoDetail) && !empty($dataSoDetail)) {
            $tmpBarang = [];
            foreach($listKartuStok as $value) {
                $listOfOutStock[] = ArrayHelper::getValue($value, 'id_stok');
                $tmpBarang[ArrayHelper::getValue($value, 'barang_id')] = $value;
            }
    
            $listBarang = [];
            foreach($dataSoDetail as $key => $val) {
                $listBarang[ArrayHelper::getValue($val, 'barang_id')] = ArrayHelper::getValue($val, 'volume_fisik');
            }
    
            $tmpInsert_temp = [];
            foreach($dataSoDetail as $key => $detailSo) {
                $barangTmp = isset($tmpBarang[$detailSo['barang_id']])  ? $tmpBarang[$detailSo['barang_id']] : [];
                $stok_fisik = ArrayHelper::getValue($detailSo, 'volume_fisik');
                $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                $tmpInsert_temp['stokopnamebarangdetail_id'] = ArrayHelper::getValue($detailSo, 'stokopnamebarangdetail_id');
                $tmpInsert_temp['barang_id'] = ArrayHelper::getValue($detailSo, 'barang_id');
                $tmpInsert_temp['nobatch'] = ArrayHelper::getValue($barangTmp, 'nobatch', null);
                $tmpInsert_temp['satuankecil_id'] = ArrayHelper::getValue($barangTmp, 'satuankecil_id');
                $tmpInsert_temp['tglkadaluarsa'] = ArrayHelper::getValue($detailSo, 'tglkadaluarsa');
                $tmpInsert_temp['harganetto'] = ArrayHelper::getValue($detailSo, 'harganetto');
                $tmpInsert_temp['tglstok_in'] = date('Y-m-d H:i:s');
                $tmpInsert_temp['qtystok_in'] = $stok_fisik;
                $tmpInsert[] = $tmpInsert_temp;
            }
        }
        return [
            'tmpInsert' => $tmpInsert,
            'listOfOutStock' => $listOfOutStock
        ];
    }

    /**
     * set untuk penyesuaian kartu stok
     */
    private static function setPenyesuaian($listKartuStok, $dataSoDetail, $idBarang, $ruangan_id, $tgl_implementasi = null)
    {
        $tmpInsert = [];
        $listOfOutStock = [];
        if (!empty($dataSoDetail)) {
            $dataMasterBarang = $dataSatuanKecil = [];
            if (!empty($idBarang)) {
                $masterBarang = Barang::find()->select(['barang_id', 'barang_harganetto', 'satuankecil_id'])->where(['IN','barang_id',$idBarang])->asArray()->all();
                if (!empty($masterBarang)) {
                    $dataMasterBarang = array_column($masterBarang, 'barang_harganetto', 'barang_id');
                    $dataSatuanKecil = array_column($masterBarang, 'satuankecil_id', 'barang_id');
                }
            }
    
            $dataBarang = [];
            foreach ($listKartuStok as $key => $value) {
                if (isset($dataSatuanKecil[$value['barang_id']]) && empty($value['satuankecil_id'])) {
                    $value['satuankecil_id'] = $dataSatuanKecil[$value['barang_id']];
                }
                $dataBarang[ArrayHelper::getValue($value, 'barang_id')][] = $value;
            }
    
            $arr_key_aktif = array_keys($dataBarang);
            $insertTemp = [];
            foreach ($dataSoDetail as $detail) {
                $primary = ArrayHelper::getValue($detail, 'barang_id');
                $isStokAktif = in_array($primary, $arr_key_aktif);
                $calculate = 0;
                if (!empty($tgl_implementasi)) {
                    $calculate = ArrayHelper::getValue($detail, 'volume_fisik', 0) - ArrayHelper::getValue($detail, 'volume_sistem', 0);
                } else {
                    $calculate = ArrayHelper::getValue($detail, 'stok_selisih');
                }
                $is_stokin = $calculate > 0 ? true : false;
                $qty_fisik = abs($calculate);
                if ($calculate != 0) {
                    if ($is_stokin > 0) {
                        $insertTemp = self::setPayloadStokBarang($detail, $ruangan_id, $tgl_implementasi, $qty_fisik, $is_stokin);
                        $tmpInsert[] = $insertTemp;
                    } else {
                        if ($isStokAktif) {
                            if(!empty($tgl_implementasi)){
                                if ($qty_fisik > ArrayHelper::getValue($detail, 'volume_sistem', 0)) {
                                    return ['message' => 'Stok barang '.$detail['barang_nama'].' tidak mencukupi'];
                                }
                            }else{
                                if ($qty_fisik > ArrayHelper::getValue($detail, 'stok_sistem', 0)) {
                                    return ['message' => 'Stok barang '.$detail['barang_nama'].' tidak mencukupi'];
                                }
                            }
    
                            foreach($dataBarang[$primary] as $key => $val) {
                                $stokNow  = ArrayHelper::getValue($val, 'total_stok');
                                $isi = 0;
                                if ($qty_fisik >= $stokNow) {
                                    $isi = $stokNow;
                                    $qty_fisik -= $stokNow;
                                    $listOfOutStock[] = $val['id_stok'];
                                    $stokNow = 0;
                                } else {
                                    $isi = $qty_fisik;
                                    $qty_fisik = 0;
                                }
                                $params = [
                                    'valStokBarang' => $val, 
                                    'isi' => $isi,
                                ];
    
                                $insertTemp = self::setPayloadStokBarang($detail, $ruangan_id, $tgl_implementasi, $qty_fisik, $is_stokin, $params);
                                $insertTemp['stokbarangasal_id'] = $val['id_stok'];
                                $tmpInsert[] = $insertTemp;
                                if ($qty_fisik <= 0) break;
                            }
                        } else {
                            return ['message' => 'Terdapat Barang dengan stok sistem 0'];
                        }
                    }
                }
            }
        }
        return [
            'tmpInsert' => $tmpInsert,
            'listOfOutStock' => $listOfOutStock,
        ];
    }

    private function getDataSOdetail($stokopname_id)
    {
        return InfoStokOpnameBarangDetailView::find()->select([
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
        ->where(['stokopnamebarang_id' => $stokopname_id])
        ->asArray()->all();
    }

    private function getKartuStok($ruangan_id, $idBarang, $tgl_implementasi = null)
    {
        $result = [];
        if (!empty($idBarang)) {
            $arrInCondition = [];
            foreach($idBarang as $key => $value) {
                $arrInCondition[":barang_id" . $key] = $value;
            }
            $barangsParams = implode(',', array_keys($arrInCondition));
    
            $tglCondition = "";
            if (!empty($tgl_implementasi)) {
                $tglCondition = "and COALESCE(tglstok_in, tglstok_out)::date <= :tgl_implementasi";
            }
    
            $query = Yii::$app->db->createCommand("
                SELECT  a.stokbarang_id as id_stok,
                        a.barang_id,
                        a.ruangan_id,
                        a.satuankecil_id,
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
                ORDER BY a.stokbarang_id, a.barang_id ,COALESCE(a.tglstok_in, a.tglstok_out)::date ASC
            ");
            $query->bindValues($arrInCondition);
            $query->bindValue(":ruangan_id", $ruangan_id);
            if (!empty($tgl_implementasi)) $query->bindValue(":tgl_implementasi", date('Y-m-d', strtotime($tgl_implementasi)));
            
            $result = $query->queryAll();
        }
        return $result;
    }

    private function setPayloadStokBarang($detailSo, $ruangan_id, $tgl_implementasi, $qty_fisik, $is_stokin, $params = [])
    {
        $valStokBarang = ArrayHelper::getValue($params, 'valStokBarang', []);
        $isi = ArrayHelper::getValue($params, 'isi', 0);

        $insertTemp['ruangan_id'] = $ruangan_id;
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
            $insertTemp['tglstok_in'] = !empty($tgl_implementasi) ? $tgl_implementasi : date('Y-m-d H:i:s');
        } else {
            $insertTemp['qtystok_in'] = 0;
            $insertTemp['qtystok_out'] = abs($isi);
            $insertTemp['tglstok_in'] = null;
            $insertTemp['tglstok_out'] = !empty($tgl_implementasi) ? $tgl_implementasi : date('Y-m-d H:i:s');
        }
        return $insertTemp;
    }

    private function updateOutOfStock($listOfOutStock)
    {
        $inCondition = "(" . implode(",", $listOfOutStock) . ")";
        Yii::$app->db->createCommand("
            UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
            WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
            AND stokbarang_aktif = true;
        ")->execute();
    }
}