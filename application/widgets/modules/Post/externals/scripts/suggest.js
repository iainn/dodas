
/* $Id: composer_link.js 8986 2011-06-15 00:14:28Z john $ */


PostLinkSuggest = new Class({

  Implements : [Events, Options],	
	
  name : 'post_link_suggest',

  options : {
    title : 'Add Link',
    lang : {},
    // Options for the link preview request
    requestOptions : {},
    // Various image filtering options
    imageMaxAspect : ( 15 / 3 ),
    imageMinAspect : ( 3 / 15 ),
    imageMinSize : 48,
    imageMaxSize : 2400,
    imageMinPixels : 9600,
    imageMaxPixels : 3600000,
    imageTimeout : 5000,
    
    loadingImage : 'application/modules/Core/externals/images/loading.gif',
    
    showImageOptOut : true,
    
    // Delay to detect links in input
    monitorDelay : 600,
    debug : false
  },

  params : {},
  
  elements : {},
  
  initialize : function(elements, options) {
    this.params = new Hash(this.params);
    this.elements = new Hash(elements);
    
    this.elements.each(function(value, key, hash){
        hash.set(key, $(value));
    });
    
    //this.elements.formInput = $(element);
    
    this.setOptions(options);
  },

  // Getting into the core stuff now

  doAttach : function() {
    var val = this.elements.formInput.value;
    if( !val ) {
      alert('no value');
      
      return;
    }
    this.elements.formInput.removeClass('required');
    
    if( !val.match(/^[a-zA-Z]{1,5}:\/\//) )
    {
      val = 'http://' + val;
    }
    this.params.set('uri', val)
    // Input is empty, ignore attachment
    if( val == '' ) {
      
      e.stop();
      return;
    }
    

    // Send request to get attachment
    var options = $merge({
      'data' : {
        'format' : 'json',
        'uri' : val
      },
      'onComplete' : this.doProcessResponse.bind(this)
    }, this.options.requestOptions);

    // Inject loading
    //this.makeLoading('empty');

    // Send request
    this.request = new Request.JSON(options);
    this.request.send();
  },

  doSuggest : function() {

	this.doClearError();
	
    var val = this.elements.formInput.value;
    if( !val ) {
      this.elements.formInput.addClass('validation-failed');		
      return;
    }
    this.elements.formInput.removeClass('validation-failed');
    if( !val.match(/^[a-zA-Z]{1,5}:\/\//) )
    {
      val = 'http://' + val;
    }
    this.params.set('uri', val)
    // Input is empty, ignore attachment
    if( val == '' ) {
      e.stop();
      return;
    }	
	
    //this.elements.body = $('photo-element');

    
    this.setFormInputValue('formInput', val);
    
    this.setFormInputValue('photo', '');
    this.setFormInputValue('thumb', '');
    $$('.post-compose-preview-images, .post-compose-preview-info').each(function(el){
    	el.destroy();
    });
    
    this.setUploadFileDisplay(true);
    
    // Send request to get attachment
    var options = $merge({
      'data' : {
        'format' : 'json',
        'uri' : val
      },
      'onComplete' : this.doProcessResponse.bind(this)
    }, this.options.requestOptions);

    // Inject loading
    //this.makeLoading('empty');

    
    this.elements.activator.setStyle('display', 'none');
    this.elements.loader.setStyle('display', '');
    
    // Send request
    this.request = new Request.JSON(options);
    this.request.send();
    
  },
  
  doProcessResponse : function(responseJSON, responseText) {
	  
	    this.elements.activator.setStyle('display', '');
	    this.elements.loader.setStyle('display', 'none');
	    
    // Handle error
    if( $type(responseJSON) != 'object' ) {
      responseJSON = {
        'status' : false
      };
    }
    //this.params.set('uri', responseJSON.url);

    if (responseJSON.error) {
      this.doDisplayError(responseJSON.message);
      if( this.options.debug && responseJSON.exception) {
        console.log('EXCEPTION: ' + responseJSON.exception);
      }
      return;
    }
    
    var images = responseJSON.images || [];

    if (responseJSON.title) {
      this.setFormInputValue('title', responseJSON.title);
    }
        
    if (responseJSON.description) {      
      this.setFormInputValue('description', responseJSON.description);
    }

    /*
    if (responseJSON.keywords) {      
      this.setFormInputValue('keywords', responseJSON.keywords);
    }    
    
    
    if (responseJSON.media) {
      $('media-' + responseJSON.media).set('checked',true);      
    }
    */
    //alert(images.length);
    
    this.params.set('images', images);
    this.params.set('loadedImages', []);
    this.params.set('thumb', '');    
    
    if( images.length > 0 ) {
        this.doLoadImages();
    }
    else {
    	//this.doDisplayError('Could not find images');
    }

    
    /*
    this.params.set('title', title);
    this.params.set('description', description);
    this.params.set('images', images);
    this.params.set('loadedImages', []);
    this.params.set('thumb', '');

    if( images.length > 0 ) {
      this.doLoadImages();
    } else {
      this.doShowPreview();
    }
    */
  },

  doClearError: function() {
	if (this.elements.has('error')) {
	  this.elements.get('error').hide();	
	  this.elements.get('error').set('text', '');	
	}   
  },
  
  doDisplayError : function(message) {
	if (message == "") {
	  message = "An error has occured.";
	}
	
	if (this.elements.has('error')) {
	  this.elements.get('error').show();	
	  this.elements.get('error').set('text', this._lang(message));	
	}  	
	else {
	  alert(this._lang(message));
	}
  },
  
  // Image loading
  
  doLoadImages : function() {
    // Start image load timeout
    var interval = this.doShowPreview.delay(this.options.imageTimeout, this);

    // Load them images
    this.params.loadedImages = [];

    //alert('doLoadImages');
    
    this.params.set('assets', new Asset.images(this.params.get('images'), {
      'properties' : {
        'class' : 'post-compose-link-image'
      },
      'onProgress' : function(counter, index) {
        this.params.loadedImages[index] = this.params.images[index];
        // Debugging
        if( this.options.debug ) {
          console.log('Loaded - ', this.params.images[index]);
        }
      }.bind(this),
      'onError' : function(counter, index) {
        delete this.params.images[index];
      }.bind(this),
      'onComplete' : function() {
        $clear(interval);
        this.doShowPreview();
      }.bind(this)
    }));
  },


  // Preview generation
  
  doShowPreview : function() {
    var self = this;
    
    //alert('doShowPreview');
    //this.elements.body.empty();
    //alert(this.elements.body);
    //alert('this.params.loadedImages.length=' + this.params.loadedImages.length);
    // Generate image thingy
    if( this.params.loadedImages.length > 0 ) {
      var tmp = new Array();
      
      this.elements.previewImages = new Element('div', {
        'id' : 'post-compose-link-preview-images',
        'class' : 'post-compose-preview-images'
      }).inject(this.elements.body);
      //alert('my params assets=');
      //alert(this.params.assets);
      this.params.assets.each(function(element, index) {
    	//alert("my index = " + index + " my element = " + element);
        if( !$type(this.params.loadedImages[index]) ) return;
        element.addClass('post-compose-preview-image-invisible').inject(this.elements.previewImages);
        if( !this.checkImageValid(element) ) {
          delete this.params.images[index];
          delete this.params.loadedImages[index];
          element.destroy();
        } else {
          element.removeClass('post-compose-preview-image-invisible').addClass('post-compose-preview-image-hidden');
          tmp.push(this.params.loadedImages[index]);
          element.erase('height');
          element.erase('width');
        }
        //alert('loop this');
      }.bind(this));

      this.params.loadedImages = tmp;

      if( this.params.loadedImages.length <= 0 ) {
        this.elements.previewImages.destroy();
        //this.doDisplayError('Could not find large images');
      }
    }

    // Generate image selector thingy
    if( this.params.loadedImages.length > 0 ) {
    	
      this.elements.previewInfo = new Element('div', {
        'id' : 'post-compose-link-preview-info',
        'class' : 'post-compose-preview-info'
      }).inject(this.elements.body);	
    	
      this.elements.previewOptions = new Element('div', {
        'id' : 'post-compose-link-preview-options',
        'class' : 'post-compose-preview-options'
      }).inject(this.elements.previewInfo);

      if( this.params.loadedImages.length > 1 ) {
        this.elements.previewChoose = new Element('div', {
          'id' : 'post-compose-link-preview-options-choose',
          'class' : 'post-compose-preview-options-choose',
          //'html' : '<span>' + this._lang('Choose Image:') + '</span>'
          'html' : ''
        }).inject(this.elements.previewOptions);

        this.elements.previewPrevious = new Element('a', {
          'id' : 'post-compose-link-preview-options-previous',
          'class' : 'post-compose-preview-options-previous',
          'href' : 'javascript:void(0);',
          'html' : '&#171; ' + this._lang('Last'),
          'events' : {
            'click' : this.doSelectImagePrevious.bind(this)
          }
        }).inject(this.elements.previewChoose);

        this.elements.previewCount = new Element('span', {
          'id' : 'post-compose-link-preview-options-count',
          'class' : 'post-compose-preview-options-count'
        }).inject(this.elements.previewChoose);


        this.elements.previewPrevious = new Element('a', {
          'id' : 'post-compose-link-preview-options-next',
          'class' : 'post-compose-preview-options-next',
          'href' : 'javascript:void(0);',
          'html' : this._lang('Next') + ' &#187;',
          'events' : {
            'click' : this.doSelectImageNext.bind(this)
          }
        }).inject(this.elements.previewChoose);
      }

      if (this.options.showImageOptOut) {
	      this.elements.previewNoImage = new Element('div', {
	        'id' : 'post-compose-link-preview-options-none',
	        'class' : 'post-compose-preview-options-none'
	      }).inject(this.elements.previewOptions);
	
	      this.elements.previewNoImageInput = new Element('input', {
	        'id' : 'post-compose-link-preview-options-none-input',
	        'class' : 'post-compose-preview-options-none-input',
	        'type' : 'checkbox',
	        'events' : {
	          'click' : this.doToggleNoImage.bind(this)
	        }
	      }).inject(this.elements.previewNoImage);
	
	      this.elements.previewNoImageLabel = new Element('label', {
	        'for' : 'post-compose-link-preview-options-none-input',
	        'html' : this._lang('Don\'t use preview image'),
	        'events' : {
	          //'click' : this.doToggleNoImage.bind(this)
	        }
	      }).inject(this.elements.previewNoImage);
      }
      // Show first image
      this.setImageThumb(this.elements.previewImages.getChildren()[0]);
      
      this.setUploadFileDisplay(false);
    }
    else {
      this.setUploadFileDisplay(true);
    }
  },

  setUploadFileDisplay : function(display) {
	//alert(this.elements.get('photo'));
	  
	if (this.elements.has('photo')) {
	  if (!display) {
		this.setFormInputValue('photo', '');		
	  }
	  this.elements.get('photo').getParent().setStyle('display', display ? '' : 'none');	
	}  

  },
  
  checkImageValid : function(element) {
    var size = element.getSize();
    var sizeAlt = {x:element.get('width'),y:element.get('height')};
    var width = sizeAlt.x || size.x;
    var height = sizeAlt.y || size.y;
    var pixels = width * height;
    var aspect = width / height;
    
    // Debugging
    if( this.options.debug ) {
      console.log(element.get('src'), sizeAlt, size, width, height, pixels, aspect);
    }

    // Check aspect
    if( aspect > this.options.imageMaxAspect ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Aspect greater than max - ', element.get('src'), aspect, this.options.imageMaxAspect);
      }
      return false;
    } else if( aspect < this.options.imageMinAspect ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Aspect less than min - ', element.get('src'), aspect, this.options.imageMinAspect);
      }
      return false;
    }
    // Check min size
    if( width < this.options.imageMinSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Width less than min - ', element.get('src'), width, this.options.imageMinSize);
      }
      return false;
    } else if( height < this.options.imageMinSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Height less than min - ', element.get('src'), height, this.options.imageMinSize);
      }
      return false;
    }
    // Check max size
    if( width > this.options.imageMaxSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Width greater than max - ', element.get('src'), width, this.options.imageMaxSize);
      }
      return false;
    } else if( height > this.options.imageMaxSize ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Height greater than max - ', element.get('src'), height, this.options.imageMaxSize);
      }
      return false;
    }
    // Check  pixels
    if( pixels < this.options.imageMinPixels ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Pixel count less than min - ', element.get('src'), pixels, this.options.imageMinPixels);
      }
      return false;
    } else if( pixels > this.options.imageMaxPixels ) {
      // Debugging
      if( this.options.debug ) {
        console.log('Pixel count greater than max - ', element.get('src'), pixels, this.options.imageMaxPixels);
      }
      return false;
    }

    return true;
  },

  doSelectImagePrevious : function() {
    if( this.elements.imageThumb && this.elements.imageThumb.getPrevious() ) {
      this.setImageThumb(this.elements.imageThumb.getPrevious());
    }
  },

  doSelectImageNext : function() {
    if( this.elements.imageThumb && this.elements.imageThumb.getNext() ) {
      this.setImageThumb(this.elements.imageThumb.getNext());
    }
  },

  doToggleNoImage : function() {
    if( !$type(this.params.thumb) ) {
      this.params.thumb = this.elements.imageThumb.src;
      this.setFormInputValue('thumb', this.params.thumb);
      this.elements.previewImages.setStyle('display', '');
      if( this.elements.previewChoose ) this.elements.previewChoose.setStyle('display', '');
      this.setUploadFileDisplay(false);
    } else {
      delete this.params.thumb;
      this.setFormInputValue('thumb', '');
      this.elements.previewImages.setStyle('display', 'none');
      if( this.elements.previewChoose ) this.elements.previewChoose.setStyle('display', 'none');
      this.setUploadFileDisplay(true);
    }
  },

  setImageThumb : function(element) {
    // Hide old thumb
    if( this.elements.imageThumb ) {
      this.elements.imageThumb.addClass('post-compose-preview-image-hidden');
    }
    if( element ) {
      element.removeClass('post-compose-preview-image-hidden');
      this.elements.imageThumb = element;
      this.params.thumb = element.src;
      this.setFormInputValue('thumb', element.src);
      if( this.elements.previewCount ) {
        var index = this.params.loadedImages.indexOf(element.src);
        //this.elements.previewCount.set('html', ' | ' + (index + 1) + ' of ' + this.params.loadedImages.length + ' | ');
        this.elements.previewCount.set('html', ' | ' + this._lang('%d of %d', index + 1, this.params.loadedImages.length) + ' | ');
      }
    } else {
      this.elements.imageThumb = false;
      delete this.params.thumb;
    }
  },


  handleEditTitle : function(element) {
    element.setStyle('display', 'none');
    var input = new Element('input', {
      'type' : 'text',
      'value' : element.get('text').trim(),
      'events' : {
        'blur' : function() {
          if( input.value.trim() != '' ) {
            this.params.title = input.value;
            element.set('text', this.params.title);
            this.setFormInputValue('title', this.params.title);
          }
          element.setStyle('display', '');
          input.destroy();
        }.bind(this)
      }
    }).inject(element, 'after');
    input.focus();
  },

  handleEditDescription : function(element) {
    element.setStyle('display', 'none');
    var input = new Element('textarea', {
      'html' : element.get('text').trim(),
      'events' : {
        'blur' : function() {
          if( input.value.trim() != '' ) {
            this.params.description = input.value;
            element.set('text', this.params.description);
            this.setFormInputValue('description', this.params.description);
          }
          element.setStyle('display', '');
          input.destroy();
        }.bind(this)
      }
    }).inject(element, 'after');
    input.focus();
  },
  
  setFormInputValue : function(key, value) {
	if (this.elements.has(key)) {
      this.elements.get(key).value = value;
	}
  },
  
  _lang : function() {
	    try {
	      if( arguments.length < 1 ) {
	        return '';
	      }

	      var string = arguments[0];
	      if( $type(this.options.lang) && $type(this.options.lang[string]) ) {
	        string = this.options.lang[string];
	      }

	      if( arguments.length <= 1 ) {
	        return string;
	      }

	      var args = new Array();
	      for( var i = 1, l = arguments.length; i < l; i++ ) {
	        args.push(arguments[i]);
	      }

	      return string.vsprintf(args);
	    } catch( e ) {
	      alert(e);
	    }
  }  
});

