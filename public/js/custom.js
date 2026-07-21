// JavaScript personalizado para ProAHUV

document.addEventListener('DOMContentLoaded', function() {
    // Detectar cambios en el estado del sidebar
    const sidebarToggleBtn = document.querySelector('.nav-link[data-widget="pushmenu"]');
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            // Dar tiempo para que se aplique la clase sidebar-collapse
            setTimeout(function() {
                // Ajustar la tabla para que se adapte al nuevo ancho
                if ($.fn.dataTable && $.fn.dataTable.tables) {
                    $($.fn.dataTable.tables()).DataTable().responsive.recalc();
                    $($.fn.dataTable.tables()).DataTable().columns.adjust();
                }
            }, 300);
        });
    }

    // Asegurarse de que el contenido principal no se solape con la barra superior
    const mainHeader = document.querySelector('.main-header');
    const contentWrapper = document.querySelector('.content-wrapper');
    
    if (mainHeader && contentWrapper) {
        const headerHeight = mainHeader.offsetHeight;
        contentWrapper.style.paddingTop = headerHeight + 'px';
    }

    // Aplicar efecto de parallax al fondo con intensidad reducida
    window.addEventListener('scroll', function() {
        const scrolled = window.scrollY;
        const contentWrapper = document.querySelector('.content-wrapper');
        
        if (contentWrapper) {
            // Aplicar un efecto de parallax muy sutil para no afectar al contenido
            contentWrapper.style.backgroundPositionY = -(scrolled * 0.02) + 'px';
        }
    });

    // Función para ajustar el tamaño de la tabla al cambiar el tamaño de la ventana
    function adjustTableLayout() {
        if ($.fn.dataTable && $.fn.dataTable.tables) {
            $($.fn.dataTable.tables()).DataTable().responsive.recalc();
            $($.fn.dataTable.tables()).DataTable().columns.adjust();
        }
    }

    // Escuchar eventos de cambio de tamaño de la ventana
    window.addEventListener('resize', adjustTableLayout);

    // Añadir clase personalizada al cuerpo para identificar nuestra aplicación
    document.body.classList.add('proahuv-theme');
}); 