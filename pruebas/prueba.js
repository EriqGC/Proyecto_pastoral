document.getElementById('toggleButton').addEventListener('click', function () {
    const supervisorContent = document.getElementById('supervisor');
    const coordinadorContent = document.getElementById('coordinador');
    const toggleButton = document.getElementById('toggleButton');

    if (supervisorContent.classList.contains('active')) {
        supervisorContent.classList.remove('active');
        coordinadorContent.classList.add('active');
        toggleButton.textContent = '⬅️';
    } else {
        coordinadorContent.classList.remove('active');
        supervisorContent.classList.add('active');
        toggleButton.textContent = '➡️';
    }
});
