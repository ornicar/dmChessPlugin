<?php

echo £('div.dm_chess_table',

  £('div.dm_chess_oponnent', $player->Opponent->isAi
    ? __('Opponent is Crafty A.I.').' '.
      $player->Opponent->getLevelSelect()->render('dm_chess_level_select', $player->Opponent->aiLevel)
    : __('Human opponent')
  ).
  
  £('div.dm_chess_separator').
  
  £('div.dm_chess_current_player',
    £('div.player.white',
      £('div.dm_chess_piece.king.white', '').
      £('p', __($player->isWhite() ? 'Your turn' : 'Waiting for opponent'))
    ).
    £('div.player.black',
      £('div.dm_chess_piece.king.black', '').
      £('p', __($player->isBlack() ? 'Your turn' : 'Waiting for opponent'))
    )
  ).
  
  £('div.dm_chess_separator').
  
  £('div.dm_chess_permalink',
    __('To continue later, use this url:').
    £link($sf_request->getUri())->text($sf_request->getUri())->set('mt10')
  ).
  
  £('div.dm_chess_give_up',
    £link('+/dmChessGame/resign')->param('player', $player->code)->text(__('Resign'))->title(__('Resign this game'))
  )

);