$(document).ready(function () {
    "use strict";

  
    const $nav = $('.navbar');
    const $menuBtn = $('.fa-bars');

    $menuBtn.click(function () {
        $(this).toggleClass('fa-times');
        $nav.toggleClass('nav-toggle');
    });

    // Close menu on scroll or link click
    $(window).on('scroll load', function () {
        $menuBtn.removeClass('fa-times');
        $nav.removeClass('nav-toggle');
    });

    /* =============================================
       2. Dynamic Header & Reading Progress
       ============================================= */
    // Add a progress bar div dynamically if it doesn't exist
    if (!$('.scroll-progress').length) {
        $('body').prepend('<div class="scroll-progress" style="position:fixed; top:0; left:0; height:4px; background:var(--secondary-color); z-index:2000; width:0%;"></div>');
    }

    $(window).on('scroll', function () {
        const scrollTop = $(window).scrollTop();
        const docHeight = $(document).height() - $(window).height();
        const scrollPercent = (scrollTop / docHeight) * 100;

        // Update progress bar
        $('.scroll-progress').css('width', scrollPercent + '%');

        // Header Transformation
        if (scrollTop > 50) {
            $('.header').addClass('header-active').css({
                'background': 'var(--primary-color)',
                'box-shadow': '0 .5rem 1.5rem rgba(0,0,0,.2)',
                'padding': '1rem 0'
            });
        } else {
            $('.header').removeClass('header-active').css({
                'background': 'transparent',
                'box-shadow': 'none',
                'padding': '2rem 0'
            });
        }
    });

   
    const startCounter = (el) => {
        const target = +el.getAttribute('data-target');
        const count = +el.innerText;
        const speed = 150; 
        const inc = target / speed;

        if (count < target) {
            el.innerText = Math.ceil(count + inc);
            setTimeout(() => startCounter(el), 15);
        } else {
            el.innerText = target;
        }
    };

    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounter(entry.target);
                observer.unobserve(entry.target); // Run only once
            }
        });
    }, { threshold: 0.8 });

    document.querySelectorAll('.counter').forEach(counter => {
        counterObserver.observe(counter);
    });

   
    if ($('.clients-carousel').length) {
        $(".clients-carousel").owlCarousel({
            autoplay: true,
            autoplayTimeout: 3000,
            dots: false,
            loop: true,
            margin: 30,
            responsive: { 0: { items: 2 }, 768: { items: 4 }, 1200: { items: 6 } }
        });
    }

    if ($('.testimonials-carousel').length) {
        $(".testimonials-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            dots: true,
            loop: true,
            margin: 20,
            responsive: { 0: { items: 1 }, 768: { items: 2 }, 992: { items: 3 } }
        });
    }

   == */
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });

    $('.back-to-top').click(function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 800); 
    });

   
    $('.accordion-header').click(function () {
        const $body = $(this).next('.accordion-body');
        
        // If clicking an already open one, close it. Otherwise, close others and open this.
        if ($body.is(':visible')) {
            $body.slideUp(300);
            $(this).find('span').text('+');
        } else {
            $('.accordion-body').slideUp(300);
            $('.accordion-header span').text('+');
            $body.slideDown(300);
            $(this).find('span').text('-');
        }
    });
});
