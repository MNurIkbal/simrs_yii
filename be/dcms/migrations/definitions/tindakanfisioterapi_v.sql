-- public.tindakanfisioterapi_v source

CREATE OR REPLACE VIEW public.tindakanfisioterapi_v
AS ( (
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 1 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 2 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 3 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 4 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 5 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 5 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 6 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 7 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 8 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 9 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 10 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 11 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 12 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 13 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 15 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 16 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 17 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
		SELECT
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
			pemeriksaanfisio_m.daftartindakan_id,
			jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
			pemeriksaanfisio_m.pemeriksaanfisio_kode,
			pemeriksaanfisio_m.pemeriksaanfisio_nama,
			pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
			pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
		FROM
			pemeriksaanfisio_m
			JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 18 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id 
			) UNION
	SELECT
		jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
		pemeriksaanfisio_m.daftartindakan_id,
		jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
		pemeriksaanfisio_m.pemeriksaanfisio_kode,
		pemeriksaanfisio_m.pemeriksaanfisio_nama,
		pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
		pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
	FROM
		pemeriksaanfisio_m
		JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 19 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id 
	) UNION ALL
SELECT
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
	pemeriksaanfisio_m.daftartindakan_id,
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
	pemeriksaanfisio_m.pemeriksaanfisio_kode,
	pemeriksaanfisio_m.pemeriksaanfisio_nama,
	pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
	pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
FROM
	pemeriksaanfisio_m
	JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 20 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
SELECT
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
	pemeriksaanfisio_m.daftartindakan_id,
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
	pemeriksaanfisio_m.pemeriksaanfisio_kode,
	pemeriksaanfisio_m.pemeriksaanfisio_nama,
	pemeriksaanfisio_m.daftartindakan_id AS daftartindakandet_id,
	pemeriksaanfisio_m.pemeriksaanfisio_nama AS daftartindakandet_nama 
FROM
	pemeriksaanfisio_m
	JOIN ( SELECT A.jenispemeriksaanfisio_id, A.jenispemeriksaanfisio_nama FROM jenispemeriksaanfisio_m A WHERE A.jenispemeriksaanfisio_id = 21 ) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id UNION ALL
SELECT
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama AS kategori,
	pemeriksaanfisio_m.daftartindakan_id,
	jenispemeriksaanfisio_m.jenispemeriksaanfisio_id,
	pemeriksaanfisio_m.pemeriksaanfisio_kode,
	pemeriksaanfisio_m.pemeriksaanfisio_nama,
	daftarpaketfisiodet_m.daftartindakandet_id,
	daftartindakandet_m.daftartindakandet_nama 
FROM
	pemeriksaanfisio_m
	JOIN (
	SELECT A
		.jenispemeriksaanfisio_id,
		A.jenispemeriksaanfisio_nama 
	FROM
		jenispemeriksaanfisio_m A 
	WHERE
		A.jenispemeriksaanfisio_id <> ALL (
			ARRAY [ 1,
			2,
			3,
			4,
			5,
			6,
			7,
			8,
			9,
			10,
			11,
			12,
			13,
			15,
			16,
			17,
			18,
			19,
			20,
			21 ] 
		) 
	) jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id
	JOIN ( SELECT A.parent_id, A.daftarpaketfisio_id FROM daftarpaketfisio_m A ) daftarpaketfisio_m ON pemeriksaanfisio_m.daftartindakan_id = daftarpaketfisio_m.parent_id
	JOIN ( SELECT A.daftarpaketfisio_id, A.daftartindakan_id AS daftartindakandet_id, A.is_deleted FROM daftarpaketfisiodet_m A WHERE A.is_deleted = FALSE ) daftarpaketfisiodet_m ON daftarpaketfisio_m.daftarpaketfisio_id = daftarpaketfisiodet_m.daftarpaketfisio_id
	JOIN ( SELECT A.daftartindakan_id AS daftartindakandet_id, A.daftartindakan_nama AS daftartindakandet_nama, A.is_deleted FROM daftartindakan_m A WHERE A.is_deleted = FALSE ) daftartindakandet_m ON daftarpaketfisiodet_m.daftartindakandet_id = daftartindakandet_m.daftartindakandet_id;