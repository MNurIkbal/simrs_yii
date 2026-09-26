-- public.hakakseslaporan_v source

CREATE OR REPLACE VIEW public.hakakseslaporan_v
AS SELECT x.mappingreportperanpengguna_id,
    x.peranpengguna_id,
    x.module_id,
    x.reports_id,
    x.peranpenggunanama,
    report_t.docmapping_id,
    report_t.code,
    report_t.content,
    report_t.filepath,
    report_t.config,
    report_t.is_file,
    report_t.title
   FROM ( SELECT a.mappingreportperanpengguna_id,
            a.peranpengguna_id,
            a.module_id,
            unnest(regexp_split_to_array(replace(replace(a.reports_id::text, '['::text, ''::text), ']'::text, ''::text), ','::text)::integer[]) AS reports_id,
            b.peranpenggunanamalain as peranpenggunanama
           FROM mappingreportperanpengguna_k a
             JOIN peranpengguna_k b ON a.peranpengguna_id = b.peranpengguna_id) x
     JOIN ( SELECT a.id,
            a.docmapping_id,
            a.code,
            a.content,
            a.filepath,
            a.config,
            a.is_file,
            a.title
           FROM report_t a) report_t ON x.reports_id = report_t.id;