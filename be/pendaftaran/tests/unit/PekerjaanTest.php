<?php

use app\modules\v1\models\Pekerjaan;

class PekerjaanTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $model;
    
    protected function _before()
    {
        $this->model = new Pekerjaan;
    }

    protected function _after()
    {
    }

    // tests
    public function testValidationRequired()
    {
        $payload_true = [
            'pekerjaan_nama' => 'IT Developer',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $payload_false = [
            'pekerjaan_nama' => null,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());

    }

    public function testValidationTypeData()
    {
        $payload_true = [
            'pekerjaan_nama' => 'IT Developer',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 50; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        //string 50
        $payload_true = [
            'pekerjaan_nama' => $randomString,
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());


        $randomString = '';
        for ($i = 0; $i <= 60; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        //string > 50
        $payload_false = [
            'pekerjaan_nama' => $randomString,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());

        $payload_false = [
            // Date format berpengaruh
            'pekerjaan_nama' => true,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());
    }

    // public function testValidationScenario()
    // {
    //     $payload_false = [
    //         // Date format berpengaruh
    //         'nama_pasien' => 'required',
    //         'jeniskelamin' => 'required',
    //         'tanggal_lahir' => '2022-02-22',
    //     ];
    //     $this->model->attributes = $payload_false;
    //     $this->assertFalse($this->model->validate());

    //     $payload_true = [
    //         'nama_pasien' => 'required',
    //         'jeniskelamin' => 'required',
    //         'tanggal_lahir' => '2022-02-22',
    //     ];
    //     $this->model->attributes = $payload_true;
    //     $this->model->scenario = 'fix-error-update';
    //     $this->assertTrue($this->model->validate());
    // }
}