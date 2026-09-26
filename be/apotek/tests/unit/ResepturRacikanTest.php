<?php
use app\modules\v1\models\ResepturRacikan;

class ResepturRacikanTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new ResepturRacikan();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'resepturracikan_t');
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'resepturracikan_id' => 1, 
            'reseptur_id' => 5, 
            'penjualanresep_id ' => 1,
            'modified_count' => 1, 
            'last_modified_by' => 1, 
            'deleted_by' => 1, 
            'rke' => 1, 
            'racikan' => "tes", 
            'created_date' => '2022-04-01',
            'last_modified_date' => '2022-06-01',
            'deleted_date' => '2022-07-01',
            'additional_data' => 'tes',  
            'is_deleted' => true, 
            'is_active' => false, 
            'no_racikan' => 'A1B2', 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'resepturracikan_id' => "tes", 
            'reseptur_id' => "tes", 
            'penjualanresep_id ' =>"tes", 
            'modified_count' => "tes", 
            'last_modified_by' => "tes", 
            'deleted_by' => "tes", 
            'rke' => "tes", 
            'racikan' => 1, 
            'created_date' => 1,
            'last_modified_date' => 1,
            'deleted_date' => 1,
            'additional_data' => 1,  
            'is_deleted' => 1, 
            'is_active' => 1, 
            'no_racikan' => 1, 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}