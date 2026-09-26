<?php 

namespace app\commands;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PasienMasukPenunjangT;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\Services\InternalService;
use yii\console\Controller;
use app\modules\v1\models\InfoPasienLabView;

class CronGetResultLaboratoriumController extends Controller
{
   protected $keyConfig = 'authentication';

   public function actionIndex($dateNow = false)
   {
      $params = Yii::$app->params['iniFile'];
      $baseConfig = isset($params[$this->keyConfig]) ? $params[$this->keyConfig] : [];

      $docoRest = Yii::$app->docoRest->dcms;
      $response = $docoRest->post('auth/get-token',[
         'form_params' => [
            'username' => $baseConfig['username'],
            'password' => $baseConfig['password']
         ]
      ]);
      $response = json_decode($response->getBody(), true);
      $token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;

      $startDate = date('Y-m-d'. ' 00:00:00');
      $endDate = date('Y-m-d'. ' 23:59:59');
      $dataPasien = InfoPasienLabView::find()
      ->select(['no_masukpenunjang','pendaftaran_id','pasienmasukpenunjang_id'])
      ->where(['NOT IN','status_periksa',[DocoConstants::ST_P_PEN_BTL, DocoConstants::ST_P_PEN_SELESAI]])
      ->andwhere([
          'is_hasil' => false,
          'image_link' => null,
      ]);
      if ($dateNow) {
         $dataPasien->andWhere(['between', 'tglmasukpenunjang', $startDate, $endDate]);
      }
      $dataPasien->orderBy(['tglmasukpenunjang' => SORT_DESC]);
      $dataPasien = $dataPasien->asArray()->limit(150)->all();

      if(!empty($dataPasien)) {
         try {
            foreach ($dataPasien as $key => $value) {
               (new InternalService)->sendTo([
                  'Lis' => [
                     'BridgingLisResult' => [
                        'no_masukpenunjang' => ArrayHelper::getValue($value, 'no_masukpenunjang'),
                        'pendaftaran_id' => ArrayHelper::getValue($value, 'pendaftaran_id'),
                        'pasienmasukpenunjang_id' => ArrayHelper::getValue($value, 'pasienmasukpenunjang_id'),
                        'token' => 'Bearer ' . $token,
                        'owner' => $baseConfig['xowner']
                     ]
                  ]
              ]);
            }
         } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            return $e->getMessage();
         } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return $e->getMessage();
         }
      }
   }
}


    