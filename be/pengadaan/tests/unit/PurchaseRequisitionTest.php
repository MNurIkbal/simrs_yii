<?php
use app\modules\v1\models\PurchaseRequisition;

class PurchaseRequisitionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new PurchaseRequisition();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'purchasereq_t');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = [
            'purchasereq_id' => 1
        ];
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = [
            'purchasereq_id' => 'TEST'
        ];
        $this->assertFalse($this->model->validate());
    }
}