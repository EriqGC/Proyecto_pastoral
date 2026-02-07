<!-- Botón para abrir el modal -->
<button onclick="openModal()" class="btn-open">Editar Evento</button>

<!-- Modal -->
<div id="modal" class="modal hidden">
  <div class="modal-content">
    <h2>Editar Evento</h2>

    <!-- Tabs -->
    <div class="tabs">
      <button onclick="showTab('datos')" class="tab-button active">Datos</button>
      <button onclick="showTab('itinerario')" class="tab-button">Itinerario</button>
      <button onclick="showTab('viaje')" class="tab-button">Viaje</button>
    </div>

    <!-- Contenido: Datos -->
    <div id="tab-datos" class="tab-section">
      <div class="modal-grid">
        <div class="column">
          <label>Imagen:</label>
          <input type="file">

          <label>Nombre:</label>
          <input type="text" value="Encuentro Misionero">

          <label>Tipo:</label>
          <select id="tipoSelect" onchange="handleTipoChange()">
            <option value="misiones">Misiones</option>
            <option value="reunion">Reunión</option>
          </select>

          <label>Categoría:</label>
          <input type="text">
        </div>

        <div class="column">
          <label>Imagen:</label>
          <input type="file">

          <div id="extraReunion" style="display: none;">
            <label>Participantes:</label>
            <input type="text">

            <label>Checkbox Todos:</label>
            <input type="checkbox">
          </div>

          <label>Costo:</label>
          <input type="text" value="$500.00">

          <label>Ubicación:</label>
          <input type="text">
        </div>

        <div class="column">
          <label>Carta Compromiso:</label>
          <input type="file">

          <label>Fecha inicio:</label>
          <input type="datetime-local" value="2025-06-01T12:00">

          <label>Fecha fin:</label>
          <input type="datetime-local" value="2025-06-01T15:00">

          <label>Descripción:</label>
          <textarea rows="4"></textarea>
        </div>
      </div>
    </div>

    <!-- Contenido: Itinerario -->
    <div id="tab-itinerario" class="tab-section hidden">
      <div class="modal-grid-1col">
        <div class="itinerary-row">
          <input type="text" placeholder="Nombre de la actividad">
          <input type="time">
          <input type="time">
        </div>
        <div class="itinerary-row">
          <input type="text" placeholder="Nombre de la actividad">
          <input type="time">
          <input type="time">
        </div>
        <button class="add-itinerary">+</button>
      </div>
    </div>

    <!-- Contenido: Viaje -->
    <div id="tab-viaje" class="tab-section hidden">
      <div class="modal-grid">
        <div class="column">
          <label>¿Viaje?</label>
          <select>
            <option>Sí</option>
            <option>No</option>
          </select>

          <label>Ubicación inicial:</label>
          <input type="text">

          <label>Ubicación siguiente:</label>
          <input type="text">

          <label>Ubicación final:</label>
          <input type="text">
        </div>

        <div class="column">
          <label>Ruta:</label>
          <input type="file">

          <label>Ruta:</label>
          <input type="file">

          <label>Ruta:</label>
          <input type="file">
        </div>
      </div>
      <button class="add-itinerary">+</button>
    </div>

    <!-- Botones finales -->
    <div class="modal-buttons">
      <button onclick="confirmar()" class="btn-confirmar">Confirmar</button>
      <button onclick="closeModal()" class="btn-cancelar">Cancelar</button>
    </div>
  </div>
</div>

<style>
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }
  .modal.hidden {
    display: none;
  }
  .modal-content {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 12px;
    width: 90%;
    max-width: 1100px;
  }
  .modal-grid {
    display: flex;
    gap: 24px;
    margin-bottom: 20px;
  }
  .column {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  label {
    font-weight: bold;
  }
  input[type="text"],
  input[type="file"],
  input[type="datetime-local"],
  input[type="time"],
  textarea,
  select {
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  textarea {
    resize: vertical;
  }
  .modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }
  .btn-confirmar {
    background: #e92030;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  .btn-cancelar {
    background: #868d96;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  .btn-open {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  .tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }
  .tab-button {
    padding: 8px 16px;
    border: none;
    background: #ccc;
    cursor: pointer;
    border-radius: 6px;
  }
  .tab-button.active {
    background: #007bff;
    color: white;
  }
  .tab-section.hidden {
    display: none;
  }
  .modal-grid-1col {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .itinerary-row {
    display: flex;
    gap: 12px;
  }
  .add-itinerary {
    padding: 8px 16px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    align-self: flex-start;
  }
</style>

<script>
  function openModal() {
    document.getElementById('modal').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('modal').classList.add('hidden');
  }

  function confirmar() {
    alert('Evento confirmado');
    closeModal();
  }

  function showTab(tabId) {
    document.querySelectorAll('.tab-section').forEach(el => el.classList.add('hidden'));
    document.querySelector(`#tab-${tabId}`).classList.remove('hidden');

    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
  }

  function handleTipoChange() {
    const tipo = document.getElementById('tipoSelect').value;
    const extra = document.getElementById('extraReunion');
    extra.style.display = tipo === 'reunion' ? 'block' : 'none';
  }
</script>
