var app = angular.module('App',['ngRoute','GHAFramework']);

app.controller('SidebarController', function($scope, $http){

	$scope.check = function(){
		console.log($scope.clicked);
	}

	$scope.group = angular.copy(Storage.get('gig_group'));

	
});

app.controller('MemberController', function($scope, $http){
	$scope.gig_group = angular.copy( Storage.get('gig_group') );
	$scope.gig_group_members = [];

	$http.get(url.api+'/gig_group_members?group_id='+$scope.gig_group.id)
		.success(function(res){
			if(res.data){
				for(var i in res.data){
					$scope.gig_group_members.push(res.data[i]);
				}
			}
		});
})

app.config(function($routeProvider){
	$routeProvider.when('/',{})
				.when('/members',{
					controller:'MemberController',
					templateUrl: url.home+'/angular-templates/gig_group/members.html'
				});
});

angular.element(document).ready(function(){
	angular.bootstrap(document, ['App']);
});