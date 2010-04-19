<?php

class dmChessAjaxCache
{
  protected $dispatcher;
  protected $dir;

  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher     = $dispatcher;
    
    $this->dir = sfConfig::get('sf_web_dir').'/cache/chess';

    if(!is_dir($this->dir))
    {
      mkdir($this->dir);
    }
  }

  public function connect()
  {
    $this->dispatcher->connect('dm.chess.player_set_events', array($this, 'listenToPlayerSetEventsEvent'));

    $this->dispatcher->connect('dm.chess.player_clear_events', array($this, 'listenToPlayerClearEventsEvent'));
  
    $this->dispatcher->connect('dm.chess.game_start', array($this, 'listenToGameStartEvent'));
  }

  public function listenToGameStartEvent(dmChessEvent $event)
  {
    foreach($event->getSubject()->Players as $player)
    {
      $this->setPlayerEventCache($player, false);
    }
  }

  public function setPlayerEventCache(DmChessPlayer $player, $value)
  {
    if(!$player->isAi)
    {
      $this->setPlayerCodeEventCache($player->get('code'), $value);
    }
  }

  public function setPlayerCodeEventCache($playerCode, $value)
  {
    file_put_contents($this->getPlayerCodeFile($playerCode), $value ? '1' : '0');
  }

  public function listenToPlayerSetEventsEvent(dmChessEvent $event)
  {
    if($player = $event->getSubject()->getOpponent())
    {
      $this->setPlayerEventCache($player, true);
    }
  }

  public function listenToPlayerClearEventsEvent(dmChessEvent $event)
  {
    if($player = $event->getSubject()->getOpponent())
    {
      $this->setPlayerEventCache($player, false);
    }
  }

  public function getPlayerFile(DmChessPlayer $player)
  {
    return $this->getPlayerCodeFile($player->get('code'));
  }

  public function getPlayerCodeFile($playerCode)
  {
    return $this->dir.'/'.$playerCode.'.txt';
  }
}