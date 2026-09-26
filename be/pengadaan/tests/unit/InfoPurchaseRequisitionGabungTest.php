<?php
use app\modules\v1\models\InfoPurchaseRequisitionGabung;

class InfoPurchaseRequisitionGabungTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new InfoPurchaseRequisitionGabung();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'infopurchasereqgabung_v');
    }
}