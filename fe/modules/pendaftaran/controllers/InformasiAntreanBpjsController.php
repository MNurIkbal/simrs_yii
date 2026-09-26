<?php
/**
 * @author: [Ardi Pratama][ardi.pratama@sirs.co.id]
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */
namespace Doco\pendaftaran\controllers;

use Yii;

use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class InformasiAntreanBpjsController extends DocoController
{
    protected $_restPendaftaran;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex()
    {
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $response = $this->_restPendaftaran->get('sync-bpjs/info-antrean?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        $no = $request->get('start',1);
        if(!empty($body['response']['data'])) {
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kodebooking']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

    public function actionResendTask()
    {
        $request = Yii::$app->request;
        $primary = $request->get('primary');
        $primary = explode(",",DocoHelpers::setDecryptIdFromString($primary));
        $body = $this->_restPendaftaran->post('sync-bpjs/resend-task-antrean-multiple',[
            'json' => [
                'kodebookings' => $primary
            ]
        ]);
        $response = json_decode($body->getBody(),true);

        return DocoHelpers::response($response);
    }

    public function actionSync()
    {
        try {
            $request = Yii::$app->request;
            $post_tanggalawal = $request->post('tanggalawal', date('Y-m-d'));
            $post_tanggalakhir = $request->post('tanggalakhir', date('Y-m-d'));
            $tanggalawal = date('Y-m-d',strtotime($post_tanggalawal));
            $tanggalakhir = date('Y-m-d',strtotime($post_tanggalakhir));

            $response = $this->_restPendaftaran->post('sync-bpjs/antrian-per-tanggal', [
                'form_params' => [
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true); 
            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionModalSyncByDate()
    {
        return $this->renderAjax('modal-sync');
    }
}
