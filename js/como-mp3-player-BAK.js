// Stop video PLayback on modal close
(function($) {
	
	var supportsAudio = !!document.createElement('audio').canPlayType;
	
	$('.playerWrap').each(function() {
		var album = $(this).attr('id');
		var albumDiv = '#' + album;
		var id = $(this).find('txtId').val();
		var songs = $(this).data('songs');
		
		console.log(album);
		
		var albums = $(this).data('songs');
		
		console.log(albums);
		
		var tracks = albums;
		
		console.log(tracks);
		
		if (supportsAudio) {
			console.log('Supports Audio');
			
			var index = 0,
			playing = false,
			mediaPath = '',
			extension = '',
			trackCount = tracks.length,
			buildPlaylist = $.each(tracks, function(key, value) {
				var trackNumber = value.track,
					trackName = value.name,
					trackLength = value.length,
					trackFile = value.file,
					trackAlbumTitle = value.album,
					trackSubtitle = value.subtitle,
					trackCover = value.cover;
				if (trackNumber.toString().length === 1) {
					trackNumber = '0' + trackNumber;
				} else {
					trackNumber = '' + trackNumber;
				}
				var playerID = value.player;
				$(albumDiv + ' #plList').append('<li data-album=\"'+ playerID +'\"><div class=\"plItem\" data-track=\"'+ trackNumber +'\" data-name=\"'+ trackName +'\" data-file=\"'+ trackFile +'\" data-length=\"'+ trackLength +'\" data-album-title=\"'+ trackAlbumTitle +'\" data-subtitle=\"'+ trackSubtitle +'\" data-cover=\"'+ trackCover +'\"><div class=\"plNum\">' + trackNumber + '.</div><div class=\"plTitle\"><div class=\"title\">' + trackName + '</div></div><div class=\"plLength\">' + trackLength + '</div></div></li>');
			}),
			//trackCount = tracks.length,
			npAction = $(albumDiv).find('.npAction'),
			npTitle = $(albumDiv).find('.npTitle'),
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
				index = id;
				audio.src = mediaPath + tracks[id].file + extension;
				npCover.attr('src', tracks[id].cover);
				// Initiate progress bar
				audio.addEventListener('timeupdate', function() {
					var currentTime = audio.currentTime;
					var duration = audio.duration;
					$('.hp_range').stop(true,true).animate({'width':(currentTime +.25)/duration*100+'%'},250,'linear');
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
})(jQuery);