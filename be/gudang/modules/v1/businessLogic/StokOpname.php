<?php

namespace app\modules\v1\businessLogic;

/**
** @author yaya
**/

use Yii;

use app\modules\v1\models\StokOpnameBarang;
use app\modules\v1\models\StokOpnameBarangDetail;
use app\modules\v1\models\FormSoBarang;
use app\modules\v1\models\FormSoBarangDetail;
use app\modules\v1\models\StokBarang;
use yii\helpers\ArrayHelper;
use app\components\GudangComponent;
use app\modules\v1\models\InfoFormSoBarangDetail;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\KonfigGudang;
use Doco\components\DocoHelpers;
use app\modules\v1\businessLogic\InsertBarangBaruSo as SO_BARANG_BARU;
use Doco\components\DocoMessages;
use Doco\Traits\ControllerHelperTrait;

class StokOpname
{
    use ControllerHelperTrait;
    // Kondisi Untuk StokOpname
    const PENYESUAIAN = '138';
    const STOK_AWAL = '139';
    /**
    * @var $data harus array [data asal dari infostokobatdetail_v yang sudah di filter]
    * @return array
    **/
    public function execute($id, $data = null)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $jenisStokOpname  = self::PENYESUAIAN;
        $post = $request->post();
        $transaction = $connection->beginTransaction();
        try {
            $inputSo = (new SO_BARANG_BARU($id))->execute();
            $inputSo = ArrayHelper::index($inputSo, 'formsobarangdetail_id');

            if (empty($data) || empty($inputSo)) {
                throw new \Exception("Tidak ada data stok opname", 1);
            }
            $ruangan_id = ArrayHelper::getValue($data, 'ruangan_id', null);

            $soBarang = new StokOpnameBarang;
            $soBarang->attributes = $post;
            $soBarang->jenisstokopname = $jenisStokOpname;
            $soBarang->ruangan_id = $ruangan_id;
            $soBarang->tglstokopname = date('Y-m-d H:i:s');
            $soBarang->totalharga_fisik = $request->post('total_harga_fisik');
            $soBarang->totalharga_sistem = $request->post('total_harga_netto');
            $soBarang->formsobarang_id = isset($data['formsobarang_id']) ? $data['formsobarang_id'] : null;
            $jenisSo = $soBarang->jenisstokopname;
            if ($soBarang->save()) {
                $idParent = $soBarang->stokopnamebarang_id;
                $inputSoDetail = $inputStok = [];

                $getLastStok = self::getLastStok($ruangan_id);
                $lastStok =  ArrayHelper::index($getLastStok, 'id_stok');

                $tmpDetail = $falseId = [];

                $detail = InfoFormSoBarangDetail::find()->select([
                    'formsobarangdetail_id', 'formsobarang_id', 'stokbarang_id','barang_id','stok','harganetto', 'satuankecil_id'
                ])->where(['formsobarang_id' => $id])->asArray()->all();

                
                foreach ($detail as $value) {
                    $idDetail = ArrayHelper::getValue($value, 'formsobarangdetail_id');
                    $idStok = ArrayHelper::getValue($value, 'stokbarang_id');
                    $dataDetail = isset($inputSo[$idDetail]) ? $inputSo[$idDetail] : [];
                    $stokFisik = isset($dataDetail['stok_fisik']) && is_numeric($dataDetail['stok_fisik']) && $dataDetail['stok_fisik'] >= 0 ? ArrayHelper::getValue($dataDetail, 'stok_fisik',0) : ArrayHelper::getValue($dataDetail, 'stok',0);
                    $selisihStok = $stokFisik - ArrayHelper::getValue($value, 'stok',0);
                    $stokRevisi = !empty($dataDetail['revisi_stok']) ? $dataDetail['revisi_stok'] : null;
                    // Kondisi Untuk menampung formulir dan ini nanti yang akan di insert kembali
                    $tmpDetail[$idDetail] = $value;
                    $inputSoDetail[] = [
                        'formsobarangdetail_id' => ArrayHelper::getValue($value, 'formsobarangdetail_id'),
                        'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id'),
                        'stokopnamebarang_id' => $idParent,
                        'barang_id' => ArrayHelper::getValue($value, 'barang_id'),
                        'volume_sistem' => ArrayHelper::getValue($value, 'stok',0),
                        'volume_fisik' => $stokFisik,
                        'hargasatuan' => ArrayHelper::getValue($value, 'harganetto'),
                        'jumlahharga' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                        'harganetto' => ArrayHelper::getValue($value, 'harganetto'),
                        'jumlahnetto' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                        'tglperiksafisik' => date('Y-m-d H:i:s'),
                        'jmlselisihstok' => $selisihStok,
                        'revisi_stok' => $stokRevisi,
                        'is_newso' => ArrayHelper::getValue($dataDetail, 'is_newso')
                    ];
                }
                // Insert Ke Detail Stok Opname
                StokOpnameBarangDetail::batchInsert($inputSoDetail,false);

                // Sesudah Insert terus Select berdasarkan parent
                $querySoDetail = StokOpnameBarangDetail::find()->select([
                    'stokopnamebarangdetail_id', 'formsobarangdetail_id', 
                    'barang_id', 'tglkadaluarsa', 'harganetto',
                    'jmlselisihstok', 'volume_fisik', 'volume_sistem', 'is_newso'
                ])->where(['stokopnamebarang_id' => $idParent])->asArray()->all();

                $backupFormDetail = [];
                foreach ($querySoDetail as $value) {
                    if (isset($tmpDetail[$value['formsobarangdetail_id']])) {
                        $row = $tmpDetail[$value['formsobarangdetail_id']];
                        $idStok = $row['stokbarang_id'];
                        $row['stokopnamebarangdetail_id'] = ArrayHelper::getValue($value, 'stokopnamebarangdetail_id');
                        $row['is_newso'] = ArrayHelper::getValue($value, 'is_newso');
                        $backupFormDetail[] = $row;
                        $tglKdls = isset($lastStok[$idStok]['tglkadaluarsa']) ? $lastStok[$idStok]['tglkadaluarsa'] : null;
                        // Kondisi untuk Stok Awal
                        $stok_in = $stok_out = 0;
                        $tgl_stok_in = $tgl_stok_out = null;
                        if (self::STOK_AWAL == $jenisSo) {
                            $falseId[] = $idStok;
                            $tgl_stok_in = date('Y-m-d H:i:s');
                            $stok_in = $value['volume_fisik'];
                        } else {
                            // Kondisi Stok Penyesuiaan
                            if ($value['jmlselisihstok'] < 0) {
                                // Ini pasti masuk ke in ka karena selisish fisik dan sistem minus
                                $tgl_stok_in = date('Y-m-d H:i:s');
                                $stok_in = abs($value['jmlselisihstok']);
                            } else {
                                // Ini pasti masuk ke out karena jumlah selisih fisik dan sistem plus
                                $tgl_stok_out = date('Y-m-d H:i:s');
                                $stok_out = abs($value['jmlselisihstok']);
                            }
                        }
                        $inputStok[] = [
                            'ruangan_id' => $ruangan_id,
                            'barang_id' => isset($lastStok[$idStok]['barang_id']) ? $lastStok[$idStok]['barang_id'] : null,
                            'tglkadaluarsa' => $tglKdls,
                            'nobatch' => isset($lastStok[$idStok]['nobatch']) ? $lastStok[$idStok]['nobatch'] : null,
                            'tglstok_in' => $tgl_stok_in,
                            'tglstok_out' => $tgl_stok_out,
                            'qtystok_in' => $stok_in,
                            'qtystok_out' => $stok_out,
                            'harganetto' => isset($lastStok[$idStok]['harganetto']) ? $lastStok[$idStok]['harganetto'] : null,
                            'persendiscount' => isset($lastStok[$idStok]['persendiscount']) 
                                                ? $lastStok[$idStok]['persendiscount'] : null,
                            'jmldiscount' => isset($lastStok[$idStok]['jmldiscount']) ? $lastStok[$idStok]['jmldiscount'] : null,
                            'persenppn' => isset($lastStok[$idStok]['persenppn']) ? $lastStok[$idStok]['persenppn'] : null,
                            'persenpph' => isset($lastStok[$idStok]['persenpph']) ? $lastStok[$idStok]['persenpph'] : null,
                            'persenmargin' => isset($lastStok[$idStok]['persenmargin']) ? $lastStok[$idStok]['persenmargin'] : null,
                            'jmlmargin' => isset($lastStok[$idStok]['jmlmargin']) ? $lastStok[$idStok]['jmlmargin'] : null,
                            'stokbarang_aktif' => true,
                            'stokbarangasal_id' => $idStok,
                            'stokopnamebarangdetail_id' => $value['stokopnamebarangdetail_id'],
                       ];
                    }
                }

                // Hard delete
                if ($backupFormDetail) {
                    $idFormulir = ArrayHelper::getValue($data, 'formsobarang_id');
                    // update form So barang
                    $updateFormBarang = FormSoBarang::find()->where(['formsobarang_id' => $idFormulir])->one();
                    $updateFormBarang->stokopnamebarang_id = $idParent;
                    $updateFormBarang->save();
                    // delete formSoBarangDetail
                    FormSoBarangDetail::deleteAll(
                        'formsobarang_id = :formsobarang_id', [
                        ':formsobarang_id' => $idFormulir
                    ]);

                    FormSoBarangDetail::batchInsert($backupFormDetail,false);
                }

                $konfig = KonfigGudang::find()->one();
                if(property_exists($konfig, 'is_verifstokopnamebarang') && $konfig->is_verifstokopnamebarang == FALSE){
                    // update stokobatalkes_t
                }

                $transaction->commit();
                return $this->responseJson(200, 'Stok Opname Behasil', ['text' => 'Stok Opname Behasil', 'id' => DocoHelpers::encrypt($idParent)]);
            } else {
                $transaction->rollBack();
                return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$soBarang->errors]);
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage()]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$e->getMessage()]);
        }
    }

    /**
    * @var $id integer [formsobarangdetail_id]
    * @return array|mix
    **/
    public static function delete($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $check = FormSoBarang::find()->where([
                'formsobarang_id' => $id
            ])->one();
            if (!empty($check)) {
                (new FormSoBarang)->delete(['formsobarang_id' => $id]);
                (new FormSoBarangDetail)->delete(['formsobarang_id' => $id]);
                $soBarang = StokOpnameBarang::find()->where([
                    'formsobarang_id' => $id
                ])->asArray()->one();
                if(!empty($soBarang)) {
                    (new StokOpnameBarang)->delete(['stokopnamebarang_id' => $soBarang['stokopnamebarang_id']]);
                    (new StokOpnameBarangDetail)->delete(['stokopnamebarang_id' => $soBarang['stokopnamebarang_id']]);
                }
                $transaction->commit();
                return [
                    'messages' => 'Data sukes di hapus',
                    'text' => 'Data berhasil dihapus',
                    'title' => 'Proses Berhasil !'
                ];
            } else {
                $transaction->rollBack();
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Data gagal dihapus'
                ]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        }

    }

    /**
    * @var $id_ruangan integer
    * @return array|mix
    **/

    public static function getLastStok($id_ruangan)
    {
        $query = "
            SELECT barang_id,id_stok,
            SUM(sub_query.qtystok_in - sub_query.qtystok_out) as total_stok ,
            nobatch,
            harganetto,
            persendiscount,
            jmldiscount,
            persenppn,
            persenpph,
            persenmargin,
            jmlmargin,
            tglstok_in,
            tglkadaluarsa
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin,t.tglkadaluarsa
                FROM stokbarang_t t
                LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.ruangan_id = {$id_ruangan}
                AND t.stokbarang_aktif = true
            ) as sub_query
            GROUP BY sub_query.id_stok ,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglstok_in,tglkadaluarsa
            ORDER BY sub_query.tglstok_in DESC
        ";

        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public function update($id) {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $inputRevisi = (new SO_BARANG_BARU($id, true))->execute();
            $index = 0;
            foreach ($inputRevisi as $key => $value) {
                $indexArr[$index] = (int) !empty($value['stokopnamebarangdetail_id']) ? $value['stokopnamebarangdetail_id'] : null;
                $updateDataArr[$index] = ArrayHelper::getValue($value, 'revisi_stok') == "" ? null : floatval(ArrayHelper::getValue($value, 'revisi_stok'));

                $index++;
            }
            $condition = ['stokopnamebarangdetail_id' => $indexArr];
            $dataUpdate = ['revisi_stok' => $updateDataArr];
            GudangComponent::updateMultiple('stokopnamebarangdetail_t', $dataUpdate, $condition);
            $soBarang = StokOpnameBarang::find()->select(['stokopnamebarang_id', 'formsobarang_id'])->where(['formsobarang_id' => $id])->one();

            $transaction->commit();
            return $this->responseJson(200, 'Proses Berhasil', ['text' => 'Data Berhasil di update', 'id' => DocoHelpers::encrypt(ArrayHelper::getValue($soBarang, 'stokopnamebarang_id'))]);
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage()]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$e->getMessage()]);
        }
    }
}