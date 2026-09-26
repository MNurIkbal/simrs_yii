<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiMutasi;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\PenerimaanObatForm;
use yii\helpers\Url;
use Doco\apotek\services\InfMutasiObatalkesService;

class PenerimaanPesananAction extends Action {
    protected $_title = "";

    public function run($id, $nomutasioa)
    {
    	try {
            $title = 'Penerimaan Obat Alkes';
            $id = DocoHelpers::decrypt($id);
            $model = new PenerimaanObatForm;
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $post['PenerimaanObatForm']['pegawai_mengetahui'] = DocoHelpers::decrypt($post['PenerimaanObatForm']['pegawai_mengetahui']);
                $post['PenerimaanObatForm']['pegawai_menyetujui'] = DocoHelpers::decrypt($post['PenerimaanObatForm']['pegawai_menyetujui']);
                $post['PenerimaanObatForm']['mutasiobatruangan_id'] = DocoHelpers::decrypt($post['PenerimaanObatForm']['mutasiobatruangan_id']);
                $response = Yii::$app->docoRest->apotek->request('POST','inf-mutasi-obatalkes/create',[
                    'query' => ['nomutasioa'=>$nomutasioa],
                    'form_params'=>$post
                ]);
                $body = json_decode($response->getBody(), true);
                $body['response']['id'] = DocoHelpers::encrypt($body['response']['id']);
                \Yii::$app->response->statusCode = $body['metadata']['status'];
                if($body['metadata']['status'] == 422){
                    $body['response']['text'] = $body['response']['message'];
                }
                return DocoHelpers::response($body,false,true);
            } else {

                $respon_nomutasi = Yii::$app->docoRest->apotek->get('inf-mutasi-obatalkes/get-no-pesan-oa',
                    [
                        'query' => [
                            'id' => $id
                        ],
                        'form_params' => []
                    ]);
                $respon_nomutasi = json_decode($respon_nomutasi->getBody(), true);
                $arr_no_mutasi = $respon_nomutasi['response']['data'];
                $arr_pemesanan = $respon_nomutasi['response']['pemesanan'];
                $mutasiobatruangan_id = DocoHelpers::encrypt($arr_no_mutasi['mutasiobatruangan_id']);
                $pesanobatalkes_id = DocoHelpers::encrypt($arr_pemesanan['pesanobatalkes_id']);

                $no_mutasi = isset($arr_no_mutasi['nomutasioa']) ? $arr_no_mutasi['nomutasioa'] : '';
                $ruangan_asal = isset($arr_no_mutasi['ruangan_asal_id'])
                                    ? $arr_no_mutasi['ruangan_asal_id'] : null;
                $ruangan_tujuan = isset($arr_no_mutasi['ruangan_tujuan_id'])
                                    ? $arr_no_mutasi['ruangan_tujuan_id'] : null;
                $optMengetahui = [
                    $arr_no_mutasi['id_pegawai_mengetahui'] => $arr_no_mutasi['nama_pegawai_mengetahui']
                ];
                $optPenerima = [
                    $arr_no_mutasi['id_pegawai_penerima'] => $arr_no_mutasi['nama_pegawai_penerima']
                ];

                return $this->controller->render('penerimaan', get_defined_vars());
            }

        } catch (RequestException $e) {
            $error = json_decode($e->getResponse()->getBody(),true);
            return DocoHelpers::response(['message' => $e->getMessage()],422);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}