<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();

$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$helper->checkPieceSquareInteraction($game, $t);

$t->ok($player1->Pawns[0]->moveToPos(1, 3), 'Move accepted');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $player1->Pawns[0]->id,
    'from'    => 's12',
    'to'      => 's13'
  )
);

$t->is_deeply($player1->getEvents(), $events, 'The move event has been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'a2 a3', 'The string event is '.$stringEvents);

$player1->clearEvents()->save();

$t->is_deeply($player1->getEvents(), null, 'Player events cleared');

$t->is_deeply($player1->getEvents(), null, 'Player events are empty');

$t->ok($player2->Pawns[0]->moveToPos(1, 5), 'Move accepted');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $player2->Pawns[0]->id,
    'from'    => 's17',
    'to'      => 's15'
  )
);

$t->is_deeply($player2->getEvents(), $events, 'The move event has been recorded');
$t->is($player2->getStringEvents(), $stringEvents = 'a7 a5', 'The string event is '.$stringEvents);