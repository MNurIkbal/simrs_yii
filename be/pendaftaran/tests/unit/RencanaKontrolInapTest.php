<?php

use app\modules\v1\models\RencanaKontrolT;

class RencanaKontrolInapTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $model;
    static protected $_headers;

    protected function _before()
    {
        $this->model = new RencanaKontrolT;
        self::$_headers = Yii::$app->request->headers;
    }

    protected function _after()
    {
    }

    // tests
    public function testRencanaKontrolT()
    {
        $payload_true = [
            'no_kartu' => null,
            'nama_spesialis' => 'Sarjana',
        ];
        $this->model->attributes = $payload_true;
        $this->assertTrue($this->model->validate());

        $payload_false = [
            'no_kartu' => null,
            'nama_spesialis' => null,
        ];
        $this->model->attributes = $payload_false;
        $this->assertFalse($this->model->validate());
    }

    public function testApiRencanaKontrolPrintTrue()
    {
        $rencanakontrol_id = 122;
        $is_online = false;
        $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $payload['headers'] = ['Content-Type' => 'application/json',
                                'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
                            ];
        
        $request = $_restPendaftaran->get('rencana-kontrol-inap/get-data-update?rencanakontrol_id='.$rencanakontrol_id, $payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertTrue($hasil);
    }

    public function testApiRencanaKontrolPrintFalse()
    {
        $rencanakontrol_id = '122';
        $is_online = false;
        $_restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $payload['headers'] = ['Content-Type' => 'application/json',
                                'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ', 
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiYWNjZXNzX3Rva2VuIjoiMTAwLXRva2VuIiwianRpIjoiNjIyOGMwNjc0MDMxMGJiZDc5NmE0MTQ2ZmJiZDQ2NTE4NWNkMTdiOGIwNWEzNmU4ZGMzYWUzYmNkN2I3NTdiNCIsImlzX21vYmlsZSI6MCwiaXNfYWxsX2V4cGVydGlzZV9sYWIiOnRydWUsInNpZ25hdHVyZV9wYXRoIjoiZHIgSGFydW4gUm9zaWRpIC0gMTA5NC5naWYifQ.V_hPVLy9J89Rs-37gDhuMjgOJvR8mJbNZAoxIsdqNME'
                            ];
        
        $request = $_restPendaftaran->get('rencana-kontrol-inap/get-data-update?rencanakontrol_id='.$rencanakontrol_id, $payload);
        $response = json_decode($request->getBody(),true);
        if ($response['metadata']['status'] == 200){
            $hasil = true;
        }
        $this->assertTrue($hasil);
    }
}