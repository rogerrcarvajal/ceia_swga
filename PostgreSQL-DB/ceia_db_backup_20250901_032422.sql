--
-- PostgreSQL database dump
--

\restrict gzHia6V9aDdDQ2MMfvs9NW8hWwz0GTjjsE8z4LnAA7cniHwyEmCJ2QDrExkY1ej

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

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
-- Name: entrada_salida_staff; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.entrada_salida_staff (
    id integer NOT NULL,
    profesor_id integer NOT NULL,
    fecha date NOT NULL,
    hora_entrada time without time zone,
    hora_salida time without time zone,
    observaciones text,
    ausente boolean DEFAULT false
);


ALTER TABLE public.entrada_salida_staff OWNER TO postgres;

--
-- Name: entrada_salida_staff_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.entrada_salida_staff_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.entrada_salida_staff_id_seq OWNER TO postgres;

--
-- Name: entrada_salida_staff_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.entrada_salida_staff_id_seq OWNED BY public.entrada_salida_staff.id;


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
    profesor_id integer,
    categoria character varying(50)
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
-- Name: registro_vehiculos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.registro_vehiculos (
    id integer NOT NULL,
    vehiculo_id integer,
    fecha date DEFAULT CURRENT_DATE NOT NULL,
    hora_entrada time without time zone,
    hora_salida time without time zone,
    observaciones text,
    registrado_por character varying(50),
    creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.registro_vehiculos OWNER TO postgres;

--
-- Name: registro_vehiculos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.registro_vehiculos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.registro_vehiculos_id_seq OWNER TO postgres;

--
-- Name: registro_vehiculos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.registro_vehiculos_id_seq OWNED BY public.registro_vehiculos.id;


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
    CONSTRAINT usuarios_rol_check CHECK (((rol)::text = ANY (ARRAY[('admin'::character varying)::text, ('consulta'::character varying)::text, ('master'::character varying)::text])))
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
-- Name: vehiculos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vehiculos (
    id integer NOT NULL,
    estudiante_id integer,
    placa character varying(20),
    modelo character varying(50),
    autorizado boolean DEFAULT true
);


ALTER TABLE public.vehiculos OWNER TO postgres;

--
-- Name: vehiculos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vehiculos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehiculos_id_seq OWNER TO postgres;

--
-- Name: vehiculos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vehiculos_id_seq OWNED BY public.vehiculos.id;


--
-- Name: entrada_salida_staff id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_staff ALTER COLUMN id SET DEFAULT nextval('public.entrada_salida_staff_id_seq'::regclass);


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
-- Name: registro_vehiculos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registro_vehiculos ALTER COLUMN id SET DEFAULT nextval('public.registro_vehiculos_id_seq'::regclass);


--
-- Name: salud_estudiantil id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salud_estudiantil ALTER COLUMN id SET DEFAULT nextval('public.salud_estudiantil_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Name: vehiculos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos ALTER COLUMN id SET DEFAULT nextval('public.vehiculos_id_seq'::regclass);


--
-- Data for Name: entrada_salida_staff; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.entrada_salida_staff (id, profesor_id, fecha, hora_entrada, hora_salida, observaciones, ausente) FROM stdin;
\.


--
-- Data for Name: estudiante_periodo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estudiante_periodo (id, estudiante_id, periodo_id, grado_cursado) FROM stdin;
13	10013	2	Grade 4
14	10014	2	Grade 4
15	10015	2	Grade 4
16	10016	2	Grade 4
17	10017	2	Grade 4
18	10018	2	Grade 4
19	10019	2	Grade 11
1	10003	2	Grade 1
2	10002	2	Prekinder 4
3	10004	2	Kindergarten
4	10001	2	Prekinder 4
5	10005	2	Prekinder 3
6	10006	2	Prekinder 4
7	10007	2	Kindergarten
8	10008	2	Grade 2
9	10009	2	Grade 3
10	10010	2	Grade 3
11	11001	2	Grade 3
12	10012	2	Grade 3
20	10020	2	Grade 6
21	10021	2	Grade 5
22	10022	2	Grade 5
23	10023	2	Grade 5
24	10024	2	Grade 5
25	10025	2	Grade 5
26	10026	2	Grade 5
27	10027	2	Grade 5
28	10028	2	Grade 5
29	10029	2	Grade 5
30	10030	2	Grade 6
31	10031	2	Grade 6
32	10032	2	Grade 6
33	10033	2	Grade 6
34	10034	2	Grade 8
35	10035	2	Grade 8
36	10036	2	Grade 8
37	10037	2	Grade 9
38	10038	2	Grade 9
39	10039	2	
40	10040	2	Grade 9
41	10041	2	Grade 9
42	10042	2	Grade 9
43	10043	2	Grade 9
44	10044	2	Grade 10
45	10045	2	Grade 10
46	10046	2	Grade 10
47	10047	2	Grade 10
48	10048	2	Grade 10
49	10049	2	Grade 10
50	10050	2	Grade 10
51	10051	2	Grade 11
52	10052	2	Grade 11
53	10053	2	Grade 11
54	10055	2	Grade 12
55	10054	2	Grade 12
56	10056	2	Grade 12
57	10057	2	Grade 12
59	10059	2	Grade 12
60	10060	2	Grade 12
61	10061	2	Grade 12
62	10062	2	Grade 10
58	10058	2	Grade 12
63	10063	2	Grade 9
87	10011	2	Grade 3
\.


--
-- Data for Name: estudiantes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.estudiantes (id, nombre_completo, fecha_nacimiento, lugar_nacimiento, nacionalidad, idioma, direccion, telefono_casa, telefono_movil, telefono_emergencia, fecha_inscripcion, recomendado_por, padre_id, madre_id, apellido_completo, edad_estudiante, staff, estudiante_hermanos, colegios_anteriores, periodo_id, activo) FROM stdin;
10002	Diego Rafaél	2021-02-23	Anaco	Venezolano	Español	Callejón Sucre #1-3	0282-4242679	0424-8378325	0424-8378325	2023-09-14		3	3	Marín García	4	t	Leticia Marín		1	t
10036	Anthony Hassib	2012-07-30	Panama	Panameño	Español 	Av. los Pilones antiguo campos de Halliburton			0412-4475005	2021-01-04		31	30	Campos el Souky	13	f			1	t
10052	Kimberly Kammel	2008-11-13	Anaco	Venezolano	Españo, Mandarin, Ingles	AV. Portuguesa N18			04148387040	2008-08-12		43	43	Mo Mok	16	f			1	t
10053	Isabella Valentina	2009-04-04	Lechería	Venezolano	Español, Ingles	Urb. Los Naranjos casa #1			0414-1990936	2021-09-02	Familia Milan	44	44	Graterol Brancato	16	f			1	t
10054	Hakim 	2008-05-14	Lechería	Venezolano	Español, Ingles	Urb. Country Club casa #18	0282-4257727		0426-5831254	2014-08-06		45	45	Abou Eid Aljaber	17	f			1	t
10055	Sophia Alejandra 	2008-10-28	El tigre	Venezolano	Español, Ingles	Urb. Ikabaru casa #H			0414-9834192	2016-08-10		30	29	Bello Villalobos	16	f	Andrés Bello		1	t
10060	Letizia Chiquinquirá	2008-11-18	Lechería	Venezolano	Español, Ingles	Av. José Antonio Anzoátegui, Res. Abadía casa #01.			0424-8467409	2018-09-26		50	50	Torrealba Rondón	16	f			1	f
10061	Kenvi Eduardo	2008-09-19	Anaco	Venezolano	Español, Mandarin, Ingles	Urb. Los Narajos casa #H06			0424-8258668	2012-08-14		28	27	Chau Mo	16	f	Christy Chau		1	f
10062	Sleiman	2011-04-06	Anaco	Venezolano	Español, Arabe	Urb. Los Cedros casa #13			0412-8413408	2022-08-16		22	21	Nizek Mayhoub	14	f	Clariluz Nizek		1	f
10063	Patricia 	2008-11-25	Anaco	Venezolano	Español, Mandarin, Ingles	Av. Merida Local #134 sector Pueblo Nuevo			0412-1938182	2023-08-11		51	51	He Cen	16	f			1	f
10001	Anabella	2021-01-16	Lecheria	Venezolano	Español	Av. José Antonio Anzoátegui, Campo Anaco Suply, C.A.	0282-4222942	0414-8171511	0414-8388165	2023-08-03	Familia Rondón Arciniegas	1	1	Velásquez Giamanco	4	f			1	t
10003	Ambar Gabriela	2018-12-21	Lechería	Venezolano	Español	Urb. Los Naranjos, casa #40	0414-0346333	0424-8350450	0414-1989695	2021-10-20		4	4	Gómez Bastidas	6	f	Iván Gabriel Gómez		1	t
10016	Isabella Sofia 	2016-05-18	Lechería	Venezolano	Español 	Urb. EL Rosario, via Hp			0414-8376296	2019-09-23	Donimer Velaásquez	17	16	Perez Velásquez	9	t			1	t
10017	Mathias David 	2016-03-09	Anaco	Venezolano	Español 	Urb. Parque Anaco, via HP			0426-3803636	2021-08-30		18	17	Pico Figuera	9	f	Nicole Pico		1	t
10018	Lucas David 	2016-05-25	Caracas	Venezolano	Español 	Urb, los Naranjos Th# 33			0412-1852741	2024-08-02	Familia Salazar Caraballo	19	18	Peñaranda Wever	9	f			1	t
10019	Isabella Victoria	2008-12-08	Lechería	Venezolano	Español, Ingles	Urb, El rosario via Hp			0414-8385115	2025-08-05	Familia Milan	20	19	Flores Zamora	17	f	Leticia Flores		1	t
10037	Johannes Danella	2011-09-30	Anaco	Venezolano	Español 	Urb. Campo Duarte casa #11	0282-4240627	0414-1164560	0414-1164560	2021-12-08	Damarys Quintero	32	31	Alfonzo Luces	14	f			1	t
10041	Santiago Mathias	2011-02-08	Anaco	Venezolano	Español 	Urb. El Rosario calle B casa # B-15 via Hp			0414-2936308	2023-08-14	Familia Carvajal Romero	35	34	González Barrios	14	f			1	t
10042	Ariadna	2009-03-13	Anaco	Venezolano	Mandarin	Urb. Los Naranjos			0412-8598952	2024-07-17		36	35	Wu Lin	12	f			1	t
10043	Zi Xiang	2010-07-30	Anaco	Venezolano	Mandarin, Español	Av. Miranda 1-16			0412-0861611	2024-08-21		37	36	Lu Huang	15	f			1	t
10044	Estefanía Beatriz	2010-03-28	Lechería	Venezolano	Español 	Conj. Res los Bugenviles piso 9 apto 3C	0281-2823973		0414-8179380	2023-07-26		38	37	Lima Naar	15	f			1	t
10045	Luisa Valentina	2010-10-07	Lechería	Venezolano	Español 	Urb. El Oasis casa #27			0424-8100330	2018-09-03		14	14	Gil Mata	14	f	Luisa Carlos y Luisa Fernanda		1	t
10046	Laura Sophia	2010-06-09	Lechería	Venezolano	Español, Ingles	Conj. Res el Oasis casa #19	0282-4226447		0414-8388630	2020-08-24		39	38	Milan  Rojas	15	f			1	t
10047	Aranza Valentina	2010-06-30	Anaco	Venezolano	Español 	Urb. Agua Clara calle 3 Quinta K-11	0282-4241510		0282-4241283	2019-08-29	Dalitzis Perales	40	39	Lopez Herrera	15	f			1	t
10048	Jie Yin	2007-06-20	Venezuela	Venezolano	Mandarin, Español	Av. Miranda con calle Democracia			0412-6911237	2024-04-08		41	40	Li Wu	18	f			1	t
10049	Osduarlis Ines	2010-10-08	El tigre	Venezolano	Español 	Urb. Las Palmas calle 6 #09	0424-8738431		0424-9179534	2024-08-16		12	41	Natera Vidal	15	f	Eduoscari Natera		1	t
10050	Leo Maximiliano	2010-06-23	Anaco	Venezolano	Español, Ingles	Via los Pilones, sector el Paraiso Qta Quarles	0282-4257878		0424-8400182	2025-07-10	FAmilia Gómez Monasterios	13	13	Uluknon Trias	15	f	Santiago Uluknon		1	t
10051	Eduardo 	2009-06-01	Caracas	Venezolano	Español, Ingles	Urb. Los Naranjos			0414-2618112	2014-08-13		42	42	Guerra Vegas	16	f			1	t
10004	Amelie	2020-04-13	Lechería	Venezolano	Español 	Conjunto residencial las Palmas casa#8	0282-4226486		0414-8997331	2025-07-08		5	5	Rondón Arceniegas	5	f			1	t
10005	Miranda Lane	2022-09-07	Anaco	Venezolano	Español 	Urb. Los Pinos, calle principal casa #06	0282-4246365		0424-8339734	2025-07-08	Familia Pico	6	6	Pino Silva	3	f			1	t
10006	Gael David	2021-05-27	El tigre	Venezolano	Español 	4ta calle sector la florida			0412-1113384	2025-08-07		8	8	Ferreira Ramos	4	t	Victoria Ferrerira		1	t
10007	Nilio Enmanuel	2020-02-28	Anaco	Venezolano	Español 	Via Hp Urb. Carmen Isabel 	0282-4248911		0414-8395580	2023-08-07	Familia Salazar	9	9	Salazar Rosas	5	f	Ambar Salazar		1	t
10008	Antoine 	2018-07-31	Lecheria 	Venezolano	Español, Ingles	Av, Jose Antonio Anzoategui Km97			0424-8459073	2022-07-11		10	10	Batour Prado	7	f	Isabella Batour 		1	t
10009	Ambar Elena	2017-07-12	Anaco	Venezolano	Español 	Via Hp Urb. Carmen Isabel calle principalk c/c5	0282-4248911		0414-8395580	2022-08-07	Familia Lopez	9	9	Salazar Rosas	8	f	Nilio Salazar		1	t
10010	Leticia Elena 	2017-06-15	Anaco	Venezolano	Español 	Callejón suvre #1-3 23 de enero			0426-2840775	2019-08-19		11	11	Marín García	8	t	Diego Marín		1	t
10011	Eduoscari Valentina	2017-10-04	Puerto la Cruz	Venezolano	Español 	Campos las palmas calle 6 #09			0424-9179534	2025-07-08		12	12	Natera Vidal	8	f	Osduarlis Natera		1	t
10012	Santiago Manuel	2017-12-21	Anaco	Venezolano	Español 	Via los Pilones Sector el paraiso 			0424-2400182	2025-07-10	Gomez Monasterios	13	13	Uluknon Trias	8	f	Leo Ulucknon		1	t
10013	Luisa Fernanda	2016-10-10	Lecheria 	Venezolano	Español 	Urb. el Oasis casa #27	0282-4242612		0414-0346005	2018-06-08	Sabrina Zivolo	14	14	Gil Mata	8	f	Luisa V. Gil Y Luis C. Gil		1	t
10014	Iván Gabriel 	2016-02-22	Anaco	Venezolano	Español 	Urb. Los naranjos			0414-83500450	2018-10-01	Edith Zamora	15	4	Gómez Bastidas	9	f	Ambar Gomez		1	t
10015	Sara Sofia	2016-09-08	Anaco	Venezolano	Español 	Urb. Mara casa#2 sectir via Hp 			0416-6281072	2019-07-11		16	15	Medori Mota	9	t	Cristian Medori		1	t
10020	Leticia Alejandra	2013-11-06	Lechería	Venezolano	Español 	Urb, EL Rosario casa # 11/12	0414-8397199		0414-2452233	2025-08-05	Familia Milan	20	19	Flores Zamora	14	f	Isabella Flores		1	t
10021	Michelle Andrea	2015-05-18	Orlando, Florida EEUU	Venezolano	Español 	Urb. Parque Anaco casa # 61		04166821064	0424-8593858	2017-03-09	Daniel Monasterio	21	20	Gómez Monasterio	10	f			1	t
10022	Clariluz 	2014-08-11	Anaco	Venezolano	Español 	Via Hp, Urb los Cedros #13			0412-8413408	2022-08-16		22	21	Nizek Mayhoub	11	f	Sleiman Nizek		1	t
10023	Samantha Victoria	2015-12-14	Anaco	Venezolano	Español 	Calle Insustria #4-A	0282-4256183		0414-2797638	2021-07-28	Familia Rondón Arciniegas	23	22	Rondón Gómez	9	f			1	t
10024	Stella Lucia	2015-10-31	Lechería	Venezolano	Español	Urb. Las Tinajas calle B # 2\r\n	0282-4243617		0414-8388710	2017-07-08		24	23	Zivolo Ruiz	9	f			1	t
10025	Isaias José	2015-08-04	Lechería	Venezolano	Español 	Urb. Los Naranjos, casa #9\r\n\r\n			0414-0898537	2020-08-24		25	24	Salazar Caraballo	10	f			1	t
10026	Gabriela Victoria de Nazareth 	2015-04-07	Lechería	Venezolano	Español 	Calle Comercio Edif Serpego apto 2-2\r\n	0282-4241283		0424-8765513	2018-04-07	Dalizis Perales	26	25	López Medina	10	f			1	t
10027	Carlota Maria	2015-09-17	Anaco	Venezolano	Español 	Via Hp, Ikabaru casa #22\r\n	0282-4256468		0412-1842882	2018-07-04	Familia Zivolo	27	26	Randolph Galea	9	f			1	t
10028	Christy Valentina	2014-11-24	Anaco	Venezolano	Españo, Mandarin, Ingles	Urb, Los Naranjos\r\n\r\n			0412-1945962	2018-08-12		28	27	Chau Mo	10	f	Kenvi Chau		1	t
10029	Valentina Nazareth	2016-03-03	Anaco	Venezolano	Español 	Urb. Villa del Este			0414-7905033	2024-02-24		29	28	Mendoza Gonález	9	f			1	t
10030	Victoria de los Angeles	2014-03-07	Puerto Ordaz	Venezolano	Español 	4ta Calle Sector la Florida			0416-6566559	2024-08-12		8	8	Ferreira Ramos	11	t			1	t
10031	Cristian Daniel	2013-06-19	Anaco	Venezolano	Español 	Urb. Mara casa #2, sector via Hp\r\n\r\n			0416-6281072	2019-08-12		16	15	Medori Mota	12	t			1	t
10033	Isabella 	2014-07-03	Lechería	Venezolano	Español, Ingles	Av. José Antonio Anzoategui Km 97			0424-8894264	2017-09-04		10	10	Batour Prado	11	f	Antoine Batour		1	t
10034	Luis Carlos	2012-04-09	Lechería	Venezolano	Español 	Urb. El Oasis casa # 27, sector Vientop Fresco	0282-4241912		0424-8100330	2018-09-18	Sabrina Zivolo	14	14	Gil Mata	13	f	Luisa V. y Luisa F. 		1	t
10035	Andres Nemecio	2012-10-13	El tigrito	Venezolano	Español 	Urb. Ikabaru casa #H			0414-9834192	2016-08-10		30	29	Bello Villalobos	13	f	Sophia Bello		1	t
10038	Barbara Valentina	2011-12-11	Merida	Venezolano	Español 	Urb, Los Angeles casa #16, via Hp			0414-9843616	2020-03-17		33	32	Nuñez Quintero	14	t			1	t
10040	Isabella Valentina	2011-07-12	Anaco	Venezolano	Español 	Urb, Mene Grande, calle 2, casa D-2	0282-4258217		0424-8667818	2021-08-25	Carlos Rondón	34	33	Muñoz Méndez	14	f			1	t
10056	Carol Michelle	2008-02-23	Lechería	Venezolano	Español, Ingles	Urb. La Orquidea casa 2C-14	0282-4240971		0414-3833765	2011-08-08		46	46	López Perales	17	f			1	f
10057	Valeria Alejandra	2008-11-04	Anaco	Venezolano	Español, Ingles	Conjunto Res. El Bosque Apto. 1C1			0426-5151416	2021-07-29		47	47	Calderón Díaz	16	f			1	f
10058	Tomas Enrique	2008-06-18	San Tomé	Venezolano	Español, Ingles	Urb. Santa Rosa casa #57			0414-8394850	2021-07-29		48	48	Guazz Ezeiza	17	f			1	f
10032	Nicole Antonella	2014-03-21	Anaco	Venezolano	Español 	Via Hp, Urb. parque Anaco\r\n			0426-2802311	2020-09-03	Andreina Hernandez	18	17	Pico Figuera	11	f			1	t
10059	Natalia 	2008-07-31	Anaco	Venezolano	Español, Ingles	Urb. Parque Anaco casa #38			0412-0917584	2019-08-05		49	49	Colmenares Azuaje	17	f			1	f
\.


--
-- Data for Name: latepass_resumen_semanal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.latepass_resumen_semanal (id, estudiante_id, periodo_id, semana_del_anio, conteo_tardes, ultimo_mensaje) FROM stdin;
\.


--
-- Data for Name: llegadas_tarde; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.llegadas_tarde (id, estudiante_id, hora_llegada, fecha_registro, semana_del_anio, dia_de_la_semana) FROM stdin;
\.


--
-- Data for Name: madres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.madres (madre_id, madre_nombre, madre_apellido, madre_fecha_nacimiento, madre_cedula_pasaporte, madre_nacionalidad, madre_idioma, madre_profesion, madre_empresa, madre_telefono_trabajo, madre_celular, madre_email, id) FROM stdin;
35	Meizhu	Lin	1984-05-14	84551468	Extranjero	Mandarin	Ama de casa	Casa		0412-6940522	meizhulin14@gmail.com	\N
36	Caixin	Huang	1979-10-19	84551469	Extranjero	Mandarin	Comerciante	Supermercado Allyson		0412-8359988	hxiaoxin79@gmail.com	\N
40	HuiDan	Wu	1981-10-24	4156829	Extranjero	Mandarin, Ingles	Comerciante	Automercado Yili 2013		0412-0911237	huidanwu41@gmail.com	\N
45	Nur	Aljaber	1988-01-20	84423220	Extranjero	Español, Arabe	Ama de casa	Casa		0414-8397262	gassanaboueid@hotmail.com	\N
3	Johanna Carolina	García Orta	1980-10-31	14552938	Venezolano	Español, Inglés	Docente	CEIA	0424-8378325	0424-8378325	ortamgg@gmail.com	\N
4	Nathalia Andreina	Bastidas Cedeño	1989-04-21	19984587	Venezolano	Español, Inglés	Médico Cirujano	Provida Center		0414-0346333	nathaliabastidasc@gmail.com	\N
5	Maria Gabriela	Arciniegas Bolívar	1979-10-10	14082337	Venezolano	Español	Odontologa	Independiente		0414-8021057	gabyarciniegas@gmail.com	\N
6	Shirley Lane	Silva Quiñones	1987-02-17	18205351	Venezolano	Español	Ingeniero	MG Adminitración C.A		0414-8104369	shirleysilva87@hotmail.com	\N
8	Milagros del Valle	Ramos Villarroel	1980-11-12	15065482	Venezolano	Español	Lcda. en Educación	CEIA		0412-1113384	mramos@ceiak12.org	\N
9	Miladys Cristina	Rosas Delgado	1978-12-28	14308209	Venezolano	Español	Licenciada RRHH	PDVSA	0282-4202149	0414-7731866	miladysrosas57@gmail.com	\N
1	Daniela	Gianmanco Bracho	1990-07-25	20712276	Venezolano	Español	Ingeniero	Anaco Fitness	0414-8171811	0414-8171811	danielagianmanco@gmail.com	\N
10	Marisol del Valle	Prado Carroz	1983-05-02	15.658.449	Venezolano	Español, Ingles	Ingeniero Quimico	Evergreen	0282-4007000	0424-8894264	marisolprado@hotmail.com	\N
11	Johanna Carolina 	García Orta	1980-10-31	14.552.938	Venezolano	Español, Ingles	Lcda. en Educación	CEIA		0424-8378325	ortamjg@gmail.com	\N
12	Oscari Yeanlesthky	Vidal Marchan	1981-02-06	21.249.278	Venezolano	Español	Administradora	Isomaluno, C.A	0281-7179634	0424-9179534	oscarvidal2@gmail.com	\N
13	Fariba Alejandra	Trias Guevara	1985-10-26	17787823	Venezolano	Español	Comerciante	Cicpetrol		0424-8400183	uluknon.mom@gmail.com	\N
14	Luisa Teresa	Mata Carvajal	1981-06-19	14.804.213	Venezolano	Español	Abogado	The Wolf Express		0424-8100330	asiulmata@hotmail.com	\N
15	Ariadna 	Mota de Medori	1985-10-15	16667082	Venezolano	Español, Ingles	Ingenirero Civil	CEIA		0416-6821072	ariannet.85@gmail.com	\N
16	Dormeris Coromoto	Velásquez Nuñez	1982-02-28	15564748	Venezolano	Español, Ingles	TSU en Informática	CEIA		0414-8374690	dorvelasquez@ceiak12.org	\N
17	Rosa Victoria	Figuera Gutierrez	1991-01-21	20634001	Venezolano	Español	Ama de casa	En casa		0412-1926040	victoriafiguera.6@gmail.com	\N
18	Roraima Mercedes	Wever Cedeño	1983-03-14	15803584	Venezolano	Español	Ingeniero	Nitor Metal C.A	0282-4246360	0424-2471250	rorawc@gmail.com	\N
19	Edith Mercedes	Zamora Díaz	0974-08-31	12074655	Venezolano	Español	Ingeniero	E/S Anaco 2 C.A		0414-8397199	echifz@hotmail.com	\N
20	Johanna Carolina 	Monasterios De Figuera	1975-12-18	12504097	Venezolano	Español	Contador	Gomo C.A	0282-4253135	0424-8593858	joha_gomoca@hotmail.com	\N
21	Daria	Mayhoub	1982-12-10	84563595	Venezolano	Arabe, Español	Ama de casa	Casa		0412-8659300	dariamayhoub@gmail.com	\N
22	Cirana del Valle	Gómez Cova	1980-03-12	15083846	Venezolano	Español	Odontologa	Centro Odontológico Anzoátegui	0282-4255896	0424-8642014	draciranagomez@gmail.com	\N
23	Sabrina José	Ruiz Trifiro	1981-12-28	20712841	Venezolano	Español	Abogado	Particular		0414-8423272	sabrinatrifiro@gmail.com	\N
24	Carolina de Lourdes	Caraballo González	1981-02-11	15064778	Venezolano	Español	Ingeniero Quimico	PDVSA		0414-0898537	caraballocd@gmail.com	\N
26	Maria José	Galea Galea	1991-03-18	19869275	Venezolano	Español	Comerciante	Independiente		0424-8310216	maria28galea_@hotmail.com	\N
27	Ligia Lau Ping	Wo Wu	1981-09-08	16171708	Venezolano	Mandarin, Ingles	Comerciante	Refrielectric		0414-8168168	superfavorito@hotmail.com	\N
28	Gleidys José	Barrancas Rivero	1988-10-26	18300465	Venezolano	Español	Ingeniero Industrial	Particular		0424-8810633	gleidysbr@gmail.com	\N
29	Anggie Josefina	Villalobos Mata	1982-10-26	16571735	Venezolano	Español	Ingeniero Petrolero	VPG,C.A		0424-8337673	villalobosanggie@gmail.com	\N
30	Samira Josefina	El Souky Acosta	1987-05-04	17421205	Venezolano	Español	Ama de casa	Casa		0412-1454656	samirasouki546@hotmail.com	\N
31	Leanys	Luces Martínez	1979-06-11	14553160	Venezolano	Español	Comerciante	Servicios Tila, C.A		0414-1164560	leanysluces@hotmail.com	\N
32	Damarys	Quintero Abreu	1979-12-11	14400075	Venezolano	Español	Administradora	CEIA		0414-1985439	abreudamarys@hotmail.com	\N
33	Kary Isabel	Méndez Goméz	1982-10-30	15211146	Venezolano	Español	Administradora	Oleorca	0282-4255458	0424-8821309	kimg.maru@gmail.com	\N
34	Merling	Barrios Bellorin	1978-03-23	13292830	Venezolano	Español	Administradora	Security Work C.A		0414-7852565	admin.legalsecurity@gmail.com	\N
37	Martha Damarys	Naar Latchman	1978-02-19	13216036	Venezolano	Español	Ingeniero Geologo	Serv y Sum. Demeter C.A	0412-8751028	0414-8179380	naarm@hotmail.com	\N
38	Raiza Maria	Rojas de Milan	1977-09-30	13169961	Venezolano	Español	Ingenirero Civil	Particular		0414-8388630	raizarojasm@gmail.com	\N
39	Vanessa Carolina	Herrera Monsalve	1986-05-07	18206822	Venezolano	Español	Abogado	Particular		0424-8960497	vanessa@hotmail.com	\N
41	Oscari Yeanlesthky	Vidal Marchan	1981-02-06	21249278	Venezolano	Español	Administradora	Isomakomao, C.A		04249179534	oscarvidal2@gmail.com	\N
43	 Mo Liting 	Mok Joa	1970-02-17	24229589	Venezolano	Español, Mandarin	Comerciante	San Popular		0414-8396576	liting_molejoa@hotmail.com	\N
44	Janie Claret 	Brancato	1975-07-18	12255937	Venezolano	Español, Italiano	Comerciante	Particular		0414-1990936	janiebrancato@hotmail.com	\N
42	Wilmary Liliana	Vegas Leon	1973-12-24	12410865	Venezolano	Español	Abogado	Evergreen Service, C.A	0412-1922727	0414-3107596	wilmaryvegas2@gmail.com	\N
46	Dalitzis Alexandra	Perales Cairo	1972-01-03	10938882	Venezolano	Español	T.S.U	Casa		0414-3833765	dalyperales72@hotmail.com	\N
47	Maryori de Jesús	Díaz	1981-09-29	15740414	Venezolano	Español	Lic. Químico	PDVSA		0426-5151416	maryoridz@gmail.com	\N
48	Jenny	Ezeiza Brito	1976-05-20	13030605	Venezolano	Español	Medico	Clinica Proinsa		0414-8394850	jennyezeiza@gmail.com	\N
49	Merileny Coromoto	Azuaje Pérez	1972-10-01	10319788	Venezolano	Español	Lic. Educación	CEIA		0412-0917585	mery.azuaje@hotmail.com	\N
50	Lorely	Rondón Ruiz	1981-12-08	15717553	Venezolano	Español	Ingenirero Civil	Particular		0424-8467409	rondonlorely@hotmail.com	\N
51	Xiao Zuly	Cen	1978-03-19	83848011	Extranjero	Español, Mandarin	Comerciante	Mi Pueblo	0412-1937607	0424-8527691	selinahecen@hotmail.com	\N
25	Liliana Trinidad	Medina Ordaz	1983-11-10	15803527	Venezolano	Español	Abogado	Riavi,C.A		0424-8765513	riavica22@gmail.com	\N
\.


--
-- Data for Name: padres; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.padres (padre_id, padre_nombre, padre_apellido, padre_fecha_nacimiento, padre_cedula_pasaporte, padre_nacionalidad, padre_idioma, padre_profesion, padre_empresa, padre_telefono_trabajo, padre_celular, padre_email, id) FROM stdin;
27	David Wayne	Randolph	1955-02-10	84586914	Extranjero	Español, Inglés	Comerciante	Independiente		0412-1842882	rchvck44@aol.com	\N
28	Xiao Peng	Chau	1977-12-02	82035642	Extranjero	Mandarin, Español	Comerciante	Refrielectric		0412-1945962	eduardxpch@hotmail.com	\N
37	Wenqing	Lu	1963-09-26	83858293	Extranjero	Mandarin	Comerciante	Supermercado Allyson		04120861611	luw0624@gmail.com	\N
41	Wu Ze	Li	1977-04-23	3512440	Extranjero	Mandarin, Español	Comerciante	Automercado Yili 2013		0412-0911237	liwuze5813@icloud.com	\N
45	Ghassan 	Abou Eid	1974-10-15	81985370	Extranjero	Español, Arabe	Comerciante	Piaza Cafe Club		0414-8397262	gassanaboueid@hotmail.com	\N
3	Neil Rafaél	Marín Medina	1969-07-17	10060256	Venezolano	Español	Analista - Control Panel	PDVSA	0426-2840775	0426-2840775	bdoper69@gmail.com	\N
5	Ángel Leonardo	Rondón	1986-01-27	17021312	Venezolano	Español	Ingeniero	Independiente		0414-8997331	angelrondonm@gmail.com	\N
6	Darwin José	Pino Pinto	1980-06-01	14516601	Venezolano	Español	Licenciado	Empresas Polar		0424-8339734	darwinjpino@gmail.com	\N
8	José Luis	Ferreira Galarraga	1982-05-31	16451837	Venezolano	Español	TSU	Sistema de Inyección		0412-1903905	jlferreiragalarra@hotmail.com	\N
4	Legnis Junior	Gómez Ortiz	1985-09-09	18079860	Venezolano	Español	Ing. Civil / Admin	Serconleca		0424-8350450	legnisjuniorgomezortiz@gmail.com	\N
9	Nilio Regino	Salazar Orocopey	1970-12-13	11003046	Venezolano	Español	Médico	Centro de especialidades medicas		0414-8395580	nilio_salazar@hotmail.com	\N
1	David Alejandro	Velásquez Ortíz	1989-04-30	18887901	Venezolano	Español, Inglés	Bussines Marketing	Anaco Fitness	0414-8157843	0414-8157843	anacosupply@gmail.com	\N
10	Antoine	Batour 	1970-11-19	84.394.417	Venezolano	Español, Inglés	Ingeniero en computadoras	Evergreen	0282-4007000	0424-8459073	antoine.batour@hotmail.com	\N
11	Niel Rafael 	Marín Medina	1969-07-16	10.060.256	Venezolano	Español	Panelista	PDVSA		0426-2840775	ortamjg@gmail.com	\N
12	Eduardo José	Natera Balboa	1111-11-11	14.119.137	Venezolano	Español	Ingeniero Industrial	Isomaluno, C.A		0424-9179534	oscarvidal2@gmail.com	\N
13	Rodolfo José	Uluknon Colina	1972-09-20	11691935	Venezolano	Español	Ingeniero	Cicpetrol		0424-8400182	ruluknon@gmail.com	\N
14	Carlos Enrique	Gil Sifontes	1981-06-26	16.077.149	Venezolano	Español, Inglés	Gerente	The Wolf Company		0414-0346005	cgthewolfcompanyinc@gmail.com	\N
15	Legnis Junior	Gómez Ortiz	1985-09-09	18077860	Venezolano	Español	Ingeniero Civil	Serconleca		0424-8350450	legnisjuniorgomezortiz@gmail.com	\N
16	Victor	Medori Mendoza	1985-03-20	17732343	Venezolano	Español	Ingeniero Civil	PDVSA		0426-5828624	victormedori@gmail.com	\N
17	Rafael Ignacio	Perez Delgado	1971-10-06	8263035	Venezolano	Español	TSU	Independiente		0414-8376296	rafaelperez739@hotmail.com	\N
18	Yorman Alexander	Pico Lucas	1986-07-18	17168406	Venezolano	Español	Comerciante	Polar		0424-8339734	yorman1802@gmail.com	\N
19	Rodolfo Daniel	Peñaranda Rodríguez	1979-02-15	13636194	Venezolano	Español	Asesor de Seguridad	Mantenimiento IPD		0412-1852741	rodapero12@gmail.com	\N
20	Luis Carlos	Flores Perez	1975-03-01	12074217	Venezolano	Español	Ingeniero	Rufaserca		0414-8385115	luiscarf@gmail.com	\N
21	Ronald Michael	Gómez Fernandez	1973-11-12	12024788	Venezolano	Español, Inglés	Ingeniero	Gomo C.A	0282-4253135	0416-6821069	ronadlgomez-1@hotmail.com	\N
22	Nasr	Nizek Zugbi	1981-06-10	18644131	Venezolano	Arabe, Español	Comerciante	Alisito		0412-8659300	nasernizek@gmail.com	\N
23	Carlos Rafael	Rondón Prieto	1983-04-18	16064072	Venezolano	Español	Comerciante	Metal Solution Corp		0414-2797638	rondonprieto@outlook.com	\N
24	Franco José	Zivolo Parucho	1983-11-09	16173625	Venezolano	Español	Ingeniero	Veneflu		0414-8388710	franco_zi@hotmail.com	\N
25	Eduardo José	Salazar Cordova	1966-05-04	8453517	Venezolano	Español	Ingeniero Industrial	Comevesa Guaya Fina		0414-0898572	comevesa@gmail.com	\N
26	Gabriel Enrrique	López Medina	1983-03-01	16171420	Venezolano	Español	Comerciante	Riavi, C.A		0414-8386396	gabrielelopez01@gmail.com	\N
29	Angel Alexander	Mendoza González	1989-01-15	18594532	Venezolano	Español	Contratista	Sebromenca		0414-7905033	gleidysbr@gmail.com	\N
30	Andres Alejandro	Bello Sotillo	1985-01-24	16649386	Venezolano	Español	Ingeniero Petrolero	VPG,C.A		0414-7869004	villalobosanggie@gmail.com	\N
31	Antonio José	Campos Alvarado	1988-09-19	19774729	Venezolano	Español	Empresario	Corporacion Gems	0282-4247692	0412-4475005	camposa.gems@icloup.com	\N
32	Johannes Alfonso	Alfonzo Franco	1975-11-09	12730096	Venezolano	Español	Supervisor de Obras	Servicios Tila, C.A		0424-8505329	jaffranko@hotmail.com	\N
33	Gabriel	Nuñez Castillo	1980-11-07	15014149	Venezolano	Español	Bussines	Particular		0414-9843616	nunez_castillo@hotmail.com	\N
34	Dunian José	Muñoz Campos	1981-01-13	15064427	Venezolano	Español	Ingeniero	Oleorca	0282-4255458	0424-8661878	dunianj13@gmail.com	\N
35	Juan 	González Griman	1975-03-10	13310178	Venezolano	Español	Ingeniero	Security Work C.A		0414-8396308	gererncia@swsecuritygrovys.com	\N
36	Wei Xiong	Wu	1972-05-06	13409388	Venezolano	Mandarin	Comerciante	Particular		0412-8598952	meizhulin14@gmail.com	\N
38	José Javier	Lima Cedeño	1970-09-15	10302305	Venezolano	Español	Ingeniero Geologo	Serv y Sum. Demeter C.A		0414-3173694	lima.jose5@gmail.com	\N
39	Elio  Antonio	Milan Ordoñez	1975-01-25	12074514	Venezolano	Español, Inglés	Ingeniero Civil	Tubo Servicios de Oriente		0414-8388630	tony.milantso@gmail.com	\N
40	Carlos Augusto	Lopez Brusco	1988-07-25	17786739	Venezolano	Español	Comerciante	Serpego	0828-4241283	0414-0871606	carlosaugustolopezbrusco@gmail.com	\N
43	Jianguan 	Mo	1969-11-29	24229590	Venezolano	Español, Mandarin	Comerciante	San Popular		0414-8387040	liting_molejoa@hotmail.com	\N
44	Juan 	Graterol	1974-09-13	14122591	Venezolano	Español, Inglés	Comerciante	Particular		0414-1990936	janiebrancato@hotmail.com	\N
42	Marcos Eduardo	Guerra Pinto	1973-01-22	11311831	Venezolano	Español, Inglés	Lic. en Computación	Toyoriente, C.A		04142618112	mguerra2273@gmail.com	\N
46	Carlos Enrique	López	1961-11-27	8467116	Venezolano	Español	Empresario	Serpego		0414-8384699	dalyperales72@hotmail.com	\N
47	Wilmer Alejandro	Calderón	1981-12-24	15123431	Venezolano	Español	Ing. Electrónico	Particular		0426-5151416	wacalderon@gmail.com	\N
48	Tomas 	Guazz Avila	61977-09-02	12819754	Venezolano	Español	Ingeniero	PDVSA		0416-8939280	tomasguazz@hotmail.com	\N
49	Humberto Enrique	Colmenares Pérez	1972-05-15	10319079	Venezolano	Español	TSU en Instrumentación	Mr. Cool		0412-0917584	hcp.colmenares@hotmail.com	\N
50	Manuel Eligio	Torrealba Milano	1978-03-11	14307755	Venezolano	Español	Ganadero	Agrp. La Troza, C.A		0424-8551578	rondonlorely@hotmail.com	\N
51	Rujun	He	1972-10-03	83846011	Venezolano	Español, Mandarin	Comerciante	Mi Pueblo		0412-1883332	selinahecen@hotmail.com	\N
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
15	20016	2	Music Teacher - Grade Pk3-12	N/A
11	20012	2	ESL - Secondary	N/A
26	20022	2	Social Studies - Grade 6-12	N/A
28	20020	2	Art Teacher - Grade Pk3-12	N/A
19	20021	2	Math teacher - Grade 10-12	N/A
17	20019	2	Spanish teacher - Grade 7-12	N/A
13	20014	2	Daycare, Pk-3	Prekinder 3
4	20006	2	Bussiness Manager	N/A
2	20005	2	Director	Grade 12
9	20007	2	Administrative Assistant	N/A
20	20023	2	Pk-4, Kindergarten	Kindergarten
16	20018	2	Librarian	N/A
18	20017	2	Language Arts - Grade 6-9	N/A
30	20040	2	Vigilancia	N/A
31	20032	2	Mantenimiento	N/A
32	20041	2	Vigilancia	N/A
33	20038	2	Vigilancia	N/A
23	20026	2	PE - Grade Pk3-12	N/A
27	20043	2	Psychology	N/A
24	20028	2	Grade 4	Grade 4
34	20042	2	Vigilancia	N/A
6	20009	2	Science Teacher - Grade 6-12	Grade 8
36	20030	2	Mantenimiento	N/A
42	20036	2	Otro	N/A
25	20029	2	IT Teacher - Grade Pk-3-12	N/A
21	20024	2	Grade 5	Grade 5
12	20013	2	ESL - Elementary	N/A
35	20033	2	Mantenimiento	N/A
37	20035	2	Mantenimiento	N/A
10	20011	2	Spanish teacher - Grade 1-6	N/A
22	20025	2	Grade 3	Grade 3
38	20031	2	Mantenimiento	N/A
39	20037	2	Vigilancia	N/A
3	20008	2	IT Manager	N/A
40	20027	2	Daycare, Pk-3 (Asist.)	N/A
41	20034	2	Mantenimiento	N/A
14	20015	2	Pk-4, Kindergarten (Asist.)	N/A
29	20039	2	Maintenance Coordinator	N/A
\.


--
-- Data for Name: profesores; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profesores (id, nombre_completo, cedula, telefono, email, profesor_id, categoria) FROM stdin;
20016	Andersón David Delgado Linarez	29.962.933	0416-4941839	andymalckovich14@gmail.com	\N	Staff Docente
20012	Andrea Estefani Adaubi Agho Milano	33.144.776	0412-8412018	aagho1802@gmail.com	\N	Staff Docente
20022	Angela Virginia Martinez Pereira	24.831.596	0424-8630824	angelavmp@gmail.com	\N	Staff Docente
20020	Ariadna Tibisay Mota de Medori	16.667.082	0416-6821072	ariannet.85@gmail.com	\N	Staff Docente
20021	Carlos Alberto Marín 	19.675.115	0412-8342313	profcarlosm88@gmail.com	\N	Staff Docente
20019	Carmen Desirée González Martínez	5.326.250	0414-8386293	desi-gonzalez@hotmail.com	\N	Staff Docente
20014	Clara Alicia Cedeño Adams	9.818.373	0424-8628079	feelmecaca@hotmail.com	\N	Staff Docente
20006	Damarys Quintero	11111112	0414-1985439	dquintero@ceiak12.org	\N	Staff Administrativo
20005	Daniela Medina	19.941.342	0426-7859564	dmedina@ceiak12.org	\N	Staff Administrativo
20007	Dormeris Velásquez	15.564.748	0414-8374690	dorvelazquez@ceiak12.org	\N	Staff Administrativo
20023	Elbani María Pérez Moya	25.567.800	0424-8511085	elbanyjecp@gmail.com	\N	Staff Docente
20018	Erimar de Jesús González Sosa	28.476.036	0412-1935650	erimargonzalez@hotmail.com	\N	Staff Docente
20017	Johanna Carolina García Orta	14.552.938	0424-8378325	ortamjg@gmail.com	\N	Staff Docente
20040	José de los Reyes González González	31.364.203	0412-8902116	delosreyesjg725@gmail.com	\N	Staff Vigilancia
20032	José Gregorio Lovera Fuentes	18.205.241	0426-6203475	joseglovera2018@gmail.com	\N	Staff Mantenimiento
20041	José Leonardo Rojas Romero	19.774.390	0416-2869717	joseleorojas5@gmail.com	\N	Staff Vigilancia
20038	José Miguel Frontado	12.504.468	0412-1936755	josefrontado96@hotmail.com	\N	Staff Vigilancia
20026	Joshue Kenneth Roberts Benitez	25.344.594	0412-6891041	joshueroberts@gmail.com	\N	Staff Docente
20043	Kristy Penelope Amon Gomez	16.064.373	0416-3866297	kristy_amon@hotmail.com	\N	Staff Administrativo
20028	Laura Carolina Sebastiani De Lima	14.804.498	0424-8011584	losebastiani23@gmail.com	\N	Staff Docente
20042	Leoncio Antonio Rodríguez	8467031	0416-3168559	rodriguezleoncio686@gmail.com	\N	Staff Vigilancia
20009	Luis Castañeda	26.313.224	0424-8896827	lcastaneda@ceiak12.org	\N	Staff Docente
20030	Luis José Guerra Guerra	9.816.514	0424-8499251	luisjose701@hotmail.com	\N	Staff Mantenimiento
20036	Manuel Enrique Campos González	11.774.580	0416-0980635	dayarbamar@gmail.com	\N	Staff Administrativo
20029	Marcos Adolfo Lopez Lopez	26.853.405	0426-3376169	marcosadolfo_151@hotmail.com	\N	Staff Docente
20024	Maria Daniela Pérez Alvarez	24.831.341	0414-7738879	madapeal9505@gmail.com	\N	Staff Docente
20013	Maria Francelis Caraballo González	9.813.034	0424-8286443	mariafrancelicaraballo@gmail.com	\N	Staff Docente
20033	Mariela del Valle Marcano Abanero	23.546.700	0426-3036924		\N	Staff Mantenimiento
20035	Melvis José Ortega Natera	16.581.655	0412-5949607	ortega_679@hotmail.com	\N	Staff Mantenimiento
20011	Merileny Coromoto Azuaje de Colemnares	10319788	0412-0917585	mery_azuaje@hotmail.com	\N	Staff Docente
20025	Milagros del Valle Ramos Villarroel	15.065.482	0416-6566589	ramosvmila@gmail.com	\N	Staff Docente
20031	Nohemi del Carmen Idrogo Merchán	17.998.124	0416-9978219		\N	Staff Mantenimiento
20037	Ramon Antonio Arellano Romero	6.120.228	0414-8192905	ramonarellano063@gmail.com	\N	Staff Vigilancia
20008	Roger Ramon Carvajal	11001150	0412-0813649	rcarvajal@ceiak12.org	\N	Staff Administrativo
20010	Usuario Master	11111111		admin@ceiak12.org	\N	Staff Administrativo
20027	Valentina de los Angeles Silva Salazar	30.320.753	0414-8242945	vvalentinadlangeles@gmail.com	\N	Staff Docente
20034	Yamilec Cristina Salazar Caruto	17.111.392	0412-7213677	yamilecsalazarc@gmail.com	\N	Staff Mantenimiento
20015	Diana Valentina Cabeza Caña	30.629.089	0412-1310862	dianavalentina239@gmail.com	\N	Staff Docente
20039	Elvis José González González	17.421.489	0412-8484712		\N	Staff Administrativo
\.


--
-- Data for Name: registro_vehiculos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.registro_vehiculos (id, vehiculo_id, fecha, hora_entrada, hora_salida, observaciones, registrado_por, creado_en) FROM stdin;
\.


--
-- Data for Name: salud_estudiantil; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.salud_estudiantil (id, estudiante_id, completado_por, fecha_salud, contacto_emergencia, relacion_emergencia, telefono1, telefono2, observaciones, dislexia, atencion, otros, info_adicional, problemas_oido_vista, fecha_examen, autorizo_medicamentos, medicamentos_actuales, autorizo_emergencia) FROM stdin;
1	10001	David Velásquez	2023-08-03	Daniela Ganmanco	Madre	0414-8171811	0414-8388165	Llamar a la madre	f	f	f		\N		f		t
4	10004	Maria Arciniegas	2025-07-08	Maria Arciniegas	Madre	0414-8021057			f	f	f				f		f
5	10005	Shirley Silva	2025-08-05	Shirley Silva	Madre	0414-8104369			f	f	f				t	Acetominofen	f
6	10006	Milagros Ramos	2024-08-12	José Ferreira	Padre	0416-6566559	0412-1903905		f	f	f			Ene 2024	t	Acetominofen, Antibiotico, Antialergico, Ibuprofeno, y Crema para el dolor.	t
2	10002	Johanna García	2023-09-14	Neil Marín	Padre	0426-2840775	0282-4254194	Le gusta bailar y tocar cuatro(juguete)	f	f	f			Jul 2023	t	Acetaminofén, Crema para el dolor,Antialergico ,Ibuprofeno	t
3	10003	Nathalia Bastidas	2021-10-26	Legnis Gómez	Padre	0424-8350450	0414-1989695	Llamar primero a la madre, antes de suministrar medicamentos	f	f	f			\N	f		t
7	10007	Miladys Rosas	2025-08-07	Miladys Rosas	Madre	0414-7731866			f	f	f				t	Acetominofen, Antibiotico, Antialergico, Ibuprofeno.	t
8	10008	Marisol Prado	2022-07-11	Marisol Prado	Madre	0424-8894264			f	f	f			Jun 2022	t	Crema de Antibiótico, Crema de Antialérgico 	f
9	10009	Miladys Rosas	2022-08-07	Miladys Rosas	Madre	0414-7731866			f	f	f				t		t
10	10010	Johanna García	2019-10-17	Johanna García	Madre	0282-4242679	0424-8378325		f	f	f			Oct 2019	t	Acetominofen, crema de Antibitico y crema de antialergico, Ibuprofeno	t
11	10011	Oscari Vidal 	2025-07-08	Oscari Vidal 	Madre	0424-9179534			f	f	f				t	Acetominofen	t
12	10012	Fariba Trias	2025-07-10	Fariba Trias	Madre	0424-8400183	0424-8874125		f	f	f		Usa lentes	Jul 2025	t	Acetominofen, crema Antialergica, Ibuprofeno	t
13	10013	Luisa Mata	2018-06-08	Luisa Mata	Madre	0424-8100330			f	f	f			May 2018	t	Acetominofen, crema antialergica	t
14	10014	Nathalia Bastidas	2025-07-08	Nathalia Bastidas	Madre	0414-0346333			f	f	f				f		t
15	10015	Ariadna Mota	2025-07-08	Ariadna Mota	Madre	0416-6821072	0426-5828624		f	f	f				t	Acetominofen 	t
16	10016	Dormeris Velásquez	2019-09-23	Rafael Perez	Padre	0414-8376296	0414-8386586		f	f	f				t	Acetominofen	t
17	10017	Victoria Viguera	2021-07-30	Victoria Viguera	Madre	0412-1926040			f	f	f				f		f
18	10018	Roraima Wever	2024-08-02	Roraima Wever	Madre	0424-2471250			f	t	f	Es Asmático			t	Ibuprofeno, crema Antibiotica y crema Antialérgica	t
19	10019	Edith Zamora	2025-08-05	Edith Zamora	Madre	0414-2452233	0412-8356031		f	f	f	Alergia al maní			t	Acetominofen, Ibuprofeno, crema de Antibiotico y crema de Antialergico	t
20	10020	Edith Zamora	2025-05-08	Mariela Florez	Tia	0414-2452233	0412-8356031		f	f	f				t	Acetominofen, Ibuprofeno, crema Antialergica y crema de Antibitico	t
21	10021	Johanna Monasterios	2025-08-08	Johanna Monasterios	Madre	0424-8593858			f	f	f				t	Acetominofen; Ibuprofeno	t
22	10022	Nasr Nizek	2022-08-16	Nasr Nizek	Padre	0412-8413408			f	f	f				f		f
23	10023	Cirana Gómez	2021-08-23	Carlos Rondón	Padre	0414-2797368	0414-7772561		f	f	f				f	Antialergico y Antibioticos en crema.	t
24	10024	Sabrina Ruiz	2017-07-08	Franco Zivolo	Padre	04148388710			f	f	f				f	Acetominofen y cremas Antibióticas, Ibuprofeno	t
25	10025	Carolina Caraballo	2020-08-24	Carolina Caraballo	Madre	0414-0898537			f	f	f	Rinosinositis y con frecuencia ocasiona sangrado nasal			f	Acetominofen	t
26	10026	Liliana Medina	2018-11-07	Liliana Medina	Madre	0424-8765513	0414-8386396		f	f	f				f	Acetominofen	t
27	10027	Maria Galea	2018-06-04	Maria Galea	Madre	0424-8310216			f	f	f				f	Acetominofen	f
28	10028	Ligia Wo	2025-08-19	Xiao Chau	Padre	0412-1945962			f	f	f				f	Acetominofen, Ibuprofeno, crema Antibiotica	t
29	10029	Gleidys Barrancas	2024-02-24	Gleidys Barrancas	Madre	0424-8810633	0414-7905033		f	f	f				f	Acetominofen, Ibuprofeno y crmas Antibioticas	t
30	10030	Milagros Ramos	2024-08-12	José Ferreira	Padre	0416-6566559			f	f	f				f	Acetominofen, Ibuprofeno y cremas Antibioticas	t
31	10031	Victor Medori	2019-08-12	Ariadna Mota	Madre	0416-6821072			f	f	f				f	Acetominofen	t
32	10032	Victoria Viguera	2020-09-03	Victoria Viguera	Madre	0424-8074656	0426-2802311		f	f	f				f		t
33	10033	Marisol Prado	2017-09-04	Marisol Prado	Madre	0424-8894264			f	f	f				f		f
34	10034	Luisa Mata	2018-09-18	Luisa Mata	Madre	0412-8100330			f	f	f				f	Acetominofen, Ibuprofeno, crema Antibiticas	t
35	10035	Anggie Villalobos	2016-08-10	Anggie Villalobos	Madre	0424-8337673			f	f	f				f	Acetominofen, Ibuprofeno y cremas Antibioticas	t
36	10036	Antonio Campos	2021-04-01	Antonio Campos	Padre	0412-4475005	0412-1454656		f	f	f				f		f
37	10037	Leanys Luces	2021-08-12	Leanys Luces	Madre	0414-1164560		Asmatica	f	f	f				f	Acetominofen, Ibuprofeno y crama Antibiotica	t
38	10038	Damarys Quintero	2020-07-03	Gabriel Nuñez	Padre	0414-9843616			f	f	f				f		f
39	10039	Damarys Quintero	2020-07-03	Gabriel Nuñez	Padre	0414-9843616			f	f	f				f		f
40	10040	Kary Méndez	2021-08-25	Dunian Muñoz	Padre	0424-8661878			f	f	f				f	Acetominofen, Ibuprofeno y crmas Antibioticas	t
41	10041	Merling Barrios 	2023-03-05	Merling Barrios 	Madre	0414-7852565	0414-8396308		f	f	f				f	Acetominofen, Ibuprofeno y cremas Antibioticas	t
42	10042	Meizhu Lin	2024-07-17	Meizhu Lin	Madre	0412-6940522			f	f	f			2023	f	Acetominofen, Ibuprofeno y cremas Antibioticas	t
43	10043	Caixin Huang	2024-08-21	Victor Lu	Padre	04120861611			f	f	f				f	Acetominofen, Ibuprofeno y cremas Antibioticas	f
44	10044	Martha Naar	2023-07-26	Martha Naar	Madre	0414-8179380			f	f	f			21/07/2023	f	Acetominofe,, Ibupofreno, crema Antibiotica	t
45	10045	Luisa Mata	2018-09-03	Luisa Mata	Madre	0412-0858398			f	f	f				f	Aceotminofen, Ibuprofeno y cremas Antibioticas	t
46	10046	Raiza de Milan	2020-08-24	Elio Milan	Padre	0414-8388630	0414-8388630		f	f	f				f	Acetominofen, Ibuprofeno y crema Antialergica	t
47	10047	Carlos Lopez	2019-08-15	Carlos Lopez	Padre	0414-0871606	0424-8960497		f	f	f			\N	f		t
48	10048	Hiudan Wu	2024-04-08	Hiudan Wu	Madre	0412-0911237			f	f	f				f		f
49	10049	Oscari Vidal 	2024-08-16	Oscari Vidal 	Madre	04249179534			f	f	f				f	Acetominofen	t
50	10050	Fariba Trias	2025-07-10	Fariba Trias	Madre	0424-8400183			f	f	f				f	Acetominofen, crema Antibitoca	t
51	10051	Wilmary Vega	2014-08-13	Wilmary Vega	Madre	0414-3107596	04142618112		f	f	f			\N	f		f
52	10052	Liting Mo	2008-08-12	Liting Mo	Madre	0414-8396576			f	f	f				f		f
53	10053	Janie Brancato	2021-09-02	Janie Brancato	Madre	0414-1990936			f	f	f				f	Acetominofen	f
54	10054	Ghassan Aboueid	2025-08-22	Ghassan Aboueid	Padre	0414-8397262	0426-5831254		f	f	f				f		t
55	10055	Anggie Villalobos	2025-08-22	Anggie Villalobos	Madre	0414-7869004			f	f	f				f		f
56	10056	Dalitzis Perales	2015-08-18	Dalitzis Perales	Madre	0414-3833765			f	f	f	Alergia a:Belicilina, citricos, lacteos, huevos y es diabetica.			f		f
57	10057	Maryorí Díaz	2021-07-29	Maryorí Díaz	Madre	0426-5151416			f	f	f				f	Acetominofen	t
58	10058	Jenny Ezeiza	2021-07-29	Jenny Ezeiza	Madre	0414-8394850			f	f	f				f	Acetominfen, Ibuprofeno, crema Antibitica, y Antialérgica	t
59	10059	Merileny Azuaje	2019-08-05	Humberto Colmenares	Padre	0412-0917584			f	f	f				f	Acetominofen, Ibuprofeno, crema Antibiótica y Antialérgica.	t
60	10060	Lorely Rondón	2018-09-26	Lorely Rondón	Madre	0424-8467409			f	f	f				f	Acetominofén, Ibuprofeno, crema Antibiótica y Antialérgica.	t
61	10061	Ligia Wo	2024-01-16	Xiao Chau	Padre	0424-8258668			f	f	f				f		f
62	10062	Nasr Nizek	2022-08-16	Nasr Nizek	Padre	0412-8413408			f	f	f				f		f
63	10063	Zuli Cen	2025-08-22	Zuli Cen	Madre	0424-8527691			f	f	f				f	Acetominofen 	t
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, username, password, rol, profesor_id) FROM stdin;
4	dmedina	$2y$10$zASKq913auF1o63wuPCC7OCYlmPs1AZcJq792b7H1vz4JrsOP7K3m	admin	20005
3	rcarvajal	$2y$10$0Pw3ILyQywEyp8DLHkEpSucEom5Dnk5YEBNX6766jHtrzDlCmR0OK	admin	20008
5	dquintero	$2y$10$vp0sPP.rm1J0tlp0V6bh0e17IxguzAyNXVNbkCRB6Ar3rvrw96Q4q	admin	20006
2	admin	$2y$10$azexVtZms3s6tmsbD46XKuUhopQczwZirMURs/NdBPCcUGOdEajfW	admin	\N
1	superusuario	$2y$10$vdWHEQXc6rslVqOJgqJW7.detcSoiGdYi5K9mvQ9hPKuMgOFmS4a6	master	\N
6	dorvelasquez	$2y$10$g103aL2pj4knuH.O3ndPpeLzvC.jpoSFaJzI3UPoylDQGH84QepiS	admin	20007
\.


--
-- Data for Name: vehiculos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vehiculos (id, estudiante_id, placa, modelo, autorizado) FROM stdin;
30001	10009	AAA-111	4runner	t
\.


--
-- Name: entrada_salida_staff_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.entrada_salida_staff_id_seq', 1, true);


--
-- Name: estudiante_periodo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.estudiante_periodo_id_seq', 87, true);


--
-- Name: estudiantes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.estudiantes_id_seq', 10064, false);


--
-- Name: latepass_resumen_semanal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.latepass_resumen_semanal_id_seq', 1, false);


--
-- Name: llegadas_tarde_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.llegadas_tarde_id_seq', 1, false);


--
-- Name: madres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.madres_id_seq', 51, true);


--
-- Name: padres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.padres_id_seq', 51, true);


--
-- Name: periodos_escolares_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.periodos_escolares_id_seq', 2, false);


--
-- Name: profesor_periodo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.profesor_periodo_id_seq', 42, true);


--
-- Name: profesores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.profesores_id_seq', 43, true);


--
-- Name: registro_vehiculos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.registro_vehiculos_id_seq', 1, true);


--
-- Name: salud_estudiantil_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.salud_estudiantil_id_seq', 63, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 10, true);


--
-- Name: vehiculos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vehiculos_id_seq', 30001, true);


--
-- Name: entrada_salida_staff entrada_salida_staff_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_staff
    ADD CONSTRAINT entrada_salida_staff_pkey PRIMARY KEY (id);


--
-- Name: entrada_salida_staff entrada_salida_staff_profesor_id_fecha_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_staff
    ADD CONSTRAINT entrada_salida_staff_profesor_id_fecha_key UNIQUE (profesor_id, fecha);


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
-- Name: registro_vehiculos registro_vehiculos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registro_vehiculos
    ADD CONSTRAINT registro_vehiculos_pkey PRIMARY KEY (id);


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
-- Name: vehiculos vehiculos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_pkey PRIMARY KEY (id);


--
-- Name: entrada_salida_staff entrada_salida_staff_profesor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrada_salida_staff
    ADD CONSTRAINT entrada_salida_staff_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES public.profesores(id);


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
    ADD CONSTRAINT profesor_periodo_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES public.profesores(id);


--
-- Name: registro_vehiculos registro_vehiculos_vehiculo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registro_vehiculos
    ADD CONSTRAINT registro_vehiculos_vehiculo_id_fkey FOREIGN KEY (vehiculo_id) REFERENCES public.vehiculos(id);


--
-- Name: vehiculos vehiculos_estudiante_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_estudiante_id_fkey FOREIGN KEY (estudiante_id) REFERENCES public.estudiantes(id);


--
-- PostgreSQL database dump complete
--

\unrestrict gzHia6V9aDdDQ2MMfvs9NW8hWwz0GTjjsE8z4LnAA7cniHwyEmCJ2QDrExkY1ej

