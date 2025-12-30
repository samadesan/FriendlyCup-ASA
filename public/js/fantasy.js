let presupuesto=document.getElementById("budget");
let presupuestoActual = Number(presupuesto.dataset.valor);
let id = window.location.pathname.split('/').pop();
let pujar=document.querySelectorAll('.btn-pujar');
pujar.forEach(boton => {
    boton.onclick=comprar
})
function comprar() {
    let costoJugador = Number(this.dataset.valor);
    if (presupuestoActual >= costoJugador) {
        let nuevoSaldo = presupuestoActual - costoJugador;
        fetch(`fantasy/api/equipo/${id}/presupuesto`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ presupuesto: nuevoSaldo })
    }).then(()=>{
        presupuestoActual=nuevoSaldo
        presupuesto.textContent = nuevoSaldo; 
        presupuesto.dataset.valor = nuevoSaldo;presupuesto.textContent = nuevoSaldo; 
        presupuesto.dataset.valor = nuevoSaldo;
    })
    }else{
        alert("No tienes suficiente saldo")
    }
    
}
function vender() {
    
}

function porcentajedeventa() {
    presupuestoActual+=(presupuestoActual*0,15)
}