<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\PengadaanComponent;
use app\modules\v1\models\InfoPoView;
use app\modules\v1\models\KonfigFarmasi;

class SetPoExpiredAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $type_po = $request->post('type_po');

            if($type_po == DocoConstants::JENIS_OBAT) {
                $tableName = 'validasipoobat_t';
                $primaryLabel = 'validasipoobat_id';
            } else {
                $tableName = 'validasipobarang_t';
                $primaryLabel = 'validasipobarang_id';
            }

            $model = InfoPoView::find()->where([
                'type_po' => $type_po,
                'is_validasi' => true
            ]);
            $model->andWhere([
                'OR',
                ['status_penerimaan' => DocoConstants::PO_BELUM_DITERIMA],
                ['status_penerimaan' => DocoConstants::BELUM_SELESAI_PO]
            ]);
            $model->orderBy('transaksi_id', SORT_ASC);
            $data = $model->asArray()->all();

            if(count($data) > 0) {
                $modelConfigFarmasi = new KonfigFarmasi;
                $config = $modelConfigFarmasi->find()->where([
                    "konfigfarmasi_id" => 1
                ])->one();

                $updateData = $updateCondition = $opCondition = [];
                foreach ($data as $key => $value) {
                    $updateData['status_penerimaan'][] = DocoConstants::PO_EXPIRED;
                    $date_po = date_create($value['tanggal_po']);
                    $day_add = $config->po_expired;
                    date_add($date_po, date_interval_create_from_date_string($day_add." days"));
                    $expiredDate = date_format($date_po, "Y-m-d");

                    $updateCondition['tgl_expired'][] = "'".$expiredDate."'";
                    $updateCondition[$primaryLabel][] = $value['transaksi_id'];
                    $opCondition['tgl_expired']['operand'] = '>=';
                    $opCondition['tgl_expired']['is_date'] = true;
                }

                $setPoExpired = PengadaanComponent::batchUpdate($tableName, $updateData, $updateCondition, $opCondition, true);
                if(!$setPoExpired) {
                    $response = [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Gagal Set PO Expired',
                    ];
                } else {
                    $response = [
                        'status' => 200,
                        'title' => 'Proses Berhasil!',
                        'message' => 'Sukses update PO '.$type_po.' expired'
                    ];
                }
            } else {
                $response = [
                    'status' => 200,
                    'title' => 'Proses Berhasil!',
                    'message' => 'Status PO '.$type_po.' expired sudah terupdate'
                ];
            }

            return $response;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }
}