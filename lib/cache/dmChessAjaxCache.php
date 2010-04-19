<?php

class dmChessAjaxCache
{
  protected $dispatcher;
  protected $dir;

  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  public function connect()
  {
    $this->dispatcher->connect('dm.chess.player_set_events', array($this, 'listenToPlayerSetEventsEvent'));

    $this->dispatcher->connect('dm.chess.player_clear_events', array($this, 'listenToPlayerClearEventsEvent'));
  }

  public function listenToPlayerSetEventsEvent(dmChessEvent $event)
  {
    if(!$event->getSubject()->isAi)
    {
      touch($this->getPlayerFile($event->getSubject()));
    }
  }

  public function listenToPlayerClearEventsEvent(dmChessEvent $event)
  {
    if(!$event->getSubject()->isAi)
    {
      unlink($this->getPlayerFile($event->getSubject()));
    }
  }

  public function getPlayerFile(DmChessPlayer $player)
  {
    return $this->getDir().'/'.$player->get('code');
  }

  public function getDir()
  {
    $dir = sfConfig::get('sf_web_dir').'/cache/chess';

    if(!is_dir($dir))
    {
      mkdir($dir);
    }

    return $dir;
  }
}