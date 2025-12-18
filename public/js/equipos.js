let torneo=document.getElementById("formTorneo")
let btnAdd = document.getElementById('btn-add-equipo');
function crearEquipo(event) {
    event.preventDefault();
    let nombre = prompt("Introduce el nombre del equipo:");
    if (!nombre) return;

    let numJugadores = parseInt(prompt("¿Cuántos jugadores tiene?"));
    let listaJugadores = [];

    for (let i = 0; i < numJugadores; i++) {
        let jugador = prompt(`Introduce el nombre del jugador ${i + 1}:`);
        if (jugador) listaJugadores.push(jugador);
    }
    //Parte AJAX para el proyecto
    $.ajax({
    url: '/equipo/guardar',
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
        nombre: nombre,
        jugadores: listaJugadores
    })})
    
}
btnAdd.onclick=crearEquipo