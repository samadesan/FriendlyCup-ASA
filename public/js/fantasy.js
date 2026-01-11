let presupuesto = document.getElementById("budget");
let maximo = Number(presupuesto.dataset.minimo);
let presupuestoActual = Number(presupuesto.dataset.valor);
let idEquipo = presupuesto.dataset.id;

let tablas = document.querySelectorAll('.torneo-content .stats-table tbody');
let tablaJugadores = tablas[0]; // primera tabla = mis jugadores
let tablaMercado = tablas[1];   // segunda tabla = mercado

let admin = document.getElementById("btnAnadirUsuario");
if (admin) admin.onclick = mostrarClave;

asignarEventosBotones();

function asignarEventosBotones() {
    let botonesVender = tablaJugadores.querySelectorAll('.vender');
    botonesVender.forEach(boton => boton.onclick = vender);

    let botonesPujar = tablaMercado.querySelectorAll('.btn-pujar');
    botonesPujar.forEach(boton => boton.onclick = comprar);
}

function comprar() {
    let numJugadoresActuales = tablaJugadores.querySelectorAll('tr').length;
    if (numJugadoresActuales >= maximo) {
        alert(`¡Has alcanzado el número máximo de jugadores (${maximo})!`);
        return;
    }

    let fila = this.closest('tr');
    let idJugador = this.dataset.id;
    let nombre = this.dataset.nombre;
    let valor = Number(this.dataset.valor);
    let puntos = this.dataset.puntos;

    if (presupuestoActual < valor) {
        alert("No tienes suficiente saldo");
        return;
    }

    let nuevoSaldo = presupuestoActual - valor;

    fetch(`/fantasy/api/equipo/${idEquipo}/presupuesto`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ presupuesto: nuevoSaldo, idJugador: idJugador })
    }).then(() => {
        presupuestoActual = nuevoSaldo;
        presupuesto.textContent = nuevoSaldo;
        presupuesto.dataset.valor = nuevoSaldo;
        fila.remove();

        let nuevaFila = tablaJugadores.insertRow();
        nuevaFila.innerHTML = `
            <td>${nombre}</td>
            <td>${puntos}</td>
            <td>${valor} €</td>
            <td>
                <button class="vender"
                    data-id="${idJugador}"
                    data-nombre="${nombre}"
                    data-valor="${valor}"
                    data-puntos="${puntos}">
                    Vender
                </button>
            </td>`;
        nuevaFila.querySelector('.vender').onclick = vender;
    });
}

function vender() {
    let fila = this.closest('tr');
    let idJugador = this.dataset.id;
    let nombre = this.dataset.nombre;
    let valor = Number(this.dataset.valor);
    let puntos = this.dataset.puntos;
    let nuevoSaldo = presupuestoActual + valor;

    fetch(`/fantasy/api/equipo/${idEquipo}/vender`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idJugador: idJugador })
    }).then(() => {
        presupuestoActual = nuevoSaldo;
        presupuesto.textContent = nuevoSaldo;
        presupuesto.dataset.valor = nuevoSaldo;
        fila.remove();

        let nuevaFila = tablaMercado.insertRow();
        nuevaFila.innerHTML = `
            <td>${nombre}</td>
            <td>${puntos}</td>
            <td>${valor} €</td>
            <td>
                <button class="btn-pujar"
                    data-id="${idJugador}"
                    data-nombre="${nombre}"
                    data-valor="${valor}"
                    data-puntos="${puntos}">
                    Pujar
                </button>
            </td>`;
        nuevaFila.querySelector('.btn-pujar').onclick = comprar;
    });
}

function mostrarClave() {
    let clave = this.dataset.clave;
    alert('La clave para unirse a esta liga es: ' + clave);
}