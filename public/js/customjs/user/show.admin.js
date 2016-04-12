$(function(){
	
	var fileWizard = new FileWizard($('.change-profile-img'),{
		url: url._api+'/upload/profile-image',
		method: 'post',
		paramName: 'img',
		multipleFiles: false,
		fileAdded: function(file){
			
			var fileReader = new FileReader;
			fileReader.addEventListener('load',function(e){
				
				$('#change-profile-dialog .profile-cropper').attr('src',e.target.result)
				$('#change-profile-dialog').modal();
				
			})
			fileReader.readAsDataURL(file);

			
		},
		progress: function(percent){
			
			$('#change-profile-dialog .progress-bar').width(percent+'%');
			if(percent == 100)
				$('.profile-content-spinner').show();
		},
		success: function(res){
			
			window.location.reload();
		},
		error: function(res){
			console.log(res);
			bsbox.alert({type:'danger',message:'Error Occured <br><b>Status</b>:' + this.status+' '+ this.statusText});
		},
		complete: function(){
			$('#upload-profile').prop('disabled', false);
			$('#change-profile-dialog .profile-cropper').cropper('enable');
		}
	});
	$('#change-profile-dialog .profile-cropper').on('built.cropper', function(){
		
		$('.profile-content-spinner').hide();
	});

	$('#change-profile-dialog').on('shown.bs.modal', function(){
		
		$('body').addClass('overflow-hidden');
		
		$('#change-profile-dialog .profile-cropper').cropper({
			aspectRatio:1,
			viewMode: 1,
			rotatable: false,
			zoomable: false,
			preview: '.cropper-preview'
		});

	});

	$('#change-profile-dialog').on('hide.bs.modal', function(){
		fileWizard.resetFiles();
		fileWizard.input.value = null;
		$('#change-profile-dialog .profile-cropper').cropper('destroy');
		$('body').removeClass('overflow-hidden');
		$('.profile-content-spinner').show();
	});
	$('#upload-profile').on('click', function(){
		$(this).prop('disabled', true);
		$('#change-profile-dialog .profile-cropper').cropper('disable');
		$('#change-profile-dialog .progress-bar').width(0);
		$('#change-profile-dialog .progress').show();
		fileWizard.send( $('#change-profile-dialog .profile-cropper').cropper('getData') );
		
	});


});

(function(){

	var app = angular.module('App',['ngRoute','GHAFramework']);

	app.controller('TabController', function($scope){
		$scope.isCurrentTab = function(tab){
			return window.location.href.split('#')[1] == tab;
		};
	});

	app.controller('CurrentEventsController', function($scope, $http){

	})

		.controller('ProgressiveEventsController', function($scope, $http){

	})

		.controller('PreviousEventsController', function($scope, $http){

	})
		.controller('CalendarEventsController', function($scope, $http){

	})

		.controller('ContactController', function($scope, $http){
			var loadingContacts = false;
			$scope.firstLoadContacts = false;
			angular.element('.tab-content-spinner').show();

			
			$http.get(url._api+'/contacts')
				.success(function(res){
					console.log(res)
					if(res.data){
						$scope.response = res;
						$scope.contacts = res.data;
						$scope.firstLoadContacts = true;
					}
				})
				.error(function(res){console.log(res)})
				.finally(function(){angular.element('.tab-content-spinner').hide();});

			$scope.delete = function(contact){
				
				bsbox.confirm('Remove '+contact.first_name+' '+contact.last_name+' from your contact?',function(res){
					if(res){
						$http.delete(url._api+'/contacts/whose?user_id='+contact.id)
							.success(function(res){
								console.log(res);
								if(res.success){
									var i = $scope.contacts.indexOf(contact);
									$scope.contacts.splice(i,1);
								}else{
									bsbox.alert({message:res.error_msg, type:'danger'});
								}
								
								
							})
							.error(function(res){console.log(res)})
						
					}
				});
		}

		angular.element(window).scrollListener({
			down: function(e,percent){
					if( $scope.response && $scope.response.next_page_url && !loadingContacts && percent >= 80){
						loadingContacts = true;
						$http.get($scope.response.next_page_url)
							.success(function(res){
								$scope.response = res;
								for(var i in res.data)
									$scope.contacts.push(res.data[i]);

							})
							.error(function(res){console.log(res)})
							.finally(function(){loadingContacts = false})
					}	
		
			}
		})
	});



	app.config(function($routeProvider){
		$routeProvider.when('/',{controller:'CurrentEventsController'})
						.when('/progressive-events',{controller:'ProgressiveEventsController'})
						.when('/previous-events',{controller:'PreviousEventsController'})
						.when('/calendar-of-events',{controller:'CalendarEventsController'})
						.when('/contacts',{controller: 'ContactController', templateUrl: url.home+'/angular-templates/user/contacts.html'})
						.otherwise({redirectTo:'#/'});
	});


	angular.element(document).ready(function(){
		angular.bootstrap(document, ['App']);

	});

})();