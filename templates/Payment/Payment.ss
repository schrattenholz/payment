	 <div class="col mb-4 card">
		<div class="card-header">
			<h3 class="accordion-heading">Zahlungsoptionen <span class="accordion-indicator"></span></h3>
		</div>
		<div class="accordion mb-4" id="paymenMethods">
			<% loop $RelatedPaymentMethods($DeliveryTypeID,$Top.Basket.PaymentMethodID) %>
			<div class="card">
				<div class="card-header">
				<h3 class="accordion-heading">
				<a href="#pM_$ID" data-target="#pM_$ID" onclick="setPaymentMethodID($ID);" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="pM_$ID">
					$Title
					<span class="accordion-indicator"><i data-feather="chevron-up"></i></span>
					</a>
				</h3>
				</div>
				<div class="collapse <% if $Top.Basket.PaymentMethodID == $ID || not $Top.Basket.PaymentMethodID && $First %>show<% end_if %>" id="pM_$ID" data-parent="#paymenMethods">
				<div class="card-body">
					<!--
					<div class="custom-control custom-radio">
						<input class="custom-control-input" type="radio" id="r_pM_$ID" name="paymentMethod" <% if $First %>checked="checked"<% end_if %>>
						<label class="custom-control-label" for="r_pM_$ID">Auswählen</label>
					</div>
					-->
					<% if $Template %>
						$Content
						$renderTemplate($Top.Basket.ID)
					<% else %>
						$Content
					<% end_if %>
				</div>
				</div>
			</div>
			<% end_loop %>				  
		</div>
		<input type="text" name="PaymentMethodID" style="display:none;" id="PaymentMethodID" value="$RelatedPaymentMethods($DeliveryTypeID,$PaymentMethodID).First.ID" />
	</div>
