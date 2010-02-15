<?php

class dmChessEvent extends sfEvent
{
  
  public function toArray()
  {
    return array(
      'action' => str_replace('dm.chess.', '', $this->getName())
    );
  }
  
}