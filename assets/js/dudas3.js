// Mostrar/ocultar respuestas
document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', function(e) {
        // No hacer nada si se hizo clic en el botón "Ver Evento"
        if (!e.target.classList.contains('ver-evento-btn')) {
            const respuesta = this.querySelector('.faq-answer');
            respuesta.classList.toggle('visible');
        }
    });
});

// Buscador de preguntas
document.getElementById('faq-search').addEventListener('input', function() {
    const termino = this.value.toLowerCase();
    document.querySelectorAll('.faq-item').forEach(item => {
        const pregunta = item.querySelector('.faq-question').textContent.toLowerCase();
        const respuesta = item.querySelector('.faq-answer').textContent.toLowerCase();
        if (pregunta.includes(termino) || respuesta.includes(termino)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});