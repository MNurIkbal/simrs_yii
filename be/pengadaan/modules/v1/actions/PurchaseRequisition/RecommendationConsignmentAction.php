<?php 

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use yii\db\Expression;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\KetersediaanObat;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\StokobatalkesRsView;
use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\ValidasiPoObatDetail;

class RecommendationConsignmentAction extends Action 
{
    public function run() 
    {
        $request = Yii::$app->request;
        $jenis_obat = $request->get('jenis_obat', []);

        // get data obat 
        $collections = $this->getCollection($jenis_obat);
        $data_obat = ArrayHelper::getValue($collections, 'data_obat', []);
        $list_obatalkes_id = ArrayHelper::getValue($collections, 'list_obatalkes_id', []);
        $list_data_obat = ArrayHelper::index($data_obat, 'obatalkes_id');;

        $pr_belum_po = $this->getPrBelumPo($list_obatalkes_id);
        $po_belum_terima = $this->getPoBelumTerima($list_obatalkes_id);

        $stok_obat = $this->getAllStok($list_obatalkes_id);
        $all_stok = ArrayHelper::getValue($stok_obat, 'all_stok', []);
        $list_stok = ArrayHelper::getValue($stok_obat, 'list_stok', []);
        $stok_ruangan = ArrayHelper::getValue($stok_obat, 'stok_ruangan', []);

        // get convertion
        $list_konversi = $this->listSatuanKonv($list_data_obat);
        // get convertion from master 06-12-2021
        $list_konversi_master = $this->listNilaiKonversiMaster($list_obatalkes_id);

        $list_konversi_pr = $this->listSatuanKonvPr($pr_belum_po);
        $list_konversi_po = $this->listSatuanKonvPo($po_belum_terima);

        $recommendation = [];
        foreach ($data_obat as $item) 
        {
            if(!isset($list_konversi[$item['obatalkes_id']])) {
                continue;
            }

            if(!isset($list_konversi_master[$item['obatalkes_id']])) {
                continue;
            }
            
            // aset rs
            $stok_aset_rs = ArrayHelper::getValue($item, 'aset_rs', 0);
            // stok saat ini
            $stok_saat_ini = isset($list_stok[$item['obatalkes_id']]) ? $list_stok[$item['obatalkes_id']] : 0;
            // qty_belum_po
            $qty_belum_po = isset($pr_belum_po[$item['obatalkes_id']]) ? ArrayHelper::getValue($pr_belum_po[$item['obatalkes_id']], 'qty_input', 0) : 0;
            $konversi_pr = isset($list_konversi_pr[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi_pr[$item['obatalkes_id']], 'nilai_konversi', 0) : 0;
            $qty_belum_po = $qty_belum_po * $konversi_pr;
            // qty_po_belum_terima
            $qty_po_belum_terima = isset($po_belum_terima[$item['obatalkes_id']]) ? ArrayHelper::getValue($po_belum_terima[$item['obatalkes_id']], 'qty_po', 0) : 0;
            $konversi_po = isset($list_konversi_po[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi_po[$item['obatalkes_id']], 'nilai_konversi', 0) : 0;
            $qty_po_belum_terima = $qty_po_belum_terima * $konversi_po;
            // qty ro consignment
            $stok_saat_ini = $stok_saat_ini + $qty_belum_po + $qty_po_belum_terima;
            $qty_ro_consignment = $stok_aset_rs - $stok_saat_ini;

            if ($qty_ro_consignment > 0) {
                $data['obatalkes_id'] = ArrayHelper::getValue($item, 'obatalkes_id');
                $data['obatalkes_kode'] = ArrayHelper::getValue($item, 'obatalkes_kode');
                $data['obatalkes_nama'] = ArrayHelper::getValue($item, 'obatalkes_nama');
                $data['satuankecil_id'] = isset($list_konversi[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi[$item['obatalkes_id']], 'satuanbesar_id') : null;
                $data['satuankecil_nama'] = isset($list_konversi[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi[$item['obatalkes_id']], 'satuan_kecil') : null;
                $data['nilai_konversi'] = isset($list_konversi[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi[$item['obatalkes_id']], 'nilai_konversi', 0) : 0;
                $data['doi'] = 0;
                $data['ss_min'] = 0;
                $data['stok_rs'] = 0;
                $data['stok_saatini'] = $stok_saat_ini;
                $data['kebutuhan'] = '-';
                $data['presentase'] = 0;
                $data['rekomendasi_order'] = ceil($qty_ro_consignment);
                $data['satuan_rekomendasi_order'] = isset($list_konversi[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi[$item['obatalkes_id']], 'satuan_besar') : null;
                $data['rekomendasi_qty_po'] = ceil($qty_ro_consignment);
                $data['satuan_rekomendasi_po'] = isset($list_konversi[$item['obatalkes_id']]) ? ArrayHelper::getValue($list_konversi[$item['obatalkes_id']], 'satuan_besar') : null;
                $data['stok_gudang'] = isset($stok_ruangan[$item['obatalkes_id']]) ? ArrayHelper::getValue($stok_ruangan[$item['obatalkes_id']], 'stok_gudang', 0) : 0;
                $data['stok_farmasi'] = isset($stok_ruangan[$item['obatalkes_id']]) ? ArrayHelper::getValue($stok_ruangan[$item['obatalkes_id']], 'stok_farmasi', 0) : 0;
                $data['stok_lain'] = isset($stok_ruangan[$item['obatalkes_id']]) ? ArrayHelper::getValue($stok_ruangan[$item['obatalkes_id']], 'stok_lain', 0) : 0;
                $data['last_7'] = 0;
                $data['last_14'] = 0;
                $data['last_30'] = 0;
                $data['qty_outstanding'] = 0;
                $data['konversi_label'] = isset($list_konversi_master[$item['obatalkes_id']]) ? $list_konversi_master[$item['obatalkes_id']]['konversi'] : 0;
                $data['reorder'] = null;
                $data['move_category_id'] = null;
                $data['move_category'] = null;
                
                array_push($recommendation, $data);
            }
        }
        
        return $this->controller->responseJson(200, 'Generate RO Consignment Berhasil', $recommendation);
    }

    public function isLargeUnitPr()
    {
        $satuan_config = 'satuanbesar_id';
        $config = KonfigFarmasi::find()->select(['is_large_unit_pr'])->asArray()->one();
        if (isset($config['is_large_unit_pr']) && !$config['is_large_unit_pr']) {
            $satuan_config = 'satuankecil_id';
        }
        // masih harcode
        $satuan_config = 'satuankecil_id';
        return $satuan_config;
    }

    public function getCollection($jenis_obat = [])
    {
        $data_obat = StokobatalkesRsView::find()->where(['in', 'jenisobatalkes_id', $jenis_obat])->asArray()->all();
        $list_obatalkes_id = ArrayHelper::getColumn($data_obat, 'obatalkes_id');
        
        return [
            'data_obat' => $data_obat,
            'list_obatalkes_id' => $list_obatalkes_id
        ];
    }

    public function getAllStok($list_obatalkes_id)
    {
        // get ketersediaan obat by obat
        $all_stok = KetersediaanObat::find()
            ->select(['obatalkes_id', 'SUM(qty_stok) as qty_stok'])
            ->where(['in', 'obatalkes_id', $list_obatalkes_id])
            ->groupBy('obatalkes_id')
            ->asArray()->all();
        $list_stok = ArrayHelper::map($all_stok, 'obatalkes_id', 'qty_stok');
        
        // get loopup transaksi
        $lookupTransaksi = LookupTransaksi::find()
            ->select(['kode_transaksi', 'kode_id'])
            ->where(['in', 'kode_transaksi', ['farmasi_utama', 'gudang_farmasi']])
            ->asArray()->all();

        $arrLookupTransaksi = ArrayHelper::index($lookupTransaksi, 'kode_transaksi');
        $farmasi_utama = isset($arrLookupTransaksi['farmasi_utama']) ? ArrayHelper::getValue($arrLookupTransaksi['farmasi_utama'], 'kode_id', null) : null;
        $gudang_farmasi = isset($arrLookupTransaksi['gudang_farmasi']) ? ArrayHelper::getValue($arrLookupTransaksi['gudang_farmasi'], 'kode_id', null) : null;

        $stok_ruangan = [];
        if (!empty($farmasi_utama) && !empty($gudang_farmasi)) {
            $getStokRuangan = KetersediaanObat::find()
                ->select(['obatalkes_id',
                    new Expression("SUM(CASE WHEN ruangan_id = {$farmasi_utama} THEN qty_stok ELSE 0 END) AS stok_farmasi"),
                    new Expression("SUM(CASE WHEN ruangan_id = {$gudang_farmasi} THEN qty_stok ELSE 0 END) AS stok_gudang"),
                    new Expression("SUM(CASE WHEN ruangan_id not in ({$farmasi_utama}, {$gudang_farmasi}) THEN qty_stok ELSE 0 END) AS stok_lain")
                ])
                ->where(['in', 'obatalkes_id', $list_obatalkes_id])
                ->groupBy('obatalkes_id')
                ->asArray()->all();
            $stok_ruangan = ArrayHelper::index($getStokRuangan, 'obatalkes_id');
        }

        return [
            'all_stok' => $all_stok,
            'list_stok' => $list_stok,
            'stok_ruangan' => $stok_ruangan
        ];
    }

    public function listSatuanKonv($list_data_obat)
    {
        $list_konversi = [];
        if (!empty($list_data_obat)) {
            $obatalkes_id = array_keys($list_data_obat);
            $konversi = SatuanKonversiView::find()
                ->where(['in', 'obatalkes_id', $obatalkes_id])
                ->asArray()->all();
            
            foreach($konversi as $item) {
                if (
                    in_array($item['obatalkes_id'], $obatalkes_id) && 
                    $item['satuanbesar_id'] == $list_data_obat[$item['obatalkes_id']]['satuanbesar_id']
                ) {
                    $list_konversi[] = $item;
                }
            }
            $list_konversi = ArrayHelper::index($list_konversi, 'obatalkes_id');
        }
        return $list_konversi;
    }

    public function listNilaiKonversiMaster($list_obatalkes_id) 
    {
        $list_obat_master = [];
        if(!empty($list_obatalkes_id)) {
            $query = new \yii\db\Query();
            $result = $query->select(["a.obatalkes_id", "b.satuanunit_id", "b.satuanunit_nama", "c.satuanunit_id", "c.satuanunit_nama", "a.kemasan_besar", "concat('1 ', b.satuanunit_nama, ' = ', a.kemasan_besar, ' ', c.satuanunit_nama) AS konversi"])
                ->from('obatalkes_m a')
                ->join('LEFT JOIN', 'satuanunit_m b', 'b.satuanunit_id = a.satuanbesar_id')
                ->join('LEFT JOIN', 'satuanunit_m c', 'c.satuanunit_id = a.satuankecil_id')
                ->where(['in', 'a.obatalkes_id', $list_obatalkes_id])->all();
            $list_obat_master = ArrayHelper::index($result, 'obatalkes_id');
        }
        return $list_obat_master;
    }

    public function getPrBelumPo($list_obatalkes_id) 
    {
        $result = [];
        $pr_belum_po = [DocoConstants::VAR_BELUM_PO, DocoConstants::VAR_BELUM_APPROVED, DocoConstants::VAR_APPROVED];
        if (!empty($list_obatalkes_id)) {
            $query = PurchaseRequisition::find()->select([
                new Expression('purchasereqdetail_t.obatalkes_id'), 
                new Expression('purchasereqdetail_t.satuankonversi_id'),
                new Expression('SUM(purchasereqdetail_t.qty_input::double precision) as qty_input')
            ])
            ->join('LEFT JOIN','purchasereqdetail_t purchasereqdetail_t', 'purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id')
            ->where(['purchasereq_t.is_consignment' => true])
            ->andWhere(['in', 'purchasereqdetail_t.obatalkes_id', $list_obatalkes_id])
            ->andWhere(['in', 'purchasereqdetail_t.status', $pr_belum_po]) // masih harcode status PR belum dibuat PO
            ->groupBy(['purchasereqdetail_t.obatalkes_id', 'purchasereqdetail_t.satuankonversi_id'])
            ->asArray()->all();

            $result = ArrayHelper::index($query, 'obatalkes_id');
        }
        return $result;
    }

    public function getPoBelumTerima($list_obatalkes_id)
    {
        $result = [];
        $status_po = [DocoConstants::PO_BELUM_DITERIMA, DocoConstants::BELUM_SELESAI_PO];
        $belum_terima = DocoConstants::PO_BELUM_DITERIMA;
        if (!empty($list_obatalkes_id)) {
            $query = ValidasiPoObatDetail::find()->select([
                new Expression('validasipoobatdetail_t.obatalkes_id'),
                new Expression('validasipoobatdetail_t.s_konversiobt_id as satuankonversi_id'),
                new Expression("SUM(CASE WHEN validasipoobat_t.status_penerimaan = {$belum_terima} 
                    then coalesce(validasipoobatdetail_t.qty_sisa::double precision, validasipoobatdetail_t.qty_input::double precision)
                    else validasipoobatdetail_t.qty_input::double precision end) as qty_po")
            ])
            ->join('JOIN','validasipoobat_t validasipoobat_t', 'validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id')
            ->join('JOIN','obatalkes_m obatalkes_m', 'obatalkes_m.obatalkes_id = validasipoobatdetail_t.obatalkes_id')
            ->where(['not', ['validasipoobatdetail_t.purchasereqdetail_id' => null]])
            ->andWhere([
                'obatalkes_m.is_consigment' => true
             ])
            ->andWhere(['in', 'validasipoobat_t.status_penerimaan', $status_po])
            ->andWhere(['in', 'validasipoobatdetail_t.obatalkes_id', $list_obatalkes_id])
            ->groupBy(['validasipoobatdetail_t.obatalkes_id', 'validasipoobatdetail_t.s_konversiobt_id'])
            ->asArray()->all();

            $result = ArrayHelper::index($query, 'obatalkes_id');
        }
        return $result;
    }

    public function listSatuanKonvPr($pr_belum_po)
    {
        $list_konversi = [];
        if (!empty($pr_belum_po)) {
            $obatalkes_id = array_keys($pr_belum_po);
            $konversi = SatuanKonversiView::find()
                ->where(['in', 'obatalkes_id', $obatalkes_id])
                ->asArray()->all();
            
            foreach($konversi as $item) {
                if (
                    in_array($item['obatalkes_id'], $obatalkes_id) && 
                    $item['satuanbesar_id'] == $pr_belum_po[$item['obatalkes_id']]['satuankonversi_id']
                ) {
                    $list_konversi[] = $item;
                }
            }
            $list_konversi = ArrayHelper::index($list_konversi, 'obatalkes_id');
        }
        return $list_konversi;
    }

    public function listSatuanKonvPo($po_belum_terima)
    {
        $list_konversi = [];
        if (!empty($po_belum_terima)) {
            $obatalkes_id = array_keys($po_belum_terima);
            $satuankonversi_id = ArrayHelper::getColumn($po_belum_terima, 'satuankonversi_id');
            $konversi = SatuanKonversiView::find()
                ->where(['in', 'obatalkes_id', $obatalkes_id])
                ->andWhere(['in', 'satuankonversi_id', $satuankonversi_id])
                ->asArray()->all();
            $list_konversi = ArrayHelper::index($konversi, 'obatalkes_id');
        }
        return $list_konversi;
    }
}