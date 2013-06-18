
en4.postvote = {

  data : {},

  vote: function(me, identity, url) {
    if( !en4.user.viewer.id ) {
      window.location.href = en4.core.baseUrl + 'login' + '?return_url=' + encodeURIComponent(window.location.href);
      return;
    }

    var postFeedBackContainer = $('post_feedback_' + identity);

    var parent = $(me).getParent('.post_feedback');
    
    en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html'
        }
      }), {
        'element' : parent
    });    

  }

};


