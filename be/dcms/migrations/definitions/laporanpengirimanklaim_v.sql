CREATE OR REPLACE VIEW public.laporanpengirimanklaim_v
    AS SELECT sy_kunjungan.kunjungan_id,
        sy_kunjungan.no_rekammedik,
        sy_kunjungan.nosep,
        sy_kunjungan.no_pendaftaran,
        sy_kunjungan.nama_pasien,
        sy_kunjungan.tgl_pendaftaran,
        sy_kunjungan.tgl_pulang,
        sy_klaiminacbg.total_tarifrs,
        sy_klaiminacbg.is_terkirim,
        sy_klaimgroup_t.group_nama,
        sy_klaimgroup_t.total as group_tarif,
        sy_klaimgroup_t.cbg,
        sy_klaimgroup_t.additional_data,
        sy_klaimgroup_t.created_date AS tgl_klaim
    FROM sy_kunjungan
        JOIN ( SELECT sy_klaiminacbg_1.total_tarifrs,
                sy_klaiminacbg_1.is_terkirim,
                sy_klaiminacbg_1.kunjungan_id,
                sy_klaiminacbg_1.klaimgroup_id
            FROM sy_klaiminacbg sy_klaiminacbg_1
            WHERE sy_klaiminacbg_1.is_deleted IS FALSE AND sy_klaiminacbg_1.is_active IS TRUE) sy_klaiminacbg ON sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id
        JOIN ( SELECT sy_klaimgroup_t_1.sy_klaimgroup_id,
                sy_klaimgroup_t_1.group_nama,
                sy_klaimgroup_t_1.group_tarif,
                sy_klaimgroup_t_1.total,
                sy_klaimgroup_t_1.cbg,
                sy_klaimgroup_t_1.additional_data,
                sy_klaimgroup_t_1.created_date
            FROM sy_klaimgroup_t sy_klaimgroup_t_1
            WHERE sy_klaimgroup_t_1.is_deleted IS FALSE AND sy_klaimgroup_t_1.is_active IS TRUE) sy_klaimgroup_t ON sy_klaimgroup_t.sy_klaimgroup_id = sy_klaiminacbg.klaimgroup_id
    WHERE sy_kunjungan.status_kunjungan = 551;