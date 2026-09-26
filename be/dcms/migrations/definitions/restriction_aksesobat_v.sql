CREATE VIEW "public"."restriction_aksesobat_v" AS  SELECT a.restriction_obat_id,
    a.restriction_obat_nama,
    a.restriction_obat_namalainnya,
    string_agg(a.instalasi_id::text, ', '::text) AS instalasi_id,
    string_agg(a.instalasi_nama::text, ', '::text) AS instalasi_nama,
    a.penjamin_nama,
    a.carabayar_id,
    a.carabayar_nama,
    a.jml,
    a.penjamin_id,
    a.is_active
   FROM ( SELECT restriction_obat_m.restriction_obat_id,
            restriction_obat_m.restriction_obat_nama,
            restriction_obat_m.restriction_obat_namalainnya,
            restriction_akses_obat_mp.instalasi_id,
            instalasi_m.instalasi_nama,
            string_agg(restriction_akses_obat_mp.penjamin_id::text, ', '::text) AS penjamin_id,
            string_agg(penjamin_m.penjamin_nama::text, ', '::text) AS penjamin_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            COALESCE(restriction_list_obat_mp.jml, 0::bigint) AS jml,
            restriction_obat_m.is_active
           FROM restriction_obat_m
             LEFT JOIN restriction_akses_obat_mp ON restriction_akses_obat_mp.restriction_obat_id = restriction_obat_m.restriction_obat_id
             JOIN penjamin_m ON penjamin_m.penjamin_id = restriction_akses_obat_mp.penjamin_id
             JOIN carabayar_m ON carabayar_m.carabayar_id = penjamin_m.carabayar_id
             JOIN instalasi_m ON instalasi_m.instalasi_id = restriction_akses_obat_mp.instalasi_id
             LEFT JOIN ( SELECT restriction_list_obat_mp_1.restriction_obat_id,
                    count(*) AS jml
                   FROM restriction_list_obat_mp restriction_list_obat_mp_1
                  WHERE restriction_list_obat_mp_1.is_deleted = false
                  GROUP BY restriction_list_obat_mp_1.restriction_obat_id) restriction_list_obat_mp ON restriction_list_obat_mp.restriction_obat_id = restriction_obat_m.restriction_obat_id
          WHERE restriction_obat_m.is_deleted = false AND restriction_akses_obat_mp.is_deleted = false
          GROUP BY restriction_obat_m.restriction_obat_id, restriction_obat_m.restriction_obat_nama, restriction_obat_m.restriction_obat_namalainnya, restriction_akses_obat_mp.instalasi_id, instalasi_m.instalasi_nama, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, restriction_list_obat_mp.jml, restriction_obat_m.is_active) a
  GROUP BY a.restriction_obat_id, a.restriction_obat_nama, a.restriction_obat_namalainnya, a.penjamin_nama, a.carabayar_id, a.carabayar_nama, a.penjamin_id, a.jml, a.is_active;