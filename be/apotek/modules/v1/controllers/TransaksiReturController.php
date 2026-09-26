<?php

/**
 * @author Randy Vianda Putra
 * @todo Trasaksi Retur
 * @copyright 8 Maret 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoPenjualanResep;
use app\modules\v1\models\InfoPenjualanResepDetailView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\ReturResep;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\DetailReturResepView;
use Doco\components\DocoPrint;

class TransaksiReturController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InformasiReseptur';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        $custom_actions = [
            'retur' => 'app\modules\v1\actions\TransaksiRetur\ReturAction',
        ];
        $actions = array_merge($actions, $custom_actions);
        return $actions;
    }

    public function init()
    {
        // $this->konfig_farmasi = DocoConstants::konfigFarmasi();
        parent::init();
    }

    /**
     * @todo get all data resep
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer penjualan id
     * @param string no_resep
     * @param integer tipe penjualan
     */
    private function getPenjualan($penjualan_id = null, $no_resep = null, $type = null)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT noresep, tglpenjualan, nama_pasien, nama_karyawan, nama_pembeli, (biayaadministrasi + totalhargajual) as totalhargajual, jenis_penjualan, no_rekam_medik
            FROM infopenjualanresep_v
        ";

        if ($penjualan_id) {
            $sql .= " WHERE penjualanresep_id = {$penjualan_id}";
        }

        if ($no_resep) {
            $sql .= " AND noresep = '{$no_resep}'";
        }

        if ($type) {
            $sql .= " AND jenispenjualan = {$type}";
        }

        $data = $connection->createCommand($sql)->queryOne();

        return $data;
    }

    /**
     * @todo get all data with ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionAjax()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $data_penjualan = $data_detail_penjualan = [];
            if (isset($get)) {
                $data_penjualan = InfoPenjualanResep::find()->where(['noresep' => $get['no_resep']])->one();
                $detail_penjualan = InfoPenjualanResepDetailView::find()->where([
                    'noresep' => $get['no_resep']
                ])->andWhere('qty_oa > 0')->orderBy([
                    'racikan_id' => SORT_DESC,
                    'rke' => SORT_DESC
                ])->asArray()->all();
                foreach ($detail_penjualan as $key => $value) {
                    $additional_data = json_decode($value['additional_data'], true);
                    $data_detail_penjualan[] = [
                        'noresep' => $value['noresep'],
                        'tglpenjualan' => $value['tglpenjualan'],
                        'jenispenjualan' => $value['jenispenjualan'],
                        'totalhargajual' => $value['totalhargajual'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'reseptur_id' => $value['reseptur_id'],
                        'qty_oa' => $value['qty_oa'],
                        'det' => $value['det'],
                        'det_konversi' => $value['det_konversi'],
                        'signa_oa' => $value['signa_oa'],
                        'hargajual_oa' => $value['hargajual_oa'],
                        'harganetto_oa' => $value['harganetto_oa'],
                        'hargasatuan_oa' => $value['hargasatuan_oa'],
                        'obatalkespasien_id' => $value['obatalkespasien_id'],
                        'resepturdetail_id' => $value['resepturdetail_id'],
                        'rke' => $value['rke'],
                        'racikan_id' => $value['racikan_id'],
                        'satuan_input' => $additional_data['satuan_input'],
                        'penjualanresep_id' => $value['penjualanresep_id'],
                        'qty_retur' => 0,
                        'qty_medis' => ceil($value['qty_medis']),
                        'det_medis' => ceil($value['det_medis'])
                    ];
                }
            }

            return [
                'data-penjualan' => $data_penjualan,
                'data-detail-penjualan' => $data_detail_penjualan
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo save retur
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSave($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->jwt->user->loginpemakai_id;
            $request = Yii::$app->request;
            $total_retur = 0;
            $data_insert = $data_update_oapasien = [];
            $ruangan_id = Yii::$app->jwt->ruangan_id;

            $penjualanResep = PenjualanResep::find()->where(['penjualanresep_id'=>$id])->one();
            if(is_null($penjualanResep))
                throw new \Exception("Resep Tidak Ditemukan", 1);

            if($penjualanResep->status_bayar == DocoConstants::LUNAS)
                throw new \Exception("Penjualan Resep Telah Dibayar", 1);

            if($penjualanResep->status_reseptur == DocoConstants::VAR_B_R)
                throw new \Exception("Penjualan Resep Telah Dibatalkan", 1);

            $list_retur  = $request->post('list_retur','{}');
            $list_retur = json_decode($list_retur,true);
            if (!empty($list_retur)) {
                foreach ($list_retur as $key => $value) {
                    if (empty($value['qty_retur'])) continue;
                    $total_retur += $value['hargasatuan_oa'] * $value['qty_retur'];
                    $data_insert[$value['obatalkespasien_id']] = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkespasien_id' => $value['obatalkespasien_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        // 'tglkadaluarsa' => !empty($value['tglkadaluarsa']) ? date('Y-m-d',strtotime($value['tglkadaluarsa'])) : null,
                        'nobatch' => !empty($value['nobatch']) ? $value['nobatch'] : null,
                        'tglstok_in' => $now,
                        'qtystok_in' => $value['qty_retur'],
                        'stokoa_aktif' => true,
                        'satuankecil_id' => $value['satuankecil_id'],
                        // 'stokobatalkesasal_id' => $value['stokobatalkesasal_id'],
                        'created_by' => $user_login,
                        'returresepdetail_id' => null,
                        'qtystok_out' => 0,
                        'harganetto' => $value['harganetto'],
                        'persendiscount' => $value['persendiscount'],
                        'jmldiscount' => $value['jmldiscount'],
                        'persenppn' => $value['persenppn'],
                        'jmlppn' => $value['jmlppn'],
                        'persenmargin' => $value['persenmargin'],
                        'jmlmargin' => $value['jmlmargin'],
                    ];
                    $data_update_oapasien[$value['obatalkespasien_id']] = [
                        'obatalkespasien_id' => $value['obatalkespasien_id'],
                        'qty_oa' => ($value['qty_oa'] - $value['qty_retur']),
                        'hargajual_oa' => ($value['qty_oa'] - $value['qty_retur']) * $value['hargasatuan_oa']
                    ];
                }
            }

            $model = new ReturResep;
            $model->ruangan_id = $ruangan_id;
            $model->penjualanresep_id = $id;
            $model->tgl_retur = $now;
            $model->total_retur = $total_retur;
            $model->created_by = $user_login;

            if ($model->save()) {
                $penjualanResep->totalhargajual = $penjualanResep->totalhargajual - $total_retur;
                if(!$penjualanResep->save())
                    throw new \Exception("Total Tagihan Gagal Update", 1);

                $returresep_id = $model->returresep_id;
                $obj_array_insert = [];
                foreach ($list_retur as $key => $value) {
                    if (!empty($value['qty_retur'])) {
                        $obj_array_insert[] = [
                            'obatalkespasien_id' => $value['obatalkespasien_id'],
                            'hargasatuan' => $value['hargasatuan_oa'],
                            'qty_retur' => $value['qty_retur'],
                            'returresep_id' => $returresep_id,
                            'created_by' => $user_login
                        ];
                    }
                }

                ApotekComponent::insertMultiple('returresepdetail_t', $obj_array_insert);
                $data = $connection->createCommand("
                    SELECT
                        returresepdetail_id,
                        obatalkespasien_id
                    FROM returresepdetail_t
                    WHERE returresep_id = {$returresep_id}
                ")->queryAll();

                foreach ($data as $value) {
                    if (isset($data_insert[$value['obatalkespasien_id']])) {
                        $data_insert[$value['obatalkespasien_id']]['returresepdetail_id'] = $value['returresepdetail_id'];
                    }
                }

                if(is_array($data_update_oapasien)){
                    foreach ($data_update_oapasien as $_update_oapasien) {
                        $oapasien = ObatAlkesPasien::find()->where(['obatalkespasien_id'=>$_update_oapasien['obatalkespasien_id']])->one();
                        if(is_null($oapasien)) continue;
                        $oapasien->attributes = $_update_oapasien;
                        if($oapasien->qty_oa == 0)
                            $oapasien->is_deleted = TRUE;
                        $oapasien->save();
                    }
                }

                StokObatAlkes::batchInsert($data_insert);

                $totalOA = ObatAlkesPasien::find()->where(['penjualanresep_id'=>$id])->sum('hargajual_oa');
                $jumlahQtyOA = ObatAlkesPasien::find()->where(['penjualanresep_id'=>$id])->sum('qty_oa');
                if((is_null($totalOA) || $totalOA == 0) && (is_null($jumlahQtyOA) || $jumlahQtyOA == 0)){
                    $penjualanResep->biayaadministrasi = 0;
                    $penjualanResep->is_deleted = TRUE;
                    if(!$penjualanResep->save())
                        throw new \Exception("Tidak Bisa Menghapus Penjualan Resep", 1);
                }

                $transaction->commit();

                // comment this slow query and not usefull queries
                // $data_detail_penjualan = ApotekComponent::getDetailReturByPenjualan($id);
                $data_retur = $connection->createCommand("
                    SELECT
                        no_returresep
                    FROM returresep_t
                    WHERE returresep_id = {$returresep_id}
                ")->queryOne();
                return [
                    'message' => 'Data Berhasil di simpan',
                    'id_retur' => DocoHelpers::encrypt($returresep_id),
                    'data_retur' => [],
                    'no_returresep' => $data_retur['no_returresep']
                ];
            } else {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'data' => $model->errors
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #ruangan_retur# => Menampilkan Ruangan
    * @attribute #no_retur# => Menampilkan No Retur
    * @attribute #no_resep# => Menampilkan No Resep
    * @attribute #nama# => Menampilkan Nama Pembeli
    * @attribute #tanggal_retur# => Menampilkan tanggal retur
    * @attribute #jenis_penjualan# => Menampilkan jenis penjualan
    * @attribute #tanggal_resep# => Menampilkan tanggal resep
    * @attribute #tabel_retur# => Menampilkan detail retur
    **/
    public function actionCetakPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        if (isset($get['id'])) {
            $id = $get['id'];
            $retur_obat_detail = DetailReturResepView::find()->where(['returresep_id' => $id])->asArray()->all();
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()
            ->select(['ruangan_id', 'ruangan_nama'])
            ->where([
                'ruangan_id' => $ruangan_id
            ])->one();
            $print = new DocoPrint();
            $print->attributes = [
                '#ruangan_retur#' => isset($ruangan['ruangan_nama']) ? $ruangan['ruangan_nama'] : '-',
                '#title_nomor#' => !empty(current($retur_obat_detail)['noresep']) ? "No Resep" : "No Pendaftaran",
                '#tanggal_retur#' => !empty(current($retur_obat_detail)['tgl_retur']) ? date('d M Y H:i', strtotime(current($retur_obat_detail)['tgl_retur'])) : '-',
                '#tanggal_resep#' => !empty(current($retur_obat_detail)['tglresep']) ? date('d M Y', strtotime(current($retur_obat_detail)['tglresep'])) : '-',
                '#no_retur#' => !empty(current($retur_obat_detail)['no_returresep']) ? current($retur_obat_detail)['no_returresep'] : '-',
                '#no_resep#' => !empty(current($retur_obat_detail)['noresep']) ? current($retur_obat_detail)['noresep'] : current($retur_obat_detail)['no_pendaftaran'],
                '#jenis_penjualan#' => !empty(current($retur_obat_detail)['jenis_penjualan']) ? current($retur_obat_detail)['jenis_penjualan'] : '-',
                '#nama#' => isset(current($retur_obat_detail)['nama_pasien']) ? current($retur_obat_detail)['nama_pasien'] : '-',
                '#tabel_retur#' => $this->renderPartial('index', [
                    'detail' => $retur_obat_detail,
                    'is_retur_pendaftaran' => current($retur_obat_detail)['is_retur_pendaftaran']
                ]),
            ];
            $print->Output();
        }
    }

}
