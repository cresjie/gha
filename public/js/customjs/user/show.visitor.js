(function(){
	var app = angular.module('App',[]);

	app.controller('UserInteractController', function($scope,$http){

		$scope.contact = GlobalStorage.get('contact');
		$scope.me = GlobalStorage.get('me');

		$scope.addContact = function(userId){

			
			$http.post(url._api+'/contacts',{user_id: userId})
				.success(function(res){
					if( res.success ){
						$scope.contact = angular.copy(res.data);
						console.log($scope);
					}
				}).error(function(res){
					console.log()
				});
			

		}

		$scope.deleteContact = function(){
			$http.delete(url._api+'/contacts/'+$scope.contact.id)
				.success(function(res){
					if(res.success)
						$scope.contact = angular.copy(null)
				});
		}

		$scope.confirmContact = function(){
			$http.put(url._api+'/contacts/'+$scope.contact.id,{cmd: 'confirm'})
				.success(function(res){
					if(res.success)
						$scope.contact = angular.copy(res.data);
				})
		}
	});
	angular.element(document).ready(function(){
		angular.bootstrap(document, ['App']);
	});
})();