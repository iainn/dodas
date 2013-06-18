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
<?php echo $this->partial('post/_vote.tpl', 'post', array('post' => $this->post, 'new'=>$this->new, 'same'=>$this->same, 'success'=>$this->success))?>
