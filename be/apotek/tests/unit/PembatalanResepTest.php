<?php
use app\modules\v1\models\PembatalanResep;

class PembatalanResepTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new PembatalanResep();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'pembatalanresep_t');
    }
    
    public function testValidateRequiredPositif()
    {
        $payload = [
            'penjualanresep_id' => 1, 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'penjualanresep_id' => null, 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'penjualanresep_id' => 1, 
            'pembatalanresep_id' => 5, 
            'penjualanresep_id ' => 1,
            'petugas_batal_id' => 1, 
            'created_by' => 1, 
            'modified_count' => 1, 
            'last_modified_by' => 1, 
            'deleted_by' => 1, 
            'is_deleted' => true, 
            'is_active' => false, 
            'additional_data' => "tes", 
            'created_date' => '2022-04-01',  
            'last_modified_date' => '2022-05-01', 
            'deleted_date' => '2022-06-01', 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'penjualanresep_id' => null, 
            'pembatalanresep_id' => "tes", 
            'penjualanresep_id ' => "tes", 
            'petugas_batal_id' =>  "tes", 
            'created_by' =>  "tes", 
            'modified_count' =>  "tes", 
            'last_modified_by' =>  "tes", 
            'deleted_by' =>  "tes", 
            'is_deleted' => 1, 
            'is_active' => 1, 
            'additional_data' => 1, 
            'created_date' => 1,   
            'last_modified_date' => 1,  
            'deleted_date' => 1, 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}