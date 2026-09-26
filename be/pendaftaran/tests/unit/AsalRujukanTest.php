<?php
use app\modules\v1\models\AsalRujukan;

class AsalRujukanTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new AsalRujukan();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'asalrujukan_nama' => 'china',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'asalrujukan_nama' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'asalrujukan_nama' => 'china',
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
            'asalrujukan_nama' => null,
            'additional_data' => 1,
            'created_by' =>  'TESSSS', 
            'modified_count' =>  'TESSSS', 
            'last_modified_by' =>  'TESSSS', 
            'deleted_by' =>  'TESSSS', 
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}