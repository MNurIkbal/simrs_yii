<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-24 11:52:02
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-01 15:10:58
 */

namespace app\components;

use yii\db\QueryBuilder;
use app\components\QueryApotek;
use yii;

use app\modules\v1\models\InfoPenjualanResep;
use app\modules\v1\models\InfoPenjualanResepDetailView;

use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\Pendaftaran;

use app\components\ApotekComponent;

use Doco\components\DocoHelpers;
use Doco\components\DocoAkunting;
use Doco\components\DocoConstants;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use SirsCore\features\FeatureTindakanBmhp;
use SirsCore\features\IntegrasiAkunting;

class CopyResepComponent
{
     public function copyResep($penjualanresep_id, $noresep, $pendaftaran_id, $dataObat){
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $newVal = [];
            $checkObat = ApotekComponent::checkStok($dataObat);
            if($checkObat['status'] != 422){
                $saveResep = CopyResepComponent::saveResep($penjualanresep_id, $noresep, $pendaftaran_id);
                $data_resep = json_decode($saveResep,true);
                $newId = $data_resep['penjualanresep_id'];
                $newIter = $data_resep['iter'];
                $saveObatalkes = CopyResepComponent::saveOA($penjualanresep_id, $pendaftaran_id, $newId, $data_resep);
                if($saveObatalkes){
                    $transaction->commit();
                    if($newIter < 1){
                        return $response['response'] = [
                                    'title' => 'Proses Gagal !',
                                    'text' => 'Iter Tidak Mencukupi',
                                    'status' => 422
                                ];
                    }
                    return $response['response'] = [
                                'status' => 200,
                                'title' => 'Proses Berhasil !',
                                'text' => 'Copy Resep Berhasil.',
                            ];
                }
            }else{
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Stok Obat tidak memenuhi.',
                            'status' => 422
                        ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage(), 1);
        }

     }

     public function saveResep($penjualanresep_id, $noresep, $pendaftaran_id)
     {
        // try {
            $where = 'where ';
            $options = [];
            if(!empty($penjualanresep_id)){
                $where .= "penjualanresep_id = '{$penjualanresep_id}'";
                $options['penjualanresep_id'] = $penjualanresep_id;
            }
            if(!empty($noresep)){
                $where .= " and noresep = '{$noresep}'";
                $options['noresep'] = $noresep;
            }
            if(!empty($pendaftaran_id)){
                $where .= "and pendaftaran_id = '{$pendaftaran_id}'";
                $options['pendaftaran_id'] = $pendaftaran_id;
            }
            $model = new PenjualanResep;
            $resData = $model::findOne($options);
            $getData = $resData->attributes;

            $newIter = $getData['iter'] - 1;
            foreach ($getData as $key => $value) {
                if(empty($value) && $key != 'rke'){
                    unset($getData[$key]);
                }
                $getData['tglpenjualan'] = date('Y-m-d H:i:s');
                unset($getData['noresep']);
                unset($getData['penjualanresep_id']);
                unset($getData['created_date']);
                unset($getData['deleted_date']);
                unset($getData['last_modified_date']);
                unset($getData['iter']);
            }
            $resData->iter = $newIter;
            $new_model = new PenjualanResep;
            foreach ($getData as $key => $value) {
                if($key != 'iter'){
                    $new_model->isNewRecord = true;
                    $new_model->$key = $value;
                }
            }
            unset($new_model->penjualanresep_id);
            unset($new_model->created_date);
            unset($new_model->last_modified_date);
            unset($new_model->deleted_date);
            unset($new_model->is_deleted);
            unset($new_model->is_active);
            unset($new_model->noresep);
            if($new_model->validate()){
                if($new_model->save()){
                    $id = $new_model->penjualanresep_id;
                    if($resData->save()){
                        $val = [
                            'penjualanresep_id'=>$id,
                            'iter'=>$newIter,
                            'carabayar_id' => $new_model->carabayar_id,
                            'penjamin_id' => $new_model->penjamin_id,
                            'ruangan_id' => $new_model->ruangan_id,
                        ];
                        return json_encode($val);
                    }
                }else{
                    throw new \Exception("Terjadi kesalahan", 1);

                }
            }else{
                throw new \Exception("Terjadi kesalahan", 1);
            }
        // } catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
     }

     public function saveObatalkes($penjualanresep_id, $pendaftaran_id, $new_penjualanresepid)
     {
        try{
            $where = 'where ';
            if(!empty($penjualanresep_id)){
                $where .= "penjualanresep_id = '{$penjualanresep_id}'";
            }
            if(!empty($pendaftaran_id)){
                $where .= "and pendaftaran_id = '{$pendaftaran_id}'";
            }
            $getData = \Yii::$app->db->createCommand("select * from obatalkespasien_t {$where}")->queryAll();
            $ruangan_id = '';
            foreach ($getData as $key => $value) {
                    foreach ($getData[$key] as $key_val => $value_val) {
                        if(empty($ruangan_id)){
                            $ruangan_id = $getData[$key]['ruangan_id'];
                        }
                        if(empty($value_val) && $key_val != 'rke'){
                            unset($getData[$key][$key_val]);
                        }
                        unset($getData[$key]['obatalkespasien_id']);
                        unset($getData[$key]['created_date']);
                        unset($getData[$key]['created_date']);
                        unset($getData[$key]['deleted_date']);
                        unset($getData[$key]['last_modified_date']);
                        $getData[$key]['penjualanresep_id'] = $new_penjualanresepid;
                        $getData[$key]['tglpelayanan'] = date('Y-m-d H:i:s');
                    }
            }
            ApotekComponent::insertMultiple('obatalkespasien_t', $getData);
            foreach ($getData as $key => $value) {
                $column_select_obat_pasien = ['obatalkespasien_id', 'obatalkes_id', 'qty_oa'];
                $conditions_obat_pasien = [
                    'penjualanresep_id' => $new_penjualanresepid,
                    'obatalkes_id' => $value['obatalkes_id']
                ];
                $get_obat_pasien = ApotekComponent::selectOne('obatalkespasien_t', $column_select_obat_pasien, $conditions_obat_pasien);
                if($get_obat_pasien){
                    ApotekComponent::updateStok($ruangan_id, $get_obat_pasien);
                }
            }
            return true;
        }catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

     }

     public function saveOA($penjualanresep_id, $pendaftaran_id, $new_penjualanresepid, $data_resep)
     {
        // try{
            $where = 'where ';
            if(!empty($penjualanresep_id)){
                $where .= "penjualanresep_id = '{$penjualanresep_id}'";
            }
            if(!empty($pendaftaran_id)){
                $where .= "and pendaftaran_id = '{$pendaftaran_id}'";
            }           
            $source_data = \Yii::$app->db->createCommand("select * from obatalkespasien_t {$where}")->queryAll();
            $ruangan_id = '';

            foreach ($source_data as $k_source => $v_source) {
                    $trx_oa_detail[] = [
                        'ruangan_id' => @$v_source['ruangan_id'],
                        'carabayar_id' => @$v_source['carabayar_id'],
                        'penjamin_id' => @$v_source['penjamin_id'],
                        'pegawai_id' => @$v_source['pegawai_id'],
                        'satuankecil_id' => @$v_source['satuankecil_id'],
                        'racikan_id' => @$v_source['racikan_id'],
                        'obatalkes_id' => @$v_source['obatalkes_id'],
                        'penjualanresep_id' => $new_penjualanresepid,
                        'tglpelayanan' => date('Y-m-d H:i:s'),
                        'qty_oa' => @$v_source['qty_oa'],
                        // 'hargasatuan_oa' => @$v_source['hargasatuan_oa'],
                        // 'harganetto_oa' => @$v_source['harganetto_oa'],
                        // 'hargajual_oa' => @$v_source['hargajual_oa'],
                        'signa_oa' => @$v_source['signa_oa'],
                        'created_by' => @$v_source['created_by'],
                        'is_ditagihkan' => true
                    ];
            }
            $trx_oa = [
                    'primary_key' => 'penjualanresep_id',
                    'pendaftaran_id' => $pendaftaran_id,
                    'penjualanresep_id' => $new_penjualanresepid,
                    'carabayar_id' => $data_resep['carabayar_id'],
                    'penjamin_id' => $data_resep['penjamin_id'],
                    'ruangan_id' => $data_resep['ruangan_id'],
                ];
            $obj_oa = [
                'trx_oa' => $trx_oa,
                'trx_oa_detail' => $trx_oa_detail
            ];
            $return_feature = FeatureTindakanBmhp::createOA($obj_oa,false);
            if(!$return_feature){
                throw new \Exception("Tidak Dapat Memproses Transaksi Obat Alkes", 1);
            }
            return true;
        // }catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
     }

     public function deleteResep($id){
            $data_obatalkespasien = Yii::$app->db->createCommand('select obatalkespasien_id from obatalkespasien_t where penjualanresep_id = '.$id.' and is_deleted = false')->queryAll();
            $newVal = [];
            if(!empty($data_obatalkespasien)){
                foreach ($data_obatalkespasien as $key => $value) {
                    $newVal[] = $value['obatalkespasien_id'];
                }
                $data_obatalkespasien = $newVal;

                //edited by Rizqi Fitrianto
                //change action delete resep
                //14-03-2018
                $obatId = implode(',', $newVal);
                $data_stokoa = Yii::$app->db->createCommand("select * from stokobatalkes_t where obatalkespasien_id IN ({$obatId}) and is_deleted = false")->queryAll();
                $data_stoknew = [];
                if(!empty($data_stokoa)){
                    foreach ($data_stokoa as $key => $value) {
                        foreach ($data_stokoa[$key] as $xkey => $xvalue) {
                            unset($data_stokoa[$key]['stokobatalkes_id']);
                            unset($data_stokoa[$key]['stok']);
                            if($xkey == 'tglstok_out'){
                                $data_stokoa[$key]['tglstok_in'] = $data_stokoa[$key]['tglstok_out'];
                                unset($data_stokoa[$key]['tglstok_out']);
                            }
                            if($xkey == 'qtystok_out'){
                                $data_stokoa[$key]['qtystok_in'] = $data_stokoa[$key]['qtystok_out'];
                                unset($data_stokoa[$key]['qtystok_out']);
                            }
                            $xval = $data_stokoa[$key];
                        }
                        $data_stoknew[$key] = $xval;
                    }
                }
            }
            $resep = PenjualanResep::find()->where(['penjualanresep_id' => $id])->asArray()->one();
            // $integrate = CopyResepComponent::integrate($resep['noresep'], 'DELETE');
            $delete_penjualan = (new PenjualanResep)->delete($id);
            $delete_obatalkespasien = (new ObatAlkesPasien)->delete(['penjualanresep_id'=>$id]);
            if(!empty($data_obatalkespasien)){
                // $delete_stokoa = (new StokObatAlkes)->delete(['obatalkespasien_id'=>$data_obatalkespasien]);
            }
            if(!empty($data_stoknew)){
                $insert_stokoa = ApotekComponent::insertMultiple('stokobatalkes_t', $data_stoknew);
            }
            if($delete_penjualan){
                $return_integrate = IntegrasiAkunting::integrateRevertByNoResep($resep['noresep'], 'DELETE'); // $this->integrate($noresep);
                return "done";
            }
    }


    /**
     * @Author: Arif Priatna
     * @Date:   2019-02-04
     * @Add by:   Arif Priatna
     * @todo: action for Integration with accounting, params needed
     * id_resep and method [EDIT / DELETE]
     */

    public function integrate($noresep, $method)
    {
        try{
            $resep = SyncPengeluaranobat::find()->where(['nomor' => $noresep])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                foreach($resep as $key => $value){
                    if($value->jenis_transaksi == "OBAT"){
                        $harga = $value->qty * $value->harga;
                    }else{
                        $harga = $value->harga;
                    }

                    $data[] = [
                        'xtransaction_type' => $value->jenis_transaksi,
                        'xcompany_id' => 1,
                        'xinstalasi_id' => $value->instalasi_id,
                        'xruangan_id' => $value->ruangan_id,
                        'xref_number_id' => $value->id,
                        'xref_number' => $value->no_pendaftaran,
                        'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                        'xcategori_code' => $value->jenisobatalkes_kode,
                        'xtransaction_at' => $value->tgl_transaksi,
                        'xamount' => $value->harga,
                        'xdiscount_amount' => $value->discount,
                        'xamount_netto' => $value->harga_netto,
                        'xamount_ppn' => ($value->jmlppn != null) ? $value->jmlppn : 0,
                        'xmedical_number' => $value->no_rekam_medik,
                        'xnotes' => $value->uraian,
                        'xis_billing' => $value->is_ditagihkan,
                        'xcrud' => $method,
                    ];
                }
                $var = DocoAkunting::api('POST', 'integrations_crud', $data);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);

        }
    }
}

