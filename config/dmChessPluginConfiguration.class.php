<?php

class dmChessPluginConfiguration extends sfPluginConfiguration
{
  protected
  $eventLog;
  
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('dm.context.loaded', array($this, 'listenToContextLoadedEvent'));
  }
  
  public function listenToContextLoadedEvent(sfEvent $e)
  {
    $this->eventLog = $e->getSubject()->get('event_log');
    
    $this->eventLog->setOption('ignore_models', array_merge($this->eventLog->getOption('ignore_models'), array(
      'DmChessGame',
      'DmChessPlayer'
    )));
    
    $this->dispatcher->connect('dm.chess.piece_move', array($this, 'listenToPieceMoveEvent'));
    
    $this->dispatcher->connect('dm.chess_server.play', array($this, 'listenToServerPlayEvent'));
  }
  
  public function listenToPieceMoveEvent(dmChessPieceMoveEvent $event)
  {
    $piece  = $event->getSubject();
    $player = $piece->get('Player');
    $game   = $player->get('Game');
    
    $this->eventLog->log(array(
      'server'  => $_SERVER,
      'action'  => 'chess',
      'type'    => 'chess move',
      'subject' => sprintf('(%d.%d) %s %s %s %s %s',
        $game->get('id'),
        $game->get('turns'),
        $player->get('color'),
        $player->get('is_ai') ? 'A.I. level '.$player->get('ai_level') : 'Human',
        ucfirst($piece->get('type')),
        ucfirst($event['from']->getHumanPos()),
        ucfirst($event['to']->getHumanPos())
      )
    ));
  }
  
  public function listenToServerPlayEvent(sfEvent $event)
  {
    $this->eventLog->log(array(
      'server'  => $_SERVER,
      'action'  => 'chess',
      'type'    => 'chess web service',
      'subject' => sprintf('A.I. level %s in %.2f s', $event['level'], $event['time'])
    ));
  }

}