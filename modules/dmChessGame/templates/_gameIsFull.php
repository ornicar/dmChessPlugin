<?php

echo _tag('div.dm_chess_game_is_full',

  _tag('h1.title', 'This game has 2 players').

  _media('/dmChessPlugin/images/piece/sprite.png').

  _tag('p', 'You cannot join this chess game, because it is already started!').

  _link($dm_page)->text(__('Start a new game'))->currentSpan(false)->title(false)

);