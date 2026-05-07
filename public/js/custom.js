// Scripts personalizados del sistema
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de Almacén cargado correctamente');
    
    // Confirmación antes de eliminar
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de eliminar este registro?')) {
                e.preventDefault();
            }
        });
    });
});