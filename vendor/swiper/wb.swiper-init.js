/**
 * Swiper JS Init
 *
 * @author We Both
 * @version 1.0
 * @requires
 *
 */
$(function () {
  'use strict';

  var mySwiper = new Swiper('.swiper-container', {
    // Optional parameters
    loop: true,
    grabCursor: true,
    slidesPerView: 3,
    // Responsive breakpoints
    breakpoints: {
      // when window width is <= 414px
      414: {
        slidesPerView: 1
        //spaceBetween: 10
      },
      // when window width is <= 600px
      800: {
        slidesPerView: 2
        //spaceBetween: 20
      }
    },

    // If we need pagination
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },

    // Navigation arrows
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    }
  });
});