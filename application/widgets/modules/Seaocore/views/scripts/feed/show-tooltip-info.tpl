<?php ?>
<div class="info_tip_wrapper" style="top:40%; left:40%;">
  <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">
    <div class="info_tip_content_wrapper">
      <div class="info_tip_content">
			<?php
				$info_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.action.link', array("poke" => "poke", "share" => "share", "message" => "message", "addfriend" => "addfriend", "suggestion" => "suggestion", "getdirection" => "getdirection", "joinpage" => "joinpage", "requestpage" => "requestpage", "review_wishlist" => "review_wishlist"));

				$informationArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.information.link', array( "category" => "category", "like" => "like" , "eventmember" => "eventmember",	"groupmember"	=> "groupmember", "mutualfriend" => "mutualfriend" , "friendcommon" => "friendcommon", "joingroupfriend" =>		"joingroupfriend", "attendingeventfriend" => "attendingeventfriend", "price" => "price", "review_count" => "review_count", "rating_count" => "rating_count", "recommend" => "recommend", "review_helpful" => "review_helpful", "rwcreated_by" => "rwcreated_by", "rewishlist_item" => "rewishlist_item", "location" => "location" ));
			?>				
				<div class="tip_main_photo">
					<?php if ($this->resource_type == 'user') : ?>
							<?php  echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result,
							'thumb.profile')); ?>
					<?php elseif ($this->resource_type == 'blog' || $this->resource_type == 'forum_topic' ||
            $this->resource_type == 'poll' || $this->resource_type == 'feedback' || $this->resource_type == 'sitefaq_faq' || $this->resource_type == 'sitereview_wishlist'  || $this->resource_type == 'sitereview_review'):   ?>
					<?php echo $this->htmlLink($this->result->getHref(),
						$this->itemPhoto($this->result->getOwner(), 'thumb.profile')); ?>
					<?php elseif($this->resource_type == 'document' || $this->resource_type == 'groupdocument_document' || $this->resource_type == 'eventdocument_document' || $this->resource_type == 'sitepagedocument_document' || $this->resource_type == 'sitebusinessdocument_document'): ?>
					
						<?php if(!empty($this->result->photo_id)): ?>
							<?php echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result, 'thumb.icon'), array()) ?>
						<?php elseif ($this->resource_type == 'document'): ?>
							<?php echo $this->htmlLink($this->result->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($this->result->thumbnail) .'" class="thumb_icon" />', array() ) ?>
						<?php elseif ($this->resource_type == 'groupdocument_document' || $this->resource_type == 'eventdocument_document' || $this->resource_type == 'sitebusinessdocument_document' || $this->resource_type == 'sitepagedocument_document'): ?>
							<?php echo $this->htmlLink($this->result->getHref(), '<img src="'. $this->result->thumbnail .'" class="thumb_icon" />', array() ) ?>
					  <?php endif; ?>
					  
						<?php //echo $this->htmlLink($this->result->getHref(), '<img src="'. $this->result->thumbnail .'"	class="photo" />');
						?>
						<?php elseif(empty($this->result->photo_id) &&  ($this->resource_type == 'music_playlist')): ?>
						<?php echo $this->htmlLink($this->result->getHref(),
						$this->itemPhoto($this->result->getOwner(), 'thumb.profile')); ?>
						<?php else: ?>
						<?php echo $this->htmlLink($this->result->getHref(), $this->itemPhoto($this->result,
							'thumb.normal')); ?>
					<?php endif; ?>
				</div>
		    <div class="tip_main_body">
		      <div class="tip_main_body_title">
						<?php echo $this->htmlLink($this->result->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->result->getTitle(), 64)) ?>
		      </div>
		      <?php //if($this->resource_type == 'user') : ?>
<!--          	<div class="tip_main_body_stat">  
							<?php //$online_status = Engine_Api::_()->seaocore()->isOnline($this->result->user_id); ?>
							<?php //if (!empty($online_status)): ?>
								<img title="Online" src='<?php //echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
								<?php  //echo $this->translate("Online");?>
							<?php //else: ?>
								<img title="Offline" src='<?php //echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />
								<?php  //echo $this->translate("Offline");?>
							<?php //endif; ?>
						</div>-->		
					<?php //endif; ?>
		      <?php if ($this->resource_type != 'user' && !empty($informationArray) && in_array("category",
	          $informationArray)) : ?>

						<?php 
									$getShortType = $this->result->getShortType();
									if($getShortType == 'playlist') {
										$getShortType = 'Music';
									} elseif($getShortType == 'topic') {
										$getShortType = 'Forum Topic';
									} elseif($getShortType == 'business') {
										$getShortType = $this->translate(' Business ');
									} elseif($getShortType == 'page') {
										$getShortType = $this->translate(' Page ');
									}
									else {
									$getShortTypeArray=explode('_',$this->result->getShortType());
									foreach ($getShortTypeArray as  $k=>$str)
										$getShortTypeArray[$k]=ucfirst($str);
									$getShortType=implode(' ',$getShortTypeArray);
									}
									if (empty($this->getCategoryText)) { ?>
								<div class="tip_main_body_stat">
										<?php echo $this->translate($getShortType); ?> &#160;
								</div>
						<?php } else { ?>
							<div class="tip_main_body_stat">
							 <?php echo $this->translate($getShortType); ?>
							 &#187;
								<?php echo $this->getCategoryText; ?> &#160;
							</div>
						<?php } ?>
              <?php endif; ?>
              
							<?php  //here we show date of event content.
							if (!empty($this->result->starttime) && $this->resource_type == 'event') : ?>
								<div class="tip_main_body_stat">
									<?php  echo $this->translate('Date: '); ?>
									<?php echo $this->locale()->toDateTime($this->result->starttime);  ?>
								</div>
							<?php endif; ?>
							
							<?php if($this->resource_type == "sitereview_wishlist") : ?>
								<div class="tip_main_body_stat">
								  <?php if (!empty($informationArray) && in_array("rwcreated_by", $informationArray)) : ?>
										<?php echo $this->translate('Created by %s', $this->result->getOwner()->toString()) ?><br />
									<?php endif; ?>
									<?php if (!empty($informationArray) && in_array("rewishlist_item", $informationArray)) : ?>
									<?php echo $this->translate(array('%s entry', '%s entries', count($this->result)), count($this->result)); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						
							<?php if (($this->resource_type == 'sitereview_listing') && !empty($this->result->price) && !empty($informationArray) && in_array("price", $informationArray)) : ?>
								<div class="tip_main_body_stat">
									<?php  echo $this->translate('Price: '); ?>
									<span class="discount_value"><?php echo $this->locale()->toCurrency($this->result->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></span>
								</div>
							<?php elseif($this->resource_type == 'siteestore_product'): ?>
									<div class="tip_main_body_stat">
										<?php  echo $this->translate('Price: '); ?>
										<span class="discount_value"><?php echo $this->result->price; ?></span>
									</div>
							<?php endif; ?>

              <?php if ($this->resource_type == 'sitereview_listing') : ?>
							  <div class="tip_main_body_stat">
							  	<?php if (!empty($this->result->review_count) && !empty($informationArray) && in_array("review_count", $informationArray)) : ?>
										<?php echo $this->translate(array('%s review', '%s reviews', $this->result->review_count), $this->result->review_count) ?>&nbsp;&nbsp;
									<?php endif; ?>
									<?php if (!empty($this->result->rating_avg) && !empty($informationArray) && in_array("rating_count", $informationArray)) : ?>
											<?php echo $this->translate(array('%s rating', '%s ratings', $this->result->rating_avg), $this->result->rating_avg) ?>
									<?php endif; ?>
								</div>
              <?php endif; ?>


              <?php if ($this->resource_type == 'sitereview_review' && !empty($informationArray) && in_array("recommend", $informationArray)) : ?>
								<div class="tip_main_body_stat">
									<?php echo $this->translate('Recommended: '); ?>
									<?php if ($this->result->recommend == '1') : ?>
										<?php echo $this->translate('Yes'); ?>
									<?php else: ?>
										<?php echo $this->translate('No'); ?>
									<?php endif; ?>
								</div>
								
								<?php $review = $this->result;
								$ratingData = $review->getRatingData(); ?>
								<?php
								$rating_value = 0;
								foreach ($ratingData as $reviewcat):
									if (empty($reviewcat['reviewcat_name'])):
										$rating_value = $reviewcat['rating'];
										break;
									endif;
								endforeach;
								?>
								<?php echo $this->showRatingStar($rating_value, 'user', 'big-star'); ?>
								<div class="tip_main_body_stat">
									<?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitereview'); ?>
									<span><?php echo $this->translate("Helpful: "); ?></span> 
									<?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 1); ?>
									<span class="thumb-up"></span>
									<?php echo $this->countHelpfulReviews ?><?php echo $this->translate(" Yes,"); ?>
									<?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 2); ?>
									<span class="thumb-down"></span>
									<?php echo $this->countHelpfulReviews; ?><?php echo $this->translate(" No"); ?>
								</div>
              <?php endif; ?>
              
							<?php if ($this->resource_type == "sitepage_page" || $this->resource_type == "sitebusiness_business") : ?>
								<div class="tip_main_body_stat">
									<?php if (!empty($informationArray) && in_array("phone", $informationArray) && !empty($this->result->phone)) : ?>
									<?php  echo  $this->translate("Phone: ") ?><?php echo $this->result->phone; ?><br />
									<?php endif; ?>
									<?php if (!empty($informationArray) && in_array("email", $informationArray) && !empty($this->result->email)) : ?>
									<?php  echo  $this->translate("Email: ") ?><?php echo $this->result->email; ?><br />
									<?php endif; ?>
									<?php if (!empty($informationArray) && in_array("website", $informationArray) && !empty($this->result->website)) : ?>
									<?php  echo  $this->translate("Website: ") ?><?php echo $this->result->website; ?>
									<?php endif; ?>
								</div>
              <?php endif; ?>
              
              <?php if (!empty($informationArray) && in_array("location", $informationArray)) : ?>
								<?php if (!empty($this->result->location)) : ?>
									<div class="tip_main_body_stat">
										<?php  echo $this->translate('Location: '); ?>
										<?php echo $this->result->location;  ?>
									</div>
								<?php endif; ?>
								
								<?php if ($this->resource_type == 'recipe' || $this->resource_type == 'list_listing') : ?>
									<?php if(!empty($this->locationItem)) : ?>
										<div class="tip_main_body_stat">
											<?php  echo $this->translate('Location: '); ?>
											<?php echo $this->locationItem->location;  ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($this->resource_type == 'classified'): ?>
									<?php if(!empty($this->locationItem)) : ?>
										<div class="tip_main_body_stat">
											<?php  echo $this->translate('Location: '); ?>
											<?php echo $this->locationItem;  ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
						<?php endif; ?>

	          <?php //FOR GROUP SHOW MEMBER.
            if ($this->resource_type == 'group' && !empty($informationArray) && in_array("groupmember",$informationArray)) { ?>
           	<div class="tip_main_body_stat">
              <?php echo $this->translate(array('%s member', '%s members', $this->result->member_count), $this->result->member_count) ?>
            </div>
            <?php if (!empty($informationArray) && in_array("joingroupfriend", $informationArray)) : ?>
            <div class="tip_main_body_stat" style="margin-top:7px;">
              <?php if (!empty($this->friendLikeCount))  { ?>
								<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' =>'seaocore', 'controller' => 'feed', 'action'=>'common-member-list', 'resouce_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend is member','%s friends are members', $this->friendLikeCount),$this->friendLikeCount);?> </a>
        <!--      echo $this->translate(array('%s friend joined', '%s friends joined',
                 $this->friendLikeCount),$this->friendLikeCount);-->
                <?php } ?>
            </div>
            <?php endif; ?>
            <?php } elseif ($this->resource_type == 'event' && !empty($informationArray) && in_array("eventmember", $informationArray)) { ?>
              <div class="tip_main_body_stat">
                <?php echo $this->translate(array('%s member', '%s members', $this->result->member_count), $this->result->member_count) ?>
              </div>
              <?php if (!empty($informationArray) && in_array("attendingeventfriend", $informationArray)) : ?>
              <div class="tip_main_body_stat" style="margin-top:7px;">
                <?php if (!empty($this->friendLikeCount))  { ?>
                <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' =>'seaocore', 'controller' => 'feed', 'action'=>'common-member-list', 'resouce_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend is attending','%s friends are attending', $this->friendLikeCount),$this->friendLikeCount);?> </a>
              <!--  echo $this->translate(array('%s friend attending' , '%s friend attending',
                 $this->friendLikeCount), $this->friendLikeCount);-->
                 <?php } ?>
              </div>
              <?php endif; ?>
              <?php } elseif ($this->resource_type == 'user') { ?>
              <div class="tip_main_body_stat">
                <?php if (!empty($this->likeCount) && !empty($informationArray) && in_array("like",
                  $informationArray))
                echo $this->translate(array('%s likes this', '%s like this', $this->likeCount), $this->likeCount) ?>&#160;
              </div>

              <?php  if (!empty($this->muctualfriendLikeCount) && ($this->resource_id != $this->viewer_id)
                 && !empty($informationArray) && in_array("mutualfriend", $informationArray) ) { ?>
                <div class="tip_main_body_stat" style="margin-top:7px;">
									<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed','action'=>'more-mutual-friend', 'id' =>	$this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php	echo $this->translate(array('%s mutual friend','%s mutual friends', $this->muctualfriendLikeCount), $this->muctualfriendLikeCount);?>		</a>
                </div>
              <?php  } ?>
             <?php } else  { ?>
             
              <div class="tip_main_body_stat">
                <?php if (!empty($this->likeCount) && !empty($informationArray) && in_array("like",$informationArray))
                echo $this->translate(array('%s likes this', '%s like this', $this->likeCount), $this->likeCount) ?>
              </div>
              <div class="tip_main_body_stat" style="margin-top:7px;">
                <?php
                if (!empty($this->friendLikeCount) && !empty($informationArray) && in_array("friendcommon",
                 $informationArray) ) { ?>
										<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action'=>'common-member-list', 'resouce_type'	=> $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default'	, true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend likes this','%s friends like this', $this->friendLikeCount),$this->friendLikeCount);?>
	                  </a>
                <!--echo $this->translate(array('%s friend likes this', '%s friends like this',
$this->friendLikeCount), $this->friendLikeCount);-->
               <?php } ?>
              </div>
            <?php  } ?>
            <?php if($this->resource_type == 'user' && ($this->resource_id != $this->viewer_id) &&
!empty($informationArray) && in_array("mutualfriend", $informationArray) ) { ?>
            	<?php  if (!empty($this->muctualfriendLikeCount)): ?>
		            <?php
		              $container = 1;
		              foreach( $this->muctualFriend as $friendInfo ) { 
		            ?>
            	<div class="info_tip_member_thumb info_show_tooltip_wrapper">
	              <?php
	                  $user_subject = Engine_Api::_()->user()->getUser($friendInfo['user_id']);
	                  $profile_url = $this->url(array('id' => $friendInfo['user_id']), 'user_profile');
	              ?>
	              <div class="info_show_tooltip">
	              	<?php echo $this->user($user_subject)->getTitle() ?>
	              	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
	              </div>
                <a href="<?php echo $profile_url ?>" target="_parent">
                	<?php echo $this->itemPhoto($this->user($user_subject),   'thumb.icon') ?>
                </a>
              </div>
              <?php if($container == 5): break; endif; ?>
                <?php $container++ ; } ?>
              <?php endif; ?>
            <?php } elseif($this->resource_type != 'user'  && !empty($informationArray) &&
in_array("friendcommon", $informationArray)){ ?>
	            <?php  if (!empty($this->friendLikeCount)): ?>
		            <?php
		              $container = 1;
		              foreach( $this->activity_result as $friendInfo ) {
		              ?>
              <div class="info_tip_member_thumb info_show_tooltip_wrapper">
                <?php
                  if ($this->resource_type == 'group' || $this->resource_type == 'event' ) {
                    $user_subject = Engine_Api::_()->user()->getUser($friendInfo->user_id);
                    $profile_url = $this->url(array('id' => $friendInfo->user_id), 'user_profile');
                  } else {
                    $user_subject = Engine_Api::_()->user()->getUser($friendInfo->poster_id);
                    $profile_url = $this->url(array('id' => $friendInfo->poster_id), 'user_profile');
                  }
                ?>
	              <div class="info_show_tooltip">
	              	<?php echo $this->user($user_subject)->getTitle() ?>
	              	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
	              </div>
                <a href="<?php echo $profile_url ?>" target="_parent">
                	<?php echo $this->itemPhoto($this->user($user_subject),   'thumb.icon') ?>
                </a>
              </div>
              <?php if($container == 5): break; endif; ?>
              <?php $container++ ; } ?>
              <?php endif; ?>
            <?php } ?>
    		</div>
			</div>
       <?php $flag = false; ?>
			  <?php if ($this->resource_type == 'user') { ?>
                <?php  //POKE WORK
								$user_subject = Engine_Api::_()->user()->getUser($this->resource_id);

                if (!empty($this->pokeEnabled) && (!empty($this->getpokeFriend)) && ($this->resource_id !=
                   $this->viewer_id) && !empty($info_values) && in_array("poke", $info_values) && (!$this->viewer->isBlockedBy($user_subject) || $this->viewer->isAdmin())) { ?>
                  <?php if (!$flag) : ?>
                    <div class="info_tip_content_bottom">
                  <?php $flag = true; endif; ?>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'poke_general', 'module' => 'poke', 'controller' => 'pokeusers',
                  'action'=>'pokeuser', 'pokeuser_id' =>  $this->resource_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poke/externals/images/poke_icon.png);"><?php echo $this->translate("Poke") ?></a>
                <?php } //END POKE WORK. ?>

                <?php //FOR SUGGESTION LINK SHOW IF SUGGESTION PLUGIN INSTALL AT HIS SITE. ?>
                <?php 
                if (!empty($this->suggestionEnabled) && !empty($this->getMemberFriend) &&
                 (!empty($this->suggestion_frienf_link_show)) && !empty($info_values) &&
                  in_array("suggestion", $info_values) && (!empty($this->viewer_id))) {
                ?>
                  <?php if (!$flag) : ?>
                    <div class="info_tip_content_bottom">
                  <?php $flag = true; endif; ?>
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module'=> 'suggestion', 'controller' => 'index', 'action' =>
                  'switch-popup','modName' => $this->moduleNmae, 'modContentId' => $this->resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/sugg_blub.png);"><?php echo $this->translate("Suggest to Friends") ?></a>
                <?php } //END SUGGESTION WORK. ?>

                <?php //FOR MESSAGE LINK
                $item = Engine_Api::_()->getItem('user', $this->resource_id);
                if (!empty($info_values) && in_array("message",
                 $info_values) && (Engine_Api::_()->seaocore()->canSendUserMessage($item)) &&
(!empty($this->viewer_id))) {
                ?>
                  <?php if (!$flag) : ?>
                    <div class="info_tip_content_bottom">
                  <?php $flag = true; endif; ?>
                  <a href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $this->resource_id ?>" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Messages/externals/images/send.png);">
                  <?php echo $this->translate('Message'); ?> </a>
                <?php } ?>
                <?php  if( !empty($info_values) && in_array("addfriend", $info_values) &&
(!empty($this->viewer_id)) ): ?>
                <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($this->resource_id)); ?>
                <?php if (!$flag && !empty($uaseFRIENFLINK)) : ?>
                    <div class="info_tip_content_bottom">
                    <!--<div id="addfriend">-->
                  <?php $flag = true; endif; ?>
                  <?php //VIEWER IS VIEW PROFILE OF ANOTHER USER AND NOT A FRIEND THEN ADD FRIEND
                   //LINK IS SHOW. ?>
                  <?php echo $uaseFRIENFLINK; ?>
                <?php endif; ?>
                <?php //VIEWER IS VIEW PROFILE OF ANOTHER USER AND NOT A FRIEND THEN ADD FRIEND LINK IS SHOW.?>
        <?php }	?>
        
				<?php if (!empty($this->suggestionEnabled) && ($this->resource_type != 'user') &&
				(!empty($this->suggestion_frienf_link_show)) && !empty($info_values) && in_array("suggestion", $info_values) && (!empty($this->viewer_id))) : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?> 
					<?php endif; ?>
					<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module'=> 'suggestion', 'controller' => 'index', 'action' =>
					'switch-popup','modName' =>$this->moduleNmae, 'modContentId' => $this->resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/sugg_blub.png);"><?php echo $this->translate("Suggest to Friends") ?></a>
				<?php endif; ?>

				<?php if ((!empty($info_values) && in_array("getdirection", $info_values)) && ($this->resource_type == 'sitepage_page' || $this->resource_type == 'sitebusiness_business' || $this->resource_type == 'event' || $this->resource_type == 'recipe' || $this->resource_type == 'classified' || $this->resource_type == 'sitepageevent_event' || $this->resource_type == 'sitebusinessevent_event' || $this->resource_type == 'list_listing' || $this->resource_type == 'group'|| $this->resource_type == 'user')) : ?>
					<?php if (!empty($this->result->location) || !empty($this->locationItem)) : ?>
						<?php if (!$flag) : ?>
							<div class="info_tip_content_bottom">
							<?php $flag = true; ?>
						<?php endif; ?>
						<?php if ($this->resource_type == 'sitepage_page' || $this->resource_type == 'sitebusiness_business' || $this->resource_type == 'recipe' || $this->resource_type == 'list_listing') : ?>
						<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->resource_id, 'resouce_type' => $this->resource_type), $this->translate("Directions"), array('onclick' => 'owner(this);return false', 'style' => 'background-image:url('. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/direction.png);')) ; ?>
						<?php elseif($this->resource_type == 'classified') : ?>
							<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->resource_id, 'resouce_type' => 'classified'), $this->translate("Directions"), array('onclick' => 'owner(this);return false', 'style' => 'background-image:url('. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/direction.png);')) ; ?>
						<?php elseif (($this->resource_type == 'event' || $this->resource_type == 'group' || $this->resource_type == 'user') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')): ?>
							<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->result->seao_locationid, 'resouce_type' => 'seaocore'), $this->translate("Directions"), array('onclick' => 'owner(this);return false', 'style' => 'background-image:url('. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/direction.png);')) ; ?>
						<?php elseif ($this->resource_type != 'event'): ?>
							<?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->result->seao_locationid, 'resouce_type' => 'seaocore'), $this->translate("Directions"), array('onclick' => 'owner(this);return false', 'style' => 'background-image:url('. $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/direction.png);')) ; ?>
						<?php endif; ?>
					<?php endif;?>
				<?php endif; ?>

        <?php if ((!empty($info_values) && in_array("review_wishlist", $info_values)) && !empty($this->listingtypeArray->wishlist) && $this->resource_type == 'sitereview_listing')  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitereview_wishlist_general','module' => 'sitereview','controller' => 'wishlist', 'action' => 'add', 'listing_id' => $this->resource_id), 'default' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitereview/externals/images/icons/wishlist_add.png);"><?php echo $this->translate('Add to Wishlist');?></a>
				<?php endif; ?>

				<?php if ((!empty($info_values) && in_array("joinpage", $info_values)) && !empty($this->joinFlag) && $this->resource_type == 'sitepage_page' && !empty($this->member_approval))  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepagemember', 'module' => 'sitepagemember', 'controller' => 'member', 'action' => 'join', "page_id" => $this->resource_id), 'default' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagemember/externals/images/member/join.png);"><?php echo $this->translate("Join Page");?></a>
				<?php endif; ?>
				
        <?php if ((!empty($info_values) && in_array("requestpage", $info_values)) && !empty($this->requestFlag) && $this->resource_type == 'sitepage_page' && empty($this->member_approval))  : ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
						<?php $flag = true; ?>
					<?php endif; ?>
					<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepagemember', 'module' => 'sitepagemember', 'controller' => 'member', 'action' => 'request', "page_id" => $this->resource_id), 'default' , true)); ?>'); return false;" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagemember/externals/images/member/join.png);"><?php echo $this->translate('Request Member for Page');?></a>
				<?php endif; ?>

        <?php //FOR SHARE LINK.
         if ( !empty ($this->viewer_id) && ($this->resource_type != 'user') && ($this->resource_type != 'forum') && ($this->resource_type != 'album') && !empty($info_values) && in_array("share",
          $info_values) ): ?>
					<?php if (!$flag) : ?>
						<div class="info_tip_content_bottom">
					<?php $flag = true; endif; ?>
          <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo
$this->escape($this->url(array('module'=> 'advancedactivity', 'controller' => 'index', 'action' =>
'share','type' => $this->resource_type, 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default' ,
true)); ?>'); return false;" style="background-image:
url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/share.png);"><?php echo $this->translate("Share") ?></a>
        <?php endif; ?>		
			<?php if ($flag) : ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script type="text/javascript">

function showSmoothBox(url)
{ 
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
</script>
  <script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>