<?php
/**
 */
class PluginDmChessGameTable extends myDoctrineTable
{

  public function broom()
  {
    $this->getBroomQuery()->delete()->execute();
  }

  public function getBroomQuery()
  {
    return $this->createQuery('g')
    // games created three days ago, with less than 3 moves
    ->where('g.created_at < ? AND g.turns < ?', array(date ("Y-m-d H:i:s", strtotime('-3 day')), 3))
    // games with last move 6 months ago, and not finished
    ->orWhere('g.updated_at < ? AND g.is_finished = ?', array(date ("Y-m-d H:i:s", strtotime('-6 month')), false));
  }

  public function preload($id)
  {
    return $this->createQuery('g')
    ->where('g.id = ?', $id)
    ->leftJoin('g.Players players')
    ->leftJoin('players.Pieces pieces')
    ->fetchRecord();
  }

  public function getAdminListQuery(dmDoctrineQuery $query)
  {
    return parent::getAdminListQuery($query)->leftJoin($query->getRootAlias().'.Players');
  }
}