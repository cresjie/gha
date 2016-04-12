(function(){
	var app = angular.module('App',['ngRoute','GHAFramework']);

	app.controller('TabController', function($scope){
		$scope.isCurrentTab = function(tab){
			return window.location.href.split('#')[1] == tab;
		};
	});
	app.controller('BasicInfoController', function($scope, $http, $rootScope, BSBoxService, ErrorService){
		angular.element('.content-spinner').fadeIn();
		$http.get(url._api+'/user/me')
			.success(function(res){
				if( res.success){
					$scope.user = res.data;
					GlobalStorage.store('user', res.data);
					$rootScope.$broadcast('slug.set', $scope.user.slug);
				}
			})
			.error(function(){})
			.finally(function(){
				angular.element('.content-spinner').fadeOut();
			});

		$rootScope.$on('slug.change',function(e, val){
			$http.post(url._api+'/slug/user',{q:val,except_me: 1})
				.success(function(res){
					$scope.user.slug = res.slug;
					$rootScope.$broadcast('slug.set',res.slug);
				})
				.error(function(res){console.log(res)});
		});

		$scope.update = function(){
			angular.element('.content-spinner').fadeIn();
			angular.element('#error-input-box').slideUp();
			$scope.errors = {};
			$http.put(url._api+'/user/me', $scope.user)
				.success(function(res){
					if(res.success){
						angular.extend($scope.user, res.data);
						GlobalStorage.store('user',res.data);
						BSBoxService.notif.check('Updated');
					}else{
						$scope.errors = res.error_msg;
						ErrorService.locateField();
						angular.element('#error-input-box').slideDown();
					}
				})
				.error(function(res){console.log(res)})
				.finally(function(){
					angular.element('.content-spinner').fadeOut();
				});
		}

		$scope.cancel = function(){
			window.location.href = url.home+ '/'+GlobalStorage.get('user').slug;
		}
		$scope.check = function(){
			console.log($scope)
		}
		
	});



	app.controller('PasswordController', function($scope, $http, BSBoxService, ErrorService){

		$scope.update = function(){
			angular.element('.content-spinner').fadeIn();
			angular.element('#error-input-box').slideUp();
			$http.put(url._api+'/password/change',$scope.password)
				.success(function(res){
					if(res.success){
						$scope.password ={};
						BSBoxService.notif.check('Password changed.');

					}else{
						if(res.error_msg.constructor == Object){
							$scope.errors = res.error_msg;
							ErrorService.locateField();
							angular.element('#error-input-box').slideDown();
						}else{
							bsbox.dialog({type:'danger', message: res.error_msg, title: 'Invalid password'})
						}
						
					}
				})
				.error(function(res){
					console.log(res)
				}).finally(function(){
					angular.element('.content-spinner').fadeOut();
				});
		}
		$scope.cancel = function(){
			window.location.href = url.user;
		}
	});

	app.config(function($routeProvider){
		$routeProvider.when('/',{controller: 'BasicInfoController', templateUrl: url.home+'/angular-templates/settings/basic-info.html'})
						.when('/password-setting',{controller: 'PasswordController', templateUrl: url.home+'/angular-templates/settings/password.html'})
						.otherwise({redirectTo:'#/'});
	});
	angular.element(document).ready(function(){
		angular.bootstrap(document, ['App']);
	});
})();
