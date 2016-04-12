(function(window, angular, $){

	var app = angular.module('GHAFramework',[]);

	app.controller('SlugController', function($scope, $rootScope){

		$scope.slugEdit = function(){
			$scope.slug_editing = true;
		}
		$scope.doneEdit = function(){
			$scope.slug_editing = false;
			$rootScope.$broadcast('slug.change', $scope.slug);
		}
		$scope.changeWidth = function(e,p){
			
			var el = e.target;
			el.size = el.value.length > 20 ? el.value.length : 20 ;
		}
		$rootScope.$on('slug.set', function(e, val){
			$scope.slug = val;
		});
	});

	app.directive('ngFocusThis',function($timeout){
		return {
			restrict: 'AC',
			link: function($scope, element, attrs){
				
				$scope.$watch(attrs.ngFocusThis, function(val){
					if(val)
						$timeout(function(){element.focus();}, 0)
				})
			}
		}
	});

	app.service('ErrorService', function($timeout){
		return {
			locateField: function(){
				$timeout(function(){
					if(  angular.element('.has-error').length ){
						var top = angular.element('.has-error').offset().top;
						angular.element('html,body').animate({scrollTop: top-50});
					}
					
				}, 100);
				
			}
		};
	});

	app.directive('ngFor', function($parse, $animate){

		var list = [],i = 0;

		return {
			restrict:'A',
			
		    compile: function( element, attr){
		    	
		    	if(i<3){
		    		$animate.enter(element.clone(),null,element);
		    		i++;
		    	}
		    	
		    	
		    	//$animate.enter(element.clone(),null,element);
		    }

		};
	});

	app.service('BSBoxService',function(){
		return {
			notif:{
				rawMessage: function(icon, msg){
					var _msg = msg.constructor == Object ? msg.message : msg;
					var content = '<table class="inherit-color"><tbody><tr><td><i class="icon-lg fa '+icon+'"></i></td><td><div class="pad-10">'+_msg+'</div></td></tr></tbody></table>';
					if(msg.constructor == Object)
						msg.message = content;
					else
						msg = {message: content};
					return msg;
				},
				loading: function(msg){
					var msg = this.rawMessage('fa-refresh animate-spin',msg);
					msg.sticky = true;
					return bsbox.notif(msg);
				},
				check: function(msg){
					var msg = this.rawMessage('fa-check',msg);
					msg.type = 'info';

					return bsbox.notif(msg);
				},
				error: function(msg){
					var msg = this.rawMessage('fa-minus-circle',msg);
					msg.type = 'danger';
					return bsbox.notif(msg);
				},
				warning: function(msg){
					var msg = this.rawMessage('fa-exclamation-triangle',msg);
					msg.type = 'warning';
					return bsbox.notif(msg);
				}
			}
			

		};
	});

	


	//Global

	//GlobalStorage 	
	(function(){
		var collection = {};

		window.GlobalStorage ={

			store: function(name, data){
				
				collection[name] = data;
				
				return this;
			},
			get: function(name, _default){
				
				return collection[name] ? collection[name] : _default;
			}
		};

		

	})();

	(function(){
		var ScrollListener = function(element,options){
			var _fn = function(){},
				DEFAULTS = {
					reset: true,
					up: _fn,
					down: _fn,
					left: _fn,
					right: _fn,
					upDown: _fn,
					leftRight: _fn
				};
			$.extend(this,{
				element: element,
				$el: $(element),
				settings: $.extend(DEFAULTS, options),
			});
			
			
			this.init();
			return this;
		}

		ScrollListener.prototype.init = function(){
			var $el = this.$el;

			if(this.settings.reset)
				$el.off('scroll',this.scrolling);

			this.$el.on('scroll', this.scrolling);
			
			this.$el.data('ScrollListener',{
				settings: this.settings,
				previous:{
					top: $el.scrollTop(),
					left: $el.scrollLeft()
				}
			});
		}

		ScrollListener.prototype.scrolling = function(e){
			var percent = 0,
				data 	= $(this).data('ScrollListener'),
				settings = data.settings,
			 	previous = data.previous;

			if( previous.top != $(this).scrollTop() ){

				var innerHeight = this.innerHeight ? this.innerHeight : $(this).innerHeight();
				percent = ( $(this).scrollTop()/($(this).outerHeight() - innerHeight)) * 100;
				
	 			if(previous.top < $(this).scrollTop())
	 				settings.down.call(this,e,percent);
	 			else
	 				settings.up.call(this,e,percent);

	 			settings.upDown.call(this,e,percent);
	 			previous.top = $(this).scrollTop();

	 		}else if( previous.left != $(this).scrollLeft() ){
	 			var innerWidth = this.element.innerWidth ? this.element.innerWidth : $(this).innerWidth();
	 			percent = ( $(this).scrollLeft() / ($(this).outerWidth() - innerWidth)) * 100;

	 			if( this.previous.left < this.$el.scrollLeft() )
	 				settings.right.call(this, e,percent);
	 			else
	 				settings.left.call(this, e,percent);

	 			settings.leftRight.call(this, e,percent);
	 			previous.left = $(this).scrollLeft();
	 		}
		}
		
		function Plugin(options){
			return this.each(function(i, el){
				new ScrollListener(el,options);
			});
		}
		$.fn.scrollListener = Plugin;
		$.fn.scrollListener.Constructor = ScrollListener;
		window.ScrollListener = ScrollListener;
		
	})(jQuery);
})(window, angular, jQuery);