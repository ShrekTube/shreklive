#!/bin/bash

on_die () {
    pkill -KILL -P $$
}

trap 'on_die' TERM echo $(date +[%FT%TZ]) screenshot screenshot_$1.png >> /var/log/rachni/screenshot.log

ffmpeg -i rtmp://localhost/live/$1 -f image2 -vframes 1 -y /var/www/html/img/channel/channel_$1.png
ffmpeg -i rtmp://localhost/live/$1 -f image2 -vframes 1 -s 540x304 -y /var/www/html/img/thumbs/thumb_$1.png

sleep 30s
