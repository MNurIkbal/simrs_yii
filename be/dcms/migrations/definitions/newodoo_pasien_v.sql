-- public.newodoo_pasien_v source

CREATE OR REPLACE VIEW public.newodoo_pasien_v
AS SELECT t.id,
    t.sync_id_api,
    t.registration_code,
    t.name,
    t.display_name,
    t.title_name,
    t.date_of_birth,
    t.gender,
    t.phone,
    t.mobile,
    t.contact_person,
    t.fax,
    t.email,
    t.street,
    t.street2,
    t.street3,
    t.city,
    t.zip,
    t.passport,
    t.ktp,
    t.wipro_block,
    t.patient,
    t.active,
    t.keterangan,
    t.tgl_proses,
    t.pasien_id,
        CASE t.jeniskelamin
            WHEN '15'::text THEN 'L'::text
            WHEN '16'::text THEN 'P'::text
            ELSE '-'::text
        END::character varying(20) AS gender_id
   FROM ( SELECT DISTINCT ON (pasien_r.pasien_id) pasien_r.id,
            pasien_r.pasien_id AS sync_id_api,
            pasien_r.no_rekam_medik AS registration_code,
            pasien_r.nama_pasien AS name,
            pasien_r.nama_pasien AS display_name,
            lower(fgetnamalookup(pasien_r.namadepan::integer)::text) AS title_name,
            COALESCE(pasien_r.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
            lower(fgetvaluelookup(pasien_r.jeniskelamin::integer)::text) AS gender,
            COALESCE(pasien_r.no_telepon_pasien, '-'::character varying) AS phone,
            COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS mobile,
            COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS contact_person,
            '-'::text AS fax,
            COALESCE(pasien_r.alamatemail, '-'::character varying) AS email,
            COALESCE(pasien_r.alamat_sekarang, '-'::text) AS street,
            '-'::text AS street2,
            '-'::text AS street3,
            COALESCE(pasien_r.alamat_pasien, '-'::text) AS city,
            '-'::text AS zip,
                CASE
                    WHEN pasien_r.jenisidentitas::text = '99'::text THEN pasien_r.no_identitas_pasien
                    ELSE '-'::character varying
                END AS passport,
                CASE
                    WHEN pasien_r.jenisidentitas::text = '94'::text THEN pasien_r.no_identitas_pasien
                    ELSE '-'::character varying
                END AS ktp,
                CASE
                    WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS TRUE THEN true
                    WHEN pasien_r.is_active IS TRUE AND pasien_r.is_deleted IS FALSE THEN false
                    WHEN pasien_r.is_active IS FALSE AND pasien_r.is_deleted IS FALSE THEN true
                    ELSE true
                END AS wipro_block,
            true AS patient,
            true AS active,
            pasien_r.keterangan,
            pasien_r.tgl_proses,
            pasien_r.pasien_id,
            pasien_r.jeniskelamin
           FROM pasien_r
          WHERE pasien_r.keterangan::text = 'UPDATE'::text AND date(pasien_r.tgl_proses) <> date(pasien_r.created_date)
          ORDER BY pasien_r.pasien_id, pasien_r.tgl_proses DESC) t
UNION ALL
 SELECT NULL::integer AS id,
    pasien_m.pasien_id AS sync_id_api,
    pasien_m.no_rekam_medik AS registration_code,
    pasien_m.nama_pasien AS name,
    pasien_m.nama_pasien AS display_name,
    lower(fgetnamalookup(pasien_m.namadepan::integer)::text) AS title_name,
    COALESCE(pasien_m.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
    lower(fgetvaluelookup(pasien_m.jeniskelamin::integer)::text) AS gender,
    COALESCE(pasien_m.no_telepon_pasien, '-'::character varying) AS phone,
    COALESCE(pasien_m.no_mobile_pasien, '-'::character varying) AS mobile,
    COALESCE(pasien_m.no_mobile_pasien, '-'::character varying) AS contact_person,
    '-'::text AS fax,
    COALESCE(pasien_m.alamatemail, '-'::character varying) AS email,
    COALESCE(pasien_m.alamat_sekarang, '-'::text) AS street,
    '-'::text AS street2,
    '-'::text AS street3,
    COALESCE(pasien_m.alamat_pasien, '-'::text) AS city,
    '-'::text AS zip,
        CASE
            WHEN pasien_m.jenisidentitas::text = '99'::text THEN pasien_m.no_identitas_pasien
            ELSE '-'::character varying
        END AS passport,
        CASE
            WHEN pasien_m.jenisidentitas::text = '94'::text THEN pasien_m.no_identitas_pasien
            ELSE '-'::character varying
        END AS ktp,
        CASE
            WHEN pasien_m.is_active IS TRUE AND pasien_m.is_deleted IS TRUE THEN true
            WHEN pasien_m.is_active IS TRUE AND pasien_m.is_deleted IS FALSE THEN false
            WHEN pasien_m.is_active IS FALSE AND pasien_m.is_deleted IS FALSE THEN true
            ELSE true
        END AS wipro_block,
    true AS patient,
    true AS active,
    'INSERT'::character varying AS keterangan,
    pasien_m.created_date AS tgl_proses,
    pasien_m.pasien_id,
        CASE pasien_m.jeniskelamin
            WHEN '15'::text THEN 'L'::text
            WHEN '16'::text THEN 'P'::text
            ELSE '-'::text
        END::character varying(20) AS gender_id
   FROM pasien_m;