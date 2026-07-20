document.addEventListener('DOMContentLoaded', function () {
    // Navbar toggle
    var toggle = document.querySelector('.navbar-toggle');
    var links  = document.querySelector('.navbar-links');

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (!toggle.contains(e.target) && !links.contains(e.target)) {
                links.classList.remove('open');
            }
        });
    }

    // Fecha menu ao clicar em link (mobile)
    links.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            links.classList.remove('open');
        });
    });
});
