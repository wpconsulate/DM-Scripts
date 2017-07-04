#!/bin/bash

DOMAIN="WPMU_Network_Publish"
cp "$DOMAIN.pot" lang/$DOMAIN.pot
OPTIONS="-s -j --no-wrap -d $DOMAIN -p lang -o $DOMAIN.pot --omit-header"
xgettext $OPTIONS wpmu_*php
