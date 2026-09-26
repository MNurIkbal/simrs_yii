<?php

namespace app\modules\v1\actions\KontrakSupplier;

use app\models\Model;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use app\modules\v1\models\KontrakSupplier;
use app\modules\v1\models\KontrakSupplierDetail;
use Doco\components\DocoHelpers;

class UploadKontrakSupplierAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $payloads = json_decode($request->post('payload', json_encode([], true)), true);
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $payload = $this->mappingPayload($payloads);
            if (empty($payload)) throw new \Exception("Tidak Data yang akan di simpan", 1);
            $listKontrakSupplier = $this->listKontrakSupplier($payload);
            
            $insertHeader = [];
            $insertDetail = [];
            $noKontrakUnique = [];
            foreach ($payload as $key => $value) {
                if (isset($listKontrakSupplier[ArrayHelper::getValue($value, 'kontraksupplier_no')])) {
                    // list detail existing
                    $headerExisting = $listKontrakSupplier[ArrayHelper::getValue($value, 'kontraksupplier_no')];
                    $item = $this->setDetail($value, $headerExisting);
                    $insertDetail[] = $item;
                    continue;
                }
                $item = $this->setDetail($value);
                $insertDetail[] = $item;

                // skip jika no kontrak sudah pernah diset
                if (isset($noKontrakUnique[ArrayHelper::getValue($value, 'kontraksupplier_no')])) {
                    continue;
                } else {
                    $noKontrakUnique[$value['kontraksupplier_no']] = true;
                    $tglBerlaku = ArrayHelper::getValue($value, 'tgl_berlaku', null);
                    $tglBerlaku = !empty($tglBerlaku) ? date('Y-m-d', strtotime($tglBerlaku)) : $tglBerlaku;
                    $item = [
                        'kontraksupplier_no' => ArrayHelper::getValue($value, 'kontraksupplier_no'),
                        'tgl_berlaku' => $tglBerlaku,
                        'supplier_id' => ArrayHelper::getValue($value, 'supplier_id'),
                        'payterm_id' => ArrayHelper::getValue($value, 'payterm_id'),
                        'jumlah_hari' => ArrayHelper::getValue($value, 'jumlah_hari'),
                        'pajak_id' => ArrayHelper::getValue($value, 'pajak_id'),
                        'persen_ppn' => ArrayHelper::getValue($value, 'persen_ppn'),
                        'contact_person' => ArrayHelper::getValue($value, 'contact_person'),
                        'catatan' => 'Import Data Excel '.date('d M Y H:i:s')
                    ];
                    $insertHeader[] = $item;
                }
            }

            KontrakSupplier::batchInsert($insertHeader);
            // set kontraksupplier_id detail after insert header
            $afterSaveHeader = $this->listKontrakSupplier($insertHeader);
            foreach ($insertDetail as $key => $value) {
                $header = isset($afterSaveHeader[ArrayHelper::getValue($value, 'kontraksupplier_no')]) ? $afterSaveHeader[ArrayHelper::getValue($value, 'kontraksupplier_no')] : [];
                if (empty(ArrayHelper::getValue($value, 'kontraksupplier_id')) && !empty($header)) {
                    $insertDetail[$key]['kontraksupplier_id'] = ArrayHelper::getValue($header, 'kontraksupplier_id');
                }
                unset($insertDetail[$key]['kontraksupplier_no']);
            }
            KontrakSupplierDetail::batchInsert($insertDetail);

            $data = [
                'Data Berhasil di Simpan' => count($insertDetail),
                'Data Gagal di Simpan' => count($this->mappingPayload($payloads, false))
            ];
            $transaction->commit();
            return $this->controller->responseJson(200, 'Data berhasil disimpan', $data);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(500,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage()]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(422,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage()]);
        }
    }

    /**
     * data yang di simpan yang valid,
     * dengan status true
     */
    private function mappingPayload($payload, $status = true)
    {
        return array_filter($payload, function ($var) use ($status) {
            return (ArrayHelper::getValue($var, 'status') == $status);
        });
    }

    /**
     * set kontrak supplir detail
     */
    private function setDetail($value, $haderExisting = [])
    {
        return [
            'kontraksupplier_id' => ArrayHelper::getValue($haderExisting, 'kontraksupplier_id', null),
            'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id'),
            'kode_obat' => ArrayHelper::getValue($value, 'kode_obat'),
            'nama_obat' => ArrayHelper::getValue($value, 'nama_obat'),
            'satuankecil_id' => ArrayHelper::getValue($value, 'satuankecil_id'),
            'harga' => DocoHelpers::convertCommaToPoint(DocoHelpers::convertToNumber(ArrayHelper::getValue($value, 'harga'))),
            'pengurang' => DocoHelpers::convertCommaToPoint(ArrayHelper::getValue($value, 'diskon')),
            'qty_min' => ArrayHelper::getValue($value, 'qty_min'),
            'penambah' => ArrayHelper::getValue($value, 'persen_ppn'),
            'total_harga' => empty(ArrayHelper::getValue($value, 'total_harga')) ? null : DocoHelpers::convertToNumber(ArrayHelper::getValue($value, 'total_harga')),
            'kontraksupplier_no' => ArrayHelper::getValue($value, 'kontraksupplier_no'),
        ];
    }

    /**
     * get data KontrakSupplier existing by kontraksupplier_no
     */
    private function listKontrakSupplier($payload)
    {
        $list_kode_supplier = ArrayHelper::getColumn($payload, 'kontraksupplier_no');

        $model = KontrakSupplier::find()->where(['IN', 'kontraksupplier_no', $list_kode_supplier])->asArray()->all();
        return ArrayHelper::index($model, 'kontraksupplier_no', []);
    }

    /**
     * get data KontrakSupplierDetail existing by kontraksupplier_id
     */
    private function listDetailKontrakSupplier($payload)
    {
        $list_id_header = ArrayHelper::getColumn($payload, 'kontraksupplier_id');
        return KontrakSupplierDetail::find()->where(['IN', 'kontraksupplier_id', $list_id_header])->asArray()->all();
    }
}