--
-- PostgreSQL database dump
--

\restrict zLIbvSB9RHhEsaFaQ8QRgxNrppkPcJzFenBh8jtbAphjiMMFKplmePIT9Mh8m3K

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: caja; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.caja (
    id_caja integer NOT NULL,
    numero_caja integer NOT NULL,
    estado character varying(150) DEFAULT 'Activa'::character varying,
    id_sucursal integer NOT NULL
);


--
-- Name: caja_id_caja_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.caja_id_caja_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: caja_id_caja_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.caja_id_caja_seq OWNED BY public.caja.id_caja;


--
-- Name: categoria; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categoria (
    id_categoria integer NOT NULL,
    nombre character varying(150) NOT NULL,
    estado boolean DEFAULT true,
    id_sucursal integer
);


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categoria_id_categoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categoria_id_categoria_seq OWNED BY public.categoria.id_categoria;


--
-- Name: compra; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.compra (
    id_compra bigint NOT NULL,
    fecha timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    id_producto bigint NOT NULL,
    id_sucursal bigint NOT NULL,
    id_usuario bigint NOT NULL,
    cantidad integer NOT NULL,
    costo_unitario numeric(10,2) NOT NULL,
    total numeric(10,2) NOT NULL,
    proveedor character varying(150)
);


--
-- Name: compra_id_compra_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.compra_id_compra_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: compra_id_compra_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.compra_id_compra_seq OWNED BY public.compra.id_compra;


--
-- Name: corte_caja; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.corte_caja (
    id_corte integer NOT NULL,
    fecha_apertura timestamp without time zone NOT NULL,
    fecha_cierre timestamp without time zone,
    monto_inicial numeric(10,2) NOT NULL,
    monto_final numeric(10,2),
    id_caja integer NOT NULL,
    id_usuario integer NOT NULL
);


--
-- Name: corte_caja_id_corte_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.corte_caja_id_corte_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: corte_caja_id_corte_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.corte_caja_id_corte_seq OWNED BY public.corte_caja.id_corte;


--
-- Name: detalle_venta; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.detalle_venta (
    id_detalle integer NOT NULL,
    id_venta integer NOT NULL,
    id_producto integer NOT NULL,
    cantidad integer NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    subtotal numeric(10,2) NOT NULL
);


--
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.detalle_venta_id_detalle_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.detalle_venta_id_detalle_seq OWNED BY public.detalle_venta.id_detalle;


--
-- Name: devolucion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.devolucion (
    id_devolucion bigint NOT NULL,
    fecha timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    id_venta bigint NOT NULL,
    id_detalle bigint NOT NULL,
    id_producto bigint NOT NULL,
    cantidad integer NOT NULL,
    monto numeric(10,2) NOT NULL,
    tipo character varying(20) NOT NULL,
    motivo character varying(255) NOT NULL,
    id_usuario bigint NOT NULL
);


--
-- Name: devolucion_id_devolucion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.devolucion_id_devolucion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: devolucion_id_devolucion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.devolucion_id_devolucion_seq OWNED BY public.devolucion.id_devolucion;


--
-- Name: inventario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.inventario (
    id_inventario integer NOT NULL,
    id_sucursal integer NOT NULL,
    id_producto integer NOT NULL,
    existencias integer DEFAULT 0 NOT NULL,
    stock_minimo integer DEFAULT 5 NOT NULL,
    fechas_actualizacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    precio numeric(10,2),
    iva numeric(5,2),
    estado boolean DEFAULT true NOT NULL,
    id_categoria integer
);


--
-- Name: inventario_id_inventario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.inventario_id_inventario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inventario_id_inventario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.inventario_id_inventario_seq OWNED BY public.inventario.id_inventario;


--
-- Name: metodo_pago; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.metodo_pago (
    id_metodo_pago integer NOT NULL,
    nombre character varying(150) NOT NULL
);


--
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.metodo_pago_id_metodo_pago_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.metodo_pago_id_metodo_pago_seq OWNED BY public.metodo_pago.id_metodo_pago;


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
-- Name: negocio; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.negocio (
    id_negocio bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    id_propietario bigint,
    estado character varying(20) DEFAULT 'activo'::character varying NOT NULL,
    fecha_creacion timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: negocio_id_negocio_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.negocio_id_negocio_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: negocio_id_negocio_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.negocio_id_negocio_seq OWNED BY public.negocio.id_negocio;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: producto; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.producto (
    id_producto integer NOT NULL,
    nombre character varying(150) NOT NULL,
    precio numeric(10,2) NOT NULL,
    codigo_barras character varying(150),
    estado boolean DEFAULT true,
    id_categoria integer,
    iva numeric(5,2) DEFAULT '16'::numeric NOT NULL,
    foto character varying(255),
    id_negocio bigint
);


--
-- Name: producto_id_producto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.producto_id_producto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: producto_id_producto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.producto_id_producto_seq OWNED BY public.producto.id_producto;


--
-- Name: reporte; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reporte (
    id_reporte bigint NOT NULL,
    fecha timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    id_negocio bigint NOT NULL,
    id_sucursal integer,
    id_usuario integer,
    tipo character varying(20) NOT NULL,
    asunto character varying(120) NOT NULL,
    descripcion text NOT NULL,
    estado character varying(20) DEFAULT 'pendiente'::character varying NOT NULL,
    respuesta character varying(500),
    fecha_respuesta timestamp(0) without time zone,
    id_usuario_responde integer
);


--
-- Name: reporte_id_reporte_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reporte_id_reporte_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reporte_id_reporte_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reporte_id_reporte_seq OWNED BY public.reporte.id_reporte;


--
-- Name: rol; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rol (
    id_rol integer NOT NULL,
    nombre character varying(150) NOT NULL
);


--
-- Name: rol_id_rol_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rol_id_rol_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rol_id_rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rol_id_rol_seq OWNED BY public.rol.id_rol;


--
-- Name: sucursal; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sucursal (
    id_sucursal integer NOT NULL,
    nombre character varying(150) NOT NULL,
    direccion text NOT NULL,
    contacto character varying(150),
    estado character varying(30) DEFAULT 'Activa'::character varying,
    id_gerente integer,
    id_negocio bigint
);


--
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.sucursal_id_sucursal_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.sucursal_id_sucursal_seq OWNED BY public.sucursal.id_sucursal;


--
-- Name: user_invitations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_invitations (
    id bigint NOT NULL,
    user_id integer NOT NULL,
    invited_by integer NOT NULL,
    token_hash character varying(255) NOT NULL,
    expires_at timestamp(0) without time zone NOT NULL,
    sent_at timestamp(0) without time zone,
    viewed_at timestamp(0) without time zone,
    accepted_at timestamp(0) without time zone,
    accepted_by integer,
    status character varying(20) DEFAULT 'sent'::character varying NOT NULL,
    terms_version character varying(50),
    privacy_version character varying(50),
    accepted_ip inet,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_invitations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_invitations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_invitations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_invitations_id_seq OWNED BY public.user_invitations.id;


--
-- Name: usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuario (
    id_usuario integer NOT NULL,
    nombre character varying(150) NOT NULL,
    apellido character varying(150) NOT NULL,
    correo character varying(150) NOT NULL,
    contrasena character varying(255) NOT NULL,
    id_rol integer NOT NULL,
    id_sucursal integer,
    remember_token character varying(100),
    idioma character varying(5) DEFAULT 'es'::character varying NOT NULL,
    fecha_nacimiento date,
    email_verified_at timestamp(0) without time zone,
    email_verification_code character varying(255),
    email_verification_expires_at timestamp(0) without time zone,
    id_negocio bigint,
    dos_pasos_activo boolean DEFAULT false NOT NULL,
    dos_pasos_codigo character varying(255),
    dos_pasos_expira timestamp(0) without time zone
);


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuario_id_usuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuario_id_usuario_seq OWNED BY public.usuario.id_usuario;


--
-- Name: venta; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.venta (
    id_venta integer NOT NULL,
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    subtotal numeric(10,2) NOT NULL,
    total numeric(10,2) NOT NULL,
    id_sucursal integer NOT NULL,
    id_cajero integer NOT NULL,
    id_metodo_pago integer NOT NULL,
    id_caja integer NOT NULL,
    estado character varying(20) DEFAULT 'completada'::character varying NOT NULL,
    fecha_cancelacion timestamp(0) without time zone,
    id_usuario_cancela bigint,
    motivo_cancelacion character varying(255)
);


--
-- Name: venta_id_venta_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.venta_id_venta_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: venta_id_venta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.venta_id_venta_seq OWNED BY public.venta.id_venta;


--
-- Name: caja id_caja; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.caja ALTER COLUMN id_caja SET DEFAULT nextval('public.caja_id_caja_seq'::regclass);


--
-- Name: categoria id_categoria; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria ALTER COLUMN id_categoria SET DEFAULT nextval('public.categoria_id_categoria_seq'::regclass);


--
-- Name: compra id_compra; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra ALTER COLUMN id_compra SET DEFAULT nextval('public.compra_id_compra_seq'::regclass);


--
-- Name: corte_caja id_corte; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.corte_caja ALTER COLUMN id_corte SET DEFAULT nextval('public.corte_caja_id_corte_seq'::regclass);


--
-- Name: detalle_venta id_detalle; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detalle_venta ALTER COLUMN id_detalle SET DEFAULT nextval('public.detalle_venta_id_detalle_seq'::regclass);


--
-- Name: devolucion id_devolucion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.devolucion ALTER COLUMN id_devolucion SET DEFAULT nextval('public.devolucion_id_devolucion_seq'::regclass);


--
-- Name: inventario id_inventario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventario ALTER COLUMN id_inventario SET DEFAULT nextval('public.inventario_id_inventario_seq'::regclass);


--
-- Name: metodo_pago id_metodo_pago; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.metodo_pago ALTER COLUMN id_metodo_pago SET DEFAULT nextval('public.metodo_pago_id_metodo_pago_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: negocio id_negocio; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.negocio ALTER COLUMN id_negocio SET DEFAULT nextval('public.negocio_id_negocio_seq'::regclass);


--
-- Name: producto id_producto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto ALTER COLUMN id_producto SET DEFAULT nextval('public.producto_id_producto_seq'::regclass);


--
-- Name: reporte id_reporte; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reporte ALTER COLUMN id_reporte SET DEFAULT nextval('public.reporte_id_reporte_seq'::regclass);


--
-- Name: rol id_rol; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol ALTER COLUMN id_rol SET DEFAULT nextval('public.rol_id_rol_seq'::regclass);


--
-- Name: sucursal id_sucursal; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sucursal ALTER COLUMN id_sucursal SET DEFAULT nextval('public.sucursal_id_sucursal_seq'::regclass);


--
-- Name: user_invitations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_invitations ALTER COLUMN id SET DEFAULT nextval('public.user_invitations_id_seq'::regclass);


--
-- Name: usuario id_usuario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario ALTER COLUMN id_usuario SET DEFAULT nextval('public.usuario_id_usuario_seq'::regclass);


--
-- Name: venta id_venta; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta ALTER COLUMN id_venta SET DEFAULT nextval('public.venta_id_venta_seq'::regclass);


--
-- Data for Name: caja; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.caja (id_caja, numero_caja, estado, id_sucursal) FROM stdin;
\.


--
-- Data for Name: categoria; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categoria (id_categoria, nombre, estado, id_sucursal) FROM stdin;
\.


--
-- Data for Name: compra; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.compra (id_compra, fecha, id_producto, id_sucursal, id_usuario, cantidad, costo_unitario, total, proveedor) FROM stdin;
\.


--
-- Data for Name: corte_caja; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.corte_caja (id_corte, fecha_apertura, fecha_cierre, monto_inicial, monto_final, id_caja, id_usuario) FROM stdin;
\.


--
-- Data for Name: detalle_venta; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.detalle_venta (id_detalle, id_venta, id_producto, cantidad, precio_unitario, subtotal) FROM stdin;
\.


--
-- Data for Name: devolucion; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.devolucion (id_devolucion, fecha, id_venta, id_detalle, id_producto, cantidad, monto, tipo, motivo, id_usuario) FROM stdin;
\.


--
-- Data for Name: inventario; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.inventario (id_inventario, id_sucursal, id_producto, existencias, stock_minimo, fechas_actualizacion, precio, iva, estado, id_categoria) FROM stdin;
\.


--
-- Data for Name: metodo_pago; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.metodo_pago (id_metodo_pago, nombre) FROM stdin;
1	Efectivo
2	Tarjeta
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2026_09_30_000001_add_idioma_to_usuario_table	1
2	2026_09_30_000002_add_iva_to_producto_table	1
3	2026_09_30_000003_create_compra_table	2
4	2026_09_30_000004_add_activo_to_producto_table	2
5	2026_09_30_000005_make_id_categoria_nullable_in_producto_table	3
6	2026_09_29_000000_add_fecha_nacimiento_to_usuario_table	4
7	2026_10_01_000006_drop_activo_from_producto_table	4
8	2026_10_01_000007_insert_metodos_pago_basicos	4
9	2026_10_01_000008_add_precio_iva_estado_to_inventario_table	4
10	2026_10_01_000009_add_cancelacion_to_venta_table	4
11	2026_10_01_000010_create_devolucion_table	4
12	2026_10_11_000000_create_password_reset_tokens_table	5
13	2026_10_11_000001_add_remember_token_to_usuario_table	5
14	2026_10_20_000000_add_foto_to_producto_table	6
15	2026_10_28_000000_add_categorias_por_sucursal	7
16	2026_10_28_000000_add_email_verification_to_usuario_table	8
17	2026_11_08_000000_create_negocio_table	9
18	2026_11_08_000000_create_user_invitations_table	9
19	2026_11_08_000001_add_unique_producto_sucursal_to_inventario	9
20	2026_11_08_000001_insert_default_roles	9
21	2026_11_08_000002_create_reporte_table	9
22	2026_11_15_000000_add_dos_pasos_to_usuario_table	10
\.


--
-- Data for Name: negocio; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.negocio (id_negocio, nombre, id_propietario, estado, fecha_creacion) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: producto; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.producto (id_producto, nombre, precio, codigo_barras, estado, id_categoria, iva, foto, id_negocio) FROM stdin;
\.


--
-- Data for Name: reporte; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.reporte (id_reporte, fecha, id_negocio, id_sucursal, id_usuario, tipo, asunto, descripcion, estado, respuesta, fecha_respuesta, id_usuario_responde) FROM stdin;
\.


--
-- Data for Name: rol; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.rol (id_rol, nombre) FROM stdin;
1	Administrador
2	Gerente
3	Cajero
\.


--
-- Data for Name: sucursal; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sucursal (id_sucursal, nombre, direccion, contacto, estado, id_gerente, id_negocio) FROM stdin;
\.


--
-- Data for Name: user_invitations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_invitations (id, user_id, invited_by, token_hash, expires_at, sent_at, viewed_at, accepted_at, accepted_by, status, terms_version, privacy_version, accepted_ip, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.usuario (id_usuario, nombre, apellido, correo, contrasena, id_rol, id_sucursal, remember_token, idioma, fecha_nacimiento, email_verified_at, email_verification_code, email_verification_expires_at, id_negocio, dos_pasos_activo, dos_pasos_codigo, dos_pasos_expira) FROM stdin;
\.


--
-- Data for Name: venta; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.venta (id_venta, fecha_hora, subtotal, total, id_sucursal, id_cajero, id_metodo_pago, id_caja, estado, fecha_cancelacion, id_usuario_cancela, motivo_cancelacion) FROM stdin;
\.


--
-- Name: caja_id_caja_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.caja_id_caja_seq', 1, false);


--
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categoria_id_categoria_seq', 1, false);


--
-- Name: compra_id_compra_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.compra_id_compra_seq', 1, false);


--
-- Name: corte_caja_id_corte_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.corte_caja_id_corte_seq', 1, false);


--
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.detalle_venta_id_detalle_seq', 1, false);


--
-- Name: devolucion_id_devolucion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.devolucion_id_devolucion_seq', 1, false);


--
-- Name: inventario_id_inventario_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.inventario_id_inventario_seq', 1, false);


--
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.metodo_pago_id_metodo_pago_seq', 2, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 22, true);


--
-- Name: negocio_id_negocio_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.negocio_id_negocio_seq', 1, false);


--
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.producto_id_producto_seq', 1, false);


--
-- Name: reporte_id_reporte_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.reporte_id_reporte_seq', 1, false);


--
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.rol_id_rol_seq', 3, true);


--
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.sucursal_id_sucursal_seq', 1, false);


--
-- Name: user_invitations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_invitations_id_seq', 1, false);


--
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuario_id_usuario_seq', 1, false);


--
-- Name: venta_id_venta_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.venta_id_venta_seq', 1, false);


--
-- Name: caja caja_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.caja
    ADD CONSTRAINT caja_pkey PRIMARY KEY (id_caja);


--
-- Name: categoria categoria_id_sucursal_uk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_id_sucursal_uk UNIQUE (id_categoria, id_sucursal);


--
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id_categoria);


--
-- Name: compra compra_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT compra_pkey PRIMARY KEY (id_compra);


--
-- Name: corte_caja corte_caja_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.corte_caja
    ADD CONSTRAINT corte_caja_pkey PRIMARY KEY (id_corte);


--
-- Name: detalle_venta detalle_venta_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detalle_venta
    ADD CONSTRAINT detalle_venta_pkey PRIMARY KEY (id_detalle);


--
-- Name: devolucion devolucion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.devolucion
    ADD CONSTRAINT devolucion_pkey PRIMARY KEY (id_devolucion);


--
-- Name: inventario inventario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_pkey PRIMARY KEY (id_inventario);


--
-- Name: metodo_pago metodo_pago_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.metodo_pago
    ADD CONSTRAINT metodo_pago_pkey PRIMARY KEY (id_metodo_pago);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: negocio negocio_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.negocio
    ADD CONSTRAINT negocio_pkey PRIMARY KEY (id_negocio);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: producto producto_codigo_barras_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_codigo_barras_key UNIQUE (codigo_barras);


--
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- Name: reporte reporte_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reporte
    ADD CONSTRAINT reporte_pkey PRIMARY KEY (id_reporte);


--
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- Name: sucursal sucursal_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sucursal
    ADD CONSTRAINT sucursal_pkey PRIMARY KEY (id_sucursal);


--
-- Name: user_invitations user_invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_invitations
    ADD CONSTRAINT user_invitations_pkey PRIMARY KEY (id);


--
-- Name: user_invitations user_invitations_token_hash_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_invitations
    ADD CONSTRAINT user_invitations_token_hash_unique UNIQUE (token_hash);


--
-- Name: usuario usuario_correo_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_correo_key UNIQUE (correo);


--
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- Name: venta venta_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta
    ADD CONSTRAINT venta_pkey PRIMARY KEY (id_venta);


--
-- Name: categoria_sucursal_nombre_unico; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX categoria_sucursal_nombre_unico ON public.categoria USING btree (id_sucursal, lower(btrim((nombre)::text)));


--
-- Name: inventario_id_categoria_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX inventario_id_categoria_index ON public.inventario USING btree (id_categoria);


--
-- Name: inventario_producto_sucursal_unico; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX inventario_producto_sucursal_unico ON public.inventario USING btree (id_producto, id_sucursal);


--
-- Name: reporte_id_negocio_estado_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reporte_id_negocio_estado_index ON public.reporte USING btree (id_negocio, estado);


--
-- Name: user_invitations_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_invitations_expires_at_index ON public.user_invitations USING btree (expires_at);


--
-- Name: user_invitations_user_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_invitations_user_id_status_index ON public.user_invitations USING btree (user_id, status);


--
-- Name: caja caja_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.caja
    ADD CONSTRAINT caja_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal);


--
-- Name: categoria categoria_id_sucursal_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_id_sucursal_foreign FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal);


--
-- Name: compra compra_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT compra_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: compra compra_id_sucursal_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT compra_id_sucursal_foreign FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal);


--
-- Name: compra compra_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.compra
    ADD CONSTRAINT compra_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: corte_caja corte_caja_id_caja_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.corte_caja
    ADD CONSTRAINT corte_caja_id_caja_fkey FOREIGN KEY (id_caja) REFERENCES public.caja(id_caja);


--
-- Name: corte_caja corte_caja_id_usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.corte_caja
    ADD CONSTRAINT corte_caja_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: detalle_venta detalle_venta_id_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detalle_venta
    ADD CONSTRAINT detalle_venta_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: detalle_venta detalle_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.detalle_venta
    ADD CONSTRAINT detalle_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES public.venta(id_venta);


--
-- Name: devolucion devolucion_id_detalle_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.devolucion
    ADD CONSTRAINT devolucion_id_detalle_foreign FOREIGN KEY (id_detalle) REFERENCES public.detalle_venta(id_detalle);


--
-- Name: devolucion devolucion_id_producto_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.devolucion
    ADD CONSTRAINT devolucion_id_producto_foreign FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: devolucion devolucion_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.devolucion
    ADD CONSTRAINT devolucion_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario);


--
-- Name: devolucion devolucion_id_venta_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.devolucion
    ADD CONSTRAINT devolucion_id_venta_foreign FOREIGN KEY (id_venta) REFERENCES public.venta(id_venta);


--
-- Name: sucursal fk_sucursal_gerente; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sucursal
    ADD CONSTRAINT fk_sucursal_gerente FOREIGN KEY (id_gerente) REFERENCES public.usuario(id_usuario);


--
-- Name: inventario inventario_categoria_sucursal_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_categoria_sucursal_fk FOREIGN KEY (id_categoria, id_sucursal) REFERENCES public.categoria(id_categoria, id_sucursal);


--
-- Name: inventario inventario_id_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES public.producto(id_producto);


--
-- Name: inventario inventario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal);


--
-- Name: negocio negocio_id_propietario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.negocio
    ADD CONSTRAINT negocio_id_propietario_foreign FOREIGN KEY (id_propietario) REFERENCES public.usuario(id_usuario);


--
-- Name: producto producto_id_categoria_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_id_categoria_fkey FOREIGN KEY (id_categoria) REFERENCES public.categoria(id_categoria);


--
-- Name: producto producto_id_negocio_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_id_negocio_foreign FOREIGN KEY (id_negocio) REFERENCES public.negocio(id_negocio);


--
-- Name: reporte reporte_id_negocio_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reporte
    ADD CONSTRAINT reporte_id_negocio_foreign FOREIGN KEY (id_negocio) REFERENCES public.negocio(id_negocio);


--
-- Name: reporte reporte_id_sucursal_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reporte
    ADD CONSTRAINT reporte_id_sucursal_foreign FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal) ON DELETE SET NULL;


--
-- Name: reporte reporte_id_usuario_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reporte
    ADD CONSTRAINT reporte_id_usuario_foreign FOREIGN KEY (id_usuario) REFERENCES public.usuario(id_usuario) ON DELETE SET NULL;


--
-- Name: reporte reporte_id_usuario_responde_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reporte
    ADD CONSTRAINT reporte_id_usuario_responde_foreign FOREIGN KEY (id_usuario_responde) REFERENCES public.usuario(id_usuario) ON DELETE SET NULL;


--
-- Name: sucursal sucursal_id_negocio_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sucursal
    ADD CONSTRAINT sucursal_id_negocio_foreign FOREIGN KEY (id_negocio) REFERENCES public.negocio(id_negocio);


--
-- Name: usuario usuario_id_negocio_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_id_negocio_foreign FOREIGN KEY (id_negocio) REFERENCES public.negocio(id_negocio);


--
-- Name: usuario usuario_id_rol_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES public.rol(id_rol);


--
-- Name: usuario usuario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal);


--
-- Name: venta venta_id_caja_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta
    ADD CONSTRAINT venta_id_caja_fkey FOREIGN KEY (id_caja) REFERENCES public.caja(id_caja);


--
-- Name: venta venta_id_cajero_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta
    ADD CONSTRAINT venta_id_cajero_fkey FOREIGN KEY (id_cajero) REFERENCES public.usuario(id_usuario);


--
-- Name: venta venta_id_metodo_pago_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta
    ADD CONSTRAINT venta_id_metodo_pago_fkey FOREIGN KEY (id_metodo_pago) REFERENCES public.metodo_pago(id_metodo_pago);


--
-- Name: venta venta_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta
    ADD CONSTRAINT venta_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES public.sucursal(id_sucursal);


--
-- Name: venta venta_id_usuario_cancela_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta
    ADD CONSTRAINT venta_id_usuario_cancela_foreign FOREIGN KEY (id_usuario_cancela) REFERENCES public.usuario(id_usuario);


--
-- PostgreSQL database dump complete
--

\unrestrict zLIbvSB9RHhEsaFaQ8QRgxNrppkPcJzFenBh8jtbAphjiMMFKplmePIT9Mh8m3K

