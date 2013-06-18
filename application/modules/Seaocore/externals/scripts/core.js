/*
---
name: SEATips
description: Class for creating nice tips that follow the mouse cursor when hovering an element.

Extends :Tips

requires:
  - Core/Options
  - Core/Events
  - Core/Element.Event
  - Core/Element.Style
  - Core/Element.Dimensions
  - /MooTools.More

provides: [Tips]

...
*/

(function(){
  this.SEATips = new Class({
    Extends :Tips,
    options: {
      canHide : true
    },
    hide: function(element){
      if(!this.options.canHide) return;      
      if (!this.tip) document.id(this);
      this.fireEvent('hide', [this.tip, element]);
    },   
    position: function(event){
      if (!this.tip) document.id(this);
      var size = window.getSize(), scroll = window.getScroll(),
      tip = {
        x: this.tip.offsetWidth, 
        y: this.tip.offsetHeight
      },
      props = {
        x: 'left', 
        y: 'top'
      },
      bounds = {
        y: false, 
        x2: false, 
        y2: false, 
        x: false
      },
      obj = {};
      for (var z in props){
        obj[props[z]] = event.page[z] + this.options.offset[z];
        if (obj[props[z]] < 0) bounds[z] = true;
        if ((event.page[z] - scroll[z]) > size[z] - this.options.windowPadding[z]){ 
          var extra=1;
          if(z=='x')
            extra = 51;
          obj[props[z]] = event.page[z] - tip[z]+extra;
          bounds[z+'2'] = true;
        }
      }
      
      this.fireEvent('bound', bounds);
      this.tip.setStyles(obj);
    }
  });
})();

en4.seaocore = {
  setLayoutWidth:function(elementId,width){
    var layoutColumn=null;
    if($(elementId).getParent('.layout_left')){
      layoutColumn= $(elementId).getParent('.layout_left');
    }else if($(elementId).getParent('.layout_right')){
     layoutColumn= $(elementId).getParent('.layout_right');
    }else if($(elementId).getParent('.layout_middle')){
     layoutColumn= $(elementId).getParent('.layout_middle');
    }
    if(layoutColumn){
      layoutColumn.setStyle('width',width);
    }
    $(elementId).destroy();
  }
};
/**
 * likes
 */
en4.seaocore.likes = {
  like : function(type, id,show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/like',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : 0,
        show_bottom_post : show_bottom_post
      },
      onSuccess : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if($(type+'_'+id+'like_link'))
            $(type+'_'+id+'like_link').style.display="none";
          if($(type+'_'+id+'unlike_link'))
            $(type+'_'+id+'unlike_link').style.display="inline-block";
        }
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id)
    //      "force":true
    });
  },

  unlike : function(type, id,show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/unlike',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        show_bottom_post : show_bottom_post
      },
      onSuccess : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)  ) {
          if($(type+'_'+id+'unlike_link'))
            $(type+'_'+id+'unlike_link').style.display="none";
          if($(type+'_'+id+'like_link'))
            $(type+'_'+id+'like_link').style.display="inline-block";
        }
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id)
    //      "force":true
    });
  }
};

en4.seaocore.comments = {
  loadComments : function(type, id, page,show_bottom_post){
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'seaocore/comment/list',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page,
        show_bottom_post : show_bottom_post
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id),
      "force":true
    });
  },
	
  attachCreateComment : function(formElement,type,id,show_bottom_post){
    var bind = this;
    if(show_bottom_post == 1){
      formElement.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown':'keypress',function (event){
        if (event.shift && event.key == 'enter') {      	
        } else if(event.key == 'enter') {
          event.stop();             
          var form_values  = formElement.toQueryString();
          form_values += '&format=json';
          form_values += '&id='+formElement.identity.value;
          form_values += '&show_bottom_post='+show_bottom_post;
          formElement.style.display = "none";
          if($("comment-form-loading-li_"+type+'_'+id))
            $("comment-form-loading-li_"+type+'_'+id).style.display = "block";
          en4.core.request.send(new Request.JSON({
            url : en4.core.baseUrl + 'seaocore/comment/create',
            data : form_values,
            type : type,
            id : id,
            show_bottom_post : show_bottom_post    
          }), {
            'element' : $('comments'+'_'+type+'_'+id),
            "force":true
          });
                 
        }
      });
      
      // add blur event
      formElement.body.addEvent('blur',function(){
        formElement.style.display = "none";
        if($("comment-form-open-li_"+type+'_'+id))
          $("comment-form-open-li_"+type+'_'+id).style.display = "block";
      } );
    }
    formElement.addEvent('submit', function(event){
      event.stop();
      var form_values  = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id='+formElement.identity.value;
      form_values += '&show_bottom_post='+show_bottom_post;
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'seaocore/comment/create',
        data : form_values,
        type : type,
        id : id,
        show_bottom_post : show_bottom_post
      }), {
        'element' : $('comments'+'_'+type+'_'+id),
        "force":true
      });
    })
  },
	
  comment : function(type, id, body, show_bottom_post){
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/create',
      data : {
        format : 'json',
        type : type,
        id : id,
        body : body,
        show_bottom_post : show_bottom_post
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id),
      "force":true
    });
  },
	
  like : function(type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/like',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        show_bottom_post : show_bottom_post
      },
      onSuccess : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)) {
          if($(type+'_'+id+'like_link'))
            $(type+'_'+id+'like_link').style.display="none";
          if($(type+'_'+id+'unlike_link'))
            $(type+'_'+id+'unlike_link').style.display="inline-block";
        }
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id),
      "force":true
    });
  },
	
  unlike : function(type, id, show_bottom_post, comment_id) {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/unlike',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        show_bottom_post : show_bottom_post
      },
      onSuccess : function(responseJSON) {
        if( $type(responseJSON) == 'object' && $type(responseJSON.status)  ) {
          if($(type+'_'+id+'unlike_link'))
            $(type+'_'+id+'unlike_link').style.display="none";
          if($(type+'_'+id+'like_link'))
            $(type+'_'+id+'like_link').style.display="inline-block";
        }
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id),
      "force":true
    });
  },
	
  showLikes : function(type, id, show_bottom_post){
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'seaocore/comment/list',
      data : {
        format : 'html',
        type : type,
        id : id,
        viewAllLikes : true,
        show_bottom_post : show_bottom_post
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id),
      "force":true
    });
  },
	
  deleteComment : function(type, id, comment_id) {
    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
      return;
    }
    (new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/comment/delete',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id
      },
      onComplete: function() {
        if( $('comment-' + comment_id) ) {
          $('comment-' + comment_id).destroy();
        }
        try {
          var commentCount = $$('.comments_options span')[0];
          var m = commentCount.get('html').match(/\d+/);
          var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
          commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
        } catch( e ) {}
      }
    })).send();
  }
};

en4.seaocore.facebook = {
  
  runFacebookSdk: function () {
    
    window.fbAsyncInit = function() { 
      FB.JSON.stringify = function (value) {
        return JSON.encode(value);
      };
      FB.init({
        appId: fbappid,
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml  : true  // parse XFBML
      });
  		  
      if (window.setFBContent) {
  	     
        setFBContent ();
      }
    };
    (function() {
      var catarea = $('global_footer');
      if (catarea == null) {
        catarea = $('global_content');
      }
      if (catarea != null && (typeof $('fb-root') == 'undefined' || $('fb-root') == null)) {
        var newdiv = document.createElement('div');
        newdiv.id = 'fb-root';
        newdiv.inject(catarea, 'after');
        var e = document.createElement('script');
        e.async = true;
        if (typeof local_language != 'undefined' && $type(local_language)) {
          e.src = document.location.protocol + '//connect.facebook.net/'+ local_language +'/all.js';
        }
        else {
          e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        }  
        document.getElementById('fb-root').appendChild(e);
      }
    }());
    
  }
  
};

en4.seaocore.advlightbox = {
  createDefaultContent:function(){
    
  }
}
//window.addEvent('load', function() {
//  if (typeof FB == 'undefined' && typeof fbappid != 'undefined')  {
//    en4.seaocore.facebook.runFacebookSdk (); 
//  }
//  
//});


en4.core.runonce.add(function() { 
 
  // Reload The Page on Pop State Click (Back & Forward) Pop State Button
  var defaultlocationHref = window.location.href;
  var n = defaultlocationHref.indexOf('#');
  defaultlocationHref = defaultlocationHref.substring(0, n != -1 ? n : defaultlocationHref.length);
  window.addEventListener("popstate", function(e) {
    var url = window.location.href;
    var n = url.indexOf('#');
    url = url.substring(0, n != -1 ? n : url.length);
    if(e && e.state && url != defaultlocationHref){       
      window.location.reload(true);
    }      
  }); 
// END
});

function addfriend(el, user_id) {
	
  en4.core.request.send(new Request.HTML({
    method : 'post',
    'url' : en4.core.baseUrl + 'seaocore/feed/addfriendrequest',
    'data' : {
      format : 'html',
      'resource_id' : user_id
    //'action_id' : action_id,
    },
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
      var parent = el.getParent('div');
      var nextSibling=el.nextSibling;
      el.destroy();         
      parent.insertBefore(new Element('span',{				 
        'html' : responseHTML
      }), nextSibling);
     
    }
  }), {
    'force': true
  });
}

en4.seaocore.ajaxTab ={
  click_elment_id:'',
  attachEvent : function(widget_id,params){
    params.requestParams.content_id = widget_id;
    var element;
    
    $$('.tab_'+widget_id).each(function(el){
      if(el.get('tag') == 'li'){
        element =el;
        return;
      }
    });
    var onloadAdd = true;
    if(element){
      if(element.retrieve('addClickEvent',false))
        return;
      element.addEvent('click',function(){
        if(en4.seaocore.ajaxTab.click_elment_id == widget_id)
          return;
        en4.seaocore.ajaxTab.click_elment_id = widget_id;
        en4.seaocore.ajaxTab.sendReq(params);
      });
      element.store('addClickEvent',true);
      var attachOnLoadEvent = false; 
      if( tab_content_id == widget_id){ 
        attachOnLoadEvent=true;
      }else{
        $$('.tabs_parent').each(function(element){
          var addActiveTab= true;
          element.getElements('ul > li').each(function(el){
            if(el.hasClass('active')){
              addActiveTab = false;
              return;
            }
          }); 
          element.getElementById('main_tabs').getElements('li:first-child').each(function(el){
            el.get('class').split(' ').each(function(className){
              className = className.trim();
              if( className.match(/^tab_[0-9]+$/) && className =="tab_"+widget_id  ) {
                attachOnLoadEvent=true;
                if(addActiveTab || tab_content_id == widget_id){
                  element.getElementById('main_tabs').getElements('ul > li').removeClass('active');
                  el.addClass('active');
                  element.getParent().getChildren('div.' + className).setStyle('display', null);        
                }
                return;
              }
            });          
          });
        });
      }
      if(!attachOnLoadEvent)
        return;
      onloadAdd = false;
      
    }
      
    en4.core.runonce.add(function() {
      if(onloadAdd)
        params.requestParams.onloadAdd=true;
      en4.seaocore.ajaxTab.click_elment_id = widget_id;
      en4.seaocore.ajaxTab.sendReq(params);
    });
  },
  sendReq: function(params){
    params.responseContainer.each(function(element){
      element.empty();
      new Element('div', {      
        'class' : 'sr_profile_loading_image'      
      }).inject(element);
    });
    var url = en4.core.baseUrl+'widget';
   
    if(params.requestUrl)
      url= params.requestUrl;
    
    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        params.responseContainer.each(function(container){
          container.empty();
          Elements.from(responseHTML).inject(container);
          en4.core.runonce.trigger();
          Smoothbox.bind(container);
        });
       
      }
    });
    request.send();
  }
};

en4.seaocore.nestedcomments = {
	
  loadComments: function(type, id, page, order, parent_comment_id){
		
    if($('view_more_comments_'+parent_comment_id)) {
      $('view_more_comments_'+parent_comment_id).style.display = 'inline-block';
      $('view_more_comments_'+parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';			
    }
    if($('view_previous_comments_'+parent_comment_id)) {
      $('view_previous_comments_'+parent_comment_id).style.display = 'inline-block';
      $('view_previous_comments_'+parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';			
    }
    if($('view_later_comments_'+parent_comment_id)) {
      $('view_later_comments_'+parent_comment_id).style.display = 'inline-block';
      $('view_later_comments_'+parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';			
    }
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page,
        order: order,
        parent_div: 1,
        parent_comment_id:parent_comment_id
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
    });
  },

  loadcommentssortby : function(type, id, order, parent_comment_id){
    if($('sort'+'_'+type+'_'+id+'_'+parent_comment_id)) {
      $('sort'+'_'+type+'_'+id+'_'+parent_comment_id).style.display = 'inline-block';
      $('sort'+'_'+type+'_'+id+'_'+parent_comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data : {
        format : 'html',
        type : type,
        id : id,
        order: order,
        parent_div: 1,
        parent_comment_id:parent_comment_id
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
    });
  },
	
  attachCreateComment : function(formElement,type,id,parent_comment_id){
    var bind = this;
    formElement.addEvent('submit', function(event){
      event.stop();
      if(formElement.body.value == '') 
        return;
      if($('seaocore_comment_image_'+type+'_'+id+'_'+parent_comment_id))
        $('seaocore_comment_image_'+type+'_'+id+'_'+parent_comment_id).destroy();
      var divEl= new Element('div', {
        'class' : '',
        'html': '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading">',
        'id': 'seaocore_comment_image_'+type+'_'+id+'_'+parent_comment_id,
        'styles':{
          'display':'inline-block'
        }
      });  			
	
      divEl.inject(formElement);  
      var form_values  = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id='+formElement.identity.value;

      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'seaocore/nestedcomment/create',
        data : form_values,
        type : type,
        id : id,
        onComplete: function(e) {
          if(parent_comment_id == 0) return;
          try {
            var replyCount = $$('.seaocore_replies_options span')[0];
            var m = replyCount.get('html').match(/\d+/);
            replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
          } catch( e ) {}
        }
      }), {
        'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
      });   
    })
  },

  comment : function(type, id, body, parent_comment_id){
    if(body == '') 
      return;
    var formElement  = $('comments_form_'+type+'_'+id+'_'+parent_comment_id);
    if($('seaocore_comment_image_'+type+'_'+id+'_'+parent_comment_id))
      $('seaocore_comment_image_'+type+'_'+id+'_'+parent_comment_id)
    var divEl= new Element('div', {
      'class' : '',
      'html': '<img src="application/modules/Seaocore/externals/images/spinner.gif">',
      'id': 'seaocore_comment_image_'+type+'_'+id+'_'+parent_comment_id,
      'styles':{
        'display':'inline-block'
      }
    });  			
    divEl.inject(formElement);     
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/create',
      data : {
        format : 'json',
        type : type,
        id : id,
        body : body
      },
      onComplete: function(e) {
        if(parent_comment_id == 0) return;
        try {
          var replyCount = $$('.seaocore_replies_options span')[0];
          var m = replyCount.get('html').match(/\d+/);
          replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
        } catch( e ) {}
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
    });
  },

  like : function(type, id, comment_id, order, parent_comment_id, option) {
    if($('like_comments_'+comment_id) && (option == 'child')) {
      $('like_comments_'+comment_id).style.display = 'inline-block';
      $('like_comments_'+comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';			
    }
    if($('like_comments') && (option == 'parent')) {
      $('like_comments').style.display = 'inline-block';
      $('like_comments').innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';			
    }
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/like',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        order : order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function(e) {
        if($('sitereview_most_likes_'+id)) {
          $('sitereview_most_likes_'+id).style.display = 'none';
        }
        if($('sitereview_unlikes_'+id)) {
          $('sitereview_unlikes_'+id).style.display = 'block';
        }
        
				if($(type+'_like_'+ id))
				$(type+'_like_'+ id).value = 1;
				if($(type+'_most_likes_'+ id))
				$(type+'_most_likes_'+ id).style.display = 'none';
				if($(type+'_unlikes_'+ id))
				$(type+'_unlikes_'+ id).style.display = 'inline-block';
 
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
    });
  },

  unlike : function(type, id, comment_id,order, parent_comment_id, option) {
    if($('unlike_comments_'+comment_id) && (option == 'child')) {
      $('unlike_comments_'+comment_id).style.display = 'inline-block';
      $('unlike_comments_'+comment_id).innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';
    }
    if($('unlike_comments') && (option == 'parent')) {
      $('unlike_comments').style.display = 'inline-block';
      $('unlike_comments').innerHTML = '<img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />';			
    }
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/unlike',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        order : order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function(e) {
        if($('sitereview_most_likes_'+id)) {
          $('sitereview_most_likes_'+id).style.display = 'block';
        }
        if($('sitereview_unlikes_'+id)) {
          $('sitereview_unlikes_'+id).style.display = 'none';
        }
        
				if($(type+'_like_'+ id))
				$(type+'_like_'+ id).value = 0;
				if($(type+'_most_likes_'+ id))
				$(type+'_most_likes_'+ id).style.display = 'inline-block';
				if($(type+'_unlikes_'+ id))
				$(type+'_unlikes_'+ id).style.display = 'none';
				
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
    });
  },

  showLikes : function(type, id, order, parent_comment_id){
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/list',
      data : {
        format : 'html',
        type : type,
        id : id,
        viewAllLikes : true,
        order : order,
        parent_comment_id: parent_comment_id
      }
    }), {
      'element' : $('comments'+'_'+type+'_'+id+'_'+parent_comment_id)
    });
  },

  deleteComment : function(type, id, comment_id,order, parent_comment_id) {
    if( !confirm(en4.core.language.translate('Are you sure you want to delete this?')) ) {
      return;
    }
    if( $('comment-' + comment_id) ) {
      $('comment-' + comment_id).destroy();
    }
    (new Request.JSON({
      url : en4.core.baseUrl + 'seaocore/nestedcomment/delete',
      data : {
        format : 'json',
        type : type,
        id : id,
        comment_id : comment_id,
        order : order,
        parent_comment_id: parent_comment_id
      },
      onComplete: function(e) {
        try {
          var replyCount = $$('.seaocore_replies_options span')[0];
          var m = replyCount.get('html').match(/\d+/);
          var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
          replyCount.set('html', replyCount.get('html').replace(m[0], e.commentsCount));
        } catch( e ) {}
      }
    })).send();
  }
};

var ScrollToTopSeao = function(topElementId,buttonId) {
  window.addEvent('scroll', function (){
    var element=$(buttonId);  
    if(element) {
      if($(topElementId)) {
        var elementPostionY=0;
        if( typeof( $(topElementId).offsetParent ) != 'undefined' ) {
          elementPostionY=$(topElementId).offsetTop;
        }else{
          elementPostionY=$(topElementId).y; 
        }
      }
      if(elementPostionY + window.getSize().y < window.getScrollTop()){
        if(element.hasClass('Offscreen'))
          element.removeClass('Offscreen');
      }else if(!element.hasClass('Offscreen')){       
        element.addClass('Offscreen');
      }
    }
  });
  en4.core.runonce.add(function() {
    var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
      wait: false,
      duration: 750,
      offset: {
        'x': -200, 
        'y': -100
      },
      transition: Fx.Transitions.Quad.easeInOut
    });

    $(buttonId).addEvent('click', function(event) {
      event = new Event(event).stop();
      scroll.toElement(topElementId);       
    });
  });
 
};


ActivitySEAOUpdateHandler = new Class({

  Implements : [Events, Options],
  options : {
      debug : true,
      baseUrl : '/',
      identity : false,
      delay : 5000,
      admin : false,
      idleTimeout : 600000,
      last_id : 0,
      next_id : null,
      subject_guid : null,
      showImmediately : false
    },

  state : true,

  activestate : 1,

  fresh : true,

  lastEventTime : false,

  title: document.title,
  
  //loopId : false,
  
  initialize : function(options) {
    this.setOptions(options);
  },

  start : function() {
    this.state = true;

    // Do idle checking
    this.idleWatcher = new IdleWatcher(this, {timeout : this.options.idleTimeout});
    this.idleWatcher.register();
    this.addEvents({
      'onStateActive' : function() {
        this._log('activity loop onStateActive');
        this.activestate = 1;
        this.state = true;
      }.bind(this),
      'onStateIdle' : function() {
        this._log('activity loop onStateIdle');
        this.activestate = 0;
        this.state = false;
      }.bind(this)
    });
    this.loop();
    //this.loopId = this.loop.periodical(this.options.delay, this);
  },

  stop : function() {
    this.state = false;
  },

  checkFeedUpdate : function(action_id, subject_guid){
    if( en4.core.request.isRequestActive() ) return;
    
    function getAllElementsWithAttribute(attribute) {
      var matchingElements = [];
      var values = [];
      var allElements = document.getElementsByTagName('*');
      for (var i = 0; i < allElements.length; i++) {
        if (allElements[i].getAttribute(attribute)) {
          // Element exists with attribute. Add to array.
          matchingElements.push(allElements[i]);
          values.push(allElements[i].getAttribute(attribute));
          }
        }
      return values;
    }
    var list = getAllElementsWithAttribute('data-activity-feed-item');
    this.options.last_id = Math.max.apply( Math, list );
    min_id = this.options.last_id + 1;
      
    var req = new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/name/seaocore.feed',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'subject' : this.options.subject_guid,
        'getUpdate' : true        
      }
    });    
    en4.core.request.send(req, {
      'element' : $('activity-feed'),
      'updateHtmlMode' : 'prepend'           
      }
    );
   
        
    
    req.addEvent('complete', function() {
      (function() {
        if( this.options.showImmediately && $('feed-update').getChildren().length > 0 ) {
          $('feed-update').setStyle('display', 'none');
          $('feed-update').empty();
          this.getFeedUpdate(this.options.next_id);
          }
        }).delay(50, this);
    }.bind(this));
    
   
   
   // Start LOCAL STORAGE STUFF   
   if(localStorage) {
     var pageTitle = document.title;
     //@TODO Refill Locally Stored Activity Feed
     
     // For each activity-item, get the item ID number Data attribute and add it to an array
     var feed  = document.getElementById('activity-feed');
     // For every <li> in Feed, get the Feed Item Attribute and add it to an array
     var items = feed.getElementsByTagName("li");
     var itemObject = { };
     // Loop through each item in array to get the InnerHTML of each Activity Feed Item
     var c = 0;
     for (var i = 0; i < items.length; ++i) {       
       if(items[i].getAttribute('data-activity-feed-item') != null){
         var itemId = items[i].getAttribute('data-activity-feed-item');
         itemObject[c] = {id: itemId, content : document.getElementById('activity-item-'+itemId).innerHTML };
         c++;
         }
       }
     // Serialize itemObject as JSON string
     var activityFeedJSON = JSON.stringify(itemObject);    
     localStorage.setItem(pageTitle+'-activity-feed-widget', activityFeedJSON);    
   }
   
   
   // Reconstruct JSON Object, Find Highest ID
   if(localStorage.getItem(pageTitle+'-activity-feed-widget')) {
     var storedFeedJSON = localStorage.getItem(pageTitle+'-activity-feed-widget');
     var storedObj = eval ("(" + storedFeedJSON + ")");
     
     //alert(storedObj[0].id); // Highest Feed ID
    // @TODO use this at min_id when fetching new Activity Feed Items
   }
   // END LOCAL STORAGE STUFF
   
  
   return req;  
  },

  getFeedUpdate : function(last_id){
    if( en4.core.request.isRequestActive() ) return;
    var min_id = this.options.last_id + 1;
    this.options.last_id = last_id;
    document.title = this.title;
    var req = new Request.HTML({
      url : en4.core.baseUrl + 'widget/index/name/seaocore.feed',
      data : {
        'format' : 'html',
        'minid' : min_id,
        'feedOnly' : true,
        'nolayout' : true,
        'getUpdate' : true,
        'subject' : this.options.subject_guid
      }
    });
    en4.core.request.send(req, {
      'element' : $('activity-feed'),
      'updateHtmlMode' : 'prepend'
    });
    return req;
  },

  loop : function() {
    this._log('activity update loop start');
    
    if( !this.state ) {
      this.loop.delay(this.options.delay, this);
      return;
    }

    try {
      this.checkFeedUpdate().addEvent('complete', function() {
        try {
          this._log('activity loop req complete');
          this.loop.delay(this.options.delay, this);
        } catch( e ) {
          this.loop.delay(this.options.delay, this);
          this._log(e);
        }
      }.bind(this));
    } catch( e ) {
      this.loop.delay(this.options.delay, this);
      this._log(e);
    }
    
    this._log('activity update loop stop');
  },
  
  // Utility
  _log : function(object) {
    if( !this.options.debug ) {
      return;
    }

    try {
      if( 'console' in window && typeof(console) && 'log' in console ) {
        console.log(object);
      }
    } catch( e ) {
      // Silence
    }
  }
});

en4.seaocore.locationBased = { 

	  startReq: function(params){
    window.locationsParamsSEAO = { 
      latitude:0, 
      longitude:0
    };
    window.locationsDetactSEAO = false;
    params.isExucute=false;
    var self=this;

    if (params.detactLocation && !window.locationsDetactSEAO && navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position){
        window.locationsParamsSEAO.latitude = position.coords.latitude;
        window.locationsParamsSEAO.longitude = position.coords.longitude;
        params.requestParams= $merge(params.requestParams,window.locationsParamsSEAO);
         params.isExucute = true;
        self.sendReq(params);
      },function(){
         params.isExucute = true;
        self.sendReq(params);
      });
      window.locationsDetactSEAO = true;
      window.setTimeout(function(){
        if(params.isExucute)
          return;
        self.sendReq(params);
      },3000);
    }else{ 
      if(params.detactLocation && window.locationsDetactSEAO){
        params.requestParams= $merge(params.requestParams,window.locationsParamsSEAO);
      }
      
      self.sendReq(params);
    }
    
  },
  sendReq: function(params){

    var self=this;
    var url = en4.core.baseUrl+'widget';

    if(params.requestUrl)
      url= params.requestUrl;
    var request = new Request.HTML({
      url : url,
      data : $merge(params.requestParams,{
        format : 'html',
        subject: en4.core.subject.guid,
        is_ajax_load:true
      }),
      evalScripts : true,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				
        $(params.responseContainer).innerHTML = '';
        Elements.from(responseHTML).inject($(params.responseContainer));
        
        en4.core.runonce.trigger();
        Smoothbox.bind(params.responseContainer);
      }
    });
    request.send();
  }
}