#!/bin/bash
# start servers
php -S localhost:8008 -q & disown;
php websockets.php -q & disown;

# Open default browser
name="$(uname)";
if [ "$name" = "Darwin" ]; then #macos
  open http://localhost:8008 & disown;
elif [ "$name" = "Linux" ]; then
  xdg-open http://localhost:8008 & disown;
fi

# ping for kill-file
while sleep 1; do
  if test -f "stopserver"; then
    rm ./stopserver
    ps -aux | pgrep -f "php websockets.php -q" | xargs kill;
    ps -aux | pgrep -f "php -S localhost:8008 -q" | xargs kill;
    exit 0;
  fi
done
exit 0;
