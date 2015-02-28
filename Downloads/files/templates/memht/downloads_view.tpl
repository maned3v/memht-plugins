<div class="tpl_blog_title">
	{if $plugin_view.usecomments==1}
    	<div class="tpl_blog_com"><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$plugin_view.sname}&amp;cat={$plugin_view.cname}&amp;title={$plugin_view.name}#comments" title="{t 1=X_COMMENTS 2=$plugin_view.comments}" rel="tooltip">{$plugin_view.comments}</a></div>
    {/if}
   	<div class="title">
		<strong>{$plugin_view.title}</strong>
    </div>
    <div class="tpl_blog_info">{t 1=WRITTEN_BY_X_ON_Y 2=$plugin_view._author 3=$plugin_view.created}</div>
</div>
    
    <div class="tpl_blog_tools">
    	<div style="float:left;">{$plugin_view.rating}</div>{* {t 1=RATING}: {$plugin_view.rating} *}
        <div style="text-align:right;"><a href="javascript:void(0);" onclick="javascript:openPopup('{$sys_site_url}/index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=rss&amp;sec={$plugin_view.sname}&amp;cat={$plugin_view.cname}','600','400')" title="{t 1=RSS}" rel="tooltip"><img src="{$sys_site_url}/templates/{$sys_template}/images/rss.gif" alt="{t 1=RSS}" /></a></div>
	</div>
    
    <div class="tpl_blog_body">
        {if isset($plugin_view.image) && !empty($plugin_view.image)} <img src="{$plugin_view.image}" alt="{$plugin_view.title|CleanTitleAtr}" title="{$plugin_view.title|CleanTitleAtr}" class="tpl_thumb_index" /> {/if}
        {$plugin_view.description}
		<div style="clear:both;"></div>
		<div style="margin-top:10px; padding:10px; text-align:center; background-color:#EEE; font-weight:bold;" class="tpl_rounded"><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=get&amp;id={$plugin_view.id}" title="{t 1=DOWNLOAD}" rel="tooltip">{t 1=DOWNLOAD}</a></div>
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
{if isset($sys_adv_downloads)}<div style="text-align:center;">{$sys_adv_downloads}</div>{/if}

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
	<iframe src="http://www.facebook.com/plugins/like.php?href={$sys_site_url}/index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$plugin_view.sname}&amp;cat={$plugin_view.cname}&amp;title={$plugin_view.name}&amp;layout=button_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
</div>
<div style="padding:10px;"></div>