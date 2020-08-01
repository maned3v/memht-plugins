{if isset($plugin_sec) && !empty($plugin_sec)}
	<div style="text-align:center;">
	{foreach item=value from=$plugin_sec name=cnt}
		{if isset($value.file) && !empty($value.file)}
			<div class="tpl_gallery_list">
				<div class="tpl_gallery_element">{$value.title}</div>
				<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$value.name}" title="{$value.title|CleanTitleAtr}"><img src="assets/gallery/sections/{$value.file}" alt="{$value.title|CleanTitleAtr}" title="{$value.title|CleanTitleAtr}" class="tpl_thumb_index" style="margin-top:0; float:none;" /></a>
			</div>
		{/if}
	{/foreach}
	</div>
{/if}

{if isset($plugin_cat) && !empty($plugin_cat)}
	<div style="text-align:center;">
	{foreach item=value from=$plugin_cat name=cnt}
		{if isset($value.file) && !empty($value.file)}
			<div class="tpl_gallery_list">
				<div class="tpl_gallery_element">{$value.title}</div>
				<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$value.sname}&amp;cat={$value.name}" title="{$value.title|CleanTitleAtr}"><img src="assets/gallery/categories/{$value.file}" alt="{$value.title|CleanTitleAtr}" title="{$value.title|CleanTitleAtr}" class="tpl_thumb_index" style="margin-top:0; float:none;" /></a>
			</div>
		{/if}
	{/foreach}
	</div>
{/if}

{if isset($plugin_index) && !empty($plugin_index)}
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
	{/if}
	<div style="text-align:center;">
	{foreach item=value from=$plugin_index name=cnt}
		{if isset($value.thumb) && !empty($value.thumb)}
			{if isset($plugin_opt.lightbox) && $plugin_opt.lightbox>0}
				<div class="tpl_gallery_list">
					<div class="tpl_gallery_element">{$value.title}</div>
					<a href="assets/gallery/images/{$value.file}" class="gallery" rel="gallery" title="{$value.desc|CleanTitleAtr}{if $value.usecomments>0}<a href=&quot;index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$value.sname}&amp;cat={$value.cname}&amp;title={$value.name}&quot;>{t 1=COMMENTS}</a>{/if}"><img src="assets/gallery/images/{$value.thumb}" alt="{$value.id}" title="{$value.title|CleanTitleAtr}" class="tpl_thumb_index" style="margin-top:0; float:none;" /></a>
				</div>
			{else}
				<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$value.sname}&amp;cat={$value.cname}&amp;title={$value.name}" title="{$value.title|CleanTitleAtr}"><img src="assets/gallery/images/{$value.thumb}" alt="{$value.title|CleanTitleAtr}" title="{$value.title|CleanTitleAtr}" class="tpl_thumb_index" style="margin-top:0; float:none;" /></a>
			{/if}
		{/if}
	{/foreach}
	</div>
{/if}

{if isset($plugin_sec) && empty($plugin_sec) && isset($plugin_cat) && empty($plugin_cat) && isset($plugin_index) && empty($plugin_index)}
	<div style="text-align:center;">
		{t 1=LIST_EMPTY}
	</div>
{/if}