# Cómo se guardan los registros en ProAHUV

Documento técnico del flujo de datos de epidemiología y PROA.
Última actualización: 2026-07-23.

Este documento explica **dónde** y **cómo** se guarda cada dato, para que
cualquier persona del equipo pueda mantener o auditar el sistema sin leer todo
el código.

---

## 1. Tablas y cómo se relacionan

```
pacientes  1───N  casos_microorganismo  1───N  seguimiento_microbiologico
                                                   (una fila por MUESTRA/cultivo)

pacientes  ─ (cruce por documento / historia) ─  deta_procedimientos  1───1  intervenciones_proa
                                                   (una fila por DOSIS)          (una fila por dosis)
```

| Tabla | Qué guarda | Grano (qué es una fila) |
|---|---|---|
| `pacientes` | Datos del paciente | Un paciente (clave: `identificador_unico`) |
| `seguimiento_microbiologico` | Muestra/cultivo con su antibiograma y datos complementarios | **Una muestra** |
| `casos_microorganismo` | Agrupa las muestras del mismo germen en un paciente | **Un caso** (un microorganismo del paciente) |
| `deta_procedimientos` | Suministro de un antibiótico (importación PROA) | **Una dosis** |
| `intervenciones_proa` | La intervención PROA que el equipo registra sobre una dosis | Una intervención (1 por dosis) |
| `encabezados_procedimientos` | Lote de cada importación PROA | Una importación |
| `actividades` | Bitácora de auditoría (quién hizo qué) | Una acción |

**Relación clave nueva:** cada fila de `seguimiento_microbiologico` tiene una
columna `caso_id` que apunta a `casos_microorganismo`. El caso es la unidad de
agrupación: "este microorganismo, en este paciente, en este episodio".

---

## 2. Los dos caminos por los que entran datos

1. **Importación masiva** (Excel / CSV / TXT) — carga inicial desde los archivos
   del laboratorio y de PROA.
2. **Formulario manual** — la epidemióloga completa y guarda campo por campo
   desde la página de *Registros por Servicio*.

Ambos escriben en las mismas tablas. Lo que sigue detalla cada uno.

---

## 3. Clasificación de los campos de `seguimiento_microbiologico`

Esta es la parte más importante para entender el guardado. Los campos de una
muestra se comportan de **tres formas distintas** al guardar desde el formulario:

### 3.1 Campos de la muestra — *uno por cada muestra*

Constante `EpidemiologiaRegistro::CAMPOS_MUESTRA`. Son los datos propios de cada
cultivo; se repiten dentro del bloque, una sección "Registro #n" por muestra:

```
tipo_muestra · n_reporte · cultivo_num · sede · ubicacion · fecha_toma_muestra
microorganismo · sensibles · intermedios · resistentes · marcadores_resistencia
```

En el formulario llegan como `registros[<id_de_la_muestra>][campo]`.
**Cada muestra guarda los suyos, no se comparten.**

### 3.2 Campos constantes del paciente — *se replican a TODAS sus muestras*

Lista fija en el método `guardar`:

```
tipo_id · pais_origen · departamento · municipio
diagnostico_ingreso · asegurador · peso · fecha_ingreso_hosp
```

Son iguales para el paciente completo. Al guardar cualquier bloque, se copian a
**todas** las filas de ese paciente (incluso a las de otros microorganismos),
para no tener que reescribirlos en cada bloque.

### 3.3 Campos complementarios del caso — *se llenan una vez por bloque*

Todo lo demás posterior a "marcadores" (clasificación, sitio, tipo, datos
quirúrgicos, comentarios, etc.). En el formulario se muestran **una sola vez**
por bloque de microorganismo y, al guardar, se aplican a todas las muestras de
**ese bloque**.

> **Nota de diseño pendiente:** los datos muestran que algunos de estos campos
> (`clasificacion`, `tipo`, `sitio`, `clasificacion_texto`, `comentarios`,
> `interconsulta_infectologia`, `revision_equipo`) en realidad varían por muestra,
> no por caso. Por ahora se toma el valor del primer registro. Ver sección 8.

### 3.4 Campos derivados — *los calcula el sistema, no se escriben a mano*

| Campo | Cómo se calcula |
|---|---|
| `clasificacion_texto` | A partir de `tipo` + `clasificacion` + `sitio` (tabla de reglas en `guardar`) |
| `dias_entre_qx_e_infeccion` | `fecha_toma_muestra` − `fecha_quirurgica_previa`, en días, **por cada muestra** |

---

## 4. Guardado manual de epidemiología (`EpidemiologiaController@guardar`)

Ruta: `POST epidemiologia/guardar`. Se dispara con el botón **Registrar** de cada
bloque de microorganismo. Cada bloque envía **un** formulario.

### Qué llega en la petición
- `id` → id de la muestra representativa del bloque (la primera).
- `registros[<id>][campo]` → los campos de la muestra (3.1), un juego por cada
  "Registro #n" del bloque.
- Campos planos (`sitio`, `clasificacion`, `pais_origen`, `peso`, …) → los campos
  constantes (3.2) y complementarios (3.3), una sola vez.

### Pasos que ejecuta el método
1. **Valida** todos los campos (tipos, longitudes, enums).
2. **Deriva** `clasificacion_texto` desde `tipo` + `clasificacion` + `sitio`.
3. Determina si quien guarda es **administrador**.
4. Arma `$filasMuestra`: separa los `registros[<id>][…]`, dejando solo los campos
   de 3.1 y convirtiendo `''` en `null`.
5. **Guarda la muestra principal:**
   - Si viene `id` → la busca, verifica el bloqueo de edición, recalcula
     `dias_entre_qx_e_infeccion` y hace `update`.
   - Si no viene `id` → crea una muestra nueva.
6. Si quien guarda es **usuario básico**, marca esa muestra como
   `edicion_bloqueada = true`.
7. **Recorre las muestras del bloque** (`$filasMuestra`):
   - A cada una le guarda **sus** campos de muestra (3.1).
   - A las que no son la principal les aplica además los campos complementarios
     (3.3), de modo que todo el bloque quede consistente.
   - Recalcula `dias_entre_qx_e_infeccion` **por fila** (usa la fecha de toma de
     esa muestra).
   - Si es usuario básico, bloquea cada fila.
   - Respeta el bloqueo: un usuario básico no pisa filas ya bloqueadas.
8. **Replica los campos constantes** (3.2) a **todas** las demás muestras del
   mismo paciente.
9. Registra la acción en `actividades` (crear / actualizar).
10. Responde JSON `{ success, message, id }`.

### Regla de bloqueo de edición
- **Usuario básico** (`rol = basico`): puede llenar el formulario **una vez**.
  Al guardar, la fila queda `edicion_bloqueada = true` y ya no puede modificarla.
- **Administrador** (`rol = administrador`): puede editar siempre, incluso lo ya
  bloqueado.

---

## 5. Guardado de la intervención PROA (`IntervencionProaController@guardar`)

Ruta: `POST intervenciones-proa/guardar`. Botón **Guardar Intervención** dentro
de cada dosis PROA.

1. Valida que exista el `id_deta_procedimiento` (la dosis).
2. Toma los campos de la intervención; las llaves foráneas vacías se vuelven `null`.
3. Verifica el bloqueo de edición (misma regla que epidemiología).
4. `updateOrCreate` por `id_deta_procedimiento`: **una intervención por dosis**.
   Si ya existía, la actualiza; si no, la crea.
5. **Refleja** los datos de la dosis + intervención en la muestra de epidemiología
   más reciente del paciente (cruce por documento o historia), para que se vean
   juntos en la ficha (`reflejarEnSeguimiento`).
6. Si es usuario básico, bloquea la intervención.
7. Registra la actividad en `actividades`.

> Las dosis de un mismo día se muestran agrupadas en un solo bloque de fecha en
> la interfaz, pero **cada dosis conserva su propia intervención** en la base.

---

## 6. Agrupar muestras en un caso (`EpidemiologiaController@agruparCasos`)

Ruta: `POST epidemiologia/casos/agrupar`. Botón **Agrupar seleccionadas**, tras
marcar los checkboxes de las muestras.

1. Recibe `muestra_ids[]` (mínimo 2).
2. Verifica que **todas sean del mismo paciente**.
3. Crea un `caso_microorganismo` nuevo con `origen = manual`, copiando los datos
   complementarios de la muestra **más antigua** (por fecha de toma).
4. Mueve todas las muestras marcadas a ese caso (`caso_id`).
5. Elimina los casos viejos que hayan quedado **sin muestras**.
6. Registra la actividad.

`origen = manual` marca el caso como armado a mano: la importación y el reagrupador
automático **ya no lo tocan**.

---

## 7. Importación masiva (resumen)

Controlador: `SeguimientoMicrobiologicoController`.

- **Epidemiología:** por cada fila, `updateOrCreate` del paciente y de la muestra.
  La clave lógica de la muestra es
  `identificador_unico + n_reporte + microorganismo + cultivo_num`. Reimportar el
  mismo archivo **actualiza**, no duplica.
- **PROA:** por cada fila crea una dosis en `deta_procedimientos` y refleja los
  datos en la muestra del paciente. El nombre del antibiótico se extrae y se
  **estandariza en MAYÚSCULA** (`Procedimiento::extractNombreMedicamento`).
- Una fila defectuosa **no** aborta la importación: se omite y se reporta en la
  pantalla de importación con fila, paciente y columna del archivo.

### ⚠️ Paso operativo tras cada importación nueva

La importación crea las muestras con `caso_id = NULL`. Para agruparlas en casos hay
que ejecutar:

```bash
php artisan epidemiologia:agrupar-casos
```

Ese comando **solo** agrupa las muestras sin caso y **nunca** toca los casos
`manual`. Mientras no se ejecute, las muestras nuevas se muestran agrupadas por
nombre de microorganismo (comportamiento de respaldo de la vista).

Para estandarizar nombres de antibióticos ya guardados:

```bash
php artisan antibioticos:estandarizar
```

---

## 8. Decisión de diseño pendiente (Fase 2 → Fase 3)

El modelo actual guarda los datos complementarios (clasificación, comentarios,
etc.) en la fila de la muestra y usa `casos_microorganismo` **solo como clave de
agrupación**. El backfill detectó **3.451 discrepancias** en muestras del mismo
caso, concentradas en:

`comentarios`, `clasificacion_texto`, `sitio`, `clasificacion`,
`interconsulta_infectologia`, `revision_equipo`, `tipo`.

Esto sugiere que esos campos deberían quedar **por muestra**, no compartidos por
caso. La decisión (validada con el área clínica) determinará si, en una fase
posterior, esos campos se mueven definitivamente a la muestra y el resto de
complementarios pasa a vivir en `casos_microorganismo`. Hoy **nada de esto se ha
borrado**: el esquema es aditivo y reversible.

---

## 9. Auditoría

Toda escritura relevante (epidemiología, PROA, importación, agrupación, vaciado)
deja un registro en `actividades` vía `Actividad::registrar(...)`, con el usuario,
el tipo, la acción, el paciente y la referencia a la fila afectada. El
**Historial de actividades** del dashboard (solo administradores) lo muestra.
