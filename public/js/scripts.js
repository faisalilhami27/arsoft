function notification(status, msg) {
  if (status === 'success') {
    Swal.fire({
      title: "Berhasil!",
      text: msg,
      icon: "success"
    });
  } else {
    Swal.fire({
      title: "Gagal!",
      text: msg,
      icon: "warning"
    });
  }
}
