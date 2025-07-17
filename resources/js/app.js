var processing = 0;

window.addEventListener('load', function() {
    $('body').removeAttr('style');
    $('.loader-container').fadeOut();
});
if (document.getElementById('__fixed')){
    if(window.innerWidth >= 768){
        var elementPosition = $('#__fixed').offset();
        var element_right =$(window).width() - (elementPosition.left + $('#__fixed').outerWidth());
        $('#__fixed').parent().css('height',$('#__fixed').parent().height()+'px')
        $(window).scroll(function () {
            if ($(window).scrollTop() > 60) {
                $('#__fixed').css('position', 'fixed').css('top', '123px').css('left', elementPosition.left).css('right', element_right).addClass('scroll');
            } else {
                $('#__fixed').css('position', 'static').removeClass('scroll');
            }
        });
    }

}
$(document).on('click','.show-more',function (){
    $('.more-target').toggleClass('d-flex').toggleClass('d-none')
})
$('.decimalInput').on('input', function () {
    // Remove any non-digit and non-comma characters
    var sanitizedValue = $(this).val().replace(/[^\d,]/g, '');
    // Replace any dots with commas
    sanitizedValue = sanitizedValue.replace(/\./g, ',');
    // Remove duplicated commas
    sanitizedValue = sanitizedValue.replace(/^(\d*,)(.*),(.*)$/, 't1t2t3');
    // Limit the number of digits after the comma to 2
    var parts = sanitizedValue.split(',');
    if (parts.length > 1) {
        parts[1] = parts[1].substring(0, 2);
        sanitizedValue = parts.join(',');
    }
    // Update the input value
    $(this).val(sanitizedValue);
});
$(document).on('click', '.__datatable-edit-modal', function () {
    if (processing === 0) {
        processing = 1;
        let html = $(this).html();
        $(this).attr('disabled', '').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
        let url = $(this).data('url');
        let target = '#' + $(this).data('target');
        $.ajax({
            url: url, method: 'GET', success: response => {
                processing = 0;
                $(this).removeAttr('disabled').html(html)
                $(target).find('.modal-content').html(response);
                $(target).modal('show');
            }, error: xhr => {
                processing = 0;
                $(this).removeAttr('disabled').html(html)
                if(xhr.status !== undefined) {
                    if (xhr.status === 403) {
                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                        return
                    }
                }
                toastr.error('Un erreur est produit')
            }
        })
    }

})
$.fn.select2.defaults.set("language", "fr");
$(document).on('click', '.sa-warning', function () {
    Swal.fire({
        title: "Est-vous sûr?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimez!",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-danger mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
        },
        didOpen: () => {
            $('.btn').blur()
        },
        preConfirm: async () => {
            Swal.showLoading();
            try {
                const [response] = await Promise.all([new Promise((resolve, reject) => {
                    $.ajax({
                        url: $(this).data('url'), method: 'DELETE', headers: {
                            'X-CSRF-TOKEN': __csrf_token
                        }, success: resolve, error: (_, jqXHR) => reject(_)
                    });
                })]);

                return response;
            } catch (jqXHR) {
                let errorMessage = "Une erreur s'est produite lors de la demande.";
                if(jqXHR.status !== undefined) {
                    if (jqXHR.status === 404) {
                        errorMessage = "La ressource n'a pas été trouvée.";
                    }
                    if (jqXHR.status === 403) {
                        errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                    }
                }
                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });

                throw jqXHR;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                Swal.fire({
                    title: 'Succès',
                    text: result.value,
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-success mx-2',
                    },
                }).then(result => {
                    if (typeof table != 'undefined') {
                        table.ajax.reload();
                    } else {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: "Une erreur s'est produite lors de la demande.",
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
            }
        }
    })
});
var exercice_process = 0;
$('#exercice-btn').click(function () {
    if (exercice_process === 0) {
        exercice_process = 1;
        $('#exercice-btn').find('.icon').toggleClass('d-none');
        $.ajax({
            url: __exercice_change_url, method: 'GET', headers: {
                "X-CSRF-TOKEN": __csrf_token
            }, success: response => {
                $('#exercice-btn').find('.icon').toggleClass('d-none');
                $('#exercise-modal .modal-content').html(response);
                $('#exercise-modal').modal('show').css('z-index', 1070)
                exercice_process = 0;
            }, error: () => {
                exercice_process = 0;
                $('#exercice-btn').find('.icon').toggleClass('d-none');
                toastr.error('Erreur')
            }
        })
    }
})

$('.actions-mobile').click(function (){
    $('.actions-target-mobile').toggleClass('d-flex')
})
function s() {
    for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), $ = 0, n = e.length; $ < n; $++) "nav-item dropdown active" === e[$].parentElement.getAttribute("class") && (e[$].parentElement.classList.remove("active"), e[$].nextElementSibling.classList.remove("show"))
}

function n(e) {
    1 == $("#light-mode-switch").prop("checked") && "light-mode-switch" === e ? ($("html").removeAttr("dir"), $("#dark-mode-switch").prop("checked", !1), $("#rtl-mode-switch").prop("checked", !1), $("#bootstrap-style").attr("href", "assets/css/bootstrap.min.css"), $("#app-style").attr("href", "assets/css/app.min.css"), sessionStorage.setItem("is_visited", "light-mode-switch")) : 1 == $("#dark-mode-switch").prop("checked") && "dark-mode-switch" === e ? ($("html").removeAttr("dir"), $("#light-mode-switch").prop("checked", !1), $("#rtl-mode-switch").prop("checked", !1), $("#bootstrap-style").attr("href", "assets/css/bootstrap-dark.min.css"), $("#app-style").attr("href", "assets/css/app-dark.min.css"), sessionStorage.setItem("is_visited", "dark-mode-switch")) : 1 == $("#rtl-mode-switch").prop("checked") && "rtl-mode-switch" === e && ($("#light-mode-switch").prop("checked", !1), $("#dark-mode-switch").prop("checked", !1), $("#bootstrap-style").attr("href", "assets/css/bootstrap-rtl.min.css"), $("#app-style").attr("href", "assets/css/app-rtl.min.css"), $("html").attr("dir", "rtl"), sessionStorage.setItem("is_visited", "rtl-mode-switch"))
}

function e() {
    document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || (console.log("pressed"), $("body").removeClass("fullscreen-enable"))
}

var a;
$("#side-menu").metisMenu(), $("#vertical-menu-btn").on("click", function (e) {
    e.preventDefault(), $("body").toggleClass("sidebar-enable"), 992 <= $(window).width() ? $("body").toggleClass("vertical-collpsed") : $("body").removeClass("vertical-collpsed")
    if (document.getElementById('__fixed')) {

        elementPosition = $('#__fixed').offset()
        element_right = $(window).width() - (elementPosition.left + $('#__fixed').outerWidth());
    }
}), $("#sidebar-menu a").each(function () {
    var e = window.location.href.split(/[?#]/)[0];
    this.href == e && ($(this).addClass("active"), $(this).parent().addClass("mm-active"), $(this).parent().parent().addClass("mm-show"), $(this).parent().parent().prev().addClass("mm-active"), $(this).parent().parent().parent().addClass("mm-active"), $(this).parent().parent().parent().parent().addClass("mm-show"), $(this).parent().parent().parent().parent().parent().addClass("mm-active"))
}), $(document).ready(function () {
    var e;
    0 < $("#sidebar-menu").length && 0 < $("#sidebar-menu .mm-active .active").length && (300 < (e = $("#sidebar-menu .mm-active .active").offset().top) && (e -= 300, $(".vertical-menu .simplebar-content-wrapper").animate({scrollTop: e}, "slow")))
}), $(".navbar-nav a").each(function () {
    var e = window.location.href.split(/[?#]/)[0];
    this.href == e && ($(this).addClass("active"), $(this).parent().addClass("active"), $(this).parent().parent().addClass("active"), $(this).parent().parent().parent().addClass("active"), $(this).parent().parent().parent().parent().addClass("active"), $(this).parent().parent().parent().parent().parent().addClass("active"), $(this).parent().parent().parent().parent().parent().parent().addClass("active"))
}), $('[data-toggle="fullscreen"]').on("click", function (e) {
    e.preventDefault(), $("body").toggleClass("fullscreen-enable"), document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
}), document.addEventListener("fullscreenchange", e), document.addEventListener("webkitfullscreenchange", e), document.addEventListener("mozfullscreenchange", e), $(".right-bar-toggle").on("click", function (e) {
    $("body").toggleClass("right-bar-enabled")
}), $(document).on("click", "body", function (e) {
    0 < $(e.target).closest(".right-bar-toggle, .right-bar").length || $("body").removeClass("right-bar-enabled")
}), function () {
    if (document.getElementById("topnav-menu-content")) {
        for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), $ = 0, n = e.length; $ < n; $++) e[$].onclick = function (e) {
            "#" === e.target.getAttribute("href") && (e.target.parentElement.classList.toggle("active"), e.target.nextElementSibling.classList.toggle("show"))
        };
        window.addEventListener("resize", s)
    }
}(), [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (e) {
    return new bootstrap.Tooltip(e)
}), [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(function (e) {
    return new bootstrap.Popover(e)
}), window.sessionStorage && ((a = sessionStorage.getItem("is_visited")) ? ($(".right-bar input:checkbox").prop("checked", !1), $("#" + a).prop("checked", !0), n(a)) : sessionStorage.setItem("is_visited", "light-mode-switch")), $("#light-mode-switch, #dark-mode-switch, #rtl-mode-switch").on("change", function (e) {
    n(e.target.id)
}), $(".toggle-search").on("click", function () {
    var e = $(this).data("target");
    e && $(e).toggleClass("open")
}), $(window).on("load", function () {
    $("#status").fadeOut(), $("#preloader").delay(350).fadeOut("slow")
}), Waves.init()


$(document).on('click', '.rollback', function () {
    Swal.fire({
        title: "Annuler l'inventaire ?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, annuler cet inventaire!",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-danger mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
        },
        didOpen: () => {
            $('.btn').blur()
        },
        preConfirm: async () => {
            Swal.showLoading();
            try {
                const [response] = await Promise.all([new Promise((resolve, reject) => {
                    $.ajax({
                        url: $(this).data('url'), method: 'GET', headers: {
                            'X-CSRF-TOKEN': __csrf_token
                        }, success: resolve, error: (_, jqXHR) => reject(_)
                    });
                })]);

                return response;
            } catch (jqXHR) {
                let errorMessage = "Une erreur s'est produite lors de la demande.";
                if(jqXHR.status !== undefined) {
                    if (jqXHR.status === 404) {
                        errorMessage = "La ressource n'a pas été trouvée.";
                    }
                    if (jqXHR.status === 403) {
                        errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                    }
                }
                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });

                throw jqXHR;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                Swal.fire({
                    title: 'Succès',
                    text: result.value,
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-success mx-2',
                    },
                }).then(result => {
                    if (typeof table != 'undefined') {
                        table.ajax.reload();
                    } else {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: "Une erreur s'est produite lors de la demande.",
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
            }
        }
    })
});
$(document).on('click', '.__promesse_respecter-btn', function () {
    Swal.fire({
        title: "Est-vous sûr?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, Marquer comme réspécté!",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-success mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
        },
        didOpen: () => {
            $('.btn').blur()
        },
        preConfirm: async () => {
            Swal.showLoading();
            try {
                const [response] = await Promise.all([new Promise((resolve, reject) => {
                    $.ajax({
                        url: $(this).data('url'), method: 'POST', headers: {
                            'X-CSRF-TOKEN': __csrf_token
                        }, success: resolve, error: (_, jqXHR) => reject(_)
                    });
                })]);

                return response;
            } catch (jqXHR) {
                let errorMessage = "Une erreur s'est produite lors de la demande.";
                if (jqXHR.status === 404) {
                    errorMessage = "La ressource n'a pas été trouvée.";
                }
                if (jqXHR.status === 403) {
                    errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                }
                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
                throw jqXHR;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                Swal.fire({
                    title: 'Succès',
                    text: result.value,
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-success mx-2',
                    },
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.reload()
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: "Une erreur s'est produite lors de la demande.",
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
            }
        }
    })
});
$(document).on('click', '.__promesse_rompre-btn', function () {
    Swal.fire({
        title: "Est-vous sûr?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, Marquer comme romprée!",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-warning mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
        },
        didOpen: () => {
            $('.btn').blur()
        },
        preConfirm: async () => {
            Swal.showLoading();
            try {
                const [response] = await Promise.all([new Promise((resolve, reject) => {
                    $.ajax({
                        url: $(this).data('url'), method: 'POST', headers: {
                            'X-CSRF-TOKEN': __csrf_token
                        }, success: resolve, error: (_, jqXHR) => reject(_)
                    });
                })]);

                return response;
            } catch (jqXHR) {
                let errorMessage = "Une erreur s'est produite lors de la demande.";
                if (jqXHR.status === 404) {
                    errorMessage = "La ressource n'a pas été trouvée.";
                }
                if (jqXHR.status === 403) {
                    errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                }
                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
                throw jqXHR;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                Swal.fire({
                    title: 'Succès',
                    text: result.value,
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-success mx-2',
                    },
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.reload()
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: "Une erreur s'est produite lors de la demande.",
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
            }
        }
    })
});
