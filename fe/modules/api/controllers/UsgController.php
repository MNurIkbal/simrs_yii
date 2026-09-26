<?php

namespace Doco\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\modules\api\models\HasilUsgForm;
use yii\helpers\Url;

class UsgController extends DocoController
{
    protected $allowAction = ['*'];

    public function actionFormModalUsg($id = null)
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $ruangan_id = DocoHelpers::decrypt($request->get('ruangan_id', null));

        $model = new HasilUsgForm;
        if ($request->post()) {
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $model->load($request->post());
            if ($model->validate()) {
                $response = Yii::$app->docoRest->rajal->post('usg/create', [
                    'form_params' => $model->attributes
                ]);

                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                $errors = DocoHelpers::parseError($response, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        $get_bundle_data = $this->guzzleExec(Yii::$app->docoRest->rajal, [
            'url' => 'usg/get-bundle',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => !empty($id) ? DocoHelpers::decrypt($id) : 0
                ]
            ],
        ]);

        $dokter = ArrayHelper::getValue($get_bundle_data, 'dokter');
        $pemeriksaan = ArrayHelper::getValue($get_bundle_data, 'pemeriksaan');
        $tgl_pendaftaran = ArrayHelper::getValue($get_bundle_data, 'tgl_pendaftaran');
        $tgl_pendaftaran = date('Y-m-d H:i:s', strtotime($tgl_pendaftaran));
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $model->pendaftaran_id = $pendaftaran_id;
        $model->dokterpemeriksa_id = ArrayHelper::getValue($get_bundle_data, 'dpjp_id');
        $model->tgl_pemeriksaan = date('Y-m-d H:i:s');
        $model->ruangan_id = $ruangan_id;

        return $this->renderAjax('_form_usg', get_defined_vars());
    }

    public function actionGetRiwayatUsg($id)
    {
        try {
            $request = Yii::$app->request;
            $url_params = [
                'pendaftaran_id' => DocoHelpers::decrypt($id),
                'instalasi_id' => $request->get('instalasi_id', null)
            ];
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $mode = $request->get('mode', '');
            $response = Yii::$app->docoRest->rajal->request('get', 'usg/index?' . http_build_query($url_params) . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
            $body = json_decode($response->getBody(), TRUE);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['hasilusg_id']);
                $value['primary'] = $primaryKey;
                unset($value['hasilusg_id']);

                $value['rowNum'] = $no;

                if ($value['is_deleted']) {
                    $value['hasilusg'] = '<i>' . Html::tag('p', \Yii::t('fe', 'Hasil telah dihapus oleh {user} pada {waktu}', ['waktu' => $value['deleted_date'], 'user' => $value['deleter_name']])) . '</i>';
                } else {
                    $value['hasilusg'] = Html::a(
                        Yii::t('fe', 'Hasil Pemeriksaan'),
                        [
                            '/api/usg/cetak-hasil?id=' . $primaryKey
                        ],
                        [
                            'class' => 'btn btn-info btn-sm btn-cetak-hasil-usg',
                            'target' => '_blank'
                        ]
                    );
                    if ($mode == 'form') {
                        $value['hasilusg'] = $value['hasilusg'] .
                            Html::button(
                                "<i class='fa fa-trash'></i>",
                                [
                                    'style' => 'margin-right:5px',
                                    'class' => 'btn btn-danger btn-sm delete-usg',
                                    'style' => 'margin-right:5px; padding-left:10px !important;',
                                    'action' => Url::to(['/api/usg/delete', 'id' => $primaryKey]),
                                ]
                            );
                    }
                }
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionListRiwayatUsg($id)
    {
        $instalasi_id = Yii::$app->request->get('instalasi_id', null);
        $ruangan_id = Yii::$app->request->get('ruangan_id', null);
        return $this->renderAjax('_list_riwayat', get_defined_vars());
    }

    public function actionCetakHasil($id)
    {
        return Yii::$app->report->exec('cetak-hasil-usg?id=' . DocoHelpers::decrypt($id));
    }

    public function actionDelete($id)
    {
        $usg_id = DocoHelpers::decrypt($id);

        try {
            $response = Yii::$app->docoRest->rajal->delete('usg/delete?id=' . $usg_id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                []
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
