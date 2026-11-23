// Menu samping terbuka saat kursor di atas tombol hamburger (perangkat dengan mouse).
// Menutup sendiri saat kursor meninggalkan tombol dan menu. Klik tetap berfungsi, dan
// di layar sentuh perilaku lama (ketuk untuk buka) tidak berubah.
(function () {
  var side = document.getElementById('side'),
      burger = document.querySelector('.burger'),
      scrim = document.getElementById('scrim');
  if (!side || !burger || !window.matchMedia('(hover:hover) and (pointer:fine)').matches) return;
  var t, byHover = false;
  function open() {
    clearTimeout(t);
    if (!side.classList.contains('open')) { side.classList.add('open'); byHover = true; }
  }
  function close() {
    clearTimeout(t);
    t = setTimeout(function () {
      side.classList.remove('open');
      if (scrim) scrim.classList.remove('on');
      byHover = false;
    }, 280);
  }
  burger.addEventListener('mouseenter', open);
  burger.addEventListener('mouseleave', close);
  side.addEventListener('mouseenter', function () { clearTimeout(t); });
  side.addEventListener('mouseleave', close);
  // Klik pada hamburger saat sudah terbuka karena hover: biarkan tetap terbuka (tidak menutup).
  burger.addEventListener('click', function () { clearTimeout(t); });
})();
