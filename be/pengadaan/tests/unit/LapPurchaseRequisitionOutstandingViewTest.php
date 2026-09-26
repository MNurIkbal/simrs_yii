<?php
use app\modules\v1\models\LapPurchaseRequisitionOutstandingView;

class LapPurchaseRequisitionOutstandingViewTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new LapPurchaseRequisitionOutstandingView();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'laporanproutstanding_v');
    }
}