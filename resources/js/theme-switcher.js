// === 1) Tema persistente (se ejecuta inmediatamente) ===
const savedTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', savedTheme);

// Esta función SÓLO se usará cuando el DOM ya exista
function applyThemeSettings(theme) {
    // --- Flatpickr: tema oscuro ---
    let existingDarkTheme = document.getElementById('flatpickr-dark-theme');
    if (existingDarkTheme) {
        existingDarkTheme.remove();
    }

    if (theme === 'dark') {
        const link = document.createElement('link');
        link.id  = 'flatpickr-dark-theme';
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css';
        document.head.appendChild(link);
    }

    // --- SweetAlert2: activar / desactivar CSS del tema oscuro ---
    const swalDarkLink = document.getElementById('swal2-dark-theme');
    if (swalDarkLink) {
        swalDarkLink.disabled = (theme !== 'dark'); // habilitado sólo en dark
    }
}

// === 2) Resto de lógica: sólo cuando el DOM está listo ===
document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');
    const sidebar     = document.getElementById('sidebar');
    const overlay     = document.getElementById('sidebarOverlay');
    const toggleBtn   = document.querySelector('.sidebar-toggle');

    if (themeToggle) {
        themeToggle.checked = (savedTheme === 'dark');
    }

    applyThemeSettings(savedTheme);

    themeToggle?.addEventListener('change', () => {
        const t = themeToggle.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', t);
        localStorage.setItem('theme', t);
        applyThemeSettings(t);
    });


// ==== Sidebar ====
    function openSidebar(){
        if (!sidebar || !overlay) return;
        sidebar.dataset.open = "true";
        overlay.dataset.show = "true";
        toggleBtn?.setAttribute('aria-expanded','true');
    }
    function closeSidebar(){
        if (!sidebar || !overlay) return;
        sidebar.dataset.open = "false";
        overlay.dataset.show = "false";
        toggleBtn?.setAttribute('aria-expanded','false');
    }

    toggleBtn?.addEventListener('click', () =>
        (sidebar?.dataset.open === "true") ? closeSidebar() : openSidebar()
    );
    overlay?.addEventListener('click', closeSidebar);

    sidebar?.querySelectorAll('a.sb-link').forEach(a => {
        a.addEventListener('click', () => {
            if (window.innerWidth < 992) closeSidebar();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && window.innerWidth < 992) closeSidebar();
    });
});
