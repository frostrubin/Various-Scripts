#!/bin/bash

cat > /tmp/MyPlaylist.m3u <<END
#EXTM3U
#EXTINF:-1,FFH
http://streams.ffh.de/radioffh/aac/hqlivestream.m3u

#EXTINF:-1,FFH Top 40
http://streams.ffh.de/ffhchannels/aac/hqtop40.m3u

#EXTINF:-1,FFH Euro Dance
http://streams.ffh.de/ffhchannels/aac/hqeurodance.m3u

#EXTINF:-1,FFH Soundtrack 
 http://streams.ffh.de/ffhchannels/aac/hqsoundtrack.m3u

#EXTINF:-1,SWR3 
http://www.swr3.de/wraps/musik/webradio/aplayer/stream_extern.php?format=mp3e&channel=0

#EXTINF:-1,MDR Info
http://avw.mdr.de/livestreams/mdr_info_live_128.m3u
END

~/Applications/VLC.app/Contents/MacOS/VLC /tmp/MyPlaylist.m3u > /dev/null 2>&1 &



