// La lógica de persistencia del tema debe ejecutarse antes de DOMContentLoaded para evitar el "flash" de tema
const themeToggle = document.getElementById('themeToggle');

// --- Lógica del Tema Persistente (Ejecución Inmediata) ---
const savedTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', savedTheme);
if(themeToggle) themeToggle.checked = (savedTheme === 'dark');

// Esta función aplica la configuración del tema (CSS y librerías)
function applyThemeSettings(theme) {
    // --- 1. Flatpickr: Cargar el CSS del tema oscuro si es necesario ---

    // Eliminar cualquier tema oscuro anterior
    let existingDarkTheme = document.getElementById('flatpickr-dark-theme');
    if (existingDarkTheme) {
        existingDarkTheme.remove();
    }

    if (theme === 'dark') {
        // Cargar el tema oscuro de Flatpickr
        const link = document.createElement('link');
        link.id = 'flatpickr-dark-theme';
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css';
        document.head.appendChild(link);

        // SweetAlert2: Aplicar la clase 'swal2-dark' al body
        // Esto complementa las variables CSS que ya pusiste en _theme.scss
        document.body.classList.add('swal2-dark');
    } else {
        document.body.classList.remove('swal2-dark');
    }
}

// Aplicar la configuración al cargar (el tema ya está configurado en el HTML arriba)
applyThemeSettings(savedTheme);


document.addEventListener('DOMContentLoaded', () => {
    // --- Lógica del Sidebar ---
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.sidebar-toggle');

    // --- 2. Evento de Cambio de Tema ---
    themeToggle?.addEventListener('change', ()=>{
        const t = themeToggle.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', t);
        localStorage.setItem('theme', t);

        applyThemeSettings(t); // Re-ejecutar la lógica al cambiar
    });

    // --- 3. Lógica del Sidebar ---
    function openSidebar(){ sidebar.dataset.open="true"; overlay.dataset.show="true"; toggleBtn?.setAttribute('aria-expanded','true'); }
    function closeSidebar(){ sidebar.dataset.open="false"; overlay.dataset.show="false"; toggleBtn?.setAttribute('aria-expanded','false'); }

    toggleBtn?.addEventListener('click', ()=> (sidebar.dataset.open==="true") ? closeSidebar() : openSidebar());
    overlay?.addEventListener('click', closeSidebar);

    // Cerrar sidebar al hacer clic en un enlace (en móvil)
    sidebar?.querySelectorAll('a.sb-link').forEach(a=>{
        a.addEventListener('click', ()=> { if (window.innerWidth < 992) closeSidebar(); });
    });

    // Cerrar sidebar con Esc
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape' && window.innerWidth<992) closeSidebar(); });
});
