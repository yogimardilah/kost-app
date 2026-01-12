--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9
-- Dumped by pg_dump version 16.9

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

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

-- *not* creating schema, since initdb creates it


-- ALTER SCHEMA public OWNER TO sereneco_user_kost_db;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

-- COMMENT ON SCHEMA public IS '';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: addon_transaction_details; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addon_transaction_details (
    id bigint NOT NULL,
    addon_transaction_id bigint NOT NULL,
    addon_id bigint,
    nama_addon character varying(255) NOT NULL,
    qty integer DEFAULT 1 NOT NULL,
    harga numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    subtotal numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.addon_transaction_details OWNER TO postgres;

--
-- Name: addon_transaction_details_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.addon_transaction_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.addon_transaction_details_id_seq OWNER TO postgres;

--
-- Name: addon_transaction_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.addon_transaction_details_id_seq OWNED BY public.addon_transaction_details.id;


--
-- Name: addon_transactions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.addon_transactions (
    id bigint NOT NULL,
    consumer_id bigint NOT NULL,
    room_id bigint,
    invoice_number character varying(255) NOT NULL,
    tanggal date,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    total numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    catatan text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.addon_transactions OWNER TO postgres;

--
-- Name: addon_transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.addon_transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.addon_transactions_id_seq OWNER TO postgres;

--
-- Name: addon_transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.addon_transactions_id_seq OWNED BY public.addon_transactions.id;


--
-- Name: billing_details; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.billing_details (
    id bigint NOT NULL,
    billing_id bigint NOT NULL,
    keterangan text NOT NULL,
    qty integer NOT NULL,
    harga numeric(12,2) NOT NULL,
    subtotal numeric(12,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.billing_details OWNER TO postgres;

--
-- Name: billing_details_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.billing_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.billing_details_id_seq OWNER TO postgres;

--
-- Name: billing_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.billing_details_id_seq OWNED BY public.billing_details.id;


--
-- Name: billing_reminders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.billing_reminders (
    id bigint NOT NULL,
    billing_id bigint NOT NULL,
    days_overdue integer DEFAULT 0 NOT NULL,
    note text,
    is_sent boolean DEFAULT false NOT NULL,
    sent_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.billing_reminders OWNER TO postgres;

--
-- Name: billing_reminders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.billing_reminders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.billing_reminders_id_seq OWNER TO postgres;

--
-- Name: billing_reminders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.billing_reminders_id_seq OWNED BY public.billing_reminders.id;


--
-- Name: billings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.billings (
    id bigint NOT NULL,
    invoice_number character varying(255) NOT NULL,
    consumer_id bigint NOT NULL,
    room_id bigint NOT NULL,
    periode_awal date NOT NULL,
    periode_akhir date NOT NULL,
    total_tagihan numeric(12,2) NOT NULL,
    status character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.billings OWNER TO postgres;

--
-- Name: billings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.billings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.billings_id_seq OWNER TO postgres;

--
-- Name: billings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.billings_id_seq OWNED BY public.billings.id;


--
-- Name: consumers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.consumers (
    id bigint NOT NULL,
    nik character varying(20) NOT NULL,
    nama character varying(255) NOT NULL,
    no_hp character varying(20) NOT NULL,
    kendaraan character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tanda_pengenal character varying(255),
    kontak_darurat_nama character varying(255),
    kontak_darurat_hubungan character varying(255),
    kontak_darurat_no_hp character varying(20)
);


ALTER TABLE public.consumers OWNER TO postgres;

--
-- Name: consumers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.consumers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.consumers_id_seq OWNER TO postgres;

--
-- Name: consumers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.consumers_id_seq OWNED BY public.consumers.id;


--
-- Name: employees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.employees (
    id bigint NOT NULL,
    nik character varying(20) NOT NULL,
    nama character varying(255) NOT NULL,
    jabatan character varying(255) NOT NULL,
    tanggal_bergabung date NOT NULL,
    tanggal_berakhir date,
    gaji numeric(15,2) NOT NULL,
    tanggal_gajian integer DEFAULT 1 NOT NULL,
    no_hp character varying(20) NOT NULL,
    alamat text,
    foto character varying(255),
    status character varying(255) DEFAULT 'aktif'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT employees_status_check CHECK (((status)::text = ANY ((ARRAY['aktif'::character varying, 'tidak aktif'::character varying])::text[])))
);


ALTER TABLE public.employees OWNER TO postgres;

--
-- Name: COLUMN employees.tanggal_gajian; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.employees.tanggal_gajian IS 'Tanggal gajian setiap bulan (1-31)';


--
-- Name: employees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.employees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.employees_id_seq OWNER TO postgres;

--
-- Name: employees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.employees_id_seq OWNED BY public.employees.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: kosts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kosts (
    id bigint NOT NULL,
    nama_kost character varying(255) NOT NULL,
    alamat text,
    kota character varying(255),
    provinsi character varying(255),
    telepon character varying(255),
    email character varying(255),
    harga_dasar numeric(12,0),
    deskripsi text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.kosts OWNER TO postgres;

--
-- Name: COLUMN kosts.harga_dasar; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.kosts.harga_dasar IS 'Base price for rooms';


--
-- Name: kosts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kosts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kosts_id_seq OWNER TO postgres;

--
-- Name: kosts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kosts_id_seq OWNED BY public.kosts.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: payments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payments (
    id bigint NOT NULL,
    billing_id bigint NOT NULL,
    tanggal_bayar timestamp(0) without time zone NOT NULL,
    jumlah numeric(12,2) NOT NULL,
    metode character varying(255) NOT NULL,
    bukti_bayar text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payments OWNER TO postgres;

--
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payments_id_seq OWNER TO postgres;

--
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.payments_id_seq OWNED BY public.payments.id;


--
-- Name: payrolls; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payrolls (
    id bigint NOT NULL,
    employee_id bigint NOT NULL,
    bulan integer NOT NULL,
    tahun integer NOT NULL,
    gaji_pokok numeric(15,2) NOT NULL,
    bonus numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    potongan numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    total_gaji numeric(15,2) NOT NULL,
    tanggal_bayar timestamp(0) without time zone,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    keterangan text,
    file_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    slip_number character varying(255),
    CONSTRAINT payrolls_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'dibayar'::character varying])::text[])))
);


ALTER TABLE public.payrolls OWNER TO postgres;

--
-- Name: COLUMN payrolls.bulan; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.payrolls.bulan IS '1-12';


--
-- Name: COLUMN payrolls.file_path; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.payrolls.file_path IS 'Path to uploaded file';


--
-- Name: COLUMN payrolls.slip_number; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.payrolls.slip_number IS 'Nomor Slip Gaji';


--
-- Name: payrolls_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.payrolls_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payrolls_id_seq OWNER TO postgres;

--
-- Name: payrolls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.payrolls_id_seq OWNED BY public.payrolls.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: purchases; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.purchases (
    id bigint NOT NULL,
    kost_id bigint NOT NULL,
    description character varying(255) NOT NULL,
    category character varying(255) DEFAULT 'maintenance'::character varying NOT NULL,
    amount numeric(12,2) NOT NULL,
    purchase_date timestamp(0) without time zone NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    file_path character varying(255),
    deleted_at timestamp(0) without time zone,
    created_by bigint,
    updated_by bigint,
    deleted_by bigint
);


ALTER TABLE public.purchases OWNER TO postgres;

--
-- Name: purchases_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.purchases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.purchases_id_seq OWNER TO postgres;

--
-- Name: purchases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.purchases_id_seq OWNED BY public.purchases.id;


--
-- Name: role_permissions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.role_permissions (
    id bigint NOT NULL,
    role_id bigint NOT NULL,
    menu_code character varying(255) NOT NULL,
    can_view boolean DEFAULT false NOT NULL,
    can_create boolean DEFAULT false NOT NULL,
    can_update boolean DEFAULT false NOT NULL,
    can_delete boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.role_permissions OWNER TO postgres;

--
-- Name: COLUMN role_permissions.menu_code; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.role_permissions.menu_code IS 'e.g., dashboard, master_data, kost, rooms, etc';


--
-- Name: role_permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.role_permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.role_permissions_id_seq OWNER TO postgres;

--
-- Name: role_permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.role_permissions_id_seq OWNED BY public.role_permissions.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: room_addon_maps; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.room_addon_maps (
    id bigint NOT NULL,
    room_id bigint NOT NULL,
    addon_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.room_addon_maps OWNER TO postgres;

--
-- Name: room_addon_maps_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.room_addon_maps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.room_addon_maps_id_seq OWNER TO postgres;

--
-- Name: room_addon_maps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.room_addon_maps_id_seq OWNED BY public.room_addon_maps.id;


--
-- Name: room_addons; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.room_addons (
    id bigint NOT NULL,
    nama_addon character varying(255) NOT NULL,
    harga numeric(12,2) NOT NULL,
    satuan character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.room_addons OWNER TO postgres;

--
-- Name: room_addons_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.room_addons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.room_addons_id_seq OWNER TO postgres;

--
-- Name: room_addons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.room_addons_id_seq OWNED BY public.room_addons.id;


--
-- Name: room_occupancies; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.room_occupancies (
    id bigint NOT NULL,
    room_id bigint NOT NULL,
    consumer_id bigint NOT NULL,
    tanggal_masuk date NOT NULL,
    tanggal_keluar date,
    status character varying(20) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tipe_harga character varying(20) DEFAULT 'bulanan'::character varying NOT NULL
);


ALTER TABLE public.room_occupancies OWNER TO postgres;

--
-- Name: room_occupancies_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.room_occupancies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.room_occupancies_id_seq OWNER TO postgres;

--
-- Name: room_occupancies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.room_occupancies_id_seq OWNED BY public.room_occupancies.id;


--
-- Name: rooms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rooms (
    id bigint NOT NULL,
    kost_id bigint NOT NULL,
    nomor_kamar character varying(255) NOT NULL,
    jenis_kamar character varying(255) NOT NULL,
    harga numeric(12,0) NOT NULL,
    status character varying(20) DEFAULT 'tersedia'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tipe_harga character varying(20) DEFAULT 'bulanan'::character varying NOT NULL,
    harga_harian numeric(12,0),
    fasilitas text
);


ALTER TABLE public.rooms OWNER TO postgres;

--
-- Name: rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rooms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rooms_id_seq OWNER TO postgres;

--
-- Name: rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rooms_id_seq OWNED BY public.rooms.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role_id bigint NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: addon_transaction_details id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transaction_details ALTER COLUMN id SET DEFAULT nextval('public.addon_transaction_details_id_seq'::regclass);


--
-- Name: addon_transactions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transactions ALTER COLUMN id SET DEFAULT nextval('public.addon_transactions_id_seq'::regclass);


--
-- Name: billing_details id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billing_details ALTER COLUMN id SET DEFAULT nextval('public.billing_details_id_seq'::regclass);


--
-- Name: billing_reminders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billing_reminders ALTER COLUMN id SET DEFAULT nextval('public.billing_reminders_id_seq'::regclass);


--
-- Name: billings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billings ALTER COLUMN id SET DEFAULT nextval('public.billings_id_seq'::regclass);


--
-- Name: consumers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consumers ALTER COLUMN id SET DEFAULT nextval('public.consumers_id_seq'::regclass);


--
-- Name: employees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employees ALTER COLUMN id SET DEFAULT nextval('public.employees_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: kosts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kosts ALTER COLUMN id SET DEFAULT nextval('public.kosts_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments ALTER COLUMN id SET DEFAULT nextval('public.payments_id_seq'::regclass);


--
-- Name: payrolls id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payrolls ALTER COLUMN id SET DEFAULT nextval('public.payrolls_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: purchases id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases ALTER COLUMN id SET DEFAULT nextval('public.purchases_id_seq'::regclass);


--
-- Name: role_permissions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions ALTER COLUMN id SET DEFAULT nextval('public.role_permissions_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: room_addon_maps id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_addon_maps ALTER COLUMN id SET DEFAULT nextval('public.room_addon_maps_id_seq'::regclass);


--
-- Name: room_addons id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_addons ALTER COLUMN id SET DEFAULT nextval('public.room_addons_id_seq'::regclass);


--
-- Name: room_occupancies id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_occupancies ALTER COLUMN id SET DEFAULT nextval('public.room_occupancies_id_seq'::regclass);


--
-- Name: rooms id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rooms ALTER COLUMN id SET DEFAULT nextval('public.rooms_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: addon_transaction_details; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addon_transaction_details (id, addon_transaction_id, addon_id, nama_addon, qty, harga, subtotal, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: addon_transactions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.addon_transactions (id, consumer_id, room_id, invoice_number, tanggal, status, total, catatan, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: billing_details; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.billing_details (id, billing_id, keterangan, qty, harga, subtotal, created_at, updated_at) FROM stdin;
1	1	Sewa Kamar 1B - Bulanan (Prorate: 29 hari dari 31 hari)	29	32258.00	935500.00	2026-01-07 07:56:52	2026-01-07 07:56:52
2	2	Sewa Kamar 4 C - Bulanan (Prorate: 29 hari dari 31 hari)	29	32258.00	935500.00	2026-01-07 15:38:17	2026-01-07 15:38:17
3	3	Sewa Kamar 3 B - Bulanan (Prorate: 29 hari dari 31 hari)	29	32258.00	935500.00	2026-01-07 18:10:59	2026-01-07 18:10:59
4	4	Sewa Kamar 1B - Bulanan (Prorate: 27 hari dari 28 hari)	27	35714.00	964300.00	2026-01-09 10:02:38	2026-01-09 10:02:38
5	5	Sewa Kamar 1A - Bulanan (Prorate: 27 hari dari 31 hari)	27	32258.00	871000.00	2026-01-09 14:47:23	2026-01-09 14:47:23
6	6	Sewa Kamar 1H - Bulanan (Prorate: 4 hari dari 31 hari)	4	32258.00	129000.00	2026-01-09 14:51:11	2026-01-09 14:51:11
7	7	Sewa Kamar 1D - Bulanan (Prorate: 14 hari dari 31 hari)	14	32258.00	451600.00	2026-01-09 15:01:02	2026-01-09 15:01:02
8	8	Sewa Kamar 1C - Harian (3 hari)	3	100000.00	300000.00	2026-01-09 15:09:22	2026-01-09 15:09:22
9	9	Sewa Kamar 1Y - Bulanan (Prorate: 27 hari dari 31 hari)	27	32258.00	871000.00	2026-01-09 15:12:54	2026-01-09 15:12:54
10	10	Sewa Kamar 1C - Harian (5 hari)	5	100000.00	500000.00	2026-01-09 15:16:25	2026-01-09 15:16:25
11	10	snack	2	5000.00	10000.00	2026-01-09 15:19:09	2026-01-09 15:19:09
12	10	snack	1	5000.00	5000.00	2026-01-09 15:22:14	2026-01-09 15:22:14
13	10	snack	3	5000.00	15000.00	2026-01-09 15:28:18	2026-01-09 15:28:18
\.


--
-- Data for Name: billing_reminders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.billing_reminders (id, billing_id, days_overdue, note, is_sent, sent_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: billings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.billings (id, invoice_number, consumer_id, room_id, periode_awal, periode_akhir, total_tagihan, status, created_at, updated_at) FROM stdin;
1	INV-20260107-00001	1	2	2026-01-07	2026-02-05	935500.00	lunas	2026-01-07 07:56:52	2026-01-07 07:57:42
2	INV-20260107-00002	2	55	2026-01-07	2026-02-05	935500.00	lunas	2026-01-07 15:38:17	2026-01-07 15:38:51
3	INV-20260107-00003	3	54	2026-01-07	2026-02-05	935500.00	lunas	2026-01-07 18:10:59	2026-01-07 18:11:33
4	INV-20260109-00001	1	2	2026-02-06	2026-03-05	964300.00	lunas	2026-01-09 10:02:38	2026-01-09 10:05:19
5	INV-20260109-00002	4	1	2026-01-09	2026-02-05	871000.00	lunas	2026-01-09 14:47:23	2026-01-09 14:48:07
7	INV-20260109-00004	6	4	2026-01-09	2026-01-23	451600.00	lunas	2026-01-09 15:01:02	2026-01-09 15:08:44
8	INV-20260109-00005	6	3	2026-01-09	2026-01-12	300000.00	lunas	2026-01-09 15:09:22	2026-01-09 15:12:13
9	INV-20260109-00006	6	25	2026-01-09	2026-02-05	871000.00	lunas	2026-01-09 15:12:54	2026-01-09 15:13:18
6	INV-20260109-00003	5	8	2026-01-09	2026-01-13	129000.00	lunas	2026-01-09 14:51:11	2026-01-09 15:13:54
10	INV-20260109-00007	7	3	2026-01-09	2026-01-14	530000.00	lunas	2026-01-09 15:16:25	2026-01-09 15:28:42
\.


--
-- Data for Name: consumers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.consumers (id, nik, nama, no_hp, kendaraan, created_at, updated_at, tanda_pengenal, kontak_darurat_nama, kontak_darurat_hubungan, kontak_darurat_no_hp) FROM stdin;
1	9999999999999999	bram	085794778888	motor	2026-01-07 07:56:32	2026-01-07 07:56:32	tanda_pengenal/1767772592_695e11b08bb9a.jpg	budi	Teman	085678782639
2	1241312311211	bramerame	08122311289	mio zportyyy	2026-01-07 15:37:41	2026-01-07 15:37:41	tanda_pengenal/1767775061_695e1b555b922.jpg	jokowow	Orang Tua	081929231122
3	12411321313131	setyaaaaa	081222223333	avanza - W 2345 NV	2026-01-07 18:10:38	2026-01-07 18:10:38	tanda_pengenal/1767784238_695e3f2e5e169.jpg	solikin	Orang Tua	081299994444
4	1234561122	yogi aspal	08122344412	Vespa klasik tapi matic	2026-01-09 14:46:41	2026-01-09 14:46:41	tanda_pengenal/1767944801_6960b2613cdde.jpg	solikin	Orang Tua	089122224444
5	124131231121131	harianto	081222228888	mio G - L 1234 ZVV	2026-01-09 14:50:42	2026-01-09 14:50:42	tanda_pengenal/1767945042_6960b352c6b2e.jpg	solikin	Saudara	08921287178
6	999999999999900	gilang	085794779999	mtr	2026-01-09 15:00:15	2026-01-09 15:00:15	\N	buli	Orang Tua	085678789292
7	123457654332213	sutimin	08122311289	BEAT HITAM - L 1234 WB	2026-01-09 15:15:56	2026-01-09 15:15:56	tanda_pengenal/1767946556_6960b93ca5ae9.jpg	budiiiii	Saudara	081298761234
\.


--
-- Data for Name: employees; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.employees (id, nik, nama, jabatan, tanggal_bergabung, tanggal_berakhir, gaji, tanggal_gajian, no_hp, alamat, foto, status, created_at, updated_at) FROM stdin;
1	9999999999999999	sandy	staff	2026-01-07	2027-03-31	5000000.00	1	085794669999	jkt	employees/1767772548_WhatsApp Image 2026-01-06 at 13.05.51.jpeg	aktif	2026-01-07 07:55:48	2026-01-07 07:55:48
2	12411321313131	jonii	staff jaga malam	2026-01-07	\N	1900000.00	1	08122344412	\N	employees/1767775715_Gemini_Generated_Image_953ju953ju953ju9.png	aktif	2026-01-07 15:48:35	2026-01-07 15:48:35
3	1521312311	asdasssss	petugas kebersihan	2025-12-01	\N	1500000.00	1	08122311289	asdwdasdasdadassdaas	employees/1767775840_kosan.png	aktif	2026-01-07 15:50:41	2026-01-07 15:50:41
4	6341423111	joni andrea	manager	2025-12-01	\N	3000000.00	1	08122311289	fsdfdasdaadasadasdadasdasd	employees/1767784019_kosan.png	aktif	2026-01-07 18:06:59	2026-01-07 18:06:59
5	3515181110920002	wahyu april	staff ahli	2025-12-16	\N	3500000.00	1	08122481919	\N	employees/1767949727_Gemini_Generated_Image_953ju953ju953ju9.png	aktif	2026-01-09 16:08:48	2026-01-09 16:08:48
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: kosts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kosts (id, nama_kost, alamat, kota, provinsi, telepon, email, harga_dasar, deskripsi, created_at, updated_at) FROM stdin;
1	SERENE	jkt	\N	\N	\N	\N	\N	\N	2026-01-07 07:47:19	2026-01-07 15:40:34
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_reset_tokens_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2019_12_14_000001_create_personal_access_tokens_table	1
5	2025_12_17_043840_test_pgsql	1
6	2025_12_17_044358_create_roles_table	1
7	2025_12_17_044451_add_role_id_to_users_table	1
8	2025_12_17_045700_create_kosts_table	1
9	2025_12_17_045710_create_consumers_table	1
10	2025_12_17_045720_create_rooms_table	1
11	2025_12_17_045730_create_room_addons_table	1
12	2025_12_17_045740_create_room_addon_maps_table	1
13	2025_12_17_045750_create_room_occupancies_table	1
14	2025_12_17_045802_create_billings_table	1
15	2025_12_17_045803_create_billing_details_table	1
16	2025_12_17_045804_create_payments_table	1
17	2025_12_17_050900_add_role_id_to_users_table	1
18	2025_12_17_200000_create_billing_reminders_table	1
19	2025_12_22_122259_add_tipe_harga_to_rooms_table	1
20	2025_12_22_122314_add_tipe_harga_to_rooms_table	1
21	2025_12_22_122900_add_harga_harian_to_rooms_table	1
22	2025_12_22_123134_add_harga_harian_to_rooms_table	1
23	2025_12_22_140000_add_harga_harian_to_rooms_table	1
24	2025_12_22_150000_create_role_permissions_table	1
25	2025_12_23_023149_add_tanda_pengenal_to_consumers_table	1
26	2025_12_23_120000_create_addon_transactions_table	1
27	2025_12_23_120100_create_addon_transaction_details_table	1
28	2025_12_24_000000_add_fasilitas_to_rooms_table	1
29	2025_12_24_024759_create_purchases_table	1
30	2025_12_24_035000_add_file_path_to_purchases_table	1
31	2025_12_24_095258_add_fasilitas_to_rooms_table	1
32	2025_12_24_120000_add_soft_deletes_to_purchases_table	1
33	2025_12_24_121500_add_audit_columns_to_purchases_table	1
34	2026_01_07_100000_add_emergency_contact_to_consumers_table	1
35	2026_01_07_110000_create_employees_table	1
36	2026_01_07_120000_create_payrolls_table	1
37	2026_01_07_150448_add_slip_number_to_payrolls_table	2
38	2026_01_07_180141_change_purchase_date_to_datetime_in_purchases_table	3
39	2026_01_09_000000_add_tipe_harga_to_room_occupancies_table	4
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.payments (id, billing_id, tanggal_bayar, jumlah, metode, bukti_bayar, created_at, updated_at) FROM stdin;
1	1	2026-01-07 00:00:00	935500.00	tunai	bayar kost bram | file:payments/708d938a-7402-4aa2-af1a-ba5e7ee34277.jpeg	2026-01-07 07:57:42	2026-01-07 07:57:42
2	2	2026-01-07 00:00:00	935500.00	qris	payments/f33dc12c-515f-4a73-b022-1bddeae51ce5.png	2026-01-07 15:38:51	2026-01-07 15:38:51
3	3	2026-01-07 00:00:00	935500.00	qris	lunas | file:payments/fe5b5669-066c-4750-9942-0b253f524142.png	2026-01-07 18:11:33	2026-01-07 18:11:33
4	4	2026-01-09 00:00:00	964300.00	tunai	\N	2026-01-09 10:05:19	2026-01-09 10:05:19
5	5	2026-01-09 00:00:00	871000.00	qris	payments/69c63110-1db2-402c-a772-9a752713a79c.jpeg	2026-01-09 14:48:07	2026-01-09 14:48:07
6	7	2026-01-09 00:00:00	451600.00	tunai	\N	2026-01-09 15:08:44	2026-01-09 15:08:44
7	8	2026-01-09 00:00:00	300000.00	tunai	\N	2026-01-09 15:12:13	2026-01-09 15:12:13
8	9	2026-01-09 00:00:00	871000.00	tunai	\N	2026-01-09 15:13:18	2026-01-09 15:13:18
9	6	2026-01-09 00:00:00	129000.00	tunai	\N	2026-01-09 15:13:54	2026-01-09 15:13:54
10	10	2026-01-09 00:00:00	500000.00	qris	payments/520616ed-afc5-4a38-a421-b9f408032153.jpg	2026-01-09 15:16:48	2026-01-09 15:16:48
11	10	2026-01-09 00:00:00	10000.00	qris	payments/f734b884-4b43-4d9d-9e61-6997ac2b6f42.png	2026-01-09 15:20:05	2026-01-09 15:20:05
12	10	2026-01-09 00:00:00	5000.00	tunai	\N	2026-01-09 15:22:52	2026-01-09 15:22:52
13	10	2026-01-09 00:00:00	15000.00	transfer	payments/01880a2e-1cb4-4f3e-b118-eeffa12818b8.jpeg	2026-01-09 15:28:42	2026-01-09 15:28:42
\.


--
-- Data for Name: payrolls; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.payrolls (id, employee_id, bulan, tahun, gaji_pokok, bonus, potongan, total_gaji, tanggal_bayar, status, keterangan, file_path, created_at, updated_at, slip_number) FROM stdin;
1	1	1	2026	4032300.00	0.00	0.00	4032300.00	2026-01-07 15:00:00	dibayar	gaji sandy periode januari 2025	payrolls/1767772720_695e123018f95_WhatsAppImage2026-01-06at130551.jpeg	2026-01-07 07:58:40	2026-01-07 15:00:29	\N
2	2	12	2025	1900000.00	0.00	0.00	1900000.00	2026-01-01 00:00:00	dibayar	\N	payrolls/1767775762_695e1e121297f_Gemini_Generated_Image_953ju953ju953ju9-removebg-preview.png	2026-01-07 15:49:22	2026-01-07 15:49:22	SLIP/2025/12/001
6	3	12	2025	1500000.00	0.00	0.00	1500000.00	2026-01-07 00:00:00	dibayar	\N	payrolls/1767783064_695e3a9815668_LogoAM.png	2026-01-07 17:51:04	2026-01-07 17:51:04	SLIP/2025/12/002
7	1	2	2026	5000000.00	0.00	0.00	5000000.00	2026-01-07 17:57:00	dibayar	\N	\N	2026-01-07 17:57:57	2026-01-07 17:58:08	SLIP/2026/02/001
8	4	12	2025	3000000.00	0.00	0.00	3000000.00	2026-01-07 18:07:00	dibayar	\N	payrolls/1767784059_695e3e7b8ba55_LogoAM.png	2026-01-07 18:07:39	2026-01-07 18:07:39	SLIP/2025/12/003
9	5	12	2025	3500000.00	0.00	0.00	3500000.00	2026-01-09 16:09:00	dibayar	\N	payrolls/1767949773_6960c5cdd26fe_Gemini_Generated_Image_953ju953ju953ju9.png	2026-01-09 16:09:34	2026-01-09 16:09:34	SLIP/2025/12/004
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: purchases; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.purchases (id, kost_id, description, category, amount, purchase_date, notes, created_at, updated_at, file_path, deleted_at, created_by, updated_by, deleted_by) FROM stdin;
1	1	perbaikan kloset kamar B100	perbaikan	235500.00	2026-01-07 00:00:00	jasa tukang dan material	2026-01-07 15:41:19	2026-01-07 15:41:19	purchases/6cfb1aa3-79ac-422d-8380-bdaf29f97af4.png	\N	2	\N	\N
\.


--
-- Data for Name: role_permissions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.role_permissions (id, role_id, menu_code, can_view, can_create, can_update, can_delete, created_at, updated_at) FROM stdin;
20	2	dashboard	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
21	2	master_data	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
22	2	kost	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
23	2	rooms	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
24	2	addons	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
25	2	consumers	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
26	2	transaksi	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
27	2	occupancies	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
28	2	billings	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
29	2	payments	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
30	2	addon_transactions	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
31	2	laporan	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
32	2	reports_occupancy	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
33	2	reports_finance	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
34	2	manajemen	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
35	2	users	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
36	2	roles	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
37	2	role_permissions	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
38	2	settings	t	t	t	t	2026-01-07 07:39:55	2026-01-07 07:39:55
39	1	dashboard	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
40	1	master_data	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
41	1	kost	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
42	1	rooms	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
43	1	addons	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
44	1	employees	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
45	1	consumers	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
46	1	transaksi	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
47	1	occupancies	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
48	1	billings	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
49	1	payments	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
50	1	addon_transactions	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
51	1	purchases	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
52	1	payrolls	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
53	1	laporan	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
54	1	reports_occupancy	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
55	1	reports_finance	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
56	1	reports_revenue_daily	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
57	1	reports_revenue_monthly	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
58	1	reports_traffic	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
59	1	manajemen	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
60	1	users	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
61	1	roles	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
62	1	role_permissions	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
63	1	settings	t	t	t	t	2026-01-07 07:46:44	2026-01-07 07:46:44
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, name, created_at, updated_at) FROM stdin;
1	Owner	\N	\N
2	Admin	\N	\N
\.


--
-- Data for Name: room_addon_maps; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.room_addon_maps (id, room_id, addon_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: room_addons; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.room_addons (id, nama_addon, harga, satuan, created_at, updated_at) FROM stdin;
1	snack	5000.00	pcs	2026-01-07 07:54:57	2026-01-07 07:54:57
\.


--
-- Data for Name: room_occupancies; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.room_occupancies (id, room_id, consumer_id, tanggal_masuk, tanggal_keluar, status, created_at, updated_at, tipe_harga) FROM stdin;
2	55	2	2026-01-07	2026-02-05	aktif	2026-01-07 15:38:17	2026-01-07 15:38:17	bulanan
3	54	3	2026-01-07	2026-02-05	aktif	2026-01-07 18:10:59	2026-01-07 18:10:59	bulanan
1	2	1	2026-01-07	2026-02-05	tidak aktif	2026-01-07 07:56:52	2026-01-09 10:02:38	bulanan
4	2	1	2026-02-06	2026-03-05	aktif	2026-01-09 10:02:38	2026-01-09 10:02:38	bulanan
5	1	4	2026-01-09	2026-02-05	aktif	2026-01-09 14:47:23	2026-01-09 14:47:23	bulanan
7	4	6	2026-01-09	2026-01-23	tidak aktif	2026-01-09 15:01:02	2026-01-09 15:08:58	bulanan
8	3	6	2026-01-09	2026-01-12	tidak aktif	2026-01-09 15:09:22	2026-01-09 15:12:24	harian
9	25	6	2026-01-09	2026-02-05	tidak aktif	2026-01-09 15:12:54	2026-01-09 15:13:35	bulanan
6	8	5	2026-01-09	2026-01-13	tidak aktif	2026-01-09 14:51:11	2026-01-09 15:14:06	bulanan
10	3	7	2026-01-09	2026-01-14	aktif	2026-01-09 15:16:25	2026-01-09 15:16:25	harian
\.


--
-- Data for Name: rooms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rooms (id, kost_id, nomor_kamar, jenis_kamar, harga, status, created_at, updated_at, tipe_harga, harga_harian, fasilitas) FROM stdin;
5	1	1E	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
6	1	1F	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
7	1	1G	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
9	1	1I	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
10	1	1J	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
11	1	1K	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
12	1	1L	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
13	1	1M	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
14	1	1N	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
15	1	1O	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
16	1	1P	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
17	1	1Q	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
18	1	1R	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
19	1	1S	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
20	1	1T	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
21	1	1U	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
22	1	1V	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
23	1	1W	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
24	1	1X	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
26	1	1Z	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
27	1	2A	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
28	1	2B	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
29	1	2C	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
30	1	2D	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
31	1	2E	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
32	1	2F	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
33	1	2G	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
34	1	2H	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
35	1	2I	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
36	1	2J	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
37	1	2K	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
38	1	2L	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
39	1	2M	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
40	1	2N	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
41	1	2O	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
42	1	2P	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
43	1	2Q	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
44	1	2R	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
45	1	2S	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
46	1	2T	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
47	1	2U	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
48	1	2V	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
49	1	2W	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
50	1	2X	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
51	1	2Y	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
52	1	2Z	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
53	1	3 A	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-07 07:47:59	bulanan	100000	AC, TV
2	1	1B	single	1000000	terisi	2026-01-07 07:47:59	2026-01-07 07:56:52	bulanan	100000	AC, TV
55	1	4 C	single	1000000	terisi	2026-01-07 07:47:59	2026-01-07 15:38:17	bulanan	100000	AC, TV
54	1	3 B	single	1000000	terisi	2026-01-07 07:47:59	2026-01-07 18:10:59	bulanan	100000	AC, TV
1	1	1A	single	1000000	terisi	2026-01-07 07:47:59	2026-01-09 14:47:23	bulanan	100000	AC, TV
3	1	1C	single	1000000	terisi	2026-01-07 07:47:59	2026-01-09 15:16:25	bulanan	100000	AC, TV
4	1	1D	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-09 15:08:58	bulanan	100000	AC, TV
25	1	1Y	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-09 15:13:35	bulanan	100000	AC, TV
8	1	1H	single	1000000	tersedia	2026-01-07 07:47:59	2026-01-09 15:14:06	bulanan	100000	AC, TV
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role_id) FROM stdin;
1	Admin Sistem	admin@kost.app	\N	$2y$12$4t5FBYNu0pRasH2hSlaeM.CWm2yyURg7qKHQ9/fBkX9Ml0BkQ820q	\N	2026-01-07 07:39:55	2026-01-07 07:39:55	2
2	Owner Sistem	owner@kost.app	\N	$2y$12$fEcAnO7168aN9LE.j9GSGOZwrBUqHxidAAXmxpwrIyG83nT0tIOUO	\N	2026-01-07 07:39:55	2026-01-07 07:39:55	1
3	surya	surya@kost.app	\N	$2y$12$EBrXL5/2NKIu4On6mACUbe.s4lF26QSWlvbMDq5tuvq0rqh5jfBgi	\N	2026-01-07 18:09:40	2026-01-07 18:09:40	2
\.


--
-- Name: addon_transaction_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.addon_transaction_details_id_seq', 1, false);


--
-- Name: addon_transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.addon_transactions_id_seq', 1, false);


--
-- Name: billing_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.billing_details_id_seq', 13, true);


--
-- Name: billing_reminders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.billing_reminders_id_seq', 1, false);


--
-- Name: billings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.billings_id_seq', 10, true);


--
-- Name: consumers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.consumers_id_seq', 7, true);


--
-- Name: employees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.employees_id_seq', 5, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: kosts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kosts_id_seq', 1, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 39, true);


--
-- Name: payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.payments_id_seq', 13, true);


--
-- Name: payrolls_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.payrolls_id_seq', 9, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: purchases_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.purchases_id_seq', 1, true);


--
-- Name: role_permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.role_permissions_id_seq', 63, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 2, true);


--
-- Name: room_addon_maps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.room_addon_maps_id_seq', 1, false);


--
-- Name: room_addons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.room_addons_id_seq', 1, true);


--
-- Name: room_occupancies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.room_occupancies_id_seq', 10, true);


--
-- Name: rooms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rooms_id_seq', 55, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- Name: addon_transaction_details addon_transaction_details_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transaction_details
    ADD CONSTRAINT addon_transaction_details_pkey PRIMARY KEY (id);


--
-- Name: addon_transactions addon_transactions_invoice_number_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transactions
    ADD CONSTRAINT addon_transactions_invoice_number_unique UNIQUE (invoice_number);


--
-- Name: addon_transactions addon_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transactions
    ADD CONSTRAINT addon_transactions_pkey PRIMARY KEY (id);


--
-- Name: billing_details billing_details_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billing_details
    ADD CONSTRAINT billing_details_pkey PRIMARY KEY (id);


--
-- Name: billing_reminders billing_reminders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billing_reminders
    ADD CONSTRAINT billing_reminders_pkey PRIMARY KEY (id);


--
-- Name: billings billings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billings
    ADD CONSTRAINT billings_pkey PRIMARY KEY (id);


--
-- Name: consumers consumers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consumers
    ADD CONSTRAINT consumers_pkey PRIMARY KEY (id);


--
-- Name: employees employees_nik_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_nik_unique UNIQUE (nik);


--
-- Name: employees employees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: kosts kosts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kosts
    ADD CONSTRAINT kosts_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- Name: payrolls payrolls_employee_id_bulan_tahun_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payrolls
    ADD CONSTRAINT payrolls_employee_id_bulan_tahun_unique UNIQUE (employee_id, bulan, tahun);


--
-- Name: payrolls payrolls_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payrolls
    ADD CONSTRAINT payrolls_pkey PRIMARY KEY (id);


--
-- Name: payrolls payrolls_slip_number_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payrolls
    ADD CONSTRAINT payrolls_slip_number_unique UNIQUE (slip_number);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: purchases purchases_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_pkey PRIMARY KEY (id);


--
-- Name: role_permissions role_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_pkey PRIMARY KEY (id);


--
-- Name: role_permissions role_permissions_role_id_menu_code_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_role_id_menu_code_unique UNIQUE (role_id, menu_code);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: room_addon_maps room_addon_maps_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_addon_maps
    ADD CONSTRAINT room_addon_maps_pkey PRIMARY KEY (id);


--
-- Name: room_addons room_addons_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_addons
    ADD CONSTRAINT room_addons_pkey PRIMARY KEY (id);


--
-- Name: room_occupancies room_occupancies_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_occupancies
    ADD CONSTRAINT room_occupancies_pkey PRIMARY KEY (id);


--
-- Name: rooms rooms_nomor_kamar_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_nomor_kamar_unique UNIQUE (nomor_kamar);


--
-- Name: rooms rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: addon_transaction_details addon_transaction_details_addon_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transaction_details
    ADD CONSTRAINT addon_transaction_details_addon_id_foreign FOREIGN KEY (addon_id) REFERENCES public.room_addons(id) ON DELETE SET NULL;


--
-- Name: addon_transaction_details addon_transaction_details_addon_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transaction_details
    ADD CONSTRAINT addon_transaction_details_addon_transaction_id_foreign FOREIGN KEY (addon_transaction_id) REFERENCES public.addon_transactions(id) ON DELETE CASCADE;


--
-- Name: addon_transactions addon_transactions_consumer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transactions
    ADD CONSTRAINT addon_transactions_consumer_id_foreign FOREIGN KEY (consumer_id) REFERENCES public.consumers(id) ON DELETE CASCADE;


--
-- Name: addon_transactions addon_transactions_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.addon_transactions
    ADD CONSTRAINT addon_transactions_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id) ON DELETE SET NULL;


--
-- Name: billing_details billing_details_billing_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billing_details
    ADD CONSTRAINT billing_details_billing_id_foreign FOREIGN KEY (billing_id) REFERENCES public.billings(id) ON DELETE CASCADE;


--
-- Name: billing_reminders billing_reminders_billing_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billing_reminders
    ADD CONSTRAINT billing_reminders_billing_id_foreign FOREIGN KEY (billing_id) REFERENCES public.billings(id) ON DELETE CASCADE;


--
-- Name: billings billings_consumer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billings
    ADD CONSTRAINT billings_consumer_id_foreign FOREIGN KEY (consumer_id) REFERENCES public.consumers(id);


--
-- Name: billings billings_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.billings
    ADD CONSTRAINT billings_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id);


--
-- Name: payments payments_billing_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_billing_id_foreign FOREIGN KEY (billing_id) REFERENCES public.billings(id) ON DELETE CASCADE;


--
-- Name: payrolls payrolls_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payrolls
    ADD CONSTRAINT payrolls_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE CASCADE;


--
-- Name: purchases purchases_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: purchases purchases_deleted_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_deleted_by_foreign FOREIGN KEY (deleted_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: purchases purchases_kost_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_kost_id_foreign FOREIGN KEY (kost_id) REFERENCES public.kosts(id) ON DELETE CASCADE;


--
-- Name: purchases purchases_updated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_updated_by_foreign FOREIGN KEY (updated_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: role_permissions role_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_permissions
    ADD CONSTRAINT role_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: room_addon_maps room_addon_maps_addon_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_addon_maps
    ADD CONSTRAINT room_addon_maps_addon_id_foreign FOREIGN KEY (addon_id) REFERENCES public.room_addons(id) ON DELETE CASCADE;


--
-- Name: room_addon_maps room_addon_maps_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_addon_maps
    ADD CONSTRAINT room_addon_maps_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id) ON DELETE CASCADE;


--
-- Name: room_occupancies room_occupancies_consumer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_occupancies
    ADD CONSTRAINT room_occupancies_consumer_id_foreign FOREIGN KEY (consumer_id) REFERENCES public.consumers(id);


--
-- Name: room_occupancies room_occupancies_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.room_occupancies
    ADD CONSTRAINT room_occupancies_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id);


--
-- Name: rooms rooms_kost_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_kost_id_foreign FOREIGN KEY (kost_id) REFERENCES public.kosts(id) ON DELETE CASCADE;


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;


--
-- PostgreSQL database dump complete
--

