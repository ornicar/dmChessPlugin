<?php

class dmChessPieceKillEvent extends dmChessEvent
{
  
  public function toArray()
  {
    return array_merge(parent::toArray(), array(
      'killer'  => $this->getSubject()->id,
      'killed'  => $this['killed']->id,
      'square'  => $this['square']->getKey()
    ));
  }
  
}