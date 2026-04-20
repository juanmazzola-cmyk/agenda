# ESTADO - Agenda Esteticista

## Stack
- PHP 8.2 / Laravel 11 / Livewire 3 full-page (sin controllers tradicionales)
- MySQL / XAMPP / VS Code

## Reglas del proyecto
- Livewire full-page siempre, sin controllers tradicionales
- Sin SQLite
- Componentes en español (variables, métodos, vistas)
- Confirmar antes de eliminar cualquier registro

## Estado actual
- [x] Arquitectura definida
- [x] Migraciones creadas
- [x] Módulo Clientes
- [x] Módulo Tratamientos
- [x] Módulo Agenda
- [x] Módulo Resumen económico
- [x] Módulo Estadísticas
- [ ] Módulo Ajustes

## Módulos planificados

### Clientes
Tabla: id, nombre, apellido, celular (+54), timestamps
Acciones por fila: WhatsApp (wa.me), Historial (modal), Editar, Eliminar

### Tratamientos
Tabla: id, nombre, timestamps
Acciones por fila: Editar, Eliminar

### Agenda
Tabla: id, cliente_id, tratamiento_id, fecha, hora, valor, cobrado (bool), notas, timestamps
- Vista calendario mensual, días con turno marcados visualmente
- Formulario de turno: cliente, tratamiento, día, hora, valor, cobrado, notas (opcional)
- Debajo del calendario: lista de turnos del día con WhatsApp, Editar, Eliminar

### Resumen económico
- Vista mensual y anual
- Tabla por tratamiento: cantidad y total cobrado
- Filtro: cobrados / todos

### Estadísticas
- Visitas por cliente
- Veces por tratamiento

### Ajustes
- Exportar backup (JSON)
- Importar backup (JSON)
- Mensaje de WhatsApp predeterminado (guardado en DB o config)

## Archivos clave
- `database/migrations/2026_04_20_160649_create_clientes_table.php`
- `database/migrations/2026_04_20_160700_create_tratamientos_table.php`
- `database/migrations/2026_04_20_160710_create_turnos_table.php`
- `app/Models/Cliente.php`
- `app/Models/Tratamiento.php`
- `app/Models/Turno.php`
- `app/Livewire/Clientes.php`
- `resources/views/livewire/clientes.blade.php`
- `app/Livewire/Tratamientos.php`
- `resources/views/livewire/tratamientos.blade.php`
- `app/Livewire/Agenda.php`
- `resources/views/livewire/agenda.blade.php`
- `resources/views/components/layouts/app.blade.php`

## Decisiones tomadas
- Livewire full-page, sin controllers tradicionales
- Modelos en español (variables, métodos, vistas)
- Confirmar antes de eliminar cualquier registro

## Próxima tarea
Módulo Ajustes: backup JSON, importar JSON, mensaje WhatsApp predeterminado