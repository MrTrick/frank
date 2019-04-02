# FRANK
Frank is a text-based adventure/puzzle game, inspired by the classic genre.  
There is a twist, of course - your interface is a computer shell. 

Frank can be played at:
http://mindbleach.com/frank

# Design
The application is a single-page app served by `index.html`, accessing a JSON API at `frank/`.  
Each of the player's commands is sent to the API, and the output is returned and rendered.

Because PHP executes for each new request, the player's state is serialised to disk between requests.
At the human-scale speed of interaction, there have not been any performance issues encounted.

The world the player inhabits is simulated by a persistant and functioning network of PHP objects, built when the user starts their game: 

- Computers
- Network Interfaces
- Networks
- Tools and Services
- Sessions

This general-purpose approach allows many unexpected player interactions to be handled correctly, rather than attempting to predict what a player might do.

# Open Source vs Spoilers
The game is fully open-source.  
We recommend finishing the game before reviewing the code.  
If you wish to avoid spoilers, avoid viewing the `data/` folder or `GameTest.php`.
