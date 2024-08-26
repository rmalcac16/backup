var svg_load = '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve"><path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/><path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0C22.32,8.481,24.301,9.057,26.013,10.047z"><animateTransform attributeType="xml"attributeName="transform"type="rotate"from="0 20 20"to="360 20 20"dur="0.5s"repeatCount="indefinite"/></path></svg>';

function filter(t) {
    document.getElementById("filterInsert").classList.toggle("filterInsertA"), t.classList.toggle("filtroA"), filtroA = document.querySelector(".filtroA"), null != filtroA ? document.querySelector("body").setAttribute("style", "overflow-x: hidden;overflow-y: hidden;") : document.querySelector("body").setAttribute("style", "")
}

function cfilter(t, e, n) {
    if (1 == n) var r = "genero";
    2 == n && (r = "year");
    var i = document.getElementById("cadenota").getAttribute(r);
    if (i)
        if (-1 != i.indexOf(e));
        else {
            var o = i + e + ",";
            document.getElementById("cadenota").setAttribute(r, o)
        }
    else document.getElementById("cadenota").setAttribute(r, e + ",");
    t.setAttribute("onclick", "nofilter(this, '" + e + "', '" + n + "');"), t.classList.toggle("filterON");
    var u = document.getElementById("contadorFiltro").getAttribute("cantidad");
    filtrovar = document.getElementById("contadorFiltro"), u ? (moreselect = parseInt(u) + parseInt(1), filtrovar.setAttribute("cantidad", moreselect), filtrovar.innerHTML = `Has seleccionado ${moreselect} elemento`) : (filtrovar.setAttribute("cantidad", 1), filtrovar.innerHTML = "Has seleccionado 1 elemento")
}

function nofilter(t, e, n) {
    if (1 == n) var r = "genero";
    2 == n && (r = "year");
    var i = document.getElementById("cadenota").getAttribute(r);
    i && -1 != i.indexOf(e) && (cadenada = i.replace(e + ",", ""), document.getElementById("cadenota").setAttribute(r, cadenada)), t.setAttribute("onclick", "cfilter(this, '" + e + "', '" + n + "');"), t.classList.toggle("filterON");
    var o = document.getElementById("contadorFiltro").getAttribute("cantidad");
    o && (moreselect = parseInt(o) - parseInt(1), filtrovar = document.getElementById("contadorFiltro"), filtrovar.setAttribute("cantidad", moreselect), filtrovar.innerHTML = `Has seleccionado ${moreselect} elemento`)
}

function doneFilter() {
    nameSection = document.getElementById("cadenota").getAttribute("section"), yearManual = document.querySelector("input[name=theyear]").value, getDiv = document.getElementById("cadenota"), getDiv.getAttribute("genero") ? genero = getDiv.getAttribute("genero") : genero = ",", getDiv.getAttribute("year") ? year = getDiv.getAttribute("year") : year = ",", yearManual ? yearMa = yearManual : yearMa = "";
    var t = URLBASE + "/" + nameSection + "/filtro/" + genero + "/" + year + yearMa;
    window.location.href = t
}

up.on("up:link:follow", function(t) {
    document.querySelector(".cargaAjax").classList.add("CA_Active")
}), up.on("up:fragment:inserted", function(t) {
    echo.init({
        callback: function(t, e) {}
    }), existFilter = document.querySelector(".filtro"), existFilter && (idtype = document.getElementById("movidyMain").getAttribute("idtype"), document.getElementById("filterInsert").innerHTML = `<div class="boxFilter">\n    \t\n\t\t<div id="Acciones">\n    \t\n\t\t<div id="cadenota" section="${idtype}" genero="" year=""></div>\n    \t\n\t\t<ul class="Ageneros">\n    \t\n\t\t    <li onclick="cfilter(this, 'Acción', 1);"><img src="${URLBASE}/img/genres/action.svg"><b>Acción</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Aventura', 1);"><img src="${URLBASE}/img/genres/aventura.svg"><b>Aventura</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Ciencia ficción', 1);"><img src="${URLBASE}/img/genres/ciencia-ficcion.svg"><b>Ciencia ficción</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Comedia', 1);"><img src="${URLBASE}/img/genres/comedia.svg"><b>Comedia</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Ecchi', 1);"><img src="${URLBASE}/img/genres/ecchi.svg"><b>Ecchi</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Escolares', 1);"><img src="${URLBASE}/img/genres/escolares.svg"><b>Escolares</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Fantasia', 1);"><img src="${URLBASE}/img/genres/fantasia.svg"><b>Fantasia</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Harem', 1);"><img src="${URLBASE}/img/genres/harem.svg"><b>Harem</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Magia', 1);"><img src="${URLBASE}/img/genres/magia.svg"><b>Magia</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Romance', 1);"><img src="${URLBASE}/img/genres/romance.svg"><b>Romance</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Shounen', 1);"><img src="${URLBASE}/img/genres/shounen.svg"><b>Shounen</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Super Poderes', 1);"><img src="${URLBASE}/img/genres/superpoderes.svg"><b>Super Poderes</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Suspenso', 1);"><img src="${URLBASE}/img/genres/suspenso.svg"><b>Suspenso</b></li>\n    \t\n\t\t    <li onclick="cfilter(this, 'Terror', 1);"><img src="${URLBASE}/img/genres/terror.svg"><b>Terror</b></li>\n    \t\n\t\t</ul>\n    \t\n\n\t\t<ul class="Ayears Ageneros">\n    \t\n\t\t    <li onclick="cfilter(this, 2020, 2);">2020</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2019, 2);">2019</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2018, 2);">2018</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2017, 2);">2017</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2016, 2);">2016</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2015, 2);">2015</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2014, 2);">2014</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2013, 2);">2013</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2012, 2);">2012</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2011, 2);">2011</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2010, 2);">2010</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2009, 2);">2009</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2008, 2);">2008</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2007, 2);">2007</li>\n    \t\n\t\t    <li onclick="cfilter(this, 2006, 2);">2006</li>\n    \t\n\t\t\t<input type="text" name="theyear" placeholder="1985" onclick="cfilter(this);">\n    \t\n\t\t</ul>\n    \t\n\n\t\t\t<div class="selDf">\n    \t\n\t\t\t<div id="filtrar" onclick="doneFilter();">Filtrar ${idtype}</div>\n    \t\n\t\t\t<div id="contadorFiltro" cantidad="">Has seleccionado 0 elemento</div>\n    \t\n\t\t\t</div></div></div>`), GenSelected = document.getElementById("movidyMain").getAttribute("genr"), GenSelected && (ArrayGen = GenSelected.split(","), ArrayGen.forEach(function(t) {
        if (t) {
            if ("Acción" == t) var e = 0;
            "Aventura" == t && (e = 1), "Ciencia ficciÃ³n" == t && (e = 2), "Comedia" == t && (e = 3), "Ecchi" == t && (e = 4), "Escolares" == t && (e = 5), "Fantasia" == t && (e = 6), "Harem" == t && (e = 7), "Magia" == t && (e = 8), "Romance" == t && (e = 9), "Shounen" == t && (e = 10), "Super poderes" == t && (e = 11), "Suspenso" == t && (e = 12), "Terror" == t && (e = 13), GenrElemts = document.querySelector(".Ageneros").getElementsByTagName("li")[e], cfilter(GenrElemts, t, 1)
        }
    })), YerSelected = document.getElementById("movidyMain").getAttribute("yers"), YerSelected && (ArrayYer = YerSelected.split(","), ArrayYer.forEach(function(t) {
        if (t) {
            if ("2020" == t) var e = 0;
            "2019" == t && (e = 1), "2018" == t && (e = 2), "2017" == t && (e = 3), "2016" == t && (e = 4), "2015" == t && (e = 5), "2014" == t && (e = 6), "2013" == t && (e = 7), "2012" == t && (e = 8), "2011" == t && (e = 9), "2010" == t && (e = 10), "2009" == t && (e = 11), "2008" == t && (e = 12), "2007" == t && (e = 13), "2006" == t && (e = 14), t < "2006" ? (YerasElemts = document.querySelector(".Ayears input"), YerasElemts.setAttribute("value", t), cfilter(YerasElemts, t, "2")) : (YerasElemts = document.querySelector(".Ayears").getElementsByTagName("li")[e], cfilter(YerasElemts, t, 2))
        }
    }))
});
var search_timeout, search_content_timeout, search_ajax, timer, v_search_mode = 1,
    v_search_index = 0;

function getMeta(t) {
    const e = document.getElementsByTagName("meta");
    for (let n = 0; n < e.length; n++)
        if (e[n].getAttribute("name") === t) return e[n].getAttribute("content");
    return ""
}

function liveSearch(t, e) {
    var n = document.getElementById("autoseach"),
        r = document.getElementById("InFrag"),
        i = n.value;
    if (i.length >= 1 ? n.setAttribute("style", "background: #fff;") : n.setAttribute("style", ""), i.length >= 3)
        if (r.setAttribute("style", ""), document.querySelector(".listSearch").classList.add("listSearchA"), e) {
            var o = new FormData;
            o.append("buscar", i), fetch(URLBASE + "/liveSearch", {
                headers: {
                    "X-CSRF-TOKEN": getMeta("csrf-token")
                },
                method: "POST",
                body: o
            }).then(t => t.json()).then(t => {
                var e = document.querySelector(".listSearch");
                200 == t.status && (e.innerHTML = t.result), 404 == t.status && (e.innerHTML = '<div class="noResLive">No hubo ninguna coincidencia</div>'), 500 == t.status && (e.innerHTML = '<div class="noResLive">Caracteres Invalidos</div>'), 600 == t.status && (e.innerHTML = '<div class="noResLive">Hubo un error inesperado</div>')
            }).catch(function() {
                document.querySelector(".listSearch").innerHTML = '<div class="noResLive">Hubo un error inesperado</div>'
            })
        } else document.querySelector(".listSearch").innerHTML = "<p>" + svg_load + "</p>", clearTimeout(search_timeout), search_timeout = setTimeout("liveSearch(false, 1)", 500);
    else r.setAttribute("style", ""), document.querySelector(".listSearch").classList.remove("listSearchA")
}

function meN_Mo() {
    document.querySelector(".NavMob").classList.add("NavMobA"), document.querySelector(".fndo_Mo").classList.add("fndo_MoA")
}

function fndo_Mo(t) {
    document.querySelector(".NavMob").classList.remove("NavMobA"), document.querySelector(".fndo_Mo").classList.remove("fndo_MoA")
}

function seac_Mo() {
    document.querySelector(".seMobF").classList.add("seMobFA")
}

function cseac_Mo() {
    document.querySelector(".seMobF").classList.remove("seMobFA"), document.querySelector(".listSearch").classList.remove("listSearchA"), document.getElementById("autoseach").value = ""
}

function carruselMovidy(t, e) {
    var n = document.querySelector(".contenedorCarrusel").getElementsByTagName("article");
    itemsAnchura = parseInt(t) / parseInt(e), itemsContenedor = parseInt(itemsAnchura) * parseInt(n.length), document.querySelector(".contenedorCarrusel").setAttribute("style", "width: " + itemsContenedor + "px;");
    for (var r = 0; r < n.length; r++)
        if (2 == e)
            if (0 == r || 2 == r || 4 == r) {
                var i = parseInt(itemsAnchura) - parseInt(10);
                n[r].setAttribute("style", "width: " + i + "px;margin: 0 10px 0 0;")
            } else n[r].setAttribute("style", "width: " + itemsAnchura + "px;");
    else n[r].setAttribute("style", "width: " + itemsAnchura + "px;")
}

function corkdw() {
    var t = document.getElementById("movidyCarrusel").getAttribute("items"),
        e = document.querySelector(".contenedorCarrusel").getElementsByTagName("article").length;
    Math.ceil(parseInt(e) / parseInt(t)), e > t && document.querySelector(".carrNext").setAttribute("onclick", "nextC(this, 2);")
}

function nextC(t, e) {
    echo.init({
        callback: function(t, e) {}
    });
    var n = document.getElementById("movidyCarrusel").getAttribute("items"),
        r = document.querySelector(".contenedorCarrusel").getElementsByTagName("article").length,
        i = Math.ceil(parseInt(r) / parseInt(n));
    if (i >= e) {
        sumaPage = parseInt(e) + parseInt(1), t.setAttribute("onclick", "nextC(this, " + sumaPage + ");"), i < sumaPage && document.querySelector(".carrNext").setAttribute("style", "cursor: no-drop;opacity: 0.6;");
        var o = document.getElementById("movidyCarrusel").offsetWidth;
        itemsAnchura = parseInt(o) / parseInt(n), 2 == n ? (2 == e && (cosa = 2), 3 == e && (cosa = 4), 4 == e && (cosa = 6)) : (2 == e && (cosa = 1), 3 == e && (cosa = 2), 4 == e && (cosa = 3), 5 == e && (cosa = 4), 6 == e && (cosa = 5)), move = parseInt(itemsAnchura) * parseInt(cosa);
        var u = document.querySelector(".contenedorCarrusel").offsetWidth;
        document.querySelector(".contenedorCarrusel").setAttribute("style", "width: " + u + "px;margin-left:-" + move + "px;"), restaPage = parseInt(e) - parseInt(1), document.querySelector(".carrPrev").setAttribute("onclick", "prevC(this, " + restaPage + ", " + move + ");"), document.querySelector(".carrPrev").setAttribute("style", "cursor: pointer;opacity: 1;")
    }
}

function prevC(t, e, n) {
    var r = document.getElementById("movidyCarrusel").getAttribute("items"),
        i = document.querySelector(".contenedorCarrusel").getElementsByTagName("article").length;
    if (Math.ceil(parseInt(i) / parseInt(r)), e > 0) {
        var o = document.getElementById("movidyCarrusel").offsetWidth;
        itemsAnchura = parseInt(o) / parseInt(r), 2 == r ? (2 == e && (move = parseInt(n) / parseInt(2)), 1 == e && (move = 0)) : move = parseInt(n) - parseInt(itemsAnchura), restaPage = parseInt(e) - parseInt(1), document.querySelector(".carrPrev").setAttribute("onclick", "prevC(this, " + restaPage + ", " + move + ");"), 0 == restaPage && document.querySelector(".carrPrev").setAttribute("style", "cursor: no-drop;opacity: 0.6;");
        var u = document.querySelector(".contenedorCarrusel").offsetWidth;
        document.querySelector(".contenedorCarrusel").setAttribute("style", "width: " + u + "px;margin-left:-" + move + "px;"), sumaPage = parseInt(e) + parseInt(1), document.querySelector(".carrNext").setAttribute("onclick", "nextC(this, " + sumaPage + ");"), document.querySelector(".carrNext").setAttribute("style", "")
    }
}

function nep(t, e) {
    var n = document.querySelector(".contepID_" + e),
        r = document.querySelector(".hidlinkAdd_2");
    null != r && (2 == e ? r.classList.add("hla_Ac") : r.classList.remove("hla_Ac"));
    var o = document.querySelector(".navEP2").getElementsByTagName("li");
    for (i = 0; i < o.length; i++) o[i].classList.remove("act_N");
    var u = document.getElementsByClassName("contEP");
    for (i = 0; i < u.length; i++) u[i].classList.remove("contEP_A");
    t.classList.add("act_N"), n.classList.add("contEP_A")
}
async function registerSW() {
    if ("serviceWorker" in navigator) try {
        await navigator.serviceWorker.register(URLBASE + "js/sw.js")
    } catch (t) {
        console.log("SW registration failed")
    }
}

function screenTest(t) {
    t.matches ? (null == document.querySelector(".xLat") && null == document.querySelector(".xLatSer") || (document.querySelector(".navri").classList.remove("nav-A"), document.querySelector(".inE1").classList.remove("ActOn")), null != document.querySelector(".xLat") && (document.querySelector(".xLat").classList.add("inE", "inE3", "ActOn"), document.querySelector(".puthere").innerHTML = '<li class="deletethis navri nav-A" onclick="singN(this, 3);"><i class="material-icons">remove_red_eye</i>Visualizador</li>'), null != document.querySelector(".xLatSer") && (document.querySelector(".xLatSer").classList.add("inE", "inE3", "ActOn"), document.querySelector(".puthere").innerHTML = '<li class="deletethis navri nav-A" onclick="singN(this, 3);"><i class="material-icons">list</i>Episodios</li>')) : (null == document.querySelector(".xLat") && null == document.querySelector(".xLatSer") || (document.querySelector(".navri").classList.add("nav-A"), document.querySelector(".inE1").classList.add("ActOn"), null != document.querySelector(".deletethis") && document.querySelector(".deletethis").remove()), null != document.querySelector(".xLat") && document.querySelector(".xLat").classList.remove("inE", "inE3", "ActOn"), null != document.querySelector(".xLatSer") && document.querySelector(".xLatSer").classList.remove("inE", "inE3", "ActOn"))
}! function(t, e) {
    "function" == typeof define && define.amd ? define(function() {
        return e(t)
    }) : "object" == typeof exports ? module.exports = e : t.echo = e(t)
}(this, function(t) {
    "use strict";
    var e, n, r, i, o, u = {},
        s = function() {},
        a = function(t, e) {
            if (function(t) {
                    return null === t.offsetParent
                }(t)) return !1;
            var n = t.getBoundingClientRect();
            return n.right >= e.l && n.bottom >= e.t && n.left <= e.r && n.top <= e.b
        },
        l = function() {
            !i && n || (clearTimeout(n), n = setTimeout(function() {
                u.render(), n = null
            }, r))
        };
    return u.init = function(n) {
        var a = (n = n || {}).offset || 0,
            c = n.offsetVertical || a,
            p = n.offsetHorizontal || a,
            h = function(t, e) {
                return parseInt(t || e, 10)
            };
        e = {
            t: h(n.offsetTop, c),
            b: h(n.offsetBottom, c),
            l: h(n.offsetLeft, p),
            r: h(n.offsetRight, p)
        }, r = h(n.throttle, 250), i = !1 !== n.debounce, o = !!n.unload, s = n.callback || s, u.render(), document.addEventListener ? (t.addEventListener("scroll", l, !1), t.addEventListener("load", l, !1)) : (t.attachEvent("onscroll", l), t.attachEvent("onload", l))
    }, u.render = function(n) {
        for (var r, i, l = (n || document).querySelectorAll("[data-echo], [data-echo-background]"), c = l.length, p = {
                l: 0 - e.l,
                t: 0 - e.t,
                b: (t.innerHeight || document.documentElement.clientHeight) + e.b,
                r: (t.innerWidth || document.documentElement.clientWidth) + e.r
            }, h = 0; h < c; h++) i = l[h], a(i, p) ? (o && i.setAttribute("data-echo-placeholder", i.src), null !== i.getAttribute("data-echo-background") ? i.style.backgroundImage = "url(" + i.getAttribute("data-echo-background") + ")" : i.src !== (r = i.getAttribute("data-echo")) && (i.src = r), o || (i.removeAttribute("data-echo"), i.removeAttribute("data-echo-background")), s(i, "load")) : o && (r = i.getAttribute("data-echo-placeholder")) && (null !== i.getAttribute("data-echo-background") ? i.style.backgroundImage = "url(" + r + ")" : i.src = r, i.removeAttribute("data-echo-placeholder"), s(i, "unload"));
        c || u.detach()
    }, u.detach = function() {
        document.removeEventListener ? t.removeEventListener("scroll", l) : t.detachEvent("onscroll", l), clearTimeout(n)
    }, u
}), up.on("up:fragment:inserted", function(t) {
    document.querySelector(".cargaAjax").classList.remove("CA_Active")
}), up.on("up:fragment:inserted", function(t) {
    const e = document.getElementById("conct");
    if (null != e) {
        const t = t => {
                document.getElementById("changeNowAva").innerHTML = e.files[0].name
            },
            n = () => t(e.files[0]);
        e.addEventListener("change", n, !1)
    }
}), up.on("up:fragment:inserted", function(t) {
    if (null != document.getElementById("movidyCarrusel")) {
        function e(t) {
            null != document.getElementById("movidyCarrusel") && (t.matches ? (document.getElementById("movidyCarrusel").setAttribute("items", "1"), corkdw()) : (document.getElementById("movidyCarrusel").setAttribute("items", "2"), corkdw()))
        }
        document.getElementById("movidyCarrusel").setAttribute("style", "display: block;");
        var n = window.matchMedia("(max-width: 700px)");
        e(n), n.addListener(e);
        var r = document.getElementById("movidyCarrusel").getAttribute("items"),
            i = function() {
                if (null != document.getElementById("movidyCarrusel")) {
                    var t = document.getElementById("movidyCarrusel").getAttribute("items");
                    carruselMovidy(document.getElementById("movidyCarrusel").offsetWidth, t)
                }
            };
        window.addEventListener("resize", i), i && carruselMovidy(document.getElementById("movidyCarrusel").offsetWidth, r), echo.init({
            callback: function(t, e) {}
        })
    }
}), up.on("up:fragment:inserted", function(t) {
    var e = window.matchMedia("(max-width: 1350px)");
    screenTest(e), e.addListener(screenTest);
    var n = document.location.pathname;
    ga("set", "page", n), ga("send", "pageview")
}), up.on("up:fragment:inserted", function(t) {
    registerSW()
});