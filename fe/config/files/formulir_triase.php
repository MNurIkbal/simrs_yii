<?php

return [

    'alergi' => [
        'tidak'      => 'Tidak',
        '1'         => 'Ya',
        'tidak_tahu' => 'Tidak Tahu'
    ],

    'trauma' => [
        'non_trauma'           => 'Non Trauma',
        'trauma'               => 'Trauma',
        'kebidanan'            => 'Kebidanan',
        'non_bedah'            => 'Non Bedah',
        'psikiatri'            => 'Psikiatri',
        'anak'                 => 'Anak',
        'bedah_trauma_kll'     => 'Bedah Trauma Kll',
        'bedah_trauma_non_kll' => 'Bedah Trauma Non Kll',
        'bedah_non_trauma'     => 'Bedah Non Trauma',
    ],

    'jalan_napas_extra' => [
        'sumbatan' => [
            'resusitasi' => [
                'sumbatan-resusitasi' => 'Total',
            ],
            'emergent' => [
                'parsial-emergent' => 'Parsial',
            ],
            'urgent' => [],
            'false_emergency' => [],
            'non_urgent' => [
                'bebas_non-urgent' => 'Bebas'
            ],
        ],
        'gejala_lain' => [
            'resusitasi' => [],
            'emergent' => [
                'stridor-emergent' => 'Stridor',
                'snoring-emergent' => 'Snoring',
                'gargling-emergent' => 'Gargling'
            ]
        ]
    ],

    'jalan_nafas' => [
        'resusitasi' => [
            'sumbatan-resusitasi' => 'Sumbatan (obstruction)'
        ],
        'emergent' => [
            'bebas-emergent' => 'Bebas (patent)'
        ],
        'urgent' => [
            'bebas-urgent' => 'Bebas (Patent)',
        ],
        'non_urgent' => [
            'bebas_non-urgent' => 'Bebas (Patent)'
        ],
        'false_emergency' => [
            'bebas_false-emergency' => 'Bebas (Patent)'
        ],
    ],

    'pernafasan' => [
        'resusitasi' => [
            'henti_napas-resusitasi'              => 'Henti napas',
            'frek._napas_:_<_10_x/mnt-resusitasi' => 'Frek. Napas : < 10 x/mnt',
            'sianosis-resusitasi'                 => 'Sianosis'
        ],
        'emergent' => [
            'frek._napas_:_>_32_x/mnt-emergent' => 'Frek. Napas : > 32 x/mnt',
            'wheezing-emergent'                 => 'Wheezing'
        ],
        'urgent' => [
            'frek._napas_:_24-32_x/mnt-urgent' => 'Frek. Napas : 24-32 x/mnt',
            'wheezing-urgent'                  => 'Wheezing'
        ],
        'non_urgent' => [
            'normal-non_urgent' => 'Normal'
        ],
        'false_emergency' => [
            'normal-false_emergency' => 'Normal'
        ],
    ],

    'pernapasan_extra' => [
        'nafas_spontan' => [
            'resusitasi' => [
                'tidak_ada_nafas_spontan-resusitasi' => 'Tidak ada'
            ],
            'emergent' => [],
            'urgent' => [],
            'less_urgent' => [],
            'non_urgent' => [
                'ada_nafas_spontan-emergent' => 'Ada'
            ]
        ],
        'frekuensi_napas' => [
            'resusitasi' => [
                'frek._napas_:_<_10_x/mnt-resusitasi' => '< 10x/m'
            ],
            'emergent' => [
                'frek._napas_:_>_30_x/mnt-emergent' => '> 30x/m',
            ],
            'urgent' => [
                'frek._napas_:_24-30_x/mnt-urgent' => '24-30x/m',
            ],
            'less_urgent' => [
                'frek._napas_:_20-24_x/mnt-less_urgent' => '20-24x/m',
            ],
            'non_urgent' => [
                'frek._napas_:_16-20_x/mnt-non_urgent' => '16-20x/m',
            ]
        ],
        'distress_pernapasan' => [
            'resusitasi' => [
                'distress_pernapasan_berat-resusitasi' => 'Berat'
            ],
            'emergent' => [
                'distress_pernapasan_berat-emergent' => 'Berat'
            ],
            'urgent' => [
                'distress_pernapasan_ringan-urgent' => 'Ringan'
            ],
            'less_urgent' => [],
            'non_urgent' => [
                'distress_pernapasan_tidak_ada-less_urgent' => 'Tidak ada'
            ],
        ],
        'gejala_lain' => [
            'resusitasi' => [
                'sianosis-resusitasi' => 'Sianosis'
            ],
            'emergent' => [],
            'urgent' => [
                'mengi-emergent' => 'Mengi'
            ],
            'less_urgent' => [],
            'non_urgent' => []
        ],
    ],

    'sirkulasi' => [
        'resusitasi' => [
            'henti_jantung-resusitasi'           => 'Henti jantung',
            'nadi_tidak_teraba-resusitasi'       => 'Nadi tidak teraba (pulseness)',
            'bp_<_80-resusitasi'                 => 'BP < 80 (adult)',
            'anak/infant_shock_berat-resusitasi' => 'Anak/Infant shock berat',
            'akral_dingin-resusitasi'            => 'Akral dingin (clammy)'
        ],
        'emergent' => [
            'nadi_teraba_lemah-emergent'      => 'Nadi teraba lemah (weakness pulse)',
            'nadi_(HR)_:_<_50_x/mnt-emergent' => 'Nadi (HR) : < 50 x/mnt',
            'nadi_(HR)_:_>_50_x/mnt-emergent' => 'Nadi (HR) : > 50 x/mnt',
            'pucat-emergent'                  => 'Pucat (Pale)',
            'akral_dingin-emergent'           => 'Akral dingin (Clamny)',
            'crt_<_2_detik-emergent'          => 'CRT < 2 Detik'
        ],
        'urgent' => [
            'frek_nadi_(HR):120-160_x/mnt-urgent' => 'Frek Nadi (HR):120-160 x/mnt',
            'td_sys_:_>_160_mmHg-urgent'          => 'TD Sys : > 160 mmHg',
            'td_diast_:_>_100_mmHg-urgent'        => 'TD Diast : > 100 mmHg'
        ],
        'non_urgent' => [],
        'false_emergency' => [],
    ],

    'sirkulasi_extra' => [
        'nadi' => [
            'resusitasi' => [
                'nadi_tidak_teraba-resusitasi' => 'Tidak teraba'
            ],
            'emergent' => [
                'nadi_teraba_lemah-emergent' => 'Teraba lemah'
            ],
            'urgent' => [],
            'less_urgent' => [],
            'non_urgent' => [
                'nadi_teraba-urgent' => 'Teraba'
            ]
        ],
        'frekuensi_nadi' => [
            'resusitasi' => [
                'frek._nadi_tidak_teraba-resusitasi' => 'Tidak teraba'
            ],
            'emergent' => [
                'frek._nadi_:_<_50_x/m-emergent' => '< 50x/m',
                'frek._nadi_:_>_150_x/m-emergent' => '> 150x/m'
            ],
            'urgent' => [
                'frek._nadi_:_120-150_x/m-urgent' => '120-150x/m'
            ],
            'less_urgent' => [
                'frek._nadi_:_100-120_x/m-less_urgent' => '100-120x/m'
            ],
            'non_urgent' => [
                'frek._nadi_:_80-100_x/m-non_urgent' => '80-100x/m'
            ]
        ],
        'warna' => [
            'resusitasi' => [
                'warna_pucat-resusitasi' => 'Pucat'
            ],
            'emergent' => [
                'warna_pucat-emergent' => 'Pucat'
            ],
            'urgent' => [],
            'less_urgent' => [],
            'non_urgent' => [
                'warna_normal-less_urgent' => 'Normal'
            ]
        ],
        'akral' => [
            'resusitasi' => [
                'akral_dingin-resusitasi' => 'Dingin'
            ],
            'emergent' => [
                'akral_dingin-emergent' => 'Dingin'
            ],
            'urgent' => [],
            'less_urgent' => [],
            'non_urgent' => [
                'akral_hangat-less_urgent' => 'Hangat'
            ]
        ],
        'pengisian_kapiler' => [
            'resusitasi' => [
                'pengisian_kapiler_>_4_detik-resusitasi' => '> 4 detik'
            ],
            'emergent' => [
                'pengisian_kapiler_:_2-4_detik-emergent' => '2-4 detik'
            ],
            'urgent' => [],
            'less_urgent' => [],
            'non_urgent' => [
                'pengisian_kapiler_<_2_detik-urgent' => '< 2 detik'
            ]
        ],
        'tekanan_darah' => [
            'resusitasi' => [
                'td_sis_:_<_80_mmHg-resusitasi' => 'Sis: < 80 mmHg'
            ],
            'emergent' => [],
            'urgent' => [
                'td_sis_:_>_160_mmHg-urgent' => 'Sis: > 160 mmHg',
                'td_dias_:_>_100_mmHg-urgent' => 'Dias: > 100 mmHg'
            ],
            'less_urgent' => [
                'td_sis_:_120-160_mmHg-less_urgent' => 'Sis: 120-160 mmHg',
                'td_dias_:_80-100_mmHg-less_urgent' => 'Dias: 80-100 mmHg'
            ],
            'non_urgent' => [
                'td_sis_:_90-120_mmHg-non_urgent' => 'Sis: 90-120 mmHg',
                'td_dias_:_60-80_mmHg-non_urgent' => 'Dias: 60-80 mmHg'
            ],
        ],
        'gangguan_sirkulasi' => [
            'resusitasi' => [
                'gs_syok-resusitasi' => 'Syok'
            ],
            'emergent' => [
                'gs_perdarahan_hebat-emergent' => 'Perdarahan hebat',
                'gs_nyeri_dada_kardiak-emergent' => 'Nyeri dada kardiak',
            ],
            'urgent' => [
                'gs_perdarahan_sedang_berat-urgent' => 'Perdarahan sedang-berat',
                'gs_dehidrasi-urgent' => 'Dehidrasi',
                'gs_muntah_terus_menerus-urgent' => 'Muntah terus menerus'
            ]
        ],
    ],

    'disability' => [
        'kejang' => [
            'resusitasi' => [
                'kejang_sedang_berlangsung-resusitasi' => 'Sedang berlangsung'
            ],
            'emergent' => [],
            'urgent' => [
                'kejang_riwayat_kejang-urgent' => 'Riwayat kejang (sadar saat pemeriksaan)'
            ]
        ],
        'neurologis' => [
            'resusitasi' => [],
            'emergent' => [
                'neurologis_gelisah-emergent' => 'Gelisah',
                'neurologis_somnolen-emergent' => 'Somnolen',
                'neurologis_kaku_kuduk-emergent' => 'Kaku kuduk (pada bayi usia < 28 hari)',
                'neurologis_hemiparese/disfagia-emergent' => 'Hemiparese/disfagia',
                'neurologis_demam_lethargia-emergent' => 'Demam dengan tanda-tanda lethargia'
            ],
            'urgent' => [
                'neurologis_apatis-urgent' => 'Apatis'
            ]
        ],
        'trauma_kepala' => [
            'resusitasi' => [],
            'emergent' => [
                'trauma_kepala_berat-emergent' => 'Berat'
            ],
            'urgent' => [
                'trauma_kepala_dengan_kehilangan_kesadaran-urgent' => 'Dengan kehilangan kesadaran (sadar saat pemeriksaan)'
            ],
            'less_urgent' => [
                'trauma_kepala_tanpa_kehilangan_kesadaran-less_urgent' => 'Tanpa kehilangan kesadaran'
            ]
        ],
        'trauma_lain' => [
            'resusitasi' => [],
            'emergent' => [
                'trauma_multipel-emergent' => 'Trauma multipel',
                'trauma_patah_tulang_mayor-emergent' => 'Patah tulang mayor',
                'trauma_kimia_pada_mata-emergent' => 'Trauma kimia pada mata'
            ],
            'urgent' => [],
            'less_urgent' => [
                'trauma_ringan_ekstremitas-less_urgent' => 'Trauma ringan pada ekstremitas',
                'trauma_inflamasi-less_urgent' => 'Inflamasi/benda asing di mata'
            ],
            'non_urgent' => [
                'trauma_luka_ringan-non_urgent' => 'Luka ringan',
                'trauma_kontrol_luka-non_urgent' => 'Kontrol luka',
                'trauma_lebam-non_urgent' => 'Lebam post trauma ringan'
            ]
        ],
        'nyeri' => [
            'resusitasi' => [],
            'emergent' => [
                'nyeri_hebat-emergent' => 'Nyeri hebat (apa pun sebabnya)'
            ],
            'urgent' => [
                'nyeri_sedang_berat-urgent' => 'Nyeri sedang-berat (apa pun sebabnya)',
                'nyeri_perut_sedang_berat-urgent' => 'Nyeri perut sedang-berat'
            ],
            'less_urgent' => [
                'nyeri_ringan-less_urgent' => 'Nyeri ringan (apa pun sebabnya)',
                'nyeri_perut_ringan-urgent' => 'Nyeri perut ringan'
            ],
            'non_urgent' => [
                'nyeri_ringan-non_urgent' => 'Nyeri ringan',
            ]
        ],
        'lainnya' => [
            'resusitasi' => [],
            'emergent' => [],
            'urgent' => [],
            'less_urgent' => [
                'muntah_/_diare-less_urgent' => 'Muntah/diare (tanpa tanda dehidrasi)'
            ],
            'non_urgent' => [
                'sakit_dengan_gejala_ringan-non_urgent' => 'Sakit dengan gejala ringan',
                'rencana_imunisasi-non_urgent' => 'Rencana imunisasi'
            ]
        ],
    ],

    'waktu_respon' => [
        'resusitasi' => [
            'segera-resusitasi' => 'Segera'
        ],
        'emergent' => [
            '10_menit-emergent' => '10 Menit'
        ],
        'urgent' => [
            '30_menit-urgent' => '30 Menit'
        ],
        'less_urgent' => [
            '60_menit-less_urgent' => '60 Menit'
        ],
        'non_urgent' => [
            '120_menit-non_urgent' => '120 Menit'
        ],


    ],

    'observation_site' => [
        'resusitasi' => [
            'ruang_resusitasi-resusitasi' => 'Ruang resusitasi'
        ],
        'emergent' => [
            'ruang_resusitasi-emergent' => 'Ruang resusitasi'
        ],
        'urgent' => [
            'ruang_observasi_biasa-urgent' => 'Ruang observasi biasa'
        ],
        'less_urgent' => [
            'ruang_observasi_biasa-less_urgent' => 'Ruang observasi biasa'
        ],
        'non_urgent' => [
            'ruang_observasi_biasa-non_urgent' => 'Ruang observasi biasa'
        ],
    ],

    'kesadaran' => [
        'resusitasi' => '9',
        'emergent' => '11',
        'urgent' => '14',
        // 'less_urgent' => '15',
        'non_urgent' => '15',
    ]
];
