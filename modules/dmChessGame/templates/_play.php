<?php

if(isset($justInstalled))
{
  // The widget has just been dropped, page needs to be reloaded
  include_partial('dmChessGame/justInstalled');
  return;
}
elseif(isset($notCreated))
{
  // User just arrived on the page, let javascript redirect him to the game creation
  include_partial('dmChessGame/notCreated');
  return;
}

echo £('div.dm_chess_game.clearfix',

  £('div.dm_chess_board_wrap', get_partial('dmChessGame/board', array('player' => $player))).
  
  £('div.dm_chess_table_wrap',
  
    get_partial('dmChessGame/cemetery', array('player' => $player, 'position' => 'top')).
    
    get_partial('dmChessGame/'.$tablePartial, array('player' => $player)).
    
    get_partial('dmChessGame/cemetery', array('player' => $player->Opponent, 'position' => 'bottom'))
  )
);