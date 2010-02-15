<?php

class dmChessForsytheNotation
{
  
  /*
   * Transform a game to standart Forsyth Edwards Notation
   * http://en.wikipedia.org/wiki/Forsyth%E2%80%93Edwards_Notation
   */
  public function gameToForsythe(DmChessGame $game)
  {
    $board = $game->getBoard();
    $emptySquare = 0;
    $forsythe = '';
    
    for($y = 8; $y > 0; $y --)
    {
      for($x = 1; $x < 9; $x ++)
      {
        if ($piece = $board->getPieceByPos($x, $y))
        {
          if ($emptySquare)
          {
            $forsythe .= $emptySquare;
            $emptySquare = 0;
          }
          
          $forsythe .= $this->pieceToForsythe($piece);
        }
        else
        {
          ++$emptySquare;
        }
      }
      if ($emptySquare)
      {
        $forsythe .= $emptySquare;
        $emptySquare = 0;
      }
      $forsythe .= '/';
    }
    
    $forsythe = trim($forsythe, '/');

    // b ou w to indicate turn
    $forsythe .= ' ';
    $forsythe .= $game->turns%2 ? 'b' : 'w';

    // possibles castles
    $forsythe .= ' ';
    $hasCastle = false;
    foreach($game->get('Players') as $player)
    {
      if ($player->getKing()->canCastleKingside())
      {
        $hasCastle = true;
        $forsythe .= $player->isWhite() ? 'K' : 'k';
      }
      if ($player->getKing()->canCastleQueenside())
      {
        $hasCastle = true;
        $forsythe .= $player->isWhite() ? 'Q' : 'q';
      }
    }
    if (!$hasCastle)
    {
      $forsythe .= '-';
    }
    
    return $forsythe;
  }
  
  /*
   * retourne un tableau de clés de cases [old_case_key, new_case_key]
   */
  public function diffToMove(DmChessGame $game, $forsythe)
  {
    $moves = array(
      'from'  => array(),
      'to'    => array()
    );
    
    $x = 1;
    $y = 8;
    
    $board = $game->getBoard();
    $board->compile();
    
    $forsythe = str_replace('/', '', preg_replace('#\s*([\w\d/]+)\s.+#i', '$1', $forsythe));

    for($itForsythe = 0, $forsytheLen = strlen($forsythe); $itForsythe < $forsytheLen; $itForsythe++)
    {
      $letter = $forsythe{$itForsythe};

      if (is_numeric($letter))
      {
        for($x = $x, $max = $x+intval($letter); $x < $max; $x++)
        {
          if (!$board->getSquareByKey('s'.$x.$y)->isEmpty())
          {
            $moves['from'][] = 's'.$x.$y;
          }
        }
      }
      else
      {
        $color = ctype_lower($letter) ? 'black' : 'white';
        
        switch(strtolower($letter))
        {
          case 'p': $type = 'pawn'; break;
          case 'r': $type = 'rook'; break;
          case 'n': $type = 'knight'; break;
          case 'b': $type = 'bishop'; break;
          case 'q': $type = 'queen'; break;
          case 'k': $type = 'king'; break;
        }

        if ($piece = $board->getSquareByKey('s'.$x.$y)->getPiece())
        {
          if($type != $piece->get('type') || $color != $piece->get('color'))
          {
            $moves['to'][] = 's'.$x.$y;
          }
        }
        else
        {
          $moves['to'][] = 's'.$x.$y;
        }

        ++$x;
      }
      
      if($x > 8)
      {
        $x = 1;
        --$y;
      }
    }
    
    if(1 === count($moves['from']))
    {
      $from = $board->getSquareByKey($moves['from'][0]);
      $to   = $board->getSquareByKey($moves['to'][0]);
    }
    // two pieces moved: it's a castle
    else 
    {
      if ($board->getSquareByKey($moves['from'][0])->getPiece()->isType('king'))
      {
        $from = $board->getSquareByKey($moves['from'][0]);
      }
      else
      {
        $from = $board->getSquareByKey($moves['from'][1]);
      }
      
      if (in_array($board->getSquareByKey($moves['to'][0])->getX(), array(3, 7)))
      {
        $to = $board->getSquareByKey($moves['to'][0]);
      }
      else
      {
        $to = $board->getSquareByKey($moves['to'][1]);
      }
    }
    
    return array('from' => $from, 'to' => $to);
  }
  
  protected function pieceToForsythe(DmChessPiece $piece)
  {
    $type = $piece->get('type');
    
    if ('knight' === $type)
    {
      $notation = 'n';
    }
    else
    {
      $notation = $type{0};
    }
    
    if('white' === $piece->getColor())
    {
      $notation = strtoupper($notation);
    }
    
    return $notation;
  }
}