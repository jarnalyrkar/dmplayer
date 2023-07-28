# Game Master Player
<b>Beta: Feel free to report bugs!</b>

<a href="https://www.youtube.com/watch?v=uuV0xVCfOZY"><img src="https://img.youtube.com/vi/uuV0xVCfOZY/0.jpg" alt="See showcase video on YouTube"></a>
<p>Click image above to see showcase video on YouTube</p>

## What this is for:
If you play a TTRPG for that matter, and would like to play layered tracks of music, ambience and sound effects, then this might be for you!

There are no audio packs or campaign sets included with this software. This is simply a tool for game masters to use at the game table. But maybe you already have audio files on hand, or know how to get it.

If you need complete audio sets and effects tailor-made for offical campaigns <a href="https://syrinscape.com/subscriptions/3-supersyrin/">Syrinscape</a> might be a better option, as they offer a subscription-based service for exactly that.

## How to install:
First, download this repo (code-button -> download zip)<br>
Unzip anywhere you'd like.

PHP is required, so go ahead and install that, see below for instructions.<br>

**To start the program:**<br>
Use gmplayer.bat on Windows<br>

Use gmplayer.sh on Linux and MacOS<br>
You might need to make the script executable by opening the directory in a terminal, and run the following command:<br>
`chmod +x gmplayer.sh`

### Install PHP (windows):
- Right-click install-php.bat and select "Run as administrator".
- Start gmplayer.bat

If you'd rather do it manually:
- Download the most recent <a href="https://windows.php.net/download">php</a> zip-file
- Unzip anywhere, but remember the directory
- <a href="https://www.computerhope.com/issues/ch000549.htm">Add that directory to PATH</a>
- Start gmplayer.bat

#### Troubleshooting, suggestions
- You may need to restart the machine, so windows can see the new path-variable we just added
- Right click folder, select properties, and turn off read only

If you have issues with any of these, please create an issue and attach a screenshot of what happens.

### Install PHP (Ubuntu-flavored Linux):
`sudo apt update && sudo apt upgrade`<br>
`sudo apt install software-properties-common`<br>
`sudo add-apt-repository ppa:ondrej/php`<br>
`sudo apt update`<br>
`sudo apt -y install php`<br>
Verify installation:<br>
`php -v`<br>
If you get back a version number, you're good to go.

### Install PHP (MacOS)
I haven't had the time to look into MacOS yet, but as long as you have php, you might be able to use the gmplayer.sh.

## How to use it
There is an info-button you can click inside the app to get more info.

If you want to create your own theme, write something in the first textbox and press the button with a plus icon.

A default preset is automatically added. You can double click on the title to change it to something else, if you'd like. A preset contains all the volume settings you make for the tracks, and which tracks should be playing for a given preset. If you select different presets it will fade out tracks that are no longer playing, and fade the volume sliders into the preset position.

Add a track by following the same procedure, filling out the text box, and clicking the adjacent button with the plus icon. Tracks are a little different, in that they contain the actual music or ambience files.

To add a file, click the button with an eye icon, which will display a popup. Press the "add file to track"-button to select a file. It will get added to the track. If you have multiple files on a track, the program will randomize which one to get, everytime a track plays, or ends.

Effects are essentially just tracks too, but they are not looped. They are automatically assigned to keyboard shortcuts, so you can play them at the touch of a button. The shortcut is displayed to the left of the effect title, so you can see the shortcut at a quick glance in the heat of battle.

Settings are located at the bottom-left of the application. There you can add a background image, or change the UI colors.

## How it works (technical):
The program spins up a local web server, with PHP.
Data is saved to a locally database-file, that's where your all your themes, presets and tracks are saved.
When you add a file to a track, it is automatically copied to the audio folder.

I'd love to write this again in C++ with QT or something, but I simply don't have the time at the moment, so this is it for now.

If you have any suggestions for technical improvements, I'd love to hear it!
