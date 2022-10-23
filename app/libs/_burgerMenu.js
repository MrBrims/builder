export function burgerMenu() {
  const menuBtn = document.querySelector('.menu__btn');
  const menuBody = document.querySelector('.menu__body');

  if (menuBtn) {
    menuBtn.addEventListener("click", function (e) {
      document.body.classList.toggle('--lock');
      menuBody.classList.toggle('--active');
      menuBtn.classList.toggle('--active');
    });
    menuBody.addEventListener("click", function (e) {
      document.body.classList.remove('--lock');
      menuBody.classList.remove('--active');
      menuBtn.classList.remove('--active');
    });
  }
}