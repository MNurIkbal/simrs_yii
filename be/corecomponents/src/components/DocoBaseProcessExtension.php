<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;

use Doco\components\DocoHelpers;
use Doco\Traits\ControllerHelperTrait;

abstract class DocoBaseProcessExtension implements IProcessExtension
{
    use DocoYiiInitialize, ControllerHelperTrait;

    protected $_db = null;

    protected $_transaction;

    protected $_isOnTransaction = false;

    protected $_requestData = null;

    protected $_errorValidation = null;

    protected $_error = null;

    protected function startDBTransaction()
    {
        $this->_transaction = $this->_db->beginTransaction();
        $this->_isOnTransaction = true;
    }

    protected function commitDBTransaction()
    {
        $this->_transaction->commit();
    }

    protected function cancelDBTransaction()
    {
        $this->_transaction->rollback();
    }


    protected function handlingErrorDb($e)
    {
        return true;
    }

    public function execute()
    {
        $this->initialize();
        try {
            $response =  $this->processFlow();
            return $response;
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            if ($this->_isOnTransaction) $this->cancelDBTransaction();
            $this->handlingErrorDb($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Doco\exceptions\ValidationException $e) {
            if ($this->_isOnTransaction) $this->cancelDBTransaction();
            return $e->getOptions();
        } catch (\yii\web\HttpException $e) {
            \Yii::$app->response->statusCode = $e->statusCode;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            if ($this->_isOnTransaction) $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @return array|null output nya array atau null, kalau null nanti proses di json response nya hanya sukses biasa.
     */
    abstract protected function processFlow();
}
