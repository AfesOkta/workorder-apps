/*
Template Name: Drezon - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Version: 1.0.0
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Main Js File
*/


(function ($) {

    'use strict';

    function initMetisMenu() {
        //metis menu
        $("#side-menu").metisMenu();
    }

    function initLeftMenuCollapse() {
        $('.vertical-menu-btn').on('click', function (event) {
            event.preventDefault();
            $('body').toggleClass('sidebar-enable');
            if ($(window).width() >= 992) {
                $('body').toggleClass('vertical-collpsed');
            } else {
                $('body').removeClass('vertical-collpsed');
            }
        });
    }

    function initActiveMenu() {
        // === following js will activate the menu in left side bar based on url ====
        $("#sidebar-menu a").each(function () {
            var pageUrl = window.location.href.split(/[?#]/)[0];
            if (this.href == pageUrl) {
                $(this).addClass("active");
                $(this).parent().addClass("mm-active"); // add active to li of the current link
                $(this).parent().parent().addClass("mm-show");
                $(this).parent().parent().prev().addClass("mm-active"); // add active class to an anchor
                $(this).parent().parent().parent().addClass("mm-active");
                $(this).parent().parent().parent().parent().addClass("mm-show"); // add active to li of the current link
                $(this).parent().parent().parent().parent().parent().addClass("mm-active");
            }
        });
    }

    function initMenuItemScroll() {
        // focus active menu in left sidebar
        $(document).ready(function(){
            if($("#sidebar-menu").length > 0 && $("#sidebar-menu .mm-active .active").length > 0){
                var activeMenu = $("#sidebar-menu .mm-active .active").offset().top;
                if( activeMenu > 300) {
                    activeMenu = activeMenu - 300;
                    $(".simplebar-content-wrapper").animate({ scrollTop: activeMenu }, "slow");
                }
            }
        });
    }

    function initHoriMenuActive() {
        $(".navbar-nav a").each(function () {
            var pageUrl = window.location.href.split(/[?#]/)[0];
            if (this.href == pageUrl) { 
                $(this).addClass("active");
                $(this).parent().addClass("active");
                $(this).parent().parent().addClass("active");
                $(this).parent().parent().parent().addClass("active");
                $(this).parent().parent().parent().parent().addClass("active");
                $(this).parent().parent().parent().parent().parent().addClass("active");
            }
        });
    }

    function initFullScreen() {
        $('[data-toggle="fullscreen"]').on("click", function (e) {
            e.preventDefault();
            $('body').toggleClass('fullscreen-enable');
            if (!document.fullscreenElement && /* alternative standard method */ !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        });
        document.addEventListener('fullscreenchange', exitHandler );
        document.addEventListener("webkitfullscreenchange", exitHandler);
        document.addEventListener("mozfullscreenchange", exitHandler);
        function exitHandler() {
            if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                console.log('pressed');
                $('body').removeClass('fullscreen-enable');
            }
        }
    }

    function initRightSidebar() {
        // right side-bar toggle
        $('.right-bar-toggle').on('click', function (e) {
            $('body').toggleClass('right-bar-enabled');
        });

        $(document).on('click', 'body', function (e) {
            if ($(e.target).closest('.right-bar-toggle, .right-bar').length > 0) {
                return;
            }

            $('body').removeClass('right-bar-enabled');
            return;
        });
    }

    function initDropdownMenu() {
        $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
            if (!$(this).next().hasClass('show')) {
              $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');
    
            return false;
        });   
    }
    
    function initComponents() {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(function () {
            $('[data-toggle="popover"]').popover()
        })

        // Counter Up
        var delay = $(this).attr('data-delay')?$(this).attr('data-delay'):100; //default is 100
        var time = $(this).attr('data-time')?$(this).attr('data-time'):1200; //default is 1200
         $('[data-plugin="counterup"]').each(function(idx, obj) {
            $(this).counterUp({
                delay: delay,
                time: time
            });
         });
    }

    function initPreloader() {
        $(window).on('load', function() {
            $('#status').fadeOut();
            $('#preloader').delay(350).fadeOut('slow');
        });
    }

    function initSettings() {
        if (window.sessionStorage) {
            var alreadyVisited = sessionStorage.getItem("is_visited");
            if (!alreadyVisited) {
                alreadyVisited = "light-mode-switch";
                sessionStorage.setItem("is_visited", alreadyVisited);
                updateThemeSetting(alreadyVisited);
            } else {
                $(".right-bar input:checkbox").prop('checked', false);
                $("#"+alreadyVisited).prop('checked', true);
                updateThemeSetting(alreadyVisited);
            }
        }
        $("#light-mode-switch, #dark-mode-switch, #rtl-mode-switch").on("change", function(e) {
            updateThemeSetting(e.target.id);
        });
    }

    function updateThemeSetting(id) {
        if($("#light-mode-switch").prop("checked") == true && id === "light-mode-switch"){
            $("#dark-mode-switch").prop("checked", false);
            $("#rtl-mode-switch").prop("checked", false);
            $("#bootstrap-style").attr('href','/assets/css/bootstrap.min.css');
            $("#app-style").attr('href','/assets/css/app.min.css');
            sessionStorage.setItem("is_visited", "light-mode-switch");
        } else if($("#dark-mode-switch").prop("checked") == true && id === "dark-mode-switch"){
            $("#light-mode-switch").prop("checked", false);
            $("#rtl-mode-switch").prop("checked", false);
            $("#bootstrap-style").attr('href','/assets/css/bootstrap-dark.min.css');
            $("#app-style").attr('href','/assets/css/app-dark.min.css');
            sessionStorage.setItem("is_visited", "dark-mode-switch");
        } else if($("#rtl-mode-switch").prop("checked") == true && id === "rtl-mode-switch"){
            $("#light-mode-switch").prop("checked", false);
            $("#dark-mode-switch").prop("checked", false);
            $("#bootstrap-style").attr('href','/assets/css/bootstrap.min.css');
            $("#app-style").attr('href','/assets/css/app-rtl.min.css');
            sessionStorage.setItem("is_visited", "rtl-mode-switch");
        }
    }
    function initChackAll() {
        $('#checkAll').on('change', function() {     
            $('.table-check .custom-control-input').prop('checked', $(this).prop("checked"));              
        });
        $('.table-check .custom-control-input').change(function(){ 
            if($('.table-check .custom-control-input:checked').length == $('.table-check .custom-control-input').length){
                $('#checkAll').prop('checked',true);
            }else{
                $('#checkAll').prop('checked',false);
            }
        });
    }
    function initnameupdate(params) {
        $(".edit-icon").click(function(event) {
            var parentdiv = $(this).parent();
            $(".dashboard_new_name").hide();
            $(".dashboard_name").show();
            var oldname = parentdiv.find(".dashboard_name").text();
            var newname = parentdiv.find(".dashboard_new_name");
            newname.val(oldname);
            parentdiv.find(".dashboard_name").hide();
            parentdiv.find(".dashboard_new_name").show();
            $(this).hide();
        });
        function delay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this,
                    args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function() {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }
        $('.dashboard_new_name').keyup(
            delay(function(e) {
                var parentdiv = $(this).parent();
                var text = $(this).parent().find(".dashboard_name");
                var newname = $(this).val();
                text.html(newname);
                parentdiv.find(".dashboard_name").show();
                parentdiv.find(".dashboard_new_name").hide();
                $('.edit-icon').show();
            }, 500)
        );
    }
    function initFeatherIcon() {
        $(function () {
            feather.replace()
        })
    }
    function init() {
        initMetisMenu();
        initLeftMenuCollapse();
        initActiveMenu();
        initMenuItemScroll();
        initFullScreen();
        initHoriMenuActive();
        initRightSidebar();
        initDropdownMenu();
        initComponents();
        initSettings();
        initPreloader();
        Waves.init();
        initChackAll();
        initnameupdate();
        initFeatherIcon();
    }
    init();
})(jQuery);