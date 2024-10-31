#!/usr/bin/env bash

PROJECT_DIR=~/Workshop/wp_plugin_svn/protect-wp-videos/trunk
rsync -av --exclude=".*" ./ $PROJECT_DIR
cd $PROJECT_DIR
svn add --force .
svn commit -m "bump new version"