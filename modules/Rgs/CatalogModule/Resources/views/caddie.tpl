{* file:[RgsCatalogModule]User/profile.tpl *}

{extends file='file:rgs_layout.php'}

{*********************************************************
Multi line comment block with credits block
  @ author:         Stéphane Demingongo Litemo : novice@example.com
  @ maintainer:     support@example.com
  @ para:           var that sets block style
  @ css:            the style output
**********************************************************}

{block name="title" prepend}
{'Caddie'|trans} | 
{/block}


{block name="carousel"}
{include file='file:includes/noCarousel.tpl'}
{/block}

{block  name=section}
{$total = 0}

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 main-title">
    <h1>{'Caddie'|trans}</h1>
</div>
<hr />
    	{if $rgs.caddie->count() == 0}
        <h3>
        	{'No articles in the caddie'|trans}.
        </h3>
        {/if}
 
<div >
    
    <div id="caddie" class="col-md-9 col-sm-12 col-xs-12">
        {foreach $rgs.caddie->findAll() as $r}
        {$total = $total + ($r.article.price * $r.quantity)}
        <article class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <form id="articleCaddieForm" name="articleCaddieForm" method="POST" data-novice="form-control">
        <input type="hidden" name="id" value="{$r.article.id}" />
        <div class="panel panel-default" style="min-height: 250px;" > <!-- style="height: 470px;" -->        	
        	<div class="panel-heading text-center">
            
            	<a 
                    href="{path id=rgs_catalog_article_details params=['id' => $r.article.id, 'slug' => $r.article.slug]}" 
                    title="{$r.article.name|escape}" 
                    class="hidden-lg hidden-sm hidden-xs">
                    {$r.article.name|truncate:23:'...':true}
                </a>
				<a 
                    href="{path id=rgs_catalog_article_details params=['id' => $r.article.id, 'slug' => $r.article.slug]}" 
                    title="{$r.article.name|escape}" 
                    class="hidden-md">
                    {$r.article.name|truncate:29:'...':true}
                </a>
                
		        <button type="submit" name="caddie[]" class="btn btn-xs btn-warning pull-right" value="remove"
                 title="Retirer du caddie">
					X
		        </button>
    		</div>
			<div class="panel-body" style="height: 150px;">
            	<div class="row text-center">
        			<div>
		        	<a href="#" title="{$r.article.name|escape}">
	    			<img src="{image_src path=$r.article.image package=upload}" class="img-thumbnail" alt="image" style="height: 120px; min-width: 120px;" />
    		        </a>
			    	</div>
			    </div>
            </div>
            <div class="panel-footer">
        		<div>{'Quantity'|trans}: {select_quantity min=1 max=$r.article.stock value=$r.quantity name="quantity" onchange="this.blur();
                this.form.submit();"}
                {************
                <input 
                type="number" 
                name="quantity"
                min="1" max="{$r.article.stock}" 
                value="{$r.quantity}" 
                style="text-align:right;"
                onchange="this.form.submit()" />
                *******}
                </div>
                <div>{'Unit price'|trans}: {$r.unitPrice} &euro;</div>
            </div>
        </div>
        </form>
        </article>
        {/foreach}  
    </div>
    
    <div id="receipt" class="col-md-3 col-sm-12 col-xs-12">
    	<div style="margin-bottom: 10px;">
    	<a class="btn btn-block btn-info" href="{path id='rgs_catalog_articles_all' absolute=true}">
					<h4><span class="glyphicon glyphicon-chevron-left"></span> {'Continue shopping'|trans}</h4>
		</a>
        </div>
        {if $rgs.caddie->count() > 0}
    	<form id="caddieForm" name="caddieForm" method="POST" data-novice="form-control">
    	<div >
        	<div class="panel panel-primary" >
            <div class="panel-heading">
            	<h4>{'Caddie invoice'}</h4>
            </div>
            <div class="panel-body">
            	<div >
                	<h4>
                		{'Number of articles'|trans}: <span class="pull-right"><b>{$rgs.caddie->count()}</b></span>        	
		            </h4>
    	            <h4>
        	        	{'Price'|trans}:   <span class="pull-right"><b>{$total} &euro;</b></span>          	
	        	    </h4>
                </div>
            </div>
            <div class="panel-footer">
        	
		        <button type="submit" name="caddie[]" class="btn btn-block btn-success" value="confirm" 
                title="Valider le caddie pour réserver les articles"
                 {if $rgs.caddie->count() == 0}disabled{/if}>
					<h4>{'toCommand'|trans}</h4>
		        </button>
	    	    <button type="submit" name="caddie[]" class="btn btn-block btn-danger" value="removeAll" data-novice-toggle="confirm" 
				data-novice-text="{'Would you like to empty the caddie'|trans} ?" {if $rgs.caddie->count() == 0}disabled{/if}>
					<h5><span class="glyphicon glyphicon-trash"></span> {'toEmpty'|trans}</h5>
		        </button>
            </div>
            </div>
        </div>
        </form>
        {/if}
    </div>

</div>

{/block}
