<?php

use app\modules\v1\models\Pasien;

class PasienTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $model;

    protected function _before()
    {
        $this->model = new Pasien;
    }

    protected function _after()
    {
    }

    // tests
    public function testValidationRequired()
    {
        $payload_true = [
            'tgl_rekam_medik' => '2022-02-22',
            'nama_pasien' => 'required',
            'jeniskelamin' => 'required',
            'golonganumur_id' => 1,
            'alamat_pasien' => 'required',
            'tanggal_lahir' => '2022-02-22',
            'statusperkawinan' => 'required',
            'golongandarah' => 'required',
            'tempat_lahir' => 'required',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $payload_false = [
            'tgl_rekam_medik' => null,
            'nama_pasien' => null,
            'golonganumur_id' => null,
            'jeniskelamin' => null,
            'tanggal_lahir' => null,
            'alamat_pasien' => null,
            'statusperkawinan' => null,
            'golongandarah' => null,
            'tempat_lahir' => null,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());

    }

    public function testValidationTypeData()
    {
        $payload_true = [
            'tgl_rekam_medik' => '2022-02-22',
            'nama_pasien' => 'required',
            'jeniskelamin' => 'required',
            'golonganumur_id' => 1,
            'alamat_pasien' => 'required',
            'tanggal_lahir' => '2022-02-22',
            'statusperkawinan' => 'required',
            'golongandarah' => 'required',
            'tempat_lahir' => 'required',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $payload_false = [
            // Date format berpengaruh
            'tgl_rekam_medik' => '2022-02-22',
            'nama_pasien' => 'required',
            'jeniskelamin' => true,
            'golonganumur_id' => '1',
            'alamat_pasien' => 'required',
            'tanggal_lahir' => '2022-02-22',
            'statusperkawinan' => 1,
            'golongandarah' => 0.2,
            'tempat_lahir' => 'required',
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());
    }

    public function testValidationScenario()
    {
        $payload_false = [
            // Date format berpengaruh
            'nama_pasien' => 'required',
            'jeniskelamin' => 'required',
            'tanggal_lahir' => '2022-02-22',
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());

        $payload_true = [
            'nama_pasien' => 'required',
            'jeniskelamin' => 'required',
            'tanggal_lahir' => '2022-02-22',
        ];
        $this->model->attributes = $payload_true;
        $this->model->scenario = 'fix-error-update';
        $this->assertTrue($this->model->validate());
    }
}