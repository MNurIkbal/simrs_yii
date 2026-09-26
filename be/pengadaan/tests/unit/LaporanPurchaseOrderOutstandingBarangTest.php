<?php
use app\modules\v1\models\LaporanPurchaseOrderOutstandingBarangView;

class LaporanPurchaseOrderOutstandingBarangTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new LaporanPurchaseOrderOutstandingBarangView();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'laporanpooutstandingbarang_v');
    }
}