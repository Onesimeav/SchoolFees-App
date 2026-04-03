-- ============================================================
--  Portail Étudiant — School Fees App
--  Dump complet (schéma + données de démonstration)
--  PostgreSQL 15+
--  Re-exécutable : les tables existantes sont supprimées et recréées.
--
--  Usage :
--    psql -U postgres -f school_fees_app.sql
-- ============================================================

CREATE DATABASE school_fees_app
    WITH ENCODING 'UTF8'
         LC_COLLATE = 'en_US.UTF-8'
         LC_CTYPE   = 'en_US.UTF-8'
         TEMPLATE   = template0;

\c school_fees_app

--
-- PostgreSQL database dump
--

\restrict g3ULlbefq7rCRlIP4mvcdDxabgcaES1FkfbKtdX4h9D8h6HiDTq9A8WXzBKYYJP

-- Dumped from database version 16.13 (Ubuntu 16.13-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.13 (Ubuntu 16.13-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.transactions DROP CONSTRAINT IF EXISTS transactions_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.transactions DROP CONSTRAINT IF EXISTS transactions_installment_id_foreign;
ALTER TABLE IF EXISTS ONLY public.transactions DROP CONSTRAINT IF EXISTS transactions_fee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.role_has_permissions DROP CONSTRAINT IF EXISTS role_has_permissions_role_id_foreign;
ALTER TABLE IF EXISTS ONLY public.role_has_permissions DROP CONSTRAINT IF EXISTS role_has_permissions_permission_id_foreign;
ALTER TABLE IF EXISTS ONLY public.refund_requests DROP CONSTRAINT IF EXISTS refund_requests_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.refund_requests DROP CONSTRAINT IF EXISTS refund_requests_transaction_id_foreign;
ALTER TABLE IF EXISTS ONLY public.model_has_roles DROP CONSTRAINT IF EXISTS model_has_roles_role_id_foreign;
ALTER TABLE IF EXISTS ONLY public.model_has_permissions DROP CONSTRAINT IF EXISTS model_has_permissions_permission_id_foreign;
ALTER TABLE IF EXISTS ONLY public.installments DROP CONSTRAINT IF EXISTS installments_tuition_fee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.fees DROP CONSTRAINT IF EXISTS fees_grade_id_foreign;
ALTER TABLE IF EXISTS ONLY public.class_registrations DROP CONSTRAINT IF EXISTS class_registrations_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.class_registrations DROP CONSTRAINT IF EXISTS class_registrations_transaction_id_foreign;
ALTER TABLE IF EXISTS ONLY public.class_registrations DROP CONSTRAINT IF EXISTS class_registrations_grade_id_foreign;
DROP INDEX IF EXISTS public.verification_codes_email_index;
DROP INDEX IF EXISTS public.users_verified_index;
DROP INDEX IF EXISTS public.users_matricule_index;
DROP INDEX IF EXISTS public.users_classroom_index;
DROP INDEX IF EXISTS public.users_academic_year_index;
DROP INDEX IF EXISTS public.transactions_status_index;
DROP INDEX IF EXISTS public.transactions_kkiapay_reference_index;
DROP INDEX IF EXISTS public.transactions_installment_id_index;
DROP INDEX IF EXISTS public.transactions_date_index;
DROP INDEX IF EXISTS public.sessions_user_id_index;
DROP INDEX IF EXISTS public.sessions_last_activity_index;
DROP INDEX IF EXISTS public.refund_requests_user_id_index;
DROP INDEX IF EXISTS public.refund_requests_transaction_id_index;
DROP INDEX IF EXISTS public.refund_requests_status_index;
DROP INDEX IF EXISTS public.model_has_roles_model_id_model_type_index;
DROP INDEX IF EXISTS public.model_has_permissions_model_id_model_type_index;
DROP INDEX IF EXISTS public.jobs_queue_index;
DROP INDEX IF EXISTS public.installments_tuition_fee_id_index;
DROP INDEX IF EXISTS public.installments_due_date_index;
DROP INDEX IF EXISTS public.fees_type_index;
DROP INDEX IF EXISTS public.fees_classroom_index;
DROP INDEX IF EXISTS public.fees_academic_year_index;
DROP INDEX IF EXISTS public.class_registrations_user_id_status_index;
DROP INDEX IF EXISTS public.cache_locks_expiration_index;
DROP INDEX IF EXISTS public.cache_expiration_index;
ALTER TABLE IF EXISTS ONLY public.verification_codes DROP CONSTRAINT IF EXISTS verification_codes_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_matricule_unique;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_unique;
ALTER TABLE IF EXISTS ONLY public.transactions DROP CONSTRAINT IF EXISTS transactions_pkey;
ALTER TABLE IF EXISTS ONLY public.sessions DROP CONSTRAINT IF EXISTS sessions_pkey;
ALTER TABLE IF EXISTS ONLY public.roles DROP CONSTRAINT IF EXISTS roles_pkey;
ALTER TABLE IF EXISTS ONLY public.roles DROP CONSTRAINT IF EXISTS roles_name_guard_name_unique;
ALTER TABLE IF EXISTS ONLY public.role_has_permissions DROP CONSTRAINT IF EXISTS role_has_permissions_pkey;
ALTER TABLE IF EXISTS ONLY public.refund_requests DROP CONSTRAINT IF EXISTS refund_requests_pkey;
ALTER TABLE IF EXISTS ONLY public.permissions DROP CONSTRAINT IF EXISTS permissions_pkey;
ALTER TABLE IF EXISTS ONLY public.permissions DROP CONSTRAINT IF EXISTS permissions_name_guard_name_unique;
ALTER TABLE IF EXISTS ONLY public.password_reset_tokens DROP CONSTRAINT IF EXISTS password_reset_tokens_pkey;
ALTER TABLE IF EXISTS ONLY public.model_has_roles DROP CONSTRAINT IF EXISTS model_has_roles_pkey;
ALTER TABLE IF EXISTS ONLY public.model_has_permissions DROP CONSTRAINT IF EXISTS model_has_permissions_pkey;
ALTER TABLE IF EXISTS ONLY public.migrations DROP CONSTRAINT IF EXISTS migrations_pkey;
ALTER TABLE IF EXISTS ONLY public.jobs DROP CONSTRAINT IF EXISTS jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.job_batches DROP CONSTRAINT IF EXISTS job_batches_pkey;
ALTER TABLE IF EXISTS ONLY public.installments DROP CONSTRAINT IF EXISTS installments_pkey;
ALTER TABLE IF EXISTS ONLY public.grades DROP CONSTRAINT IF EXISTS grades_pkey;
ALTER TABLE IF EXISTS ONLY public.fees DROP CONSTRAINT IF EXISTS fees_pkey;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_uuid_unique;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.class_registrations DROP CONSTRAINT IF EXISTS class_registrations_pkey;
ALTER TABLE IF EXISTS ONLY public.cache DROP CONSTRAINT IF EXISTS cache_pkey;
ALTER TABLE IF EXISTS ONLY public.cache_locks DROP CONSTRAINT IF EXISTS cache_locks_pkey;
ALTER TABLE IF EXISTS public.verification_codes ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.roles ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.permissions ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.migrations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.failed_jobs ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.verification_codes_id_seq;
DROP TABLE IF EXISTS public.verification_codes;
DROP TABLE IF EXISTS public.users;
DROP TABLE IF EXISTS public.transactions;
DROP TABLE IF EXISTS public.sessions;
DROP SEQUENCE IF EXISTS public.roles_id_seq;
DROP TABLE IF EXISTS public.roles;
DROP TABLE IF EXISTS public.role_has_permissions;
DROP TABLE IF EXISTS public.refund_requests;
DROP SEQUENCE IF EXISTS public.permissions_id_seq;
DROP TABLE IF EXISTS public.permissions;
DROP TABLE IF EXISTS public.password_reset_tokens;
DROP TABLE IF EXISTS public.model_has_roles;
DROP TABLE IF EXISTS public.model_has_permissions;
DROP SEQUENCE IF EXISTS public.migrations_id_seq;
DROP TABLE IF EXISTS public.migrations;
DROP SEQUENCE IF EXISTS public.jobs_id_seq;
DROP TABLE IF EXISTS public.jobs;
DROP TABLE IF EXISTS public.job_batches;
DROP TABLE IF EXISTS public.installments;
DROP TABLE IF EXISTS public.grades;
DROP TABLE IF EXISTS public.fees;
DROP SEQUENCE IF EXISTS public.failed_jobs_id_seq;
DROP TABLE IF EXISTS public.failed_jobs;
DROP TABLE IF EXISTS public.class_registrations;
DROP TABLE IF EXISTS public.cache_locks;
DROP TABLE IF EXISTS public.cache;
DROP SCHEMA IF EXISTS public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: class_registrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.class_registrations (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    grade_id uuid NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    transaction_id uuid,
    CONSTRAINT class_registrations_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'accepted'::character varying, 'refused'::character varying])::text[])))
);


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fees; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.fees (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    total_amount numeric(10,2) NOT NULL,
    academic_year character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    classroom character varying(255),
    description text,
    number_of_installments integer,
    required boolean,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    grade_id uuid,
    due_before date,
    late_fine_per_week numeric(10,2)
);


--
-- Name: grades; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.grades (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: installments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.installments (
    id uuid NOT NULL,
    tuition_fee_id uuid NOT NULL,
    number integer NOT NULL,
    amount numeric(10,2) NOT NULL,
    due_date date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id uuid NOT NULL
);


--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id uuid NOT NULL
);


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: refund_requests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.refund_requests (
    id uuid NOT NULL,
    transaction_id uuid NOT NULL,
    user_id uuid NOT NULL,
    reason text NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id uuid,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: transactions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.transactions (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    fee_id uuid,
    amount numeric(10,2) NOT NULL,
    date date NOT NULL,
    status character varying(255) NOT NULL,
    kkiapay_reference character varying(255),
    phone_number character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    installment_id uuid
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    surname character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    phone_number character varying(20),
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    verified boolean DEFAULT false NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    matricule character varying(255),
    classroom character varying(255),
    academic_year character varying(255),
    parent1_name character varying(255),
    parent1_surname character varying(255),
    parent1_phone character varying(20),
    parent2_name character varying(255),
    parent2_surname character varying(255),
    parent2_phone character varying(20)
);


--
-- Name: verification_codes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.verification_codes (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    code character varying(6) NOT NULL,
    type character varying(255) NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT verification_codes_type_check CHECK (((type)::text = ANY ((ARRAY['email_verification'::character varying, 'password_reset'::character varying])::text[])))
);


--
-- Name: verification_codes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.verification_codes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: verification_codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.verification_codes_id_seq OWNED BY public.verification_codes.id;


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: verification_codes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.verification_codes ALTER COLUMN id SET DEFAULT nextval('public.verification_codes_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: class_registrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.class_registrations (id, user_id, grade_id, status, notes, created_at, updated_at, transaction_id) FROM stdin;
019d4316-ab7e-7290-8447-df011f291227	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-a94e-72bd-a6c2-14e5ca8d44ba	accepted	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-ac81-73d7-92c8-f8d45ac21b8f
019d4316-ab82-7322-90da-b478a12f6721	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-a94f-7197-9a97-80b6461d745c	accepted	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-ac8b-73fb-8eaf-38cce2b187aa
019d4316-ab81-72bc-9202-bc58ed3d035b	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-a951-70a1-88c7-8021b7b50aef	refused	Dossier incomplet — pièces justificatives manquantes.	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-ac8a-7205-b442-ce3271411f9c
019d4316-ab83-737a-9867-68212f4f8ddb	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-a94e-72bd-a6c2-14e5ca8d44ba	pending	En cours d'examen.	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-ac90-729b-84f9-3c0d99cad48b
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: fees; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.fees (id, type, total_amount, academic_year, title, classroom, description, number_of_installments, required, created_at, updated_at, deleted_at, grade_id, due_before, late_fine_per_week) FROM stdin;
019d4316-aa49-7043-addc-989b75e7c4c3	App\\Models\\RegistrationFee	35000.00	2025-2026	Frais d'inscription — 6ème	\N	Frais d'inscription pour l'entrée en 6ème.	\N	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	019d4316-a94e-72bd-a6c2-14e5ca8d44ba	2025-10-15	\N
019d4316-aa4c-716a-8140-c08ee67c85fa	App\\Models\\RegistrationFee	35000.00	2025-2026	Frais d'inscription — 3ème	\N	Frais d'inscription pour l'entrée en 3ème.	\N	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	019d4316-a94f-7197-9a97-80b6461d745c	2025-10-15	\N
019d4316-aa4d-7294-8639-9ac774d7ef19	App\\Models\\RegistrationFee	40000.00	2025-2026	Frais d'inscription — Terminale	\N	Frais d'inscription pour l'entrée en Terminale.	\N	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	019d4316-a951-70a1-88c7-8021b7b50aef	2025-10-15	\N
019d4316-aa4e-710f-bb48-9dc9c042e90c	App\\Models\\TuitionFee	450000.00	2025-2026	Scolarité 6ème 2025-2026	\N	Frais de scolarité annuels — 6ème, répartis en 3 versements.	3	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	019d4316-a94e-72bd-a6c2-14e5ca8d44ba	\N	5000.00
019d4316-aa73-722e-b7fe-5cfb79969cbc	App\\Models\\TuitionFee	480000.00	2025-2026	Scolarité 3ème 2025-2026	\N	Frais de scolarité annuels — 3ème, répartis en 3 versements.	3	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	019d4316-a94f-7197-9a97-80b6461d745c	\N	5000.00
019d4316-aa78-7254-a7b4-9d4ee6a0ca0b	App\\Models\\TuitionFee	600000.00	2025-2026	Scolarité Terminale 2025-2026	\N	Frais de scolarité annuels — Terminale, répartis en 3 versements.	3	\N	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	019d4316-a951-70a1-88c7-8021b7b50aef	\N	7500.00
019d4316-aa7e-73b4-81f7-d911cbde7aa9	App\\Models\\GeneralFee	15000.00	2025-2026	Activités sportives 2025-2026	\N	Cotisation annuelle pour la participation aux activités sportives scolaires.	\N	t	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	\N	2025-11-30	\N
019d4316-aa7f-71b9-99ea-74772b8ad232	App\\Models\\GeneralFee	25000.00	2025-2026	Sortie pédagogique 2025-2026	\N	Participation à la sortie pédagogique de fin de premier semestre.	\N	f	2026-03-31 08:50:52	2026-03-31 08:50:52	\N	\N	2026-02-28	\N
\.


--
-- Data for Name: grades; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.grades (id, name, description, created_at, updated_at) FROM stdin;
019d4316-a93c-70b7-be1f-75e5a7447c64	CM2	Cours Moyen 2ème année	2026-03-31 08:50:51	2026-03-31 08:50:51
019d4316-a94e-72bd-a6c2-14e5ca8d44ba	6ème	Sixième	2026-03-31 08:50:51	2026-03-31 08:50:51
019d4316-a94f-7197-9a97-80b6461d745c	3ème	Troisième	2026-03-31 08:50:51	2026-03-31 08:50:51
019d4316-a950-7340-b8ea-f5e18b3ed74c	Seconde	Seconde générale	2026-03-31 08:50:51	2026-03-31 08:50:51
019d4316-a951-70a1-88c7-8021b7b50aef	Terminale	Terminale générale	2026-03-31 08:50:51	2026-03-31 08:50:51
\.


--
-- Data for Name: installments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.installments (id, tuition_fee_id, number, amount, due_date, created_at, updated_at) FROM stdin;
019d4316-aa50-7100-8304-c5ea27a68baf	019d4316-aa4e-710f-bb48-9dc9c042e90c	1	200000.00	2025-10-31	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa6f-700e-af93-bfcc2b0ee6f1	019d4316-aa4e-710f-bb48-9dc9c042e90c	2	150000.00	2026-01-31	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa71-7243-af42-04c6a5190202	019d4316-aa4e-710f-bb48-9dc9c042e90c	3	100000.00	2026-04-30	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa74-705a-aa9d-0f62f2cfa9d2	019d4316-aa73-722e-b7fe-5cfb79969cbc	1	210000.00	2025-10-31	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa76-71f5-a0f4-ab8a5508f7a2	019d4316-aa73-722e-b7fe-5cfb79969cbc	2	160000.00	2026-01-31	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa77-71c8-8099-56252ea349bf	019d4316-aa73-722e-b7fe-5cfb79969cbc	3	110000.00	2026-04-30	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa7a-7163-9333-92a81e2703ba	019d4316-aa78-7254-a7b4-9d4ee6a0ca0b	1	250000.00	2025-10-31	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa7b-73d1-adff-ea0a8a71f545	019d4316-aa78-7254-a7b4-9d4ee6a0ca0b	2	200000.00	2026-01-31	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-aa7c-71dd-a3f5-e5074bcdcdb8	019d4316-aa78-7254-a7b4-9d4ee6a0ca0b	3	150000.00	2026-04-30	2026-03-31 08:50:52	2026-03-31 08:50:52
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_03_16_153654_create_permission_tables	1
5	2026_03_16_154320_create_fees_table	1
6	2026_03_16_154406_create_payments_table	1
7	2026_03_16_160455_create_installments_table	1
8	2026_03_16_213731_add_installment_id_to_transactions_table	1
9	2026_03_19_184345_create_verification_codes_table	1
10	2026_03_20_095405_create_grades_table	1
11	2026_03_20_095410_create_class_registrations_table	1
12	2026_03_20_095500_add_grade_id_to_fees_table	1
13	2026_03_20_120000_add_due_before_to_fees_table	1
14	2026_03_20_130000_add_transaction_id_to_class_registrations_table	1
15	2026_03_20_231146_add_late_fine_per_week_to_fees_table	1
16	2026_03_21_114016_create_refund_requests_table	1
\.


--
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
1	App\\Models\\User	019d4316-a493-721c-ae4d-555c5e1c6ae2
2	App\\Models\\User	019d4316-a57f-7063-9bcf-0dace1fe2b30
5	App\\Models\\User	019d4316-a632-71ab-9b71-b9f7a99ae3d1
4	App\\Models\\User	019d4316-a6e5-732c-911c-7839208db280
3	App\\Models\\User	019d4316-a798-7312-85d0-e96e30bedc00
3	App\\Models\\User	019d4316-a84e-73dd-8b45-b76acb5d2c61
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
1	view users	web	2026-03-31 08:50:49	2026-03-31 08:50:49
2	create users	web	2026-03-31 08:50:49	2026-03-31 08:50:49
3	edit users	web	2026-03-31 08:50:49	2026-03-31 08:50:49
4	delete users	web	2026-03-31 08:50:49	2026-03-31 08:50:49
5	view fees	web	2026-03-31 08:50:49	2026-03-31 08:50:49
6	create fees	web	2026-03-31 08:50:49	2026-03-31 08:50:49
7	edit fees	web	2026-03-31 08:50:49	2026-03-31 08:50:49
8	delete fees	web	2026-03-31 08:50:49	2026-03-31 08:50:49
9	approve fees	web	2026-03-31 08:50:49	2026-03-31 08:50:49
10	view transactions	web	2026-03-31 08:50:49	2026-03-31 08:50:49
11	process transactions	web	2026-03-31 08:50:49	2026-03-31 08:50:49
12	refund transactions	web	2026-03-31 08:50:49	2026-03-31 08:50:49
13	view reports	web	2026-03-31 08:50:49	2026-03-31 08:50:49
14	export reports	web	2026-03-31 08:50:49	2026-03-31 08:50:49
\.


--
-- Data for Name: refund_requests; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.refund_requests (id, transaction_id, user_id, reason, status, notes, created_at, updated_at) FROM stdin;
019d4316-ad9b-70a7-807b-133f6a4fb960	019d4316-ac85-7223-9bae-18a6e8130f4e	019d4316-a798-7312-85d0-e96e30bedc00	Double paiement effectué par erreur. Le même versement a été débité deux fois depuis mon compte mobile money.	pending	\N	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-ada0-71f2-b9af-c0543435ecfb	019d4316-ac88-7019-82c3-8088b914051f	019d4316-a798-7312-85d0-e96e30bedc00	Activité annulée par l'établissement en cours d'année scolaire.	accepted	Remboursement approuvé — l'activité sportive a été annulée par l'administration. Montant intégral restitué.	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-ada1-704c-a5d3-929bc5491f67	019d4316-ac8b-73fb-8eaf-38cce2b187aa	019d4316-a84e-73dd-8b45-b76acb5d2c61	Inscription annulée avant la validation officielle du dossier par la scolarité.	refused	Les frais d'inscription ne sont pas remboursables une fois le dossier transmis au service de scolarité et traité.	2026-03-31 08:50:52	2026-03-31 08:50:52
019d4316-ada2-73bd-b272-cfbaf6f1dc95	019d4316-ac8d-726a-ab26-eb40e2fa0810	019d4316-a84e-73dd-8b45-b76acb5d2c61	Changement d'établissement scolaire en cours d'année suite à un déménagement familial.	accepted	Remboursement partiel approuvé au prorata du trimestre non effectué. Dossier transmis au service comptable.	2026-03-31 08:50:52	2026-03-31 08:50:52
\.


--
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
1	1
2	1
3	1
4	1
5	1
6	1
7	1
8	1
9	1
10	1
11	1
12	1
13	1
14	1
5	2
6	2
7	2
10	2
11	2
12	2
13	2
14	2
1	5
2	5
3	5
5	5
10	5
13	5
1	4
5	4
10	4
5	3
10	3
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	admin	web	2026-03-31 08:50:49	2026-03-31 08:50:49
2	accountant	web	2026-03-31 08:50:49	2026-03-31 08:50:49
3	parent_student	web	2026-03-31 08:50:49	2026-03-31 08:50:49
4	employee	web	2026-03-31 08:50:49	2026-03-31 08:50:49
5	secretary	web	2026-03-31 08:50:49	2026-03-31 08:50:49
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: transactions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.transactions (id, user_id, fee_id, amount, date, status, kkiapay_reference, phone_number, created_at, updated_at, installment_id) FROM stdin;
019d4316-ac81-73d7-92c8-f8d45ac21b8f	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa49-7043-addc-989b75e7c4c3	35000.00	2025-09-15	completed	ALICE-REG-2025-001	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
019d4316-ac85-7223-9bae-18a6e8130f4e	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa4e-710f-bb48-9dc9c042e90c	200000.00	2025-10-20	completed	ALICE-TUI-2025-001	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-aa50-7100-8304-c5ea27a68baf
019d4316-ac86-7012-b64c-6767db028eeb	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa4e-710f-bb48-9dc9c042e90c	150000.00	2026-01-18	completed	ALICE-TUI-2025-002	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-aa6f-700e-af93-bfcc2b0ee6f1
019d4316-ac87-705c-8dfd-0decb7d679ed	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa4e-710f-bb48-9dc9c042e90c	100000.00	2026-04-01	pending	\N	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-aa71-7243-af42-04c6a5190202
019d4316-ac88-7019-82c3-8088b914051f	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa7e-73b4-81f7-d911cbde7aa9	15000.00	2025-11-05	completed	ALICE-GEN-2025-001	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
019d4316-ac89-7289-841d-e23636e4f6bd	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa7f-71b9-99ea-74772b8ad232	25000.00	2026-02-10	failed	\N	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
019d4316-ac8a-7205-b442-ce3271411f9c	019d4316-a798-7312-85d0-e96e30bedc00	019d4316-aa4d-7294-8639-9ac774d7ef19	40000.00	2025-09-10	completed	ALICE-REG-TERM-2025-001	+2290100000012	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
019d4316-ac8b-73fb-8eaf-38cce2b187aa	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-aa4c-716a-8140-c08ee67c85fa	35000.00	2025-09-18	completed	BOB-REG-2025-001	+2290100000093	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
019d4316-ac8c-712d-b50a-6f068edfa3fb	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-aa73-722e-b7fe-5cfb79969cbc	210000.00	2025-10-22	completed	BOB-TUI-2025-001	+2290100000093	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-aa74-705a-aa9d-0f62f2cfa9d2
019d4316-ac8d-726a-ab26-eb40e2fa0810	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-aa73-722e-b7fe-5cfb79969cbc	160000.00	2026-01-20	refunded	BOB-TUI-2025-002	+2290100000093	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-aa76-71f5-a0f4-ab8a5508f7a2
019d4316-ac8e-7014-a162-15866dd2026e	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-aa73-722e-b7fe-5cfb79969cbc	110000.00	2026-04-05	pending	\N	+2290100000093	2026-03-31 08:50:52	2026-03-31 08:50:52	019d4316-aa77-71c8-8099-56252ea349bf
019d4316-ac8f-70c4-a0c1-50e9bce33a57	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-aa7e-73b4-81f7-d911cbde7aa9	15000.00	2025-11-08	completed	BOB-GEN-2025-001	+2290100000093	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
019d4316-ac90-729b-84f9-3c0d99cad48b	019d4316-a84e-73dd-8b45-b76acb5d2c61	019d4316-aa49-7043-addc-989b75e7c4c3	35000.00	2025-09-20	completed	BOB-REG-6-2025-001	+2290100000093	2026-03-31 08:50:52	2026-03-31 08:50:52	\N
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, surname, email, phone_number, email_verified_at, password, verified, remember_token, created_at, updated_at, matricule, classroom, academic_year, parent1_name, parent1_surname, parent1_phone, parent2_name, parent2_surname, parent2_phone) FROM stdin;
019d4316-a493-721c-ae4d-555c5e1c6ae2	Admin	User	admin@schoolfees.com	+2290123456789	2026-03-31 08:50:50	$2y$12$G.snwv41GRVMgsXdk3ovH.WAT33.l51J6MrommT5Bx/ZZUmi3uUV.	t	\N	2026-03-31 08:50:50	2026-03-31 08:50:50	\N	\N	\N	\N	\N	\N	\N	\N	\N
019d4316-a57f-7063-9bcf-0dace1fe2b30	John	Accountant	accountant@schoolfees.com	+2290100000089	2026-03-31 08:50:50	$2y$12$ZgYzFNzY4D.8yispmckTTe7IgR2L6Gb.OnLrv5/yzAKPILzbb4Tl2	t	\N	2026-03-31 08:50:50	2026-03-31 08:50:50	\N	\N	\N	\N	\N	\N	\N	\N	\N
019d4316-a632-71ab-9b71-b9f7a99ae3d1	Jane	Secretary	secretary@schoolfees.com	+2290100000074	2026-03-31 08:50:51	$2y$12$GxYdMjIUQgz6ZbjyASi1BOwNUwSArGO0G293l8P4fQUWuw2EPNRtC	t	\N	2026-03-31 08:50:51	2026-03-31 08:50:51	\N	\N	\N	\N	\N	\N	\N	\N	\N
019d4316-a6e5-732c-911c-7839208db280	Mike	Employee	employee@schoolfees.com	+2290100000009	2026-03-31 08:50:51	$2y$12$GGZ5PCSn6bntVRuHJICvpOvtEqprqvy5CbGPj9pLVkoD3Nvi5o0Fy	t	\N	2026-03-31 08:50:51	2026-03-31 08:50:51	\N	\N	\N	\N	\N	\N	\N	\N	\N
019d4316-a798-7312-85d0-e96e30bedc00	Alice	Student	alice.student@schoolfees.com	+2290100000012	2026-03-31 08:50:51	$2y$12$qHjTV8pKEfwCx4bdh6190eGITaM0a5VesTiNFudxR7My8I4aY3kG2	t	\N	2026-03-31 08:50:51	2026-03-31 08:50:51	STU2024001	Grade 10A	2024-2025	Robert	Student	+2290100000010	Maria	Student	+2290100000041
019d4316-a84e-73dd-8b45-b76acb5d2c61	Bob	Scholar	bob.scholar@schoolfees.com	+2290100000093	2026-03-31 08:50:51	$2y$12$g1m2UmpRBQYmUXkcUfVA5uz8WpYyULlC3fGvBxFZApztW4wn2BbcS	t	\N	2026-03-31 08:50:51	2026-03-31 08:50:51	STU2024002	Grade 11B	2024-2025	David	Scholar	+2290100000026	Sarah	Scholar	+2290100000027
\.


--
-- Data for Name: verification_codes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.verification_codes (id, email, code, type, expires_at, created_at) FROM stdin;
\.


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 16, true);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.permissions_id_seq', 14, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.roles_id_seq', 5, true);


--
-- Name: verification_codes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.verification_codes_id_seq', 1, false);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: class_registrations class_registrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.class_registrations
    ADD CONSTRAINT class_registrations_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fees fees_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fees
    ADD CONSTRAINT fees_pkey PRIMARY KEY (id);


--
-- Name: grades grades_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.grades
    ADD CONSTRAINT grades_pkey PRIMARY KEY (id);


--
-- Name: installments installments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.installments
    ADD CONSTRAINT installments_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: refund_requests refund_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.refund_requests
    ADD CONSTRAINT refund_requests_pkey PRIMARY KEY (id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: transactions transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_matricule_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_matricule_unique UNIQUE (matricule);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: verification_codes verification_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.verification_codes
    ADD CONSTRAINT verification_codes_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: class_registrations_user_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX class_registrations_user_id_status_index ON public.class_registrations USING btree (user_id, status);


--
-- Name: fees_academic_year_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fees_academic_year_index ON public.fees USING btree (academic_year);


--
-- Name: fees_classroom_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fees_classroom_index ON public.fees USING btree (classroom);


--
-- Name: fees_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fees_type_index ON public.fees USING btree (type);


--
-- Name: installments_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX installments_due_date_index ON public.installments USING btree (due_date);


--
-- Name: installments_tuition_fee_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX installments_tuition_fee_id_index ON public.installments USING btree (tuition_fee_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: refund_requests_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refund_requests_status_index ON public.refund_requests USING btree (status);


--
-- Name: refund_requests_transaction_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refund_requests_transaction_id_index ON public.refund_requests USING btree (transaction_id);


--
-- Name: refund_requests_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refund_requests_user_id_index ON public.refund_requests USING btree (user_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: transactions_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX transactions_date_index ON public.transactions USING btree (date);


--
-- Name: transactions_installment_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX transactions_installment_id_index ON public.transactions USING btree (installment_id);


--
-- Name: transactions_kkiapay_reference_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX transactions_kkiapay_reference_index ON public.transactions USING btree (kkiapay_reference);


--
-- Name: transactions_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX transactions_status_index ON public.transactions USING btree (status);


--
-- Name: users_academic_year_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_academic_year_index ON public.users USING btree (academic_year);


--
-- Name: users_classroom_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_classroom_index ON public.users USING btree (classroom);


--
-- Name: users_matricule_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_matricule_index ON public.users USING btree (matricule);


--
-- Name: users_verified_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_verified_index ON public.users USING btree (verified);


--
-- Name: verification_codes_email_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX verification_codes_email_index ON public.verification_codes USING btree (email);


--
-- Name: class_registrations class_registrations_grade_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.class_registrations
    ADD CONSTRAINT class_registrations_grade_id_foreign FOREIGN KEY (grade_id) REFERENCES public.grades(id) ON DELETE CASCADE;


--
-- Name: class_registrations class_registrations_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.class_registrations
    ADD CONSTRAINT class_registrations_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES public.transactions(id) ON DELETE SET NULL;


--
-- Name: class_registrations class_registrations_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.class_registrations
    ADD CONSTRAINT class_registrations_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: fees fees_grade_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fees
    ADD CONSTRAINT fees_grade_id_foreign FOREIGN KEY (grade_id) REFERENCES public.grades(id) ON DELETE SET NULL;


--
-- Name: installments installments_tuition_fee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.installments
    ADD CONSTRAINT installments_tuition_fee_id_foreign FOREIGN KEY (tuition_fee_id) REFERENCES public.fees(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: refund_requests refund_requests_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.refund_requests
    ADD CONSTRAINT refund_requests_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES public.transactions(id) ON DELETE CASCADE;


--
-- Name: refund_requests refund_requests_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.refund_requests
    ADD CONSTRAINT refund_requests_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: transactions transactions_fee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_fee_id_foreign FOREIGN KEY (fee_id) REFERENCES public.fees(id) ON DELETE SET NULL;


--
-- Name: transactions transactions_installment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_installment_id_foreign FOREIGN KEY (installment_id) REFERENCES public.installments(id) ON DELETE SET NULL;


--
-- Name: transactions transactions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict g3ULlbefq7rCRlIP4mvcdDxabgcaES1FkfbKtdX4h9D8h6HiDTq9A8WXzBKYYJP

