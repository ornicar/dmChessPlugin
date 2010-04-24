<?php

echo _tag('div.dm_chess_table',

  _tag('div.dm_chess_oponnent', $player->Opponent->isAi
    ? __('Opponent is Crafty A.I.').' '.
      $player->Opponent->getLevelSelect()->render('dm_chess_level_select', $player->Opponent->aiLevel)
    : __('Human opponent')
  ).
  
  _tag('div.dm_chess_separator').
  
  _tag('div.dm_chess_current_player',
    _tag('div.player.white',
      _tag('div.dm_chess_piece.king.white', '').
      _tag('p', __($player->isWhite() ? 'Your turn' : 'Waiting for opponent'))
    ).
    _tag('div.player.black',
      _tag('div.dm_chess_piece.king.black', '').
      _tag('p', __($player->isBlack() ? 'Your turn' : 'Waiting for opponent'))
    )
  ).
  
  _tag('div.dm_chess_table_controls.clearfix',

    _tag('a.dm_chess_permalink_toggle',
      __('Save')
    ).

    _link('+/dmChessGame/resign')
    ->param('player', $player->code)
    ->text(__('Resign'))
    ->title(__('Resign this game'))
    ->set('.dm_chess_give_up')
  ).

  _tag('div.dm_chess_permalink.none',
    __('To continue later, use this url:').
    _tag('span', $sf_request->getUri())
  )

);