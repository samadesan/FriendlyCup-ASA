let unirse=document.getElementById("unirseliga")
let tabs = document.querySelectorAll('.tab');
let section = document.getElementById('torneo-section');
unirse.onclick=anadirusuario
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
    .then(data=>{
        alert(`¡Éxito! Te has unido a la liga: ${data.liga}`);
       window.location.href = `/`
    })
}
let seguir=document.getElementById("seguir")
if (seguir) {
    seguir.onclick=gestionseguidores
}
let crearEvento = document.getElementById("crear-evento");
if (crearEvento) {
    crearEvento.onclick=crearevento
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
    }else{
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
        .then(r => r.text())
        .then(html=>{
            section.innerHTML = html;
            let anadirdisputa=document.getElementById("anadirdisputa")
            if (anadirdisputa) {
                anadirdisputa.onclick=function() {
                    anadirdis(anadirdisputa.dataset.torneoId);
                    window.location.reload();
                }
            }
        })
}
tabs.forEach(tab => {
    tab.onclick= function (e) {
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
    let evento=prompt("Como quieres llamar al evento")
    if (!evento) return
    let puntos=prompt("Quantos puntos quieres que cuente? (sirve para crear la fantasy)")
    if (!puntos) return
    fetch('/crear/evento', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        puntos:puntos,
        evento:evento,
        torneo_id:this.dataset.id
        })
    }).then(response => response.json())
}

function anadirdis(torneo) {
    let equipo1=prompt("Que equipo quieres poner? (Escribe el id)")
    if (!equipo1) return
    let equipo2=prompt("Que otro quieres poner?: (Escribe el id)")
    if (!equipo2) return
    fetch('/disputas/crear', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        equipo1_id:equipo1,
        equipo2_id:equipo2,
        torneo_id:torneo
        })
    }).then(response => response.json())
}