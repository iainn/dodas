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
<?php 
$link_params = array('action'=>$this->linkto);
?>
<?php if ($this->display_style == 'wide'): ?>
  <div class="posts_categories_links">
    <ul>
    <?php foreach ($this->categories[0] as $category): ?>
      <li>
        <?php if ($this->showphoto): ?>
          <div class="post_category_photo">
            <?php echo $this->htmlLink($category->getHref($link_params), $this->itemPhoto($category, 'thumb.icon')); ?>
          </div>
        <?php endif; ?>
        <div class="post_category_info">
          <?php echo $this->htmlLink($category->getHref($link_params), $this->translate($category->getTitle()), array('class' => 'post_category_title'))?>
          <?php if ($this->descriptionlength && $category->getDescription()): ?>
            <div class="post_category_desc">
              <?php echo $this->radcodes()->text()->truncate($this->translate($category->getDescription()), $this->descriptionlength); ?>
            </div>
          <?php endif; ?>
          <?php if (isset($this->categories[$category->getIdentity()]) && count($this->categories[$category->getIdentity()])): ?>
            <ul>
            <?php foreach ($this->categories[$category->getIdentity()] as $subcategory): ?>
              <li>
                <?php echo $this->htmlLink($subcategory->getHref($link_params), $this->translate($subcategory->getTitle()));?>
              </li>
            <?php endforeach; ?>
            </ul>
          <?php endif;?>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
<?php else: ?>
<div class="radcodes_categories_list">
  <ul>
    <?php foreach ($this->categories[0] as $category): ?>
      <li>
        <?php if (isset($this->categories[$category->getIdentity()]) && count($this->categories[$category->getIdentity()])): ?>
          <a class="radcodes_categories_subcategory_toggle radcodes_categories_subcategory_toggle_collapse"><span>+</span></a>
        <?php endif; ?>
        <?php 
          $attrs = array();
          if ($this->showphoto) {
            $attrs['class'] = 'buttonlink';
            if ($category->photo_id) {
              $attrs['style'] = "background-image: url(".$category->getPhotoUrl('thumb.mini').");";
            }
          }
          //print_r($attrs);
          echo $this->htmlLink($category->getHref($link_params), $this->translate($category->getTitle()), $attrs
        );?>
        <?php if ($this->showdetails): ?>
          <div class="radcodes_category_desc">
            <?php echo $this->radcodes()->text()->truncate($category->getDescription(), $this->descriptionlength); ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($this->categories[$category->getIdentity()]) && count($this->categories[$category->getIdentity()])): ?>
          <ul style="display: none;">
          <?php foreach ($this->categories[$category->getIdentity()] as $subcategory): ?>
            <li>
              <?php 
                $attrs = array();
                if ($this->showphoto) {
                  $attrs['class'] = 'buttonlink';
                  if ($subcategory->photo_id) {
                    $attrs['style'] = "background-image: url(".$subcategory->getPhotoUrl('thumb.mini').");";
                  }
                }
                echo $this->htmlLink($subcategory->getHref($link_params), $this->translate($subcategory->getTitle()), $attrs
              );?>
            </li>
          <?php endforeach; ?>
          </ul>
        <?php endif;?>
        
      </li>
    <?php endforeach;?>
  </ul>
</div>
<script type="text/javascript">
en4.core.runonce.add(function(){
  $$('a.radcodes_categories_subcategory_toggle').addEvent('click', function(){
    var radcodes_sub_cat = $(this).getParent().getChildren('ul');
    radcodes_sub_cat.toggle();
    if (radcodes_sub_cat.getStyle('display') == 'block') {
      $(this).removeClass('radcodes_categories_subcategory_toggle_collapse').addClass('radcodes_categories_subcategory_toggle_expand');
    }
    else {
      $(this).removeClass('radcodes_categories_subcategory_toggle_expand').addClass('radcodes_categories_subcategory_toggle_collapse');
    }
  });
});
</script>
<?php endif; ?>