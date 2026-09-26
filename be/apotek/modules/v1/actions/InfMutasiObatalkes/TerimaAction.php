<?php
namespace app\modules\v1\actions\InfMutasiObatalkes;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\entities\MutasiObat;
use app\modules\v1\entities\MutasiObatDetail;
use app\modules\v1\entities\TerimaMutasi;
use app\modules\v1\models\TerimaMutasiObat;
use app\modules\v1\models\TerimaMutasiObatDetail;

class TerimaAction extends Action
{
    public function run($nomutasioa)
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $post = $post['PenerimaanObatForm'];
        $Mutasi = MutasiObat::getByNoMutasi($nomutasioa);

        if (!$Mutasi) {
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Mutasi obat tidak ditemukan',
                'text' => 'Gagal Validasi Data'
            ];
        } else if ($Mutasi->status_mutasi == DocoConstants::STATUS_MUTASI_DITERIMA) {
            \Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Penerimaan mutasi dengan no. '.$nomutasioa.' sudah dilakukan.',
                'text' => 'Gagal Validasi Data'
            ];
        }

        $mutasiobatruangan_id = $Mutasi->mutasiobatruangan_id;

        $model = new TerimaMutasiObat;
        $transaction = $model->getDb()->beginTransaction();
        try {
            $model->mutasiobatruangan_id = $mutasiobatruangan_id;
            $model->tglterima = date("Y-m-d", strtotime($post['tglterima']));
            $model->noterimamutasi = '1';
            $model->totalharganetto = 0;
            $model->totalhargajual = 0;
            $model->keterangan_terima = null;
            $model->ruanganpenerima_id = $Mutasi->ruangantujuan_id;
            $model->ruanganasal_id = $Mutasi->ruanganasal_id;
            $model->pegawaipenerima_id = $post['pegawai_mengetahui'];
            $model->pegawaimengetahui_id = $post['pegawai_mengetahui'];
            $_POST['ruangan_id'] = $Mutasi->ruanganasal_id;
            $_POST['ruangan_penerima_id'] = $Mutasi->ruangantujuan_id;

            if(!$model->save()){
                throw new \yii\db\Exception('Gagal Simpan Terima Mutasi', $model->getErrors(),500);
            }

            $terimamutasiobat_id = $model->getPrimaryKey();

            $dataobat = MutasiObatDetail::getListById($mutasiobatruangan_id);

            if(count($dataobat) == 0) throw new \yii\base\ErrorException("Tidak Ada Data Detail Mutasi Obat", 500);

            $sum_harganetto = $sum_hargajual = 0;

            $konfig = "SELECT hargaygdigunakan FROM konfigfarmasi_k LIMIT 1";
            $konfigFarmasi = \Yii::$app->db->createCommand($konfig)->queryOne();

            // $this->_config = $konfigFarmasi;
            $batchInsert = $stokIn = $stokOut = [];


            foreach ($dataobat as $d_obat) {
                $harga_netto_ = $d_obat['harganetto'];
                $harga_jual_ = $this->controller->getHargaJual($d_obat,$konfigFarmasi);

                $harga_netto_terima = $harga_netto_ * $d_obat['jumlah_mutasi'];
                $harga_jual_terima = $harga_jual_ * $d_obat['jumlah_mutasi'];
                $sum_hargajual += $harga_jual_terima;
                $sum_harganetto += $harga_netto_terima;
                // prepare untuk nginsert ke penerimaan detail
                $batchInsert[] = [
                    'terimamutasiobat_id' => $terimamutasiobat_id,
                    'mutasiobatdetail_id' => $d_obat['mutasiobatdetail_id'],
                    'satuankecil_id' => $d_obat['satuankecil_id'],
                    'ruangan_id' => $d_obat['ruangan_asal_id'],
                    'obatalkes_id' => $d_obat['obatalkes_id'],
                    'jmlmutasi' => $d_obat['jumlah_mutasi'],
                    'jmlterima' => $d_obat['jumlah_mutasi'],
                    'harganettoterima' => $harga_netto_terima,
                    'hargajualterima' => $harga_jual_terima
                ];

                $stokIn[$d_obat['mutasiobatdetail_id']] = [
                    'ruangan_id' => $d_obat['ruangan_tujuan_id'],
                    'obatalkes_id' => $d_obat['obatalkes_id'],
                    'terimamutasidetail_id' => null,
                    'mutasiobatdetail_id' => $d_obat['mutasiobatdetail_id'],
                    'qty_satuanpakai' => $d_obat['jumlah_mutasi'],
                    'satuankecil_id' => $d_obat['satuankecil_id'],
                    'kadaluarsa' => @$d_obat['expired']
                ];
            }

            uasort($stokIn, function($a,$b){
                return strtotime($a['kadaluarsa']) - strtotime($b['kadaluarsa']);
            });

            $tanggalBerlaku = date('Y-m-d H:i:s');

            TerimaMutasiObatDetail::batchInsert($batchInsert,false);

            $query = Yii::$app->db->createCommand("
                SELECT obatalkes_id, terimamutasiobatdetail_id, mutasiobatdetail_id FROM terimamutasiobatdetail_t
                WHERE terimamutasiobat_id = {$terimamutasiobat_id}
            ")->queryAll();

            $listTerimaMutasiDetail = ArrayHelper::getColumn($query,'terimamutasiobatdetail_id');

            foreach ($query as $key => $value) {
                $id_parent = $value['terimamutasiobatdetail_id'];
                $id_mutasi_detail = $value['mutasiobatdetail_id'];
                if (isset($stokIn[$id_mutasi_detail])) {
                    $stokIn[$id_mutasi_detail]['terimamutasidetail_id'] = $id_parent;
                }
            }

            // //cek expire
            $isMutasiLangsung = FALSE;
            foreach ($dataobat as $v_dataobat) {
                if(is_null($v_dataobat['expired'])){
                    $isMutasiLangsung = TRUE;
                }
            }
            if($isMutasiLangsung){
                $stockCheck = TerimaMutasi::checkStockIn($Mutasi->ruanganasal_id,$stokIn);

                if(count($stockCheck) > 0){
                    $listObatGagal = ArrayHelper::getColumn($stockCheck,'obatalkes_nama');
                    $transaction->rollBack();
                    \Yii::$app->response->statusCode = 500;
                    return [
                        'message' => 'Stok Obat '.implode(',', $listObatGagal).' tidak mencukupi.',
                        'text' => 'Data Gagal Disimpan',
                        'status' => 500,
                        'title' => 'Proses Gagal!'
                    ];
                }

                TerimaMutasi::langsung($stokIn);

                TerimaMutasi::verifyStock($listTerimaMutasiDetail);
            }else{
                TerimaMutasi::ByExpire($stokIn);
            }

            $model->totalharganetto = $sum_harganetto;
            $model->totalhargajual = $sum_hargajual;
            if(!$model->update()){
                throw new \yii\db\Exception('Gagal simpan harga netto dan harga jual', $model->getErrors(),500);
            }

            $status_terima = DocoConstants::STATUS_MUTASI_DITERIMA;

            Yii::$app->db->createCommand("
                UPDATE mutasiobatruangan_t SET status_mutasi = {$status_terima}
                WHERE mutasiobatruangan_id = {$mutasiobatruangan_id}
            ")->execute();
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => json_encode($e->getMessage()),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=> $e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }catch(\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=> json_encode($e->getMessage()),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }
        $mTerima = TerimaMutasiObat::findOne($model->getPrimaryKey());
        if($mTerima){
            $no_terimamutasi = $mTerima['noterimamutasi'];
            $msg_simpan_mutasi = "Mutasi berhasil diterima dengan no: ".$no_terimamutasi;
        }
        return [
            'message' => 'Data Berhasil Disimpan',
            'text' => isset($msg_simpan_mutasi)? $msg_simpan_mutasi : 'Mutasi berhasil diterima',
            'id' => $terimamutasiobat_id
        ];
    }
}