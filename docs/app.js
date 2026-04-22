const db = new Dexie('AgendaAndrea');
db.version(1).stores({
    clientes: '++id, nombre', tratamientos: '++id, nombre',
    turnos: '++id, fecha, clienteId, tratamientoId'
});
db.version(2).stores({
    clientes: '++id, nombre', tratamientos: '++id, nombre',
    turnos: '++id, fecha, clienteId, tratamientoId',
    historial: '++id, clienteId, fecha'
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
        historialAll: [],

        mesVista: new Date().getMonth(),
        anioVista: new Date().getFullYear(),
        diaSeleccionado: new Date().getDate(),

        modalActivo: null,
        modoEdicion: false,
        formTurno: {},
        formCliente: {},
        formTratamiento: {},

        // Historial por cliente
        historialClienteId: null,
        historialItems: [],
        mostrarFormHistorial: false,
        formHistorial: {},

        busquedaClientes: '',

        resumenMes: new Date().getMonth(),
        resumenAnio: new Date().getFullYear(),
        estadisticasMesSel: new Date().getMonth(),
        estadisticasAnioSel: new Date().getFullYear(),

        mensajeWA: localStorage.getItem('mensajeWA') ||
            'Hola {nombre}! 👋 Te recuerdo tu turno el {fecha} a las {hora} ({tratamiento}). ¡Saludos, Andrea! 💕',

        MESES, DIAS,

        get aniosDisponibles() {
            const a = new Date().getFullYear();
            return [a-2, a-1, a, a+1].filter(x => x > 2020);
        },

        async init() {
            this.$watch('mensajeWA', val => localStorage.setItem('mensajeWA', val));
            await this.cargar();
        },

        async cargar() {
            this.clientes     = await db.clientes.orderBy('nombre').toArray();
            this.tratamientos = await db.tratamientos.orderBy('nombre').toArray();
            this.turnos       = await db.turnos.toArray();
            this.historialAll = await db.historial.orderBy('fecha').reverse().toArray();
        },

        // ── Navegación ───────────────────────────────────────────────
        ir(s) {
            this.seccion = s;
            this.menuAbierto = false;
            const hoy = new Date();
            // Al volver a agenda, seleccionar hoy si estamos en el mes actual
            if (s === 'agenda' && this.mesVista === hoy.getMonth() && this.anioVista === hoy.getFullYear()) {
                this.diaSeleccionado = hoy.getDate();
            } else if (s !== 'agenda') {
                this.diaSeleccionado = null;
            }
        },

        get titulo() {
            return { agenda:'Agenda', clientes:'Clientes', tratamientos:'Tratamientos',
                     resumen:'Resumen', estadisticas:'Estadísticas', ajustes:'Ajustes' }[this.seccion] || '';
        },

        // ── Calendario ───────────────────────────────────────────────
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

        // ── Turnos ───────────────────────────────────────────────────
        abrirNuevoTurno(dia) {
            const fecha = dia ? this.fechaStr(this.anioVista, this.mesVista, dia) : '';
            this.formTurno = { fecha, hora: '09:00', clienteId: '', tratamientoId: '', notas: '', estado: 'pendiente', valor: 0 };
            this.modoEdicion = false; this.modalActivo = 'turno';
        },

        editarTurno(t) {
            this.formTurno = { ...t }; this.modoEdicion = true; this.modalActivo = 'turno';
        },

        async guardarTurno() {
            if (!this.formTurno.fecha || !this.formTurno.hora || !this.formTurno.clienteId || !this.formTurno.tratamientoId) {
                alert('Completá fecha, hora, cliente y tratamiento.'); return;
            }
            const d = {
                fecha: this.formTurno.fecha, hora: this.formTurno.hora,
                clienteId: Number(this.formTurno.clienteId),
                tratamientoId: Number(this.formTurno.tratamientoId),
                notas: this.formTurno.notas || '',
                estado: this.formTurno.estado || 'pendiente',
                valor: Number(this.formTurno.valor) || 0,
            };
            this.formTurno.id ? await db.turnos.update(this.formTurno.id, d) : await db.turnos.add(d);
            await this.cargar(); this.cerrarModal();
        },

        async eliminarTurno(id) {
            if (!confirm('¿Eliminar este turno?')) return;
            await db.turnos.delete(id); await this.cargar();
        },

        // ── Clientes ─────────────────────────────────────────────────
        get clientesFiltrados() {
            if (!this.busquedaClientes) return this.clientes;
            const q = this.busquedaClientes.toLowerCase();
            return this.clientes.filter(c =>
                c.nombre.toLowerCase().includes(q) ||
                (c.apellido||'').toLowerCase().includes(q) ||
                (c.telefono||'').includes(q)
            );
        },

        proximoTurnoDeCliente(clienteId) {
            const hoy = new Date().toISOString().slice(0,10);
            return this.turnos
                .filter(t => t.clienteId === clienteId && t.fecha >= hoy)
                .sort((a,b) => a.fecha.localeCompare(b.fecha) || a.hora.localeCompare(b.hora))[0] || null;
        },

        abrirNuevoCliente() {
            this.formCliente = { nombre:'', apellido:'', telefono:'', notas:'' };
            this.modoEdicion = false; this.modalActivo = 'cliente';
        },

        editarCliente(c) {
            this.formCliente = { ...c }; this.modoEdicion = true; this.modalActivo = 'cliente';
        },

        async guardarCliente() {
            if (!this.formCliente.nombre) { alert('El nombre es requerido.'); return; }
            const d = { nombre: this.formCliente.nombre, apellido: this.formCliente.apellido||'',
                        telefono: this.formCliente.telefono||'', notas: this.formCliente.notas||'' };
            this.formCliente.id ? await db.clientes.update(this.formCliente.id, d) : await db.clientes.add(d);
            await this.cargar(); this.cerrarModal();
        },

        async eliminarCliente(id) {
            if (this.turnos.some(t => t.clienteId === id)) { alert('Tiene turnos asignados. Eliminá los turnos primero.'); return; }
            if (!confirm('¿Eliminar esta cliente?')) return;
            await db.historial.where('clienteId').equals(id).delete();
            await db.clientes.delete(id); await this.cargar();
        },

        abrirWhatsApp(c) {
            if (!c.telefono) { alert('Esta cliente no tiene teléfono registrado.'); return; }
            const proximo = this.proximoTurnoDeCliente(c.id);
            const msg = this.mensajeWA
                .replace('{nombre}', c.nombre)
                .replace('{fecha}', proximo ? this.formatFecha(proximo.fecha) : 'a confirmar')
                .replace('{hora}', proximo ? proximo.hora : '')
                .replace('{tratamiento}', proximo ? this.nombreTratamiento(proximo.tratamientoId) : '');
            const tel = c.telefono.replace(/\D/g, '');
            const num = tel.length === 10 ? '549' + tel : tel;
            window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
        },

        // ── Historial ────────────────────────────────────────────────
        async verHistorial(clienteId) {
            this.historialClienteId = clienteId;
            await this.recargarHistorial();
            this.mostrarFormHistorial = false;
            this.formHistorial = { fecha: new Date().toISOString().slice(0,10), tratamientoId: '', nota: '', importe: 0 };
            this.modalActivo = 'historial';
        },

        async recargarHistorial() {
            this.historialItems = await db.historial
                .where('clienteId').equals(this.historialClienteId)
                .reverse().sortBy('fecha');
        },

        get clienteHistorialActual() {
            return this.clientes.find(c => c.id === this.historialClienteId) || null;
        },

        get historialTotal() {
            return this.historialItems.reduce((s, h) => s + Number(h.importe||0), 0);
        },

        abrirFormHistorial() {
            this.formHistorial = { fecha: new Date().toISOString().slice(0,10), tratamientoId: '', nota: '', importe: 0 };
            this.mostrarFormHistorial = true;
        },

        async guardarHistorial() {
            if (!this.formHistorial.fecha) { alert('Ingresá la fecha.'); return; }
            const d = {
                clienteId: this.historialClienteId,
                fecha: this.formHistorial.fecha,
                tratamientoId: this.formHistorial.tratamientoId ? Number(this.formHistorial.tratamientoId) : null,
                nota: this.formHistorial.nota || '',
                importe: Number(this.formHistorial.importe) || 0,
            };
            await db.historial.add(d);
            await this.recargarHistorial();
            await this.cargar();
            this.mostrarFormHistorial = false;
        },

        async eliminarHistorial(id) {
            if (!confirm('¿Eliminar esta entrada del historial?')) return;
            await db.historial.delete(id);
            await this.recargarHistorial();
            await this.cargar();
        },

        // ── Tratamientos ─────────────────────────────────────────────
        abrirNuevoTratamiento() {
            this.formTratamiento = { nombre:'' };
            this.modoEdicion = false; this.modalActivo = 'tratamiento';
        },

        editarTratamiento(t) {
            this.formTratamiento = { ...t }; this.modoEdicion = true; this.modalActivo = 'tratamiento';
        },

        async guardarTratamiento() {
            if (!this.formTratamiento.nombre) { alert('El nombre es requerido.'); return; }
            const d = { nombre: this.formTratamiento.nombre };
            this.formTratamiento.id ? await db.tratamientos.update(this.formTratamiento.id, d) : await db.tratamientos.add(d);
            await this.cargar(); this.cerrarModal();
        },

        async eliminarTratamiento(id) {
            if (this.turnos.some(t => t.tratamientoId === id)) { alert('Está en uso en turnos. Eliminá los turnos primero.'); return; }
            if (!confirm('¿Eliminar este tratamiento?')) return;
            await db.tratamientos.delete(id); await this.cargar();
        },

        // ── Helpers ──────────────────────────────────────────────────
        nombreCliente(id) {
            const c = this.clientes.find(c => c.id === id);
            return c ? `${c.nombre}${c.apellido ? ' '+c.apellido : ''}` : '—';
        },

        nombreTratamiento(id) {
            const t = this.tratamientos.find(t => t.id === id);
            return t ? t.nombre : '—';
        },

        formatFecha(f) {
            if (!f) return '';
            const [y,m,d] = f.split('-');
            return `${d}/${m}/${y}`;
        },

        formatPrecio(n) { return '$' + Number(n||0).toLocaleString('es-AR'); },

        cerrarModal() { this.modalActivo = null; this.historialClienteId = null; },

        // ── Resumen ──────────────────────────────────────────────────
        get resumenDatos() {
            const pref = this.resumenMes === -1
                ? `${this.resumenAnio}-`
                : `${this.resumenAnio}-${String(this.resumenMes+1).padStart(2,'0')}`;
            const entradas = this.turnos.filter(t => t.fecha && t.fecha.startsWith(pref));
            const totalIngresos = entradas.reduce((s, t) => s + Number(t.valor||0), 0);

            const grupos = {};
            entradas.forEach(t => {
                const key = t.tratamientoId || '__sin__';
                if (!grupos[key]) grupos[key] = { nombre: t.tratamientoId ? this.nombreTratamiento(t.tratamientoId) : 'Sin tratamiento', count: 0, total: 0 };
                grupos[key].count++;
                grupos[key].total += Number(t.valor||0);
            });

            return {
                total: entradas.length,
                ingresos: totalIngresos,
                clientesUnicos: new Set(entradas.map(t => t.clienteId)).size,
                porTratamiento: Object.values(grupos).sort((a,b) => b.total - a.total),
            };
        },

        // ── Estadísticas ─────────────────────────────────────────────
        get estadisticasMensuales() {
            const indices = this.estadisticasMesSel === -1
                ? Array.from({ length: 12 }, (_, i) => i)
                : [this.estadisticasMesSel];
            return indices.map(i => {
                const pref = `${this.estadisticasAnioSel}-${String(i+1).padStart(2,'0')}`;
                const ts = this.turnos.filter(t => t.fecha && t.fecha.startsWith(pref));
                const ingresos = ts.reduce((s,t) => s + Number(t.valor||0), 0);
                return { mes: MESES[i].slice(0,3), total: ts.length, ingresos };
            });
        },

        get maxEstadisticas() {
            return Math.max(1, ...this.estadisticasMensuales.map(e => e.total));
        },

        // ── Backup ───────────────────────────────────────────────────
        async exportar() {
            const datos = { version:2, exportado: new Date().toISOString(),
                            clientes: this.clientes, tratamientos: this.tratamientos,
                            turnos: this.turnos, historial: this.historialAll };
            const blob = new Blob([JSON.stringify(datos, null, 2)], { type: 'application/json' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href = url; a.download = `agenda-backup-${new Date().toISOString().slice(0,10)}.json`;
            document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
        },

        async importar(e) {
            const file = e.target.files[0]; if (!file) return;
            try {
                const datos = JSON.parse(await file.text());
                if (!confirm('Esto reemplazará TODOS los datos actuales. ¿Continuar?')) return;
                await db.transaction('rw', db.clientes, db.tratamientos, db.turnos, db.historial, async () => {
                    await db.clientes.clear(); await db.tratamientos.clear();
                    await db.turnos.clear(); await db.historial.clear();

                    if (datos.clientes?.length) {
                        await db.clientes.bulkAdd(datos.clientes.map(({ id, celular, telefono, ...c }) => ({
                            ...c,
                            telefono: telefono || celular || '',  // soporta ambos nombres
                        })));
                    }

                    if (datos.tratamientos?.length) {
                        await db.tratamientos.bulkAdd(datos.tratamientos.map(({ id, ...t }) => t));
                    }

                    if (datos.turnos?.length) {
                        await db.turnos.bulkAdd(datos.turnos.map(({ id, cobrado, estado, ...t }) => ({
                            ...t,
                            // soporta "cobrado: true/false" y "estado: 'pagado'/'pendiente'"
                            estado: estado || (cobrado ? 'pagado' : 'pendiente'),
                        })));
                    }

                    if (datos.historial?.length) {
                        await db.historial.bulkAdd(datos.historial.map(({ id, ...h }) => h));
                    }
                });
                await this.cargar(); alert('Datos importados correctamente.');
            } catch(err) { alert('Error al importar: ' + err.message); }
            e.target.value = '';
        },
    };
}
