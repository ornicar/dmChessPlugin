<?php

if (!$player)
{
  echo _tag('div.dm_chess_cemetery.dm_chess_cemetery_'.$position);
  return;
}

echo _open('div.dm_chess_cemetery.dm_chess_cemetery_'.$position.'.'.$player->color);

echo _open('ul');

foreach($player->deadPieces as $piece)
{
  echo _tag('li', _tag('div.dm_chess_piece.'.$piece->color.'.'.$piece->type));
}

echo _close('ul');

echo _close('div');