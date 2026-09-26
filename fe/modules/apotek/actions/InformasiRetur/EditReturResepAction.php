<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class EditReturResepAction extends Action
{
    public function run($id)
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $data_retur = $request->post('data_retur', []);
            $valid = false;
            $payload_retur = [];
            $ruangan_retur = ArrayHelper::getValue($post, 'ruangan');
            $tanggal_retur = ArrayHelper::getValue($post, 'tanggal');
            $returresep_id = ArrayHelper::getValue($post, 'returresep_id');
            $alasan_edit = ArrayHelper::getValue($post, 'alasan_edit');
            foreach ($data_retur as $key => $value) {
                $payload_retur['detail'][] = [
                    'identifier' => $value['obatalkes_id'],
                    'qty_retur' => isset($value['qty_retur']) ? $value['qty_retur'] : 0
                ];

                $value['qty_retur'] = isset($value['qty_retur']) ? $value['qty_retur']: 0;

                if ($value['qty_retur'] <= 0 && $value['qty_resep'] > 0) {
                    $racikan = filter_var($value['is_racikan'], FILTER_VALIDATE_BOOLEAN);
                    if($racikan == false) {
                        return DocoHelpers::response([
                            'response' => [
                                'text' => 'Qty Retur tidak boleh kosong.',
                                'title' => 'Proses Gagal!',
                            ]
                        ], 422);
                    }
                }
            }

            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $cacheLabel = 'editRetur' . $ruangan_id . '-' . $pegawai_id;
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
                'url' => 'inf-retur/edit-retur-resep',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pendaftaran_id' => $id,
                        'ruangan_retur' => $ruangan_retur,
                        'tanggal_retur' => $tanggal_retur,
                        'data_retur' => $data_retur,
                        'returresep_id' => $returresep_id,
                        'alasan' => $alasan_edit,
                        'username' => $request->post('nama_pemakai'),
                        'pass' => $request->post('katakunci_pemakai')
                    ]
                ],
            ]);

            if(isset($response['httpStatusCode']) == 422) {
                return DocoHelpers::response($response, 422);
            }

            return DocoHelpers::response($response, 200, true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
