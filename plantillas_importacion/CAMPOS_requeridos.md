# Campos requeridos para importar datos históricos

Este documento describe **exactamente** los campos que el aplicativo espera en cada archivo de texto, en el **orden** correcto. Entrega este documento (y las plantillas de ejemplo) a la persona que va a extraer los datos.

Se necesitan **dos archivos de texto**:

1. `epidemiologia.txt` — resultados de microbiología / epidemiología.
2. `proa.txt` — procedimientos y antibióticos (PROA).

> ⚠️ **Clave para que los datos se relacionen:** un paciente mostrará su bloque **PROA** solo si su **número de documento (cédula)** y/o su **historia clínica** son **los mismos** en los dos archivos. Pídeles que usen exactamente el mismo documento e historia en ambos.

---

## 1. Archivo de EPIDEMIOLOGÍA (`epidemiologia.txt`)

- **Separador de columnas: TABULACIÓN (Tab).**
- Una fila por cada **microorganismo / cultivo** (un paciente puede tener varias filas).
- Orden fijo de **16 columnas**:

| # | Columna | Descripción / formato | Obligatorio |
|---|---------|-----------------------|:---:|
| 1 | Información del paciente | Nombre completo. Puede venir como `APELLIDOS NOMBRES (documento - dd/mm/aaaa)`; el sistema recorta lo que está entre paréntesis | Sí |
| 2 | ID del paciente | Documento / cédula. **Si va vacío, la fila se descarta** | **Sí** |
| 3 | Fecha de nacimiento | `dd/mm/aaaa` | No |
| 4 | Sexo | `Masculino` / `Femenino` (o `M` / `F`) | No |
| 5 | Código del paciente | Historia clínica | No |
| 6 | Tipo y zona | Tipo de muestra (ej. HEMOCULTIVO, UROCULTIVO) | No |
| 7 | N° de acceso | Número de reporte del laboratorio | No |
| 8 | ID cliente de la muestra | Sede / institución | No |
| 9 | Servicio | Sala / servicio (**esto arma la lista de servicios**) | No |
| 10 | Fecha | Fecha de toma de la muestra `dd/mm/aaaa` | No |
| 11 | Organismo | Nombre del microorganismo | No |
| 12 | Cultivo | Consecutivo del cultivo en la misma muestra | No |
| 13 | Fármacos sensibles | Texto (lista) | No |
| 14 | Fármacos intermedios | Texto (lista) | No |
| 15 | Fármacos resistentes | Texto (lista) | No |
| 16 | Marcadores de resistencia | Abreviaturas (ej. BLEE, KPC) | No |

**Notas:**
- Se permite (y se recomienda) una fila de encabezado; el sistema la ignora si la primera celda contiene "Información" o "Nombre".
- Mínimo deben venir las primeras 11 columnas.
- Identidad única de cada fila = **ID del paciente + N° de acceso + Organismo + Cultivo** (si se repite, se actualiza en vez de duplicar).

---

## 2. Archivo de PROA (`proa.txt`)

- **Separador de columnas: PIPE `|`.**
- Una fila por cada **antibiótico / suministro** del paciente.
- Orden fijo de **20 columnas**:

| # | Columna | Descripción / formato | Obligatorio |
|---|---------|-----------------------|:---:|
| 1 | Cod_Episodio | Código de episodio (número) | No |
| 2 | Cod_Sala | Código de sala | No |
| 3 | Nom_Sala | Nombre de la sala / servicio | No |
| 4 | Num_Cama | Número de cama | No |
| 5 | F_Ingreso | Fecha y hora de ingreso `aaaa-mm-dd HH:MM:SS` | No |
| 6 | Cod_Eps | Código EPS | No |
| 7 | Nom_Eps | Nombre EPS | No |
| 8 | Hist_Clinica | **Historia clínica** (clave de cruce con epidemiología) | Sí* |
| 9 | Tipo_Ident | Tipo de documento (CC, RC, TI, CE…) | No |
| 10 | Num_Ident | **Documento / cédula** (clave de cruce con epidemiología) | Sí* |
| 11 | Nombre | Primer/segundo nombre del paciente | No |
| 12 | Apellido1 | Primer apellido | No |
| 13 | Apellido2 | Segundo apellido | No |
| 14 | Sexo | `M` / `F` | No |
| 15 | Fec_Nacimiento | `aaaa/mm/dd` (se usa para calcular la edad) | No |
| 16 | CIE10 | Código diagnóstico CIE10 | No |
| 17 | Diagnóstico | Texto del diagnóstico | No |
| 18 | Antimicrobiano | Nombre + presentación, ej. `Piperacilina / tazobactam vial x 4.5 gr` (el sistema separa el nombre genérico de la presentación) | No |
| 19 | Instrucciones | **4 partes separadas por COMA**: `Cantidad, Vía, Frecuencia, Duración` — ej. `1400 MILIGRAMOS, ENDOVENOSA, Cada 8 horas, por 24 HORAS` | No |
| 20 | Fecha y hora de suministro | `aaaa-mm-dd HH:MM:SS` | No |

**Notas:**
- (*) `Hist_Clinica` y `Num_Ident` no son obligatorios técnicamente, pero **sin al menos uno de ellos el registro PROA no se puede asociar a un paciente** y no aparecerá en el aplicativo. Idealmente envíen ambos.
- Se permite una fila de encabezado; se ignora si la primera celda contiene "COD_EPISODIO".
- La columna 19 (Instrucciones) **debe** llevar las 4 partes separadas por coma, en ese orden.

---

## 3. Datos COMPLEMENTARIOS (los que el usuario llena en la página)

> Son datos de juicio clínico / PROA que normalmente se escriben en la página. En la plantilla de Excel van en las columnas con **encabezado verde**. **Ahora SÍ se importan por TXT**: si el archivo de epidemiología trae sus columnas complementarias (hasta 43 en total) y el de PROA las suyas (hasta 48 en total), el importador combinado los carga junto con los datos base. Es decir, un solo par de archivos TXT alimenta todo (azul + verde), cruzando por documento / historia clínica.

### 3.1 Complementarios de EPIDEMIOLOGÍA (se agregan después de la columna 16)

`Nacionalidad` *(nombre del país, de la lista DANE; en el Excel es un desplegable)* · `Diagnóstico de ingreso` · `Asegurador` · `Peso (kg)` · `Fecha de ingreso` *(estos 5 son constantes por paciente)* · `Fecha quirúrgica previa` · `Categoría quirúrgica` · `Egreso (VIVO/MUERTO/N/A)` · `Sitio` · `Tipo (1/2)` · `Clasificación (1-7)` · `Especialidad que realizó cirugía` · `Procedimiento quirúrgico` · `Tiempo quirúrgico` · `Baño quirúrgico (SI/NO)` · `Asepsia quirúrgica (SI/NO)` · `Profilaxis (SI/NO)` · `Antibióticos usados (SI/NO)` · `ASA preoperatoria (SI/NO)` · `Tipo cirugía (ELECTIVA/URGENCIA)` · `Clasificación cirugía` · `Puntaje NNIS (0/1/2/3/SD)` · `Revisión con equipo` · `Interconsulta infectología (SI/NO)` · `Fecha de inserción` · `Fecha de retiro` · `Comentarios`

*(Los campos "Días entre Qx e infección" y "Clasificación en texto" se calculan solos, no se diligencian.)*

### 3.2 Complementarios de PROA — Intervención (se agregan después de la columna 20)

`Mes` · `Fecha intervención` · `Fecha inicio antibiótico` · `Dosis suministrada` · `Sistema internacional` · `Perfil antimicrobiano` · `Especialista tratante` · `Diagnóstico infeccioso` · `Dosis adecuada (Si/No/No aplica)` · `Fecha fin antibiótico` · `Tiempo tratamiento` · `Duración adecuada (Si/No/No aplica)` · `Cultivo previo (Si/No)` · `Resultado cultivo` · `Solicitudes de pruebas` · `Oportunidad reporte` · `Indicación terapia` · `Tratamiento` · `Valoración grupo 1 (Si/No/No aplica)` · `Valoración UCI (Si/No/No aplica)` · `Fecha valoración` · `Ajuste prescripción (Si/No/No aplica)` · `Adherencia PROA (Si/No/Parcial)` · `Adherencia guías (Si/No)` · `Razón no adherencia` · `Observación` · `Caso cerrado (Si/No)` · `Mortalidad (Si/No)`

---

## Resumen de fechas

| Archivo | Campo | Formato |
|---------|-------|---------|
| Epidemiología | Fecha nacimiento / Fecha toma muestra | `dd/mm/aaaa` |
| PROA | F_Ingreso / Fecha suministro | `aaaa-mm-dd HH:MM:SS` |
| PROA | Fec_Nacimiento | `aaaa/mm/dd` |

## Consejo de calidad
- Mismo **documento** y misma **historia clínica** en ambos archivos (así se cruza PROA con epidemiología).
- Codificación del archivo: UTF-8 o Windows-1252 (ambas se soportan; tildes y Ñ funcionan).
- No usar el carácter separador dentro de un valor (no meter TAB dentro de un campo de epidemiología, ni `|` dentro de un campo de PROA).
