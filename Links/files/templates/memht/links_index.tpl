{if isset($plugin_cats) && !empty($plugin_cats)}
	{foreach item=value from=$plugin_cats name=cnt}
			<div class="tpl_links_box">				
				<a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;cat={$value.cname}" title="{$value.ctitle|CleanTitleAtr}">{$value.ctitle|CleanTitleAtr}</a>
			</div>

	{/foreach}    
{/if}

{if isset($plugin_links) && !empty($plugin_links)}
	{foreach item=value from=$plugin_links name=cnt}
	    <div class="tpl_links">		
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <th colspan="2"><span class="tpl_links_title"><a  href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=go&amp;cat={$value.category}&amp;title={$value.name}" title="{$value.title|CleanTitleAtr}">{$value.title|CleanTitleAtr}</a></span></th>
                </tr>
                <tr>
                    <td width="75%"><div class="tpl_links_content">{$value.description}</div></td>
                    <td style="text-align:right;"><img src="{$sys_site_url}/assets/links/images/{$value.image}" class="tpl_links_image" /></td>
                </tr>
                <tr>
                    <td colspan="2"><div class="tpl_links_info">{$value.url}</div></td>
                </tr>
            </table>
         </div>
	{/foreach}
{/if}

{if isset($plugin_cats) && empty($plugin_cats) && isset($plugin_links) && empty($plugin_links)}
	<div style="text-align:center;">
		{t 1=LIST_EMPTY}
	</div>
{/if}