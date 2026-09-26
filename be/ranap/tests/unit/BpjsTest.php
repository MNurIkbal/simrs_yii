<?php

use Doco\models\bpjs\Bpjs;

class BpjsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    // tests
    public function testRefObatPRB()
    {
        $bpjs = new Bpjs();
        var_dump($bpjs->refObatPRB('METILPREDNISOLON TAB 4 MG'));
        ob_flush();
    }

    public function testDokter()
    {
        $bpjs = new Bpjs();
        $data = $bpjs->referensiDokter(2, '2022-01-28', 'INT');
    }
 
    public function testDiagnosaPrb()
    {
        $bpjs = new Bpjs();
        $data = $bpjs->diagnosaprb();
        var_dump([$data]);
        ob_flush();
    }


    public function testDeletePRB()
    {
        $bpjs = new Bpjs();
        var_dump($bpjs->deletePRB([
            "noSrb"=> "2494976",
            "noSep"=> "0114R0330122V000032",
            "user"=> "001037",
        ]));
        ob_flush();
    }
    
}