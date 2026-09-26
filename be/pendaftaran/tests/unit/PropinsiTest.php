<?php
use app\modules\v1\models\Propinsi;

class PropinsiTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    public $model;
    
    protected function _before()
    {
        $this->model = new Propinsi();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'propinsi_nama' => 'jawa barat',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'propinsi_nama' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'propinsi_nama' => 'china',
            'longitude' => '-sds3esdsd', 
            'latitude' => '-sds3esds8',
            'additional_data' => 'additional_data',
            'created_by' => 2, 
            'modified_count' => 3, 
            'last_modified_by' => 1, 
            'deleted_by' => 2, 
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'propinsi_nama' => null,
            'additional_data' => 1,
            'longitude' => 1, 
            'latitude' => 2,
            'created_by' =>  'TESSSS', 
            'modified_count' =>  'TESSSS', 
            'last_modified_by' =>  'TESSSS', 
            'deleted_by' =>  'TESSSS', 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}