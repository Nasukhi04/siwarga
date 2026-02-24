// assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {
  // Konfirmasi hapus
  document.querySelectorAll(".form-delete").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      const yakin = confirm("Yakin ingin menghapus data ini? File gambar/PDF terkait juga akan dihapus.");
      if (!yakin) {
        e.preventDefault();
      }
    });
  });
});