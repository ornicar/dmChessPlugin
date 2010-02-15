<?php

echo £('div.dm_chess_table',

  __('To invite someone to play, give this url:').
    
  £('div.dm_chess_join_url',
    £link($dm_page)->param('g', $player->Game->code)->getAbsoluteHref()
  ).
    
  £('div.dm_chess_separator').
  
  £('div.dm_chess_join_ai',
    £link('+/dmChessGame/inviteAi')->param('player', $player->code)->text(__('Or challenge the Artificial Intelligence'))
  )

);