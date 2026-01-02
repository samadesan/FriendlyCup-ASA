let unirse=document.getElementById("unirseliga")
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