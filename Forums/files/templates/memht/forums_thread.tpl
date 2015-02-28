{if isset($plugin_posts)}
	{foreach item=post from=$plugin_posts name=cnt}
		<div class="tpl_forums_post_ring">
			<div class="tpl_forums_post">
				<div>
					<div style="float:left;" class="title">{$post.title}</div>
					<div style="text-align:right;"><a name="post{$post.id}">#post{$post.id}</a></div>
					<div style="clear:both;"></div>
				</div>
				<div>
					<div style="float:left;margin-right:5px;">{$post.userdata.avatar}</div>
					<div class="info">
						<div style="float:left;"><a href="index.php?{$smarty.const._NODE}=user&amp;op=info&amp;uid={$post.userdata.uid}">{$post.userdata.options.stylename}</a><br />{$post.userdata.options.rolename}<br /><span>{$post.created}</span></div>
						<div style="text-align:right;">{$post.userdata.posts} {t 1=POSTS}</div>
						<div style="clear:both;"></div>
					</div>
				</div>
				<div class="body">{$post.text}</div>
				{if isset($plugin_form.form.additional)}
					{foreach item=add from=$post.additional name=cnt}
						<div class="additional">
							{$add.content}
						</div>
					{/foreach}
				{/if}
				{if ($post.modauthor_id>0)}
					<div class="foot">{t 1=MODIFIED_BY_X_ON_Y 2=$post.modauthor_name 3=$post.modified}</div>
				{/if}
				<div class="tpl_forums_buttons_inthread">
					{if isset($plugin_data.buttons.reply) && $plugin_data.buttons.reply>0} 
						<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=reply&amp;t={$plugin_data.thread_id}&amp;p={$post.id}" title="{t 1=QUOTE}">{t 1=QUOTE}</a></div>
					{/if}
					{if isset($post.buttons.edit) && $post.buttons.edit>0} 
						<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=edit&amp;p={$post.id}" title="{t 1=EDIT}">{t 1=EDIT}</a></div>
					{/if}
					{if isset($post.buttons.delete) && $post.buttons.delete>0} 
						<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=delete&amp;p={$post.id}" title="{t 1=DELETE}">{t 1=DELETE}</a></div>
					{/if}
					<span style="display:block;clear:both;"></span>
				</div>
			</div>
		</div>
	{/foreach}
{/if}
<div class="tpl_forums_buttons">
	{if isset($plugin_data.buttons.reply) && $plugin_data.buttons.reply>0} 
		<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=reply&amp;t={$plugin_data.thread_id}" title="{t 1=REPLY}">{t 1=REPLY}</a></div>
	{/if}
	<span style="display:block;clear:both;"></span>
</div>

{if isset($plugin_form) && sizeof($plugin_form)}
	<div class="quickreply">
		<div class="inner">
			<form action="{$plugin_form.form.action}" method="{$plugin_form.form.method}" enctype="{$plugin_form.form.enctype}">
				<input type="hidden" name="title" value="{$plugin_form.form.title}" />
				<div class="textarea">
					<textarea name="text">{$plugin_form.form.text}</textarea>
				</div>
				{if isset($plugin_form.form.additional)}
					<div class="additional">
					{foreach item=addon from=$plugin_form.form.additional name=cnt}
						<div class="row">
							{$addon.content}
						</div>
					{/foreach}
					</div>
				{/if}
				<div class="button">
					<div><input type="submit" name="submit" value="{t 1=QUICKREPLY}" /></div>
				</div>
				<input name="ctok" type="hidden" value="{$plugin_form.form.ctok}" />
				<input name="ftok" type="hidden" value="{$plugin_form.form.ftok}" />
			</form>
		</div>
	</div>
{/if}