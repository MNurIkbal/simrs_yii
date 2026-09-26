<?php
namespace components;

use Doco\components\DocoHelpers;

class DocoHelpersTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * @dataProvider filterArrayInputDataProvider
     * @test Function DocoHelpers@filterArray()
     */
    public function testFilterArray($param1, $param2, $param3, $expected)
    {
        $this->assertEquals($expected, DocoHelpers::filterArray($param1, $param2, $param3));
    }

    public function filterArrayInputDataProvider(): array
    {
        return [    
            'found with array' => [
                [
                    [
                        'lookup_id' => 176,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Farmasi',
                        'lookup_value' => 'Farmasi',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2121,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 2',
                        'lookup_value' => 'Pasien Lama BPJS',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2244,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 3',
                        'lookup_value' => 'Pasien Poli Executive',
                        'additional_data' => '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                    ],
                ],
                'lookup_id',
                2121,
                [
                    'lookup_id' => 2121,
                    'lookup_type' => 'jenis_antrian',
                    'lookup_name' => 'Pendaftaran Versi 2',
                    'lookup_value' => 'Pasien Lama BPJS',
                    'additional_data' => null,
                ]
            ],
            'not found with array' => [
                [
                    [
                        'lookup_id' => 176,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Farmasi',
                        'lookup_value' => 'Farmasi',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2121,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 2',
                        'lookup_value' => 'Pasien Lama BPJS',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2244,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 3',
                        'lookup_value' => 'Pasien Poli Executive',
                        'additional_data' => '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                    ],
                ],
                'lookup_id',
                2122222,
                []
            ],
            'not found with array' => [
                [
                    [
                        'lookup_id' => 176,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Farmasi',
                        'lookup_value' => 'Farmasi',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2121,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 2',
                        'lookup_value' => 'Pasien Lama BPJS',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2244,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 3',
                        'lookup_value' => 'Pasien Poli Executive',
                        'additional_data' => '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                    ],
                    [
                        'lookup_id' => 2244,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 35',
                        'lookup_value' => 'Pasien Poli Executive',
                        'additional_data' => '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                    ],
                ],
                'lookup_id',
                2244,
                [
                    'lookup_id' => 2244,
                    'lookup_type' => 'jenis_antrian',
                    'lookup_name' => 'Pendaftaran Versi 3',
                    'lookup_value' => 'Pasien Poli Executive',
                    'additional_data' => '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                ]
            ],
            'parameter comparison value is null' => [
                [
                    [
                        'lookup_id' => 176,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Farmasi',
                        'lookup_value' => 'Farmasi',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2121,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 2',
                        'lookup_value' => 'Pasien Lama BPJS',
                        'additional_data' => null,
                    ],
                ],
                'lookup_id',
                null,
                []
            ],
            'parameter comparison key is null' => [
                [
                    [
                        'lookup_id' => 176,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Farmasi',
                        'lookup_value' => 'Farmasi',
                        'additional_data' => null,
                    ],
                    [
                        'lookup_id' => 2121,
                        'lookup_type' => 'jenis_antrian',
                        'lookup_name' => 'Pendaftaran Versi 2',
                        'lookup_value' => 'Pasien Lama BPJS',
                        'additional_data' => null,
                    ],
                ],
                '',
                2121,
                []
            ],
            'parameter data is empty' => [
                [],
                '',
                2121,
                []
            ],
            'parameter data is null' => [
                null,
                '',
                2121,
                []
            ],

        ];
    }

    /**
     * @dataProvider getVariableFromAdditionalDataInputDataProvider
     * @test Function DocoHelpers@getVariableFromAdditionalData()
     */
    public function testGetVariableFromAdditionalData($param1, $param2, $param3, $expected)
    {
        $this->assertEquals($expected, DocoHelpers::getVariableFromAdditionalData($param1, $param2, $param3));
    }

    public function testGetVariableFromAdditionalDataFalseFormatJson()
    {
        $json_data = '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id: 5';
        $this->assertEquals(null, DocoHelpers::getVariableFromAdditionalData($json_data, 'klasifikasipasien_id', false));
    }

    public function getVariableFromAdditionalDataInputDataProvider(): array
    {
        return [    
            'by json data' => [
                '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                'klasifikasipasien_id',
                true,
                '5'
            ],
            'by array data' => [
                [
                    'is_executive' => 1,
                    'layarantrian_nama' => 'Antrian Poli Eksekutif',
                    'view_layarantrian' => 'antrian-bpjs',
                    'jenis_layarantrian' => 'Rawat Jalan',
                    'klasifikasipasien_id' => 5
                ],
                'klasifikasipasien_id',
                false,
                '5'
            ],
            'not found by json data' => [
                '{"is_executive" : 1, "layarantrian_nama": "Antrian Poli Eksekutif", "view_layarantrian": "antrian-bpjs", "jenis_layarantrian": "Rawat Jalan", "klasifikasipasien_id": 5}',
                'klasifikasipasien_ids',
                true,
                null
            ],
            'not found by array data' => [
                [
                    'is_executive' => 1,
                    'layarantrian_nama' => 'Antrian Poli Eksekutif',
                    'view_layarantrian' => 'antrian-bpjs',
                    'jenis_layarantrian' => 'Rawat Jalan',
                    'klasifikasipasien_id' => 5
                ],
                'klasifikasipasien_ids',
                false,
                null
            ],
            'not found by params data is null' => [
                null,
                'klasifikasipasien_ids',
                false,
                null
            ],
            
        ];
    }
}