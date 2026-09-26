CREATE VIEW notifikasijoborder_v AS 
            SELECT
            'Tindakan' :: TEXT AS tipe,
            FALSE AS is_obat,
            instruksitindakan_t.instruksitindakan_id AS joborder_id,
            instruksi_t.tgl_instruksi AS tgl_pelayanan,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            instruksitindakan_t.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            instruksitindakan_t.penjamin_id,
            instruksitindakan_t.kelaspelayanan_id,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            instruksitindakan_t.qty,
            0 AS harga,
            instalasi_m.instalasi_nama AS instalasi_tujuan,
            petugas.pegawai_nama 
            FROM
                instruksi_t
                JOIN (
                SELECT A
                    .instruksi_id,
                    A.pendaftaran_id,
                    A.instruksitindakan_id,
                    A.instalasi_id,
                    A.ruangan_id,
                    A.penjamin_id,
                    A.kelaspelayanan_id,
                    A.status_implementasi,
                    A.qty,
                    A.is_deleted,
                    A.daftartindakan_id,
                    A.created_by 
                FROM
                    instruksitindakan_t A 
                ) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id
                JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN ( SELECT A.pendaftaran_id, A.pasienadmisi_id FROM pendaftaran_t A ) pendaftaran_t ON instruksitindakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON instruksitindakan_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN ( SELECT tindakanpelayanan_t_1.instruksitindakan_id FROM tindakanpelayanan_t tindakanpelayanan_t_1 WHERE tindakanpelayanan_t_1.is_deleted IS FALSE ) tindakanpelayanan_t ON instruksitindakan_t.instruksitindakan_id = tindakanpelayanan_t.instruksitindakan_id
                LEFT JOIN (
                SELECT A
                    .loginpemakai_id,
                    b.nama_pegawai AS pegawai_nama 
                FROM
                    loginpemakai_k
                    A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                ) petugas ON instruksitindakan_t.created_by = petugas.loginpemakai_id 
            WHERE
                instruksitindakan_t.status_implementasi :: TEXT = '454' :: TEXT 
                AND instruksitindakan_t.is_deleted IS FALSE 
                AND tindakanpelayanan_t.instruksitindakan_id IS NULL UNION ALL
            SELECT
                'Paket' :: TEXT AS tipe,
                FALSE AS is_obat,
                instruksitindakan_t.instruksitindakan_id AS joborder_id,
                instruksi_t.tgl_instruksi AS tgl_pelayanan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                instruksitindakan_t.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                instruksitindakan_t.penjamin_id,
                instruksitindakan_t.kelaspelayanan_id,
                tipepaket_m.tipepaket_id AS daftartindakan_id,
                tipepaket_m.tipepaket_nama AS daftartindakan_nama,
                instruksitindakan_t.qty,
                0 AS harga,
                instalasi_m.instalasi_nama AS instalasi_tujuan,
                petugas.pegawai_nama 
            FROM
                instruksi_t
                JOIN (
                SELECT A
                    .instruksi_id,
                    A.pendaftaran_id,
                    A.instruksitindakan_id,
                    A.instalasi_id,
                    A.ruangan_id,
                    A.penjamin_id,
                    A.kelaspelayanan_id,
                    A.status_implementasi,
                    A.qty,
                    A.is_deleted,
                    A.tipepaket_id,
                    A.created_by 
                FROM
                    instruksitindakan_t A 
                ) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id
                JOIN ( SELECT A.tipepaket_id, A.tipepaket_nama FROM tipepaket_m A ) tipepaket_m ON instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id
                JOIN ( SELECT A.pendaftaran_id, A.pasienadmisi_id FROM pendaftaran_t A ) pendaftaran_t ON instruksitindakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON instruksitindakan_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN (
                SELECT A
                    .loginpemakai_id,
                    b.nama_pegawai AS pegawai_nama 
                FROM
                    loginpemakai_k
                    A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                ) petugas ON instruksitindakan_t.created_by = petugas.loginpemakai_id 
            WHERE
                instruksitindakan_t.status_implementasi :: TEXT = '454' :: TEXT 
                AND instruksitindakan_t.is_deleted IS FALSE UNION ALL
            SELECT
                'BMHP' :: TEXT AS tipe,
                TRUE AS is_obat,
                instruksitindakanbmhp_t.instruksitindakan_id AS joborder_id,
                instruksi_t.tgl_instruksi AS tgl_pelayanan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.pasienadmisi_id,
                instruksitindakanbmhp_t.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                instruksitindakanbmhp_t.penjamin_id,
                instruksitindakanbmhp_t.kelaspelayanan_id,
                obatalkes_m.obatalkes_id AS daftartindakan_id,
                obatalkes_m.obatalkes_nama AS daftartindakan_nama,
                instruksitindakanbmhp_t.qty,
                obatalkes_m.harganetto AS harga,
                instalasi_m.instalasi_nama AS instalasi_tujuan,
                petugas.pegawai_nama 
            FROM
                instruksi_t
                JOIN (
                SELECT A
                    .pendaftaran_id,
                    A.instruksi_id,
                    A.obatalkes_id,
                    A.instruksitindakan_id,
                    A.ruangan_id,
                    A.penjamin_id,
                    A.kelaspelayanan_id,
                    A.qty,
                    A.instalasi_id,
                    A.status_implementasi,
                    A.is_deleted,
                    A.created_by 
                FROM
                    instruksitindakanbmhp_t A 
                ) instruksitindakanbmhp_t ON instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id
                JOIN ( SELECT A.obatalkes_id, A.obatalkes_nama, A.harganetto FROM obatalkes_m A ) obatalkes_m ON instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id
                JOIN ( SELECT A.pendaftaran_id, A.pasienadmisi_id FROM pendaftaran_t A ) pendaftaran_t ON instruksitindakanbmhp_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON instruksitindakanbmhp_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN (
                SELECT A
                    .loginpemakai_id,
                    b.nama_pegawai AS pegawai_nama 
                FROM
                    loginpemakai_k
                    A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                ) petugas ON instruksitindakanbmhp_t.created_by = petugas.loginpemakai_id 
            WHERE
                instruksitindakanbmhp_t.status_implementasi :: TEXT = '454' :: TEXT 
                AND instruksitindakanbmhp_t.is_deleted IS FALSE UNION ALL
            SELECT
                'Penunjang RI' :: TEXT AS tipe,
                FALSE AS is_obat,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AS joborder_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pelayanan,
                pasienadmisi_t.pendaftaran_id,
                pasienadmisi_t.pasienadmisi_id,
                pasienkirimkeunitlain_t.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                pasienadmisi_t.penjamin_id,
                pasienadmisi_t.kelaspelayanan_id,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                permintaankepenunjang_t.qtypermintaan AS qty,
                0 AS harga,
                instalasi_m.instalasi_nama AS instalasi_tujuan,
                petugas.pegawai_nama 
            FROM
                pasienkirimkeunitlain_t
                JOIN (
                SELECT A
                    .daftartindakan_id,
                    A.pasienkirimkeunitlain_id,
                    A.qtypermintaan 
                FROM
                    permintaankepenunjang_t A 
                WHERE
                    A.is_approve IS FALSE 
                    AND A.is_deleted IS FALSE 
                ) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_kode, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN (
                SELECT A
                    .pasienadmisi_id,
                    b.no_pendaftaran,
                    b.pendaftaran_id,
                    A.kelaspelayanan_id,
                    A.penjamin_id 
                FROM
                    pasienadmisi_t
                    A JOIN pendaftaran_t b ON A.pasienadmisi_id = b.pasienadmisi_id 
                ) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN (
                SELECT A
                    .loginpemakai_id,
                    b.nama_pegawai AS pegawai_nama 
                FROM
                    loginpemakai_k
                    A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                ) petugas ON pasienkirimkeunitlain_t.created_by = petugas.loginpemakai_id 
            WHERE
                pasienkirimkeunitlain_t.pasienmasukpenunjang_id IS NULL 
                AND ( pasienkirimkeunitlain_t.status_penunjang :: TEXT <> ALL ( ARRAY [ '472' :: CHARACTER VARYING :: TEXT, '541' :: CHARACTER VARYING :: TEXT ] ) ) UNION ALL
            SELECT
                'Penunjang RD/RJ' :: TEXT AS tipe,
                FALSE AS is_obat,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AS joborder_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pelayanan,
                pendaftaran_t.pendaftaran_id,
                pasienkirimkeunitlain_t.pasienadmisi_id,
                pasienkirimkeunitlain_t.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.kelaspelayanan_id,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                permintaankepenunjang_t.qtypermintaan AS qty,
                0 AS harga,
                instalasi_m.instalasi_nama AS instalasi_tujuan,
                petugas.pegawai_nama 
            FROM
                pasienkirimkeunitlain_t
                JOIN (
                SELECT A
                    .daftartindakan_id,
                    A.pasienkirimkeunitlain_id,
                    A.qtypermintaan 
                FROM
                    permintaankepenunjang_t A 
                WHERE
                    A.is_approve IS FALSE 
                    AND A.is_deleted IS FALSE 
                ) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_kode, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN ( SELECT A.no_pendaftaran, A.pendaftaran_id, A.kelaspelayanan_id, A.penjamin_id FROM pendaftaran_t A ) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN (
                SELECT A
                    .loginpemakai_id,
                    b.nama_pegawai AS pegawai_nama 
                FROM
                    loginpemakai_k
                    A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                ) petugas ON pasienkirimkeunitlain_t.created_by = petugas.loginpemakai_id 
            WHERE
                pasienkirimkeunitlain_t.pasienmasukpenunjang_id IS NULL 
                AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL 
                AND ( pasienkirimkeunitlain_t.status_penunjang :: TEXT <> ALL ( ARRAY [ '472' :: CHARACTER VARYING :: TEXT, '541' :: CHARACTER VARYING :: TEXT ] ) ) UNION ALL
            SELECT
                'Penunjang Bedah Approve' :: TEXT AS tipe,
                FALSE AS is_obat,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AS joborder_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pelayanan,
                pendaftaran_t.pendaftaran_id,
                pasienkirimkeunitlain_t.pasienadmisi_id,
                pasienkirimkeunitlain_t.ruangan_id,
                ruangan_m.ruangan_nama,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.kelaspelayanan_id,
                daftartindakan_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                permintaankepenunjang_t.qtypermintaan AS qty,
                0 AS harga,
                instalasi_m.instalasi_nama AS instalasi_tujuan,
                petugas.pegawai_nama 
            FROM
                pasienkirimkeunitlain_t
                JOIN (
                SELECT A
                    .daftartindakan_id,
                    A.pasienkirimkeunitlain_id,
                    A.qtypermintaan 
                FROM
                    permintaankepenunjang_t A 
                WHERE
                    A.is_approve IS FALSE 
                    AND A.is_deleted IS FALSE 
                ) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                JOIN ( SELECT A.daftartindakan_id, A.daftartindakan_kode, A.daftartindakan_nama FROM daftartindakan_m A ) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN ( SELECT A.no_pendaftaran, A.pendaftaran_id, A.kelaspelayanan_id, A.penjamin_id FROM pendaftaran_t A ) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN (
                SELECT A
                    .loginpemakai_id,
                    b.nama_pegawai AS pegawai_nama 
                FROM
                    loginpemakai_k
                    A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                ) petugas ON pasienkirimkeunitlain_t.created_by = petugas.loginpemakai_id 
            WHERE
                pasienkirimkeunitlain_t.pasienadmisi_id IS NULL 
                AND pasienkirimkeunitlain_t.status_penunjang :: TEXT <> '541' :: TEXT 
                AND pasienkirimkeunitlain_t.instalasi_id = 12 
                AND NOT ( pasienkirimkeunitlain_t.pasienmasukpenunjang_id IN ( SELECT verifikasibedah_r.pasienmasukpenunjang_id FROM verifikasibedah_r ) ) UNION ALL
            SELECT
                'Penunjang Bedah' :: TEXT AS tipe,
                FALSE AS is_obat,
                verifikasibedah_r.ID AS joborder_id,
                verifikasibedah_r.created_date AS tgl_pelayanan,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienadmisi_t.pasienadmisi_id,
                pasienmasukpenunjang_t.ruangan_id,
                ruangan_m.ruangan_nama,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                pasienadmisi_t.penjamin_id,
                pasienadmisi_t.kelaspelayanan_id,
                verifikasibedah_r.daftartindakan_id,
                verifikasibedah_r.daftartindakan_nama,
                verifikasibedah_r.qty,
            CASE
            
            WHEN verifikasibedah_r.kode_posisi :: TEXT = '508' :: TEXT THEN
            CASE
                WHEN verifikasibedah_r.persentase > 0 :: DOUBLE PRECISION THEN
            CASE
                    
                    WHEN verifikasibedah_r.is_cyto IS TRUE 
                    AND verifikasibedah_r.is_penyulit IS FALSE THEN
                        ( verifikasibedah_r.harga + verifikasibedah_r.harga * verifikasibedah_r.persencyto_tindakan / 100 :: DOUBLE PRECISION ) * verifikasibedah_r.persentase / 100 :: DOUBLE PRECISION 
                        WHEN verifikasibedah_r.is_cyto IS FALSE 
                        AND verifikasibedah_r.is_penyulit IS TRUE THEN
                            ( verifikasibedah_r.harga + verifikasibedah_r.harga * verifikasibedah_r.persen_penyulit / 100 :: DOUBLE PRECISION ) * verifikasibedah_r.persentase / 100 :: DOUBLE PRECISION 
                            WHEN verifikasibedah_r.is_cyto IS TRUE 
                            AND verifikasibedah_r.is_penyulit IS TRUE THEN
                                ( verifikasibedah_r.harga + verifikasibedah_r.harga * ( verifikasibedah_r.persencyto_tindakan + verifikasibedah_r.persen_penyulit ) / 100 :: DOUBLE PRECISION ) * verifikasibedah_r.persentase / 100 :: DOUBLE PRECISION ELSE verifikasibedah_r.harga * verifikasibedah_r.persentase / 100 :: DOUBLE PRECISION 
                                END ELSE verifikasibedah_r.harga 
                        END ELSE verifikasibedah_r.harga 
                    END AS harga,
                    instalasi_m.instalasi_nama AS instalasi_tujuan,
                    petugas.pegawai_nama 
                FROM
                    verifikasibedah_r
                    JOIN ( SELECT A.pendaftaran_id, A.ruangan_id, A.pasienmasukpenunjang_id, A.status_periksa FROM pasienmasukpenunjang_t A ) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = verifikasibedah_r.pasienmasukpenunjang_id
                    JOIN ( SELECT A.pendaftaran_id, A.pasienadmisi_id FROM pendaftaran_t A ) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    LEFT JOIN ( SELECT pasienadmisi_t_1.pasienadmisi_id, pasienadmisi_t_1.penjamin_id, pasienadmisi_t_1.kelaspelayanan_id FROM pasienadmisi_t pasienadmisi_t_1 ) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                    JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A ) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
                    JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    LEFT JOIN (
                    SELECT A
                        .loginpemakai_id,
                        b.nama_pegawai AS pegawai_nama 
                    FROM
                        loginpemakai_k
                        A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                    ) petugas ON verifikasibedah_r.created_by = petugas.loginpemakai_id 
                WHERE
                    ( pasienmasukpenunjang_t.status_periksa :: TEXT <> ALL ( ARRAY [ '483' :: CHARACTER VARYING :: TEXT, '476' :: CHARACTER VARYING :: TEXT ] ) ) 
                    AND verifikasibedah_r.is_deleted IS FALSE UNION ALL
                SELECT
                    'Reseptur' :: TEXT AS tipe,
                    TRUE AS is_obat,
                    instruksi_t.instruksi_id AS joborder_id,
                    instruksi_t.tgl_instruksi AS tgl_pelayanan,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    reseptur_t.ruangan_id,
                    ruangan_m.ruangan_nama,
                    instalasi_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    pendaftaran_t.penjamin_id,
                    pendaftaran_t.kelaspelayanan_id,
                    obatalkes_m.obatalkes_id AS daftartindakan_id,
                    obatalkes_m.obatalkes_nama AS daftartindakan_nama,
                    resepturdetail_t.qty_reseptur AS qty,
                    resepturdetail_t.hargasatuan_reseptur AS harga,
                    instalasi_m.instalasi_nama AS instalasi_tujuan,
                    petugas.pegawai_nama 
                FROM
                    instruksi_t
                    JOIN (
                    SELECT A
                        .instruksi_id,
                        A.reseptur_id,
                        A.ruangan_id,
                        A.pendaftaran_id,
                        A.status_reseptur,
                        A.penjualanresep_id 
                    FROM
                        reseptur_t A 
                    ) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id 
                    AND reseptur_t.penjualanresep_id
                    IS NULL JOIN ( SELECT A.reseptur_id, A.obatalkes_id, A.qty_reseptur, A.hargasatuan_reseptur FROM resepturdetail_t A WHERE A.is_deleted IS FALSE ) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
                    JOIN ( SELECT A.obatalkes_id, A.obatalkes_nama, A.harganetto FROM obatalkes_m A ) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                    JOIN ( SELECT pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.penjamin_id, pendaftaran_t_1.kelaspelayanan_id FROM pendaftaran_t pendaftaran_t_1 ) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A ) ruangan_m ON reseptur_t.ruangan_id = ruangan_m.ruangan_id
                    LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    LEFT JOIN (
                    SELECT A
                        .loginpemakai_id,
                        b.nama_pegawai AS pegawai_nama 
                    FROM
                        loginpemakai_k
                        A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                    ) petugas ON instruksi_t.created_by = petugas.loginpemakai_id 
                WHERE
                    reseptur_t.status_reseptur <> ALL ( ARRAY [ 660, 432 ] ) UNION ALL
                SELECT
                CASE
                        
                    WHEN COALESCE
                        ( resepturracikan_t.TYPE, 'OR' :: CHARACTER VARYING ) :: TEXT = 'OR' :: TEXT THEN
                            'Reseptur Racikan' :: TEXT ELSE'Reseptur Non Racikan' :: TEXT 
                            END AS tipe,
                        TRUE AS is_obat,
                        instruksi_t.instruksi_id AS joborder_id,
                        instruksi_t.tgl_instruksi AS tgl_pelayanan,
                        pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasienadmisi_id,
                        reseptur_t.ruangan_id,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_id,
                        instalasi_m.instalasi_nama,
                        pendaftaran_t.penjamin_id,
                        pendaftaran_t.kelaspelayanan_id,
                        NULL :: INTEGER AS daftartindakan_id,
                        resepturracikan_t.racikan AS daftartindakan_nama,
                        NULL :: DOUBLE PRECISION AS qty,
                        1 AS harga,
                        instalasi_m.instalasi_nama AS instalasi_tujuan,
                        petugas.pegawai_nama 
                    FROM
                        instruksi_t
                        JOIN (
                        SELECT A
                            .instruksi_id,
                            A.reseptur_id,
                            A.ruangan_id,
                            A.pendaftaran_id,
                            A.status_reseptur,
                            A.penjualanresep_id 
                        FROM
                            reseptur_t A 
                        ) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id 
                        AND reseptur_t.penjualanresep_id
                        IS NULL JOIN ( SELECT resepturracikan_t_1.reseptur_id, resepturracikan_t_1.racikan, resepturracikan_t_1.TYPE FROM resepturracikan_t resepturracikan_t_1 ) resepturracikan_t ON reseptur_t.reseptur_id = resepturracikan_t.reseptur_id
                        JOIN (
                        SELECT A
                            .pasienadmisi_id,
                            A.no_pendaftaran,
                            A.pendaftaran_id,
                            COALESCE ( b.kelaspelayanan_id, A.kelaspelayanan_id ) AS kelaspelayanan_id,
                            COALESCE ( b.penjamin_id, A.penjamin_id ) AS penjamin_id 
                        FROM
                            pendaftaran_t
                            A LEFT JOIN pasienadmisi_t b ON A.pasienadmisi_id = b.pasienadmisi_id 
                        ) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A ) ruangan_m ON reseptur_t.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        LEFT JOIN (
                        SELECT A
                            .loginpemakai_id,
                            b.nama_pegawai AS pegawai_nama 
                        FROM
                            loginpemakai_k
                            A JOIN ( SELECT pegawai_m.pegawai_id, pegawai_m.nama_pegawai FROM pegawai_m ) b ON b.pegawai_id = A.pegawai_id 
                        ) petugas ON instruksi_t.created_by = petugas.loginpemakai_id 
                WHERE
            reseptur_t.status_reseptur <> ALL ( ARRAY [ 660, 432 ] );