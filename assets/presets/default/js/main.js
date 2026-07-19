/* Main Js Start */

(function ($) {
  "use strict";

  $(document).ready(function () {
    // odometer init
    if ($(".odometer").length) {
      var odo = $(".odometer");
      odo.each(function () {
        $(this).appear(function () {
          var countNumber = $(this).attr("data-count");
          $(this).html(countNumber);
        });
      });
    }

    // sidebar dropdown
    $(".has-dropdown > a").on("click", function (e) {
      e.preventDefault();
      var $submenu = $(this).next(".sidebar-submenu");
      var $parent = $(this).parent();
      if ($submenu.css("display") === "block") {
        $submenu.slideUp(200);
        $parent.removeClass("active");
      } else {
        $(".sidebar-submenu").not($submenu).slideUp(200);
        $(".has-dropdown.active").removeClass("active");
        $parent.addClass("active");
        $submenu.slideDown(200);
      }
    });

    $(".dashboard-body__bar-icon").on("click", function () {
      $(".sidebar-menu").addClass("show-sidebar");
      $(".sidebar-overlay").addClass("show");
    });
    $(".sidebar-menu__close, .sidebar-overlay").on("click", function () {
      $(".sidebar-menu").removeClass("show-sidebar");
      $(".sidebar-overlay").removeClass("show");
    });

    $(".counterup-item").each(function () {
      $(this).isInViewport(function (status) {
        if (status === "entered") {
          for (
            var i = 0;
            i < document.querySelectorAll(".odometer").length;
            i++
          ) {
            var el = document.querySelectorAll(".odometer")[i];
            el.innerHTML = el.getAttribute("data-odometer-final");
          }
        }
      });
    });

    $(".add").on("click", function () {
      if ($(this).prev().val() < 999) {
        $(this)
          .prev()
          .val(+$(this).prev().val() + 1);
      }
    });
    $(".sub").on("click", function () {
      if ($(this).next().val() > 1) {
        if ($(this).next().val() > 1)
          $(this)
            .next()
            .val(+$(this).next().val() - 1);
      }
    });

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $("#imagePreview").css(
            "background-image",
            "url(" + e.target.result + ")"
          );
          $("#imagePreview").hide();
          $("#imagePreview").fadeIn(650);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    $("#imageUpload").on("change", function () {
      readURL(this);
    });
  });

  // preloader
  $(window).on("load", function () {
    $("#preloader").fadeOut();
  });

  // sticky header
  $(window).on("scroll", function () {
    if ($(window).scrollTop() >= 60) {
      $(".header").addClass("fixed-header");
    } else {
      $(".header").removeClass("fixed-header");
    }
  });

  $(".header-search-icon").on("click", function () {
    $(".header-search-hide-show").addClass("show");
    $(".header-search-icon").hide();
    $(".close-hide-show").addClass("show");
  });

  $(".close-hide-show").on("click", function () {
    $(".close-hide-show").removeClass("show");
    $(".header-search-hide-show").removeClass("show");
    $(".header-search-icon").show();
  });

  $(".sidebar-overlay, .close-hide-show").on("click", function () {
    $(".sidebar-menu-wrapper").removeClass("show");
    $(".sidebar-overlay").removeClass("show");
  });

  // tap to top with progress

  if ($(".scroll-top").length > 0) {
    var $scrollTopBtn = $(".scroll-top");
    var $progressPath = $(".scroll-top path");
    var pathLength = $progressPath[0].getTotalLength();



    $progressPath.css({
      transition: "none",
      strokeDasharray: pathLength + " " + pathLength,
      strokeDashoffset: pathLength,
    });

    $progressPath[0].getBoundingClientRect();
    $progressPath.css({
      transition: "stroke-dashoffset 10ms linear",
    });

    var updateProgress = function () {
      var scroll = $(window).scrollTop();
      var height = $(document).height() - $(window).height();
      var progress = pathLength - (scroll * pathLength) / height;
      $progressPath.css("strokeDashoffset", progress);
    };

    updateProgress();

    $(window).on("scroll", updateProgress);

    $(window).on("scroll", function () {
      if ($(this).scrollTop() > 50) {
        $scrollTopBtn.addClass("show");
      } else {
        $scrollTopBtn.removeClass("show");
      }
    });

    $scrollTopBtn.on("click", function (event) {
      event.preventDefault();
      $("html, body").animate({ scrollTop: 0 }, 800);
      return false;
    });
  }

  // slider
  $(".testimonial-slider").slick({
    dots: false,
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: true,
    initialSlide: 1,
    centerMode: true,
    autoplay: true,
    autoplaySpeed: 2000,
    prevArrow:
      '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow:
      '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          centerMode: false,
          variableWidth: false,
        },
      },
      {
        breakpoint: 780,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          centerMode: false,
          variableWidth: false,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: false,
          variableWidth: false,
        },
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: false,
          variableWidth: false,
        },
      },
    ],
  });

  // toggle show hide password
  $(".toggle-password-change").on("click", function () {
    var targetId = $(this).data("target");
    var target = $("#" + targetId);
    var icon = $(this);
    if (target.attr("type") === "password") {
      target.attr("type", "text");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    } else {
      target.attr("type", "password");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    }
  });

  // wow init
  new WOW().init();

  // tap to show balance

  // tap balance
  $(document).on("click", ".textt, .balance, .tap-circle--icon", function () {
    var $parent = $(this).closest(".tap--balance");

    if (!$parent.data("clicked")) {
      $parent.data("clicked", false);
    }

    var clicked = $parent.data("clicked");
    $parent.data("clicked", !clicked);

    setTimeout(function () {
      $parent.find(".textt, .balance").toggleClass("d-none");
    }, 1000);

    if (!clicked) {
      $parent.addClass("goLeft").removeClass("goRight");
      $parent.find(".textt").addClass("op--0");
      $parent.find(".balance").removeClass("op--0");
    } else {
      $parent.removeClass("goLeft").addClass("goRight");
      $parent.find(".textt").removeClass("op--0");
      $parent.find(".balance").addClass("op--0");
    }
  });

  Splitting();

  $(".image--popup").magnificPopup({
    type: "image",
    gallery: {
      enabled: true,
    },
  });

  $(".image--popup-group").magnificPopup({
    type: "image",
    delegate: "a",
    gallery: {
      enabled: true,
    },
  });

  $(".popup__video").magnificPopup({
    type: "iframe",
  });

  $(".filter-btn--wrap button").on("click", function () {
    $(".filter--box").toggleClass("d-block");
  });

  // gsap
  document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector(".fly--image__wrap");
    const bubbles = document.querySelectorAll(".fly--thumb");

    gsap.registerPlugin(ScrollTrigger);

    function animateBubble(bubble) {
      const containerWidth = container.offsetWidth;
      const containerHeight = container.offsetHeight;
      const startX = Math.random() * containerWidth;
      const endX = startX + (Math.random() * 100 - 50);
      const duration = Math.random() * 4 + 2;
      const size = Math.random() * 50 + 30;

      gsap.set(bubble, {
        x: startX,
        y: containerHeight,
        scale: size / 50,
        opacity: 1,
      });

      gsap.to(bubble, {
        x: endX,
        y: 0,
        scale: 1,
        opacity: 1,
        duration: duration,
        ease: "power1.out",
        onComplete: () => {
          gsap.set(bubble, { x: startX, y: containerHeight, opacity: 1 });
          animateBubble(bubble);
        },
      });
    }
    bubbles.forEach((bubble) => {
      animateBubble(bubble);
    });


    const whychooseElement = document.querySelector(".why-choose .bg--element");
    if (whychooseElement) {
      gsap.to(whychooseElement, {
        rotation: 360,
        ease: "none",
        duration: 3,
        scrollTrigger: {
          trigger: ".why-choose",
          start: "top bottom",
          end: "bottom top",
          scrub: 1,
        },
      });
    }

    const aboutElement = document.querySelector(".about--us .bg--element");
    if (aboutElement) {
      gsap.to(aboutElement, {
        rotation: 360,
        ease: "none",
        duration: 3,
        scrollTrigger: {
          trigger: ".about--us",
          start: "top bottom",
          end: "bottom top",
          scrub: 1,
        },
      });
    }
  });

  // location card

  $(document).on("mouseenter", ".location__card", function () {
    $(".location__card").removeClass("active");
    $(this).addClass("active");
  });

  // select two
  $(document).ready(function () {
    $(".from-select2").select2();
    $(".from-select3").select2();
  });


  


})(jQuery);



