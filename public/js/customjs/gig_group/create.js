var templates = {
	searchUser:function(o){
				var t = '<div class="row suggest-organizer" style="margin:0;padding:5px">'+
								'<div class="col-xs-3"  style="background:viole;text-align:right">'+
									'<img class="img-responsive" src="'+o.profile_img_info.sizes['w50']+'">'+
								'</div>'+
								'<div class="col-xs-9" style="margin-left:-15px">'+
									'<div><strong>'+o.first_name + ' '+o.last_name+'</strong></div>'+
									//'<span>'+o.email+'</span>'+
								'</div>'+
							'</div>';
				return t;
			}
	};




var app = angular.module('App',['GHAFramework']);

app.controller('GigGroupController', function($scope, $rootScope, $http, ErrorService){
	var selectedUser = null;
	$scope.groupMembers = [];

	if( !$scope.group )
		$scope.group = {};

	$scope.group.privacy = 'public';
	
	$scope.setSlug = function(e, val){
		
		$http.post(url.api.slug + '/gig-group', {q: val})
			.success(function(res){
				if( !$scope.group )
					$scope.group = {};

				$scope.group.slug = res.slug;
				$rootScope.$emit('slug.set', res.slug);

			})
			.error(function(res){console.log(res)});
	}

	$rootScope.$on('slug.change', $scope.setSlug);


	$scope.addMember = function(){
		if(selectedUser){
			$scope.groupMembers.push(selectedUser);
			selectedUser = null;
			angular.element('#member-input').val('')
		}
	}

	$scope.removeMember = function(member){
		var i = $scope.groupMembers.indexOf(member);
		$scope.groupMembers.splice(i, 1);
	}

	$scope.submit = function(){
		/*
		$http.post(url.api.gig_group).success(function(res){console.log(res)}).error(function(res){console.log(res)});
		*/
		/*
		var ids = [];
		for( var i in $scope.groupMembers ){
			ids.push( $scope.groupMembers[i].id );
		}
		$http.post(url.api.gig_group_members, {id: ids}).success(function(res){console.log(res)}).error(function(res){console.log(res)});
		console.log(ids);
		*/
		$http.post(url.api.gig_group, $scope.group)
			.success(function(res){
				console.log(res);
				if( res.success){
					angular.extend($scope.group, res.data);
					$scope.$emit('group.created', res);
				}else {
					
					if( res.error_msg.constructor == Object ){
						$scope.error_msg = res.error_msg;
						ErrorService.locateField();
					}
				}
			})
			.error(function(res){console.log(res)});
	}

	$scope.$on('group.created', function(e, groupResult){
		var ids = [];
		for( var i in $scope.groupMembers ){
			ids.push( $scope.groupMembers[i].id );
		}

		if(ids.length ){
			$http.post(url.api.gig_group_members,{group_id: groupResult.data.id, user_ids: ids})
				.success(function(res){
					console.log(res);
					if(res.success){
						window.location.replace(groupResult.redirect);

					}else{
						
					}
				})
				.error(function(res){console.log(res)});
			}else{
				window.location.replace(groupResult.redirect);
			}
		
	});

	$scope.check = function(){
		console.log($scope);
	}

	angular.element('#member-input').typeahead(null,{
		templates:{
			empty:'<p>No Result found</p>',
			suggestion:templates.searchUser
		},
		displayKey:function(o){return o.first_name +' '+o.last_name;},
		source:function(q,cb){
			var notInId = [];
			for(var i in $scope.groupMembers){
				notInId.push($scope.groupMembers[i].id);
			}
			
			$.ajax({
				url: url.api.search.users,
				method: 'post',
				data:{q:q, not_in_id: notInId, except_me: true},
				success: function(res){
					cb(res);

				},
				error: function(res){console.log(res.responseText)}
			})
		}
	}).on('typeahead:selected', function(e, selected){
		selectedUser = selected;
	});
	
});

angular.element(document).ready(function(){
	angular.bootstrap(document, ['App']);
});

