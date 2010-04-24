<?php

echo _tag('div.dm_chess_table.dm_chess_table_not_started',

  _tag('a.button.toggle_join_url', 'Play with a friend').
    
  _tag('div.dm_chess_join_url.none',
    _tag('p', __('To invite someone to play, give this url:')).
    _tag('span', _link($dm_page)->param('g', $player->Game->code)->getAbsoluteHref())
  ).
  
  _tag('div.dm_chess_join_ai',
    _link('+/dmChessGame/inviteAi')
    ->param('player', $player->code)
    ->text(__('Play with the machine'))
    ->set('.button')
  )

);