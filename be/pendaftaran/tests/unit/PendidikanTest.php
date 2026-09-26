<?php

use app\modules\v1\models\Pendidikan;

class PendidikanTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $model;
    
    protected function _before()
    {
        $this->model = new Pendidikan;
    }

    protected function _after()
    {
    }

    // tests
    public function testValidationRequired()
    {
        $payload_true = [
            'pendidikan_urutan' => 1,
            'pendidikan_nama' => 'Sarjana',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $payload_false = [
            'pendidikan_urutan' => null,
            'pendidikan_nama' => null,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());

    }

    public function testValidationTypeData()
    {
        $payload_true = [
            'pendidikan_urutan' => 1,
            'pendidikan_nama' => 'Sarjana',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $payload_false = [
            'pendidikan_urutan' => '1',
            'pendidikan_nama' => 4,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());
    }
}