<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\actions;

use Yii;
use yii\base\Action;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class GetDataDaftarPasienAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = DocoDatatableHelper::advancedFilterParam();

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->rm, [
                'url' => 'inf-daftar-pasien/get-data',
                'payload' => [
                    'query' => $payload
                ]
            ]);

            $body = $response;
            
            $no = $request->get('start',1);
            foreach ($body['data'] as $key => $value) {
                $no++;
                $value['primary'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['pendaftaranol_id'] = DocoHelpers::encrypt($value['pendaftaranol_id']);
                $value['is_from'] = in_array($value['jenis_reservasi_id'], [DocoConstants::JNS_RSVRVS_OL, DocoConstants::JNS_RSVRVS_R]) ? 'reservasi' : 'rekam_medik';
                $value['tgl_pendaftaran'] = DocoHelpers::display_label($value['tgl_pendaftaran'], true, 
                    date('d M Y H:i:s', strtotime($value['tgl_pendaftaran'])));

                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            // dump($result);
            // die();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}