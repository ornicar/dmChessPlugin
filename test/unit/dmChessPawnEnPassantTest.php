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

$killer = $board->getPieceByHumanPos('e2');
$killed = $board->getPieceByHumanPos('d7');

$helper
->move($game, 'e2', 'e4')
->move($game, 'e4', 'e5')
->move($game, 'd7', 'd5')
->move($game, 'e5', 'd6');

$t->ok(!$board->getPieceByHumanPos('d5'), 'No more pawn on d5');
$t->ok($killed->isDead, 'Killed pawn is dead');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $killer->id,
    'from'    => $board->humanPosToKey('e5'),
    'to'      => $board->humanPosToKey('d6')
  ),
  array(
    'action'    => 'pawn_en_passant',
    'killer'    => $killer->id,
    'killed'    => $killed->id,
    'square'    => $board->humanPosToKey('d5')
  )
);

$t->is_deeply($player1->getEvents(), $events, 'The move and en passant events have been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'e5 d6', 'The string event is '.$stringEvents);