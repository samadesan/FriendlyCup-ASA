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

tabs.forEach(tab => {
    tab.onclick= function (e) {
        e.preventDefault()
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        fetch(this.dataset.url)
        .then(r => r.text())
        .then(html=>{
            section.innerHTML = html;
        })
    }
})
let activa = document.querySelector('.tab.active');
if (activa) {
    cargar(activa.dataset.url);
}