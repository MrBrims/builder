import { Swiper, Navigation, Pagination } from 'swiper';
Swiper.use([Navigation, Pagination])

export function swiperSlider() {
  const slider = new Swiper('.slider__body', {
    navigation: {
      nextEl: ".slider__next",
      prevEl: ".slider__prev",
    },
    pagination: {
      el: ".slider__nav",
    },
    loop: true,
    slidesPerView: 4,
    spaceBetween: 30,
  });
}