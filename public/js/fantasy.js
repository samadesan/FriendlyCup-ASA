let presupuesto=document.getElementById("budget");
let presupuestoActual = Number(presupuesto.dataset.valor);
let idEquipo = presupuesto.dataset.id;
let contenedorJugadores = document.querySelector('.jugadores');
let pujar=document.querySelectorAll('.btn-pujar');
let botonesVender=document.querySelectorAll('.vender')
botonesVender.forEach(boton=>{
    boton.onclick=vender
})
pujar.forEach(boton => {
    boton.onclick=comprar
})
function comprar() {
    let idJugador = this.dataset.id;
    let nombreJugador = this.dataset.nombre;
    let costoJugador = Number(this.dataset.valor);
    let puntosJugador = this.dataset.puntos; 
    let tarjetaJugador = this.parentElement;
    let contenedorJugadores = document.querySelector('.jugadores'); 
    if (presupuestoActual >= costoJugador) {
        let nuevoSaldo = presupuestoActual - costoJugador;
        fetch(`/fantasy/api/equipo/${idEquipo}/presupuesto`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                presupuesto: nuevoSaldo, 
                idJugador: idJugador 
            })
        })
        .then(() => {
            presupuestoActual = nuevoSaldo;
            presupuesto.textContent = nuevoSaldo; 
            presupuesto.dataset.valor = nuevoSaldo;
            tarjetaJugador.remove();
            let nuevaFicha = document.createElement('div');
            nuevaFicha.classList.add('jugador-card', 'titular');
            nuevaFicha.innerHTML = `
                Jugador: ${nombreJugador} 
                Puntos: ${puntosJugador} 
                Valor de mercado: ${costoJugador}
                <button class="vender"
                    data-id="${idJugador}"
                    data-nombre="${nombreJugador}"
                    data-valor="${costoJugador}"
                    data-puntos="${puntosJugador}">
                    Vender
                </button>`;
                contenedorJugadores.appendChild(nuevaFicha);
                let nuevoBotonVender = nuevaFicha.querySelector('.vender');
                nuevoBotonVender.onclick = vender;
        });
    } else {
        alert("No tienes suficiente saldo");
    }
}
function vender() {
    let idJugador = this.dataset.id;
    let nombreJugador = this.dataset.nombre;
    let valorJugador = Number(this.dataset.valor);
    let puntosJugador = this.dataset.puntos;
    let tarjetaJugador = this.parentElement;
    let contenedorMercado = document.querySelector('.mercado');
    let nuevoSaldo = presupuestoActual + valorJugador;

    fetch(`/fantasy/api/equipo/${idEquipo}/vender`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idJugador: idJugador })
    })
    .then(()=>{
        presupuestoActual = nuevoSaldo;
        presupuesto.textContent = nuevoSaldo; 
        presupuesto.dataset.valor = nuevoSaldo;
        tarjetaJugador.remove();
        let p = document.createElement('p');
        p.innerHTML = `
            Jugador: ${nombreJugador}
            Puntos: ${puntosJugador}
            Valor de mercado: ${valorJugador}
            <button class="btn-pujar"
                data-id="${idJugador}"
                data-nombre="${nombreJugador}"
                data-valor="${valorJugador}"
                data-puntos="${puntosJugador}">
                Pujar
            </button>`;
        contenedorMercado.appendChild(p);
        p.querySelector('.btn-pujar').onclick = comprar;
    });
}