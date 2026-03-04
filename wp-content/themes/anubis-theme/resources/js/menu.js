document.addEventListener('DOMContentLoaded', () => {

  const body = document.querySelector('body');
  const navheader = document.querySelector('.nav-mobil');
  const nav = document.querySelector('.nav-primary');
  const btnOpen = document.getElementById('btn-menu--open');
  const btnClose = document.getElementById('btn-menu--close');

  if (!nav || !btnOpen || !btnClose) return;

  const openMenu = () => {
    nav.classList.add('open');
    btnOpen.classList.add('btn-hidden');
    btnClose.classList.remove('btn-hidden');
    document.body.classList.add('no-scroll');
  };

  const closeMenu = () => {
    nav.classList.remove('open');
    btnClose.classList.add('btn-hidden');
    btnOpen.classList.remove('btn-hidden');
    document.body.classList.remove('no-scroll');
  };

  btnOpen.addEventListener('click', openMenu);
  btnClose.addEventListener('click', closeMenu);

  document.addEventListener('click', (e) => {
    if (
      nav.classList.contains('open') &&
      !nav.contains(e.target) &&
      !navheader.contains(e.target) &&
      !btnOpen.contains(e.target)
    ) {
      closeMenu();
    }
  });

  // Touche ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeMenu();
    }
  });

});