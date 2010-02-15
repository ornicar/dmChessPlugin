(function($)
{
  $.widget("cheek.game", {
  
    _init: function()
    {
      var self = this;
      self.title_timeout = null;
			self.pieceMoving = false
			self.$board = $("div.dm_chess_board", self.element);
      self.$table = $("div.dm_chess_table", self.element);
      
      self.indicateTurn();

      // init squares
			$("div.dm_chess_square", self.$board).each(function()
      {
        $(this).droppable({
          accept: function(draggable)
          {
            return self.isMyTurn() && self.inArray($(this).attr("id"), self.options.targets[draggable.attr("id").substr(1)]);
          },
          drop: function(ev, ui)
          {
            var $piece = ui.draggable, $oldSquare = $piece.parent();
						
            $("div.droppable-active", self.$board).removeClass("droppable-active");
            self.options.targets = null;
            self.movePiece($oldSquare.attr("id"), $(this).attr("id"));
            $.ajax({
              dataType: "json",
              url: $.dm.ctrl.getHref('+/dmChessGame/move'),
              data: {
                player: self.options.player.code,
                piece:  $piece.attr("id").substr(1),
                square: $(this).attr("id")
              },
              success: function(data)
              {
                self.updateFromJson(data);
                if (self.options.opponent.ai && !self.options.game.finished) 
                {
                  self.aiMove();
                }
              }
            });
          },
          activeClass: 'droppable-active',
          hoverClass: 'droppable-hover'
        });
      });
      
			// init pieces
      $("div.dm_chess_piece." + self.options.player.color, self.$board).each(function()
			{
				$(this).draggable({
	        //distance: 10,
	        containment: self.$board,
	        helper: function()
	        {
	          return $('<div>')
	          .attr("class", $(this).attr("class"))
	          .attr("id", "moving_" + $(this).attr("id"))
	          .appendTo(self.$board);
	        },
	        start: function()
	        {
	          self.pieceMoving = true;
	          $(this).addClass("moving").parent().addClass("droppable-active")
	        },
	        stop: function()
	        {
            self.pieceMoving = false;
	          $(this).removeClass("moving").parent().removeClass("droppable-active")
	        }
	      })
	      .hover(function()
	      {
	        if (!self.pieceMoving && self.isMyTurn() && (targets = self.options.targets[$(this).attr('id').substr(1)]) && targets.length) 
	        {
	          $("#" + targets.join(", #")).addClass("droppable-active");
	        }
	      }, function()
	      {
	        if (!self.pieceMoving) 
	        {
	          $("div.droppable-active", self.$board).removeClass("droppable-active");
	        }
	      });
			});
      
      self.restartBeat();
      
      self.$table.find("div.dm_chess_give_up a").click(function()
      {
        if (confirm($(this).attr('title')+' ?')) 
        {
		      $.ajax({
		        dataType: "json",
		        url: $(this).attr("href"),
		        success: function(data)
		        {
		          self.updateFromJson(data);
		        }
		      });
        }
				
        return false;
      });

      self.$table.find("select#dm_chess_level_select").change(function()
      {
        $.ajax({
          url:  $.dm.ctrl.getHref('+/dmChessGame/setAiLevel'),
          data: {
						player: self.options.player.code,
            level:  $(this).val()
          }
        });
      });
    },
    updateFromJson: function(data)
    {
      var self = this;
      $("div.dm_chess_square.check", self.$board).removeClass("check");
      
			self.options.targets = data.targets;
      self.displayEvents(data.events);
      self.indicateTurn();
    },
    isMyTurn: function()
    {
      return this.options.targets != null;
    },
    getTargets: function()
    {
      return this.options.targets;
    },
    indicateTurn: function()
    {
      if (this.options.game.finished) 
      {
        document.title = this.translate('Game over');
			}
			else if (this.isMyTurn()) 
      {
        this.element.addClass("my_turn");
        document.title = this.translate('Your turn');
      }
      else 
      {
        this.element.removeClass("my_turn");
        document.title = this.translate('Waiting for opponent');
      }
			
			if (!this.$table.hasClass('finished')) 
			{
				this.$table.find("div.dm_chess_current_player div.player:visible").fadeOut(500);
				this.$table.find("div.dm_chess_current_player div.player." + (this.isMyTurn() ? this.options.player.color : this.options.opponent.color)).fadeIn(500);
			}
    },
    beat: function()
    {
      var self = this;
      if (self.options.game.finished) 
        return;
      $.ajax({
        url: self.options.beat.url,
        dataType: "json",
        error: function()
        {
          self.restartBeat();
        },
        success: function(data)
        {
					if (data) 
					{
						self.updateFromJson(data);
					}
					self.restartBeat();
        }
      });
    },
    movePiece: function(from, to)
    {
			var $piece = $("div#"+from+" div.dm_chess_piece", this.$board);
			
      if (!$piece.length)
      {
        // already moved
        return;
      }
			
      var self = this;
      $("div.dm_chess_square.moved", self.$board).removeClass("moved");
			var $from = $("div#" + from, self.$board).addClass("moved"), from_offset = $from.offset();
			var $to = $("div#" + to, self.$board).addClass("moved"), to_offset = $to.offset();
      var animation = $piece.hasClass(self.options.player.color) ? 500 : 1000;
      
      $("body").append($piece.css({
        top: from_offset.top,
        left: from_offset.left
      }));
      $piece.animate({
        top: to_offset.top,
        left: to_offset.left
      }, animation, function()
      {
        if ($killed = $to.find("div.dm_chess_piece").orNot()) 
        {
          self.killPiece($killed);
        }
        $to.append($piece.css({
          top: 0,
          left: 0
        }));
      });
    },
    aiMove: function()
    {
      var self = this;
      $.ajax({
        dataType: "json",
        url: $.dm.ctrl.getHref('+/dmChessGame/aiMove')+'?player='+self.options.player.code,
        success: function(data)
        {
          self.updateFromJson(data);
        }
      });
    },
    killPiece: function($piece)
    {
      $piece.draggable("destroy");
      var self = this, $deads = $piece.hasClass("white") ? $("div.dm_chess_cemetery.white ul", self.element) : $("div.dm_chess_cemetery.black ul", self.element), $square = $piece.parent(), square_offset = $square.offset();
      $deads.append($("<li>"));
      var $tomb = $("li:last", $deads), tomb_offset = $tomb.offset();
      self.element.append($piece.css({
        top: square_offset.top,
        left: square_offset.left
      }));
      $piece.css("opacity", 0).animate({
        top: tomb_offset.top,
        left: tomb_offset.left,
        opacity: 0.5
      }, 2000, function()
      {
        $tomb.append($piece.css({
          position: "relative",
          top: 0,
          left: 0
        }));
      });
    },
    displayEvents: function(events)
    {
      var self = this;
      for (var i in events) 
      {
        var event = events[i];
        switch (event.action)
        {
					case "piece_move":
					  self.movePiece(event.from, event.to);
            break;
          case "pawn_promotion":
            $("div#p"+event.old_piece).attr('id', 'p'+event.new_piece);
            var $pawn = $("div#p"+event.new_piece);
            $pawn.fadeOut(500, function()
            {
              $pawn.removeClass("pawn").addClass(event.type).fadeIn(500);
            });
            break;
          case "piece_castle":
            $("div#" + event.rook_to, self.$board).append($("div#p" + event.rook, self.$board));
            break;
          case "pawn_en_passant":
            self.killPiece($("div#p" + event.killed, self.$board));
            break;
          case "check":
            $("div#" + event.square, self.$board).addClass("check");
            break;
          case "mate":
				  case "resign":
					  self.options.game.finished = true;
            document.title = self.translate('Game over');
						$.ajax({
							url: $.dm.ctrl.getHref('+/dmChessGame/getTableFinished')+'?player='+self.options.player.code,
							success: function(html)
							{
								$("div.dm_chess_table").replaceWith(html);
							}
						})
            $("div.ui-draggable").draggable("destroy");
            clearTimeout(self.options.beat.timeout);
            clearTimeout(self.title_timeout);
            self.element.removeClass("my_turn");
        }
      }
    },
    restartBeat: function()
    {
      var self = this;
      if (self.options.opponent.ai) 
      {
				if (!self.isMyTurn() && !self.options.game.finished) 
				{
					self.aiMove();
				}
				
				return;
      }
      if (self.options.beat.timeout) 
      {
        clearTimeout(self.options.beat.timeout);
      }
      self.options.beat.timeout = setTimeout(function()
      {
        self.beat();
      }, self.isMyTurn() ? self.options.beat.delay * 2 : self.options.beat.delay);
    },
		translate: function(message)
		{
			return this.options.i18n[message] || message;
		},
    inArray: function(needle, haystack)
    {
      for (var i in haystack)
      {
        if (haystack[i] == needle) {
          return true;
        }
      }
      return false;
    }
  });
  
  $.extend($.cheek.game, {
    getter: "getBoard getTable"
  });
  
})(jQuery);
