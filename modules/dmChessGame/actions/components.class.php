<?php
/**
 * Dm chess components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class dmChessGameComponents extends myFrontModuleComponents
{

  public function executePlay(dmWebRequest $request)
  {
    if(!$this->getServiceContainer()->hasParameter('dm_chess.player'))
    {
      $this->justInstalled = true;
    }
    elseif(!$this->player = $this->getServiceContainer()->getParameter('dm_chess.player'))
    {
      $this->notCreated = true;
    }
    else
    {
      $this->getService('dm_chess_javascript_config')->execute();
      
      if($this->player->Game->isFinished)
      {
        $this->tablePartial = 'tableFinished';
      }
      elseif($this->player->Game->isStarted)
      {
        $this->tablePartial = 'table';
      }
      else
      {
        $this->tablePartial = 'tableNotStarted';
      }
    }
  }
}