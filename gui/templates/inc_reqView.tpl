{* TestLink Open Source Project - http://testlink.sourceforge.net/ *}
{* $Id: inc_reqView.tpl,v 1.2 2007/08/27 19:07:10 jbarchibald Exp $ *}
{* Purpose: smarty include template - show requirement *}
{* Author: Martin Havlat *}
{* Revisions: 

    20070827 - jbarchibald - fixed timestamm fields from create_date to creation_ts.
                            - and a few other field name fixes. 

    *}

	<p><span class="bold">{lang_get s='title'}</span> &nbsp; {$arrReq.title|escape}</p>
	<p class="bold">{lang_get s='scope'}</p>
	<div>{$arrReq.scope}</div>
	<p><span class="bold">{lang_get s='status'}</span> &nbsp; {$selectReqStatus[$arrReq.status]}</p>
	<p class="bold">{lang_get s='coverage'}
	<div>
		{section name=row loop=$arrReq.coverage}
			<span>{$arrReq.coverage[row].name}</span><br />
		{sectionelse}
			<span>{lang_get s='req_msg_notestcase'}</span>
		{/section}
	</div>
    </p>
	<p>{lang_get s="Author"}: {$arrReq.author} [{localize_date d=$arrReq.creation_ts}]</p>
	{if $arrReq.modifier <> ''}
	<p>{lang_get s="last_edit"}: {$arrReq.modifier} [{localize_date d=$arrReq.modification_ts}]</p>
	{/if}

{*** END ***}