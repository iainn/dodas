<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Post
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<?php $this->headScript()->appendFile('application/modules/Post/externals/scripts/suggest.js') ?>


<?php echo $this->partial('index/_js_fields.tpl', 'post', array())?>
<?php echo $this->form->render($this);?>
  
<script type="text/javascript">
<!--

function post_find_images()
{
    var elements = {
            'error' : 'posts_post_create_error_js',
            'formInput' : 'url',
            'title' : 'title',
            'description' : 'description',
            'body' : 'url-element',
            //'photo' : 'photo',
            'thumb' : 'thumb',
            'activator' : 'post-link-suggest-activator',
            'loader' : 'post-link-suggest-loader'
          };
            
          var options = {
              debug: false,
                  title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Link')) ?>',
                  lang : {
                    'Could not find large images' : '<?php echo $this->string()->escapeJavascript($this->translate('Could not find large images')) ?>',
                    'Could not find images' : '<?php echo $this->string()->escapeJavascript($this->translate('Could not find images')) ?>',
                    'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
                    'Last' : '<?php echo $this->string()->escapeJavascript($this->translate('Last')) ?>',
                    'Next' : '<?php echo $this->string()->escapeJavascript($this->translate('Next')) ?>',
                    'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
                    'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
                    'Don\'t use preview image' : '<?php echo $this->string()->escapeJavascript($this->translate('No thumbnail?')) ?>',
                    'Choose Image:' : '<?php echo $this->string()->escapeJavascript($this->translate('Choose Image:')) ?>',
                    '%d of %d' : '<?php echo $this->string()->escapeJavascript($this->translate('%d of %d')) ?>'
                  },
                  requestOptions : {
                    'url' : en4.core.baseUrl + 'posts/suggest-find-images'
                  }
                };
          
          var postLinkSuggest = new PostLinkSuggest(elements, options);
          postLinkSuggest.doSuggest();
}



en4.core.runonce.add(function() {
  
	$('url').addEvent('change',  function(event){
		  console.log('change');
		  post_find_images();
  });

  new OverText($('url'), {'textOverride':'<?php echo $this->string()->escapeJavascript($this->translate('http://')) ?>','element':'span'});
  
  var actlink = new Element('span', {
    //'html': '<?php echo $this->string()->escapeJavascript($this->translate('Find Images')) ?>',
    'title' : '<?php echo $this->string()->escapeJavascript($this->translate('suggest title and photo')) ?>',
    'id' : 'post-link-suggest-activator',
    'class' : 'post_ajax_action_suggest',  
    'events' : {
      'click' : function(e) {
        post_find_images();
      }
    }
  });

  if (!$('url').get('readonly')) {
    actlink.inject($('url'), 'after');
    new Element('span', {
      'id' : 'post-link-suggest-loader',
      'class' : 'post_ajax_action_loading',  
      //'html' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
      styles: {
          display: 'none'
      }
    }).inject(actlink, 'after');
  }

});
//-->
</script>  
