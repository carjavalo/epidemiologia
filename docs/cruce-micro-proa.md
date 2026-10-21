# Cómo se cruzan la base de microbiología y la base de PROA

> Documento de referencia para un agente que vaya a trabajar sobre este código.
> Describe **únicamente** el cruce entre las dos fuentes de datos: cómo se
> localiza en PROA al paciente que aparece en microbiología, y cómo se decide
> qué pacientes tienen antibióticos y cuáles no.
>
> Proyecto: Epidemiología Hospitalaria (ProaHUV) · Laravel 12 · PHP 8.2
> Verificado contra el código el 20/10/2026.

---

## 1. Las dos bases y la tabla que las une

Son dos universos de datos que llegan en **dos archivos TXT distintos** y viven
en tablas distintas. No hay clave foránea entre ellos: el cruce es **lógico**,
por número de documento o historia clínica.

| Tabla | Rol | Modelo | Filas (local) |
|---|---|---|---|
| `seguimiento_microbiologico` | **MICRO.** Una fila por muestra/microorganismo. Es el eje del aplicativo. | `EpidemiologiaRegistro` | 9.505 |
| `deta_procedimientos` | **PROA.** Una fila por **dosis** de antibiótico suministrada. | `Procedimiento` | 11.972 |
| `pacientes` | **El pivote.** Identidad única del paciente. | `Paciente` | 4.248 |
| `intervenciones_proa` | La intervención que el usuario registra sobre una dosis. | `IntervencionProa` | — |

Detalle crítico de granularidad:

- **MICRO**: 1 fila = 1 microorganismo de 1 muestra. Un paciente tiene varias.
- **PROA**: 1 fila = 1 dosis. Un paciente con 7 días de tratamiento puede tener
  decenas de filas del mismo antibiótico.

### Las columnas del cruce

Los nombres **no coinciden** entre las dos tablas. Esta correspondencia es la
pieza central de todo el documento:

| Concepto | En MICRO (`seguimiento_microbiologico`) | En PROA (`deta_procedimientos`) |
|---|---|---|
| Documento de identidad | `identificador_unico` | `Num_Ident` |
| Historia clínica | `id_historia` | `Hist_Clinica` |
| Servicio / ubicación | `ubicacion` | `Nom_Sala` |
| Antibiótico | *(no existe)* | `Antimicrobiano` |
| Fecha de la dosis | *(no existe)* | `Fec_Sumistro` *(sic, sin la "n")* |

> `Fec_Sumistro` está mal escrito en el esquema original. **No lo corrijas**:
> el nombre aparece literalmente en consultas, agrupaciones y vistas.

---

## 2. El cruce ocurre en DOS momentos distintos

Esto es lo que más confunde al leer el código por primera vez. No hay un solo
punto de cruce, hay dos, y hacen cosas diferentes:

| | Cuándo | Dónde | Qué hace |
|---|---|---|---|
| **A. Escritura** | Al importar el TXT de PROA | `SeguimientoMicrobiologicoController::procesarProa()` | Marca banderas en la fila de MICRO |
| **B. Lectura** | En cada carga de la pantalla Registros | `RegistrosController::index()` | Arma el árbol paciente → medicamentos → dosis |

El cruce **B es el que manda** para lo que se ve en pantalla. El A solo deja
banderas que se usan para contadores rápidos.

---

## 3. Momento A — el cruce al importar (escritura)

**Archivo:** `app/Http/Controllers/SeguimientoMicrobiologicoController.php`
**Método:** `procesarProa()`, línea 628

El TXT de PROA se recorre fila por fila (recuerda: una fila por dosis). Por
cada fila se hacen tres cosas, en este orden.

### A.1 — Siempre se inserta la dosis en `deta_procedimientos`

Línea 706:

```php
// 1. Crear SIEMPRE la fila en deta_procedimientos (esto hace aparecer
//    el bloque PROA y permite el cruce por documento / historia).
$recortados = [];
$procCreado = Procedimiento::insertarDesdeCampos($campos, $encabezado->id_procedimiento, $recortados);
```

**Por qué importa:** la fila se crea *aunque el paciente no exista todavía en
microbiología*. Es lo que permite que el cruce B la encuentre más adelante, y
lo que hace posible el caso "paciente solo-PROA" (sección 7).

### A.2 — Se busca al paciente por documento **o** historia

Línea 728. Aquí está la primera mitad del cruce:

```php
// 2. Reflejar procedimiento + intervención en el seguimiento del paciente.
$identificador = $campos[9] ?? null;   // Num_Ident
$historia      = $campos[7] ?? null;   // Hist_Clinica

// Un paciente aparece en muchas filas (una por dosis). Se resuelve
// una sola vez por importación.
$clavePaciente = $identificador . '|' . $historia;

if (array_key_exists($clavePaciente, $cachePacientes)) {
    $paciente = $cachePacientes[$clavePaciente];
} else {
    $paciente = Paciente::where('identificador_unico', $identificador)
        ->orWhere('id_historia', $historia)
        ->first();
    $cachePacientes[$clavePaciente] = $paciente;
}

if (!$paciente) {
    $sinPaciente++;
    continue;
}
```

Tres cosas que un agente debe registrar:

1. **Es `orWhere`, no `AND`.** Basta con que coincida el documento *o* la
   historia. Es deliberado: la fuente externa a veces trae uno de los dos vacío.
2. **El caché `$cachePacientes`** evita miles de SELECT repetidos, porque el
   mismo paciente aparece en decenas de filas.
3. **Si no hay paciente, se abandona la fila** (`continue`) pero la dosis **ya
   quedó insertada** en el paso A.1. Esa asimetría es intencional.

### A.3 — Se localiza la fila de MICRO destino

```php
// El seguimiento destino es siempre el mismo para un paciente dado,
// así que también se resuelve una sola vez.
if (isset($cacheRegistros[$paciente->id])) {
    $registro = $cacheRegistros[$paciente->id];
} else {
    $registro = EpidemiologiaRegistro::where('paciente_id', $paciente->id)
        ->orderByDesc('fecha_toma_muestra')
        ->first();

    if (!$registro) {
        $registro = EpidemiologiaRegistro::create([
            'paciente_id'         => $paciente->id,
            'nombre'              => $paciente->nombre,
            'id_historia'         => $paciente->id_historia,
            'fecha_nacimiento'    => $paciente->fecha_nacimiento,
            'sexo'                => $paciente->sexo,
            'identificador_unico' => $paciente->identificador_unico,
        ]);
    }

    $cacheRegistros[$paciente->id] = $registro;
}
```

**Regla:** se elige **la muestra más reciente** del paciente
(`orderByDesc('fecha_toma_muestra')`). Si el paciente no tiene ninguna, se crea
una fila de MICRO vacía para colgar de ella los datos de PROA.

### A.4 — Se marcan las banderas

Línea 834:

```php
$intervencion    = array_filter($intervencion, fn ($v) => !is_null($v));
$hayIntervencion = !empty($intervencion);

$registro->update(array_merge(
    $procedimiento,
    $intervencion,
    ['tiene_procedimiento' => true, 'tiene_intervencion_proa' => $hayIntervencion]
));
```

| Bandera | Significado |
|---|---|
| `tiene_procedimiento` | El paciente aparece en PROA (tiene al menos una dosis). |
| `tiene_intervencion_proa` | El TXT traía además datos de intervención ya diligenciados. |

Estas banderas **solo se usan para contadores rápidos** (por ejemplo la píldora
"N con PROA" en las tarjetas de servicio, `RegistrosController:109`). La
pantalla no confía en ellas para armar el árbol; para eso rehace el cruce.

### A.5 — El lado MICRO: cómo nace el paciente

En el otro importador (`procesarEpidemiologia`), el paciente se crea o
actualiza con el documento como llave, línea 568:

```php
$paciente = Paciente::updateOrCreate(
    ['identificador_unico' => $datos['identificador_unico']],
    [
        'nombre'           => $datos['nombre'],
        'id_historia'      => $datos['id_historia'],
        'fecha_nacimiento' => $datos['fecha_nacimiento'],
        'sexo'             => $datos['sexo'],
    ]
);
```

Y la fila de MICRO tiene esta clave lógica única (línea 585):

```php
// Clave única lógica: identificador + n_reporte + microorganismo + cultivo_num
$registro = EpidemiologiaRegistro::firstOrNew([
    'identificador_unico' => $datos['identificador_unico'],
    'n_reporte'           => $datos['n_reporte'],
    'microorganismo'      => $datos['microorganismo'],
    'cultivo_num'         => $datos['cultivo_num'],
]);
```

> **Orden de importación recomendado: MICRO primero, PROA después.** Si PROA
> entra primero, muchas filas caen en `$sinPaciente` porque `pacientes` todavía
> está vacío para ellas.

---

## 4. Momento B — el cruce al consultar (lectura)

**Archivo:** `app/Http/Controllers/RegistrosController.php`
**Método:** `index()`, línea 40. El cruce arranca en la **línea 200**.

Este es el cruce que construye lo que el usuario ve.

### B.1 — Se recogen las llaves de los pacientes en pantalla

```php
// ── 3. Cruce con PROA: agrupar por identificador / historia ────────
$identificadores = $seguimientos->pluck('identificador_unico')->filter()->unique()->values();
$historias       = $seguimientos->pluck('id_historia')->filter()->unique()->values();
```

`$seguimientos` son las filas de MICRO ya filtradas por servicio, búsqueda y
fechas. De ahí salen las dos listas de llaves.

### B.2 — Una sola consulta a PROA para toda la página

```php
if ($identificadores->isNotEmpty() || $historias->isNotEmpty()) {
    $todosProc = Procedimiento::query()
        ->where(function ($q) use ($identificadores, $historias) {
            if ($identificadores->isNotEmpty()) {
                $q->whereIn('Num_Ident', $identificadores);
            }
            if ($historias->isNotEmpty()) {
                $q->orWhereIn('Hist_Clinica', $historias);
            }
        })
        ->get();

    $procedimientosPorIdent = $todosProc->groupBy('Num_Ident');
    $procedimientosPorHist  = $todosProc->groupBy('Hist_Clinica');
    $todosIds               = $todosProc->pluck('id');
}
```

**Patrón clave:** una consulta para toda la página, y luego dos índices en
memoria (`groupBy`). Evita el problema N+1 — si no, sería una consulta por
paciente.

> El `where(...)` envolvente es **obligatorio**. Sin él, el `orWhereIn` se
> saldría del grupo y arrastraría toda la tabla.

### B.3 — Las intervenciones ya registradas

Línea 225:

```php
$intervenciones = IntervencionProa::query()
    ->whereIn('id_deta_procedimiento', $todosIds)
    ->get()
    ->keyBy('id_deta_procedimiento');

// Ids de dosis que YA tienen intervención registrada.
$idsConIntervencion = $intervenciones->keys()->flip();
```

El `->flip()` convierte la lista en un índice para que el `has()` posterior sea
O(1) en vez de recorrer la colección por cada dosis.

### B.4 — El emparejamiento, paciente por paciente

Aquí es donde se decide **quién tiene antibióticos**:

```php
// Representante para cabecera del paciente y cruce PROA
// (identificador / historia son iguales en todos sus registros)
$rep     = $registrosPaciente->first();
$ident   = $rep->identificador_unico;
$hist    = $rep->id_historia;
$matches = collect();

if ($ident && $procedimientosPorIdent->has($ident)) {
    $matches = $matches->concat($procedimientosPorIdent->get($ident));
}
if ($hist && $procedimientosPorHist->has($hist)) {
    $matches = $matches->concat($procedimientosPorHist->get($hist));
}

$matches = $matches->unique('id');
```

Lectura paso a paso:

1. Se toma **un registro representativo** del paciente. Todas sus filas de MICRO
   comparten documento e historia, así que da igual cuál.
2. Se busca por documento **y también** por historia, y se concatenan.
3. **`unique('id')` es imprescindible**: un paciente que coincide por ambas vías
   traería cada dosis duplicada.

`$matches` es ahora **todas las dosis de antibiótico de ese paciente**. Si está
vacío, el paciente no tiene PROA.

### B.5 — Las dosis se agrupan por antibiótico

```php
// Agrupar por antibiótico en MAYÚSCULA para que un mismo fármaco
// no aparezca en varios bloques por diferencias de capitalización.
$medicamentos = $matches->isNotEmpty()
    ? $matches->sortBy('Fec_Sumistro')
              ->groupBy(fn ($p) => mb_strtoupper(trim((string) $p->Antimicrobiano), 'UTF-8'))
    : null;
```

`$medicamentos` queda como `['MEROPENEM 1G' => Collection<dosis>, ...]`, o
`null` si no hay PROA. Ese `null` se propaga a la vista.

### B.6 — Lo que se entrega a la vista

```php
return [
    'paciente'      => $rep->paciente,
    'seguimiento'   => $rep,                          // back-compat (cabecera / PROA)
    'seguimientos'  => $registrosPaciente->values(),  // TODOS los microorganismos del paciente
    'tiene_proa'    => $matches->isNotEmpty(),
    'medicamentos'  => $medicamentos,
    'total_medic'   => $medicamentos ? $medicamentos->count() : 0,
    'total_dosis'   => $matches->count(),
    'epi_completo'  => $epiCompleto,
    'proa_completo' => $proaCompleto,
];
```

**`tiene_proa` es la respuesta directa a "¿este paciente tiene antibióticos?"**
Es, literalmente, si el cruce encontró alguna dosis.

Y es lo que alimenta el filtro de la barra superior:

```php
// ── Filtro por tipo: con PROA / solo epidemiología / todos ─────────
if (in_array($tipo, ['proa', 'epidemiologia'], true)) {
    $dataPorServicio = $dataPorServicio->map(function ($listaPacientes) use ($tipo) {
        return $listaPacientes->filter(function ($info) use ($tipo) {
            return $tipo === 'proa' ? $info['tiene_proa'] : ! $info['tiene_proa'];
        });
    });
}
```

---

## 5. De dosis a "cursos de tratamiento"

**Archivo:** `app/Support/ProaCursos.php`

PROA entrega dosis sueltas, pero clínicamente lo que importa es el **curso**: un
tratamiento ideal dura 7 días. Esta clase agrupa las dosis en cursos.

```php
/** Días que dura idealmente un curso. */
public const DIAS_CURSO = 7;
```

La regla, tal como la implementa `agrupar()` (línea 29):

- Una dosis a **menos de 7 días** del inicio de un curso **pertenece** a ese curso.
- Una dosis a **7 días o más** abre un **curso nuevo** (es un re-tratamiento).
- Dentro de un curso, las dosis de la misma fecha se consideran repetidas y se
  conserva la primera.

```php
$dentroDe7 = ($fecha !== null && $curso['inicio'] !== null
    && $curso['inicio']->diffInDays($fecha) < self::DIAS_CURSO);
```

Cada curso expone una **dosis representativa** —la primera de la fecha más
temprana— que es sobre la que se guarda la intervención:

```php
$cursos[$i]['representativa'] = $curso['dosis']
    ->sortBy(fn ($d) => $d->Fec_Sumistro ? (string) $d->Fec_Sumistro : '9999-12-31')
    ->first();
```

Y el contador en vivo del día de tratamiento (línea 75):

```php
public static function diaActual(?Carbon $inicio): ?int
{
    if ($inicio === null) {
        return null;
    }

    return $inicio->startOfDay()->diffInDays(Carbon::today()) + 1;
}
```

> Con datos reales hay cursos abiertos desde hace cientos de días, así que
> `diaActual()` devuelve valores muy por encima de 7 con normalidad. La vista lo
> acota al dibujar.

---

## 6. Semaforización: cuándo un paciente está "completo"

Línea 258 de `RegistrosController`. Son dos condiciones independientes.

```php
// Epidemiología: verde si TODAS las muestras están registradas.
$epiCompleto = $registrosPaciente->every(fn ($s) => (bool) $s->registrado);

// PROA: verde si las dosis MOSTRADAS (la primera de cada fecha,
// por medicamento) tienen intervención registrada.
$proaCompleto = $medicamentos && $medicamentos->isNotEmpty()
    && $medicamentos->every(function ($dosisMed) use ($idsConIntervencion) {
        return $dosisMed->groupBy(function ($p) {
                return $p->Fec_Sumistro
                    ? \Carbon\Carbon::parse($p->Fec_Sumistro)->format('Y-m-d')
                    : 'sin-fecha';
            })
            ->map(fn ($g) => $g->first())
            ->every(fn ($d) => $idsConIntervencion->has($d->id));
    });
```

El matiz importante de `$proaCompleto`: **no exige intervención en todas las
dosis**, solo en las que la pantalla muestra —la primera de cada fecha, por
medicamento—. Si exigiera todas, un paciente con 40 dosis nunca se pondría en
verde.

En la vista, el paciente se considera completo cuando:

```php
$pacienteCompleto = $info['epi_completo'] && (!$tienePROA || $info['proa_completo']);
```

Es decir: **un paciente sin PROA puede estar completo solo con epidemiología.**

---

## 7. Casos borde que hay que conocer

### 7.1 — Paciente solo-PROA (tiene antibióticos pero no microbiología)

La pantalla Registros es **centrada en servicios**, y los servicios salen de
MICRO. Un paciente que solo está en PROA es, por construcción, invisible.

Se detecta y se avisa. Línea 92:

```php
// ── Aviso "paciente solo-PROA": si se busca un documento/historia que NO
// tiene registros de microbiología pero SÍ tiene tratamiento en PROA,
// se avisa (evita el desconcertante "No se encontraron servicios"). ──
$avisoSoloProa = null;
if ($search !== '' && $serviciosPaginados->total() === 0) {
    $proc = Procedimiento::where('Num_Ident', $search)
        ->orWhere('Hist_Clinica', $search)
        ->first(['Num_Ident', 'Hist_Clinica', 'Medico_Trata', 'Nom_Sala']);
    if ($proc) {
        $avisoSoloProa = [
            'documento' => $proc->Num_Ident ?: $search,
            'nombre'    => trim((string) $proc->Medico_Trata),
            'sala'      => $proc->Nom_Sala,
        ];
    }
}
```

> **Trampa documentada:** aquí `'nombre'` se llena con **`Medico_Trata`**, que es
> el médico tratante, no el paciente. `deta_procedimientos` no guarda el nombre
> del paciente. Es un apaño conocido, no un error de tipeo.

### 7.2 — Dosis de PROA sin paciente en MICRO

Quedan en `deta_procedimientos` pero no se reflejan en ninguna fila de MICRO.
Se cuentan en `$sinPaciente` y se reportan al terminar la importación.

### 7.3 — Coincidencia por historia pero no por documento

El cruce la acepta (`orWhere`). Con `unique('id')` no se duplica. Pero implica
que **dos pacientes distintos con la misma historia clínica se mezclarían.** No
hay protección contra eso; si aparece, es un problema de calidad del dato en la
fuente.

---

## 8. Mapa de archivos

| Archivo | Qué contiene del cruce |
|---|---|
| `app/Http/Controllers/RegistrosController.php` | **El cruce de lectura.** Líneas 92 (aviso solo-PROA), 109 (contador), 200-290 (cruce y semáforo) |
| `app/Http/Controllers/SeguimientoMicrobiologicoController.php` | **El cruce de escritura.** `procesarProa()` línea 628; emparejamiento en 728-775; banderas en 834 |
| `app/Support/ProaCursos.php` | Agrupación de dosis en cursos de 7 días |
| `app/Models/Procedimiento.php` | Modelo PROA → tabla `deta_procedimientos` |
| `app/Models/EpidemiologiaRegistro.php` | Modelo MICRO → tabla `seguimiento_microbiologico` |
| `app/Models/Paciente.php` | El pivote de identidad |
| `app/Models/IntervencionProa.php` | Intervención, ligada por `id_deta_procedimiento` |
| `app/Http/Controllers/IntervencionProaController.php` | Guarda la intervención y refleja banderas en MICRO |
| `resources/views/registros/index.blade.php` | Consume `$info['tiene_proa']`, `['medicamentos']`, `['proa_completo']` |

---

## 9. Advertencias para quien modifique esto

1. **No cambies `orWhere` por `where`** en los dos emparejamientos
   (`SeguimientoMicrobiologicoController:740` y `RegistrosController:215`). La
   fuente trae documento o historia vacíos con frecuencia; exigir ambos
   rompería el cruce de muchos pacientes.

2. **No quites `unique('id')`** tras concatenar los dos índices. Duplicaría cada
   dosis de los pacientes que coinciden por ambas llaves.

3. **No conviertas el cruce de lectura en una consulta por paciente.** Está
   escrito como una consulta + índices en memoria a propósito. Con ~9.500 filas
   de MICRO y ~12.000 de PROA, el N+1 es inmediato.

4. **`Fec_Sumistro` va sin "n".** Corregirlo rompe consultas y agrupaciones.

5. **Las banderas `tiene_procedimiento` / `tiene_intervencion_proa` pueden quedar
   desfasadas.** Se escriben en la importación y en el guardado de una
   intervención, pero la pantalla rehace el cruce en vivo. Si ves un desajuste
   entre la píldora del servicio y lo que muestra el paciente, es por eso.

6. **El orden de importación importa:** MICRO antes que PROA.
