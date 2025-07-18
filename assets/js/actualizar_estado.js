document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".btn-aceptar").forEach(btn => {
    btn.addEventListener("click", () => {
      const row = btn.closest("tr");
      const reservaId = row.querySelector("td").textContent.trim();
      const estado = row.querySelector("select").value;

      fetch("actualizar_estado.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${reservaId}&estado=${estado}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(`Reserva ${estado.toLowerCase()}`);
        } else {
          alert("Error al actualizar el estado.");
        }
      })
      .catch(error => {
        console.error("Error de red:", error);
        alert("Error de red.");
      });
    });
  });
});
