const db = new Dexie('AgendaAndrea');
db.version(1).stores({
    clientes:      '++id, nombre',
    tratamientos:  '++id, nombre',
    turnos:        '++id, fecha, clienteId, tratamientoId'
});

const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const DIAS  = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];

function agendaApp() {
    return {
        seccion: 'agenda',
        menuAbierto: false,

        clientes: [],
        tratamientos: [],
        turnos: [],

        mesVista: new Date().getMonth(),
        anioVista: new Date().getFullYear(),
        diaSeleccionado: null,

        modalActivo: null,
        modoEdicion: false,
        formTurno: {},
        formCliente: {},
        formTratamiento: {},
        busquedaClientes: '',

        MESES,
        DIAS,

        async init() {
            await this.cargar();
        },

        async cargar() {
            this.clientes     = await db.clientes.orderBy('nombre').toArray();
            this.tratamientos = await db.tratamientos.orderBy('nombre').toArray();
            this.turnos       = await db.turnos.toArray();
        },

        // ── Navegación ──────────────────────────────────────────────
        ir(s) {
            this.seccion = s;
            this.menuAbierto = false;
            this.diaSeleccionado = null;
        },

        get titulo() {
            return { agenda:'Agenda', clientes:'Clientes', tratamientos:'Tratamientos',
                     resumen:'Resumen', estadisticas:'Estadísticas', ajustes:'Ajustes' }[this.seccion] || '';
        },

        // ── Calendario ──────────────────────────────────────────────
        get nombreMesVista() { return `${MESES[this.mesVista]} ${this.anioVista}`; },

        get diasCalendario() {
            const primer = new Date(this.anioVista, this.mesVista, 1);
            const ultimo = new Date(this.anioVista, this.mesVista + 1, 0);
            let offset = primer.getDay();
            offset = offset === 0 ? 6 : offset - 1;
            const dias = Array(offset).fill(null);
            for (let d = 1; d <= ultimo.getDate(); d++) dias.push(d);
            return dias;
        },

        fechaStr(anio, mes, dia) {
            return `${anio}-${String(mes+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
        },

        turnosDia(dia) {
            if (!dia) return [];
            const f = this.fechaStr(this.anioVista, this.mesVista, dia);
            return this.turnos.filter(t => t.fecha === f).sort((a,b) => a.hora.localeCompare(b.hora));
        },

        esHoy(dia) {
            const h = new Date();
            return dia && dia === h.getDate() && this.mesVista === h.getMonth() && this.anioVista === h.getFullYear();
        },

        mesAnterior() {
            if (this.mesVista === 0) { this.mesVista = 11; this.anioVista--; } else this.mesVista--;
            this.diaSeleccionado = null;
        },

        mesSiguiente() {
            if (this.mesVista === 11) { this.mesVista = 0; this.anioVista++; } else this.mesVista++;
            this.diaSeleccionado = null;
        },

        // ── Turnos ──────────────────────────────────────────────────
        abrirNuevoTurno(dia) {
            const fecha = dia ? this.fechaStr(this.anioVista, this.mesVista, dia) : '';
            this.formTurno = { fecha, hora: '09:00', clienteId: '', tratamientoId: '', notas: '', estado: 'pendiente' };
            this.modoEdicion = false;
            this.modalActivo = 'turno';
        },

        editarTurno(t) {
            this.formTurno = { ...t };
            this.modoEdicion = true;
            this.modalActivo = 'turno';
        },

        async guardarTurno() {
            if (!this.formTurno.fecha || !this.formTurno.hora || !this.formTurno.clienteId || !this.formTurno.tratamientoId) {
                alert('Completá fecha, hora, cliente y tratamiento.');
                return;
            }
            const d = {
                fecha: this.formTurno.fecha, hora: this.formTurno.hora,
                clienteId: Number(this.formTurno.clienteId), tratamientoId: Number(this.formTurno.tratamientoId),
                notas: this.formTurno.notas || '', estado: this.formTurno.estado || 'pendiente',
            };
            this.formTurno.id ? await db.turnos.update(this.formTurno.id, d) : await db.turnos.add(d);
            await this.cargar();
            this.cerrarModal();
        },

        async eliminarTurno(id) {
            if (!confirm('¿Eliminar este turno?')) return;
            await db.turnos.delete(id);
            await this.cargar();
        },

        colorEstado(e) {
            return { pendiente:'bg-yellow-100 text-yellow-700', confirmado:'bg-green-100 text-green-700',
                     cancelado:'bg-red-100 text-red-600', completado:'bg-blue-100 text-blue-700' }[e] || 'bg-gray-100 text-gray-600';
        },

        // ── Clientes ────────────────────────────────────────────────
        get clientesFiltrados() {
            if (!this.busquedaClientes) return this.clientes;
            const q = this.busquedaClientes.toLowerCase();
            return this.clientes.filter(c =>
                c.nombre.toLowerCase().includes(q) ||
                (c.apellido||'').toLowerCase().includes(q) ||
                (c.telefono||'').includes(q)
            );
        },

        abrirNuevoCliente() {
            this.formCliente = { nombre:'', apellido:'', telefono:'', email:'', notas:'' };
            this.modoEdicion = false; this.modalActivo = 'cliente';
        },

        editarCliente(c) {
            this.formCliente = { ...c };
            this.modoEdicion = true; this.modalActivo = 'cliente';
        },

        async guardarCliente() {
            if (!this.formCliente.nombre) { alert('El nombre es requerido.'); return; }
            const d = { nombre: this.formCliente.nombre, apellido: this.formCliente.apellido||'',
                        telefono: this.formCliente.telefono||'', email: this.formCliente.email||'', notas: this.formCliente.notas||'' };
            this.formCliente.id ? await db.clientes.update(this.formCliente.id, d) : await db.clientes.add(d);
            await this.cargar(); this.cerrarModal();
        },

        async eliminarCliente(id) {
            if (this.turnos.some(t => t.clienteId === id)) { alert('Tiene turnos asignados. Eliminá los turnos primero.'); return; }
            if (!confirm('¿Eliminar esta cliente?')) return;
            await db.clientes.delete(id); await this.cargar();
        },

        // ── Tratamientos ────────────────────────────────────────────
        abrirNuevoTratamiento() {
            this.formTratamiento = { nombre:'', duracion:60, precio:0 };
            this.modoEdicion = false; this.modalActivo = 'tratamiento';
        },

        editarTratamiento(t) {
            this.formTratamiento = { ...t };
            this.modoEdicion = true; this.modalActivo = 'tratamiento';
        },

        async guardarTratamiento() {
            if (!this.formTratamiento.nombre) { alert('El nombre es requerido.'); return; }
            const d = { nombre: this.formTratamiento.nombre,
                        duracion: Number(this.formTratamiento.duracion)||60,
                        precio: Number(this.formTratamiento.precio)||0 };
            this.formTratamiento.id ? await db.tratamientos.update(this.formTratamiento.id, d) : await db.tratamientos.add(d);
            await this.cargar(); this.cerrarModal();
        },

        async eliminarTratamiento(id) {
            if (this.turnos.some(t => t.tratamientoId === id)) { alert('Está en uso en turnos. Eliminá los turnos primero.'); return; }
            if (!confirm('¿Eliminar este tratamiento?')) return;
            await db.tratamientos.delete(id); await this.cargar();
        },

        // ── Helpers ─────────────────────────────────────────────────
        nombreCliente(id) {
            const c = this.clientes.find(c => c.id === id);
            return c ? `${c.nombre}${c.apellido ? ' '+c.apellido : ''}` : '—';
        },

        nombreTratamiento(id) {
            const t = this.tratamientos.find(t => t.id === id);
            return t ? t.nombre : '—';
        },

        precioTratamiento(id) {
            const t = this.tratamientos.find(t => t.id === id);
            return t ? Number(t.precio) : 0;
        },

        formatFecha(f) {
            if (!f) return '';
            const [y,m,d] = f.split('-');
            return `${d}/${m}/${y}`;
        },

        formatPrecio(n) { return '$' + Number(n||0).toLocaleString('es-AR'); },

        cerrarModal() { this.modalActivo = null; },

        // ── Resumen ─────────────────────────────────────────────────
        get resumenMes() {
            const h = new Date();
            const pref = `${h.getFullYear()}-${String(h.getMonth()+1).padStart(2,'0')}`;
            const ts = this.turnos.filter(t => t.fecha.startsWith(pref) && t.estado !== 'cancelado');
            let ingresos = 0;
            ts.forEach(t => { ingresos += this.precioTratamiento(t.tratamientoId); });
            return { mes: MESES[h.getMonth()], total: ts.length, ingresos, clientes: new Set(ts.map(t => t.clienteId)).size };
        },

        get proximosTurnos() {
            const hoy = new Date().toISOString().slice(0,10);
            return this.turnos
                .filter(t => t.fecha >= hoy && t.estado !== 'cancelado')
                .sort((a,b) => a.fecha.localeCompare(b.fecha) || a.hora.localeCompare(b.hora))
                .slice(0, 6);
        },

        // ── Estadísticas ────────────────────────────────────────────
        get estadisticasAnio() {
            const anio = new Date().getFullYear();
            return Array.from({ length: 12 }, (_, i) => {
                const pref = `${anio}-${String(i+1).padStart(2,'0')}`;
                const ts = this.turnos.filter(t => t.fecha.startsWith(pref) && t.estado !== 'cancelado');
                let ingresos = 0;
                ts.forEach(t => { ingresos += this.precioTratamiento(t.tratamientoId); });
                return { mes: MESES[i].slice(0,3), total: ts.length, ingresos };
            });
        },

        get maxTurnosAnio() {
            return Math.max(1, ...this.estadisticasAnio.map(e => e.total));
        },

        // ── Backup ──────────────────────────────────────────────────
        async exportar() {
            const datos = { version:1, exportado: new Date().toISOString(),
                            clientes: this.clientes, tratamientos: this.tratamientos, turnos: this.turnos };
            const blob = new Blob([JSON.stringify(datos, null, 2)], { type: 'application/json' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href = url;
            a.download = `agenda-backup-${new Date().toISOString().slice(0,10)}.json`;
            document.body.appendChild(a); a.click();
            document.body.removeChild(a); URL.revokeObjectURL(url);
        },

        async importar(e) {
            const file = e.target.files[0];
            if (!file) return;
            try {
                const datos = JSON.parse(await file.text());
                if (!confirm('Esto reemplazará TODOS los datos actuales. ¿Continuar?')) return;
                await db.transaction('rw', db.clientes, db.tratamientos, db.turnos, async () => {
                    await db.clientes.clear();
                    await db.tratamientos.clear();
                    await db.turnos.clear();
                    if (datos.clientes?.length)
                        await db.clientes.bulkAdd(datos.clientes.map(({id,...c}) => c));
                    if (datos.tratamientos?.length)
                        await db.tratamientos.bulkAdd(datos.tratamientos.map(({id,...t}) => t));
                    if (datos.turnos?.length)
                        await db.turnos.bulkAdd(datos.turnos.map(({id,...t}) => t));
                });
                await this.cargar();
                alert('Datos importados correctamente.');
            } catch(err) {
                alert('Error al importar: ' + err.message);
            }
            e.target.value = '';
        },
    };
}
