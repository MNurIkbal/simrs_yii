CREATE OR REPLACE FUNCTION "public"."get_restriction_obat_detail"("p_restriction_obat_id" int4)
  RETURNS TABLE("restriction_obat_id" int4, "restriction_obat_nama" varchar, "restriction_obat_namalainnya" varchar, "restriction_akses_obat_id" int4, "instalasi_id" int4, "instalasi_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "carabayar_id" int4, "carabayar_nama" varchar, "obatalkes_id" int4, "obatalkes_kode" varchar, "obatalkes_nama" varchar, "is_activeobat" bool) AS $BODY$
BEGIN
    RETURN QUERY
    SELECT 
        restriction_obat_m.restriction_obat_id,
        restriction_obat_m.restriction_obat_nama,
        restriction_obat_m.restriction_obat_namalainnya,
        restriction_akses_obat_mp.restriction_akses_obat_id,
        restriction_akses_obat_mp.instalasi_id,
        instalasi_m.instalasi_nama,
        restriction_akses_obat_mp.penjamin_id,
        penjamin_m.penjamin_nama,
        penjamin_m.carabayar_id,
        carabayar_m.carabayar_nama,
        restriction_list_obat_mp.obatalkes_id,
        obatalkes_m.obatalkes_kode,
        obatalkes_m.obatalkes_nama,
        obatalkes_m.is_active as is_activeobat
    FROM restriction_obat_m
    LEFT JOIN restriction_akses_obat_mp ON restriction_akses_obat_mp.restriction_obat_id = restriction_obat_m.restriction_obat_id
    LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = restriction_akses_obat_mp.penjamin_id
    LEFT JOIN carabayar_m ON carabayar_m.carabayar_id = penjamin_m.carabayar_id
    LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = restriction_akses_obat_mp.instalasi_id
    LEFT JOIN restriction_list_obat_mp ON restriction_list_obat_mp.restriction_akses_obat_id = restriction_akses_obat_mp.restriction_akses_obat_id
    LEFT JOIN obatalkes_m ON obatalkes_m.obatalkes_id = restriction_list_obat_mp.obatalkes_id
    WHERE restriction_obat_m.is_deleted = false 
      AND restriction_akses_obat_mp.is_deleted = false
			and restriction_list_obat_mp.is_deleted = false
      AND restriction_obat_m.restriction_obat_id = p_restriction_obat_id;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000