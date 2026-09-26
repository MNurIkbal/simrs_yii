<?php

namespace SirsCore\features;

use Yii;
use yii\base\Component;
use yii\base\Model;

use GuzzleHttp\Client;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use SirsCore\models\ObatAlkesPasien;
use SirsCore\models\attributevalidation\TrxTindakan;
use SirsCore\models\attributevalidation\TrxTindakanDetail;
use SirsCore\models\attributevalidation\TrxOa;
use SirsCore\models\attributevalidation\TrxOaDetail;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use Doco\models\InfoStokObatAlkesFnr;
use Doco\models\InfoStokObatAlkesFnrNew;
use Doco\models\Pendaftaran;
use yii\helpers\ArrayHelper;

class FeatureTindakanBmhp
{
    /**
     * @param array of obatalkes data, is_distribusi
     * @return string || false
     * @desc
     */
    public static function createOA($obatalkes_data = [], $is_distribusi = true, $update_stok = true)
    {
        /**
        Validate & Save to Obat Alkes Pasien
        **/

        if(!isset($obatalkes_data['trx_oa']) || empty($obatalkes_data['trx_oa'])){
            throw new \Exception("Tidak Ada Transaksi", 1);
        }
        $trx_oa = $obatalkes_data['trx_oa'];
        $modelOA = new TrxOa;
        $modelOA->attributes = $trx_oa;
        if(!$modelOA->validate()) {
            throw new \Exception("Data Obat Alkes Tidak Sesuai", 1);
        }
        $primaryAttributeModelOa = $modelOA->primary_key;
        $primaryValueModelOa = $trx_oa[$primaryAttributeModelOa];

        $trx_detail = [];
        if(isset($obatalkes_data['trx_oa_detail']) && is_array($obatalkes_data['trx_oa_detail'])) {
            foreach ($obatalkes_data['trx_oa_detail'] as $k_oa => $v_oa) {
                $modelOAdetail = new TrxOaDetail;
                $modelOAdetail->attributes = $v_oa;
                if(!$modelOAdetail->validate()) {
                    throw new \Exception("Data Obat Alkes Detail Tidak Sesuai", 1);
                }

                $infoObat = (new InfoStokObatAlkesFnrNew(['extParam' =>[strval($modelOA->penjamin_id), strval($modelOA->kelaspelayanan_id), $trx_oa['ruangan_id']]]))->find()->where([
                    'obatalkes_id' => $v_oa['obatalkes_id']
                ])->asArray()->one();
                if(empty($infoObat)){
                    throw new \Exception("Data Obat/Alkes Tidak Ada", 1);
                }

                $detailTrans[$v_oa['obatalkes_id']] = [
                    'obatalkes_id' => $v_oa['obatalkes_id'],
                    'satuankecil_id' => ArrayHelper::getValue($infoObat, 'satuankecil_id'),
                    'persendiscount' => $infoObat['persen_disc'],
                    'persenppn' => $infoObat['persen_ppn'],
                    'persenmargin' => $infoObat['persen_margin'],
                    'jmlmargin' => $infoObat['jml_margin'],
                    'jmldiscount' => $infoObat['jml_discount'],
                    'jmlppn' => $infoObat['jml_ppn']
                ];
                $generatedDetail= [
                    'tglpelayanan' => date('Y-m-d H:i:s'),
                    'satuankecil_id' => $infoObat['satuankecil_id'],
                    'hargasatuan_oa' => $modelOAdetail->is_ditagihkan == 1 ? ceil($infoObat['jml_hargajual']) : 0,
                    'hargajual_oa' => $modelOAdetail->is_ditagihkan == 1 ? ceil($infoObat['jml_hargajual']) * $v_oa['qty_oa'] : 0,
                    'harganetto_oa' => $infoObat['jml_harganetto'],
                    'carabayar_id' => $modelOA->carabayar_id,
                    'penjamin_id' => $modelOA->penjamin_id,
                    'pendaftaran_id' => $modelOA->pendaftaran_id,
                    'pasien_id' => $modelOA->pasien_id,
                    'pasienadmisi_id' => $modelOA->pasienadmisi_id,
                    'kelaspelayanan_id' => $modelOA->kelaspelayanan_id,
                    $primaryAttributeModelOa => $primaryValueModelOa,
                    'additional_data' => empty($modelOAdetail->additional_data) ? null : $modelOAdetail->additional_data
                ];
                $trx_detail[] = array_replace($generatedDetail, $obatalkes_data['trx_oa_detail'][$k_oa]);
            }

            $resOA = ObatAlkesPasien::batchInsert($trx_detail);
        }
        $mOA = ObatAlkesPasien::find()->select([
                    'obatalkes_id',
                    'qty_oa AS qty_satuanpakai',
                    'satuankecil_id',
                    'obatalkespasien_id',
                    'harganetto_oa as harganetto',
                    'additional_data'
                ])->where([
                    $primaryAttributeModelOa => $primaryValueModelOa
                ])->asArray()->all();
        $dataOAappended = [];
        foreach($mOA as $k_ins_oa => $v_ins_oa):
            $dataOAtemp = [];
            if(isset($detailTrans[$v_ins_oa['obatalkes_id']])){
                $dataOAtemp = array_merge($v_ins_oa,$detailTrans[$v_ins_oa['obatalkes_id']]);
                $dataOAtemp['jmldiscount'] *= $dataOAtemp['qty_satuanpakai'];
                $dataOAtemp['jmlppn'] *= $dataOAtemp['qty_satuanpakai'];
                $dataOAappended[] = $dataOAtemp;
            }
        endforeach;

        /**
        Set Status Bayar
        **/

        if(!empty($modelOA->pendaftaran_id) && $modelOA->set_tagihan) {
            $statusbayar = DocoConstants::BELUM_LUNAS;
            $res_update_pendaftaran = self::updateStatusBayarPendaftaran($trx_oa['pendaftaran_id'],$statusbayar);
        }

        if($update_stok){
            $transObat = self::stokObatAlkes($dataOAappended, $is_distribusi);
            return $transObat;
        }else {
            return true;
        }
    }

    /**
     * additional @param pendaftaran_id untuk validasi pasien sudah bayar
     */
    public static function stokObatAlkes($dataObat, $is_distribusi, $pendaftaran_id = null)
    {
        if(!empty($pendaftaran_id)) {
            $pendaftaran = Pendaftaran::find()->select(['status_bayar'])->where(['pendaftaran_id' => $pendaftaran_id])->one();
            if($pendaftaran['status_bayar'] == DocoConstants::LUNAS) {
                throw new \Exception("Pasien Sudah Melakukan Pembayaran. Tidak Bisa Menambah Obat", 1);
            }
        }

        LogicStokObatAlkes::$distribusi = $is_distribusi;
        /**
        Get Metode Antrian From Konfig Farmasi
        **/

        $currentMetode = LogicStokObatAlkes::FEFO;
        if ($metodekonfig = self::getKonfigFarmasi()) {
            $currentMetode = strtoupper($metodekonfig);
        }

        $stockMinus = self::getKonfigFarmasi('is_transaksiobat_0');

        /**
        Set Bisnis Logic From Konfig Farmasi
        **/

        $tanggalPemakaian = date('Y-m-d H:i:s');
        if ($currentMetode === LogicStokObatAlkes::FEFO) {
           $methode = LogicStokObatAlkes::methodeFEFO($dataObat, $tanggalPemakaian, false, false, $stockMinus);
        } else {
           $methode = LogicStokObatAlkes::methodeFIFO($dataObat, $tanggalPemakaian, false, false, $stockMinus);
        }

        if (is_array($methode)) {
            return $methode;
        }

        return true;
    }

    /**
     * @param array of tindakan
     * @return string || false
     * @desc
     */
    public static function createTindakan($tindakan_data = [])
    {
        /**
        Validate & Save to Tindakan Pelayanan
        **/
        if(!isset($tindakan_data['trx_tindakan']) || empty($tindakan_data['trx_tindakan'])){
            return ['message'=>'Tidak Ada Transaksi Tindakan'];
        }
        $trx_tindakan = $tindakan_data['trx_tindakan'];
        $modelTindakan = new TrxTindakan;
        $modelTindakan->attributes = $trx_tindakan;
        if(!$modelTindakan->validate()){
            return ['message'=>'Data Tindakan Tidak Sesuai'];
        }
        $primaryAttributeModelTindakan = $modelTindakan->primary_key;
        $primaryValueModelTindakan = $trx_tindakan[$primaryAttributeModelTindakan];

        if(isset($tindakan_data['trx_tindakan_detail']) && is_array($tindakan_data['trx_tindakan_detail']) && count($tindakan_data['trx_tindakan_detail'])>0){
            foreach ($tindakan_data['trx_tindakan_detail'] as $k_tindakan => $v_tindakan) {
            }
        }
            // batch insert
    }

    /**
     * @param column name
     * @return string || false
     * @desc
     */
    private static function getKonfigFarmasi($keyword = null)
    {
        if($keyword == null) {
            $keyword = 'metodeantrian';
        }

        $tanggalBerlaku = date('Y-m-d');
        $konfig = Yii::$app->db->createCommand("
            SELECT " . $keyword . " FROM konfigfarmasi_k
            WHERE tglberlaku >= '{$tanggalBerlaku}'
            AND konfigfarmasi_aktif = true
            AND is_active = true
        ")->queryOne();
        return isset($konfig[$keyword]) ? $konfig[$keyword] : false;
    }

    /**
     * @param pendaftaran_id
     * @return boolean
     * @desc
     */
    private static function updateStatusBayarPendaftaran($pendaftaran_id, $statusbayar)
    {
        $res = Yii::$app->db->createCommand("
            UPDATE pendaftaran_t SET status_bayar = {$statusbayar}
            WHERE pendaftaran_id = {$pendaftaran_id}
        ")->execute();
        return $res;
    }

    public static function tindakanBmhp(array $data, $distribusi = true)
    {
        if (!isset($data['detail_trans']) && !isset($data['list_obat']))
            throw new \Exception("Data Obat/Alkes Tidak Ada", 1);

        $ruanganId = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : Yii::$app->jwt->ruangan_id;
        $modelOA = new TrxOa;
        $modelOA->attributes = isset($data['header']) ? $data['header'] : [];

        $infoObat = (new InfoStokObatAlkesFnr(['extParam'=>[
            $modelOA->penjamin_id,
            $modelOA->kelaspelayanan_id,
            $ruanganId
        ]]))->find()
        ->andWhere([
            'obatalkes_id' => $data['list_obat']
        ])->asArray()->all();

        if (empty($infoObat))
            throw new \Exception("Data Obat/Alkes Tidak Ada", 1);

        $listHargaObat = [];
        foreach ($infoObat as $value) {
            $obatId = $value['obatalkes_id'];
            $listHargaObat[$obatId] = $value;
        }

        /**
         * Mapping harga jugal
         */
        $stokObat = [];
        foreach ($data['detail_trans'] as $value) {
            $obatId = $value['obatalkes_id'];
            $row = $value;
            $row['harganetto'] = $listHargaObat[$obatId]['jml_harganetto'];
            $row['persendiscount'] = $listHargaObat[$obatId]['persen_disc'];
            $row['persenppn'] = $listHargaObat[$obatId]['persen_ppn'];
            $row['persenmargin'] = $listHargaObat[$obatId]['persen_margin'];
            $row['jmldiscount'] = $listHargaObat[$obatId]['jml_discount'];
            $row['jmlmargin'] = $listHargaObat[$obatId]['jml_margin'];
            $row['jmlppn'] = $listHargaObat[$obatId]['jml_ppn'];
            $stokObat[] = $row;
        }

        $stokObat = self::stokObatAlkes($stokObat, $distribusi);

        /**
         * Update status pembayaran pasien
         */
        if(!empty($modelOA->pendaftaran_id)){
            $statusbayar = DocoConstants::BELUM_LUNAS;
            $res_update_pendaftaran = self::updateStatusBayarPendaftaran($modelOA->pendaftaran_id,$statusbayar);
        }
    }

    public static function hapusTindakanBmhp($items = [], $alasan_batal = null)
    {
        $items = !is_array($items) ? (array) $items : $items;
        (new ObatAlkesPasien)->delete([
                            'obatalkespasien_id' => $items
                        ],$alasan_batal);
        return LogicStokObatAlkes::returnObatPasien($items);
    }

    public static function hapusStokBmhp($items = [])
    {
        $items = !is_array($items) ? (array) $items : $items;
        return LogicStokObatAlkes::returnObatPasien($items);
    }
}
