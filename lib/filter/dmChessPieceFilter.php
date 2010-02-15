<?php

class dmChessPieceFilter
{
  
  // remove dead pieces
  public function filterAlive($pieces)
  {
    if ($pieces instanceof dmDoctrineCollection)
    {
      $pieces = $pieces->getData();
    }
    
    foreach($pieces as $it => $piece)
    {
      if ($piece->get('is_dead'))
      {
        unset($pieces[$it]);
      }
    }
    
    return array_values($pieces);
  }

  // remove alive pieces
  public function filterDead($pieces)
  {
    if ($pieces instanceof dmDoctrineCollection)
    {
      $pieces = $pieces->getData();
    }
    
    foreach($pieces as $it => $piece)
    {
      if (!$piece->get('is_dead'))
      {
        unset($pieces[$it]);
      }
    }
    
    return array_values($pieces);
  }

  // only return bishop, rook and queen
  public function filterProjection($pieces)
  {
    if ($pieces instanceof dmDoctrineCollection)
    {
      $pieces = $pieces->getData();
    }
    
    foreach($pieces as $it => $piece)
    {
      if (!($piece instanceof DmChessBishop || $piece instanceof DmChessRook || $piece instanceof DmChessQueen))
      {
        unset($pieces[$it]);
      }
    }

    return array_values($pieces);
  }

  // only keep asked type
  public function filterType($pieces, $type)
  {
    if ($pieces instanceof dmDoctrineCollection)
    {
      $pieces = $pieces->getData();
    }
    
    $class = 'DmChess'.ucfirst($type);
    
    foreach($pieces as $it => $piece)
    {
      if (!$piece instanceof $class)
      {
        unset($pieces[$it]);
      }
    }
    
    return array_values($pieces);
  }

  // remove asked type
  public function filterNotType($pieces, $type)
  {
    if ($pieces instanceof dmDoctrineCollection)
    {
      $pieces = $pieces->getData();
    }
    
    $class = 'DmChess'.ucfirst($type);
    
    foreach($pieces as $it => $piece)
    {
      if ($piece instanceof $class)
      {
        unset($pieces[$it]);
      }
    }
    
    return array_values($pieces);
  }
}