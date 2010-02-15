<?php

class dmChessPieceMoveEvent extends dmChessEvent
{
  
  public function toArray()
  {
    return array_merge(parent::toArray(), array(
      'piece' => $this->getSubject()->get('id'),
      'from'  => $this['from']->getKey(),
      'to'    => $this['to']->getKey()
    ));
  }
}