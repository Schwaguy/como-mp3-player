// MP3 PLayer
(function($) {
	var supportsAudio = !!document.createElement('audio').canPlayType;
	$('.playerWrap').each(function() {
		var album = $('.playerWrap').attr('id'),
		albumDiv = '#' + album,
		songs = $(albumDiv).data('songs'),
		tracks = albums[album];
		if (supportsAudio) { 
			var index = 0,
			playing = false,
			mediaPath = $(location).attr('protocol') + '//' + $(location).attr('host') + '/wp-content/uploads/',
			extension = '',
			buildPlaylist = $.each(tracks, function(key, value) {
				var trackNumber = value.track,
					trackName = value.name.replace(/'/g, "\\'"),
					trackLength = value.length,
					trackFile = value.file,
					trackAlbumTitle = value.album.replace(/'/g, "\\'"),
					trackSubtitle = value.subtitle.replace(/'/g, "\\'"),
					trackCover = value.cover,
					albumType = value.type,
					trackBand = value.band.replace(/'/g, "\\'");
				if (trackNumber.toString().length === 1) {
					trackNumber = '0' + trackNumber;
				} else {
					trackNumber = '' + trackNumber;
				}
				var playerID = value.player;
				var trackID = trackAlbumTitle.toLowerCase().replace(/\s/g, '-') +'-'+ trackNumber;
				trackID = trackID.replace(/[^a-z0-9\s]/gi, '').replace(/'/g, "\\'").replace(/[_\s]/g, '-');

				$(albumDiv + ' #plList').append('<li data-album="'+ playerID +'" id="'+ trackID +'"><div class="plItem" data-track="'+ trackNumber +'" data-name="'+ trackName +'" data-file="'+ trackFile +'" data-length="'+ trackLength +'" data-album-title="'+ trackAlbumTitle +'" data-subtitle="'+ trackSubtitle +'" data-cover="'+ trackCover +'"><div class="plNum">' + trackNumber + '.</div><div class="plTitle"><span class="title">'+ ((albumType !== 'single') ? trackBand.replace(/\\/g, '') +' - ' : '') +' ' + trackName.replace(/\\/g, '') + '</span></div><div class="plLength">' + trackLength + '</div></div></li>');

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
				loadTrack(id);
				$(albumDiv + ' .btnPlay').addClass('hide');
				$(albumDiv + ' .btnPause').removeClass('hide');
				audio.play();
			};
			//extension = audio.canPlayType('audio/mpeg') ? '.mp3' : audio.canPlayType('audio/ogg') ? '.ogg' : '';
			loadTrack(index);  
		}
	});
})(jQuery);