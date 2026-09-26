<?php 

return [
    'jalan_napas_extra' => [
        'sumbatan-resusitasi' => [
            'remove' => [
                'parsial-emergent', 'bebas-emergent', 'bebas_non-urgent', 'stridor-emergent', 'snoring-emergent', 'gargling-emergent'
            ],
        ],
        'bebas-emergent' => [
            'remove' => [
                'parsial-emergent', 'bebas-urgent', 'sumbatan-resusitasi'
            ]
        ], 
        'parsial-emergent' => [
            'remove' => [
                'bebas_non-urgent', 'bebas-emergent', 'sumbatan-resusitasi'
            ]
        ],
        'bebas_non-urgent' => [
            'remove' => [
                'parsial-emergent', 'bebas-emergent', 'sumbatan-resusitasi'
            ]
        ],
        'stridor-emergent' => [
            'remove' => [
                'sumbatan-resusitasi'
            ]
        ],
        'snoring-emergent' => [
            'remove' => [
                'sumbatan-resusitasi'
            ]
        ],
        'gargling-emergent' => [
            'remove' => [
                'sumbatan-resusitasi'
            ]
        ],
    ],
    'pernapasan_extra' => [
        'tidak_ada_nafas_spontan-resusitasi' => [
            'remove-all-except' => [
                'sianosis-resusitasi', 'mengi-emergent'
            ]
        ],
        'ada_nafas_spontan-emergent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ]
        ],
        'frek._napas_:_<_10_x/mnt-resusitasi' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'frek._napas_:_>_30_x/mnt-emergent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'frek._napas_:_24-30_x/mnt-urgent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'frek._napas_:_20-24_x/mnt-less_urgent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'frek._napas_:_16-20_x/mnt-non_urgent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'distress_pernapasan_berat-resusitasi' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'distress_pernapasan_berat-emergent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'distress_pernapasan_ringan-urgent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'distress_pernapasan_tidak_ada-less_urgent' => [
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'sianosis-resusitasi' => [
            'allow-select-same-line' => true
        ],
        'mengi-emergent' => [
            'allow-select-same-line' => true,
            'add' => [
                'ada_nafas_spontan-emergent'
            ],
            'remove' => [
                'tidak_ada_nafas_spontan-resusitasi'
            ]
        ]
    ],
    'sirkulasi_extra' => [
        'nadi_tidak_teraba-resusitasi' => [
            'remove-all-except' => true
        ],
        'nadi_teraba_lemah-emergent' => [
            'remove' => [
                'nadi_teraba-urgent', 'frek._nadi_:_120-150_x/m-urgent', 'frek._nadi_:_100-120_x/m-less_urgent', 'frek._nadi_:_80-100_x/m-non_urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'frek._nadi_:_<_50_x/m-emergent' => [
            'remove' => [
                'frek._nadi_:_>_150_x/m-emergent', 'nadi_tidak_teraba-resusitasi', 'frek._nadi_:_120-150_x/m-urgent', 'frek._nadi_:_100-120_x/m-less_urgent', 'frek._nadi_:_80-100_x/m-non_urgent', 'nadi_teraba-urgent'
            ],
            'add' => [
                'nadi_teraba_lemah-emergent'
            ],
            'remove-not-sibling' => true
        ],
        'frek._nadi_:_>_150_x/m-emergent' => [
            'remove' => [
                'frek._nadi_:_<_50_x/m-emergent', 'nadi_tidak_teraba-resusitasi','nadi_teraba-urgent'
            ],
            'add' => [
                'nadi_teraba_lemah-emergent'
            ],
            'remove-not-sibling' => true
        ],
        'frek._nadi_:_120-150_x/m-urgent' => [
            'remove' => [
                'frek._nadi_:_<_50_x/m-emergent', 'nadi_tidak_teraba-resusitasi', 'frek._nadi_:_>_150_x/m-emergent', 'nadi_teraba_lemah-emergent'
            ],
            'add' => [
                'nadi_teraba-urgent'
            ],
            'remove-not-sibling' => true
        ],
        'frek._nadi_:_100-120_x/m-less_urgent' => [
            'add' => [
                'nadi_teraba-urgent'
            ],
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'frek._nadi_:_80-100_x/m-non_urgent' => [
            'add' => [
                'nadi_teraba-urgent'
            ],
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ],
            'remove-not-sibling' => true
        ],
        'warna_pucat-resusitasi' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'warna_pucat-emergent' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'warna_normal-non_urgent' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'akral_dingin-resusitasi' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'akral_dingin-emergent' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'akral_hangat-non_urgent' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'pengisian_kapiler_>_4_detik-resusitasi' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'pengisian_kapiler_:_2-4_detik-emergent' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'pengisian_kapiler_<_2_detik-urgent' => [
            'remove-not-sibling' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_perdarahan_hebat-emergent' => [
            'remove' => [
                'gs_perdarahan_sedang_berat-urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_perdarahan_hebat-emergent' => [
            'remove' => [
                'gs_perdarahan_sedang_berat-urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_syok-resusitasi' => [
            'remove' => [
                'gs_perdarahan_hebat-emergent', 'gs_perdarahan_sedang_berat-urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_perdarahan_hebat-emergent' => [
            'remove' => [
                'gs_perdarahan_sedang_berat-urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_perdarahan_sedang_berat-urgent' => [
            'remove' => [
                'gs_perdarahan_hebat-emergent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_nyeri_dada_kardiak-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_dehidrasi-urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'gs_muntah_terus_menerus-urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_sis_:_<_80_mmHg-resusitasi' => [
            'remove' => [
                'td_sis_:_120-160_mmHg-less_urgent', 'td_sis_:_90-120_mmHg-non_urgent', 'td_sis_:_>_160_mmHg-urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_sis_:_>_160_mmHg-urgent' => [
            'remove' => [
                'td_sis_:_120-160_mmHg-less_urgent', 'td_sis_:_90-120_mmHg-non_urgent', 'td_sis_:_<_80_mmHg-resusitasi', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_sis_:_120-160_mmHg-less_urgent' => [
            'remove' => [
                'td_sis_:_>_160_mmHg-urgent', 'td_sis_:_90-120_mmHg-non_urgent', 'td_sis_:_<_80_mmHg-resusitasi', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_sis_:_90-120_mmHg-non_urgent' => [
            'remove' => [
                'td_sis_:_120-160_mmHg-less_urgent', 'td_sis_:_>_160_mmHg-urgent', 'td_sis_:_<_80_mmHg-resusitasi', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_dias_:_>_100_mmHg-urgent' => [
            'remove' => [
                'td_dias_:_80-100_mmHg-less_urgent', 'td_dias_:_60-80_mmHg-non_urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_dias_:_80-100_mmHg-less_urgent' => [
            'remove' => [
                'td_dias_:_>_100_mmHg-urgent', 'td_dias_:_60-80_mmHg-non_urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ],
        'td_dias_:_60-80_mmHg-non_urgent' => [
            'remove' => [
                'td_dias_:_80-100_mmHg-less_urgent', 'td_dias_:_>_100_mmHg-urgent', 'nadi_tidak_teraba-resusitasi'
            ]
        ]
    ],
    'disability' => [
        'kejang_sedang_berlangsung-resusitasi' => [
            'remove-all-except' => true
        ],
        'neurologis_gelisah-emergent' => [
            'remove' => [
                'neurologis_apatis-urgent',
                'remove' => [
                    'kejang_sedang_berlangsung-resusitasi'
                ]
            ]
        ],
        'neurologis_apatis-urgent' => [
            'remove' => [
                'neurologis_gelisah-emergent',
                'remove' => [
                    'kejang_sedang_berlangsung-resusitasi'
                ]
            ]
        ],
        'neurologis_somnolen-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'neurologis_kaku_kuduk-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'neurologis_hemiparese/disfagia-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'neurologis_demam_lethargia-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_multipel-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_kimia_pada_mata-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_patah_tulang_mayor-emergent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_ringan_ekstremitas-less_urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_inflamasi-less_urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_luka_ringan-non_urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_kontrol_luka-non_urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'trauma_lebam-non_urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'muntah_/_diare-less_urgent' => [
            'remove' => [
                'sakit_dengan_gejala_ringan-non_urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'sakit_dengan_gejala_ringan-non_urgent' => [
            'remove' => [
                'muntah_/_diare-less_urgent', 'kejang_sedang_berlangsung-resusitasi'
            ],
        ],
        'rencana_imunisasi-non_urgent' => [
            'allow-select-same-line' => true,
            'remove' => [
                'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'nyeri_perut_sedang_berat-urgent' => [
            'remove' => [
                'nyeri_perut_ringan-urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'nyeri_perut_ringan-urgent' => [
            'remove' => [
                'nyeri_perut_sedang_berat-urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'nyeri_hebat-emergent' => [
            'remove' => [
                'nyeri_sedang_berat-urgent', 'nyeri_ringan-less_urgent', 'nyeri_ringan-non_urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'nyeri_sedang_berat-urgent' => [
            'remove' => [
                'nyeri_hebat-emergent', 'nyeri_ringan-less_urgent', 'nyeri_ringan-non_urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'nyeri_ringan-less_urgent' => [
            'remove' => [
                'nyeri_hebat-emergent', 'nyeri_sedang_berat-urgent', 'nyeri_ringan-non_urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
        'nyeri_ringan-non_urgent' => [
            'remove' => [
                'nyeri_hebat-emergent', 'nyeri_sedang_berat-urgent', 'nyeri_ringan-less_urgent', 'kejang_sedang_berlangsung-resusitasi'
            ]
        ],
    ]
];