<div class="tpl_blog_title">
	{if $plugin_view.usecomments==1}
    	<div class="tpl_blog_com"><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$plugin_view.sname}&amp;cat={$plugin_view.cname}&amp;title={$plugin_view.name}#comments" title="{t 1=X_COMMENTS 2=$plugin_view.comments}" rel="tooltip">{$plugin_view.comments}</a></div>
    {/if}
   	<div class="title">
		<strong>{$plugin_view.title}</strong>
    </div>
</div>

<div class="tpl_blog_body">
{if isset($plugin_opt.lightbox) && $plugin_opt.lightbox>0}
    {literal}
	<script type="text/javascript" src="libraries/jQuery/plugins/fancybox/jquery.fancybox.js"></script>
	<link rel="stylesheet" href="libraries/jQuery/plugins/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
	<script type="text/javascript">
		$(document).ready(function() {
			$("a.gallery").fancybox({
				'overlayColor'	:	'#000',
				'overlayOpacity':	0.7,
				'transitionIn'	:	'fade',
				'transitionOut'	:	'fade',
				'speedIn'		:	600, 
				'speedOut'		:	200, 
				'overlayShow'	:	true,
				'titlePosition'	:	'outside'
			});
		});
	</script>
	<style type="text/css">
		.fancybox-title-outside {
			position:relative;
			text-shadow:#333 1px 1px 3px;
		}
		.fancybox-title-outside a {
			position:absolute;
			top:10px;
			right:10px;
			color:#FFF;
			margin-left:10px;
			margin-bottom:10px;
		}
		.fancybox-title-outside a:hover {
			color:#F90;
		}
	</style>
	{/literal}
	
	<div class="tpl_gallery_list">
		<div class="tpl_gallery_element">{$plugin_view.title}</div>
		<a href="assets/gallery/images/{$plugin_view.file}" class="gallery" rel="gallery" title="{$plugin_view.desc|CleanTitleAtr}"><img src="assets/gallery/images/{$plugin_view.thumb}" alt="{$plugin_view.id}" title="{$plugin_view.title|CleanTitleAtr}" class="tpl_thumb_index" style="margin-top:0; float:none;" /></a>
	</div>
	<div style="clear:both;"></div>
{else}
	{if isset($plugin_view.file) && !empty($plugin_view.file)} <img src="assets/gallery/images/{$plugin_view.file}" alt="{$plugin_view.title|CleanTitleAtr}" title="{$plugin_view.title|CleanTitleAtr}" class="tpl_thumb_view" /> {/if}
    <div>{$plugin_view.desc}</div>
{/if}
	<!-- TAGS begin -->
    {if sizeof($plugin_view.tags)>0}
       	<div class="tpl_tags_box"><strong>{t 1=TAGS}:</strong>
           {foreach item=tag from=$plugin_view.tags}
               <span><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=related&amp;tag={$tag.name}" title="{$tag.title|CleanTitleAtr}">{$tag.title}</a></span>
           {/foreach}
        </div>
    {/if}
    <!-- TAGS end -->
</div>
<div style="padding:10px;"></div>
{if isset($sys_adv_gallery)}<div style="text-align:center;">{$sys_adv_gallery}</div>{/if}

{if $related.info.status=="active" && isset($related.data) && sizeof($related.data)>0}
	<div class="tpl_related_title">{$related.info.related}</div>
    <div class="tpl_related_box tpl_rounded">
		{foreach item=value from=$related.data name=rel}
        	<div class="tpl_related_item"><img src="{$sys_site_url}/templates/{$sys_template}/images/out.png" width="13" height="11" alt="{$value.title|CleanTitleAtr}" title="{$value.title|CleanTitleAtr}" />&nbsp;<a href="{$value.url}" title="{$value.title|CleanTitleAtr}">{$value.title}</a></div>
        {/foreach}
	</div>
{/if}

<div class="tpl_social_title">{t 1=SHARE}</div>
<div class="tpl_social_box tpl_rounded">
	<span style='float:left;'><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></span>
	<iframe src="http://www.facebook.com/plugins/like.php?href={$sys_site_url}/index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$plugin_view.sname}&amp;cat={$plugin_view.cname}&amp;year={$plugin_view.year}&amp;month={$plugin_view.month}&amp;title={$plugin_view.name}&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
</div>
<div style="padding:10px;"></div>