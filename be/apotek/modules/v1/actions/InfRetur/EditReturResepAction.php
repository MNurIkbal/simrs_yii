<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use SirsCore\businessLogic\ReturResep as BusinessLogicReturResep;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\models\LoginForm;
use Exception;

class EditReturResepAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $password = ArrayHelper::getValue($post, 'pass');
            $username = ArrayHelper::getValue($post, 'username');
            $modelLogin = new LoginForm();
            $modelLogin->username = $username;
            $modelLogin->password = $password;
            if(!$modelLogin->validate()) {
               return $this->controller->responseJson(400, 'Password Salah', [], ['title' => 'Proses Gagal !']);
            }

            $returBL = new BusinessLogicReturResep;
            $returBL->setPayload($post);
            $returBL->setPayloadLog($post);
            $returresep_id = $returBL->saveReturPendaftaran();

            $transaction->commit();
            return [
                'message' => 'Ubah Retur Berhasil di simpan',
                'id_retur' => DocoHelpers::encrypt($returresep_id),
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw new Exception($e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
