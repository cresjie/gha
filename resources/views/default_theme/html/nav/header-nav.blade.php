<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?=URL::route('home')?>">
				<img alt="gighubapp" height="40" src="<?=asset('images/logo/main-logo-horizontal.png')?>">
				
				</a>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
		            <span class="sr-only">Toggle navigation</span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
	         	</button>
			</div>

			
			<div class="collapse navbar-collapse " id="navbar">
				<div class="col-sm-6 col-md-7">
			        <form class="navbar-form" role="search">
			        <div class="input-group">
			            <input type="text" class="form-control" placeholder="Search" name="q">
			            <div class="input-group-btn">
			                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" style="font-size:20px"></i></button>
			            </div>
			        </div>
			        </form>
			    </div>
			    <ul class="nav navbar-nav navbar-right">
			    @if( !Auth::check() )
					<li><a href="<?=URL::to('signup')?>">Sign up</a></li>
			      	<li class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">Login <strong class="caret"></strong></a>
						<ul class="dropdown-menu" style="padding: 15px;min-width: 250px;">
                        <li>
                           <div class="row">
                              <div class="col-md-12">
                                 
                                 	<?=Form::open(['route' => 'login.store','method' => 'POST'])?>
                                 	<?=Form::hidden('_redirect',Request::url())?>
                                    <div class="form-group">
                                       <label class="sr-only">Email address</label>
                                      
                                       <?=Form::email('email',null,['class' => 'form-control','placeholder' => 'Email Address', 'required'])?>
                                    </div>
                                    <div class="form-group">
                                       <label class="sr-only">Password</label>
                                       <?=Form::password('password',['class' => 'form-control','placeholder' => 'Password','required'])?>
                                    </div>
                                    <div class="checkbox">
                                       <label>
                                       <?=Form::checkbox('remember_me')?> Remember me
                                       </label>
                                    </div>
                                    <div class="form-group">
                                       <?=Form::submit('Signin',['class' => 'btn btn-success btn-block'])?>
                                    </div>
                                 <?=Form::close()?>
                              </div>
                           </div>
                        </li>
                        <li class="divider"></li>
                        <li><div>
                           <a href="#" class="btn btn-google btn-block">Signin with Google</a>
                           <a href="#" class="btn btn-fb btn-block" type="button">Signin with Facebook</a>
                           </div>
                        </li>
                     </ul>
					</li>
				
				@else
			    	<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="glyphicon glyphicon-user"></i> <b class="caret"></b></a>
						<ul class="dropdown-menu" style="min-width:350px;">
							<li style="padding:0 15px">
								<div class="row">
									<div class="col-xs-4 user-profile-pic-wrapper">

										<img src="<?=App\Helpers\Upload\Image::getImage(Auth::user()->profile_img,'user_profile_img',200)?>" class="img-responsive">
									</div>
									<div class="col-xs-8">
										<p class="text-left"><strong><?=Auth::user()->first_name?> <?=Auth::user()->last_name?></strong></p>
										<p class="small text-left"><?=Auth::user()->email?></p>
										<a href="<?=URL::to(Auth::user()->slug)?>" class="btn btn-primary btn-block"><i class="glyphicon glyphicon-user"></i> Profile</a>
									</div>
								</div>
							</li>
							<li class="divider"></li>
							<li class="text-center"><a class="fs-15" href="<?=URL::route('settings.index')?>#/"> <i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>
							<li class="text-center"><a class="fs-15" href="<?=URL::route('events.create')?>">Create Event</a></li>

							<li class="divider"></li>
							<li style="padding:0 15px">
								<div><a href="<?=URL::route('logout')?>" class="btn btn-danger btn-block logout"><i class="glyphicon glyphicon-off"></i> Log out</a></div>
							</li>
						</ul>
					</li>
			    @endif
			    </ul>
			  </div><!-- /.navbar-collapse -->
				
		</div>
		
	</nav> <!-- /navbar navbar-default navbar-fixed-top -->
@section('header-nav-offset')
	<div class="navbar-offset-top" style="height:50px"></div>
@show

