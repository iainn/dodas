<?php ?>


<script type="text/javascript">

var photoLightbox = 1;

</script>


<?php if(empty($this->is_ajax_lightbox)): ?>
<div id="ads_hidden" style="display: none;" >  
</div>
<?php endif; ?>
<?php
  $showLink = false;
  if (isset($this->params['type']) && !empty($this->params['type'])):
    if ($this->type_count > 1):
      $showLink = true;
    endif;
  elseif ($this->album->count() > 1):
    $showLink = true;
  endif;
?>

<div class="photo_lightbox_cont">
  <div class="photo_lightbox_left" id="photo_lightbox_seaocore_left" style="right: 1px;">
  	<table width="100%" height="100%">
  		<tr>
  			<td width="100%" height="100%" valign="middle">
			    <div class="photo_lightbox_image" id='media_image_div_seaocore'>     
			        <?php if($this->viewPermission): ?>      
			      <div id='media_photo_next' <?php if ($showLink): ?> onclick='getNextPhotoSEAOCore()' <?php endif; ?> >
			        <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
			            'id' => 'media_photo',
			            'class'=>"lightbox_photo"
			          )); ?>     
			        </div>
              <div class="photo_lightbox_swf" style="display: none;" id="full_mode_photo_button" onclick="switchFullModePhotoSEAO(true);">
                <div class="photo_lightbox_cm_f"></div>    
              </div>
              <div id="comment_count_photo_button" class="photo_lightbox_cm_box" style="display: none;" onclick="switchFullModePhotoSEAO(false);" title="<?php echo $this->translate('Show Comments');?>">
                <?php //if(isset ($this->photo->like_count)):?>
                <div class="photo_lightbox_cm_cc"><?php echo $this->photo->likes()->getLikeCount()?></div>
                <div class="photo_lightbox_cml_c"></div>
                <?php //endif;?>
                <?php //if(isset ($this->photo->comment_count)):?>
                <div class="photo_lightbox_cm_cc"><?php echo $this->photo->comments()->getCommentCount() ?></div>
                <div class="photo_lightbox_cm_c" ></div>
                <?php //endif;?>
              </div>
			        <?php else:?>
			          <div class="tip">
			            <span><?php echo $this->translate('You do not have the permission to view this photo.');?> </span>
			          </div>
                <div  style="display: none;" id="full_mode_photo_button"></div>
                <div id="comment_count_photo_button" style="display: none;"></div>
			       <?php endif; ?>
            <div id="full_screen_display_captions_on_image" style="display: none;">
					    <?php if (!empty($this->photo->description) || (!empty($this->photo->title) && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)) ): ?>    
					    	<div class="photo_lightbox_stc">
					        <?php if (!empty($this->photo->title) && Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)): ?>
					          <b><?php echo $this->photo->getTitle() ?></b>
					          <?php if (!empty($this->photo->description)): ?>
					            <br />
					          <?php endif; ?>
					        <?php endif; ?>
					        <?php if (!empty($this->photo->description)): ?>	
                     <span id="full_screen_display_captions_on_image_dis"><?php echo $this->photo->getDescription() ?></span>
					        <?php endif; ?>
					      </div>
					    <?php endif; ?>
				    </div>
			    </div>
			   <?php if($showLink):?>
				   <div class="photo_lightbox_options" id="seaocore_photo_scroll">
				      <div class="photo_lightbox_pre" onclick='getPrevPhotoSEAOCore()' title="<?php echo $this->translate('Previous');?>" ><i></i></div>
				     <div onclick='getNextPhotoSEAOCore()'  title="<?php echo $this->translate('Next');?>" class="photo_lightbox_nxt"><i></i></div>     
				   </div>
			   <?php endif; ?>
    		</td>
    	</tr>
    </table>
    <?php if (strtolower($this->album->getModuleName()) == 'album' && $this->canMakeFeatured && !$this->allowView): ?>
	    <div class="tip photo_lightbox_privacy_tip">
	      <span>
	        <?php echo $this->translate("SITEALBUM_PHOTO_VIEW_PRIVACY_MESSAGE"); ?>
	      </span>
	    </div>
	  <?php endif; ?>
  </div>
  <div class="photo_lightbox_right" id="photo_lightbox_right_content"> 
    <div id="main_right_content_area"  style="height: 100%">
      <div id="main_right_content" class="scroll_content">        
        <div id="photo_right_content" class="photo_lightbox_right_content">
          <?php if($this->viewPermission): ?>
          <div class='photo_right_content_top'>
            <div class='photo_right_content_top_l'>
                <?php
                echo (strtolower($this->album->getModuleName()) == 'album') ? $this->htmlLink($this->album->getOwner()->getHref(), $this->itemPhoto($this->album->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo', 'title' => $this->album->getOwner()->getTitle())) :
                        $this->htmlLink($this->photo->getOwner()->getHref(), $this->itemPhoto($this->photo->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo', 'title' => $this->photo->getOwner()->getTitle()));
                ?>
            </div>
            <div class='photo_right_content_top_r'>
                <?php
                if (strtolower($this->album->getModuleName()) == 'album'):
                  echo $this->album->getOwner()->__toString();
                else:
                  echo $this->photo->getOwner()->__toString();
                endif;
                ?>    
               <?php //echo $this->timestamp($this->photo->modified_date) ?>                
            </div>
        	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.title', 0)):?>
        		<div class="photo_right_content_top_title" style="margin-top:5px;">
		          <?php if ($this->canEdit || !empty($this->photo->title)): ?>
		      			<div id="link_seaocore_title" style="display:block;">
		        			<span id="seaocore_title">
		        				<?php if (!empty($this->photo->title)): ?>
		  								<?php echo $this->photo->getTitle() ?>
										<?php elseif ($this->canEdit): ?>
		            			<?php echo $this->translate('Add a title'); ?>
		        				<?php endif; ?>
		        			</span>
		        			<?php if ($this->canEdit): ?>
		            		<a href="javascript:void(0);" onclick="showeditPhotoTitleSEAO()" class="photo_right_content_top_title_edit"><?php echo $this->translate('Edit'); ?></a>
		        			<?php endif; ?>
		      			</div>
		        	<?php endif; ?>
        			<div id="edit_seaocore_title" class="photo_right_content_top_edit" style="display: none;">
          			<input type="text"  name="edit_title" id="editor_seaocore_title" title="<?php echo $this->translate('Add a title'); ?>" value="<?php echo $this->photo->title; ?>" />
	          		<div class="buttons">
	            		<button name="save" onclick="saveEditTitlePhotoSEAO('<?php echo $this->photo->getIdentity(); ?>','<?php echo $this->resource_type; ?>')"><?php echo $this->translate('Save'); ?></button>
	            		<button name="cancel" onclick="showeditPhotoTitleSEAO();"><?php echo $this->translate('Cancel'); ?></button>
	          		</div>
        			</div>
	        		<div id="seaocore_title_loading" style="display: none;" >
	          		<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
	        		</div>
	        	</div>	
      		<?php endif;?>
          </div>  
        	
      		<div class="photo_right_content_top_title photo_right_content_top_caption">
	          <?php if($this->canEdit  || !empty($this->photo->description)  ): ?>
		          <div id="link_seaocore_description" style="display:block;">
		            <span id="seaocore_description" class="lightbox_photo_description">
		              <?php if (!empty($this->photo->description) ): ?>
		                 <?php echo $this->viewMore($this->photo->getDescription(),400,5000,400,true); ?>
		              <?php elseif($this->canEdit): ?>
		                <?php echo $this->translate('Add a caption');?>
		              <?php endif; ?>
		            </span>
		            <?php if($this->canEdit): ?>
		            	<a href="javascript:void(0);" onclick="showeditDescriptionSEAO()" class="photo_right_content_top_title_edit"><?php echo $this->translate('Edit');?></a>
		            <?php endif; ?>
		          </div>
	          <?php endif; ?>
            <div id="edit_seaocore_description" class="photo_right_content_top_edit" style="display: none;">
            	<textarea rows="2" cols="10"  name="edit_description" id="editor_seaocore_description" title="<?php echo $this->translate('Add a caption');?>" ><?php echo $this->photo->description; ?></textarea>
              <div class="buttons">
                <button name="save" onclick="saveEditDescriptionPhotoSEAO('<?php echo $this->photo->getIdentity(); ?>','<?php echo $this->photo->getType(); ?>')"><?php echo $this->translate('Save'); ?></button>
                <button name="cancel" onclick="showeditDescriptionSEAO();"><?php echo $this->translate('Cancel'); ?></button>
              </div>
            </div>
            <div id="seaocore_description_loading" style="display: none;" >
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
            </div>
	        </div> 
          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) :?>
						<div class="seaotagcheckinshowlocation">
							<?php
								// Render LOCAION WIDGET
								echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin", array('showSuggest'=> 0)); 
							?>
						</div>
					<?php endif;?> 
					<div class="photo_right_content_tags" id="media_tags" style="display: none;">
					  <?php echo $this->translate('In this photo:'); ?>
					</div>
	        <div id="photo_view_comment" class="photo_right_content_comments">
	        	<?php echo $this->action("list", "comment", "seaocore", array("type"=>$this->photo->getType(), "id"=>$this->photo->getIdentity(),"show_bottom_post"=> 1)); ?>
	        </div> 
          <?php endif; ?>
      	</div>       
     	</div>
    </div>
    <div class="photo_right_content_add" id="ads">
      <?php if(empty($this->isajax)): ?>
        <?php echo $this->content()->renderWidget("seaocore.lightbox-ads", array('limit' => 1)) ?>
      <?php endif; ?>
    </div>
  </div> 
</div>

<div id="close_all_photos" class="sea_val_photos_box_wrapper_overlay" style="height:0px; " onclick="closeAllPhotoContener()"></div>
<div id="close_all_photos_btm" class="sea_val_photos_box_wrapper_overlay_btm" onclick="closeAllPhotoContener()"  style="height:0px;" ></div>
<div id="all_photos" class="sea_val_photos_box_wrapper" style="height:0px;">
  <div class="sea_val_photos_box_header">
  	<?php echo $this->album->getTitle() ?>
    (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->count()), $this->locale()->toNumber($this->album->count())); ?>)
  </div>
  <div class="photo_lightbox_close" onclick="closeAllPhotoContener()"></div>
  <div id="main_photos_contener"> 
	  <div id="photos_contener" class="lb_photos_contener scroll_content sea_val_photos_thumbs_wrapper">
	  	<div class="sea_val_photos_box_loader">
	    	<img alt="<?php echo $this->translate("Loading...") ?>" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/icons/loader-large.gif" />
	    </div>
	  </div>
  </div>  
</div>
<div class="lightbox_btm_bl">
  <?php if($this->viewPermission): ?>
  <div class="lightbox_btm_bl_left">
    <?php if($this->album->count()>1 && (!isset($this->params['type']) || empty ($this->params['type']))):?>
	    <div class="lightbox_btm_bl_btn" style="" onclick="showAllSEAOPhotoContener('<?php echo $this->album->getGuid() ?>','<?php echo $this->photo->getIdentity() ?>','<?php echo $this->album->count() ?>')"> 
	      <span class="b-a-Ua lightbox_btm_bl_btn_t"><?php echo $this->translate('View All'); ?></span>
	      <span class="lightbox_btm_bl_btn_i"></span>
	    </div>
    <?php endif;?>
    <div id="photo_owner_lb_fullscreen" style="display: none;" class='lightbox_btm_bl_left_photo'>      
        <?php
        echo (strtolower($this->album->getModuleName()) == 'album') ? $this->htmlLink($this->album->getOwner()->getHref(), $this->itemPhoto($this->album->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo')) :
                $this->htmlLink($this->photo->getOwner()->getHref(), $this->itemPhoto($this->photo->getOwner(), 'thumb.icon', '', array('align' => 'left')), array('class' => 'item_photo'));
        ?>
    </div>  
    <div class="lightbox_btm_bl_left_links">
      
      <div class="lbbll_ml" id="photo_owner_titile_lb_fullscreen" style="display: none;">
          <?php echo (strtolower($this->album->getModuleName()) == 'album') ? $this->album->getOwner()->__toString() : $this->photo->getOwner()->__toString(); ?>
      </div>
      <div class="lbbll_s" id="photo_owner_titile_lb_fullscreen_sep" style="display: none;">-</div>
        
    	<?php if(!empty ($this->displayTitle) && isset($this->params['type']) && !empty ($this->params['type'])):?>    
      	<div class="lbbll_ml"><?php echo $this->translate(ucfirst($this->displayTitle));?></div>
       	<div class="lbbll_s">-</div>
    	<?php endif;?>        
      <div class="lbbll_ml">
      	<?php echo (strtolower($this->album->getModuleName())=='album') ? $this->htmlLink($this->album, $this->album->getTitle()) : $this->htmlLink($this->album->getParent(), $this->album->getParent()->getTitle()); ?>
      </div>     
      <br class="clr" />   	
      
			<?php if(!isset($this->params['type']) || empty ($this->params['type'])):?>        
      	<div class="lbbll_ol"><?php echo $this->translate('%1$s of %2$s',
          $this->locale()->toNumber($this->getPhotoIndex + 1),
          $this->locale()->toNumber($this->album->count())) ?></div>
       <div class="lbbll_s">-</div>
     	<?php endif;?>       
      <div class="lbbll_ol">
    		<?php echo $this->timestamp($this->photo->modified_date) ?>
    	</div>  
       <?php if(($this->viewer()->getIdentity() && (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO ||SEA_PHOTOLIGHTBOX_REPORT)) || SEA_PHOTOLIGHTBOX_DOWNLOAD || (SITEALBUM_ENABLED && $this->canMakeFeatured && $this->allowView) || $this->canDelete || ($this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO)):?> 
      <div class="lbbll_s">-</div>
      <div class="lbbll_ol p_r">
        <div id="photos_options_area" class="lbbll_ol_uop">
          <?php if ($this->viewer()->getIdentity()): ?>            
            <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>
              <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo ($this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" >
                <?php echo $this->translate("Report") ?>
              </a>
            <?php endif; ?>               
            <?php if (SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO): ?>
               <?php if(!in_array(strtolower($this->photo->getModuleName()) ,array( 'sitepagenote','sitepage', 'sitebusinessnote','sitebusiness'))):?>
              <a href="<?php echo $this->url(array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true); ?>" onclick="showSmoothBox('<?php echo $this->escape($this->url(Array('controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), 'user_extended', true)); ?>'); return false;" > <?php echo $this->translate("Make Profile Photo") ?></a>
              <?php elseif ($this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && strtolower($this->photo->getModuleName()) == 'sitepage'):?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'photo', 'action' => 'make-page-profile-photo', 'photo' => $this->photo->getGuid(), 'page_id' => $this->sitepage->page_id, 'format' => 'smoothbox'), 'sitepage_imagephoto_specific', true); ?>', 'profilephoto');">
                  <?php echo $this->translate('Make Page Profile Photo'); ?>
                </a>
							<?php elseif ($this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && strtolower($this->photo->getModuleName()) == 'sitebusiness'):?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'sitebusiness', 'controller' => 'photo', 'action' => 'make-business-profile-photo', 'photo' => $this->photo->getGuid(), 'business_id' => $this->sitebusiness->business_id, 'format' => 'smoothbox'), 'sitebusiness_imagephoto_specific', true); ?>', 'profilephoto');">
                  <?php echo $this->translate('Make Business Profile Photo'); ?>
                </a>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>
          <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>
            <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
            <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->photo->getPhotoUrl()).'&file_id='.$this->photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download')?></a>
          <?php endif; ?>
          <?php if (SITEALBUM_ENABLED && $this->canMakeFeatured && $this->allowView): ?>           
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->subject()->getIdentity()), 'sitealbum_extended', true)) . "'); return false;")) ?>
            <a href="javascript:void(0);"  onclick='featuredPhoto("<?php echo $this->subject()->getGuid() ?>");'><span id="featured_sitealbum_photo" <?php if ($this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitealbum_photo" <?php if (!$this->subject()->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>           
          <?php endif; ?>
           <?php if($this->allowFeatured):?>					
           <?php if(strtolower($this->photo->getModuleName()) == 'sitepage'):?>
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('module' =>'sitepage','controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->photo->photo_id), 'default', true)) . "'); return false;")) ?>
						<a href="javascript:void(0);"  onclick='featuredpagealbumPhoto("<?php echo $this->photo->photo_id;?>");'><span id="featured_sitepagealbum_photo" <?php if ($this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitepagealbum_photo" <?php if (!$this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>
            <?php elseif(strtolower($this->photo->getModuleName()) == 'sitebusiness'):?>
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Make Photo of the Day'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('module' =>'sitebusiness','controller' => 'photo', 'action' => 'add-photo-of-day', 'photo_id' => $this->photo->photo_id), 'default', true)) . "'); return false;")) ?>
							<a href="javascript:void(0);"  onclick='featuredbusinessalbumPhoto("<?php echo $this->photo->photo_id;?>");'><span id="featured_sitebusinessalbum_photo" <?php if ($this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Featured"); ?>" ><?php echo $this->translate("Make Featured"); ?> </span> <span id="un_featured_sitebusinessalbum_photo" <?php if (!$this->photo->featured): ?> style="display:none;" <?php endif; ?> title="<?php echo $this->translate("Make Un-Featured"); ?>" > <?php echo $this->translate("Make Un-featured"); ?> </span></a>
            <?php endif;?>
        <?php endif;?>
        <?php if ($this->canDelete):             
             if (strtolower($this->photo->getModuleName()) == 'sitepagenote') :
                  echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepage->page_id, 'owner_id' => $this->photo->user_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepage->page_id, 'owner_id' => $this->photo->user_id,), $this->deleteRoute, true)) . "'); return false;"));
            elseif (strtolower($this->photo->getModuleName()) == 'sitebusinessnote') :
                  echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitebusinessnote->note_id, 'business_id' => $this->sitebusiness->business_id, 'owner_id' => $this->photo->user_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'note_id' => $this->sitebusinessnote->note_id, 'business_id' => $this->sitebusiness->business_id, 'owner_id' => $this->photo->user_id,), $this->deleteRoute, true)) . "'); return false;"));
            elseif (strtolower($this->photo->getModuleName()) == 'sitepageevent') : 
              $deleteurl = $this->url(array('action' => 'remove', 'photo_id' => $this->photo->photo_id, 'event_id' => $this->sitepageevent->event_id, 'page_id' => $this->sitepageevent->page_id, 'owner_id' => $this->photo->user_id), 'sitepageevent_photo_extended', true);
             echo $this->htmlLink($deleteurl, $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($deleteurl) . "'); return false;"));
             elseif (strtolower($this->photo->getModuleName()) == 'sitebusinessevent') : 
              $deleteurl = $this->url(array('action' => 'remove', 'photo_id' => $this->photo->photo_id, 'event_id' => $this->sitebusinessevent->event_id, 'business_id' => $this->sitebusinessevent->business_id, 'owner_id' => $this->photo->user_id), 'sitebusinessevent_photo_extended', true);
             echo $this->htmlLink($deleteurl, $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($deleteurl) . "'); return false;"));
            elseif (strtolower($this->photo->getModuleName()) == 'sitepage') :
              echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'page_id' => $this->sitepage->page_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'page_id' => $this->sitepage->page_id,), $this->deleteRoute, true)) . "'); return false;"));
            elseif (strtolower($this->photo->getModuleName()) == 'sitebusiness') :
              echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'business_id' => $this->sitebusiness->business_id,), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->collection_id, 'photo_id' => $this->photo->getIdentity(), 'business_id' => $this->sitebusiness->business_id,), $this->deleteRoute, true)) . "'); return false;"));
            else :
              echo $this->htmlLink(array('route' => $this->deleteRoute, 'controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->getParent()->getIdentity(), 'photo_id' => $this->photo->getIdentity()), $this->translate('Delete'), array('onclick' => "showSmoothBox('" . $this->escape($this->url(array('controller' => 'photo', 'action' => $this->deleteAction, 'album_id' => $this->photo->getParent()->getIdentity(), 'photo_id' => $this->photo->getIdentity()), $this->deleteRoute, true)) . "'); return false;"));
            endif;
        endif; ?>        
        </div>        
        <span onclick="showPhotoToggleContent('photos_options_area')" class="op_box">
        	<?php echo $this->translate('Options'); ?>
        	<span class="sea_pl_at"></span>        
        </span>        
      </div>
      <?php endif; ?>  
    </div>
  </div>
  
  <div class="lightbox_btm_bl_right">
    <?php if($this->enablePinit): ?>
	  <div class="seaocore_pinit_button">
	  	<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://") . $_SERVER['HTTP_HOST'].$this->photo->getHref()); ?>&media=<?php  echo urlencode(!preg_match("~^(?:f|ht)tps?://~i", $this->photo->getPhotoUrl()) ?(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://") . $_SERVER['HTTP_HOST'] ):''.$this->photo->getPhotoUrl()); ?>&description=<?php echo $this->photo->getTitle(); ?>" class="pin-it-button" count-layout="horizontal"  id="new_pin" >Pin It</a>
			<script type="text/javascript" >
			   en4.core.runonce.add(function() {              
			      new Asset.javascript( 'http://assets.pinterest.com/js/pinit.js',{});                 
			   });			 
			</script>
	  </div>
   <?php endif;?> 
    <?php echo $this->socialShareButton();?>  
  	<?php if( $this->canEdit ): ?>
	    <div class="lightbox_btm_bl_rop" id="">
	      <a class="icon_photos_lightbox_rotate_ccw" onclick="$(this).set('class', 'icon_loading');en4.photoadvlightbox.rotate(<?php echo $this->photo->getIdentity() ?>, 90, '<?php echo $this->resource_type; ?>').addEvent('complete', function(){ this.set('class','icon_photos_lightbox_rotate_ccw') }.bind(this));" title="<?php echo $this->translate("Rotate Left"); ?>" ></a>
	      <a class="icon_photos_lightbox_rotate_cw" onclick="$(this).set('class', 'icon_loading'); en4.photoadvlightbox.rotate(<?php echo $this->photo->getIdentity() ?>, 270,'<?php echo $this->resource_type; ?>').addEvent('complete', function(){ this.set('class', 'icon_photos_lightbox_rotate_cw') }.bind(this)); " title="<?php echo $this->translate("Rotate Right"); ?>" ></a>
	      <a class="icon_photos_lightbox_flip_horizontal" onclick="$(this).set('class', 'icon_loading'); en4.photoadvlightbox.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal','<?php echo $this->resource_type; ?>').addEvent('complete', function(){ this.set('class', 'icon_photos_lightbox_flip_horizontal') }.bind(this)); " title="<?php echo $this->translate("Flip Horizontal"); ?>" ></a>
	      <a class="icon_photos_lightbox_flip_vertical" onclick="$(this).set('class', 'icon_loading'); en4.photoadvlightbox.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical','<?php echo $this->resource_type; ?>').addEvent('complete', function(){ this.set('class', 'icon_photos_lightbox_flip_vertical') }.bind(this));" title="<?php echo $this->translate("Flip Vertical"); ?>"></a>
    	</div>
  	<?php endif ?>
  	
    <?php if( $this->canTag ): ?>      
    	<span class="lightbox_btm_bl_btn" onclick='taggerInstanceSEAO.begin();'><?php echo $this->translate('Tag This Photo');?></span>     
    <?php endif; ?>
    
    <?php $viewer_id=$this->viewer()->getIdentity();
      if($this->canComment):?>   
	    <span class="lightbox_btm_bl_btn" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>like_link" <?php if( $this->subject()->likes()->isLike($this->viewer()) ): ?>style="display: none;" <?php endif; ?>onclick="en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');" title="<?php echo $this->translate('Press L to Like');?>"><?php echo $this->translate('Like');?></span>
	    <span class="lightbox_btm_bl_btn" id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>unlike_link" <?php if( !$this->subject()->likes()->isLike($this->viewer()) ): ?>style="display:none;" <?php endif;?> onclick="en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');" title="<?php echo $this->translate('Press L to Unlike');?>"><?php echo $this->translate('Unlike');?></span>
	    <span class="lightbox_btm_bl_btn" onclick="if(fullmode_photo){ switchFullModePhotoSEAO(false); }
	      if($('comment-form-open-li_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>')){
	      $('comment-form-open-li_<?php echo $this->subject()->getType()?>_<?php echo $this->subject()->getIdentity() ?>').style.display = 'none';
	      }
	      $('comment-form_<?php echo $this->subject()->getType()."_".$this->subject()->getIdentity() ?>').style.display = '';$('comment-form_<?php echo $this->subject()->getType()."_".$this->subject()->getIdentity() ?>').body.focus();"  ><?php echo $this->translate('Comments');?></span>  
  	<?php endif;?>     
    <?php if (!empty($viewer_id) && SEA_PHOTOLIGHTBOX_SHARE): ?>
      <span class="lightbox_btm_bl_btn"  onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $this->photo->getType(), 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox','not_parent_refresh'=>1), 'default', true)); ?>'); return false;" >
        <?php echo $this->translate("Share") ?>
      </span>
    <?php endif; ?>
    <a href="<?php echo $this->subject()->getHref() ?>" target="_blank" style="text-decoration:none;">
        	<span class="lightbox_btm_bl_btn lightbox_btm_bl_btn_photo"> 
        		<i></i>
        		<?php echo $this->translate("Go to Photo") ?>
        	</span>
        </a>
  </div>
  <?php endif; ?> 
</div>

<script type="text/javascript">
var taggerInstanceSEAO;
  if(window.parent.defaultLoad)
  window.parent.defaultLoad=false;
var existingTags=<?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>;

function getTaggerInstanceSEAO(){
    if(!$('media_photo_next'))return;
    taggerInstanceSEAO = new SEAOTagger('media_photo_next', {
    'title' : '<?php echo $this->string()->escapeJavascript($this->translate('Tag This Photo')); ?>',
    'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.')); ?>',
    'createRequestOptions' : {
      'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
      'data' : {
        'subject' : '<?php echo $this->subject()->getGuid() ?>'
      }
    },
    'deleteRequestOptions' : {
      'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
      'data' : {
        'subject' : '<?php echo $this->subject()->getGuid() ?>'
      }
    },
    'cropOptions' : {
      'container' : $('media_photo_next')
    },
    'tagListElement' : 'media_tags',    
    'existingTags' : existingTags,
    'suggestProto' : 'request.json',
    'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
    'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'" . $this->viewer()->getGuid() . "'" : 'false' ) ?>,
    'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
    'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>,
    'enableShowToolTip' : true,
    'showToolTipRequestOptions' : {
      'url' : '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'show-tooltip-tag'), 'default', true) ?>',
      'data' : {
        'subject' : '<?php echo $this->subject()->getGuid() ?>'
      }
      
    }
  });

  var onclickNext = $('media_photo_next').getProperty('onclick');
  taggerInstanceSEAO.addEvents({
    'onBegin' : function() {
      $('media_photo_next').setProperty('onclick','');
    },
    'onEnd' : function() {
      $('media_photo_next').setProperty('onclick',onclickNext);    
    },
    'onCreateTag': function(params) {      
      existingTags.push(params);
    },
    'onRemoveTag' : function(id) {       
        for(var i=0; i< existingTags.length; i++) {         
          if(existingTags[i].id == id) {           
            existingTags.splice(i, 1);
            break;
          }
       }
       if(existingTags.length <1)
         $("media_tags").style.display="none";
    }
  });

}

en4.core.runonce.add(function() { 
 
  var descEls = $$('.lightbox_photo_description');
  if( descEls.length > 0 ) {
    descEls[0].enableLinks();
  }
  
  if($('editor_seaocore_description'))
   $('editor_seaocore_description').autogrow();
   $('ads').style.bottom="-500px";  
    resetPhotoContentSEAO();
    (function(){
      if(!$('main_right_content_area'))return;
    rightSidePhotoContent = new SEAOMooVerticalScroll('main_right_content_area', 'main_right_content',{ });}).delay(500);   
});
if($type(keyDownEventsSEAOCorePhoto))
  document.removeEvent("keydown",keyDownEventsSEAOCorePhoto);
var keyDownEventsSEAOCorePhoto =function (e){ 
  if( e.target.get('tag') == 'html' ||
    e.target.get('tag') == 'body' ||
    e.target.get('tag') == 'div' ||
    e.target.get('tag') == 'span' ||
    e.target.get('tag') == 'a') {
    if( e.key == 'right' ) {
      getNextPhotoSEAOCore();        
    } else if( e.key == 'left' ) {
      getPrevPhotoSEAOCore();
    }else if( e.key == 'esc' ) {
      closeSEAOLightBoxAlbum();
    }        
    
  }
};
if($type(keyUpLikeEventSEAOCorePhoto))
  document.removeEvent("keyup" , keyUpLikeEventSEAOCorePhoto);
var keyUpLikeEventSEAOCorePhoto=function(e) {      
  
<?php if ($this->canComment && $this->viewPermission): ?>
    if( e.key == 'l' && (
    e.target.get('tag') == 'html' ||
      e.target.get('tag') == 'div' ||
      e.target.get('tag') == 'span' ||
      e.target.get('tag') == 'a' ||
      e.target.get('tag') == 'body' )) {
      var  photo_like_id="<?php echo $this->subject()->getType() ?>_<?php echo $this->subject()->getIdentity() ?>";
      if($(photo_like_id+"unlike_link") && $(photo_like_id+"unlike_link").style.display == "none"){
        en4.seaocore.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');
      } else if($(photo_like_id+"like_link") && $(photo_like_id+"like_link").style.display == "none"){
        en4.seaocore.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>','1');
      }
    }
<?php endif; ?>
        
  };
  document.addEvents({   
    'keyup': keyUpLikeEventSEAOCorePhoto,    
    'keydown': keyDownEventsSEAOCorePhoto
  });
  
var addPhotopaginationKEyEvent="<?php echo $showLink?>";
  function getPrevPhotoSEAOCore(){
    if(addPhotopaginationKEyEvent == 1){
   photopaginationSocialenginealbum("<?php echo $this->escape($this->prevPhoto->getHref()) ?>",<?php echo json_encode(array_merge($this->params, array('offset' => $this->PrevOffset,'tab'=>$this->tab))); ?>);
   }
  }
  function getNextPhotoSEAOCore(){
  if(addPhotopaginationKEyEvent == 1){
   photopaginationSocialenginealbum("<?php echo $this->escape($this->nextPhoto->getHref()) ?>",<?php echo json_encode( array_merge($this->params, array('offset' => $this->NextOffset,'tab'=>$this->tab))); ?>);
   }
  }
</script>
