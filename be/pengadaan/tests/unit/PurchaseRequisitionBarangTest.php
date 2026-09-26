<?php
use app\modules\v1\models\PurchaseRequisitionBarang;

class PurchaseRequisitionBarangTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new PurchaseRequisitionBarang();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'purchasereqbrg_t');
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'purchasereqbrg_id' => 1, 
            'ruangan_id' => 55, 
            'pegawai_id' => 12,
            'reference' => "TESSS", 
            'status' => 712, 
            'created_date' => '2022-01-01',
            'is_deleted' => false, 
            'is_active' => true, 
            'is_prcyto' => true, 
            'is_admin' => true, 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'purchasereqbrg_id' => "TESS", 
            'ruangan_id' => "TESS", 
            'pegawai_id' => "TESS", 
            'reference' => 2, 
            'status' => "TESS",  
            'created_date' => "TESS", 
            'is_deleted' => "TESS", 
            'is_active' => "TESS", 
            'is_prcyto' => "TESS", 
            'is_admin' => "TESS",  
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}