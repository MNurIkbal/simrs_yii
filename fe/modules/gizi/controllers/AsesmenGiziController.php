<?php


namespace Doco\gizi\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

use app\modules\gizi\components\traits\SgaTrait;
use app\modules\gizi\components\traits\PagtTrait;
use app\modules\gizi\components\traits\NrsTrait;
use app\modules\gizi\components\traits\AsuhanGiziTrait;
use app\modules\gizi\components\traits\PermintaanMakanTrait;
use app\modules\gizi\components\traits\CpptGiziTrait;


class AsesmenGiziController extends DocoController
{
    protected $_title = "Asesmen Gizi";
    protected $_module = '/gizi/asesmen-gizi';
    protected $_controller = '/gizi/asesmen-gizi';
    protected $_restGizi;
    protected $_data_pasien;

    protected $allowAction = ['*'];

    use SgaTrait;
    use PagtTrait;
    use NrsTrait;
    use AsuhanGiziTrait;
    use PermintaanMakanTrait;
    use CpptGiziTrait;
    
    public function init()
    {
        parent::init();
        $this->_restGizi = Yii::$app->docoRest->gizi;
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        $pendId = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendId);
        $data_pasien = [];
        $cache_data_pasien = Yii::$app->cache->get('gizi-pendaftaran-id-'. $pendId);
        try {
            if(!$cache_data_pasien || empty($cache_data_pasien)){
                $response = $this->_restGizi->get('allow/get-pasien-gizi?id=' . $pendaftaran_id);
                $response = json_decode($response->getBody(), true);
                $data_pasien = $response["response"];
                Yii::$app->cache->set('gizi-pendaftaran-id-'. $pendId, $data_pasien, 3600);
            }else{
                $data_pasien = $cache_data_pasien;
            }
        } catch (RequestException $e) {
            $data_pasien = [];
            throw new \yii\web\HttpException(400,Yii::t("fe","Tidak Ada Data Pasien"));
        } catch (Exception $e) {
            $data_pasien = [];
            throw new \yii\web\HttpException(400,Yii::t("fe","Tidak Ada Data Pasien"));
        }
        $this->_data_pasien = $data_pasien;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $permintaan_makan = $request->get('makan',false);
            $title = $this->_title;
            $data_pasien = $this->_data_pasien;

            $style_button_pulang = 'style="display:display;"';
            if (isset($data_pasien['status_ranap']) && $data_pasien['status_ranap'] != '') {
                if($data_pasien['status_ranap'] == DocoConstants::STATUS_RANAP_PULANG ){
                    $style_button_pulang = 'style="display:none;"';
                }
            }

            $style_button = 'style="display:none;"';
            if (isset($data_pasien['skor']) && $data_pasien['skor'] != '') {
                if($data_pasien['skor'] >= 2){
                    $style_button = 'style="display:display;"';
                }
            }

            $r_penyakitkeluarga = '-';
            if (isset($data_pasien['r_penyakitkeluarga']) && $data_pasien['r_penyakitkeluarga'] != '') {
                $arrPenyakitKeluarga = json_decode($data_pasien['r_penyakitkeluarga'], true);

                if (!empty($arrPenyakitKeluarga)) {
                    $r_penyakitkeluarga = [];
                    foreach ($arrPenyakitKeluarga as $key => $value) {
                        $r_penyakitkeluarga[] = $value['text'];
                    }
                }
                if(empty($r_penyakitkeluarga)){
                    $r_penyakitkeluarga = '-';
                } else {
                    $r_penyakitkeluarga = implode(', ', $r_penyakitkeluarga);
                }
            }

            $merokok = '-';
            if (isset($data_pasien['is_merokok']) && $data_pasien['is_merokok'] == true) {
                $merokok = Yii::t('fe', 'Ya, ').$data_pasien['jml_rokok'].Yii::t('fe', ' batang rokok perhari');
            } else {
                $merokok = Yii::t('fe', 'Tidak');
            }

            $diagnosa_nama = '-';
            if (isset($data_pasien['diagnosa_nama']) && $data_pasien['diagnosa_nama'] != '') {
                $arrDiagnosaNama = json_decode($data_pasien['diagnosa_nama'], true);

                if(!empty($arrDiagnosaNama)){
                    $diagnosa_nama = isset($arrDiagnosaNama['text']) ? (empty($arrDiagnosaNama['text']) ? '-' : $arrDiagnosaNama['text']) : '-';
                }
            }
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e){
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        } catch (\Exception $e){
            throw new \yii\web\HttpException(400,Yii::t("fe","Terdapat kesalahan"));
        }
    }
}