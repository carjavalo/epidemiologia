-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-03-2026 a las 14:07:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proahuv`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_carjavalo@gmail.com|127.0.0.1', 'i:1;', 1758224481),
('laravel_cache_carjavalo@gmail.com|127.0.0.1:timer', 'i:1758224481;', 1758224481),
('laravel_cache_carjavalosiste@gmail.com|127.0.0.1', 'i:1;', 1754917439),
('laravel_cache_carjavalosiste@gmail.com|127.0.0.1:timer', 'i:1754917439;', 1754917439),
('laravel_cache_carjavalosistem@mail.com|127.0.0.1', 'i:1;', 1754516369),
('laravel_cache_carjavalosistem@mail.com|127.0.0.1:timer', 'i:1754516369;', 1754516369),
('laravel_cache_carjavalosistema@gmail.com|127.0.0.1', 'i:1;', 1754515328),
('laravel_cache_carjavalosistema@gmail.com|127.0.0.1:timer', 'i:1754515328;', 1754515328);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_procedimientos`
--

CREATE TABLE `deta_procedimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_procedi` bigint(20) UNSIGNED NOT NULL,
  `Cod_Episodio` int(11) DEFAULT NULL,
  `Cod_Sala` int(11) DEFAULT NULL,
  `Nom_Sala` varchar(100) DEFAULT NULL,
  `Num_Cama` varchar(20) DEFAULT NULL,
  `F_Ingreso` datetime DEFAULT NULL,
  `Cod_Eps` varchar(20) DEFAULT NULL,
  `Nom_Eps` varchar(100) DEFAULT NULL,
  `Hist_Clinica` int(11) DEFAULT NULL,
  `Tipo_Ident` varchar(5) DEFAULT NULL,
  `Num_Ident` varchar(20) DEFAULT NULL,
  `Edad` int(11) DEFAULT NULL,
  `Sexo` char(1) DEFAULT NULL,
  `Servicio` varchar(100) DEFAULT NULL,
  `Estado` varchar(50) DEFAULT NULL,
  `Medico_Trata` varchar(100) DEFAULT NULL,
  `Cod_Diag` varchar(10) DEFAULT NULL,
  `CIE10` varchar(10) DEFAULT NULL,
  `Diagnostico` varchar(255) DEFAULT NULL,
  `Antimicrobiano` varchar(100) DEFAULT NULL,
  `Cantidad` varchar(50) DEFAULT NULL,
  `Presentacion` varchar(50) DEFAULT NULL,
  `Via_Aplicacion` varchar(50) DEFAULT NULL,
  `Tiem_Horas` varchar(50) DEFAULT NULL,
  `Dias_Antibioticos` varchar(50) DEFAULT NULL,
  `Fec_Sumistro` date DEFAULT NULL,
  `Ho_Sumisnistro` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diag_infecciosos`
--

CREATE TABLE `diag_infecciosos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL COMMENT 'Descripción del diagnóstico infeccioso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `diag_infecciosos`
--

INSERT INTO `diag_infecciosos` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Neumonía Bacteriana', '2025-08-06 16:51:29', '2025-08-06 16:51:29'),
(2, 'Infección del Tracto Urinario', '2025-08-06 16:51:49', '2025-08-06 16:51:49'),
(3, 'Sepsis', '2025-08-06 16:51:58', '2025-08-06 16:51:58'),
(4, 'Meningitis Bacteriana', '2025-08-06 16:52:08', '2025-08-06 16:52:08'),
(5, 'Endocarditis Infecciosa', '2025-08-06 16:52:13', '2025-08-06 16:52:13'),
(6, 'Tuberculosis Pulmonar', '2025-08-06 16:52:31', '2025-08-06 16:52:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encabezados_procedimientos`
--

CREATE TABLE `encabezados_procedimientos` (
  `id_procedimiento` bigint(20) UNSIGNED NOT NULL,
  `fecha_procedimiento` datetime NOT NULL,
  `Nom_procedimiento` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `esp_tratante`
--

CREATE TABLE `esp_tratante` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL COMMENT 'Descripción de la especialidad tratante',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `esp_tratante`
--

INSERT INTO `esp_tratante` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Cardiología', '2025-08-06 15:12:47', '2025-08-06 15:12:47'),
(2, 'Neurología', '2025-08-06 15:13:07', '2025-08-06 15:13:07'),
(3, 'Gastroenterología', '2025-08-06 15:13:11', '2025-08-06 15:13:11'),
(4, 'Medicina Interna', '2025-08-06 15:13:17', '2025-08-06 15:13:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frecuencia`
--

CREATE TABLE `frecuencia` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `frecuencia`
--

INSERT INTO `frecuencia` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Diaria - Una vez al día', '2025-08-05 21:28:58', '2025-08-05 21:28:58'),
(2, 'Semanal - Una vez por semana', '2025-08-05 21:28:58', '2025-08-05 21:28:58'),
(3, 'Mensual - Una vez al mes', '2025-08-05 21:28:58', '2025-08-05 21:28:58'),
(4, 'Cada 8 horas - Tres veces al día', '2025-08-05 21:28:58', '2025-08-05 21:28:58'),
(5, 'Según necesidad - PRN (Pro Re Nata)', '2025-08-05 21:28:58', '2025-08-05 21:28:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicaciones_terapia`
--

CREATE TABLE `indicaciones_terapia` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `indicaciones_terapia`
--

INSERT INTO `indicaciones_terapia` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Des-escalar', '2025-08-11 13:04:18', '2025-08-11 13:04:18'),
(2, 'Escalar', '2025-08-11 13:04:30', '2025-08-11 13:04:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `microorganismos`
--

CREATE TABLE `microorganismos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL COMMENT 'Descripción del microorganismo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `microorganismos`
--

INSERT INTO `microorganismos` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Escherichia coli', '2025-08-06 20:24:55', '2025-08-06 20:24:55'),
(2, 'Staphylococcus aureus', '2025-08-06 20:25:20', '2025-08-06 20:25:20'),
(3, 'Streptococcus pneumoniae', '2025-08-06 20:25:27', '2025-08-06 20:25:27'),
(4, 'Pseudomonas aeruginosa', '2025-08-06 20:25:33', '2025-08-06 20:25:33'),
(5, 'Klebsiella pneumoniae', '2025-08-06 20:25:44', '2025-08-06 20:25:44'),
(6, 'Candida albicans', '2025-08-06 20:25:56', '2025-08-06 20:25:56'),
(7, 'Mycobacterium tuberculosis', '2025-08-06 20:26:03', '2025-08-06 20:26:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_05_181325_add_apellidos_to_users_table', 1),
(5, '2025_06_05_213518_create_procedimientos_table', 1),
(6, '2025_06_06_190439_rename_procedimientos_to_deta_procedimientos', 1),
(7, '2025_06_09_123310_create_encabezados_procedimientos_table', 1),
(8, '2025_06_09_123455_update_encabezados_procedimientos_structure', 1),
(9, '2025_06_09_123536_drop_and_create_encabezados_procedimientos', 1),
(10, '2025_06_09_125252_add_id_procedi_to_deta_procedimientos', 1),
(11, '2025_08_04_162457_add_profile_image_to_users_table', 1),
(12, '2025_08_04_220211_create_ind_terapia_table', 1),
(13, '2025_08_05_155815_create_sis_internacional_table', 1),
(14, '2025_08_05_161343_create_frecuencia_table', 1),
(15, '2025_08_05_164132_add_foto_to_users_table', 2),
(16, '2025_08_06_090154_create_pantimicrobiano_table', 3),
(17, '2025_08_06_100639_create_esp_tratante_table', 4),
(18, '2025_08_06_113106_create_tip_muestras_table', 5),
(19, '2025_08_06_114514_create_diag_infecciosos_table', 6),
(20, '2025_08_06_135541_create_resultados_table', 7),
(21, '2025_08_06_151837_create_microorganismos_table', 8),
(22, '2025_08_06_164240_create_tratamientos_table', 9),
(23, '2025_08_19_102852_create_perfiles_table', 10),
(24, '2025_08_19_135125_create_plantillas_observaciones_table', 10),
(25, '2025_08_19_141710_remove_unico_from_plantillas_observaciones_table', 10),
(26, '2025_09_18_160156_create_procedimiento_excels_table', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pantimicrobiano`
--

CREATE TABLE `pantimicrobiano` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pantimicrobiano`
--

INSERT INTO `pantimicrobiano` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(4, 'Anti/bacteriano', '2025-08-06 14:15:48', '2025-08-06 14:15:48'),
(5, 'Anti/micotico', '2025-08-06 14:17:16', '2025-08-06 14:17:16'),
(6, 'Anti/Viral', '2025-08-06 14:18:03', '2025-08-06 14:18:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantillas_observaciones`
--

CREATE TABLE `plantillas_observaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procedimiento_excels`
--

CREATE TABLE `procedimiento_excels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mes` varchar(50) DEFAULT NULL,
  `fecha_intervencion` date DEFAULT NULL,
  `sala` varchar(100) DEFAULT NULL,
  `cama` varchar(50) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `id_eps` varchar(50) DEFAULT NULL,
  `eps` varchar(200) DEFAULT NULL,
  `hc` varchar(50) DEFAULT NULL,
  `tipo_id` varchar(10) DEFAULT NULL,
  `id_paciente` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `apellido_2` varchar(100) DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `cie_10` varchar(20) DEFAULT NULL,
  `diagnosticos` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `antimicrobianos` varchar(200) DEFAULT NULL,
  `via_administracion` varchar(100) DEFAULT NULL,
  `dosis_suministrada` varchar(100) DEFAULT NULL,
  `sistema_internacional_unidades` varchar(100) DEFAULT NULL,
  `frecuencia` varchar(100) DEFAULT NULL,
  `perfil_antimicrobiano` varchar(200) DEFAULT NULL,
  `especialidad_tratante` varchar(200) DEFAULT NULL,
  `diagnostico_infeccioso` text DEFAULT NULL,
  `dosis_adecuada` varchar(10) DEFAULT NULL,
  `fecha_finalizacion_antibiotico` date DEFAULT NULL,
  `tiempo_tratamiento` varchar(100) DEFAULT NULL,
  `duracion_adecuada` varchar(10) DEFAULT NULL,
  `toma_cultivo_previo` varchar(10) DEFAULT NULL,
  `fecha_muestra` date DEFAULT NULL,
  `tipo_muestra` varchar(200) DEFAULT NULL,
  `resultados` text DEFAULT NULL,
  `microorganismo` varchar(200) DEFAULT NULL,
  `perfil` varchar(200) DEFAULT NULL,
  `solicitudes_pruebas_especiales` text DEFAULT NULL,
  `oportunidad_reporte` varchar(200) DEFAULT NULL,
  `indicacion_terapia` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `valoracion_infectologia_grupo1` varchar(10) DEFAULT NULL,
  `valoracion_infectologia_uci` varchar(10) DEFAULT NULL,
  `fecha_valoracion` date DEFAULT NULL,
  `ajuste_prescripcion` varchar(10) DEFAULT NULL,
  `adherencia_intervencion_proa` varchar(10) DEFAULT NULL,
  `adherencia_guias` varchar(10) DEFAULT NULL,
  `porque_no_adherente` text DEFAULT NULL,
  `caso_cerrado` varchar(10) DEFAULT NULL,
  `mortalidad` varchar(10) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `archivo_origen` varchar(255) DEFAULT NULL,
  `fecha_importacion` timestamp NULL DEFAULT NULL,
  `usuario_importacion` bigint(20) UNSIGNED DEFAULT NULL,
  `procesado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados`
--

CREATE TABLE `resultados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL COMMENT 'Descripción del resultado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `resultados`
--

INSERT INTO `resultados` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Positivo', '2025-08-06 19:01:15', '2025-08-06 19:01:15'),
(2, 'Negativo', '2025-08-06 19:01:34', '2025-08-06 19:01:34'),
(3, 'Indeterminado', '2025-08-06 19:01:40', '2025-08-06 19:01:40'),
(4, 'Normal', '2025-08-06 19:01:45', '2025-08-06 19:01:45'),
(5, 'Anormal', '2025-08-06 19:01:51', '2025-08-06 19:01:51'),
(6, 'Reactivo', '2025-08-06 19:01:56', '2025-08-06 19:01:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('o9jozagCAolnEASGZ1NU2sZLo17PWH4LmNLfEhf0', 1, '192.168.2.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNHN4MVA1aHRXTHEwMDZwY2dBTXVkWVJrc1VERnA2Q0lPODNFc29USSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xOTIuMTY4LjIuMjAwOjgwMDMvdXNlcnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1773061346);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sis_internacional`
--

CREATE TABLE `sis_internacional` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sis_internacional`
--

INSERT INTO `sis_internacional` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(6, 'Miligramos', '2025-08-11 13:06:06', '2025-08-11 13:06:06'),
(7, 'Gramos', '2025-08-11 13:06:19', '2025-08-11 13:06:31'),
(8, 'Unidades Internacionales', '2025-08-11 13:06:50', '2025-08-11 13:07:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tip_muestras`
--

CREATE TABLE `tip_muestras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) NOT NULL COMMENT 'Descripción del tipo de muestra',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tip_muestras`
--

INSERT INTO `tip_muestras` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Sangre', '2025-08-06 16:37:45', '2025-08-06 16:37:45'),
(2, 'Orina', '2025-08-06 16:38:06', '2025-08-06 16:38:06'),
(3, 'Heces', '2025-08-06 16:38:11', '2025-08-06 16:38:11'),
(4, 'Esputo', '2025-08-06 16:38:16', '2025-08-06 16:38:16'),
(5, 'Líquido Cefalorraquídeo', '2025-08-06 16:38:22', '2025-08-06 16:38:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE `tratamientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(250) NOT NULL COMMENT 'Descripción del tratamiento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tratamientos`
--

INSERT INTO `tratamientos` (`id`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Amoxicilina 500mg cada 8 horas por 7 días para infección del tracto respiratorio superior', '2025-08-06 21:48:17', '2025-08-06 21:48:17'),
(2, 'Ciprofloxacina 250mg cada 12 horas por 10 días para infección del tracto urinario', '2025-08-06 21:48:41', '2025-08-06 21:48:41'),
(3, 'Azitromicina 500mg el primer día, luego 250mg diarios por 4 días para neumonía atípica', '2025-08-06 21:48:46', '2025-08-06 21:48:46'),
(4, 'Vancomicina 1g IV cada 12 horas para infección por MRSA', '2025-08-06 21:48:51', '2025-08-06 21:48:51'),
(5, 'Fluconazol 150mg dosis única para candidiasis vaginal', '2025-08-06 21:48:58', '2025-08-06 21:48:58'),
(6, 'Ceftriaxona 1g IV cada 24 horas por 14 días para meningitis bacteriana', '2025-08-06 21:49:03', '2025-08-06 21:49:03'),
(7, 'Metronidazol 500mg cada 8 horas por 7 días para infección intraabdominal', '2025-08-06 21:49:09', '2025-08-06 21:49:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `apellido1` varchar(100) DEFAULT NULL,
  `apellido2` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `apellido1`, `apellido2`, `email`, `email_verified_at`, `foto`, `password`, `profile_image`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Carlos Jairton', 'Valderrama', 'Orobio', 'carjavalosistem@gmail.com', NULL, '1758211562_68cc2deabdffb.jpg', '$2y$12$LXimAfL.jvNqpe6sBIpRlO/sfqNSJMnL1Qo7r3r5LAz65uwahrlPa', NULL, NULL, '2025-08-05 21:33:32', '2025-09-18 16:06:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `deta_procedimientos`
--
ALTER TABLE `deta_procedimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deta_procedimientos_id_procedi_foreign` (`id_procedi`);

--
-- Indices de la tabla `diag_infecciosos`
--
ALTER TABLE `diag_infecciosos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diag_infecciosos_descripcion_index` (`descripcion`);

--
-- Indices de la tabla `encabezados_procedimientos`
--
ALTER TABLE `encabezados_procedimientos`
  ADD PRIMARY KEY (`id_procedimiento`);

--
-- Indices de la tabla `esp_tratante`
--
ALTER TABLE `esp_tratante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `esp_tratante_descripcion_index` (`descripcion`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `frecuencia`
--
ALTER TABLE `frecuencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `frecuencia_descripcion_unique` (`descripcion`);

--
-- Indices de la tabla `indicaciones_terapia`
--
ALTER TABLE `indicaciones_terapia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `microorganismos`
--
ALTER TABLE `microorganismos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `microorganismos_descripcion_index` (`descripcion`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pantimicrobiano`
--
ALTER TABLE `pantimicrobiano`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plantillas_observaciones`
--
ALTER TABLE `plantillas_observaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `procedimiento_excels`
--
ALTER TABLE `procedimiento_excels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `procedimiento_excels_fecha_intervencion_procesado_index` (`fecha_intervencion`,`procesado`),
  ADD KEY `procedimiento_excels_archivo_origen_index` (`archivo_origen`),
  ADD KEY `procedimiento_excels_usuario_importacion_foreign` (`usuario_importacion`);

--
-- Indices de la tabla `resultados`
--
ALTER TABLE `resultados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resultados_descripcion_index` (`descripcion`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `sis_internacional`
--
ALTER TABLE `sis_internacional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sis_internacional_descripcion_unique` (`descripcion`);

--
-- Indices de la tabla `tip_muestras`
--
ALTER TABLE `tip_muestras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tip_muestras_descripcion_index` (`descripcion`);

--
-- Indices de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tratamientos_descripcion_index` (`descripcion`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `deta_procedimientos`
--
ALTER TABLE `deta_procedimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `diag_infecciosos`
--
ALTER TABLE `diag_infecciosos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `encabezados_procedimientos`
--
ALTER TABLE `encabezados_procedimientos`
  MODIFY `id_procedimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `esp_tratante`
--
ALTER TABLE `esp_tratante`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `frecuencia`
--
ALTER TABLE `frecuencia`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `indicaciones_terapia`
--
ALTER TABLE `indicaciones_terapia`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `microorganismos`
--
ALTER TABLE `microorganismos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `pantimicrobiano`
--
ALTER TABLE `pantimicrobiano`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plantillas_observaciones`
--
ALTER TABLE `plantillas_observaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `procedimiento_excels`
--
ALTER TABLE `procedimiento_excels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resultados`
--
ALTER TABLE `resultados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sis_internacional`
--
ALTER TABLE `sis_internacional`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tip_muestras`
--
ALTER TABLE `tip_muestras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `deta_procedimientos`
--
ALTER TABLE `deta_procedimientos`
  ADD CONSTRAINT `deta_procedimientos_id_procedi_foreign` FOREIGN KEY (`id_procedi`) REFERENCES `encabezados_procedimientos` (`id_procedimiento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `procedimiento_excels`
--
ALTER TABLE `procedimiento_excels`
  ADD CONSTRAINT `procedimiento_excels_usuario_importacion_foreign` FOREIGN KEY (`usuario_importacion`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
