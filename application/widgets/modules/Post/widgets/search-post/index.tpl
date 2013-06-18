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
<?php //echo $this->form->render($this) ?>
<form id="post_search_post_form" action="<?php echo $this->url(array("action" => "browse"), "post_general", true) ?>" method="get">
  <input type="text" name="keyword" id="post_search_post_form_field" size="20" maxlength="48" alt="<?php echo $this->translate("Search post") ?>" />
  <button type="submit" id="post_search_post_form_button" name=""><?php echo $this->translate('GO')?></button>
</form>

<script type="text/javascript">
en4.core.runonce.add(function(){

    if($("post_search_post_form_field")){
        new OverText($("post_search_post_form_field"), {
          poll: true,
          pollInterval: 500,
          positionOptions: {
            position: ( en4.orientation == "rtl" ? "upperRight" : "upperLeft" ),
            edge: ( en4.orientation == "rtl" ? "upperRight" : "upperLeft" ),
            offset: {
              x: ( en4.orientation == "rtl" ? -4 : 4 ),
              y: 2
            }
          }
        });
      }

    
});
</script>
