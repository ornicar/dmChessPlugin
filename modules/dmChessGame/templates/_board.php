<?php

$squares = $player->Game->Board->getSquares();

if ($player->isBlack())
{
  $squares = array_reverse($squares, true);
}

$x = $y = 1;

echo _open('div.dm_chess_board');

foreach($squares as $key => $square)
{
  $piece = $square->getPiece();
  
  $squareCss = '#'.$key.'.dm_chess_square.'.$square->getColor();
  
  if($piece && $piece->isType('king') && $piece->isAttacked())
  {
    $squareCss .= '.check';
  }
  
  echo _tag('div'.$squareCss, array('style' => sprintf('top: %dpx;left: %dpx;', 64*(8-$x), 64*($y-1))),
  
    _tag('div.dm_chess_square_inner', '').
    
    ($piece ? _tag('div.dm_chess_piece.'.$piece->get('type').'.'.$piece->get('color').'#p'.$piece->get('id')) : '')
  
  );
  
  if (++$x === 9)
  {
    $x = 1; ++$y;
  }
}

echo _close('div');