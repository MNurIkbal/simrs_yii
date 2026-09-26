<?php
use app\modules\v1\models\InfoStokOpnameBarangDetailView;

class InfoStokOpnameBarangDetailViewTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before() {
        $this->model = new InfoStokOpnameBarangDetailView();
    }

    protected function _after() {
        $this->model = null;
    }

    public function testTableName() {
        $tableName = $this->model->tableSchema->name;
        $this->assertEquals($tableName, 'infostokopnamebarangdetail_v');
    }
}