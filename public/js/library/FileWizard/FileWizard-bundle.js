(function(window){
	
	window.FileUploader = function(options, header){
		var _options = {
			method: 'get',
			data:{}
		},
			_header = {
				'Cache-Control': 'no-cache',
				'X-Requested-With': 'XMLHttpRequest',
		};

		if( !(this instanceof FileUploader)){
			return new FileUploader(options, header);
		}

		FileUploader.extend(_options, options);
		FileUploader.extend(_header, header);
		var xhr = new XMLHttpRequest();

		if(_options.method.toLowerCase() == 'get'){
			var params = Object.keys(_options.data).map(function(k) {
					    return encodeURIComponent(k) + '=' + encodeURIComponent(_options.data[k])
					}).join('&')
			xhr.open(_options.method, _options.url + '?'+params);
		}else
			xhr.open(_options.method, _options.url);

		for(var key in _header)
			xhr.setRequestHeader(key, _header[key]);

		
		if( !_options.data || ( _options.data && _options.data.constructor != FormData) )
			_options.data = FileUploader.toFormData(_options.data);

		//add events
		
		xhr.onabort = _options.abort;
		xhr.upload.onprogress =  function(e){
			if(_options.progress)
				_options.progress.call(this, (e.loaded/e.total) * 100, e);
		};
		xhr.onload = function(e){

			var response = xhr.responseText;

			if(xhr.getResponseHeader("content-type") && xhr.getResponseHeader("content-type").indexOf("application/json") > -1 ){
				try {
	              response = JSON.parse(response);
	            } catch (_error) {
	              e = _error;
	              response = "Invalid JSON response from server.";
	            }
			}
			if(xhr.status == 200){
				if(_options.success)
					_options.success.call(this,response,e);
			}else{
				if(_options.error)
					_options.error.call(this,response,e)
			}

			if(_options.complete)
				_options.complete.call(this,response,e)
		};
		

		xhr.send(_options.data);

		this.abort = function(){
			xhr.abort();
			return this;
		}
		this.getRequest = function(){
			return xhr;
		}
		return this;
	}

	FileUploader.toFormData = function(key,value){
		var formData = new FormData();
		if(key){
			if(key.constructor == String){
				formData.append(key,value);
			}
			if(key.constructor == Object){
				for(var field in key)
					formData.append(field, key[field]);
			}
		}
		
		return formData;
	}
	
	FileUploader.extend = function(obj,obj2){
		for(var field in obj2){
			obj[field] = obj2[field];
		}
		return obj;
	}

})(window);
(function(){
	
	var counter = 1,
		_noop = function(){};
	function FileWizard(element, options, headers){
		/*
		if( !(this instanceof FileUploader)){
			return new FileUploader(options, header);
		}
		*/
		this.$element = $(element);
		this.settings = $.extend({},FileWizard.DEFAULTS, options);
		this.headers = $.extend({},FileWizard.HEADERS, headers);
		this.files = [];
		this.init();
		return this;
	}

	FileWizard.DEFAULTS = {
		dragover: _noop,
		drop: _noop,
		dragenter: _noop,
		dragleave: _noop,
		rejected: _noop,
		fileAdded: _noop,
		beforeFilesAdded: _noop,
		beforeFileAdded: _noop,
		filesAdded: _noop,

		paramName: 'files',
		url:'',
		method:'POST',

		clickable: true,
		autoSend: false,
		acceptedFiles: 'image/*',
		maxSize: 5,
		multipleFiles: true
		
	};

	FileWizard.HEADERS = {};

	FileWizard.sizeToMB = function(b){
		return b/1024/1024;
	}

	var methods = {
		addData: function(key, value){
			if( !this.settings.data || (this.settings.data && this.settings.data.constructor != FormData) )
				this.settings.data = new FormData();

			
			if(key){
				if(key.constructor == Object){
					for(var i in key)
						this.settings.data.append(i, key[i]);
				}else	
					this.settings.data.append(key, value)
			}
			

			return this;
		},
		getFiles: function(){
			return this.files;
		},
		resetFiles:function(){
			this.files = [];
			return this;
		},
		addFiles: function(files){
			fw = this;
			limit = fw.settings.multipleFiles ? files.length : 1;

			fw.settings.beforeFilesAdded.call(this, files);

			for(var i =0 ; i < limit ; i++ ){

				fw.settings.beforeFileAdded.call(this, files[i]);

				if( FileWizard.sizeToMB(files[i].size) > fw.settings.maxSize   )
					fw.settings.rejected.call(this, files[i],'file_limit', e)
				if( !files[i].type.match(fw.settings.acceptedFiles) )
					fw.settings.rejected.call(this, files[i],'file_type', e)
				else{
					fw.files.push(files[i]);
					fw.settings.fileAdded.call(this, files[i]);
				}
			}
			
			fw.settings.filesAdded.call(this, files);

			return this;
		},
		removeFile: function(i,range){
			range = range ? range : 1;
			this.files.splice(i, range);
			return this;
		},
		setOptions: function(options){
			$.extend(this.settings,options)
			return this;
		},
		send: function(){
			this.addData.apply(this,arguments);	
			
			for(var i in this.files){
				var paramName = this.files.length > 1 ? this.settings.paramName + '[]' : this.settings.paramName;
				this.addData(paramName, this.files[i]);	

			}
			
			this.fileUploader = new FileUploader(this.settings, this.headers);
			return this;
		},
		abort: function(){
			if( this.fileUploader )
				this.fileUploader.abort();
			return this;
		},
		init: function(){
			fw = this;
			this.$element.each(function(i, el){

				$(el).on({
					dragenter: function(e){
						if(e.originalEvent.dataTransfer.types.indexOf('Files') > -1){
							$(this).addClass('filewizard-dragenter');
							fw.settings.dragenter.call(this, e);
						}	
					},
					dragleave: function(e){
						$(this).removeClass('filewizard-dragenter');
						fw.settings.dragleave.call(this, e);
					},
					dragover: function(e){
						e.preventDefault();
						fw.settings.dragover.call(this,e);
					},
					drop: function(_e){
						e = _e.originalEvent;
						e.stopPropagation();
						e.preventDefault();
						$(this).removeClass('filewizard-dragenter');

						if(e.dataTransfer.types.indexOf('Files') > -1){
							files = e.dataTransfer.files;
							fw.settings.drop.call(this,e, files);
							fw.addFiles(files);
						}else
							fw.settings.rejected.call(this,null,'not_file' ,e);
						
					}
				});

			});

			if(fw.settings.clickable)
				this.initForm();

			

			return this;
		}, //end init

		initForm: function(){
			var fw = this;
				fw.input = document.createElement('input');
			 	
			$(fw.input).attr({
				type: 'file',
				class: 'filewizard-input filewizard-input-' + counter
			}).on('change', function(e){
				fw.addFiles(this.files);
			});

			this.$element.on('click', function(e){
				e.preventDefault();
				$(fw.input).trigger('click');
			});
			
			$('body').append(fw.input);
			counter++;
		}
		
	}

	$.extend(FileWizard.prototype, methods);

	// DROPDOWN PLUGIN DEFINITION
  	// ==========================
  	function Plugin(options, headers){
  		
  		var arg = [];
  		if(arguments.length > 1){
  			for(var i in arguments)
  				arg.push(arguments[i]);
  		}
		var $this = $(this),
			data = $this.data('FileWizard');
		if( !data ){
			$this.data('FileWizard',new FileWizard($this, options, headers) );
			return $this;
		}
		else{
			if(data[options]){
				return data[options].apply(data, arg.splice(1,1));
			}
		}
  		
  	}
  	$.fn.FileWizard = Plugin;
  	$.fn.FileWizard.Constructor = FileWizard;
	window.FileWizard =FileWizard;
	
})();
