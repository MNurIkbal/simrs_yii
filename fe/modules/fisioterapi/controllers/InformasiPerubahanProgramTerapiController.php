<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use app\components\DocoDatatableHelper;


class InformasiPerubahanProgramTerapiController extends DocoController
{
    protected $_module = '/fisioterapi/informasi-perubahan-program-terapi';
    protected $_titleInfo = "Informasi Perubahan Program Terapi";
    protected $_restFisioterapi;

    public function init()
    {
        parent::init();
        $this->_restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['batal-program-form'] = ['GET'];
        $verbs['batal-program'] = ['POST'];
        $verbs['get-data'] = ['GET'];
        $verbs['edit'] = ['GET'];
        $verbs['edit-tambah-tindakan'] = ['GET'];
        $verbs['update-data'] = ['POST'];
        return $verbs;
    }

    public function actionIndex()
    {
        try {
            $status_arr = [1 => Yii::t('fe','Pegawai Aktif'), Yii::t('fe','Pegawai Tidak aktif')];
            $title = $this->_titleInfo;
            return $this->render('index', get_defined_vars());
        } catch (\Exception $e){
            $status_arr = [];
            return $status_arr;
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $advancedFilter = $request->get('advancedFilter', []);

        if (!empty($advancedFilter)) {
            $status_approval = array_merge($yiiRestfulParams['advanced-filter'], $advancedFilter);
            $yiiRestfulParams['advanced-filter'] = $status_approval;
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        $response = $this->_restFisioterapi->get('informasi-perubahan-program-terapi?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        $no = $request->get('start',1);
        if(!empty($body['response']['data'])) {
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['programterapiapprove_id']);

                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;

                $data[$key] = $value;
            }
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

    public function actionDitolak($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restFisioterapi->post('informasi-perubahan-program-terapi/update?id='.$id, [
                'form_params' => ["status_approval" => 'ditolak']
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Program Terapi Berhasil Ditolak.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
    public function actionDisetujui($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restFisioterapi->post('informasi-perubahan-program-terapi/update?id='.$id, [
                'form_params' => ["status_approval" => 'disetujui']
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Program Terapi Berhasil Disetujui.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(),
                "OK",
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
