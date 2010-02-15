<?php

echo £('div.dm_chess_table.finished',

  £('p.game_over', __('Game over')).
  
  £('div.dm_chess_separator').
  
  (($winner = $player->Game->Winner)
  ? 
    £('div.dm_chess_current_player',
      £('div.player.clearfix',
        £('div.dm_chess_piece.king.fleft.'.$winner->color, '').
        £('p', __('%1% is victorious', array('%1%' => $winner->color)))
      )
    )
  : ___('Draw')
  ).
  
  £('div.dm_chess_separator').
  
  £link($dm_page)->text(__('Start a new game'))->currentSpan(false)

);