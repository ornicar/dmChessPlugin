<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$helper->setLimeTest($t = new lime_test());

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();
$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$t->ok(!$player1->isMate(), 'Player 1 is not mate');
$t->ok(!$player2->isMate(), 'Player 2 is not mate');

$helper
->move($game, 'e2', 'e4')
->move($game, 'e7', 'e5')
->move($game, 'd1', 'h5')
->move($game, 'b8', 'c6')
->move($game, 'f1', 'c4')
->move($game, 'g8', 'f6')
->move($game, 'h5', 'f7');

$t->ok($game->isFinished, 'The game is finished');

$t->ok($player1->isWinner, 'Player 1 is winner');
$t->ok(!$player2->isWinner, 'Player 2 is not winner');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $player1->Queens[0]->id,
    'from'    => $board->humanPosToKey('h5'),
    'to'      => $board->humanPosToKey('f7')
  ),
  array(
    'action'  => 'piece_kill',
    'killer'  => $player1->Queens[0]->id,
    'killed'  => $player2->Pawns[5]->id,
    'square'  => $board->humanPosToKey('f7')
  ),
  array(
    'action'  => 'check',
    'king'    => $player2->King->id,
    'square'  => $player2->King->squareKey
  ),
  array(
    'action'  => 'mate'
  )
);

$t->is_deeply($player1->getEvents(), $events, 'The move, check and mate events have been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'h5 f7', 'The string event is '.$stringEvents);