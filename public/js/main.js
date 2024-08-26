$(document).ready(function() {
    var search_opts = {
        callback: function (value) {
            searchy(value);
        },
        wait: 500,
        highlight: true,
        allowSubmit: false,
        captureLength: 2
    };
    $("#search-tv").typeWatch(search_opts);
    $("#search-tv").on('input', function () {
        var searchTerm = $(this).val();
        if (searchTerm.length > 1) {
            $('.content-search').addClass('On');
            $(".list-results").html('<li class="Loading"><img src="/img/loading.svg" alt="Loading" / width="30px"></li>');
        } else
            $('.content-search').removeClass('On');
    });

    function searchy(value) {
        if (value.length < 2)
            return;
        $.ajax({
            url:  baseurl + '/' +'ajax/searchtv',
            type: 'post',
            data: {
                value: value
            },
            headers: {
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
            dataType: 'json',
            success: function (a) {
                $('.content-search').addClass('On');
                $(".list-results").empty();
                var t = a.length;
                if (t)
                for (var i = 0; i < t; i++) {
                    switch (a[i].type.toLowerCase()) {
                        case 'tv':
                            var tipo_lista = 'Anime';
                            var tipo_css = 'anime';
                        break;
                        case 'movie':
                            var tipo_lista = 'Pelicula';
                            var tipo_css = 'pelicula';
                        break;
                        case 'ona':
                            var tipo_lista = 'ONA';
                            var tipo_css = 'ona';
                        break;
                        case 'ova':
                            var tipo_lista = 'OVA';
                            var tipo_css = 'ova';
                        break;
                        case 'special':
                            var tipo_lista = 'Especial';
                            var tipo_css = 'special';
                        break;
                        default:
                            var tipo_lista = 'No definido';
                            var tipo_css = 'not';
                    }

                    if (i >= 5) {
                        $(".list-results").append('<li class="mas-busquedas"><a href="/animes/search?s=' + encodeURI(value) + '">Más Resultados</a></li>');
                        return false;
                    }
                    $(".list-results").append(' <li><a href="' + a[i].slug + '"><figure><a href="' + a[i].slug + '"><img src="' + a[i].poster + '" alt=""></a></figure></a><a href="' + a[i].slug + '"><span class="title">' + a[i].title + '</span></a><div class="temporadas"><span class="' + tipo_css + '">' + tipo_lista + "</span></div></li>")
                } else $(".list-results").append('<li class="Loading">No se encontraron resultados</li>')
            }
        });
        
    }

    $('.content-search').click(function (e) {
        e.stopPropagation();
    });
    $(document).click(function () {
        $('.content-search').removeClass('On');
    });

    edgrid.menu('nav', 'menu');

        $(document).ready(function () {
            $("#buscador-menu").on("click", function () {
                $('#buscador-mobil').toggle("slow");
            });
        });
    $(document).ready(function () {
        $(".select-tem__link").on("click", function () {
            $('.select-tem__content').toggle("slow");
        });
    });
    function markEpiRequest(seen) {
        $.ajax({
            url: baseurl + '/ajax/checkViewEpi',
            method: 'post',
            data: {
                'seen': seen,
                'number': episodie_id
            },
            success: function(data) {
                if (data.success) {
                    if (seen == 1) {
                        seen = 0;
                        var stext = '<i class="fa fa-eye-slash"></i> Marcar como no visto';
                        var act = 'active';
                    } else {
                        seen = 1;
                        var stext = '<i class="fa fa-eye"></i> Marcar como visto';
                        var act = '';
                    }
                    if (act === 'active'){
                        $(".btn-visto").attr("data-seen", seen).addClass(act).html(stext);
                    } else{
                        $(".btn-visto").attr("data-seen", seen).removeClass('active').html(stext);
                    }
                    alertify.success(data.success);
                } else {
                    alertify.error(data.error);
                }
            },
            error: function() {}
        });
    }
    function markEpisode(seen) {
        if (!is_user) {
            alertify.error("Necesitas ser usuario registrado para poder usar está opción.");
            return;
        }
        if (episodie_id){
            if (seen == 1){
                alertify.confirm("El episodio sera marcado como visto y se mostrará en tu perfil.", function() {
                    markEpiRequest(seen)
                });
            }else if(seen == 0){
                    alertify.confirm("El episodio sera desmarcado como visto y no se mostrará en tu perfil.", function() {
                        markEpiRequest(seen)
                    });
            }
        }
    }
    $('.btn-visto').on('click', function() {
        var seen = $(this).attr("data-seen");
        markEpisode(seen);
    });


    function followTvRequest(seen) {
        $.ajax({
            url: baseurl + '/ajax/followTv',
            method: 'post',
            data: {
                'seen': seen,
                'number': tv_id
            },
            success: function(data) {
                if (data.success) {
                    if (seen == 1) {
                        seen = 0;
                        var stext = '<i class="fa fa-heart"></i> Siguiendo';
                    } else {
                        seen = 1;
                        var stext = '<i class="fa fa-heart-o"></i> Seguir';
                    }
                    $(".btn-favorite").attr("data-seen", seen).html(stext);
                    alertify.success(data.success);
                } else {
                    alertify.error(data.error);
                }
            },
            error: function() {}
        });
    }
    function followTv(seen) {
        if (!is_user) {
            alertify.error("Necesitas ser usuario registrado para poder usar está opción.");
            return;
        }
        if (tv_id){
            if (seen == 1){
                alertify.confirm("Deseas seguir?, tambíen aparecera en tu perfil.", function() {
                    followTvRequest(seen)
                });
            }else if(seen == 0){
                alertify.confirm("Deseas dejar de seguir?.", function() {
                    followTvRequest(seen)
                });
            }
        }
    }
    $('.btn-favorite').on('click', function() {
        var seen = $(this).attr("data-seen");
        followTv(seen);
    });
    $("[id$='circle']").percircle();
    $('.RateIt>a').on('click', function() {
        if (!is_user) {
            alertify.error("Necesitas ser usuario registrado para poder calificar.");
            return;
        }
        var rating = $(this).attr('data-value');
        var parent = $(this).closest(".Strs");
        $.ajax({
            url: baseurl +  '/ajax/rateTv',
            method: 'post',
            data: {
                'type': parent.attr("data-type"),
                'rating': rating,
                'id': parent.attr("data-id")
            },
            success: function(data) {
                console.log(data.error);
                if (data.error)
                    alert(data.error);
                else {
                    var new_stars;
                    for (i = 5; i >= 1; i--) {
                        new_stars += '<span class="fa fa-star-o';
                        if (data.rating >= i)
                            new_stars += ' On';
                        else if ((data.rating + 0.6) >= i)
                            new_stars += ' Hf';
                        new_stars += '  data-value="' + i + '" title="' + i + ' Estrellas"></span>';
                    }
                    parent.html(new_stars);
                    $("#votes_nmbr").text(data.rating_votes);
                    $("#A-circle").attr("data-percent", Math.round(data.rating * 20)).empty();
                    $("[id$='circle']").percircle();
                }
            },
            error: function() {}
        });
    });

    $(window).load(function(){
        $(".scrolling").mCustomScrollbar({
            theme: "minimal-dark",
            scrollButtons: {
                enable: !0
            },
            callbacks: {
                onTotalScrollOffset: 100,
                alwaysTriggerOffsets: !1
            }
        });
    })

});

function getEpisodes(a) {
    var dataurl = baseurl + '/' + 'ajax/getseason';
    $(".box-loader").removeClass("loader-hide"), $(".box-loader").addClass("loader"),
        $.ajax({
            type: "POST",
            url:dataurl,
            data:{value:a},
            success:function (a) {
                $(".box-content").html(a), $("time.timeago").timeago(), $(".box-loader").removeClass("loader"), $(".box-loader").addClass("loader-hide")
            }, error:function (a) {
                console.log(a)
            }});
    $(".box-content").hide();
    $(".box-content").slideDown('slow');
}
function episodetab(a) {
    getEpisodes(a)
}
