<?php 

use app\components\Pelayanan\PelayananHelpers;

class PelayuananHelpersTest extends \Codeception\Test\Unit
{
    // tests
    public function testCoalesceUseFirstOptionData()
    {
        $datas = ['data1', 'data2', null];
        $data = PelayananHelpers::coalesce($datas, 'defaultdata');
        $this->assertEquals($data, 'data1');
        
    }
    
    public function testCoalesceUseSecondOptionData()
    {
        $datas = [null, 'data2', 'data3'];
        $data = PelayananHelpers::coalesce($datas, 'defaultdata');
        $this->assertEquals($data, 'data2');
    }
    
    public function testCoalesceUseDefaultDataOption()
    {
        $datas = [];
        $data = PelayananHelpers::coalesce($datas, 'defaultdata');
        $this->assertEquals($data, 'defaultdata');
    }
    
    public function testCoalesceUseDefaultDataEmpty()
    {
        $datas = [];
        $data = PelayananHelpers::coalesce($datas);
        $this->assertFalse($data);
    }
}