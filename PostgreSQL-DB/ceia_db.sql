--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5

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
-- Name: auditoria_general; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.auditoria_general (
    id integer NOT NULL,
    tabla_afectada character varying(100) NOT NULL,
    registro_id integer NOT NULL,
    usuario_id integer NOT NULL,
    accion character varying(20),
    campos_modificados jsonb,
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ip_usuario inet,
    nota text,
    CONSTRAINT auditoria_general_accion_check CHECK (((accion)::text = ANY ((ARRAY['insertar'::character varying, 'actualizar'::character varying, 'eliminar'::character varying, 'reasignar'::character varying, 'registrar_tarde'::character varying])::text[])))
);


ALTER TABLE public.auditoria_general OWNER TO postgres;

--
-- Name: auditoria_general_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.auditoria_general_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.auditoria_general_id_seq OWNER TO postgres;

--
-- Name: auditoria_general_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.auditoria_general_id_seq OWNED BY public.auditoria_general.id;


--
-- Name: entrada_salida_profesores; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.entrada_salida_profesores (
    id integer NOT NULL,
    profesor_id integer,
    tipo_movimiento character varying(10),
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT entrada_salida_profesores_tipo_movimiento_check CHECK (((tipo_movimiento)::text = ANY ((ARRAY['Entrada'::character varying, 'Salida'::character varying])::text[])))
);


ALTER TABLE public.entrada_salida_profesores OWNER TO postgres;

--
-- Name: entrada_salida_profesores_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.entrada_salida_profesores_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.entrada_salida_profesores_id_seq OWNER TO postgres;

--
-- Name: entrada_salida_profesores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.entrada_salida_profesores_id_seq OWNED BY public.entrada_salida_profesores.id;


--
-- Name: estudiante_periodo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.estudiante_periodo (
    id integer NOT NULL,
    estudiante_id integer,
    periodo_id integer NOT NULL,
    grado_cursado character varying(50) NOT NULL
);


ALTER TABLE public.estudiante_periodo OWNER TO postgres;

--
-- Name: estudiante_periodo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.estudiante_periodo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.estudiante_periodo_id_seq OWNER TO postgres;

--
-- Name: estudiante_periodo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.estudiante_periodo_id_seq OWNED BY public.estudiante_periodo.id;


--
-- Name: estudiantes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.estudiantes (
    id integer NOT NULL,
    nombre_completo character varying(100) NOT NULL,
    fecha_nacimiento date NOT NULL,
    lugar_nacimiento character varying(100),
    nacionalidad character varying(50),
    idioma character varying(100),
    direccion text,
    telefono_casa character varying(20),
    telefono_movil character varying(20),
    telefono_emergencia character varying(20),
    fecha_inscripcion date,
    recomendado_por character varying(100),
    padre_id integer,
    madre_id integer,
    apellido_completo character varying(100),
    edad_estudiante integer,
    staff boolean,
    estudiante_hermanos character varying(100),
    colegios_anteriores character varying(10),
    periodo_id integer,
    activo boolean DEFAULT false
);


ALTER TABLE public.estudiantes OWNER TO postgres;

--
-- Name: estudiantes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.estudiantes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.estudiantes_id_seq OWNER TO postgres;

--
-- Name: estudiantes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.estudiantes_id_seq OWNED BY public.estudiantes.id;


--
-- Name: latepass_resumen_semanal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.latepass_resumen_semanal (
    id integer NOT NULL,
    estudiante_id integer NOT NULL,
    periodo_id integer NOT NULL,
    semana_del_anio integer NOT NULL,
    conteo_tardes integer DEFAULT 0,
    ultimo_mensaje character varying(255)
);


ALTER TABLE public.latepass_resumen_semanal OWNER TO postgres;

--
-- Name: latepass_resumen_semanal_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.latepass_resumen_semanal_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.latepass_resumen_semanal_id_seq OWNER TO postgres;

--
-- Name: latepass_resumen_semanal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.latepass_resumen_semanal_id_seq OWNED BY public.latepass_resumen_semanal.id;


--
-- Name: llegadas_tarde; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.llegadas_tarde (
    id integer NOT NULL,
    estudiante_id integer,
    hora_llegada time without time zone NOT NULL,
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    semana_del_anio integer,
    dia_de_la_semana integer
);


ALTER TABLE public.llegadas_tarde OWNER TO postgres;

--
-- Name: llegadas_tarde_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.llegadas_tarde_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.llegadas_tarde_id_seq OWNER TO postgres;

--
-- Name: llegadas_tarde_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.llegadas_tarde_id_seq OWNED BY public.llegadas_tarde.id;


--
-- Name: madres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.madres (
    madre_id integer NOT NULL,
    madre_nombre character varying(50),
    madre_apellido character varying(50),
    madre_fecha_nacimiento date,
    madre_cedula_pasaporte character varying(30),
    madre_nacionalidad character varying(50),
    madre_idioma character varying(100),
    madre_profesion character varying(50),
    madre_empresa character varying(100),
    madre_telefono_trabajo character varying(20),
    madre_celular character varying(20),
    madre_email character varying(100),
    id integer
);


ALTER TABLE public.madres OWNER TO postgres;

--
-- Name: madres_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.madres_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.madres_id_seq OWNER TO postgres;

--
-- Name: madres_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.madres_id_seq OWNED BY public.madres.madre_id;


--
-- Name: padres; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.padres (
    padre_id integer NOT NULL,
    padre_nombre character varying(50),
    padre_apellido character varying(50),
    padre_fecha_nacimiento date,
    padre_cedula_pasaporte character varying(30),
    padre_nacionalidad character varying(50),
    padre_idioma character varying(100),
    padre_profesion character varying(50),
    padre_empresa character varying(100),
    padre_telefono_trabajo character varying(20),
    padre_celular character varying(20),
    padre_email character varying(100),
    id integer
);


ALTER TABLE public.padres OWNER TO postgres;

--
-- Name: padres_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.padres_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.padres_id_seq OWNER TO postgres;

--
-- Name: padres_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.padres_id_seq OWNED BY public.padres.padre_id;


--
-- Name: periodos_escolares; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.periodos_escolares (
    id integer NOT NULL,
    nombre_periodo character varying(100) NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_fin date NOT NULL,
    activo boolean DEFAULT false,
    periodo_id integer
);


ALTER TABLE public.periodos_escolares OWNER TO postgres;

--
-- Name: periodos_escolares_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.periodos_escolares_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.periodos_escolares_id_seq OWNER TO postgres;

--
-- Name: periodos_escolares_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.periodos_escolares_id_seq OWNED BY public.periodos_escolares.id;


--
-- Name: profesor_periodo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profesor_periodo (
    id integer NOT NULL,
    profesor_id integer NOT NULL,
    periodo_id integer NOT NULL,
    posicion character varying(255) NOT NULL,
    homeroom_teacher character varying(255)
);


ALTER TABLE public.profesor_periodo OWNER TO postgres;

--
-- Name: profesor_periodo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.profesor_periodo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.profesor_periodo_id_seq OWNER TO postgres;

--
-- Name: profesor_periodo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.profesor_periodo_id_seq OWNED BY public.profesor_periodo.id;


--
-- Name: profesores; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profesores (
    id integer NOT NULL,
    nombre_completo character varying(100),
    cedula character varying(30),
    telefono character varying(20),
    email character varying(100),
    profesor_id integer
);


ALTER TABLE public.profesores OWNER TO postgres;

--
-- Name: profesores_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.profesores_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.profesores_id_seq OWNER TO postgres;

--
-- Name: profesores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.profesores_id_seq OWNED BY public.profesores.id;


--
-- Name: salud_estudiantil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.salud_estudiantil (
    id integer NOT NULL,
    estudiante_id integer,
    completado_por character varying(100),
    fecha_salud date,
    contacto_emergencia character varying(100),
    relacion_emergencia character varying(50),
    telefono1 character varying(20),
    telefono2 character varying(20),
    observaciones text,
    dislexia boolean,
    atencion boolean,
    otros boolean,
    info_adicional text,
    problemas_oido_vista text,
    fecha_examen text,
    autorizo_medicamentos boolean,
    medicamentos_actuales text,
    autorizo_emergencia boolean
);


ALTER TABLE public.salud_estudiantil OWNER TO postgres;

--
-- Name: salud_estudiantil_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.salud_estudiantil_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.salud_estudiantil_id_seq OWNER TO postgres;

--
-- Name: salud_estudiantil_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.salud_estudiantil_id_seq OWNED BY public.salud_estudiantil.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    rol character varying(20) NOT NULL,
    profesor_id integer,
    CONSTRAINT usuarios_rol_check CHECK (((rol)::text = ANY ((ARRAY['admin'::character varying, 'consulta'::character varying])::text[])))
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: vehiculos_autorizados; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vehiculos_autorizados (
    id integer NOT NULL,
    estudiante_id integer,
    placa character varying(15),
    conductor_nombre character varying(100),
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.vehiculos_autorizados OWNER TO postgres;

--
-- Name: vehiculos_autorizados_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vehiculos_autorizados_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehiculos_autorizados_id_seq OWNER TO postgres;

--
-- Name: vehiculos_autorizados_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vehiculos_autorizados_id_seq OWNED BY public.vehiculos_autorizados.id;


--
-- Name: auditoria_general id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_general ALTER COLUMN id SET DEFAULT nextval('public.auditoria_general_id_seq'::regclass);


--
-- Name: entrada_salida_profesores id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_profesores ALTER COLUMN id SET DEFAULT nextval('public.entrada_salida_profesores_id_seq'::regclass);


--
-- Name: estudiante_periodo id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiante_periodo ALTER COLUMN id SET DEFAULT nextval('public.estudiante_periodo_id_seq'::regclass);


--
-- Name: estudiantes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiantes ALTER COLUMN id SET DEFAULT nextval('public.estudiantes_id_seq'::regclass);


--
-- Name: latepass_resumen_semanal id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.latepass_resumen_semanal ALTER COLUMN id SET DEFAULT nextval('public.latepass_resumen_semanal_id_seq'::regclass);


--
-- Name: llegadas_tarde id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.llegadas_tarde ALTER COLUMN id SET DEFAULT nextval('public.llegadas_tarde_id_seq'::regclass);


--
-- Name: madres madre_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.madres ALTER COLUMN madre_id SET DEFAULT nextval('public.madres_id_seq'::regclass);


--
-- Name: padres padre_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.padres ALTER COLUMN padre_id SET DEFAULT nextval('public.padres_id_seq'::regclass);


--
-- Name: periodos_escolares id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.periodos_escolares ALTER COLUMN id SET DEFAULT nextval('public.periodos_escolares_id_seq'::regclass);


--
-- Name: profesor_periodo id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor_periodo ALTER COLUMN id SET DEFAULT nextval('public.profesor_periodo_id_seq'::regclass);


--
-- Name: profesores id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesores ALTER COLUMN id SET DEFAULT nextval('public.profesores_id_seq'::regclass);


--
-- Name: salud_estudiantil id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salud_estudiantil ALTER COLUMN id SET DEFAULT nextval('public.salud_estudiantil_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Name: vehiculos_autorizados id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos_autorizados ALTER COLUMN id SET DEFAULT nextval('public.vehiculos_autorizados_id_seq'::regclass);


--
-- Data for Name: auditoria_general; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.auditoria_general (id, tabla_afectada, registro_id, usuario_id, accion, campos_modificados, fecha_hora, ip_usuario, nota) FROM stdin;
\.


--
-- Data for Name: entrada_salida_profesores; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.entrada_salida_profesores (id, profesor_id, tipo_movimiento, fecha_hora) FROM stdin;
\.


--
-- Data for Name: estudiante_periodo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estudiante_periodo (id, estudiante_id, periodo_id, grado_cursado) FROM stdin;
13	55	2	Grade 5
14	57	2	Grade 4
\.


--
-- Data for Name: estudiantes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estudiantes (id, nombre_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, fecha_inscripcion, recomendado_por, padre_id, madre_id, apellido_completo, edad_estudiante, staff, estudiante_hermanos, colegios_anteriores, periodo_id, activo) FROM stdin;
57	Nivmarys Milagros	2015-02-13	Anaco	Venezolana	espanol	Calle Pichincha casa S/N, Sector Parlcelas I, Anaco, Anzoategui 6003	0282-4247381	04141488520	0282-4247381	2025-07-19	nadie	55	43	Carvajal	9	f	Roger Carvajal		2	t
55	Roger Ramon	2016-06-04	Anaco	Venezolana	espanol	Calle Pichincha casa S/N, Sector Parlcelas I, Anaco, Anzoategui 6003	0282-4247381	04120813649	0282-4247381	2025-07-08	nadie	55	43	Carvajal	9	f	Nivmarys Carvajal		\N	t
\.


--
-- Data for Name: latepass_resumen_semanal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.latepass_resumen_semanal (id, estudiante_id, periodo_id, semana_del_anio, conteo_tardes, ultimo_mensaje) FROM stdin;
4	55	2	29	3	Llegada Tarde - TERCER STRIKE. Notificar al representante.
7	57	2	29	1	Llegada Tarde - Primer Strike
\.


--
-- Data for Name: llegadas_tarde; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.llegadas_tarde (id, estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana) FROM stdin;
8	55	20:57:13	2025-07-19 00:00:00	29	6
9	57	21:10:41	2025-07-19 00:00:00	29	6
\.


--
-- Data for Name: madres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.madres (madre_id, madre_nombre, madre_apellido, madre_fecha_nacimiento, madre_cedula_pasaporte, madre_nacionalidad, madre_idioma, madre_profesion, madre_empresa, madre_telefono_trabajo, madre_celular, madre_email, id) FROM stdin;
43	Mary	Carvajal Sequea	1947-11-12	3687010	Venezolana	Español	Costurera	Independiente	0416-1693290	04161693290	marycarvajal1947@gmail.com	\N
\.


--
-- Data for Name: padres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.padres (padre_id, padre_nombre, padre_apellido, padre_fecha_nacimiento, padre_cedula_pasaporte, padre_nacionalidad, padre_idioma, padre_profesion, padre_empresa, padre_telefono_trabajo, padre_celular, padre_email, id) FROM stdin;
55	Neris Ramon	Rondon	1945-07-31	3441225	Venezolano	Español, Inglés	Ingeniero	Petroleo	04248693290	04248693290	roger.carvajal@yahoo.com.ve	\N
\.


--
-- Data for Name: periodos_escolares; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.periodos_escolares (id, nombre_periodo, fecha_inicio, fecha_fin, activo, periodo_id) FROM stdin;
2	Agosto 2025 - Junio 2026	2025-08-18	2026-06-04	t	\N
\.


--
-- Data for Name: profesor_periodo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profesor_periodo (id, profesor_id, periodo_id, posicion, homeroom_teacher) FROM stdin;
2	5	2	Director	Grade 12
3	8	2	IT Manager	N/A
4	6	2	Bussiness Manager	N/A
6	9	2	Science Teacher - Grade 6-12	Grade 8
7	7	2	Administrative Assistant	N/A
\.


--
-- Data for Name: profesores; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profesores (id, nombre_completo, cedula, telefono, email, profesor_id) FROM stdin;
5	Daniela Medina	19.941.342	0426-7859564	dmedina@ceiak12.org	\N
8	Roger Ramon Carvajal	11001150	0412-0813649	rcarvajal@ceiak12.org	\N
6	Damarys Quintero	11111112	0414-1985439	dquintero@ceiak12.org	\N
9	Luis Castañeda	26.313.224	0424-8896827	lcastaneda@ceiak12.org	\N
7	Dormeris Velásquez	15.564.748	0414-8374690	dorvelazquez@ceiak12.org	\N
\.


--
-- Data for Name: salud_estudiantil; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.salud_estudiantil (id, estudiante_id, completado_por, fecha_salud, contacto_emergencia, relacion_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional, problemas_oido_vista, fecha_examen, autorizo_medicamentos, medicamentos_actuales, autorizo_emergencia) FROM stdin;
9	55	Damarys	2025-07-08	Mary Carvajal	madre	0416-1693290			f	f	f			\N	t	Acetaminofen	f
11	57	Damarys Quintero	2025-07-19	Mary Carvajal	madre	0416-1693290			f	f	f		\N		f		f
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, username, password, rol, profesor_id) FROM stdin;
4	admin	$2y$10$DNrf1lbCHY9zxsNObWUAMOlv/YsvNIaIrI8b7M37TQZ8IXBGY7rhC	admin	\N
6	lcastaneda	$2y$10$7laz3w8DUKA0J.azFugBY.2S6medyqggUhR6tpryZuGFe5OM5pFWa	consulta	9
7	rcarvajal	$2y$10$0Pw3ILyQywEyp8DLHkEpSucEom5Dnk5YEBNX6766jHtrzDlCmR0OK	admin	8
5	dmedina	$2y$10$zASKq913auF1o63wuPCC7OCYlmPs1AZcJq792b7H1vz4JrsOP7K3m	admin	5
8	dquintero	$2y$10$vp0sPP.rm1J0tlp0V6bh0e17IxguzAyNXVNbkCRB6Ar3rvrw96Q4q	admin	6
9	dorvelasquez	$2y$10$iFfCh8H6xpeVndIrWaFVL.g9jvZdIRZzX6/yOHIyY0tEYCDsuZwq2	admin	7
\.


--
-- Data for Name: vehiculos_autorizados; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vehiculos_autorizados (id, estudiante_id, placa, conductor_nombre, fecha_hora) FROM stdin;
\.


--
-- Name: auditoria_general_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.auditoria_general_id_seq', 1, false);


--
-- Name: entrada_salida_profesores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.entrada_salida_profesores_id_seq', 1, false);


--
-- Name: estudiante_periodo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.estudiante_periodo_id_seq', 14, true);


--
-- Name: estudiantes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.estudiantes_id_seq', 57, true);


--
-- Name: latepass_resumen_semanal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.latepass_resumen_semanal_id_seq', 7, true);


--
-- Name: llegadas_tarde_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.llegadas_tarde_id_seq', 9, true);


--
-- Name: madres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.madres_id_seq', 44, true);


--
-- Name: padres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.padres_id_seq', 57, true);


--
-- Name: periodos_escolares_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.periodos_escolares_id_seq', 2, true);


--
-- Name: profesor_periodo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.profesor_periodo_id_seq', 7, true);


--
-- Name: profesores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.profesores_id_seq', 9, true);


--
-- Name: salud_estudiantil_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.salud_estudiantil_id_seq', 11, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 9, true);


--
-- Name: vehiculos_autorizados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vehiculos_autorizados_id_seq', 1, false);


--
-- Name: auditoria_general auditoria_general_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.auditoria_general
    ADD CONSTRAINT auditoria_general_pkey PRIMARY KEY (id);


--
-- Name: entrada_salida_profesores entrada_salida_profesores_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_profesores
    ADD CONSTRAINT entrada_salida_profesores_pkey PRIMARY KEY (id);


--
-- Name: estudiante_periodo estudiante_periodo_estudiante_id_periodo_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiante_periodo
    ADD CONSTRAINT estudiante_periodo_estudiante_id_periodo_id_key UNIQUE (estudiante_id, periodo_id);


--
-- Name: estudiante_periodo estudiante_periodo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiante_periodo
    ADD CONSTRAINT estudiante_periodo_pkey PRIMARY KEY (id);


--
-- Name: estudiantes estudiantes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_pkey PRIMARY KEY (id);


--
-- Name: latepass_resumen_semanal latepass_resumen_semanal_estudiante_id_periodo_id_semana_de_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.latepass_resumen_semanal
    ADD CONSTRAINT latepass_resumen_semanal_estudiante_id_periodo_id_semana_de_key UNIQUE (estudiante_id, periodo_id, semana_del_anio);


--
-- Name: latepass_resumen_semanal latepass_resumen_semanal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.latepass_resumen_semanal
    ADD CONSTRAINT latepass_resumen_semanal_pkey PRIMARY KEY (id);


--
-- Name: llegadas_tarde llegadas_tarde_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.llegadas_tarde
    ADD CONSTRAINT llegadas_tarde_pkey PRIMARY KEY (id);


--
-- Name: madres madres_cedula_pasaporte_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.madres
    ADD CONSTRAINT madres_cedula_pasaporte_key UNIQUE (madre_cedula_pasaporte);


--
-- Name: madres madres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.madres
    ADD CONSTRAINT madres_pkey PRIMARY KEY (madre_id);


--
-- Name: padres padres_cedula_pasaporte_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.padres
    ADD CONSTRAINT padres_cedula_pasaporte_key UNIQUE (padre_cedula_pasaporte);


--
-- Name: padres padres_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.padres
    ADD CONSTRAINT padres_pkey PRIMARY KEY (padre_id);


--
-- Name: periodos_escolares periodos_escolares_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.periodos_escolares
    ADD CONSTRAINT periodos_escolares_pkey PRIMARY KEY (id);


--
-- Name: profesor_periodo profesor_periodo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor_periodo
    ADD CONSTRAINT profesor_periodo_pkey PRIMARY KEY (id);


--
-- Name: profesor_periodo profesor_periodo_profesor_id_periodo_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor_periodo
    ADD CONSTRAINT profesor_periodo_profesor_id_periodo_id_key UNIQUE (profesor_id, periodo_id);


--
-- Name: profesores profesores_cedula_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesores
    ADD CONSTRAINT profesores_cedula_key UNIQUE (cedula);


--
-- Name: profesores profesores_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesores
    ADD CONSTRAINT profesores_pkey PRIMARY KEY (id);


--
-- Name: salud_estudiantil salud_estudiantil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salud_estudiantil
    ADD CONSTRAINT salud_estudiantil_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_profesor_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_profesor_id_key UNIQUE (profesor_id);


--
-- Name: usuarios usuarios_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_username_key UNIQUE (username);


--
-- Name: vehiculos_autorizados vehiculos_autorizados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos_autorizados
    ADD CONSTRAINT vehiculos_autorizados_pkey PRIMARY KEY (id);


--
-- Name: entrada_salida_profesores entrada_salida_profesores_profesor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_profesores
    ADD CONSTRAINT entrada_salida_profesores_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES public.profesores(id) ON DELETE CASCADE;


--
-- Name: estudiantes estudiantes_madre_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_madre_id_fkey FOREIGN KEY (madre_id) REFERENCES public.madres(madre_id);


--
-- Name: estudiantes estudiantes_padre_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiantes
    ADD CONSTRAINT estudiantes_padre_id_fkey FOREIGN KEY (padre_id) REFERENCES public.padres(padre_id);


--
-- Name: estudiante_periodo fk_estudiante; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiante_periodo
    ADD CONSTRAINT fk_estudiante FOREIGN KEY (estudiante_id) REFERENCES public.estudiantes(id) ON DELETE CASCADE;


--
-- Name: estudiante_periodo fk_periodo; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.estudiante_periodo
    ADD CONSTRAINT fk_periodo FOREIGN KEY (periodo_id) REFERENCES public.periodos_escolares(id) ON DELETE CASCADE;


--
-- Name: latepass_resumen_semanal latepass_resumen_semanal_estudiante_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.latepass_resumen_semanal
    ADD CONSTRAINT latepass_resumen_semanal_estudiante_id_fkey FOREIGN KEY (estudiante_id) REFERENCES public.estudiantes(id) ON DELETE CASCADE;


--
-- Name: latepass_resumen_semanal latepass_resumen_semanal_periodo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.latepass_resumen_semanal
    ADD CONSTRAINT latepass_resumen_semanal_periodo_id_fkey FOREIGN KEY (periodo_id) REFERENCES public.periodos_escolares(id);


--
-- Name: llegadas_tarde llegadas_tarde_estudiante_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.llegadas_tarde
    ADD CONSTRAINT llegadas_tarde_estudiante_id_fkey FOREIGN KEY (estudiante_id) REFERENCES public.estudiantes(id) ON DELETE CASCADE;


--
-- Name: profesor_periodo profesor_periodo_periodo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor_periodo
    ADD CONSTRAINT profesor_periodo_periodo_id_fkey FOREIGN KEY (periodo_id) REFERENCES public.periodos_escolares(id) ON DELETE CASCADE;


--
-- Name: profesor_periodo profesor_periodo_profesor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor_periodo
    ADD CONSTRAINT profesor_periodo_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES public.profesores(id) ON DELETE CASCADE;


--
-- Name: salud_estudiantil salud_estudiantil_estudiante_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salud_estudiantil
    ADD CONSTRAINT salud_estudiantil_estudiante_id_fkey FOREIGN KEY (estudiante_id) REFERENCES public.estudiantes(id) ON DELETE CASCADE;


--
-- Name: usuarios usuarios_profesor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES public.profesores(id) ON DELETE SET NULL;


--
-- Name: vehiculos_autorizados vehiculos_autorizados_estudiante_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos_autorizados
    ADD CONSTRAINT vehiculos_autorizados_estudiante_id_fkey FOREIGN KEY (estudiante_id) REFERENCES public.estudiantes(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

