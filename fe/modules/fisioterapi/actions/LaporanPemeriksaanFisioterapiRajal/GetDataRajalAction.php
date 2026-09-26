<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataRajalAction extends BaseCurrentAction
{
    public function getJumlah($filter = null){
        try {
        $response = Yii::$app->docoRest->fisioterapi->get('laporan-pemeriksaan-fisioterapi-rajal/generate-row-jumlah', ['query' => $filter]);
        $body = json_decode($response->getBody(), True);
        return ArrayHelper::getValue($body,'response');
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $result['rowJumlah'] = $this->getJumlah($filter);
            $response = Yii::$app->docoRest->fisioterapi->get('laporan-pemeriksaan-fisioterapi-rajal/get-data-rajal', ['query' => $filter]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
                $value['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tgl_pendaftaran'] = date("d-m-Y H:i:s", strtotime($value["tgl_pendaftaran"]));
                $value['jeniskelamin'] = DocoHelpers::genderCode($value['jeniskelamin_id']);
                $value['jumlah'] = 1;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($body, 'response._meta.totalCount');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
