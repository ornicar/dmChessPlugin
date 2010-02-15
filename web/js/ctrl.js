$(function()
{
	if ($game = $('#dm_page div.dm_chess_game').orNot())
	{
		var options = dm_configuration.dm_chess_game;

		options.opponent
		? $game.game(options)
	  : setTimeout(waitForOpponent = function()
    {
      $.ajax({
        url:       $.dm.ctrl.getHref('+/dmChessGame/getNbPlayers')+'?game='+options.game.code,
        success:   function(response)
        {
          response == 2 ? location.reload() : setTimeout(waitForOpponent, options.beat.delay);
        }
      });
    }, options.beat.delay);
  }
	else if($waiting = $('div.dm_chess_not_created').orNot())
	{
		$waiting.find('.yescript').show();
		
		location.href = $.dm.ctrl.getHref('+/dmChessGame/create');
	}
});