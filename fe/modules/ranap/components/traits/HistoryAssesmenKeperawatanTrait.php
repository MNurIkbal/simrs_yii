<?php
// author : ardi pratama

namespace app\modules\ranap\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\json_encode;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;


use app\modules\ranap\models\AsesmenKeperawatanHistoryForm;
use app\modules\ranap\models\AsesmenKeperawatanResikoJatuhHistory;

trait HistoryAssesmenKeperawatanTrait
{
    public function actionHistoryAssesmenKeperawatan()
    {
        try{
            $title = Yii::t('fe', 'History Assesmen Keperawatan');
            $request = Yii::$app->request;
            $pendaftaran_id =  !empty($request->get('pendaftaran_id')) ? $request->get('pendaftaran_id') : null;
            
            return $this->renderAjax('history-assesmen-keperawatan/_modal_assesmen_keperawatan', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionDetailHistoryAsesmen() {
        $data = $this->guzzleExec($this->_restRanap, [
            'url' => 'history-assesmen-keperawatan/detail-asesmen',
            'payload' => [
                'query' => [
                    'history_asesmenawal_id' => Yii::$app->request->get('history_asesmenawal_id'),
                    'pendaftaran_id' => Yii::$app->request->get('pendaftaran_id')
                ]
            ]
        ]);
        $dataBmi = $this->guzzleExec($this->_restRanap, [
            'url' => 'allow/data-bmi',
        ]);
        $jeniskelamin = $this->_jeniskelamin;
        $data_bmi     = empty($dataBmi['data-bmi']) ? [] : $dataBmi['data-bmi'];
        $data_bmi     = json_encode($data_bmi);
        $model = new AsesmenKeperawatanHistoryForm;
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
        return $this->renderAjax('history-assesmen-keperawatan/__form', compact('model', 'arrayConfig', 'modelResiko', 'data', 'pendaftaran_id', 'data_bmi', 'jeniskelamin'));
    }


   public function actionGetDataHistory()
   {
       // Try catch
       try {
           Yii::$app->response->format = Response::FORMAT_JSON;
           $params = Yii::$app->request;
           $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
           $draw = $params->get('draw', 1);
           $pendaftaran_id = $params->get('pendaftaran_id');
           $start = $params->get('start');
           $length = $params->get('length');
           $data = [];
           $result = [];
           $result['data'] = $data;
           $result['draw'] = $draw;
           $result['recordsTotal'] = 0;
           $result['recordsFiltered'] = 0;

           $request = $this->_restRanap->get('history-assesmen-keperawatan/index',[
               'query' => [
                   'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                   'offset' => $start,
                   'limit' => $length,
               ]
           ]);
           $response = json_decode($request->getBody(), true);
           Yii::error($response);
           $no = $params->get('start', 1);
           foreach ($response['response']["data"] as $key => $value) {
               $no++;
               $data[] = [
                   'rowNum' => $no,
                   'tgl_asesmen' => date('d-M-Y h:i:s',strtotime($value['tgl_asesmen'])),
                   'perawar_assesmen' => $value['nama_pegawai'], 
                   'hasil_assesmend' => $this->getBtnHtmlHasilAssesmen($value)
               ];
           }

           $result['data'] = $data;
           $result['recordsTotal'] = $response['response']['_meta']['totalCount'];
           $result['recordsFiltered'] = $response['response']['_meta']['totalCount'];
           return $result;
        //    return DocoHelpers::response($result);
       } catch (RequestException $e) {
           $this->logError($e);
           return DocoHelpers::dataTabelsException($e->getMessage());
       } catch (\Exception $e) {
           $this->logError($e);
           return DocoHelpers::dataTabelsException($e->getMessage());
       }
   }
   
   protected function getBtnHtmlHasilAssesmen($data)
   {Yii::error($data);
        $btn = '';
        if (!empty($data['asesmenawal_id'])) {
            $btn .= Html::button('View Hasil Assesmen',
                [
                    'id' => 'btn-hasil-assesmen'.@$data['rowNum'],
                    'class'=>'btn btn-info btn-sm btn-view-assesmen',
                    'data-toggle'=>'modal',
                    'data-target' => '#modal_riwayat',
                    // 'action' => '/igd/history-assesmen-keperawatan/detail-asesmen?id='.DocoHelpers::encrypt($data['id'])
                    'action' => 'detail-history-asesmen?history_asesmenawal_id='.$data['history_asesmenawal_id'].'&pendaftaran_id='.$data['pendaftaran_id']
                ]
            );
        }
        return $btn;
    }
}
