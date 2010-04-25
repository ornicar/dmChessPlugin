<?php

abstract class PluginDmChessPlayer extends BaseDmChessPlayer
{
  
  public function clearCache($cacheKey = null)
  {
    $king = $this->getKing();
    
    parent::clearCache();
    
    $this->setCache('king', $king->clearCache());
    
    return $this;
  }
  
  public function movePieceToSquare(DmChessPiece $piece, dmChessSquare $square, $checkMoveIntegrity = true)
  {
    if ($checkMoveIntegrity && ($piece->get('Player') !== $this || !$piece->canMoveToSquare($square)))
    {
      return false;
    }
    
    $eventLog = $this->getServiceContainer()->getService('dm_chess_event_log')->connect();
    
    $oldSquare = $piece->getSquare();

    $piece->preMove($oldSquare, $square);

    // kill someone
    if ($opponentPiece = $square->getPiece())
    {
      $opponentPiece->kill();
    }

    $piece->set('x', $square->getX());
    $piece->set('y', $square->getY());

    if (!$piece->hasMoved())
    {
      $piece->set('first_move', $this->get('Game')->get('turns'));
    }
    
    $this->getEventDispatcher()->notify(new dmChessPieceMoveEvent($piece, 'dm.chess.piece_move', array('from' => $oldSquare, 'to' => $square)));
    
    if($opponentPiece)
    {
      $this->getEventDispatcher()->notify(new dmChessPieceKillEvent($piece, 'dm.chess.piece_kill', array('killed' => $opponentPiece, 'square' => $square)));
    }

    $piece->postMove($oldSquare, $square);
    
    $this->get('Game')->clearCache()->getBoard()->compile();
    
    $opponent = $this->getOpponent();
    
    if ($opponent->getKing()->isAttacked())
    {
      $this->getEventDispatcher()->notify(new dmChessCheckEvent($this, 'dm.chess.check', array('king' => $opponent->getKing())));
      
      if ($opponent->isMate())
      {
        $this->getEventDispatcher()->notify(new dmChessMateEvent($this, 'dm.chess.mate', array('king' => $opponent->getKing())));
      }
    }

    $this->get('Game')->addTurn()->save();
    $this->setEvents($eventLog->toArray())->save();
    
    $eventLog->clear();

    return true;
  }
  
  public function getControlledKeys()
  {
    if ($this->hasCache('controlled_keys'))
    {
      return $this->getCache('controlled_keys');
    }
    
    $controlledKeys = array();
    foreach($this->getTargetKeysByPieces(false, true) as $keys)
    {
      $controlledKeys = array_merge($controlledKeys, $keys);
    }
    
    return $this->setCache('controlled_keys', array_unique($controlledKeys));
  }

  public function getTargetKeysByPieces($protectKing = true, $exceptKing = false)
  {
    $targets = array();
    
    $pieces = $this->getTable()->getPieceFilter()->filterAlive($this->get('Pieces'));
    
    if ($exceptKing)
    {
      $pieces = $this->getTable()->getPieceFilter()->filterNotType($pieces, 'king');
    }
    
    foreach($pieces as $piece)
    {
      $targets[$piece->get('id')] = $piece->getTargetKeys($protectKing);
    }
    
    return $targets;
  }
  
  public function isMate($andSave = true)
  {
    if(!$this->getKing()->isAttacked())
    {
      return false;
    }
    
    $isMate = true;
    foreach($this->getTargetKeysByPieces() as $pieceId => $targetKeys)
    {
      if(!empty($targetKeys))
      {
        $isMate = false;
        break;
      }
    }
    
    if($isMate && $andSave)
    {
      $this->Game->isFinished = true;
      $this->Opponent->isWinner = true;
      $this->Opponent->save();
    }
    
    return $isMate;
  }
  
  public function getPieceById($id)
  {
    foreach($this->get('Pieces') as $piece)
    {
      if($id == $piece->get('id'))
      {
        return $piece;
      }
    }
  }
  
  public function isWhite()
  {
    return 'white' === $this->color;
  }
  
  public function isBlack()
  {
    return 'black' === $this->color;
  }
  
  public function __toString()
  {
    $string = $this->get('color').' '.($this->get('is_ai') ? 'A.I.' : 'Human');
    
    return $string;
  }

  public function getOpponent()
  {
    return $this->get('Game')->getPlayerByColor($this->isWhite() ? 'black' : 'white');
  }
  
  public function isMyTurn()
  {
    return $this->get('Game')->get('turns') %2 ? $this->isBlack() : $this->isWhite();
  }
  
  public function is(DmChessPlayer $player)
  {
    return $this->get('code') === $player->get('code');
  }


  public function setEvents($events)
  {
    $this->_set('events', json_encode($events), false);

    $this->getEventDispatcher()->notify(new dmChessEvent($this, 'dm.chess.player_set_events'));
    
    return $this;
  }
  
  public function getEvents()
  {
    return json_decode($this->_get('events'), true);
  }
  
  public function getStringEvents()
  {
    foreach((array) $this->getEvents() as $event)
    {
      if ('piece_move' === $event['action'])
      {
        return $this->getBoard()->getSquareByKey($event['from'])->getHumanPos().' '.$this->getBoard()->getSquareByKey($event['to'])->getHumanPos();
      }
    }
  }
  
  public function clearEvents()
  {
    $this->_set('events', null, false);

    $this->getEventDispatcher()->notify(new dmChessEvent($this, 'dm.chess.player_clear_events'));
    
    return $this;
  }
  
  public function getPawns()
  {
    return $this->getPiecesByType('pawn');
  }
  
  public function getBishops()
  {
    return $this->getPiecesByType('bishop');
  }
  
  public function getKnights()
  {
    return $this->getPiecesByType('knight');
  }
  
  public function getRooks()
  {
    return $this->getPiecesByType('rook');
  }
  
  public function getQueens()
  {
    return $this->getPiecesByType('queen');
  }
  
  public function getKing()
  {
    if ($this->hasCache('king'))
    {
      return $this->getCache('king');
    }
    
    foreach($this->get('Pieces') as $piece)
    {
      if ($piece instanceof DmChessKing)
      {
        return $this->setCache('king', $piece);
      }
    }
    
    throw new dmException('I have no king. This cannot be.');
  }

  public function getPiecesByType($type)
  {
    return $this->getTable()->getPieceFilter()->filterType($this->get('Pieces'), $type);
  }
  
  public function getDeadPieces()
  {
    $pieces = array();
    
    foreach($this->get('Pieces') as $piece)
    {
      if ($piece->get('is_dead'))
      {
        $pieces[] = $piece;
      }
    }
    
    return $pieces;
  }
  
  public function getBoard()
  {
    return $this->get('Game')->getBoard();
  }
  
  
  public function resign()
  {
    $eventLog = $this->getServiceContainer()->getService('dm_chess_event_log')->connect();
    
    $this->Game->isFinished = true;
    $this->Opponent->isWinner = true;
    
    $this->getEventDispatcher()->notify(new dmChessResignEvent($this, 'dm.chess.resign', array()));
    
    $this->Game->save();
    
    $this->setEvents($eventLog->toArray())->save();
  }
  
  public function getLevelSelect()
  {
    if($this->isAi)
    {
      $choices = array();
      for($i=1; $i<=8; $i++)
      {
        $choices[$i] = 'Level '.$i;
      }
      return new sfWidgetFormSelect(array('choices' => $choices));
    }
  }
  
  public function preInsert($event)
  {
    parent::preInsert($event);
    
    $this->code = dmString::random(8);
    
    foreach(explode(' ', 'rook knight bishop queen king bishop knight rook') as $x => $piece)
    {
      $this->createPiece('pawn', $x+1);
      $this->createPiece($piece, $x+1);
    }
    
    if($this->isAi)
    {
      $this->aiLevel = $this->getDefaultAiLevel();
    }
  }
  
  protected function getDefaultAiLevel()
  {
    return 1;
  }
  
  protected function createPiece($type, $x)
  {
    $this->get('Pieces')->add(dmDb::table('DmChess'.ucfirst($type))->create()->set('x', $x)->set('Player', $this));
  }

  public function exchangePosition()
  {
    if($this->Opponent)
    {
      throw new dmChessException('Can not exchange position');
    }

    $this->color = $this->isWhite() ? 'black' : 'white';

    foreach($this->Pieces as $piece)
    {
      $piece->set('y', 9 - $piece->get('y'));
    }

    $this->Pieces->save();

    $this->save();
  }
}