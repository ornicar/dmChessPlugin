<?php
/**
 * Dm chess actions
 */
class dmChessGameActions extends myFrontModuleActions
{
  /*
   * Move a piece
   */
  public function executeMove(dmWebRequest $request)
  {
    $this->forward404Unless(
      ($player  = $this->compilePlayer(dmDb::table('DmChessPlayer')->findOneByCode($request->getParameter('player')))) &&
      $player->isMyTurn() &&
      ($piece   = $player->getPieceById($request->getParameter('piece'))) &&
      ($square  = $player->Game->Board->getSquareByKey($request->getParameter('square'))) &&
      $piece->moveToSquare($square)
    );

    return $this->renderJson(array(
      'targets'  => null,
      'events'   => $player->getEvents()
    ));
  }
  
  /*
   * Move a piece with an AI
   */
  public function executeAiMove(dmWebRequest $request)
  {
    $this->forward404Unless(
      ($player  = $this->compilePlayer(dmDb::table('DmChessPlayer')->findOneByCode($request->getParameter('player')))) &&
      ($opponent = $player->Opponent) &&
      $opponent->isAi &&
      $opponent->isMyTurn()
    );
    
    $this->getServiceContainer()
    ->setParameter('dm_chess.player', $opponent)
    ->getService('dm_chess_ai')
    ->setOption('level', $opponent->aiLevel)
    ->move();
    
    $opponentEvents = $opponent->getEvents();
    // clear opponent events
    $opponent->clearEvents()->save();
    
    $player->Game->getBoard()->compile();
    
    return $this->renderJson(array(
      'targets'  => $player->getTargetKeysByPieces(),
      'events'   => $opponentEvents
    ));
  }
  
  public function executeSetAiLevel(dmWebRequest $request)
  {
    $this->forward404Unless(
      ($player  = $this->compilePlayer(dmDb::table('DmChessPlayer')->findOneByCode($request->getParameter('player')))) &&
      ($opponent = $player->Opponent) &&
      $opponent->isAi &&
      ($level = (int)$request->getParameter('level', 3)) &&
      $level >= 1 &&
      $level <= 8
    );
    
    $opponent->aiLevel = $level;
    $opponent->save();
    
    return $this->renderText('ok');
  }
  
  public function executeResign(dmWebRequest $request)
  {
    $this->forward404Unless(
      ($player  = $this->compilePlayer(dmDb::table('DmChessPlayer')->findOneByCode($request->getParameter('player'))))
    );
    
    $player->resign();

    return $this->renderJson(array(
      'targets'  => null,
      'events'   => $player->getEvents()
    ));
    
  }
  
  /*
   * Invite AI to join the game
   */
  public function executeInviteAi(dmWebRequest $request)
  {
    $this->forward404Unless(
      ($player  = dmDb::table('DmChessPlayer')->findOneByCode($request->getParameter('player'))) &&
      !$player->Game->isStarted
    );
    
    dmDb::table('DmChessPlayer')->joinGameByCode($player->Game->code, array('is_ai' => true));
    
    return $this->redirect($this->getHelper()->£link($this->getPage())->param('p', $player->code)->getHref());
  }
  
  /*
   * Get information about what's going on in the game
   */
  public function executeWhatsUp(dmWebRequest $request)
  {
    $this->forward404Unless($playerCode = $request->getParameter('player'));
    
    // optimized hard coded query
    $opponentEvents = dmDb::pdo('SELECT p.events
FROM dm_chess_player p
WHERE p.code != ?
AND p.game_id = (SELECT pg.game_id FROM dm_chess_player pg WHERE pg.code = ?)
LIMIT 1', array($playerCode, $playerCode))
    ->fetchColumn();
    
    if(empty($opponentEvents))
    {
      return $this->renderJson(null);
    }
    
    // opponent has moved
    $player = $this->compilePlayer(dmDb::table('DmChessPlayer')->findOneByCode($playerCode));
    
    // clear opponent events
    $player->Opponent->clearEvents()->save();
    
    return $this->renderJson(array(
      'targets'  => $player->isMyTurn() ? $player->getTargetKeysByPieces() : null,
      'events'   => json_decode($opponentEvents)
    ));
  }
  
  /*
   * Get number of players in the game
   */
  public function executeGetNbPlayers(dmWebRequest $request)
  {
    return $this->renderText(dmDb::table('DmChessPlayer')->getNbByGameCode($request->getParameter('game')));
  }

  /*
   * Play a game
   */
  public function executePlayWidget(dmWebRequest $request)
  {
    if ($playerCode = $request->getParameter('p', $request->getParameter('player')))
    {
      $this->forward404Unless($player = dmDb::table('DmChessPlayer')->findOneByCode($playerCode));
    }
    elseif($gameCode = $request->getParameter('g', $request->getParameter('game')))
    {
      $this->forward404Unless($player = dmDb::table('DmChessPlayer')->joinGameByCode($gameCode));
      
      return $this->redirect($this->getHelper()->£link($this->getPage())->param('p', $player->code)->getHref());
    }
    else
    {
      $player = null;
    }
    
    $this->getServiceContainer()->setParameter('dm_chess.player', $this->compilePlayer($player));
    
    $this->getService('dm_chess_asset_loader')->execute();
  }
  
  /*
   * Create a game
   */
  public function executeCreate(dmWebRequest $request)
  {
    // clean old games
    dmDb::table('DmChessGame')->broom();

    return $this->redirect($this->getHelper()->£link($this->getPage())->param('p', dmDb::table('DmChessPlayer')->startNewGame()->code)->getHref());
  }
  
  public function executeGetTableFinished(dmWebRequest $request)
  {
    $this->forward404Unless($this->player = dmDb::table('DmChessPlayer')->findOneByCode($request->getParameter('player')));
    
    $this->forward404Unless($this->player->Game->isFinished);
    
    return $this->renderPartial('dmChessGame/tableFinished');
  }
  
  protected function compilePlayer(DmChessPlayer $player = null)
  {
    if($player)
    {
      $player->set('Game', dmDb::table('DmChessGame')->preload($player->get('game_id')));
      $player->get('Game')->getBoard()->compile();
      return $player;
    }
  }

}