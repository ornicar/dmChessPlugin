<?php

class dmChessPawnPromotionEvent extends dmChessEvent
{

  public function toArray()
  {
    return array_merge(parent::toArray(), array(
      'type'      => $this['type'],
      'old_piece' => $this['old_piece']->id,
      'new_piece' => $this['new_piece']->id,
      'square'    => $this['square']->getKey()
    ));
  }
}