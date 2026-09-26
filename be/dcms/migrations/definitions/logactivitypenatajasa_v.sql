CREATE OR REPLACE VIEW public.logactivitypenatajasa_v
        AS SELECT pendaftaranpenjamin_t.pendaftaran_id,
            pendaftaranpenjamin_t.pendaftaranpenjamin_id AS transaksi_id,
            pendaftaranpenjamin_t.created_date,
            pendaftaranpenjamin_t.penjamin_nama AS keterangan,
            'Tambah Penjamin'::character varying AS tipe,
            pendaftaranpenjamin_t.alasan_batal AS alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            3 AS track,
            pendaftaranpenjamin_t.created_date AS waktu
           FROM pendaftaranpenjamin_t
             JOIN loginpemakai_k ON pendaftaranpenjamin_t.created_by = loginpemakai_k.loginpemakai_id
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
        UNION ALL
         SELECT pendaftaranpenjamin_t.pendaftaran_id,
            pendaftaranpenjamin_t.pendaftaranpenjamin_id AS transaksi_id,
            pendaftaranpenjamin_t.deleted_date AS created_date,
            pendaftaranpenjamin_t.penjamin_nama AS keterangan,
            'Hapus Penjamin'::character varying AS tipe,
            pendaftaranpenjamin_t.alasan_batal AS alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            4 AS track,
            pendaftaranpenjamin_t.deleted_date AS waktu
           FROM pendaftaranpenjamin_t
             JOIN loginpemakai_k ON pendaftaranpenjamin_t.deleted_by = loginpemakai_k.loginpemakai_id
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
        UNION ALL
         SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
            historipenatajasa_r.tgl_perubahan AS created_date,
            daftartindakan_m.daftartindakan_nama AS keterangan,
            tipeperubahan_m.tipeperubahan_nama AS tipe,
            historipenatajasa_r.alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            5 AS track,
            historipenatajasa_r.tgl_perubahan AS waktu
           FROM historipenatajasa_r
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.daftartindakan_id,
                    a.pendaftaran_id
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON historipenatajasa_r.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             JOIN loginpemakai_k ON historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi IS TRUE
             LEFT JOIN tipeperubahan_m ON historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id
        UNION ALL
         SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
            historipenatajasa_r.tgl_perubahan AS created_date,
            daftartindakan_m.daftartindakan_nama AS keterangan,
            tipeperubahan_m.tipeperubahan_nama AS tipe,
            historipenatajasa_r.alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            6 AS track,
            historipenatajasa_r.tgl_perubahan AS waktu
           FROM historipenatajasa_r
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.daftartindakan_id,
                    a.pendaftaran_id,
                    a.is_penatajasa
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON historipenatajasa_r.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             JOIN loginpemakai_k ON historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_akomodasi IS FALSE
             LEFT JOIN tipeperubahan_m ON historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id
        UNION ALL
         SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tindakanpelayanan_id AS transaksi_id,
            historipenatajasa_r.tgl_perubahan AS created_date,
            tipepaket_m.tipepaket_nama AS keterangan,
            tipeperubahan_m.tipeperubahan_nama AS tipe,
            historipenatajasa_r.alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            6 AS track,
            historipenatajasa_r.tgl_perubahan AS waktu
           FROM historipenatajasa_r
             JOIN ( SELECT a.tindakanpelayanan_id,
                    a.tipepaket_id,
                    a.pendaftaran_id,
                    a.is_penatajasa
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON historipenatajasa_r.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
             JOIN loginpemakai_k ON historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN tipeperubahan_m ON historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id
        UNION ALL
         SELECT obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.obatalkespasien_id AS transaksi_id,
            COALESCE(historipenatajasa_r.tgl_perubahan, historipenatajasa_r.created_date) AS created_date,
            obatalkes_m.obatalkes_nama AS keterangan,
            tipeperubahan_m.tipeperubahan_nama AS tipe,
            historipenatajasa_r.alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            7 AS track,
            COALESCE(historipenatajasa_r.tgl_perubahan, historipenatajasa_r.created_date) AS waktu
           FROM historipenatajasa_r
             JOIN ( SELECT a.obatalkespasien_id,
                    a.obatalkes_id,
                    a.pendaftaran_id
                   FROM obatalkespasien_t a) obatalkespasien_t ON historipenatajasa_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
             JOIN loginpemakai_k ON historipenatajasa_r.created_by = loginpemakai_k.loginpemakai_id
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN tipeperubahan_m ON historipenatajasa_r.tipeperubahan_id = tipeperubahan_m.tipeperubahan_id
        UNION ALL
         SELECT logclosebill_r.pendaftaran_id,
            logclosebill_r.logclosebill_id AS transaksi_id,
            COALESCE(logclosebill_r.tgl_close_bill, logclosebill_r.created_date) AS created_date,
            logclosebill_r.keterangan,
            logclosebill_r.tipe,
            logclosebill_r.alasan,
            loginpemakai_k.loginpemakai_id,
            loginpemakai_k.nama_pemakai,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            8 AS track,
            COALESCE(logclosebill_r.tgl_close_bill, logclosebill_r.created_date) AS waktu
           FROM logclosebill_r
             JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = logclosebill_r.created_by
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
          ORDER BY 3 DESC;