CREATE OR REPLACE FUNCTION public.tariftotalkamarrs_fn(xruangan_id integer, xpenjamin_id integer, xkelaspelayanan_id integer, xtipe character varying DEFAULT 'kamar'::character varying)
 RETURNS TABLE(jenis text, tariftindakan_id integer, ruangan_id integer, ruangan_nama character varying, instalasi_id integer, instalasi_nama character varying, ruanganpaket_id integer, ruanganpaket_nama character varying, perdatarif_id integer, perdanama_sk character varying, kelaspelayanan_id integer, kelaspelayanan_nama character varying, penjamin_id integer, penjamin_nama character varying, kelompoktindakan_id integer, kelompoktindakan_nama character varying, kategoritindakan_id integer, kategoritindakan_nama character varying, daftartindakan_id integer, daftartindakan_nama character varying, tipepaket_id integer, tipepaket_nama character varying, komponentarif_id integer, komponentarif_nama character varying, harga_tariftindakan numeric, persencyto_tindakan numeric, persendiskon_tindakan numeric, is_default boolean, is_akomodasi boolean, carabayar_id integer, is_konsultasi boolean, kamarruangan_nokamar character varying, kamarruangan_id integer, ambulan_id integer, no_polisi character varying, kelompokpemeriksaanlab_id integer, nama_kelompok character varying, jenispemeriksaanlab_id integer, jenispemeriksaanlab_nama character varying, pemeriksaanlab_id integer, pemeriksaanlab_nama character varying, persen_penyulit numeric, kamarruangan_jenis integer, kamarruangan_jenis_nama character varying, status_isi boolean, jeniskasuspenyakit_id integer, jeniskasuspenyakit_nama character varying, kettempattidur_id integer, kettempattidur_nama character varying, kode_warna character varying, kettempattidur_warna character varying, kamartempattidur_id integer, no_tempattidur character varying, isi_jk integer, klasifikasikamar_id integer, klasifikasikamar_nama character varying)
 LANGUAGE plpgsql
AS $function$ 
DECLARE vpenjamin_id int4;
BEGIN
    IF(xruangan_id = 0)
    THEN
        xruangan_id := NULL;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        xkelaspelayanan_id := NULL;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;
    
    RETURN QUERY
	WITH jeniskelaminpasien as (
		SELECT 
            pasien_m.jeniskelamin,
            pasienadmisi_t.kamarruangan_id
        FROM pendaftaran_t
		JOIN (SELECT
                pasienadmisi_t.pasienadmisi_id,
                pasienadmisi_t.kamarruangan_id,
                pasienadmisi_t.pasienpulang_id,
                pasienadmisi_t.status_ranap
            FROM pasienadmisi_t
            WHERE pasienadmisi_t.pasienpulang_id IS NULL 
            AND pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441])
		)pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
		JOIN (SELECT
                pasien_m.pasien_id,
                pasien_m.jeniskelamin
            FROM pasien_m)pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
	),
	queryutama as (
		SELECT *FROM (
	       SELECT
	                    'kamar'::text AS jenis,
	                    COALESCE( tarif_normal.tariftindakan_id, 
	                                        tarif_kelas.tariftindakan_id) AS tariftindakan_id ,
	                    COALESCE( tarif_normal.ruangan_id, 
	                                        tarif_kelas.ruangan_id) as ruangan_id,                 
	                    COALESCE( tarif_normal.ruangan_nama, 
	                                        tarif_kelas.ruangan_nama) as ruangan_nama,
	                    COALESCE( tarif_normal.instalasi_id, 
	                                        tarif_kelas.instalasi_id) as instalasi_id,
	                    NULL::VARCHAR as instalasi_nama,
	                    NULL::integer AS ruanganpaket_id,
	                    NULL::character varying AS ruanganpaket_nama,
	                    COALESCE( tarif_normal.perdatarif_id, 
	                                        tarif_kelas.perdatarif_id) AS perdatarif_id,
	                    COALESCE( tarif_normal.perdanama_sk, 
	                                        tarif_kelas.perdanama_sk) as perdanama_sk,
	                    COALESCE( tarif_normal.kelaspelayanan_id, 
	                                        tarif_kelas.kelaspelayanan_id) as kelaspelayanan_id,
	                    COALESCE( tarif_normal.kelaspelayanan_nama,  
	                                        tarif_kelas.kelaspelayanan_nama) as kelaspelayanan_nama,
	                    COALESCE( tarif_normal.penjamin_id, 
	                                        tarif_kelas.penjamin_id) AS penjamin_id,
	                    COALESCE( tarif_normal.penjamin_nama, 
	                                        tarif_kelas.penjamin_nama) AS penjamin_nama,
	                    COALESCE( tarif_normal.kelompoktindakan_id, 
	                                        tarif_kelas.kelompoktindakan_id) as kelompoktindakan_id,
	                    COALESCE( tarif_normal.kelompoktindakan_nama, 
	                                        tarif_kelas.kelompoktindakan_nama) as kelompoktindakan_nama,
	                    COALESCE( tarif_normal.kategoritindakan_id, 
	                                        tarif_kelas.kategoritindakan_id) AS kategoritindakan_id,
	                    NULL::VARCHAR AS kategoritindakan_nama,
	                    COALESCE( tarif_normal.daftartindakan_id, 
	                                        tarif_kelas.daftartindakan_id) AS daftartindakan_id,
	                    COALESCE(tarif_normal.daftartindakan_nama, 
	                                        tarif_kelas.daftartindakan_nama) as daftartindakan_nama,
	                    NULL::integer AS tipepaket_id,
	                    NULL::character varying AS tipepaket_nama,
	                    COALESCE(tarif_normal.komponentarif_id, 
	                                        tarif_kelas.komponentarif_id) as komponentarif_id,
	                    COALESCE(tarif_normal.komponentarif_nama, 
	                                        tarif_kelas.komponentarif_nama) as komponentarif_nama,
	                    COALESCE(tarif_normal.harga_tariftindakan, 
	                                        tarif_kelas.harga_tariftindakan) AS harga_tariftindakan,
	                    COALESCE(tarif_normal.persencyto_tindakan, 
	                                        tarif_kelas.persencyto_tindakan) AS persencyto_tindakan,
	                    COALESCE(tarif_normal.persendiskon_tindakan, 
	                                        tarif_kelas.persendiskon_tindakan) AS persendiskon_tindakan,
	                    NULL::BOOLEAN AS is_default,
	                    NULL::BOOLEAN AS is_akomodasi,
	                    COALESCE( tarif_normal.carabayar_id, 
	                                        tarif_kelas.carabayar_id) as carabayar_id,
	                    NULL::BOOLEAN AS is_konsultasi,
	                    COALESCE( tarif_normal.kamarruangan_nokamar, 
	                                        tarif_kelas.kamarruangan_nokamar) as  kamarruangan_nokamar,
	                    COALESCE( tarif_normal.kamarruangan_id, 
	                                        tarif_kelas.kamarruangan_id) as kamarruangan_id,
	                    NULL::int4 as ambulan_id,
	                    NULL::VARCHAR as no_polisi,
	                    NULL::integer as kelompokpemeriksaanlab_id,
	                    NULL::character varying AS nama_kelompok,
	                    NULL::integer AS jenispemeriksaanlab_id,
	                    NULL::character varying AS jenispemeriksaanlab_nama,
	                    NULL::integer AS pemeriksaanlab_id,
	                    NULL::character varying AS pemeriksaanlab_nama,
	                    COALESCE(tarif_normal.persen_penyulit, 
	                                        tarif_kelas.persen_penyulit) AS persen_penyulit ,
	                    COALESCE(tarif_normal.kamarruangan_jenis, 
	                                        tarif_kelas.kamarruangan_jenis) as kamarruangan_jenis,
	                    COALESCE(tarif_normal.kamarruangan_jenis_nama, 
	                                        tarif_kelas.kamarruangan_jenis_nama)  AS kamarruangan_jenis_nama,             
	                    COALESCE(tarif_normal.status_isi, 
	                                        tarif_kelas.status_isi) as status_isi,
	                    COALESCE(tarif_normal.jeniskasuspenyakit_id, 
	                                        tarif_kelas.jeniskasuspenyakit_id) as jeniskasuspenyakit_id,
	                    COALESCE(tarif_normal.jeniskasuspenyakit_nama, 
	                                        tarif_kelas.jeniskasuspenyakit_nama) as jeniskasuspenyakit_nama,
	                    COALESCE(tarif_normal.kettempattidur_id, 
	                                        tarif_kelas.kettempattidur_id) as kettempattidur_id,
	                    COALESCE(tarif_normal.kettempattidur_nama, 
	                                        tarif_kelas.kettempattidur_nama) as kettempattidur_nama,
	                    COALESCE(tarif_normal.kode_warna, 
	                                        tarif_kelas.kode_warna) as kode_warna,
	                    COALESCE(tarif_normal.kettempattidur_warna, 
	                                        tarif_kelas.kettempattidur_warna) as kettempattidur_warna,
	                    COALESCE(tarif_normal.kamartempattidur_id, 
	                                        tarif_kelas.kamartempattidur_id) as kamartempattidur_id,
	                    COALESCE(tarif_normal.no_tempattidur, 
	                                        tarif_kelas.no_tempattidur) as no_tempattidur,
	                    COALESCE(tarif_normal.isi_jk, 
	                                        tarif_kelas.isi_jk) AS isi_jk,
	                    COALESCE(tarif_normal.klasifikasikamar_id, 
	                                        tarif_kelas.klasifikasikamar_id) AS klasifikasikamar_id,
	                    COALESCE(tarif_normal.klasifikasikamar_nama, 
	                                        tarif_kelas.klasifikasikamar_nama) AS klasifikasikamar_nama
	    FROM kamarruangan_m
	------------------------------tarif normal------------------------------         
	   LEFT JOIN (
	        SELECT 
	                        'normal' AS tipe,   
	                        tariftindakan_m.daftartindakan_id,
	                        tariftindakan_m.tariftindakan_id,
	                        tariftindakan_m.kelaspelayanan_id,
	                        kelaspelayanan_m.kelaspelayanan_nama,
	                        tariftindakan_m.penjamin_id,
	                        penjamin_m.carabayar_id,
	                        tariftindakan_m.harga_tariftindakan,
	                        tariftindakan_m.persencyto_tindakan,
	                        tariftindakan_m.persendiskon_tindakan,
	                        tariftindakan_m.persen_penyulit,
	                        tariftindakan_m.perdatarif_id,
	                        perdatarif_m.perdanama_sk,
	                        penjamin_m.penjamin_nama,
	                        tariftindakan_m.komponentarif_id,
	                        komponentarif_m.komponentarif_nama,
	                        tariftindakan_m.dokter_id,
	                        tariftindakan_m.kamarruangan_id,
	                        kamarruangan_m.ruangan_id as ruangan_id,
	                        kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
	                        kamarruangan_m.kamarruangan_jenis,
	                        kamar_jenis.lookup_name AS kamarruangan_jenis_nama,
	                        kamarruangan_m.jeniskasuspenyakit_id,
	                        kamartempattidur_m.status_isi,
	                        kamartempattidur_m.kamartempattidur_id,
	                        kamartempattidur_m.no_tempattidur,
	                        kettempattidur_m.kettempattidur_id,
	                        kettempattidur_m.kettempattidur_nama,
	                        kettempattidur_m.kode_warna,
	                        kettempattidur_m.kettempattidur_warna,
	                        ruangan_m.ruangan_nama,
	                        ruangan_m.instalasi_id,
	                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
	                        daftartindakan_m.daftartindakan_nama,
	                        daftartindakan_m.kelompoktindakan_id,
	                        kelompoktindakan_m.kelompoktindakan_nama,
	                        daftartindakan_m.kategoritindakan_id,                                                    
	                        (
								SELECT 
		                            jeniskelaminpasien.jeniskelamin
		                        FROM jeniskelaminpasien
								WHERE jeniskelaminpasien.kamarruangan_id = kamarruangan_m.kamarruangan_id
								LIMIT 1
	                        )::int4 AS isi_jk,
	                kamarruangan_m.klasifikasikamar_id,
	                klasifikasikamar_m.klasifikasikamar_nama
	        FROM tariftindakan_m
	                JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
	                                                            AND daftartindakan_m.is_akomodasi=TRUE
	                JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
	        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
	        JOIN (SELECT * FROM perdatarif_m
	                    WHERE is_deleted=FALSE AND is_active=TRUE   
	                        AND perda_tgl <= now() 
	                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
	        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
	        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
	        JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
	        JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
	                                                                AND kamartempattidur_m.is_deleted = FALSE AND kamartempattidur_m.is_active=TRUE
	        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
	                                             AND ruangan_m.is_deleted = FALSE AND ruangan_m.is_active=TRUE
	        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
	        LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
	        LEFT JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
	                LEFT JOIN lookup_m kamar_jenis ON kamarruangan_m.kamarruangan_jenis = kamar_jenis.lookup_id 
	                -- klasifikasikamar_m --
	                LEFT JOIN klasifikasikamar_m ON kamarruangan_m.klasifikasikamar_id = klasifikasikamar_m.klasifikasikamar_id
	        WHERE komponentarif_m.is_deleted IS FALSE
	                AND tariftindakan_m.is_deleted = FALSE 
	                AND tariftindakan_m.is_active = TRUE 
	                AND tariftindakan_m.tarifparent_id IS NULL
	                AND tariftindakan_m.komponentarif_id = 6
	                AND tariftindakan_m.penjamin_id = xpenjamin_id
	--                              AND tariftindakan_m.kelaspelayanan_id = COALESCE(xkelaspelayanan_id,tariftindakan_m.kelaspelayanan_id)
	AND CASE WHEN xkelaspelayanan_id IS NULL THEN TRUE ELSE tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id END
	    ) tarif_normal ON kamarruangan_m.kamarruangan_id = tarif_normal.kamarruangan_id
	
	------------------------------tarif kelas------------------------------         
	        LEFT JOIN (
	            SELECT 
	                    'kelas' AS tipe,
	                    tariftindakan_m.daftartindakan_id,
	                    tariftindakan_m.tariftindakan_id,
	                    tariftindakan_m.kelaspelayanan_id,
	                    kelaspelayanan_m.kelaspelayanan_nama,
	                    tariftindakan_m.penjamin_id,
	                    penjamin_m.carabayar_id,
	                    tariftindakan_m.harga_tariftindakan,
	                    tariftindakan_m.persencyto_tindakan,
	                    tariftindakan_m.persendiskon_tindakan,
	                    tariftindakan_m.persen_penyulit,
	                    tariftindakan_m.perdatarif_id,
	                    perdatarif_m.perdanama_sk,
	                    penjamin_m.penjamin_nama,
	                    tariftindakan_m.komponentarif_id,
	                    komponentarif_m.komponentarif_nama,
	                    tariftindakan_m.dokter_id,
	                    tariftindakan_m.kamarruangan_id,
	                    kamarruangan_m.ruangan_id as ruangan_id,
	                    kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
	                    kamarruangan_m.kamarruangan_jenis,
	                                        kamar_jenis.lookup_name AS kamarruangan_jenis_nama,
	                    kamarruangan_m.jeniskasuspenyakit_id,
	                    kamartempattidur_m.status_isi,
	                    kamartempattidur_m.kamartempattidur_id,
	                    kamartempattidur_m.no_tempattidur,
	                    kettempattidur_m.kettempattidur_id,
	                    kettempattidur_m.kettempattidur_nama,
	                    kettempattidur_m.kode_warna,
	                    kettempattidur_m.kettempattidur_warna,
	                    ruangan_m.ruangan_nama,
	                    ruangan_m.instalasi_id,
	                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
	                                        daftartindakan_m.daftartindakan_nama,
	                                        daftartindakan_m.kelompoktindakan_id,
	                                        kelompoktindakan_m.kelompoktindakan_nama,
	                                        daftartindakan_m.kategoritindakan_id,   
	                    	(
								SELECT 
		                            jeniskelaminpasien.jeniskelamin
		                        FROM jeniskelaminpasien
								WHERE jeniskelaminpasien.kamarruangan_id = kamarruangan_m.kamarruangan_id
								LIMIT 1
	                        )::int4 AS isi_jk,
	                        kamarruangan_m.klasifikasikamar_id,
	                        klasifikasikamar_m.klasifikasikamar_nama
	            FROM tariftindakan_m
	                        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id 
	                                                                    AND daftartindakan_m.is_akomodasi=TRUE
	                        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
	            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
	            JOIN (SELECT * FROM perdatarif_m
	                        WHERE is_deleted=FALSE AND is_active=TRUE   
	                            AND perda_tgl <= now() 
	                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
	            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
	            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
	            JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
	            JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
	                                                                        AND kamartempattidur_m.is_deleted = FALSE AND kamartempattidur_m.is_active=TRUE
	            JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
	                                                     AND ruangan_m.is_deleted = FALSE AND ruangan_m.is_active=TRUE
	            JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
	            LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
	            LEFT JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
	                        LEFT JOIN lookup_m kamar_jenis ON kamarruangan_m.kamarruangan_jenis = kamar_jenis.lookup_id 
	                        --      klasifikasikamar_m --
	                        LEFT JOIN klasifikasikamar_m ON kamarruangan_m.klasifikasikamar_id = klasifikasikamar_m.klasifikasikamar_id
	                        WHERE komponentarif_m.is_deleted IS FALSE
	                    AND tariftindakan_m.is_deleted = FALSE 
	                    AND tariftindakan_m.is_active = TRUE 
	                    AND tariftindakan_m.tarifparent_id IS NULL
	                    AND tariftindakan_m.komponentarif_id = 6
	                    AND tariftindakan_m.penjamin_id = vpenjamin_id
	--                     AND  tariftindakan_m.kelaspelayanan_id = COALESCE(xkelaspelayanan_id,tariftindakan_m.kelaspelayanan_id)
	AND CASE WHEN xkelaspelayanan_id IS NULL THEN TRUE ELSE tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id END
	    ) tarif_kelas ON kamarruangan_m.kamarruangan_id = tarif_kelas.kamarruangan_id
	                                    AND tarif_normal.tariftindakan_id IS NULL 
	---------------------------------------------------------------------------------------------------------------------       
	           WHERE kamarruangan_m.is_deleted=FALSE AND kamarruangan_m.is_active=TRUE AND kamarruangan_m.is_kamarthruput=TRUE
	                        AND (tarif_normal.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL)
	                                                                                                            
	    ) AS x
	        WHERE 
	            CASE WHEN xruangan_id IS NULL THEN true 
	                ELSE x.ruangan_id = xruangan_id 
	            END
	    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kamarruangan_jenis, x.kamarruangan_jenis_nama , x.status_isi, x.jeniskasuspenyakit_id, x.jeniskasuspenyakit_nama, x.kettempattidur_id, x.kettempattidur_nama, x.kode_warna, x.kettempattidur_warna, x.kamartempattidur_id  , x.no_tempattidur , x.isi_jk , x.klasifikasikamar_id , x.klasifikasikamar_nama
	)
	select * from queryutama;
    
END; 
$function$
;