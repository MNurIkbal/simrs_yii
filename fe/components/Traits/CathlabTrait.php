<?php

namespace app\components\Traits;

use Yii;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;

use app\modules\igd\models\CathlabForm;

trait CathlabTrait
{
    /**
     * @var String $instalasi
     * @author Tsani Nashrullah (tsani.nashrullah@gmail.com)
     */
    public $instalasi = 'igd';

    /**
     * @var String $restGeneral
     * @author Tsani Nashrullah (tsani.nashrullah@gmail.com)
     */
    public $restGeneral;

    public function actionCathlab($id, $tipe)
    {
        try {
            $pendaftaran_id  = DocoHelpers::decrypt($id);
            $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
            $tgl_pendaftaran = $this->_data_pasien['tgl_pendaftaran'];
            if ($this->instalasi == 'ranap' && empty($pasienadmisi_id)) {
                return $this->responseJson(400, 'Pasien admisi tidak tersedia');
            } else if ($this->instalasi != 'ranap') {
                $pasienadmisi_id = null;
            }

            $data = $this->guzzleExec($this->restGeneral, [
                'url' => 'cathlab',
                'payload' => [
                    'query' => [
                        'pendaftaran_id'  => $pendaftaran_id,
                        'pasienadmisi_id' => $pasienadmisi_id,
                        'tipe'            => $tipe
                    ]
                ]
            ]);
            if(!empty($data['data']['tgl_prosedure'])){
                $data['data']['tgl_prosedure'] = date('d/m/Y', strtotime($data['data']['tgl_prosedure']));
            }
            else{
                $data['data']['tgl_prosedure'] = date('d/m/Y', strtotime("now"));
            }
            $url         = '';
            $urlEmployee = '';
            $urlPrint    = '';
            switch ($this->instalasi) {
                case 'igd':
                    $urlEmployee = '/igd/end-point/doctor-list';
                    $url = '/igd/pemeriksaan-igd/save-cathlab';
                    $urlPrint = '/igd/pemeriksaan-igd/cetak-cathlab-pdf?id=' . $id . '&tipe=' . $tipe;
                    break;
                case 'rajal':
                    $urlEmployee = '/igd/end-point/doctor-list';
                    $url = '/rajal/pemeriksaan/save-cathlab';
                    $urlPrint = '/rajal/pemeriksaan/cetak-cathlab-pdf?id=' . $id . '&tipe=' . $tipe;
                    break;
                case 'ranap':
                    $urlEmployee = '/ranap/end-point/doctor-list';
                    $url = '/ranap/pemeriksaan-rawat-inap/save-cathlab';
                    $urlPrint = '/ranap/pemeriksaan-rawat-inap/cetak-cathlab-pdf?id=' . $id . '&tipe=' . $tipe . '&pasienadmisi_id=' . $pasienadmisi_id;
                    break;
            }

            return $this->renderAjax('//cathlab/index', [
                'model'           => new CathlabForm,
                'pendaftaranId'   => $pendaftaran_id,
                'pasienAdmisiId'  => $pasienadmisi_id,
                'tipe'            => $tipe,
                'url'             => $url,
                'urlEmployee'     => $urlEmployee,
                'urlPrint'        => $urlPrint,
                'cathlabData'     => empty($data['data']) ? [] : $data['data'],
                'tgl_pendaftaran' => $tgl_pendaftaran
            ]);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionSaveCathlab()
    {
        $payload                    = Yii::$app->request->post();
        $pendaftaran_id             = Yii::$app->request->get('pendaftaran_id', null);
        $pasienadmisi_id            = Yii::$app->request->get('pasienadmisi_id', null);
        $tipe                       = Yii::$app->request->get('tipe', null);
        $payload['pendaftaran_id']  = $pendaftaran_id;
        $payload['pasienadmisi_id'] = $pasienadmisi_id;
        $payload['tipe']            = $tipe;

        !empty($payload['tgl_prosedure']) ? $payload['tgl_prosedure'] = date_format(date_create_from_format('d/m/Y', $payload['tgl_prosedure']), 'Y-m-d').' '.date('h:i:s') : null;

        $modelValidation = new CathlabForm;
        $modelValidation->attributes = $payload;
        if (!$modelValidation->validate()) {
            return $this->helper->macroResponseJson(422, 'Silakan cek kembali inputan.', $this->helper->mapErrorForm($modelValidation->errors, 'CathlabForm'));
        } else {
            return $this->guzzleExec($this->restGeneral, [
                'url' => 'cathlab/save-cathlab',
                'returnResponse' => true,
                'method' => 'POST',
                'payload' => [
                    'form_params' => $payload
                ]
            ]);
        }
    }

    public function actionCetakCathlabPdf()
    {
        $params          = Yii::$app->request;
        $pendaftaran_id  = DocoHelpers::decrypt($params->get('id', null));
        $tipe            = $params->get('tipe', null);
        $pasienadmisi_id = $params->get('pasienadmisi_id', null);
        $path            = Yii::getAlias("@download") . "/cathlab.pdf";

        $response       = $this->restGeneral->get('cathlab/cetak-cathlab-pdf', [
            'query' => [
                'pendaftaran_id'  => $pendaftaran_id,
                'tipe'            => $tipe,
                'pasienadmisi_id' => $pasienadmisi_id,
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
}
