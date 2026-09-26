<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

trait HistoryAssesmenKeperawatanTrait 
{    
   public function actionGetDataHistory()
   {
       // Try catch
       try {
           Yii::$app->response->format = Response::FORMAT_JSON;
           $params = Yii::$app->request;
           $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
           $draw = $params->get('draw', 1);
           $pendaftaran_id = $params->get('pendaftaran_id');
           $data = [];
           $result = [];
           $result['data'] = $data;
           $result['draw'] = $draw;
           $result['recordsTotal'] = 0;
           $result['recordsFiltered'] = 0;

           $request = $this->_restIgd->get('history-assesmen-keperawatan/index',[
               'query' => [
                   'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                   'start' => $params->get('start', 1),
                   'length' => $params->get('length', 10),
               ]
           ]);
           $response = json_decode($request->getBody(), true);
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
   {
		$btn = '';
        if (!empty($data['asesmenperawatrd_id'])) {
            $btn .= Html::button('View Hasil Assesmen',
                [
                    'id' => 'btn-hasil-assesmen'.@$data['rowNum'],
                    'class'=>'btn btn-info btn-sm btn-view-assesmen',
                    'data-toggle'=>'modal',
                    'data-target' => '#modal_riwayat',
                    // 'action' => '/igd/history-assesmen-keperawatan/detail-asesmen?id='.DocoHelpers::encrypt($data['id'])
                    'action' => '/igd/history-assesmen-keperawatan/detail-asesmen?id='.$data['id'].'&pendaftaran_id='.$data['pendaftaran_id']
                ]
            );
        }
		return $btn;
	}
}