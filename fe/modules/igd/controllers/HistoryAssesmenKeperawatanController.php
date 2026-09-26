<?php
// author : ardi pratama

namespace Doco\igd\controllers;

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

use app\modules\igd\models\AsesmenKeperawatanIgdHistoryForm;
use app\modules\igd\models\AsesmenKeperawatanResikoJatuhHistory;

use app\modules\igd\components\traits\HistoryAssesmenKeperawatanTrait;

class HistoryAssesmenKeperawatanController extends DocoController
{
    use HistoryAssesmenKeperawatanTrait;

    protected $allowAction = ['*'];
    protected $_module = 'igd/history-assesmen-keperawatan/';
    protected $_title = "History Assesmen Keperawatan";
    protected $_restIgd;

    protected $_restApotek;
    protected $_user_identity;
    protected $_pendaftaran_id;
    protected $_data_pasien;
    protected $_data_pegawai;
    protected $_jeniskelamin;
    protected $_pegawai_id;
    
    /**
     * @inheritdoc
     */
    public function initx()
    {
        parent::init();
        $this->_restIgd = Yii::$app->docoRest->igd;
    }

    public function init()
    {
        parent::init();

        $docoVars = Yii::$app->docoVars;
        $this->_restIgd = Yii::$app->docoRest->igd;
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_user_identity = Yii::$app->session->get('user_identity');
        if (empty($this->_user_identity)) return false;
        $this->_pendaftaran_id = Yii::$app->request->get('pendaftaran_id');
        $this->_pegawai_id = $docoVars->user('id_pegawai') ? $docoVars->user('id_pegawai') : 1;

        $data_pasien_igd = Yii::$app->cache->get('data-pasien-igd-'.$this->_pendaftaran_id);
        $data_pegawai = Yii::$app->cache->get('data-pegawai-'.$this->_pegawai_id);
        
        $this->_data_pasien = $data_pasien_igd;
        $this->_jeniskelamin = !empty($data_pasien_igd['jeniskelamin']) ? $data_pasien_igd['jeniskelamin'] : null;
        $this->_data_pegawai = $data_pegawai;
    }

    public function actionIndex()
    {
        try{
            $title = Yii::t('fe', 'History Assesmen Keperawatan');
            $request = Yii::$app->request;
            $pendaftaran_id =  !empty($request->get('pendaftaran_id')) ? $request->get('pendaftaran_id') : null;
            
            return $this->renderAjax('_modal_assesmen_keperawatan', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionDetailAsesmen() {
        $data = $this->guzzleExec($this->_restIgd, [
            'url' => 'history-assesmen-keperawatan/detail-asesmen',
            'payload' => [
                'query' => [
                    'id' => Yii::$app->request->get('id'),
                    'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id')
                ]
            ]
        ]);
        
        $dataBmi = $this->guzzleExec($this->_restIgd, [
            'url' => 'allow/data-bmi',
        ]);
        $jeniskelamin = $this->_jeniskelamin;
        $data_bmi     = empty($dataBmi['data-bmi']) ? [] : $dataBmi['data-bmi'];
        $data_bmi     = json_encode($data_bmi);
        $model = new AsesmenKeperawatanIgdHistoryForm;
        $model->attributes = $data['asesmenkeperawatan'];
        if ( !empty($model->diagnosa_keperawatan) ) {
            $diagnosaKeperawatan = [];
            foreach(json_decode($model->diagnosa_keperawatan, true) as $index => $diagnosa) {
                if( empty($diagnosa['id']) && empty($diagnosa['kode']) ){
                    $id = $diagnosa['text'];
                } else {
                    $id = $diagnosa['id'].'_'.$diagnosa['kode'].' - '.$diagnosa['text'];
                }
                $diagnosaKeperawatan[$id] = (!empty($diagnosa['kode']) ? $diagnosa['kode'].' - ' : '').$diagnosa['text'];
            }
            $model->diagnosa_keperawatan = $diagnosaKeperawatan;
        }
        $data['asesmenkeperawatan']['pendaftaran_id'] = Yii::$app->request->get('pendaftaran_id');
        $modelResiko = new AsesmenKeperawatanResikoJatuhHistory;
        $arrayConfig = $this->getConfig('asesmen_keperawatan_rd');
        if (isset($data['asesmenkeperawatan']['keluhan_utama'])) {
            $model->keluhan = $data['asesmenkeperawatan']['keluhan_utama'];
        }
        if (isset($data['asesmenkeperawatan']['riwayat_penyakit_sekarang'])) {
            $model->r_penyakitsaatini = $data['asesmenkeperawatan']['riwayat_penyakit_sekarang'];
        }
        if (isset($data['asesmenkeperawatan']['riwayat_penyakit_dahulu'])) {
            $model->r_penyakitdahulu = $data['asesmenkeperawatan']['riwayat_penyakit_dahulu'];
        }
        if (isset($data['asesmenkeperawatan']['riwayat_terapi_sebelumnya'])) {
            $model->r_pengobatan = $data['asesmenkeperawatan']['riwayat_terapi_sebelumnya'];
        }
        if (isset($data['asesmenkeperawatan']['alergi_obat']) || isset($data['asesmenkeperawatan']['alergi_lainnya'])) {
            $model->is_alergi = ( !is_null($data['asesmenkeperawatan']['alergi_obat']) || !is_null($data['asesmenkeperawatan']['alergi_lainnya']) ) ? true : false;
            if (isset($data['asesmenkeperawatan']['alergi_obat'])) {
                $model->is_alergiobat = !is_null($data['asesmenkeperawatan']['alergi_obat']) ? true : false;
            }
            if (isset($data['asesmenkeperawatan']['alergi_lainnya'])) {
                $model->is_alergilainnya = !is_null($data['asesmenkeperawatan']['alergi_lainnya']) ? true : false;
            }
        }
        if (isset($data['jenisResiko'])) {
            $model->jenis_resiko = $data['jenisResiko'];
        }
        
        $tidak_ada_kelainan = 'tidak_ada_kelainan';
        if(!isset($data['asesmenkeperawatan']['survey_kepala'])) {
            $model->survey_kepala = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_mata'])) {
            $model->survey_mata = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_mulut'])) {
            $model->survey_mulut = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_telinga'])) {
            $model->survey_telinga = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_leher'])) {
            $model->survey_leher = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_extremitas'])) {
            $model->survey_extremitas = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_dada'])) {
            $model->survey_dada = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_abdomen'])) {
            $model->survey_abdomen = $tidak_ada_kelainan;
        }

        if(!isset($data['asesmenkeperawatan']['survey_pelvis'])) {
            $model->survey_pelvis = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_medulla_spinalis'])) {
            $model->survey_medulla_spinalis = $tidak_ada_kelainan;
        }
        if(!isset($data['asesmenkeperawatan']['survey_kolumna_vertebralis'])) {
            $model->survey_kolumna_vertebralis = $tidak_ada_kelainan;
        }
        $model->tgl_datang = date('d/m/Y H:i:s', strtotime($model->tgl_datang));
        $model->tgl_keluar = !empty($data["asesmenkeperawatan"]['tgl_keluar']) ? date('d/m/Y H:i:s', strtotime($data["asesmenkeperawatan"]['tgl_keluar'])) : date('d/m/Y H:i:s');
        return $this->renderAjax('__form', compact('model', 'arrayConfig', 'modelResiko', 'data', 'pendaftaran_id', 'data_bmi', 'jeniskelamin'));
    }
}
