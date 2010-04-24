$(function()
{
  if ($game = $('#dm_page div.dm_chess_game').orNot())
  {
    var options = dm_configuration.dm_chess_game;

    if(options.opponent)
    {
      $game.game(options);
    }
    else
    {
      $('a.toggle_join_url').click(function()
      {
        $('div.dm_chess_join_url').toggle(100);
      });
      
      setTimeout(waitForOpponent = function()
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
  }
  else if($waiting = $('div.dm_chess_not_created').orNot())
  {
    $waiting.find('.yescript').show();

    location.href = $.dm.ctrl.getHref('+/dmChessGame/create');
  }
});