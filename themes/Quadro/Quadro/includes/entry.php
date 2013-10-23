<?php
global $post;
$postid = $post->ID;
$title = $post->post_title;
$date = date('M d, Y',  strtotime($post->post_date));
if($post->post_type=='post'){
    $aPostCats =wp_get_post_categories($post->ID,array('fields'=>'all'));
    $sCategory = $aPostCats[0]->name;                            
    $sReadMoreLink = get_permalink($post->ID);
    $width = 271;
    $height = 167;
    $classtext = 'no-border';
    $thumbnail = get_thumbnail($width, $height, $classtext, $title, $title, true, 'Featured');

    $thumb = $thumbnail['thumb'];
    $sImgSrc = print_thumbnail($thumb, $thumbnail["use_timthumb"], $title, $width, $height, $classtext,false,true);
    if($sImgSrc==''){
        $sFirstImg = catch_that_image();
        if($sFirstImg!=''){
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
    
    $sPostLabelImgClass='cDivBlogPostImageTag';
}elseif($post->post_type=='project_update'){
    $aPostAttachments = AkvoPartnerCommunication::getUpdateImages($post->ID);
    $sCategory = 'project update';
    $sAttachmentLink = null;
    if ($aPostAttachments) {
        foreach ($aPostAttachments as $oAttachment) {
            $sAttachmentLink = wp_get_attachment_url($oAttachment->ID);
        }
    }
    $sImgSrc = "";
    if (!is_null($sAttachmentLink)) {

        $sImgSrc = str_replace('uploads2012', 'uploads/2012', $sAttachmentLink);
        if(!@getimagesize($sImgSrc))$sImgSrc='';
    }
    if($sImgSrc==''){
        $sCountry = AkvoPartnerCommunication::readProjectUpdateCountry($post->ID);
        if($sCountry)$sImgSrc = '/wp-content/themes/Quadro/images/countryplaceholders/'.$sCountry.'.jpg';
    }
    $sPostLabelImgClass='cDivProjUpdateImageTag';
    //get the project Id to read more link (link to akvo.org site)
    $sReadMoreLink = "http://washalliance.akvoapp.org/en/project/";
    $oProjectId = $wpdb->get_results("SELECT project_id,update_id FROM " . $wpdb->prefix . "project_update_log WHERE post_id = ".$post->ID);
    foreach ($oProjectId as $iId){
        $iProjectId = $iId->project_id;
        $iUpdateId = $iId->update_id;
    }
    $sReadMoreLink = $sReadMoreLink.$iProjectId.'/update/'.$iUpdateId;
}
$sNoImgClass = ($sImgSrc=='') ? 'noImg' : '';

//$i++;
?>
<li class="cLiBlogPost <?php echo $sNoImgClass; ?>">
    <div class="<?php echo $sPostLabelImgClass.' '.$sTagPlacementClass; ?>"></div>
    <?php if($sImgSrc!=''){ ?>
        <div class="cDivBlogPostImageWrapper">
            <div class="cDivBlogPostImage">
                <img src="<?php echo $sImgSrc; ?>" />
            </div>
        </div>
    <?php } ?>
    <div class ="cDivBlogPostTitle">
    <h2>
        <a href="<?php echo esc_url($sReadMoreLink); ?>" title="<?php echo esc_attr($title); ?>">
            <?php $sTitle = textClipper(strip_tags($title), 40);?>	
            <?php echo $sTitle; ?>
										
        </a>
    </h2>
    </div>

    <div class="cDivBlogPostDate">
        <?php echo $date.'  -  '.$sCategory; ?>
    </div>
    <div class="cDivBlogPostTextContent">
        <?php
        $sContent = $post->post_content;
        $iClipText =($sNoImgClass=='noImg') ? 800 : 200;
        echo textClipper(strip_tags($sContent), $iClipText);
        ?>
    </div>

    <div class="cDivReadmore">
        <a href="<?php echo $sReadMoreLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s', 'Quadro'), the_title()) ?>"><?php esc_html_e('Read More', 'Quadro'); ?></a>
    </div>
    <br style="clear:both;" />
</li>