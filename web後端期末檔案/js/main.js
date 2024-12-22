// 存儲初始卡片狀態
const originalCards = document.querySelectorAll('.service-item');

document.getElementById('edit-submit-clone-of-eq').addEventListener('click', function(event) {
    event.preventDefault();
    const keyword = document.getElementById('edit-title').value.toLowerCase();
    filterCards(keyword);
    updateHistory(keyword);
});

window.addEventListener('popstate', function(event) {
    const keyword = (event.state && event.state.keyword) ? event.state.keyword : '';
    document.getElementById('edit-title').value = keyword;
    if (!keyword) {
        resetCards();
    }
});

function updateHistory(keyword) {
    const url = new URL(window.location.href);
    url.searchParams.set('search', keyword);
    history.pushState({ keyword: keyword }, '', url);
}

function filterCards(keyword) {
    // 如果沒有關鍵詞，則重置卡片為初始狀態
    if (!keyword) {
        resetCards();
        return;
    }

    // 獲取所有的卡片元素
    const cards = document.querySelectorAll('.service-item');

    // 遍歷所有卡片，根據關鍵詞進行顯示或隱藏
    cards.forEach(card => {
        const titleElement = card.querySelector('h5');
        const titleText = titleElement.textContent.toLowerCase();

        if (titleText.includes(keyword)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function resetCards() {
    originalCards.forEach(card => {
        card.style.display = 'block';
    });
}

// 初始化時處理 URL 中的搜索參數
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const keyword = urlParams.get('search') || '';
    document.getElementById('edit-title').value = keyword;
    filterCards(keyword);
};

(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner(0);
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) {
            $('.navbar').addClass('sticky-top shadow-sm');
        } else {
            $('.navbar').removeClass('sticky-top shadow-sm');
        }
    });


    // Hero Header carousel
    $(".header-carousel").owlCarousel({
        animateOut: 'slideOutDown',
        items: 1,
        autoplay: true,
        smartSpeed: 1000,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
    });


    // International carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        items: 1,
        smartSpeed: 1500,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ]
    });


    // Modal Video
    $(document).ready(function () {
        var $videoSrc;
        $('.btn-play').click(function () {
            $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);

        $('#videoModal').on('shown.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
        })

        $('#videoModal').on('hide.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc);
        })
    });


    // testimonial carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:1
            },
            992:{
                items:1
            },
            1200:{
                items:1
            }
        }
    });

    
    
   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


})(jQuery);