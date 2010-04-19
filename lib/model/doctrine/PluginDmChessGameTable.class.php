<?php
/**
 */
class PluginDmChessGameTable extends myDoctrineTable
{

  public function broom()
  {
    $this->createQuery('g')
    // games created yesterday, with less than 3 moves
    ->where('g.created_at < ? AND g.turns < ?', array(date ("Y-m-d H:i:s", strtotime('-1 day')), 3))
    // games with last move one month ago, and not finished
    ->orWhere('g.updated_at < ? AND g.is_finished = ?', array(date ("Y-m-d H:i:s", strtotime('-1 month')), false))
    // hop!
    ->delete()->execute();
  }

  public function preload($id)
  {
    return $this->createQuery('g')
    ->where('g.id = ?', $id)
    ->leftJoin('g.Players players')
    ->leftJoin('players.Pieces pieces')
    ->fetchRecord();
  }
}