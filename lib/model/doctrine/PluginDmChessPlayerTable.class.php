<?php
/**
 */
class PluginDmChessPlayerTable extends myDoctrineTable
{
  protected
  $pieceFilter;
  
  public function getNbByGameCode($code)
  {
    return $this->createQuery('p')
    ->leftJoin('p.Game g')
    ->where('g.code = ?', $code)
    ->count();
  }
  
  public function getPieceFilter()
  {
    if(null === $this->pieceFilter)
    {
      if($sc = $this->getServiceContainer())
      {
        $this->pieceFilter = $sc->getService('dm_chess_piece_filter');
      }
      else
      {
        throw new dmException('Need a service container');
      }
    }
    
    return $this->pieceFilter;
  }

  public function startNewGame(array $options = array())
  {
    return $this->create(array(
      'color' => dmArray::get($options, 'color', 'white'),
      'Game'  => new DmChessGame(),
      'is_creator' => true
    ))->saveGet();
  }

  public function joinGame(DmChessGame $game, array $options = array())
  {
    if(!$game->exists() || $game->Players->count() != 1)
    {
      return null;
    }
    
    $player = $this->create(array_merge($options, array(
      'color' => $game->Players[0]->isWhite() ? 'black' : 'white'
    )))
    ->set('Game', $game)
    ->saveGet();
    
    $game->refreshRelated('Players');
    
    $game->start()->save();
    
    return $player;
  }
  
  public function joinGameByCode($code, array $options = array())
  {
    if($game = dmDb::table('DmChessGame')->findOneByCode($code))
    {
      return $this->joinGame($game, $options);
    }
  }

  public function findOneByCode($code)
  {
    return $this->createQuery('p')->where('p.code = ?', $code)->fetchRecord();
  }
}