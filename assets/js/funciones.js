function cargarMesas() {
  const restauranteId = document.getElementById('restaurante').value;
  const zona = document.getElementById('zonaSeleccionada').value;
  const mesaSelect = document.getElementById('mesa');

  mesaSelect.innerHTML = '<option value="">Cargando mesas...</option>';

  let url = 'obtener_mesas.php?restaurante_id=' + restauranteId;
  if (zona) {
    url += '&zona=' + encodeURIComponent(zona);
  }

  fetch(url)
    .then(res => res.json())
    .then(data => {
      mesaSelect.innerHTML = '<option value="">Seleccione una mesa</option>';
      data.forEach(mesa => {
        const option = document.createElement('option');
        option.value = mesa.id;
        option.textContent = 'Mesa #' + mesa.numero + ' - ' + mesa.zona;
        mesaSelect.appendChild(option);
      });
    })
    .catch(error => {
      mesaSelect.innerHTML = '<option value="">Error al cargar mesas</option>';
      console.error(error);
    });
}
