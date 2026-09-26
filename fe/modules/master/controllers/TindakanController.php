<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan
 * @copyright 26 April 2018 aweutist
 */

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

// all trait
use app\modules\master\components\traits\KategoriTrait;
use app\modules\master\components\traits\KelompokTrait;
use app\modules\master\components\traits\KegiatanTrait;
use app\modules\master\components\traits\TindakanBmhpTrait;
use app\modules\master\components\traits\TindakanLuarBedahTrait;
use app\modules\master\components\traits\TindakanRuanganTrait;
use app\modules\master\components\traits\TindakanSpesialisTrait;
use app\modules\master\components\traits\GroupInacbgTrait;
use app\modules\master\components\traits\JenisPemeriksaanFisioterapiTrait;
use app\modules\master\components\traits\TindakanTrait;
use app\modules\master\components\traits\PaketTrait;
use app\modules\master\components\traits\PaketRuanganTindakanTrait;
use app\modules\master\components\traits\PaketFisioTrait;
use app\modules\master\models\TipePaketForm;

class TindakanController extends DocoController
{
    // use all trait
    use KategoriTrait;
    use KelompokTrait;
    use KegiatanTrait;
    use TindakanBmhpTrait;
    use TindakanLuarBedahTrait;
    use GroupInacbgTrait;
    use TindakanTrait;
    use TindakanRuanganTrait;
    use PaketTrait;
    use PaketRuanganTindakanTrait;
    use TindakanSpesialisTrait;
    use PaketFisioTrait;
    use JenisPemeriksaanFisioterapiTrait;

    protected $_title = "Master Tindakan";
    protected $_module = '/master/tindakan';
    protected $_restMaster;
    protected $allowAction = [
        // 'rumah-sakit'
    ];
    protected $_pageTitle;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = Yii::t('fe', $this->_title);
        $sub_title = $this->_pageTitle;

        try {
            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // action
    public function actions()
    {
        $actions = parent::actions();
        $oldActions = [
            'create-paket' => [
                'class' => 'app\modules\master\components\actions\CreateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceCreateAction' => 'tipe-paket/create',
                'modelForm' => new TipePaketForm,
                'viewForm' => 'paket/form',
            ],
            'update-komponen' => [
                'class' => 'app\modules\master\components\actions\UpdateModalAction',
                'serviceName' => $this->_restMaster,
                'serviceUpdateAction' => 'komponen-tarif/update',
                'serviceViewAction' => 'komponen-tarif/view',
                'requestUpdateMethod' => 'POST',
                'module' => $this->_module,
                // 'modelForm' => new KomponenTarifForm,
                'viewForm' => 'komponen/form'
            ],
        ];
        $actions = array_merge($actions, $oldActions);
        // Paket Fisio Route (use PaketFisioTrait)
        $actions = array_merge($actions, $this->paketFisioRoutes);
        $actions = array_merge($actions, $this->jenisPemeriksaanFisioterapiRoutes);
        return $actions;
    }
    // action

    public function actionFormPaket()
    {
        // Get request
        $request = Yii::$app->request;
        $model = new TipePaketForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', "Tambah Paket");

        // Check post
        if ($request->post()) {
            // Load
            $model->load($request->post());

            // Validate model
            if ($model->validate()) {
                // Try catch
                try {
                    // Get response
                    $response = $this->_restMaster->post('kelompok-pemeriksaan-rad/create', [
                        'form_params' => $model->attributes
                    ]);

                    // Return
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    // Return
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    // Return
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                // Errors
                $errors = DocoHelpers::parseError($model->errors, $formName);

                // Return
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // Return form
            return $this->renderPartial('components/paket/form', get_defined_vars());
        }
    }


    public function actionGetTindakan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $q = $request->get('search');
        $is_mcu = $request->get('is_mcu', null);
        $is_fisioterapi = $request->get('is_fisioterapi', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restMaster->get('tipe-paket/auto-tindakan?advanced-filter[daftartindakan_nama]=' . $q . '&advanced-filter[kelompoktindakan_nama]=' . $q . '&advanced-filter[ruangan_id]=' . $ruangan_id . '&is_mcu=' . $is_mcu. '&is_fisioterapi=' . $is_fisioterapi);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $value)
                    if (!array_key_exists($value['daftartindakan_id'], $temp_dokter)) {
                        $result['results'][] = [
                            'id' => $value['daftartindakan_id'],
                            'text' => $value['daftartindakan_nama'] . " - " . $value['kelompoktindakan_nama'],
                            'kelompoktindakan_nama' => $value['kelompoktindakan_nama'],
                            'ruangan_id' => ($ruangan_id) ? $value['ruangan_id'] : null,
                            'ruangan_nama' => ($ruangan_id) ? $value['ruangan_nama'] : null,
                            'instalasi_id' => ($ruangan_id) ? $value['instalasi_id'] : null,
                            'instalasi_nama' => ($ruangan_id) ? $value['instalasi_nama'] : null,
                        ];
                        $temp_dokter[$value['daftartindakan_id']] = $value['daftartindakan_nama'];
                    }
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetTindakanMaping($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restMaster->get('tindakan/get-tindakan-maping?q=' . $request->get('search') . '&column_id=' . $request->get('column_id'));
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            $temp = array();
            foreach ($body['response']['data'] as $value)
                if (!array_key_exists($value['daftartindakan_id'], $temp)) {
                    $result['results'][] = [
                        'id' => $value['daftartindakan_id'],
                        'text' => $value['daftartindakan_nama']
                    ];
                    $temp[$value['daftartindakan_id']] = $value['daftartindakan_nama'];
                }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionGetObatMapping($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restMaster->get('tindakan/get-obat-maping?q=' . $request->get('search') . '&column_id=' . $request->get('column_id'));
            $body = json_decode($response->getBody(), true);
            $temp = array();
            foreach ($body['response'] as $value)
                if (!array_key_exists($value['obatalkes_id'], $temp)) {
                    $result['results'][] = [
                        'id' => $value['obatalkes_id'],
                        'text' => $value['obatalkes_nama']
                    ];
                    $temp[$value['obatalkes_id']] = $value['obatalkes_nama'];
                }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetListTindakan()
    {
        $request = Yii::$app->request;
        if (isset($request->get('q')['term']) && !empty($request->get('q')['term'])) {
            $response = $this->guzzleExec($this->_restMaster, [
                'url' => 'tindakan/list-daftar-tindakan-nama',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'term' => $request->get('q')['term']
                    ]
                ]
            ]);

            $data = [];
            foreach ($response as $key => $value) {
                $data[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => '(' . $value['daftartindakan_kode'] . ') ' . $value['daftartindakan_nama'],
                ];
            }
            $total = count($response);
            $return = [
                'result' => $data,
                'total_count' => $total,
                'incomplete_results' => false
            ];
            return $this->helper->response($return);
        }
    }
}
