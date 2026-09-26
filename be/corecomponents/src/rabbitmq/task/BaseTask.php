<?php
namespace Doco\rabbitmq\task;

use Yii;

abstract class BaseTask implements TaskInterface
{
    protected $_db = null;

    protected $_transaction;

    protected $_isOnTransaction = false;

    protected $token;
    protected $xOwner;
    protected $unique_str;
    protected $filter;
    protected $jwtUser;

    public function setParams(array $params) 
    {
        foreach($params as $key => $value) {
            $this->$key = $value;
        } 
        /** set manual untuk jwt karna console tidak dapat sesion login dari app */
        if (!empty($this->jwtUser)) {
            Yii::$app->jwt->user = $this->jwtUser;
        }
    }

    public function initialize()
    {
        $this->_db = $this->connection();
    }

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

    protected function connection()
    {
        return Yii::$app->db;
    }

    public function execute(array $data)
    {
        $this->initialize();
        try {
            $response =  $this->processFlow($data);
            return $response;
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            if ($this->_isOnTransaction) $this->cancelDBTransaction();
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            if ($this->_isOnTransaction) $this->cancelDBTransaction();
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Log error
     *
     * @param Class $e | Exception Class
     **/
    public function logError($e)
    {
        Yii::error(
            'Message : ' . $e->getMessage() . '--||--Line : ' . $e->getLine() . '--||--File : ' . $e->getFile(),
            'server-error'
        );
    }

    /**
     * @return array|null output nya array atau null, kalau null nanti proses di json response nya hanya sukses biasa.
     */
    abstract protected function processFlow(array $data);
}
