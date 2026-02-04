@if(isset($status) && isset($message))
<script>
console.log('Notificacion PHP detectada:', { 
    status: '{{$status}}', 
    message: '{{$message}}' 
});

// Mostrar notificacion inmediatamente con SweetAlert
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: '{{$status}}',
    title: '{{$message}}',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});
</script>

@else
<script>
console.log('No hay variables de notificacion disponibles');
</script>
@endif