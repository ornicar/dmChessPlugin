<?php

class dmChessJavascriptConfig
{
  protected
  $response,
  $helper,
  $i18n,
  $player;
  
  public function __construct(dmWebResponse $response, dmHelper $helper, dmi18n $i18n, DmChessPlayer $player)
  {
    $this->response = $response;
    $this->helper   = $helper;
    $this->i18n     = $i18n;
    $this->player   = $player;
  }
  
  public function execute()
  {
    $this->response->addJavascriptConfig('dm_chess_game', $this->getJavascriptConfig());
  }
  
  protected function getJavascriptConfig()
  {
    return array(
      'player'    => array(
        'color'   => $this->player->color,
        'code'    => $this->player->code
      ),
      'opponent'  => ($opponent = $this->player->Opponent) ? array(
        'color'   => $opponent->color,
        'ai'      => $opponent->isAi
      ) : false,
      'targets'   => ($this->player->isMyTurn() && $this->player->Game->isStarted) ? $this->player->getTargetKeysByPieces() : null,
      'beat'      => array(
        'url'     => $this->helper->£link('+/dmChessGame/whatsUp')->param('player', $this->player->code)->getHref(),
        'delay'   => 2000
      ),
      'game'      => array(
        'code'    => $this->player->Game->code,
        'finished' => $this->player->Game->isFinished
      ),
      'i18n'      => $this->getTranslatedMessages()
    );
  }
  
  protected function getTranslatedMessages()
  {
    $messages = array();
    
    foreach($this->getMessages() as $message)
    {
      $messages[$message] = $this->i18n->__($message);
    }
    
    return $message;
  }
  
  protected function getMessages()
  {
    return array(
      'Game over',
      'Waiting for opponent',
      'Your turn'
    );
  }
}