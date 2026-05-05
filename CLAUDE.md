# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

_Actualizado: 2026-05-04_

## Dos apps independientes — CRÍTICO

Este repositorio contiene **dos aplicaciones completamente separadas** que deben mantenerse sincronizadas manualmente:

| | App local (PC) | App celular (GitHub Pages) |
|---|---|---|
| **Ubicación** | `resources/views/`, `app/` | `docs/` |
| **Stack** | Laravel + Livewire + MySQL | HTML estático + Alpine.js + Dexie (IndexedDB) |
| **URL** | `http://localhost/agenda-estetica/public` | `https://juanmazzola-cmyk.github.io/agenda/` |
| **Datos** | MySQL | localStorage / IndexedDB (Dexie) |

**Los nuevos features se implementan solo en `docs/` (celular).** La app Laravel/localhost es secundaria y solo se toca si se pide explícitamente.

### App celular (`docs/`)
- `docs/index.html` — toda la UI en Alpine.js
- `docs/app.js` — lógica y acceso a Dexie
- `docs/sw.js` — service worker con caché versionado (`agenda-vXX`). **Al modificar `index.html` o `app.js`, incrementar la versión del caché** para forzar actualización en el celular.
- Dexie gestiona las migraciones de IndexedDB con `.version(N).stores({...})`. Siempre agregar versión nueva al añadir tablas, nunca modificar versiones existentes.

## Entorno y despliegue

- **Local**: XAMPP en Windows. URL: `http://localhost/agenda-estetica/public`. No usar `php artisan serve`.
- **GitHub Pages**: el push a `main` despliega automáticamente `docs/` en `https://juanmazzola-cmyk.github.io/agenda/`.
- **Base de datos local**: MySQL, nombre `agenda_estetica`.

## Comandos frecuentes

```bash
php artisan migrate          # correr migraciones
npm run dev                  # Vite dev server (assets en caliente)
npm run build                # compilar assets para producción
php artisan make:livewire    # crear componente Livewire
php artisan make:migration   # crear migración
```

## Stack

- **Laravel** + **Livewire 3** (componentes full-page, sin controladores)
- **Alpine.js** para interactividad client-side dentro de los componentes Livewire
- **Tailwind CSS v4** vía Vite
- **PWA**: `manifest.json` + `sw.js` (el service worker fuerza red en navegación para evitar HTML cacheado)

## Arquitectura

Todas las rutas en `routes/web.php` apuntan directamente a componentes Livewire full-page. No hay controladores. El layout `components.layouts.app` contiene el sidebar + main.

```
/agenda        → Livewire\Agenda
/clientes      → Livewire\Clientes
/tratamientos  → Livewire\Tratamientos
/resumen       → Livewire\Resumen
/estadisticas  → Livewire\Estadisticas
/ajustes       → Livewire\Ajustes
```

No hay autenticación; la app es mono-usuario.

## Modelos y tablas (PC)

| Modelo | Tabla | Nota |
|---|---|---|
| `Turno` | `turnos` | belongsTo Cliente, Tratamiento |
| `Cliente` | `clientes` | nombre, apellido, celular |
| `Tratamiento` | `tratamientos` | — |
| `Configuracion` | `configuracion` | key-value; usar `obtener()`/`establecer()` |
| `DiaBloqueado` | `dias_bloqueados` | días sin turnos |

**Importante**: Laravel no pluraliza bien algunos nombres en español. Los modelos con tabla no estándar deben declarar `protected $table` explícitamente (ya lo hacen `Configuracion` y `DiaBloqueado`). Al crear nuevos modelos en español, verificar siempre que la tabla inferida sea correcta.

### Sesiones impagadas (celular)

- `estado` del turno: `'pagado'` | `'impaga'` (el estado `'pendiente'` fue eliminado)
- Un turno pasado impago tiene `estado === 'impaga'` y `fecha < hoy`
- **Lista de clientes**: badge rojo "X sesión/es impaga/s" — método `impagasDe(clienteId)`
- **Panel del día (grilla)**: badge rojo "Impaga" en el turno pasado no cobrado; badge naranja "Deuda" + fecha(s) en el próximo turno futuro del cliente si tiene deuda anterior — métodos `getBadgeTurno(t)` y `deudaFechasTexto(t)`
- **Historial del cliente**: muestra todos los turnos (pasados sombreados, futuros destacados) con estado, notas e importe

## Datos del celular (Dexie)

Stores de IndexedDB en `docs/app.js`:

| Store | Campos clave | Nota |
|---|---|---|
| `clientes` | `++id, nombre` | nombre, apellido, telefono |
| `tratamientos` | `++id, nombre` | — |
| `turnos` | `++id, fecha, clienteId, tratamientoId` | estado: 'pagado'\|'impaga'; campos: fecha, hora, clienteId, tratamientoId, notas, estado, valor |
| `historial` | `++id, clienteId, fecha` | entradas manuales de historial |
| `diasBloqueados` | `++id, fecha` | fechas bloqueadas (sin atención) |

Versión actual de Dexie: **v3**. Al agregar stores, crear `db.version(4).stores({...})` con todos los stores.

## Patrones clave (celular)

**`impagasDe(clienteId)`** — devuelve turnos con `estado === 'impaga'`. Usado en lista de clientes para el badge de deuda.

**`getBadgeTurno(t)`** — retorna `'impaga'` si el turno es pasado con `estado === 'impaga'`; retorna `'deuda'` si es el próximo turno futuro del cliente y tiene deuda anterior; retorna `null` en cualquier otro caso.

**`deudaFechasTexto(t)`** — retorna el string `'No pagó: DD/MM/AAAA, ...'` para el badge Deuda, o `''` si no aplica. Se usa con `x-text` directo (sin condicional de display) para evitar inconsistencias de Alpine dentro de `x-for`.

**Alpine.js dentro de `x-for`** — patrón confiable para visibilidad condicional: usar `:style="{display: metodo(t) === 'valor' ? 'inline' : 'none'}"` con objeto (NO string). `x-show` e `x-if` son poco confiables dentro de `x-for`. Para contenido de texto condicional, preferir un método que retorne `''` y usar `x-text` directo.

**Días bloqueados** — `diasBloqueados` store en Dexie. En el calendario se muestra una X SVG absoluta sobre el día. El botón Bloquear/Desbloquear aparece en el panel al seleccionar un día. Método `toggleBloqueo(dia)` en `app.js`.

**Historial** — `recargarHistorial()` usa `.toArray()` + `.sort()` en JS (NO `.reverse().sortBy()` de Dexie, que no funciona correctamente). Muestra todos los turnos del cliente (pasados y futuros).

**`cerrarModal()`** — limpia `modalActivo`, `historialClienteId`, `busquedaTurnoCliente` y `dropdownClienteTurno`.

**Backup / Export** — `exportar()` usa `navigator.share()` con File API en iOS/Android (abre el share sheet nativo); fallback a `<a download>` con blob URL en desktop. `URL.revokeObjectURL` se llama con `setTimeout(1000)` para evitar cancelar la descarga. El import maneja dos formatos: nuestro formato (`clientes`/`turnos`) y un formato React legacy (`clients`/`appointments`).

## Patrones clave (PC Laravel)

**Configuracion** funciona como un key-value store:
```php
Configuracion::obtener('clave', 'valor_default');
Configuracion::establecer('clave', 'valor');
```

**Agenda**: en `render()` construye el grid del calendario mes a mes, consulta `fechasConTurno` y `fechasBloqueadas` para ese mes. El estado vive en `$anio`, `$mes`, `$diaSeleccionado`.

**Alpine.js + Livewire**: el buscador de clientes en el modal de turno es Alpine puro, sincroniza con Livewire vía `$wire.set('clienteId', ...)`.

## Assets (PC)

Después de cambios en CSS/JS, correr `npm run build` antes del deploy. Los assets compilados van a `public/build/`.
