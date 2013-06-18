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
<?php // echo date('r'); ?>
<?php // echo $this->locale()->toDateTime(date('Y-m-d h:i:s')); ?>
<script type="text/javascript">

var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('order_direction').value = default_direction;
  }
  $('post_admin_manage_filter').submit();
}


  var delectSelected = function(){

      var checkboxes = $$('input.checkboxes');
      var selecteditems = [];

      checkboxes.each(function(item, index){
        var checked = item.get('checked');
        if (checked) {
          selecteditems.push(item.get('value'));
        }
      });

    if (selecteditems == "") {
      return false;
    }  

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
  
function selectAll()
{
  var checkboxes = $$('input.checkboxes');
  var selecteditems = [];

  var chked = $('checkboxes_toggle').get('checked');
  
  checkboxes.each(function(item, index){
    item.set('checked', chked);
  });
}
</script>

<h2><?php echo $this->translate("Posts Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("This page lists all of the posts your members have posted. You can use this page to monitor these posts and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific posts. Leaving the filter fields blank will show all the posts on your social network.") ?>
</p>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $postCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%d post found", "%d posts found", $postCount), ($postCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    
  </div>
</div>
<?php //print_r($this->params)?>
<br />

<?php if( count($this->paginator) ): ?>

<table class='admin_table' id='post_list_posts'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' id='checkboxes_toggle' /></th>
      <th class='admin_table_short'>ID</th>
      <th class='post_header_post'><?php echo $this->translate("Post") ?></th>
      <th class='post_header_member'><?php echo $this->translate("Member"); ?></th>
      <th class='post_header_media'><?php echo $this->translate("Media"); ?></th>
      <th class='post_header_votes'><?php echo $this->translate("Votes") ?></th>
      <th class='post_header_icon'><?php echo $this->translate("Icon") ?> [<a href="javascript:void(0);" onclick="Smoothbox.open($('post_icons_legend')); return false;">?</a>]</th>
      <th class='post_header_status'><?php echo $this->translate("Status") ?></th>
      <th class='post_header_options'><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): // $this->string()->chunk($item->getTitle(), 5) ?>
      <tr>
        <td><input type='checkbox' class='checkboxes' value="<?php echo $item->post_id ?>"/></td>
        <td><?php echo $item->post_id ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('target'=>'_blank') );?>
          <div class="post_text_desc">
            <?php echo $this->locale()->toDate($item->creation_date); ?>
                - <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
                - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
                - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
          </div>    
        </td>
        <td>
          <?php echo $item->getOwner()->toString(); ?>
        </td>
        <td>
          <?php echo $this->translate($item->getMediaText());?>
        </td>
        <td>
          <?php echo $this->locale()->toNumber($item->point_count); ?>
          <div class="post_text_desc">
          [<?php echo $this->locale()->toNumber($item->helpful_count); ?>/<?php echo $this->locale()->toNumber($item->nothelpful_count); ?>]
          </div>
        </td>  
        <td><?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'post', 'controller' => 'manage', 'action' => 'featured', 'post_id' => $item->post_id),
            $this->htmlImage('./application/modules/Post/externals/images/featured'.($item->featured ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->featured ? "Featured" : "Not Featured"))) ?>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'post', 'controller' => 'manage', 'action' => 'sponsored', 'post_id' => $item->post_id),
            $this->htmlImage('./application/modules/Post/externals/images/sponsored'.($item->sponsored ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->sponsored ? "Sponsored" : "Not Sponsored"))) ?>
        </td>
        <td>
          <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'post', 'controller' => 'manage', 'action' => 'update-status', 'post_id' => $item->post_id),
            $this->translate($item->getStatusText()),
            array('class' => 'smoothbox')) ?>
        </td>
        <td>
          <?php echo $this->htmlLink(array('route'=>'post_specific', 'action'=>'edit', 'post_id'=>$item->post_id), $this->translate('edit'), array('target'=>'_blank'))?>
          |
          <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'post', 'controller' => 'manage', 'action' => 'delete', 'post_id' => $item->post_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>

<?php //print_r($this->params)?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no posts posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>


<div style="display: none">
    
  <ul class="radcodes_admin_icons_legend" id="post_icons_legend">
    <li><?php echo $this->htmlImage('./application/modules/Post/externals/images/featured.png');?><?php echo $this->translate('Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Post/externals/images/featured_off.png');?><?php echo $this->translate('Not Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Post/externals/images/sponsored.png');?><?php echo $this->translate('Sponsored')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Post/externals/images/sponsored_off.png');?><?php echo $this->translate('Not Sponsored')?></li>  
  </ul>
  
</div>
