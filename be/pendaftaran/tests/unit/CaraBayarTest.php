<?php

use app\modules\v1\models\CaraBayar;  

class CaraBayarTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    public $class;
    
    protected function _before()
    {
        $this->model = new CaraBayar;
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testRequiredValidation()
    {
        $payload = [
            'carabayar_nama' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testNegativeTipeDataInt()
    {
        $this->model->carabayar_nama = 'asdas';
        $this->model->created_by = 'asdasd';
        $this->assertFalse($this->model->validate());
    }

    public function testPositiveTipeDataInt()
    {
        $this->model->carabayar_nama = 'asdas';
        $this->model->created_by = 1;
        $this->assertTrue($this->model->validate());
    }
}