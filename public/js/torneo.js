let unirse = document.getElementById("unirseliga")
let tabs = document.querySelectorAll('.tab');
let section = document.getElementById('torneo-section');
unirse.onclick = anadirusuario
function anadirusuario() {
    let clave = prompt("Introduce la clave privada de la liga:");
    if (!clave) return;
    fetch(`/fantasy/api/liga/unirse`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ clave })
    }).then(response => response.json())
        .then(data => {
            alert(`¡Éxito! Te has unido a la liga: ${data.liga}`);
            window.location.href = `/`
        })
}
let seguir = document.getElementById("seguir")
if (seguir) {
    seguir.onclick = gestionseguidores
}
let crearEvento = document.getElementById("crear-evento");
if (crearEvento) {
    crearEvento.onclick = crearevento
}
function gestionseguidores() {
    let idTorneo = this.getAttribute('data-id');
    let contador = document.getElementById('contador-seguidores');
    let estoySiguiendo = this.classList.contains('btn-danger');
    if (estoySiguiendo) {
        this.textContent = 'Seguir';
        this.classList.replace('btn-danger', 'btn-secondary');
        let actual = parseInt(contador.innerText);
        actual--;
        contador.innerText = actual
    } else {
        this.textContent = 'Dejar de seguir';
        this.classList.replace('btn-secondary', 'btn-danger');
        let actual = parseInt(contador.innerText);
        contador.innerText = actual + 1;
    }
    fetch(`/torneo/${idTorneo}/seguir`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
}
function cargar(url) {
    fetch(url)
        .then(response => response.text())
        .then(html => {
            section.innerHTML = html;
            let btnMostrar = document.getElementById("btnmostrarform");
            let formDisputa = document.getElementById("formnuevadisputa");
            let btnGuardar = document.getElementById("btnguardardisputa");
            if (btnMostrar) {
                btnMostrar.onclick = function () {
                    formDisputa.style.display = formDisputa.style.display === 'none' ? 'block' : 'none';
                }
            }
            if (btnGuardar) {
                btnGuardar.onclick = function () {
                    let equipo1 = document.getElementById("selectequipo1").value;
                    let equipo2 = document.getElementById("selectequipo2").value;
                    let torneo = this.dataset.torneoid;
                    if (!equipo1 || !equipo2) {
                        alert("Selecciona ambos equipos");
                        return;
                    }
                    if (equipo1 === equipo2) {
                        alert("Los equipos deben ser diferentes");
                        return;
                    }
                    fetch('/disputas/crear', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            equipo1_id: equipo1,
                            equipo2_id: equipo2,
                            torneo_id: torneo
                        })
                    }).then(response => response.json())
                        .then(data => window.location.reload());
                }
            }
        })
}
tabs.forEach(tab => {
    tab.onclick = function (e) {
        e.preventDefault()
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        cargar(this.dataset.url);
    }
})
let activa = document.querySelector('.tab.active');
if (activa) {
    cargar(activa.dataset.url);
}
function crearevento() {
    let evento = prompt("Como quieres llamar al evento")
    if (!evento) return
    let puntos = prompt("Quantos puntos quieres que cuente? (sirve para crear la fantasy)")
    if (!puntos) return
    fetch('/crear/evento', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            puntos: puntos,
            evento: evento,
            torneo_id: this.dataset.id
        })
    }).then(response => response.json())
}

function anadirdis(torneo) {
    let equipo1 = prompt("Que equipo quieres poner? (Escribe el id)")
    if (!equipo1) return
    let equipo2 = prompt("Que otro quieres poner?: (Escribe el id)")
    if (!equipo2) return
    fetch('/disputas/crear', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            equipo1_id: equipo1,
            equipo2_id: equipo2,
            torneo_id: torneo
        })
    }).then(response => response.json())
}
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("modificardisputa")) {
        modificaresultado(e.target);
    }
});

function modificaresultado(elemento) {
    let id = elemento.dataset.disputaId;
    let resultado = prompt("Introduce puntos de" + elemento.dataset.disputaEquipo1)
    let resultado2 = prompt("Introduce puntos de" + elemento.dataset.disputaEquipo2)
    let ganador
    if (!resultado) return
    if (!resultado2) return
    if (parseInt(resultado) > parseInt(resultado2)) {
        ganador = elemento.dataset.disputaEquipo1
    } else if (parseInt(resultado) < parseInt(resultado2)) {
        ganador = elemento.dataset.disputaEquipo2
    } else {
        ganador = null
    }
    let resultadototal = resultado + "-" + resultado2
    fetch(`/disputas/${id}/modificar`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            resultado: resultadototal,
            ganador_id: ganador
        })
    }).then(response => response.json())
}