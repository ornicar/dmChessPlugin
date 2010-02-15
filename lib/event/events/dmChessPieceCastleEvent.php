<?php

class dmChessPieceCastleEvent extends dmChessEvent
{
  
  public function toArray()
  {
    return array_merge(parent::toArray(), array(
      'side'      => $this['side'],
      'king'      => $this['king']->id,
      'king_from' => $this['king_from']->getKey(),
      'king_to'   => $this['king_to']->getKey(),
      'rook'      => $this['rook']->id,
      'rook_from' => $this['rook_from']->getKey(),
      'rook_to'   => $this['rook_to']->getKey()
    ));
  }
}