'use strict'

document.addEventListener('DOMContentLoaded', async _ => {
    await recuperarYLlenarDesplegable()

    document.getElementById('eButCrear').addEventListener('click', crear)
    document.getElementById('eButModificar').addEventListener('click', modificar)
    document.getElementById('eButEliminar').addEventListener('click', eliminar)

    document.getElementById('eBtnGuardar').addEventListener('click', guardar)

})

async function recuperarYLlenarDesplegable() {
    const nSelect = document.getElementById('eSelProfesores')
    // while (nSelect.hasChildNodes() && nSelect.childNodes.length > 3)
    //     nSelect.removeChild(nSelect.lastChild)

    nSelect.innerHTML =
        '<option value="no_seleccionado" disabled selected>Seleccione un profesor ...</option>'

    const url = 'http://127.0.0.1/profesores'
    const respuesta = await fetch(url);
    if (!respuesta.ok) {
        alert('No se han podido recuperar los profesores')
        window.location = '../../error.php'
        return
    }

    const datos = await respuesta.json()
    const profesores = datos.resultado

    for (const { id, nombre, apellidos } of profesores) {
        const nOption = document.createElement('option')
        nOption.setAttribute('value', id)
        nSelect.appendChild(nOption)

        const nTexto = document.createTextNode(`${nombre} ${apellidos}`)
        nOption.appendChild(nTexto)
    }

    nSelect.selectedIndex = 0;
}

async function eliminar(_) {
    const nSelect = document.getElementById('eSelProfesores')
    const profesorId = nSelect.value

    const url = `http://127.0.0.1/profesores/${profesorId}`
    const respuesta = await fetch(url, {
        method: 'DELETE'
    })

    if (!respuesta.ok) {
        alert('No se ha podido eliminar el profesor')
        window.location = '../../error.php'
        return
    }

    alert('Profesor eliminado')

    recuperarYLlenarDesplegable()
}

function crear(e) {
    eFrmPrincipal.classList.toggle('oculto')
    eFrmDatos.classList.toggle('oculto')
    eFrmDatos.operacion = 'crear'
}

async function modificar(e) {
    const profesorId = eSelProfesores.value
    const url = `http://127.0.0.1/profesores/${profesorId}`
    const respuesta = await fetch(url);
    if (!respuesta.ok) {
        alert('No se ha podido recuperar el profesor')
        window.location = '../../error.php'
        return
    }

    const datos = await respuesta.json()
    const profesor = datos.resultado
    eTxtNombre.value = profesor.nombre
    eTxtApellidos.value = profesor.apellidos

    eFrmPrincipal.classList.toggle('oculto')
    eFrmDatos.classList.toggle('oculto')
    eFrmDatos.operacion = 'modificar'
}

async function guardar(_) {
    const nTxtNombre = document.getElementById('eTxtNombre')
    const nTxtApellidos = document.getElementById('eTxtApellidos')

    if (nTxtNombre.value === '') {
        nTxtNombre.placeholder = 'Obligatorio'
        nTxtNombre.focus()
        return
    }

    if (nTxtApellidos.value === '') {
        nTxtApellidos.placeholder = 'Obligatorio'
        nTxtApellidos.focus()
        return
    }

    const datos = new FormData()
    datos.append('nombre', nTxtNombre.value)
    datos.append('apellidos', nTxtApellidos.value)

    if (eFrmDatos.operacion === 'crear') {
        const url = `http://127.0.0.1/profesores`
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        })

        if (!respuesta.ok) {
            alert('No se ha podido crear el profesor')
            window.location = '../../error.php'
            return
        }

        alert('Profesor creado')

        eFrmPrincipal.classList.toggle('oculto')
        eFrmDatos.classList.toggle('oculto')
        recuperarYLlenarDesplegable()
    }

    if (eFrmDatos.operacion === 'modificar') {
        const profesorId = eSelProfesores.value
        datos.append('id', profesorId)
        const url = `http://127.0.0.1/profesores`

        var myHeaders = new Headers();
        myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

        var urlencoded = new URLSearchParams();
        urlencoded.append("id", profesorId);
        urlencoded.append("nombre", nTxtNombre.value);
        urlencoded.append("apellidos", nTxtApellidos.value);

        const respuesta = await fetch(url, {
            method: 'PUT',
            body: urlencoded,
            headers: {
                method: 'PUT',
                headers: myHeaders,
                body: urlencoded,
                redirect: 'follow'
            }
        })

        if (!respuesta.ok) {
            alert('No se ha podido modificar el profesor')
            window.location = '../error.php'
            return
        }

        alert('Profesor modificado')

        eFrmPrincipal.classList.toggle('oculto')
        eFrmDatos.classList.toggle('oculto')
        recuperarYLlenarDesplegable()
    }
}