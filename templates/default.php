<?php // Default MP3 Template

	// $album_array('playerID',array('id','title','year','release','type','format','band','songs','label','link','slug',excerpt','content'))

	/* CJ Album Display */
	$albumDisplay = '';
	$albumScripts  = ''; 
	
	$post_content = apply_filters('the_content',get_the_content());

	$playerID = ''; 
	$albumArray = array();
	if (isset($album_array)) {
		foreach ($album_array as $key=>$value) {
			if ($key === 'playerID') {
				$playerID = $album_array['playerID'];		
			} else {
				$albumArray[] = $value;
			}
		}
		$playerID = ((!empty($playerID)) ? $playerID : 'como-mp3-player');
		$tracks = array();
		$albumCount = count($albumArray);

		$playerInitial = array();
		$a = 0;
		foreach ($albumArray as $album) {
			$albumTitle = (($album['title']) ? addslashes($album['title']) : '');
			$albumCover = get_the_post_thumbnail_url($album['id'],'thumbnail');
			$imgLink = wp_get_attachment_image_src( get_post_thumbnail_id($album['id']),'full',false);

			$albumSubtitle = (($album['label']) ? addslashes($album['label']) : '');
			$albumSubtitle .= (($album['release']) ? ' #'. $album['release'] : '');
			$albumSubtitle .= (($album['label'] && $album['year']) ? ' - ' : '');
			$albumSubtitle .= (($album['year']) ? $album['year'] : '');

			$type = $album['type'];

			if ($a == 0) { 
				$playerInitial['id'] = $album['id']; 
				$playerInitial['title'] = addslashes($albumTitle);
				$playerInitial['subtitle'] = addslashes($albumSubtitle); 
				$playerInitial['slug'] = (($album['slug']) ? $album['slug'] : preg_replace('/[[:space:]]+/', '-', strtolower($albumTitle))); 
				$playerInitial['cover'] = $albumCover; 
				$playerInitial['cover-link'] = $imgLink[0]; 
			}

			if (!empty($album['songs'])) {
				$songs = maybe_unserialize($album['songs']);
				foreach ($songs as $track) {
					$trk = "{'track': ". $track['song-track'] .", 'name': '". addslashes($track['song-title']) ."', 'length': '". str_replace('.',':',$track['song-time']) ."', 'file': '". wp_get_attachment_url($track['song-file']) ."', 'album': '". addslashes($albumTitle) ."', 'subtitle': '". addslashes($albumSubtitle) ."', 'cover': '". $albumCover ."', 'player': '". $playerID ."', 'type': '". $type ."', 'band': '". addslashes(get_the_title($track['song-band'])) ."' }";
					if (!empty($player['band'])) { 
						if ($player['band'] === $track['song-band']) {
							$tracks[] = $trk; 		
						} 
					} else {
						$tracks[] = $trk;
					}
				}
				array_unique($tracks);
			}
			$a++;
		}

		if (count($tracks) > 0) {
			$albumInfo = "'". $playerID ."': [". implode(',',$tracks) ."]";

			//$albumInfo = "[". implode(',',$tracks) ."]";
			$albumSongs = $albumInfo;

			$mp3player = '<div class="playerWrap" data-album="'. $playerInitial['slug'] .'" id="'. $playerID .'" data-albumID="'. $playerInitial['id'] .'" data-songs="'. $albumSongs .'">
			  <div class="playerHead">
				<div class="nowPlay">
					<div class="row justify-content-between">
					  <div class="npTitle col-10"></div>
					  <div class="npTime col-2"></div>
					</div>
					<span class="left npAction" id="npAction">Paused...</span>
				 </div>
				 <div class="audiowrap">
					  <div class="audio0">
						   <audio preload class="audio1">Your browser does not support HTML5 Audio!</audio>
					  </div>
					  <div class="tracks">
						<div class="hp_slide"><div class="hp_range"></div></div>
						<div class="stereo-buttons row justify-content-center">
							<a class="col-xs-6 col-sm-2 btn btn-stereo btnPlay nopadding"><i class="fa fa-play" aria-hidden="true"></i></a>
							<a class="col-xs-6 col-sm-2 btn btn-stereo btnPause nopadding hide"><i class="fa fa-pause" aria-hidden="true"></i></a>
							<a class="col-xs-6 col-sm-2 btn btn-stereo btnPrev nopadding"><i class="fa fa-backward" aria-hidden="true"></i></a>
							<a class="col-xs-6 col-sm-2 btn btn-stereo btnNext nopadding"><i class="fa fa-forward" aria-hidden="true"></i></a>
							<div class="col-xs-6 col-sm-4 btn btn-stereo nopadding">
								<input type="range" class="volume-bar" title="volume" min="0" max="10" step="1" value="10">
							</div>
							<a class="col-xs-6 col-sm-2 btn btn-stereo btnMute nopadding">
								<i class="fa fa-volume-up" aria-hidden="true"></i>
								<i class="fa fa-volume-off hide" aria-hidden="true"></i>
							</a>
						</div>
					 </div>
				 </div>
			  </div>
			  <div class="playerBody">
				 <div class="plwrap row">
					  <div class="cover col-xs-0 col-sm-4 col-md-4 h-100"><div class="clearfix"><a href="'. $playerInitial['cover-link'] .'" class="coverWrap imgLink"><img src="'. $playerInitial['cover'] .'" id="npCover" class="npCover d-block"></a></div></div>
					  <div class="col-xs-12 col-sm-8 col-md-8 h-100"><div class="playList h-100"><div class="listWrap"><ul id="plList" class="plList"></ul></div></div></div>
				 </div>
			  </div>
			</div>';

			$albumDisplay .= '<div id="album-players">'. $mp3player .'</div>';

			$albumDisplay .= "<script>var albums = { ". $albumSongs ."};</script>"; 
			
			
			/*$playerScript = "<script>
			jQuery(function($) {
				var supportsAudio = !!document.createElement('audio').canPlayType;
				var albums = { 
					". $albumSongs ."
				};
				$('.playerWrap').each(function() {
					var album = $('.playerWrap').attr('id');
					var albumDiv = '#' + album;
					var id = $(this).find('txtId').val();
					tracks = albums[album];
					if (supportsAudio) {
						var index = 0,
						playing = false,
						mediaPath = '',
						extension = '',
						buildPlaylist = $.each(tracks, function(key, value) {
							var trackNumber = value.track,
								trackName = value.name.replace(/'/g, \"\\'\"),
								trackLength = value.length,
								trackFile = value.file,
								trackAlbumTitle = value.album.replace(/'/g, \"\\'\"),
								trackSubtitle = value.subtitle.replace(/'/g, \"\\'\"),
								trackCover = value.cover;
								albumType = value.type;
								trackBand = value.band.replace(/'/g, \"\\'\");
							if (trackNumber.toString().length === 1) {
								trackNumber = '0' + trackNumber;
							} else {
								trackNumber = '' + trackNumber;
							}
							var playerID = value.player;
							var trackID = trackAlbumTitle.toLowerCase().replace(/\s/g, '-') +'-'+ trackNumber;
							trackID = trackID.replace(/[^a-z0-9\s]/gi, '').replace(/'/g, \"\\'\").replace(/[_\s]/g, '-');

							$(albumDiv + ' #plList').append('<li data-album=\"'+ playerID +'\" id=\"'+ trackID +'\"><div class=\"plItem\" data-track=\"'+ trackNumber +'\" data-name=\"'+ trackName +'\" data-file=\"'+ trackFile +'\" data-length=\"'+ trackLength +'\" data-album-title=\"'+ trackAlbumTitle +'\" data-subtitle=\"'+ trackSubtitle +'\" data-cover=\"'+ trackCover +'\"><div class=\"plNum\">' + trackNumber + '.</div><div class=\"plTitle\"><span class=\"title\">'+ ((albumType !== 'single') ? trackBand +' - ' : '') +' ' + trackName + '</span></div><div class=\"plLength\">' + trackLength + '</div></div></li>');

							var trackWidth = $(document).find('#'+ trackID).width();
							var titleWidth = $(document).find('#'+ trackID).find('.title').width();
							var trackTimerWidth = $(document).find('#'+ trackID).find('.plLength').width();
							trackWidth = trackWidth-trackTimerWidth 

							if ((titleWidth) > (trackWidth-trackTimerWidth)) {
								$(document).find('#'+ trackID).find('.plItem').addClass('long-scroll');
							} 
						}),
						trackCount = tracks.length,
						npAction = $(albumDiv).find('.npAction'),
						npTitle = $(albumDiv).find('.npTitle'),
						npTime = $(albumDiv).find('.npTime'),
						npCover = $(albumDiv).find('.npCover'),
						audio = $(albumDiv).find('audio').bind('play', function () {
							playing = true;
							npAction.text('Now Playing...');
						}).bind('pause', function () {
							playing = false;
							npAction.text('Paused...');
						}).bind('ended', function () {
							npAction.text('Paused...');
							if ((index + 1) < trackCount) {
								index++;
								loadTrack(index);
								audio.play();
							} else {
								audio.pause();
								index = 0;
								loadTrack(index);
							}
						}).get(0),
						btnPrev = $(albumDiv + ' .btnPrev').click(function () {
						   if ((index - 1) > -1) {
								index--;
								loadTrack(index);
								if (playing) {
									audio.play();
								}
							} else {
								audio.pause();
								index = 0;
								loadTrack(index);
							}
						}),
						btnNext = $(albumDiv + ' .btnNext').click(function () {
							if ((index + 1) < trackCount) {
								index++;
								loadTrack(index);
								if (playing) {
									audio.play();
								}
							} else {
								audio.pause();
								index = 0;
								loadTrack(index);
							}
						}),
						btnPause = $(albumDiv + ' .btnPause').click(function () {
							audio.pause();
							$(albumDiv + ' .btnPlay').removeClass('hide');
							$(albumDiv + ' .btnPause').addClass('hide');
						}),
						btnPlay = $(albumDiv + ' .btnPlay').click(function () {
							audio.play();
							$(albumDiv + ' .btnPlay').addClass('hide');
							$(albumDiv + ' .btnPause').removeClass('hide');
						}),
						btnMute = $(albumDiv + ' .btnMute').click(function () {
							if( $(albumDiv + ' .audio1').prop('muted') ) {
								$(albumDiv + ' .audio1').prop('muted', false);
								$(albumDiv + ' .btnMute .fa-volume-off').addClass('hide');
								$(albumDiv + ' .btnMute .fa-volume-up').removeClass('hide');
							} else {
								$(albumDiv + ' .audio1').prop('muted', true);
								$(albumDiv + ' .btnMute .fa-volume-up').addClass('hide');
								$(albumDiv + ' .btnMute .fa-volume-off').removeClass('hide');
							}
						}),
						volumeSlider = $(albumDiv + ' .volume-bar').change(function (evt) {
							var player = $(albumDiv).find('.audio1');
							var playerVolume = parseInt(evt.target.value) / 10;
							$(player).prop('muted', false);
							$(albumDiv + ' .btnMute .fa-volume-off').addClass('hide');
							$(albumDiv + ' .btnMute .fa-volume-up').removeClass('hide');
							$(albumDiv + ' .audio1').prop('volume', playerVolume);
						}),
						li = $(albumDiv + ' .plList li').click(function () {
							var id = parseInt($(this).index());
							if (id !== index) {
								playTrack(id);
							}
						}),
						loadTrack = function (id) {
							$(albumDiv + ' .plSel').removeClass('plSel');
							$(albumDiv + ' .plList li:eq(' + id + ')').addClass('plSel');
							npTitle.text(tracks[id].name);
							//npTime.text(tracks[id].length);
							npTime.text(tracks[id].length);
							index = id;
							audio.src = mediaPath + tracks[id].file + extension;
							npCover.attr('src', tracks[id].cover);

							audio.addEventListener('timeupdate', function() {
								// Initiate progress bar
								var currentTime = audio.currentTime;
								var duration = audio.duration;
								$('.hp_range').stop(true,true).animate({'width':(currentTime +.25)/duration*100+'%'},250,'linear');

								// Track Countdoen Timer
								var timeleft = npTime,
								duration = parseInt( audio.duration ),
								currentTime = parseInt( audio.currentTime ),
								timeLeft = duration - currentTime,
								s, m;
								s = timeLeft % 60;
								m = Math.floor( timeLeft / 60 ) % 60;
								s = s < 10 ? '0'+s : s;
								m = m < 10 ? '0'+m : m;
								timeleft.html(m+':'+s);
							});

							// Interactive Progress Bar
							$(albumDiv + ' .hp_slide').on('click', function(e) {
								var timeline = $(this);
								var duration = audio.duration;
								var timelineWidth = timeline.width();
								var timeToSeek = e.offsetX / parseInt(timelineWidth) * duration;
								audio.currentTime = timeToSeek;
							});
						},
						playTrack = function (id) {
							//alert('ID:' + id);
							loadTrack(id);
							$(albumDiv + ' .btnPlay').addClass('hide');
							$(albumDiv + ' .btnPause').removeClass('hide');
							audio.play();
						};
						//extension = audio.canPlayType('audio/mpeg') ? '.mp3' : audio.canPlayType('audio/ogg') ? '.ogg' : '';
						loadTrack(index);  
					}
				});
			});
			</script>";

			$GLOBALS['footScript'] .= $playerScript;*/
		} 
	}
	//$albumDisplay .= (($playerInitial['subtitle']) ? '<h2 class="album-subtitle">'. $playerInitial['subtitle'] .'</h2>' : '');
	$albumDisplay .= $post_content;
	unset($tracks);