<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>AnimeLHD Player</title>
		<script src="https://content.jwplatform.com/libraries/KB5zFt7A.js"></script>
		<script src="//bowercdn.net/c/jquery-1.11.1/dist/jquery.min.js"></script>
		<meta name="robots" content="noindex, nofollow" />
		<meta name="referrer" content="never" />
		<meta name="referrer" content="no-referrer" />
		<style>
			html, body, #container {
				margin: 0;
				padding: 0;
				width: 100%;
				height: 100%;
				background: #000;
				color: #fff;
				overflow: hidden;
			}
			#container {
				position: absolute;
				text-align: center;
			}
			video {
				outline: none;
			}
			#player{
				width:100%!important;
				height:100%!important;
				overflow:hidden;
				background-color:#000
			}
		</style>
	</head>
	<body>
		<div id="container" class="container">
			<div style="position: relative; top: 50%; margin-top: -7px;" id="message">
				<img src="https://i.imgur.com/Re5J5or.png" alt="Reproducir" style="cursor: pointer; margin-top: -64px;" id="start" />
			</div>
			<div id="player"></div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
			    var lastRequest = 0;
			    var startTime = 0;
			    var checkUrl;
			    var limitRequest = 3;
			    var resizer;
			    
			    request(0);
			
			    $("#start").click(function(){
			        $("#message").text('Solicitando video...');
			        request(0);
			    });
			
			    function request(num){
			        lastRequest = new Date().getTime() / 1000;
			        if(!checkUrl) return;
			
			        $.get(checkUrl, function(data) {
			            if(typeof data[0].sleep != "undefined"){
			                if(isNaN(data[0].sleep)) data[0].sleep = 3000;
			
			                if(++num < limitRequest) setTimeout(function(){ request(num); }, data[0].sleep);
			                else $("#message").text('No se pudo obtener video.');
			            } else if(typeof data[0].file !== 'undefined') {
			                
			                setup = { 
								primary: 'html5',
								autostart: true,
								sources: data,
								type: "video/mp4",
							};
			                jwplayer('player').setup(setup);
			                window.onresize = function(){
			                    clearTimeout(resizer);
			                    resizer = setTimeout(resizeVideo, 100);
			                };
			                $('#message').hide();
			
			                jwplayer().on('ready', function(event){
			                    if(startTime != 0) jwplayer('player').seek(startTime);
			                });
			
			                jwplayer().on('seek', function(event){ startTime = event.offset; });
			                jwplayer().on('time', function(event){ startTime = event.position; });
			
			                jwplayer().on('error', function(event){
			                    var currentTime = new Date().getTime() / 1000;
			                    if(currentTime - 5 < lastRequest) return;
			
			                    jwplayer().remove();
			                    $("#message").show().text("Espere un momento...");
			                    request(0);
			                });
			            } else {
			                $("#message").text('Ha ocurrido un error: '+ data.error);
			            }
			        }, "json").fail(function() {
			            $("#message").text('Ha ocurrido un error, el servidor no respondio correctamente');
			        });
			    }
			    function resizeVideo(){
			        jwplayer().resize($(window).width(),$(window).height());
			    }
			    function init(){
			        checkUrl = "{{ route('generateVideo',[$player->server->title,Crypt::encryptString($player->code)]) }}";
			    }
			    init();
			});
		</script>
	</body>
</html>