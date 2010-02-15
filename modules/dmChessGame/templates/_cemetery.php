<?php

if (!$player)
{
  echo £('div.dm_chess_cemetery.dm_chess_cemetery_'.$position);
  return;
}

echo £o('div.dm_chess_cemetery.dm_chess_cemetery_'.$position.'.'.$player->color);

echo £o('ul');

foreach($player->deadPieces as $piece)
{
  echo £('li', £('div.dm_chess_piece.'.$piece->color.'.'.$piece->type));
}

echo £c('ul');

echo £c('div');