<?php

class dmChessEventLog
{
  protected
  $dispatcher,
  $connected = false,
  $events;
  
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    $this->clear();
  }
  
  public function toArray()
  {
    $events = array();
    
    foreach($this->events as $event)
    {
      $events[] = $event->toArray();
    }
    
    return $events;
  }
  
  public function connect()
  {
    if(!$this->connected)
    {
      foreach($this->getEventNames() as $eventName)
      {
        $this->dispatcher->connect('dm.chess.'.$eventName, array($this, 'listenToDmChessEvent'));
      }
      
      $this->connected = true;
    }
    
    return $this;
  }
  
  protected function getEventNames()
  {
    return array(
      'piece_move',
      'piece_kill',
      'piece_castle',
      'pawn_promotion',
      'pawn_en_passant',
      'check',
      'mate',
      'resign'
    );
  }
  
  public function listenToDmChessEvent(dmChessEvent $event)
  {
    $this->events[] = $event;
  }
  
  public function clear()
  {
    $this->events = array();
  }
}