<?php

echo _tag('div.dm_chess_table.dm_chess_table_not_started',

  __('To invite someone to play, give this url:').
    
  _tag('div.dm_chess_join_url',
    _link($dm_page)->param('g', $player->Game->code)->getAbsoluteHref()
  ).
    
  _tag('div.dm_chess_separator').
  
  _tag('div.dm_chess_join_ai',
    _link('+/dmChessGame/inviteAi')->param('player', $player->code)->text(__('Or challenge the Artificial Intelligence'))
  )

);