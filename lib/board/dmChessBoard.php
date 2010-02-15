<?php

class dmChessBoard extends dmMicroCache
{
  protected
  $game,
  $squares,
  $compiled;
  
  public function __construct(DmChessGame $game, array $options)
  {
    $this->game = $game;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->createSquares($options['square_class']);
  }
  
  public function compile()
  {
    $this->clearCache();
    $this->compilePieceByKey();
    $this->compiled = true;
  }
  
  protected function compilePieceByKey()
  {
    $pieceByKey = array();
    
    foreach($this->getSquareKeys() as $squareKey)
    {
      $pieceByKey[$squareKey] = null;
    }
    
    foreach($this->getPieces() as $piece)
    {
      $pieceByKey[$piece->getSquareKey()] = $piece;
    }
    
    $this->setCache('piece_by_key', $pieceByKey);
  }
  
  public function clearCache($key = null)
  {
    $this->compiled = false;
    return parent::clearCache($key);
  }
  
  public function getGame()
  {
    return $this->game;
  }
  
  public function getPlayers()
  {
    return $this->getGame()->get('Players');
  }
  
  public function getPieces()
  {
    return $this->getGame()->getPieces();
  }
  
  public function getSquares()
  {
    return $this->squares;
  }
  
  public function getSquareKeys()
  {
    return array_keys($this->squares);
  }

  public function getSquareByKey($key)
  {
    return isset($this->squares[$key]) ? $this->squares[$key] : null;
  }
  
  public function getSquareByPos($x, $y)
  {
    return $this->getSquareByKey('s'.$x.$y);
  }
  
  public function getPieceByKey($key)
  {
    if ($this->compiled)
    {
      return $this->cache['piece_by_key'][$key];
    }
    
    foreach($this->getPieces() as $piece)
    {
      if ($key == $piece->getSquareKey())
      {
        return $piece;
      }
    }
    
    return null;
  }
  
  public function getPieceByPos($x, $y)
  {
    return $this->getPieceByKey('s'.$x.$y);
  }
  public function getPieceByHumanPos($humanPos)
  {
    return $this->getPieceByKey($this->humanPosToKey($humanPos));
  }
  
  protected function createSquares($squareClass)
  {
    $this->squares = array();
    
    for($x=1; $x<9; $x++)
    {
      for($y=1; $y<9; $y++)
      {
        $this->squares['s'.$x.$y] = new $squareClass($this, $x, $y);
      }
    }
  }

  public function squaresToKeys(array $squares)
  {
    $keys = array();
    foreach($squares as $square)
    {
      $keys[] = $square->getKey();
    }
    return $keys;
  }

// removes non existing or duplicated squares
  public function cleanSquares(array $squares, $passedKeys = array())
  {
    foreach($squares as $it => $square)
    {
      if($square instanceof dmChessSquare)
      {
        $key = $square->getKey();
      }
      else
      {
        unset($squares[$it]);
        continue;
      }
      
      if(in_array($key, $passedKeys))
      {
        unset($squares[$it]);
      }
      else
      {
        $passedKeys[] = $key;
      }
    }
    
    return array_values($squares);
  }
  
  public function humanPosToKey($pos)
  {
    if(is_array($pos))
    {
      foreach($pos as $i => $p)
      {
        $pos[$i] = $this->humanPosToKey($p);
      }
      return $pos;
    }
    
    $letters = 'abcdefgh';
    return 's'.(1+strpos($letters, $pos{0})).$pos{1};
  }
  
  public function keyToHumanPos($key)
  {
    if(is_array($key))
    {
      foreach($key as $i => $k)
      {
        $key[$i] = $this->keyToHumanPos($k);
      }
      return $key;
    }
    
    $letters = 'abcdefgh';
    return $letters{$key{1}-1}.$key{2};
  }
}