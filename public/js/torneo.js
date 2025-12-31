let unirse=document.getElementById("unirseliga")
unirse.onclick=anadirusuario
function anadirusuario() {
    let clave = prompt("Introduce la clave privada de la liga:");
    if (!clave) return;
    fetch(`/api/liga/unirse`, {
            method: 'POST',
            headers: { 
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ clave })
    }).then(()=>{
       window.location.href = `/`
    })
}