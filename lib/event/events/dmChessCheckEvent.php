<?php

class dmChessCheckEvent extends dmChessEvent
{
  public function toArray()
  {
    return array_merge(parent::toArray(), array(
      'king'    => $this['king']->id,
      'square'  => $this['king']->squareKey
    ));
  }
}