<?php
use app\modules\v1\models\FormStokOpname;

class FormStokOpnameTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new FormStokOpname();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){ // validasi nama tabel/view
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'formstokopname_t');
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'ruangan_id' => 1, 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'ruangan_id' => null, 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'formstokopname_id' => 1,
            'stokopnamedetail_id' => 1,
            'obatalkes_id' => 1,
            'formulirstokopname_id' => 1,
            'volume_stok' => 1,
            'periodestok_id' => 1,
            'ruangan_id' => 1,
            'additional_data' => '2022-07-18',
            'created_date' => '2022-07-18',
            'created_by' => 1,
            'modified_count' => 1,
            'last_modified_date' => '2022-07-18',
            'last_modified_by' => 1,
            'is_deleted' => false,
            'is_active' => true,
            'deleted_date' => '2022-07-18',
            'deleted_by' => 1,
            'nobatch' => 'A1B2',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'formstokopname_id' => "TESSSSSS",
            'stokopnamedetail_id' => "TESSSSSS",
            'obatalkes_id' => "TESSSSSS",
            'formulirstokopname_id' => "TESSSSSS",
            'volume_stok' => "TESSSSSS",
            'periodestok_id' => "TESSSSSS",
            'ruangan_id' => "TESSSSSS",
            'additional_data' => 1,
            'created_date' => 1,
            'created_by' => "TESSSSSS",
            'modified_count' => "TESSSSSS",
            'last_modified_date' => 1,
            'last_modified_by' => "TESSSSSS",
            'is_deleted' => "TESSS",
            'is_active' => "TESSS",
            'deleted_date' => 1,
            'deleted_by' => "TESSSSSS",
            'nobatch' => 1,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}