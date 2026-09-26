<?php
use app\modules\v1\models\InfoPoView;

class InfoPoViewTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $model;
    
    protected function _before()
    {
        $this->model = new InfoPoView();
    }

    protected function _after()
    {
        $this->model = null;
    }

    public function testValidationTable(){
        $data = $this->model->tableSchema->name;
        $this->assertEquals($data,'infopo_v');
    }
}