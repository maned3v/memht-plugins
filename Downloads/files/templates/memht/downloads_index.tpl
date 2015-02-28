{if isset($plugin_dir) && !empty($plugin_dir)}
	{foreach item=sec from=$plugin_dir name=s_cnt}
		{if !empty($sec.body)}
			<div class="tpl_downloads">
				<div class="ring">
					{if isset($sec.head.head.title)}
						<div class="sectitle">{$sec.head.head.title}</div>
					{/if}
					{foreach item=cat from=$sec.body name=c_cnt}
						<div class="category">
							{if isset($cat.head.title)}
								<div class="title"><img src="templates/{$sys_template}/images/out.png" width="13" height="11" alt="{$cat.head.id}" title="-" />&nbsp;<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$sec.head.head.name}&amp;cat={$cat.head.name}" title="{$cat.head.title|CleanTitleAtr}">{$cat.head.title}</a></div>
							{/if}
							{foreach item=scat from=$cat.body name=sc_cnt}
								<div class="body">
									{if isset($scat.head.title)}
										<div><img src="templates/{$sys_template}/images/outg.png" width="13" height="11" alt="{$scat.head.id}" title="-" />&nbsp;<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$sec.head.head.name}&amp;cat={$scat.head.name}" title="{$scat.head.title|CleanTitleAtr}">{$scat.head.title}</a></div>
									{/if}
								</div>
							{/foreach}
						</div>
					{/foreach}
				</div>
			</div>
		{/if}
	{/foreach}
{/if}

{if isset($plugin_cat) && !empty($plugin_cat)}
	<div class="tpl_downloads">
		<div class="ring">
			{if isset($plugin_cat.image) && !empty($plugin_cat.image)}<img src="assets/downloads/categories/{$plugin_cat.image}" alt="{$plugin_cat.title|CleanTitleAtr}" title="{$plugin_cat.title|CleanTitleAtr}" class="tpl_thumb_index" style="margin-top:0;" />{/if}
			<div style="font-weight:bold;">{$plugin_cat.title}</div>
			<div style="margin-top:10px; font-style:italic;">{$plugin_cat.desc}</div>
			<div style="clear:both;"></div>
		</div>
	</div>
{/if}


{if !empty($plugin_index)}
	{foreach item=value from=$plugin_index name=cnt}
		<div class="tpl_blog_title">
			{if isset($value.image) && !empty($value.image)}<img src="{$value.image}" alt="{$value.title|CleanTitleAtr}" title="{$value.title|CleanTitleAtr}" class="tpl_thumb_index" />{/if}

			{if $value.usecomments==1}
			<div class="tpl_blog_com"><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$value.sname}&amp;cat={$value.cname}&amp;title={$value.name}#comments" title="{t 1=X_COMMENTS 2=$value.comments}" rel="tooltip">{$value.comments}</a></div>
			{/if}
			<div class="title">
				<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;sec={$value.sname}&amp;cat={$value.cname}&amp;title={$value.name}" title="{$value.title|CleanTitleAtr}"><strong>{$value.title}</strong></a>
			</div>
			<div class="tpl_blog_info">{t 1=ADDED_BY_X_ON_Y 2=$value._author 3=$value.created}</div>
		</div>

		<div class="tpl_blog_body">
			{$value.description}

			<!-- DOWNLOADS: TAGS begin -->
			{if sizeof($value.tags)>0}
				<div class="tpl_tags_box"><strong>{t 1=TAGS}:</strong>
				{foreach item=tag from=$value.tags}
					<span class="tpl_tags"><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=related&amp;tag={$tag.name}" title="{$tag.title|CleanTitleAtr}">{$tag.title}</a></span>
				{/foreach}
				</div>
			{/if}
			<!-- DOWNLOADS: TAGS end -->

		</div>
		<div style="clear:both;"></div>
		<div class="tpl_blog_spacer"></div>
	{/foreach}
{/if}