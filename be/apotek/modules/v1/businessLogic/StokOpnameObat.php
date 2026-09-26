<?php

namespace app\modules\v1\businessLogic;

/**
** @author yaya
**/

use Yii;

use app\modules\v1\models\StokOpname;
use app\modules\v1\models\StokOpnameDetail;
use app\modules\v1\models\FormStokOpname;
use yii\helpers\ArrayHelper;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoStokOpnameDetailView;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\FormulirStokOpname;
use app\modules\v1\models\KonfigGudang;
use Doco\components\DocoHelpers;
use app\modules\v1\businessLogic\InsertObatBaruSo as SO_OBAT_BARU;
use app\modules\v1\businessLogic\FormulirStokOpname as BL_FSO;
use SirsCore\businessLogic\StokObatAlkes as BL_SOA;
use SirsCore\features\IntegrasiAkunting;
use Doco\components\DocoMessages;
use Doco\Traits\ControllerHelperTrait;

class StokOpnameObat
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
        $jenisStokOpname = self::PENYESUAIAN;
        $post = $request->post();
        $transaction = $connection->beginTransaction();
        try {
            $inputSo = (new SO_OBAT_BARU($id))->execute();
            $inputSo = ArrayHelper::index($inputSo, 'formstokopname_id');
            
            if (empty($data) && empty($inputSo)) {
                throw new \Exception("Tidak ada data stok opname", 1);
            }
            
            $formulir_so = FormulirStokOpname::find()->where(['formulirstokopname_id' => $id])->asArray()->one();
            $modelStokOpname = new StokOpname;
            $modelStokOpname->attributes = $post;
            $modelStokOpname->jenisstokopname = $jenisStokOpname;
            $modelStokOpname->ruangan_id = $formulir_so['ruangan_id'];
            $modelStokOpname->tglstokopname = date('Y-m-d H:i:s');
            $modelStokOpname->formulirstokopname_id = isset($formulir_so['formulirstokopname_id']) ? $formulir_so['formulirstokopname_id'] : null;
            if ($modelStokOpname->save()) {
                $idParent = $modelStokOpname->stokopname_id;

                $modelStokOpname->is_verifikasi = false;
                $modelStokOpname->update();
                // prepare data detail SO
                $detail_formstokopname = DetailFormulirStokOpnameView::find()->where([
                    'formulirstokopname_id' => $formulir_so['formulirstokopname_id']
                ])->asArray()->all();
                $detail_formstokopname = ArrayHelper::index($detail_formstokopname, 'formstokopname_id');
                $list_columns = [];
                foreach ($detail_formstokopname as $value) {
                    $idDetail = ArrayHelper::getValue($value, 'formstokopname_id');
                    $dataDetail = !empty($inputSo[$idDetail]) ? $inputSo[$idDetail] : [];
                    if (isset($dataDetail['stok_fisik'])) {
                        $stokFisik = $dataDetail['stok_fisik'] != "" ? floatval($dataDetail['stok_fisik']) : floatval($detail_formstokopname[$idDetail]['stok_sistem']);    
                     }else {
                        $stokFisik = floatval($detail_formstokopname[$idDetail]['stok_sistem']);
                     }                    
                    // $stokFisik = $dataDetail['stok_fisik'] != "" ? floatval($dataDetail['stok_fisik']) : floatval($detail_formstokopname[$idDetail]['stok_sistem']);
                    $selisihStok = $stokFisik - ArrayHelper::getValue($value, 'stok_saatini', 0);
                    $stokRevisi = !empty($dataDetail['revisi_stok']) ? $dataDetail['revisi_stok'] : null;
                    $kondisibarang = 9999;

                    $list_columns[] = [
                        'formstokopname_id' => $detail_formstokopname[$idDetail]['formstokopname_id'],
                        'stokopname_id' => $modelStokOpname->stokopname_id,
                        'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id'),
                        'obatalkes_id' => $value['obatalkes_id'],
                        'hargasatuan' => $value['hargajual'],
                        'harganetto' => $value['harganetto'],
                        'tglkadaluarsa' => $value['tglkadaluarsa'],
                        'volume_sistem' => $detail_formstokopname[$idDetail]['stok_sistem'],
                        'volume_fisik' => $stokFisik,
                        'kondisibarang' => $kondisibarang,
                        'jumlahharga' => $stokFisik * ArrayHelper::getValue($value, 'hargajual', 0),
                        'jumlahnetto' => $stokFisik * ArrayHelper::getValue($value, 'harganetto', 0),
                        'jmlselisihstok' =>  $selisihStok,
                        'revisi_stok' => $stokRevisi,
                        'is_newso' => ArrayHelper::getValue($dataDetail, 'is_newso', false)
                    ];
                }
                
                // Insert SO detail
                StokOpnameDetail::batchInsert($list_columns, false);

                $konfig = KonfigFarmasi::find()->one();
                if(property_exists($konfig, 'is_verifstokopname') && $konfig->is_verifstokopname == FALSE) {
                    // update stokobatalkes_t
                    BL_SOA::updateStokObatAlkes($idParent, $modelStokOpname->ruangan_id, false);
                }

                // update formulirstokopname & formstokopname
                BL_FSO::updateFormulirStokOpname($formulir_so['formulirstokopname_id'], $idParent);
                
                $getSo = StokOpname::findOne($idParent);
                $noStok = isset($getSo['nostokopname']) ? $getSo['nostokopname'] : '';
                IntegrasiAkunting::integrateStokOpname($noStok);
                $transaction->commit();
                return $this->responseJson(200, 'Stok Opname Behasil', ['text' => 'Stok Opname Behasil', 'id' => DocoHelpers::encrypt($idParent)]);
            } else {
                $transaction->rollBack();
                return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$modelStokOpname->errors]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$e->getMessage()]);
        }
    }

    public function update($id) {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $inputRevisi = (new SO_OBAT_BARU($id, true))->execute();
            $index = 0;
            foreach ($inputRevisi as $key => $value) {
                $indexArr[$index] = (int) !empty($value['stokopnamedetail_id']) ? $value['stokopnamedetail_id'] : null;
                $updateDataArr[$index] = ArrayHelper::getValue($value, 'revisi_stok') == "" ? null : floatval(ArrayHelper::getValue($value, 'revisi_stok'));

                $index++;
                $condition = ['stokopnamedetail_id' => $indexArr];
                $dataUpdate = ['revisi_stok' => $updateDataArr];
            }
            ApotekComponent::updateMultiple('stokopnamedetail_t', $dataUpdate, $condition);
            $soObat = StokOpname::find()->select(['stokopname_id', 'formulirstokopname_id'])->where(['formulirstokopname_id' => $id])->one();

            $transaction->commit();
            return $this->responseJson(200, 'Proses Berhasil', ['text' => 'Data Berhasil di update', 'id' => DocoHelpers::encrypt(ArrayHelper::getValue($soObat, 'stokopname_id'))]);
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$e->getMessage()]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, DocoMessages::KEY_ERR_CUSTOM, ['text'=>$e->getMessage()]);
        }
    }
}