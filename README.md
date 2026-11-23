# Animegame Browsergame

This repository contains the original source code (GPLV3 licenced) of an old browser game called "Animegame".

Be cautious when running the code on a public webserver as security wise it is far from the minimal standards that should be applied nowadays.

It consists out of 3 parts.

 * ag - the code to be put in the `public_html` directory of your webserver, except the design.
 * cronjob - the code that contains the background tasks (most important one is the viertelstunde.php that should be scheduled each 15m minutes)
 * db - the database schema not including the game data.

Note: That the `include` directory is the same for the `ag` and the `cronjob` directory.

You may use a symlink between these two directories, to avoid copy&paste issues. 
