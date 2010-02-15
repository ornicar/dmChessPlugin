<?php

class dmChessAiDriverWeb extends dmChessAiDriver
{
  protected
  $serviceContainer;
  
  public function __construct(DmChessPlayer $player, dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->player           = $player;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  public function move()
  {
    $oldForsythe = $this->serviceContainer->get('dm_chess_forsythe')->gameToForsythe($this->player->Game);
    
    $newForsythe = $this->getNewForsythe($oldForsythe);
    
    $move = $this->serviceContainer->get('dm_chess_forsythe')->diffToMove($this->player->Game, $newForsythe);
    
    if (!$this->player->movePieceToSquare($move['from']->getPiece(), $move['to']))
    {
      throw new dmException('Illegal move: '.$move['from'].'->'.$move['to']);
    }
    
    return true;
  }
  
  protected function getNewForsythe($oldForsythe)
  {
    $b = new sfWebBrowser();
    
    $b->get($this->getOption('url'), array(
      'forsythe'  => $oldForsythe,
      'level'     => $this->getOption('level')
    ));
    
    return $b->getResponseText();
  }
}