<?php

use app\modules\v1\models\GolonganUmur;

class GolonganUmurTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new GolonganUmur;
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidateRequiredPositif()
    {
        $payload = [
            'golonganumur_nama' => 'Balita',
            'golonganumur_namalainnya' => 'Infant',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testValidateRequiredNegatif()
    {
        $payload = [
            'golonganumur_nama' => null,
            'golonganumur_namalainnya' => null,
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }

    public function testTipeDataPositif()
    {
        $payload = [
            'golonganumur_nama' => 'Balita',
            'golonganumur_namalainnya' => 'Infant',
            'golonganumur_minimal' => 1, 
            'golonganumur_maksimal' => 4, 
            'additional_data' => 'Informasi tambahan',
        ];
        $this->model->attributes = $payload;
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif()
    {
        $payload = [
            'golonganumur_nama' => (int) 817174,
            'golonganumur_namalainnya' => (int) 173,
            'golonganumur_minimal' => 'Satu', 
            'golonganumur_maksimal' => 'Empat', 
            'additional_data' => ['data' => 'Balita (Infant)'],
        ];
        $this->model->attributes = $payload;
        $this->assertFalse($this->model->validate());
    }
}