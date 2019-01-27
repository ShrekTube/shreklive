#!/bin/bash

echo $(date +[%FT%TZ]) convert $2.flv >> /var/log/rachni/ffmpeg-converts.log

ffmpeg -i $1/$2.flv -vcodec copy -acodec copy $1/$2.mp4 2>> /var/log/rachni/ffmpeg-converts.log
ffmpeg -i $1/$2.mp4 -updatefirst 1 -f image2 -vcodec mjpeg -vframes 1 -s 853x480 -y /var/www/html/img/video/video_$2.png 2> /var/log/rachni/ffmpeg-screenshots.log

rm -f $1/$2.flv
