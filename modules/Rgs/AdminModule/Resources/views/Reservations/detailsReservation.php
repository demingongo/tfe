{extends file='file:[RgsAdminModule]layout.php'}

{block name=page-wrapper}
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Reservation: Details</h1>
    </div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
<div class="col-xs-10 col-sm-8 col-md-6">
	<form data-toggle="validator" lang={#lang#} {*data-novice='form-control'*} method="post" {*class="form-inline"*}>
    
    <h3>{'Reserved by'|trans}: 
    	<a href="{path id='rgs_admin_users_edit' params=['id' => $reservation.user.id] absolute=true}">{$reservation.user.login}</a>
        <small>({$reservation.user.email})</small>
    </h3>

{$total = 0}
{$qtt = 0}
<div class="table-responsive">
<table id="tab" class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Article</th>
      <th><span class="pull-right">{'Unit price'|trans}</span></th>
      <th><span class="pull-right">{'Quantity'|trans}</span></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
		{foreach name=reservation from=$reservation.reservationArticles item=ra}
        {$total = $total + ($ra.unitPrice * $ra.quantity)}
        {$qtt = $qtt + $ra.quantity}
        	<tr>
            <td>
            	<a href="{path id='rgs_admin_articles_edit' params=['id' => $ra.article.id, 'slug' => $ra.article.slug] absolute=true}">
                	{$ra.article.name}
                </a>
            </td>
            <td>
            	<span class="pull-right">{$ra.unitPrice}</span>
            </td>
            <td>
            	<span class="pull-right">{$ra.quantity}</span>
            </td>
            <td>
            	<span class="pull-right">{($ra.unitPrice * $ra.quantity)}&nbsp;&euro;</span>
            </td>
            </tr>
        {/foreach}
        <tr>
        	<td>
            	<b>TOTAL:</b>
            </td>
            <td>
            </td>
            <td>
            	<b class="pull-right">{$qtt}</b>
            </td>
        	<td>
            	<b class="pull-right">{$total}&nbsp;&euro;</b>
            </td>
        </tr>
  </tbody>
</table>
</div>

		{* begin submit *}
		<div class="row form-group col-md-12">
			<button type="submit" id="_submit" name="submit[]" class="btn btn-warning pull-right" value="cancel" title="Cancel the reservation" >
                {'Cancel'|trans}
            </button>
            <button type="submit" id="_submit" name="submit[]" class="btn btn-primary pull-right" value="valid" title="Confirm that reservation was paid" >
                {'Paid'|trans}
            </button>
		</div>
		{* end submit *}
	</form>
</div>
</div>
{/block}