document.addEventListener('DOMContentLoaded', () => {
  const restaurantes = JSON.parse(document.getElementById('restaurantes-data').textContent);
  const mesas = JSON.parse(document.getElementById('mesas-data').textContent);

  // Botones modificar
  document.querySelectorAll('.btn-modificar').forEach(button => {
    button.addEventListener('click', () => {
      const tr = button.closest('tr');
      activarEdicion(tr);
    });
  });

  // Botones aplicar cambios
  document.querySelectorAll('.aplicar-cambio').forEach(button => {
    button.addEventListener('click', () => {
      if (button.textContent.trim() === 'Guardar') {
        const tr = button.closest('tr');
        guardarCambios(tr);
      }
    });
  });

  // Botones borrar/cancelar
  document.querySelectorAll('.btn-borrar').forEach(button => {
    button.addEventListener('click', () => {
      const tr = button.closest('tr');
      if (button.textContent.trim() === 'Cancelar') {
        cancelarEdicion(tr);
      } else {
        if (confirm("¿Seguro que quieres eliminar esta reserva?")) {
          borrarReserva(tr.dataset.id);
        }
      }
    });
  });

  // Cambiar estado
  document.querySelectorAll('.estado-select').forEach(select => {
    select.addEventListener('change', () => {
      const tr = select.closest('tr');
      actualizarEstado(tr.dataset.id, select.value);
    });
  });

  // Funciones

  function activarEdicion(tr) {
    tr.querySelector('.btn-modificar').style.display = 'none';

    const aplicarBtn = tr.querySelector('.aplicar-cambio');
    aplicarBtn.textContent = 'Guardar';
    aplicarBtn.classList.replace('btn-success', 'btn-primary');

    const borrarBtn = tr.querySelector('.btn-borrar');
    borrarBtn.textContent = 'Cancelar';
    borrarBtn.classList.replace('btn-danger', 'btn-secondary');

    // Guardar valores originales
    tr.dataset.restauranteOriginal = tr.querySelector('.restaurante-text').textContent;
    tr.dataset.mesaOriginal = tr.querySelector('.mesa-text').textContent;
    tr.dataset.fechaOriginal = tr.querySelector('.fecha-text').textContent;
    tr.dataset.horaOriginal = tr.querySelector('.hora-text').textContent;

    // Crear selects e inputs
    const restauranteTD = tr.querySelector('.restaurante-text');
    const mesaTD = tr.querySelector('.mesa-text');
    const fechaTD = tr.querySelector('.fecha-text');
    const horaTD = tr.querySelector('.hora-text');

    // Restaurante select
    const selectRest = document.createElement('select');
    selectRest.className = 'form-select form-select-sm edit-restaurante';
    restaurantes.forEach(r => {
      const option = document.createElement('option');
      option.value = r.id;
      option.textContent = r.nombre;
      if (r.nombre === restauranteTD.textContent) option.selected = true;
      selectRest.appendChild(option);
    });
    restauranteTD.textContent = '';
    restauranteTD.appendChild(selectRest);

    // Mesa select
    const selectMesa = document.createElement('select');
    selectMesa.className = 'form-select form-select-sm edit-mesa';

    function cargarMesas(restauranteId) {
      selectMesa.innerHTML = '';
      mesas.filter(m => m.restaurante_id == restauranteId).forEach(m => {
        const option = document.createElement('option');
        option.value = m.id;
        option.textContent = m.numero;
        if (m.numero === mesaTD.textContent) option.selected = true;
        selectMesa.appendChild(option);
      });
    }

    cargarMesas(selectRest.value);

    selectRest.addEventListener('change', () => {
      cargarMesas(selectRest.value);
    });

    mesaTD.textContent = '';
    mesaTD.appendChild(selectMesa);

    // Fecha input
    const inputFecha = document.createElement('input');
    inputFecha.type = 'date';
    inputFecha.className = 'form-control form-control-sm edit-fecha';
    inputFecha.value = fechaTD.textContent;
    fechaTD.textContent = '';
    fechaTD.appendChild(inputFecha);

    // Hora input
    const inputHora = document.createElement('input');
    inputHora.type = 'time';
    inputHora.className = 'form-control form-control-sm edit-hora';
    inputHora.value = horaTD.textContent;
    horaTD.textContent = '';
    horaTD.appendChild(inputHora);
  }

  function cancelarEdicion(tr) {
    tr.querySelector('.btn-modificar').style.display = 'inline-block';

    const aplicarBtn = tr.querySelector('.aplicar-cambio');
    aplicarBtn.textContent = '';
    aplicarBtn.classList.replace('btn-primary', 'btn-success');
    aplicarBtn.innerHTML = '<i class="bi bi-check-lg"></i>';

    const borrarBtn = tr.querySelector('.btn-borrar');
    borrarBtn.textContent = '';
    borrarBtn.classList.replace('btn-secondary', 'btn-danger');
    borrarBtn.innerHTML = '<i class="bi bi-trash"></i>';

    // Restaurar textos
    tr.querySelector('.restaurante-text').textContent = tr.dataset.restauranteOriginal;
    tr.querySelector('.mesa-text').textContent = tr.dataset.mesaOriginal;
    tr.querySelector('.fecha-text').textContent = tr.dataset.fechaOriginal;
    tr.querySelector('.hora-text').textContent = tr.dataset.horaOriginal;
  }

  function guardarCambios(tr) {
    const reserva_id = tr.dataset.id;
    const restaurante_id = tr.querySelector('.edit-restaurante').value;
    const mesa_id = tr.querySelector('.edit-mesa').value;
    const fecha = tr.querySelector('.edit-fecha').value;
    const hora = tr.querySelector('.edit-hora').value;

    if (!fecha || !hora) {
      alert('Por favor, ingrese fecha y hora válidas');
      return;
    }

    const formData = new FormData();
    formData.append('editar_reserva_id', reserva_id);
    formData.append('restaurante_id', restaurante_id);
    formData.append('mesa_id', mesa_id);
    formData.append('fecha', fecha);
    formData.append('hora', hora);

    fetch('ver_reservas.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        tr.querySelector('.restaurante-text').textContent = data.restaurante_nombre;
        tr.querySelector('.mesa-text').textContent = data.mesa_numero;
        tr.querySelector('.fecha-text').textContent = data.fecha;
        tr.querySelector('.hora-text').textContent = data.hora;
        cancelarEdicion(tr);
        mostrarMensaje(data.mensaje);
      } else {
        alert('Error al actualizar la reserva');
      }
    })
    .catch(() => alert('Error en la conexión'));
  }

  function borrarReserva(id) {
    const formData = new FormData();
    formData.append('eliminar_reserva_id', id);

    fetch('ver_reservas.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const tr = document.querySelector(`tr[data-id="${id}"]`);
        if (tr) tr.remove();
        mostrarMensaje(data.mensaje);
      } else {
        alert('Error al eliminar la reserva');
      }
    })
    .catch(() => alert('Error en la conexión'));
  }

  function actualizarEstado(reserva_id, estado) {
    const formData = new FormData();
    formData.append('reserva_id', reserva_id);
    formData.append('estado', estado);

    fetch('ver_reservas.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        mostrarMensaje(data.mensaje);
      } else {
        alert('Error al actualizar el estado');
      }
    })
    .catch(() => alert('Error en la conexión'));
  }

  function mostrarMensaje(texto) {
    const mensaje = document.getElementById('mensaje-exito');
    mensaje.textContent = texto;
    mensaje.style.display = 'block';
    setTimeout(() => {
      mensaje.style.display = 'none';
    }, 3000);
  }
});
