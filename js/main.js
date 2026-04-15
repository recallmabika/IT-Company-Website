$(document).ready(function () {
  "use strict";

  const $window = $(window);
  const $navbar = $(".navbar");
  const $header = $(".header");
  const $bars = $(".fa-bars");

  // ===== Mobile menu toggle =====
  $bars.on("click", function () {
    $(this).toggleClass("fa-times");
    $navbar.toggleClass("nav-toggle");
  });

  function updateHeaderStyle() {
    if ($window.scrollTop() > 35) {
      $header.css({
        background: "#002e5f",
        boxShadow: "0 .2rem .5rem rgba(0,0,0,.4)",
      });
    } else {
      $header.css({
        background: "none",
        boxShadow: "none",
      });
    }
  }

  // Reset on load/scroll and update header style
  $window.on("load scroll", function () {
    $bars.removeClass("fa-times");
    $navbar.removeClass("nav-toggle");
    updateHeaderStyle();
  });

  // Run once on ready (optional but useful)
  updateHeaderStyle();

  // ===== Animated counters =====
  // Uses requestAnimationFrame for smooth updates and fewer CPU spikes.
  const counters = document.querySelectorAll(".counter");
  const speed = 120; // larger = slower

  counters.forEach((counter) => {
    const target = Number(counter.getAttribute("data-target")) || 0;

    // Initialize display as 0 (or keep existing if you want)
    let current = Number(counter.innerText) || 0;

    const step = () => {
      if (current < target) {
        // Compute increment proportionally to remaining distance
        const inc = Math.max(1, (target - current) / speed);
        current = Math.min(target, current + inc);

        // If you want integer counters, uncomment rounding:
        // counter.innerText = Math.round(current);
        counter.innerText = current;

        requestAnimationFrame(step);
      } else {
        // counter.innerText = Math.round(target);
        counter.innerText = target;
      }
    };

    step();
  });

  // ===== Owl Carousel =====
  (function ($) {
    "use strict";

    if ($(".clients-carousel").length) {
      $(".clients-carousel").owlCarousel({
        autoplay: true,
        dots: true,
        loop: true,
        responsive: {
          0: { items: 2 },
          768: { items: 4 },
          900: { items: 6 },
        },
      });
    }

    if ($(".testimonials-carousel").length) {
      $(".testimonials-carousel").owlCarousel({
        autoplay: true,
        dots: true,
        loop: true,
        responsive: {
          0: { items: 1 },
          576: { items: 2 },
          768: { items: 3 },
          992: { items: 4 },
        },
      });
    }
  })(jQuery);

  // ===== Back to top =====
  const $backToTop = $(".back-to-top");

  if ($backToTop.length) {
    $window.on("scroll", function () {
      $backToTop.stop(true, true).fadeToggle($window.scrollTop() > 100, "slow");
    });

    $backToTop.on("click", function (e) {
      e.preventDefault();

      // If jquery.easing isn't available, "easeInOutExpo" may not work.
      // Fallback: change easing to "swing" or remove it.
      $("html, body").stop(true).animate(
        { scrollTop: 0 },
        1500,
        "easeInOutExpo"
        // "swing"
      );
    });
  }

  // ===== Accordion =====
  $(".accordion-header").on("click", function () {
    const $accordion = $(this).closest(".accordion");
    const $allBodies = $accordion.find(".accordion-body");

    // Close all
    $allBodies.slideUp(500);

    // Open the next body (based on your original structure)
    $(this).next(".accordion-body").slideDown(500);

    // Update plus/minus icons within this accordion
    $accordion.find(".accordion-header span").text("+");
    $(this).children("span").text("-");
  });
});
