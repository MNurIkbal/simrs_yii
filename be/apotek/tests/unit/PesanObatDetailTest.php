<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use app\modules\v1\models\PesanObatDetail;

class PesanObatDetailTest extends \Codeception\Test\Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before() {
        $this->model = new PesanObatDetail();
    }

    protected function _after() {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'pesanobatdetail_t');
    }

    public function testTipeDataPositif() {
        $this->model->attributes = $this->setPayloadTipeData(true);
        $this->assertTrue($this->model->validate());
    }

    public function testTipeDataNegatif() {
        $this->model->attributes = $this->setPayloadTipeData(false);
        $this->assertFalse($this->model->validate());
    }

    public function testRequiredPositif() {
        $this->model->attributes = [
            'pesanobatdetail_id' => 1
        ];
        $this->assertTrue($this->model->validate());
    }

    public function testRequiredNegatif() {
        $this->model->attributes = [
            'pesanobatdetail_id' => null
        ];
        $this->assertFalse($this->model->validate());
    }
    
    private function setPayloadTipeData($isPositif = true) {
        return [
            'pesanobatdetail_id' => $isPositif ? 1 : "String",
            'pesanobatalkes_id' => $isPositif ? 1 : "String",
            'satuankecil_id' => $isPositif ? 1 : "String",
            'obatalkes_id' => $isPositif ? 1 : "String",
            'created_by' => $isPositif ? 1 : "String",
            'modified_count' => $isPositif ? 1 : "String",
            'last_modified_by' => $isPositif ? 1 : "String",
            'deleted_by' => $isPositif ? 1 : "String",
            'jumlah_pesan' => $isPositif ? 5 : "String",
            'is_deleted' => $isPositif ? false : "false",
            'is_active' =>  $isPositif ? true : "true",
            'additional_data' => $isPositif ? "String" : 2022
        ];
    }
}
