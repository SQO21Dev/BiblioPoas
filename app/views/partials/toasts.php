<?php if (!empty($_SESSION['flash_error'])): ?>
<script>
Swal.fire({icon:'error', title:'Error', text:<?= json_encode($_SESSION['flash_error']) ?>});
</script>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
<script>
Swal.fire({icon:'success', title:'OK', text:<?= json_encode($_SESSION['flash_success']) ?>, timer:2000, showConfirmButton:false});
</script>
<?php unset($_SESSION['flash_success']); endif; ?>
