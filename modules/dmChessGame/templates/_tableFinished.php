<?php

echo _tag('div.dm_chess_table.finished',

  _tag('p.game_over', __('Game over')).
  
  _tag('div.dm_chess_separator').
  
  (($winner = $player->Game->Winner)
  ? 
    _tag('div.dm_chess_current_player',
      _tag('div.player.clearfix',
        _tag('div.dm_chess_piece.king.fleft.'.$winner->color, '').
        _tag('p', __('%1% is victorious', array('%1%' => $winner->color)))
      )
    )
  : ___('Draw')
  ).
  
  _tag('div.dm_chess_separator').
  
  _link($dm_page)->text(__('Start a new game'))->currentSpan(false)

);