<?php
global $post, $blog_id;
$postid = $post->ID;
$title = $post->post_title;
$date = date('M d, Y',  strtotime($post->post_date));
if($post->post_type=='video'){
    $sCategory = 'video';
    $sReadMoreLink = '/video';

    $sImgSrc = $post->image;

    $title = $post->title;
    $date = date('M d, Y',  strtotime($post->date));
    $sPostLabelImgClass= 'cDivVideoPostImageTag';
}
if($post->post_type=='post' || $post->post_type=='news'  || $post->post_type=='page'){
    $aPostCats =wp_get_post_categories($post->ID,array('fields'=>'all'));
    $sCategory = $aPostCats[0]->name;
    $sReadMoreLink = get_permalink($post->ID);
    $width = 271;
    $height = 167;
    $classtext = 'no-border';
    $sImgSrc = get_post_meta($post->ID,'enclosure', true);
    $sImgSrc = str_replace('-190x130', '', $sImgSrc);
    if(strpos($sImgSrc, 'youtube')===false && strpos($sImgSrc,'vimeo')===false){
        $sImgSrc = substr($sImgSrc,0,-6);
    }
	//str_replace('
//
//image', '', $sImgSrc);
    if($sImgSrc!='' && strpos($sImgSrc, '.bmp')===false){
        ///blog post from feed
        $sImgSrc = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$sImgSrc.'&w=271&h=167&zc=1&q=100';

    }else{
        $thumbnail = get_thumbnail($width, $height, $classtext, $title, $title, true, 'Featured');

        $thumb = $thumbnail['thumb'];
        $sImgSrc = print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, $classtext,false,true);
        if($sImgSrc==''){
            $sFirstImg = catch_that_image();
            if($sFirstImg!='' && strpos($sFirstImg, '.bmp')===false){
                $sImgSrc = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.catch_that_image().'&w=271&h=167&zc=1&q=100';
            }//$sImgSrc = catch_that_image();
        }
        if($sImgSrc==''){
            if (function_exists('z_taxonomy_image_url')){
                if(count($aPostTags)>0){
                    //var_dump($aPostTags[0]);
                    $sImgSrc= z_taxonomy_image_url($aPostTags[0]->term_id);
                }else{
                    $sImgSrc= z_taxonomy_image_url($aPostCats[0]->term_id);
                }
            }
        }

    }

    $sPostLabelImgClass=($post->post_type=='post') ? 'cDivBlogPostImageTag' : 'cDivNewsPostImageTag entry';
	if($post->post_type=='page')$sPostLabelImgClass='';
}elseif($post->post_type=='project_update'){
    $sCategory = 'project update';
    $sAttachmentLink = null;
    $sImgSrc = "";
    $sAttachmentLink = get_post_meta($post->ID, 'enclosure', true);

    if (!is_null($sAttachmentLink)) {

        $sImgSrc = str_replace('uploads2012', 'uploads/2012', $sAttachmentLink);
        if(!@getimagesize($sImgSrc))$sImgSrc='';
        else $sImgSrc = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$sImgSrc.'&w=271&h=167&zc=1&q=100';   
                                        
    }
    
    if($sImgSrc==''){
        $sImgSrc = '/wp-content/themes/braced/images/placeholder.jpg';
    }
    $sPostLabelImgClass='cDivProjUpdateImageTag';
    //get the project Id to read more link (link to akvo.org site)
    $sProjectURL = get_option('akvo_project_domain',"http://".str_replace(' ','-',wp_get_theme()).".akvoapp.org/en");
    $sReadMoreLink = $sProjectURL. "/project/";
//    $sReadMoreLink = "http://rain4food.akvoapp.org/en/project/";
    $oProjectId = $wpdb->get_results("SELECT project_id,update_id FROM " . $wpdb->prefix . "project_update_log WHERE post_id = ".$post->ID);
    foreach ($oProjectId as $iId){
        $iProjectId = $iId->project_id;
        $iUpdateId = $iId->update_id;
    }
    $sReadMoreLink = $sReadMoreLink.$iProjectId.'/update/'.$iUpdateId;
}
if($sImgSrc=='')$sImgSrc='/wp-content/themes/braced/images/placeholder.jpg';
$sNoImgClass = ($sImgSrc=='') ? 'noImg' : '';

//$i++;
?>
<li class="cLiBlogPost <?php echo $sNoImgClass; ?>">
    <div class="<?php echo $sPostLabelImgClass.' '.$sTagPlacementClass; ?>"></div>
    <?php if($sImgSrc!=''){ ?>
        <div class="cDivBlogPostImageWrapper">
            <div class="cDivBlogPostImage">
                <a href="<?php echo esc_url($sReadMoreLink); ?>" rel="bookmark" title="<?php echo esc_attr($title); ?>">
					<img src="<?php echo $sImgSrc; ?>" />
                </a>
            </div>
        </div>
    <?php } ?>
    <div class ="cDivBlogPostTitle">
    <h2 class="<?php echo $post->post_type; ?>">
        <a href="<?php echo esc_url($sReadMoreLink); ?>" title="<?php echo esc_attr($title); ?>">
            <?php $sTitle = textClipper(strip_tags($title), 35);?>
            <?php echo $sTitle; ?>

        </a>
    </h2>
    </div>

    <div class="cDivBlogPostDate">
        <?php echo $date; ?>
    </div>
    <div class="cDivBlogPostTextContent">
        <?php
        $sContent = $post->post_content;
        $iClipText =($sNoImgClass=='noImg') ? 800 : 200;
        echo textClipper(strip_tags(strip_shortcodes($sContent)), $iClipText);
        ?>
    </div>

    <div class="cDivReadmore">
        <a href="<?php echo $sReadMoreLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), $title) ?>"><?php esc_html_e('Read more', 'Quadro'); ?></a>
    </div>
    <br style="clear:both;" />
</li>