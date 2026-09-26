-- public.infohistoritarif_v source

CREATE OR REPLACE VIEW public.infohistoritarif_v
AS SELECT tariftindakan_r.id,
    tariftindakan_r.daftartindakan_id,
    tariftindakan_r.penjamin_id,
    tariftindakan_r.kelaspelayanan_id,
    tariftindakan_r.tipepaket_id,
    tariftindakan_r.dokter_id,
    tariftindakan_r.perdatarif_id,
        CASE
            WHEN tariftindakan_r.tipepaket_id IS NULL THEN daftartindakan_m.daftartindakan_nama
            ELSE tipepaket_m.tipepaket_nama
        END AS tindakan_paket,
    concat(kelaspelayanan_m.kelaspelayanan_nama, ' / ', carabayar_m.carabayar_nama, ' / ', penjamin_m.penjamin_nama, ' / ', perdatarif_m.perdanama_sk) AS keterangan,
    tariftindakan_r.harga_tariftindakan,
    to_char(tariftindakan_r.tgl_proses, 'YYYY-MM-DD HH24:MI:SS'::text) AS tgl_proses,
    tariftindakan_r.keterangan_rekap AS aksi,
        CASE
            WHEN tariftindakan_r.last_modified_by IS NULL THEN peg_create.nama_pegawai
            ELSE peg_edit.nama_pegawai
        END AS nama_user,
    tariftindakan_r.ruangan_id
   FROM tariftindakan_r
     JOIN ( SELECT min(a.tariftindakan_id) AS tariftindakan_id,
            a.daftartindakan_id,
            a.kelaspelayanan_id,
            a.penjamin_id,
            a.dokter_id,
            a.perdatarif_id
           FROM tariftindakan_r a
          WHERE a.keterangan_rekap::text = 'CREATE'::text AND a.komponentarif_id = 6
          GROUP BY a.daftartindakan_id, a.kelaspelayanan_id, a.penjamin_id, a.dokter_id, a.perdatarif_id) min_tarif ON tariftindakan_r.tariftindakan_id = min_tarif.tariftindakan_id
     LEFT JOIN daftartindakan_m ON tariftindakan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tariftindakan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN kelaspelayanan_m ON tariftindakan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN perdatarif_m ON tariftindakan_r.perdatarif_id = perdatarif_m.perdatarif_id
     LEFT JOIN loginpemakai_k log_create ON tariftindakan_r.created_by = log_create.loginpemakai_id
     LEFT JOIN loginpemakai_k log_edit ON tariftindakan_r.last_modified_by = log_edit.loginpemakai_id
     LEFT JOIN pegawai_m peg_create ON log_create.pegawai_id = peg_create.pegawai_id
     LEFT JOIN pegawai_m peg_edit ON log_edit.pegawai_id = peg_edit.pegawai_id
  WHERE tariftindakan_r.komponentarif_id = 6 AND (tariftindakan_r.keterangan_rekap::text <> ALL (ARRAY['DELETE'::character varying::text, 'EDIT'::character varying::text]))
UNION ALL
 SELECT tariftindakan_r.id,
    tariftindakan_r.daftartindakan_id,
    tariftindakan_r.penjamin_id,
    tariftindakan_r.kelaspelayanan_id,
    tariftindakan_r.tipepaket_id,
    tariftindakan_r.dokter_id,
    tariftindakan_r.perdatarif_id,
        CASE
            WHEN tariftindakan_r.tipepaket_id IS NULL THEN daftartindakan_m.daftartindakan_nama
            ELSE tipepaket_m.tipepaket_nama
        END AS tindakan_paket,
    concat(kelaspelayanan_m.kelaspelayanan_nama, ' / ', carabayar_m.carabayar_nama, ' / ', penjamin_m.penjamin_nama, ' / ', perdatarif_m.perdanama_sk) AS keterangan,
    tariftindakan_r.harga_tariftindakan,
    to_char(tariftindakan_r.tgl_proses, 'YYYY-MM-DD HH24:MI:SS'::text) AS tgl_proses,
    tariftindakan_r.keterangan_rekap AS aksi,
        CASE
            WHEN tariftindakan_r.last_modified_by IS NULL THEN peg_create.nama_pegawai
            ELSE peg_edit.nama_pegawai
        END AS nama_user,
  tariftindakan_r.ruangan_id
   FROM tariftindakan_r
     LEFT JOIN daftartindakan_m ON tariftindakan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tariftindakan_r.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN kelaspelayanan_m ON tariftindakan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_r.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN perdatarif_m ON tariftindakan_r.perdatarif_id = perdatarif_m.perdatarif_id
     LEFT JOIN loginpemakai_k log_create ON tariftindakan_r.created_by = log_create.loginpemakai_id
     LEFT JOIN loginpemakai_k log_edit ON tariftindakan_r.last_modified_by = log_edit.loginpemakai_id
     LEFT JOIN pegawai_m peg_create ON log_create.pegawai_id = peg_create.pegawai_id
     LEFT JOIN pegawai_m peg_edit ON log_edit.pegawai_id = peg_edit.pegawai_id
  WHERE tariftindakan_r.komponentarif_id = 6 AND (tariftindakan_r.keterangan_rekap::text <> ALL (ARRAY['CREATE'::character varying::text, 'DELETE'::character varying::text]));